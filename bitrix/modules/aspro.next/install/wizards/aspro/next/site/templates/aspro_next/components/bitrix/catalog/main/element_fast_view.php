<?$APPLICATION->ShowHeadScripts();?>

<div class="catalog_detail" itemscope itemtype="http://schema.org/Product">
	<?@include_once('page_blocks/'.$arTheme["USE_FAST_VIEW_PAGE_DETAIL"]["VALUE"].'.php');?>
</div>
<?if($arRegion)
{
	$arTagSeoMarks = array();
	foreach($arRegion as $key => $value)
	{
		if(strpos($key, 'PROPERTY_REGION_TAG') !== false && strpos($key, '_VALUE_ID') === false)
		{
			$tag_name = str_replace(array('PROPERTY_', '_VALUE'), '', $key);
			$arTagSeoMarks['#'.$tag_name.'#'] = $key;
		}
	}
	if($arTagSeoMarks)
		CNextRegionality::addSeoMarks($arTagSeoMarks);
}?>