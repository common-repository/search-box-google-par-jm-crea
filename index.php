<?php
/**
 * Plugin Name: Search Box Google par JM Créa
 * Plugin URI: http://www.jm-crea.com
 * Description: Intégrez un moteur de recherche à votre site dans les résultats Google
 * Version: 1.7
 * Author: JM Crea
 * Author URI: http://www.jm-crea.com
 */

//On créé le menu
function menu_sbg() {
add_submenu_page( 'tools.php', 'Search Box Google', 'Search Box Google', 'manage_options', 'sbg', 'sbg_form' ); 
}
add_action('admin_menu', 'menu_sbg');

//On créé la table mysql
function creer_table_sbg() {
global $wpdb;
$table_sbg = $wpdb->prefix . 'sbg';
$sql = "CREATE TABLE IF NOT EXISTS $table_sbg (
id_sbg int(11) NOT NULL AUTO_INCREMENT,
sbg_actif text DEFAULT NULL,
sbg_context text DEFAULT NULL,
sbg_type text DEFAULT NULL,
sbg_url text DEFAULT NULL,
sbg_typeaction text DEFAULT NULL,
sbg_target text DEFAULT NULL,
sbg_query text DEFAULT NULL,
UNIQUE KEY id (id_sbg)
);";
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}

//On insere les infos dans la table
function insert_table_sbg() {
global $wpdb;
$table_sbg = $wpdb->prefix . 'sbg';
$wpdb->insert( 
$table_sbg, 
array('id_sbg'=>' ','sbg_actif'=>'ON','sbg_context'=>'http://schema.org','sbg_type'=>'WebSite','sbg_url'=>'' . site_url() . '','sbg_typeaction'=>'SearchAction','sbg_target'=>'' . site_url() . '/?s={search_term}','sbg_query'=>'required name=search_term'), 
array('%s','%s','%s','%s','%s','%s','%s','%s')
);
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}

register_activation_hook( __FILE__, 'creer_table_sbg' );
register_activation_hook( __FILE__, 'insert_table_sbg' );


