<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/predlozheniya_uslug
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoServices extends Avito {
	
	CONST DATE_UPDATED = '2021-08-05';

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
		return parent::getCode().'_SERVICES';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_services.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'STREET',
			'DISPLAY_CODE' => 'Street',
			'NAME' => static::getMessage('FIELD_STREET_NAME'),
			'SORT' => 540,
			'DESCRIPTION' => static::getMessage('FIELD_STREET_DESC'),
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'VALUE' => 'PROPERTY_STREET',
				),
			),
			'PARAMS' => array(
				'MAXLENGTH' => '256',
			),
		));
		#
		$this->modifyField($arResult, 'CATEGORY', array(
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SERVICE_TYPE',
			'DISPLAY_CODE' => 'ServiceType',
			'NAME' => static::getMessage('FIELD_SERVICE_TYPE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_SERVICE_TYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'SERVICE_SUBTYPE',
			'DISPLAY_CODE' => 'ServiceSubtype',
			'NAME' => static::getMessage('FIELD_SERVICE_SUBTYPE_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_SERVICE_SUBTYPE_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSPORT_TYPE',
			'DISPLAY_CODE' => 'TransportType',
			'NAME' => static::getMessage('FIELD_TRANSPORT_TYPE_NAME'),
			'SORT' => 1020,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSPORT_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PURPOSE',
			'DISPLAY_CODE' => 'Purpose',
			'NAME' => static::getMessage('FIELD_PURPOSE_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_PURPOSE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENT_TYPE',
			'DISPLAY_CODE' => 'RentType',
			'NAME' => static::getMessage('FIELD_RENT_TYPE_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_RENT_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRAILER_TYPE',
			'DISPLAY_CODE' => 'TrailerType',
			'NAME' => static::getMessage('FIELD_TRAILER_TYPE_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_TRAILER_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARRYING_CAPACITY',
			'DISPLAY_CODE' => 'CarryingCapacity',
			'NAME' => static::getMessage('FIELD_CARRYING_CAPACITY_NAME'),
			'SORT' => 1060,
			'DESCRIPTION' => static::getMessage('FIELD_CARRYING_CAPACITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAXIMUM_PERMITTED_WEIGHT',
			'DISPLAY_CODE' => 'MaximumPermittedWeight',
			'NAME' => static::getMessage('FIELD_MAXIMUM_PERMITTED_WEIGHT_NAME'),
			'SORT' => 1070,
			'DESCRIPTION' => static::getMessage('FIELD_MAXIMUM_PERMITTED_WEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PLEDGE',
			'DISPLAY_CODE' => 'Pledge',
			'NAME' => static::getMessage('FIELD_PLEDGE_NAME'),
			'SORT' => 1080,
			'DESCRIPTION' => static::getMessage('FIELD_PLEDGE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COMMISSION',
			'DISPLAY_CODE' => 'Commission',
			'NAME' => static::getMessage('FIELD_COMMISSION_NAME'),
			'SORT' => 1090,
			'DESCRIPTION' => static::getMessage('FIELD_COMMISSION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUYOUT',
			'DISPLAY_CODE' => 'Buyout',
			'NAME' => static::getMessage('FIELD_BUYOUT_NAME'),
			'SORT' => 1110,
			'DESCRIPTION' => static::getMessage('FIELD_BUYOUT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DELIVERY',
			'DISPLAY_CODE' => 'Delivery',
			'NAME' => static::getMessage('FIELD_DELIVERY_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_DELIVERY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'RENT_PURPOSE',
			'DISPLAY_CODE' => 'RentPurpose',
			'NAME' => static::getMessage('FIELD_RENT_PURPOSE_NAME'),
			'SORT' => 1130,
			'DESCRIPTION' => static::getMessage('FIELD_RENT_PURPOSE_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXTRA',
			'DISPLAY_CODE' => 'Extra',
			'NAME' => static::getMessage('FIELD_EXTRA_NAME'),
			'SORT' => 1140,
			'DESCRIPTION' => static::getMessage('FIELD_EXTRA_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'WORK_TYPES',
			'DISPLAY_CODE' => 'WorkTypes',
			'NAME' => static::getMessage('FIELD_WORK_TYPES_NAME'),
			'SORT' => 1150,
			'DESCRIPTION' => static::getMessage('FIELD_WORK_TYPES_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'BODY_REPAIR',
			'DISPLAY_CODE' => 'BodyRepair',
			'NAME' => static::getMessage('FIELD_BODY_REPAIR_NAME'),
			'SORT' => 1151,
			'DESCRIPTION' => static::getMessage('FIELD_BODY_REPAIR_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => ['MULTIPLE' => 'multiple'],
		));
		$arResult[] = new Field(array(
			'CODE' => 'WORK_EXPERIENCE',
			'DISPLAY_CODE' => 'WorkExperience',
			'NAME' => static::getMessage('FIELD_WORK_EXPERIENCE_NAME'),
			'SORT' => 1152,
			'DESCRIPTION' => static::getMessage('FIELD_WORK_EXPERIENCE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GUARANTEE',
			'DISPLAY_CODE' => 'Guarantee',
			'NAME' => static::getMessage('FIELD_GUARANTEE_NAME'),
			'SORT' => 1153,
			'DESCRIPTION' => static::getMessage('FIELD_GUARANTEE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MINIMUM_RENTAL_PERIOD',
			'DISPLAY_CODE' => 'MinimumRentalPeriod',
			'NAME' => static::getMessage('FIELD_MINIMUM_RENTAL_PERIOD_NAME'),
			'SORT' => 1154,
			'DESCRIPTION' => static::getMessage('FIELD_MINIMUM_RENTAL_PERIOD_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PLEDGE_AMOUNT',
			'DISPLAY_CODE' => 'PledgeAmount',
			'NAME' => static::getMessage('FIELD_PLEDGE_AMOUNT_NAME'),
			'SORT' => 1155,
			'DESCRIPTION' => static::getMessage('FIELD_PLEDGE_AMOUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COMMISSION_AMOUNT',
			'DISPLAY_CODE' => 'CommissionAmount',
			'NAME' => static::getMessage('FIELD_COMMISSION_AMOUNT_NAME'),
			'SORT' => 1156,
			'DESCRIPTION' => static::getMessage('FIELD_COMMISSION_AMOUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'HEIGHT',
			'DISPLAY_CODE' => 'Height',
			'NAME' => static::getMessage('FIELD_HEIGHT_NAME'),
			'SORT' => 1157,
			'DESCRIPTION' => static::getMessage('FIELD_HEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WIDTH',
			'DISPLAY_CODE' => 'Width',
			'NAME' => static::getMessage('FIELD_WIDTH_NAME'),
			'SORT' => 1158,
			'DESCRIPTION' => static::getMessage('FIELD_WIDTH_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'LENGTH',
			'DISPLAY_CODE' => 'Length',
			'NAME' => static::getMessage('FIELD_LENGTH_NAME'),
			'SORT' => 1159,
			'DESCRIPTION' => static::getMessage('FIELD_LENGTH_DESC'),
		));
		#
		$this->sortFields($arResult);
		return $arResult;
	}
	
	/**
	 *	Process single element (generate XML)
	 *	@return array
	 */
	public function processElement($arProfile, $intIBlockID, $arElement, $arFields){
		$intProfileID = $arProfile['ID'];
		$intElementID = $arElement['ID'];
		# Build XML
		$arXmlTags = array(
			'Id' => array('#' => $arFields['ID']),
		);
		if(!Helper::isEmpty($arFields['DATE_BEGIN']))
			$arXmlTags['DateBegin'] = Xml::addTag($arFields['DATE_BEGIN']);
		if(!Helper::isEmpty($arFields['DATE_END']))
			$arXmlTags['DateEnd'] = Xml::addTag($arFields['DATE_END']);
		if(!Helper::isEmpty($arFields['LISTING_FEE']))
			$arXmlTags['ListingFee'] = Xml::addTag($arFields['LISTING_FEE']);
		if(!Helper::isEmpty($arFields['AD_STATUS']))
			$arXmlTags['AdStatus'] = Xml::addTag($arFields['AD_STATUS']);
		if(!Helper::isEmpty($arFields['AVITO_ID']))
			$arXmlTags['AvitoId'] = Xml::addTag($arFields['AVITO_ID']);
		#
		if(!Helper::isEmpty($arFields['ALLOW_EMAIL']))
			$arXmlTags['AllowEmail'] = Xml::addTag($arFields['ALLOW_EMAIL']);
		if(!Helper::isEmpty($arFields['MANAGER_NAME']))
			$arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
		if(!Helper::isEmpty($arFields['CONTACT_PHONE']))
			$arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
		#
		if(!Helper::isEmpty($arFields['DESCRIPTION']))
			$arXmlTags['Description'] = Xml::addTag($arFields['DESCRIPTION']);
		if(!Helper::isEmpty($arFields['IMAGES']))
			$arXmlTags['Images'] = $this->getXmlTag_Images($arFields['IMAGES']);
		if(!Helper::isEmpty($arFields['VIDEO_URL']))
			$arXmlTags['VideoURL'] = Xml::addTag($arFields['VIDEO_URL']);
		if(!Helper::isEmpty($arFields['TITLE']))
			$arXmlTags['Title'] = Xml::addTag($arFields['TITLE']);
		if(!Helper::isEmpty($arFields['PRICE']))
			$arXmlTags['Price'] = Xml::addTag($arFields['PRICE']);
		#
		if(!Helper::isEmpty($arFields['ADDRESS']))
			$arXmlTags['Address'] = Xml::addTag($arFields['ADDRESS']);
		if(!Helper::isEmpty($arFields['REGION']))
			$arXmlTags['Region'] = Xml::addTag($arFields['REGION']);
		if(!Helper::isEmpty($arFields['CITY']))
			$arXmlTags['City'] = Xml::addTag($arFields['CITY']);
		if(!Helper::isEmpty($arFields['SUBWAY']))
			$arXmlTags['Subway'] = Xml::addTag($arFields['SUBWAY']);
		if(!Helper::isEmpty($arFields['DISTRICT']))
			$arXmlTags['District'] = Xml::addTag($arFields['DISTRICT']);
		if(!Helper::isEmpty($arFields['STREET']))
			$arXmlTags['Street'] = Xml::addTag($arFields['STREET']);
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['SERVICE_TYPE']))
			$arXmlTags['ServiceType'] = Xml::addTag($arFields['SERVICE_TYPE']);
		if(!Helper::isEmpty($arFields['SERVICE_SUBTYPE']))
			$arXmlTags['ServiceSubtype'] = Xml::addTag($arFields['SERVICE_SUBTYPE']);
		#
		if(!Helper::isEmpty($arFields['TRANSPORT_TYPE']))
			$arXmlTags['TransportType'] = Xml::addTag($arFields['TRANSPORT_TYPE']);
		if(!Helper::isEmpty($arFields['PURPOSE']))
			$arXmlTags['Purpose'] = Xml::addTag($arFields['PURPOSE']);
		if(!Helper::isEmpty($arFields['RENT_TYPE']))
			$arXmlTags['RentType'] = Xml::addTag($arFields['RENT_TYPE']);
		if(!Helper::isEmpty($arFields['TRAILER_TYPE']))
			$arXmlTags['TrailerType'] = Xml::addTag($arFields['TRAILER_TYPE']);
		if(!Helper::isEmpty($arFields['CARRYING_CAPACITY']))
			$arXmlTags['CarryingCapacity'] = Xml::addTag($arFields['CARRYING_CAPACITY']);
		if(!Helper::isEmpty($arFields['MAXIMUM_PERMITTED_WEIGHT']))
			$arXmlTags['MaximumPermittedWeight'] = Xml::addTag($arFields['MAXIMUM_PERMITTED_WEIGHT']);
		if(!Helper::isEmpty($arFields['PLEDGE']))
			$arXmlTags['Pledge'] = Xml::addTag($arFields['PLEDGE']);
		if(!Helper::isEmpty($arFields['COMMISSION']))
			$arXmlTags['Commission'] = Xml::addTag($arFields['COMMISSION']);
		if(!Helper::isEmpty($arFields['BUYOUT']))
			$arXmlTags['Buyout'] = Xml::addTag($arFields['BUYOUT']);
		if(!Helper::isEmpty($arFields['DELIVERY']))
			$arXmlTags['Delivery'] = Xml::addTag($arFields['DELIVERY']);
		if(!Helper::isEmpty($arFields['RENT_PURPOSE']))
			$arXmlTags['RentPurpose'] = Xml::addTagWithSubtags($arFields['RENT_PURPOSE'], 'option');
		if(!Helper::isEmpty($arFields['EXTRA']))
			$arXmlTags['Extra'] = Xml::addTagWithSubtags($arFields['EXTRA'], 'option');
		if(!Helper::isEmpty($arFields['WORK_TYPES']))
			$arXmlTags['WorkTypes'] = Xml::addTagWithSubtags($arFields['WORK_TYPES'], 'option');
		if(!Helper::isEmpty($arFields['BODY_REPAIR']))
			$arXmlTags['BodyRepair'] = Xml::addTagWithSubtags($arFields['BODY_REPAIR'], 'option');
		if(!Helper::isEmpty($arFields['WORK_EXPERIENCE']))
			$arXmlTags['WorkExperience'] = Xml::addTag($arFields['WORK_EXPERIENCE']);
		if(!Helper::isEmpty($arFields['GUARANTEE']))
			$arXmlTags['Guarantee'] = Xml::addTag($arFields['GUARANTEE']);
		if(!Helper::isEmpty($arFields['MINIMUM_RENTAL_PERIOD']))
			$arXmlTags['MinimumRentalPeriod'] = Xml::addTag($arFields['MINIMUM_RENTAL_PERIOD']);
		if(!Helper::isEmpty($arFields['PLEDGE_AMOUNT']))
			$arXmlTags['PledgeAmount'] = Xml::addTag($arFields['PLEDGE_AMOUNT']);
		if(!Helper::isEmpty($arFields['COMMISSION_AMOUNT']))
			$arXmlTags['CommissionAmount'] = Xml::addTag($arFields['COMMISSION_AMOUNT']);
		if(!Helper::isEmpty($arFields['HEIGHT']))
			$arXmlTags['Height'] = Xml::addTag($arFields['HEIGHT']);
		if(!Helper::isEmpty($arFields['WIDTH']))
			$arXmlTags['Width'] = Xml::addTag($arFields['WIDTH']);
		if(!Helper::isEmpty($arFields['LENGTH']))
			$arXmlTags['Length'] = Xml::addTag($arFields['LENGTH']);
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
	
}

?>