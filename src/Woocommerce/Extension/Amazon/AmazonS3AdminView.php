<?php

namespace Woocommerce\Extension\Amazon;

use WC_Admin_Settings;

/**
 * Admin view class.
 *
 * @author Piotr Włoch
 */
class AmazonS3AdminView {

	/**
	 * Get Woocommerce Settings tab view for Amazon S3.
	 *
	 * @return array
	 */
	public static function getSettings() {
		$settings = array(
			'section_title' => array(
				'name' => __( 'Download prodcts from AWS S3', 'wc-download-products-from-aws-s3' ),
				'type' => 'title',
				'desc' => 'Settings',
				'id' => 'wc_settings_tab_amazon_section_title',
			),
			'amazon_access_key_id' => array(
				'name' => __( 'Amazon AWS S3 Access Key ID', 'wc-download-products-from-aws-s3' ),
				'type' => 'text',
				'desc' => __( 'Enter Amazon Access Key ID you created in "My Security Credentials".', 'wc-download-products-from-aws-s3' ),
				'id' => 'wc_settings_tab_amazon_access_key_id',
			),
			'amazon_secret_key' => array(
				'name' => __( 'Amazon AWS S3 Secret Key', 'wc-download-products-from-aws-s3' ),
				'type' => 'text',
				'desc' => __( 'Enter Amazon Secret Key you created in "My Security Credentials".', 'wc-download-products-from-aws-s3' ),
				'id' => 'wc_settings_tab_amazon_secret_key',
			),
			'amazon_endpoint_select' => array(
				'name' => __( 'Select Amazon Endpoint', 'wc-download-products-from-aws-s3' ),
				'type' => 'select',
				'desc' => __( 'Select the region you choose when creating a bucket.', 'wc-download-products-from-aws-s3' ),
				'id' => 'wc_settings_tab_amazon_endpoint',
				'options' => array(
					'us-east-2 ' => 'US East (Ohio) (us-east-2)',
					'us-east-1' => 'US East (N. Virginia) (us-east-1)',
					'us-west-1 ' => 'US West (N. California) (us-west-1)',
					'us-west-2' => 'US West (Oregon) (us-west-2)',
					'ap-east-1' => 'Asia Pacific (Hong Kong) (ap-east-1)',
					'ap-south-1' => 'Asia Pacific (Mumbai) (ap-south-1)',
					'ap-northeast-3 ' => 'Asia Pacific (Osaka-Local) (ap-northeast-3)',
					'ap-northeast-2' => 'Asia Pacific (Seoul) (ap-northeast-2)',
					'ap-southeast-1' => 'Asia Pacific (Singapore) (ap-southeast-1)',
					'ap-southeast-2' => 'Asia Pacific (Sydney) (ap-southeast-2)',
					'ap-northeast-1' => 'Asia Pacific (Tokyo) (ap-northeast-1)',
					'ca-central-1' => 'Canada (Central) (ca-central-1)',
					'cn-north-1' => 'China (Beijing) (cn-north-1)',
					'cn-northwest-1' => 'China (Ningxia) (cn-northwest-1)',
					'eu-central-1' => 'EU (Frankfurt) (eu-central-1)',
					'eu-west-1' => 'EU (Ireland) (eu-west-1)',
					'eu-west-2' => 'EU (London) (eu-west-2)',
					'eu-west-3' => 'EU (Paris) (eu-west-3)',
					'eu-north-1' => 'EU (Stockholm) (eu-north-1)',
					'sa-east-1' => 'South America (São Paulo) (sa-east-1)',
					'me-south-1' => 'Middle East (Bahrain) 	(me-south-1)',
					'other' => __( 'Other', 'wc-download-products-from-aws-s3' ),
				),
			),
			'amazon_endpoint_input' => array(
				'name' => __( 'Amazon Endpoint', 'wc-download-products-from-aws-s3' ),
				'type' => 'woo_amazon_text',
				'desc' => __( 'Enter the region you choose.', 'wc-download-products-from-aws-s3' ),
				'id' => 'wc_settings_tab_amazon_endpoint_custom',
			),
			'amazon_sectionend' => array(
				'type' => 'sectionend',
				'id' => 'wc_settings_tab_amazon_sectionend',
			),
		);

		return apply_filters( 'wc_settings_tab_amazon_settings', $settings );
	}

	/**
	 * Handle view 'woo_amazon_text' custom field in admin settings.
	 *
	 * @param array $value
	 *
	 * @return void
	 */
	public static function handleCustomTextField( $value ) {
		$field_description = WC_Admin_Settings::get_field_description( $value );
		$description = $field_description['description'];
		$tooltip_html = $field_description['tooltip_html'];
		$custom_attributes = array();

		if ( !empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
			foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}
		?><tr valign="top" id="wc_settings_tab_amazon_endpoint_custom_row">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo esc_html( $tooltip_html ); // WPCS: XSS ok.   ?></label>
			</th>
			<td class="forminp forminp-text forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<input
					name="<?php echo esc_attr( $value['id'] ); ?>"
					id="<?php echo esc_attr( $value['id'] ); ?>"
					type="text"
					style="<?php echo esc_attr( $value['css'] ); ?>"
					value="<?php echo esc_attr( $value['value'] ); ?>"
					class="<?php echo esc_attr( $value['class'] ); ?>"
					placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
					<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok.  ?>
					/><?php echo esc_html( $value['suffix'] ); ?> <?php echo $description; // WPCS: XSS ok.   ?>
			</td>
		<script>
		    jQuery(function ($) {
		        var select_val;
		        $(document).ready(function (e) {
		            select_val = $('#wc_settings_tab_amazon_endpoint').val();
		            if (select_val !== 'other') {
		                $('#wc_settings_tab_amazon_endpoint_custom_row').hide();
		            }
		        });
		        $('#wc_settings_tab_amazon_endpoint').change(function (e) {
		            select_val = $('#wc_settings_tab_amazon_endpoint').val();
		            if (select_val === 'other') {
		                $('#wc_settings_tab_amazon_endpoint_custom_row').show();
		            } else {
		                $('#wc_settings_tab_amazon_endpoint_custom_row').hide();
		            }
		        })
		    });
		</script>
		</tr>
		<?php
	}

}
