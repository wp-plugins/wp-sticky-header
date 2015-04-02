<?php
/**
 * Plugin Name: WP Sticky Header
 * Plugin URI: http://wordpress.org/plugins/wp-sticky-header/
 * Description: Plugin to display some content/notification on top of the webpage.
 * Version: 1.4
 * Author: wpnaga
 * Author URI: http://profiles.wordpress.org/wpnaga/
 * License: GPL2
 */

add_action('admin_menu', 'wpsh_create_menu'); // Adding Menu to Dashboard

function wpsh_create_menu() {

	//create new top-level menu
	add_menu_page('WP Sticky Header Plugin Settings', 'WP Sticky Header', 'administrator', __FILE__, 'wpsh_settings_page',plugins_url('/images/icon.png', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}




function register_mysettings() {
	//register our settings
	register_setting( 'wpsh-settings-group', 'wpsh_bg_color' );
	register_setting( 'wpsh-settings-group', 'wpsh_text_color' );
	register_setting( 'wpsh-settings-group', 'wpsh_closable' );
	register_setting( 'wpsh-settings-group', 'wpsh_position' );
	register_setting( 'wpsh-settings-group', 'wpsh_content' );
	register_setting( 'wpsh-settings-group', 'wpsh_where' );
	register_setting( 'wpsh-settings-group', 'wpsh_auto_close' );	
	register_setting( 'wpsh-settings-group', 'wpsh_page_ids' );  
}

function wpsh_settings_page() {
?>
<div class="wrap">
<h2>WP Sticky Header Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'wpsh-settings-group' ); ?>
    <?php do_settings_sections( 'wpsh-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Background Color</th>
        <td><input type="text" name="wpsh_bg_color" class="color-field" value="<?php echo esc_attr( get_option('wpsh_bg_color') ); ?>" /> &nbsp;&nbsp;background color of the header</td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Text Color</th>
        <td><input type="text" name="wpsh_text_color" class="color-field" value="<?php echo esc_attr( get_option('wpsh_text_color') ); ?>" /> &nbsp;&nbsp;text color of the header</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Is Closable?</th>
        <td><input type="checkbox" name="wpsh_closable" value="1" <?php if(esc_attr( get_option('wpsh_closable'))) echo "checked"; ?> /> &nbsp;&nbsp;can the viewer close the header?</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Position</th>
        <td>
			<p><input type="radio" name="wpsh_position" value="top" <?php if(esc_attr( get_option('wpsh_position')) == "top") echo "checked"; ?> /><b>Top</b> </p>
			<p><input type="radio" name="wpsh_position" value="bottom" <?php if(esc_attr( get_option('wpsh_position')) == "bottom") echo "checked"; ?> /><b>Bottom</b> </p>
		</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Where to Display?</th>
        <td><input type="radio" class="display" name="wpsh_where" value="home" <?php if(esc_attr( get_option('wpsh_where')) == "home") echo "checked"; ?> /><b>In home page</b> 
		<br><input type="radio" class="display" name="wpsh_where" value="all" <?php if(esc_attr( get_option('wpsh_where')) == "all") echo "checked"; ?> /><b>In all pages</b>
		<br><input type="radio" class="display" name="wpsh_where" value="posts" <?php if(esc_attr( get_option('wpsh_where')) == "posts") echo "checked"; ?> /><b>In all posts</b>
		<br><input type="radio" class="display" name="wpsh_where" value="selected" <?php if(esc_attr( get_option('wpsh_where')) == "selected") echo "checked"; ?> /><b>In Selected pages / posts</b> 
			<div class="pageids" style="display:none;margin-left:25px;">
				<input type="text" name="wpsh_page_ids" value="<?php echo esc_attr( get_option('wpsh_page_ids') ); ?>" placeholder="Enter page ids" />&nbsp;(enter page/posts ids seperated by commas ,)
			</div>
		</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Close Automatically</th>
        <td><input type="text" name="wpsh_auto_close" value="<?php echo esc_attr( get_option('wpsh_auto_close') ); ?>" required> <span style="vertical-align:top;padding-top:5px;">&nbsp;(Enter seconds)&nbsp;0 for not closing automatically</span></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Content</th>
        <td><textarea name="wpsh_content" style="resize:none" rows="5" cols="35" required><?php echo esc_attr( get_option('wpsh_content') ); ?></textarea> <span style="vertical-align:top;padding-top:5px;">&nbsp;content of the header</span></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>
	
	<br><br>
	<p><b>Help : </b> <a id="help" href="#">How to find page / post ids?</a> (for Selected pages/ posts option)</p>
	<div class="showhelp" style="display:none;">
		<p><b>Step 1</b>: Login to your admin account and in dashboard select page option</p>
		<img src="<?php echo plugins_url('/images/help1.png', __FILE__); ?>" style="padding:10px;border:1px solid #ccc;">
		<p><b>Step 2</b>: Edit the page(to get ID), on address bar you can find the ID</p>
		<img src="<?php echo plugins_url('/images/help2.png', __FILE__); ?>" style="padding:10px;border:1px solid #ccc;">
	</div>

</form>
</div>
<?php } 

add_action( 'wp_footer', 'show_wpsh_header');
function show_wpsh_header(){
	$where = esc_attr(get_option('wpsh_where'));
	if($where == "all"){
		echo wpsh_filtered_content();
	}
	else{
		if(($where == "home") && (is_home())){
			echo wpsh_filtered_content();
		}
		if(($where == "posts") && (is_single())){
			echo wpsh_filtered_content();
		}
		if($where == "selected"){
			$pages = explode(',',esc_attr( get_option('wpsh_page_ids')));
			$isPage = false;
			foreach($pages as $page){
				if(is_page($pages) || is_single($pages)){
					$isPage = true;
				}	
			}
			if($isPage == true)
				echo wpsh_filtered_content();
		}
		
	}
}

function wpsh_filtered_content(){
	wp_enqueue_script( 'wpsh_headerjs', plugins_url('wp-sticky-header/js/wpsh_header.js'), array(), '1.0.0', true );	
	$auto_close = esc_attr(get_option('wpsh_auto_close'));
	if($auto_close != 0){
		wp_register_script( 'wpsh_autoclose', plugins_url('wp-sticky-header/js/wpsh_autoclose.js') );
		wp_enqueue_script( 'wpsh_autoclose');
		$array_val = array( 'close_seconds' => $auto_close );
		wp_localize_script( 'wpsh_autoclose', 'php_vars', $array_val );
	}
	$header_content = get_option('wpsh_content');
	$closable = esc_attr(get_option('wpsh_closable'));
	if($closable == 1){
		$output = "<div class='wpsh_fixed'>".$header_content."<span class='wpsh_close'>X</span></div>";
	}
	else{
		$output = "<div class='wpsh_fixed'>".$header_content."</div>";
	}	
	return $output;
}


add_action( 'admin_enqueue_scripts', 'wpsh_color_picker' );
function wpsh_color_picker( $hook_suffix ) {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wpsh-script-handle', plugins_url('js/wpsh_pickcolor.js', __FILE__ ), array('wp-color-picker'), false, true );
}

add_action( 'wp_head', 'wpsh_custom_styles' );
function wpsh_custom_styles(){ ?>
	<style type="text/css">
		.wpsh_fixed{
			background-color: <?php echo esc_attr( get_option('wpsh_bg_color') ); ?> ;
			clear: both;
			color: <?php echo esc_attr( get_option('wpsh_text_color') ); ?>;
			margin-top: 0px;
			padding: 20px;
			position: fixed;
			text-align: center;
			<?php if(esc_attr(get_option('wpsh_position')) == 'top') { echo "top: 0;"; } else { echo "bottom:0;"; } ?>
			font-weight:bold;
			width: 100%;
			box-shadow: 5px 5px 5px 5px #888888;
			z-index:10001;
		} 
	<?php if(esc_attr(get_option('wpsh_closable')) == 1) { ?>
		.wpsh_close{
			color:#fff;
			opacity:0.5;
			float:right;
			margin-right:30px;
			font-weight:bold;
		}
		.wpsh_close:hover{
			opacity:1;
			cursor:pointer;
		}
	<?php } ?>
	</style>
	
<?php }

add_action( 'admin_init', 'wpsh_admin_init' );
add_action( 'upgrader_process_complete', 'wpsh_plugin_update');

function wpsh_plugin_update() { 
	if(!get_option('wpsh_position')){
		add_option('wpsh_position','top'); 
	}
	if(!get_option('wpsh_auto_close')){
		add_option('wpsh_auto_close',0);
	}	
}

function wpsh_admin_init() {
    /* Register our script. */
    wp_register_script( 'wpsh-script', plugins_url( 'js/wpsh-adminjs.js', __FILE__ ) );
	wp_enqueue_script( 'wpsh-script' );
}

function wpsh_admin_scripts() {
    /* Link our already registered script to a page */
    wp_enqueue_script( 'wpsh-script' );
} 

/* Installation and Un-installation part */
register_activation_hook(__FILE__, 'wpsh_install');
register_uninstall_hook(__FILE__, 'wpsh_uninstall');

function wpsh_install(){
	if(!get_option('wpsh_bg_color')){
		add_option('wpsh_bg_color','#E14C37');
	}
	if(!get_option('wpsh_text_color')){
		add_option('wpsh_text_color','#FFF');
	}
	if(!get_option('wpsh_closable')){
		add_option('wpsh_closable','1');
	}
	if(!get_option('wpsh_position')){
		add_option('wpsh_position','top');
	}
	if(!get_option('wpsh_content')){
		add_option('wpsh_content','Thanks for using Sticky Header');
	}
	if(!get_option('wpsh_where')){
		add_option('wpsh_where','all');
	}
	if(!get_option('wpsh_auto_close')){
		add_option('wpsh_auto_close',0);
	}
}


function wpsh_uninstall(){
	delete_option('wpsh_bg_color');
	delete_option('wpsh_text_color');
	delete_option('wpsh_closable');
	delete_option('wpsh_position');
	delete_option('wpsh_content');
	delete_option('wpsh_where');
	delete_option('wpsh_auto_close');
}

?>