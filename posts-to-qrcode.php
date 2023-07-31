<?php
/*
 * Plugin Name:       Posts to QR Code
 * Plugin URI:        https://hasan4web.com/plugins/posts-to-qrcode/
 * Description:       Display QR code Below every post
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Hasan Ali
 * Author URI:        https://hasan4web.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       posts-to-qrcode
 * Domain Path:       /languages/
 */

/**
 * Activation Hook
 */
function pqrc_activation_hook() {

}
register_activation_hook( __FILE__, 'pqrc_activation_hoo' );

/**
 * Deactivation Hook
 */
function pqrc_deactivation_hook() {

}
register_deactivation_hook( __FILE__, 'pqrc_deactivation_hook' );

/**
 * Load Text Domain
 */
function pqrc_load_textdomain() {
    load_plugin_textdomain( 'post-to-qrcode', false, dirname(__FILE__) . '/languages' );
}
add_action( 'plugins_loaded', 'pqrc_load_textdomain' );

/**
 * Posts to QR code
 */
function pqrc_display_qr_code( $content ) {
    $current_post_id    = get_the_ID();
    $current_post_title = get_the_title( $current_post_id );
    $current_post_url   = urldecode( get_the_permalink( $current_post_id ) );
    $current_post_type  = get_post_type( $current_post_id );

    /**
     * Post type check
     */
    $excluded_post_types = apply_filters( 'pqrc_excluded_post_type', array() );
    if(in_array( $current_post_type, $excluded_post_types )) {
        return $content;
    }

    /**
     * Dimension Hook
     */
    $width = get_option( 'pqrc_width' );
    $height = get_option( 'pqrc_height' );
    $width = $width ? $width : 180;
    $height = $width ? $width : 180;
    $dimension = apply_filters( 'pqrc_qrcode_dimension', "{$width}x{$height}" );

    // Image Attributes
    $image_attributes = apply_filters( 'pqrc_image_attribute', '' );

    $image_src = sprintf( 'https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s', $dimension, $current_post_url );
    $content .= sprintf( '<div class="qrcode"><img %s src="%s" alt="%s" /></div>', $image_attributes , $image_src, $current_post_title);
    return $content;
}
add_filter( 'the_content', 'pqrc_display_qr_code' );

/**
 * Settings for the QR code
 */
function pqrc_settings_init() {
    add_settings_field( 'pqrc_section', __( 'Post to QR Code', 'posts-to-qrcode' ), 'pqrc_settings_callback', 'general' );
    add_settings_field( 'pqrc_width', __( 'QR Code Width', 'posts-to-qrcode' ), 'pqrc_display_width', 'general' );
    add_settings_field( 'pqrc_height', __( 'QR Code Height', 'posts-to-qrcode' ), 'pqrc_display_height', 'general' );

    register_setting( 'general', 'pqrc_width', array( 'sanitize_callback' => 'esc_attr' ) );
    register_setting( 'general', 'pqrc_height', array( 'sanitize_callback' => 'esc_attr' ) );

    function pqrc_settings_callback() {
        echo '<p>' . __( 'Settings for post to QR Code', 'posts-to-qrcode' ) . '</p>';
    }
    
    function pqrc_display_width() {
        $width = get_option( 'pqrc_width' );
        printf( '<input type="text" id="%s" name="%s" value="%s"/>', 'pqrc_width', 'pqrc_width', $width );
    }

    function pqrc_display_height() {
        $height = get_option( 'pqrc_width' );
        printf( '<input type="text" id="%s" name="%s" value="%s"/>', 'pqrc_height', 'pqrc_height', $height );
    }
}
 add_filter( 'admin_init', 'pqrc_settings_init' );