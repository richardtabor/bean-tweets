<?php
/*
Plugin Name: Bean Tweets
Plugin URI: http://themebeans.com/plugin/bean-twitter-widget/?ref=plugin_bean_tweets
Description: Enables a Twitter widget using Twitter API v1.1. You must create a <a href="https://dev.twitter.com/apps/">Twitter App</a> to retrieve access tokens. <a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/">Learn More</a>
Version: 2.0
Author: ThemeBeans
Author URI: http://www.themebeans.com/?ref=plugin_bean_tweets
*/

// DON'T CALL ANYTHING
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define('BEAN_TWEETS_PATH', plugin_dir_url( __FILE__ ));

// INCLUDE WIDGET
require_once('widget-tweets.php');
?>