<?
$arHost = explode( ":", $_SERVER["HTTP_HOST"]);
$_SERVER["HTTP_HOST"] = $arHost[0];
$hostname = $_SERVER['HTTP_HOST'];

function echoTextFile($file) {
	if (! file_exists($file)) return false;
	if (! is_readable($file)) return false;

	$timestamp = filemtime($file);
	$tsstring = gmdate('D, d M Y H:i:s ', $timestamp) . 'GMT';
	$etag = md5($file . $timestamp);

	header('Content-Type: application/xml');
	header('Content-Length: '.filesize($file));
	header("Last-Modified: $tsstring");
    header("ETag: \"{$etag}\"");

	readfile($file);

	return true;
}

$sitemapHost = dirname(__FILE__) . "/aspro_regions/sitemap/sitemap_{$hostname}.xml";
$sitemapDefault = dirname(__FILE__) . "/sitemap.xml";

if(!echoTextFile($sitemapHost) && !echoTextFile($sitemapDefault)) 
{
	header('HTTP/1.0 404 Not Found');
}
