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

class FacebookRealty extends UniversalPlugin {

   CONST DATE_UPDATED = '2021-09-15';

   protected static $bSubclass = true;
   # General
   protected $strDefaultFilename = 'facebook_realty.xml';
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
      $arResult['home_listing_id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
      $arResult['home_listing_group_id'] = ['FIELD' => 'ID'];
      $arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
      $arResult['availability'] = [
          'ALLOWED_VALUES' => ['for_sale', 'for_rent', 'sale_pending', 'recently_sold', 'off_market', ' available_soon'],
          'REQUIRED' => true
      ];
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
      /* $arResult['address.city'] = [];
        $arResult['address.region'] = [];
        $arResult['address.country'] = [];
        $arResult['address.postal_code'] = []; */
      $arResult['latitude'] = ['REQUIRED' => true];
      $arResult['longitude'] = ['REQUIRED' => true];
      $arResult['neighborhood'] = ['MULTIPLE' => true, 'MAX_COUNT' => 20];
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
      $arResult['url'] = ['FIELD' => 'DETAIL_PAGE_URL', 'REQUIRED' => true];
      $arResult['description'] = [
          'FIELD' => 'DETAIL_TEXT', 'CDATA' => true, 'FIELD_PARAMS' => ['HTMLSPECIALCHARS' => 'skip'],
          'PARAMS' => ['HTMLSPECIALCHARS' => 'cdata']];
      ;
      $arResult['num_beds'] = [];
      $arResult['num_baths'] = [];
      $arResult['num_rooms'] = [];
      $arResult['property_type'] = [
          'ALLOWED_VALUES' => ['apartment', 'condo', 'house', 'land', 'manufactured', 'other', 'townhouse', 'house_in_condominium', 'house_in_villa', 'loft', 'penthouse', 'studio', 'townhouse', 'other'],
      ];
      $arResult['listing_type'] = [
          'ALLOWED_VALUES' => ['for_rent_by_agent', 'for_rent_by_owner', 'for_sale_by_agent', 'for_sale_by_owner', 'foreclosed', 'new_construction', 'new_listing'],
      ];
      $arResult['area_size'] = [];
      $arResult['area_unit'] = [
          'ALLOWED_VALUES' => ['sq_ft', 'sq_m'],
      ];
      $arResult['ac_type'] = [
          'ALLOWED_VALUES' => ['central', 'other', 'none'],
      ];
      $arResult['furnish_type'] = [
          'ALLOWED_VALUES' => ['furnished', 'semi-furnished', 'unfurnished'],
      ];
      $arResult['heating_type'] = [
          'ALLOWED_VALUES' => ['central', 'gas', 'electric', 'radiator', 'other', 'none'],
      ];
      $arResult['laundry_type'] = [
          'ALLOWED_VALUES' => ['in_unit', 'in_building', 'other', 'none'],
      ];
      $arResult['num_units'] = [];
      $arResult['parking_type'] = [
          'ALLOWED_VALUES' => ['garage', 'street', 'off-street', 'other', 'none'],
      ];
      $arResult['partner_verification'] = [
          'ALLOWED_VALUES' => ['verified', 'none'],
      ];
      $arResult['year_built'] = [];
      $arResult['pet_policy'] = [
          'ALLOWED_VALUES' => ['cat', 'dog', 'all', 'none'],
      ];

      $arResult['HEADER_AVAILABLE_DATES_PRICE_CONFIG'] = [];
      $arResult['available_dates_price_config.start_date'] = [];
      $arResult['available_dates_price_config.end_date'] = [];
      $arResult['available_dates_price_config.rate'] = [];
      $arResult['available_dates_price_config.currency'] = [];
      $arResult['available_dates_price_config.interval'] = [
          'ALLOWED_VALUES' => ['nightly', 'weekly', 'monthly', 'sale']
      ];
      $arResult['applink'] = [];


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