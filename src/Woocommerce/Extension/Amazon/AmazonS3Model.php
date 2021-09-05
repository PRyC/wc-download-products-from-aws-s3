<?php

namespace Woocommerce\Extension\Amazon;

require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/vendor/autoload.php';

use Exception;
use Aws\S3\S3Client;
use S3Exception;

/**
 * Amazon AWS S3 Model class, handling connection between Wordpress and Amazon S3.
 *
 * @author Piotr Włoch
 */
class AmazonS3Model {

	/**
	 * Create select options for amazon buckets.
	 *
	 * @return string
	 */
	public static function getSelectOptionsAmazonBuckets() {
		$bucketsArray = self::getAmazonBuckets();
		$options = '';
		if ( !empty( $bucketsArray ) ) {
			foreach ( $bucketsArray as $bucket ) {
				$options .= '<option value="' . esc_attr( $bucket['Name'] ) . '">' . esc_attr( $bucket['Name'] ) . '</option>';
			}
		}

		return $options;
	}

	/**
	 * Retrieve amazon buckets method.
	 *
	 * @return array
	 */
	public static function getAmazonBuckets() {
		$key = AmazonS3AdminModel::getAmazonKeyID();
		$secret = AmazonS3AdminModel::getAmazonSecretKey();
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		if ( empty( $key ) || $key == '' || empty( $secret ) || $secret == '' || empty( $endpoint ) || $endpoint == '' ) {
			return array();
		}

		try {
			$amazonS3 = new S3Client( [
				'version' => 'latest',
				'region' => $endpoint,
				'credentials' => [
					'key' => $key,
					'secret' => $secret,
				],
					] );
			$buckets = $amazonS3->listBuckets();
			$returnBuckets = (!empty( $buckets['Buckets'] ) && is_array( $buckets['Buckets'] )) ? $buckets['Buckets'] : array();
		} catch ( S3Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with getting a list of Amazon buckets.', 'wc-download-products-from-aws-s3' ) );
		} catch ( Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with getting a list of Amazon buckets.', 'wc-download-products-from-aws-s3' ) );
		}

		return $returnBuckets;
	}

	/**
	 * Retrieves Amazon Bucket content.
	 *
	 * @param string $bucket
	 *
	 * @return array
	 */
	public static function getAmazonBucketContent( $bucket ) {
		$key = AmazonS3AdminModel::getAmazonKeyID();
		$secret = AmazonS3AdminModel::getAmazonSecretKey();
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		if ( empty( $key ) || $key == '' || empty( $secret ) || $secret == '' || empty( $endpoint ) || $endpoint == '' ) {
			return array();
		}

		try {
			$amazonS3 = new S3Client( [
				'version' => 'latest',
				'region' => $endpoint,
				'credentials' => [
					'key' => $key,
					'secret' => $secret,
				],
					] );
			$listObjects = $amazonS3->listObjects( [ 'Bucket' => $bucket ] );
			$returnArray = (!empty( $listObjects['Contents'] ) && is_array( $listObjects['Contents'] )) ? $listObjects['Contents'] : array();
		} catch ( S3Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with taking bucket content.', 'wc-download-products-from-aws-s3' ) );
		} catch ( Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with taking bucket content.', 'wc-download-products-from-aws-s3' ) );
		}
		if ( !empty( $returnArray ) ) {
			$returnArray = self::unsetFolders( $returnArray );
		}

		return $returnArray;
	}

	/**
	 * Unset Folder in bucket content list
	 * @param array $listObjects
	 * @return array
	 */
	public static function unsetFolders( $listObjects ) {
		foreach ( $listObjects as $key => $file ) {
			if ( $file['Size'] == 0 ) {
				unset( $listObjects[$key] );
			}
		}

		return $listObjects;
	}

