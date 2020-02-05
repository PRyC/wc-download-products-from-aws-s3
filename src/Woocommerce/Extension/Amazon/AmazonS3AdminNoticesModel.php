<?php

namespace Woocommerce\Extension\Amazon;

/**
 * Admin notices model.
 *
 * @author Piotr Włoch
 */
class AmazonS3AdminNoticesModel {

	/**
	 * Update Notices.
	 *
	 * @param array $notices
	 *
	 * @return void
	 */
	public static function updateNotices( $notices ) {
		update_option( 'woocommerce_amazon_s3_all_notices', $notices );
	}

	/**
	 * Add Notice.
	 *
	 * @param string $message
	 *
	 * @return void
	 */
	public static function addNotice( $message ) {
		$allNotices = get_option( 'woocommerce_amazon_s3_all_notices', array() );
		$allNotices[] = trim( $message );
		update_option( 'woocommerce_amazon_s3_all_notices', $allNotices );

		return;
	}

	/**
	 * Return Notices.
	 *
	 * @return array
	 */
	public static function getNotices() {
		return get_option( 'woocommerce_amazon_s3_all_notices', array() );
	}

}
