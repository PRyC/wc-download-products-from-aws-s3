<?php

namespace Woocommerce\Extension\Amazon;

/**
 * Controler class for Woocommerce Download Products from AWS S3 Integration.
 *
 * @author Piotr Włoch
 */
class AmazonS3Controler {

	private static $instance;

	/**
	 * The Construct.
	 */
	private function __construct() {
		$this->requiredFiles();
		$this->init();
	}

	/**
	 * Hook in methods.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'addSettingsTab' ), 50 );
		add_action( 'woocommerce_settings_tabs_settings_tab_amazons3', array( $this, 'settingsTab' ) );
		add_action( 'woocommerce_update_options_settings_tab_amazons3', array( $this, 'updateSettings' ) );
		add_action( 'media_upload_tabs', array( $this, 'addMediaTabs' ) );
		add_action( 'media_upload_woocommerce_amazon_s3_upload', array( $this, 'uploadIframe' ) );
		add_action( 'media_upload_woocommerce_amazon_s3_buckets', array( $this, 'getBuckets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'addScripts' ) );
		add_action( 'wp_loaded', array( $this, 'handleUploadIframe' ) );
		add_action( 'woocommerce_file_download_method', array( $this, 'fileDownloadMethod' ), 10, 2 );
		add_action( 'woocommerce_download_file_from_wc_amazon_s3_product_download', array( $this, 'downloadAmazonS3File' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'printNotices' ) );
		add_action( 'woocommerce_admin_field_woo_amazon_text', array( $this, 'addCustomTextField' ), 10, 1 );
	}

	/**
	 * Include necessary files.
	 *
	 * @return void
	 */
	public function requiredFiles() {
		require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/vendor/autoload.php';
	}

