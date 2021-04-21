<?
namespace Aspro\Solution;

use Bitrix\Main\Application;
use Bitrix\Main\Web\DOM\Document;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\DOM\CssParser;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('catalog');

class CAsproMarketing{
    const MODULE_ID = \CNext::moduleID;
    const LOCAL_DIR_TMPL = "/modules/aspro.next/preset/template/";
    // const LOCAL_DIR_IMG = '/images/'.Cnext::moduleID.'/preset/template/';

    public static $arColoredHead = array("TYPE_2", "TYPE_6", "TYPE_7", "TYPE_8");

    public static function getById($templateName){
        $fileContent = '';
        $file = \Bitrix\Main\Loader::getLocal(static::LOCAL_DIR_TMPL.bx_basename($templateName).'.php');
        if($file && File::isFileExists($file))
        {
            $fullPathOfFile = $file;
            if($fullPathOfFile)
              $fileContent = File::getFileContents($fullPathOfFile);            
        }
        
        if (!empty($fileContent))
        {
          \Bitrix\Main\Loader::includeModule('fileman');
          if(\Bitrix\Fileman\Block\Editor::isContentSupported($fileContent))
            $fileContent = static::replaceTemplateByDefaultData($fileContent);
        }
        return $fileContent;
    }

    public static function senderTemplateList(){
        $arResultList = array();
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_all',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_all')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_basket',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_BASKET'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_basket')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_basket_coupon',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_BASKET_COUPON'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_basket_coupon')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_yandex_market',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_YANDEX_MARKET'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_yandex_market')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_order_info',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_ORDER_INFO'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_order_info')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_sale',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_SALE'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_sale')
        );
        $arResultList[] = array(
            'TYPE' => 'ADDITIONAL',
            'ID' => 'aspro_no_auth',
            'NAME' => Loc::getMessage('ASPRO_TEMPLATE_NO_AUTH'),
            'ICON' => '/bitrix/images/sender/preset/template/1column1.png',
            'HTML' => static::getById('aspro_no_auth')
        );
        return $arResultList;
    }

    public static function replaceTemplateByDefaultData($template){
        // include CMainPage
        require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
        // get site_id by host
        $site_id = \CMainPage::GetSiteByHost();
        if(!$site_id)
            $site_id = "s1";

        // get site info
        $arSite = \CSite::GetByID($site_id)->Fetch();
        $arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
        if(!strlen($arSite['DOC_ROOT']))
        {
            $arSite['DOC_ROOT'] = Application::getDocumentRoot();
        }
        $arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
        $siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);
        $siteProtocol = \Bitrix\Main\Config\Option::get("sender", "link_protocol", "");
        $siteProtocol = ($siteProtocol ? $siteProtocol : "http");
        $siteAddress = $arSite['SERVER_NAME'];
        $siteAddressFull = $siteProtocol."://".$arSite['SERVER_NAME'];

        //get iblock linked to site
        \Bitrix\Main\Loader::includeModule('iblock');
        $arIblocks = array();
        $dbIblock = \CIBlock::GetList(array(), array("ACTIVE" => "Y", "LID" => $site_id));
        while($arIblock = $dbIblock->Fetch())
        {
            $arIblocks[$arIblock["IBLOCK_TYPE_ID"]][$arIblock["CODE"]][] = $arIblock["ID"];
        }
        $tizer_iblock_id = ($arIblocks["aspro_next_content"]["aspro_next_tizers"][0] ? $arIblocks["aspro_next_content"]["aspro_next_tizers"][0] : 0);
        $sale_iblock_id = ($arIblocks["aspro_next_content"]["aspro_next_stock"][0] ? $arIblocks["aspro_next_content"]["aspro_next_stock"][0] : 0);

        //get vars
        $arLogo = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "LOGO_IMAGE", false, $site_id);
        $color_theme = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "BASE_COLOR", 9, $site_id);
        $custom_color_theme = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "BASE_COLOR_CUSTOM", false, $site_id);
        $type_head = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "HEAD", "TYPE_1", $site_id);
        $logo_color_bg = \Bitrix\Main\Config\Option::get(static::MODULE_ID, "COLORED_LOGO", "", $site_id);
        $order_email_sale = \Bitrix\Main\Config\Option::get("sale", "order_email", "sale@".$siteAddress);
        $order_email = ($arSite["EMAIL"] ? $arSite["EMAIL"] : $order_email_sale);

        $isColoredHead = (in_array($type_head, static::$arColoredHead) ? true : false);
        $type_color = ($isColoredHead ? "colored" : "main");
        $arModuleOptions = \CNext::GetBackParametrsValues($site_id);
        $arThemeValues = array();
        $bg_color = $bg_color_logo = "";
        $phone = '+0 000 000-00-00';
        $copyright = '&copy; '.$arSite["NAME"];
        $social = '';
        
        $iPhoneCount = (int)$arModuleOptions["HEADER_PHONES"];
        if($iPhoneCount)
        {
            $phone = '';
            for($i=0;$i<$iPhoneCount;++$i)
            {
                $phone = $arModuleOptions['HEADER_PHONES_array_PHONE_VALUE_'.$i];
                $href = 'tel:'.str_replace(array(' ', '-', '(', ')'), '', $phone);
                $phone .= '<a rel="nofollow" href="'.$href.'" class="dark-color">'.$phone.'</a><br>';
            }
        }

        $bg_image = $siteAddressFull.'/bitrix/templates/'.str_replace(".", "_", static::MODULE_ID).'/images/phone_mail.png';
        $phonePath = $siteDir.'/include/phone.php';
        $copyrightPath = $siteDir.'/include/footer/copy/copyright.php';
        $socialPath = $siteDir.'/include/footer/social.info.next.default.php';
        
        if($arLogo == serialize(array()) || $arLogo == false)
        {
            $logo_src = "/include/logo.png";
        }
        else
        {
            $arLogoValue = unserialize($arLogo);
            $logo_src = \CFIle::GetPath(current($arLogoValue));
        }

        $bg_color = \CNext::$arParametrsList['MAIN']['OPTIONS']['BASE_COLOR']['LIST'][$color_theme]['COLOR'];
        if($color_theme == "CUSTOM")
            $bg_color = "#".$custom_color_theme;

        $theme_color =  $bg_color;

        $bg_color_logo = $bg_color;
        if($logo_color_bg == "Y")
            $bg_color_logo = "";
        $bg_color = "#fff";

 
        if(File::isFileExists($copyrightPath))
        {
            $copyright = File::getFileContents($copyrightPath);
            // cut php
            $pattern = '/<\?(.*)\?>/is';
            preg_match($pattern, $copyright,$matches);
            if($matches[1])
                $copyright = str_replace(array($matches[1], '<?', '?>'), "", $copyright);            
        }
        if(File::isFileExists($socialPath))
        {
            $social = '';
            $social = File::getFileContents($socialPath);
            $social = str_replace(array('$APPLICATION->IncludeComponent', '.default'), array('EventMessageThemeCompiler::includeComponent', 'mail'), $social);
        }
       
        \Bitrix\Main\Loader::includeModule('sale');
        \Bitrix\Main\Loader::includeModule('catalog');

        $themeContent = File::getFileContents(\Bitrix\Main\Loader::getLocal(static::LOCAL_DIR_TMPL.'theme.php'));
        return str_replace(
            array(
            '%TEMPLATE_CONTENT%', '%TYPE_COLOR%', '%SITE_ADDRESS%', '%BG_COLOR%', '%BG_COLOR_LOGO%', '%LOGO_PATH_HEADER%', '%IMAGE_URL%', '%PHONE%', '%THEME_COLOR%', 
            '%BUTTON%', 
            '%HEADER%', '%HEADER_FORGOT_BASKET%', '%HEADER_YANDEX_MARKET%', '%HEADER_SALE%', '%HEADER_NO_AUTH%', '%HEADER_ORDER_INFO%',
            '%SUB_HEADER%', '%TEXT1%', '%TEXT_BASKET%', '%TEXT_BASKET_COUPON_UNDER%', '%TEXT_BASKET_COUPON_BOTTOM%', '%TEXT_YANDEX_MARKET_UNDER%', '%TEXT_YANDEX_MARKET_BOTTOM%', '%TEXT_YANDEX_MARKET_BOTTOM2%', '%TEXT_SALE%', '%TEXT_NO_AUTH_UNDER%', '%TEXT_NO_AUTH_BOTTOM%', '%TEXT_ORDER_INFO%', '%BASKET_PAGE_LINK%', '%ORDER_PAGE_LINK%', '%BASKET_TITLE%', '%MORE_ITEMS%', '%TEXT_ORDER_INFO_BOTTOM%', '%SHOW_CATALOG_LINK%',
            '%PERSONAL_BIG_DATA%', '%BUTTON_REVIEW%', '%NO_AUTH_GOODS_TITLE%',
            '%TIZER_IBLOCK_ID%', '%SALE_IBLOCK_ID%', '%COPYRIGHT%', '%SOCIAL%', '%VK%',
            '%SITE_ID%', '%MODULE_ID%',
            '%UNSUB_TEXT%', '%UNSUB_LINK_TEXT%'
            ),
            array(
            $template, $type_color, $siteAddressFull, $bg_color, $bg_color_logo, $logo_src, $bg_image, $phone, $theme_color,
            Loc::getMessage('PRESET_TEMPLATE_LIST_BLANK_BUTTON'),
            Loc::getMessage('PRESET_TEMPLATE_LIST_BLANK_HEADER'),  Loc::getMessage('HEADER_FORGOT_BASKET'), Loc::getMessage('HEADER_YANDEX_MARKET'), Loc::getMessage('HEADER_SALE'), Loc::getMessage('HEADER_NO_AUTH'), Loc::getMessage('HEADER_ORDER_INFO'), 
            Loc::getMessage('WELCOME_TEXT'), Loc::getMessage('CUSTOM_TEXT'), Loc::getMessage('TEXT_BASKET', array('#SITE_ADDRESS#' => $siteAddressFull, '#EMAIL_SALE#' =>  $order_email)), Loc::getMessage('TEXT_BASKET_COUPON_UNDER', array('#SITE_ADDRESS#' => $siteAddressFull, '#EMAIL_SALE#' =>  $order_email)),Loc::getMessage('TEXT_BASKET_COUPON_BOTTOM', array('#EMAIL_SALE#' =>  $order_email)), Loc::getMessage('TEXT_YANDEX_MARKET_UNDER'), Loc::getMessage('TEXT_YANDEX_MARKET_BOTTOM'), Loc::getMessage('TEXT_YANDEX_MARKET_BOTTOM2'), Loc::getMessage('TEXT_SALE', array('#EMAIL_SALE#' =>  $order_email)), Loc::getMessage('TEXT_NO_AUTH_UNDER'), Loc::getMessage('TEXT_NO_AUTH_BOTTOM'), Loc::getMessage('TEXT_ORDER_INFO', array('#EMAIL_SALE#' =>  $order_email, '#PHONES#' => $phone)), Loc::getMessage('BASKET_PAGE_LINK'),  Loc::getMessage('ORDER_PAGE_LINK'), Loc::getMessage('BASKET_TITLE'), Loc::getMessage('MORE_ITEMS'), Loc::getMessage('TEXT_ORDER_INFO_BOTTOM'), Loc::getMessage('SHOW_CATALOG_LINK'),
            Loc::getMessage('PERSONAL_BIG_DATA'), Loc::getMessage('BUTTON_REVIEW'), Loc::getMessage('NO_AUTH_GOODS_TITLE'),
            $tizer_iblock_id, $sale_iblock_id, $copyright, $social, Loc::getMessage('VK'),
            $site_id, static::MODULE_ID,
            Loc::getMessage('UNSUB_TEXT', array('#SITE_ADDRESS#' => $siteAddressFull)), Loc::getMessage('UNSUB_LINK_TEXT'),
            ),
            $themeContent
        );
    }
}?>