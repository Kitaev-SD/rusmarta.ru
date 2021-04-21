<?php
class CASD_ChangeNofollow {
	public static function Handler(&$content) {
		// thanks guys from http://forum.searchengines.ru/showthread.php?t=209929
		if (!defined('ADMIN_SECTION') || ADMIN_SECTION!==true)
			$content = preg_replace_callback('#<a([^>]+?)href\s*=\s*(["\']*)\s*(http|https|ftp)://([^"\'\s>]+)\s*\\2([^>]*?)>(.*?)</a>#is',
									create_function(
									'$matches',
									'if (strpos($matches[0], "rel=")===false)
										return "<a$matches[1]href=$matches[2]$matches[3]://$matches[4]$matches[2]$matches[5] rel=\"nofollow\">$matches[6]</a>";
									else
										return $matches[0];'
								),
								$content);
	}
}