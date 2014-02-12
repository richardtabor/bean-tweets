<?php
/**
 * Widget Name: Bean Tweets Widget
 * Widget URI: http://themebeans.com
 * Description:  A custom widget that displays your most recent tweets.
 * Author: ThemeBeans
 * Author URI: http://themebeans.com
 *  
 *   
 * @package WordPress
 * @subpackage BeanTweets
 * @author ThemeBeans
 * @since Bean Tweets 2.0
 */

// ADD FUNTION TO WIDGETS_INIT
add_action( 'widgets_init', create_function( '', 'register_widget("Bean_Tweets_Plugin_Widget");' ) );

//REQUIRED
require_once('oauth/twitteroauth.php');

//WIDGET CLASS
class Bean_Tweets_Plugin_Widget extends WP_Widget {

private $bean_twitter_oauth = array();


	
	
	/*===================================================================*/
	/*	WIDGET SETUP
	/*===================================================================*/
	public function __construct() 
	{
		parent::__construct(
			'bean_tweets', // BASE ID
			__('Bean Tweets (ThemeBeans)', 'bean'), // NAME
			array(
				'classname' => 'widget_bean_tweets',
				'description' => __('A widget that displays your most recent tweets', 'bean')
			)
		);
	}
	
	
	
	
	/*===================================================================*/
	/*	DISPLAY WIDGET
	/*===================================================================*/	
	public function widget( $args, $instance ) 
	{
		extract( $args, EXTR_SKIP );
	
		echo $before_widget;
	
		$title = apply_filters('widget_title', $instance['title'] );
		if ( $title ) { echo $before_title . $title . $after_title; }
	
		$result = $this->getTweets($instance['username'], $instance['count']);
	
		echo '<ul>';
	
		if( $result && is_array($result) ) {
			foreach( $result as $tweet ) {
				$text = $this->link_replace($tweet['text']);
				echo '<li>';
					echo $text;
					echo '<a class="twitter-time-stamp" href="http://twitter.com/' . $instance['username'] . '/status/' . $tweet['id'] . '">' . $tweet['timestamp'] . '</a>';
				echo '</li>';
			}
		} else {
			echo '<li>' . __('There was an error grabbing the Twitter feed', 'bean') . '</li>';
		}
	
		echo '</ul>';
	
		if( !empty($instance['tweettext']) ) {
			echo '<a class="follow-link button" href="http://twitter.com/' . $instance['username'] . '">' . $instance['tweettext'] . '</a>';
		}
	
		echo $after_widget;
	} // end widget
	
	
	
	
	/*===================================================================*/
	/*	UPDATE WIDGET
	/*===================================================================*/
	public function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;
	
		// STRIP TAGS TO REMOVE HTML - IMPORTANT FOR TEXT IMPUTS
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['count'] = strip_tags( $new_instance['count'] );
		$instance['tweettext'] = strip_tags( $new_instance['tweettext'] );
	
