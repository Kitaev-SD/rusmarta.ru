<?
/**
 * Acrit Core: Google.news plugin
 * @documentation https://support.google.com/news/publisher-center/answer/9545420?hl=ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class GoogleNewsAtom extends GoogleNews {
	
	const DATE_UPDATED = '2021-02-25';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'google_news_atom.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	
	# Basic settings
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	
	# XML settings
	protected $strXmlItemElement = 'entry';
	protected $intXmlDepthItems = 1;
	
	# Other export settings
	protected $bZip = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['id'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['published'] = ['FIELD' => 'DATE_CREATE', 'FIELD_PARAMS' => [
			'DATEFORMAT' => 'Y',
			'DATEFORMAT_from' => '#DATETIME#',
			'DATEFORMAT_to' => 'c',
		]];
		$arResult['updated'] = ['FIELD' => 'TIMESTAMP_X', 'FIELD_PARAMS' => [
			'DATEFORMAT' => 'Y',
			'DATEFORMAT_from' => '#DATETIME#',
			'DATEFORMAT_to' => 'c',
		]];
		$arResult['title'] = ['FIELD' => 'NAME'];
		$arResult['title@type'] = ['CONST' => 'text'];
		$arResult['content'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => [
			'HTMLSPECIALCHARS' => 'skip',
		], 'PARAMS' => [
			'HTMLSPECIALCHARS' => 'cdata',
		]];
		$arResult['content@type'] = ['CONST' => 'html'];
		$arResult['author.name'] = ['FIELD' => 'PROPERTY_AUTHOR_NAME'];
		$arResult['author.email'] = ['FIELD' => 'PROPERTY_AUTHOR_EMAIL'];
		
		#
		return $arResult;
	}

	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['XML_TITLE'] = $this->getUpSettingsTitle();
		$arSettings['XML_DESCRIPTION'] = $this->getUpSettingsDescription();
	}
	
	/**
	 *	Settings: title
	 */
	protected function getUpSettingsTitle(){
		ob_start();
		?>
		<input type="text" name="PROFILE[PARAMS][XML_TITLE]" size="40" maxlength="255" spellcheck="false"
			value="<?=htmlspecialcharsbx($this->arParams['XML_TITLE']);?>" />
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Settings: description
	 */
	protected function getUpSettingsDescription(){
		ob_start();
		?>
		<textarea name="PROFILE[PARAMS][XML_DESCRIPTION]" cols="60" rows="3" spellcheck="false"
			style="max-height:500px; resize:vertical;"
			><?=htmlspecialcharsbx($this->arParams['XML_DESCRIPTION']);?></textarea>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<feed xmlns="http://www.w3.org/2005/Atom">'.static::EOL;
		$strXml .= '	<id>#XML_ID#</id>'.static::EOL;
		$strXml .= '	<updated>#XML_GENERATION_DATE#</updated>'.static::EOL;
		$strXml .= '	<title type="text">#XML_TITLE#</title>'.static::EOL;
		$strXml .= '	<subtitle type="html">#XML_DESCRIPTION#</subtitle>'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</feed>'.static::EOL;
		
		# Prepare URL
		$strUrl = $this->arParams['XML_LINK'];
		if(!preg_match('#^http[s]?://#i', $strUrl)){
			$strUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y', 
				substr($strUrl, 0, 1) == '/' ? $strUrl : '/'.$strUrl);
		}
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_ID#' => htmlspecialcharsbx(Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y')),
			'#XML_GENERATION_DATE#' => date('c'),
			'#XML_TITLE#' => htmlspecialcharsbx($this->arParams['XML_TITLE']),
			'#XML_DESCRIPTION#' => htmlspecialcharsbx($this->arParams['XML_DESCRIPTION']),
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

	/**
	 *	Handler on generate json for single item
	 */
	/*
	protected function onUpBuildXml(&$arXmlTags, &$arXmlAttr, &$strXmlItem, &$arElement, &$arFields, &$arElementSections, $mDataMore){
		#P($arFields);
	}
	*/

}

?>