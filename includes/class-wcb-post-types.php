<?php

if ( ! function_exists('transtops_post_type') ) {

// Register Custom Post Type
function transtops_post_type() {

    $labels = array(
        'name'                  => _x( 'Transportation stops', 'Post Type General Name', 'wcb' ),
        'singular_name'         => _x( 'Transportation stop', 'Post Type Singular Name', 'wcb' ),
        'menu_name'             => __( 'Transportation stops', 'wcb' ),
        'name_admin_bar'        => __( 'Transportation stop', 'wcb' ),
        'archives'              => __( 'TS Archives', 'wcb' ),
        'attributes'            => __( 'TS Attributes', 'wcb' ),
        'parent_item_colon'     => __( 'Parent TS:', 'wcb' ),
        'all_items'             => __( 'All TS', 'wcb' ),
        'add_new_item'          => __( 'Add New TS', 'wcb' ),
        'add_new'               => __( 'Add New', 'wcb' ),
        'new_item'              => __( 'New Ts', 'wcb' ),
        'edit_item'             => __( 'Edit TS', 'wcb' ),
        'update_item'           => __( 'Update TS', 'wcb' ),
        'view_item'             => __( 'View TS', 'wcb' ),
        'view_items'            => __( 'View TS', 'wcb' ),
        'search_items'          => __( 'Search TS', 'wcb' ),
        'not_found'             => __( 'Not found', 'wcb' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'wcb' ),
        'featured_image'        => __( 'Featured Image', 'wcb' ),
        'set_featured_image'    => __( 'Set featured image', 'wcb' ),
        'remove_featured_image' => __( 'Remove featured image', 'wcb' ),
        'use_featured_image'    => __( 'Use as featured image', 'wcb' ),
        'insert_into_item'      => __( 'Insert into ts', 'wcb' ),
        'uploaded_to_this_item' => __( 'Uploaded to this ts', 'wcb' ),
        'items_list'            => __( 'TS list', 'wcb' ),
        'items_list_navigation' => __( 'TS list navigation', 'wcb' ),
        'filter_items_list'     => __( 'Filter ts list', 'wcb' ),
    );
    $args = array(
        'label'                 => __( 'Transportation stop', 'wcb' ),
        'labels'                => $labels,
        'supports'              => array( 'title' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 50,
        'menu_icon'             => 'dashicons-location-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => false,
        'capability_type'       => 'page',
    );
    register_post_type( 'transtops', $args );

}
add_action( 'init', 'transtops_post_type', 0 );

}