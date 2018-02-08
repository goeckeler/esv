<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'generate_mobile_header_activated' ) ) :
/**
 * Check to see if the mobile header is activated
 */
function generate_mobile_header_activated()
{
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}
	
	$generate_menu_plus_settings = wp_parse_args( 
		get_option( 'generate_menu_plus_settings', array() ), 
		generate_menu_plus_get_defaults() 
	);
	
	return ( 'enable' == $generate_menu_plus_settings[ 'mobile_header' ] ) ? true : false;
}
endif;

if ( ! function_exists( 'generate_mobile_header_sticky_activated' ) ) :
/**
 * Check to see if the mobile header is activated
 */
function generate_mobile_header_sticky_activated()
{
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}
	
	$generate_menu_plus_settings = wp_parse_args( 
		get_option( 'generate_menu_plus_settings', array() ), 
		generate_menu_plus_get_defaults() 
	);
	
	return ( 'enable' == $generate_menu_plus_settings[ 'mobile_header' ] && 'enable' == $generate_menu_plus_settings[ 'mobile_header_sticky' ] ) ? true : false;
}
endif;

if ( ! function_exists( 'generate_sticky_navigation_activated' ) ) :
/**
 * Check to see if the sticky navigation is activated
 */
function generate_sticky_navigation_activated()
{
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}
	
	$generate_menu_plus_settings = wp_parse_args( 
		get_option( 'generate_menu_plus_settings', array() ), 
		generate_menu_plus_get_defaults() 
	);
	
	return ( 'false' !== $generate_menu_plus_settings[ 'sticky_menu' ] ) ? true : false;
}
endif;

if ( ! function_exists( 'generate_navigation_logo_activated' ) ) :
/**
 * Check to see if the sticky navigation is activated
 */
function generate_navigation_logo_activated()
{
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}
	
	$generate_menu_plus_settings = wp_parse_args( 
		get_option( 'generate_menu_plus_settings', array() ), 
		generate_menu_plus_get_defaults() 
	);
	
	return ( '' !== $generate_menu_plus_settings[ 'sticky_menu_logo' ] ) ? true : false;
}
endif;

if ( ! function_exists( 'generate_slideout_navigation_activated' ) ) :
/**
 * Check to see if the sticky navigation is activated
 */
function generate_slideout_navigation_activated()
{
	if ( ! function_exists( 'generate_menu_plus_get_defaults' ) ) {
		return false;
	}
	
	$generate_menu_plus_settings = wp_parse_args( 
		get_option( 'generate_menu_plus_settings', array() ), 
		generate_menu_plus_get_defaults() 
	);
	
	return ( 'false' !== $generate_menu_plus_settings[ 'slideout_menu' ] ) ? true : false;
}
endif;

if ( ! function_exists( 'generate_page_header_blog_content_exists' ) ) :
/**
 * This is an active_callback
 * Check if page header content exists
 */
function generate_page_header_blog_content_exists()
{
	if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
		return false;
	}
	
	$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
	if ( isset( $options[ 'page_header_content' ] ) && '' !== $options[ 'page_header_content' ] ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_page_header_blog_image_exists' ) ) :
/**
 * This is an active_callback
 * Check if page header image exists
 */
function generate_page_header_blog_image_exists()
{
	if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
		return false;
	}
	
	$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
	if ( isset( $options[ 'page_header_image' ] ) && '' !== $options[ 'page_header_image' ] ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_page_header_blog_crop_exists' ) ) :
/**
 * This is an active_callback
 * Check if page header image resizing is enabled
 */
function generate_page_header_blog_crop_exists()
{
	if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
		return false;
	}
	
	$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );

	if ( isset( $options[ 'page_header_hard_crop' ] ) && 'disable' !== $options[ 'page_header_hard_crop' ] ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_page_header_blog_combined' ) ) :
/**
 * This is an active_callback
 * Check if page header is merged
 */
function generate_page_header_blog_combined()
{
	if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
		return false;
	}
	
	$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
	if ( isset( $options[ 'page_header_combine' ] ) && '' !== $options[ 'page_header_combine' ] ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_page_header_full_screen_vertical' ) ) :
/**
 * This is an active_callback
 * Check if our page header is full screen and vertically centered
 */
function generate_page_header_full_screen_vertical()
{
	if ( ! function_exists( 'generate_page_header_get_defaults' ) ) {
		return false;
	}
	
	$options = get_option( 'generate_page_header_options', generate_page_header_get_defaults() );
	
	if ( $options[ 'page_header_full_screen' ] && $options[ 'page_header_vertical_center' ] ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_secondary_nav_show_merge_top_bar' ) ) :
/**
 * This is an active callback
 * Determines whether we should show the Merge with Secondary Navigation option
 */
function generate_secondary_nav_show_merge_top_bar() {
	if ( ! function_exists( 'generate_secondary_nav_get_defaults' ) ) {
		return false;
	}
	
	$generate_settings = wp_parse_args( 
		get_option( 'generate_secondary_nav_settings', array() ), 
		generate_secondary_nav_get_defaults() 
	);
	
	if ( 'secondary-nav-above-header' == $generate_settings[ 'secondary_nav_position_setting' ] && has_nav_menu( 'secondary' ) && is_active_sidebar( 'top-bar' ) ) {
		return true;
	}
	
	return false;
}
endif;

if ( ! function_exists( 'generate_premium_is_top_bar_active' ) ) :
/**
 * Check to see if the top bar is active
 *
 * @since 1.3.45
 */
function generate_premium_is_top_bar_active()
{
	$top_bar = is_active_sidebar( 'top-bar' ) ? true : false;
	return apply_filters( 'generate_is_top_bar_active', $top_bar );
}
endif;

if ( ! function_exists( 'generate_masonry_callback' ) ) :
/**
 * Check to see if masonry is activated
 */
function generate_masonry_callback()
{
	if ( ! function_exists( 'generate_blog_get_defaults' ) ) {
		return false;
	}
	
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	// If masonry is enabled, set to true
	return ( 'true' == $generate_blog_settings['masonry'] ) ? true : false;

}
endif;

if ( ! function_exists( 'generate_premium_is_posts_page' ) ) :
/**
 * Check to see if we're on a posts page
 */
function generate_premium_is_posts_page()
{
	$blog = ( is_home() || is_archive() || is_attachment() || is_tax() ) ? true : false;
	
	return $blog;
}
endif;

if ( ! function_exists( 'generate_premium_is_posts_page_single' ) ) :
/**
 * Check to see if we're on a posts page or a single post
 */
function generate_premium_is_posts_page_single()
{
	$blog = ( is_home() || is_archive() || is_attachment() || is_tax() || is_single() ) ? true : false;
	
	return $blog;
}
endif;

if ( ! function_exists( 'generate_premium_is_excerpt' ) ) :
/**
 * Check to see if we're displaying excerpts
 */
function generate_premium_is_excerpt()
{
	if ( ! function_exists( 'generate_get_defaults' ) ) {
		return false;
	}
	
	$generate_settings = wp_parse_args( 
		get_option( 'generate_settings', array() ), 
		generate_get_defaults() 
	);
	
	return ( 'excerpt' == $generate_settings['post_content'] ) ? true : false;
}
endif;