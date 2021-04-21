<?
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

$strExample = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
	<url>
		<loc>http://www.example.org/business/article55.html</loc>
		<news:news>
			<news:publication>
				<news:name>The Example Times</news:name>
				<news:language>en</news:language>
			</news:publication>
			<news:publication_date>2008-12-23</news:publication_date>
			<news:title>Companies A, B in Merger Talks</news:title>
		</news:news>
	</url>
</urlset>

XML;
if(!Helper::isUtf()){
	$strExample = Helper::convertEncoding($strExample, 'UTF-8', 'CP1251');
}
?>
<div class="acrit-exp-plugin-example">
	<pre><code class="xml"><?=htmlspecialcharsbx($strExample);?></code></pre>
</div>
<script>
$('.acrit-exp-plugin-example pre code.xml').each(function(i, block) {
	highlighElement(block);
});
</script>
