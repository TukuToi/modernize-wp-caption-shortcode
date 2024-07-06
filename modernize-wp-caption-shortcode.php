<?php
/**
 * Plugin Name: Modernize WP Caption ShortCode
 * Description: Customizes the output of the WP caption shortcode with <picture> element for different formats.
 * Version: 1.0
 * Author: bedas
 * Author URI: https://www.tukutoi.com/
 * License: GPL3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Filter to customize the output of the caption shortcode with <picture> element for different formats.
 *
 * @param string $output The caption output.
 * @param array  $attr Attributes of the caption shortcode.
 * @param string $content The image element, possibly wrapped in a hyperlink.
 * @return string Modified caption output.
 */
function custom_img_caption_shortcode_with_picture_formats( $output, $attr, $content ) {
	// Extract shortcode attributes
	$atts = shortcode_atts(
		array(
			'id'      => '',
			'align'   => '',
			'width'   => '',
			'caption' => '',
		),
		$attr,
		'caption'
	);

	// If there is no caption, return the image content only
	if ( empty( $atts['caption'] ) ) {
		return $content;
	}

	// Clean up the attributes
	$id        = $atts['id'] ? ' id="' . esc_attr( $atts['id'] ) . '"' : '';
	$align     = ' class="wp-caption ' . esc_attr( $atts['align'] ) . ' img-fluid"';
	$max_width = $atts['width'] ? ' style="max-width: ' . (int) $atts['width'] . 'px;"' : ' style="max-width: 100%;"';

	// Generate the picture element with different sources
	$img_id       = preg_match( '/wp-image-([0-9]+)/i', $content, $matches ) ? $matches[1] : 0;
	$image_url    = wp_get_attachment_image_url( $img_id, 'full' );
	$image_srcset = wp_get_attachment_image_srcset( $img_id, 'full' );
	$image_sizes  = wp_get_attachment_image_sizes( $img_id, 'full' );
	$image_webp   = wp_get_attachment_image_src( $img_id, 'full' )[0]; // Assuming you have WebP versions of your images stored similarly

	$output  = '<figure' . $id . $align . $max_width . '>';
	$output .= '<picture>';
	$output .= '<source type="image/webp" srcset="' . esc_url( $image_webp ) . '">';
	$output .= '<source type="image/jpeg" srcset="' . esc_url( $image_url ) . '">';
	$output .= '<img src="' . esc_url( $image_url ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" alt="' . esc_attr( $atts['caption'] ) . '" class="img-fluid">';
	$output .= '</picture>';
	$output .= '<figcaption class="wp-caption-text">' . $atts['caption'] . '</figcaption>';
	$output .= '</figure>';

	return $output;
}
add_filter( 'img_caption_shortcode', 'custom_img_caption_shortcode_with_picture_formats', 10, 3 );
