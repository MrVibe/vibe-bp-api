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
			register_rest_route( $this->namespace, '/friends/(?P<id>\d+)?/(?P<per_page>\d+)?/(?P<page_no>\d+)?', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_get_friends' ),
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

			register_rest_route( $this->namespace, '/addfriendship/(?P<initiator_userid>\d+)?/(?P<friend_userid>\d+)?', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_friends_add_friend' ),
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

			register_rest_route( $this->namespace, '/removefriendship/(?P<initiator_userid>\d+)?/(?P<friend_userid>\d+)?', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_friends_remove_friend' ),
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

			register_rest_route( $this->namespace, '/acceptfriendship/(?P<friendship_id>\d+)?/', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_friends_accept_friendship' ),
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
			register_rest_route( $this->namespace, '/rejectfriendship/(?P<friendship_id>\d+)?/', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_bp_api_friends_reject_friendship' ),
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


			register_rest_route( $this->namespace, '/check/', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'checkfuction' ),
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
			register_rest_route( $this->namespace, '/requset/(?P<user_id>\d+)?/', array(
				array(
					'methods'             => 'POST',
					'callback'            =>  array( $this, 'vibe_friends_get_friendId_request_ids_for_user' ),
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
    		return $this->vibe_bp_api_get_member_by_id($id);
    		
    		
    	}

    	function vibe_bp_api_get_member_by_id($id){

    		$member_details=array();
    		$filter=array();     					 // filteration purpose

    		$member_details= $this->vibe_bp_api_get_member_by_id_response($id);

    		
			$data=apply_filters( 'vibe_bp_api_get_member_by_id', $member_details, $filter );
			return new WP_REST_Response( $data, 200 );	
    		
    		
    	}
    	function vibe_bp_api_get_member_by_id_response($id){
	    	return get_userdata($id);
    	}


    	function vibe_bp_api_get_friends($request){

    		$id = (int)$request->get_param('id');	 // get param data 'id'
    		$per_page= (int)$request->get_param('per_page');	 // get param data 'per_page'
    		$page_no= (int)$request->get_param('page_no');	 // get param data 'page_no'

    		$friends_details=array();
    		$filter=array();

    		$friends_details=friends_get_alphabetically($id,$per_page,$page_no,'');

    		$data=apply_filters( 'vibe_bp_api_get_friends', $friends_details, $filter );
			return new WP_REST_Response( $data, 200 );
    	}

    	// for sending frienship request get true if send else false
    	function vibe_bp_api_friends_add_friend($request){

    		$initiator_userid = (int)$request->get_param('initiator_userid');	 // get param data 'initiator_userid'
    		$friend_userid= (int)$request->get_param('friend_userid');	 // get param data 'friend_userid'
    		
    		$friends_add_friend=friends_add_friend($initiator_userid,$friend_userid,false);    //return bool 

    		$data=apply_filters( 'vibe_bp_api_friends_add_friend', $friends_add_friend, $filter );
			return new WP_REST_Response( $data, 200 );    	

    	}

    	function vibe_bp_api_friends_remove_friend($request){


    		$initiator_userid = (int)$request->get_param('initiator_userid');	 // get param data 'initiator_userid'
    		$friend_userid= (int)$request->get_param('friend_userid');	 // get param data 'friend_userid'

    		$friends_remove_friend=friends_remove_friend($initiator_userid,$friend_userid);


    		$data=apply_filters( 'vibe_bp_api_friends_remove_friend', $friends_remove_friend, $filter );
			return new WP_REST_Response( $data, 200 );    	




    	}

    	function vibe_get_friendship_ids_for_user($id){   		
    		return BP_Friends_Friendship::get_friendship_ids_for_user($id);
    	}




// this function not send notifiCATION . IT USES OWN QUERIES FOR DATABASE

/**

friends_accept_friendship() ->  BP_Friends_Friendship::accept()     bp_loggedin_user_id

*/    	

    	function vibe_bp_api_friends_accept_friendship($request){
    		
    		$friendship_id = (int)$request->get_param('friendship_id');	 // get param data 'friendship_id'
    	    $filter=array();

    		global $wpdb;
			$bp = buddypress();
			$acceptfriendship= $wpdb->query( $wpdb->prepare( "UPDATE {$bp->friends->table_name} SET is_confirmed = 1, date_created = %s WHERE id = %d" , bp_core_current_time(), $friendship_id ) );



    		$data=apply_filters( 'vibe_bp_api_get_friends', $acceptfriendship, $filter );
			return new WP_REST_Response( $data, 200 );    	// return 1 or 0 

    	}

    	function vibe_bp_api_friends_reject_friendship($request){

    		$friendship_id = (int)$request->get_param('friendship_id');	 // get param data 'friendship_id'
    		$filter=array();

    		global $wpdb;
			$bp = buddypress();

			$rejectfriendship= $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->friends->table_name} WHERE id = %d", $friendship_id) );

			$data=apply_filters( 'vibe_bp_api_get_friends', $rejectfriendship, $filter );
			return new WP_REST_Response( $data, 200 );    	// return 1 or 0 
    	}

    	// fetch friendship id for user
    	function vibe_friends_get_friendship_ids_for_user($user_id,$is_confirmed){
    		

	    	global $wpdb;

			$bp = buddypress();

			$friendship_ids = $wpdb->get_results( $wpdb->prepare( "SELECT id, initiator_user_id  FROM {$bp->friends->table_name} WHERE (initiator_user_id = %d OR friend_user_id = %d) AND (is_confirmed=%d)  ORDER BY date_created DESC", $user_id, $user_id ,$is_confirmed ) );

			return $friendship_ids;
    	
    	}


		// friend id and friendship id who  is request to this user;
    	function vibe_friends_get_friendId_request_ids_for_user($request){
    	
    		$id = (int)$request->get_param('user_id');	 // get param data 'user_id'
    	    $filter=array();
    	    $data=array();
    	    $user_details=array();

    	    $initiator_friendship_ids=$this-> vibe_friends_get_friendship_ids_for_user($id,0);
    	    
    	   
    		foreach ($initiator_friendship_ids as $initiator_friendship_id) {

    			 $user_details[]=array(
    			 	'friendship_id'=>(int)$initiator_friendship_id->id,
    			 	'user_detail'=>$this->vibe_bp_api_get_member_by_id_response((int)$initiator_friendship_id->initiator_user_id),
    			 );

    		}

    		$data=apply_filters( 'vibe_bp_api_friends_remove_friend', $user_details, $filter );
			return new WP_REST_Response( $data, 200 );   

    	}
    			



    	function checkfuction($user_id){

		    $args =array(
				'group_id'     => 0,
				'creator_id'   => 1,
				'name'         => 'vibe_group_api',
				'description'  => 'vibe_group_api',
				'slug'         => 'vibe_group_api',
				'status'       => null,
				'parent_id'    => null,
				'enable_forum' => 1,
				'date_created' => null
			);

    		return groups_create_group($args);
    		
    		// // return groups_get_group(1);
    		// return groups_total_groups_for_user(1);
    		// return groups_get_user_groups(1,0,2);
    		// return groups_get_total_group_count();
    		// return groups_get_total_member_count(2);
    	}

    	


	}
}
