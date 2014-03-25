<?php
/**
 * Plugin Name: Bean Tweets
 * Plugin URI: http://themebeans.com/plugin/bean-twitter-widget/?ref=plugin_bean_tweets
 * Description: Enables a Twitter widget using Twitter API v1.1. You must create a <a href="https://dev.twitter.com/apps/">Twitter App</a> to retrieve access tokens. <a href="http://themebeans.com/how-to-create-access-tokens-for-twitter-api-1-1/">Learn More</a>
 * Version: 2.2.1
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
/*
/* PLUGIN UPDATER FUNCTIONALITY
/*
/*===================================================================*/
define( 'EDD_BEANTWEETS_TB_URL', 'http://themebeans.com' );
define( 'EDD_BEANTWEETS_NAME', 'Bean Tweets' );

//LOAD UPDATER CLASS
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) 
{
	include( dirname( __FILE__ ) . '/updates/EDD_SL_Plugin_Updater.php' );
}
//INCLUDE UPDATER SETUP
include( dirname( __FILE__ ) . '/updates/EDD_SL_Activation.php' );


/*===================================================================*/
/* UPDATER SETUP
/*===================================================================*/
function beantweets_license_setup() 
{
	add_option( 'edd_beantweets_activate_license', 'BEANTWEETS' );
	add_option( 'edd_beantweets_license_status' );
}
add_action( 'init', 'beantweets_license_setup' );

function edd_beantweets_plugin_updater() 
{
	//RETRIEVE LICENSE KEY
	$license_key = trim( get_option( 'edd_beantweets_activate_license' ) );

	$edd_updater = new EDD_SL_Plugin_Updater( EDD_BEANTWEETS_TB_URL, __FILE__, array( 
			'version' 	=> '2.2.1',
			'license' 	=> $license_key,
			'item_name' => EDD_BEANTWEETS_NAME,
			'author' 	=> 'Rich Tabor / ThemeBeans'
		)
	);
}
add_action( 'admin_init', 'edd_beantweets_plugin_updater' );


/*===================================================================*/
/* DEACTIVATION HOOK - REMOVE OPTION
/*===================================================================*/
function beantweets_deactivate() 
{
	delete_option( 'edd_beantweets_activate_license' );
	delete_option( 'edd_beantweets_license_status' );
}
register_deactivation_hook( __FILE__, 'beantweets_deactivate' );








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