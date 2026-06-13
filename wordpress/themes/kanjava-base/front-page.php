<?php
/**
 * Front page — gradient hero + recent posts grid.
 *
 * @package KanjavaBase
 */

get_header();
?>

<section class="hero is-kanjava is-medium">
	<div class="hero-body">
		<div class="container has-text-centered">
			<p class="title is-1"><?php bloginfo( 'name' ); ?></p>
			<p class="subtitle is-4"><?php bloginfo( 'description' ); ?></p>
			<div class="buttons is-centered">
				<a class="button is-light is-medium" href="#latest">
					<span class="icon"><i class="fa-solid fa-compass"></i></span>
					<span><?php esc_html_e( 'Explore', 'kanjava-base' ); ?></span>
				</a>
			</div>
		</div>
	</div>
</section>

<main id="latest" class="section">
	<div class="container">
		<h2 class="title is-3 has-text-centered mb-6"><?php esc_html_e( 'Latest', 'kanjava-base' ); ?></h2>

		<?php
		$recent = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 6,
				'ignore_sticky_posts' => true,
			)
		);

		if ( $recent->have_posts() ) :
			?>
			<div class="columns is-multiline">
				<?php
				while ( $recent->have_posts() ) :
					$recent->the_post();
					?>
					<div class="column is-12-mobile is-6-tablet is-4-desktop">
						<article <?php post_class( 'card' ); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="card-image">
									<figure class="image is-16by9">
										<a href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( 'large', array( 'class' => 'has-ratio' ) ); ?>
										</a>
									</figure>
								</div>
							<?php endif; ?>
							<div class="card-content">
								<h3 class="title is-5">
									<a href="<?php the_permalink(); ?>" class="has-text-primary"><?php the_title(); ?></a>
								</h3>
								<div class="content"><?php the_excerpt(); ?></div>
							</div>
						</article>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="notification is-info is-light has-text-centered">
				<?php esc_html_e( 'No posts yet — add some in wp-admin and they will appear here.', 'kanjava-base' ); ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
