<?php 
/*
	Plugin Name: Wp Script Loader
	Version: 1.0
	Author: Rajesh Kumar Sharma
	Author URI: http://sitextensions.com
	Description: Wp Script Loader is used to upload script files i.e. js, css files.
	Tags: Script Loader, Load Script Files, Load Stylesheets, Add Scripts, Add CSS, Scripts in admin, stylesheets in admin
*/

add_action( 'admin_menu', 'wp_script_loader_menu' );
function wp_script_loader_menu(){
	add_menu_page( 'Wp Script Loader', 'Wp Script Loader', 'manage_options', 'wp-script-loader', 'wp_script_loader_menu_page'); 
}

add_action( 'admin_init', 'register_wp_script_loader_setting' );
function register_wp_script_loader_setting() {
	register_setting( 'wp_script_loader_settings', 'wp_script_loader_settings', 'wp_script_loader_settings_callback' ); 
} 

function wp_script_loader_settings_callback($result = array()){
	// print_r($result);
	// print_r($_POST);die;
	return $result;
}

function wp_script_loader_menu_page(){
	?>
		<div class="wrap">
			<h1>Wp Script Loader</h1>
			<form id="upload_script_files_form"  method="post" action="options.php" enctype="multipart/form-data">
				<?php 
					settings_fields( 'wp_script_loader_settings' );
					$options = get_option('wp_script_loader_settings');
					// print_r($options);
				?>
				<div id="script_files_container">
					<div class="single_file">
						<label>
							<span>Enter file title here...</span>
							<input type="text" required name="script_file_name[]" />
						</label>
						<label>
							<span>Select an script file here...</span>
							<input type="file" required name="script_files[]" />
						</label>
						<label>
							<span>Use this file in front end website...</span>
							<input name="use_in_front[]" checked type="checkbox" value="1" />
						</label>
						<label>
							<span>Use this file in back end (in admin)...</span>
							<input name="use_in_back[]" type="checkbox" value="1" />
						</label>
					</div>
				</div>
				<!-- <input type="button" class="button button-default" name="add_new_script_file" id="add_new_script_file" value="Add New" /> -->
				
				<hr>
				<p class="submit">
					<input type="submit" value="Upload" class="button button-primary" id="submit_image_btn" name="submit_image_btn">
				</p>
				<?php // submit_button('Upload'); ?>
				<hr>
			</form>
			<hr>

			<table class="script_files_table">
				<tr>
					<th>#</th>
					<th>Script Name</th>
					<th>Use in admin</th>
					<th>Use in front end</th>
					<th>Actions</th>
				</tr>
			

			<?php 
				$options = get_option('wp_script_loader_settings');
				// print_r($options);

				$options_count = count($options);
				if($options_count && !empty($options)){
					for($i = 0; $i < $options_count; $i++){
						?>
							<tr>
								<td><?php echo $i + 1; ?></td>
								<td><?php echo $options[$i]['file_name']; ?></td>
								<td><input data-id="<?php echo $i; ?>" class="use_in_back" type="checkbox" <?php echo $options[$i]['use_in_back'] == 1 ? 'checked' : ''; ?>></td>
								<td><input data-id="<?php echo $i; ?>" class="use_in_front" type="checkbox" <?php echo $options[$i]['use_in_front'] == 1 ? 'checked' : ''; ?>></td>
								<td><input data-id="<?php echo $i; ?>" type="button" value="Delete" class="button button-primary delete_script_file"></td>
							</tr>
						<?php 
					}
				}
				else{
					?>
						<tr class="no-result">
							<td colspan="5">No result found.</td>
						</tr>
					<?php 
				}
			?>
			</table>


			<!-- <p>No result found.</p> -->
			<hr>
		</div>
	<?php
}


add_action( 'wp_ajax_upload_script_files', 'upload_script_files' );
function upload_script_files(){
	// print_r($_POST);
	// print_r($_FILES);
	// die('asdf');

	$response = array();
	$response['status'] = 'Success';
	$response['message'] = 'Success';

	$wp_script_options = get_option('wp_script_loader_settings');

	if(isset($_FILES) && !empty($_FILES['script_files']['tmp_name']) && $_FILES['script_files']['tmp_name'][0] != ''){
		$files_count = count($_FILES['script_files']['tmp_name']);

		for($i = 0; $i < $files_count; $i++){
			if(!empty($_FILES['script_files']['tmp_name'][$i])){
				if(!file_exists(plugin_dir_path(__FILE__) . 'wp-script-loader-files' . DIRECTORY_SEPARATOR . $_FILES['script_files']['name'][$i])){
					if(move_uploaded_file($_FILES['script_files']['tmp_name'][$i], plugin_dir_path(__FILE__) . 'wp-script-loader-files' . DIRECTORY_SEPARATOR . $_FILES['script_files']['name'][$i])){
						chmod(plugin_dir_path(__FILE__) . 'wp-script-loader-files' . DIRECTORY_SEPARATOR . $_FILES['script_files']['name'][$i], 0777);
						$wp_script_options[] = array(
													'file_name' => $_FILES['script_files']['name'][$i],
													'use_in_front' => isset($_POST['use_in_front'][$i]) ? 1 : 0,
													'use_in_back' => isset($_POST['use_in_back'][$i]) ? 1 : 0,
													'script_file_name' => isset($_POST['script_file_name'][$i]) ? $_POST['script_file_name'][$i] : '',
												);

					}
				}
				else{
					$response['status'] = 'Fail';
					$response['message'] = 'File already exists.';
					// echo 'File already exists.';
				}
			}
		}
	}
	else{
		$response['status'] = 'Fail';
		$response['message'] = 'Please a file first.';
	}
	update_option('wp_script_loader_settings', $wp_script_options);

	die(json_encode($response));
}

