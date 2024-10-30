<?php
/** YD MediaPay WP Plugin main class **/
class ydmpPlugin extends YD_Plugin {
	
	private 	$_options 			= array();		// Plugin options as set by settings page
	private	$_filter			= true;
	private 	$_authorize			= false;
	private 	$_id_number			= '';
	
	/** 
	 * Constructor 
	 * 
	 */
	function __construct( $opts ) {
		
		parent::YD_Plugin( $opts );
		$this->form_blocks = $opts['form_blocks']; // No backlinkware
		
		$this->_options = get_option( $this->option_key );
		
		add_filter( 'the_content', array( &$this, 'content_filter' ), 100, 1 );
		add_action( 'init', array( &$this, 'pre_verify' ) );
		
		if( isset( $this->_options['monetize_registration'] ) && $this->_options['monetize_registration'] ) {
			add_action( 'register_form', array( &$this, 'monetize_registration' ) );
			add_filter( 'registration_errors', array( &$this, 'check_registration' ), 10, 3);
		}
		
		if( isset( $this->_options['use_stylesheet'] ) && $this->_options['use_stylesheet'] ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'add_stylesheet' ) );
			add_action( 'login_enqueue_scripts', array( &$this, 'add_stylesheet' ) );
		}
	}
	
	/**
	 * Custom stylesheet
	 * 
	 */
	public function add_stylesheet() {
		wp_register_style( 'mediapay-style', plugins_url( 'css/mp_style.css', dirname( __FILE__ ) ) );
		wp_enqueue_style( 'mediapay-style' );
	}
	
	/**
	 * If we have a code, verify it
	 * 
	 */
	public function pre_verify() {
		//error_log( 'pre_verify' );	//Debug YD
		$this->_authorize = false;
		$code = '';
		
		if( isset( $_COOKIE['mediapay_code'] ) && !empty( $_COOKIE['mediapay_code'] ) )
			$code = $_COOKIE['mediapay_code'];
		
		if( isset( $_POST['paywall_code'] ) && !empty( $_POST['paywall_code'] ) )
			$code = $_POST['paywall_code'];
		
		if( $code )
			$this->_authorize = $this->mediapay_verify( $code );
		
		if( $this->_authorize && $this->_options['remember_codes'] )
			$this->set_cookie( $code );
	}
	
	/**
	 * Dispatches display of content to display pay wall if necessary
	 * 
	 */
	public function content_filter( $content ) {
		if( !$this->_filter )
			return $content;
		
		/** avoid filter loop **/
		$this->_filter = false;

		/** Manage special cases (per-article switch) **/
		$free = false;
		$pay = false;
		$mp_status = get_post_meta( get_the_ID(), 'mediapay', true );
		if( 'free' == $mp_status || 'gratuit' == $mp_status || 'gratis' == $mp_status )
			$free = true;
		if( 'charged' == $mp_status || 'payant' == $mp_status || 'pay' == $mp_status ) {
			$free = false;
			$pay = true;
		}
		if( preg_match( '/^([a-z0-9]){10}$/', $mp_status ) ) {	// id number
			//error_log( 'setting _id_number: ' . $mp_status );	//Debug YD
			$free = false;
			$pay = true;
			$this->_id_number = $mp_status;
		}
		
		if( ( $pay && is_single() ) || ( !$free && is_single() && $this->_options['monetize_articles'] ) )
			$content = $this->monetize_content( $content );
		
		if( is_page() && $this->_options['monetize_pages'] )
			$content = $this->monetize_content( $content );
		
		return $content;
	}
	
	/**
	 * Filters display of content to display pay wall if necessary
	 * 
	 */
	private function monetize_content( $content ) {
		
		/** If we have a specific _id_number, we have to check again for single page authorizations **/
		if( $this->_id_number && ( isset( $_COOKIE['mediapay_code'] ) || isset( $_POST['paywall_code'] ) ) ) {
			//error_log('second verify');	//Debug YD
			$this->pre_verify();
		}
		
		if( $this->_authorize ) {
			return $content;
		}

		$content = '';
		
		if( $this->_options['show_excerpt'] )
			$content .= get_the_excerpt();
		
		$content .= $this->display_paywall();
		
		return $content;
	}
	
	/**
	 * Display (or return) the pay wall
	 * 
	 */
	private function display_paywall( $echo=false ) {
		$paywall_html = '';
		
		if( isset( $_POST['paywall_code'] ) && !empty( $_POST['paywall_code'] ) ) {
							
			/** Failure **/
			$paywall_html .= '<div class="mediapay paywall failure">';
			$paywall_html .= '<h3>Contenu payant</h3>';
			$paywall_html .= '<p class="mp_auth_failure">Désolé, ce code n\'est pas valide.</p>';
			$paywall_html .= $this->mediapay_form();
			
		} else {
			
			/** Initial form **/
			$paywall_html .= '<div class="mediapay paywall init">';
			$paywall_html .= '<h3>Contenu payant</h3>';
			$paywall_html .= $this->mediapay_form();
		}
		
		/** Maybe the country code was forced as post meta **/
		$mp_country = get_post_meta( get_the_ID(), 'mediapay_tarif', true );
		
		/** MediaPay API **/
		$TAB_DATA['URLSITE'] 		= parse_url( site_url(), PHP_URL_HOST );
		//$TAB_DATA['TARIFS_PAYS'] 	= "TARIF_AUDIO_FR";
		if( $mp_country ) {
			$TAB_DATA['TARIFS_PAYS'] = $mp_country;
		} else {
			$TAB_DATA['TARIFS_PAYS'] = 'TARIFS_ALL';
		}
		$TAB_DATA['IDENTIFIANT'] 	= $this->_options['mediapay_id'];
		$debug 						= $this->_options['debug_api'];
		$inc = dirname( __FILE__) . '/mediapay-phonenumber-api.inc.php';
		include_once( $inc );
		/** ------------ **/
		
		if( $table_result['resultat'] )
			$paywall_html .= $this->display_phonenumber( $table_result, $mp_country );
		
		$paywall_html .= '</div>';
		
		if( $debug )
			$paywall_html .= $api_debug_html;
		
		if( $echo )
			echo $paywall_html;
		
		return $paywall_html;	
	}
	
	/**
	 * Authentification form display code
	 * @return string
	 */
	private function mediapay_form() {
		$paywall_html = '';
		$paywall_html .= '<p class="please">Veuillez appeler le numéro ci-dessous pour obtenir un code d\'accès.</p>';
		$paywall_html .= '<form id="paywall_form" method="post">';
		$paywall_html .= '<label for="paywall_code">Code d\'accès à ce contenu&nbsp;:</label>';
		$paywall_html .= '<input type="text" id="paywall_code" name="paywall_code" size="8" />';
		$paywall_html .= '<input type="submit">';
		$paywall_html .= '</form>';
		return $paywall_html;
	}
	
	/**
	 * Displays the phone number information
	 * 
	 */
	private function display_phonenumber( $pn_data, $mp_country = '' ) {
		$html = '';
		
		$html .= '<div class="mediapay phone_info tarif' . $pn_data['tarif'] . '">';
		
		if( $mp_country ) {
			/** Just one country code **/
			$html .= '<p class="mediapay phone">Numéro de téléphone à appeler&nbsp;: <strong class="mediapay number">' . $pn_data['numero_audiotel'] . '</strong></p>';
			$html .= '<p class="mediapay tarif appel">Tarif&nbsp;: ' . $pn_data['tarif_appel'] . ' € par appel</p>';
			$html .= '<p class="mediapay tarif minute">+' . $pn_data['tarif_minute'] . ' € / minute</p>';
		} else {
			/** Table of available countries **/
			
			if(
					!is_array( $pn_data ) 			||
					!isset( $pn_data['resultat'] )	||
					!$pn_data['resultat'] === 1		||
					!isset( $pn_data['table'] )		||
					!is_array( $pn_data['table'] )	||
					empty( $pn_data['table'] )
			)
				return '<p class="error">Erreur de récupération des codes pays.</p>';
			
			$display_all = false;
			if( 
				!isset( $this->_options['countries'] ) ||
				!is_array( $this->_options['countries'] ) ||
				empty( $this->_options['countries'] )
			)
				$display_all = true;
			
			include( dirname( __FILE__) . '/country_codes.inc.php' );
			
			$html .= '<ul>';
			foreach( $pn_data['table'] as $country_data ) {
				if( 
					!$display_all && 
					!in_array( $country_data['pays'], $this->_options['countries'] )
				)
					continue;
				
				$img = plugins_url( 'flags/gif/' . strtolower( $country_data['pays'] ) . '.gif' , dirname( __FILE__ ) );
				
				$html .= '<li>';
				
				$html .= '<span class="sep sep1">';
				$html .= '<img class="flag" src="' . $img . '" title="' . $cc[strtolower( $country_data['pays'] )] . '" />';
				//$html .= '<span class="country_label">Pays&nbsp;: </span>';
				$html .= '<span class="country_code">' . $cc[strtolower( $country_data['pays'] )] . '</span>';
				$html .= '</span>';
				
				$html .= '<span class="sep sep2">';
				$html .= '<span class="tarif_label">Tarif&nbsp;: </span>';
				$html .= '<span class="tarif_appel">' . $country_data['tarif_appel'] . '€/appel</span>+';
				$html .= '<span class="tarif_minute">' . $country_data['tarif_minute'] . '€/min</span>';
				$html .= '</span>';
				
				$html .= '<span class="sep sep3">';
				$html .= '<span class="number_label">N°&nbsp;Tél&nbsp;: </span>';
				$html .= '<span class="number">' . $country_data['numero_audiotel'] . '</span></li>';
				$html .= '</span>';
			}
			$html .= '</ul>';
		}
		
		$html .= '</div>';
		
		return $html;
	}
	
	private function display_available_countries( $pn_data ) {
		$html = '';
		
		if( 
			!is_array( $pn_data ) 			|| 
			!isset( $pn_data['resultat'] )	|| 
			!$pn_data['resultat'] === 1		|| 
			!isset( $pn_data['table'] )		||
			!is_array( $pn_data['table'] )	||
			empty( $pn_data['table'] )
		) 
			return '<p class="error">Erreur de récupération des codes pays.</p>';
		
		$this->_options = get_option( $this->option_key );
		
		$html .= '<table style="margin:10px;width:95%"><tbody>';
		foreach( $pn_data['table'] as $country_data ) {
			$html .= '<tr>';
			$html .= '<td><span class="country_label">Pays&nbsp;: </span>';
			$html .= '<span class="country_code">' . $country_data['pays'] . '</span></td>';
			$html .= '<td><span class="tarif_label">Tarif&nbsp;: </span>';
			$html .= '<span class="tarif">' . $country_data['tarif'] . '€</span></td>';
			$html .= '<td><span class="tarif_appel">' . $country_data['tarif_appel'] . '€/appel</span></td>';
			$html .= '<td><span class="tarif_minute">' . $country_data['tarif_minute'] . '€/min</span></td>';
			$html .= '<td><span class="number_label">N°&nbsp;Tél&nbsp;: </span>';
			$html .= '<span class="number">' . $country_data['numero_audiotel'] . '</span></td>';
			$html .= '<td class="values">';
			$html .= '<input type="checkbox" name="countries[]" value="' . $country_data['pays'] . '" ';
			if( 
				!isset( $this->_options['countries'] )	||
				empty( $this->_options['countries'] )	||
				in_array( $country_data['pays'], $this->_options['countries'] )
			)
				$html .= 'checked="checked" ';
			
			$html .= '/>';
			$html .= '</td>';
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		
		//$html .= var_export($this->_options, true);	//Debug
		
		return $html;
	}
	
	/**
	 * Add custom code field to WP registration dialog
	 * 
	 */
	public function monetize_registration() {
		$code = ( isset( $_POST['mediapay_code'] ) ) ? $_POST['mediapay_code'] : '';
		?>
		<p>
        <label for="mediapay_code">Code de paiement<br />
            <input type="text" name="mediapay_code" id="mediapay_code" class="input" value="<?php echo esc_attr(stripslashes($code)); ?>" size="8" /></label>
    	</p>
    	<p>L'inscription à ce site est payante, veuillez utiliser le numéro de téléphone indiqué ci-dessous pour obtenir un code de paiement.</p>
		<?php
		
		/** MediaPay API **/
		$TAB_DATA['URLSITE'] 		= parse_url( site_url(), PHP_URL_HOST );
		$TAB_DATA['TARIFS_PAYS'] = 'TARIFS_ALL';
		$TAB_DATA['IDENTIFIANT'] 	= $this->_options['mediapay_id'];
		$debug 						= $this->_options['debug_api'];
		$inc = dirname( __FILE__) . '/mediapay-phonenumber-api.inc.php';
		include_once( $inc );
		/** ------------ **/
		
		if( $table_result['resultat'] )
			$paywall_html .= $this->display_phonenumber( $table_result, $mp_country );
		
		if( $debug )
			$paywall_html .= $api_debug_html;
		
		echo $paywall_html;
	}
	
	/**
	 * Check paywall code after WP registration form is submitted
	 * 
	 */
	public function check_registration( $errors, $sanitized_user_login, $user_email ) {
	
		if( !isset( $_POST['mediapay_code'] ) || empty( $_POST['mediapay_code'] ) ) {
			$errors->add( 'nocode_error', '<strong>ERREUR</strong>&nbsp;: Merci de saisir un code de paiement.' );		
		} else {
			if( !$this->mediapay_verify( $_POST['mediapay_code'] ) )
				$errors->add( 'wrongcode_error', '<strong>ERREUR</strong>&nbsp;: Ce code de paiement n\'est pas valide.' );
		}
		
		return $errors;
	}
	
	/**
	 * Mediapay verify API encapsulation method
	 * 
	 */
	private function mediapay_verify( $code ) {
		
		//error_log( 'recv _id_number: ' . $this->_id_number );	//Debug YD
		
		/** MediaPay API **/
		if( $this->_options['same_code'] && !$this->_id_number ) {
			$TAB_DATA['URL_PAGE']	= 'http://' . parse_url( site_url(), PHP_URL_HOST ) . '/*';
		} else {
			$TAB_DATA['URL_PAGE']	= get_permalink();
		}
		$TAB_DATA['CODE[0]']		= $code;						 // attention uniquement des chiffres sur audiotel
		if( $this->_id_number ) {
			$TAB_DATA['IDENTIFIANT'] 	= $this->_id_number;
		} else {
			$TAB_DATA['IDENTIFIANT'] 	= $this->_options['mediapay_id'];
		}
		$debug 						= $this->_options['debug_api'];
		$inc = dirname( __FILE__) . '/mediapay-validate-api.php';
		include( $inc );
		/** ------------ **/
		
		if( isset( $table_result['resultat'] ) && $table_result['resultat'] === 1 )
			return true;
		
		if( $debug )
			echo $api_debug_html;
		
		return false;
	}
	
	/**
	 * Set cookie to remember valid code
	 * 
	 */
	private function set_cookie( $code ) {
		$path = '';
		if( $this->_options['same_code'] && !$this->_id_number )
			$path = '/';
		
		setcookie( 'mediapay_code', $code, 0, $path );
	}
	
	/**
	 * overloaded
	 * Affiche une page d'option de plugin standard dans le menu Réglages de l'admin de WordPress
	 *
	 * @see trunk/inc/YD_Plugin#plugin_options()
	 */
	function plugin_options() {
	
		/** réservé aux administrateurs **/
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
		$op = new YD_OptionPage( &$this );
		if ( $this->option_page_title ) {
			$op->title = $this->option_page_title;
		} else {
			$op->title = __( $this->plugin_name, $this->tdomain );
		}
		$op->sanitized_name = $this->sanitized_name;
		$op->yd_logo = 'http://www.yann.com/' . $this->sanitized_name . '-logo.gif';
		$op->support_url = $this->support_url;
		$op->initial_funding = $this->initial_funding; 			// array( name, url )
		$op->additional_funding = $this->additional_funding;	// array of arrays
		$op->version = $this->version;
		$op->translations = $this->translations;
		$op->plugin_dir = $this->plugin_dir;
		$op->has_cache = $this->has_cache;
		$op->option_page_text = $this->option_page_text;
		$op->plg_tdomain = $this->tdomain;
		$op->donate_block = $this->op_donate_block;
		$op->credit_block = $this->op_credit_block;
		$op->support_block = $this->op_support_block;
		$this->option_field_labels['disable_backlink'] = 'Disable backlink in the blog footer:';
		$op->option_field_labels = $this->option_field_labels;
		$op->form_add_actions = $this->form_add_actions;
		$op->form_method =  $this->form_method;
		if( $_GET['do'] || $_POST['do'] ) $op->do_action( &$this );
		$op->header();
		$op->option_values = get_option( $this->option_key );
		//$op->sidebar();
		$op->form_header();
	  	foreach( $this->form_blocks as $block_name => $block_fields ) {
  			$op->form_block( $block_name, $block_fields );
  		}
  		foreach( $this->form_add_actions as $action_name => $action ) {
  			$op->form_action_button( $action_name, $action );
  		}
  		
  		$this->display_page();
  		
  		//$op->form_block( 'Other options:', array( 'disable_backlink' => 'bool' ) );
		$op->form_footer();
		if( $this->has_cron ) $op->cron_status( $this->crontab );
		$op->footer();
	}
	
	/**
	 * Met en place l'affichage de l'outil, appelé depuis le template WP
	 *
	 */
	private function display_page() {
	
		include( dirname( __FILE__) . '/display-settings.inc.php' );
	
	}
}
?>