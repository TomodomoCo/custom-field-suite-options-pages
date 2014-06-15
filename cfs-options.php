<?php
/*
Plugin Name: Custom Field Suite Options Pages
*/

require_once(plugin_dir_path(__FILE__) . '../custom-field-suite/cfs.php');

class CFS_Options extends Custom_Field_Suite
{

	public $version = '0.1';

	public function __construct($cfs)
	{
		$this->cfs = $cfs;
		register_activation_hook(__FILE__, array($this, 'install'));
		add_action('init', array($this, 'init'));
		add_action('init', array($this, 'check_for_updates'));
		add_action('admin_menu', array($this, 'create_menu'));
	}

	public function init()
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
			'supports' => array('title','custom-fields'),
			'exclude_from_search' => true,
			'public' => false,
			'show_ui' => false
			)
		);		
	}

	public function create_menu()
	{
		add_menu_page('CFS Options','CFS Options','manage_options','cfs-options','cfs_global', '',80); //add url to icon in empty spot
		add_submenu_page('cfs-options', 'Create Options Page', 'Create New','manage_options','cfs-options-create',array($this,'options_create'));
		remove_submenu_page('cfs-options','cfs-options'); //Remove the duplicate CFS Options link.  Will not be used.

		$query = new WP_Query('post_type=cfs-options&orderby=title&order=asc');

		if($query->have_posts())
		{
			while($query->have_posts())
			{
				$query->the_post();
				add_submenu_page('cfs-options', get_the_title(), get_the_title(),'manage_options','cfs-options-edit-'.get_the_ID(), array($this, 'options_edit'));
			}
		}
	}

	public function options_create()
	{
		echo 'CREATE OPTIONS PAGE';
	}

	public function options_edit()
	{
		global $cfs;

		$cfs->form->load_assets();
		$item = array(
			'post_id' => str_replace('cfs-options-edit-','',$_GET['page'])
			);

		$page = get_post($item['post_id']);

		echo '<div class="wrapper page wrap">';
		echo '<h2>'.$page->post_title.'</h2>';
		echo $cfs->form($item);
		echo '</div>';
	}

	public function install()
	{
		wp_insert_post(array(
			'post_name' => 'options',
			'post_title' => 'Options',
			'post_type' => 'cfs-options',
			'post_status' => 'publish'));
	}

	/**
	 * Creates an options page programmatically, if one with given title
	 * and slug do not already exist.
	 * @param  string $title
	 * @param  string $slug
	 * @return void
	 */
	function add_page($title, $slug)
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

	/**
	 * Delete an options page with the given slug.  Only delete is the options page exists.
	 * @param  string $slug
	 * @return void
	 */
	function delete_page($slug)
	{

		$page = get_posts(array('name'=>$slug,'post_type'=>'cfs-options'));

		if(!empty($page))
		{
			wp_delete_post($page[0]->ID, true);
		}
	}


	function cfs_options($title=null)
	{
		$page = get_page_by_title($title, 'OBJECT', 'cfs-options');
		if($page !== NULL)
		{
			return $page->ID;
		}
	}

	public function check_for_updates()
	{

	}

}
global $cfs;
$cfsop = new CFS_Options($cfs);