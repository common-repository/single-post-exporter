<?php if ( ! defined( 'ABSPATH' ) ) exit;
global $wp_roles;
$opt = get_option('export_setting_options');?>
<div class="wrap single_export_page_settings">
<h1><?php _e('Export Page Settings', 'single_post_exporter')?></h1>
<?php $exportsettingoptions = array();
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
if(isset($_POST['submit_export'])):
echo 'Saving please wait...';
	foreach($_POST as $key => $val):
		$exportsettingoptions[$key] = $val;
		endforeach;
		 $saveExportSettings = update_option('export_setting_options', $exportsettingoptions );
		if($saveExportSettings){
			$this->spx_single_post_redirect('options-general.php?page=single_post_exporter_settings&msg=1');
		}
		else {
		    $this->spx_single_post_redirect('options-general.php?page=single_post_exporter_settings&msg=2');
		}
endif;
if(!empty($msg) && $msg == 1):
  _e( '<div class="updated settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p><strong>Settings saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'single_post_exporter');	
elseif(!empty($msg) && $msg == 2):
  _e( '<div class="error settings-error notice is-dismissible" id="setting-error-settings_updated"> 
<p><strong>Settings not saved.</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>', 'single_post_exporter');
endif;
?> 
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<div id="post-body-content" style="position: relative;">
<form action="" method="post" name="export_page_form">
<table class="form-table">
<tbody>
<tr>
<th scope="row"><label for="export_post_status"><?php _e('User Roles', 'single_post_exporter')?></label></th>
<td>
<?php 
$roles = $wp_roles->get_names();
$userRoleData = isset($opt['userRole']) ? $opt['userRole'] : array();
foreach ($roles as $key=>$val){
if(!empty($userRoleData)) {	?>
	<input type="checkbox" name="userRole[]" value="<?php echo $key; ?>"  <?php if (in_array($key, $userRoleData)) echo 'checked="checked"'; ?>><?php echo $val; ?>
<?php } else {?>
<input type="checkbox" name="userRole[]" value="<?php echo $key; ?>"><?php echo $val; ?>	
<?php } }?>	
</td>
</tr>
<tr>
<th scope="row"><label for="export_post_title"><?php _e('Select Post Type', 'single_post_exporter')?></label></th>
<td>
<?php
$postTypeData = isset($opt['export_post']) ? $opt['export_post'] : array();
$def = array('post','page');
$args = array(
    'public'   => true,
    '_builtin' => false
      );
$post_types = array_merge($def, get_post_types( $args ));
foreach ( $post_types as $post_type ) { ?>
   <input type="checkbox" name="export_post[]" value="<?php echo $post_type; ?>"  <?php if (in_array($post_type, $postTypeData)) echo 'checked="checked"'; ?>><?php echo $post_type; ?>
<?php } ?>
</td>
</tr>
</tbody></table>
<p class="submit"><input type="submit" value="Save Changes" class="button button-primary" id="submit" name="submit_export"></p>
</form>
</div>
</div>
</div>
</div>