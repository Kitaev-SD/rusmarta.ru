<?

/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/appliances
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
    \Bitrix\Main\EventManager,
    \Acrit\Core\Helper,
    \Acrit\Core\Xml,
    \Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoAppliances extends Avito {

   CONST DATE_UPDATED = '2021-03-05';

   protected static $bSubclass = true;

   /**
    * Base constructor
    */
   public function __construct($strModuleId) {
      parent::__construct($strModuleId);
   }

   /* START OF BASE STATIC METHODS */

   /**
    * Get plugin unique code ([A-Z_]+)
    */
   public static function getCode() {
      return parent::getCode() . '_APPLIANCES';
   }

   /**
    * Get plugin short name
    */
   public static function getName() {
      return static::getMessage('NAME');
   }

   /* END OF BASE STATIC METHODS */

   public function getDefaultExportFilename() {
      return 'avito_appliances.xml';
   }

   /**
    * 	Get adailable fields for current plugin
    */
   public function getFields($intProfileID, $intIBlockID, $bAdmin = false) {
      $arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
      #
      $arResult[] = new Field(array(
				'CODE' => 'GOODS_TYPE',
				'DISPLAY_CODE' => 'GoodsType',
				'NAME' => static::getMessage('FIELD_GOODS_TYPE_NAME'),
				'SORT' => 1000,
				'DESCRIPTION' => static::getMessage('FIELD_GOODS_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
				'CODE' => 'CONDITION',
				'DISPLAY_CODE' => 'Condition',
				'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
				'SORT' => 360,
				'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
				'REQUIRED' => true,
      ));
      $arResult[] = new Field(array(
				'CODE' => 'PRODUCTS_TYPE',
				'DISPLAY_CODE' => 'ProductsType',
				'NAME' => static::getMessage('FIELD_PRODUCTS_TYPE_NAME'),
				'SORT' => 370,
				'DESCRIPTION' => static::getMessage('FIELD_PRODUCTS_TYPE_DESC'),
      ));
      $arResult[] = new Field(array(
				'CODE' => 'PRODUCTS_TYPE_MFD',
				'DISPLAY_CODE' => 'ProductsTypeMFD',
				'NAME' => static::getMessage('FIELD_PRODUCTS_TYPE_MFD_NAME'),
				'SORT' => 380,
				'DESCRIPTION' => static::getMessage('FIELD_PRODUCTS_TYPE_MFD_DESC'),
      ));
			if($bAdmin){
				$arResult[] = new Field(array(
					'SORT' => 2000,
					'NAME' => static::getMessage('HEADER_MOBILE'),
					'IS_HEADER' => true,
				));
			}
			$arResult[] = new Field(array(
				'CODE' => 'PHONE_VENDOR',
				'DISPLAY_CODE' => 'Phone -> Vendor',
				'NAME' => static::getMessage('FIELD_PHONE_VENDOR_NAME'),
				'SORT' => 2010,
				'DESCRIPTION' => static::getMessage('FIELD_PHONE_VENDOR_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PHONE_MODEL',
				'DISPLAY_CODE' => 'Phone -> Model',
				'NAME' => static::getMessage('FIELD_PHONE_MODEL_NAME'),
				'SORT' => 2020,
				'DESCRIPTION' => static::getMessage('FIELD_PHONE_MODEL_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PHONE_COLOR',
				'DISPLAY_CODE' => 'Phone -> Color',
				'NAME' => static::getMessage('FIELD_PHONE_COLOR_NAME'),
				'SORT' => 2030,
				'DESCRIPTION' => static::getMessage('FIELD_PHONE_MODEL_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PHONE_MEMORY_SIZE',
				'DISPLAY_CODE' => 'Phone -> MemorySize',
				'NAME' => static::getMessage('FIELD_MEMORY_SIZE_NAME'),
				'SORT' => 2040,
				'DESCRIPTION' => static::getMessage('FIELD_MEMORY_SIZE_DESC'),
			));
			$arResult[] = new Field(array(
				'CODE' => 'PHONE_RAM_SIZE',
				'DISPLAY_CODE' => 'Phone -> RamSize',
				'NAME' => static::getMessage('FIELD_RAM_SIZE_NAME'),
				'SORT' => 2050,
				'DESCRIPTION' => static::getMessage('FIELD_RAM_SIZE_DESC'),
			));
      #
      $this->sortFields($arResult);
      return $arResult;
   }

   /**
    * 	Process single element (generate XML)
    * 	@return array
    */
   public function processElement($arProfile, $intIBlockID, $arElement, $arFields) {
      $intProfileID = $arProfile['ID'];
      $intElementID = $arElement['ID'];
      # Build XML
      $arXmlTags = array(
          'Id' => array('#' => $arFields['ID']),
      );
      if (!Helper::isEmpty($arFields['DATE_BEGIN']))
         $arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
      if (!Helper::isEmpty($arFields['DATE_END']))
         $arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
      if (!Helper::isEmpty($arFields['LISTING_FEE']))
         $arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
      if (!Helper::isEmpty($arFields['AD_STATUS']))
         $arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
      if (!Helper::isEmpty($arFields['AVITO_ID']))
         $arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
      #
      if (!Helper::isEmpty($arFields['ALLOW_EMAIL']))
         $arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
			if(!Helper::isEmpty($arFields['EMAIL']))
				$arXmlTags['Email'] = Xml::addTag($arFields['EMAIL']);
      if (!Helper::isEmpty($arFields['MANAGER_NAME']))
         $arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
      if (!Helper::isEmpty($arFields['CONTACT_PHONE']))
         $arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
			if(!Helper::isEmpty($arFields['CONTACT_METHOD']))
				$arXmlTags['ContactMethod'] = Xml::addTag($arFields['CONTACT_METHOD']);
      #
      if (!Helper::isEmpty($arFields['DESCRIPTION']))
         $arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
      if (!Helper::isEmpty($arFields['IMAGES']))
         $arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
      if (!Helper::isEmpty($arFields['VIDEO_URL']))
         $arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
      if (!Helper::isEmpty($arFields['TITLE']))
         $arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
      if (!Helper::isEmpty($arFields['PRICE']))
         $arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
      if (!Helper::isEmpty($arFields['CONDITION']))
         $arXmlTags['Condition'] = Xml::addTag($arFields['CONDITION']);
      if (!Helper::isEmpty($arFields['PRODUCTS_TYPE']))
         $arXmlTags['ProductsType'] = Xml::addTag($arFields['PRODUCTS_TYPE']);
      if (!Helper::isEmpty($arFields['PRODUCTS_TYPE_MFD']))
         $arXmlTags['ProductsTypeMFD'] = Xml::addTag($arFields['PRODUCTS_TYPE_MFD']);
      #
      if (!Helper::isEmpty($arFields['ADDRESS']))
         $arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
      if (!Helper::isEmpty($arFields['REGION']))
         $arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
      if (!Helper::isEmpty($arFields['CITY']))
         $arXmlTags['City'] = Xml::addTag($arFields['CITY']);
      if (!Helper::isEmpty($arFields['SUBWAY']))
         $arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
      if (!Helper::isEmpty($arFields['DISTRICT']))
         $arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
      #
      if (!Helper::isEmpty($arFields['CATEGORY']))
         $arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
      if (!Helper::isEmpty($arFields['GOODS_TYPE']))
         $arXmlTags['GoodsType'] = Xml::addTag($arFields['GOODS_TYPE']);
      if (!Helper::isEmpty($arFields['PHONE_VENDOR']) && !Helper::isEmpty($arFields['PHONE_MODEL']))
         $arXmlTags['Phone'] = $this->getXmlTag_Phone($arFields);
      # build XML
      $arXml = array(
          'Ad' => array(
              '#' => $arXmlTags,
          ),
      );
      foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoXml') as $arHandler) {
         ExecuteModuleEventEx($arHandler, array(&$arXml, $arProfile, $intIBlockID, $arElement, $arFields));
      }
      $strXml = Xml::arrayToXml($arXml);
      # build result
      $arResult = array(
          'TYPE' => 'XML',
          'DATA' => $strXml,
          'CURRENCY' => '',
          'SECTION_ID' => static::getElement_SectionID($intProfileID, $arElement),
          'ADDITIONAL_SECTIONS_ID' => Helper::getElementAdditionalSections($intElementID, $arElement['IBLOCK_SECTION_ID']),
          'DATA_MORE' => array(),
      );
      foreach (EventManager::getInstance()->findEventHandlers($this->strModuleId, 'OnAvitoResult') as $arHandler) {
         ExecuteModuleEventEx($arHandler, array(&$arResult, $arXml, $arProfile, $intIBlockID, $arElement, $arFields));
      }
      # after..
      unset($intProfileID, $intElementID, $arXmlTags, $arXml);
      return $arResult;
   }
	
	/**
		* 	Get XML tag: <Phone>
		*/
	protected function getXmlTag_Phone($arFields) {
		$arResult = [];
		$arResult['Vendor'] = Xml::addTag($arFields['PHONE_VENDOR']);
		$arResult['Model'] = Xml::addTag($arFields['PHONE_MODEL']);
		if(!Helper::isEmpty($arFields['PHONE_COLOR']))
			$arResult['Color'] = Xml::addTag($arFields['PHONE_COLOR']);
		if(!Helper::isEmpty($arFields['PHONE_MEMORY_SIZE']))
			$arResult['MemorySize'] = Xml::addTag($arFields['PHONE_MEMORY_SIZE']);
		if(!Helper::isEmpty($arFields['PHONE_RAM_SIZE']))
			$arResult['RamSize'] = Xml::addTag($arFields['PHONE_RAM_SIZE']);
		$arResult = [
			['#' => $arResult]
		];
		return $arResult;
	}

}

?>