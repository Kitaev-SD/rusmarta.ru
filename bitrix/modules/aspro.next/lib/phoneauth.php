<?
namespace Aspro\Next;
use CNext as Solution;

class PhoneAuth {
	public static function getOptions(){
		static $arParams;

		if(!isset($arParams)){
			$bSupported = $bUse = false;
			$bShow = $bRequired = true;
			$authType = 'LOGIN';
			if(\Bitrix\Main\Loader::includeModule('messageservice')){
				if($bSupported = \CheckVersion(SM_VERSION, '18.5.0') && class_exists('\Bitrix\Main\Controller\PhoneAuth')){
					$bRequired = false;
					if($bShow = \COption::GetOptionString('main', 'new_user_phone_auth', 'N') === 'Y'){
						$bRequired = \COption::GetOptionString('main', 'new_user_phone_required', 'N') === 'Y';
						$bUse = Solution::GetFrontParametrValue('USE_PHONE_AUTH') === 'Y';
					}
				}
			}

			$arParams = array($bSupported, $bShow, $bRequired, $bUse);
		}

		return $arParams;
	}

	public static function modifyResult(&$arResult, $arParams){
		// get phone auth params
		list($bPhoneAuthSupported, $bPhoneAuthShow, $bPhoneAuthRequired, $bPhoneAuthUse) = self::getOptions();
		$arResult['PHONE_AUTH_PARAMS'] = array(
			'SUPPORTED' => &$bPhoneAuthSupported,
			'SHOW' => &$bPhoneAuthShow,
			'REQUIRED' => &$bPhoneAuthRequired,
			'USE' => &$bPhoneAuthUse,
		);

		// auth by phone?
		$bByPhoneRequest = $bPhoneAuthUse && isset($_POST['USER_PHONE_NUMBER']) && isset($_POST['Login']);
		$arResult['PHONE_REQUEST'] = &$bByPhoneRequest;

		// need show sms code field&
		$arResult['SHOW_SMS_FIELD'] = false;

		if($bByPhoneRequest){
			// phone number in request
			$phoneNumber = \Bitrix\Main\UserPhoneAuthTable::normalizePhoneNumber($_POST['USER_PHONE_NUMBER']);
			$arResult['USER_PHONE_NUMBER'] = &$phoneNumber;

			// entered sms code?
			$bByPhoneSMSCodeRequest = isset($_POST['SIGNED_DATA']) && isset($_POST['SMS_CODE']);
			$arResult['SMS_REQUEST'] = &$bByPhoneSMSCodeRequest;

			// check captcha
			$bNeedCheckCaptcha = $GLOBALS['APPLICATION']->NeedCAPTHAForLogin($arResult['USER_LOGIN']);
			if($bNeedCheckCaptcha){
				$bCaptchaError = true;
				$captcha_sid = isset($_POST['captcha_sid']) ? strtoupper(trim($_POST['captcha_sid'])) : '';
				$captcha_word = isset($_POST['captcha_word']) ? strtoupper(trim($_POST['captcha_word'])) : '';

				if(strlen($captcha_word) && strlen($captcha_sid)){
					if($GLOBALS['APPLICATION']->captchaCheckCode($captcha_word, $captcha_sid)){
						$bCaptchaError = false;
					}
				}

				if($bCaptchaError){
					// show captcha in future
					$_SESSION['BX_LOGIN_NEED_CAPTCHA'] = true;

					$arResult['ERROR_MESSAGE'] = array(
						'MESSAGE' => GetMessage('PHONE_AUTH_ERROR_BAD_CAPTCHA').'<br />',
						'TYPE' => 'ERROR',
					);
				}

				if($captcha_sid){
					include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/captcha.php');
					$cpt = new \CCaptcha();
					$cpt->Delete($captcha_sid);
				}
			}
			else{
				$bCaptchaError = false;
			}

			if(!$bCaptchaError){
				// search user
				$arUser = \Bitrix\Main\UserPhoneAuthTable::getList([
					'select' => array('USER_ID'),
					'filter' => array('=PHONE_NUMBER' => $phoneNumber),
				])->fetch();
				if($arUser){
					unset($arResult['ERROR'], $arResult['ERROR_MESSAGE']);

					$arResult['PHONE_CODE_RESEND_INTERVAL'] = \CUser::PHONE_CODE_RESEND_INTERVAL;

					if($bByPhoneSMSCodeRequest){
						// sms code in request
						$smsCode = trim($_POST['SMS_CODE']);
						$arResult['SIGNED_DATA'] = $_POST['SIGNED_DATA'];

						// verify sms code
						if($userId = \CUser::VerifyPhoneCode($phoneNumber, $smsCode)){
							// compare with user ID
							if($userId == $arUser['USER_ID']){
								if($GLOBALS['USER']){
									// auth and remember
									$GLOBALS['USER']->Authorize($userId, (isset($_POST['USER_REMEMBER']) && $_POST['USER_REMEMBER'] === 'Y'));
									if(strlen($arParams['PROFILE_URL'])){
										if($arParams['POPUP_AUTH'] !== 'Y'){
											\LocalRedirect($arParams['PROFILE_URL']);
										}
										die();
									}
								}
								else{
									$arResult['ERROR_MESSAGE'] = array(
										'MESSAGE' => 'No global USER variable<br />',
										'TYPE' => 'ERROR',
									);
								}
							}
							else{
								// show captcha in future
								$_SESSION['BX_LOGIN_NEED_CAPTCHA'] = true;

								$arResult['ERROR_MESSAGE'] = array(
									'MESSAGE' => GetMessage('PHONE_AUTH_CODE_VERIFY_ERROR').'<br />',
									'TYPE' => 'ERROR',
								);
							}
						}
						else{
							// show captcha in future
							$_SESSION['BX_LOGIN_NEED_CAPTCHA'] = true;

							$arResult['ERROR_MESSAGE'] = array(
								'MESSAGE' => GetMessage('PHONE_AUTH_CODE_VERIFY_ERROR').'<br />',
								'TYPE' => 'ERROR',
							);
						}
					}
					else{
						$bGenerate = true;

						// get last generated
						if($row = \Bitrix\Main\UserPhoneAuthTable::getRowById($arUser['USER_ID'])){
							// check time expired
							$now = new \Bitrix\Main\Type\DateTime();
							if($row['DATE_SENT'] && ($row['DATE_SENT']->getTimestamp() + \CUser::PHONE_CODE_RESEND_INTERVAL > $now->getTimestamp())){
								$bGenerate = false;
							}
						}

						if($bGenerate){
							// generate sms code
							list($smsCode, $phoneNumber) = \CUser::GeneratePhoneCode($arUser['USER_ID']);

							$bSend = true;
						}
						else{
							$arResult['SHOW_SMS_FIELD'] = true;
						}

						$smsEventName = 'SMS_USER_AUTH_CODE';

						// check sms event exist
						$arSmsEvent = \CEventType::GetByID($smsEventName, LANGUAGE_ID)->Fetch();

						if($arSmsEvent){
							$smsEventId = $arSmsEvent['ID'];
						}
						else{
							// add sms event
							$et = new \CEventType;
							$arEventFields = array(
								'LID' => LANGUAGE_ID,
								'EVENT_NAME' => $smsEventName,
								'EVENT_TYPE' => 'sms',
								'NAME' => GetMessage('PHONE_AUTH_EVENT_NAME_'.LANGUAGE_ID),
								'DESCRIPTION' => GetMessage('PHONE_AUTH_EVENT_DESCRIPTION_'.LANGUAGE_ID),
							);
							$smsEventId = $et->Add($arEventFields);

							if($smsEventId){
								unset($et);
								$et = new \CEventType;
								$arEventFields['LID'] = (LANGUAGE_ID === 'ru' ? 'en' : 'ru');
								$arEventFields['NAME'] = GetMessage('PHONE_AUTH_EVENT_NAME_'.(LANGUAGE_ID === 'ru' ? 'en' : 'ru'));
								$arEventFields['DESCRIPTION'] = GetMessage('PHONE_AUTH_EVENT_DESCRIPTION_'.(LANGUAGE_ID === 'ru' ? 'en' : 'ru'));
								$et->Add($arEventFields);
							}
							else{
								$arResult['ERROR_MESSAGE'] = array(
									'MESSAGE' => $et->LAST_ERROR,
									'TYPE' => 'ERROR',
								);
							}
						}

						if(class_exists('\Bitrix\Main\Sms\TemplateTable')){
							// check sms template exist
							$arSmsTemplate = \Bitrix\Main\Sms\TemplateTable::getList(array(
								'filter' => array('EVENT_NAME' => $smsEventName)
							))->fetch();

							// add sms template
							if(!$arSmsTemplate){
								$entity = \Bitrix\Main\Sms\TemplateTable::getEntity();
								$template = $entity->createObject();
								$template->setEventName($smsEventName);
								$template->set('ACTIVE', 'Y');
								$template->set('SENDER', '#DEFAULT_SENDER#');
								$template->set('RECEIVER', '#USER_PHONE#');
								$template->set('MESSAGE', GetMessage('PHONE_AUTH_TEMPLATE_MESSAGE'));
								$dbRes = \CSite::GetList( $by = 'sort', $order = 'asc', array('ACTIVE' => 'Y'));
								while($item = $dbRes->Fetch()){
									$site = \Bitrix\Main\SiteTable::getEntity()->wakeUpObject($item["LID"]);
									$template->addToSites($site);
								}
								$addResult = $template->save();
								if(!$addResult->isSuccess()){
									$arResult['ERROR_MESSAGE'] = array(
										'MESSAGE' => implode('<br />', $addResult->getErrorMessages()),
										'TYPE' => 'ERROR',
									);
									$bSend = false;
								}
							}
						}
						else{
							$arResult['ERROR_MESSAGE'] = array(
								'MESSAGE' => GetMessage('PHONE_AUTH_CODE_SENT_ERROR_NEED_MAIN_UPDATE').'<br />',
								'TYPE' => 'ERROR',
							);
							$bSend = false;
						}

						if($bSend){
							// send sms
							$sms = new \Bitrix\Main\Sms\Event(
								$smsEventName,
								array(
									'USER_PHONE' => $phoneNumber,
									'CODE' => $smsCode,
								)
							);
							$sms->setSite(SITE_ID);
							$smsResult = $sms->send(true);

							if($smsResult->isSuccess()){
								$arResult['SIGNED_DATA'] = \Bitrix\Main\Controller\PhoneAuth::signData(
									array(
										'phoneNumber' => $phoneNumber,
										'smsTemplate' => 'SMS_USER_AUTH_CODE',
									)
								);

								$arResult['SHOW_SMS_FIELD'] = true;
							}
							else{
								$arResult['ERROR_MESSAGE'] = array(
									'MESSAGE' => implode('<br />', $smsResult->getErrorMessages()),
									'TYPE' => 'ERROR',
								);

								$arResult['SHOW_SMS_FIELD'] = false;
							}
						}
					}
				}
				else{
					// show captcha in future
					$_SESSION['BX_LOGIN_NEED_CAPTCHA'] = true;

					$arResult['ERROR_MESSAGE'] = array(
						'MESSAGE' => GetMessage('PHONE_AUTH_CODE_SENT_ERROR_PHONE_NOT_FINDED').'<br />',
						'TYPE' => 'ERROR',
					);
				}
			}

			$arResult['ERROR'] = $arResult['ERROR_MESSAGE'] && $arResult['ERROR_MESSAGE']['TYPE'] === 'ERROR';
			$_POST['USER_PHONE_NUMBER'] = $_REQUEST['USER_PHONE_NUMBER'] = $phoneNumber;

			if($arResult['SMS_REQUEST'] && $arResult['ERROR']){
				$arResult['SHOW_SMS_FIELD'] = true;
			}

			unset(
				$_POST['Login'],
				$_REQUEST['Login'],
				$arResult["POST"]['Login'],
				$_POST['USER_PHONE_NUMBER'],
				$_REQUEST['USER_PHONE_NUMBER'],
				$arResult["POST"]['USER_PHONE_NUMBER'],
				$_POST['SIGNED_DATA'],
				$_REQUEST['SIGNED_DATA'],
				$arResult["POST"]['SIGNED_DATA'],
				$_POST['SMS_CODE'],
				$_REQUEST['SMS_CODE'],
				$arResult["POST"]['SMS_CODE'],
				$_POST['POPUP_AUTH'],
				$_REQUEST['POPUP_AUTH'],
				$arResult["POST"]['POPUP_AUTH'],
				$_POST['captcha_word'],
				$_REQUEST['captcha_word'],
				$arResult["POST"]['captcha_word'],
				$_POST['captcha_sid'],
				$_REQUEST['captcha_sid'],
				$arResult["POST"]['captcha_sid'],
				$_POST['USER_LOGIN'],
				$_REQUEST['USER_LOGIN'],
				$arResult["POST"]['USER_LOGIN'],
				$_POST['USER_PASSWORD'],
				$_REQUEST['USER_PASSWORD'],
				$arResult["POST"]['USER_PASSWORD']
			);

			if($arResult['CAPTCHA_CODE']){
				$arResult['ONLY_PHONE_CAPTCHA'] = 'N';
			}
			else{
				if($GLOBALS['APPLICATION']->NeedCAPTHAForLogin($arResult['USER_LOGIN'])){
					// add capctha if need
					$arResult['ONLY_PHONE_CAPTCHA'] = 'Y';
					$arResult['CAPTCHA_CODE'] = $GLOBALS['APPLICATION']->CaptchaGetCode();
				}
			}
		}
	}
}
?>