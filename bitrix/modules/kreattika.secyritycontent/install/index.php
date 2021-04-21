<?
##########################################################################
# Name: kreattika.secyritycontent                                        #
# http://marketplace.1c-bitrix.ru/solutions/kreattika.secyritycontent/   #
# File: index.php                                                        #
# Version: 1.0.2                                                         #
# (c) 2011-2015 Kreattika, Sedov S.Y.                                    #
# Proprietary licensed                                                   #
# http://kreattika.ru/                                                   #
# mailto:info@kreattika.ru                                               #
##########################################################################
?>
<?
global $MESS;
$PathInstall = str_replace("\\", "/", __FILE__);
$PathInstall = substr($PathInstall, 0, strlen($PathInstall)-strlen("/index.php"));
IncludeModuleLangFile(__FILE__);

Class kreattika_secyritycontent extends CModule
{
        var $MODULE_ID = "kreattika.secyritycontent";
        var $MODULE_VERSION;
        var $MODULE_VERSION_DATE;
        var $MODULE_NAME;
        var $MODULE_DESCRIPTION;
        var $MODULE_CSS;
        var $MODULE_GROUP_RIGHTS = "Y";

        function kreattika_secyritycontent()
        {
                $arModuleVersion = array();

                $path = str_replace("\\", "/", __FILE__);
                $path = substr($path, 0, strlen($path) - strlen("/index.php"));
                include($path."/version.php");

                if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
                {
                        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
                        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
                }
                else
                {
                        $this->MODULE_VERSION = "1.0";
                        $this->MODULE_VERSION_DATE = "2010-11-01 00:00:00";
                }

                $this->MODULE_NAME = GetMessage("KREATTIKA_SECURITYCONTENT_MODULE_NAME");
                $this->MODULE_DESCRIPTION = GetMessage("KREATTIKA_SECURITYCONTENT_MODULE_DESCRIPTION");

                $this->PARTNER_NAME = GetMessage("KREATTIKA_PARTNER_NAME");
                $this->PARTNER_URI = GetMessage("KREATTIKA_PARTNER_URI");
        }
        function DoInstall()
        {
                global $DB, $APPLICATION, $step;

                $step = IntVal($step);
                if($step<2)
				{
					$GLOBALS["install_step"] = 1;
	                $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_SECURITYCONTENT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.secyritycontent/install/step1.php");
				}
				elseif($step==2)
				{
					if($this->InstallDB()){

						$this->InstallFiles();
                        $this->SendRequest('install');
		          	}

					$GLOBALS["errors"] = $this->errors;
					$GLOBALS["install_step"] = 2;
	                $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_SECURITYCONTENT_INSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.secyritycontent/install/step1.php");
				}

        }
        function DoUninstall()
        {
                global $DB, $APPLICATION, $step;
                $step = IntVal($step);

                if($step<2)
                {
                        $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_SECURITYCONTENT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.secyritycontent/install/unstep1.php");
                }
                elseif($step==2)
                {
                        $this->UnInstallDB(array(
                                "savedata" => $_REQUEST["savedata"],
                        ));
                        $this->UnInstallFiles();
                        $this->SendRequest('uninstall');
                        $APPLICATION->IncludeAdminFile(GetMessage("KREATTIKA_SECURITYCONTENT_UNINSTALL_TITLE"), $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.secyritycontent/install/unstep2.php");
                }

        }
        function InstallDB()
        {

                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                RegisterModule($this->MODULE_ID);
				#RegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "CSecyrityContent", "AddSecurityContentScript", "100");
                RegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CSecyrityContent", "AddSecurityContentScript", "100");
				return true;

        }
        function UnInstallDB($arParams = array())
        {

                global $DB, $DBType, $APPLICATION;
                $this->errors = false;

                if (!$arParams['savedata']) COption::RemoveOption($this->MODULE_ID);
                UnRegisterModule($this->MODULE_ID);
				#UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "CSecyrityContent", "AddSecurityContentScript");
                UnRegisterModuleDependences("main", "OnEpilog", $this->MODULE_ID, "CSecyrityContent", "AddSecurityContentScript");
                return true;

        }
        function InstallFiles()
        {
       			global $DB;

                //CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/kreattika.secyritycontent/install/components", $_SERVER["DOCUMENT_ROOT"]."/bitrix/components", true, true);
 
                return true;
        }
        function UnInstallFiles()
        {

                //DeleteDirFilesEx("/bitrix/components/kreattika/kreattika.secyritycontent");
                return true;

        }
        function SendRequest($type)
        {
                global $APPLICATION, $USER;

                if( in_array('curl', get_loaded_extensions()) ):
                        $arSitesEmail = array();
                        $MainEmail = COption::GetOptionString('main','email_from');
                        $obSites = CSite::GetList();
                        $ch=0;
                        while ($arSite = $obSites->Fetch()):
                                if( isset($arSite['EMAIL']) && !empty($arSite['EMAIL']) ):
                                        $arSiteEmail = array(
                                            "VALUE" => $arSite['EMAIL'],
                                            "DESCRIPTION" => $arSite['SERVER_NAME'],
                                        );
                                        $arSitesEmail['n'.$ch] = $arSiteEmail;
                                        $ch++;
                                endif;
                        endwhile;
                        $UserEmail = $USER->GetEmail();

                        $PostData = array(
                            'TYPE' => $type,
                            'SOLUTION_CODE' => $this->MODULE_ID,
                            'SOLUTION_VERSION' => $this->MODULE_VERSION,
                            'EMAIL_MAIN' => $MainEmail,
                            'EMAIL_SITE' => serialize($arSitesEmail),
                            'EMAIL_USER' => $UserEmail,
                            'DOMAIN' => $_SERVER['SERVER_NAME'],
                        );

                        if( isset($_REQUEST["uq"]) && !empty($_REQUEST["uq"]) ):
                                $PostData['UNINSTALL_QUESTION'] = $APPLICATION->ConvertCharset($_REQUEST["uq"], SITE_CHARSET, "UTF-8");
                        endif;
                        if( isset($_REQUEST["utext"]) && !empty($_REQUEST["utext"]) ):
                                $PostData['UNINSTALL_DETAIL_TEXT'] = $APPLICATION->ConvertCharset($_REQUEST["utext"], SITE_CHARSET, "UTF-8");
                        endif;

                        $ch = curl_init( 'http://kreattika.ru/s/' );
                        curl_setopt ( $ch, CURLOPT_HEADER, false );
                        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, false );
                        curl_setopt ( $ch, CURLOPT_POST, true );
                        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $PostData );
                        $data = curl_exec($ch);
                        curl_close($ch);
                endif;
        }

}
?>