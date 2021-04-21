<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;
if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
CJSCore::Init(array("popup"));
if(\Bitrix\Main\Loader::includeModule("aspro.next"))
{
	global $arRegion;
	$arRegion = CNextRegionality::getCurrentRegion();
}
$arItems=CNext::getBasketItems();?>
<script>
	touchItemBlock('.catalog_item a');
	<?if(is_array($arItems["DELAY"]) && !empty($arItems["DELAY"])):?>
		<?foreach( $arItems["DELAY"] as $key=>$item ){?>
			$('.wish_item.to[data-item=<?=$key?>]').hide();
			$('.wish_item.in[data-item=<?=$key?>]').show();
			if ($('.wish_item[data-item=<?=$key?>]').find(".value.added").length) {
				$('.wish_item[data-item=<?=$key?>]').addClass("added");
				$('.wish_item[data-item=<?=$key?>]').find(".value").hide();
				$('.wish_item[data-item=<?=$key?>]').find(".value.added").css('display','block');
			}
		<?}?>
	<?endif;?>
	<?if(is_array($arItems["COMPARE"]) && !empty($arItems["COMPARE"])):?>
		<?foreach( $arItems["COMPARE"] as $key=>$item ){?>
			$('.compare_item.to[data-item=<?=$key?>]').hide();
			$('.compare_item.in[data-item=<?=$key?>]').show();
			if ($('.compare_item[data-item=<?=$key?>]').find(".value.added").length){
				$('.compare_item[data-item=<?=$key?>]').addClass("added");
				$('.compare_item[data-item=<?=$key?>]').find(".value").hide();
				$('.compare_item[data-item=<?=$key?>]').find(".value.added").css('display','block');
			}
		<?}?>
	<?endif;?>
	/*check mobile device*/
	if(jQuery.browser.mobile){
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
</script>
