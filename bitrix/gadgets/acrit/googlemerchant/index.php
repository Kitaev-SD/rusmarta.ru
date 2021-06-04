<?if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

if( !CModule::IncludeModule( "iblock" ) || !CModule::IncludeModule( "sale" ) ){
    return false;
}

require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php" );

if( !class_exists( "CGooglemerchantInformerGadget" ) ){
    class CGooglemerchantInformerGadget{
        public static $moduleId = "acrit.googlemerchant";
        public static $modulePrefix = "acrit";
        public static $timeExpire = 2592000;

        private function GetMarketModuleList(){
            $arModules = array();

            $arRequestedModules = CUpdateClientPartner::GetRequestedModules( self::$moduleId );
            $arUpdateList = CUpdateClientPartner::GetUpdatesList(
                $errorMessage,
                LANGUAGE_ID,
                "N",
                $arRequestedModules,
                array(
                    "fullmoduleinfo" => "Y"
                )
            );

            $arModules = $arUpdateList;

            return $arModules;
        }

        public static function GetMarketModulesInfo( &$arUpdateData ){
            $result = false;

            $arModuleList = self::GetMarketModuleList();
            if( is_array( $arModuleList["MODULE"] ) && !empty( $arModuleList["MODULE"] ) ){
                foreach( $arModuleList["MODULE"] as $arModule ){
                    if( stripos( $arModule["@"]["ID"], self::$modulePrefix ) !== false ){
                        if( $arModule["@"]["ID"] == self::$moduleId ){
                            if(is_array($arModule["#"]) && is_array($arModule["#"]["VERSION"])) {
                              foreach( $arModule["#"]["VERSION"] as $arModuleVersion ){
                                  if( is_array( $arModuleVersion["@"] ) && !empty( $arModuleVersion["@"] )
                                      && is_array( $arModuleVersion["#"] ) && !empty( $arModuleVersion["#"] )
                                  ){
                                      $arUpdateData[$arModuleVersion["@"]["ID"]] = $arModuleVersion["#"]["DESCRIPTION"][0]["#"];
                                  }
                              }
                            }

                            $moduleStatus = CModule::IncludeModuleEx( self::$moduleId );
                            $licenseDateTo = MakeTimeStamp( $arModule["@"]["DATE_TO"] );
                            if( ( $licenseDateTo - time() ) < self::$timeExpire ){
                                if( $licenseDateTo < time() ){
                                    $result = array(
                                        "IS_EXPIRE" => true,
                                        "NEED_BUY_UPDATE" => true,
                                        "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                        "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                    );
                                }
                                else{
                                    $result = array(
                                        "IS_EXPIRE" => false,
                                        "NEED_BUY_UPDATE" => true,
                                        "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                        "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                    );
                                }
                            }
                            else{
                                $result = array(
                                    "IS_EXPIRE" => false,
                                    "NEED_BUY_UPDATE" => false,
                                    "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                    "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                );
                            }
                        }
                    }
                }
            }

            return $result;
        }
    }
}

$arModuleNameParts = explode( ".", CGooglemerchantInformerGadget::$moduleId );

global $APPLICATION;
$APPLICATION->SetAdditionalCSS( "/bitrix/gadgets/".$arModuleNameParts[0]."/".$arModuleNameParts[1]."/styles.css" );

$arUpdateData = array();
$arModuleData = CGooglemerchantInformerGadget::GetMarketModulesInfo( $arUpdateData );

$showInfoRow = "";
if( $arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) > 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_DEMO_PERIOD_INFO" ).$arModuleData["DATE_EXPIRE"];
    $showBuyText = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_BUY_LICENCE_INFO" );
}
elseif( $arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) <= 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_DEMO_PERIOD_EXPIRED_INFO" );
    $showBuyText = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_BUY_LICENCE_INFO" );
}
elseif( !$arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) <= 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_LICENSE_PERIOD_EXPIRED_INFO" );
    $showBuyText = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_PROLONG_LICENCE_INFO" );
}
elseif( !$arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) > 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_LICENSE_PERIOD_INFO" ).$arModuleData["DATE_EXPIRE"];
    $showBuyText = false;
}

$buyLicenceUrl = "http://www.acrit-studio.ru/market/rabota-s-torgovymi-ploshchadkami/eksport-tovarov-na-torgovye-portaly/?action=BUY&id=94948";
$acritUrl = "https://www.acrit-studio.ru/";


?>

<div class="acrit-info-widget">
    <div class="show-gadget-title"><?=GetMessage( "GD_ACRIT_GOOGLEMERCHANT_NAME" );?></div>
    <div class="show-info-row"><?=$showInfoRow;?></div>
		
		<a href="<?=$acritUrl;?>" class="logo" target="_blank"></a>

    <?if( is_array( $arUpdateData ) && !empty( $arUpdateData ) ){?>
        <div class="show-info-row-version">
            <br/>
            <?foreach( $arUpdateData as $updateDataVersion => $updateDataDescription ){?>
                <b><?=GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_HAS_UPDATE" );?><?=$updateDataVersion?></b><br/>
                <?=$updateDataDescription;?>
                <br/><br/>
            <?}?>
        </div>
				<div class="updates-more-info">
					<a href="/bitrix/admin/update_system_partner.php?lang=<?=LANGUAGE_ID;?>&tabControl_active_tab=tab2" target="_blank">
						<?=GetMessage( "GD_ACRIT_GOOGLEMERCHANT_MORE" );?>
					</a>
				</div>
    <?} elseif ( is_array( $arUpdateData ) && empty( $arUpdateData ) ){?>
        <div class="show-info-row-version">
						<br/>
            <?=GetMessage( "GD_ACRIT_GOOGLEMERCHANT_EXPORT_UPDATED" );?>
        </div>
		<?}?>
    <?if( $showBuyText ){?>
        <a class="acrit-info-widget-button acrit-info-widget-button-buy " href="<?=$buyLicenceUrl;?>" target="_blank">
            <div class="acrit-info-widget-button-text"><?=$showBuyText;?></div>
        </a>
    <?}?>
</div>