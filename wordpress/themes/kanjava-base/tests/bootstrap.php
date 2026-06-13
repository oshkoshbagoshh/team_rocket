<?php
/**
 * PHPUnit bootstrap — pure unit tests (no WordPress, no database).
 *
 * WordPress functions are mocked per-test with Brain Monkey. This file only
 * wires up the autoloader, the constants the theme expects, and minimal stubs
 * for the WordPress classes the navbar walker extends.
 *
 * @package KanjavaBase
 */

declare( strict_types=1 );

require_once __DIR__ . '/../vendor/autoload.php';

// WordPress would normally define ABSPATH; theme files bail without it.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/tmp/' );
}

// Theme constants normally defined in functions.php. KANJAVA_BASE_DIR points at
// a scratch fixtures dir so enqueue-assets tests can create/remove dist/* files.
if ( ! defined( 'KANJAVA_BASE_DIR' ) ) {
	define( 'KANJAVA_BASE_DIR', __DIR__ . '/tmp' );
}
if ( ! defined( 'KANJAVA_BASE_URI' ) ) {
	define( 'KANJAVA_BASE_URI', 'https://example.test/wp-content/themes/kanjava-base' );
}
if ( ! defined( 'KANJAVA_BASE_VERSION' ) ) {
	define( 'KANJAVA_BASE_VERSION', '0.1.0-test' );
}

// Ensure the fixtures dir exists for the duration of the run.
if ( ! is_dir( KANJAVA_BASE_DIR ) ) {
	mkdir( KANJAVA_BASE_DIR, 0777, true );
}

/**
 * Minimal stand-ins for the WordPress walker classes so
 * Kanjava_Navbar_Walker can be loaded and instantiated without WordPress.
 */
if ( ! class_exists( 'Walker' ) ) {
	class Walker {}
}

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
	class Walker_Nav_Menu extends Walker {}
}
