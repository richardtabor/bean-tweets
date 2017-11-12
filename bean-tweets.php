<?php
/**
 * Plugin Name: Bean Tweets
 * Plugin URI: http://themebeans.com/plugin/bean-twitter-widget/?ref=plugin_bean_tweets
 * Description: Enables a Twitter widget using Twitter API v1.1. You must create a <a href="https://dev.twitter.com/apps/">Twitter App</a> to retrieve access tokens. <a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/">Learn More</a>
 * Version: 2.3.1
 * Author: ThemeBeans
 * Author URI: http://www.themebeans.com/?ref=plugin_bean_tweets
 *
 *
 * @package Bean Plugins
 * @subpackage BeanTweets
 * @author ThemeBeans
 * @since BeanTweets 1.0
 */




/*===================================================================*/
/* MAKE SURE WE DO NOT EXPOSE ANY INFO IF CALLED DIRECTLY
/*===================================================================*/
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

/*===================================================================*/
/* ADD SETTINGS LINK TO PLUGINS PAGE
/*===================================================================*/
define( 'BEANTWEETS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_filter( 'plugin_action_links', 'beantweets_plugin_action_links', 10, 2 );

function beantweets_plugin_action_links( $links, $file ) {
	if ( $file != BEANTWEETS_PLUGIN_BASENAME )
		return $links;

	$settings_link = '<a href="' . menu_page_url( 'bean-tweets', false ) . '">'
		. esc_html( __( 'Settings', 'bean-tweets' ) ) . '</a>';

	array_unshift( $links, $settings_link );

	return $links;
}

/*===================================================================*/
/*
/* BEGIN BEAN TWEETS PLUGIN
/*
/*===================================================================*/
/*===================================================================*/
/* INCLUDES
/*===================================================================*/
require_once('widget-tweets.php');
?>