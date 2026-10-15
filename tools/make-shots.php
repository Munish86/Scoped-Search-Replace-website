<?php
/**
 * Turns the plugin screenshots from tools/plugin-shots.mjs (tools/.shots/plugin/*.png, 2x) into WebP files in the
 * theme (assets/images/plugin-*.webp), at most 1600px wide.
 *
 * Usage: php -d memory_limit=1G tools/make-shots.php
 */

$root = dirname( __DIR__ );
$in   = "$root/tools/.shots/plugin";
$dest = "$root/theme/scoped-sr/assets/images";

$files = glob( "$in/*.png" );
if ( ! $files ) {
	fwrite( STDERR, "No PNGs in $in. Run node tools/plugin-shots.mjs first.\n" );
	exit( 1 );
}
foreach ( $files as $file ) {
	$src = imagecreatefrompng( $file );
	$w   = imagesx( $src );
	$h   = imagesy( $src );
	if ( $w > 1600 ) {
		$nh  = (int) round( $h * 1600 / $w );
		$dst = imagecreatetruecolor( 1600, $nh );
		imagecopyresampled( $dst, $src, 0, 0, 0, 0, 1600, $nh, $w, $h );
		$src = $dst;
	}
	$name = 'plugin-' . basename( $file, '.png' ) . '.webp';
	imagewebp( $src, "$dest/$name", 86 );
	printf( "%s %dx%d\n", $name, imagesx( $src ), imagesy( $src ) );
}
