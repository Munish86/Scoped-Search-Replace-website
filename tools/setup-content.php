<?php
/**
 * Puts the pages and blog posts from content/ into WordPress and sets the home page, blog page and privacy page.
 *
 * Usage (from search-replace-website/):
 *   tools/wp.sh eval-file tools/setup-content.php --user=sradmin            # only adds what is missing
 *   tools/wp.sh eval-file tools/setup-content.php force --user=sradmin      # also overwrites existing pages/posts
 *   tools/wp.sh eval-file tools/setup-content.php force only=pricing --user=sradmin
 *
 * Without "force", pages already in WordPress are left alone, so edits made in the WordPress editor are never lost.
 *
 * @package scoped-sr
 */

// phpcs:disable WordPress.WP.AlternativeFunctions -- CLI script reading local files.

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$sr_root  = dirname( __DIR__ ) . '/content/';
$sr_site  = require $sr_root . 'site.php';
$sr_force = in_array( 'force', $args, true );
$sr_only  = '';
foreach ( $args as $sr_arg ) {
	if ( 0 === strpos( $sr_arg, 'only=' ) ) {
		$sr_only = substr( $sr_arg, 5 );
	}
}
if ( ! current_user_can( 'unfiltered_html' ) ) {
	WP_CLI::error( 'Run with --user=<admin user> so page markup is saved as written.' );
}

/**
 * Reads a content file.
 *
 * @param string $root Content folder.
 * @param string $file Relative path; empty = empty content.
 * @return string
 */
function sr_read( $root, $file ) {
	if ( '' === $file ) {
		return '';
	}
	$text = file_get_contents( $root . $file );
	if ( false === $text ) {
		WP_CLI::error( "Missing content file: $file" );
	}
	return trim( $text ) . "\n";
}

/**
 * Creates or updates one post/page. Returns its id, or the existing id when it is kept.
 *
 * @param string $type  page|post.
 * @param string $slug  URL slug.
 * @param array  $data  Post fields.
 * @param bool   $force Overwrite when it exists.
 * @return int
 */
function sr_upsert( $type, $slug, $data, $force ) {
	$existing = get_page_by_path( $slug, OBJECT, $type );
	if ( $existing && ! $force ) {
		WP_CLI::log( "kept    $type $slug (already there; use force to overwrite)" );
		return (int) $existing->ID;
	}
	$data = array_merge(
		array(
			'post_type'      => $type,
			'post_status'    => 'publish',
			'post_name'      => $slug,
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
		),
		$data
	);
	if ( $existing ) {
		$data['ID'] = $existing->ID;
		$id         = wp_update_post( wp_slash( $data ), true );
	} else {
		$id = wp_insert_post( wp_slash( $data ), true );
	}
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( "$slug: " . $id->get_error_message() );
	}
	WP_CLI::log( ( $existing ? 'updated ' : 'created ' ) . "$type $slug" );
	return (int) $id;
}

/**
 * Adds a cover image from content/covers/ to the media library once and returns its id.
 *
 * @param string $root  Content folder.
 * @param string $file  File name in covers/.
 * @param string $alt   Alt text.
 * @return int
 */
function sr_cover( $root, $file, $alt ) {
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_rd_cover', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$path = $root . 'covers/' . $file;
	if ( ! is_file( $path ) ) {
		WP_CLI::warning( "No cover image $file" );
		return 0;
	}
	$tmp = wp_tempnam( $file );
	copy( $path, $tmp );
	$id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $alt );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$file: " . $id->get_error_message() );
		return 0;
	}
	update_post_meta( $id, '_rd_cover', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return (int) $id;
}

// ---- Pages ----
$sr_ids = array();
foreach ( $sr_site['pages'] as $sr_slug => $sr_page ) {
	if ( $sr_only && $sr_only !== $sr_slug ) {
		$found = get_page_by_path( $sr_slug );
		if ( $found ) {
			$sr_ids[ $sr_slug ] = (int) $found->ID;
		}
		continue;
	}
	$sr_id              = sr_upsert(
		'page',
		$sr_slug,
		array(
			'post_title'   => $sr_page['title'],
			'post_content' => sr_read( $sr_root, $sr_page['file'] ),
			'post_excerpt' => $sr_page['desc'],
		),
		$sr_force
	);
	$sr_ids[ $sr_slug ] = $sr_id;
	update_post_meta( $sr_id, '_wp_page_template', $sr_page['template'] ?? '' );
	update_post_meta( $sr_id, '_yoast_wpseo_metadesc', $sr_page['desc'] );
	update_post_meta( $sr_id, '_yoast_wpseo_title', 'home' === $sr_slug ? $sr_page['seo'] : $sr_page['seo'] . ' | Scoped Search & Replace' );
}

if ( isset( $sr_ids['home'], $sr_ids['blog'] ) ) {
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $sr_ids['home'] );
	update_option( 'page_for_posts', $sr_ids['blog'] );
}
if ( isset( $sr_ids['privacy-policy'] ) ) {
	update_option( 'wp_page_for_privacy_policy', $sr_ids['privacy-policy'] );
}

// ---- Posts ----
foreach ( $sr_site['posts'] as $sr_slug => $sr_post ) {
	if ( $sr_only && $sr_only !== $sr_slug ) {
		continue;
	}
	$sr_cat = (int) wp_create_category( $sr_post['category'] );
	$sr_id  = sr_upsert(
		'post',
		$sr_slug,
		array(
			'post_title'    => $sr_post['title'],
			'post_content'  => sr_read( $sr_root, $sr_post['file'] ),
			'post_excerpt'  => $sr_post['desc'],
			'post_date'     => $sr_post['date'],
			'post_category' => array( $sr_cat ),
		),
		$sr_force
	);
	update_post_meta( $sr_id, '_yoast_wpseo_metadesc', $sr_post['desc'] );
	if ( ! empty( $sr_post['cover'] ) ) {
		$sr_img = sr_cover( $sr_root, $sr_post['cover'], $sr_post['title'] );
		if ( $sr_img ) {
			set_post_thumbnail( $sr_id, $sr_img );
		}
	}
}

// The default category is "Guides" instead of "Uncategorized".
$sr_guides = get_term_by( 'name', 'Guides', 'category' );
if ( $sr_guides ) {
	update_option( 'default_category', $sr_guides->term_id );
	$sr_uncat = get_term_by( 'slug', 'uncategorized', 'category' );
	if ( $sr_uncat && 0 === (int) $sr_uncat->count ) {
		wp_delete_term( $sr_uncat->term_id, 'category' );
	}
}

// Yoast: share image for pages without their own, "|" between page title and site name.
if ( defined( 'WPSEO_VERSION' ) ) {
	$sr_social                     = (array) get_option( 'wpseo_social', array() );
	$sr_social['og_default_image'] = home_url( '/wp-content/themes/scoped-sr/assets/images/og-image.png' );
	update_option( 'wpseo_social', $sr_social );
	$sr_titles                 = (array) get_option( 'wpseo_titles', array() );
	$sr_titles['separator']    = 'sc-pipe';
	$sr_titles['company_name'] = 'Scoped Search & Replace';
	update_option( 'wpseo_titles', $sr_titles );
}

flush_rewrite_rules( false );
WP_CLI::success( 'Content is in place.' );
