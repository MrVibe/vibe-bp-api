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
					'methods'             => ' WP_REST_Server::READABLE',
					'permission_callback' => array( $this, 'get_groups_permissions' ),
					'callback'            =>  array( $this, 'get_groups' ),
				),
			));
			register_rest_route( $this->namespace, '/group/(?P<group_id>\d+)?', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'get_group_by_id' ),
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

			register_rest_route( $this->namespace, '/create_update/', array(
				array(
					'methods'             =>  'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_groups_create_group'),
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
	    	return true;
	    	
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

    	function get_group_by_id($request){
    		$group_id = (int)$request->get_param('group_id');	

    		return groups_get_group( $group_id );

    	}
    	/**
 * Create a group.
 *
 * @since 1.0.0
 *
 * @param array|string $args {
 *     An array of arguments.
 *     @type int|bool $group_id     Pass a group ID to update an existing item, or
 *                                  0 / false to create a new group. Default: 0.
 *     @type int      $creator_id   The user ID that creates the group.
 *     @type string   $name         The group name.
 *     @type string   $description  Optional. The group's description.
 *     @type string   $slug         The group slug.
 *     @type string   $status       The group's status. Accepts 'public', 'private' or
 *                                  'hidden'. Defaults to 'public'.
 *     @type int      $parent_id    The ID of the parent group. Default: 0.
 *     @type int      $enable_forum Optional. Whether the group has a forum enabled.
 *                                  If a bbPress forum is enabled for the group,
 *                                  set this to 1. Default: 0.
 *     @type string   $date_created The GMT time, in Y-m-d h:i:s format, when the group
 *                                  was created. Defaults to the current time.
 * }
 * @return int|bool The ID of the group on success. False on error.
 */

    	function vibe_bp_api_groups_create_group($request){
		   
		    $args =array(
				'group_id'     => $_POST['group_id'],
				'creator_id'   => $_POST['creator_id'],
				'name'         => $_POST['name'],
				'description'  => $_POST['description'],
				'slug'         => $_POST['slug'],
				'status'       => $_POST['status'],
				'parent_id'    => $_POST['parent_id'],
				'enable_forum' => $_POST['enable_forum'],
				'date_created' => $_POST['groudate_createdp_id']
			);
			// return $args;
    		$filter=array();
    		$data=array();
    		return  groups_create_group($args);
   //  		$group_create_response= groups_create_group($args);
   //  		// return  $group_create_response;

   //  		$data=apply_filters( 'vibe_bp_api_groups_create_group', $group_create_response, $filter );
			// return new WP_REST_Response( $data, 200 );   

    	}
	}
}
