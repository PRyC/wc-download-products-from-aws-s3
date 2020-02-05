<div id="media-items">
	<h3 class="media-title"><?php _e( 'Select Amazon Bucket', 'wc-download-products-from-aws-s3' ); ?></h3>
    <table class="wp-list-table widefat striped ">
        <tr>
            <th><?php _e( 'Bucket', 'wc-download-products-from-aws-s3' ); ?></th>
            <th><?php _e( 'Action', 'wc-download-products-from-aws-s3' ); ?></th>
        </tr>
		<?php
		if ( is_array( $buckets ) ) {
			foreach ( $buckets as $buck ) {
				?>
				<tr>
					<td><?php echo esc_attr( $buck['Name'] ); ?></td>
					<td><a href="<?php echo esc_url( add_query_arg( 'bucket', $buck['Name'] ) ); ?>" type="button" class="button-secondary"><?php _e( 'Browse', 'wc-download-products-from-aws-s3' ); ?></a></td>
				</tr>
				<?php
			}
		}
		?>
    </table>
    <div class="amazon-pagination tablenav">
		<?php
		echo paginate_links( array(
			'base' => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, $page ),
			'total' => $total,
		) );
		?>
    </div>
</div>