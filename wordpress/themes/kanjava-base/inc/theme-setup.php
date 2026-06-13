<?php
/**
 * Theme supports, menus, and content registration.
 *
 * @package KanjavaBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme feature support.
 */
function kanjava_base_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'kanjava-base' ),
			'footer'  => __( 'Footer Menu', 'kanjava-base' ),
		)
	);
}
add_action( 'after_setup_theme', 'kanjava_base_setup' );

/**
 * Set the editor/content width.
 */
function kanjava_base_content_width() {
	$GLOBALS['content_width'] = 960;
}
add_action( 'after_setup_theme', 'kanjava_base_content_width', 0 );
