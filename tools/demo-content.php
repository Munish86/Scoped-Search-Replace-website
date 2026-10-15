<?php
/**
 * Sample content for the screenshot demo site (http://localhost/srdemo): a small shop whose pages still link to
 * its old http:// domain. Used only to take real screenshots of the Scoped Search & Replace plugin.
 *
 * Usage: tools/wp-demo.sh eval-file tools/demo-content.php --user=demo   (re-running resets the content)
 *
 * @package scoped-sr
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit;
}

$old = 'http://harbour-coffee.example';

// Start clean: remove every post, page and the default content.
foreach ( get_posts( array( 'post_type' => array( 'post', 'page' ), 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) ) as $sr_id ) {
	wp_delete_post( $sr_id, true );
}

$items = array(
	array( 'page', 'publish', 'About us', "<p>Harbour Coffee Roasters started in 2014 with one small roaster. Read <a href=\"$old/our-story/\">our story</a> or visit the <a href=\"$old/shop/\">shop</a>.</p>" ),
	array( 'page', 'publish', 'Wholesale', "<p>We supply cafés and offices. Download the <a href=\"$old/wp-content/uploads/wholesale-price-list.pdf\">price list</a>.</p><p>Ready to order? Use the <a href=\"$old/wholesale/order/\">order form</a> or see <a href=\"$old/delivery/\">delivery times</a>.</p>" ),
	array( 'page', 'publish', 'Delivery and returns', "<p>Free UK delivery over £30. Full details on <a href=\"$old/delivery/\">delivery</a> and <a href=\"$old/returns/\">returns</a>.</p>" ),
	array( 'page', 'publish', 'Contact', "<p>Questions? Use the form below, or see the <a href=\"$old/faq/\">FAQ</a>.</p>" ),
	array( 'page', 'publish', 'Brew guides', "<p>Step-by-step guides for <a href=\"$old/brew/v60/\">V60</a>, <a href=\"$old/brew/aeropress/\">AeroPress</a> and <a href=\"$old/brew/french-press/\">French press</a>.</p><img src=\"$old/wp-content/uploads/brew-guides.jpg\" alt=\"Brewing coffee\">" ),
	array( 'page', 'draft', 'Christmas gift guide', "<p>Gift boxes are in the <a href=\"$old/shop/gifts/\">gift shop</a>.</p>" ),
	array( 'post', 'publish', 'New single origin: Ethiopia Guji', "<p>Our new natural-process Ethiopian coffee is in the <a href=\"$old/shop/ethiopia-guji/\">shop</a> now.</p>" ),
	array( 'post', 'publish', 'How we roast', "<p>Small batches, roasted to order. More in <a href=\"$old/our-story/\">our story</a>.</p>" ),
	array( 'post', 'private', 'Supplier notes', "<p>Green coffee orders: see <a href=\"$old/wholesale/\">wholesale</a>.</p>" ),
);

$ids = array();
foreach ( $items as $i => list( $type, $status, $title, $content ) ) {
	$ids[ $title ] = wp_insert_post(
		array(
			'post_type'    => $type,
			'post_status'  => $status,
			'post_title'   => $title,
			'post_content' => $content,
			'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '2026-09-01' ) + $i * DAY_IN_SECONDS ),
		)
	);
}

// Custom fields: a plain URL and a serialized array, as page builders and theme options store them.
update_post_meta( $ids['About us'], 'hero_button_link', "$old/shop/" );
update_post_meta( $ids['Wholesale'], 'hero_button_link', "$old/wholesale/order/" );
update_post_meta(
	$ids['Brew guides'],
	'_layout_settings',
	array(
		'header' => array( 'logo' => "$old/wp-content/uploads/logo.png", 'link' => "$old/" ),
		'cta'    => array( 'text' => 'Shop coffee', 'url' => "$old/shop/" ),
	)
);
update_option( 'page_on_front', 0 );

WP_CLI::success( count( $ids ) . ' demo items created.' );
