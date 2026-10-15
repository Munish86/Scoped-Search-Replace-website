<?php
/**
 * Title: Feature cards (3 columns)
 * Slug: scoped-sr/feature-cards
 * Categories: scoped-sr, features
 * Description: Section heading and three cards with icons. Change an icon by editing the heading's class (sr-ic--search, --scope, --replace, --shield, --eye, --layers, --link, --lock, --globe, --code, --undo, --zap, --doc, --clock, --sheet, --why, --chart).
 *
 * @package scoped-sr
 */
?>
<!-- wp:group {"align":"full","className":"sr-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull sr-section"><!-- wp:group {"className":"sr-section-head","layout":{"type":"constrained"}} -->
<div class="wp-block-group sr-section-head"><!-- wp:paragraph {"className":"sr-eyebrow"} -->
<p class="sr-eyebrow">Section label</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A short, clear section heading</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"sr-lead"} -->
<p class="sr-lead">One or two sentences that say what this section is about.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","className":"sr-cards"} -->
<div class="wp-block-columns alignwide sr-cards"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"sr-card","layout":{"type":"default"}} -->
<div class="wp-block-group sr-card"><!-- wp:heading {"level":3,"className":"sr-ic sr-ic--sheet"} -->
<h3 class="wp-block-heading sr-ic sr-ic--sheet">First point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the site owner, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"sr-card","layout":{"type":"default"}} -->
<div class="wp-block-group sr-card"><!-- wp:heading {"level":3,"className":"sr-ic sr-ic--doc"} -->
<h3 class="wp-block-heading sr-ic sr-ic--doc">Second point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the site owner, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"sr-card","layout":{"type":"default"}} -->
<div class="wp-block-group sr-card"><!-- wp:heading {"level":3,"className":"sr-ic sr-ic--image"} -->
<h3 class="wp-block-heading sr-ic sr-ic--image">Third point</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>What it does for the site owner, in one or two sentences.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
