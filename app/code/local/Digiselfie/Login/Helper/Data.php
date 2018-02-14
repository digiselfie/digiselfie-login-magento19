<?php

/**
 * Digiselfie Login data Helper
 *
 * @category    Digiselfie
 * @package     Digiselfie_Login
 * @author      Digiselfie Inc.
 */

class Digiselfie_Login_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * redirect to 404 page
     */
public function redirect404($frontController)
    {
        $frontController->getResponse()
            ->setHeader('HTTP/1.1','404 Not Found');
        $frontController->getResponse()
            ->setHeader('Status','404 File not found');

        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($frontController, $pageId)) {
				$frontController->_forward('defaultNoRoute');
			}
    }

    public function getEnvironmentInformation () {
    	$info = array();
    	$info['domain_name'] = Mage::getBaseUrl();
    	$info['framework'] = 'magento';
    	$info['edition'] = 'default';
    	if(method_exists('Mage','getEdition')) $info['edition'] = Mage::getEdition();
    	$info['version'] = Mage::getVersion();
    	$info['php_version'] = phpversion();
    	$info['feed_types'] = Mage::getStoreConfig(Digiselfie_Login_Model_Feed::XML_FEED_TYPES);
    	$info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_general/name');
    	if(strlen($info['admin_name']) == 0) $info['admin_name'] =  Mage::getStoreConfig('trans_email/ident_sales/name');
    	$info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_general/email');
    	if(strlen($info['admin_email']) == 0) $info['admin_email'] =  Mage::getStoreConfig('trans_email/ident_sales/email');

    	return $info;
    }

    public function addParams($url = '', $params = array(), $urlencode = true) {
    	if(count($params)>0){
    		foreach($params as $key=>$value){
    			if(parse_url($url, PHP_URL_QUERY)) {
    				if($urlencode)
    					$url .= '&'.$key.'='.$this->prepareParams($value);
    				else
    					$url .= '&'.$key.'='.$value;
    			} else {
    				if($urlencode)
    					$url .= '?'.$key.'='.$this->prepareParams($value);
    				else
    					$url .= '?'.$key.'='.$value;
    			}
    		}
    	}
    	return $url;
    }
    public function prepareParams($data){
    	if(!is_array($data) && strlen($data)){
    		return urlencode($data);
    	}
    	if($data && is_array($data) && count($data)>0){
    		foreach($data as $key=>$value){
    			$data[$key] = urlencode($value);
    		}
    		return $data;
    	}
    	return false;
    }
}
