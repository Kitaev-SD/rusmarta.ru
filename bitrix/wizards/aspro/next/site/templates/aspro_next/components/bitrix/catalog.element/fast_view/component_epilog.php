<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])){
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Bitrix\Main\Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency){?>
		<script type="text/javascript">
			BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
		</script>
	<?}
}?>
<?if(\Bitrix\Main\Loader::includeModule("aspro.next"))
{
	global $arRegion;
	$arRegion = CNextRegionality::getCurrentRegion();
}?>
<script type="text/javascript">
	var viewedCounter = {
		path: '/bitrix/components/bitrix/catalog.element/ajax.php',
		params: {
			AJAX: 'Y',
			SITE_ID: "<?= SITE_ID ?>",
			PRODUCT_ID: "<?= $arResult['ID'] ?>",
			PARENT_ID: "<?= $arResult['ID'] ?>"
		}
	};
	BX.ready(
		BX.defer(function(){
			BX.ajax.post(
				viewedCounter.path,
				viewedCounter.params
			);
		})		
	);
	/*check mobile device*/
	if(jQuery.browser.mobile){
		$('.hint span').remove();

		$('*[data-event="jqm"]').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			var _this = $(this);
			var name = _this.data('name');
			if(window.matchMedia('(min-width:992px)').matches)
			{
				if(!$(this).hasClass('clicked'))
				{
					$(this).addClass('clicked');
					$(this).jqmEx();
					$(this).trigger('click');
				}
				return false;
			}
			else if(name.length){
				var script = arNextOptions['SITE_DIR'] + 'form/';
				var paramsStr = ''; var arTriggerAttrs = {};
				$.each(_this.get(0).attributes, function(index, attr){
					var attrName = attr.nodeName;
					var attrValue = _this.attr(attrName);
					arTriggerAttrs[attrName] = attrValue;
					if(/^data\-param\-(.+)$/.test(attrName)){
						var key = attrName.match(/^data\-param\-(.+)$/)[1];
						paramsStr += key + '=' + attrValue + '&';
					}
				});

				var triggerAttrs = JSON.stringify(arTriggerAttrs);
				var encTriggerAttrs = encodeURIComponent(triggerAttrs);
				script += '?name=' + name + '&' + paramsStr + 'data-trigger=' + encTriggerAttrs;
				location.href = script;
			}
		});
	}

	viewItemCounter('<?=$arResult["ID"];?>','<?=current($arParams["PRICE_CODE"]);?>');
</script>