//Affichage du formulaire
function sbg_form() {

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
global $wpdb;
$table_sbg = $wpdb->prefix . "sbg";
$voir_sbg = $wpdb->get_row("SELECT * FROM $table_sbg WHERE id_sbg='1'");


echo "<h1>Search Box Google par JM Créa</h1>
<h2>Intégrez un mini moteur de recherche de votre site dans les résultats Google.</h2>";


if (isset($_GET['action'])&&($_GET['action'] == 'maj-ok')) {
echo '<div class="updated"><p>Search Box Google mis à jour avec succès !</p></div>';		
}
echo "<div style='position:relative;background-color:#FFF;padding:8px;border:#CCC solid 1px;margin-top:20px; width:700px;'>";
echo "
<form id='form1' name='form1' method='post' action=''>
<table border='0' cellspacing='8' cellpadding='0'>
<tr>
<td colspan='3'><h2>Paramétrage</h2></td>
</tr>
<tr>
<td>Activer ou désactiver Google Search Box :</td>
<td>";

if ($voir_sbg->sbg_actif == 'ON') {
echo "
<input type='radio' name='sbg_actif' id='radio' value='ON' checked='checked' /> ON 
<input type='radio' name='sbg_actif' id='radio2' value='OFF' /> OFF ";
}
else {
echo "
<input type='radio' name='sbg_actif' id='radio' value='ON' /> ON 
<input type='radio' name='sbg_actif' id='radio2' value='OFF' checked='checked' /> OFF ";	
}
echo "
</td>
</tr>
<tr>
<td>@context (par défaut : <code>http://schema.org</code>)</td>
<td><input name='sbg_context' type='text' id='sbg_context' value='" . $voir_sbg->sbg_context . "'></td>
</tr>
<tr>
<td>@type (par défaut : <code>WebSite</code>)</td>
<td><input name='sbg_type' type='text' id='sbg_type' value='" . $voir_sbg->sbg_type. "'></td>
</tr>
<tr>
<td>URL (par défaut : <code>" . site_url() . "</code>)</td>
<td><input type='text' name='sbg_url' id='sbg_url' value='" . $voir_sbg->sbg_url . "'></td>
</tr>
<tr>
<td>@type action (par défaut : <code>SearchAction</code>)</td>
<td><input type='text' name='sbg_typeaction' id='sbg_typeaction' value='" . $voir_sbg->sbg_typeaction . "'></td>
</tr>
<tr>
<td>Target (par défaut : <code>" . site_url() . "?s={search_term}</code>)</td>
<td><input type='text' name='sbg_target' id='sbg_target' value='" . $voir_sbg->sbg_target . "'></td>
</tr>
<tr>
<td>Query input (par défaut : <code>required name=search_term</code>)</td>
<td><input type='text' name='sbg_query' id='sbg_query' value='" . $voir_sbg->sbg_query. "'></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td colspan='3' align='right'><input type='submit' name='maj' id='maj' value='Mettre à jour' class='button button-primary' /></td>
</tr>
<tr>
<td colspan='3' align='right'>&nbsp;</td>
</tr>
</table>
</form>
</div>";

//Apercu du code javascript
echo "<div style='position:relative;background-color:#FFF;padding:8px;border:#CCC solid 1px;margin-top:20px; width:700px;'>";
echo '
<h1>Apercu de votre code Javascript</h1>
<textarea rows="12" cols="80" readonly="readonly">
<script type="application/ld+json">
{
"@context": "' . $voir_sbg->sbg_context . '",
"@type": "' . $voir_sbg->sbg_type . '",
"url": "' . $voir_sbg->sbg_url . '",
"potentialAction": {
"@type": "' . $voir_sbg->sbg_typeaction. '",
"target": "' . $voir_sbg->sbg_target . '",
"query-input": "' . $voir_sbg->sbg_query . '"
}
}
</script>
</textarea>
';
echo "</div>";

//On met à jour la table
if (isset($_POST['maj'])) {
$sbg_actif = stripslashes($_POST['sbg_actif']);
$sbg_context = stripslashes($_POST['sbg_context']);
$sbg_type = stripslashes($_POST['sbg_type']);
$sbg_url = stripslashes($_POST['sbg_url']);
$sbg_typeaction = stripslashes($_POST['sbg_typeaction']);
$sbg_target = stripslashes($_POST['sbg_target']);
$sbg_query = stripslashes($_POST['sbg_query']);

global $wpdb;
$table_sbg = $wpdb->prefix . "sbg";
$wpdb->query($wpdb->prepare("UPDATE $table_sbg SET sbg_actif='$sbg_actif',sbg_context='$sbg_context',sbg_type='$sbg_type',sbg_url='$sbg_url',sbg_typeaction='$sbg_typeaction',sbg_target='$sbg_target',sbg_query='$sbg_query' WHERE id_sbg='1'",APP_POST_TYPE )) or die(mysql_error());
echo '<script>document.location.href="tools.php?page=sbg&action=maj-ok"</script>';
}
}

//Fonction pour ajouter le module sur le site entre <head> et </head>
function ajouter_sbg() {
global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$table_sbg = $wpdb->prefix . "sbg";
$voir_sbg = $wpdb->get_row("SELECT * FROM $table_sbg WHERE id_sbg='1'");

dbDelta( $sql );

$afficher_sbg = 
'<!-- PLUGIN WORDPRESS SEARCH BOX GOOGLE PAR JM CREA -->
<script type="application/ld+json">
{
"@context": "' . $voir_sbg->sbg_context . '",
"@type": "' . $voir_sbg->sbg_type . '",
"url": "' . $voir_sbg->sbg_url . '",
"potentialAction": {
"@type": "' . $voir_sbg->sbg_typeaction . '",
"target": "' . $voir_sbg->sbg_target. '",
"query-input": "' . $voir_sbg->sbg_query . '"
}
}
</script>';

if ($voir_sbg->sbg_actif == 'ON') {
if (is_home() || is_front_page() ) {
echo $afficher_sbg . "\n";
}
}
}

add_action( 'wp_head', 'ajouter_sbg' );

function head_meta_sbg_jm_crea() {
echo("<meta name='Search Box Google par JM Créa' content='1.7' />\n");
}
add_action('wp_head', 'head_meta_sbg_jm_crea');
?>