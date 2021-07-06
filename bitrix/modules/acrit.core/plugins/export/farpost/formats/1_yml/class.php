<?

/**
 * Acrit Core: GoodsRu plugin
 * @package acrit.core
 * @copyright 2019 Acrit
 */

namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper,
		\Acrit\Core\Xml,
		\Acrit\Core\HttpRequest,
		\Acrit\Core\Export\Field\Field,
		\PhpOffice\PhpSpreadsheet\Spreadsheet,
		\PhpOffice\PhpSpreadsheet\Writer\Xlsx,
		\PhpOffice\PhpSpreadsheet\IOFactory,
		\PhpOffice\PhpSpreadsheet\Cell\Coordinate;

Helper::loadMessages(__FILE__);

require_once realpath(__DIR__ . '/../../../yandex.market/class.php');
require_once realpath(__DIR__ . '/../../../yandex.market/formats/1_simple/class.php');

class FarpostSimple extends YandexMarketSimple
{

	CONST DATE_UPDATED = '2021-06-25';

	protected $bShopName = true;
	protected $bDelivery = true;
	protected $bEnableAutoDiscounts = false;
	protected $bPlatform = true;
	protected $bZip = false;
	protected $bPromoGift = false;
	protected $bPromoSpecialPrice = false;
	protected $bPromoCode = false;
	protected $bPromoNM = false;

	/**
	 * Base constructor
	 */
	public function __construct($strModuleId)
	{
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode()
	{
		return 'FARPOST_YML';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName()
	{
		return static::getMessage('NAME');
	}

	/**
	 * 	Is it subclass?
	 */
	public static function isSubclass()
	{
		return true;
	}

	/* END OF BASE STATIC METHODS */

	public function getDefaultExportFilename()
	{
		return 'farpost.yml';
	}

	/**
	 * 	Get custom tabs for profile edit
	 */
	public function getAdditionalTabs($intProfileID)
	{
		return [];
	}

	/**
	 * 	Get custom tabs for profile edit
	 */
	public function getAdditionalSubTabs($intProfileID, $intIBlockID)
	{
		return [];
	}

}

?>