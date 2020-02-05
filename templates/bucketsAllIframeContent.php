<div id="media-items">
	<script type="text/javascript">
        jQuery(function ($) {
            $(document.body).on('click', '.insert-amazon-file', function (e) {
                e.preventDefault();
                var file = $(this).data('amazon-key');
                var file_path = $(this).data('amazon-path');
                $(parent.window.file_name_field).val(file);
                $(parent.window.file_path_field).val(file_path);
                parent.window.tb_remove();
            });
        });
	</script>
	<p class="amazon_back_button">
		<a class="button-secondary" href="#" onclick="history.back()"><?php _e( 'Back', 'wc-download-products-from-aws-s3' ); ?></a>
	</p>
	<table class="wp-list-table widefat striped">
		<tr>
			<th><?php _e( 'File Name', 'wc-download-products-from-aws-s3' ); ?></th>
			<th><?php _e( 'File Size', 'wc-download-products-from-aws-s3' ); ?></th>
			<th><?php _e( 'Upload Date', 'wc-download-products-from-aws-s3' ); ?></th>
			<th><?php _e( 'Action', 'wc-download-products-from-aws-s3' ); ?></th>
		</tr>
		<?php
		if ( is_array( $bucketContent ) ) :
			foreach ( $bucketContent as $id => $file ) :
				if ( $file['Size'] > 0 ) :
					?>
					<tr>
						<td><?php echo esc_html( $file['Key'] ); ?></td>
						<td><?php echo size_format( esc_html($file['Size']) ); ?></td>
						<td><?php echo date_format( date_create( esc_html($file['LastModified'] )), 'Y-m-d H:i:s' ); ?></td>
						<td><a href="#" data-amazon-key="<?php echo esc_attr( $file['Key'] ); ?>" data-amazon-path="<?php echo trailingslashit( 'https://' . esc_attr($endpoint) . '.' . esc_attr($amazon_link) . '?as3bucket=' . esc_attr($bucket) . '&as3file=/' . esc_attr($file['Key']) ); ?>" class="insert-amazon-file button-secondary"><?php _e( 'Use File', 'wc-download-products-from-aws-s3' ); ?></a></td>
					</tr>
					<?php
				endif;
			endforeach;
		endif;
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