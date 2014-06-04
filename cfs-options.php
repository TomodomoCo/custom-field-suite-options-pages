<?php
/*
Plugin Name: Custom Field Suite Options Pages
*/
function create_options_pages()
{
	register_post_type('cfs-options',
		array(
			'labels' => array(
				'name' => __('Options'),
				'singular_name' => __('Options Page'),
				'menu_name' => 'Options',
				'name_admin_bar' => 'Options Page',
				'all_items' => 'Options Pages',
				'add_new_item' => 'New Options Page',
				'edit_item' => 'Edit Options Page',
				'new_item' => 'Options Page'
			),
		'public' => true,
		'has_archive' => false,
		'menu_position' => 80,
		'show_in_menu' => true,
		'supports' => array('title'),
		'exclude_from_search' => true,
		'public' => false,
		'show_ui' => true
		)
	);
}

add_action('init','create_options_pages');

function cfs_options($title=null)
{
	$page = get_page_by_title($title, 'OBJECT', 'cfs-options');
	if($page !== NULL)
	{
		return $page->ID;
	}
}