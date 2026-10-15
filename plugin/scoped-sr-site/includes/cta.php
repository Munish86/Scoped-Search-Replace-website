<?php
/**
 * Button switches, so pages never hold a hard-coded download link:
 * - "sr-cta": keeps its own link (Download free -> /download/) until a WordPress.org URL is set in
 *   Settings > Scoped Search & Replace site; then it links there with the "listed" label.
 * - "sr-download": always links to the ZIP (srs_download_url()) and is labelled with the current version.
 * - "sr-github": links to the source code URL from the settings.
 *
 * @package scoped-sr-site
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sets the link (and optionally the text) of a rendered button block.
 *
 * @param string      $html  Button HTML.
 * @param string      $url   New link.
 * @param string|null $label New text, or null to keep it.
 * @return string
 */
function srs_button_link( $html, $url, $label = null ) {
	$p = new WP_HTML_Tag_Processor( $html );
	if ( $p->next_tag( 'a' ) ) {
		$p->set_attribute( 'href', esc_url( $url ) );
		$p->set_attribute( 'rel', 'noopener' );
	}
	$html = $p->get_updated_html();
	if ( null === $label ) {
		return $html;
	}
	$label = esc_html( $label );
	return preg_replace_callback(
		'#(<a\b[^>]*>)(.*?)(</a>)#s',
		function ( $m ) use ( $label ) {
			return $m[1] . $label . $m[3];
		},
		$html,
		1
	);
}

add_filter(
	'render_block_core/button',
	function ( $html, $block ) {
		$class = ' ' . ( isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '' ) . ' ';
		if ( false !== strpos( $class, ' sr-download ' ) ) {
			/* translators: %s: plugin version. */
			return srs_button_link( $html, srs_download_url(), sprintf( __( 'Download version %s (ZIP)', 'scoped-sr-site' ), srs_setting( 'version' ) ) );
		}
		if ( false !== strpos( $class, ' sr-github ' ) && srs_setting( 'github_url' ) ) {
			return srs_button_link( $html, srs_setting( 'github_url' ) );
		}
		if ( false !== strpos( $class, ' sr-cta ' ) && srs_setting( 'wporg_url' ) ) {
			return srs_button_link( $html, srs_setting( 'wporg_url' ), srs_setting( 'live_label' ) );
		}
		return $html;
	},
	10,
	2
);