	/**
	 * Handle Upload Iframe.
	 *
	 * @return void
	 */
	public static function handleUploadIframe() {

		if ( !is_admin() || !isset( $_POST['woocommerce_amazon_s3_upload_submit'] ) || !wp_verify_nonce( $_POST['_wpnonce'], 'woocommerce_amazon_s3_upload_file_nonce' ) ) {
			return;
		}
		if ( empty( $_FILES['woocommerce_amazon_s3_upload_file'] ) || empty( $_FILES['woocommerce_amazon_s3_upload_file']['name'] ) ) {
			wp_die( __( 'Please select a file to upload', 'wc-download-products-from-aws-s3' ), __( 'Error', 'wc-download-products-from-aws-s3' ) );
		}
		$bucket = trim( (!empty( $_POST['woocommerce_amazon_s3_bucket'] )) ? sanitize_text_field( $_POST['woocommerce_amazon_s3_bucket'] ) : '' );
		$file_name = trim( (!empty( $_FILES['woocommerce_amazon_s3_upload_file']['name'] )) ?  sanitize_file_name( $_FILES['woocommerce_amazon_s3_upload_file']['name'] ) : '' );
		$file_tmp_name = trim( (!empty( $_FILES['woocommerce_amazon_s3_upload_file']['tmp_name'] )) ? esc_url( $_FILES['woocommerce_amazon_s3_upload_file']['tmp_name'] ) : '' );
		$result = self::uploadFile( $bucket, $file_name, $file_tmp_name );
		$amazon_link = 'amazon.com';
		if ( $result['ObjectURL'] != '' && !empty( $result['ObjectURL'] ) ) {
			set_transient( 'woo_amazon_product_download_link', trailingslashit( 'https://' . AmazonS3AdminModel::getAmazonEndpoint() . '.' . $amazon_link . '?as3bucket=' . $bucket . '&as3file=/' . $file_name ), MINUTE_IN_SECONDS );
			set_transient( 'woo_amazon_product_download_file', $file_name, MINUTE_IN_SECONDS );
			set_transient( 'woo_amazon_product_download_bucket', $bucket, MINUTE_IN_SECONDS );
			wp_safe_redirect( add_query_arg( 'woocommerce_amazon_s3_upload_success', 1, $_SERVER['HTTP_REFERER'] ) );
			exit();
		} else {
			delete_transient( 'woo_amazon_product_download_link' );
			delete_transient( 'woo_amazon_product_download_file' );
			delete_transient( 'woo_amazon_product_download_bucket' );
			wp_die( __( 'Something went wrong with upload process', 'wc-download-products-from-aws-s3' ) );
		}
	}

