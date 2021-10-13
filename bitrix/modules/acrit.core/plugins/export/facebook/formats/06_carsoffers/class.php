<?

/**
 * Acrit Core: Facebook plugin
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Export\UniversalPlugin,
    \Acrit\Core\Export\Plugin,
    \Acrit\Core\Export\Field\Field,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Export\Filter,
    \Acrit\Core\Export\Exporter,
    \Acrit\Core\Export\ExportDataTable as ExportData,
    \Acrit\Core\Log,
    \Acrit\Core\Xml;

Loc::loadMessages(__FILE__);

class FacebookCarsOffers extends UniversalPlugin {

   CONST DATE_UPDATED = '2021-09-20';

   protected static $bSubclass = true;
   # General
   protected $strDefaultFilename = 'facebook_cars_offers.xml';
   protected $arSupportedFormats = ['XML'];
   protected $arSupportedEncoding = [self::UTF8];
   protected $strFileExt = 'xml';
   protected $arSupportedCurrencies = ['RUB', 'RUR', 'USD', 'UAH', 'KZT'];
   # XML settings
   protected $strXmlItemElement = 'listing';
   protected $intXmlDepthItems = 1;

   /**
    * 	Get available fields for current plugin
    */
   public function getUniversalFields($intProfileId, $intIBlockId) {
      $arResult = [];
      $arResult['vehicle_offer_id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
      $arResult['make'] = ['REQUIRED' => true];
      $arResult['model'] = ['REQUIRED' => true];
      $arResult['year'] = ['REQUIRED' => true];
      $arResult['offer_type'] = ['REQUIRED' => true];
      $arResult['title'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
      $arResult['offer_description'] = [
          'FIELD' => 'DETAIL_TEXT', 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'],
          'REQUIRED' => true
      ];
      $arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL', 'REQUIRED' => true];

      $arResult['offer_disclaimer'] = ['REQUIRED' => true];


      $arResult['image.url'] = [
          'FIELD' => 'PARENT.DETAIL_PICTURE',
          'MULTIPLE' => true,
          'MAX_COUNT' => 20,
          'REQUIRED' => true
      ];
      $arResult['image.tag'] = [
          'MULTIPLE' => true,
          'PARAMS' => array(
              'MULTIPLE' => 'join',
              'MULTIPLE_separator' => 'comma'
          )
      ];
      $arResult['amount_price'] = [];
      $arResult['amount_percentage'] = [];
      $arResult['amount_qualifier'] = [];
      $arResult['term_length'] = [];
      $arResult['term_qualifier'] = [];
      $arResult['downpayment'] = [];
      $arResult['downpayment_qualifier'] = [];
      $arResult['trim'] = [];

      $arResult['price'] = [
          'FIELD' => 'CATALOG_PRICE_1',
          'MULTIPLE' => false,
          'REQUIRED' => true,
          'DEFAULT_VALUE' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'CATALOG_PRICE_1',
              ),
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
              ),
          ),
          'DEFAULT_VALUE_OFFERS' => array(
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'CATALOG_PRICE_1',
              ),
              array(
                  'TYPE' => 'FIELD',
                  'VALUE' => 'CATALOG_PRICE_1__CURRENCY',
              ),
          ),
          'PARAMS' => array(
              'MULTIPLE' => 'join',
              'MULTIPLE_separator' => 'space'
          ),
      ];
      $arResult['body_style'] = [];
      $arResult['start_date'] = [];
      $arResult['end_date'] = [];
      $arResult['market_name'] = [];
      $arResult['dma_codes'] = [];
      /*
        $arResult['address@format'] = [
        'DEFAULT_VALUE' => [
        array(
        'TYPE' => 'CONST',
        'CONST' => 'simple'
        )]
        ];
        $arResult['address.component@name'] = [
        'MULTIPLE' => true,
        'DEFAULT_VALUE' => array(
        ['CONST' => 'addr1'],
        ['CONST' => 'city'],
        ['CONST' => 'region'],
        ['CONST' => 'country'],
        ['CONST' => 'postal_code'],
        ),];
        $arResult['address.component'] = [
        'MULTIPLE' => true,
        'DEFAULT_VALUE' => array(
        ['FIELD' => ''],
        ['FIELD' => ''],
        ['FIELD' => ''],
        ['FIELD' => ''],
        ['FIELD' => ''],
        ),
        'REQUIRED' => true];

       */

      return $arResult;
   }

   protected function onUpGetXmlStructure(&$strXml) {
      # Build xml
      $strXml = '<?xml version="1.0" encoding="UTF-8"?>' . static::EOL;
      $strXml .= '<listings>' . static::EOL;
      $strXml .= '	<title>#TITLE#</title>' . static::EOL;
      $strXml .= '	<link rel="self" href="#SITE_URL#"/>' . static::EOL;
      $strXml .= '	#XML_ITEMS#' . static::EOL;
      $strXml .= '</listings>' . static::EOL;
      # Replace macros
      $arReplace = [
          '#TITLE#' => $this->arParams['TITLE'],
          '#SITE_URL#' => Helper::siteUrl($this->arData['PROFILE']['DOMAIN'], $this->arData['PROFILE']['IS_HTTPS'] == 'Y'),
      ];
      $strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
   }

   protected function onUpShowSettings(&$arSettings) {
      $arSettings['TITLE'] = $this->includeHtml(__DIR__ . '/include/settings/title.php');
   }

}

?>