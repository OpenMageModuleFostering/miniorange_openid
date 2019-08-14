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
					$this->saveConfig('miniorange_2factor_email',$email,$id);
					$storeConfig = new Mage_Core_Model_Config();
					$storeConfig ->saveConfig('miniOrange/2factor/customerKey',$customerKey['id'], 'default', 0);
					$storeConfig ->saveConfig('miniOrange/2factor/apiKey',$customerKey['apiKey'], 'default', 0);
					$storeConfig ->saveConfig('miniOrange/2factor/2factorToken',$customerKey['token'], 'default', 0);
					//$session->unsaddAdmin();
					//$session->setShowTwoFactorSettings(1);
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
	
		//print_r($params);
		//exit();
		$helper = $this->getHelper1();
		$helper->displayMessage('Your configuration has been saved.',"SUCCESS");
		//$this->redirect("*/*/index");
		$redirect = Mage::helper("adminhtml")->getUrl("*/*/index");
		Mage::app()->getResponse()->setRedirect($redirect."?q=sharing");
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
			Mage::log($e->getMessage(), null, 'miniorage_openid_error.log', true);
		}
	}
	
	private function getSession(){
		return  Mage::getSingleton('admin/session');
	}
	
	private function getId(){
		return $this->getSession()->getUser()->getUserId();
	}
	
}