<?php
class MiniOrange_Openid_Helper_Data extends Mage_Core_Helper_Abstract {
	private $hostname = "https://auth.miniorange.com";
	private $defaultCustomerKey = "16555";
	private $defaultApiKey = "fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq";
	function adminExists($username) {
		$adminuser = Mage::getModel ( 'admin/user' );
		$adminuser->loadByUsername ( $username );
		if ($adminuser->getId ()) {
			return true;
		} else {
			return false;
		}
	}
	function getHostURl() {
		return $this->hostname;
	}
	function getdefaultCustomerKey() {
		return $this->defaultCustomerKey;
	}
	function getdefaultApiKey() {
		return $this->defaultApiKey;
	}
	function getAdmin($username) {
		$adminuser = Mage::getModel ( 'admin/user' );
		$adminuser->loadByUsername ( $username );
		if ($adminuser->getId ()) {
			return $adminuser;
		} else {
			return;
		}
	}
	
	/* Function to extract config stored in the database */
	function getConfig($config, $id=null) {
		switch ($config) {
			case 'email' :
				$result = Mage::getModel ( 'admin/user' )->load ( $id )->getData ( 'miniorange_openid_email' );
				break;
			case 'customerKey' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/customerKey' );
				break;
			case 'status' :
				$result= Mage::getStoreConfig ( 'miniOrange/Openid/registration/status' );
				break;
			case 'apiKey' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/apiKey' );
				break;
			case 'apiToken' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/token' );
				break;
			case 'googleEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/googleEnable' );
				break;
			case 'facebookEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/facebookEnable' );
				break;
			case 'twitterEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/twitterEnable' );
				break;
			case 'instagramEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/instagramEnable' );
				break;
			case 'linkedinEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/linkedinEnable' );
				break;
			case 'amazonEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/amazonEnable' );
				break;
			case 'salesforceEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/salesforceEnable' );
				break;
			case 'windowsliveEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/windowsliveEnable' );
				break;
			case 'loginTheme' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/loginTheme' );
				break;
			case 'loginCustomTheme' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/loginCustomTheme' );
				break;
			case 'iconSpace' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/iconSpace' );
				break;
			case 'iconCustomSize' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/iconCustomSize' );
				break;
			case 'iconCustomWidth' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/iconCustomWidth' );
				break;
			case 'iconCustomHeight' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/iconCustomHeight' );
				break;
			case 'iconCustomColor' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/iconCustomColor' );
				break;
				
			case 'facebookShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/facebookShareEnable' );
				break;
			case 'twitterShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/twitterShareEnable' );
				break;
			case 'googleShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/googleShareEnable' );
				break;
			case 'vkontakteShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/vkontakteShareEnable' );
				break;
			case 'tumblrShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/tumblrShareEnable' );
				break;
			case 'stumbleShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/stumbleShareEnable' );
				break;
			case 'linkedinShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/linkedinShareEnable' );
				break;
			case 'redditShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/redditShareEnable' );
				break;
			case 'pinterestShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/pinterestShareEnable' );
				break;
			case 'pocketShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/pocketShareEnable' );
				break;
			case 'diggShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/diggShareEnable' );
				break;
			case 'deliciousShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/deliciousShareEnable' );
				break;
			case 'odnoklassnikiShareEnable' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/odnoklassnikiShareEnable' );
				break;
			case 'shareIconTheme' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconTheme' );
				break;
			case 'shareIconCustomTheme' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconCustomTheme' );
				break;
			case 'shareIconCustomColor' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconCustomColor' );
				break;
			case 'shareIconCustomFont' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconCustomFont' );
				break;
			case 'shareIconSpace' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconSpace' );
				break;
			case 'shareIconSize' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconSize' );
				break;
			case 'shareIconPosition' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/shareIconPosition' );
				break;
			case 'sharePositionTop' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/sharePositionTop' );
				break;
			case 'longButtonText' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/longButtonText' );
				break;
			case 'customerValid' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/customerValid' );
				break;
			case 'plan' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/plan' );
				break;
			case 'showOnAdmin' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/showOnAdmin' );
				break;
			case 'showOnFront' :
				$result = Mage::getStoreConfig ( 'miniOrange/Openid/showOnFront' );
				break;
			default :
				return;
				break;
		}
		return $result;
	}
	
	/* Function to show his partial registered email to user */
	function showEmail($id) {
		$email = $this->getConfig ( 'email', $id );
		$emailsize = strlen ( $email );
		$partialemail = substr ( $email, 0, 1 );
		$temp = strrpos ( $email, "@" );
		$endemail = substr ( $email, $temp - 1, $emailsize );
		for($i = 1; $i < $temp; $i ++) {
			$partialemail = $partialemail . 'x';
		}
		$showemail = $partialemail . $endemail;
		
		return $showemail;
	}
	
	/* Function to show his partial phone number to user */
	function showPhone($id) {
		$phone = $this->getConfig ( 'phone', $id );
		$phonesize = strlen ( $phone );
		$endphone = substr ( $phone, $phonesize - 4, $phonesize );
		$partialphone = '+';
		for($i = 1; $i < $phonesize - 4; $i ++) {
			$partialphone = $partialphone . 'x';
		}
		$showphone = $partialphone . $endphone;
		
		return $showphone;
	}
	
	/* Function to show his partial phone number to user */
	function showCustomerPhone($id) {
		$phone = $this->getConfig ( 'miniorange_phone', $id );
		$phonesize = strlen ( $phone );
		$endphone = substr ( $phone, $phonesize - 4, $phonesize );
		$partialphone = '+';
		for($i = 1; $i < $phonesize - 4; $i ++) {
			$partialphone = $partialphone . 'x';
		}
		$showphone = $partialphone . $endphone;
	
		return $showphone;
	}
	
	function showCustomerEmail($id) {
		$email = $this->getConfig ( 'miniorange_email', $id );
		$emailsize = strlen ( $email );
		$partialemail = substr ( $email, 0, 1 );
		$temp = strrpos ( $email, "@" );
		$endemail = substr ( $email, $temp - 1, $emailsize );
		for($i = 1; $i < $temp; $i ++) {
			$partialemail = $partialemail . 'x';
		}
		$showemail = $partialemail . $endemail;
		
		return $showemail;
	}
	
	/* Function to check if cURL is enabled */
	function is_curl_installed() {
		if (in_array ( 'curl', get_loaded_extensions () )) {
			return 1;
		} else
			return 0;
	}
	
	
	function displayMessage($message, $type) {
		Mage::getSingleton ( 'core/session' )->getMessages ( true );
		if (strcasecmp ( $type, "SUCCESS" ) == 0)
			Mage::getSingleton ( 'core/session' )->addSuccess ( $message );
		else if (strcasecmp ( $type, "ERROR" ) == 0)
			Mage::getSingleton ( 'core/session' )->addError ( $message );
		else if (strcasecmp ( $type, "NOTICE" ) == 0)
			Mage::getSingleton ( 'core/session' )->addNotice ( $message );
		else
			Mage::getSingleton ( 'core/session' )->addWarning ( $message );
	}
}  