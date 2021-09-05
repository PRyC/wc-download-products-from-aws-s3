<?php

/*
  Plugin Name: WC Download Products from AWS S3
  Plugin URI: https://github.com/Bragi26/wc-download-products-from-aws-s3
  Description: Download Woocommerce Products from AWS S3 storage
  Version: 1.0.0
  Author: Piotr Włoch
  Author URI: pwloch.eu
  Text Domain: wc-download-products-from-aws-s3
  Domain Path: /languages/
  License: GPLv3 or later
  License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * WC requires at least: 3.6.0
 * WC tested up to: 4.0.1
 */

/*
  WC Download Products from AWS S3 Plugin is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  any later version.

  WC Download Products from AWS S3 Plugin is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with WC Download Products from AWS S3 Plugin. If not, see http://www.gnu.org/licenses/gpl-3.0.html.
 */

if ( !defined( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_VERSION' ) ) {
	define( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_VERSION', '1.0.0' );
}

if ( !defined( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR' ) ) {
	define( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR', dirname( __FILE__ ) );
}

if ( !defined( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_URL' ) ) {
	define( 'WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_URL', plugins_url( '', __FILE__ ) );
}

/**
 * Init function 
 *
 * @return void
 */
function woocommerce_aws_s3_download_products_init() {
	$dir = trailingslashit( WP_LANG_DIR );
	load_textdomain( 'wc-download-products-from-aws-s3', $dir . '/wc-download-products-from-aws-s3-' . get_locale() . '.mo' );
	load_plugin_textdomain( 'wc-download-products-from-aws-s3', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	
	require_once WOOCOMMERCE_DOWNLOAD_PRODUCTS_FROM_AWS_S3_DIR . '/src/Woocommerce/Extension/Amazon/AmazonS3Controler.php';
	$woocommerce_download_products_from_aws_s3 = Woocommerce\Extension\Amazon\AmazonS3Controler::getInstance();
}

add_action( 'plugins_loaded', 'woocommerce_aws_s3_download_products_init' );
