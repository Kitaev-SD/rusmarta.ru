<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Возможности");
?>
<p>В решении поддерживается множество UI элементов, которые Вы с легкостью можете использовать для развития сайта и добавления нового функционала.</p>

<div class="row"> 						
	<div class="col-md-3"> 							
		<p><a href="<?=SITE_DIR;?>info/more/typograpy/" class="icon-text" data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!"><i class="fa fa-user"></i>Оформление</a></p>
	</div>
	<div class="col-md-3"> 							
		<p><a href="<?=SITE_DIR;?>info/more/buttons/" class="icon-text" data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!"><i class="fa fa-book"></i>Кнопки</a></p>
	</div>
	<div class="col-md-3"> 							
		<p><a href="<?=SITE_DIR;?>info/more/icons/" class="icon-text" data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!"><i class="fa fa-trophy"></i>Иконки</a></p>
	</div>
	<div class="col-md-3"> 							
		<p><a href="<?=SITE_DIR;?>info/more/elements/" class="icon-text" data-toggle="tooltip" title="" data-original-title="Можно использовать Tooltip!"><i class="fa fa-user"></i>Элементы</a></p>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>