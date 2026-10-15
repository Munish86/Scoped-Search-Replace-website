<?php
/**
 * Title: Text beside a screenshot
 * Slug: scoped-sr/screenshot-split
 * Categories: scoped-sr, features
 * Description: Heading, text and a check list on one side, a plugin screenshot on the other. Add the class sr-split--flip to the columns to swap sides.
 *
 * @package scoped-sr
 */

$img = esc_url( get_theme_file_uri( 'assets/images/plugin-results.webp' ) );
?>
<!-- wp:group {"align":"full","className":"sr-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull sr-section"><!-- wp:columns {"align":"wide","className":"sr-split"} -->
<div class="wp-block-columns alignwide sr-split"><!-- wp:column {"width":"42%"} -->
<div class="wp-block-column" style="flex-basis:42%"><!-- wp:paragraph {"className":"sr-eyebrow"} -->
<p class="sr-eyebrow">Section label</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What this part of the plugin does</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two or three sentences about it.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"sr-check"} -->
<ul class="wp-block-list sr-check"><!-- wp:list-item -->
<li>First detail</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Second detail</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","className":"sr-shot"} -->
<figure class="wp-block-image size-full sr-shot"><img src="<?php echo $img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above ?>" alt="Scoped Search &amp; Replace screenshot"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->
