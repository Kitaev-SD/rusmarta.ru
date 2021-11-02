<?
/**
 * Acrit Core: ozon.ru api
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Orders\Plugins\RozetkaRuHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;

Helper::loadMessages(__FILE__);

class Request {
	const URL = 'https://api-seller.rozetka.com.ua/';
	const DATE_FORMAT = 'Y-m-d';

	protected $obPlugin;
	protected $strClientLogin;
	protected $strClientPwd;
	protected $strClientLang;
	protected $strAccessToken;
	protected $intProfileId;
	protected $strModuleId;
	
	/**
	 *	Constructor
	 */
//	public function __construct($strClientLogin, $strClientPwd, $strClientLang, $intProfileId, $strModuleId){
//		$this->strClientLogin = $strClientLogin;
//		$this->strClientPwd = $strClientPwd;
//		$this->strClientLang = $strClientLang;
//		$this->intProfileId = $intProfileId;
//		$this->strModuleId = $strModuleId;
//		$this->strAccessToken = 'LnN0HPDaYJB5amUonaq1da-gBAfekqXQ';
//	}
	public function __construct($obPlugin) {
		$arProfile = $obPlugin->getProfileArray();
		$this->obPlugin = $obPlugin;
		$this->strClientLogin = $arProfile['CONNECT_CRED']['login'];
		$this->strClientPwd = $obPlugin->getCredPwd();
		$this->strClientLang = $arProfile['CONNECT_CRED']['lang'];
		$this->intProfileId = $arProfile['ID'];
		$this->strModuleId = $obPlugin->getModuleId();
		$this->strAccessToken = $obPlugin->getOption('access_token');
	}

	/**
	 *	Wrapper for Loc::getMessage()
	 */
	public static function getMessage($strMessage, $arReplace=null){
		static $strLang;
		$strFile = realpath(__DIR__.'/../class.php');
		if(is_null($strLang)){
			\Acrit\Core\Export\Exporter::getLangPrefix($strFile, $strLang, $strHead, $strName, $strHint);
		}
		return Helper::getMessage($strLang.$strMessage, $arReplace);
	}

//	/**
//	 *	Save data to log
//	 */
//	public function addToLog($strMessage, $bDebug=false){
//		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
//	}

//	/**
//	 *	Is debug mode for log?
//	 */
//	public function isDebugMode(){
//		return Log::getInstance($this->strModuleId)->isDebugMode();
//	}

	/**
	 *	Execute http-request
	 */
	public function execute($strCommand, $arJson=null, $arParams=[], $token=false){
		$result = $this->request($strCommand, $arJson, $arParams, $token);
		if (!$token && ($result['errors']['message'] == 'incorrect_access_token' || $result['errors']['message'] == 'invalid_credentials')) {
			// Get new token
			$res = $this->getToken();
			if ($res['success'] && $res['content']['access_token']) {
				// Save token
				$this->strAccessToken = $res['content']['access_token'];
				$this->obPlugin->setOption('access_token', $this->strAccessToken);
				// Repeat the request
				$result = $this->request($strCommand, $arJson, $arParams, $token);
			}
		}
		return $result;
	}
	public function request($strCommand, $arJson=null, array $arUrlParams=[], $token=false){
		$strUri = static::URL;
		$token = $token ? : $this->strAccessToken;
		$bSkipErrors = false;
		$strUri .= $strCommand;
		$arParams['HEADER'] = [
			'Authorization' => 'Bearer ' . $token,
			'Content-Type' => 'application/json',
			'Content-Language' => $this->strClientLang,
		];
		if (is_array($arJson)) {
			$arParams['CONTENT'] = Json::encode($arJson, JSON_UNESCAPED_SLASHES);
		}
		elseif (is_string($arJson)) {
			$arParams['CONTENT'] = $arJson;
		}
		if (!empty($arUrlParams)) {
			$strUri .= '?' . http_build_query($arUrlParams);
		}
		$arParams['TIMEOUT'] = 30;
		$strJson = HttpRequest::getHttpContent($strUri, $arParams);
		if ($strJson === false && static::getHeaders() === []) {
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL '.static::URL . $strCommand,
				'code' => 'TIMEOUT',
			]]);
		}
		if (strlen($strJson)) {
			$arJson = Json::decode($strJson);
			if (is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors) {
//				$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
				$strMessage = 'ERROR_GENERAL';
				$strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
				$strMessage = sprintf(static::getMessage($strMessage,  [
					'#COMMAND#' => $strCommand,
					'#JSON#' => $arParams['CONTENT'],
					'#ERROR#' => $strError,
				]));
//				$this->addToLog($strMessage);
			}
			return $arJson;
		}
		$strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
		$strMessage = sprintf(static::getMessage($strMessage,  [
			'#COMMAND#' => $strCommand,
			'#JSON#' => $arParams['CONTENT'],
			'#RESPONSE#' => $strJson,
		]));
//		$this->addToLog($strMessage);
//		usleep(500000);
		return false;
	}

	/**
	 *	Get access token
	 */
	public function getToken(){
		$strCommand = 'sites';
		$bSkipErrors = false;
		$arParams['HEADER'] = [
			'Content-Type' => 'application/json',
		];
		$arParams['METHOD'] = 'POST';
		$arJson = [
			'username' => $this->strClientLogin,
			'password' => base64_encode($this->strClientPwd),
		];
		if (is_array($arJson)) {
			$arParams['CONTENT'] = Json::encode($arJson, JSON_UNESCAPED_SLASHES);
		}
		elseif(is_string($arJson)){
			$arParams['CONTENT'] = $arJson;
		}
		$arParams['TIMEOUT'] = 30;
		$strJson = HttpRequest::getHttpContent(static::URL . $strCommand, $arParams);
		if ($strJson === false && static::getHeaders() === []) {
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL '.static::URL . $strCommand,
				'code' => 'TIMEOUT',
			]]);
		}
		if (strlen($strJson)) {
			$arJson = Json::decode($strJson);
			if(is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
//				$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
				$strMessage = 'ERROR_GENERAL';
				$strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
				$strMessage = sprintf(static::getMessage($strMessage,  [
					'#COMMAND#' => $strCommand,
					'#JSON#' => $arParams['CONTENT'],
					'#ERROR#' => $strError,
				]));
//				$this->addToLog($strMessage);
			}
			return $arJson;
		}
		$strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
		$strMessage = sprintf(static::getMessage($strMessage,  [
			'#COMMAND#' => $strCommand,
			'#JSON#' => $arParams['CONTENT'],
			'#RESPONSE#' => $strJson,
		]));
//		$this->addToLog($strMessage);
//		usleep(500000);
		return false;
	}
	
	/**
	 *	Get headers from last request
	 */
	public function getHeaders(){
		return HttpRequest::getHeaders();
	}

}

