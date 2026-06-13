<?php
/**
 * Kanjava Base theme bootstrap.
 *
 * @package KanjavaBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'KANJAVA_BASE_VERSION', '0.1.0' );
define( 'KANJAVA_BASE_DIR', get_template_directory() );
define( 'KANJAVA_BASE_URI', get_template_directory_uri() );

require_once KANJAVA_BASE_DIR . '/inc/class-kanjava-navbar-walker.php';
require_once KANJAVA_BASE_DIR . '/inc/theme-setup.php';
require_once KANJAVA_BASE_DIR . '/inc/enqueue-assets.php';
