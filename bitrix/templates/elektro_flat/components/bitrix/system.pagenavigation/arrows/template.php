<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$ClientID = 'navigation_'.$arResult['NavNum'];

if(!$arResult["NavShowAlways"]) {
	if($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}?>
<style type="text/css">
	.ajax_load_btn {
	    text-align: center;
	    margin: 15px 0px 0px;
	    border: 1px solid #dee0ee;
	    cursor: pointer;
	    float: left;
	    width: calc(100% - 20px);
	    //border-right: none;
	    //border-left: none;
	}
	.ajax_loading_btn {
	    text-align: center;
	    margin: 15px 0px 0px;
	    float: left;
	    width: calc(100% - 20px);
	    display: none;
	}
	.ajax_load_btn:hover {
	    border-color: #346699;
	}
	.more_text_ajax {
	    font-size: 13px;
	    line-height: 20px;
	    font-weight: 400;
	    cursor: pointer;
	    display: inline-block;
	    position: relative;
	    padding: 15px 0px 15px 34px;
	    color: #333;

	}
	.more_text_ajax:after {
	    content: "";
	    display: block;
	    position: absolute;
	    width: 19px;
	    height: 19px;
	    left: 5px;
	    margin-top: -10px;
	    top: 50%;
	    background: #ddd url(<?= SITE_TEMPLATE_PATH;?>/images/Show_more.svg) center no-repeat;
	    -webkit-transition: background 0.7s ease-in-out;
	    -moz-transition: background 0.7s ease-in-out;
	    -o-transition: background 0.7s ease-in-out;
	    transition: background 0.7s ease-in-out;
	    background-color: #346699;
	    -webkit-box-sizing: border-box;
    	-moz-box-sizing: border-box;
	    box-sizing: border-box;

	}
	.ajax_loading_btn .more_text_ajax:after {
	    content: "";
	    display: block;
	    position: absolute;
	    width: 19px;
	    height: 19px;
	    left: 5px;
	    margin-top: -10px;
	    top: 50%;
	    background: #ddd url(<?= SITE_TEMPLATE_PATH;?>/images/Show_more.svg) center no-repeat;
	    -webkit-transition: background 0.7s ease-in-out;
	    -moz-transition: background 0.7s ease-in-out;
	    -o-transition: background 0.7s ease-in-out;
	    transition: background 0.7s ease-in-out;
	    background-color: #346699;
	    -webkit-box-sizing: border-box;
    	-moz-box-sizing: border-box;
	    box-sizing: border-box;
	}
	.ajax_loading_btn.__loading .more_text_ajax:after {
		-webkit-animation: spinner .5s ease-out 10000;
	    animation: spinner .5s ease 10000;
	    -webkit-transform-style: preserve-3d;
	    -moz-transform-style: preserve-3d;
	    -ms-transform-style: preserve-3d;
	    transform-style: preserve-3d;
	}
	.ajax_load_btn:hover .more_text_ajax{
		color: #346699;
	}
	.ajax_load_btn:hover .more_text_ajax:after {
	    -webkit-animation: spinner .5s ease-out 1;
	    animation: spinner .5s ease 1;
	    -webkit-transform-style: preserve-3d;
	    -moz-transform-style: preserve-3d;
	    -ms-transform-style: preserve-3d;
	    transform-style: preserve-3d;
	}
	/* WebKit Opera */
	@-webkit-keyframes spinner{
		from{
			-webkit-transform:rotate(0deg);
		}
		to{
			-webkit-transform:rotate(360deg);
		}
	}
	/* Other */
	@keyframes spinner{
		from{
			-moz-transform:rotate(0deg);
			-ms-transform:rotate(0deg);
			transform:rotate(0deg);
		}
		to{
			-moz-transform:rotate(360deg);
			-ms-transform:rotate(360deg);
			transform:rotate(360deg);
		}
	}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$('body').on('click', '.ajax_load_btn', function(){
			var next_page = $('#navigation_1_next_page').attr('href');
			var pagination = $('.pagination');
			$.ajax({
				url: next_page,
				beforeSend: function(){
	                $(".pagination").remove();
	                $(".ajax_load_btn").remove();
	                $(".ajax_loading_btn").addClass('__loading').show();
	            },
				success: function(data) {
					items = $(data).find('.catalog-item-card');
					items.each(function(i,e){
						$(this).find('[data-lazy-src]').each(function(){
							$(this).attr('src', $(this).attr('data-lazy-src')).removeClass('data-lazy-src');
						})
					});
					pagination = $(data).find('.pagination');
					ajax_load_btn = $(data).find('.ajax_load_btn');
					$(".ajax_loading_btn").removeClass('__loading').hide();
					$('.ajax_loading_btn').before(items);
					if (pagination.find('#navigation_1_next_page').length >0){
						$('#catalog').append(ajax_load_btn);
					}
					$('#catalog').append(pagination);
					history.pushState(null, null, window.location.origin+next_page);
				}
			})
		})
	})
