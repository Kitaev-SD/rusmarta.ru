<?
namespace Aspro\Next;
use CNext as Solution;
use Bitrix\Main\Config\Option,
	Bitrix\Main\Localization\Loc;

class PWA {
	const MODULE_CLASS = 'CNext';

	public static function showMeta($siteId = false){
		if(!$siteId){
			if(defined('SITE_ID')){
				$siteId = SITE_ID;
			}
		}

		if($siteId){
			if(Option::get(Solution::moduleID, 'PWA_USE', 'N', $siteId) === 'Y'){
				$path = self::_getPath($siteId);
				$GLOBALS['APPLICATION']->AddHeadString('<link rel="manifest" href="/'.str_replace($_SERVER['DOCUMENT_ROOT'].'/', '', $path).'">', true);

				$arIcons = array();
				foreach(array(
					36, 48, 72, 96, 144, 192
				) as $size){
					if($value = Option::get(Solution::moduleID, 'PWA_ICON_'.$size, 'a:0:{}', $siteId)){
						if($arIcon = unserialize($value)){
							$arIcons[] = array(
								'id' => $arIcon[0],
								'size' => $size.'x'.$size,
							);
						}
					}
				}
				if($arIcons){
					\Bitrix\Main\Loader::includeModule('fileman');

					foreach($arIcons as $arIcon){
						if($path = \CFile::GetPath($arIcon['id'])){
							$GLOBALS['APPLICATION']->AddHeadString('<link rel="apple-touch-icon" sizes="'.$arIcon['size'].'" href="'.str_replace($_SERVER['DOCUMENT_ROOT'], '', $path).'">', true);
						}
					}
				}
			}
		}
	}

	public static function generate($siteId){
		$arData = array();

		if($siteId){
			$arValues = self::getValues($siteId);

			if($arValues['PWA_USE'] === 'Y'){
				if($arValues['PWA_NAME']){
					$arData['name'] = $arValues['PWA_NAME'];
				}

				if($arValues['PWA_SHORT_NAME']){
					$arData['short_name'] = $arValues['PWA_SHORT_NAME'];
				}

				if($arValues['PWA_START_URL']){
					$arData['start_url'] = $arValues['PWA_START_URL'];
				}

				if($arValues['PWA_DISPLAY']){
					$arData['display'] = $arValues['PWA_DISPLAY'];
				}

				if($arValues['PWA_BACKGROUND_COLOR']){
					$arData['background_color'] = '#'.$arValues['PWA_BACKGROUND_COLOR'];
				}

				if($arValues['PWA_THEME_COLOR']){
					$arData['theme_color'] = '#'.$arValues['PWA_THEME_COLOR'];
				}

				$arData['orientation'] = 'natural';

				$dbRes = \CSite::GetList(($by = 'id'), ($sort = 'asc'), array('ACTIVE' => 'Y', 'LID' => $siteId));
				if($arSite = $dbRes->Fetch()){
					$arData['lang'] = $arSite['LANGUAGE_ID'];
				}

				\Bitrix\Main\Loader::includeModule('fileman');
				$arIcons = array();
				foreach(array(
					36, 48, 72, 96, 144, 192
				) as $size){
					if($arValues['PWA_ICON_'.$size]){
						if($arIcon = unserialize($arValues['PWA_ICON_'.$size])){
							if($path = \CFile::GetPath($arIcon[0])){
								$arIcons[] = array(
									'src' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $path),
									'sizes' => $size.'x'.$size,
									'type' => 'image/png',
									'density' => number_format($size / 48, 2, '.', ''),
								);
							}
						}
					}
				}
				if($arIcons){
					$arData['icons'] = $arIcons;
				}

				if($filepath = self::_getPath($siteId)){
					self::_backup($siteId);
					@file_put_contents($filepath, self::_beautify(\Bitrix\Main\Web\Json::encode($arData)));
				}
			}
		}

