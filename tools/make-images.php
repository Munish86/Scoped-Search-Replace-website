<?php
/**
 * Draws the blog cover images (content/covers/*.png, 1600x900) and the social share image
 * (theme assets/images/og-image.png, 1200x630) in the brand colours. Needs PHP GD with FreeType and the
 * Segoe UI fonts that come with Windows.
 *
 * Usage: php tools/make-images.php
 */

$root  = dirname( __DIR__ );
$bold  = 'C:/Windows/Fonts/segoeuib.ttf';
$semi  = 'C:/Windows/Fonts/seguisb.ttf';
$plain = 'C:/Windows/Fonts/segoeui.ttf';

function hex( $im, $hex, $alpha = 0 ) {
	return imagecolorallocatealpha( $im, hexdec( substr( $hex, 1, 2 ) ), hexdec( substr( $hex, 3, 2 ) ), hexdec( substr( $hex, 5, 2 ) ), $alpha );
}

// Soft glow: many transparent circles, largest first.
function glow( $im, $cx, $cy, $r, $hex, $strength ) {
	for ( $i = 0; $i < 40; $i++ ) {
		$rr = (int) ( $r * ( 1 - $i / 40 ) );
		imagefilledellipse( $im, $cx, $cy, $rr * 2, $rr * 2, hex( $im, $hex, 127 - $strength ) );
	}
}

// Logo mark (as logo-mark.svg: teal rounded square, white scope corners, amber magnifier) at x,y, size s.
function mark( $im, $x, $y, $s ) {
	$k  = $s / 32;
	$px = function ( $v ) use ( $k ) {
		return (int) round( $v * $k );
	};
	$brand = hex( $im, '#0F766E' );
	imagefilledrectangle( $im, $x + $px( 8 ), $y, $x + $px( 24 ), $y + $s, $brand );
	imagefilledrectangle( $im, $x, $y + $px( 8 ), $x + $s, $y + $px( 24 ), $brand );
	foreach ( array( array( 8, 8 ), array( 24, 8 ), array( 8, 24 ), array( 24, 24 ) ) as $c ) {
		imagefilledellipse( $im, $x + $px( $c[0] ), $y + $px( $c[1] ), $px( 16 ), $px( 16 ), $brand );
	}
	$white = hex( $im, '#FFFFFF' );
	imagesetthickness( $im, max( 2, $px( 2 ) ) );
	foreach ( array( array( 6, 11, 6, 6, 11, 6 ), array( 21, 6, 26, 6, 26, 11 ), array( 26, 21, 26, 26, 21, 26 ), array( 11, 26, 6, 26, 6, 21 ) ) as $l ) {
		imageline( $im, $x + $px( $l[0] ), $y + $px( $l[1] ), $x + $px( $l[2] ), $y + $px( $l[3] ), $white );
		imageline( $im, $x + $px( $l[2] ), $y + $px( $l[3] ), $x + $px( $l[4] ), $y + $px( $l[5] ), $white );
	}
	$amber = hex( $im, '#F5A524' );
	imagefilledellipse( $im, $x + $px( 14.8 ), $y + $px( 14.8 ), $px( 11.6 ), $px( 11.6 ), $amber );
	imagefilledellipse( $im, $x + $px( 14.8 ), $y + $px( 14.8 ), $px( 6.8 ), $px( 6.8 ), $brand );
	imagesetthickness( $im, max( 2, $px( 2.6 ) ) );
	imageline( $im, $x + $px( 18.3 ), $y + $px( 18.3 ), $x + $px( 21.7 ), $y + $px( 21.7 ), $amber );
	imagesetthickness( $im, 1 );
}

// Wraps text to a width; returns lines.
function wrap( $text, $font, $size, $width ) {
	$lines = array();
	$line  = '';
	foreach ( explode( ' ', $text ) as $word ) {
		$try = '' === $line ? $word : "$line $word";
		$box = imagettfbbox( $size, 0, $font, $try );
		if ( $box[2] - $box[0] > $width && '' !== $line ) {
			$lines[] = $line;
			$line    = $word;
		} else {
			$line = $try;
		}
	}
	$lines[] = $line;
	return $lines;
}

function canvas( $w, $h ) {
	$im = imagecreatetruecolor( $w, $h );
	imagealphablending( $im, true );
	imagefilledrectangle( $im, 0, 0, $w, $h, hex( $im, '#0D4F49' ) );
	glow( $im, (int) ( $w * 1.0 ), 0, (int) ( $w * 0.55 ), '#F5A524', 3 );
	glow( $im, 0, $h, (int) ( $w * 0.45 ), '#5EEAD4', 6 );
	// Faint grid
	$line = hex( $im, '#FFFFFF', 120 );
	for ( $x = 0; $x < $w; $x += 80 ) {
		imageline( $im, $x, 0, $x, $h, $line );
	}
	for ( $y = 0; $y < $h; $y += 80 ) {
		imageline( $im, 0, $y, $w, $y, $line );
	}
	return $im;
}

$covers = array(
	'cover-domain.png'     => array( 'MOVING SITES', 'Change your WordPress domain in the database' ),
	'cover-https.png'      => array( 'HTTPS', 'From http to https: fix old links in your content' ),
	'cover-serialized.png' => array( 'UNDER THE HOOD', 'Why search and replace breaks serialized data' ),
	'cover-one-page.png'   => array( 'HOW-TO', 'Replace text on just one page in WordPress' ),
);
@mkdir( "$root/content/covers", 0777, true );
foreach ( $covers as $file => list( $label, $title ) ) {
	$w  = 1600;
	$h  = 900;
	$im = canvas( $w, $h );
	imagettftext( $im, 26, 0, 120, 210, hex( $im, '#FFD48A' ), $semi, $label );
	$y = 310;
	foreach ( wrap( $title, $bold, 72, 1250 ) as $line ) {
		imagettftext( $im, 72, 0, 116, $y, hex( $im, '#FFFFFF' ), $bold, $line );
		$y += 108;
	}
	mark( $im, 120, 730, 56 );
	imagettftext( $im, 30, 0, 196, 772, hex( $im, '#FFFFFF' ), $bold, 'Scoped Search & Replace' );
	imagepng( $im, "$root/content/covers/$file", 9 );
	echo "content/covers/$file\n";
}

// Social share image
$im = canvas( 1200, 630 );
mark( $im, 90, 90, 72 );
imagettftext( $im, 34, 0, 186, 140, hex( $im, '#FFFFFF' ), $bold, 'Scoped Search & Replace' );
$y = 300;
foreach ( wrap( 'Search and replace in WordPress, only where you choose', $bold, 60, 1000 ) as $line ) {
	imagettftext( $im, 60, 0, 86, $y, hex( $im, '#FFFFFF' ), $bold, $line );
	$y += 88;
}
imagettftext( $im, 26, 0, 90, $y + 30, hex( $im, '#B7E2D9' ), $plain, 'Count every match first. Serialized data stays intact. Free plugin.' );
imagepng( $im, "$root/theme/scoped-sr/assets/images/og-image.png", 9 );
echo "theme/scoped-sr/assets/images/og-image.png\n";
