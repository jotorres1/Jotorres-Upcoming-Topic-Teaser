<?php
/*
Plugin Name: Jotorres Upcoming Topic Teaser
Plugin URI: http://www.jotorres.com/myprojects/jt-upcoming-teaser
Description: Displays your upcoming posts to tease your readers
Version: 0.2
Author: Jorge Torres
Author URI: http://www.jotorres.com
License: GPL2

 *  Copyright 2012  Jorge Torres  (email : http://www.jotorres.com/contact/)

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

/* Start of class jt-teaser-widget */

class jt_teaser_widget extends WP_Widget{
	
	// Constructor
	function __construct(){
		// Create widget settings 
		$widget_opt = array(
						'classname' => 'jt_teaser_widget',
						'description' => 'Display a small list of your upcoming topics to keep readers intrigued'
						);
		// Create widget control settings							
		$control_opt = array(
						'id_base' => 'jt_teaser_widget'
					);
		// Create the widget
		$this->WP_Widget('jt_teaser_widget', 'Topic Teaser', $widget_opt, $control_opt);
	}
	// Extract Args //

	function widget($args, $instance) {
		extract($args);
		$title 		= apply_filters('widget_title', $instance['title']); // the widget title
		$teaser_num = $instance['teaser_num']; // the number of posts to show
		$posttype 	= $instance['post_type']; // the type of posts to show
		$showfeedsurl 	= isset($instance['show_feeds_url']) ? $instance['show_feeds_url'] : false ; // whether or not to show the newsletter link
		$feedsurl 	= $instance['feeds_url']; // URL of newsletter signup
		$authorcredit	= isset($instance['author_credit']) ? $instance['author_credit'] : false ; // give plugin author credit
		
		// Before widget
		echo $before_widget;
		// Title of widget
		if($title){ echo $before_title.$title.$after_title; }
		// Widget output
		?>
		<p>
		<?php 
			$teaserquery = new WP_Query(array('posts_per_page' => $teaser_num, 'nopaging' => 0, 'post_status' => $posttype, 'order' => 'ASC'));
			if($teaserquery->have_posts()){
				while($teaserquery->have_posts()) : $teaserquery->the_post();
				$do_not_duplicate = $post->ID; ?>
				<ul>
					<li>
						<?php the_title(); ?>
					</li>
				</ul>
				<?php endwhile;
			}else{ ?>
				No upcoming posts, but stay tuned!.<?php } ?>
		</p>
		<p>
			<a href="<?php bloginfo('rss2_url') ?>" title="Subscribe to <?php bloginfo('name') ?>">
                <img style="vertical-align:middle; margin:0 10px 0 0;" src="<?php bloginfo('wpurl') ?>/wp-content/plugins/jt-upcoming-teaser/icons/rss.png" width="16px" height="16px" alt="Subscribe to <?php bloginfo('name') ?>" />
            </a>
            Don't miss it - <strong><a href="<?php if($showfeedsurl){ echo $feedsurl; }else { bloginfo('rss2_url'); } ?>" title="Subscribe to <?php bloginfo('name') ?>">Subscribe by RSS.</a></strong>
		</p><?php 
		if($authorcredit){?>
			<p style="font-size:10px;">
            Widget created by <a href="http://www.jotorres.com" title="PHP Web Development tutorials">Jorge Torres</a>
        	</p>
        <?php }
		// After widget
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['teaser_num'] = strip_tags($new_instance['teaser_num']);
		$instance['post_type'] = $new_instance['post_type'];
		$instance['show_feeds_url'] = $new_instance['show_feeds_url'];
		$instance['feeds_url'] = strip_tags($new_instance['feeds_url'],'<a>');
		$instance['author_credit'] = $new_instance['author_credit'];
		return $instance;
	}
	
	// Widget control panel
	function form($instance) {

	$defaults = array( 'title' => 'Upcoming Posts', 'teaser_num' => 3, 'post_type' => 'future', 'show_newsletter' => false, newsletter_url => '', author_credit => 'on' );
	$instance = wp_parse_args( (array) $instance, $defaults ); ?>

	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>'" type="text" value="<?php echo $instance['title']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('teaser_num'); ?>"><?php _e('Number of upcoming posts to display'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('teaser_num'); ?>" name="<?php echo $this->get_field_name('teaser_num'); ?>" type="text" value="<?php echo $instance['teaser_num']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('post_type'); ?>">Post status:</label>
		<select id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>" class="widefat" style="width:100%;">
			<option value="future,draft" <?php selected('future,draft', $instance['post_type']); ?>>Both scheduled posts and drafts</option>
			<option value="future" <?php selected('future', $instance['post_type']); ?>>Scheduled posts only</option>
			<option value="draft" <?php selected('draft', $instance['post_type']); ?>>Drafts only</option>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('show_feeds_url'); ?>"><?php _e('Show Custom Feeds URL?'); ?></label>
		<input type="checkbox" class="checkbox" <?php checked( $instance['show_feeds_url'], 'on' ); ?> id="<?php echo $this->get_field_id('show_feeds_url'); ?>" name="<?php echo $this->get_field_name('show_feeds_url'); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('feeds_url'); ?>"><?php _e('Feeds URL:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('feeds_url'); ?>" name="<?php echo $this->get_field_name('feeds_url'); ?>" type="text" value="<?php echo $instance['feeds_url']; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('author_credit'); ?>"><?php _e('Give credit to plugin author?'); ?></label>
		<input type="checkbox" class="checkbox" <?php checked( $instance['author_credit'], 'on' ); ?> id="<?php echo $this->get_field_id('author_credit'); ?>" name="<?php echo $this->get_field_name('author_credit'); ?>" />
	</p>
    <?php }
			
}

/* End class of teaser widget */

add_action('widgets_init', create_function('','return register_widget("jt_teaser_widget");'));
?>