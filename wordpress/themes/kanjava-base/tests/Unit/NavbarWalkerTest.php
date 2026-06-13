<?php
/**
 * Unit tests for the Bulma navbar walker (inc/class-kanjava-navbar-walker.php).
 *
 * @package KanjavaBase
 */

declare( strict_types=1 );

namespace KanjavaBase\Tests\Unit;

use Brain\Monkey\Functions;
use KanjavaBase\Tests\TestCase;
use Kanjava_Navbar_Walker;

final class NavbarWalkerTest extends TestCase {

	private Kanjava_Navbar_Walker $walker;

	protected function setUp(): void {
		parent::setUp();

		require_once __DIR__ . '/../../inc/class-kanjava-navbar-walker.php';

		// Escapers are pass-throughs for assertion purposes.
		Functions\when( 'esc_url' )->returnArg();
		Functions\when( 'esc_html' )->returnArg();
		Functions\when( 'esc_attr' )->returnArg();

		$this->walker = new Kanjava_Navbar_Walker();
	}

	/**
	 * @param string[] $classes Menu-item CSS classes.
	 */
	private function item( array $classes = array(), string $url = 'https://example.test/page', string $title = 'Page' ): object {
		return (object) array(
			'classes' => $classes,
			'url'     => $url,
			'title'   => $title,
		);
	}

	public function test_plain_top_level_item_renders_as_navbar_item(): void {
		$output = '';
		$this->walker->start_el( $output, $this->item(), 0, null, 0 );

		$this->assertSame(
			'<a class="navbar-item" href="https://example.test/page">Page</a>',
			$output
		);
	}

	public function test_current_item_gets_is_active_modifier(): void {
		$output = '';
		$this->walker->start_el( $output, $this->item( array( 'current-menu-item' ) ), 0, null, 0 );

		$this->assertStringContainsString( 'class="navbar-item is-active"', $output );
	}

	public function test_parent_item_opens_hoverable_dropdown(): void {
		$item   = $this->item( array( 'menu-item-has-children' ), 'https://example.test/parent', 'Parent' );
		$output = '';

		$this->walker->start_el( $output, $item, 0, null, 0 );
		$this->assertStringContainsString( '<div class="navbar-item has-dropdown is-hoverable">', $output );
		$this->assertStringContainsString( '<a class="navbar-link" href="https://example.test/parent">Parent</a>', $output );

		// end_el must close the wrapper opened in start_el.
		$this->walker->end_el( $output, $item, 0, null );
		$this->assertStringEndsWith( '</div>', $output );
	}

	public function test_submenu_wrapper_is_a_navbar_dropdown(): void {
		$output = '';
		$this->walker->start_lvl( $output, 1, null );
		$this->walker->end_lvl( $output, 1, null );

		$this->assertSame( '<div class="navbar-dropdown"></div>', $output );
	}
}
