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



if ( ! defined( 'Vibe_BP_API_NAMESPACE ' ) )
	define( 'Vibe_BP_API_NAMESPACE', 'vbp/v1' );

if ( ! class_exists( 'Vibe_BP_API' ) ) {

	/**
	 * WPLMS Course API class.
	 *
	 * @since 3.0.0
	 */
	class Vibe_BP_API {

		/**
		 * Initialize the Course API.
		 * 
		 * @since 3.0.0
		 */
		public static function initialize() {

			
			self::includes();
			self::hooks();
		}

		/**
		 * Includes the required plugin files.
		 * 
		 * @since 3.0.0
		 * @access private
		 */	
		private static function includes() {

			//$tips = WPLMS_tips::init();
			//if(isset($tips) && isset($tips->lms_settings) && isset($tips->lms_settings['api']) && isset($tips->lms_settings['api']['api'])){
		
			require_once dirname( __FILE__ ) . '/class-api-controller.php';
			require_once dirname( __FILE__ ) . '/class.members.php';
			require_once dirname( __FILE__ ) . '/class.groups.php';

			require_once dirname( __FILE__ ) . '/class-api-members-controller.php';
			require_once dirname( __FILE__ ) . '/class-api-groups-controller.php';

			/**
			 * Fires when all BP COURSE API files are loaded.
			 *
			 * @since 3.0.0
			 */
			do_action( 'vibe_bp_api_loaded' );
		}

		/**
		 * Adds the required action hooks.
		 * 
		 * @since 3.0.0
		 * @access private
		 */
		private static function hooks() {
			add_action( 'rest_api_init', array( __CLASS__, 'create_rest_routes' ), 10 );
		}

		/**
		 * Creates the BP COURSE API endpoints.
		 * 
		 * @since 3.0.0
		 * @access private
		 */
		public static function create_rest_routes() {

			$types = array(
				'members',
				'groups',
			);

			/**
			 * Filter the list of resource types.
			 * 
			 * @since 3.0.0
			 */
			$types = apply_filters( 'vibe_bp_api_types', $types );
			
			if ( is_array( $types ) && count( $types ) > 0 ) {
				foreach( $types as $type ) {
					if(bp_is_active($type)){
						$type = ucfirst( $type );
						$class_name = "Vibe_BP_API_Rest_{$type}_Controller";
						if ( class_exists( $class_name ) ) {
							
								$controller = new $class_name( $type );

							$controller->register_routes();
							
							
						}
					}
				}
			}
			/**
			 * Fires after BP COURSE REST API routes are created.
			 *
			 * @since 3.0.0
			 */
			do_action( 'vibe_bp_api_init' );
		}

		/**
		 * Returns true if the WP API is active.
		 * 
		 * @since 3.0.0
		 * 
		 * @return bool
		 */
		public static function is_wp_api_active() {
			return class_exists( 'WP_REST_Controller' );
		}

		/**
		 * Displays an admin notice if the WP API is not available.
		 * 
		 * @since 3.0.0
		 */
		public static function missing_wp_api_notice() {
			/*if ( false != bp_course_get_setting( 'api', 'api', 'bool' ) && false == self::is_wp_api_active() ) {
				// REST API IS NOT ACTIVE
				add_action('admin_notices',function(){
					echo '<div class="error"><p>'.sprintf(__( 'REST API not active ! Please update WordPress %d or greater version.', 'vibe' ),'4.7').'</p></div>';
				});
			}*/
		}
		
	}

	add_action( 'init', array( 'Vibe_BP_API', 'initialize' ) );
	add_action( 'admin_notices', array( 'Vibe_BP_API', 'missing_wp_api_notice' ) );
}
