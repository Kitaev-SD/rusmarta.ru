$(document).ready(function(){
	$('.catalog_block .catalog_item_wrapp .catalog_item .item_info:visible .item-title').sliceHeight({item:'.catalog_item', mobile: true});
	$('.catalog_block .catalog_item_wrapp .catalog_item .item_info:visible').sliceHeight({item:'.catalog_item', mobile: true});
	$('.catalog_block .catalog_item_wrapp .catalog_item:visible').sliceHeight({classNull: '.footer_button', item:'.catalog_item', mobile: true});

	SelectOfferProp = function(){
		// return false;
		var _this = $(this),
			obParams = {},
			obSelect = {},
			objUrl = parseUrlQuery(),
			add_url = '',
			container = _this.closest('.bx_catalog_item_scu');

		/* request params */
		obParams = {
			'PARAMS': _this.closest('.js_wrapper_items').data('params'),
			'ID': container.data('offer_id'),
			'SITE_ID': container.data('site_id'),
			'LINK_ID': container.data('id')+'_'+_this.closest('.tab').data('code'),
			'IBLOCK_ID': container.data('offer_iblockid'),
			'PROPERTY_ID': container.data('propertyid'),
			'DEPTH': _this.closest('.item_wrapper').index(),
			'VALUE': _this.data('onevalue'),
			'CLASS': 'inner_content',
			'PICTURE': _this.closest('.catalog_item_wrapp').find('.thumb img').attr('src'),
			'ARTICLE_NAME': _this.closest('.catalog_item_wrapp').find('.article_block').data('name'),
			'ARTICLE_VALUE': _this.closest('.catalog_item_wrapp').find('.article_block').data('value'),
		}
		/**/

		if("clear_cache" in objUrl)
		{
			if(objUrl.clear_cache == "Y")
				add_url += "?clear_cache=Y";
		}

		/* save selected values */
		for (i = 0; i < obParams.DEPTH+1; i++)
		{
			strName = 'PROP_'+container.find('.item_wrapper:eq('+i+') > div').data('id');
			obSelect[strName] = container.find('.item_wrapper:eq('+i+') li.item.active').data('onevalue');
			obParams[strName] = container.find('.item_wrapper:eq('+i+') li.item.active').data('onevalue');
		}

		// obParams.SELECTED = JSON.stringify(obSelect);
		/**/
		
		_this.siblings().removeClass('active');
		_this.addClass('active');

		/* get sku */
		$.ajax({
			url: arNextOptions['SITE_DIR']+'ajax/js_item_detail.php'+add_url,
			type: 'POST',
			data: obParams,
		}).success(function(html){
			var ob = BX.processHTML(html);BX.ajax.processScripts(ob.SCRIPT);
		})
	}
	$(document).on('click', '.bx_catalog_item_scu li.item', SelectOfferProp)
});

