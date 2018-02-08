<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

// Load functions built to migrate old options
require_once trailingslashit( dirname(__FILE__) ) . 'migration.php';

// Load Secondary Nav typography options
require_once trailingslashit( dirname(__FILE__) ) . 'secondary-nav-fonts.php';

// Load WooCommerce options
require_once trailingslashit( dirname(__FILE__) ) . 'woocommerce-fonts.php';

if ( ! function_exists( 'generate_fonts_customize_register' ) ) :
/**
 * Build the Customizer controls
 * @since 0.1
 */
add_action( 'customize_register', 'generate_fonts_customize_register' );
function generate_fonts_customize_register( $wp_customize ) {
	
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) ) {
		return;
	}
	
	// Get our custom controls
	require_once GP_LIBRARY_DIRECTORY . 'customizer-helpers.php';
	
	// Get our defaults
	$defaults = generate_get_default_fonts();
	 
	// Register our custom control types
	if ( method_exists( $wp_customize,'register_control_type' ) ) {
		$wp_customize->register_control_type( 'GeneratePress_Pro_Range_Slider_Control' );
		$wp_customize->register_control_type( 'GeneratePress_Pro_Typography_Customize_Control' );
	}
	
	// Add the typography panel
	if ( class_exists( 'WP_Customize_Panel' ) ) :
		$wp_customize->add_panel( 'generate_typography_panel', array(
			'priority'       => 30,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => __( 'Typography','generate-typography' ),
			'description'    => '',
		) );
	endif;

	// Body section
	$wp_customize->add_section(
		'font_section',
		array(
			'title' => __( 'Body', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 30,
			'panel' => 'generate_typography_panel'
		)
	);
	
	// Font family
	$wp_customize->add_setting( 
		'generate_settings[font_body]', 
		array(
			'default' => $defaults['font_body'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Variants
	$wp_customize->add_setting( 
		'font_body_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	// Category
	$wp_customize->add_setting( 
		'font_body_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Font weight
	$wp_customize->add_setting( 
		'generate_settings[body_font_weight]', 
		array(
			'default' => $defaults['body_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[body_font_transform]', 
		array(
			'default' => $defaults['body_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'body_typography', 
			array(
				'section' => 'font_section',
				'priority' => 1,
				'settings' => array( 
					'family' => 'generate_settings[font_body]',
					'variant' => 'font_body_variants',
					'category' => 'font_body_category',
					'weight' => 'generate_settings[body_font_weight]',
					'transform' => 'generate_settings[body_font_transform]',
				),
			)
		)
	);
	
	// Font size
	$wp_customize->add_setting( 
		'generate_settings[body_font_size]', 
		array(
			'default' => $defaults['body_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[body_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_section',
				'priority' => 40,
				'settings' => array( 
					'desktop' => 'generate_settings[body_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 25,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	// Line height
	$wp_customize->add_setting( 
		'generate_settings[body_line_height]', 
		array(
			'default' => $defaults['body_line_height'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[body_line_height]', 
			array(
				'description' => __( 'Line height', 'generate-typography' ), 
				'section' => 'font_section',
				'priority' => 45,
				'settings' => array( 
					'desktop' => 'generate_settings[body_line_height]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 1,
						'max' => 5,
						'step' => .1,
						'edit' => true,
						'unit' => '',
					),
				),
			)
		)
	);
	
	// Paragraph margin
	$wp_customize->add_setting( 
		'generate_settings[paragraph_margin]', 
		array(
			'default' => $defaults['paragraph_margin'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[paragraph_margin]', 
			array(
				'description' => __( 'Paragraph margin', 'generate-typography' ), 
				'section' => 'font_section',
				'priority' => 47,
				'settings' => array( 
					'desktop' => 'generate_settings[paragraph_margin]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 0,
						'max' => 5,
						'step' => .1,
						'edit' => true,
						'unit' => 'em',
					),
				),
			)
		)
	);
	
	// Top bar section
	$wp_customize->add_section(
		'generate_top_bar_typography',
		array(
			'title' => __( 'Top Bar', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 30,
			'panel' => 'generate_typography_panel'
		)
	);
		
	if ( isset( $defaults[ 'font_top_bar' ] ) && function_exists( 'generate_is_top_bar_active' ) ) {
		
		// Font family
		$wp_customize->add_setting( 
			'generate_settings[font_top_bar]', 
			array(
				'default' => $defaults['font_top_bar'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		// Category
		$wp_customize->add_setting( 
			'font_top_bar_category', 
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		// Variants
		$wp_customize->add_setting( 
			'font_top_bar_variants', 
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);
	
		// Font weight
		$wp_customize->add_setting( 
			'generate_settings[top_bar_font_weight]', 
			array(
				'default' => $defaults['top_bar_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
	

		// Text transform
		$wp_customize->add_setting( 
			'generate_settings[top_bar_font_transform]', 
			array(
				'default' => $defaults['top_bar_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'top_bar_typography', 
				array(
					'section' => 'generate_top_bar_typography',
					'settings' => array( 
						'family' => 'generate_settings[font_top_bar]',
						'variant' => 'font_top_bar_variants',
						'category' => 'font_top_bar_category',
						'weight' => 'generate_settings[top_bar_font_weight]',
						'transform' => 'generate_settings[top_bar_font_transform]',
					),
					'active_callback' => 'generate_premium_is_top_bar_active',
				)
			)
		);
		
	}
	
	if ( isset( $defaults[ 'top_bar_font_size' ] ) && function_exists( 'generate_is_top_bar_active' ) ) {
		// Font size
		$wp_customize->add_setting( 
			'generate_settings[top_bar_font_size]', 
			array(
				'default' => $defaults['top_bar_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[top_bar_font_size]', 
				array(
					'description' => __( 'Font size', 'generate-typography' ), 
					'section' => 'generate_top_bar_typography',
					'priority' => 75,
					'settings' => array( 
						'desktop' => 'generate_settings[top_bar_font_size]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 6,
							'max' => 25,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
					'active_callback' => 'generate_premium_is_top_bar_active',
				)
			)
		);
	}
	
	// Header section
	$wp_customize->add_section(
		'font_header_section',
		array(
			'title' => __( 'Header', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 40,
			'panel' => 'generate_typography_panel'
		)
	);
	
	// Font family
	$wp_customize->add_setting( 
		'generate_settings[font_site_title]', 
		array(
			'default' => $defaults['font_site_title'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Category
	$wp_customize->add_setting( 
		'font_site_title_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Variants
	$wp_customize->add_setting( 
		'font_site_title_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	// Font weight
	$wp_customize->add_setting( 
		'generate_settings[site_title_font_weight]', 
		array(
			'default' => $defaults['site_title_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[site_title_font_transform]', 
		array(
			'default' => $defaults['site_title_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'site_title_typography', 
			array(
				'label' => __( 'Site title', 'generate-typography' ), 
				'section' => 'font_header_section',
				'settings' => array( 
					'family' => 'generate_settings[font_site_title]',
					'variant' => 'font_site_title_variants',
					'category' => 'font_site_title_category',
					'weight' => 'generate_settings[site_title_font_weight]',
					'transform' => 'generate_settings[site_title_font_transform]',
				),
				'priority' => 50,
			)
		)
	);
	
	// Font size
	$wp_customize->add_setting( 
		'generate_settings[site_title_font_size]', 
		array(
			'default' => $defaults['site_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	// Mobile font size
	$wp_customize->add_setting( 
		'generate_settings[mobile_site_title_font_size]', 
		array(
			'default' => $defaults['mobile_site_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[site_title_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_header_section',
				'priority' => 75,
				'settings' => array( 
					'desktop' => 'generate_settings[site_title_font_size]',
					'mobile' => 'generate_settings[mobile_site_title_font_size]'
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 200,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 10,
						'max' => 200,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	// Tagline font family
	$wp_customize->add_setting( 
		'generate_settings[font_site_tagline]', 
		array(
			'default' => $defaults['font_site_tagline'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Category
	$wp_customize->add_setting( 
		'font_site_tagline_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Variants
	$wp_customize->add_setting( 
		'font_site_tagline_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	// Font weight
	$wp_customize->add_setting( 
		'generate_settings[site_tagline_font_weight]', 
		array(
			'default' => $defaults['site_tagline_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[site_tagline_font_transform]', 
		array(
			'default' => $defaults['site_tagline_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'font_site_tagline_control', 
			array(
				'label' => __( 'Site tagline', 'generate-typography' ), 
				'section' => 'font_header_section',
				'settings' => array( 
					'family' => 'generate_settings[font_site_tagline]',
					'variant' => 'font_site_tagline_variants',
					'category' => 'font_site_tagline_category',
					'weight' => 'generate_settings[site_tagline_font_weight]',
					'transform' => 'generate_settings[site_tagline_font_transform]',
				),
				'priority' => 80,
			)
		)
	);
	
	// Font size
	$wp_customize->add_setting( 
		'generate_settings[site_tagline_font_size]', 
		array(
			'default' => $defaults['site_tagline_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[site_tagline_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_header_section',
				'priority' => 105,
				'settings' => array( 
					'desktop' => 'generate_settings[site_tagline_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 50,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	// Primary navigation section
	$wp_customize->add_section(
		'font_navigation_section',
		array(
			'title' => __( 'Primary Navigation', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 50,
			'panel' => 'generate_typography_panel'
		)
	);
	
	// Font family
	$wp_customize->add_setting( 
		'generate_settings[font_navigation]', 
		array(
			'default' => $defaults['font_navigation'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Category
	$wp_customize->add_setting( 
		'font_navigation_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Variants
	$wp_customize->add_setting( 
		'font_navigation_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	// Font weight
	$wp_customize->add_setting( 
		'generate_settings[navigation_font_weight]', 
		array(
			'default' => $defaults['navigation_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[navigation_font_transform]', 
		array(
			'default' => $defaults['navigation_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'google_font_site_navigation_control', 
			array( 
				'section' => 'font_navigation_section',
				'settings' => array( 
					'family' => 'generate_settings[font_navigation]',
					'variant' => 'font_navigation_variants',
					'category' => 'font_navigation_category',
					'weight' => 'generate_settings[navigation_font_weight]',
					'transform' => 'generate_settings[navigation_font_transform]',
				),
				'priority' => 120,
			)
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[navigation_font_size]', 
		array(
			'default' => $defaults['navigation_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[mobile_navigation_font_size]', 
		array(
			'default' => $defaults['mobile_navigation_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[navigation_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_navigation_section',
				'priority' => 165,
				'settings' => array(
					'desktop' => 'generate_settings[navigation_font_size]',
					'mobile' => 'generate_settings[mobile_navigation_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 30,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 6,
						'max' => 30,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	// Buttons section
	$wp_customize->add_section(
		'font_buttons_section',
		array(
			'title' => __( 'Buttons', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 55,
			'panel' => 'generate_typography_panel'
		)
	);
	
	if ( isset( $defaults['font_buttons'] ) ) {
		$wp_customize->add_setting( 
			'generate_settings[font_buttons]', 
			array(
				'default' => $defaults['font_buttons'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_buttons_category', 
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_buttons_variants', 
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[buttons_font_weight]',  
			array(
				'default' => $defaults['buttons_font_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[buttons_font_transform]',
			array(
				'default' => $defaults['buttons_font_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_buttons_control', 
				array( 
					'section' => 'font_buttons_section',
					'settings' => array( 
						'family' => 'generate_settings[font_buttons]',
						'variant' => 'font_buttons_variants',
						'category' => 'font_buttons_category',
						'weight' => 'generate_settings[buttons_font_weight]',
						'transform' => 'generate_settings[buttons_font_transform]',
					),
				)
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[buttons_font_size]', 
			array(
				'default' => $defaults['buttons_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'buttons_font_sizes', 
				array(
					'description' => __( 'Font size', 'generate-typography' ), 
					'section' => 'font_buttons_section',
					'settings' => array(
						'desktop' => 'generate_settings[buttons_font_size]'
					),
					'choices' => array(
						'desktop' => array(
							'min' => 5,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	}
	
	// Headings section
	$wp_customize->add_section(
		'font_content_section',
		array(
			'title' => __( 'Headings', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 60,
			'panel' => 'generate_typography_panel'
		)
	);

	// H1
	$wp_customize->add_setting( 
		'generate_settings[font_heading_1]', 
		array(
			'default' => $defaults['font_heading_1'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_1_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_1_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_1_weight]',  
		array(
			'default' => $defaults['heading_1_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_1_transform]',
		array(
			'default' => $defaults['heading_1_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'font_heading_1_control', 
			array(
				'label' => __( 'Heading 1 (H1)', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array( 
					'family' => 'generate_settings[font_heading_1]',
					'variant' => 'font_heading_1_variants',
					'category' => 'font_heading_1_category',
					'weight' => 'generate_settings[heading_1_weight]',
					'transform' => 'generate_settings[heading_1_transform]',
				),
			)
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_1_font_size]', 
		array(
			'default' => $defaults['heading_1_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[mobile_heading_1_font_size]', 
		array(
			'default' => $defaults['mobile_heading_1_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'h1_font_sizes', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array(
					'desktop' => 'generate_settings[heading_1_font_size]',
					'mobile' => 'generate_settings[mobile_heading_1_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 15,
						'max' => 100,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 15,
						'max' => 100,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	if ( isset( $defaults['heading_1_line_height'] ) ) {
		// Line height
		$wp_customize->add_setting( 
			'generate_settings[heading_1_line_height]', 
			array(
				'default' => $defaults['heading_1_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[heading_1_line_height]', 
				array(
					'description' => __( 'Line height', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'desktop' => 'generate_settings[heading_1_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);
	}
	
	// H2
	$wp_customize->add_setting( 
		'generate_settings[font_heading_2]', 
		array(
			'default' => $defaults['font_heading_2'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_2_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_2_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_2_weight]',  
		array(
			'default' => $defaults['heading_2_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_2_transform]',
		array(
			'default' => $defaults['heading_2_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'font_heading_2_control', 
			array(
				'label' => __( 'Heading 2 (H2)', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array( 
					'family' => 'generate_settings[font_heading_2]',
					'variant' => 'font_heading_2_variants',
					'category' => 'font_heading_2_category',
					'weight' => 'generate_settings[heading_2_weight]',
					'transform' => 'generate_settings[heading_2_transform]',
				),
			)
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_2_font_size]', 
		array(
			'default' => $defaults['heading_2_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);

	$wp_customize->add_setting( 
		'generate_settings[mobile_heading_2_font_size]', 
		array(
			'default' => $defaults['mobile_heading_2_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'h2_font_sizes', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array(
					'desktop' => 'generate_settings[heading_2_font_size]',
					'mobile' => 'generate_settings[mobile_heading_2_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
					'mobile' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	if ( isset( $defaults['heading_2_line_height'] ) ) {
		// Line height
		$wp_customize->add_setting( 
			'generate_settings[heading_2_line_height]', 
			array(
				'default' => $defaults['heading_2_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[heading_2_line_height]', 
				array(
					'description' => __( 'Line height', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'desktop' => 'generate_settings[heading_2_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);
	}
	
	// H3
	$wp_customize->add_setting( 
		'generate_settings[font_heading_3]', 
		array(
			'default' => $defaults['font_heading_3'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_3_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	$wp_customize->add_setting( 
		'font_heading_3_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_3_weight]',  
		array(
			'default' => $defaults['heading_3_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_3_transform]',
		array(
			'default' => $defaults['heading_3_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'font_heading_3_control', 
			array(
				'label' => __( 'Heading 3 (H3)', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array( 
					'family' => 'generate_settings[font_heading_3]',
					'variant' => 'font_heading_3_variants',
					'category' => 'font_heading_3_category',
					'weight' => 'generate_settings[heading_3_weight]',
					'transform' => 'generate_settings[heading_3_transform]',
				),
			)
		)
	);
	
	$wp_customize->add_setting( 
		'generate_settings[heading_3_font_size]', 
		array(
			'default' => $defaults['heading_3_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'h3_font_sizes', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_content_section',
				'settings' => array(
					'desktop' => 'generate_settings[heading_3_font_size]'
				),
				'choices' => array(
					'desktop' => array(
						'min' => 10,
						'max' => 80,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	if ( isset( $defaults['heading_3_line_height'] ) ) {
		// Line height
		$wp_customize->add_setting( 
			'generate_settings[heading_3_line_height]', 
			array(
				'default' => $defaults['heading_3_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[heading_3_line_height]', 
				array(
					'description' => __( 'Line height', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'desktop' => 'generate_settings[heading_3_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);
	}
	
	if ( isset( $defaults['font_heading_4'] ) ) {
		// H4
		$wp_customize->add_setting( 
			'generate_settings[font_heading_4]', 
			array(
				'default' => $defaults['font_heading_4'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_heading_4_category', 
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_heading_4_variants', 
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_4_weight]',  
			array(
				'default' => $defaults['heading_4_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_4_transform]',
			array(
				'default' => $defaults['heading_4_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_heading_4_control', 
				array(
					'label' => __( 'Heading 4 (H4)', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'family' => 'generate_settings[font_heading_4]',
						'variant' => 'font_heading_4_variants',
						'category' => 'font_heading_4_category',
						'weight' => 'generate_settings[heading_4_weight]',
						'transform' => 'generate_settings[heading_4_transform]',
					),
				)
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_4_font_size]', 
			array(
				'default' => $defaults['heading_4_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'h4_font_sizes', 
				array(
					'description' => __( 'Font size', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[heading_4_font_size]'
					),
					'choices' => array(
						'desktop' => array(
							'min' => 10,
							'max' => 80,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	
		// Line height
		$wp_customize->add_setting( 
			'generate_settings[heading_4_line_height]', 
			array(
				'default' => $defaults['heading_4_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[heading_4_line_height]', 
				array(
					'description' => __( 'Line height', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'desktop' => 'generate_settings[heading_4_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);
	}
	
	if ( isset( $defaults['font_heading_5'] ) ) {
		// H5
		$wp_customize->add_setting( 
			'generate_settings[font_heading_5]', 
			array(
				'default' => $defaults['font_heading_5'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_heading_5_category', 
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_heading_5_variants', 
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_5_weight]',  
			array(
				'default' => $defaults['heading_5_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_5_transform]',
			array(
				'default' => $defaults['heading_5_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_heading_5_control', 
				array(
					'label' => __( 'Heading 5 (H5)', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'family' => 'generate_settings[font_heading_5]',
						'variant' => 'font_heading_5_variants',
						'category' => 'font_heading_5_category',
						'weight' => 'generate_settings[heading_5_weight]',
						'transform' => 'generate_settings[heading_5_transform]',
					),
				)
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[heading_5_font_size]', 
			array(
				'default' => $defaults['heading_5_font_size'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_empty_absint',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'h5_font_sizes', 
				array(
					'description' => __( 'Font size', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array(
						'desktop' => 'generate_settings[heading_5_font_size]'
					),
					'choices' => array(
						'desktop' => array(
							'min' => 10,
							'max' => 80,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	
		// Line height
		$wp_customize->add_setting( 
			'generate_settings[heading_5_line_height]', 
			array(
				'default' => $defaults['heading_5_line_height'],
				'type' => 'option',
				'sanitize_callback' => 'generate_premium_sanitize_decimal_integer_empty',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[heading_5_line_height]', 
				array(
					'description' => __( 'Line height', 'generate-typography' ), 
					'section' => 'font_content_section',
					'settings' => array( 
						'desktop' => 'generate_settings[heading_5_line_height]',
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 5,
							'step' => .1,
							'edit' => true,
							'unit' => 'em',
						),
					),
				)
			)
		);
	}
	
	// Widgets section
	$wp_customize->add_section(
		'font_widget_section',
		array(
			'title' => __( 'Widgets', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 60,
			'panel' => 'generate_typography_panel'
		)
	);
	
	// Font family
	$wp_customize->add_setting( 
		'generate_settings[font_widget_title]', 
		array(
			'default' => $defaults['font_widget_title'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Category
	$wp_customize->add_setting( 
		'font_widget_title_category', 
		array(
			'default' => '',
			'sanitize_callback' => 'sanitize_text_field'
		)
	);
	
	// Variants
	$wp_customize->add_setting( 
		'font_widget_title_variants', 
		array(
			'default' => '',
			'sanitize_callback' => 'generate_premium_sanitize_variants'
		)
	);
	
	// Font weight
	$wp_customize->add_setting( 
		'generate_settings[widget_title_font_weight]', 
		array(
			'default' => $defaults['widget_title_font_weight'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	// Text transform
	$wp_customize->add_setting( 
		'generate_settings[widget_title_font_transform]', 
		array(
			'default' => $defaults['widget_title_font_transform'],
			'type' => 'option',
			'sanitize_callback' => 'sanitize_key',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Typography_Customize_Control(
			$wp_customize,
			'google_font_widget_title_control', 
			array(
				'label' => __( 'Widget titles', 'generate-typography' ), 
				'section' => 'font_widget_section',
				'settings' => array( 
					'family' => 'generate_settings[font_widget_title]',
					'variant' => 'font_widget_title_variants',
					'category' => 'font_widget_title_category',
					'weight' => 'generate_settings[widget_title_font_weight]',
					'transform' => 'generate_settings[widget_title_font_transform]',
				),
			)
		)
	);
	
	// Font size
	$wp_customize->add_setting( 
		'generate_settings[widget_title_font_size]', 
		array(
			'default' => $defaults['widget_title_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[widget_title_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_widget_section',
				'settings' => array( 
					'desktop' => 'generate_settings[widget_title_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 30,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	if ( isset( $defaults['widget_title_separator'] ) ) {
		$wp_customize->add_setting( 
			'generate_settings[widget_title_separator]', 
			array(
				'default' => $defaults['widget_title_separator'],
				'type' => 'option',
				'sanitize_callback' => 'absint',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Range_Slider_Control(
				$wp_customize,
				'generate_settings[widget_title_separator]', 
				array(
					'description' => __( 'Bottom margin', 'generate-typography' ), 
					'section' => 'font_widget_section',
					'settings' => array(
						'desktop' => 'generate_settings[widget_title_separator]'
					),
					'choices' => array(
						'desktop' => array(
							'min' => 0,
							'max' => 100,
							'step' => 1,
							'edit' => true,
							'unit' => 'px',
						),
					),
				)
			)
		);
	}
	
	// Widget content font size
	$wp_customize->add_setting( 
		'generate_settings[widget_content_font_size]', 
		array(
			'default' => $defaults['widget_content_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[widget_content_font_size]', 
			array(
				'description' => __( 'Content font size', 'generate-typography' ), 
				'section' => 'font_widget_section',
				'priority' => 240,
				'settings' => array( 
					'desktop' => 'generate_settings[widget_content_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 30,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
	
	// Footer section
	$wp_customize->add_section(
		'font_footer_section',
		array(
			'title' => __( 'Footer', 'generate-typography' ),
			'capability' => 'edit_theme_options',
			'description' => '',
			'priority' => 70,
			'panel' => 'generate_typography_panel'
		)
	);
	
	if ( isset( $defaults['font_footer'] ) ) {
		// H5
		$wp_customize->add_setting( 
			'generate_settings[font_footer]', 
			array(
				'default' => $defaults['font_footer'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_footer_category', 
			array(
				'default' => '',
				'sanitize_callback' => 'sanitize_text_field'
			)
		);
		
		$wp_customize->add_setting( 
			'font_footer_variants', 
			array(
				'default' => '',
				'sanitize_callback' => 'generate_premium_sanitize_variants'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[footer_weight]',  
			array(
				'default' => $defaults['footer_weight'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_setting( 
			'generate_settings[footer_transform]',
			array(
				'default' => $defaults['footer_transform'],
				'type' => 'option',
				'sanitize_callback' => 'sanitize_key',
				'transport' => 'postMessage'
			)
		);
		
		$wp_customize->add_control(
			new GeneratePress_Pro_Typography_Customize_Control(
				$wp_customize,
				'font_footer_control', 
				array(
					'section' => 'font_footer_section',
					'settings' => array( 
						'family' => 'generate_settings[font_footer]',
						'variant' => 'font_footer_variants',
						'category' => 'font_footer_category',
						'weight' => 'generate_settings[footer_weight]',
						'transform' => 'generate_settings[footer_transform]',
					),
				)
			)
		);
	}
	
	// Font size
	$wp_customize->add_setting( 
		'generate_settings[footer_font_size]', 
		array(
			'default' => $defaults['footer_font_size'],
			'type' => 'option',
			'sanitize_callback' => 'absint',
			'transport' => 'postMessage'
		)
	);
	
	$wp_customize->add_control(
		new GeneratePress_Pro_Range_Slider_Control(
			$wp_customize,
			'generate_settings[footer_font_size]', 
			array(
				'description' => __( 'Font size', 'generate-typography' ), 
				'section' => 'font_footer_section',
				'settings' => array( 
					'desktop' => 'generate_settings[footer_font_size]',
				),
				'choices' => array(
					'desktop' => array(
						'min' => 6,
						'max' => 30,
						'step' => 1,
						'edit' => true,
						'unit' => 'px',
					),
				),
			)
		)
	);
}
endif;

if ( ! function_exists( 'generate_enqueue_google_fonts' ) ) :
/** 
 * Enqueue Google Fonts
 * @since 0.1
 */
add_action( 'wp_enqueue_scripts','generate_enqueue_google_fonts', 0 );
function generate_enqueue_google_fonts() {
	
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) )
		return;
		
	$generate_settings = wp_parse_args( 
		get_option( 'generate_settings', array() ), 
		generate_get_default_fonts() 
	);
		
	// List our non-Google fonts
	$not_google = str_replace( ' ', '+', generate_typography_default_fonts() );
	
	// Grab our font family settings
	$font_settings = array(
		'font_body',
		'font_top_bar',
		'font_site_title',
		'font_site_tagline',
		'font_navigation',
		'font_widget_title',
		'font_buttons',
		'font_heading_1',
		'font_heading_2',
		'font_heading_3',
		'font_heading_4',
		'font_heading_5',
		'font_footer',
	);
	
	// Create our Google Fonts array
	$google_fonts = array();
	if ( ! empty( $font_settings ) ) :
	
		foreach ( $font_settings as $key ) {
			
			// If the key isn't set, move on
			if ( ! isset( $generate_settings[$key] ) ) {
				continue;
			}
		
			// If our value is still using the old format, fix it
			if ( strpos( $generate_settings[$key], ':' ) !== false )
				$generate_settings[$key] = current( explode( ':', $generate_settings[$key] ) );
		
			// Replace the spaces in the names with a plus
			$value = str_replace( ' ', '+', $generate_settings[$key] );
			
			// Grab the variants using the plain name
			$variants = generate_get_google_font_variants( $generate_settings[$key], $key, generate_get_default_fonts() );
			
			// If we have variants, add them to our value
			$value = ! empty( $variants ) ? $value . ':' . $variants : $value;
			
			// Make sure we don't add the same font twice
			if( ! in_array( $value, $google_fonts ) ) {
				$google_fonts[] = $value;
			}
			
		}
		
	endif;

	// Ignore any non-Google fonts
	$google_fonts = array_diff($google_fonts, $not_google);
	
	// Separate each different font with a bar
	$google_fonts = implode('|', $google_fonts);
	
	// Apply a filter to the output
	$google_fonts = apply_filters( 'generate_typography_google_fonts', $google_fonts );
	
	// Get the subset
	$subset = apply_filters( 'generate_fonts_subset','' );
	
	// Set up our arguments
	$font_args = array();
	$font_args[ 'family' ] = $google_fonts;
	if ( '' !== $subset )
		$font_args[ 'subset' ] = urlencode( $subset );
	
	// Create our URL using the arguments
	$fonts_url = add_query_arg( $font_args, '//fonts.googleapis.com/css' );
	
	// Enqueue our fonts
	if ( $google_fonts ) { 
		wp_enqueue_style('generate-fonts', $fonts_url, array(), null, 'all' );
	}
}
endif;

if ( ! function_exists( 'generate_get_all_google_fonts' ) ) :
/**
 * Return an array of all of our Google Fonts
 * @since 1.3.0
 */
function generate_get_all_google_fonts( $amount = 'all' ) {
	// Our big list Google Fonts
	// We use json_decode to reduce PHP memory usage
	$content = json_decode( '[{"family":"Roboto","category":"sans-serif","variants":["100","100italic","300","300italic","regular","italic","500","500italic","700","700italic","900","900italic"]},{"family":"Open Sans","category":"sans-serif","variants":["300","300italic","regular","italic","600","600italic","700","700italic","800","800italic"]},{"family":"Lato","category":"sans-serif","variants":["100","100italic","300","300italic","regular","italic","700","700italic","900","900italic"]},{"family":"Slabo 27px","category":"serif","variants":["regular"]},{"family":"Oswald","category":"sans-serif","variants":["200","300","regular","500","600","700"]},{"family":"Roboto Condensed","category":"sans-serif","variants":["300","300italic","regular","italic","700","700italic"]},{"family":"Source Sans Pro","category":"sans-serif","variants":["200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","900","900italic"]},{"family":"Montserrat","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Raleway","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"PT Sans","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Roboto Slab","category":"serif","variants":["100","300","regular","700"]},{"family":"Merriweather","category":"serif","variants":["300","300italic","regular","italic","700","700italic","900","900italic"]},{"family":"Open Sans Condensed","category":"sans-serif","variants":["300","300italic","700"]},{"family":"Droid Sans","category":"sans-serif","variants":["regular","700"]},{"family":"Lora","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Ubuntu","category":"sans-serif","variants":["300","300italic","regular","italic","500","500italic","700","700italic"]},{"family":"Droid Serif","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Playfair Display","category":"serif","variants":["regular","italic","700","700italic","900","900italic"]},{"family":"Arimo","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Noto Sans","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"PT Serif","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Titillium Web","category":"sans-serif","variants":["200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","900"]},{"family":"PT Sans Narrow","category":"sans-serif","variants":["regular","700"]},{"family":"Muli","category":"sans-serif","variants":["200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Indie Flower","category":"handwriting","variants":["regular"]},{"family":"Bitter","category":"serif","variants":["regular","italic","700"]},{"family":"Poppins","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Inconsolata","category":"monospace","variants":["regular","700"]},{"family":"Dosis","category":"sans-serif","variants":["200","300","regular","500","600","700","800"]},{"family":"Fjalla One","category":"sans-serif","variants":["regular"]},{"family":"Oxygen","category":"sans-serif","variants":["300","regular","700"]},{"family":"Hind","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Cabin","category":"sans-serif","variants":["regular","italic","500","500italic","600","600italic","700","700italic"]},{"family":"Anton","category":"sans-serif","variants":["regular"]},{"family":"Arvo","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Noto Serif","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Crimson Text","category":"serif","variants":["regular","italic","600","600italic","700","700italic"]},{"family":"Lobster","category":"display","variants":["regular"]},{"family":"Yanone Kaffeesatz","category":"sans-serif","variants":["200","300","regular","700"]},{"family":"Nunito","category":"sans-serif","variants":["200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Bree Serif","category":"serif","variants":["regular"]},{"family":"Catamaran","category":"sans-serif","variants":["100","200","300","regular","500","600","700","800","900"]},{"family":"Libre Baskerville","category":"serif","variants":["regular","italic","700"]},{"family":"Abel","category":"sans-serif","variants":["regular"]},{"family":"Josefin Sans","category":"sans-serif","variants":["100","100italic","300","300italic","regular","italic","600","600italic","700","700italic"]},{"family":"Fira Sans","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Gloria Hallelujah","category":"handwriting","variants":["regular"]},{"family":"Abril Fatface","category":"display","variants":["regular"]},{"family":"Exo 2","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Merriweather Sans","category":"sans-serif","variants":["300","300italic","regular","italic","700","700italic","800","800italic"]},{"family":"Pacifico","category":"handwriting","variants":["regular"]},{"family":"Roboto Mono","category":"monospace","variants":["100","100italic","300","300italic","regular","italic","500","500italic","700","700italic"]},{"family":"Varela Round","category":"sans-serif","variants":["regular"]},{"family":"Asap","category":"sans-serif","variants":["regular","italic","500","500italic","700","700italic"]},{"family":"Amatic SC","category":"handwriting","variants":["regular","700"]},{"family":"Quicksand","category":"sans-serif","variants":["300","regular","500","700"]},{"family":"Karla","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Ubuntu Condensed","category":"sans-serif","variants":["regular"]},{"family":"Signika","category":"sans-serif","variants":["300","regular","600","700"]},{"family":"Alegreya","category":"serif","variants":["regular","italic","700","700italic","900","900italic"]},{"family":"Questrial","category":"sans-serif","variants":["regular"]},{"family":"Rubik","category":"sans-serif","variants":["300","300italic","regular","italic","500","500italic","700","700italic","900","900italic"]},{"family":"Shadows Into Light","category":"handwriting","variants":["regular"]},{"family":"PT Sans Caption","category":"sans-serif","variants":["regular","700"]},{"family":"Archivo Narrow","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Play","category":"sans-serif","variants":["regular","700"]},{"family":"Cuprum","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Dancing Script","category":"handwriting","variants":["regular","700"]},{"family":"Rokkitt","category":"serif","variants":["100","200","300","regular","500","600","700","800","900"]},{"family":"Work Sans","category":"sans-serif","variants":["100","200","300","regular","500","600","700","800","900"]},{"family":"Francois One","category":"sans-serif","variants":["regular"]},{"family":"Vollkorn","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Source Code Pro","category":"monospace","variants":["200","300","regular","500","600","700","900"]},{"family":"Exo","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Maven Pro","category":"sans-serif","variants":["regular","500","700","900"]},{"family":"Architects Daughter","category":"handwriting","variants":["regular"]},{"family":"Orbitron","category":"sans-serif","variants":["regular","500","700","900"]},{"family":"Pathway Gothic One","category":"sans-serif","variants":["regular"]},{"family":"Acme","category":"sans-serif","variants":["regular"]},{"family":"Ropa Sans","category":"sans-serif","variants":["regular","italic"]},{"family":"Patua One","category":"display","variants":["regular"]},{"family":"EB Garamond","category":"serif","variants":["regular"]},{"family":"Lobster Two","category":"display","variants":["regular","italic","700","700italic"]},{"family":"Crete Round","category":"serif","variants":["regular","italic"]},{"family":"Cinzel","category":"serif","variants":["regular","700","900"]},{"family":"Josefin Slab","category":"serif","variants":["100","100italic","300","300italic","regular","italic","600","600italic","700","700italic"]},{"family":"Source Serif Pro","category":"serif","variants":["regular","600","700"]},{"family":"Alegreya Sans","category":"sans-serif","variants":["100","100italic","300","300italic","regular","italic","500","500italic","700","700italic","800","800italic","900","900italic"]},{"family":"Comfortaa","category":"display","variants":["300","regular","700"]},{"family":"Russo One","category":"sans-serif","variants":["regular"]},{"family":"News Cycle","category":"sans-serif","variants":["regular","700"]},{"family":"ABeeZee","category":"sans-serif","variants":["regular","italic"]},{"family":"Yellowtail","category":"handwriting","variants":["regular"]},{"family":"Noticia Text","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Monda","category":"sans-serif","variants":["regular","700"]},{"family":"Quattrocento Sans","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Hammersmith One","category":"sans-serif","variants":["regular"]},{"family":"Libre Franklin","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Satisfy","category":"handwriting","variants":["regular"]},{"family":"Pontano Sans","category":"sans-serif","variants":["regular"]},{"family":"Righteous","category":"display","variants":["regular"]},{"family":"Poiret One","category":"display","variants":["regular"]},{"family":"BenchNine","category":"sans-serif","variants":["300","regular","700"]},{"family":"Arapey","category":"serif","variants":["regular","italic"]},{"family":"Kaushan Script","category":"handwriting","variants":["regular"]},{"family":"Economica","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Kanit","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Old Standard TT","category":"serif","variants":["regular","italic","700"]},{"family":"Sanchez","category":"serif","variants":["regular","italic"]},{"family":"Courgette","category":"handwriting","variants":["regular"]},{"family":"Quattrocento","category":"serif","variants":["regular","700"]},{"family":"Domine","category":"serif","variants":["regular","700"]},{"family":"Gudea","category":"sans-serif","variants":["regular","italic","700"]},{"family":"Permanent Marker","category":"handwriting","variants":["regular"]},{"family":"Armata","category":"sans-serif","variants":["regular"]},{"family":"Cantarell","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Archivo Black","category":"sans-serif","variants":["regular"]},{"family":"Istok Web","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Cardo","category":"serif","variants":["regular","italic","700"]},{"family":"Playfair Display SC","category":"serif","variants":["regular","italic","700","700italic","900","900italic"]},{"family":"Passion One","category":"display","variants":["regular","700","900"]},{"family":"Tinos","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Cookie","category":"handwriting","variants":["regular"]},{"family":"Cormorant Garamond","category":"serif","variants":["300","300italic","regular","italic","500","500italic","600","600italic","700","700italic"]},{"family":"Philosopher","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Yantramanav","category":"sans-serif","variants":["100","300","regular","500","700","900"]},{"family":"Chewy","category":"display","variants":["regular"]},{"family":"Antic Slab","category":"serif","variants":["regular"]},{"family":"Handlee","category":"handwriting","variants":["regular"]},{"family":"Teko","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Boogaloo","category":"display","variants":["regular"]},{"family":"Vidaloka","category":"serif","variants":["regular"]},{"family":"Audiowide","category":"display","variants":["regular"]},{"family":"Coming Soon","category":"handwriting","variants":["regular"]},{"family":"Alfa Slab One","category":"display","variants":["regular"]},{"family":"Cabin Condensed","category":"sans-serif","variants":["regular","500","600","700"]},{"family":"Ruda","category":"sans-serif","variants":["regular","700","900"]},{"family":"Ek Mukta","category":"sans-serif","variants":["200","300","regular","500","600","700","800"]},{"family":"Changa One","category":"display","variants":["regular","italic"]},{"family":"Tangerine","category":"handwriting","variants":["regular","700"]},{"family":"Great Vibes","category":"handwriting","variants":["regular"]},{"family":"Sintony","category":"sans-serif","variants":["regular","700"]},{"family":"Khand","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Bevan","category":"display","variants":["regular"]},{"family":"Kalam","category":"handwriting","variants":["300","regular","700"]},{"family":"Days One","category":"sans-serif","variants":["regular"]},{"family":"Bangers","category":"display","variants":["regular"]},{"family":"Rajdhani","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Droid Sans Mono","category":"monospace","variants":["regular"]},{"family":"Kreon","category":"serif","variants":["300","regular","700"]},{"family":"Rambla","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Shrikhand","category":"display","variants":["regular"]},{"family":"Fredoka One","category":"display","variants":["regular"]},{"family":"Shadows Into Light Two","category":"handwriting","variants":["regular"]},{"family":"Playball","category":"display","variants":["regular"]},{"family":"Neuton","category":"serif","variants":["200","300","regular","italic","700","800"]},{"family":"Copse","category":"serif","variants":["regular"]},{"family":"Didact Gothic","category":"sans-serif","variants":["regular"]},{"family":"Signika Negative","category":"sans-serif","variants":["300","regular","600","700"]},{"family":"Amiri","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Gentium Book Basic","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Glegoo","category":"serif","variants":["regular","700"]},{"family":"Oleo Script","category":"display","variants":["regular","700"]},{"family":"Voltaire","category":"sans-serif","variants":["regular"]},{"family":"Actor","category":"sans-serif","variants":["regular"]},{"family":"Amaranth","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Paytone One","category":"sans-serif","variants":["regular"]},{"family":"Volkhov","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Sorts Mill Goudy","category":"serif","variants":["regular","italic"]},{"family":"Bad Script","category":"handwriting","variants":["regular"]},{"family":"Coda","category":"display","variants":["regular","800"]},{"family":"Damion","category":"handwriting","variants":["regular"]},{"family":"Sacramento","category":"handwriting","variants":["regular"]},{"family":"Squada One","category":"display","variants":["regular"]},{"family":"Rock Salt","category":"handwriting","variants":["regular"]},{"family":"Adamina","category":"serif","variants":["regular"]},{"family":"Alice","category":"serif","variants":["regular"]},{"family":"Cantata One","category":"serif","variants":["regular"]},{"family":"Luckiest Guy","category":"display","variants":["regular"]},{"family":"Rochester","category":"handwriting","variants":["regular"]},{"family":"Covered By Your Grace","category":"handwriting","variants":["regular"]},{"family":"Heebo","category":"sans-serif","variants":["100","300","regular","500","700","800","900"]},{"family":"VT323","category":"monospace","variants":["regular"]},{"family":"Nothing You Could Do","category":"handwriting","variants":["regular"]},{"family":"Patrick Hand","category":"handwriting","variants":["regular"]},{"family":"Gentium Basic","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Nobile","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Rancho","category":"handwriting","variants":["regular"]},{"family":"Marck Script","category":"handwriting","variants":["regular"]},{"family":"Special Elite","category":"display","variants":["regular"]},{"family":"Julius Sans One","category":"sans-serif","variants":["regular"]},{"family":"Varela","category":"sans-serif","variants":["regular"]},{"family":"PT Mono","category":"monospace","variants":["regular"]},{"family":"Alex Brush","category":"handwriting","variants":["regular"]},{"family":"Homemade Apple","category":"handwriting","variants":["regular"]},{"family":"Scada","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Jura","category":"sans-serif","variants":["300","regular","500","600"]},{"family":"Antic","category":"sans-serif","variants":["regular"]},{"family":"Gochi Hand","category":"handwriting","variants":["regular"]},{"family":"Sarala","category":"sans-serif","variants":["regular","700"]},{"family":"Electrolize","category":"sans-serif","variants":["regular"]},{"family":"Sigmar One","category":"display","variants":["regular"]},{"family":"Candal","category":"sans-serif","variants":["regular"]},{"family":"Prata","category":"serif","variants":["regular"]},{"family":"Homenaje","category":"sans-serif","variants":["regular"]},{"family":"Pinyon Script","category":"handwriting","variants":["regular"]},{"family":"Unica One","category":"display","variants":["regular"]},{"family":"Basic","category":"sans-serif","variants":["regular"]},{"family":"Neucha","category":"handwriting","variants":["regular"]},{"family":"Convergence","category":"sans-serif","variants":["regular"]},{"family":"Molengo","category":"sans-serif","variants":["regular"]},{"family":"Caveat Brush","category":"handwriting","variants":["regular"]},{"family":"Monoton","category":"display","variants":["regular"]},{"family":"Calligraffitti","category":"handwriting","variants":["regular"]},{"family":"Kameron","category":"serif","variants":["regular","700"]},{"family":"Share","category":"display","variants":["regular","italic","700","700italic"]},{"family":"Alegreya Sans SC","category":"sans-serif","variants":["100","100italic","300","300italic","regular","italic","500","500italic","700","700italic","800","800italic","900","900italic"]},{"family":"Enriqueta","category":"serif","variants":["regular","700"]},{"family":"Martel","category":"serif","variants":["200","300","regular","600","700","800","900"]},{"family":"Black Ops One","category":"display","variants":["regular"]},{"family":"Just Another Hand","category":"handwriting","variants":["regular"]},{"family":"Caveat","category":"handwriting","variants":["regular","700"]},{"family":"PT Serif Caption","category":"serif","variants":["regular","italic"]},{"family":"Ultra","category":"serif","variants":["regular"]},{"family":"Ubuntu Mono","category":"monospace","variants":["regular","italic","700","700italic"]},{"family":"Carme","category":"sans-serif","variants":["regular"]},{"family":"Cousine","category":"monospace","variants":["regular","italic","700","700italic"]},{"family":"Cherry Cream Soda","category":"display","variants":["regular"]},{"family":"Reenie Beanie","category":"handwriting","variants":["regular"]},{"family":"Hind Siliguri","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Bubblegum Sans","category":"display","variants":["regular"]},{"family":"Aldrich","category":"sans-serif","variants":["regular"]},{"family":"Lustria","category":"serif","variants":["regular"]},{"family":"Alef","category":"sans-serif","variants":["regular","700"]},{"family":"Freckle Face","category":"display","variants":["regular"]},{"family":"Fanwood Text","category":"serif","variants":["regular","italic"]},{"family":"Advent Pro","category":"sans-serif","variants":["100","200","300","regular","500","600","700"]},{"family":"Allura","category":"handwriting","variants":["regular"]},{"family":"Ceviche One","category":"display","variants":["regular"]},{"family":"Press Start 2P","category":"display","variants":["regular"]},{"family":"Overlock","category":"display","variants":["regular","italic","700","700italic","900","900italic"]},{"family":"Niconne","category":"handwriting","variants":["regular"]},{"family":"Limelight","category":"display","variants":["regular"]},{"family":"Frank Ruhl Libre","category":"sans-serif","variants":["300","regular","500","700","900"]},{"family":"Allerta Stencil","category":"sans-serif","variants":["regular"]},{"family":"Marcellus","category":"serif","variants":["regular"]},{"family":"Pragati Narrow","category":"sans-serif","variants":["regular","700"]},{"family":"Michroma","category":"sans-serif","variants":["regular"]},{"family":"Fauna One","category":"serif","variants":["regular"]},{"family":"Syncopate","category":"sans-serif","variants":["regular","700"]},{"family":"Telex","category":"sans-serif","variants":["regular"]},{"family":"Marvel","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Cabin Sketch","category":"display","variants":["regular","700"]},{"family":"Hanuman","category":"serif","variants":["regular","700"]},{"family":"Cairo","category":"sans-serif","variants":["200","300","regular","600","700","900"]},{"family":"Chivo","category":"sans-serif","variants":["300","300italic","regular","italic","700","700italic","900","900italic"]},{"family":"Allerta","category":"sans-serif","variants":["regular"]},{"family":"Fugaz One","category":"display","variants":["regular"]},{"family":"Viga","category":"sans-serif","variants":["regular"]},{"family":"Ruslan Display","category":"display","variants":["regular"]},{"family":"Nixie One","category":"display","variants":["regular"]},{"family":"Marmelad","category":"sans-serif","variants":["regular"]},{"family":"Average","category":"serif","variants":["regular"]},{"family":"Spinnaker","category":"sans-serif","variants":["regular"]},{"family":"Leckerli One","category":"handwriting","variants":["regular"]},{"family":"Judson","category":"serif","variants":["regular","italic","700"]},{"family":"Lusitana","category":"serif","variants":["regular","700"]},{"family":"Montserrat Alternates","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Contrail One","category":"display","variants":["regular"]},{"family":"Oranienbaum","category":"serif","variants":["regular"]},{"family":"Hind Vadodara","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Rufina","category":"serif","variants":["regular","700"]},{"family":"Quantico","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Concert One","category":"display","variants":["regular"]},{"family":"Marcellus SC","category":"serif","variants":["regular"]},{"family":"Jockey One","category":"sans-serif","variants":["regular"]},{"family":"Parisienne","category":"handwriting","variants":["regular"]},{"family":"Carter One","category":"display","variants":["regular"]},{"family":"Arbutus Slab","category":"serif","variants":["regular"]},{"family":"Slabo 13px","category":"serif","variants":["regular"]},{"family":"Tauri","category":"sans-serif","variants":["regular"]},{"family":"Goudy Bookletter 1911","category":"serif","variants":["regular"]},{"family":"Carrois Gothic","category":"sans-serif","variants":["regular"]},{"family":"Sue Ellen Francisco","category":"handwriting","variants":["regular"]},{"family":"Walter Turncoat","category":"handwriting","variants":["regular"]},{"family":"Annie Use Your Telescope","category":"handwriting","variants":["regular"]},{"family":"Puritan","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Grand Hotel","category":"handwriting","variants":["regular"]},{"family":"Yesteryear","category":"handwriting","variants":["regular"]},{"family":"Jaldi","category":"sans-serif","variants":["regular","700"]},{"family":"Khula","category":"sans-serif","variants":["300","regular","600","700","800"]},{"family":"Cinzel Decorative","category":"display","variants":["regular","700","900"]},{"family":"Crafty Girls","category":"handwriting","variants":["regular"]},{"family":"Merienda","category":"handwriting","variants":["regular","700"]},{"family":"Hind Guntur","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Cutive","category":"serif","variants":["regular"]},{"family":"Prompt","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Coustard","category":"serif","variants":["regular","900"]},{"family":"Arima Madurai","category":"display","variants":["100","200","300","regular","500","700","800","900"]},{"family":"Doppio One","category":"sans-serif","variants":["regular"]},{"family":"Radley","category":"serif","variants":["regular","italic"]},{"family":"Fontdiner Swanky","category":"display","variants":["regular"]},{"family":"Iceland","category":"display","variants":["regular"]},{"family":"Alegreya SC","category":"serif","variants":["regular","italic","700","700italic","900","900italic"]},{"family":"Halant","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Schoolbell","category":"handwriting","variants":["regular"]},{"family":"Waiting for the Sunrise","category":"handwriting","variants":["regular"]},{"family":"Italianno","category":"handwriting","variants":["regular"]},{"family":"Fredericka the Great","category":"display","variants":["regular"]},{"family":"Average Sans","category":"sans-serif","variants":["regular"]},{"family":"Rosario","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Mr Dafoe","category":"handwriting","variants":["regular"]},{"family":"Port Lligat Slab","category":"serif","variants":["regular"]},{"family":"Aclonica","category":"sans-serif","variants":["regular"]},{"family":"Graduate","category":"display","variants":["regular"]},{"family":"Racing Sans One","category":"display","variants":["regular"]},{"family":"Berkshire Swash","category":"handwriting","variants":["regular"]},{"family":"Forum","category":"display","variants":["regular"]},{"family":"Anonymous Pro","category":"monospace","variants":["regular","italic","700","700italic"]},{"family":"Love Ya Like A Sister","category":"display","variants":["regular"]},{"family":"Nunito Sans","category":"sans-serif","variants":["200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Magra","category":"sans-serif","variants":["regular","700"]},{"family":"Lateef","category":"handwriting","variants":["regular"]},{"family":"Assistant","category":"sans-serif","variants":["200","300","regular","600","700","800"]},{"family":"Six Caps","category":"sans-serif","variants":["regular"]},{"family":"Gilda Display","category":"serif","variants":["regular"]},{"family":"Oregano","category":"display","variants":["regular","italic"]},{"family":"Metrophobic","category":"sans-serif","variants":["regular"]},{"family":"Lalezar","category":"display","variants":["regular"]},{"family":"Caudex","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Kelly Slab","category":"display","variants":["regular"]},{"family":"Reem Kufi","category":"sans-serif","variants":["regular"]},{"family":"Cambay","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Gruppo","category":"display","variants":["regular"]},{"family":"Give You Glory","category":"handwriting","variants":["regular"]},{"family":"GFS Didot","category":"serif","variants":["regular"]},{"family":"Duru Sans","category":"sans-serif","variants":["regular"]},{"family":"Andika","category":"sans-serif","variants":["regular"]},{"family":"Tenor Sans","category":"sans-serif","variants":["regular"]},{"family":"Knewave","category":"display","variants":["regular"]},{"family":"Averia Serif Libre","category":"display","variants":["300","300italic","regular","italic","700","700italic"]},{"family":"Eczar","category":"serif","variants":["regular","500","600","700","800"]},{"family":"Inder","category":"sans-serif","variants":["regular"]},{"family":"Martel Sans","category":"sans-serif","variants":["200","300","regular","600","700","800","900"]},{"family":"Trocchi","category":"serif","variants":["regular"]},{"family":"Wire One","category":"sans-serif","variants":["regular"]},{"family":"Petit Formal Script","category":"handwriting","variants":["regular"]},{"family":"Mako","category":"sans-serif","variants":["regular"]},{"family":"Frijole","category":"display","variants":["regular"]},{"family":"Zeyada","category":"handwriting","variants":["regular"]},{"family":"Slackey","category":"display","variants":["regular"]},{"family":"Karma","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Mate","category":"serif","variants":["regular","italic"]},{"family":"Belleza","category":"sans-serif","variants":["regular"]},{"family":"Montez","category":"handwriting","variants":["regular"]},{"family":"Quando","category":"serif","variants":["regular"]},{"family":"Capriola","category":"sans-serif","variants":["regular"]},{"family":"Lilita One","category":"display","variants":["regular"]},{"family":"Trirong","category":"serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Lekton","category":"sans-serif","variants":["regular","italic","700"]},{"family":"Just Me Again Down Here","category":"handwriting","variants":["regular"]},{"family":"Bowlby One SC","category":"display","variants":["regular"]},{"family":"The Girl Next Door","category":"handwriting","variants":["regular"]},{"family":"Happy Monkey","category":"display","variants":["regular"]},{"family":"Merienda One","category":"handwriting","variants":["regular"]},{"family":"Alike","category":"serif","variants":["regular"]},{"family":"Chelsea Market","category":"display","variants":["regular"]},{"family":"Unkempt","category":"display","variants":["regular","700"]},{"family":"Anaheim","category":"sans-serif","variants":["regular"]},{"family":"Strait","category":"sans-serif","variants":["regular"]},{"family":"Brawler","category":"serif","variants":["regular"]},{"family":"Clicker Script","category":"handwriting","variants":["regular"]},{"family":"Delius","category":"handwriting","variants":["regular"]},{"family":"Mouse Memoirs","category":"sans-serif","variants":["regular"]},{"family":"IM Fell Double Pica","category":"serif","variants":["regular","italic"]},{"family":"Crushed","category":"display","variants":["regular"]},{"family":"Rammetto One","category":"display","variants":["regular"]},{"family":"Prosto One","category":"display","variants":["regular"]},{"family":"Kranky","category":"display","variants":["regular"]},{"family":"IM Fell English","category":"serif","variants":["regular","italic"]},{"family":"Aladin","category":"handwriting","variants":["regular"]},{"family":"Mr De Haviland","category":"handwriting","variants":["regular"]},{"family":"NTR","category":"sans-serif","variants":["regular"]},{"family":"Londrina Solid","category":"display","variants":["regular"]},{"family":"Skranji","category":"display","variants":["regular","700"]},{"family":"Allan","category":"display","variants":["regular","700"]},{"family":"Baumans","category":"display","variants":["regular"]},{"family":"Ovo","category":"serif","variants":["regular"]},{"family":"Changa","category":"sans-serif","variants":["200","300","regular","500","600","700","800"]},{"family":"Lemon","category":"display","variants":["regular"]},{"family":"Federo","category":"sans-serif","variants":["regular"]},{"family":"Herr Von Muellerhoff","category":"handwriting","variants":["regular"]},{"family":"Arizonia","category":"handwriting","variants":["regular"]},{"family":"Bowlby One","category":"display","variants":["regular"]},{"family":"Orienta","category":"sans-serif","variants":["regular"]},{"family":"Short Stack","category":"handwriting","variants":["regular"]},{"family":"Bungee Inline","category":"display","variants":["regular"]},{"family":"Andada","category":"serif","variants":["regular"]},{"family":"Baloo Paaji","category":"display","variants":["regular"]},{"family":"Oxygen Mono","category":"monospace","variants":["regular"]},{"family":"Yeseva One","category":"display","variants":["regular"]},{"family":"UnifrakturMaguntia","category":"display","variants":["regular"]},{"family":"Bentham","category":"serif","variants":["regular"]},{"family":"Londrina Outline","category":"display","variants":["regular"]},{"family":"Pompiere","category":"display","variants":["regular"]},{"family":"Gabriela","category":"serif","variants":["regular"]},{"family":"Qwigley","category":"handwriting","variants":["regular"]},{"family":"Nova Square","category":"display","variants":["regular"]},{"family":"Poly","category":"serif","variants":["regular","italic"]},{"family":"Sniglet","category":"display","variants":["regular","800"]},{"family":"Patrick Hand SC","category":"handwriting","variants":["regular"]},{"family":"Gravitas One","category":"display","variants":["regular"]},{"family":"Khmer","category":"display","variants":["regular"]},{"family":"Shojumaru","category":"display","variants":["regular"]},{"family":"Kurale","category":"serif","variants":["regular"]},{"family":"Gafata","category":"sans-serif","variants":["regular"]},{"family":"Biryani","category":"sans-serif","variants":["200","300","regular","600","700","800","900"]},{"family":"Cambo","category":"serif","variants":["regular"]},{"family":"Titan One","category":"display","variants":["regular"]},{"family":"Carrois Gothic SC","category":"sans-serif","variants":["regular"]},{"family":"La Belle Aurore","category":"handwriting","variants":["regular"]},{"family":"Holtwood One SC","category":"serif","variants":["regular"]},{"family":"Oleo Script Swash Caps","category":"display","variants":["regular","700"]},{"family":"Headland One","category":"serif","variants":["regular"]},{"family":"Cherry Swash","category":"display","variants":["regular","700"]},{"family":"Belgrano","category":"serif","variants":["regular"]},{"family":"Norican","category":"handwriting","variants":["regular"]},{"family":"Mountains of Christmas","category":"display","variants":["regular","700"]},{"family":"Julee","category":"handwriting","variants":["regular"]},{"family":"Ramabhadra","category":"sans-serif","variants":["regular"]},{"family":"Mallanna","category":"sans-serif","variants":["regular"]},{"family":"Kristi","category":"handwriting","variants":["regular"]},{"family":"Imprima","category":"sans-serif","variants":["regular"]},{"family":"Lily Script One","category":"display","variants":["regular"]},{"family":"Chau Philomene One","category":"sans-serif","variants":["regular","italic"]},{"family":"Bilbo Swash Caps","category":"handwriting","variants":["regular"]},{"family":"Finger Paint","category":"display","variants":["regular"]},{"family":"Voces","category":"display","variants":["regular"]},{"family":"Itim","category":"handwriting","variants":["regular"]},{"family":"Megrim","category":"display","variants":["regular"]},{"family":"Simonetta","category":"display","variants":["regular","italic","900","900italic"]},{"family":"Cutive Mono","category":"monospace","variants":["regular"]},{"family":"Stardos Stencil","category":"display","variants":["regular","700"]},{"family":"IM Fell DW Pica","category":"serif","variants":["regular","italic"]},{"family":"Unna","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Loved by the King","category":"handwriting","variants":["regular"]},{"family":"Prociono","category":"serif","variants":["regular"]},{"family":"Corben","category":"display","variants":["regular","700"]},{"family":"Amiko","category":"sans-serif","variants":["regular","600","700"]},{"family":"Denk One","category":"sans-serif","variants":["regular"]},{"family":"Palanquin","category":"sans-serif","variants":["100","200","300","regular","500","600","700"]},{"family":"Baloo","category":"display","variants":["regular"]},{"family":"Fondamento","category":"handwriting","variants":["regular","italic"]},{"family":"Seaweed Script","category":"display","variants":["regular"]},{"family":"Shanti","category":"sans-serif","variants":["regular"]},{"family":"Wendy One","category":"sans-serif","variants":["regular"]},{"family":"Raleway Dots","category":"display","variants":["regular"]},{"family":"Amethysta","category":"serif","variants":["regular"]},{"family":"Vast Shadow","category":"display","variants":["regular"]},{"family":"Stalemate","category":"handwriting","variants":["regular"]},{"family":"Fira Mono","category":"monospace","variants":["regular","500","700"]},{"family":"Expletus Sans","category":"display","variants":["regular","italic","500","500italic","600","600italic","700","700italic"]},{"family":"Scheherazade","category":"serif","variants":["regular","700"]},{"family":"Fenix","category":"serif","variants":["regular"]},{"family":"Delius Swash Caps","category":"handwriting","variants":["regular"]},{"family":"Rouge Script","category":"handwriting","variants":["regular"]},{"family":"Life Savers","category":"display","variants":["regular","700"]},{"family":"IM Fell English SC","category":"serif","variants":["regular"]},{"family":"Meddon","category":"handwriting","variants":["regular"]},{"family":"Tienne","category":"serif","variants":["regular","700","900"]},{"family":"Over the Rainbow","category":"handwriting","variants":["regular"]},{"family":"Share Tech Mono","category":"monospace","variants":["regular"]},{"family":"Abhaya Libre","category":"serif","variants":["regular","500","600","700","800"]},{"family":"Kotta One","category":"serif","variants":["regular"]},{"family":"Pridi","category":"serif","variants":["200","300","regular","500","600","700"]},{"family":"Euphoria Script","category":"handwriting","variants":["regular"]},{"family":"Engagement","category":"handwriting","variants":["regular"]},{"family":"Podkova","category":"serif","variants":["regular","500","600","700","800"]},{"family":"Salsa","category":"display","variants":["regular"]},{"family":"Sofia","category":"handwriting","variants":["regular"]},{"family":"Suranna","category":"serif","variants":["regular"]},{"family":"Mada","category":"sans-serif","variants":["300","regular","500","900"]},{"family":"Chonburi","category":"display","variants":["regular"]},{"family":"Ledger","category":"serif","variants":["regular"]},{"family":"Italiana","category":"serif","variants":["regular"]},{"family":"Dawning of a New Day","category":"handwriting","variants":["regular"]},{"family":"Nokora","category":"serif","variants":["regular","700"]},{"family":"Angkor","category":"display","variants":["regular"]},{"family":"Medula One","category":"display","variants":["regular"]},{"family":"Taviraj","category":"serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Vampiro One","category":"display","variants":["regular"]},{"family":"Englebert","category":"sans-serif","variants":["regular"]},{"family":"Nova Mono","category":"monospace","variants":["regular"]},{"family":"Dorsa","category":"sans-serif","variants":["regular"]},{"family":"Averia Sans Libre","category":"display","variants":["300","300italic","regular","italic","700","700italic"]},{"family":"Cedarville Cursive","category":"handwriting","variants":["regular"]},{"family":"Geo","category":"sans-serif","variants":["regular","italic"]},{"family":"Rationale","category":"sans-serif","variants":["regular"]},{"family":"Balthazar","category":"serif","variants":["regular"]},{"family":"Sunshiney","category":"handwriting","variants":["regular"]},{"family":"Aguafina Script","category":"handwriting","variants":["regular"]},{"family":"Mystery Quest","category":"display","variants":["regular"]},{"family":"Fjord One","category":"serif","variants":["regular"]},{"family":"Fira Sans Extra Condensed","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Kadwa","category":"serif","variants":["regular","700"]},{"family":"McLaren","category":"display","variants":["regular"]},{"family":"Rye","category":"display","variants":["regular"]},{"family":"Sail","category":"display","variants":["regular"]},{"family":"Athiti","category":"sans-serif","variants":["200","300","regular","500","600","700"]},{"family":"Rosarivo","category":"serif","variants":["regular","italic"]},{"family":"Kite One","category":"sans-serif","variants":["regular"]},{"family":"Odor Mean Chey","category":"display","variants":["regular"]},{"family":"Numans","category":"sans-serif","variants":["regular"]},{"family":"Artifika","category":"serif","variants":["regular"]},{"family":"Poller One","category":"display","variants":["regular"]},{"family":"Gurajada","category":"serif","variants":["regular"]},{"family":"Inika","category":"serif","variants":["regular","700"]},{"family":"Mandali","category":"sans-serif","variants":["regular"]},{"family":"Bungee Shade","category":"display","variants":["regular"]},{"family":"Suwannaphum","category":"display","variants":["regular"]},{"family":"Mate SC","category":"serif","variants":["regular"]},{"family":"Creepster","category":"display","variants":["regular"]},{"family":"Arsenal","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Montserrat Subrayada","category":"sans-serif","variants":["regular","700"]},{"family":"Metamorphous","category":"display","variants":["regular"]},{"family":"Palanquin Dark","category":"sans-serif","variants":["regular","500","600","700"]},{"family":"Coda Caption","category":"sans-serif","variants":["800"]},{"family":"Dynalight","category":"display","variants":["regular"]},{"family":"Maitree","category":"serif","variants":["200","300","regular","500","600","700"]},{"family":"IM Fell French Canon","category":"serif","variants":["regular","italic"]},{"family":"Amarante","category":"display","variants":["regular"]},{"family":"Quintessential","category":"handwriting","variants":["regular"]},{"family":"Codystar","category":"display","variants":["300","regular"]},{"family":"Cantora One","category":"sans-serif","variants":["regular"]},{"family":"Aref Ruqaa","category":"serif","variants":["regular","700"]},{"family":"Griffy","category":"display","variants":["regular"]},{"family":"Revalia","category":"display","variants":["regular"]},{"family":"Buenard","category":"serif","variants":["regular","700"]},{"family":"Donegal One","category":"serif","variants":["regular"]},{"family":"IM Fell DW Pica SC","category":"serif","variants":["regular"]},{"family":"Habibi","category":"serif","variants":["regular"]},{"family":"Share Tech","category":"sans-serif","variants":["regular"]},{"family":"Flamenco","category":"display","variants":["300","regular"]},{"family":"Mitr","category":"sans-serif","variants":["200","300","regular","500","600","700"]},{"family":"Maiden Orange","category":"display","variants":["regular"]},{"family":"Diplomata SC","category":"display","variants":["regular"]},{"family":"Cormorant","category":"serif","variants":["300","300italic","regular","italic","500","500italic","600","600italic","700","700italic"]},{"family":"Delius Unicase","category":"handwriting","variants":["regular","700"]},{"family":"Stoke","category":"serif","variants":["300","regular"]},{"family":"Vibur","category":"handwriting","variants":["regular"]},{"family":"Sarpanch","category":"sans-serif","variants":["regular","500","600","700","800","900"]},{"family":"Baloo Bhaina","category":"display","variants":["regular"]},{"family":"Baloo Tamma","category":"display","variants":["regular"]},{"family":"Averia Libre","category":"display","variants":["300","300italic","regular","italic","700","700italic"]},{"family":"Esteban","category":"serif","variants":["regular"]},{"family":"Battambang","category":"display","variants":["regular","700"]},{"family":"Bokor","category":"display","variants":["regular"]},{"family":"Wallpoet","category":"display","variants":["regular"]},{"family":"IM Fell Great Primer","category":"serif","variants":["regular","italic"]},{"family":"Tulpen One","category":"display","variants":["regular"]},{"family":"Proza Libre","category":"sans-serif","variants":["regular","italic","500","500italic","600","600italic","700","700italic","800","800italic"]},{"family":"IM Fell French Canon SC","category":"serif","variants":["regular"]},{"family":"Vesper Libre","category":"serif","variants":["regular","500","700","900"]},{"family":"Rozha One","category":"serif","variants":["regular"]},{"family":"Sansita","category":"sans-serif","variants":["regular","italic","700","700italic","800","800italic","900","900italic"]},{"family":"Kavoon","category":"display","variants":["regular"]},{"family":"Della Respira","category":"serif","variants":["regular"]},{"family":"Milonga","category":"display","variants":["regular"]},{"family":"Condiment","category":"handwriting","variants":["regular"]},{"family":"Junge","category":"serif","variants":["regular"]},{"family":"New Rocker","category":"display","variants":["regular"]},{"family":"Chicle","category":"display","variants":["regular"]},{"family":"Mrs Saint Delafield","category":"handwriting","variants":["regular"]},{"family":"League Script","category":"handwriting","variants":["regular"]},{"family":"Miniver","category":"display","variants":["regular"]},{"family":"Galindo","category":"display","variants":["regular"]},{"family":"Moul","category":"display","variants":["regular"]},{"family":"Amatica SC","category":"display","variants":["regular","700"]},{"family":"Stint Ultra Expanded","category":"display","variants":["regular"]},{"family":"Yrsa","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Stint Ultra Condensed","category":"display","variants":["regular"]},{"family":"Suez One","category":"serif","variants":["regular"]},{"family":"Text Me One","category":"sans-serif","variants":["regular"]},{"family":"Buda","category":"display","variants":["300"]},{"family":"Ruluko","category":"sans-serif","variants":["regular"]},{"family":"Sonsie One","category":"display","variants":["regular"]},{"family":"Krona One","category":"sans-serif","variants":["regular"]},{"family":"Elsie","category":"display","variants":["regular","900"]},{"family":"Pangolin","category":"handwriting","variants":["regular"]},{"family":"Linden Hill","category":"serif","variants":["regular","italic"]},{"family":"Secular One","category":"sans-serif","variants":["regular"]},{"family":"IM Fell Double Pica SC","category":"serif","variants":["regular"]},{"family":"Alike Angular","category":"serif","variants":["regular"]},{"family":"Sancreek","category":"display","variants":["regular"]},{"family":"Bilbo","category":"handwriting","variants":["regular"]},{"family":"Antic Didone","category":"serif","variants":["regular"]},{"family":"Paprika","category":"display","variants":["regular"]},{"family":"Asul","category":"sans-serif","variants":["regular","700"]},{"family":"David Libre","category":"serif","variants":["regular","500","700"]},{"family":"Almendra","category":"serif","variants":["regular","italic","700","700italic"]},{"family":"Sriracha","category":"handwriting","variants":["regular"]},{"family":"Miriam Libre","category":"sans-serif","variants":["regular","700"]},{"family":"Ribeye","category":"display","variants":["regular"]},{"family":"Swanky and Moo Moo","category":"handwriting","variants":["regular"]},{"family":"Trade Winds","category":"display","variants":["regular"]},{"family":"Overlock SC","category":"display","variants":["regular"]},{"family":"Nova Round","category":"display","variants":["regular"]},{"family":"Cagliostro","category":"sans-serif","variants":["regular"]},{"family":"Glass Antiqua","category":"display","variants":["regular"]},{"family":"Offside","category":"display","variants":["regular"]},{"family":"El Messiri","category":"sans-serif","variants":["regular","500","600","700"]},{"family":"Bigshot One","category":"display","variants":["regular"]},{"family":"IM Fell Great Primer SC","category":"serif","variants":["regular"]},{"family":"Akronim","category":"display","variants":["regular"]},{"family":"Fira Sans Condensed","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","500","500italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Nosifer","category":"display","variants":["regular"]},{"family":"Autour One","category":"display","variants":["regular"]},{"family":"Pirata One","category":"display","variants":["regular"]},{"family":"Port Lligat Sans","category":"sans-serif","variants":["regular"]},{"family":"Scope One","category":"serif","variants":["regular"]},{"family":"Sumana","category":"serif","variants":["regular","700"]},{"family":"Lemonada","category":"display","variants":["300","regular","600","700"]},{"family":"Montaga","category":"serif","variants":["regular"]},{"family":"Iceberg","category":"display","variants":["regular"]},{"family":"Ruthie","category":"handwriting","variants":["regular"]},{"family":"Content","category":"display","variants":["regular","700"]},{"family":"Henny Penny","category":"display","variants":["regular"]},{"family":"Nova Slim","category":"display","variants":["regular"]},{"family":"Hind Madurai","category":"sans-serif","variants":["300","regular","500","600","700"]},{"family":"Harmattan","category":"sans-serif","variants":["regular"]},{"family":"UnifrakturCook","category":"display","variants":["700"]},{"family":"Sarina","category":"display","variants":["regular"]},{"family":"Bubbler One","category":"sans-serif","variants":["regular"]},{"family":"Dekko","category":"handwriting","variants":["regular"]},{"family":"Redressed","category":"handwriting","variants":["regular"]},{"family":"Laila","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Faster One","category":"display","variants":["regular"]},{"family":"Arya","category":"sans-serif","variants":["regular","700"]},{"family":"Peralta","category":"display","variants":["regular"]},{"family":"Meie Script","category":"handwriting","variants":["regular"]},{"family":"Overpass","category":"sans-serif","variants":["100","100italic","200","200italic","300","300italic","regular","italic","600","600italic","700","700italic","800","800italic","900","900italic"]},{"family":"Monsieur La Doulaise","category":"handwriting","variants":["regular"]},{"family":"Bungee","category":"display","variants":["regular"]},{"family":"Snippet","category":"sans-serif","variants":["regular"]},{"family":"Emilys Candy","category":"display","variants":["regular"]},{"family":"Trykker","category":"serif","variants":["regular"]},{"family":"MedievalSharp","category":"display","variants":["regular"]},{"family":"Space Mono","category":"monospace","variants":["regular","italic","700","700italic"]},{"family":"Croissant One","category":"display","variants":["regular"]},{"family":"Monofett","category":"display","variants":["regular"]},{"family":"Lovers Quarrel","category":"handwriting","variants":["regular"]},{"family":"Oldenburg","category":"display","variants":["regular"]},{"family":"Galdeano","category":"sans-serif","variants":["regular"]},{"family":"Spicy Rice","category":"display","variants":["regular"]},{"family":"Wellfleet","category":"display","variants":["regular"]},{"family":"Germania One","category":"display","variants":["regular"]},{"family":"GFS Neohellenic","category":"sans-serif","variants":["regular","italic","700","700italic"]},{"family":"Baloo Thambi","category":"display","variants":["regular"]},{"family":"Jolly Lodger","category":"display","variants":["regular"]},{"family":"Rubik Mono One","category":"sans-serif","variants":["regular"]},{"family":"Pattaya","category":"sans-serif","variants":["regular"]},{"family":"Joti One","category":"display","variants":["regular"]},{"family":"Siemreap","category":"display","variants":["regular"]},{"family":"Ranga","category":"display","variants":["regular","700"]},{"family":"Chango","category":"display","variants":["regular"]},{"family":"Miltonian Tattoo","category":"display","variants":["regular"]},{"family":"Koulen","category":"display","variants":["regular"]},{"family":"Eagle Lake","category":"handwriting","variants":["regular"]},{"family":"Nova Flat","category":"display","variants":["regular"]},{"family":"Petrona","category":"serif","variants":["regular"]},{"family":"Jacques Francois","category":"serif","variants":["regular"]},{"family":"Amita","category":"handwriting","variants":["regular","700"]},{"family":"Plaster","category":"display","variants":["regular"]},{"family":"Ramaraja","category":"serif","variants":["regular"]},{"family":"Sura","category":"serif","variants":["regular","700"]},{"family":"Kenia","category":"display","variants":["regular"]},{"family":"Fresca","category":"sans-serif","variants":["regular"]},{"family":"Jomhuria","category":"display","variants":["regular"]},{"family":"Lancelot","category":"display","variants":["regular"]},{"family":"Pavanam","category":"sans-serif","variants":["regular"]},{"family":"Rum Raisin","category":"sans-serif","variants":["regular"]},{"family":"Almendra SC","category":"serif","variants":["regular"]},{"family":"Purple Purse","category":"display","variants":["regular"]},{"family":"Kumar One","category":"display","variants":["regular"]},{"family":"Modern Antiqua","category":"display","variants":["regular"]},{"family":"Piedra","category":"display","variants":["regular"]},{"family":"Irish Grover","category":"display","variants":["regular"]},{"family":"Molle","category":"handwriting","variants":["italic"]},{"family":"Cormorant Infant","category":"serif","variants":["300","300italic","regular","italic","500","500italic","600","600italic","700","700italic"]},{"family":"Margarine","category":"display","variants":["regular"]},{"family":"Sahitya","category":"serif","variants":["regular","700"]},{"family":"Mukta Vaani","category":"sans-serif","variants":["200","300","regular","500","600","700","800"]},{"family":"Astloch","category":"display","variants":["regular","700"]},{"family":"Snowburst One","category":"display","variants":["regular"]},{"family":"Rhodium Libre","category":"serif","variants":["regular"]},{"family":"Smythe","category":"display","variants":["regular"]},{"family":"Asset","category":"display","variants":["regular"]},{"family":"Ewert","category":"display","variants":["regular"]},{"family":"Keania One","category":"display","variants":["regular"]},{"family":"Ranchers","category":"display","variants":["regular"]},{"family":"Gorditas","category":"display","variants":["regular","700"]},{"family":"Sirin Stencil","category":"display","variants":["regular"]},{"family":"Averia Gruesa Libre","category":"display","variants":["regular"]},{"family":"Trochut","category":"display","variants":["regular","italic","700"]},{"family":"Baloo Chettan","category":"display","variants":["regular"]},{"family":"Kdam Thmor","category":"display","variants":["regular"]},{"family":"Timmana","category":"sans-serif","variants":["regular"]},{"family":"Original Surfer","category":"display","variants":["regular"]},{"family":"Supermercado One","category":"display","variants":["regular"]},{"family":"Mirza","category":"display","variants":["regular","500","600","700"]},{"family":"Passero One","category":"display","variants":["regular"]},{"family":"Nova Oval","category":"display","variants":["regular"]},{"family":"Caesar Dressing","category":"display","variants":["regular"]},{"family":"Taprom","category":"display","variants":["regular"]},{"family":"Fascinate","category":"display","variants":["regular"]},{"family":"Seymour One","category":"sans-serif","variants":["regular"]},{"family":"Freehand","category":"display","variants":["regular"]},{"family":"Ravi Prakash","category":"display","variants":["regular"]},{"family":"Coiny","category":"display","variants":["regular"]},{"family":"Atomic Age","category":"display","variants":["regular"]},{"family":"Jacques Francois Shadow","category":"display","variants":["regular"]},{"family":"Diplomata","category":"display","variants":["regular"]},{"family":"Dr Sugiyama","category":"handwriting","variants":["regular"]},{"family":"Miltonian","category":"display","variants":["regular"]},{"family":"Ribeye Marrow","category":"display","variants":["regular"]},{"family":"Elsie Swash Caps","category":"display","variants":["regular","900"]},{"family":"Felipa","category":"handwriting","variants":["regular"]},{"family":"Galada","category":"display","variants":["regular"]},{"family":"Nova Script","category":"display","variants":["regular"]},{"family":"Bayon","category":"display","variants":["regular"]},{"family":"Underdog","category":"display","variants":["regular"]},{"family":"Devonshire","category":"handwriting","variants":["regular"]},{"family":"Atma","category":"display","variants":["300","regular","500","600","700"]},{"family":"Londrina Shadow","category":"display","variants":["regular"]},{"family":"Sofadi One","category":"display","variants":["regular"]},{"family":"Tillana","category":"handwriting","variants":["regular","500","600","700","800"]},{"family":"Inknut Antiqua","category":"serif","variants":["300","regular","500","600","700","800","900"]},{"family":"Goblin One","category":"display","variants":["regular"]},{"family":"Rakkas","category":"display","variants":["regular"]},{"family":"Metal","category":"display","variants":["regular"]},{"family":"Farsan","category":"display","variants":["regular"]},{"family":"Londrina Sketch","category":"display","variants":["regular"]},{"family":"Nova Cut","category":"display","variants":["regular"]},{"family":"BioRhyme","category":"serif","variants":["200","300","regular","700","800"]},{"family":"Warnes","category":"display","variants":["regular"]},{"family":"Romanesco","category":"handwriting","variants":["regular"]},{"family":"Fascinate Inline","category":"display","variants":["regular"]},{"family":"Mrs Sheppards","category":"handwriting","variants":["regular"]},{"family":"Princess Sofia","category":"handwriting","variants":["regular"]},{"family":"Modak","category":"display","variants":["regular"]},{"family":"Spirax","category":"display","variants":["regular"]},{"family":"Cormorant Upright","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Geostar Fill","category":"display","variants":["regular"]},{"family":"Cormorant SC","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Baloo Bhai","category":"display","variants":["regular"]},{"family":"Sree Krushnadevaraya","category":"serif","variants":["regular"]},{"family":"Smokum","category":"display","variants":["regular"]},{"family":"Arbutus","category":"display","variants":["regular"]},{"family":"Fruktur","category":"display","variants":["regular"]},{"family":"Geostar","category":"display","variants":["regular"]},{"family":"Marko One","category":"serif","variants":["regular"]},{"family":"Erica One","category":"display","variants":["regular"]},{"family":"Yatra One","category":"display","variants":["regular"]},{"family":"Uncial Antiqua","category":"display","variants":["regular"]},{"family":"Combo","category":"display","variants":["regular"]},{"family":"Gidugu","category":"sans-serif","variants":["regular"]},{"family":"Aubrey","category":"display","variants":["regular"]},{"family":"Jim Nightshade","category":"handwriting","variants":["regular"]},{"family":"Butterfly Kids","category":"handwriting","variants":["regular"]},{"family":"Metal Mania","category":"display","variants":["regular"]},{"family":"Macondo","category":"display","variants":["regular"]},{"family":"Chenla","category":"display","variants":["regular"]},{"family":"Miss Fajardose","category":"handwriting","variants":["regular"]},{"family":"Macondo Swash Caps","category":"display","variants":["regular"]},{"family":"Barrio","category":"display","variants":["regular"]},{"family":"Sevillana","category":"display","variants":["regular"]},{"family":"Bigelow Rules","category":"display","variants":["regular"]},{"family":"Rasa","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Risque","category":"display","variants":["regular"]},{"family":"Federant","category":"display","variants":["regular"]},{"family":"Dangrek","category":"display","variants":["regular"]},{"family":"Chathura","category":"sans-serif","variants":["100","300","regular","700","800"]},{"family":"Almendra Display","category":"display","variants":["regular"]},{"family":"Chela One","category":"display","variants":["regular"]},{"family":"Bonbon","category":"handwriting","variants":["regular"]},{"family":"Stalinist One","category":"display","variants":["regular"]},{"family":"Mr Bedfort","category":"handwriting","variants":["regular"]},{"family":"Eater","category":"display","variants":["regular"]},{"family":"Fasthand","category":"serif","variants":["regular"]},{"family":"Mogra","category":"display","variants":["regular"]},{"family":"Padauk","category":"sans-serif","variants":["regular","700"]},{"family":"Preahvihear","category":"display","variants":["regular"]},{"family":"Flavors","category":"display","variants":["regular"]},{"family":"Ruge Boogie","category":"handwriting","variants":["regular"]},{"family":"Tenali Ramakrishna","category":"sans-serif","variants":["regular"]},{"family":"Unlock","category":"display","variants":["regular"]},{"family":"Butcherman","category":"display","variants":["regular"]},{"family":"Baloo Da","category":"display","variants":["regular"]},{"family":"Lakki Reddy","category":"handwriting","variants":["regular"]},{"family":"Cormorant Unicase","category":"serif","variants":["300","regular","500","600","700"]},{"family":"Katibeh","category":"display","variants":["regular"]},{"family":"Asar","category":"serif","variants":["regular"]},{"family":"Emblema One","category":"display","variants":["regular"]},{"family":"Moulpali","category":"display","variants":["regular"]},{"family":"Meera Inimai","category":"sans-serif","variants":["regular"]},{"family":"Kantumruy","category":"sans-serif","variants":["300","regular","700"]},{"family":"Suravaram","category":"serif","variants":["regular"]},{"family":"Bungee Hairline","category":"display","variants":["regular"]},{"family":"Overpass Mono","category":"monospace","variants":["300","regular","600","700"]},{"family":"Peddana","category":"serif","variants":["regular"]},{"family":"Bahiana","category":"display","variants":["regular"]},{"family":"Kumar One Outline","category":"display","variants":["regular"]},{"family":"Hanalei Fill","category":"display","variants":["regular"]},{"family":"Dhurjati","category":"sans-serif","variants":["regular"]},{"family":"Hanalei","category":"display","variants":["regular"]},{"family":"Kavivanar","category":"handwriting","variants":["regular"]},{"family":"Bungee Outline","category":"display","variants":["regular"]},{"family":"BioRhyme Expanded","category":"serif","variants":["200","300","regular","700","800"]}]' );

	// Loop through them and put what we need into our fonts array
	$fonts = array();
	foreach ( $content as $item ) {

		// Grab what we need from our big list
		$atts = array( 
			'name'     => $item->family,
			'category' => $item->category,
			'variants' => $item->variants
		);

		// Create an ID using our font family name
		$id = strtolower( str_replace( ' ', '_', $item->family ) );

		// Add our attributes to our new array
		$fonts[ $id ] = $atts;
	}

	if ( 'all' !== $amount ) {
		$fonts = array_slice( $fonts, 0, $amount );
	}

	// Alphabetize our fonts
	if ( apply_filters( 'generate_alphabetize_google_fonts', true ) ) {
		asort( $fonts );
	}
	
	// Filter to allow us to modify the fonts array
	return apply_filters( 'generate_google_fonts_array', $fonts );
}
endif;

if ( ! function_exists( 'generate_get_all_google_fonts_ajax' ) ) :
/**
 * Return an array of all of our Google Fonts
 * @since 1.3.0
 */
add_action( 'wp_ajax_generate_get_all_google_fonts_ajax', 'generate_get_all_google_fonts_ajax' );
function generate_get_all_google_fonts_ajax() {
	// Bail if the nonce doesn't check out
	if ( ! isset( $_POST[ 'gp_customize_nonce' ] ) || ! wp_verify_nonce( $_POST[ 'gp_customize_nonce' ], 'gp_customize_nonce' ) ) {
		wp_die();
	}

	// Do another nonce check
	check_ajax_referer( 'gp_customize_nonce', 'gp_customize_nonce' );

	// Bail if user can't edit theme options
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die();
	}

	// Get all of our fonts
	$fonts = apply_filters( 'generate_typography_customize_list', generate_get_all_google_fonts() );

	// Send all of our fonts in JSON format
	echo wp_json_encode( $fonts );

	// Exit
	die();
}
endif;

if ( ! function_exists( 'generate_get_google_font_variants' ) ) :
/**
 * Wrapper function to find variants for chosen Google Fonts
 * Example: generate_get_google_font_variation( 'Open Sans' )
 * @since 1.3.0
 */
function generate_get_google_font_variants( $font, $key = '', $default = '' )
{
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) )
		return;
	
	// Don't need variants if we're using a system font
	if ( in_array( $font, generate_typography_default_fonts() ) )
		return;
	
	// Return if we have our variants saved
	if ( '' !== $key && get_theme_mod( $key . '_variants' ) ) return get_theme_mod( $key . '_variants' );
	
	// Make sure we have defaults
	if ( '' == $default ) $default = generate_get_default_fonts();
	
	// If our default font is selected and the category isn't saved, we already know the category
	if ( $default[ $key ] == $font ) return $default[ $key . '_variants' ];
	
	// Grab all of our fonts
	// It's a big list, so hopefully we're not even still reading
	$fonts = generate_get_all_google_fonts();
	
	// Get the ID from our font
	$id = strtolower( str_replace( ' ', '_', $font ) );
	
	// If the ID doesn't exist within our fonts, we can bail
	if ( ! array_key_exists( $id, $fonts ) )
		return;
	
	// Grab all of the variants associated with our font
	$variants = $fonts[$id]['variants'];
	
	// Loop through them and put them into an array, then turn them into a comma separated list
	$output = array();
	if ( $variants ) :
		foreach ( $variants as $variant ) {
			$output[] = $variant;
		}
		return implode(',', apply_filters( 'generate_typography_variants', $output ));
	endif;
	
}
endif;

if ( ! function_exists( 'generate_get_google_font_category' ) ) :
/**
 * Wrapper function to find the category for chosen Google Font
 * Example: generate_get_google_font_category( 'Open Sans' )
 * @since 1.3.0
 */
function generate_get_google_font_category( $font, $key = '', $default = '' )
{
	// Bail if we don't have our defaults function
	if ( ! function_exists( 'generate_get_default_fonts' ) )
		return;
	
	// Don't need a category if we're using a system font
	if ( in_array( $font, generate_typography_default_fonts() ) )
		return;
	
	// Return if we have our variants saved
	if ( '' !== $key && get_theme_mod( $key . '_category' ) ) return ', ' . get_theme_mod( $key . '_category' );
	
	// Make sure we have defaults
	if ( '' == $default ) $default = generate_get_default_fonts();
	
	// If our default font is selected and the category isn't saved, we already know the category
	if ( $default[ $key ] == $font ) return ', ' . $default[ $key . '_category' ];
	
	// Get all of our fonts
	// It's a big list, so hopefully we're not even still reading
	$fonts = generate_get_all_google_fonts();
	
	// Get the ID from our font
	$id = strtolower( str_replace( ' ', '_', $font ) );
	
	// If the ID doesn't exist within our fonts, we can bail
	if ( ! array_key_exists( $id, $fonts ) )
		return;
	
	// Let's grab our category to go with our font
	$category = ! empty( $fonts[$id]['category'] ) ? ', ' . $fonts[$id]['category'] : '';
	
	// Return it to be used by our function
	return $category;
	
}
endif;

if ( ! function_exists( 'generate_get_font_family_css' ) ) :
/**
 * Wrapper function to create font-family value for CSS
 * @since 1.3.0
 */
function generate_get_font_family_css( $font, $settings, $default )
{
	$generate_settings = wp_parse_args( 
		get_option( $settings, array() ), 
		$default 
	);
	
	// We don't want to wrap quotes around these values
	$no_quotes = array(
		'inherit',
		'Arial, Helvetica, sans-serif',
		'Georgia, Times New Roman, Times, serif',
		'Helvetica',
		'Impact',
		'Segoe UI, Helvetica Neue, Helvetica, sans-serif',
		'Tahoma, Geneva, sans-serif',
		'Trebuchet MS, Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif'
	);
	
	// Get our font
	$font_family = $generate_settings[ $font ];
	
	// If our value is still using the old format, fix it
	if ( strpos( $font_family, ':' ) !== false )
		$font_family = current( explode( ':', $font_family ) );

	// Set up our wrapper
	if ( in_array( $font_family, $no_quotes ) ) :
		$wrapper_start = null;
		$wrapper_end = null;
	else :
		$wrapper_start = '"';
		$wrapper_end = '"' . generate_get_google_font_category( $font_family, $font, $default );
	endif;
	
	// Output the CSS
	$output = ( 'inherit' == $font_family ) ? 'inherit' : $wrapper_start . $font_family . $wrapper_end;
	return $output;
}
endif;

if ( ! function_exists( 'generate_typography_customizer_live_preview' ) ) :
/**
 * Add our live preview JS
 */
add_action( 'customize_preview_init', 'generate_typography_customizer_live_preview' );
function generate_typography_customizer_live_preview()
{
	wp_enqueue_script( 
		  'generate-typography-customizer',
		  trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/customizer.js',
		  array( 'jquery','customize-preview' ),
		  GENERATE_FONT_VERSION,
		  true
	);
	
	wp_localize_script( 'generate-typography-customizer', 'gp_typography', array(
		'mobile' => apply_filters( 'generate_mobile_media_query', '(max-width:768px)' ),
		'tablet' => apply_filters( 'generate_tablet_media_query', '(min-width: 769px) and (max-width: 1024px)' ),
		'desktop' => apply_filters( 'generate_desktop_media_query', '(min-width:1025px)' ),
	) );
}
endif;

if ( ! function_exists( 'generate_typography_default_fonts' ) ) :
/**
 * Get our system fonts
 */
function generate_typography_default_fonts() {
	$fonts = array(
		'inherit',
		'Arial, Helvetica, sans-serif',
		'Century Gothic',
		'Comic Sans MS',
		'Courier New',
		'Georgia, Times New Roman, Times, serif',
		'Helvetica',
		'Impact',
		'Lucida Console',
		'Lucida Sans Unicode',
		'Palatino Linotype',
		'Segoe UI, Helvetica Neue, Helvetica, sans-serif',
		'Tahoma, Geneva, sans-serif',
		'Trebuchet MS, Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif'
	);
	
	return apply_filters( 'generate_typography_default_fonts', $fonts );
}
endif;

if ( ! function_exists( 'generate_include_typography_defaults' ) ) :
/**
 * Check if we should include our default.css file
 * @since 1.3.42
 */
function generate_include_typography_defaults() {
	return true;
}
endif;

if ( ! function_exists( 'generate_typography_premium_css_defaults' ) ) :
/**
 * Add premium control defaults
 *
 * @since 1.3
 */
add_filter( 'generate_font_option_defaults','generate_typography_premium_css_defaults' );
function generate_typography_premium_css_defaults( $defaults ) {

	$defaults[ 'mobile_navigation_font_size' ] = '';
	return $defaults;
	
}
endif;

if ( ! function_exists( 'generate_typography_premium_css' ) ) :
/**
 * Add premium control CSS
 *
 * @since 1.3
 */
add_filter( 'generate_typography_css_output','generate_typography_premium_css' );
function generate_typography_premium_css( $css ) {
	
	$generate_settings = wp_parse_args( 
		get_option( 'generate_settings', array() ), 
		generate_get_default_fonts() 
	);
	
	// Initiate our CSS class
	require_once GP_LIBRARY_DIRECTORY . 'class-make-css.php';
	$premium_css = new GeneratePress_Pro_CSS;
	
	if ( '' !== $generate_settings['mobile_navigation_font_size'] ) {
		$mobile_subnav_font_size = $generate_settings['mobile_navigation_font_size'] >= 17 ? $generate_settings['mobile_navigation_font_size'] - 3 : $generate_settings['mobile_navigation_font_size'] - 1;
	}
	
	// Mobile
	$premium_css->start_media_query( apply_filters( 'generate_mobile_media_query', '(max-width:768px)' ) );
		if ( ( '' !== generate_get_navigation_location() || is_customize_preview() ) && '' !== $generate_settings[ 'mobile_navigation_font_size' ] ) {
			$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) a, .menu-toggle' );
			$premium_css->add_property( 'font-size', absint( $generate_settings[ 'mobile_navigation_font_size' ] ), false, 'px' );
			
			$premium_css->set_selector( '.main-navigation:not(.slideout-navigation) .main-nav ul ul li a' );
			$premium_css->add_property( 'font-size', absint( $mobile_subnav_font_size ), false, 'px' );
		}
	$premium_css->stop_media_query();
	
	return $css . $premium_css->css_output();
}
endif;