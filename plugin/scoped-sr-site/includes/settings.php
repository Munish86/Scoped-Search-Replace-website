<?php
/**
 * Settings > Scoped Search & Replace site: WordPress.org URL, plugin version and download, support and notification emails.
 *
 * @package scoped-sr-site
 */

defined( 'ABSPATH' ) || exit;

/** Defaults for every setting. */
function srs_defaults() {
	return array(
		'wporg_url'     => '', // WordPress.org plugin page; empty = not listed yet, buttons go to /download/.
		'live_label'    => 'Get it on WordPress.org',
		'version'       => '1.1.0',
		'download_url'  => '', // Empty = the ZIP bundled in this plugin's download/ folder.
		'github_url'    => 'https://github.com/Munish86/WordPress-Search-Replace-Tool',
		'support_email' => '',
		'notify_email'  => '', // Where new messages are emailed; empty = the site admin email.
	);
}

/**
 * One setting's value.
 *
 * @param string $key Setting name from srs_defaults().
 * @return string
 */
function srs_setting( $key ) {
	$all = wp_parse_args( (array) get_option( 'srs_settings', array() ), srs_defaults() );
	return isset( $all[ $key ] ) ? (string) $all[ $key ] : '';
}

/** Path of the bundled plugin ZIP. */
function srs_bundled_zip() {
	return plugin_dir_path( SRS_FILE ) . 'download/scoped-search-replace.zip';
}

/** Where the "Download ZIP" buttons point: the setting, else the bundled ZIP, else GitHub. */
function srs_download_url() {
	$url = srs_setting( 'download_url' );
	if ( '' !== $url ) {
		return $url;
	}
	if ( is_file( srs_bundled_zip() ) ) {
		// The file time makes browsers and caches fetch a replaced ZIP instead of an old copy.
		return add_query_arg( 'v', (string) filemtime( srs_bundled_zip() ), plugin_dir_url( SRS_FILE ) . 'download/scoped-search-replace.zip' );
	}
	return srs_setting( 'github_url' );
}

/**
 * Cleans the settings form.
 *
 * @param mixed $input Posted values.
 * @return array
 */
function srs_sanitize_settings( $input ) {
	$input = is_array( $input ) ? $input : array();
	$out   = srs_defaults();

	foreach ( array( 'wporg_url', 'download_url', 'github_url' ) as $key ) {
		$url = isset( $input[ $key ] ) ? esc_url_raw( trim( $input[ $key ] ), array( 'https' ) ) : '';
		if ( '' !== $url && ! wp_http_validate_url( $url ) ) {
			add_settings_error( 'srs_settings', 'srs_' . $key, __( 'Links must be full https:// addresses.', 'scoped-sr-site' ) );
			$url = '';
		}
		$out[ $key ] = $url;
	}

	$label             = isset( $input['live_label'] ) ? sanitize_text_field( $input['live_label'] ) : '';
	$out['live_label'] = '' !== $label ? mb_substr( $label, 0, 60 ) : srs_defaults()['live_label'];

	$version        = isset( $input['version'] ) ? sanitize_text_field( $input['version'] ) : '';
	$out['version'] = preg_match( '/^\d+(\.\d+){1,3}$/', $version ) ? $version : srs_defaults()['version'];

	foreach ( array( 'support_email', 'notify_email' ) as $key ) {
		$email       = isset( $input[ $key ] ) ? sanitize_email( $input[ $key ] ) : '';
		$out[ $key ] = is_email( $email ) ? $email : '';
	}
	return $out;
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'srs_settings',
			'srs_settings',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'srs_sanitize_settings',
				'default'           => srs_defaults(),
			)
		);
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Scoped Search & Replace site', 'scoped-sr-site' ),
			__( 'Scoped Search & Replace site', 'scoped-sr-site' ),
			'manage_options',
			'srs-settings',
			'srs_render_settings_page'
		);
	}
);

/**
 * One text/url/email row of the settings table.
 *
 * @param array  $s     Current settings.
 * @param string $key   Setting name.
 * @param string $label Row label.
 * @param string $type  Input type.
 * @param string $help  Description under the field.
 */
function srs_settings_row( $s, $key, $label, $type, $help = '' ) {
	?>
	<tr>
		<th scope="row"><label for="srs-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
		<td>
			<input name="srs_settings[<?php echo esc_attr( $key ); ?>]" id="srs-<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $type ); ?>" class="regular-text" value="<?php echo esc_attr( $s[ $key ] ); ?>">
			<?php if ( $help ) : ?>
				<p class="description"><?php echo esc_html( $help ); ?></p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}

/** Settings page. */
function srs_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$s = wp_parse_args( (array) get_option( 'srs_settings', array() ), srs_defaults() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Scoped Search & Replace site', 'scoped-sr-site' ); ?></h1>
		<?php settings_errors( 'srs_settings' ); ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'srs_settings' ); ?>
			<table class="form-table" role="presentation">
				<?php
				srs_settings_row( $s, 'wporg_url', __( 'WordPress.org plugin page', 'scoped-sr-site' ), 'url', __( 'Leave empty until the plugin is approved (it will be https://wordpress.org/plugins/scoped-search-replace/). Once filled in, every button with the class "sr-cta" (header, home page, call-to-action bands) links there and shows the label below instead of "Download free".', 'scoped-sr-site' ) );
				srs_settings_row( $s, 'live_label', __( 'Button label once listed', 'scoped-sr-site' ), 'text' );
				srs_settings_row( $s, 'version', __( 'Current plugin version', 'scoped-sr-site' ), 'text', __( 'Shown on the "Download ZIP" buttons. Change it together with the ZIP.', 'scoped-sr-site' ) );
				srs_settings_row( $s, 'download_url', __( 'ZIP download link', 'scoped-sr-site' ), 'url', __( "Empty: the ZIP in the site plugin's download/ folder (replace that file for a new version). Or a full link, such as a GitHub release or the WordPress.org download.", 'scoped-sr-site' ) );
				srs_settings_row( $s, 'github_url', __( 'Source code (GitHub)', 'scoped-sr-site' ), 'url' );
				srs_settings_row( $s, 'support_email', __( 'Support email', 'scoped-sr-site' ), 'email', __( 'Shown wherever a page uses [sr_support_email]. Empty: a link to the contact page is shown instead.', 'scoped-sr-site' ) );
				srs_settings_row( $s, 'notify_email', __( 'Email new messages to', 'scoped-sr-site' ), 'email', __( 'Empty: the site admin email. Every message is saved under Messages either way, even when email sending does not work (as on a local computer).', 'scoped-sr-site' ) );
				?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
