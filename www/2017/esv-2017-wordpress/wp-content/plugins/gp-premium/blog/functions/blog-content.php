<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'generate_excerpt_length' ) ) :
/**
 * Set our excerpt length
 * @since 0.1
 */
add_filter( 'excerpt_length', 'generate_excerpt_length', 15 );
function generate_excerpt_length( $length ) {
	$generate_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	return absint( apply_filters( 'generate_excerpt_length', $generate_settings['excerpt_length'] ) );
}
endif;

if ( ! function_exists( 'generate_blog_css' ) ) :
/**
 * Build our inline CSS
 * @since 0.1
 */
function generate_blog_css()
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	global $post;
	
	// Get disable headline meta
	$disable_headline = ( isset( $post ) ) ? get_post_meta( $post->ID, '_generate-disable-headline', true ) : '';
	
	$return = '';
	
	if ( 'false' == $generate_blog_settings['categories'] && 'false' == $generate_blog_settings['comments'] && 'false' == $generate_blog_settings['tags'] ) {
		$return .= '.blog footer.entry-meta, .archive footer.entry-meta {display:none;}';
	}
	
	if ( 'false' == $generate_blog_settings['date'] && 'false' == $generate_blog_settings['author'] && $disable_headline ) {
		$return .= '.single .entry-header{display:none;}.single .entry-content {margin-top:0;}';
	}
	
	if ( 'false' == $generate_blog_settings['date'] && 'false' == $generate_blog_settings['author'] ) {
		$return .= '.entry-header .entry-meta {display:none;}';
	}
	
	if ( function_exists( 'generate_spacing_get_defaults' ) ) {
		$spacing_settings = wp_parse_args( 
			get_option( 'generate_spacing_settings', array() ), 
			generate_spacing_get_defaults() 
		);
	}
	
	$separator = ( function_exists('generate_spacing_get_defaults') ) ? absint( $spacing_settings['separator'] ) : 20;
	
	if ( 'true' == generate_blog_get_masonry() ) {
		$return .= '.masonry-post .inside-article {margin-left: ' . $separator . 'px}';
		$return .= '.masonry-container > article {margin-bottom: ' . $separator . 'px;}';
		$return .= '.masonry-container {margin-left: -' . $separator . 'px;}';
		$return .= '.page-header {margin-bottom: ' . $separator . 'px;margin-left: ' . $separator . 'px}';
		$return .= '.separate-containers .site-main > .masonry-load-more {margin-bottom: ' . $separator . 'px;}';
	}
	
	return $return;
}
endif;

if ( ! function_exists( 'generate_blog_excerpt_more' ) ) :
/**
 * Prints the read more HTML
 */
add_filter( 'excerpt_more', 'generate_blog_excerpt_more', 15 );
function generate_blog_excerpt_more( $more ) {
	$generate_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	// If empty, return
	if ( '' == $generate_settings['read_more'] )
		return;
	
	return apply_filters( 'generate_excerpt_more_output', sprintf( ' ... <a title="%1$s" class="read-more" href="%2$s">%3$s</a>',
		the_title_attribute( 'echo=0' ),
		esc_url( get_permalink( get_the_ID() ) ),
		wp_kses_post( $generate_settings['read_more'] )
	) );
}
endif;

if ( ! function_exists( 'generate_blog_content_more' ) ) :
/**
 * Prints the read more HTML
 */
add_filter( 'the_content_more_link', 'generate_blog_content_more', 15 );
function generate_blog_content_more( $more ) {
	$generate_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	// If empty, return
	if ( '' == $generate_settings['read_more'] )
		return;
	
	return apply_filters( 'generate_content_more_link_output', sprintf( '<p class="read-more-container"><a title="%1$s" class="read-more content-read-more" href="%2$s">%3$s</a></p>',
		the_title_attribute( 'echo=0' ),
		esc_url( get_permalink( get_the_ID() ) . apply_filters( 'generate_more_jump','#more-' . get_the_ID() ) ),
		wp_kses_post( $generate_settings['read_more'] )
	) );
}
endif;

if ( ! function_exists( 'generate_disable_post_date' ) ) :
/**
 * Remove the post date if set
 * @since 0.1
 */
add_filter( 'generate_post_date', 'generate_disable_post_date' );
function generate_disable_post_date( $date )
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	if ( 'false' == $generate_blog_settings['date'] )
		return false;
	
	return $date;
}
endif;

if ( ! function_exists( 'generate_disable_post_author' ) ) :
/**
 * Set the author if set
 * @since 0.1
 */
add_filter( 'generate_post_author', 'generate_disable_post_author' );
function generate_disable_post_author( $author )
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	if ( 'false' == $generate_blog_settings['author'] )
		return false;
	
	return $author;
}
endif;

if ( ! function_exists( 'generate_disable_post_categories' ) ) :
/**
 * Remove the categories if set
 * @since 0.1
 */
add_filter( 'generate_show_categories', 'generate_disable_post_categories' );
function generate_disable_post_categories( $categories )
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	if ( 'false' == $generate_blog_settings['categories'] )
		return false;
	
	return $categories;
}
endif;

if ( ! function_exists( 'generate_disable_post_tags' ) ) :
/**
 * Remove the tags if set
 * @since 0.1
 */
add_filter( 'generate_show_tags', 'generate_disable_post_tags' );
function generate_disable_post_tags( $tags )
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	if ( 'false' == $generate_blog_settings['tags'] )
		return false;
	
	return $tags;
}
endif;

if ( ! function_exists( 'generate_disable_post_comments_link' ) ) :
/**
 * Remove the link to comments if set
 * @since 0.1
 */
add_filter( 'generate_show_comments', 'generate_disable_post_comments_link' );
function generate_disable_post_comments_link( $comments_link )
{
	$generate_blog_settings = wp_parse_args( 
		get_option( 'generate_blog_settings', array() ), 
		generate_blog_get_defaults() 
	);
	
	if ( 'false' == $generate_blog_settings['comments'] )
		return false;
	
	return $comments_link;
}
endif;