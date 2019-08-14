<?php

class MiniOrange_Openid_Customer_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'customerSocialLogin' ) !== false ) {
			
			$customer = Mage::helper('MiniOrange_Openid');
			
			$customerKey = $customer->getConfig('customerKey'); //change it
			$api_key = $customer->getConfig('apiKey'); //change it
			$customer_token = $customer->getConfig('apiToken'); //change it

			//echo $customerKey." ".$api_key."".$customer_token." ".$this->hostname;
			//exit();
			
			$client_name = "wordpress";
			$timestamp = round( microtime(true) * 1000 );
			$token = $client_name . ':' . number_format($timestamp, 0, '', ''). ':' . $api_key;

			$blocksize = 16;
			$pad = $blocksize - ( strlen( $token ) % $blocksize );
			$token =  $token . str_repeat( chr( $pad ), $pad );
			$token_params_encrypt = mcrypt_encrypt( MCRYPT_RIJNDAEL_128, $customer_token, $token, MCRYPT_MODE_ECB );
			$token_params_encode = base64_encode( $token_params_encrypt );
			$token_params = urlencode( $token_params_encode );
			$userdata = 'true';
			
			if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
				$http = "https://";
			} else {
				$http =  "http://";
			}
			
			//$base_return_url =  $http . $_SERVER["HTTP_HOST"] . strtok($_SERVER["REQUEST_URI"],'?');
    		//$return_url = urlencode( $base_return_url . '?option=mocustomersso' );

			$return_url  = urlencode(Mage::getBaseUrl()."sociallogin/index/index?option=mocustomersso");
			
			//$url =  'https://auth.miniorange.com/moas/openid-connect/client-app/authenticate?token=' . $token_params .'&userdata='. $userdata . '&id=' . $customerKey . '&encrypted=true&app=' . $_REQUEST['app_name'] . '_oauth&returnurl=' . $return_url;
			$url =  'https://auth.miniorange.com/moas/openid-connect/client-app/authenticate?token=' . $token_params .'&id=' . $customerKey . '&encrypted=true&app=' . $_REQUEST['app_name'] . '_oauth&returnurl=' . $return_url;
			header("Location: ".$url);
			exit;
		}else if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'mocustomersso' ) !== false ){
			//do stuff after returning from oAuth processing			
			$user_email = '';
			if( isset( $_POST['email']  ) ) {
				$user_email = $_POST['email'];
			} else if( isset( $_POST['username']  )){
				$user_name = $_POST['username'];
				$user_email = $user_name.'@instagram.com';
			}

			$websiteId = Mage::app()->getWebsite()->getId();
			$store = Mage::app()->getStore();
			if( $user_email ) {
				$customer = Mage::getModel("customer/customer");
				$customer->setWebsiteId($websiteId);
				$customer->loadByEmail($user_email);
				
				if($customer->getId()){	
					Mage::getSingleton('customer/session')->loginById($customer->getId());
				}else{
					$length = 10;
					$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
					$customer = Mage::getModel("customer/customer");
					$customer->setWebsiteId($websiteId)
							->setStore($store)
							->setFirstname($user_email)
							->setLastname('')
							->setEmail($user_email)
							->setPassword($randomString);
					$customer->save();
					$customer->loadByEmail(trim($user_email));
					Mage::getSingleton('customer/session')->loginById($customer->getId());
				}
				Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
			}
		}
    }
}