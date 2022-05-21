<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Plugin Name: AK Taxes
 * Plugin URI: https://github.com/faktycznie
 * Description: Taxes for WordPress
 * Version: 1.00
 * Author: Artur Kaczmarek
 * Author URI: https://github.com/faktycznie
 * Text Domain: ak-taxes
 */

define('AKTAXES_VER', '1.00');

if ( !class_exists ( 'AKtaxes' ) ) {
  class AKtaxes {
    function __construct() {
      add_action( 'plugins_loaded', array( $this, 'loaded' ) );
      add_action( 'init', array( $this, 'init' ) );
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ) );
      add_action( 'init', array($this, 'add_post_type') );
      add_action( 'wp_ajax_nopriv_ak_taxes_save_data', array($this, 'save_data') );
      add_action( 'wp_ajax_ak_taxes_save_data', array($this, 'save_data') );
      add_filter( 'manage_calculations_posts_columns', array($this, 'custom_columns') );
      add_action( 'manage_calculations_posts_custom_column', array($this, 'custom_column_value'), 10, 2 );
    }

    function loaded() {
      load_plugin_textdomain('ak-taxes', false, dirname( plugin_basename(__FILE__) ) . '/languages/'); //just an example
    }

    function init() {
      add_shortcode('taxes', array($this, 'add_shortcode'));
    }

    public static function getTemplate() { //for a separate template file
      $nonce = wp_create_nonce( 'ak_taxes_save_data' );
      ob_start();
      include( dirname( __FILE__ ) . '/templates/form.php' );
      $output = ob_get_clean();
      return $output;
    }

    function add_shortcode() {
      return $this->getTemplate();
    }

    function add_post_type() {
      register_post_type( 'calculations',
      array(
              'labels' => array(
                  'name'          => __( 'Kalkulacje', 'ak-taxes' ),
                  'singular_name' => __( 'Kalkulacja', 'ak-taxes' )
              ),
              'public'       => true,
              'rewrite'      => array('slug' => 'kalkulacje'),
              'show_in_rest' => true,
              'supports'     => array( 'editor' )
          )
      );
    }

    function save_data() {
      check_ajax_referer( 'ak_taxes_save_data', 'security' );

      $product = sanitize_text_field($_POST['product']);
      $msg = sanitize_text_field($_POST['msg']);
      $fprice = intval($_POST['finalPrice']);

      //some other variables which we can use:
      $price = intval($_POST['price']);
      $currency = sanitize_text_field($_POST['currency']);
      $taxrate = intval($_POST['taxrate']);
      $orgTaxrate = sanitize_text_field($_POST['orgTaxrate']);
      $tax = intval($_POST['tax']);

      $user_ip = $this->get_the_user_ip();

      if( !empty($product) && !empty($msg) ) {
        $new_post = array(
          'post_title'    => $product,
          'post_content'  => $msg,
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'calculations',
          'meta_input'    => array(
              '_ak_ip'    => $user_ip,
              '_ak_price' => $fprice,
              '_ak_time'  => time() //it is not needed because we have post publish date but it is just an example
          ),
        );

        $post_id = wp_insert_post( $new_post );
        if( ! is_wp_error($post_id) ) {
          wp_send_json_success($post_id);
        } else {
          wp_send_json_error($post_id->get_error_message());
        }
      } else {
        wp_send_json_error('Product name or message empty!');
      }
    }

    function get_the_user_ip() {
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
          $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function custom_columns($columns) {
      $columns['price'] = __('Price', 'ak-taxes');
      $columns['ip'] = __('IP', 'ak-taxes');
      return $columns;
    }
    
    function custom_column_value($column, $post_id) {
      switch ($column) {
        case 'price' :
          echo ( get_post_meta( $post_id, '_ak_price', true ) ) ? get_post_meta( $post_id, '_ak_price', true ) : '';
          echo __('zł', 'ak-taxes');
          break;
        case 'ip' :
          echo ( get_post_meta( $post_id, '_ak_ip', true ) ) ? get_post_meta( $post_id, '_ak_ip', true ) : '';
          break;
      }
    }

    function enqueue_files() {
      wp_enqueue_script( 'polyfill', 'https://polyfill.io/v3/polyfill.min.js?features=es7%2Ces6', array() ); //just in case ;-)
      wp_enqueue_style('ak-taxes-css', plugin_dir_url( __FILE__ ) . 'assets/style.css', array(), AKTAXES_VER);
      wp_enqueue_script('ak-taxes-js', plugin_dir_url( __FILE__ ) . 'assets/script.js', array(), AKTAXES_VER, true);
      wp_localize_script( 'ak-taxes-js', 'ak_taxes', array(
        'url' => admin_url( 'admin-ajax.php' ),
        'labels' => array(
          'no_product'  => __('Podaj nazwę produktu', 'ak-taxes'),
          'no_price'    => __('Podaj kwotę', 'ak-taxes'),
          'no_currency' => __('Podaj walutę', 'ak-taxes'),
          'no_taxrate'  => __('Podaj stawkę podatku', 'ak-taxes'),
          'result'      => __('Cena produktu {product} wynosi: {finalPrice} zł brutto, kwota podatku to {tax} zł', 'ak-taxes'),
          )
      ) );
    }
  }
  new AKtaxes();
}
