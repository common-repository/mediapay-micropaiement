<?php
// ACTION curl mode POST
// URLSITE : nom de domaine du site 
// TARIFS_PAYS : valeurs disponnibles( "TARIF_AUDIO_FR" , "TARIF_AUDIO_BE")
// IDENTIFIANT : media-pay ==> menu: Mes Sites  -> Liste des pages protégées , colonne  "Num d'identification" 

// REPONSE
// resultat : "1" c'est  bon
// tarif : cout TTC de la communication (90s ou 30s)
// tarif_appel : cout de l'appel TTC
// tarif_minute : cout de la minute TTC
// nb_codes : nombre de code demandes
// numero_audiotel : numero audiotel
//
// resultat : "0" , message d'erreur dans cause
// cause : message erreur
	
if( $debug )
	$api_debug_html = '<div class="debug yd" style="border: 2px dashed blue;padding: 15px;color:grey;"><h3>Debug MediaPay</h3><p>Début API MediaPay get_phonenumber...</p>';

// URL media-pay pour infos
$URLP = "http://www.media-pay.fr/mediapay_api/get_phonenumber.php";

$RESULTAT = wp_remote_retrieve_body( wp_remote_post( $URLP, array( 'method' => 'POST', 'body' => $TAB_DATA ) ) );

if ((!$RESULTAT) || (!strlen($RESULTAT))) {
	
	$erreur = 'erreur';
	$table_result = array();

} else {

	// La variable $RESULTAT  retourne un tableau sérialisé
	$table_result = unserialize($RESULTAT);
		if (!is_array($table_result)) {
		$table_result = array();
	}
}

if( $debug ) {
	$api_debug_html .= "<pre>Dump réponse:\n";
	$api_debug_html .= var_export($table_result, true);
	$api_debug_html .= '</pre>';
	$api_debug_html .= '<p>...fin API MediaPay</p></div>';
}
?>