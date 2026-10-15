<?php
/**
 * What tools/setup-content.php puts into WordPress. Each page/post body is a block-markup file next to this one.
 * "desc" becomes the page excerpt and the Yoast meta description (keep it under ~155 characters).
 *
 * @package scoped-sr
 */

return array(
	'pages' => array(
		'home'           => array(
			'title' => 'Scoped Search & Replace: safe search and replace for WordPress',
			'file'  => 'pages/home.html',
			'seo'   => 'Scoped Search & Replace: search and replace in WordPress, only where you choose',
			'desc'  => 'A free WordPress plugin to search, count and replace URLs or text in posts, pages and custom fields, limited to the field, post type, status or page you pick.',
		),
		'features'       => array(
			'title'    => 'Features',
			'file'     => 'pages/features.html',
			'template' => 'page-landing',
			'seo'      => 'Features: scoped search, counts, serialized-safe replace',
			'desc'     => 'Everything Scoped Search & Replace does: choose table, field, content type, status or one page; count matches; replace safely, including serialized data.',
		),
		'how-it-works'   => array(
			'title'    => 'How it works',
			'file'     => 'pages/how-it-works.html',
			'template' => 'page-landing',
			'seo'      => 'How it works: choose a scope, search, then replace',
			'desc'     => 'A walk through Scoped Search & Replace on a real site: pick where to look, count every match, check the list, then replace inside the same scope.',
		),
		'download'       => array(
			'title'    => 'Download',
			'file'     => 'pages/download.html',
			'template' => 'page-landing',
			'seo'      => 'Download Scoped Search & Replace (free)',
			'desc'     => 'Download Scoped Search & Replace, a free GPL WordPress plugin. Requires WordPress 5.8 and PHP 7.4. Install by uploading the ZIP under Plugins.',
		),
		'docs'           => array(
			'title' => 'Documentation',
			'file'  => 'pages/docs.html',
			'seo'   => 'Documentation: using Scoped Search & Replace',
			'desc'  => 'How to install and use Scoped Search & Replace: scopes, searching, replacing, serialized data, page builders, troubleshooting and uninstalling.',
		),
		'changelog'      => array(
			'title' => 'Changelog',
			'file'  => 'pages/changelog.html',
			'seo'   => 'Changelog',
			'desc'  => 'What changed in each version of the Scoped Search & Replace WordPress plugin.',
		),
		'contact'        => array(
			'title'    => 'Contact',
			'file'     => 'pages/contact.html',
			'template' => 'page-landing',
			'seo'      => 'Contact',
			'desc'     => 'A question about Scoped Search & Replace, a problem or an idea? Send a message; we reply within one working day.',
		),
		'privacy-policy' => array(
			'title' => 'Privacy policy',
			'file'  => 'pages/privacy-policy.html',
			'seo'   => 'Privacy policy',
			'desc'  => 'What this website and the Scoped Search & Replace plugin collect (very little), why, and how to have it deleted.',
		),
		'blog'           => array(
			'title' => 'Blog',
			'file'  => '',
			'seo'   => 'Blog: guides for safer WordPress changes',
			'desc'  => 'Guides for WordPress site owners: moving to a new domain, switching to https, page builder data and changing text across many pages safely.',
		),
	),
	'posts' => array(
		'change-wordpress-domain-in-the-database'    => array(
			'title'    => 'How to change your WordPress domain in the database (without breaking links)',
			'file'     => 'posts/change-wordpress-domain-in-the-database.html',
			'category' => 'Guides',
			'date'     => '2026-10-15 09:00:00',
			'cover'    => 'cover-domain.png',
			'desc'     => 'Moved to a new domain? Which settings to change, where the old address hides in the database, and how to replace it step by step without breaking data.',
		),
		'wordpress-http-to-https-fix-mixed-content'  => array(
			'title'    => 'Switching WordPress from http to https: fix the old links in your content',
			'file'     => 'posts/wordpress-http-to-https-fix-mixed-content.html',
			'category' => 'Guides',
			'date'     => '2026-10-14 09:00:00',
			'cover'    => 'cover-https.png',
			'desc'     => 'Your certificate works but the padlock is missing? Old http:// links in posts and custom fields cause mixed content. How to find and fix them.',
		),
		'search-replace-serialized-data-wordpress'   => array(
			'title'    => 'Why search and replace can break WordPress serialized data (and how to avoid it)',
			'file'     => 'posts/search-replace-serialized-data-wordpress.html',
			'category' => 'Guides',
			'date'     => '2026-10-13 09:00:00',
			'cover'    => 'cover-serialized.png',
			'desc'     => 'A plain SQL REPLACE can wipe page builder layouts and plugin settings. What serialized data is, why lengths matter, and how to replace inside it safely.',
		),
		'replace-text-on-one-page-wordpress'         => array(
			'title'    => 'How to replace text on just one page in WordPress',
			'file'     => 'posts/replace-text-on-one-page-wordpress.html',
			'category' => 'Guides',
			'date'     => '2026-10-12 09:00:00',
			'cover'    => 'cover-one-page.png',
			'desc'     => 'Change a phone number, price or link on one page, or only in drafts, or only in one post type, without touching the rest of the site.',
		),
	),
);
