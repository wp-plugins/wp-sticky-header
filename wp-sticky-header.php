<?php
/**
 * Plugin Name: WP Sticky Header
 * Plugin URI: http://wordpress.org/plugins/wp-sticky-header/
 * Description: Plugin to display some content/notification on top of the webpage.
 * Version: 1.01
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
	register_setting( 'wpsh-settings-group', 'wpsh_content' );
	register_setting( 'wpsh-settings-group', 'wpsh_where' );
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
        <th scope="row">Where to Display?</th>
        <td><input type="radio" name="wpsh_where" value="home" <?php if(esc_attr( get_option('wpsh_where')) == "home") echo "checked"; ?> /><b>In home page</b> 
		<br><input type="radio" name="wpsh_where" value="all" <?php if(esc_attr( get_option('wpsh_where')) == "all") echo "checked"; ?> /><b>In all pages</b></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Content</th>
        <td><textarea name="wpsh_content" style="resize:none" ><?php echo esc_attr( get_option('wpsh_content') ); ?></textarea> &nbsp;&nbsp;content of the header</td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

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
		if(is_home()){
			echo wpsh_filtered_content();
		}
	}
}

function wpsh_filtered_content(){
	wp_enqueue_script( 'wpsh_headerjs', plugins_url('wp-sticky-header/js/wpsh_header.js'), array(), '1.0.0', true );
		
	$closable = esc_attr(get_option('wpsh_closable'));
	$header_content = get_option('wpsh_content');
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
			top: 0;
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
			padding:5px;
		}
		.wpsh_close:hover{
			opacity:1;
			cursor:pointer;
		}
	<?php } ?>
	</style>
<?php }


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
	if(!get_option('wpsh_content')){
		add_option('wpsh_content','Thanks for using Sticky Header');
	}
	if(!get_option('wpsh_where')){
		add_option('wpsh_where','all');
	}
}


function wpsh_uninstall(){
	delete_option('wpsh_bg_color');
	delete_option('wpsh_text_color');
	delete_option('wpsh_closable');
	delete_option('wpsh_content');
}

?>