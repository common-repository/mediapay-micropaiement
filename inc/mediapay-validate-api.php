<?php
// ACTION curl mode POST
// IDENTIFIANT : media-pay ==> menu: Mes Sites  -> Liste des pages protégées , colonne  "Num d'identification"
// URL_PAGE : Url de la page d'accès déclare sur la page, http://domaine/page.php
// CODE : il peut y avoir plusieurs codes, voir config donc si un seul code-> CODE[0]=xx, CODE[1]=xddd

// REPONSE
// resultat : "1" code valide
// resultat : "0" , message d'erreur dans cause
// cause : message erreur

if( $debug )
	$api_debug_html = '<div class="debug yd" style="border: 2px dashed blue;padding: 15px;color:grey;"><h3>Debug MediaPay</h3><p>Début API MediaPay validate...</p>';

// URL media-pay pour valider le code
$URLP = "http://www.media-pay.fr/mediapay_api/validate_code.php";

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
	$api_debug_html .= "<pre>Dump requête:\n";
	$api_debug_html .= var_export($TAB_DATA, true);
	$api_debug_html .= "<pre>Dump réponse:\n";
	$api_debug_html .= var_export($table_result, true);
	$api_debug_html .= '</pre>';
	$api_debug_html .= '<p>...fin API MediaPay</p></div>';
}
?>