<?php
/**
 * Plugin Name: Scoped Search & Replace site
 * Description: Contact form (saved in WordPress, exported as CSV), the plugin download, and one switch that turns every "Download free" button into a WordPress.org link once the plugin is listed there. Also page meta and software schema for search engines.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Scoped Search & Replace
 * License: GPL-2.0-or-later
 * Text Domain: scoped-sr-site
 *
 * @package scoped-sr-site
 */

defined( 'ABSPATH' ) || exit;

define( 'SRS_DB_VERSION', '1' );
define( 'SRS_FILE', __FILE__ );

require __DIR__ . '/includes/settings.php';
require __DIR__ . '/includes/leads.php';
require __DIR__ . '/includes/admin.php';
require __DIR__ . '/includes/cta.php';
require __DIR__ . '/includes/meta.php';

register_activation_hook( __FILE__, 'srs_install' );

// Also creates or upgrades the table when the plugin was copied in without being re-activated (e.g. after a migration).
add_action(
	'plugins_loaded',
	function () {
		if ( get_option( 'srs_db_version' ) !== SRS_DB_VERSION ) {
			srs_install();
		}
	}
);
