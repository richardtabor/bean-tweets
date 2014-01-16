<?php
/**
 * This file sets up the license updater.
 *
 *  
 * @package Bean Plugins
 * @subpackage BeanTweets
 * @author ThemeBeans
 * @since BeanTweets 2.1
 */
 
/*===================================================================*/
/* REGISTER OPTION
/*===================================================================*/
function edd_beantweets_register_option() 
{
	register_setting('edd_beantweets_license', 'edd_beantweets_license_key', 'edd_bean_tweets_sanitize_license' );
}
add_action('admin_init', 'edd_beantweets_register_option');

function edd_bean_tweets_sanitize_license( $new ) 
{
	$old = get_option( 'edd_beantweets_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'edd_beantweets_license_status' );
	}
	return $new;
}




/*===================================================================*/
/* ACTIVATE LICENSE KEY
/*===================================================================*/
function edd_beantweets_activate_license() 
{
	//LISTEN FOR ACTIVATE BUTTON
	if( isset( $_POST['edd_beantweets_license_activate'] ) ) {

		//SECUIRTY CHECK
	 	if( ! check_admin_referer( 'edd_beantweets_nonce', 'edd_beantweets_nonce' ) ) 	
			return; //GET OUT IF WE DIDNT CLICK ACTIVATE

		//RETRIEVE LICENSE FROM DATABASE
		$license = trim( get_option( 'edd_beantweets_license_key' ) );
			
		//DATA TO SEND
		$api_params = array( 
			'edd_action'=> 'activate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( BEANTWEETS_EDD_TB_NAME )
		);
		
		//CALL CUSTOM API
		$response = wp_remote_get( add_query_arg( $api_params, BEANTWEETS_EDD_TB_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		//MAKE SURE RESPONSE IS GOOD
		if ( is_wp_error( $response ) )
			return false;

		//DECODE LICENSE DATA
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		update_option( 'edd_beantweets_license_status', $license_data->license );
	}
}
add_action('admin_init', 'edd_beantweets_activate_license');




/*===================================================================*/
/* DEACTIVATE LICENSE KEY
/*===================================================================*/
function edd_beantweets_deactivate_license() 
{
	//LISTEN FOR ACTIVATION CLICK
	if( isset( $_POST['edd_beantweets_license_deactivate'] ) ) 
	{
		//SECURITY CHECK
	 	if( ! check_admin_referer( 'edd_beantweets_nonce', 'edd_beantweets_nonce' ) ) 	
			return; // get out if we didn't click the Activate button

		//RETRIEVE LICENSE FROM DATABASE
		$license = trim( get_option( 'edd_beantweets_license_key' ) );

		//DATA TO SEND
		$api_params = array( 
			'edd_action'=> 'deactivate_license', 
			'license' 	=> $license, 
			'item_name' => urlencode( BEANTWEETS_EDD_TB_NAME ) // the name of our product in EDD
		);
		
		//CALL CUSTOM API
		$response = wp_remote_get( add_query_arg( $api_params, BEANTWEETS_EDD_TB_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

		//MAKE SURE RESPONSE IS GOOD
		if ( is_wp_error( $response ) )
			return false;

		//DECODE LICENSE DATA
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
		//DECODE LICENSE DATA
		if( $license_data->license == 'deactivated' )
			delete_option( 'edd_beantweets_license_status' );
	}
}
add_action('admin_init', 'edd_beantweets_deactivate_license');