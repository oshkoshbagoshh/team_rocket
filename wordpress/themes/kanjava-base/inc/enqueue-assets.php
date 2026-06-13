<?php
/**
 * Vite asset loading — dual mode.
 *
 * Dev:  when dist/hot exists (Vite dev server running), load assets from the
 *       dev server with HMR.
 * Prod: read dist/.vite/manifest.json and enqueue the hashed JS + CSS.
 *
 * @package KanjavaBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const KANJAVA_VITE_ENTRY = 'src/js/main.js';

/**
 * Path to the Vite "hot" file written while the dev server runs.
 *
 * @return string
 */
function kanjava_vite_hot_file() {
	return KANJAVA_BASE_DIR . '/dist/hot';
}

/**
 * The dev server origin, or null when not running.
 *
 * @return string|null
 */
function kanjava_vite_dev_origin() {
	$hot = kanjava_vite_hot_file();
	if ( ! file_exists( $hot ) ) {
		return null;
	}
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a local build file, not a remote URL.
	return untrailingslashit( trim( (string) file_get_contents( $hot ) ) );
}

/**
 * Enqueue theme assets via Vite.
 */
function kanjava_base_enqueue_assets() {
	$dev_origin = kanjava_vite_dev_origin();

	if ( null !== $dev_origin ) {
		kanjava_base_enqueue_dev( $dev_origin );
	} else {
		kanjava_base_enqueue_prod();
	}
}
add_action( 'wp_enqueue_scripts', 'kanjava_base_enqueue_assets' );

/**
 * Dev mode: load the Vite client + entry module from the dev server.
 *
 * @param string $origin Dev server origin, e.g. http://localhost:5173.
 */
function kanjava_base_enqueue_dev( $origin ) {
	// Dev server assets are served fresh by Vite, so a cache-busting version is
	// intentionally omitted (null). Client must load before the entry.
	// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion -- dev server assets are never browser-cached.
	wp_enqueue_script( 'kanjava-vite-client', $origin . '/@vite/client', array(), null, false );
	wp_enqueue_script( 'kanjava-vite-entry', $origin . '/' . KANJAVA_VITE_ENTRY, array(), null, true );
	// phpcs:enable WordPress.WP.EnqueuedResourceParameters.MissingVersion
}

/**
 * Prod mode: enqueue hashed assets from the build manifest.
 */
function kanjava_base_enqueue_prod() {
	$manifest_path = KANJAVA_BASE_DIR . '/dist/.vite/manifest.json';

	if ( ! file_exists( $manifest_path ) ) {
		// No build yet — fail quietly rather than fatal.
		return;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- reading a local build manifest, not a remote URL.
	$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );
	if ( ! is_array( $manifest ) || empty( $manifest[ KANJAVA_VITE_ENTRY ] ) ) {
		return;
	}

	$entry    = $manifest[ KANJAVA_VITE_ENTRY ];
	$dist_uri = KANJAVA_BASE_URI . '/dist/';

	// Imported CSS chunks.
	if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
		foreach ( $entry['css'] as $i => $css_file ) {
			wp_enqueue_style(
				'kanjava-base-' . $i,
				$dist_uri . $css_file,
				array(),
				KANJAVA_BASE_VERSION
			);
		}
	}

	// Entry JS.
	if ( ! empty( $entry['file'] ) ) {
		wp_enqueue_script(
			'kanjava-base-entry',
			$dist_uri . $entry['file'],
			array(),
			KANJAVA_BASE_VERSION,
			true
		);
	}
}

/**
 * Emit Vite/entry scripts as ES modules.
 *
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function kanjava_base_module_type( $tag, $handle ) {
	$module_handles = array( 'kanjava-vite-client', 'kanjava-vite-entry', 'kanjava-base-entry' );
	if ( in_array( $handle, $module_handles, true ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'kanjava_base_module_type', 10, 2 );
