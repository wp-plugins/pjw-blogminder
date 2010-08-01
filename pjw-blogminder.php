<?php
/*
	Plugin Name: PJW Blogminder
	Plugin URI: http://blog.ftwr.co.uk/wordpress/pjw-blogminder/
	Description: Allows users to configure a reminder for if they haven't blogged in a while.
	Author: Peter Westwood
	Version: 0.92
	Author URI: http://blog.ftwr.co.uk/
 */

class pjw_blogminder 
{
	function pjw_blogminder()
	{
		if ( is_admin() )
		{
			add_action('personal_options', array(&$this,'action_personal_options'));
			add_action('personal_options_update', array(&$this,'action_process_option_update'));
			add_action('edit_user_profile_update', array(&$this,'action_process_option_update'));
		}
		add_filter('get_user_option_pjw_blogminder_threshold', array($this,'filter_get_user_option_pjw_blogminder_threshold'));
		add_action('pjw_blogminder_cron', array(&$this,'action_pjw_blogminder_cron'));
		
		// If we have never been scheduled then schedule us up.
		if ( !wp_next_scheduled('pjw_blogminder_cron') )
			wp_schedule_event(time(), 'twicedaily', 'pjw_blogminder_cron');
	}
	
	/**
	 * Filter the return value of our user option
	 * 
	 * We require that the user option is a positive integer this filter enforces this so we don't have to worry
	 * everywhere we use get_user_option
	 * 
	 * @param mixed $option_value The user options value.
	 * @return int The filtered value
	 **/
	function filter_get_user_option_pjw_blogminder_threshold($option_value)
	{
		return absint($option_value);
	}

	/**
	 * Display the current value of the user option.
	 * 
	 * Filters the maximum Threshold through the 'pjw_blogminder_maximum_threshold' filter for another plugin to modify.
	 * 
	 * @return none
	 */
	function action_personal_options()
	{
		global $user_id;
		$user_reminder_interval = get_user_option('pjw_blogminder_threshold', $user_id);
	?>
	<tr>
		<th scope="row"><?php _e('Blogminder Threshold')?></th>
		<td>
			<label for="pjw_blogminder_threshold">
				<?php _e('Remind if I have not posted to this blog in the last', 'pjw_blogminder'); ?>
				<select name="pjw_blogminder_threshold" id="pjw_blogminder_threshold">
				<option value="0" <?php selected(0,$user_reminder_interval)?>><?php _e('&mdash; No reminder &mdash;', 'pjw_blogminder');?></option>
				<?php for ($num_days = 1; $num_days <= apply_filters('pjw_blogminder_maximum_threshold', 7) ; $num_days++) {?>
				<option value="<?php echo $num_days; ?>"<?php selected($num_days, $user_reminder_interval)?>>
					<?php printf( _n( '%d day', '%d days', $num_days, 'pjw_blogminder'), $num_days );?>
				</option>
				<?php }?>
				</select>
			</label>
		</td>
	</tr>
	<?php
	}

	/**
	 * Process the updated option
	 * 
	 * We can rely on WordPress having already checked a nonce for this action so we don't need our own protection here.
	 * 
	 * @return none
	 */
	function action_process_option_update()
	{
		global $user_id;
		update_user_option($user_id, 'pjw_blogminder_threshold', ( isset($_POST['pjw_blogminder_threshold']) ? absint($_POST['pjw_blogminder_threshold']) : 0 ) );
	}
	
	
	/**
	 * Go and check to see if all the users who have enabled this feature are up-to-date
	 * 
	 * We take a snapshot of the current time and then go and check all the users of this blog to see if they have
	 * written a post in there own defined interval.  If they haven't then they get a nice reminder email.
	 * 
	 * @return none
	 */
	function action_pjw_blogminder_cron()
	{
		$blog_users = get_users_of_blog();
		$now = time();
		
		foreach ($blog_users as $blog_user)
		{
			$user_reminder_interval = get_user_option('pjw_blogminder_threshold', $blog_user->user_id);
			
			if (0 != $user_reminder_interval)
			{
				$post = get_posts( array(	'numberposts' => 1,
											'author' => $blog_user->user_id,
											'post_status' => 'publish',
											'post_type' => 'post'
										) );
				if (isset($post[0]))
				{
					$post_gmttime = mysql2date('G', $post[0]->post_date_gmt);
					if ( ( $now - $post_gmttime ) > (86400 * $user_reminder_interval) )	
					{
						$message  = sprintf(__("Hi,\r\n\r\nThis is your friendly blogminder service.\r\n\r\nI just wanted to let you know that you haven't posted in at least %d day(s) and you asked me to remind you.\r\n\r\nYou might want to head over to your blog and write something now!\r\n\r\nOver and Out,\r\n\r\nBlogminder"), $user_reminder_interval) . "\r\n";
					
						wp_mail($blog_user->user_email, sprintf(__('[%s] Blogminder'), get_option('blogname')), $message);
					}					
				}		
			}
		}
	}
}

/* Initialise outselves */
add_action('plugins_loaded', create_function('','global $pjw_blogminder_instance; $pjw_blogminder_instance = new pjw_blogminder();'));
?>
