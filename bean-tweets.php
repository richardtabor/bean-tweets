<?php
/**
 * Plugin Name: Bean Tweets
 * Plugin URI: http://themebeans.com/plugin/bean-twitter-widget/?ref=plugin_bean_tweets
 * Description: Enables a Twitter widget using Twitter API v1.1. You must create a <a href="https://dev.twitter.com/apps/">Twitter App</a> to retrieve access tokens. <a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/">Learn More</a>
 * Version: 2.1
 * Author: Rich Tabor / ThemeBeans
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

define('BEAN_TWEETS_PATH', plugin_dir_url( __FILE__ ));




/*===================================================================*/
/* PLUGIN UPDATER
/*===================================================================*/
//CONSTANTS
define( 'BEANTWEETS_EDD_TB_URL', 'http://themebeans.com' );
define( 'BEANTWEETS_EDD_TB_NAME', 'Bean Tweets' );

//INCLUDE UPDATER
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include( dirname( __FILE__ ) . '/updates/EDD_SL_Plugin_Updater.php' );
}

include( dirname( __FILE__ ) . '/updates/EDD_SL_Setup.php' );

//LICENSE KEY
$license_key = trim( get_option( 'edd_beantweets_license_key' ) );

//CURRENT BUILD
$edd_updater = new EDD_SL_Plugin_Updater( BEANTWEETS_EDD_TB_URL, __FILE__, array(
		'version' 	=> '2.1',
		'license' 	=> $license_key,
		'item_name' => BEANTWEETS_EDD_TB_NAME,
		'author' 	=> 'ThemeBeans'
	)
);




/*===================================================================*/
/* INCLUDES
/*===================================================================*/
require_once('widget-tweets.php');
?>