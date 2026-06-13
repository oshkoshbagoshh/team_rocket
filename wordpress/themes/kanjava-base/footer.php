<?php
/**
 * Site footer.
 *
 * @package KanjavaBase
 */

?>
</div><!-- .site-content -->

<footer class="footer is-kanjava">
	<div class="container">
		<div class="columns is-vcentered">
			<div class="column">
				<p class="has-text-weight-bold"><?php bloginfo( 'name' ); ?></p>
				<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'kanjava-base' ); ?></p>
			</div>
			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div class="column has-text-right">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'container'      => false,
							'items_wrap'     => '%3$s',
							'walker'         => new Kanjava_Navbar_Walker(),
							'depth'          => 1,
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
