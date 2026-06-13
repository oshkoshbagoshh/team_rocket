<?php
/**
 * Main template — post listing in a responsive Bulma card grid.
 *
 * @package KanjavaBase
 */

get_header();
?>

<main class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="columns is-multiline">
				<?php
				while ( have_posts() ) :
					the_post();
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
								<h2 class="title is-5">
									<a href="<?php the_permalink(); ?>" class="has-text-primary"><?php the_title(); ?></a>
								</h2>
								<p class="subtitle is-6 has-text-grey">
									<span class="icon-text">
										<span class="icon"><i class="fa-regular fa-calendar"></i></span>
										<span><?php echo esc_html( get_the_date() ); ?></span>
									</span>
								</p>
								<div class="content"><?php the_excerpt(); ?></div>
								<a href="<?php the_permalink(); ?>" class="button is-primary is-small">
									<span><?php esc_html_e( 'Read more', 'kanjava-base' ); ?></span>
									<span class="icon is-small"><i class="fa-solid fa-arrow-right"></i></span>
								</a>
							</div>
						</article>
					</div>
					<?php
				endwhile;
				?>
			</div>

			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( 'Previous', 'kanjava-base' ),
					'next_text' => __( 'Next', 'kanjava-base' ),
				)
			);
			?>
		<?php else : ?>
			<div class="notification is-info is-light">
				<?php esc_html_e( 'No posts found yet. Create your first post in wp-admin.', 'kanjava-base' ); ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
