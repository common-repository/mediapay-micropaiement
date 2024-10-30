<?php
/**
 * @package YD-mediapay-wp-plugin
 * @author Yann Dubois 
 * @version 0.1.2
 */

/*
 Plugin Name: YD MediaPay WordPress Plugin
 Plugin URI: http://www.yann.com/en/wp-plugins/mediapay-micropaiement
 Description: Permet de monétiser l'accès aux contenus du site ou l'inscription des membres via la plateforme de micro-paiements par téléphone MediaPay.
 Version: 0.1.2
 Author: Yann Dubois
 Author URI: http://www.yann.com/
 License: GPL2
 */

/**
 * @copyright 2012  Yann Dubois  ( email : yann _at_ abc.fr )
 *
 *  Original development of this plugin was kindly funded by www.media-pay.fr
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/**
 *  
 Revision 0.1.2:
 - Small bugfix
 - Documentation corrections
 Revision 0.1.1:
 - Documentation upgrade
 Revision 0.1.0:
 - Original alpha release 00
 */

/** Class includes **/
include_once( dirname( __FILE__ ) . '/inc/yd-widget-framework.inc.php' );		// standard framework VERSION 20110405-01 or better
include_once( dirname( __FILE__ ) . '/inc/mediapay.inc.php' );					// custom classes

/**
 * Just fill up necessary settings in the configuration array
 * to create a new custom plugin instance...
 * 
 */
global $ydmpp_o;
$ydmpp_o = new ydmpPlugin(
	array(
		'name' 				=> 'MediaPay',
		'version'			=> '0.1.2',
		'has_option_page'	=> true,
		'option_page_title' => 'Configuration de MediaPay',
		'op_donate_block'	=> false,
		'op_credit_block'	=> false,
		'op_support_block'	=> false,
		'has_toplevel_menu'	=> false,
		'has_shortcode'		=> false,
		'shortcode'			=> '',
		'has_widget'		=> false,
		'widget_class'		=> '',
		'has_cron'			=> false,
		'crontab'			=> array(
			//'daily'		=> array( 'YD_MiscWidget', 'daily_update' ),
			//'hourly'		=> array( 'YD_MiscWidget', 'hourly_update' )
		),
		'has_stylesheet'	=> false,
		'stylesheet_file'	=> '',
		'has_translation'	=> true,
		'translation_domain'=> 'ydmpp', // must be copied in the widget class!!!
		'translations'		=> array(
			array( 'French', 'Yann Dubois', 'http://www.yann.com/' ),
		),		
		'initial_funding'	=> array( 'MediaPay', 'www.media-pay.fr' ),
		'additional_funding'=> array(),
		'form_blocks'		=> array(
			'Main options' => array(
				'mediapay_id'			=> 'text',
				'monetize_articles'		=> 'bool',
				'monetize_pages'		=> 'bool',
				'show_excerpt'			=> 'bool',
				'remember_codes'		=> 'bool',
				'same_code'				=> 'bool',
				'monetize_registration'	=> 'bool',
				'use_stylesheet'		=> 'bool',
				'countries'				=> 'hidden',
				'debug_api'				=> 'bool'
			)
		),
		'option_field_labels'=>array(
				'mediapay_id'			=> 'Votre identifiant MediaPay',
				'monetize_articles'		=> 'Monétiser tous les articles',
				'monetize_pages'		=> 'Monétiser toutes les pages',
				'show_excerpt'			=> 'Afficher gratuitement le début des contenus monétisés',
				'remember_codes'		=> 'Se souvenir des codes pendant toute la session',
				'same_code'				=> 'Utiliser le même code pour tout le site',
				'monetize_registration'	=> 'Monétiser l\'inscription',
				'use_stylesheet'		=> 'Utiliser la feuille de style Media Pay pour l\'habillage',
				'countries'				=> '',
				'debug_api'				=> 'Mode débogage pour l\'APi MediaPay'
		),
		'option_defaults'	=> array(
				'mediapay_id'			=> '',
				'monetize_articles'		=> false,
				'monetize_pages'		=> false,
				'show_excerpt'			=> true,
				'remember_codes'		=> true,
				'same_code'				=> true,
				'monetize_registration'	=> false,
				'use_stylesheet'		=> true,
				'countries'				=> '',
				'debug_api'				=> false
		),
		'form_add_actions'	=> array(
				//'Manually run hourly process'	=> array( 'YD_MiscWidget', 'hourly_update' ),
				//'Check latest'				=> array( 'YD_MiscWidget', 'check_update' )
		),
		'has_cache'			=> false,
		'option_page_text'	=> 	'Si vous utilisez le même code pour tout le site (option par défaut), ' .
								'entrez une étoile (*) dans les 3 champs d\'URL de votre ' .
								'interface de configuration Media-Pay.',
		'backlinkware_text' => '',
		'plugin_file'		=> __FILE__,
		'has_activation_notice'	=> false,
		'activation_notice' => '',
		'form_method'		=> 'post'
 	)
);
?>