</script>
<div class="ajax_loading_btn">
	<span class="more_text_ajax"></span>
</div>
<div class="ajax_load_btn">
	<span class="more_text_ajax">Показать еще</span>
</div>
<div class="pagination">	
	<?$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
	$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
	
	if($arResult["bDescPageNumbering"] === true) {
		// to show always first and last pages
		$arResult["nStartPage"] = $arResult["NavPageCount"];
		$arResult["nEndPage"] = 1;

		$sPrevHref = '';
		if($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			$bPrevDisabled = false;
			if($arResult["bSavePage"]) {
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
			} else {
				if($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1)) {
					$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
				} else {
					$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
				}
			}
		} else {
			$bPrevDisabled = true;
		}
		
		$sNextHref = '';
		if($arResult["NavPageNomer"] > 1) {
			$bNextDisabled = false;
			$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
		} else {
			$bNextDisabled = true;
		}?>
		
		<ul>
			<?if(!$bPrevDisabled):?>
				<li class="first">
					<a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page"><?=GetMessage("PREVIOUS_PAGE")?></a>
				</li>		
			<?endif;
			$bFirst = true;
			$bPoints = false;
			
			do {
				$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
				if($arResult["nStartPage"] <= 2 || $arResult["NavPageCount"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2) {
					if($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
						<li class="active">
							<span class="nav-current-page"><?=$NavRecordGroupPrint?></span>
						</li>
					<?elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):?>
						<li>
							<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a>
						</li>
					<?else:?>
						<li>
							<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a>
						</li>
					<?endif;
					$bFirst = false;
					$bPoints = true;
				} else {
					if($bPoints) {?>
						<li class="points"><span>...</span></li>
						<?$bPoints = false;
					}
				}
				$arResult["nStartPage"]--;
			}
			while($arResult["nStartPage"] >= $arResult["nEndPage"]);
			
			if(!$bNextDisabled):?>
				<li class="last">
					<a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page"><?=GetMessage("NEXT_PAGE")?></a>
				</li>
			<?endif;?>
		</ul>
	
	<?} else {
		// to show always first and last pages
		$arResult["nStartPage"] = 1;
		$arResult["nEndPage"] = $arResult["NavPageCount"];

		$sPrevHref = '';
		if($arResult["NavPageNomer"] > 1) {
			$bPrevDisabled = false;
			
			if($arResult["bSavePage"] || $arResult["NavPageNomer"] > 2) {
				$sPrevHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1);
			} else {
				$sPrevHref = $arResult["sUrlPath"].$strNavQueryStringFull;
			}
		} else {
			$bPrevDisabled = true;
		}

		$sNextHref = '';
		if($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
			$bNextDisabled = false;
			$sNextHref = $arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1);
		} else {
			$bNextDisabled = true;
		}?>

		<ul>
			<?if(!$bPrevDisabled):?>
				<li class="first">
					<a href="<?=$sPrevHref;?>" id="<?=$ClientID?>_previous_page"><?=GetMessage("PREVIOUS_PAGE")?></a>
				</li>		
			<?endif;
			$bFirst = true;
			$bPoints = false;
			
			do {
				if($arResult["nStartPage"] <= 2 || $arResult["nEndPage"]-$arResult["nStartPage"] <= 1 || abs($arResult['nStartPage']-$arResult["NavPageNomer"])<=2) {
					if($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
						<li class="active">
							<span class="nav-current-page"><?=$arResult["nStartPage"]?></span>
						</li>
					<?elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):?>
						<li>
							<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a>
						</li>
					<?else:?>
						<li>
							<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a>
						</li>
					<?endif;
					$bFirst = false;
					$bPoints = true;
				} else {
					if($bPoints) {?>
						<li class="points"><span>...</span></li>
						<?$bPoints = false;
					}
				}
				$arResult["nStartPage"]++;
			}
			while($arResult["nStartPage"] <= $arResult["nEndPage"]);
			
			if(!$bNextDisabled):?>
				<li class="last">
					<a href="<?=$sNextHref;?>" id="<?=$ClientID?>_next_page"><?=GetMessage("NEXT_PAGE")?></a>
				</li>
			<?endif;?>
		</ul>
	<?}?>
</div>

<?CJSCore::Init();?>
<script type="text/javascript">
	//<![CDATA[
	BX.bind(document, "keydown", function (event) {
		event = event || window.event;
		if(!event.ctrlKey)
			return;

		var target = event.target || event.srcElement;
		if(target && target.nodeName && (target.nodeName.toUpperCase() == "INPUT" || target.nodeName.toUpperCase() == "TEXTAREA"))
			return;

		var key = (event.keyCode ? event.keyCode : (event.which ? event.which : null));
		if(!key)
			return;

		var link = null;
		if(key == 39)
			link = BX('<?=$ClientID?>_next_page');
		else if(key == 37)
			link = BX('<?=$ClientID?>_previous_page');

		if(link && link.href)
			document.location = link.href;
	});
	//]]>
</script>