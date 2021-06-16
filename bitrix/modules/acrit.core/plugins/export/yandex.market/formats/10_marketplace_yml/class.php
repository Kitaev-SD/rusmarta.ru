<?
/**
 * Acrit Core: Yandex.Market YML for FBY, FBY+, FBS
 * @documentation https://yandex.ru/support/marketplace/catalog/yml-requirements.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Export\UniversalPlugin;

class YandexMarketplaceYml extends UniversalPlugin {
	
	const DATE_UPDATED = '2021-06-08';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'yandex_marketplace.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'UAH', 'KZT'];
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;
	protected $bCategoriesUpdate = false;
	protected $bCurrenciesExport = true;
	protected $bCategoriesList = false;
	
	# XML settings
	protected $strXmlItemElement = 'offer';
	protected $intXmlDepthItems = 3;
	
	# Other export settings
	protected $arFieldsWithUtm = ['url'];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		$bNewMode = $this->isNewMode();
		$arResult['HEADER_GENERAL'] = [];
		if($bNewMode){
			$arResult['@id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		}
		else{
			$arResult['shop-sku'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		}
		$arResult['name'] = ['FIELD' => 'NAME', 'PARAMS' => ['ENTITY_DECODE' => 'Y'], 'REQUIRED' => true];
		$arResult['categoryId'] = ['FIELD' => 'IBLOCK_SECTION_ID', 'REQUIRED' => true];
		if($bNewMode){
			$arResult['picture'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true, 'MAX_COUNT' => 10];
		}
		$arResult['vendor'] = ['FIELD' => 'PROPERTY_MANUFACTURER', 'REQUIRED' => true];
		$arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL', 'REQUIRED' => true];
		if($bNewMode){
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'IS_PRICE' => true, 'REQUIRED' => true];
			$arResult['oldprice'] = ['FIELD' => 'CATALOG_PRICE_1', 'IS_PRICE' => true];
			$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_YANDEX'];
		}
		$arResult['currencyId'] = ['FIELD' => 'CATALOG_PRICE_1__CURRENCY', 'IS_CURRENCY' => true, 'REQUIRED' => true];
		$arResult['manufacturer'] = ['FIELD' => ['PROPERTY_MANUFACTURER', 'PROPERTY_BRAND'], 'REQUIRED' => true];
		$arResult['country_of_origin'] = ['FIELD' => 'PROPERTY_COUNTRY', 'REQUIRED' => true];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['vendorCode'] = ['FIELD' => ['PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTNUMBER', 'PROPERTY_ARTICLE'], 'PARAMS' => ['MULTIPLE' => 'first']];
		$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'], 'PARAMS' => ['HTMLSPECIALCHARS' => 'cdata']];
		$arResult['period-of-validity-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-validity-days'] = ['CONST' => static::getMessage('DEFAULT_comment-validity-days')];
		$arResult['service-life-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-life-days'] = ['CONST' => static::getMessage('DEFAULT_comment-life-days')];
		$arResult['warranty-days'] = ['CONST' => 'P1Y2M10D'];
		$arResult['comment-warranty'] = ['CONST' => static::getMessage('DEFAULT_comment-warranty')];
		$arResult['certificate'] = ['CONST' => ''];
		$arResult['dimensions'] = ['CONST' => ['{=catalog.CATALOG_LENGTH} / 10', '{=catalog.CATALOG_WIDTH} / 10', '{=catalog.CATALOG_HEIGHT} / 10'], 'CONST_PARAMS' => ['MATH' => 'Y'], 'PARAMS' => ['MULTIPLE' => 'join', 'MULTIPLE_separator' => 'slash'], 'REQUIRED' => true];
		$arResult['weight'] = ['CONST' => '{=catalog.CATALOG_WEIGHT} / 1000', 'CONST_PARAMS' => ['MATH' => 'Y'], 'REQUIRED' => true];
		$arResult['tn-ved-codes.tn-ved-code'] = ['CONST' => ['1234567890', '1234567891'], 'MULTIPLE' => true];
		#
		$arResult['HEADER_STORES'] = [];
		$arResult['market-sku'] = [];
		$arResult['availability'] = [
			'TYPE' => 'CONDITION',
			'CONDITIONS' => $this->getFieldFilter($intIBlockID, [
				'FIELD' => 'CATALOG_QUANTITY',
				'LOGIC' => 'MORE',
				'VALUE' => '0',
			]),
			'DEFAULT_VALUE' => [
				[
					'TYPE' => 'CONST',
					'CONST' => 'ACTIVE',
					'SUFFIX' => 'Y',
				],
				[
					'TYPE' => 'CONST',
					'CONST' => 'INACTIVE',
					'SUFFIX' => 'N',
				],
			],
		];
		$arResult['transport-unit'] = ['CONST' => '2'];
		$arResult['min-delivery-pieces'] = ['CONST' => '10'];
		$arResult['quantum'] = ['CONST' => '12'];
		$arResult['leadtime'] = ['CONST' => '1'];
		$arResult['box-count'] = ['CONST' => '2'];
		$arResult['delivery-weekdays.delivery-weekday'] = ['CONST' => ['MONDAY', 'FRIDAY'], 'MULTIPLE' => true];
		return $arResult;
	}

	/**
	 * Add settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['ASSORTMENT_MODE'] = require __DIR__.'/settings/assortment_mode.php';
	}

	/**
	 * Check current mode
	 */
	protected function isNewMode(){
		return $this->arParams['ASSORTMENT_MODE'] == 'new';
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="#XML_ENCODING#"?>'.static::EOL;
		$strXml .= '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'.static::EOL;
		$strXml .= '<yml_catalog date="#YML_DATE#">'.static::EOL;
		$strXml .= '	<shop>'.static::EOL;
		$strXml .= '		<categories>'.static::EOL;
		$strXml .= '			#EXPORT_CATEGORIES#'.static::EOL;
		$strXml .= '		</categories>'.static::EOL;
		$strXml .= '		<currencies>'.static::EOL;
		$strXml .= '			#EXPORT_CURRENCIES#'.static::EOL;
		$strXml .= '		</currencies>'.static::EOL;
		$strXml .= '		<offers>'.static::EOL;
		$strXml .= '			#XML_ITEMS#'.static::EOL;
		$strXml .= '		</offers>'.static::EOL;
		$strXml .= '	</shop>'.static::EOL;
		$strXml .= '</yml_catalog>'.static::EOL;
		# Replace macros
		$arReplace = [
			'#YML_DATE#' => date('Y-m-d H:i'),
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

}

?>