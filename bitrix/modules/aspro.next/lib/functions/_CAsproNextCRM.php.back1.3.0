<?
namespace Aspro\Functions;

use Bitrix\Main\Application;
use Bitrix\Main\Web\DOM\Document;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\DOM\CssParser;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\Config\Option;

Loc::loadMessages(__FILE__);

if(!class_exists('CAsproNextCRM'))
{
	class CAsproNextCRM{
		const MODULE_ID = \CNext::moduleID;
		const FLOWLU_PATH = 'https://#DOMAIN#';
		const FLOWLU_SHORT_PATH = 'https://#DOMAIN#.flowlu.ru';
		const AMO_CRM_PATH = 'https://#DOMAIN#.amocrm.ru';

		public static $arCrmFileds = array(
			'MAIN' => array(
				'FORM_NAME' => '',
				'FORM_SID' => '',
				'SITE_ID' => '',
				'RESULT_ID' => '',
				'FORM_ALL' => '',
				'FORM_ALL_HTML' => '',
			),
			'FLOWLU' => array(
				'no' => '',
				'name' => '',
				'description' => '',
				'budget' => '',
				'contact_name' => '',
				'contact_phone' => '',
				'contact_mobile' => '',
				'contact_email' => '',
				'contact_web' => '',
				'contact_position' => '',
				'contact_company' => '',
				'start_date' => '',
				'deadline' => '',
				'active' => '',
				'closing_date' => '',
				'closing_comment' => '',
			),
			'AMO_CRM' => array(
				'leads' => array(
					'PROPS' => array(
						'name_leads' => '',
						'tags_leads' => '',
						'price_leads' => '',
						'notes_leads' => '',
					)
				),
				'contacts' => array(
					'PROPS' => array(
						'name_contacts' => '',
					)
				),
				'companies' => array(
					'PROPS' => array(
						'name_companies' => '',
					)
				)
			)
		);

		public static $arCrmMethods = array(
			'FLOWLU' => array(
				'CREATE_LEAD' => '/api/v1/module/crm/lead/create'
			),
			'AMO_CRM' => array(
				'CREATE_LEAD' => '/private/api/v2/json/leads/set/',
				'CREATE_CONTACT' => '/private/api/v2/json/contacts/set/',
				'CREATE_COMPANY' => '/private/api/v2/json/company/set/',
				'CREATE_NOTES' => '/private/api/v2/json/notes/set/',
				'AUTH' => '/private/api/auth.php?type=json',
			),
		);

		public static function addFileds($arFields = array()){
			self::$arCrmFileds = array_merge(self::$arCrmFileds, (array)$arFields);
		}

		public static function query($url, $method, $arPostFields = array(), $with_curl = false, $decode = false){
			global $APPLICATION;

			if($with_curl)
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				// curl_setopt($ch,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
				curl_setopt($ch, CURLOPT_URL, $url.$method);
				if (!empty($arPostFields)){
					curl_setopt($ch, CURLOPT_POST, true);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arPostFields));
				}
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
				curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
				
				curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
				curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
				
				$result_text = curl_exec ($ch);
			}
			else
			{
				$obHTTP = new \Bitrix\Main\Web\HttpClient();
				$obHTTP->setCharset('UTF-8');
				$result_text = $obHTTP->post($url.$method, $arPostFields);
			}

			if($decode)
				$result_text = $APPLICATION->ConvertCharset($result_text, 'UTF-8', LANG_CHARSET);
			return $result_text;
		}

		public static function authAmoCrm($site = SITE_ID){
			$url = str_replace('#DOMAIN#', Option::get(self::MODULE_ID, 'DOMAIN_AMO_CRM', '', $site), self::AMO_CRM_PATH);

			$domain = Option::get(self::MODULE_ID, "DOMAIN_AMO_CRM", "", $site);
			$token = Option::get(self::MODULE_ID, "TOKEN_AMO_CRM", "", $site);
			$login = Option::get(self::MODULE_ID, "LOGIN_AMO_CRM", "", $site);
			
			$arPostFields = array(
				"USER_HASH" => $token,
				"USER_LOGIN" => $login,
			);
			$result = json_decode(self::query($url, "/private/api/auth.php?type=json", $arPostFields, true, true), true);

			return $result;
		}
	}
}?>