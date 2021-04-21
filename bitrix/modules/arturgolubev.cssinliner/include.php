<?
use \Arturgolubev\Cssinliner\Unitools as UTools;

IncludeModuleLangFile(__FILE__);

Class CArturgolubevCssinliner 
{
	const MODULE_ID = 'arturgolubev.cssinliner';
	var $MODULE_ID = 'arturgolubev.cssinliner';
	
	const CACHE_TIME = 86400;
	
	function onBufferContent(&$bufferContent){
		
		if(!UTools::checkStatus() || !CModule::IncludeModule(self::MODULE_ID) || defined('LOCK_CSSINLINER')) return false;

		if(UTools::getSetting('disable') == 'Y' || UTools::getSetting('disabled_'.SITE_ID) == 'Y') return false;
		
		if(!UTools::isHtmlPage($bufferContent)) return false;
		
		// if(true)
		if(!UTools::isAdmin())
		{
			$bufferContent = self::applyInlineCss($bufferContent);
			$bufferContent = self::addPrelinks($bufferContent);
		}
	}
	
	function addPrelinks($bufferContent){
		$s = '';
		$arPreconnect = explode(PHP_EOL, UTools::getSetting('preconnect'));
		$arPreloading = explode(PHP_EOL, UTools::getSetting('preloading'));
		
		if(!empty($arPreconnect))
		{
			foreach($arPreconnect as $k=>$v)
			{
				$v = trim($v);
				if($v != '')
				{
					$s .= '<link rel="preconnect" href="'.$v.'" crossorigin />';
				}
			}
		}
		
		if(!empty($arPreloading))
		{
			foreach($arPreloading as $k=>$v)
			{
				$v = trim($v);
				if($v != '')
				{
					$type = '';
					if(strstr($v, '.css'))
						$type = 'style';
					elseif(strstr($v, '.js'))
						$type = 'script';
					elseif(strstr($v, '.woff2') || strstr($v, '.ttf'))
						$type = 'font';
					
					$s .= '<link rel="preload" href="'.$v.'" '.(($type)?'as="'.$type.'"':'').' crossorigin="anonymous" />';
				}
			}
		}
		
		if($s)
		{
			$h = '<head>';
			$pos = UTools::getFirstPositionIgnoreCase($bufferContent, $h);
			if ($pos !== false)
				$bufferContent = substr_replace($bufferContent, $h.PHP_EOL.$s, $pos, strlen($h));
		}
		
		return $bufferContent;
	}
	
	function baseOptimize($style, $useOptimize, $arDopSearch = array(), $arDopReplace = array()){
		$arOptiSearch = array();
		$arOptiReplace = array();

		if($useOptimize == 'Y')
		{
			$arOptiSearch[] = '/\/\*.*?\*\//si'; $arOptiReplace[] = "";
			$arOptiSearch[] = "/\n/"; $arOptiReplace[] = " ";
			$arOptiSearch[] = "/\t/"; $arOptiReplace[] = " ";
			$arOptiSearch[] = '/(\s)+/s'; $arOptiReplace[] = '\\1';
			$arOptiSearch[] = '/;\s+/'; $arOptiReplace[] = ';';
			$arOptiSearch[] = '/:\s+/'; $arOptiReplace[] = ':';
			$arOptiSearch[] = '/\s+\{\s+/'; $arOptiReplace[] = '{';
			$arOptiSearch[] = '/\{\s+/'; $arOptiReplace[] = '{';
			$arOptiSearch[] = '/\,\s+/'; $arOptiReplace[] = ',';
			$arOptiSearch[] = '/;*\}/'; $arOptiReplace[] = '}';
		}
		
		$mergedSearch = array_merge($arOptiSearch, $arDopSearch);
		$mergedReplace = array_merge($arOptiReplace, $arDopReplace);
		
		if(!empty($mergedSearch))
			$style = preg_replace($mergedSearch, $mergedReplace, $style);
		
		if(UTools::getSetting('use_font_display') == 'Y')
		{
			$reg = "/\@font\-face[^}]+}/";
			$style = preg_replace_callback($reg, "self::_addFontDisplay", $style);
		}
		
		return trim($style);
	}
	
	private function _addFontDisplay($matches){
		if(!strstr($matches[0], 'font-display'))
			$matches[0] = str_replace('}', ';font-display: swap;}', $matches[0]);
		
		return $matches[0];
	}
	
	function _getOuterStyle($url){
		if(extension_loaded('curl')){
			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
				curl_setopt($ch, CURLOPT_HEADER, 0);
				$style = curl_exec($ch);
				curl_close($ch);
			}catch(Exception $e){
				$style = file_get_contents($url);
			}
		}else{
			$style = file_get_contents($url);
		}
		
		return trim($style);
	}
	
	function getOuterCss($url, $useOptimize = 'N', $arDopSearch = array(), $arDopReplace = array()){
		if(substr($url,0,2) == '//') $url = 'https:'.$url;
		
		$ufd = UTools::getSetting('use_font_display');
		
		$cacheId = md5($url.$useOptimize.serialize($arDopSearch).serialize($arDopReplace).$ufd);
		$cachePath = '/'.SITE_ID.'/arturgolubev.cssinliner/outer/';
		
		$obCache = new CPHPCache();
		if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath))
		{
			$vars = $obCache->GetVars();
			$style = $vars['style'];
		}
		elseif($obCache->StartDataCache())
		{
			$style = self::_getOuterStyle($url);
			$style = self::baseOptimize($style, $useOptimize, $arDopSearch, $arDopReplace);
			
			$obCache->EndDataCache(array('style' => $style));
		}
		
		return $style;
	}
	
	function getInnerCss($url, $useOptimize = 'N', $arDopSearch = array(), $arDopReplace = array()){
		$tm = filemtime($_SERVER["DOCUMENT_ROOT"].$url);
		
		$ufd = UTools::getSetting('use_font_display');
		
		$cacheId = md5($url.$useOptimize.serialize($arDopSearch).serialize($arDopReplace).$tm.$ufd);
		$cachePath = '/'.SITE_ID.'/arturgolubev.cssinliner/'.basename($url);
		
		$obCache = new CPHPCache();
		if($obCache->InitCache(self::CACHE_TIME, $cacheId, $cachePath))
		{
			$vars = $obCache->GetVars();
			$style = $vars['style'];
		}
		elseif($obCache->StartDataCache())
		{
			$style = file_get_contents($_SERVER['DOCUMENT_ROOT'].$url);
			$style = self::baseOptimize($style, $useOptimize, $arDopSearch, $arDopReplace);
			$obCache->EndDataCache(array('style' => $style));
		}
		
		return $style;
	}
	
	function applyInlineCss($bufferContent){
		preg_match_all('/\<link.*\>/sU', $bufferContent, $arLinks);
		if(!empty($arLinks[0]))
		{
			$rFrom = array();
			$rTo = array();
			
			/* get setting */
			$use_compress = UTools::getSetting('use_compress');
			$inline_max_weight = IntVal(UTools::getSetting('inline_max_weight'));
			$google_fonts_inline = UTools::getSetting('google_fonts_inline');
			$outer_style_inline = UTools::getSetting('outer_style_inline');
			$exceptions = UTools::getSetting('exceptions');
			$del_open_sans = UTools::getSetting('del_open_sans');
			
			/* set default setting */
			if($inline_max_weight <= 0) $inline_max_weight = 1512;
			if($exceptions) $exceptions = explode("\n",$exceptions);
			
			$arLinksWork = array();
			foreach($arLinks[0] as $link)
			{
				if($google_fonts_inline == 'Y' && strstr($link, '//fonts.googleapis.com/css'))
				{
					preg_match_all('/href=[\"\'](.*)[\"\']/sU', $link, $tmphref);
					$cssItemHref = $tmphref[1][0];
					
					if($cssItemHref){
						$style = self::getOuterCss($cssItemHref, $use_compress);
						if($style){
							$rFrom[] = $link;
							$rTo[] = '<style>'.$style.'</style>';
						}
					}
				}
				
				if(!strstr($link, '.css')) continue;
				if(strstr($link, 'media="print"') || strstr($link, "media='print'")) continue;
				if($outer_style_inline != 'Y' && strstr($link, '//')) continue;
				
				if($del_open_sans == 'Y' && strstr($link, '/bitrix/js/ui/fonts/opensans/ui.font.opensans.')){
					$rFrom[] = $link;
					$rTo[] = '';
					continue;
				}
				
				if(is_array($exceptions) && count($exceptions)>0){
					foreach($exceptions as $eLink){
						$eLink = trim($eLink);
						if($eLink && strstr($link, $eLink)) continue(2);
					}
				}
				
				$arLinksWork[] = $link;
			}
			
			if(count($arLinksWork))
			{
				$arPregUrl = array(
					'/url\s?\([\"\']?((?!\'?\"?data:image)(?!http\:)(?!https\:)[\w\.]+.*)[\"\']?\)/sU'
				);
				foreach($arLinksWork as $link){
					unset($style);
					
					preg_match_all('/href=[\"\'](.*\.css).*[\"\']/sU', $link, $tmphref);
					$cssitem = $tmphref[1][0];
					if($cssitem)
					{
						$path = dirname($cssitem).'/';
						$arPregUrlR = array("url('".$path."$1')");
						
						if(strstr($link, 'http://') || strstr($link, 'https://') || substr($cssitem, 0, 2) == '//')
						{
							$style = self::getOuterCss($cssitem, $use_compress, $arPregUrl, $arPregUrlR);
						}
						elseif(file_exists($_SERVER['DOCUMENT_ROOT'].$cssitem))
						{
							$size = (filesize($_SERVER['DOCUMENT_ROOT'].$cssitem) / 1024);
							if($size < $inline_max_weight)
							{
								$style = self::getInnerCss($cssitem, $use_compress, $arPregUrl, $arPregUrlR);
							}
						}
						
						if($style){
							$rFrom[] = $link;
							$rTo[] = '<style>'.$style.'</style>';
						}
					}
				}
			}
			
			if(!empty($rFrom)){
				$bufferContent = str_replace($rFrom, $rTo, $bufferContent);
			}
			
		}
		unset($style);unset($rFrom);unset($rTo);
		unset($arLinks);unset($arLinksWork);
		
		return $bufferContent;
	}
}
?>