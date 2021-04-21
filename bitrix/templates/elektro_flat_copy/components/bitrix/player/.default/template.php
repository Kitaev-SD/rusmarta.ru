<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script src="http://rusmarta.ru/upload/medialibrary/688/688801dc1febd36062824726aae1c3d6.js"></script> 
<script src="http://rusmarta.ru/upload/medialibrary/cf6/cf6959906738467ae3b3daa2659e6e4d.js"></script>

<div id="videoplayer<?=$arResult["ID"]?>" style="width:<?= $arParams['WIDTH']?>px;height:<?= $arParams['HEIGHT']?>px;" ></div><script type="text/javascript">this.videoplayer<?=$arResult["ID"]?> = new Uppod({m:"video",uid:"videoplayer<?=$arResult["ID"]?>",comment:"Тест",file:"<?= $arResult['PATH']?>",st:"uppodvideo"});</script>