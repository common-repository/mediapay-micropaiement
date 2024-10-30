<?php
?>
<!-- /** bloc d'admin WP standard ( div postbox / h3 hndle / div inside / p ) **/ -->
<div class="postbox" id="latest_transact">
	<h3 class="hndle">Choix des tarifs et pays</h3>
	<div class="inside">
		<?php 
		/** MediaPay API **/
		$TAB_DATA['URLSITE'] 		= parse_url( site_url(), PHP_URL_HOST );
		$TAB_DATA['TARIFS_PAYS'] 	= "TARIFS_ALL";
		$TAB_DATA['IDENTIFIANT'] 	= $this->_options['mediapay_id'];
		$debug 						= $this->_options['debug_api'];
		$inc = dirname( __FILE__) . '/mediapay-phonenumber-api.inc.php';
		include_once( $inc );
		/** ------------ **/
		
		if( $table_result['resultat'] )
			$paywall_html .= $this->display_available_countries( $table_result );
		
		if( $debug )
			$paywall_html .= $api_debug_html;
		
		echo $paywall_html;
		?>

		<p style="float: right">
			<a href="#top">^haut</a>
		</p>
	</div>
	<!--  // / inside -->
</div>
<!-- 	// / postbox -->