<?    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;


	$this->setFrameMode(true);

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();

	$COMPONENT_NAME = 'BXMAKER.GEOIP.LINE.';
?>

<div class="c-bxmaker_geoip_line_default_box">

	<span class="city_title_box"><?= Loc::getMessage($COMPONENT_NAME . 'CITY_LABEL'); ?></span>
	<span class="city_box">
		<span class="bxmaker_geoip_epilog_city_name city_name_box "><?=$oManager->getParam('DEFAULT_CITY', Loc::getMessage($COMPONENT_NAME . 'DEFAULT_CITY')); ?></span>


		<div class="city_question_box">
			<div class="city_question_title_box">
				<?= Loc::getMessage($COMPONENT_NAME . 'CITY_QUESTION', array(
					'#CITY#' => $oManager->getParam('DEFAULT_CITY', Loc::getMessage($COMPONENT_NAME . 'DEFAULT_CITY'))
				)); ?>
			</div>
			<div class="btn_box">
				<div class="btn_no"><?= Loc::getMessage($COMPONENT_NAME . 'CITY_QUESTION_NO'); ?></div>
				<div class="btn_yes"><?= Loc::getMessage($COMPONENT_NAME . 'CITY_QUESTION_YES'); ?></div>
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="city_info_box">
			<div class="city_info_content">
				<!--noindex-->
				<a class="btn-delivery-info" href="<?=$oManager->getParam('DELIVERY_PAGE', '/delivery/');?>" rel="nofollow"><?= Loc::getMessage($COMPONENT_NAME .'DELIVERY_MORE'); ?></a>
				<!--/noindex-->

			</div>
			<div class="btn_box">
				<div class="btn-city-change"><?= Loc::getMessage($COMPONENT_NAME .'BTN_CITY_CHANGE'); ?></div>
			</div>
			<div class="clearfix"></div>
		</div>

	</span>
</div>

<script type="text/javascript">
	BX.message({
		"BXMAKER_GEOIP_PARAMS_CITY_QUESTION_ON": "<?=$oManager->getParam('CITY_QUESTION_ON', 'Y');?>",
		"BXMAKER_GEOIP_PARAMS_DELIVERY_INFO_ON": "<?=$oManager->getParam('DELIVERY_INFO_ON', 'Y');?>",
		"BXMAKER_GEOIP_PARAMS_DELIVERY_PAGE": "<?=$oManager->getParam('DELIVERY_PAGE', '/delivery/');?>"
	});
</script>