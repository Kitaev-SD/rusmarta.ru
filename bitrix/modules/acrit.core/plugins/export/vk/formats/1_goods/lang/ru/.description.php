<?
use \Acrit\Core\Export\Plugins\Vk;

$accessTokenUrl = Vk::getAccessUrl();

$strMessPrefix = 'ACRIT_EXP_VK_GOODS_';

$MESS[$strMessPrefix.'HEADER_FIRST'] = '?????? ????????';
$MESS[$strMessPrefix.'INTRO'] = '?????? ????? ?????????? ???????????? ? <a href="https://vk.com" target="_blank">vk.com</a> ? ??????? ????? ??????, ???? ??? ??? ?? ???????. ??? ???????? ?????? ????? ?????????? ?????? ?????????? ? ?????? ? ? ????????.';
$MESS[$strMessPrefix.'GET_VK_ACCESS_TITLE'] = '????????? ??????? ? ?????? ??????';
$MESS[$strMessPrefix.'GET_VK_ACCESS'] = '??? ?????? ??????? ?????????? ??????? Access Token (???? ???????) ? ID ?????? ?????????.<br/><br/>
??? ????????? Access Token:
<ul>
	<li>????????? ?? <a href="'.$accessTokenUrl.'" target="_blank">??????</a>,</li>
	<li>? ??????????? ????, ???? ???????? ??????, ????????????? ? ??????????? ????? (<i>???? ????? ???????? ?? ?????????? - ??????, ??? ??? ???? ??????? ??????)</i>,</li>
	<li>????? ???? ??? ?? ?????? ????? ????????? "??????????, ?? ????????? ?????? ...", <b>?????????? ????? ?? ???????? ?????? ? ???????? ??? ? ???? "Access Token"</b>,</li>
	<li>????? ????? ????????????? ????????? ? ??????????. ??????.</li>
</ul>
<p>????? ?????? ID ??????, ????? ??????? ? ??? ??????, ? ????? ??????? ? ?????? "<b>??????????</b>" (? ?????? ???????, ??? ????????? ??????). ????? ???????? ???????? ?????? ?????? ????????: https://vk.com/stats?gid=<b>ID_??????</b> - ? ????? ?????? ????? ID ????? ??????.</p>';
$MESS[$strMessPrefix.'RECOMMEND_TITLE'] = '????????????';
$MESS[$strMessPrefix.'HEADER_WARNING_INFO'] = '?????? ???????!';
$MESS[$strMessPrefix.'WARNING_INFO'] = '<p>API VK ????? ??????????? ?? ?????????? ????????: ?? ????? 5-?? ???????? ? ???????. ??? ??????, ??? ??????? ????? ????????? ?????????? ????????, ?.?. ??????? ?????? ?????? ??????? ?????????? ???????? ? API</p>
<p>??? ?? ????????, ??? API VK ???????? ?????? ?? ????????? ?????. ???????, ????? ???????? ????????, ??? ????????????? ?????????? ???????????????? ????????, ????? ????????? ???????? ??????? ??????????? ????????, ?? ??????? ????, ? 10 ?????.</p>';
$MESS[$strMessPrefix.'CATEGORY_REDEFINITION'] = '?? ???????? ??? ??????? ????????? ????????? ????????????? ???????? (??????? ??????? ????? ??????????? ?????? vk.com), ????? ???????? ?? ????? ????????. ??? ???????? ?????? ???????, ????? ? categoryId ??????????? ID ??????? ?????.';
$MESS[$strMessPrefix.'MAX_COUNT'] = '?????? ?????? ?????, ??? vk.com ????????? ????????? ?? ????? 15 ????? ??????? ? ???? ??????. ? ?? ???? ??? <strong>?? ?????????????</strong> ????????? ????? 1000 ???????, ? ? ????? &ndash; ????? 7000.';
$MESS[$strMessPrefix.'RUN_BY_PARTS'] = '??????? ????????? <strong>????????? ????????</strong>:
<ol>
	<li>?????????? ???????? "??????????? ?? ??? ???????" (??????? "????? ?????????") ?????? 100 ???????.</li>
    <li>?????? ??????? ??????, ????? ??????????????? ??? ??????? ??? ???????? ? ?????????? ?????? 100 ???????.</li>
    <li>??????????? ?????????? ?? ?????? ?????? 10 ????? (??????? "??????????").</li>
</ol>';
$MESS[$strMessPrefix.'OTHER_INFO'] = '<p>??? ????, ????? ? ??????? ????????? ?????? "??????", ?????????? ??? ?????? ?????????? ?????????? <a href="https://vk.com/app5792770" target="_blank">"??????? ???????"</a>. ????????? ??????? ?????: <a href="https://vk.com/page-19542789_53327576" target="_blank">https://vk.com/page-19542789_53327576</a>.</p>';
$MESS[$strMessPrefix.'ERRORS_DESCRIPTION'] = '???????? ????????? ??????';
$MESS[$strMessPrefix.'CONSOLE'] = '??????? Alt+C ??? ???????? PHP-???????.';

?>
