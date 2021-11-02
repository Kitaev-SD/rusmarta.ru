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
		$arParamsCarMultiple = array(
			'MULTIPLE' => 'multiple',
		);
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
		$arResult[] = new Field(array(
			'CODE' => 'SELF_SERVICE',
			'DISPLAY_CODE' => 'SelfService',
			'NAME' => static::getMessage('FIELD_SELF_SERVICE_NAME'),
			'SORT' => 1160,
			'DESCRIPTION' => static::getMessage('FIELD_SELF_SERVICE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'DIAGNOSTICS',
			'DISPLAY_CODE' => 'Diagnostics',
			'NAME' => static::getMessage('FIELD_DIAGNOSTICS_NAME'),
			'SORT' => 1161,
			'DESCRIPTION' => static::getMessage('FIELD_DIAGNOSTICS_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WHEEL_SERVICE',
			'DISPLAY_CODE' => 'WheelService',
			'NAME' => static::getMessage('FIELD_WHEEL_SERVICE_NAME'),
			'SORT' => 1162,
			'DESCRIPTION' => static::getMessage('FIELD_WHEEL_SERVICE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ADDITIONAL_EQUIPMENT',
			'DISPLAY_CODE' => 'AdditionalEquipment',
			'NAME' => static::getMessage('FIELD_ADDITIONAL_EQUIPMENT_NAME'),
			'SORT' => 1163,
			'DESCRIPTION' => static::getMessage('FIELD_ADDITIONAL_EQUIPMENT_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'TUNING',
			'DISPLAY_CODE' => 'Tuning',
			'NAME' => static::getMessage('FIELD_TUNING_NAME'),
			'SORT' => 1164,
			'DESCRIPTION' => static::getMessage('FIELD_TUNING_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MAINTENANCE',
			'DISPLAY_CODE' => 'Maintenance',
			'NAME' => static::getMessage('FIELD_MAINTENANCE_NAME'),
			'SORT' => 1165,
			'DESCRIPTION' => static::getMessage('FIELD_MAINTENANCE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSMISSION_REPAIR',
			'DISPLAY_CODE' => 'TransmissionRepair',
			'NAME' => static::getMessage('FIELD_TRANSMISSION_REPAIR_NAME'),
			'SORT' => 1166,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSMISSION_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'BRAKE_REPAIR',
			'DISPLAY_CODE' => 'BrakeRepair',
			'NAME' => static::getMessage('FIELD_BRAKE_REPAIR_NAME'),
			'SORT' => 1167,
			'DESCRIPTION' => static::getMessage('FIELD_BRAKE_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'STEERING_REPAIR',
			'DISPLAY_CODE' => 'SteeringRepair',
			'NAME' => static::getMessage('FIELD_STEERING_REPAIR_NAME'),
			'SORT' => 1168,
			'DESCRIPTION' => static::getMessage('FIELD_STEERING_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'SUSPENSION_REPAIR',
			'DISPLAY_CODE' => 'SuspensionRepair',
			'NAME' => static::getMessage('FIELD_SUSPENSION_REPAIR_NAME'),
			'SORT' => 1169,
			'DESCRIPTION' => static::getMessage('FIELD_SUSPENSION_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CONDITIONER_REPAIR',
			'DISPLAY_CODE' => 'ConditionerRepair',
			'NAME' => static::getMessage('FIELD_CONDITIONER_REPAIR_NAME'),
			'SORT' => 1170,
			'DESCRIPTION' => static::getMessage('FIELD_CONDITIONER_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'LOCK_REPAIR',
			'DISPLAY_CODE' => 'LockRepair',
			'NAME' => static::getMessage('FIELD_LOCK_REPAIR_NAME'),
			'SORT' => 1171,
			'DESCRIPTION' => static::getMessage('FIELD_LOCK_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ENGINE_REPAIR',
			'DISPLAY_CODE' => 'EngineRepair',
			'NAME' => static::getMessage('FIELD_ENGINE_REPAIR_NAME'),
			'SORT' => 1172,
			'DESCRIPTION' => static::getMessage('FIELD_ENGINE_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'EXHAUST_REPAIR',
			'DISPLAY_CODE' => 'ExhaustRepair',
			'NAME' => static::getMessage('FIELD_EXHAUST_REPAIR_NAME'),
			'SORT' => 1173,
			'DESCRIPTION' => static::getMessage('FIELD_EXHAUST_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'BUYING_HELP',
			'DISPLAY_CODE' => 'BuyingHelp',
			'NAME' => static::getMessage('FIELD_BUYING_HELP_NAME'),
			'SORT' => 1174,
			'DESCRIPTION' => static::getMessage('FIELD_BUYING_HELP_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ROADSIDE_HELP',
			'DISPLAY_CODE' => 'RoadsideHelp',
			'NAME' => static::getMessage('FIELD_ROADSIDE_HELP_NAME'),
			'SORT' => 1175,
			'DESCRIPTION' => static::getMessage('FIELD_ROADSIDE_HELP_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PAINTING',
			'DISPLAY_CODE' => 'Painting',
			'NAME' => static::getMessage('FIELD_PAINTING_NAME'),
			'SORT' => 1176,
			'DESCRIPTION' => static::getMessage('FIELD_PAINTING_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'RE_EQUIPMENT',
			'DISPLAY_CODE' => 'ReEquipment',
			'NAME' => static::getMessage('FIELD_RE_EQUIPMENT_NAME'),
			'SORT' => 1177,
			'DESCRIPTION' => static::getMessage('FIELD_RE_EQUIPMENT_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WINDOW_TINTING',
			'DISPLAY_CODE' => 'WindowTinting',
			'NAME' => static::getMessage('FIELD_WINDOW_TINTING_NAME'),
			'SORT' => 1178,
			'DESCRIPTION' => static::getMessage('FIELD_WINDOW_TINTING_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'ELECTRICAL_REPAIR',
			'DISPLAY_CODE' => 'ElectricalRepair',
			'NAME' => static::getMessage('FIELD_ELECTRICAL_REPAIR_NAME'),
			'SORT' => 1179,
			'DESCRIPTION' => static::getMessage('FIELD_ELECTRICAL_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'GLASS_REPAIR',
			'DISPLAY_CODE' => 'GlassRepair',
			'NAME' => static::getMessage('FIELD_GLASS_REPAIR_NAME'),
			'SORT' => 1180,
			'DESCRIPTION' => static::getMessage('FIELD_GLASS_REPAIR_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'WASH_AND_CARE',
			'DISPLAY_CODE' => 'WashAndCare',
			'NAME' => static::getMessage('FIELD_WASH_AND_CARE_NAME'),
			'SORT' => 1181,
			'DESCRIPTION' => static::getMessage('FIELD_WASH_AND_CARE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		#
		$arResult[] = new Field(array(
			'CODE' => 'TRANSPORTATION_TYPE',
			'DISPLAY_CODE' => 'TransportationType',
			'NAME' => static::getMessage('FIELD_TRANSPORTATION_TYPE_NAME'),
			'SORT' => 1182,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSPORTATION_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'DISTANCE',
			'DISPLAY_CODE' => 'Distance',
			'NAME' => static::getMessage('FIELD_DISTANCE_NAME'),
			'SORT' => 1183,
			'DESCRIPTION' => static::getMessage('FIELD_DISTANCE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARGO_TRANSPORTATION_TYPE',
			'DISPLAY_CODE' => 'CargoTransportationType',
			'NAME' => static::getMessage('FIELD_CARGO_TRANSPORTATION_TYPE_NAME'),
			'SORT' => 1184,
			'DESCRIPTION' => static::getMessage('FIELD_CARGO_TRANSPORTATION_TYPE_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARGO_TYPE',
			'DISPLAY_CODE' => 'CargoType',
			'NAME' => static::getMessage('FIELD_CARGO_TYPE_NAME'),
			'SORT' => 1185,
			'DESCRIPTION' => static::getMessage('FIELD_CARGO_TYPE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'URGENCY',
			'DISPLAY_CODE' => 'Urgency',
			'NAME' => static::getMessage('FIELD_URGENCY_NAME'),
			'SORT' => 1186,
			'DESCRIPTION' => static::getMessage('FIELD_URGENCY_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'MOVERS_QUANTITY',
			'DISPLAY_CODE' => 'MoversQuantity',
			'NAME' => static::getMessage('FIELD_MOVERS_QUANTITY_NAME'),
			'SORT' => 1187,
			'DESCRIPTION' => static::getMessage('FIELD_MOVERS_QUANTITY_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'PASSENGERS_QUANTITY',
			'DISPLAY_CODE' => 'PassengersQuantity',
			'NAME' => static::getMessage('FIELD_PASSENGERS_QUANTITY_NAME'),
			'SORT' => 1188,
			'DESCRIPTION' => static::getMessage('FIELD_PASSENGERS_QUANTITY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MOVE_TYPE',
			'DISPLAY_CODE' => 'MoveType',
			'NAME' => static::getMessage('FIELD_MOVE_TYPE_NAME'),
			'SORT' => 1189,
			'DESCRIPTION' => static::getMessage('FIELD_MOVE_TYPE_DESC'),
			'MULTIPLE' => true,
			'DEFAULT_VALUE' => array(
				array(
					'TYPE' => 'FIELD',
					'PARAMS' => $arParamsCarMultiple,
				),
			),
			'PARAMS' => $arParamsCarMultiple,
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARGO_HEIGHT',
			'DISPLAY_CODE' => 'CargoHeight',
			'NAME' => static::getMessage('FIELD_CARGO_HEIGHT_NAME'),
			'SORT' => 1190,
			'DESCRIPTION' => static::getMessage('FIELD_CARGO_HEIGHT_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARGO_LENGTH',
			'DISPLAY_CODE' => 'CargoLength',
			'NAME' => static::getMessage('FIELD_CARGO_LENGTH_NAME'),
			'SORT' => 1191,
			'DESCRIPTION' => static::getMessage('FIELD_CARGO_LENGTH_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'CARGO_WIDTH',
			'DISPLAY_CODE' => 'CargoWidth',
			'NAME' => static::getMessage('FIELD_CARGO_WIDTH_NAME'),
			'SORT' => 1192,
			'DESCRIPTION' => static::getMessage('FIELD_CARGO_WIDTH_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'TRANSPORT_BODY',
			'DISPLAY_CODE' => 'TransportBody',
			'NAME' => static::getMessage('FIELD_TRANSPORT_BODY_NAME'),
			'SORT' => 1193,
			'DESCRIPTION' => static::getMessage('FIELD_TRANSPORT_BODY_DESC'),
		));
		$arResult[] = new Field(array(
			'CODE' => 'MINIMUM_ORDER_TIME',
			'DISPLAY_CODE' => 'MinimumOrderTime',
			'NAME' => static::getMessage('FIELD_MINIMUM_ORDER_TIME_NAME'),
			'SORT' => 1194,
			'DESCRIPTION' => static::getMessage('FIELD_MINIMUM_ORDER_TIME_DESC'),
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
			$arXmlTags['RentPurpose'] = Xml::addTagWithSubtags($arFields['RENT_PURPOSE'], 'Option');
		if(!Helper::isEmpty($arFields['EXTRA']))
			$arXmlTags['Extra'] = Xml::addTagWithSubtags($arFields['EXTRA'], 'Option');
		if(!Helper::isEmpty($arFields['WORK_TYPES']))
			$arXmlTags['WorkTypes'] = Xml::addTagWithSubtags($arFields['WORK_TYPES'], 'Option');
		if(!Helper::isEmpty($arFields['BODY_REPAIR']))
			$arXmlTags['BodyRepair'] = Xml::addTagWithSubtags($arFields['BODY_REPAIR'], 'Option');
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
		if(!Helper::isEmpty($arFields['SELF_SERVICE']))
			$arXmlTags['SelfService'] = Xml::addTagWithSubtags($arFields['SELF_SERVICE'], 'Option');
		if(!Helper::isEmpty($arFields['DIAGNOSTICS']))
			$arXmlTags['Diagnostics'] = Xml::addTagWithSubtags($arFields['DIAGNOSTICS'], 'Option');
		if(!Helper::isEmpty($arFields['WHEEL_SERVICE']))
			$arXmlTags['WheelService'] = Xml::addTagWithSubtags($arFields['WHEEL_SERVICE'], 'Option');
		if(!Helper::isEmpty($arFields['ADDITIONAL_EQUIPMENT']))
			$arXmlTags['AdditionalEquipment'] = Xml::addTagWithSubtags($arFields['ADDITIONAL_EQUIPMENT'], 'Option');
		if(!Helper::isEmpty($arFields['TUNING']))
			$arXmlTags['Tuning'] = Xml::addTagWithSubtags($arFields['TUNING'], 'Option');
		if(!Helper::isEmpty($arFields['MAINTENANCE']))
			$arXmlTags['Maintenance'] = Xml::addTagWithSubtags($arFields['MAINTENANCE'], 'Option');
		if(!Helper::isEmpty($arFields['TRANSMISSION_REPAIR']))
			$arXmlTags['TransmissionRepair'] = Xml::addTagWithSubtags($arFields['TRANSMISSION_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['BRAKE_REPAIR']))
			$arXmlTags['BrakeRepair'] = Xml::addTagWithSubtags($arFields['BRAKE_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['STEERING_REPAIR']))
			$arXmlTags['SteeringRepair'] = Xml::addTagWithSubtags($arFields['STEERING_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['SUSPENSION_REPAIR']))
			$arXmlTags['SuspensionRepair'] = Xml::addTagWithSubtags($arFields['SUSPENSION_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['CONDITIONER_REPAIR']))
			$arXmlTags['ConditionerRepair'] = Xml::addTagWithSubtags($arFields['CONDITIONER_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['LOCK_REPAIR']))
			$arXmlTags['LockRepair'] = Xml::addTagWithSubtags($arFields['LOCK_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['ENGINE_REPAIR']))
			$arXmlTags['EngineRepair'] = Xml::addTagWithSubtags($arFields['ENGINE_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['EXHAUST_REPAIR']))
			$arXmlTags['ExhaustRepair'] = Xml::addTagWithSubtags($arFields['EXHAUST_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['BUYING_HELP']))
			$arXmlTags['BuyingHelp'] = Xml::addTagWithSubtags($arFields['BUYING_HELP'], 'Option');
		if(!Helper::isEmpty($arFields['ROADSIDE_HELP']))
			$arXmlTags['RoadsideHelp'] = Xml::addTagWithSubtags($arFields['ROADSIDE_HELP'], 'Option');
		if(!Helper::isEmpty($arFields['PAINTING']))
			$arXmlTags['Painting'] = Xml::addTagWithSubtags($arFields['PAINTING'], 'Option');
		if(!Helper::isEmpty($arFields['RE_EQUIPMENT']))
			$arXmlTags['ReEquipment'] = Xml::addTagWithSubtags($arFields['RE_EQUIPMENT'], 'Option');
		if(!Helper::isEmpty($arFields['WINDOW_TINTING']))
			$arXmlTags['WindowTinting'] = Xml::addTagWithSubtags($arFields['WINDOW_TINTING'], 'Option');
		if(!Helper::isEmpty($arFields['ELECTRICAL_REPAIR']))
			$arXmlTags['ElectricalRepair'] = Xml::addTagWithSubtags($arFields['ELECTRICAL_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['GLASS_REPAIR']))
			$arXmlTags['GlassRepair'] = Xml::addTagWithSubtags($arFields['GLASS_REPAIR'], 'Option');
		if(!Helper::isEmpty($arFields['WASH_AND_CARE']))
			$arXmlTags['WashAndCare'] = Xml::addTagWithSubtags($arFields['WASH_AND_CARE'], 'Option');
		if(!Helper::isEmpty($arFields['TRANSPORTATION_TYPE']))
			$arXmlTags['TransportationType'] = Xml::addTag($arFields['TRANSPORTATION_TYPE']);
		if(!Helper::isEmpty($arFields['DISTANCE']))
			$arXmlTags['Distance'] = Xml::addTagWithSubtags($arFields['DISTANCE'], 'Option');
		if(!Helper::isEmpty($arFields['CARGO_TRANSPORTATION_TYPE']))
			$arXmlTags['CargoTransportationType'] = Xml::addTag($arFields['CARGO_TRANSPORTATION_TYPE']);
		if(!Helper::isEmpty($arFields['CARGO_TYPE']))
			$arXmlTags['CargoType'] = Xml::addTagWithSubtags($arFields['CARGO_TYPE'], 'Option');
		if(!Helper::isEmpty($arFields['URGENCY']))
			$arXmlTags['Urgency'] = Xml::addTagWithSubtags($arFields['URGENCY'], 'Option');
		if(!Helper::isEmpty($arFields['MOVERS_QUANTITY']))
			$arXmlTags['MoversQuantity'] = Xml::addTagWithSubtags($arFields['MOVERS_QUANTITY'], 'Option');
		if(!Helper::isEmpty($arFields['PASSENGERS_QUANTITY']))
			$arXmlTags['PassengersQuantity'] = Xml::addTag($arFields['PASSENGERS_QUANTITY']);
		if(!Helper::isEmpty($arFields['MOVE_TYPE']))
			$arXmlTags['MoveType'] = Xml::addTagWithSubtags($arFields['MOVE_TYPE'], 'Option');
		if(!Helper::isEmpty($arFields['CARGO_HEIGHT']))
			$arXmlTags['CargoHeight'] = Xml::addTag($arFields['CARGO_HEIGHT']);
		if(!Helper::isEmpty($arFields['CARGO_LENGTH']))
			$arXmlTags['CargoLength'] = Xml::addTag($arFields['CARGO_LENGTH']);
		if(!Helper::isEmpty($arFields['CARGO_WIDTH']))
			$arXmlTags['CargoWidth'] = Xml::addTag($arFields['CARGO_WIDTH']);
		if(!Helper::isEmpty($arFields['TRANSPORT_BODY']))
			$arXmlTags['TransportBody'] = Xml::addTag($arFields['TRANSPORT_BODY']);
		if(!Helper::isEmpty($arFields['MINIMUM_ORDER_TIME']))
			$arXmlTags['MinimumOrderTime'] = Xml::addTag($arFields['MINIMUM_ORDER_TIME']);

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