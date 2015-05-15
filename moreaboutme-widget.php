<?php
/**
 * @package MoreAboutMe_widget
 * @version 1.3.1
 */
/*
  Plugin Name: MoreAboutMe Widget
  Plugin URI: http://wordpress.org/plugins/moreaboutme
  Description: Displays an AboutMe bloc, also known as a More About Me bloc, including a picture and some text. This plug-in is useful if you want to put a widget in a sidebar or footer to show a picture of your own and some descriptive text. Check the screenshots to see if it could fit your needs.
  Version: 1.3.1
  Author: Stéphane Moitry
  Author URI: http://stephane.moitry.fr
  License: GPL2
  Text Domain: moreaboutme-widget
  Domain Path: /languages
 */

/*  Copyright 2013-2015  Stéphane Moitry (stephane.moitry@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * moreaboutme_widget_Widget Class
 */
class moreaboutme_widget_Widget extends WP_Widget {

	/** constructor */
	function moreaboutme_widget_Widget() {
		parent::WP_Widget(false, 'MoreAboutMe Widget', array('description' => __('Displays an AboutMe bloc, including a picture and some text.', 'moreaboutme-widget')));
		$control_ops = array('width' => 400, 'height' => 350);
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$imageurl = esc_attr($instance['imageurl']);
		$filter = $instance['filter'];
		$allowhtml = $instance['allowhtml'];
		if (!empty( $allowhtml ))
			$texte = $instance['texte'];
		else
			$texte = esc_attr($instance['texte']);

		echo $before_widget;

		if ($title) {
			echo $before_title . $title . $after_title;
		}

		echo $this->render($texte, $imageurl, $filter);

		echo $after_widget;
	}

	/* Render the content, used by widget and shortcode methods */
	public static function render($texte, $imageurl, $filter) {
		$text = '';
		
		if ($imageurl != '') {
				$text = $text . '<div class="moreaboutme_img"><img src="' . $imageurl .'"></div>';
		}

		$text = $text . '<div class="moreaboutme_txt">' . (!empty( $filter ) ? wpautop( $texte ) : $texte) . '</div>';
		
		return $text;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['texte'] =  $new_instance['texte'];
		else
			$instance['texte'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['texte']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		$instance['allowhtml'] = isset($new_instance['allowhtml']);
		$instance['imageurl'] = strip_tags($new_instance['imageurl']);
		return $instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = '';
		$texte = '';
		$imageurl = '';

		if (isset($instance['title'])) {
			$title = esc_attr($instance['title']);
		}
		
		if (isset($instance['texte'])) {
			$texte = esc_textarea($instance['texte']);
		}
		
		if (isset($instance['imageurl'])) {
			$imageurl = esc_attr($instance['imageurl']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'moreaboutme-widget'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('texte'); ?>"><?php _e('Text:', 'moreaboutme-widget'); ?> <textarea class="widefat" class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('texte'); ?>" name="<?php echo $this->get_field_name('texte'); ?>"><?php echo $texte; ?></textarea></label></p>
		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'moreaboutme-widget'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('allowhtml'); ?>" name="<?php echo $this->get_field_name('allowhtml'); ?>" type="checkbox" <?php checked(isset($instance['allowhtml']) ? $instance['allowhtml'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('allowhtml'); ?>"><?php _e('Allow HTML tags', 'moreaboutme-widget'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('imageurl'); ?>"><?php _e('Image Url:', 'moreaboutme-widget'); ?> <input class="widefat" id="<?php echo $this->get_field_id('imageurl'); ?>" name="<?php echo $this->get_field_name('imageurl'); ?>" type="text" value="<?php echo $imageurl; ?>" /></label></p>
<?php
	}

}

/* ShortCode Handler */
function moreaboutme_widget_shortcode( $atts ) {
	$attributes = shortcode_atts( array(
	    'texte' => '',
	    'imageurl' => ''
	), $atts );
	
	$text = "<div class='widget_moreaboutme_widget'>".moreaboutme_widget_Widget::render($attributes['texte'], $attributes['imageurl'])."</div>";
	
	return $text;
}

/* Initialization Handler */
function moreaboutme_widget_init() {
	load_plugin_textdomain( 'moreaboutme-widget', false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
	register_widget( 'moreaboutme_widget_Widget' );
	wp_enqueue_style( 'moreaboutme_widget_Widget', plugin_dir_url( __FILE__ ).'moreaboutme-widget.css');
}

// register Widget
add_action('widgets_init', 'moreaboutme_widget_init');
// register ShortCode
add_shortcode( 'moreaboutme', 'moreaboutme_widget_shortcode' );
