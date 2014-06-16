<?php
/*
Plugin Name: Custom Field Suite Options Pages
Plugin URI: https://github.com/vanpattenmedia/custom-field-suite-options-pages
Description: Create centralized option pages utilizing the Custom Field Suite
Version: 0.1
Author: Van Patten Media Inc.
Author URI: https://www.vanpattenmedia.com/

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

require_once(plugin_dir_path(__FILE__) . '../custom-field-suite/cfs.php');


class CFS_Options extends Custom_Field_Suite
{

	public $version = '0.1';

    /**
     * Constructor
     */
   	public function __construct()
	{
		register_activation_hook(__FILE__, array($this, 'install'));
		add_action('init', array($this, 'init'));
		add_action('admin_menu', array($this, 'create_menu'));
	}

    /**
     * Fire up CFSOP
     */
	public function init()
	{
		$this->dir = dirname( __FILE__ );
        $this->url = plugins_url( 'cfs-options' );

		$this->api = new cfs_api($this);
        $this->form = new cfs_form($this);
        $this->field_group = new cfs_field_group($this);
        $this->third_party = new cfs_third_party($this);
        $this->fields = parent::get_field_types();
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

		if ( !is_admin() ) {
			add_action( 'parse_query', array( $this, 'parse_query' ) );
		}	
	}

	/**
	 * Create CFS Options Menu
	 * @return void
	 */
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

	/**
	 * Create initial options page during plugin activation.
	 * @return void
	 */
	public function install()
	{
		$this->add_page('Options','options');
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


	/**
	 * Get the Page ID of the options page using the title or slug
	 * @param  string $title
	 * @return integer The page ID of the options page.
	 */
	function cfs_options($searchfor='options')
	{
		$page = get_page_by_title($searchfor, 'OBJECT', 'cfs-options');
		$slug = get_page_by_path($searchfor, 'OBJECT', 'cfs-options');
		if($page !== null)
		{
			return $page->ID;
		}
		else if($slug !== null)
		{
			return $slug->ID;
		}
	}

	/**
	 * Render the field from the options page.
	 * @param  string|boolean $field_name The name of the field
	 * @param  string  $options_page The slug or title of the options page
	 * @param  array   $options
	 * @return mixed
	 */
	public function get($field_name = false, $options_page = 'options', $options = array())
	{

		$post_id = $this->cfs_options($options_page);

		return parent::get($field_name, $post_id, $options);
	}

    /**
     * Make sure that $cfsop exists for template parts
     * @since 1.8.8
     */
    function parse_query( $wp_query ) {
        $wp_query->query_vars['cfsop'] = $this;
    }

}

$cfsop = new CFS_Options($cfs);