		return $instance;
	} // end update
	
	
	
	
	/*===================================================================*/
	/*	WIDGET SETTINGS (FRONT END PANEL)
	/*===================================================================*/ 
	public function form( $instance ) 
	{
		$instance = wp_parse_args(
			(array) $instance
		);
	
		//WIDGET DEFAULTS
		$defaults = array(
			'title' => 'Twitter.',
			'username' => '',
			'count' => '3',
			'tweettext' => 'Follow',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	
		$access_token = get_option('bean_tw_access_token');
		$access_token_secret = get_option('bean_tw_access_token_secret');
		$consumer_key = get_option('bean_tw_consumer_key');
		$consumer_key_secret = get_option('bean_tw_consumer_secret');
	
		//IF SETTINGS ARE EMPTY
		if( empty($access_token) || empty($access_token_secret) || empty($consumer_key) || empty($consumer_key_secret) ) {
			echo '<p><a href="options-general.php?page=bean-tweets-plugin-settings">Configure Twitter Widget</a></p>'; 
		} else { ?>
	
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'bean') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Twitter Username: (ex: <a href="http://www.twitter.com/themebeans" target="_blank">ThemeBeans</a>)', 'bean') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e('Number of Tweets:', 'bean') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" value="<?php echo $instance['count']; ?>" />
			</p>
			
			<p>
			<label for="<?php echo $this->get_field_id( 'tweettext' ); ?>"><?php _e('Button Text:', 'bean') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tweettext' ); ?>" name="<?php echo $this->get_field_name( 'tweettext' ); ?>" value="<?php echo $instance['tweettext']; ?>" />
			</p>
		
		<?php
		} //END if( empty($access_token)
	
	} // END FORM
	
	
	




	/**
	 * Return tweets, grab links and output.
	 *  
	 *   
	 * @package WordPress
	 * @subpackage Bean Tweets
	 * @author ThemeBeans
	 * @since Bean Tweets 2.0
	 */
 		 
	/*===================================================================*/
	/*	RETURN TWEETS
	/*===================================================================*/ 	 
	public function getTweets($username, $count) 
	{
		$config = array();
		$config['username'] = $username;
		$config['count'] = $count;
		$config['access_token'] = get_option('bean_tw_access_token');
		$config['access_token_secret'] = get_option('bean_tw_access_token_secret');
		$config['consumer_key'] = get_option('bean_tw_consumer_key');
		$config['consumer_key_secret'] = get_option('bean_tw_consumer_secret');
	
		$transname = 'bean_tw_' . $username . '_' . $count;
	
		$result = get_transient( $transname );
		if( !$result ) {
			$result = $this->oauthRetrieveTweets($config);
	
			if( isset($result['errors']) ){
				$result = NULL; 
			} else {
				$result = $this->parseTweets( $result );
				set_transient( $transname, $result, 300 );
			}
		} else {
			if( is_string($result) )
				unserialize($result);
		}
	
		return $result;
	}
		
	
	
	
	/*===================================================================*/
	/*	OAUTH - API 1.1
	/*===================================================================*/  
	private function oauthRetrieveTweets($config) 
	{
		if( empty($config['access_token']) ) 
			return array('error' => __('Not properly configured, check settings', 'bean'));		
		if( empty($config['access_token_secret']) ) 
			return array('error' => __('Not properly configured, check settings', 'bean'));
		if( empty($config['consumer_key']) ) 
			return array('error' => __('Not properly configured, check settings', 'bean'));		
		if( empty($config['consumer_key_secret']) ) 
			return array('error' => __('Not properly configured, check settings', 'bean'));		
	
		$options = array(
			'trim_user' => true,
			'exclude_replies' => false,
			'include_rts' => true,
			'count' => $config['count'],
			'screen_name' => $config['username']
		);
	
		$connection = new TwitterOAuth($config['consumer_key'], $config['consumer_key_secret'], $config['access_token'], $config['access_token_secret']);
		$result = $connection->get('statuses/user_timeline', $options);
	
		return $result;
	}
	
		
		
	
	/*===================================================================*/
	/*	PARSE / SANITIZE
	/*===================================================================*/   
	public function parseTweets($results = array()) 
	{
		$tweets = array();
		foreach($results as $result) {
			$temp = explode(' ', $result['created_at']);
			$timestamp = $temp[2] . ' ' . $temp[1] . ' ' . $temp[5];
	
			$tweets[] = array(
				'timestamp' => $timestamp,
				'text' => filter_var($result['text'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH),
				'id' => $result['id_str']
			);
		}
	
		return $tweets;
	}
	
	
	
	
	/*===================================================================*/
	/*	CHANGE TEXT TO LINK
	/*===================================================================*/    
	private function bean_change_text_links($matches) 
	{
		return '<a href="' . $matches[0] . '" target="_blank">' . $matches[0] . '</a>';
	} //END bean_change_text_links
	
	
	
	
	/*===================================================================*/
	/*	USERNAME LINK
	/*===================================================================*/ 
	private function bean_change_username_link($matches) 
	{
		return '<a href="http://twitter.com/' . $matches[0] . '" target="_blank">' . $matches[0] . '</a>';
	} //END bean_change_username_link
	
	
	
	
	/*===================================================================*/
	/*	CONVERT LINKS
	/*===================================================================*/ 
	public function link_replace($text) 
	{
		//LINKS
		$string = preg_replace_callback(
			"/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/",
			array(&$this, 'bean_change_text_links'),
			$text
		);
	
		//USERNAMES
		$string = preg_replace_callback(
			'/@([A-Za-z0-9_]{1,15})/', 
			array(&$this, 'bean_change_username_link'), 
			$string
		);
	
		return $string;
	} //END link_replace
	
} // END CLASS







	
/**
 * Widget Settings Admin Page Output.
 * This section adds a "Twitter Settings" to the Settings dashboard link.
 *  
 *   
 * @package WordPress
 * @subpackage Bean Tweets
 * @author ThemeBeans
 * @since Bean Tweets 2.0
 */
 
