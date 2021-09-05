<div class="wrap">
    <script type="text/javascript">
        /* <![CDATA[ */
        var wooAmazonProdDownload = <?php echo json_encode( $amazon_data ); ?>; /* ]]> */
    </script>
    <style>

    </style>
    <form enctype="multipart/form-data" method="POST" action="<?php echo esc_attr( admin_url() ); ?>">
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="woocommerce_amazon_s3_bucket"><?php _e( 'Select a Bucket', 'wc-download-products-from-aws-s3' ); ?></label></th>
                    <td>
                        <select name="woocommerce_amazon_s3_bucket" id="woocommerce_amazon_s3_buckets">
                            <option disabled value=""><?php _e( 'Select a Bucket', 'wc-download-products-from-aws-s3' ); ?></option>
							<?php echo wp_kses($selectOption, array('option' => array('value' => array()))); ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="woocommerce_amazon_s3_upload_file"><?php _e( 'Chose File to Upload:', 'wc-download-products-from-aws-s3' ); ?></label></th>
                    <td>
                        <input id="woocommerce_amazon_s3_upload_file" type="file" name="woocommerce_amazon_s3_upload_file">
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                        <input type="submit" name="woocommerce_amazon_s3_upload_submit" class="button" value="<?php _e( 'Upload To Amazon', 'wc-download-products-from-aws-s3' ); ?>">
                    </td>
                </tr>
				<?php if ( !empty( $_GET['woocommerce_amazon_s3_upload_success'] ) && $_GET['woocommerce_amazon_s3_upload_success'] == '1' ) : ?>
					<tr>
						<th>
							<p class="woocommerce_amazon_s3_upload_error"><?php _e( 'Success!', 'wc-download-products-from-aws-s3' ); ?> </p>
						</th>
						<td >
							<div class="woocommerce_amazon_s3_upload_error">
								<p class="woocommerce_amazon_s3_upload_error"><a href="#" class="button button-secondary" id="woocommerce-product-download-from-amazon-insert"><?php _e( 'Insert uploaded file', 'wc-download-products-from-aws-s3' ); ?></a></p>
							</div>
						</td>
					</tr>
				<?php endif; ?>
            </tbody>
        </table>
		<?php wp_nonce_field( 'woocommerce_amazon_s3_upload_file_nonce' ); ?>
    </form>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('#woocommerce-product-download-from-amazon-insert').on('click', function (e) {
                e.preventDefault();
                $(parent.window.file_name_field).val(wooAmazonProdDownload.file);
                $(parent.window.file_path_field).val(wooAmazonProdDownload.link);
                parent.window.tb_remove();
                $('.woocommerce_amazon_s3_upload_error').remove();
            });
        });
    </script>
</div>