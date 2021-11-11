<?php

namespace Woocommerce\Extension\Amazon;

/**
 * Admin model class.
 *
 * @author Piotr Włoch
 */
class AmazonS3AdminModel
{

  /**
   * Get Amazon Key ID.
   *
   * @return string
   */
  public static function getAmazonKeyID()
  {
    return trim(get_option('wc_settings_tab_amazon_access_key_id', ''));
  }

  /**
   * Get Amazon Secret Key.
   *
   * @return string
   */
  public static function getAmazonSecretKey()
  {
    return trim(get_option('wc_settings_tab_amazon_secret_key', ''));
  }

  /**
   * Get Amazon Endpoint.
   *
   * @return string
   */
  public static function getAmazonEndpoint()
  {
    $tmp_endpoint = self::getAmazonEndpointSelect();
    if ($tmp_endpoint == 'other') {
      $tmp_endpoint = self::getAmazonEndpointCustom();
    }

    return trim($tmp_endpoint);
  }

  /**
   * Get Amazon Select Endpoint.
   *
   * @return string
   */
  public static function getAmazonEndpointSelect()
  {
    return trim(get_option('wc_settings_tab_amazon_endpoint', ''));
  }

  /**
   * Get Amazon Custom Endpoint.
   *
   * @return string
   */
  public static function getAmazonEndpointCustom()
  {
    return trim(get_option('wc_settings_tab_amazon_endpoint_custom', ''));
  }
}
