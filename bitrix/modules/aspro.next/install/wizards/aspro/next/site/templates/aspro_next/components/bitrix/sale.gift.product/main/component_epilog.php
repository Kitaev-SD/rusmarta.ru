<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
if(\Bitrix\Main\Loader::includeModule("aspro.next"))
{
	global $arRegion;
	$arRegion = CNextRegionality::getCurrentRegion();
}
?>
<script type="text/javascript">
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