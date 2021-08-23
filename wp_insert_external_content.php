<?php
/*
Plugin Name:       WP H-Insert External Content
Plugin URI:        https://herbrand.org/wordpress/eigene-plugins/wp-h-insert-external-content/
Description:       Plugin zum Einbinden externer Inhalte in WordPress. Nach der Aktivierung k&ouml;nnen externe Inhalte in Seiten bzw. Beitr&auml;gen integriert werden. Dazu wird im Inhaltsbereich mit dem WordPress-Editor folgender Shortcode eingef&uuml;gt: [wpiec]URL[/wpiec]  (URL ist durch die richtige Web-Adresse zu ersetzen).
Author:            Hans M. Herbrand
Author URI:        https://herbrand.org
Version:           1.6
Date:              2021-08-23
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
GitHub Plugin URI: https://github.com/m266/wp-insert-external-content
 */

// Block external access
defined('ABSPATH') || exit();

//////////////////////////////////////////////////////////////////////////////////////////

// Erinnerung an Git Updater 
register_activation_hook( __FILE__, 'wpiec_activate' ); // Funktions-Name anpassen
function wpiec_activate() { // Funktions-Name anpassen
$to = get_option('admin_email');
$subject = 'Plugin "WP H-Insert External Content"'; // Plugin-Name anpassen
$message = 'Falls nicht vorhanden:
Bitte das Plugin "Git Updater" hier https://herbrand.org/tutorials/github/git-updater/ herunterladen, 
installieren und aktivieren, um weiterhin Updates zu erhalten!';
wp_mail($to, $subject, $message );
}

//////////////////////////////////////////////////////////////////////////////////////////
/* Externe HTML-Seite einfügen
 * Shortcode [wpiec]URL[/wpiec] in Seite/Beitrag einfügen
*/
function wpiec_plugin( $atts = array(), $content = null ) {
    $content = file_get_contents($content);
    return $content;
}
add_shortcode( 'wpiec', 'wpiec_plugin' );
?>