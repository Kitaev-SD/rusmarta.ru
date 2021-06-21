<?
/**
 * Acrit Core: wildberries
 * @documentation https://suppliers.wildberries.ru/remote-wh-site/api-content.html
 */

namespace Acrit\Core\Export\Plugins\WildberriesHelpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest;

Helper::loadMessages();

class Api {
	
	#const URL = 'https://content-suppliers.wildberries.ru';
	const URL = 'https://suppliers-api.wildberries.ru';
	
	protected $strClientId;
	protected $strApiKey;
	protected $intProfileId;
	protected $strModuleId;

	protected $intMaxAttempts = 5;
	
	/**
	 *	Constructor
	 */
	public function __construct($intProfileId, $strModuleId){
		$this->intProfileId = $intProfileId;
		$this->strModuleId = $strModuleId;
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
	
	/**
	 *	Save data to log
	 */
	public function addToLog($strMessage, $bDebug=false){
		return Log::getInstance($this->strModuleId)->add($strMessage, $this->intProfileId, $bDebug);
	}
	
	/**
	 *	Is debug mode for log?
	 */
	public function isDebugMode(){
		return Log::getInstance($this->strModuleId)->isDebugMode();
	}
	
	/**
	 *	Execute http-request
	 */
	public function execute($strCommand, $arJson=null, $arParams=[], $attemptIndex=1){
		$bSkipErrors = false;
		$arParamsOriginal = $arParams;
		$intElementId = $arParams['ELEMENT_ID'];
		$strElementId = $intElementId ? static::getMessage('INFO_ELEMENT', ['#ELEMENT_ID#' => $intElementId]) : '';
		unset($arParams['ELEMENT_ID']);
		if($arParams['SKIP_ERRORS']){
			$bSkipErrors = true;
			unset($arParams['SKIP_ERRORS']);
		}
		if(is_array($arJson)){
			$arParams['CONTENT'] = Json::encode($arJson);
		}
		elseif(is_string($arJson)){
			$arParams['CONTENT'] = $arJson;
		}
		if(!is_array($arParams['HEADER'])){
			$arParams['HEADER'] = [];
		}
		$arParams['HEADER'] = array_merge([
			'Content-Type' => 'application/json',
			#'Content-Length' => mb_strlen($arParams['CONTENT']),
			#'Expect' => 'application/json',
		], $arParams['HEADER']);
		$arParams['TIMEOUT'] = 30;
		$strUrl = $strCommand;
		if(!preg_match('#https?://#', $strUrl)){
			$strUrl = (strlen($arParams['HOST']) ? $arParams['HOST'] : static::URL).$strUrl;
		}
		$time = time();
		$strJson = HttpRequest::getHttpContent($strUrl, $arParams);
		$time = time() - $time;
		$bTimeOut = $strJson === false && $time >= $arParams['TIMEOUT'];
		$responseCode = $this->getResponseCode();
		$responseCodeFull = $this->getResponseCode(true);
		# Do next attempt
		if($bTimeOut || in_array($responseCode, [500, 502, 503, 504])){
			$attemptIndex++;
			$strMessage = $strElementId.static::getMessage('NOTICE_ATTEMPT', [
				'#COMMAND#' => $strCommand,
				'#INDEX#' => $attemptIndex,
				'#COUNT#' => $this->intMaxAttempts,
				'#CODE#' => $bTimeOut ? 'TIMEOUT' : $responseCodeFull,
			]);
			$this->addToLog($strMessage);
			if($attemptIndex <= $this->intMaxAttempts){
				return $this->execute($strCommand, $arJson, $arParamsOriginal, $attemptIndex);
			}
		}
		# Handle timeout
		if($bTimeOut){
			$strJson = \Bitrix\Main\Web\Json::encode(['error' => [
				'message' => 'Timeout on URL '.static::URL.$strCommand,
				'code' => 'TIMEOUT',
			]]);
		}
		# Check results
		if(strlen($strJson)){
			if(stripos(implode(' ', HttpRequest::getHeaders()), 'json')){
				try{
					$arJson = Json::decode($strJson);
					if(is_array($arJson['error']) && !empty($arJson['error']) && !$bSkipErrors){
						$strMessage = 'ERROR_GENERAL'.($this->isDebugMode() ? '_DEBUG' : '');
						$strError = sprintf('%s [%s]', $arJson['error']['message'], $arJson['error']['code']);
						$strMessage = static::getMessage($strMessage, [
							'#COMMAND#' => $strCommand,
							'#JSON#' => $arParams['CONTENT'],
							'#ERROR#' => $strError,
						]);
						$this->addToLog($strElementId.$strMessage);
					}
					return $arParams['RETURN_TEXT'] ? $strJson : $arJson;
				} catch(\Exception $obError){}
			}
			return $strJson;
		}
		elseif($arParams['EXPECT_EMPTY_REQUEST']){
			return true;
		}
		# Handle error
		$strMessage = 'ERROR_REQUEST'.($this->isDebugMode() ? '_DEBUG' : '');
		$strMessage = sprintf(static::getMessage($strMessage,  [
			'#COMMAND#' => $strCommand,
			'#JSON#' => $arParams['CONTENT'],
			'#RESPONSE#' => $strJson,
			'#CODE#' => $responseCodeFull,
		]));
		$this->addToLog($strElementId.$strMessage);
		return false;
	}
	
	/**
	 *	Get headers from last request
	 */
	public function getHeaders(){
		return HttpRequest::getHeaders();
	}

	/**
	 * Get token from response cookie
	 */
	public function getCookieWbToken(){
		foreach($this->getHeaders() as $strHeader){
			if(preg_match('#^set\-cookie:[\s]*WBToken=(.*?);.*?$#i', $strHeader, $arMatch)){
				return $arMatch[1];
			}
		}
		return false;
	}

	/**
	 * Get response code
	 */
	public function getResponseCode($bFull=false){
		$strCode = false;
		foreach($this->getHeaders() as $strHeader){
			if(preg_match('#^HTTP/([\d\.]+)\s*(.*?)$#', $strHeader, $arMatch)){
				$strCode = $bFull ? $arMatch[2] : intVal($arMatch[2]);
			}
		}
		return $strCode;
	}

}

