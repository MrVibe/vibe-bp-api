<?php
/**
 * Action functions for Course Module
 *
 * @author      VibeThemes
 * @category    Admin
 * @package     Vibe Course Module
 * @version     2.0
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

class Vibe_BP_API_Members{

    public static $instance;
    public static function init(){
    if ( is_null( self::$instance ) )
        self::$instance = new Vibe_BP_API_Members();

        return self::$instance;
    }

    function get_members($args){

    	$defaults = array(
    		'type'                => 'active',
			'page'                => 1,
			'per_page'            => 20,
			'max'                 => false,

			'page_arg'            => 'upage',  // See https://buddypress.trac.wordpress.org/ticket/3679.

			'include'             => false,    // Pass a user_id or a list (comma-separated or array) of user_ids to only show these users.
			'exclude'             => false,    // Pass a user_id or a list (comma-separated or array) of user_ids to exclude these users.

			'user_id'             => $user_id, // Pass a user_id to only show friends of this user.
			'member_type'         => $member_type,
			'member_type__in'     => '',
			'member_type__not_in' => '',
			'search_terms'        => $search_terms_default,

			'meta_key'            => false,    // Only return users with this usermeta.
			'meta_value'          => false,    // Only return users where the usermeta value matches. Requires meta_key.

			'populate_extras'     => true 
    		);
    }


}

Vibe_BP_API_Members::init();