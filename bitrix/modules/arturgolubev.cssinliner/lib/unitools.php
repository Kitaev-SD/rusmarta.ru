<?
namespace Arturgolubev\Cssinliner; //1.5.1

class Unitools {
	const MODULE_ID = 'arturgolubev.cssinliner';
	var $MODULE_ID = 'arturgolubev.cssinliner';
	
	public static $storage = array();
	public static function setStorage($type, $name, $value){ self::$storage[$type][$name] = $value;}
	public static function getStorage($type, $name){ return self::$storage[$type][$name];}
	
	function getSetting($name, $def = false){
		if(!isset(self::$storage["setting"][$name]))
		{
			self::setStorage("setting", $name, trim(\COption::GetOptionString(self::MODULE_ID, $name, $def)));
		}
		
		$r = self::getStorage("setting", $name);
		
		return $r;
	}
	
	function getSiteSetting($name, $def = false){
		if(!isset(self::$storage["setting_site"][$name]))
		{
			self::setStorage("setting_site", $name, trim(\COption::GetOptionString(self::MODULE_ID, $name.'_'.SITE_ID, $def)));
		}
		
		$r = self::getStorage("setting_site", $name);
		
		return $r;
	}
	
	function getSiteSettingEx($name, $def = false){
		if(!isset(self::$storage["setting_site_ex"][$name]))
		{
			$val = trim(\COption::GetOptionString(self::MODULE_ID, $name.'_'.SITE_ID));
			if(!$val) $val = trim(\COption::GetOptionString(self::MODULE_ID, $name));
			if(!$val && $def) $val = $def;
			
			self::setStorage("setting_site_ex", $name, $val);
		}
		else
		{
			$val = self::getStorage("setting_site_ex", $name);
		}
		
		return $val;
	}
	
	function isAdminPage(){
		if(!isset(self::$storage["main"]["is_admin_page"]))
		{
			$r = 0;
			
			if(defined("ADMIN_SECTION") && ADMIN_SECTION == true) $r = 1;
			if(strpos($_SERVER['PHP_SELF'], BX_ROOT.'/admin') === 0) $r = 1;
			if(strpos($_SERVER['PHP_SELF'], BX_ROOT.'/tools') === 0) $r = 1;
			
			self::setStorage("main", "is_admin_page", $r);
		}
		else
			$r = self::getStorage("main", "is_admin_page");
		
		return $r;
	}
	
	function checkStatus(){
		if(!isset(self::$storage["main"]["status"]))
		{
			$r = (self::isAdminPage() || $_SERVER['REQUEST_METHOD'] == 'POST') ? 0 : 1;
			self::setStorage("main", "status", $r);
		}
		else
			$r = self::getStorage("main", "status");
		
		return $r;
	}
	
	function checkAjax(){
		$check = (strtolower($_REQUEST['ajax']) == 'y' || (isset($_REQUEST["bxajaxid"]) && strlen($_REQUEST["bxajaxid"]) > 0)) ? 0 : 1;
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') $check = 0;
		return $check;
	}
	
	function isAdmin(){
		global $USER;
		if(!is_object($USER)) $USER = new CUser();
		
		return $USER->IsAdmin();
	}
	
	function addJs($script){
		global $APPLICATION;
		$APPLICATION->AddHeadScript($script);
	}
	function addCss($script){
		global $APPLICATION;
		$APPLICATION->SetAdditionalCSS($script, true);
	}
	
	function textOneLine($text){
		return str_replace(array("\r\n", "\r", "\n"), '',  $text);
	}
	
	function checkPageException($pages){
		if($pages)
		{
			global $APPLICATION;
			
			$cur = $APPLICATION->GetCurPage(false);
			$curParams = $APPLICATION->GetCurPageParam();
			
			$ar_pages = explode("\n",$pages);
			foreach($ar_pages as $checkValue)
			{
				$checkValue = trim($checkValue);
				if(!$checkValue) continue;
				
				$pattern = '/^'.str_replace(array('/', '*'), array('\/', '.*'), $checkValue).'$/sU';
				
				if(preg_match($pattern, $cur) || preg_match($pattern, $curParams))
					return 0;
			}
		}
		
		return 1;
	}
	
	function addBodyScript($script, $oldBuffer){
		$search = '</body>';
		$replace = $script. PHP_EOL .$search;
		
		$bufferContent = $oldBuffer;
		
		if(substr_count($oldBuffer, $search) == 1)
		{
			$bufferContent = str_replace($search, $replace, $oldBuffer);
		}
		else
		{
			$bodyEnd = self::getLastPositionIgnoreCase($oldBuffer, $search);
			if ($bodyEnd !== false)
			{
				$bufferContent = substr_replace($oldBuffer, $replace, $bodyEnd, strlen($search));
			}
		}
		
		return $bufferContent;
	}
	
	function isHtmlPage($page){
		if(!defined("AG_CHECK_DOCTYPE"))
		{
			$t = (stripos(substr($page,0,512), '<!DOCTYPE') === false) ? 0 : 1;
			define('AG_CHECK_DOCTYPE', $t);
		}
		
		return AG_CHECK_DOCTYPE;
	}
	
	
	
	public static function getLastPositionIgnoreCase($haystack, $needle, $offset = 0)
	{
		if (defined("BX_UTF"))
		{
			if (function_exists("mb_orig_strripos"))
			{
				return mb_orig_strripos($haystack, $needle, $offset);
			}

			return mb_strripos($haystack, $needle, $offset, "latin1");
		}

		return strripos($haystack, $needle, $offset);
	}
	
	public static function getFirstPositionIgnoreCase($haystack, $needle, $offset = 0)
	{
		if (defined("BX_UTF"))
		{
			if (function_exists("mb_orig_stripos"))
			{
				return mb_orig_stripos($haystack, $needle, $offset);
			}

			return mb_stripos($haystack, $needle, $offset, "latin1");
		}

		return stripos($haystack, $needle, $offset);
	}
}