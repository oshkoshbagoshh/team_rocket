<?php
/**
 * Site header + responsive Bulma navbar.
 *
 * @package KanjavaBase
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header>
	<nav class="navbar is-spaced has-shadow" role="navigation" aria-label="<?php esc_attr_e( 'Main navigation', 'kanjava-base' ); ?>">
		<div class="container">
			<div class="navbar-brand">
				<?php if ( has_custom_logo() ) : ?>
					<span class="navbar-item"><?php the_custom_logo(); ?></span>
				<?php else : ?>
					<a class="navbar-item has-text-weight-bold is-size-5" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<?php bloginfo( 'name' ); ?>
					</a>
				<?php endif; ?>

				<a role="button" class="navbar-burger" aria-label="<?php esc_attr_e( 'menu', 'kanjava-base' ); ?>" aria-expanded="false" data-target="kanjava-navbar">
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
				</a>
			</div>

			<div id="kanjava-navbar" class="navbar-menu">
				<div class="navbar-end">
					<?php
					if ( has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'primary',
								'container'      => false,
								'items_wrap'     => '%3$s',
								'walker'         => new Kanjava_Navbar_Walker(),
								'depth'          => 2,
							)
						);
					} else {
						echo '<a class="navbar-item" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'kanjava-base' ) . '</a>';
						echo '<a class="navbar-item" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . esc_html__( 'Set up a menu', 'kanjava-base' ) . '</a>';
					}
					?>
				</div>
			</div>
		</div>
	</nav>
</header>

<div class="site-content">
