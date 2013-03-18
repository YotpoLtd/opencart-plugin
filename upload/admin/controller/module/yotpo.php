<?php
class ControllerModuleYotpo extends Controller {
	const YOTPO_API_URL = 'https://api.yotpo.com';
	const HTTP_REQUEST_TIMEOUT = 30;
	const YOTPO_OAUTH_TOKEN_URL = 'https://api.yotpo.com/oauth/token';
	private $error = array(); 
	private static $language_assigns = array('heading_title','heading_settings_title','heading_signup_title','button_save','button_cancel',
										 'entry_appkey','entry_secret','entry_language','entry_review_tab_name','entry_widget_location',
										 'entry_user_name','entry_password','entry_confirm_password','entry_email','entry_bottom_line');
	private static $error_assigns = array('error_appkey','error_secret','error_user_name','error_email','error_password',
									 	  'error_confirm_password','error_warning');
	private static $config_assigns = array('yotpo_appkey','yotpo_secret','yotpo_language','yotpo_user_name','yotpo_email',
									 	   'yotpo_password','yotpo_confirm_password','yotpo_review_tab_name','yotpo_widget_location','yotpo_bottom_line_enabled');
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/yotpo');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));

		//Load the settings model.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$result = null;
			$success_text = $this->language->get('text_success');
			if($this->request->request['action'] == 'signup') {
				$this->load->model('tool/yotpo');
				$result = $this->model_tool_yotpo->signUp($this->request->post);
				if(isset($result['appkey']) && isset($result['secret'])) {
					$this->request->post['yotpo_appkey'] = $result['appkey'];
					$this->request->post['yotpo_secret'] = $result['secret'];
					$success_text = $this->language->get('text_signup_success');
				}
			}
			if(is_null($result) || !isset($result['message'])) {
				$this->session->data['success'] = $success_text;
				$this->model_setting_setting->editSetting('yotpo', $this->request->post);
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			else {
				$this->data['error_warning'] = $result['message'];
			}			
		}
		
		//Pull languages
		foreach (self::$language_assigns as $data_name) {
			$this->data[$data_name] = $this->language->get($data_name);
		}
		
		foreach (self::$error_assigns as $error_assign) {			
	 		if (isset($this->error[$error_assign])) {
				$this->data[$error_assign] = $this->error[$error_assign];
			} else {
				$this->data[$error_assign] = '';
			}
		}		

		//User entries
		foreach (self::$config_assigns as $config_assign) {
			if (isset($this->request->post[$config_assign])) {
				$this->data[$config_assign] = $this->request->post[$config_assign];
			} else {
				$this->data[$config_assign] = $this->config->get($config_assign);
			}
		}
		
		/* Default widget tab name: 'Reviews', Default widget tab location: footer Default widget language: english*/	
		if(is_null($this->data['yotpo_review_tab_name']) || $this->data['yotpo_review_tab_name'] == '') {
			$this->data['yotpo_review_tab_name'] = $this->language->get('yotpo_default_reviews_tab_name');
		}	
		
		if(is_null($this->data['yotpo_widget_location']) || $this->data['yotpo_widget_location'] == '') {
			$this->data['yotpo_widget_location'] = $this->language->get('yotpo_default_widget_location');
		}	

		if(is_null($this->data['yotpo_language']) || $this->data['yotpo_language'] == '') {
			$this->data['yotpo_language'] = $this->language->get('yotpo_default_widget_language');
		}	

		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS.
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/yotpo', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/yotpo', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['sign_up'] = $this->url->link('module/yotpo', 'token=' . $this->session->data['token'] . '&action=signup', 'SSL');
				
		//Choose which template file will be used to display this request.
		$this->template = 'module/yotpo.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		//Send the output.
		$this->response->setOutput($this->render());
	}
	
	/*
	 * 
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 * 
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/yotpo')) {
			$this->error['error_permission'] = $this->language->get('error_permission');
		}

		if(isset($this->request->request['action']) && $this->request->request['action'] == 'signup') {
			if (!$this->request->post['yotpo_user_name']) {
				$this->error['error_user_name'] = $this->language->get('error_user_name');
			}

			if (!$this->request->post['yotpo_email']) {
				$this->error['error_email'] = $this->language->get('error_email');
			}	

			if (!$this->request->post['yotpo_password'] || strlen($this->request->post['yotpo_password']) < 6 || strlen($this->request->post['yotpo_password']) > 128) {
				$this->error['error_password'] = $this->language->get('error_password');
			}	

			if ($this->request->post['yotpo_confirm_password'] != $this->request->post['yotpo_password']) {
				$this->error['error_confirm_password'] = $this->language->get('error_confirm_password');
			}
		}
		else {
			if (!$this->request->post['yotpo_appkey']) {
				$this->error['error_appkey'] = $this->language->get('error_appkey');
			}
	
			if (!$this->request->post['yotpo_secret']) {
				$this->error['error_secret'] = $this->language->get('error_secret');
			}
		}
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}		
}
?>