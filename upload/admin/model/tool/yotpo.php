<?php
class ModelToolYotpo extends Model {
	const YOTPO_API_URL = 'https://api.yotpo.com';
	const HTTP_REQUEST_TIMEOUT = 30;
	const YOTPO_OAUTH_TOKEN_URL = 'https://api.yotpo.com/oauth/token';
	const PAST_ORDERS_DAYS_BACK = 90;
	const PAST_ORDERS_LIMIT = 10000;
	const BULK_SIZE = 1000;

	private function get_map_data($order) {
			$data = array();
		    $customer = NULL;
		    $data["order_date"] = $order['date_added'];
		    $data["email"] = $order['email'];
		    $data["customer_name"] = $order['firstname'] . ' ' . $order['lastname'];
		    $data["order_id"] = $order['order_id'];
		    $data['platform'] = 'opencart';
		    $data["currency_iso"] = $order['currency_code'];

		    $products_arr = array();

		    $this->load->model('sale/order');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
		    $products = $this->model_sale_order->getOrderProducts($order['order_id']);
		    foreach ($products as $order_product) {

		      $full_product = $this->model_catalog_product->getProduct($order_product['product_id']);
		      $product_data = array();
		      if ($full_product['image']) {
		      	$product_data['image'] = $this->model_tool_image->resize($full_product['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
		      } else {
		      	$product_data['image'] = '';
		      }
		      $product_data['url'] = HTTP_CATALOG . 'index.php?route=product/product&product_id=' . $order_product['product_id'];
		      $product_data['name'] = strip_tags(html_entity_decode($full_product['name']));
		      $product_data['description'] = strip_tags(html_entity_decode($full_product['description']));
		      $product_data['price'] = $order_product['price'];

		      $products_arr[$order_product['product_id']] = $product_data;
		    }
		    $data['products'] = $products_arr;
		    return $data;
	}

	private function getPastOrders()
	{	
		$this->load->model('localisation/order_status');
		$order_statuses = $this->model_localisation_order_status->getOrderStatuses();

		foreach ($order_statuses as $status) {
			if ($status['name'] == 'Shipped' ||
			$status['name'] == 'Complete') {

				$accepted_status[] = $status['order_status_id'];
			}
		}
	
		$query = $this->db->query('SELECT `order_id`, `firstname`, `lastname`, `email`, `date_added`, `currency_code`, `total` 
								   FROM `'. DB_PREFIX. 'order` 
								   WHERE `order_status_id` IN ('.join(',', $accepted_status).') AND 
								   `date_added` <  NOW() AND 
								   DATE_SUB(NOW(), INTERVAL '.self::PAST_ORDERS_DAYS_BACK.' day) < `date_added` 
								   LIMIT 0,'.self::PAST_ORDERS_LIMIT.'');
		$result = $query->rows;		
		if (is_array($result))
		{
			$orders = array();
			foreach ($result as $singleMap)
			{
				$res = $this->get_map_data($singleMap);
				if (!is_null($res))
					$orders[] = $res;
			}
			$post_bulk_orders = array_chunk($orders, self::BULK_SIZE);
			$data = array();
			foreach ($post_bulk_orders as $index => $bulk)
			{
				$data[$index] = array();
				$data[$index]['orders'] = $bulk;
				$data[$index]['platform'] = 'opencart';			
			}
			return $data;
		}
		return null;
	}

	public function makePastOrdersRequest($data, $app_key, $secret_token)
	{
		$token = $this->grantOauthAccess($app_key, $secret_token);
		if (!empty($token) && is_string($token))
		{
			$data['utoken'] = $token;
		    return $this->makePostRequest(self::YOTPO_API_URL.'/apps/'.$app_key.'/purchases/mass_create', $data);
		}
		return $token;
	}
	
	public function signUp($params)	{
		$is_mail_valid = $this->checkeMailAvailability($params['yotpo_email']);
		if ($is_mail_valid['status']['code'] == 200 && $is_mail_valid['response']['available'] == true) {
			$registerResponse = $this->register($params['yotpo_email'], $params['yotpo_user_name'], $params['yotpo_password'], HTTP_CATALOG);
	
			if ($registerResponse['status']['code'] == 200) {
				$app_key = $registerResponse['response']['app_key'];
				$secret = $registerResponse['response']['secret'];
				$accountPlatformResponse = $this->createAcountPlatform($app_key, $secret, HTTP_CATALOG);
				if ($accountPlatformResponse['status']['code'] == 200)
				{
					return array('appkey' => $app_key, 'secret' => $secret);
				}
				else
					return array('message' => $accountPlatformResponse['status']['message']);
				}
		}
		else {
			return $is_mail_valid['status']['code'] == 200 ? array('message' => $this->language->get('error_email_in_use')) : array('message' => $is_mail_valid['status']['message']);
		}
	}
	
	public function checkeMailAvailability($email) {
  		return $this->makePostRequest(self::YOTPO_API_URL . '/apps/check_availability', 
		array('model' => 'user', 'field' => 'email', 'value' => $email));
	}
	
	public function register($email, $name, $password, $url)
	{
		return $this->makePostRequest(self::YOTPO_API_URL . '/users.json', array('install_step' => 'done',
		'user' => array('email' => $email, 'display_name' => $name, 'password' => $password, 'url' => $url)));
	}
	
	public function createAcountPlatform($app_key, $secret_token, $shop_url)
	{
		$token = $this->grantOauthAccess($app_key, $secret_token);
		if (!empty($token))
			return $this->makePostRequest(self::YOTPO_API_URL . '/apps/' . $app_key .'/account_platform', array('utoken' => $token,
			'account_platform' => array('platform_type_id' => 10, 'shop_domain' => $shop_url)));
		return array('status' => array('message' => 'Could not create account correctly, authorization failed','code' => '401'));
	}
		
	public function makePostRequest($url, $data)
	{
		$ch = curl_init($url);
		$json_data = json_encode($data);                                                                                                                         
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,self::HTTP_REQUEST_TIMEOUT);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-length: '.strlen($json_data)));                                                                                                                   
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);		
		$result = curl_exec($ch);
		curl_close ($ch);	
		return json_decode($result, true);
	}	
	
	public function grantOauthAccess($app_key, $secret_token)
	{
		include_once(DIR_SYSTEM . 'library/oauth-php/library/YotpoOAuthStore.php');
		include_once(DIR_SYSTEM . 'library/oauth-php/library/YotpoOAuthRequester.php');
		$yotpo_options = array('consumer_key' => $app_key, 'consumer_secret' => $secret_token,
		'client_id' => $app_key, 'client_secret' => $secret_token, 'grant_type' => 'client_credentials');
    
		YotpoOAuthStore::instance('2Leg', $yotpo_options);
		try
		{
			$request = new YotpoOAuthRequester(self::YOTPO_OAUTH_TOKEN_URL, 'POST', $yotpo_options);         
			$result = $request->doRequest(0);
			$body = json_decode($result['body'],true);
			return empty($body['access_token']) ? null : $body['access_token'];
		}
		catch(YotpoOAuthException2 $e)
		{
			return array('status' => array('message' => 'Authorization failed','code' => '401'));
		}
	}

	public function make_single_map($order_id) {
		$app_key = $this->config->get('yotpo_appkey');
		$secret_token = $this->config->get('yotpo_secret');
		if(!empty($app_key) && !empty($secret_token)) {
			$this->load->model('sale/order');
			$order = $this->model_sale_order->getOrder($order_id);
			$this->load->model('localisation/order_status');
			$order_statuses = $this->model_localisation_order_status->getOrderStatuses();

			foreach ($order_statuses as $status) {
				if ($status['name'] == 'Shipped' ||
				$status['name'] == 'Complete') {

					$accepted_status[] = $status['order_status_id'];
				}
			}

			if (in_array($order['order_status_id'], $accepted_status)) {
				$token = $this->grantOauthAccess($app_key, $secret_token);
				if(!empty($token) && is_string($token)) {
					$data = $this->get_map_data($order);
					$data['utoken'] = $token;
					$this->makePostRequest(self::YOTPO_API_URL . '/apps/' . $app_key . "/purchases/", $data);
				}
			}
		}
	}
	
	public function past_orders() {
		$api_key = $this->config->get('yotpo_appkey');
		$secret_token = $this->config->get('yotpo_secret');
		if(is_string($secret_token) && is_string($api_key) && strlen($secret_token) > 0 && strlen($api_key) > 0) {
			$past_orders = $this->getPastOrders();
			$message = null;
			foreach ($past_orders as $post_bulk)
			if (!is_null($post_bulk))
			{
				$response = $this->makePastOrdersRequest($post_bulk, $api_key, $secret_token);
				if ($response['status']['code'] != 200 && is_null($message))
				{
					$message = 	$response['status']['message'];
				}
			}
			return is_null($message) ? null : array('message' => $message);
		}
		else {
			return array('message' => 'You need to set your app key and secret token to post past orders');
		}
			
	}	
}
?>