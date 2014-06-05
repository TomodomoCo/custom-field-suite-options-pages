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
		'show_in_menu' => false,
		'supports' => array('title'),
		'exclude_from_search' => true,
		'public' => false,
		'show_ui' => false
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

function create_cfs_options()
{
	add_menu_page('CFS Options','CFS Options','manage_options','cfs-options','cfs_global','',80);
	add_submenu_page('cfs-options', 'Create Options Page', 'Create New','manage_options','cfs-options-create','cfs_options_create');

	$query = new WP_Query('post_type=cfs-options&orderby=title&order=asc');

	if($query->have_posts()): while($query->have_posts()): $query->the_post();
		add_submenu_page('cfs-options', get_the_title(), get_the_title(),'manage_options','cfs-options-edit-'.get_the_ID(),'cfs_options_edit');
	endwhile;endif;
}

add_action('admin_menu','create_cfs_options');

function cfs_global()
{
	echo 'THE_DEFAULT_PAGE';
}

function cfs_options_create()
{
	echo 'CREATE OPTIONS PAGE';
}

function cfs_options_edit()
{
	echo 'EDIT OPTIONS PAGE';
}

/**
 * Creates an options page programmatically, if one with given title
 * and slug do not already exist.
 * @param  string $title
 * @param  string $slug
 * @return void
 */
function create_option_page($title, $slug)
{

	$exists = wp_cache_get('cfs-options-'.$slug);
	if($exists === false)
	{
		$exists = new WP_Query(array('title'=>$title,'name'=>$slug,'post_type'=>'cfs-options'));
		wp_cache_set('cfs-options-'.$slug, $exists);
	}

	if(!$exists->have_posts())
	{
		wp_insert_post(array(
			'post_name' => $slug,
			'post_title' => $title,
			'post_type' => 'cfs-options',
			'post_status' => 'publish'
		));
	}
}

function delete_option_page($slug)
{

	$page = get_posts(array('name'=>$slug,'post_type'=>'cfs-options'));

	if(!empty($page))
	{
		wp_delete_post($page[0]->ID, true);
	}
}