add_action( 'wp_ajax_use_in_back', 'use_in_back' );
function use_in_back(){
	// print_r($_POST);
	$options = get_option('wp_script_loader_settings');
	// print_r($options);

	$options[$_POST['data_id']]['use_in_back'] = $_POST['use_in_back'];
	// print_r($options);
	update_option('wp_script_loader_settings', $options);

	$response = array(
						'status' => 'Updated',
						'Message' => 'Complate'
					);

	die(json_encode($response));
}

add_action( 'wp_ajax_use_in_front', 'use_in_front' );
function use_in_front(){
	// print_r($_POST);
	$options = get_option('wp_script_loader_settings');
	// print_r($options);

	$options[$_POST['data_id']]['use_in_front'] = $_POST['use_in_front'];
	// print_r($options);
	update_option('wp_script_loader_settings', $options);

	$response = array(
						'status' => 'Updated',
						'Message' => 'Complate'
					);

	die(json_encode($response));
}

add_action( 'wp_ajax_delete_script_file', 'delete_script_file' );
function delete_script_file(){
	// print_r($_POST);
	$options = get_option('wp_script_loader_settings');
	// print_r($options);

	// $options = array_splice($options, $_POST['data_id'], 1);
	unlink(plugin_dir_path(__FILE__) . 'wp-script-loader-files' . DIRECTORY_SEPARATOR . $options[$_POST['data_id']]['file_name']);
	unset($options[$_POST['data_id']]);

	$options = array_values($options);

	// print_r($options);
	update_option('wp_script_loader_settings', $options);

	$response = array(
						'status' => 'Updated',
						'Message' => 'Complate'
					);

	die(json_encode($response));
}


/* Include files in wp */

function create_slug($string){
   $slug=preg_replace('/[^A-Za-z0-9-]+/', '-', $string);
   return $slug;
}

add_action('wp_enqueue_scripts', 'add_script_files_front');
function add_script_files_front(){
	$options = get_option('wp_script_loader_settings');
	$options_count = count($options);

	$include_url = plugin_dir_url(__FILE__) . 'wp-script-loader-files/';

	for($i = 0; $i < $options_count; $i++){
		$opt = $options[$i];
		$opt['file_name'] = isset($opt['file_name']) ? $opt['file_name'] : '';
		$type = end(explode('.', $opt['file_name']));
		if($type == "css"){
			if($opt['use_in_front'] == 1){
				$script_id = $opt['script_file_name'] != '' ? $opt['script_file_name'] : $opt['file_name'];
				wp_enqueue_style(create_slug($script_id), $include_url . $opt['file_name']);
			}
		}
		else{
			if($opt['use_in_front'] == 1){
				$script_id = $opt['script_file_name'] != '' ? $opt['script_file_name'] : $opt['file_name'];
				wp_enqueue_script(create_slug($script_id), $include_url . $opt['file_name']);
			}
		}
	}
}

add_action('admin_enqueue_scripts', 'add_script_files_admin');
function add_script_files_admin(){
	$options = get_option('wp_script_loader_settings');
	$options_count = count($options);

	$include_url = plugin_dir_url(__FILE__) . 'wp-script-loader-files/';

	for($i = 0; $i < $options_count; $i++){
		$opt = $options[$i];
		$opt['file_name'] = isset($opt['file_name']) ? $opt['file_name'] : '';
		$type = end(explode('.', $opt['file_name']));
		if($type == "css"){
			if($opt['use_in_back'] == 1){
				$script_id = $opt['script_file_name'] != '' ? $opt['script_file_name'] : $opt['file_name'];
				wp_enqueue_style(create_slug($script_id), $include_url . $opt['file_name']);
			}
		}
		else{
			if($opt['use_in_back'] == 1){
				$script_id = $opt['script_file_name'] != '' ? $opt['script_file_name'] : $opt['file_name'];
				wp_enqueue_script(create_slug($script_id), $include_url . $opt['file_name']);
			}
		}
	}
}


/* Include files */
add_action('admin_enqueue_scripts', 'include_self_files');
function include_self_files(){
	$include_url = plugin_dir_url(__FILE__);

	wp_enqueue_style('wp-script-loader-style-css', $include_url . 'wp-script-loader-style.css');

	wp_register_script('wp-script-loader-script-js', $include_url . 'wp-script-loader-script.js');
	wp_localize_script( 'wp-script-loader-script-js', 'wp_script_loader_script_js', array('admin_ajax_url' => admin_url( 'admin-ajax.php' )) );
	wp_enqueue_script( 'wp-script-loader-script-js' );
}