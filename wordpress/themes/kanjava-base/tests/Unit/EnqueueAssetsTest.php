<?php
/**
 * Unit tests for the Vite dual-mode asset loader (inc/enqueue-assets.php).
 *
 * @package KanjavaBase
 */

declare( strict_types=1 );

namespace KanjavaBase\Tests\Unit;

use Brain\Monkey\Functions;
use KanjavaBase\Tests\TestCase;

final class EnqueueAssetsTest extends TestCase {

	/** @var string Fixtures dist dir (KANJAVA_BASE_DIR/dist). */
	private string $dist_dir;

	protected function setUp(): void {
		parent::setUp();

		$this->dist_dir = KANJAVA_BASE_DIR . '/dist';

		// add_action / add_filter fire at include time; swallow them.
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'add_filter' )->justReturn( true );
		Functions\when( 'untrailingslashit' )->alias(
			static fn( $value ) => rtrim( (string) $value, '/' )
		);

		require_once __DIR__ . '/../../inc/enqueue-assets.php';

		$this->clean_dist();
	}

	protected function tearDown(): void {
		$this->clean_dist();
		parent::tearDown();
	}

	private function clean_dist(): void {
		array_map( 'unlink', glob( $this->dist_dir . '/.vite/*' ) ?: array() );
		array_map( 'unlink', glob( $this->dist_dir . '/*' ) ?: array() );
		@rmdir( $this->dist_dir . '/.vite' );
		@rmdir( $this->dist_dir );
	}

	private function write_hot( string $origin ): void {
		if ( ! is_dir( $this->dist_dir ) ) {
			mkdir( $this->dist_dir, 0777, true );
		}
		file_put_contents( $this->dist_dir . '/hot', $origin );
	}

	private function write_manifest( array $manifest ): void {
		if ( ! is_dir( $this->dist_dir . '/.vite' ) ) {
			mkdir( $this->dist_dir . '/.vite', 0777, true );
		}
		file_put_contents(
			$this->dist_dir . '/.vite/manifest.json',
			(string) wp_json_encode_for_test( $manifest )
		);
	}

	public function test_dev_origin_is_null_without_hot_file(): void {
		$this->assertNull( kanjava_vite_dev_origin() );
	}

	public function test_dev_origin_is_trimmed_when_hot_file_present(): void {
		$this->write_hot( "http://localhost:5173\n" );
		$this->assertSame( 'http://localhost:5173', kanjava_vite_dev_origin() );
	}

	public function test_dev_mode_enqueues_vite_client_and_entry_as_modules(): void {
		$this->write_hot( 'http://localhost:5173' );

		$scripts = array();
		Functions\when( 'wp_enqueue_script' )->alias(
			static function ( $handle, $src ) use ( &$scripts ) {
				$scripts[ $handle ] = $src;
			}
		);
		// Styles must not be touched in dev mode.
		Functions\expect( 'wp_enqueue_style' )->never();

		kanjava_base_enqueue_assets();

		$this->assertSame(
			'http://localhost:5173/@vite/client',
			$scripts['kanjava-vite-client']
		);
		$this->assertSame(
			'http://localhost:5173/src/js/main.js',
			$scripts['kanjava-vite-entry']
		);
	}

	public function test_prod_mode_enqueues_hashed_css_and_js_from_manifest(): void {
		$this->write_manifest(
			array(
				'src/js/main.js' => array(
					'file' => 'assets/main-ABC123.js',
					'css'  => array( 'assets/main-DEF456.css' ),
				),
			)
		);

		$styles  = array();
		$scripts = array();
		Functions\when( 'wp_enqueue_style' )->alias(
			static function ( $handle, $src ) use ( &$styles ) {
				$styles[ $handle ] = $src;
			}
		);
		Functions\when( 'wp_enqueue_script' )->alias(
			static function ( $handle, $src ) use ( &$scripts ) {
				$scripts[ $handle ] = $src;
			}
		);

		kanjava_base_enqueue_assets();

		$this->assertSame(
			KANJAVA_BASE_URI . '/dist/assets/main-DEF456.css',
			$styles['kanjava-base-0']
		);
		$this->assertSame(
			KANJAVA_BASE_URI . '/dist/assets/main-ABC123.js',
			$scripts['kanjava-base-entry']
		);
	}

	public function test_prod_mode_is_a_noop_without_a_manifest(): void {
		Functions\expect( 'wp_enqueue_style' )->never();
		Functions\expect( 'wp_enqueue_script' )->never();

		// No hot file and no manifest on disk -> nothing enqueued, no fatal.
		kanjava_base_enqueue_assets();

		$this->assertTrue( true );
	}

	public function test_module_filter_targets_only_known_handles(): void {
		$tag = '<script src="x.js" id="h-js"></script>';

		$this->assertStringContainsString(
			'type="module"',
			kanjava_base_module_type( $tag, 'kanjava-vite-client' )
		);
		$this->assertStringContainsString(
			'type="module"',
			kanjava_base_module_type( $tag, 'kanjava-vite-entry' )
		);
		$this->assertStringContainsString(
			'type="module"',
			kanjava_base_module_type( $tag, 'kanjava-base-entry' )
		);
		$this->assertSame(
			$tag,
			kanjava_base_module_type( $tag, 'some-other-script' )
		);
	}
}

/**
 * Local JSON encoder so the test does not depend on WordPress's wp_json_encode.
 *
 * @param mixed $data Data to encode.
 * @return string
 */
function wp_json_encode_for_test( $data ): string {
	return (string) json_encode( $data );
}
