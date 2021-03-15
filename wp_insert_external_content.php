<?php
/*
Plugin Name:       WP H-Insert External Content
Plugin URI:        https://github.com/m266/wp-insert-external-content
Description:       Plugin zum Einbinden externer Inhalte in WordPress. Nach der Aktivierung k&ouml;nnen externe Inhalte in Seiten bzw. Beitr&auml;gen integriert werden. Dazu wird im Inhaltsbereich mit dem WordPress-Editor folgender Shortcode eingef&uuml;gt: [wpiec]URL[/wpiec]  (URL ist durch die richtige Web-Adresse zu ersetzen). Update- und Alarm-Intervall lassen sich ab Zeile 40 anpassen.
Author:            Hans M. Herbrand
Author URI:        https://www.web266.de
Version:           1.4
Date:              2021-03-15
License:           GNU General Public License v2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html
Credits:           Daniel Gruber, http://zeit-zu-handeln.net/?p=739
GitHub Plugin URI: https://github.com/m266/wp-insert-external-content
 */

// Block external access
defined('ABSPATH') || exit();

//////////////////////////////////////////////////////////////////////////////////////////
// Check GitHub Updater aktiv
// Anpassungen Plugin-Name und Funktions-Name vornehmen
if (!function_exists('is_plugin_inactive')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}
if (is_plugin_inactive('github-updater/github-updater.php')) {
// E-Mail an Admin senden, wenn inaktiv
register_activation_hook( __FILE__, 'wpiec_activate' ); // Funktions-Name anpassen
function wpiec_activate() { // Funktions-Name anpassen
$to = get_option('admin_email');
$subject = 'Plugin "WP H-Insert External Content"'; // Plugin-Name anpassen
$message = 'Bitte das Plugin "GitHub Updater" hier https://web266.de/tutorials/github/github-updater/ herunterladen, installieren und aktivieren, um weiterhin Updates zu erhalten!';
wp_mail($to, $subject, $message );
}
}

//////////////////////////////////////////////////////////////////////////////////////////

// Zeit-Definition
$wpiec_intervall = (60 * 60); // Update-Intervall: Zeit in Sekunden [Standard 1 Std (60*60)]
$wpiec_alarm = (24 * 60 * 60); // Alarm-Intervall: Zeit in Sekunden [Standard 1 Tag (24*60*60)]

class wpiec {

/**
 * Constructor.
 */
    function __construct() {
// empty for now
    }
    function displayShortcode($atts, $content = null) {
        // Variablen als Global deklarieren
        global $wpiec_intervall;
        global $wpiec_alarm;

        extract(shortcode_atts(array('pattern' => '#(.*)#s', 'before' => '', 'after' => '', ), $atts));
        if ($websitecontent = @file($content)) {
            $data = join("", $websitecontent);
        }
        $before = str_replace('{', '<', $before);
        $before = str_replace('}', '>', $before);
        $before = str_replace('°', '"', $before);
        $after = str_replace('{', '<', $after);
        $after = str_replace('}', '>', $after);
        $after = str_replace('°', '"', $after);
        $pattern = str_replace('{', '<', $pattern);
        $pattern = str_replace('}', '>', $pattern);
        $pattern = str_replace('°', '"', $pattern);
        $ID = md5($pattern . $content);
        $db = get_option($ID);
// Meldung an Admin über Ausfall des verlinkten Contents
        if ((time() - $db[0]) > ($wpiec_alarm) && $websitecontent == false && $db[2] != true) {
            wp_mail(get_option("admin_email"), "Warnung: Veralteter Inhalt - Website nicht erreichbar", "Dies ist eine Mail des Wordpress-Plugins WP Insert External Content zum Einbinden und Filtern von Inhalten aus externen Websites. Die betroffene Website ist: " . get_option("blogname") . " (" . get_option("siteurl") . "). Die Website " . $content . " ist aktuell nicht mehr erreichbar. Die gecachte Version ist u. U. veraltet!");
            update_option($ID, array($db[0], $db[1], true));
        }
// Update-Intervall Content
        if ((!$db || $db[0] + $wpiec_intervall < time()) && $websitecontent != false) {
            preg_match($pattern, $data, $matches);
            preg_match('#(https?://[^/]*)/#', $content, $matches2);
            $base_url = $matches2[1] . "/";
            $matches[1] = preg_replace('#href="\.?/#', 'href="' . $base_url, $matches[1]);
            preg_match('#(.*/)#', $content, $matches1);
            $url = $matches1[1];
            $matches[1] = preg_replace('#href="(?!https?://|ftp://|mailto:|news:|\#)([^"]*)"#', 'href="' . $url . '${1}"', $matches[1]);
            if (!$db) {
                add_option($ID, array(time(), $matches[1]));
            } else {
                update_option($ID, array(time(), $matches[1]));
            }
        } else {
            $matches[1] = $db[1];
        }
        return $before . $matches[1] . $after;
    }
}
$wpiec = new wpiec();
add_shortcode('wpiec', array($wpiec, 'displayShortcode'));
?>