<?php

class ControllerModuleYotpo extends Controller {
	protected function index() {
		//Load the language file for this module - catalog/language/module/yotpo.php
$this->language->load('module/yotpo');
$this->data['yotpo_review'] = $this->language->get('yotpo_review');
		//Get the title from the language file
      	//$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['appkey'] = $this->config->get('yotpo_appkey');
		$this->data['domain'] = HTTP_SERVER;
		$this->data['language'] = "en" //$this->config->get('yotpo_language');
		$this->data['product_id'] = $this->request->get['product_id'];
		$product = $this->model_catalog_product->getProduct($this->data['product_id']);
		$this->data['product_name'] = $product['name'];
		$this->data['product_description'] = $product['description'];
		$this->data['product_url'] = HTTP_SERVER . 'index.php?route=product/product&product_id=' . $this->data['product_id'];
		$this->data['product_image_url'] = HTTPS_IMAGE . $product['image'];
		$this->data['product_models'] = $product['model'];
		$this->data['product_bread_crumbs'] = "crumbs";

		//Choose which template to display this module with
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/yotpo.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/yotpo.tpl';
		} else {
			$this->template = 'default/template/module/yotpo.tpl';
		}

		//Render the page with the chosen template
		$this->render();
	}
}
?>