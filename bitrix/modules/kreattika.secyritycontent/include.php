<?
##########################################################################
# Name: kreattika.secyritycontent                                        #
# http://marketplace.1c-bitrix.ru/solutions/kreattika.secyritycontent/   #
# File: include.php                                                      #
# Version: 1.0.2                                                         #
# (c) 2011-2015 Kreattika, Sedov S.Y.                                    #
# Proprietary licensed                                                   #
# http://kreattika.ru/                                                   #
# mailto:info@kreattika.ru                                               #
##########################################################################
?>
<?
global $DBType;
IncludeModuleLangFile(__FILE__);

class CSecyrityContent
{
	private static $MODULE_ID = "kreattika.secyritycontent";

	public function AddSecurityContentScript()
	{
		global $APPLICATION, $USER;

		$MODULE_ID = self::$MODULE_ID;

		$ModuleInstallStatus = CModule::IncludeModuleEx($MODULE_ID);
		if ($ModuleInstallStatus == 1 || $ModuleInstallStatus == 2 ):

		#if (IsModuleInstalled($MODULE_ID)):
		
			if(!defined(ADMIN_SECTION) && ADMIN_SECTION!==true):
			
				$ModuleOn = COption::GetOptionString($MODULE_ID, "secyritycontent_on", "Y");

				if ($ModuleOn == "Y"):

					$ModuleDisableUserGroup = explode(",", COption::GetOptionString($MODULE_ID, "secyritycontent_disable_user_group"));

					$arCurUserGroup = $USER->GetUserGroupArray();

					$arFindUserGroup = array_intersect($arCurUserGroup, $ModuleDisableUserGroup);

					if( count($ModuleDisableUserGroup) > 0 ):
						if( count($arFindUserGroup) > 0 ):
							return;
						endif;
					endif;

					$ModuleSettingJquery = COption::GetOptionString($MODULE_ID, "secyritycontent_jquery");
					$ModuleSettingContentType = COption::GetOptionString($MODULE_ID, "secyritycontent_type");
					$ModuleSettingContentSite = COption::GetOptionString($MODULE_ID, "secyritycontent_site");

					if( isset($ModuleSettingContentSite) && !empty($ModuleSettingContentSite) ):
						if(SITE_ID!=$ModuleSettingContentSite):
							return;
						endif;
					endif;

					if($ModuleSettingJquery=="Y"):
						CJSCore::Init(array("jquery"));
					endif;

					if( $ModuleSettingContentType==1 || $ModuleSettingContentType==2 || $ModuleSettingContentType==3 ):
	
						$ModuleSettingCopy = COption::GetOptionString($MODULE_ID, "secyritycontent_copy");
						$ModuleSettingSelect = COption::GetOptionString($MODULE_ID, "secyritycontent_select");
						$ModuleSettingRightBtn = COption::GetOptionString($MODULE_ID, "secyritycontent_right_btn_mouse");
						$ModuleSettingDragAndDrop = COption::GetOptionString($MODULE_ID, "secyritycontent_drag_and_drop");

						$ScriptSelector = 'body';
						$ScriptCSS = '-webkit-touch-callout: none;';
						$ScriptText = '$(document).ready(function() {';
	
						if($ModuleSettingContentType==1):
							$ScriptSelector = 'body';
						elseif($ModuleSettingContentType==2):
							$ModuleSettingContentID = COption::GetOptionString($MODULE_ID, "secyritycontent_id");
							$ScriptSelector = '#'.$ModuleSettingContentID;
						elseif($ModuleSettingContentType==3):
							$ModuleSettingContentClass = COption::GetOptionString($MODULE_ID, "secyritycontent_class");
							$ScriptSelector = '.'.$ModuleSettingContentClass;
						endif;
	
						if($ModuleSettingCopy=="Y"):
							$ScriptText .= ' $(\''.$ScriptSelector.'\').attr(\'oncopy\',\'return false\');';
#$ScriptCSS .= ' -webkit-user-copy: none; -khtml-user-copy: none; -moz-user-copy: none; -ms-user-copy: none; -o-user-copy: none; user-copy: none;';
						endif;
	
						if($ModuleSettingSelect=="Y"):
							$ScriptText .= ' $(\''.$ScriptSelector.'\').attr(\'onselectstart\',\'return false\');';
							$ScriptCSS .= ' -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; -o-user-select: none; user-select: none;';
						endif;
	
						if($ModuleSettingRightBtn=="Y"):
							$ScriptText .= ' $(\''.$ScriptSelector.'\').attr(\'oncontextmenu\',\'return false\');';
						endif;
	
						if($ModuleSettingDragAndDrop=="Y"):
							$ScriptText .= ' $(\''.$ScriptSelector.'\').attr(\'ondragstart\',\'return false\');';
							$ScriptCSS .= ' -webkit-user-drag: none; -khtml-user-drag: none; -moz-user-drag: none; -ms-user-drag: none; -o-user-drag: none; user-drag: none;';
						endif;
	
						if(!empty($ScriptCSS)):
							$ScriptText .= ' $(\''.$ScriptSelector.'\').attr(\'style\', \''.$ScriptCSS.'\');';
						endif;
	
						$ScriptText .= ' });';

					elseif( $ModuleSettingContentType==4 || $ModuleSettingContentType==5 ):

						$ModuleSettingIncMess = COption::GetOptionString($MODULE_ID, "secyritycontent_include_message", GetMessage("KREATTIKA_SECURITYCONTENT_SET_INC_MESS"));
						$ScriptText = '';

					endif;

					$APPLICATION->AddHeadString('<script type="text/javascript">'.$ScriptText.'</script>',true);
				endif;
			endif;
		endif;
	}
}
?>