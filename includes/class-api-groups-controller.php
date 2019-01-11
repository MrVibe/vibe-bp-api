<?php

defined( 'ABSPATH' ) or die();


//Scope => My , public, group,
// Contenxt => information: select dropdown, member card, groups directory, full profile view
if ( ! class_exists( 'Vibe_BP_API_Rest_Groups_Controller' ) ) {
	
	class Vibe_BP_API_Rest_Groups_Controller extends Vibe_BP_API_Rest_Controller {


		/**
		 * Register the routes for the objects of the controller.
		 *
		 * @since 3.0.0
		 */
		public function register_routes() {

			register_rest_route( $this->namespace, '/groups', array(
				array(
					'methods'             =>  WP_REST_Server::READABLE,
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'callback'            =>  array( $this, 'get_groups' ),
				),
			));
			register_rest_route( $this->namespace, '/group/(?P<id>\d+)?', array(
				array(
					'methods'             =>  WP_REST_Server::READABLE,
					'callback'            =>  array( $this, 'get_group' ),
					'permission_callback' => array( $this, 'get_groups_permissions' ),
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
	    function get_groups_permissions($request){
	    	
	    	if($request->get_param('client_id') == 'abc'){
	    		return true;
	    	}

	    	return false;
	    }

    	function get_groups($request){
					
			$defaults = array(
				'scope'=>'',
				'context'=>'',

				);
			$args =array();

    		$items_per_page = apply_filters('vibe_bp_groups_items_per_page',20,$args);

    		$active = apply_filters('vibe_bp_groups_type','active',$args);

			$query_defaults = array(
				'type'               => null,
				'orderby'            => 'date_created',
				'order'              => 'DESC',
				'per_page'           => $items_per_page ,
				'page'               => null,
				'user_id'            => 0,
				'slug'               => array(),
				'search_terms'       => false,
				'search_columns'     => array(),
				'group_type'         => '',
				'group_type__in'     => '',
				'group_type__not_in' => '',
				'meta_query'         => false,
				'include'            => false,
				'parent_id'          => null,
				'update_meta_cache'  => true,
				'update_admin_cache' => false,
				'exclude'            => false,
				'show_hidden'        => false,
				'status'             => array(),
				'fields'             => 'all',
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
			$query_args = apply_filters('vibe_bp_api_groups_query_args',$query_args,$request);

			foreach($defaults as $key=>$value){
				$args[$key]=$request->get_param($key);
			}
    		
    		//scope or context
				//Prepare args
    		//
    		$groups = Vibe_BP_API_Groups::init();
    		$groups_response = $groups->get_groups($query_args);

    		$groups_data = apply_filters( 'vibe_bp_api_get_groups', $groups_response, $request );

			return new WP_REST_Response( $groups_data, 200 );
    	}

    	function get_group(){

    	}
	}
}
