<?php
/**
 * Plugin Name: wp-vuetify-admin-options
 * Plugin URI: https://github.com/supraniti/wp-vuetify-admin-options/
 * Description: Rapid Settings Panel Developer
 * Version: 1.0.0
 * Author: Supraniti
 * Author URI: https://github.com/supraniti/
 * License: GPL2
 */
add_action('admin_menu', 'wp_vao_menu');
function wp_vao_menu(){
	$config = json_decode( file_get_contents( plugin_dir_url( __FILE__ ) . 'config/config.json' ), true )['FormConfiguration'];
	add_menu_page($config['page_title'], $config['menu_title'], 'administrator', $config['menu_slug'], 'wp_vao_page', $config['dashicon'], $config['position']);
	add_action( 'admin_init', 'wp_vao_register_settings' );
}
function wp_vao_register_settings(){
	$config = json_decode( file_get_contents( plugin_dir_url( __FILE__ ) . 'config/config.json' ), true )['FormConfiguration'];
	register_setting( $config['settings_slug'] . '-settings-group', $config['settings_slug']);
}
add_action( 'wp_ajax_vao_update','vao_update');
function vao_update(){
	$settings = json_decode( get_option($_GET['settings']),true );
	$settings[$_GET['key']] = $_GET['value'];
	update_option( $_GET['settings'], json_encode($settings, JSON_UNESCAPED_UNICODE) );
	echo 'OK';
	exit;
}

function wp_vao_page(){
	$config = json_decode( file_get_contents( plugin_dir_url( __FILE__ ) . 'config/config.json' ), true )['FormConfiguration'];
	$settings = json_decode( get_option($config['settings_slug']),true );
	if (!$settings){
		$settings = [];
	}
	?>
	<style>
		#wp-vuetify-admin-options{
			height:calc(100vh - 55px);
			margin-top: 10px;
		}
		@media screen and (min-width: 783px) {
		  #wp-vuetify-admin-options{
			margin-right: -10px;
		  }
		}
	</style>
	<script>
		window.FormConfiguration = <?php echo json_encode($config, JSON_UNESCAPED_UNICODE); ?>;
		window.CurrentSettings = <?php echo json_encode($settings, JSON_UNESCAPED_UNICODE); ?>;
		window.AjaxURL = <?php echo "'" . admin_url( 'admin-ajax.php' ) . "'"; ?>;
		function resizeIframe(iframe) {
			iframe.height = iframe.contentWindow.document.body.scrollHeight + "px";
		}
	</script>
	<iframe id="wp-vuetify-admin-options" style="" onload="resizeIframe(this)" scrolling="no" frameborder="0" width="100%" allowtransparency="true" src="<?php echo plugin_dir_url(__FILE__) . 'app.html'?>"></iframe>
	<?php
}
