=== WC Download Products from AWS S3 ===
Contributors: Piotr Włoch
Tags: woocommerce, amazon s3, s3, aws, aws s3, AWS S3, amazon aws s3, download, downloadable-product, downloadable product, downloadable productst, storage, digital products,
Requires at least: 4.9
Tested up to: 5.4
Requires PHP: 5.6
Stable tag: 1.0.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Allows using Amazon S3 to upload and download Woocommerce digital products.

== Description ==

The WCDownload Products from AWS S3  plugin will allow you to store your products through your Amazon AWS S3 service. You can explore buckets and upload files directly to Amazon AWS S3. When a customer downloads their purchase the extension will translate this into Amazon AWS S3 URL and serve that file as a download.

Using WC Download Products from AWS S3 plugin to serve your digital products offers scalability, availability, security and performance. Especially when serving big files and providing greater download speed for your customers.

WC Download Products from AWS S3  extension also supports multiple buckets within one product so you can easily provide download access to files stored on different Amazon buckets when purchasing one product.

== Installation ==

1. Unzip "wc-download-products-from-aws-s3.zip" and copy folder "wc-download-products-from-aws-s3" into your wordpress plugins folder, normally located in /wp-content/plugins/. 
2. Activate the plugin through the 'Plugins' menu in Wordpress

== Frequently Asked Questions ==

= How to configure Amazon AWS S3 settings? =

1. Go to aws.amazon.com and sign for an account there
2. Go to console.aws.amazon.com. Select “Your Name” at the top right corner and then My Security Credentials.
3. Create New Access Key
4. Copy the Access Key ID and Secret Access Key and keep them, you will be using them below.
5. Go to the WooCommerce Settings -> Download Products from Amazon S3  menu tab in your WordPress admin area.
6. Paste the  Access Key ID and  Secret Access Key into the fields.
7. Select your Amazon Endpoint where your files will be stored.

== Screenshots ==

1. Settings page
2. List of Amazon AWS S3 Buckets
3. List of files in Amazon AWS S3 Bucket
4. Upload to Amazon AWS S3 window

== Changelog ==

= 1.0.0 = 
Initial release
