<div class="wc-amazons3-product-download <?php echo esc_attr( $class ); ?>">
	<?php foreach ( $allNotices as $notice ) : ?>
	    <p><?php echo wp_kses( $notice, wp_kses_allowed_html( 'post' ) ); ?></p>
	<?php endforeach; ?>
</div>