		return $arData;
	}

	protected static function _getPath($siteId){
		$filepath = '';

		if($siteId){
			$dbRes = \CSite::GetList(($by = 'id'), ($sort = 'asc'), array('ACTIVE' => 'Y', 'LID' => $siteId));
			if($arItem = $dbRes->Fetch()){
				$arSite = $arItem;
				if(!strlen($arSite['DOC_ROOT'])){
					$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
				}
			}

			$siteDir = rtrim(preg_replace('/\/+/', '/', $arSite['DOC_ROOT'].'/'.$arSite['DIR'].'/'), '/');

			$arParams = self::getParams();
			$filenameDeafult = $arParams['PWA']['OPTIONS']['PWA_FILENAME']['DEFAULT'];
			$filename = Option::get(Solution::moduleID, 'PWA_FILENAME', $filenameDeafult, $siteId);
			$filename = preg_replace('/[^a-z\.\_\-\d]/i'.BX_UTF_PCRE_MODIFIER, '', $filename);
			$name = basename($filename, '.json');
			$filename = (strlen($name) ? $name.'.json' : $filenameDeafult);
			$filepath = $siteDir.'/'.$filename;
		}

		return $filepath;
	}

	protected static function _backup($siteId){
		if($filepath = self::_getPath($siteId)){
			$content = @file_get_contents($filepath);
			if(strlen($content)){
				@file_put_contents($filepath.'.back', $content);
			}
		}
	}

	protected static function _beautify($content){
		$content = str_replace(array('{', '[', '}', ']', ',', '":'), array('{'.PHP_EOL, '['.PHP_EOL, PHP_EOL.'}', PHP_EOL.']', ','.PHP_EOL, '": '), $content);
		return $content;
	}

	public static function getValues($siteId){
		$arValues = array();

		if($siteId){
			$arSite = array();
			$dbRes = \CSite::GetList(($by = 'id'), ($sort = 'asc'), array('ACTIVE' => 'Y', 'LID' => $siteId));
			if($arItem = $dbRes->Fetch()){
				$arSite = $arItem;
			}
		}

		$moduleClass = self::MODULE_CLASS;

		foreach(PWA::getParams() as $blockCode => $arBlock){
			foreach($arBlock['OPTIONS'] as $optionCode => $arOption){
				$optionType = $arOption['TYPE'];
				$optionTypeExt = array_key_exists('TYPE_EXT', $arOption) ? $arOption['TYPE_EXT'] : false;

				if($optionType !== 'note'){
					$optionDefault = $arOption['DEFAULT'];

					if($optionCode === 'PWA_NAME' || $optionCode === 'PWA_SHORT_NAME'){
						$optionDefault = $arSite['NAME'];
					}
					elseif($optionCode === 'PWA_START_URL'){
						$optionDefault = preg_replace('/\/+/', '/', '/'.$arSite['DIR'].'/');
					}
					elseif($optionTypeExt === 'colorpicker'){
						$baseColor = Option::get(Solution::moduleID, 'BASE_COLOR', $moduleClass::$arParametrsList['MAIN']['OPTIONS']['BASE_COLOR']['DEFAULT'], $siteId);
						if($baseColor === 'CUSTOM'){
							$colorValue = Option::get(Solution::moduleID, 'BASE_COLOR_CUSTOM', $moduleClass::$arParametrsList['MAIN']['OPTIONS']['BASE_COLOR_CUSTOM']['DEFAULT'], $siteId);
						}
						else{
							$colorValue = ltrim($moduleClass::$arParametrsList['MAIN']['OPTIONS']['BASE_COLOR']['LIST'][$baseColor]['COLOR'], '#');
						}

						$optionDefault = $colorValue;
					}

					$optionVal = Option::get(Solution::moduleID, $optionCode, $optionDefault, $siteId);

					// all text values are required
					if($optionType === 'text'){
						if(!strlen($optionVal)){
							$optionVal = $optionDefault;
						}
					}
					elseif($optionType === 'selectbox' && $arOption['LIST']){
						if(!array_key_exists($optionVal, $arOption['LIST'])){
							$optionVal = $optionDefault;
						}
					}
					if($optionType === 'multiselectbox' && $arOption['LIST']){
						$arValues = explode(',', $optionVal);
						if(!$arValues){
							$arValues = array();
						}
						foreach($arValues as $i => $val){
							if(!array_key_exists($val, $arOption['LIST'])){
								unset($arValues[$i]);
							}
						}
						if(!$arValues){
							$arValues = array();
						}
						$arValues = array_values($arValues);
						$optionVal = implode(',', $arValues);
					}

					if($optionCode === 'PWA_FILENAME'){
						$optionVal = preg_replace('/[^a-z\.\_\-\d]/i'.BX_UTF_PCRE_MODIFIER, '', $optionVal);
						$name = basename($optionVal, '.json');
						$optionVal = (strlen($name) ? $name.'.json' : $optionDefault);
					}

					$arValues[$optionCode] = $optionVal;
				}
			}
		}

		return $arValues;
	}

	public static function getParams(){
		static $arParams;

		if(!isset($arParams)){
			$arParams = array(
				'PWA' => array(
					'TITLE' => GetMessage('PWA_OPTIONS'),
					'THEME' => 'N',
					'OPTIONS' => array(
						'PWA_USE' => array(
							'TITLE' => GetMessage('PWA_USE_TITLE'),
							'TYPE' => 'checkbox',
							'DEFAULT' => 'N',
						),
						'PWA_FILENAME' => array(
							'TITLE' => GetMessage('PWA_FILENAME_TITLE'),
							'TYPE' => 'text',
							'DEFAULT' => 'manifest.json',
						),
						'PWA_NAME' => array(
							'TITLE' => GetMessage('PWA_NAME_TITLE'),
							'TYPE' => 'text',
							'DEFAULT' => '',
						),
						'PWA_SHORT_NAME' => array(
							'TITLE' => GetMessage('PWA_SHORT_NAME_TITLE'),
							'TYPE' => 'text',
							'DEFAULT' => '',
						),
						'PWA_START_URL' => array(
							'TITLE' => GetMessage('PWA_START_URL_TITLE'),
							'TYPE' => 'text',
							'DEFAULT' => '',
						),
						'PWA_DISPLAY' => array(
							'TITLE' => GetMessage('PWA_DISPLAY_TITLE'),
							'TYPE' => 'selectbox',
							'LIST' => array(
								'fullscreen' => GetMessage('PWA_DISPLAY_FULLSCREEN'),
								'standalone' => GetMessage('PWA_DISPLAY_STANDALONE'),
								'browser' => GetMessage('PWA_DISPLAY_BROWSER'),
							),
							'DEFAULT' => 'browser',
						),
						'PWA_BACKGROUND_COLOR' => array(
							'TITLE' => GetMessage('PWA_BACKGROUND_COLOR_TITLE'),
							'TYPE' => 'text',
							'TYPE_EXT' => 'colorpicker',
							'DEFAULT' => '',
						),
						'PWA_THEME_COLOR' => array(
							'TITLE' => GetMessage('PWA_THEME_COLOR_TITLE'),
							'TYPE' => 'text',
							'TYPE_EXT' => 'colorpicker',
							'DEFAULT' => '',
						),
						'PWA_ICON_36' => array(
							'TITLE' => GetMessage('PWA_ICON_36_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
						'PWA_ICON_48' => array(
							'TITLE' => GetMessage('PWA_ICON_48_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
						'PWA_ICON_72' => array(
							'TITLE' => GetMessage('PWA_ICON_72_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
						'PWA_ICON_96' => array(
							'TITLE' => GetMessage('PWA_ICON_96_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
						'PWA_ICON_144' => array(
							'TITLE' => GetMessage('PWA_ICON_144_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
						'PWA_ICON_192' => array(
							'TITLE' => GetMessage('PWA_ICON_192_TITLE'),
							'TYPE' => 'file',
							'DEFAULT' => serialize(array()),
						),
					),
				),
			);
		}

		return $arParams;
	}
}
?>