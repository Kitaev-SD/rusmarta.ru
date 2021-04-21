<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<div class="mail_soc_wrapper" style="text-align:center;padding-top:20px;font-size:0px;">
	<?if(!empty($arResult['SOCIAL_VK'])):?>
		<a href="<?=$arResult['SOCIAL_VK']?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/vk.png" alt="<?=GetMessage("VKONTAKTE")?>" title="<?=GetMessage("VKONTAKTE")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_ODNOKLASSNIKI'])):?>
		<a href="<?=$arResult['SOCIAL_ODNOKLASSNIKI']?>" target="_blank"  class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/odn.png" alt="<?=GetMessage("ODN")?>" title="<?=GetMessage("ODN")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_FACEBOOK'])):?>
		<a href="<?=$arResult['SOCIAL_FACEBOOK']?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/facebook.png" alt="<?=GetMessage("FACEBOOK")?>" title="<?=GetMessage("FACEBOOK")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_TWITTER'])):?>
		<a href="<?=$arResult["SOCIAL_TWITTER"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/twitter.png" alt="<?=GetMessage("TWITTER")?>" title="<?=GetMessage("TWITTER")?>" /> 
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_INSTAGRAM'])):?>
		<a href="<?=$arResult["SOCIAL_INSTAGRAM"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/inst.png" alt="<?=GetMessage("INST")?>" title="<?=GetMessage("INST")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_TELEGRAM'])):?>
		<a href="<?=$arResult["SOCIAL_TELEGRAM"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/telegram.png" alt="<?=GetMessage("TELEGRAM")?>" title="<?=GetMessage("TELEGRAM")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_MAIL'])):?>
		<a href="<?=$arResult["SOCIAL_MAIL"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/mail.png" alt="<?=GetMessage("MAIL")?>" title="<?=GetMessage("MAIL")?>" />
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_GOOGLEPLUS'])):?>
		<a href="<?=$arResult["SOCIAL_GOOGLEPLUS"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/gplus.png" alt="<?=GetMessage("GOOGLEPLUS")?>" title="<?=GetMessage("GOOGLEPLUS")?>" /> 
		</a>
	<?endif;?>
	<?if(!empty($arResult['SOCIAL_YOUTUBE'])):?>
		<a href="<?=$arResult["SOCIAL_YOUTUBE"]?>" target="_blank" class="mail_soc" style="display:inline-block;font-size:0px;padding:5px;">
			<img src="/bitrix/components/aspro/social.info.next/images/youtube.png" alt="<?=GetMessage("YOUTUBE")?>" title="<?=GetMessage("YOUTUBE")?>" /> 
		</a>
	<?endif;?>
</div>