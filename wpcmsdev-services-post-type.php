<?php
/*
Plugin Name: wpCMSdev Services Post Type
Plugin URI:  http://wpcmsdev.com/plugins/services-post-type/
Description: Registers a "Services" custom post type.
Author:      wpCMSdev
Author URI:  http://wpcmsdev.com
Version:     1.0
Text Domain: wpcmsdev-services-post-type
Domain Path: /languages
License:     GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Copyright (C) 2014  wpCMSdev

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


/**
 * Registers the "service" post type.
 */
function wpcmsdev_services_post_type_register() {

	$labels = array(
		'name'               => __( 'Services',                    'wpcmsdev-services-post-type' ),
		'singular_name'      => __( 'Service',                     'wpcmsdev-services-post-type' ),
		'all_items'          => __( 'All Services',                'wpcmsdev-services-post-type' ),
		'add_new'            => _x( 'Add New', 'service',          'wpcmsdev-services-post-type' ),
		'add_new_item'       => __( 'Add New Service',             'wpcmsdev-services-post-type' ),
		'edit_item'          => __( 'Edit Service',                'wpcmsdev-services-post-type' ),
		'new_item'           => __( 'New Service',                 'wpcmsdev-services-post-type' ),
		'view_item'          => __( 'View Service',                'wpcmsdev-services-post-type' ),
		'search_items'       => __( 'Search Services',             'wpcmsdev-services-post-type' ),
		'not_found'          => __( 'No services found.',          'wpcmsdev-services-post-type' ),
		'not_found_in_trash' => __( 'No services found in Trash.', 'wpcmsdev-services-post-type' ),
	);

	$args = array(
		'labels'        => $labels,
		'menu_icon'     => 'dashicons-hammer',
		'menu_position' => 5,
		'public'        => true,
		'has_archive'   => false,
		'supports'      => array(
			'author',
			'comments',
			'custom-fields',
			'editor',
			'excerpt',
			'page-attributes',
			'revisions',
			'trackbacks',
			'thumbnail',
			'title',
		),
	);

	$args = apply_filters( 'wpcmsdev_services_post_type_args', $args );

	register_post_type( 'service', $args );

}
add_action( 'init', 'wpcmsdev_services_post_type_register' );


/**
 * Flushes the site's rewrite rules.
 */
function wpcmsdev_services_rewrite_flush() {

	wpcmsdev_services_post_type_register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wpcmsdev_services_rewrite_flush' );


/**
 * Loads the translation files.
 */
function wpcmsdev_services_load_translations() {

	load_plugin_textdomain( 'wpcmsdev-services-post-type', false, dirname( plugin_basename( __FILE__ ) ) ) . '/languages/';
}
add_action( 'plugins_loaded', 'wpcmsdev_services_load_translations' );


/**
 * Initializes additional functionality when used with a theme that declares support for the plugin.
 */
function wpmcsdev_services_additional_functionality_init() {

	if ( current_theme_supports( 'wpcmsdev-services-post-type' ) ) {
		add_action( 'admin_enqueue_scripts',              'wpcmsdev_services_manage_posts_css' );
		add_action( 'manage_service_posts_custom_column', 'wpcmsdev_services_manage_posts_columm_content' );
		add_filter( 'cmb2_meta_boxes',                    'wpcmsdev_services_meta_box' );
		add_filter( 'manage_edit-service_columns',        'wpcmsdev_services_manage_posts_columns' );
	}
}
add_action( 'after_setup_theme', 'wpmcsdev_services_additional_functionality_init', 11 );


/**
 * Registers custom columns for the Manage Services admin page.
 */
function wpcmsdev_services_manage_posts_columns( $columns ) {

	$column_order = array( 'order' => __( 'Order', 'wpcmsdev-services-post-type' ) );

	$columns = array_slice( $columns, 0, 2, true ) + $column_order + array_slice( $columns, 2, null, true );

	return $columns;
}


/**
 * Outputs the custom column content for the Manage Services admin page.
 */
function wpcmsdev_services_manage_posts_columm_content( $column ) {

	global $post;

	switch( $column ) {

		case 'order':
			$order = $post->menu_order;
			if ( 0 === $order ) {
				echo '<span class="default-value">' . $order . '</span>';
			} else {
				echo $order;
			}
			break;
	}
}


/**
 * Outputs the custom columns CSS used on the Manage Services admin page.
 */
function wpcmsdev_services_manage_posts_css() {

	global $pagenow, $typenow;
	if ( ! ( 'edit.php' == $pagenow && 'service' == $typenow ) ) {
		return;
	}

?>
<style>
	.edit-php .posts .column-order {
		width: 10%;
	}
	.edit-php .posts .column-order .default-value {
		color: #bbb;
	}
</style>
<?php
}


/**
 * Creates the Service Settings meta box and fields.
 */
function wpcmsdev_services_meta_box( $meta_boxes ) {

	$meta_boxes['service-settings'] = array(
		'id'           => 'service-settings',
		'title'        => __( 'Service Settings', 'wpcmsdev-services-post-type' ),
		'object_types' => array( 'service' ),
		'fields'       => array(
			array(
				'name' => __( 'Read more text', 'wpcmsdev-services-post-type' ),
				'id'   => 'read_more_text',
				'type' => 'text',
			),
		),
	);

	return $meta_boxes;
}
