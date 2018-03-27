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
					'methods'             =>  WP_REST_Server::READABLE,
					'permission_callback' => array( $this, 'get_members_permissions' ),
					'callback'            =>  array( $this, 'get_members' ),
				),
			));
			register_rest_route( $this->namespace, '/member/(?P<user_id>\d+)?', array(
				array(
					'methods'             =>  WP_REST_Server::READABLE,
					'callback'            =>  array( $this, 'get_member' ),
					'permission_callback' => array( $this, 'get_members_permissions' ),
					'args'                     	=>  array(
						'user_id'                       	=>  array(
							'validate_callback'     =>  function( $param, $request, $key ) {
														return is_numeric( $param );
													}
						),
					),
				),
			));

		}


		/*
	    TRACKER PERMISSIONS
	     */
	    function get_members_permissions($request){
	    	
	    	if($request->get_param('client_id') == 'abc'){
	    		return true;
	    	}

	    	return false;
	    }

    	function get_members(){
					
			$defaults = array(
				'scope'=>'',
				'context'=>'',

				);
			$args = $defaults;
			foreach($defaults as $key=>$value){
				$args[$key]=$request->get_param($key);
			}
    		
    		//scope or context
				//Prepare args
    		//
    		$members = Vibe_BP_API_Members::init();
    		$members->get_members($args);
    	}

    	function get_member(){

    	}
	}
}
