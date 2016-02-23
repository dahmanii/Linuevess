<?php
/*
Plugin Name: Contact Box Widget
Plugin URI: http://imath.owni.fr/2011/04/30/contact-box/
Description: Widget to display a contact box.
Version: 1.0
Requires at least: 3.0
Tested up to: 3.1
License: GNU/GPL 2
Author: imath
Author URI: http://imath.owni.fr/
*/

define( "CBW_JS_URL" , WP_PLUGIN_URL . '/' . 'contact-box-widget/cb-js.js');

function cbw_imath_register_widget() {
	add_action('widgets_init', create_function('', 'return register_widget("CBW_Imath_Widget");') );
}
add_action( 'plugins_loaded', 'cbw_imath_register_widget' );

class CBW_Imath_Widget extends WP_Widget {

	function cbw_imath_widget() {
		parent::WP_Widget( false, $name = 'Contact Box');
	}

	function widget( $args, $instance ) {

		extract( $args );

		echo $before_widget;
		
		preg_match_all("/id\=\"cbw_imath_widget-(.*)\" class/", $before_widget, $matches, PREG_SET_ORDER);
		
		if($instance['cbw_custom_name']){
			echo $before_title .
				 $instance['cbw_custom_name'].
			     $after_title;
		}
		else echo $before_title .
			 $widget_name.
		     $after_title; ?>
		
		<div class="cbw_widget" id="cbw_<?php echo $matches[0][1];?>" style="display:none">
			<div class="cbw-from">
				<label for="cbw_from_mail">Votre mail</label><br/>
				<input type="text" name="cbw_from_mail" style="width:100%">
				<input type="hidden" name="ref_cbw" value="<?php echo $matches[0][1];?>">
			</div>
			<div class="cbw-message">
				<label for="cbw_from_message">Votre message</label><br/>
				<textarea name="cbw_from_message" style="width:100%" rows="5"></textarea>
			</div>
			<div class="cbw-send" style="cursor:pointer">
				<a class="cbw-submit">Envoyer</a>
				<span class="cbw_wait" style="display:none"></span>
			</div>
			
		</div>

	<?php echo $after_widget; ?>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['cbw_custom_name'] = strip_tags( $new_instance['cbw_custom_name'] );
		$instance['cbw_to'] = strip_tags( $new_instance['cbw_to'] );
		$instance['cbw_custom_object'] = strip_tags( $new_instance['cbw_custom_object'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'cbw_custom_name' => 'Contact','cbw_to' => 'mail', 'cbw_custom_object' => 'Objet' ) );
		$cbw_custom_name = strip_tags( $instance['cbw_custom_name'] );
		$cbw_to = strip_tags( $instance['cbw_to'] );
		$cbw_custom_object = strip_tags( $instance['cbw_custom_object'] );
		?>
		
		<p><label for="cbw-imath-widget-cbw-custom-name">Titre <input class="widefat" id="<?php echo $this->get_field_id( 'cbw_custom_name' ); ?>" name="<?php echo $this->get_field_name( 'cbw_custom_name' ); ?>" type="text" value="<?php echo attribute_escape( $cbw_custom_name ); ?>" style="width: 80%" /></label></p>
		<p><label for="cbw-imath-widget-cbw-custom-object">Objet <input class="widefat" id="<?php echo $this->get_field_id( 'cbw_custom_object' ); ?>" name="<?php echo $this->get_field_name( 'cbw_custom_object' ); ?>" type="text" value="<?php echo attribute_escape( $cbw_custom_object ); ?>" style="width: 80%" /></label></p>
		<p><label for="cbw-imath-widget-cbw-to">Mail de réception <input class="widefat" id="<?php echo $this->get_field_id( 'cbw_to' ); ?>" name="<?php echo $this->get_field_name( 'cbw_to' ); ?>" type="text" value="<?php echo attribute_escape( $cbw_to ); ?>" style="width: 100%" /></label></p>
	<?php
	}
}

function cbw_load_js(){
	if(!is_admin()){
		wp_enqueue_script('jquery');
	}
}

function cbw_load_custom_js(){
	?>
	<script type="text/javascript">
		if(!window.ajaxurl){
			ajaxurl = "<?php echo admin_url('admin-ajax.php');?>";
		}
	</script>
	<script type="text/javascript" src="<?php echo CBW_JS_URL;?>"></script>
	<?php
}

add_action('wp_head','cbw_load_custom_js');
add_action('get_header','cbw_load_js');

function cbw_send_mail(){
	$cbw_infos = get_option('widget_cbw_imath_widget');
	$ref = $_POST['ref'];
	$to = $cbw_infos[$ref]['cbw_to'];
	$admin_email = get_bloginfo('admin_email');
	$from_name = get_bloginfo('name');
	$subject = stripslashes(wp_kses($cbw_infos[$ref]['cbw_custom_object'],array()));
	$message = stripslashes(wp_kses($_POST['txt'],array()));
	$message .= "\n________________\n\nEnvoyé par : ".$_POST['from'];
	$message_headers = "MIME-Version: 1.0\n" . "From: \"{$from_name}\" <{$admin_email}>\n" . "Content-Type: text/plain; charset=\"" . get_option( 'blog_charset' ) . "\"\n";
	
	if(wp_mail( $to, $subject, $message, $message_headers) ){
		echo "ok";
	}
	else echo "ko";
	die();
}

add_action('wp_ajax_cbw_send', 'cbw_send_mail');
add_action('wp_ajax_nopriv_cbw_send', 'cbw_send_mail');