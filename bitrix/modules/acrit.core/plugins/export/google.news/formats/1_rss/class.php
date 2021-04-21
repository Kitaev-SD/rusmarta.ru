<?
/**
 * Acrit Core: Google.news plugin
 * @documentation https://support.google.com/news/publisher-center/answer/9545420?hl=ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class GoogleNewsRss extends GoogleNews {
	
	const DATE_UPDATED = '2021-02-25';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'google_news_rss.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	
	# Basic settings
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	
	# XML settings
	protected $strXmlItemElement = 'item';
	protected $intXmlDepthItems = 2;
	
	# Other export settings
	protected $bZip = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['guid'] = ['FIELD' => 'ID'];
		$arResult['guid@isPermaLink'] = ['CONST' => 'false'];
		$arResult['pubDate'] = ['FIELD' => 'DATE_CREATE', 'FIELD_PARAMS' => [
			'DATEFORMAT' => 'Y',
			'DATEFORMAT_from' => '#DATETIME#',
			'DATEFORMAT_to' => 'r',
		]];
		$arResult['title'] = ['FIELD' => 'NAME'];
		$arResult['description'] = ['FIELD' => 'PREVIEW_TEXT', 'FIELD_PARAMS' => [
			'HTMLSPECIALCHARS' => 'cut',
		]];
		$arResult['content:encoded'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => [
			'HTMLSPECIALCHARS' => 'skip',
		], 'PARAMS' => [
			'HTMLSPECIALCHARS' => 'cdata',
		]];
		$arResult['link'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['author'] = ['FIELD' => 'PROPERTY_AUTHOR'];
		#
		return $arResult;
	}

	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['XML_TITLE'] = $this->getUpSettingsTitle();
		$arSettings['XML_DESCRIPTION'] = $this->getUpSettingsDescription();
		$arSettings['XML_LINK'] = $this->getUpSettingsLink();
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
	 *	Settings: link
	 */
	protected function getUpSettingsLink(){
		ob_start();
		?>
		<input type="text" name="PROFILE[PARAMS][XML_LINK]" size="40" maxlength="255" spellcheck="false"
			value="<?=htmlspecialcharsbx($this->arParams['XML_LINK']);?>" />
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:media="http://search.yahoo.com/mrss/">'.static::EOL;
		$strXml .= '	<channel>'.static::EOL;
		$strXml .= '		<lastBuildDate>#XML_GENERATION_DATE#</lastBuildDate>'.static::EOL;
		$strXml .= '		<title>#XML_TITLE#</title>'.static::EOL;
		$strXml .= '		<description>#XML_DESCRIPTION#</description>'.static::EOL;
		$strXml .= '		<link>#XML_LINK#</link>'.static::EOL;
		$strXml .= '		#XML_ITEMS#'.static::EOL;
		$strXml .= '	</channel>'.static::EOL;
		$strXml .= '</rss>'.static::EOL;
		# Prepare URL
		$strUrl = $this->arParams['XML_LINK'];
		if(!preg_match('#^http[s]?://#i', $strUrl)){
			$strUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y', 
				substr($strUrl, 0, 1) == '/' ? $strUrl : '/'.$strUrl);
		}
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_GENERATION_DATE#' => date('r'),
			'#XML_TITLE#' => htmlspecialcharsbx($this->arParams['XML_TITLE']),
			'#XML_DESCRIPTION#' => htmlspecialcharsbx($this->arParams['XML_DESCRIPTION']),
			'#XML_LINK#' => htmlspecialcharsbx($strUrl),
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