/*===================================================================*/
/*	CREATE ADMIN LINK
/*===================================================================*/ 
function bean_tweets_options_page_settings() 
{
	add_options_page(
		__('Twitter Settings', 'bean'), __('Bean Tweets', 'bean'), 'manage_options', 'bean-tweets-plugin-settings', 'bean_tweets_admin_page'
	);
} //END bean_tweets_settings

add_action( 'admin_menu', 'bean_tweets_options_page_settings' );




/*===================================================================*/
/*	REGISTER SETTINGS
/*===================================================================*/  
add_action('admin_init', 'bean_tw_register_settings');

function bean_tweets_settings() 
{
	$bean_tw = array();
	$bean_tw[] = array('label' => 'Consumer Key:', 'name' => 'bean_tw_consumer_key');
	$bean_tw[] = array('label' => 'Consumer Secret:', 'name' => 'bean_tw_consumer_secret');
	$bean_tw[] = array('label' => 'Account Access Token:', 'name' => 'bean_tw_access_token');
	$bean_tw[] = array('label' => 'Account Access Token Secret:', 'name' => 'bean_tw_access_token_secret');

	return $bean_tw;
} //END bean_tweets_settings

function bean_tw_register_settings() 
{
	$settings = bean_tweets_settings();
	foreach($settings as $setting) {
		register_setting('bean_tweets_settings', $setting['name']);
	}
} //END bean_tw_register_settings




/*===================================================================*/
/*	CREATE THE SETTINGS PAGE
/*===================================================================*/  
function bean_tweets_admin_page() 
{
	if( !current_user_can('manage_options') ) { wp_die( __('Insufficient permissions', 'bean') ); }

	$settings = bean_tweets_settings();
	
	$license = get_option( 'edd_beantweets_license_key' );
	$status = get_option( 'edd_beantweets_license_status' );

	echo '<div class="wrap">';
	 	screen_icon();
		echo '<h2>Bean Tweets Plugin</h2>';
		echo '<div class="wrap">'; 
		echo '<p>' . __('Display your most recent tweets throughout your theme with the Bean Tweets widget. In order to do this, you must first create a Twitter application and insert the required codes below. Then, simply add the Bean Tweets widget to a widget area within your Widgets Dashboard. If you need additional help, we wrote a detailed <strong><a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/" target="_blank">OAuth Guide</a></strong> to help you along. Cheers!', 'bean' ) . '</p></br>';
		?>
			<?php
			echo '<form method="post" action="options.php">';
				
				
				
				echo '<h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 10px;">' . __('How To', 'bean' ) . '</h4>';
				echo '<ol>';
					echo '<li><a href="https://dev.twitter.com/apps/new" target="_blank">' . __( 'Create a Twitter application', 'bean' ) . '</a></li>';
					echo '<li>' . __( 'Fill in all fields on the create application page.', 'bean' ) . '</li>';
					echo '<li>' . __( 'Agree to rules, fill out captcha, and submit your application.', 'bean' ) . '</li>';
					echo '<li>' . __( 'Click the "Create my Access Tokens" button.', 'bean' ) . '</li>';
					echo '<li>' . __( 'Upon refresh, copy the Consumer Key, Consumer Secret, Access Token & Access Token Secret codes.', 'bean' ) . '</li>';
					echo '<li>' . __( "Paste each code into their respective fields below." ) . '</li>';
					echo '<li>' . __( "Click the 'Save Changes' button below." ) . '</li>';
					echo '<li>' . __( "Add the 'Bean Tweets' widget to a widget area in your <a href='widgets.php'>Widgets Dashboard</a>." ) . '</li>';
				echo '</ol></br>';
	
				settings_fields('bean_tweets_settings');
				
				echo '<h4 style="font-size: 15px; font-weight: 600; color: #222; margin-bottom: 7px;">' . __('OAuth Codes', 'bean' ) . '</h4>';
				
				echo '<table>';
					foreach($settings as $setting) {
						echo '<tr>';
							echo '<td style="padding-right: 20px;">' . $setting['label'] . '</td>';
							echo '<td><input type="text" style="width:500px;" name="'.$setting['name'].'" value="'.get_option($setting['name']).'" /></td>';
						echo '</tr>';
					}
				echo '</table>';
	
				submit_button();
	
			echo '</form>';
		echo '</div>';
	echo '</div>';
} //END bean_tweets_admin_page
?>