	/**
	 * Return Instance.
	 *
	 * @return object
	 */
	public static function getInstance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Add scripts.
	 *
	 * @return void
	 */
	public function addScripts() {
		wp_enqueue_media();
		wp_enqueue_script( 'wc-download-products-from-aws-s3-script', WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_URL . '/assets/js/script.js', array( 'jquery' ), false, true );
		wp_localize_script( 'wc-download-products-from-aws-s3-script', 'woo_amazon_s3_var', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'check_connection_nonce' => wp_create_nonce( '' ),
		) );
	}

	/**
	 * Create Settings Tab in Woocommerce.
	 *
	 * @param array $settings_tabs
	 *
	 * @return void
	 */
	public function addSettingsTab( $settings_tabs ) {
		$settings_tabs['settings_tab_amazons3'] = __( 'Download Products from AWS S3', 'wc-download-products-from-aws-s3' );

		return $settings_tabs;
	}

	/**
	 * Handle Settings Tab view.
	 *
	 * @return void
	 */
	public function settingsTab() {
		woocommerce_admin_fields( AmazonS3AdminView::getSettings() );
	}

	/**
	 * Handle printing notices in admin.
	 *
	 * @return void
	 */
	public function printNotices() {
		$allNotices = AmazonS3AdminNoticesModel::getNotices();
		$print_notice = '';
		if ( !empty( $allNotices ) ) {
			$class = 'notice notice-error is-dismissible';
			ob_start();

			require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/templates/adminNotice.php';
			$print_notice .= ob_get_clean();
			AmazonS3AdminNoticesModel::updateNotices( array() );
		}

		echo $print_notice;
	}

	/**
	 * Handle updating settings tab in Woocommerce Settings.
	 *
	 * @return void
	 */
	public function updateSettings() {
		woocommerce_update_options( AmazonS3AdminView::getSettings() );
	}

	/**
	 * Handle upload file to Amazon S3 from an iframe.
	 *
	 * @return void
	 */
	public function handleUploadIframe() {
		AmazonS3Model::handleUploadIframe();
	}

	/**
	 * Handle download file from Amazon S3.
	 *
	 * @param string $file_path
	 * @param string $file_name
	 *
	 * @return void
	 */
	public function downloadAmazonS3File( $file_path, $file_name ) {
		AmazonS3Model::downloadAmazonS3File( $file_path, $file_name );
	}

	/**
	 * Add Media Tabs in Download Media Frame.
	 *
	 * @param array $tabs
	 *
	 * @return void
	 */
	public function addMediaTabs( $tabs ) {
		$tabs['woocommerce_amazon_s3_buckets'] = esc_html__( 'Browse Amazon AWS S3 Buckets', 'wc-download-products-from-aws-s3' );
		$tabs['woocommerce_amazon_s3_upload'] = esc_html__( 'Upload to Amazon AWS S3', 'wc-download-products-from-aws-s3' );

		return $tabs;
	}

	/**
	 * Add upload iframe in Download Media tab.
	 *
	 * @return void
	 */
	public function uploadIframe() {
		wp_iframe( array( $this, 'uploadIframeContent' ) );
	}

	/**
	 * Handle upload iframe view.
	 *
	 * @return void
	 */
	public function uploadIframeContent() {
		wp_enqueue_style( 'media' );
		$amazon_data = array(
			'link' => get_transient( 'woo_amazon_product_download_link' ),
			'file' => get_transient( 'woo_amazon_product_download_file' ),
			'bucket' => trailingslashit( get_transient( 'woo_amazon_product_download_bucket' ) ),
		);
		$selectOption = AmazonS3Model::getSelectOptionsAmazonBuckets();
		ob_start();
		require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/templates/uploadIframeContent.php';
		$output = ob_get_clean();
		echo $output;
	}

	/**
	 * Handle download method.
	 *
	 * @param string $download_method
	 * @param int    $product_id
	 *
	 * @return void
	 */
	public function fileDownloadMethod( $download_method, $product_id ) {
		return 'from_wc_amazon_s3_product_download';
	}

	/**
	 * Add Buckets list and Bucket content iframe in Download Media tab.
	 *
	 * @return void
	 */
	public function getBuckets() {
		wp_iframe( array( $this, 'bucketsIframeContent' ) );
	}

	/**
	 * Handle Buckets list and Bucket content view.
	 *
	 * @param string $type
	 * @param array  $errors
	 * @param int    $id
	 *
	 * @return void
	 */
	public function bucketsIframeContent( $type = 'file', $errors = null, $id = null ) {
		wp_enqueue_style( 'media' );
		$bucket = (isset( $_GET['bucket'] ) && !empty( $_GET['bucket'] )) ? sanitize_text_field( $_GET['bucket'] ) : false;
		$page = (isset( $_GET['paged'] ) && !empty( $_GET['paged'] ) && is_numeric( $_GET['paged'] )) ? intval( $_GET['paged'] ) : 1;
		$total = 1;
		$endpoint = AmazonS3AdminModel::getAmazonEndpoint();
		$amazon_link = 'amazon.com';
		$buckets = array();
		$bucketContent = array();
		$max_number_per_page = 16;
		$bucket_url = '';
		if ( !$bucket ) {
			$buckets = AmazonS3Model::getAmazonBuckets();
			$total = ceil( count( $buckets ) / $max_number_per_page );
			if ( $page != 1 ) {
				$offset = ($page - 1) * $max_number_per_page;
				$buckets = array_slice( $buckets, $offset, $max_number_per_page, true );
			} else {
				$buckets = array_slice( $buckets, 0, $max_number_per_page, true );
			}
			ob_start();
			require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/templates/bucketsListIframeContent.php';
			$output = ob_get_clean();
			echo $output;
		} else {
			$bucketContent = AmazonS3Model::getAmazonBucketContent( $bucket );

			$bucket_url = $endpoint . '.' . $amazon_link . '?as3bucket=' . $bucket . '&as3file=';

			$total = ceil( count( $bucketContent ) / $max_number_per_page );
			if ( $page != 1 ) {
				$offset = ($page - 1) * $max_number_per_page;
				$bucketContent = array_slice( $bucketContent, $offset, $max_number_per_page, true );
			} else {
				$bucketContent = array_slice( $bucketContent, 0, $max_number_per_page, true );
			}
			ob_start();
			require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/templates/bucketsAllIframeContent.php';
			$output = ob_get_clean();
			echo $output;
		}
	}

	/**
	 * Add custom field to admin settings.
	 *
	 * @param array $value
	 * 
	 * @return void
	 */
	public function addCustomTextField( $value ) {
		AmazonS3AdminView::handleCustomTextField( $value );
	}

}
