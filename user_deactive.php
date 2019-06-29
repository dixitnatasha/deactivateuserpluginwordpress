<?php
/**
 * Plugin Name: Deactivate User
 * Author: Natasha Dixit
 * Version: 1.0.0
 */

function add_custom_userprofile_fields() {   

	$current_user = wp_get_current_user();
	$profileuser_id = (int)$_GET['user_id'];
	$profile_user = get_userdata($profileuser_id); 
?>

<table class="form-table">
	<tr>
		<th>
			<label for="deactivate_user"><?php _e('User Account Deactivate'); ?></label>
		</th>
		<td>
			<label class="switch">
			  <input type="checkbox" name="user_account_status" id="user_account_status" onchange="add_value_switch()"  value="" <?php if ($profile_user->user_account_status === '1') { echo "checked";} ?> >
			  <span class="slider round"></span>
			</label>
			<input type="hidden" name="activate_user_id" value="<?php echo $profile_user->ID; ?>">
			<?php if ($profile_user->user_account_status === '1') { ?>
				<span class="description"><?php _e(' &nbsp; &nbsp; &nbsp; Shows User\'s account is Deactive now.'); ?></span>
			<?php } else {?>
				<span class="description"><?php _e(' &nbsp; &nbsp; &nbsp; Shows User\'s account is Active now.'); ?></span>
			<?php } ?>
		</td>
	</tr>
</table>

<?php }

/**
 * User Account Deactive means user_account_status == 1
 * 2 means active
 */

function wp_set_password( $password, $user_id ) {

    global $wpdb;
 
    $hash = wp_hash_password( $password );
    $wpdb->update(
        $wpdb->users,
        array(
            'user_pass'           => $hash,
            'user_activation_key' => '',
        ),
        array( 'ID' => $user_id )
    );
 
    wp_cache_delete( $user_id, 'users' );
}


function save_custom_userprofile_fields() {

	$profileuser_id = (int)$_POST['activate_user_id'];
	$profile_user = get_userdata($profileuser_id);

	$result = metadata_exists('user', $profile_user->ID , 'user_account_status' );

	if ($result) {
		if ($profile_user->user_account_status == 2) {
			update_user_meta( $profile_user->ID, 'user_account_status', 1);
			wp_set_password( $profile_user->user_current_password , $profile_user->ID );
		} else {
			update_user_meta( $profile_user->ID, 'user_account_status', 2);
			wp_set_password( $profile_user->user_current_password , $profile_user->ID );
		}
	} else {
		add_user_meta( $profile_user->ID, 'user_account_status', $_POST['user_account_status']);
		add_user_meta( $profile_user->ID, 'user_current_password', $profile_user->user_pass );
		wp_set_password( wp_rand(24), $profile_user->ID );
	}
	
} // end of save_custom_userprofile_fields

add_action( 'edit_user_profile', 'add_custom_userprofile_fields' );
add_action( 'edit_user_profile_update', 'save_custom_userprofile_fields' );

/**
 * Register and enqueue a custom stylesheet in the WordPress admin.
 */
function custom_style_deactivate_user() {
        wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'deactivate_user.css');
        wp_enqueue_style( 'custom_wp_admin_css' );
}

add_action( 'admin_enqueue_scripts', 'custom_style_deactivate_user' ); 

/**
 * enqueue a custom Javascript in the WordPress admin.
 */
function custom_script_deactivate_user(){
  wp_enqueue_script( 'my-custom-script', plugin_dir_url( __FILE__ ) . '/deactivate_user.js', array( 'jquery' ) );
}

add_action( 'admin_enqueue_scripts', 'custom_script_deactivate_user' );

