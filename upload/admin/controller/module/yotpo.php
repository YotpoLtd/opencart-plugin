<?php
class ControllerModuleYotpo extends Controller {
	private $error = array(); 
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/yotpo');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));

		//Load the settings model.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('yotpo', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		//Pull languages
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['entry_appkey'] = $this->language->get('entry_appkey');		
		$this->data['entry_secret'] = $this->language->get('entry_secret');
		$this->data['entry_default'] = $this->language->get('entry_default');

		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		//User entries
 		if (isset($this->error['appkey'])) {
			$this->data['error_appkey'] = $this->error['appkey'];
		} else {
			$this->data['error_appkey'] = '';
		}

		if (isset($this->error['secret'])) {
			$this->data['error_secret'] = $this->error['secret'];
		} else {
			$this->data['error_secret'] = '';
		}

		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
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

		// Config values
		if (isset($this->request->post['yotpo_appkey'])) {
			$this->data['yotpo_appkey'] = $this->request->post['yotpo_appkey'];
		} else {
			$this->data['yotpo_appkey'] = $this->config->get('yotpo_appkey');
		}	

		if (isset($this->request->post['yotpo_secret'])) {
			$this->data['yotpo_secret'] = $this->request->post['yotpo_secret'];
		} else {
			$this->data['yotpo_secret'] = $this->config->get('yotpo_secret');
		}	
		
		if (isset($this->request->post['yotpo_default'])) {
			$this->data['yotpo_default'] = $this->request->post['yotpo_default'];
		} else {
			$this->data['yotpo_default'] = $this->config->get('yotpo_default');
		}	

		$this->data['modules'] = array();
		
		if (isset($this->request->post['google_talk_module'])) {
			$this->data['modules'] = $this->request->post['google_talk_module'];
		} elseif ($this->config->get('google_talk_module')) { 
			$this->data['modules'] = $this->config->get('google_talk_module');
		}			

		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();

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
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['yotpo_appkey']) {
			$this->error['appkey'] = $this->language->get('error_appkey');
		}

		if (!$this->request->post['yotpo_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}


}
?>