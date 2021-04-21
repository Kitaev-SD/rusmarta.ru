<?
use Bitrix\Main\Config;
use Bitrix\Main\Localization;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CAsproAuth extends CBitrixComponent{
	protected $dbResult = array();
	protected $page;

	protected $errorsFatal = array();

	public function __construct( $component = null ){
		parent::__construct( $component );

		Localization\Loc::loadMessages(__FILE__);
	}

	protected function getPage(){

		$arDefaultUrlTemplates404 = array(
			"auth" => "",
			"registration" => "registration/",
			"forgot_password" => "forgot-password/",
			"change_password" => "change-password/",
			"confirm_password" => "confirm-password/",
			"confirm_registration" => "confirm-registration/",
		);

		$arVariables = array();

		if( $this->arParams["SEF_MODE"] == "Y" ){
			$arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates( $arDefaultUrlTemplates404,
																		   $this->arParams["SEF_URL_TEMPLATES"] );

			$componentPage = CComponentEngine::ParseComponentPath(
				$this->arParams["SEF_FOLDER"],
				$arUrlTemplates,
				$arVariables
			);

			if($_REQUEST)
			{
				if( $_REQUEST["registration"] == "yes" )
					$componentPage = "registration";
				elseif( $_REQUEST["forgot_password"] == "yes" )
					$componentPage = "forgot_password";
				elseif( $_REQUEST["change_password"] == "yes" )
					$componentPage = "change_password";
				elseif( $_REQUEST["confirm_password"] == "yes" )
					$componentPage = "confirm_password";
				elseif( $_REQUEST["confirm_registration"] == "yes" )
					$componentPage = "confirm_registration";
			}

			if( !$componentPage ){
				$componentPage = 'auth';
			}
			$this->arResult = array_merge(
				array(
					"SEF_FOLDER" => $this->arParams["SEF_FOLDER"],
					"URL_TEMPLATES" => $arUrlTemplates,
				),
				$this->arResult
			);
		}else{
			if( $_REQUEST["registration"] == "yes" )
				$componentPage = "registration";
			elseif( $_REQUEST["forgot_password"] == "yes" )
				$componentPage = "forgot_password";
			elseif( $_REQUEST["change_password"] == "yes" )
				$componentPage = "change_password";
			elseif( $_REQUEST["confirm_password"] == "yes" )
				$componentPage = "confirm_password";
			elseif( $_REQUEST["confirm_registration"] == "yes" )
				$componentPage = "confirm_registration";
			else
				$componentPage = "auth";
		}

		$this->page = $componentPage;
	}

	public function executeComponent(){
		try{
			$this->getPage();
		}catch( Exception $e ){
			$this->errorsFatal[htmlspecialcharsEx($e->getCode())] = htmlspecialcharsEx( $e->getMessage() );
		}

		// 24.05.2019: if unrem than not working forgot and change password by phone http://joxi.ru/brRnKB5U78eWKr
		/*if(isset($_REQUEST['backurl']) && $_REQUEST['backurl']) // fix ajax url
		{
			if($_REQUEST['backurl'] != $_SERVER['REQUEST_URI'])
			{
				global $APPLICATION;
				$_SERVER['REQUEST_URI'] = $_REQUEST['backurl'];
				$APPLICATION->reinitPath();
			}
		}*/

		global $arTheme;

		if($arTheme['CABINET']['VALUE'] != 'N')
			$this->includeComponentTemplate( $this->page );
		else
			LocalRedirect(SITE_DIR);	// "ERROR";
	}
}
?>