<?php

class MiniOrange_Openid_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
	
	private $_helper1 = "MiniOrange_Openid"; //MiniOrange_Openid_Helper_Data
	private $_helper2 = "MiniOrange_Openid/moOpenidUtility"; //moOpenidUtility 
	
    public function indexAction(){
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('core/template'));
        $this->renderLayout();
    }
	public function validateNewAction(){
		$params = $this->getRequest()->getParams();
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		$id = $this->getId();
		$submit = $params['submit'];
		$storeConfig = new Mage_Core_Model_Config();
		if($datahelper->is_curl_installed()){
			if(array_key_exists('otp',$params) && strcasecmp($submit,"Validate OTP")==0){
				$otp = $params['otp'];
				$transactionId  =  $session->getRegTxID();
				$email = $session->getaddAdmin();
				$phone = $session->getaddPhone();
				$pass = $session->getaddPass();
				$content = json_decode($customer->validate_otp_token('EMAIL',null, $transactionId, $otp, $datahelper->getdefaultCustomerKey(), $datahelper->getdefaultApiKey()),true);//print_r ($content); exit;
				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
					$this->create_customer($email,$phone,$pass);
				}else{
					$datahelper->displayMessage('Invalid one time passcode. Please enter a valid otp.',"ERROR");
					$this->redirect("*/*/index");
				}
			}else if(strcasecmp($submit,"Back")==0){
				$session->unsaddAdmin();
				$session->unsaddPass();
				$session->unsmostatus();
				$session->unsRegTxID();
				$this->redirect("*/*/index");
			}else{
				$datahelper->displayMessage('Please enter a value in otp field',"ERROR");
				$this->redirect("*/*/index");
			}
		}else{
			$datahelper->displayMessage('cURL is not enabled. Please <a id="cURL" href="#cURLfaq">click here</a> to see how to enable cURL.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	public function sendOTPPhoneAction(){
		$params = $this->getRequest()->getParams();
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		$storeConfig = new Mage_Core_Model_Config();
		if(array_key_exists('phone',$params)){
			$phone = $params['phone'];
			$storeConfig ->saveConfig('miniorange/Openid/phone',$phone,'default', 0);
			$content = json_decode($customer->send_otp_token(null,'OTP_OVER_SMS',$datahelper->getdefaultCustomerKey(),$datahelper->getdefaultApiKey(), $phone), true);
			if(strcasecmp($content['status'], 'SUCCESS') == 0) {
				$datahelper->displayMessage(' A one time passcode is sent to ' . $phone . '. Please enter the otp here to verify your email.',"SUCCESS");
				$session->setRegTxID($content['txId']);
				$session->setaddPhone($phone);
				$session->setmostatus('MO_OTP_PHONE_VALIDATE');
				$this->redirect("*/*/index");
			}else{
				$datahelper->displayMessage('There was an error in sending SMS. Please click on Resend OTP to try again.',"ERROR");
				$session->setmostatus('MO_OTP_DELIVERED_FAILURE');
				$this->redirect("*/*/index");
			}
		}else{
			$datahelper->displayMessage('Please Enter a Phone Number.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	public function registerNewUserAction(){
	
		$params = $this->getRequest()->getParams();
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		$storeConfig = new Mage_Core_Model_Config();
		if($datahelper->is_curl_installed()){
			
			$email = $params['email'];
			$password = $params['password'];
			$confirm = $params['confirmPassword'];
			$submit = $params['submit'];
					
			if(strcasecmp($submit,"Register") == 0){
				if($password==$confirm){ 
					$session->setaddAdmin($email);
					$session->setaddPhone($phone);
					$session->setaddPass($password);				
					$content = $customer->check_customer($email);
					$content = json_decode($content, true);//print_r ($content); exit;
					if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){ 
						$content = json_decode($customer->send_otp_token($email,'EMAIL',$datahelper->getdefaultCustomerKey(),$datahelper->getdefaultApiKey()), true);//print_r ($content); exit;
						if(strcasecmp($content['status'], 'SUCCESS') == 0) {
							$session->setRegTxID($content['txId']);
							$session->setmostatus('MO_OTP_EMAIL_VALIDATE');
							$datahelper->displayMessage('A one time passcode is sent to '. $email .'. Please enter the otp here to verify your email.',"SUCCESS");
							$this->redirect("*/*/index");
						}else{
							
							$session->setmostatus('MO_OTP_DELIVERED_FAILURE');
							$datahelper->displayMessage('There was an error in sending email. Please verify your email and try again.',"ERROR");
							$this->redirect("*/*/index");
						}
					}else{
						$this->get_current_customer($email,$password);
					}
				}else{
					
					$datahelper->displayMessage('Passwords do not match',"ERROR");
					$this->redirect("*/*/index");
				}
			}else if(strcasecmp($submit,"Forgot Password?") == 0){
				$this->forgotPass($email);
				$this->redirect("*/*/index");
			}else{
				$this->redirect("*/*/index");
			}
		}else{
			$datahelper->displayMessage('cURL is not enabled. Please <a id="cURL" href="#cURLfaq">click here</a> to see how to enable cURL.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	private function get_current_customer($email,$password){
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		$storeConfig = new Mage_Core_Model_Config();
		$content = $customer->get_customer_key($email,$password);
		$id = $this->getId();
		$customerKey = json_decode($content, true);
		if(json_last_error() == JSON_ERROR_NONE) {
			$storeConfig = new Mage_Core_Model_Config();
			$this->saveConfig('miniorange_openid_email',$email,$id);
			$storeConfig ->saveConfig('miniOrange/Openid/customerKey',$customerKey['id'], 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/apiKey',$customerKey['apiKey'], 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/token',$customerKey['token'], 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/pass','','default', 0);
			$session->unsmostatus();
			$datahelper->displayMessage('Your account has been retrieved successfully.',"SUCCESS");
			$this->redirect("*/*/index");
		}
		else{
			$datahelper->displayMessage('You already have an account with miniOrange. Please enter a valid password.',"ERROR");
			$storeConfig ->saveConfig('miniOrange/Openid/pass','','default', 0);
			$session->setmostatus('MO_VERIFY_CUSTOMER');
			$this->redirect("*/*/index");
		}
	}
	private function create_customer($email,$phone,$pass){
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		$storeConfig = new Mage_Core_Model_Config();
		$customerKey = json_decode( $customer->create_customer($email,$phone,$pass), true );
		if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 ) {
					$this->get_current_customer($email,$pass);
		} else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 ) {
			$id = $this->getId();
			$this->saveConfig('miniorange_openid_email',$email,$id);
			$storeConfig = new Mage_Core_Model_Config();
			$session->unsmostatus();
			$storeConfig ->saveConfig('miniOrange/Openid/customerKey',$customerKey['id'], 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/apiKey',$customerKey['apiKey'], 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/token',$customerKey['token'], 'default', 0);
			$datahelper->displayMessage('Login Successful. You can configure Social Login & Sharing now.',"SUCCESS");
			$this->redirect("*/*/index");
			
		}
		$storeConfig ->saveConfig('miniOrange/Openid/pass','','default', 0);
	}
	public function resendValidationOTPAction(){
		$helper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		if($helper->is_curl_installed()){
			//$admin = $session->getUser();
			$id = $this->getId();
			$email =$session->getaddAdmin();
			$content = json_decode($customer->send_otp_token($email,'EMAIL',$helper->getdefaultCustomerKey(),$helper->getdefaultApiKey()), true); //send otp for verification
			if(strcasecmp($content['status'], 'SUCCESS') == 0){
				$session->setMytextid($content['txId']);
				$session->setShowOTP(1);
				$helper->displayMessage('OTP has been sent to your Email. Please check your mail and enter the otp below.',"SUCCESS");
				$this->redirect("*/*/index");
			}
			else{
				$this->displayMessage('Error sending OTP. Please try again!',"ERROR");
				$this->redirect("*/*/index");
			}
		}
		else{
			$this->displayMessage('cURL is not enabled. Please <a id="cURL" href="#cURLfaq">click here</a> to see how to enable cURL.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	
	
	public function existingUserAction(){
		$params = $this->getRequest()->getParams();
		$datahelper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		//print_r($session);
		//exit();
		if($datahelper->is_curl_installed()){
			$email = $params['loginemail'];
			$session->setEnteredEmail($email);
			$session->setaddAdmin($email);
			$password = $params['loginpassword'];
			$submit = $params['submit'];
			//$admin = $session->getUser();
			$id = $this->getId();
			if(strcasecmp($submit,"Submit") == 0){
				$content = $customer->get_customer_key($email,$password);
				$customerKey = json_decode($content, true);
				if(json_last_error() == JSON_ERROR_NONE) {
					$this->saveConfig('miniorange_openid_email',$email,$id);
					$storeConfig = new Mage_Core_Model_Config();
					$session->unsmostatus();
					$storeConfig ->saveConfig('miniOrange/Openid/customerKey',$customerKey['id'], 'default', 0);
					$storeConfig ->saveConfig('miniOrange/Openid/apiKey',$customerKey['apiKey'], 'default', 0);
					$storeConfig ->saveConfig('miniOrange/Openid/token',$customerKey['token'], 'default', 0);
					$datahelper->displayMessage('Login Successful. You can configure Social Login & Sharing now.',"SUCCESS");
					$this->redirect("*/*/index");
				}
				else{
					$datahelper->displayMessage('Invalid Credentials',"ERROR");
					$this->redirect("*/*/index");
				}
			}
			else if(strcasecmp($submit,"Forgot Password?") == 0){
				$this->forgotPass($email);
				$this->redirect("*/*/index");
			}
			else{
				$this->redirect("*/*/index");
			}
		}
		else{
			$datahelper->displayMessage('cURL is not enabled. Please <a id="cURL" href="#cURLfaq">click here</a> to see how to enable cURL.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	
	
	public function saveSocialLoginConfAction(){
		$params = $this->getRequest()->getParams();
		$storeConfig = new Mage_Core_Model_Config();
		$storeConfig ->saveConfig('miniOrange/Openid/googleEnable',$params['mo_openid_google_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/facebookEnable',$params['mo_openid_facebook_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/twitterEnable',$params['mo_openid_twitter_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/instagramEnable',$params['mo_openid_instagram_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/linkedinEnable',$params['mo_openid_linkedin_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/amazonEnable',$params['mo_openid_amazon_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/salesforceEnable',$params['mo_openid_salesforce_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/windowsliveEnable',$params['mo_openid_windowslive_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/loginTheme',$params['mo_openid_login_theme'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/loginCustomTheme',$params['mo_openid_login_custom_theme'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/iconSpace',$params['mo_login_icon_space'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/iconCustomSize',$params['mo_login_icon_custom_size'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/iconCustomWidth',$params['mo_login_icon_custom_width'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/iconCustomHeight',$params['mo_login_icon_custom_height'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/iconCustomColor',$params['mo_login_icon_custom_color'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/longButtonText',$params['mo_sharing_long_button_text'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/showOnAdmin',$params['mo_openid_show_on_admin'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/showOnFront',$params['mo_openid_show_on_front'], 'default', 0);
		
		//print_r($params);
		//exit();
		$helper = $this->getHelper1();
		$helper->displayMessage('Your configuration has been saved.',"SUCCESS");
		$this->redirect("*/*/index");
	}
	
	public function saveSocialSharingConfAction(){
		$params = $this->getRequest()->getParams();
		$storeConfig = new Mage_Core_Model_Config();
		$storeConfig ->saveConfig('miniOrange/Openid/facebookShareEnable',$params['mo_openid_facebook_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/twitterShareEnable',$params['mo_openid_twitter_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/googleShareEnable',$params['mo_openid_google_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/vkontakteShareEnable',$params['mo_openid_vkontakte_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/tumblrShareEnable',$params['mo_openid_tumblr_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/stumbleShareEnable',$params['mo_openid_stumble_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/linkedinShareEnable',$params['mo_openid_linkedin_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/redditShareEnable',$params['mo_openid_reddit_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/pinterestShareEnable',$params['mo_openid_pinterest_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/pocketShareEnable',$params['mo_openid_pocket_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/diggShareEnable',$params['mo_openid_digg_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/deliciousShareEnable',$params['mo_openid_delicious_share_enable'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/odnoklassnikiShareEnable',$params['mo_openid_odnoklassniki_share_enable'], 'default', 0);

		$storeConfig ->saveConfig('miniOrange/Openid/shareIconTheme',$params['mo_openid_share_theme'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconCustomTheme',$params['mo_openid_share_custom_theme'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconCustomColor',$params['mo_sharing_icon_custom_color'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconCustomFont',$params['mo_sharing_icon_custom_font'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconSpace',$params['mo_sharing_icon_space'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconSize',$params['mo_sharing_icon_custom_size'], 'default', 0);
		
		$storeConfig ->saveConfig('miniOrange/Openid/shareIconPosition',$params['mo_openid_share_position'], 'default', 0);
		$storeConfig ->saveConfig('miniOrange/Openid/sharePositionTop',$params['mo_sharing_position_top'], 'default', 0);
		$helper = $this->getHelper1();
		$helper->displayMessage('Your configuration has been saved.',"SUCCESS");
		$redirect = Mage::helper("adminhtml")->getUrl("*/*/index");
		Mage::app()->getResponse()->setRedirect($redirect."?q=sharing");
	}

	public function checkUserValidationAction(){
		$helper = $this->getHelper1();
		$customer = $this->getHelper2();
		$customerKey = $helper->getConfig('customerKey');
		$apiKey = $helper->getConfig('apiKey');
		$content = $customer->check_customer_valid($customerKey,$apiKey);
		$content = json_decode($content,true);
		if(strcasecmp($content['status'], 'SUCCESS') == 0){
			$storeConfig = new Mage_Core_Model_Config();
			$type = strcasecmp($content['licenseType'], 'Premium') !== FALSE ? 1 : 0;
			$plan = isset($content['licensePlan']) ? base64_encode($content['licensePlan']) : 0;
			$storeConfig ->saveConfig('miniOrange/Openid/customerValid',$type, 'default', 0);
			$storeConfig ->saveConfig('miniOrange/Openid/plan',$plan, 'default', 0);
			if( $helper->getConfig('customerValid') && isset($content['licensePlan'])){
				$license = array();
				$license = explode(' -', $content['licensePlan']);
				$lp = $license[0];
				$helper->displayMessage('You are on ' . $lp . '.','SUCCESS');
			} else
				$helper->displayMessage('You are on Free - Forever Plan.','SUCCESS');
		}else{
			$helper->displayMessage('An error occured while processing your request. Please try again.',"ERROR");
		}	

		$redirect = Mage::helper("adminhtml")->getUrl("*/*/index");
		Mage::app()->getResponse()->setRedirect($redirect."?q=pricing");	
	}
	
	public function supportSubmitAction(){
		$helper = $this->getHelper1();
		$customer = $this->getHelper2();
		$session = $this->getSession();
		if($helper->is_curl_installed()){
			$params = $this->getRequest()->getParams();
			$user = $session->getUser();
			$customer->submit_contact_us($params['query_email'], $params['query_phone'], $params['query'], $user);
			$helper->displayMessage('Your query has been sent. We will get in touch with you soon',"SUCCESS");
			$this->redirect("*/*/index");
		}
		else{
			$helper->displayMessage('cURL is not enabled. Please <a id="cURL" href="#cURLfaq">click here</a> to see how to enable cURL.',"ERROR");
			$this->redirect("*/*/index");
		}
	}

	
	private function forgotPass($email){
		$helper = $this->getHelper1();
		$customer = $this->getHelper2();
		$params = $this->getRequest()->getParams();
		$content = json_decode($customer->forgot_password($email,$helper->getConfig('customerKey'),$helper->getConfig('apiKey')), true); 
		if(strcasecmp($content['status'], 'SUCCESS') == 0){
			$this->displayMessage('Your new password has been generated and sent to '.$email.'.',"SUCCESS");
			$this->redirect("*/*/index");
		}
		else{
			$this->displayMessage('Sorry we encountered an error while reseting your password.',"ERROR");
			$this->redirect("*/*/index");
		}
	}
	
	private function getHelper1(){
		return Mage::helper($this->_helper1);
	}
	
	private function getHelper2(){
		return Mage::helper($this->_helper2);
	}
	
	private function redirect($url){
		$redirect = Mage::helper("adminhtml")->getUrl($url);
		Mage::app()->getResponse()->setRedirect($redirect);
	}
	
	private function saveConfig($url,$value,$id){
		$data = array($url=>$value);
		$model = Mage::getModel('admin/user')->load($id)->addData($data);
		try {
			$model->setId($id)->save(); 
		} catch (Exception $e){
			Mage::log($e->getMessage(), null, 'miniorange_openid_error.log', true);
		}
	}
	
	private function getSession(){
		return  Mage::getSingleton('admin/session');
	}
	
	private function getId(){
		return $this->getSession()->getUser()->getUserId();
	}
	
}