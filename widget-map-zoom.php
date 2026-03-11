<?php
/**
 * Plugin Name: Map zoom list points
 * Description: Widget per Elementor che mostra una mappa con dei punti e una lista di questi punti. Al click sulla lista, la mappa zooma sul punto corrispondente.
 * Version: 1.0.2
 * Author: Danilo Calabrese
 */

defined('ABSPATH') || exit;

add_action('elementor/frontend/after_register_styles', function () {

    wp_register_style(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        [],
        '1.9.4'
    );

    wp_register_style(
        'custom-map-css',
        plugin_dir_url(__FILE__) . 'assets/css/map.css',
        ['leaflet'],
        '1.0.3'
    );
});

add_action('elementor/frontend/after_register_scripts', function () {

    wp_register_script(
        'leaflet',
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
        [],
        '1.9.4',
        true
    );

    wp_register_script(
        'custom-map-js',
        plugin_dir_url(__FILE__) . 'assets/js/map.js',
        ['jquery', 'leaflet'],
        '1.0.0',
        true
    );
});


function map_elementor_widgets_include_files() {
    require_once plugin_dir_path(__FILE__) . 'widgets/widget-map-custom.php';
}
add_action('elementor/widgets/widgets_registered', 'map_elementor_widgets_include_files');

function map_elementor_register_widgets($widgets_manager) {
    $widgets_manager->register(new \Widget_Map_Custom());
}
add_action('elementor/widgets/widgets_registered', 'map_elementor_register_widgets');

// Register Custom Widget Category
function add_elementor_widget_categories_map_zoom( $elements_manager ) {

	$elements_manager->add_category(
		'dc_cat',
		[
			'title' => esc_html__( 'DC Plugin', 'textdomain' ),
			'icon' => 'fa fa-plug',
		]
	);
}
add_action( 'elementor/elements/categories_registered', 'add_elementor_widget_categories_map_zoom' );
