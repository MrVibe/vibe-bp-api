<?php

defined( 'ABSPATH' ) or die();


//Scope => My , public, group,
// Contenxt => information: select dropdown, member card, members directory, full profile view
if ( ! class_exists( 'VIBE_BP_API_Rest_Members_Controller' ) ) {
	
	class Vibe_BP_API_Rest_Members_Controller extends Vibe_BP_API_Rest_Controller {


		/**
		 * Register the routes for the objects of the controller.
		 *
		 * @since 3.0.0
		 */
		public function register_routes() {

			register_rest_route( $this->namespace, '/members', array(
				array(
					'methods'             =>  'POST',
					'permission_callback' => array( $this, 'get_members_permissions' ),
					'callback'            =>  array( $this, 'get_members' ),
				),
			));
			register_rest_route( $this->namespace, '/member/(?P<id>\d+)?', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'get_member' ),
					'permission_callback' => array( $this, 'get_members_permissions' ),
					'args'                     	=>  array(
						'id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

		}


		/*
	    PERMISSIONS
	     */
	    function get_members_permissions($request){
	    	return true;
	    	
	    	if($request->get_param('client_id') == 'abc'){
	    		return true;
	    	}

	    	return false;
	    }

    	function get_members($request){
					
			$defaults = array(
				'scope'=>'',
				'context'=>'',

				);
			$args =array();

    		$items_per_page = apply_filters('vibe_bp_members_items_per_page',20,$args);

    		$active = apply_filters('vibe_bp_members_type','active',$args);
			$query_defaults = array(
	    		'type'                => '',
				'page'                => 1,
				'per_page'            => $items_per_page,
				'max'                 => false,

				'page_arg'            => 'upage',  // See https://buddypress.trac.wordpress.org/ticket/3679.

				'include'             => false,    // Pass a user_id or a list (comma-separated or array) of user_ids to only show these users.
				'exclude'             => false,    // Pass a user_id or a list (comma-separated or array) of user_ids to exclude these users.

				'user_id'             => '', // Pass a user_id to only show friends of this user.
				'member_type'         => '',
				'member_type__in'     => '',
				'member_type__not_in' => '',
				'search_terms'        => '',

				'meta_key'            => false,    // Only return users with this usermeta.
				'meta_value'          => false,    // Only return users where the usermeta value matches. Requires meta_key.

				'populate_extras'     => true 
	    	);

			$filters = urldecode($request['filter']);
			$filters = json_decode($filters);
			$filters = (Array)$filters;

			
    		//make $args based on $filters 
    		$query_args =array();



    		if(empty($query_args)){
    			$query_args = $query_defaults;
    		}

			wp_parse_args($query_args,$query_defaults);
			$query_args = apply_filters('vibe_bp_api_members_query_args',$query_args,$request);

			foreach($defaults as $key=>$value){
				$args[$key]=$request->get_param($key);
			}
    		
    		//scope or context
				//Prepare args
    		//
    		$members = Vibe_BP_API_Members::init();
    		$members_response = $members->get_members($query_args);

    		$members_data = apply_filters( 'vibe_bp_api_get_members', $members_response, $request );

			return new WP_REST_Response( $members_data, 200 );
    	}


    	function get_member($request){

    		$id = (int)$request->get_param('id');	 // get param data 'id'
    		
    		$member_details=array();
    		$filter=array();     					 // filteration purpose


    		$member_details= get_userdata($id);


    		$data=apply_filters( 'vibe_bp_api_get_member', $member_details, $filter );
			return new WP_REST_Response( $data, 200 );
    		
    	}
	}
}
