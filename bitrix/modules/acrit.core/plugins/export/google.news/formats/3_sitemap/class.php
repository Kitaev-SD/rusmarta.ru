<?
/**
 * Acrit Core: Google.news plugin
 * @documentation https://support.google.com/news/publisher-center/answer/9606710?hl=ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

class GoogleNewsSitemap extends GoogleNews {
	
	const DATE_UPDATED = '2021-02-25';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'google_news_sitemap.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	
	# Basic settings
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	
	# XML settings
	protected $strXmlItemElement = 'url';
	protected $intXmlDepthItems = 1;
	
	# Other export settings
	protected $bZip = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['loc'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['news:news.news:publication.news:name'] = ['FIELD' => 'PROPERTY_PUBLISHER'];
		$arResult['news:news.news:publication.news:language'] = ['CONST' => LANGUAGE_ID];
		$arResult['news:news.news:publication_date'] = ['FIELD' => 'DATE_CREATE', 'FIELD_PARAMS' => [
			'DATEFORMAT' => 'Y',
			'DATEFORMAT_from' => '#DATETIME#',
			'DATEFORMAT_to' => 'c',
		]];
		$arResult['news:news.news:title'] = ['FIELD' => 'NAME'];
		
		#
		return $arResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="UTF-8"?>'.static::EOL;
		$strXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</urlset>'.static::EOL;
		
		# Prepare URL
		$strUrl = $this->arParams['XML_LINK'];
		if(!preg_match('#^http[s]?://#i', $strUrl)){
			$strUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y', 
				substr($strUrl, 0, 1) == '/' ? $strUrl : '/'.$strUrl);
		}
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_ID#' => Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y'),
			'#XML_GENERATION_DATE#' => date('c'),
			'#XML_TITLE#' => $this->arParams['XML_TITLE'],
			'#XML_DESCRIPTION#' => $this->arParams['XML_DESCRIPTION'],
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