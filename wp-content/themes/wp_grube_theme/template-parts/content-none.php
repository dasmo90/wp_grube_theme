<?php
/**
 * The template part for displaying a message that posts cannot be found.
 *
 *
 * @package Nisarg
 */

?>

<section class="no-results not-found">
	<header class="page-header">
		<span class="screen-reader-text"><?php esc_html_e( 'Nichts gefunden', 'nisarg' ); ?></span>
		<h1 class="page-title"><?php esc_html_e( 'Nichts gefunden', 'nisarg' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

			<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'nisarg' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

		<?php elseif ( is_search() ) : ?>

			<p><?php esc_html_e( 'Leider wurde nichts gefunden. Versuchen Sie etwas anderes (;', 'nisarg' ); ?></p>
			<?php
			//get_search_form();
			?>

		<?php else : ?>

			<p><?php esc_html_e( 'Sie haben sich verirrt. Vielleicht kann die Suche Ihnen weiterhelfen.', 'nisarg' );
				?></p>
			<?php
			//get_search_form();
			?>

		<?php endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
