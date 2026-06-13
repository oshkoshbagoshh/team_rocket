<?php
/**
 * Nav menu walker that renders WordPress menus as Bulma navbar items.
 *
 * @package KanjavaBase
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Outputs each top-level item as a `.navbar-item` (or a hoverable dropdown
 * when it has children), so menus assigned in wp-admin pick up Bulma styling.
 */
class Kanjava_Navbar_Walker extends Walker_Nav_Menu {

	/**
	 * Start the submenu container.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<div class="navbar-dropdown">';
	}

	/**
	 * End the submenu container.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 */
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</div>';
	}

	/**
	 * Start a menu item.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 * @param int      $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );
		$url          = ! empty( $item->url ) ? esc_url( $item->url ) : '#';
		$title        = esc_html( $item->title );
		$active       = in_array( 'current-menu-item', (array) $item->classes, true ) ? ' is-active' : '';

		if ( 0 === $depth && $has_children ) {
			$output .= '<div class="navbar-item has-dropdown is-hoverable">';
			$output .= '<a class="navbar-link' . $active . '" href="' . $url . '">' . $title . '</a>';
		} else {
			$output .= '<a class="navbar-item' . $active . '" href="' . $url . '">' . $title . '</a>';
		}
	}

	/**
	 * End a menu item.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item.
	 * @param stdClass $args   Menu arguments.
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 === $depth && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
			$output .= '</div>';
		}
	}
}
