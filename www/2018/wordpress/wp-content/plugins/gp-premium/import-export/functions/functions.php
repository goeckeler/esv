<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'generate_insert_import_export' ) ) {
	add_action( 'generate_admin_right_panel', 'generate_insert_import_export', 15 );

	function generate_insert_import_export() {
		?>
		<div class="postbox generate-metabox" id="generate-ie">
			<h3 class="hndle"><?php _e( 'Export Settings', 'gp-premium' );?></h3>
			<div class="inside">
				<form method="post">
					<span class="show-advanced"><?php _e( 'Advanced', 'gp-premium' ); ?></span>
					<div class="export-choices advanced-choices">
						<label><input type="checkbox" name="module_group[]" value="generate_settings" checked /><?php _ex( 'Core', 'Module name', 'gp-premium' ); ?></label>

						<?php if ( generatepress_is_module_active( 'generate_package_backgrounds', 'GENERATE_BACKGROUNDS' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_background_settings" checked /><?php _ex( 'Backgrounds', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_blog', 'GENERATE_BLOG' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_blog_settings" checked /><?php _ex( 'Blog', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_hooks', 'GENERATE_HOOKS' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_hooks" checked /><?php _ex( 'Hooks', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_page_header', 'GENERATE_PAGE_HEADER' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_page_header_settings" checked /><?php _ex( 'Page Header', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_secondary_nav', 'GENERATE_SECONDARY_NAV' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_secondary_nav_settings" checked /><?php _ex( 'Secondary Navigation', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_spacing', 'GENERATE_SPACING' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_spacing_settings" checked /><?php _ex( 'Spacing', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_menu_plus', 'GENERATE_MENU_PLUS' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_menu_plus_settings" checked /><?php _ex( 'Menu Plus', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_woocommerce', 'GENERATE_WOOCOMMERCE' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="generate_woocommerce_settings" checked /><?php _ex( 'WooCommerce', 'Module name', 'gp-premium' ); ?></label>
						<?php } ?>

						<?php if ( generatepress_is_module_active( 'generate_package_copyright', 'GENERATE_COPYRIGHT' ) ) { ?>
							<label><input type="checkbox" name="module_group[]" value="copyright" checked /><?php _ex( 'Copyright', 'Module name', 'gp-premium' ); ?></label>
						<?php }?>

						<hr style="margin:10px 0;border-bottom:0;" />

						<label><input type="checkbox" name="module_group[]" value="generatepress-site" /><?php _ex( 'GeneratePress Site', 'Module name', 'gp-premium' ); ?></label>

						<?php do_action( 'generate_export_items' ); ?>
					</div>
					<p><input type="hidden" name="generate_action" value="export_settings" /></p>
					<p style="margin-bottom:0">
						<?php wp_nonce_field( 'generate_export_nonce', 'generate_export_nonce' ); ?>
						<?php submit_button( __( 'Export', 'gp-premium' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
					</p>
				</form>
			</div>
		</div>
		<?php
	}
}

add_action( 'generate_admin_right_panel', 'generate_ie_import_form', 15 );

function generate_ie_import_form() {
	?>
	<div class="postbox generate-metabox" id="generate-ie">
		<h3 class="hndle"><?php _e( 'Import Settings', 'gp-premium' );?></h3>
		<div class="inside">
			<form method="post" enctype="multipart/form-data">
				<p>
					<input type="file" name="import_file"/>
				</p>
				<p style="margin-bottom:0">
					<input type="hidden" name="generate_action" value="import_settings" />
					<?php wp_nonce_field( 'generate_import_nonce', 'generate_import_nonce' ); ?>
					<?php submit_button( __( 'Import', 'gp-premium' ), 'button-primary', 'submit', false, array( 'id' => '' ) ); ?>
				</p>
			</form>

		</div>
	</div>
	<?php
}

if ( ! function_exists( 'generate_process_settings_export' ) ) {
	add_action( 'admin_init', 'generate_process_settings_export' );
	/**
	 * Process a settings export that generates a .json file of the shop settings
	 */
	function generate_process_settings_export() {
		if ( empty( $_POST['generate_action'] ) || 'export_settings' != $_POST['generate_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['generate_export_nonce'], 'generate_export_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$theme_mods = array(
			'font_body_variants',
			'font_body_category',
			'font_site_title_variants',
			'font_site_title_category',
			'font_site_tagline_variants',
			'font_site_tagline_category',
			'font_navigation_variants',
			'font_navigation_category',
			'font_secondary_navigation_variants',
			'font_secondary_navigation_category',
			'font_buttons_variants',
			'font_buttons_category',
			'font_heading_1_variants',
			'font_heading_1_category',
			'font_heading_2_variants',
			'font_heading_2_category',
			'font_heading_3_variants',
			'font_heading_3_category',
			'font_heading_4_variants',
			'font_heading_4_category',
			'font_heading_5_variants',
			'font_heading_5_category',
			'font_heading_6_variants',
			'font_heading_6_category',
			'font_widget_title_variants',
			'font_widget_title_category',
			'font_footer_variants',
			'font_footer_category',
			'generate_copyright',
		);

		$settings = array(
			'generate_settings',
			'generate_background_settings',
			'generate_blog_settings',
			'generate_hooks',
			'generate_page_header_settings',
			'generate_secondary_nav_settings',
			'generate_spacing_settings',
			'generate_menu_plus_settings',
			'generate_woocommerce_settings',
		);

		$data = array(
			'mods' => array(),
			'options' => array()
		);

		foreach ( $theme_mods as $theme_mod ) {
			if ( 'generate_copyright' == $theme_mod ) {
				if ( in_array( 'copyright', $_POST['module_group'] ) ) {
					$data['mods'][$theme_mod] = get_theme_mod( $theme_mod );
				}
			} else {
				if ( in_array( 'generate_settings', $_POST['module_group'] ) ) {
					$data['mods'][$theme_mod] = get_theme_mod( $theme_mod );
				}
			}
		}

		foreach ( $settings as $setting ) {
			if ( in_array( $setting, $_POST['module_group'] ) ) {
				$data['options'][$setting] = get_option( $setting );
			}
		}

		$data = apply_filters( 'generate_export_data', $data );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=generate-settings-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );

		echo json_encode( $data );
		exit;
	}
}

if ( ! function_exists( 'generate_process_settings_import' ) ) {
	add_action( 'admin_init', 'generate_process_settings_import' );
	/**
	 * Process a settings import from a json file
	 */
	function generate_process_settings_import() {
		if ( empty( $_POST['generate_action'] ) || 'import_settings' != $_POST['generate_action'] ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['generate_import_nonce'], 'generate_import_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$filename = $_FILES['import_file']['name'];
		$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

		if ( $extension != 'json' ) {
			wp_die( __( 'Please upload a valid .json file', 'gp-premium' ) );
		}

		$import_file = $_FILES['import_file']['tmp_name'];

		if ( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import', 'gp-premium' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = json_decode( file_get_contents( $import_file ), true );

		foreach( $settings['mods'] as $key => $val ) {
			set_theme_mod( $key, $val );
		}

		foreach( $settings['options'] as $key => $val ) {
			update_option( $key, $val );
		}

		// Delete existing dynamic CSS cache
		delete_option( 'generate_dynamic_css_output' );
		delete_option( 'generate_dynamic_css_cached_version' );

		wp_safe_redirect( admin_url( 'admin.php?page=generate-options&status=imported' ) );
		exit;
	}
}

if ( ! function_exists( 'generate_ie_exportable' ) ) {
	function generate_ie_exportable() {
		// A check to see if other addons can add their export button
	}
}
