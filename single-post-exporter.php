<?php
/*
Plugin Name: Single Post Exporter
Description: Export Posts, Pages and Custom Posts using single click.
Author: mndpsingh287
Version: 1.1.1
Author URI: https://profiles.wordpress.org/mndpsingh287/
License: GPLv2
Text Domain: spx_single_post
*/
if (!defined("EXPORT_PLUGIN_DIRNAME")) define("EXPORT_PAGE_PLUGIN_DIRNAME", plugin_basename(dirname(__FILE__)));
if (!function_exists('get_userdata')) { require_once(ABSPATH . 'wp-includes/pluggable.php');}
if(!class_exists('spx_single_post_exporter')):
	class spx_single_post_exporter
	{
		/*
		* AutoLoad Hooks
		*/	
		public function __construct(){		
				    register_activation_hook(__FILE__, array(&$this, 'spx_single_post_export_install'));
					add_action( 'admin_menu', array(&$this, 'spx_single_post_export_settings_page'));
					add_action( 'admin_action_export_data', array(&$this,'spx_single_post_export_data')); 
					add_filter( 'post_row_actions', array(&$this,'spx_single_post_export'), 10, 2);
					add_filter( 'page_row_actions', array(&$this,'spx_single_post_export'), 10, 2);
					add_action( 'admin_footer-edit.php', array(&$this, 'spx_single_post_export_bulk'));
					add_action( 'load-edit.php', array(&$this, 'spx_single_post_export_bulk_action'));
					add_action( 'post_submitbox_misc_actions', array(&$this,'spx_single_post_custom_button'));
		}
		/*
		* Activation Hook
		*/
		public function spx_single_post_export_install(){
					$defaultsettings = array(
											 'userRole' => array('administrator'),
											 'export_post' => array('post','page')
											 );
					$opt = get_option('export_setting_options');
					if(!isset($opt['userRole'])) {
						update_option('export_setting_options', $defaultsettings);
					}           	
		}
		/*
		* Main function
		*/
		public function spx_single_post_export_data(){
			     if (! ( isset( $_GET['post']) || isset( $_POST['post']) || ( isset($_REQUEST['action'])  ) ) ) {
					 wp_die('No post to export has been supplied!');
				 } 
				  $post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
				  if(!empty($post_id)) {
				    include('inc/exporter.php' );	
				  } else {
					   wp_die('No post to export has been supplied!');
				  }
		}
		/*
		* Export multiple data
		*/
		public function spx_single_post_export_bulk() {
			if(empty($_GET['post_type'])) {
				$pVal= "post";
			}
			else {
				 $pVal = sanitize_text_field($_GET['post_type']);
			}
			$posttype = get_option('export_setting_options');
			$postData = $posttype['export_post'];
			if (current_user_can('edit_posts') && in_array($pVal,$postData)) { ?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery('<option>').val('export').text('<?php _e('Export','single_post_exporter')?>').appendTo("select[name='action']");
						});
					</script>
				<?php
				}
			}
		/*
		* Admin Menu 
		*/
		public function spx_single_post_export_settings_page(){	
		 add_options_page( __( 'Single Post Export', 'single_post_exporter' ), __( 'Single Post Export', 'single_post_exporter' ), 'manage_options', 'single_post_exporter_settings',array(&$this, 'spx_single_post_export_setting'));
		}
		
		/**
		 * Export Settings
		 */
		public function spx_single_post_export_setting(){
			if(current_user_can( 'manage_options' )){
			include('admin-settings.php');
			}
		}
		/**
		 * Export Bulk Action
		 */
		public function spx_single_post_export_bulk_action() {
			$wp_list_table = _get_list_table('WP_Posts_List_Table');  
				$action = $wp_list_table->current_action();
				$allowed_actions = array("export");
				if(!in_array($action, $allowed_actions)) return false;
				switch($action) {
					case 'export':
						$sendback = 'admin.php?action=export_data&post=' . join(',', $_REQUEST['post']);
					break;
					
					default: return;
				}
				wp_redirect($sendback);
				exit();			
		}
		/*
		 * Add Export Link 
		 */
		public function spx_single_post_export( $actions, $post ) {
			if(empty($_GET['post_type'])){
				 $pVal= "post";
			}
			else{
				 $pVal = sanitize_text_field($_GET['post_type']);
			}
			$posttype = get_option('export_setting_options');
			$postData =  isset($posttype['export_post']) ? $posttype['export_post'] : array();
			if (current_user_can('edit_posts') && in_array($pVal,$postData)) {
			 $actions['export'] = '<a href="admin.php?action=export_data&amp;post=' . $post->ID . '" title="Export" rel="permalink">'.__( "Export", "single_post_exporter" ).'</a>';
			 }
			 return $actions;
		}
		/*
		 * Add the export button to edit screen
		 */
		public function spx_single_post_custom_button(){
			global $post;
			$id = $post->ID;
			$getCustomPost = get_post($id);
			$pVal = $getCustomPost->post_type;
			$posttype = get_option('export_setting_options');
			$postData = isset($posttype['export_post']) ? $posttype['export_post'] : array();
			if (current_user_can('edit_posts') && in_array($pVal,$postData)) {
			    $html  = '<div id="major-publishing-actions">';
				$html .= '<div id="export-action">';
				$html .= '<a href="admin.php?action=export_data&amp;post=' . $post->ID . '" title="Export" rel="permalink">'.__( "Export", "single_post_exporter" ).'</a>';
				$html .= '</div>';
				$html .= '</div>';
				echo $html;
			  }
		}
		/*
		 * Redirect function
		*/
		public function spx_single_post_redirect($url){
			echo '<script>window.location.href="'.$url.'"</script>';
		}
	}
new spx_single_post_exporter;
endif;
?>