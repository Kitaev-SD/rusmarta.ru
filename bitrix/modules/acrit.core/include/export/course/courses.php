<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);
$strCourseLang = 'ACRIT_CORE_COURSE_';

$strUrl = 'https://maed.ru/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=main_web_--acrit&utm_content=text_ad';

$arCourses = [
	'nima-b-digital' => 'https://sale.maed.ru/learning-programs/nima-b-digital?utm_source=cpa_partner&utm_medium=offer&utm_campaign=nimadd_web_--acrit&utm_content=text_ad',
	'internet-marketing' => 'https://sale.maed.ru/learning-programs/internet-marketing?utm_source=cpa_partner&utm_medium=offer&utm_campaign=imarketolog_web_--acrit&utm_content=text_ad',
	'smm' => 'https://sale.maed.ru/learning-programs/smm?utm_source=cpa_partner&utm_medium=offer&utm_campaign=smm_web_--acrit&utm_content=text_ad',
	'content-marketing' => 'https://sale.maed.ru/learning-programs/content-marketing?utm_source=cpa_partner&utm_medium=offer&utm_campaign=kmr_web_--acrit&utm_content=text_ad',
	'international-marketing' => 'https://sale.maed.ru/international-marketing?utm_source=cpa_partner&utm_medium=offer&utm_campaign=nimaa_web_--acrit&utm_content=text_ad',
	'e-marketing-b2b' => 'https://sale.maed.ru/learning-programs/e-marketing-b2b/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=emb2b_web_--acrit&utm_content=text_ad',
	'nima-b' => 'https://maed.ru/learning-programs/nima-b/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=nima-b_web_--acrit&utm_content=text_ad',
	'smm-direktor-profession' => 'https://sale.maed.ru/smm-direktor-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=smmdir_web_--acrit&utm_content=text_ad',
	'ecommerce-marketer-profession' => 'https://sale.maed.ru/ecommerce-marketer-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=ecommerce_web_--acrit&utm_content=text_ad',
	'messenger-marketolog' => 'https://sale.maed.ru/messenger-marketolog/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=messenger_web_--acrit&utm_content=text_ad',
	'crm-marketer-profession' => 'https://sale.maed.ru/crm-marketer-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=profcrm_web_--acrit&utm_content=text_ad',
	'traffic-manager-performance' => 'https://sale.maed.ru/traffic-manager-performance/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=traffic_web_--acrit&utm_content=text_ad',
	'crm-integration-marketer' => 'https://sale.maed.ru/crm-integration-marketer/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=crm_web_--acrit&utm_content=text_ad',
	'lead-imarketer-profession' => 'https://sale.maed.ru/lead-imarketer-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=profim_web_--acrit&utm_content=text_ad',
	'marketplace-manager' => 'https://sale.maed.ru/marketplace-manager/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=marketplace_web_--acrit&utm_content=text_ad',
	'p_targetologist' => 'https://sale.maed.ru/p_targetologist/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=tarhetpro_web_--acrit&utm_content=text_ad',
	'wa-profi' => 'https://sale.maed.ru/wa-profi/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=webanal_web_--acrit&utm_content=text_ad',
	'seo-marketolog' => 'https://sale.maed.ru/seo-marketolog/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=seo_web_--acrit&utm_content=text_ad',
	'top_manager' => 'https://sale.maed.ru/top_manager/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=digitaltop_web_--acrit&utm_content=text_ad',
	'new_context' => 'https://sale.maed.ru/new_context/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=context_web_--acrit&utm_content=text_ad',
	'digital-direktor-profession' => 'https://sale.maed.ru/digital-direktor-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=profdd_web_--acrit&utm_content=text_ad',
	'b2b-direktor-profession' => 'https://sale.maed.ru/b2b-direktor-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=profb2b_web_--acrit&utm_content=text_ad',
	'marketer-analyst-profession' => 'https://sale.maed.ru/marketer-analyst-profession/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=profwebanal_web_--acrit&utm_content=text_ad',
	'email-marketolog' => 'https://sale.maed.ru/email-marketolog/?utm_source=cpa_partner&utm_medium=offer&utm_campaign=email_web_--acrit&utm_content=text_ad',
];

if(isset($arGet['code']) && strlen($arGet['code']) && isset($arCourses[$arGet['code']])){
	localRedirect($arCourses[$arGet['code']], true);
}
elseif(isset($arGet['goto']) && strlen($arGet['goto']) && isset($arCourses[$arGet['goto']])){
	localRedirect($arCourses[$arGet['goto']], true);
}

?>

<?
$obTabControl->BeginCustomField('COURSES', Helper::getMessage($strCourseLang.'LIST'));
?>
<style>
.acrit_exp_export_courses {
	margin:15px auto;
	max-width:800px;
}
.acrit_exp_export_courses ul{
	margin:0;
	padding:0;
}
.acrit_exp_export_courses ul li{
	display:block;
	margin-bottom:15px;
}
.acrit_exp_export_courses ul li a{
	background:#fefefe;
	border:1px solid #ddd;
	display:block;
	padding:15px;
	text-decoration:none;
	transition:0.2s;
}
.acrit_exp_export_courses ul li a:hover{
	background-color:#fff;
	box-shadow:0 1px 10px 0 rgb(0 0 0 / 20%);
}
.acrit_exp_export_courses ul li a .text{
	color:#2675d7;
	display:inline-block;
	font-size:15px;
	font-weight:bold;
	line-height:16px;
	padding:8px;
}
.acrit_exp_export_courses ul li a .more{
	background-color:silver;
	border-radius:8px;
	color:#ffffff;
	float:right;
	font-size:14px;
	font-weight:600;
	line-height:24px;
	margin-left:40px;
	padding:4px 12px;
	transition:background-color 0.1s ease-in-out, color 0.1s ease-in-out, border-color 0.1s ease-in-out;
}
.acrit_exp_export_courses ul li a:hover .more{
	background-color:#4a4cef;
}
.acrit_exp_export_courses ul li a:hover .more:hover{
	background-color:#fb1943;
}
.acrit_exp_export_courses ul li a:after{
	clear:both;
	content:'';
	display:block;
}
</style>
<tr>
	<td>
		<?/*
		<div>
			<a href="<?=$strUrl;?>" target="_blank"><?=$strUrl;?></a>
			<?=Helper::getMessage($strCourseLang.'DESCRIPTION');?>
		</div><br/>
		*/?>
	</td>
</tr>
<tr class="heading"><td><?=$obTabControl->GetCustomLabelHTML()?></td></tr>
<tr>
	<td>
		<div class="acrit_exp_export_courses">
			<ul>
				<?foreach($arCourses as $strCourseCode => $strCourseUrl):?>
					<li>
						<a href="<?=$APPLICATION->getCurPageParam('code='.$strCourseCode, ['code']);?>" target="_blank">
							<span class="text"><?=Helper::getMessage($strCourseLang.$strCourseCode);?></span>
							<span class="more"><?=Helper::getMessage($strCourseLang.'DETAIL');?></span>
						</a>
					</li>
				<?endforeach?>
			</ul>
		</div>
	</td>
</tr>
<?
$obTabControl->EndCustomField('COURSES');