	/**
	 * Upload File to Amazon S3 server function.
	 *
	 * @param string $bucket
	 * @param string $file_name
	 * @param string $file_tmp_name
	 *
	 * @return boolval;
	 */
	public static function uploadFile( $bucket, $file_name, $file_tmp_name ) {
		$upload = null;
		$key = AmazonS3AdminModel::getAmazonKeyID();
		$secret = AmazonS3AdminModel::getAmazonSecretKey();
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		if ( empty( $key ) || $key == '' || empty( $secret ) || $secret == '' || empty( $endpoint ) || $endpoint == '' || empty( $bucket ) || $bucket == '' || empty( $file_name ) || $file_name == '' || empty( $file_tmp_name ) || $file_tmp_name == '' ) {
			return null;
		}
		try {
			$s3Client = new s3Client( [
				'version' => 'latest',
				'region' => $endpoint,
				'credentials' => [
					'key' => $key,
					'secret' => $secret,
				], ] );
			$upload = $s3Client->putObject( [
				'Bucket' => trim( $bucket ),
				'Key' => $file_name,
				'SourceFile' => $file_tmp_name,
					] );
		} catch ( S3Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with uploading a file at this moment.', 'wc-download-products-from-aws-s3' ) );
		} catch ( Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'There is a problem with uploading a file at this moment.', 'wc-download-products-from-aws-s3' ) );
		}
		return $upload;
	}

	/**
	 * Download File from Amazon S3 method.
	 *
	 * @param string $file_path
	 * @param string $file_name
	 *
	 * @return void
	 */
	public static function downloadAmazonS3File( $file_path, $file_name ) {
		$file_download_method = get_option( 'woocommerce_file_download_method', 'force' );
		if ( !self::isAmazonS3Link( $file_path ) ) {
			do_action( 'woocommerce_download_file_' . $file_download_method, $file_path, $file_name );
		} else {
			try {
				$file_path = apply_filters( 'woocommerce_amazon_s3_file_path', rawurldecode( $file_path ) );
				$query = '';
				$as3file = '';
				$as3bucket = '';
				$parts = parse_url( $file_path );
				parse_str( $parts['query'], $query );
				$as3file = trim( $query['as3file'], '/' );
				$as3bucket = $query['as3bucket'];
				$file_link = self::getAmazonFileURI( $as3file, $as3bucket );
				$headers = get_headers( $file_link, true );
				preg_match( '/\d{3}/', $headers[0], $remote_code );
				if ( $remote_code[0] == '404' || $remote_code[0] == '403' ) {
					self::downloadError( esc_html__( 'File not found. Please try again.', 'wc-download-products-from-aws-s3' ) );
				}
				call_user_func( sprintf( 'WC_Download_Handler::download_file_%s', $file_download_method ), $file_link, $file_name );
			} catch ( Exception $e ) {
				AmazonS3AdminNoticesModel::addNotice( __( 'An error occurred during the download process, please try later.', 'wc-download-products-from-aws-s3' ) );
			}
		}
		exit();
	}

	/**
	 * Check if a link is to Amazon S3.
	 *
	 * @param string $file_path
	 *
	 * @return boolean
	 */
	public static function isAmazonS3Link( $file_path ) {
		$parse_url = parse_url( $file_path );
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		$is_bool = (strstr( $parse_url['host'], $endpoint )) ? true : false;

		return $is_bool;
	}

	/**
	 * Handle download error.
	 *
	 * @param string $message
	 * @param string $title
	 * @param string $status
	 *
	 * @return void
	 */
	public static function downloadError( $message, $title = '', $status = '404' ) {
		if ( !strstr( $message, '<a ' ) ) {
			$message .= ' <a href="' . esc_url( home_url() ) . '" class="wc-forward">' . __( 'Go to homepage', 'wc-download-products-from-aws-s3' ) . '</a>';
		}
		wp_die( $message, $title, array( 'response' => $status ) );
	}

	/**
	 * Get File URI from Amazon S3 method.
	 *
	 * @param string $file_name
	 * @param string $bucket
	 *
	 * @return string
	 */
	public static function getAmazonFileURI( $file_name, $bucket ) {
		$key = AmazonS3AdminModel::getAmazonKeyID();
		$secret = AmazonS3AdminModel::getAmazonSecretKey();
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		if ( empty( $key ) || $key == '' || empty( $secret ) || $secret == '' || empty( $endpoint ) || $endpoint == '' ) {
			return '';
		}
		$presignedUrl = '';
		try {
			$amazonClient = new S3Client( [
				'version' => 'latest',
				'region' => $endpoint,
				'credentials' => [
					'key' => $key,
					'secret' => $secret,
				],
					] );

			$cmd = $amazonClient->getCommand( 'GetObject', [
				'Bucket' => $bucket,
				'Key' => $file_name,
					] );
			$request = $amazonClient->createPresignedRequest( $cmd, '+20 minutes' );
			$presignedUrl = (string) $request->getUri();
		} catch ( S3Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'Cant download file at this moment.', 'wc-download-products-from-aws-s3' ) );
		} catch ( Exception $e ) {
			AmazonS3AdminNoticesModel::addNotice( __( 'Cant download file at this moment.', 'wc-download-products-from-aws-s3' ) );
		}
		return $presignedUrl;
	}

}
