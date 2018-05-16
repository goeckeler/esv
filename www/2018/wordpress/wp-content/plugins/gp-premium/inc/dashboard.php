<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'generate_dashboard_inside_container', 'generate_do_dashboard_tabs', 5 );
/**
 * Adds our tabs to the GeneratePress dashboard.
 *
 * @since 1.6
 */
function generate_do_dashboard_tabs() {
	$tabs = apply_filters( 'generate_dashboard_tabs', array(
		'Modules' => array(
			'name' => __( 'Modules', 'gp-premium' ),
			'url' => admin_url( 'themes.php?page=generate-options' ),
			'class' => isset( $_GET['page'] ) && 'generate-options' == $_GET['page'] && ! isset( $_GET['area'] ) ? 'active' : '',
		)
	) );

	// Don't print any markup if we only have one tab.
	if ( count( $tabs ) === 1 ) {
		return;
	}
	?>
	<div class="generatepress-dashboard-tabs">
		<?php
		foreach ( $tabs as $tab ) {
			printf( '<a href="%1$s" class="%2$s">%3$s</a>',
				esc_url( $tab['url'] ),
				esc_attr( $tab['class'] ),
				esc_html( $tab['name'] )
			);
		}
		?>
	</div>
	<?php
}
