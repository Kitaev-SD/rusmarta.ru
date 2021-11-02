<?
/**
 * Acrit Core: Avito plugin
 * @documentation http://autoload.avito.ru/format/gruzoviki_i_spetstehnika
 */

namespace Acrit\Core\Export\Plugins;

use \Bitrix\Main\Localization\Loc,
	\Bitrix\Main\EventManager,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Export\Field\Field;

Loc::loadMessages(__FILE__);

class AvitoTrucks extends Avito {
	
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
		return parent::getCode().'_TRUCKS';
	}
	
	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return static::getMessage('NAME');
	}
	
	/* END OF BASE STATIC METHODS */
	
	public function getDefaultExportFilename(){
		return 'avito_trucks.xml';
	}

	/**
	 *	Get adailable fields for current plugin
	 */
	public function getFields($intProfileID, $intIBlockID, $bAdmin=false){
		$arResult = parent::getFields($intProfileID, $intIBlockID, $bAdmin);
		#
		$arResult[] = new Field(array(
			'CODE' => 'DISPLAY_AREAS',
			'DISPLAY_CODE' => 'DisplayAreas',
			'NAME' => static::getMessage('FIELD_DISPLAY_AREAS_NAME'),
			'SORT' => 350,
			'DESCRIPTION' => static::getMessage('FIELD_DISPLAY_AREAS_DESC'),
			'MULTIPLE' => true,
			'PARAMS' => array(
				'MULTIPLE' => 'multiple',
			),
		));
		$this->modifyField($arResult, 'CATEGORY', array(
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_CATEGORY_DEFAULT'),
				),
			),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GOODS_TYPE',
			'DISPLAY_CODE' => 'GoodsType',
			'NAME' => static::getMessage('FIELD_GOODS_TYPE_NAME'),
			'SORT' => 980,
			'DESCRIPTION' => static::getMessage('FIELD_GOODS_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONDITION',
			'DISPLAY_CODE' => 'Condition',
			'NAME' => static::getMessage('FIELD_CONDITION_NAME'),
			'SORT' => 990,
			'DESCRIPTION' => static::getMessage('FIELD_CONDITION_DESC'),
			'REQUIRED' => true,
		));
		$arResult[] = new Field(array(
			'CODE' => 'KILOMETRAGE',
			'DISPLAY_CODE' => 'Kilometrage',
			'NAME' => static::getMessage('FIELD_KILOMETRAGE_NAME'),
			'SORT' => 991,
			'DESCRIPTION' => static::getMessage('FIELD_KILOMETRAGE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TECHNICAL_PASSPORT',
			'DISPLAY_CODE' => 'TechnicalPassport',
			'NAME' => static::getMessage('FIELD_TECHNICAL_PASSPORT_NAME'),
			'SORT' => 992,
			'DESCRIPTION' => static::getMessage('FIELD_TECHNICAL_PASSPORT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_HOURS',
			'DISPLAY_CODE' => 'EngineHours',
			'NAME' => static::getMessage('FIELD_ENGINE_HOURS_NAME'),
			'SORT' => 993,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_HOURS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'VIN',
			'DISPLAY_CODE' => 'VIN',
			'NAME' => static::getMessage('FIELD_VIN_NAME'),
			'SORT' => 994,
			'DESCRIPTION' => static::getMessage('FIELD_VIN_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE',
			'DISPLAY_CODE' => 'Make',
			'NAME' => static::getMessage('FIELD_MAKE_NAME'),
			'SORT' => 995,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL',
			'DISPLAY_CODE' => 'Model',
			'NAME' => static::getMessage('FIELD_MODEL_NAME'),
			'SORT' => 996,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR',
			'DISPLAY_CODE' => 'Year',
			'NAME' => static::getMessage('FIELD_YEAR_NAME'),
			'SORT' => 997,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BODY_TYPE',
			'DISPLAY_CODE' => 'BodyType',
			'NAME' => static::getMessage('FIELD_BODY_TYPE_NAME'),
			'SORT' => 998,
			'DESCRIPTION' => static::getMessage('FIELD_BODY_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TYPE_OF_VEHICLE',
			'DISPLAY_CODE' => 'TypeOfVehicle',
			'NAME' => static::getMessage('FIELD_TYPE_OF_VEHICLE_NAME'),
			'SORT' => 999,
			'DESCRIPTION' => static::getMessage('FIELD_TYPE_OF_VEHICLE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'SUB_TYPE_OF_VEHICLE',
			'DISPLAY_CODE' => 'SubTypeOfVehicle',
			'NAME' => static::getMessage('FIELD_SUB_TYPE_OF_VEHICLE_NAME'),
			'SORT' => 1000,
			'DESCRIPTION' => static::getMessage('FIELD_SUB_TYPE_OF_VEHICLE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TYPE_OF_TRAILER',
			'DISPLAY_CODE' => 'TypeOfTrailer',
			'NAME' => static::getMessage('FIELD_TYPE_OF_TRAILER_NAME'),
			'SORT' => 1010,
			'DESCRIPTION' => static::getMessage('FIELD_TYPE_OF_TRAILER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRAILER_VIN',
			'DISPLAY_CODE' => 'TrailerVIN',
			'NAME' => static::getMessage('FIELD_TRAILER_VIN_NAME'),
			'SORT' => 1020,
			'DESCRIPTION' => static::getMessage('FIELD_TRAILER_VIN_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TYPE_OF_VEHICLE_SEMI_TRAILER_COUPLING',
			'DISPLAY_CODE' => 'TypeOfVehicleSemiTrailerCoupling',
			'NAME' => static::getMessage('FIELD_TYPE_OF_VEHICLE_SEMI_TRAILER_COUPLING_NAME'),
			'SORT' => 1030,
			'DESCRIPTION' => static::getMessage('FIELD_TYPE_OF_VEHICLE_SEMI_TRAILER_COUPLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE_SEMI_TRAILER_COUPLING',
			'DISPLAY_CODE' => 'MakeSemiTrailerCoupling',
			'NAME' => static::getMessage('FIELD_MAKE_SEMI_TRAILER_COUPLING_NAME'),
			'SORT' => 1040,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_SEMI_TRAILER_COUPLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL_SEMI_TRAILER_COUPLING',
			'DISPLAY_CODE' => 'ModelSemiTrailerCoupling',
			'NAME' => static::getMessage('FIELD_MODEL_SEMI_TRAILER_COUPLING_NAME'),
			'SORT' => 1050,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_SEMI_TRAILER_COUPLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TYPE_SEMI_TRAILER_COUPLING',
			'DISPLAY_CODE' => 'TypeSemiTrailerCoupling',
			'NAME' => static::getMessage('FIELD_TYPE_SEMI_TRAILER_COUPLING_NAME'),
			'SORT' => 1060,
			'DESCRIPTION' => static::getMessage('FIELD_TYPE_SEMI_TRAILER_COUPLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'YEAR_SEMI_TRAILER_COUPLING',
			'DISPLAY_CODE' => 'YearSemiTrailerCoupling',
			'NAME' => static::getMessage('FIELD_YEAR_SEMI_TRAILER_COUPLING_NAME'),
			'SORT' => 1070,
			'DESCRIPTION' => static::getMessage('FIELD_YEAR_SEMI_TRAILER_COUPLING_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE_KMU',
			'DISPLAY_CODE' => 'MakeKmu',
			'NAME' => static::getMessage('FIELD_MAKE_KMU_NAME'),
			'SORT' => 1080,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_KMU_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL_KMU',
			'DISPLAY_CODE' => 'ModelKmu',
			'NAME' => static::getMessage('FIELD_MODEL_KMU_NAME'),
			'SORT' => 1090,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_KMU_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BRAND',
			'DISPLAY_CODE' => 'Brand',
			'NAME' => static::getMessage('FIELD_BRAND_NAME'),
			'SORT' => 1100,
			'DESCRIPTION' => static::getMessage('FIELD_BRAND_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'BODY',
			'DISPLAY_CODE' => 'Body',
			'NAME' => static::getMessage('FIELD_BODY_NAME'),
			'SORT' => 1110,
			'DESCRIPTION' => static::getMessage('FIELD_BODY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DOORS_COUNT',
			'DISPLAY_CODE' => 'DoorsCount',
			'NAME' => static::getMessage('FIELD_DOORS_COUNT_NAME'),
			'SORT' => 1120,
			'DESCRIPTION' => static::getMessage('FIELD_DOORS_COUNT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GENERATION',
			'DISPLAY_CODE' => 'Generation',
			'NAME' => static::getMessage('FIELD_GENERATION_NAME'),
			'SORT' => 1130,
			'DESCRIPTION' => static::getMessage('FIELD_GENERATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_TYPE',
			'DISPLAY_CODE' => 'EngineType',
			'NAME' => static::getMessage('FIELD_ENGINE_TYPE_NAME'),
			'SORT' => 1140,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DRIVE_TYPE',
			'DISPLAY_CODE' => 'DriveType',
			'NAME' => static::getMessage('FIELD_DRIVE_TYPE_NAME'),
			'SORT' => 1150,
			'DESCRIPTION' => static::getMessage('FIELD_DRIVE_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSMISSION',
			'DISPLAY_CODE' => 'Transmission',
			'NAME' => static::getMessage('FIELD_TRANSMISSION_NAME'),
			'SORT' => 1160,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSMISSION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODIFICATION',
			'DISPLAY_CODE' => 'Modification',
			'NAME' => static::getMessage('FIELD_MODIFICATION_NAME'),
			'SORT' => 1170,
			'DESCRIPTION' => static::getMessage('FIELD_MODIFICATION_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRIM',
			'DISPLAY_CODE' => 'Trim',
			'NAME' => static::getMessage('FIELD_TRIM_NAME'),
			'SORT' => 1180,
			'DESCRIPTION' => static::getMessage('FIELD_TRIM_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEEL_TYPE',
			'DISPLAY_CODE' => 'WheelType',
			'NAME' => static::getMessage('FIELD_WHEEL_TYPE_NAME'),
			'SORT' => 1190,
			'DESCRIPTION' => static::getMessage('FIELD_WHEEL_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'OWNERS_BY_DOCUMENTS',
			'DISPLAY_CODE' => 'OwnersByDocuments',
			'NAME' => static::getMessage('FIELD_OWNERS_BY_DOCUMENTS_NAME'),
			'SORT' => 1200,
			'DESCRIPTION' => static::getMessage('FIELD_OWNERS_BY_DOCUMENTS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'COLOR',
			'DISPLAY_CODE' => 'Color',
			'NAME' => static::getMessage('FIELD_COLOR_NAME'),
			'SORT' => 1210,
			'DESCRIPTION' => static::getMessage('FIELD_COLOR_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'ACCIDENT',
			'DISPLAY_CODE' => 'Accident',
			'NAME' => static::getMessage('FIELD_ACCIDENT_NAME'),
			'SORT' => 1220,
			'DESCRIPTION' => static::getMessage('FIELD_ACCIDENT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAKE_CHASSIS',
			'DISPLAY_CODE' => 'MakeChassis',
			'NAME' => static::getMessage('FIELD_MAKE_CHASSIS_NAME'),
			'SORT' => 1230,
			'DESCRIPTION' => static::getMessage('FIELD_MAKE_CHASSIS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MODEL_CHASSIS',
			'DISPLAY_CODE' => 'ModelChassis',
			'NAME' => static::getMessage('FIELD_MODEL_CHASSIS_NAME'),
			'SORT' => 1240,
			'DESCRIPTION' => static::getMessage('FIELD_MODEL_CHASSIS_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'AVAILABILITY',
			'DISPLAY_CODE' => 'Availability',
			'NAME' => static::getMessage('FIELD_AVAILABILITY_NAME'),
			'SORT' => 1250,
			'DESCRIPTION' => static::getMessage('FIELD_AVAILABILITY_DESC'),
			'DEFAULT_VALUE' => [
				array(
					'TYPE' => 'CONST',
					'CONST' => static::getMessage('FIELD_AVAILABILITY_IN'),
				),
			],
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_CAPACITY',
			'DISPLAY_CODE' => 'EngineCapacity',
			'NAME' => static::getMessage('FIELD_ENGINE_CAPACITY_NAME'),
			'SORT' => 1260,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_CAPACITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'GROSS_VEHICLE_WEIGHT',
			'DISPLAY_CODE' => 'GrossVehicleWeight',
			'NAME' => static::getMessage('FIELD_GROSS_VEHICLE_WEIGHT_NAME'),
			'SORT' => 1270,
			'DESCRIPTION' => static::getMessage('FIELD_GROSS_VEHICLE_WEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'PERMISSIBLE_GROSS_VEHICLE_WEIGHT',
			'DISPLAY_CODE' => 'PermissibleGrossVehicleWeight',
			'NAME' => static::getMessage('FIELD_PERMISSIBLE_GROSS_VEHICLE_WEIGHT_NAME'),
			'SORT' => 1280,
			'DESCRIPTION' => static::getMessage('FIELD_PERMISSIBLE_GROSS_VEHICLE_WEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEEL_FORMULA',
			'DISPLAY_CODE' => 'WheelFormula',
			'NAME' => static::getMessage('FIELD_WHEEL_FORMULA_NAME'),
			'SORT' => 1290,
			'DESCRIPTION' => static::getMessage('FIELD_WHEEL_FORMULA_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'POWER',
			'DISPLAY_CODE' => 'Power',
			'NAME' => static::getMessage('FIELD_POWER_NAME'),
			'SORT' => 1300,
			'DESCRIPTION' => static::getMessage('FIELD_POWER_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'EMISSION_CLASS',
			'DISPLAY_CODE' => 'EmissionClass',
			'NAME' => static::getMessage('FIELD_EMISSION_CLASS_NAME'),
			'SORT' => 1310,
			'DESCRIPTION' => static::getMessage('FIELD_EMISSION_CLASS_DESC'),
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
		if(!Helper::isEmpty($arFields['EMAIL']))
			$arXmlTags['Email'] = Xml::addTag($arFields['EMAIL']);
		if(!Helper::isEmpty($arFields['MANAGER_NAME']))
			$arXmlTags['ManagerName'] = Xml::addTag($arFields['MANAGER_NAME']);
		if(!Helper::isEmpty($arFields['CONTACT_PHONE']))
			$arXmlTags['ContactPhone'] = Xml::addTag($arFields['CONTACT_PHONE']);
		if(!Helper::isEmpty($arFields['CONTACT_METHOD']))
			$arXmlTags['ContactMethod'] = Xml::addTag($arFields['CONTACT_METHOD']);
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
		if(!Helper::isEmpty($arFields['DISPLAY_AREAS']))
			$arXmlTags['DisplayAreas'] = Xml::addTagWithSubtags($arFields['DISPLAY_AREAS'], 'Area');
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
		if(!Helper::isEmpty($arFields['LATITUDE']))
			$arXmlTags['Latitude'] = Xml::addTag($arFields['LATITUDE']);
		if(!Helper::isEmpty($arFields['LONGITUDE']))
			$arXmlTags['Longitude'] = Xml::addTag($arFields['LONGITUDE']);
		#
		if(!Helper::isEmpty($arFields['CATEGORY']))
			$arXmlTags['Category'] = Xml::addTag($arFields['CATEGORY']);
		if(!Helper::isEmpty($arFields['CONDITION']))
			$arXmlTags['Condition'] = Xml::addTag($arFields['CONDITION']);
		if(!Helper::isEmpty($arFields['KILOMETRAGE']))
			$arXmlTags['Kilometrage'] = Xml::addTag($arFields['KILOMETRAGE']);
		if(!Helper::isEmpty($arFields['TECHNICAL_PASSPORT']))
			$arXmlTags['TechnicalPassport'] = Xml::addTag($arFields['TECHNICAL_PASSPORT']);
		if(!Helper::isEmpty($arFields['ENGINE_HOURS']))
			$arXmlTags['EngineHours'] = Xml::addTag($arFields['ENGINE_HOURS']);
		if(!Helper::isEmpty($arFields['VIN']))
			$arXmlTags['VIN'] = Xml::addTag($arFields['VIN']);
		if(!Helper::isEmpty($arFields['MAKE']))
			$arXmlTags['Make'] = Xml::addTag($arFields['MAKE']);
		if(!Helper::isEmpty($arFields['MODEL']))
			$arXmlTags['Model'] = Xml::addTag($arFields['MODEL']);
		if(!Helper::isEmpty($arFields['YEAR']))
			$arXmlTags['Year'] = Xml::addTag($arFields['YEAR']);
		if(!Helper::isEmpty($arFields['BODY_TYPE']))
			$arXmlTags['BodyType'] = Xml::addTag($arFields['BODY_TYPE']);
		if(!Helper::isEmpty($arFields['TYPE_OF_VEHICLE']))
			$arXmlTags['TypeOfVehicle'] = Xml::addTag($arFields['TYPE_OF_VEHICLE']);
		if(!Helper::isEmpty($arFields['SUB_TYPE_OF_VEHICLE']))
			$arXmlTags['SubTypeOfVehicle'] = Xml::addTag($arFields['SUB_TYPE_OF_VEHICLE']);
		if(!Helper::isEmpty($arFields['TYPE_OF_TRAILER']))
			$arXmlTags['TypeOfTrailer'] = Xml::addTag($arFields['TYPE_OF_TRAILER']);
		if(!Helper::isEmpty($arFields['TRAILER_VIN']))
			$arXmlTags['TrailerVIN'] = Xml::addTag($arFields['TRAILER_VIN']);
		if(!Helper::isEmpty($arFields['TYPE_OF_VEHICLE_SEMI_TRAILER_COUPLING']))
			$arXmlTags['TypeOfVehicleSemiTrailerCoupling'] = Xml::addTag($arFields['TYPE_OF_VEHICLE_SEMI_TRAILER_COUPLING']);
		if(!Helper::isEmpty($arFields['MAKE_SEMI_TRAILER_COUPLING']))
			$arXmlTags['MakeSemiTrailerCoupling'] = Xml::addTag($arFields['MAKE_SEMI_TRAILER_COUPLING']);
		if(!Helper::isEmpty($arFields['MODEL_SEMI_TRAILER_COUPLING']))
			$arXmlTags['ModelSemiTrailerCoupling'] = Xml::addTag($arFields['MODEL_SEMI_TRAILER_COUPLING']);
		if(!Helper::isEmpty($arFields['TYPE_SEMI_TRAILER_COUPLING']))
			$arXmlTags['TypeSemiTrailerCoupling'] = Xml::addTag($arFields['TYPE_SEMI_TRAILER_COUPLING']);
		if(!Helper::isEmpty($arFields['YEAR_SEMI_TRAILER_COUPLING']))
			$arXmlTags['YearSemiTrailerCoupling'] = Xml::addTag($arFields['YEAR_SEMI_TRAILER_COUPLING']);
		if(!Helper::isEmpty($arFields['GOODS_TYPE']))
			$arXmlTags['GoodsType'] = Xml::addTag($arFields['GOODS_TYPE']);
		if(!Helper::isEmpty($arFields['MAKE_KMU']))
			$arXmlTags['MakeKmu'] = Xml::addTag($arFields['MAKE_KMU']);
		if(!Helper::isEmpty($arFields['MODEL_KMU']))
			$arXmlTags['ModelKmu'] = Xml::addTag($arFields['MODEL_KMU']);
		if(!Helper::isEmpty($arFields['BRAND']))
			$arXmlTags['Brand'] = Xml::addTag($arFields['BRAND']);
		if(!Helper::isEmpty($arFields['BODY']))
			$arXmlTags['Body'] = Xml::addTag($arFields['BODY']);
		if(!Helper::isEmpty($arFields['DOORS_COUNT']))
			$arXmlTags['DoorsCount'] = Xml::addTag($arFields['DOORS_COUNT']);
		if(!Helper::isEmpty($arFields['GENERATION']))
			$arXmlTags['Generation'] = Xml::addTag($arFields['GENERATION']);
		if(!Helper::isEmpty($arFields['ENGINE_TYPE']))
			$arXmlTags['EngineType'] = Xml::addTag($arFields['ENGINE_TYPE']);
		if(!Helper::isEmpty($arFields['DRIVE_TYPE']))
			$arXmlTags['DriveType'] = Xml::addTag($arFields['DRIVE_TYPE']);
		if(!Helper::isEmpty($arFields['TRANSMISSION']))
			$arXmlTags['Transmission'] = Xml::addTag($arFields['TRANSMISSION']);
		if(!Helper::isEmpty($arFields['MODIFICATION']))
			$arXmlTags['Modification'] = Xml::addTag($arFields['MODIFICATION']);
		if(!Helper::isEmpty($arFields['TRIM']))
			$arXmlTags['Trim'] = Xml::addTag($arFields['TRIM']);
		if(!Helper::isEmpty($arFields['WHEEL_TYPE']))
			$arXmlTags['WheelType'] = Xml::addTag($arFields['WHEEL_TYPE']);
		if(!Helper::isEmpty($arFields['OWNERS_BY_DOCUMENTS']))
			$arXmlTags['OwnersByDocuments'] = Xml::addTag($arFields['OWNERS_BY_DOCUMENTS']);
		if(!Helper::isEmpty($arFields['COLOR']))
			$arXmlTags['Color'] = Xml::addTag($arFields['COLOR']);
		if(!Helper::isEmpty($arFields['ACCIDENT']))
			$arXmlTags['Accident'] = Xml::addTag($arFields['ACCIDENT']);
		if(!Helper::isEmpty($arFields['MAKE_CHASSIS']))
			$arXmlTags['MakeChassis'] = Xml::addTag($arFields['MAKE_CHASSIS']);
		if(!Helper::isEmpty($arFields['MODEL_CHASSIS']))
			$arXmlTags['ModelChassis'] = Xml::addTag($arFields['MODEL_CHASSIS']);
		if(!Helper::isEmpty($arFields['AVAILABILITY']))
			$arXmlTags['Availability'] = Xml::addTag($arFields['AVAILABILITY']);
		if(!Helper::isEmpty($arFields['ENGINE_CAPACITY']))
			$arXmlTags['EngineCapacity'] = Xml::addTag($arFields['ENGINE_CAPACITY']);
		if(!Helper::isEmpty($arFields['GROSS_VEHICLE_WEIGHT']))
			$arXmlTags['GrossVehicleWeight'] = Xml::addTag($arFields['GROSS_VEHICLE_WEIGHT']);
		if(!Helper::isEmpty($arFields['PERMISSIBLE_GROSS_VEHICLE_WEIGHT']))
			$arXmlTags['PermissibleGrossVehicleWeight'] = Xml::addTag($arFields['PERMISSIBLE_GROSS_VEHICLE_WEIGHT']);
		if(!Helper::isEmpty($arFields['WHEEL_FORMULA']))
			$arXmlTags['WheelFormula'] = Xml::addTag($arFields['WHEEL_FORMULA']);
		if(!Helper::isEmpty($arFields['POWER']))
			$arXmlTags['Power'] = Xml::addTag($arFields['POWER']);
		if(!Helper::isEmpty($arFields['EMISSION_CLASS']))
			$arXmlTags['EmissionClass'] = Xml::addTag($arFields['EMISSION_CLASS']);
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