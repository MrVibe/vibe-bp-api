<?php


 if ( ! defined( 'ABSPATH' ) ) exit;


 class Vibe_BP_Api_Admin{

 	public $option = 'vibe_api_api';
	public static $instance;
    public static function init(){
        if ( is_null( self::$instance ) )
            self::$instance = new Vibe_BP_Api_Admin();
        return self::$instance;
    }

    public function __construct(){

    	add_action( 'admin_menu', array($this,'menu_page' ));
	}


	

	function menu_page(){

	    add_menu_page( __('Learning Management System','vibe-bp-api'), 'Vibe BP Api', 'manage_options', 'vibe_bp_api', array($this,'vibe_bp_api_admin'),'dashicons-welcome-learn-more',7 );
	    add_submenu_page( 'vibe_bp_api', __('Auth Server','vibe-bp-api'), __('Statistics','vibe-bp-api'),  'manage_options', 'auth-server', array($this,'auth_server') );
	    

	}

	function vibe_bp_api_admin(){

		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
		$tabs = apply_filters('vibebp_settings_tabs',array( 
	    		'general' => __('General','vibe-customtypes'), 
	    		'auth-server' => __('Auth Server','vibe-customtypes'), 
	    		));
	    echo '<div id="icon-themes" class="icon32"><br></div>';
	    echo '<h2 class="nav-tab-wrapper">';
	    foreach( $tabs as $tab => $name ){
	        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
	        echo "<a class='nav-tab$class' href='?page=vibe_bp_api&tab=$tab'>$name</a>";

	    }
	    echo '</h2>';


	    if(isset($_POST['save'])){
			echo $this->save_settings($tab);
		}		
		switch($tab){
			case 'auth-server':
				$this->auth_server();
			break; 
			default:
				$this->admin_page();
			break;
		}
	}

	function admin_page(){
		echo 'Insme kuch dalna hai';
	}


	function auth_server(){

		echo '<h3>'.__('Auth Server Settings','vibe-customtypes').'</h3>';
		echo '<p>'.__('Auth Server settings and configuration','vibe-customtypes').'</p>';
		$template_array = apply_filters('wplms_lms_api_tabs',array(
			'general'=> __('General Settings','vibe-customtypes'),
			'apps'=> __('Keys/Apps','vibe-customtypes'),
			'clients'=> __('Connected Clients','vibe-customtypes'),
			'updates' => __('Notifications/Updates','vibe-customtypes')
			));
		echo '<ul class="subsubsub">';
		foreach($template_array as $k=>$value){
			if(empty($_GET['sub']) && empty($current)){
				$current = $k;
			}else if(!empty($_GET['sub']) && empty($current)){
				$current = $_GET['sub'];
			}
			echo '<li><a href="?page=vibe_bp_api&tab=auth-server&sub='.$k.'" '.(($k == $current)?'class="current"':'').'>'.$value.'</a>  &#124; </li>';
		}
		echo '</ul><hr class="clear"/>';
		if(!isset($_GET['sub'])){$_GET['sub']='';}
		switch($_GET['sub']){
			case 'apps':
				$this->apps();
			break;
			case 'updates':
				$this->send_updates();
			break;
			case 'clients':
				$this->connected_clients();
			break;
			default:
				$html = '';
				ob_start();
				do_action('lms_api_settings_sub',$_GET);
				$html = ob_get_clean();
				if(empty($html)){

				
					$settings= apply_filters('lms_api_settings',array(
					array(
						'label'=>__('WPLMS API Settings','vibe-customtypes' ),
						'type'=> 'heading',
					),
					array(
							'label' => __('Enable API','vibe-customtypes'),
							'name' => 'api',
							'type' => 'checkbox',
							'desc' => __('WPLMS API, enables Mobile apps, WebApps and unlimited possibilities in WPLMS.','vibe-customtypes')
						),
					array(
							'label' => __('API Version','vibe-customtypes'),
							'name' => 'api_version',
							'type' => 'number',
							'desc' => __('Version controls cached data in Apps.','vibe-customtypes'),
							'default'=>1,
						),
					array(
							'label' => __('API Security State','vibe-customtypes'),
							'name' => 'api_security_state',
							'type' => 'text',
							'desc' => __('API security, used in authentications, the bigger the better.','vibe-customtypes'),
							'default'=> wp_generate_password(8),
						),
					array(
							'label' => __('Enable oAuth2 Server','vibe-customtypes'),
							'name' => 'oauth',
							'type' => 'checkbox',
							'desc' => __('WPLMS oAuth2 server for user registration and logins from Apps. Only required if your app supports login and registration.','vibe-customtypes')
						),
					array(
							'label' => __('Enable Registrations via API','vibe-customtypes'),
							'name' => 'api_registrations',
							'type' => 'checkbox',
							'desc' => __('Registrations via Mobile apps and WebApps.','vibe-customtypes')
						),
					array(
							'label' => __('Enable Quiz Lock on APP and Site','vibe-customtypes'),
							'name' => 'quiz_lock',
							'type' => 'checkbox',
							'desc' => __('Avoid cheating in quizzes by enabling this option.','vibe-customtypes')
						),
					array(
							'label' => __('App Version','vibe-customtypes'),
							'name' => 'app_version',
							'type' => 'select',
							'options'=>array(
										1 => 1,
										2 => 2,
										3 =>3,
									),
							'desc' => __('WPLMS App version, required for App sold on codecanyon.','vibe-customtypes')
						),
					array(
							'label' => __('Enable Wallet','vibe-customtypes'),
							'name' => 'wallet',
							'type' => 'checkbox',
							'desc' => __('Enable Wallet in App.','vibe-customtypes')
						),
					));
					$this->generate_form('api',$settings);
				}else{
					echo $html;
				}
				break;
			break;
		}

	}

	function apps(){
		echo '<h3>'.__('WPLMS Apps','vibe-customtypes').'</h3>';
		echo '<p>'.__('Connected clients with WPLMS oAuth server','vibe-customtypes').'</p>';
		include_once('auth_server/class-apps.php');
		$app = VibeBP_oAuth_Apps::init();
		$app->display();
	}

	function updates(){
		echo '<h3>'.__('Updates','vibe-customtypes').'</h3>';
		echo '<p>'.__('Send updates to all of your app users','vibe-customtypes').'</p>';
		include_once('auth_server/class-apps.php');
		$app = VibeBP_oAuth_Apps::init();
		$app->send_updates();
	}

	function connected_clients(){
		echo '<h3>'.__('Clients','vibe-customtypes').'</h3>';
		echo '<p>'.__('Clients with WPLMS oAuth server','vibe-customtypes').'</p>';
		include_once('auth_server/class-apps.php');
		$app = VibeBP_oAuth_Apps::init();
		$app->clients();
	}


	function save_settings($tab){
		if ( !empty($_POST) && check_admin_referer('vibe_bp_api_settings','_wpnonce') ){
			$settings=array();

			$settings = get_option($this->option);	

			unset($_POST['_wpnonce']);
			unset($_POST['_wp_http_referer']);
			unset($_POST['save']);
			switch($tab){
				case 'instructor':
					$settings['instructor'] = $_POST;
				break;
				case 'auth_server':
					$lms_settings['auth_server'] = $_POST;
					$this->update_api_settings($_POST);
				break;
			}
		}
	}

	function update_api_settings($_post){
		$settings = get_option('lms_settings');
		
		if(!empty($settings['api']['api_version']) && !empty($_post['api_version']) && $settings['api']['api_version'] != $_post['api_version']){
			global $wpdb;
			delete_option('vibebp_api_tracker');
			$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'vibebp_api_tracker' ) );
		}
	}

	function generate_form($tab,$settings=array()){

		if(empty($settings))
			return;
	
		echo '<form method="post">';
		wp_nonce_field('vibe_bp_api_settings','_wpnonce');
		echo '<table class="form-table">
				<tbody>';

		$lms_settings=get_option($this->option);

		foreach($settings as $setting ){
			echo '<tr valign="top" '.(empty($setting['class'])?'':'class="'.$setting['class'].'"').'>';
			switch($setting['type']){
				case 'heading':
					echo '<th scope="row" class="titledesc" colspan="2"><h3>'.$setting['label'].'</h3></th>';
				break;
				case 'link':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><a href="'.$setting['value'].'" class="button">'.$setting['button_label'].'</a>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'select':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><select name="'.$setting['name'].'">';
					foreach($setting['options'] as $key=>$option){
						echo '<option value="'.$key.'" '.(isset($lms_settings[$tab][$setting['name']])?selected($key,$lms_settings[$tab][$setting['name']]):'').'>'.$option.'</option>';
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'checkbox':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="checkbox" name="'.$setting['name'].'" '.(isset($lms_settings[$tab][$setting['name']])?'CHECKED':'').' />';
					echo '<span>'.$setting['desc'].'</span>';
				break;
				case 'number':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="number" name="'.$setting['name'].'" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:(isset($setting['default'])?$setting['default']:'')).'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'cptselect':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp">';
					echo '<select name="'.$setting['name'].'"><option value="">'.__('Select','vibe-customtypes').' '.$setting['cpt'].'</option>';
					global $wpdb;
					$cpts = '';
					if($setting['cpt']){
						$cpts = $wpdb->get_results("
							SELECT ID,post_title 
							FROM {$wpdb->posts} 
							WHERE post_type = '".$setting['cpt']."' 
							AND post_status='publish' 
							ORDER BY post_title DESC LIMIT 0,999");	
					}
					if(is_array($cpts)){
						foreach($cpts as $cpt){
							echo '<option value="'.$cpt->ID.'" '.((isset($lms_settings[$tab][$setting['name']]) && $lms_settings[$tab][$setting['name']] == $cpt->ID)?'selected="selected"':'').'>'.$cpt->post_title.'</option>';
						}
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'clpstepselect2':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp">';
					echo '<select id="'.$setting['name'].'" name="'.$setting['name'].'[]" class="clpstepselect2" multiple>';
					if( function_exists('wplms_clp_get_step_types') ){
						$all_steps = wplms_clp_get_step_types();
						if( !empty($all_steps) ){
							if( isset($lms_settings[$tab][$setting['name']]) ){
								$selected = $lms_settings[$tab][$setting['name']];
							}
							foreach ($all_steps as $step) {
								echo '<option '.((is_array($selected) && in_array($step['type'], $selected))?'selected="selected"':'').' value="'.$step['type'].'">'.$step['label'].'</option>';
							}
						}
					}
					echo '</select>';
					echo '<span>'.$setting['desc'].'</span><script>jQuery("#'.$setting['name'].'").select2();</script></td>';
				break;
				case 'title':
					echo '<th scope="row" class="titledesc"><h3>'.$setting['label'].'</h3></th>';
					echo '<td class="forminp"><hr /></td>';
				break;
				case 'color':
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp forminp-color"><input type="text" name="'.$setting['name'].'" class="colorpicker" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:'').'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
				case 'hidden':
					echo '<td><input type="hidden" name="'.$setting['name'].'" value="1"/></td>';
				break;
				case 'touchpoint': 
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><strong>'.__('STUDENT','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('messages'))?'':'disabled').' name="'.$setting['name'].'[student][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('notifications'))?'':'disabled').' name="'.$setting['name'].'[student][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[student][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['student']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['student']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['student']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['student']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';

					/**/
					do_action('wplms_student_touchpoint_setting_html',$lms_settings[$tab][$setting['name']]['student'],$lms_settings[$tab][$setting['name']],$setting['name']);


					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['student'].'" class="button">','</a>');
					echo '</td></tr><tr valign="top">';
					echo '<th scope="row"></th>';
					echo '<td class="forminp"><strong>'.__('INSTRUCTOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('messages'))?'':'disabled').' name="'.$setting['name'].'[instructor][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('notifications'))?'':'disabled').' name="'.$setting['name'].'[instructor][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';

					do_action('wplms_instructor_touchpoint_setting_html',$lms_settings[$tab][$setting['name']]['instructor'],$lms_settings[$tab][$setting['name']],$setting['name']);

					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['instructor'].'" class="button">','</a>');
					echo '</td>
						<tr><td colspan="3"><hr></td>';
				break;
				case 'touchpoint_admin': 
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><strong>'.__('INSTRUCTOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('messages'))?'':'disabled').' name="'.$setting['name'].'[instructor][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('notifications'))?'':'disabled').' name="'.$setting['name'].'[instructor][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[instructor][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['instructor']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['instructor']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['instructor'].'" class="button">','</a>');
					echo '</td></tr><tr valign="top">';
					echo '<th scope="row"></th>';
					echo '<td class="forminp"><strong>'.__('ADMINISTRATOR','vibe-customtypes').'</strong></td>';
					echo '<td class="forminp">';
					echo __('Message','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('messages'))?'':'disabled').' name="'.$setting['name'].'[admin][message]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['message'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['message']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['message'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['message']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Notification','vibe-customtypes').'&nbsp; <select '.((function_exists('bp_is_active') && bp_is_active('notifications'))?'':'disabled').' name="'.$setting['name'].'[admin][notification]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['notification'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['notification']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['notification'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['notification']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.__('Email','vibe-customtypes').'&nbsp; <select name="'.$setting['name'].'[admin][email]">';
					echo '<option value="0" '.(isset($lms_settings[$tab][$setting['name']]['admin']['email'])?selected(0,$lms_settings[$tab][$setting['name']]['admin']['email']):'').'>'.__('No','vibe-customtypes').'</option>';
					echo '<option value="1" '.(isset($lms_settings[$tab][$setting['name']]['admin']['email'])?selected(1,$lms_settings[$tab][$setting['name']]['admin']['email']):'').'>'.__('Yes','vibe-customtypes').'</option>';
					echo '</select>';
					echo '&nbsp;&nbsp;'.sprintf(__('%s Edit Email Template %s','vibe-customtypes'),'<a href="'.$setting['value']['admin'].'" class="button">','</a>');
					echo '</td>
						<tr><td colspan="3"><hr></td>';
				break;
				default:
					echo '<th scope="row" class="titledesc"><label>'.$setting['label'].'</label></th>';
					echo '<td class="forminp"><input type="text" name="'.$setting['name'].'" value="'.(isset($lms_settings[$tab][$setting['name']])?$lms_settings[$tab][$setting['name']]:(isset($setting['default'])?$setting['default']:'')).'" />';
					echo '<span>'.$setting['desc'].'</span></td>';
				break;
			}
			
			echo '</tr>';
		}
		echo '</tbody>
		</table>';
		if(!empty($settings))
			echo '<input type="submit" name="save" value="'.__('Save Settings','vibe-customtypes').'" class="button button-primary" /></form>';
	}





}

Vibe_BP_Api_Admin::init();