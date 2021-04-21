<?
namespace Aspro\Functions;

use Bitrix\Main\Application,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\IO\File,
	Bitrix\Main\Config\Option,
	Bitrix\Main\Web\Json,
	Bitrix\Main\Web\HttpClient,
	Bitrix\Main\HttpRequest,
	Bitrix\Main\SystemException;

Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('catalog');

if(!class_exists("CAsproNextReCaptcha"))
{
	class CAsproNextReCaptcha{
		const MODULE_ID = \CNext::moduleID;
		const URL = 'https://www.google.com/recaptcha/api/siteverify';

		public static function getPublicKey(){
			if($key = Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_PUBLIC_KEY'))
				return $key;

			return false;
		}

		public static function getPrivateKey(){
			if($key = Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_PRIVATE_KEY'))
				return $key;

			return false;
		}

		public static function checkRecaptchaActive(){
			static $bActive;
			if($bActive === NULL)
			{
				if(Option::get(self::MODULE_ID, 'USE_GOOGLE_RECAPTCHA', 'N') == 'Y' && !self::checkMask(Option::get(self::MODULE_ID, 'GOOGLE_RECAPTCHA_MASK_PAGE', '')))
					return ($bActive = self::getPrivateKey() && self::getPublicKey());
			}

			return false;
		}

		public static function callbackReplaceImage($arImage){
			// remove styl before src
			$arImage['tag'] = preg_replace('!style=("|\').*?("|\')!', '', $arImage['tag'], -1);

			// replace src
			if($arImage['src'])
				$arImage['src'] = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
			
			//replace style
			if ($arImage['tail'])
			{
				$replaceImageStyle = 'style="display: none; opacity: 0; width: 0; height: 0; margin: 0;"';
				$subCount = 0;
				$arImage['tail'] = preg_replace('!style=("|\').*?("|\')!', $replaceImageStyle, $arImage['tail'], -1, $subCount);

				if (!$subCount)
				{
					$arImage['tail'] = preg_replace("/(\\/)?\\>/", "$replaceImageStyle />", $arImage['tail']);
				}
			}

			return implode('', $arImage);
		}

		/**
		 * @param $post
		 * @return bool
		 */
		public static function getCodeByPostList($arPost = array())
		{
			$reCaptchaResponse = $arPost['g-recaptcha-response'];

			if(!$reCaptchaResponse || !self::checkClientResponse($reCaptchaResponse))
				return false;


			$captchaSid = $arPost["captcha_sid"] ? $arPost["captcha_sid"] : $arPost["captcha_code"];

			if(!$captchaSid)
				return false;

			$res = Application::getConnection()->query("SELECT CODE FROM b_captcha WHERE id='" . $captchaSid . "'")->fetch();
			if($res['CODE'])
				return $res['CODE'];

			return false;
		}

		/**
		 * @param $application
		 * @param $request
		 */
		public static function reInitContext($application, $request)
		{
			$context = $application->getContext();
			$server = $context->getServer();

			$httpRequest = new HttpRequest(
				$server,
				$request->getQueryList()->toArray(),
				$_POST,
				$request->getFileList()->toArray(),
				$request->getCookieList()->toArray()
			);

			$context->initialize(
				$httpRequest,
				$context->getResponse(),
				$server,
				array(
					'env' => $context->getEnvironment()
				)
			);

			$application->setContext($context);
		}

		/**
		 * @param $captchaResponse
		 * @return bool
		 * @throws \Bitrix\Main\ArgumentNullException
		 */
		public static function checkClientResponse($captchaResponse)
		{
			try
			{
				if(!is_string($captchaResponse) || strlen($captchaResponse) == 0)
				{
					throw new \Bitrix\Main\ArgumentNullException('Missing input parameter "response"');
				}

				$data = array(
					'secret' => static::getPrivateKey(),
					'response' => $captchaResponse,
				);
				$httpClient = new HttpClient();
				$response = $httpClient->post(self::URL, $data);

				if(empty($response))
				{
					throw new \Bitrix\Main\ArgumentTypeException('Wrong argument type, "json" expected');
				}

				$response = Json::decode($response, true);

				if(!$response['success'])
				{
					if(!$response['error-codes'])
					{
						throw new \Bitrix\Main\ArgumentTypeException('Wrong argument type, "array" expected');
					}
					else
					{
						throw new SystemException(implode('<br>', $response['error-codes']));
					}
				}

				return true;

			}
			catch(SystemException $exception)
			{
				\CEventLog::Add(array(
					"SEVERITY" => "WARNING",
					"AUDIT_TYPE_ID" => 'ASPRO_NEXT.RECAPTCHA_ERROR',
					"MODULE_ID" => self::MODULE_ID,
					"ITEM_ID" => self::MODULE_ID,
					"DESCRIPTION" => $exception->getMessage(),
				));
			}
			return false;
		}

		public function checkMask($mask_exc = ''){
			if($mask_exc)
			{
				$request = Application::getInstance()->getContext()->getServer(); 
				$arMaskTmp = explode(";", $mask_exc);

				// trim space in array items
				$arMask = array_map(function($n){
					return trim($n);
				}, $arMaskTmp);

				$reg = '%^' . implode('|', $arMask) . '%i'; // set reg

				if($request["REAL_FILE_PATH"]) // real page
					$url = $request["REAL_FILE_PATH"];
				else
					$url = $request->getScriptName();

				if(!preg_match($reg, $url))
					return false; // no page in mask
				else
					return true; // page in mask
			}
			return false;
		}
	}
}?>