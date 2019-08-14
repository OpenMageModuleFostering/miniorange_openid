<?php
class MiniOrange_Openid_Block_MoOpenidConfig extends Mage_Core_Block_Template{
	
	private $hostname = "https://auth.miniorange.com";
	
	public function isEnabled(){
		$customer = Mage::helper('MiniOrange_Openid');
		$admin = Mage::getSingleton('admin/session')->getUser();
		$id = $admin->getUserId();
		if($customer->getConfig('isEnabled',$id)==1){
			return 'checked';
		}
		else{
			return '';
		}
	}
	
	public function getadminurl($value){
		return Mage::helper("adminhtml")->getUrl($value);
	}
	
	public function miniorange_geturl($value){
		return Mage::getUrl($value,array('_secure'=>true));
	}
	
	public function getcurrentUrl(){
		return Mage::getBaseUrl();
	}
	
	public function getHostURl(){
		return  Mage::helper('MiniOrange_Openid')->getHostURl();
	}
	
	public function getCurrentUser(){
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			return $customer->getEmail();
		}
		return;
	}
	
	public function showEmail(){
		$admin = Mage::getSingleton('admin/session')->getUser();
		$customer = Mage::helper('MiniOrange_Openid');
		$id = $admin->getUserId();
		return $customer->showEmail($id);
	}
	
	public function saveConfig($url,$value){
		$admin = Mage::getSingleton('admin/session')->getUser();
		$id = $admin->getUserId();
		$data = array($url=>$value);
		$model = Mage::getModel('admin/user')->load($id)->addData($data);
		try {
				$model->setId($id)->save(); 
			} catch (Exception $e){
				Mage::log($e->getMessage(), null, 'miniorage_error.log', true);
		}
	}
	
	public function getImage($image){
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		return $url.'adminhtml/default/default/MiniOrange_Openid/images/'.$image.'.png';
	}
	
	public function getIconImage($image){
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
		return $url.'adminhtml/default/default/MiniOrange_Openid/images/icons/'.$image.'.png';
	}
	
	
	public function isCustomerEnabled(){
		$customer = Mage::helper('MiniOrange_Openid');
		if($customer->getConfig('isCustomerEnabled','')==1){
			return 'checked';
		}
		else{
			return '';
		}
	}
	
	public function getConfig($config,$id=""){
		$user = Mage::helper('MiniOrange_Openid');
		if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
			$admin = Mage::getSingleton('admin/session')->getUser();
			$id = $admin->getUserId();
			return $user->getConfig($config,$id);
		}
		else{
			$id = Mage::getSingleton('customer/session')->getCustomer()->getId();
			return $user->getConfig($config,$id);
		}
	}
	
	public function getConfigForAdmin($config){
		$user = Mage::helper('MiniOrange_Openid');
		$model = Mage::getModel("admin/user");
		$userid = $model->getCollection()->getFirstItem()->getId();
		return $user->getConfig($config,$userid);
	}
	
	public function cURLEnabled(){
		$customer = Mage::helper('MiniOrange_Openid');
		return $customer->is_curl_installed();
	}
	
	public function getSession(){
		if( !Mage::getSingleton('customer/session')->isLoggedIn() ) {
			$session = Mage::getSingleton('customer/session');
		}else{
			$session = Mage::getSingleton('admin/session');
		}
		return $session;
	}
	
	public function getOpenIdAdminUrl(){
		return Mage::helper("adminhtml")->getUrl("*/index/index");
	}
	
	function mo_openid_login_validate(){
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'getMoSocialLogin' ) !== false ) {
			
			$customer = Mage::helper('MiniOrange_Openid');
			
			$customerKey = $customer->getConfig('customerKey'); //change it
			$api_key = $customer->getConfig('apiKey'); //change it
			$customer_token = $customer->getConfig('apiToken'); //change it
			$client_name = "wordpress";
		
		
			$timestamp = round( microtime(true) * 1000 );
			$token = $client_name . ':' . number_format($timestamp, 0, '', ''). ':' . $api_key;
			$blocksize = 16;
			$pad = $blocksize - ( strlen( $token ) % $blocksize );
			$token =  $token . str_repeat( chr( $pad ), $pad );
			$token_params_encrypt = mcrypt_encrypt( MCRYPT_RIJNDAEL_128, $customer_token, $token, MCRYPT_MODE_ECB );
			$token_params_encode = base64_encode( $token_params_encrypt );
			$token_params = urlencode( $token_params_encode );
			
			if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
				$http = "https://";
			} else {
				$http =  "http://";
			}
			
			$base_return_url =  $http . $_SERVER["HTTP_HOST"] . strtok($_SERVER["REQUEST_URI"],'?');

    		$return_url = urlencode( $base_return_url . '?option=moopenid' );

			$url = $this->hostname . '/moas/openid-connect/client-app/authenticate?token=' . $token_params . '&id=' . $customerKey . '&encrypted=true&app=' . $_REQUEST['app_name'] . '_oauth&returnurl=' . $return_url;
			header("Location: ".$url);
			exit;
		} else if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'moopenid' ) !== false ){
		
			//do stuff after returning from oAuth processing			
			$user_email = '';
			if( isset( $_POST['email']  ) ) {
				$user_email = $_POST['email'];
			} else if( isset( $_POST['username']  )){
				$user_name = $_POST['username'];
				$user_email = $user_name.'@instagram.com';
			}

			if( $user_email ) {
				
				$user_name= Mage::getModel('admin/user')->getCollection()->addFieldToFilter('email',$user_email)->getFirstItem()->getUsername();
				
				$user = Mage::getModel('admin/user')->loadByUsername($user_name); 
				if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
				  Mage::getSingleton('adminhtml/url')->renewSecretUrls();
				}
					
				$session = Mage::getSingleton('admin/session');
				$session->setIsFirstVisit(true);
				$session->setUser($user);
				$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

				Mage::dispatchEvent('admin_session_user_login_success',array('user'=>$user));

				if ($session->isLoggedIn()) {
					$redirectUrl = Mage::getSingleton('adminhtml/url')->getUrl(Mage::getModel('admin/user')->getStartupPageUrl(), array('_current' => false));
					header('Location: ' . $redirectUrl);
					exit;
				}else{
					$datahelper = Mage::helper("MiniOrange_Openid"); //MiniOrange_Openid_Helper_Data
					$datahelper->displayMessage('User does not exist in our system. Please check your Email ID.',"ERROR");
					$this->redirect("*/index/index");
				}
				
			}
		}
	}

	function mo_openid_is_customer_valid(){
		$customer = Mage::helper('MiniOrange_Openid');
		$valid =  $customer->getConfig('customerValid'); 
		if(isset($valid) && $customer->getConfig('plan'))
			return $valid;
		else
			return false;
	}

	function mo_openid_check_customer_plan($customerPlan){
		$customer = Mage::helper('MiniOrange_Openid');
		$plan = $customer->getConfig('plan'); 
		if($plan) {
			if(strcasecmp($plan, $customerPlan)===0)
				return true;
			else
				return false;
		} else
			return false;
	}
	
	function getOpendIdConfig($data){
		return Mage::helper('MiniOrange_Openid')->getConfig($data);
	}
	
	private function redirect($url){
		$redirect = Mage::helper("adminhtml")->getUrl($url);
		Mage::app()->getResponse()->setRedirect($redirect);
	}
	

}