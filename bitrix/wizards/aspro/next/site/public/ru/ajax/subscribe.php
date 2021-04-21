<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<a href="#" class="close jqmClose"><i></i></a>
<?$itemID = (int)$_GET['id'];?>
<form class="form subform">
	<input type="hidden" name="manyContact" value="N">
	<?=bitrix_sessid_post();?>
	<input type="hidden" name="itemId" value="<?=$itemID;?>">
	<input type="hidden" name="siteId" value="s1">
	<input type="hidden" name="contactFormSubmit" value="Y">

	<div class="form_head">
		<h2><?=\Bitrix\Main\Localization\Loc::getMessage('SUBSCRIBE_ITEM');?></h2>
	</div>
	<div class="form_body">
		<div class="mess"></div>
		<div class="form-control">
			<label><span><?=\Bitrix\Main\Localization\Loc::getMessage('SUBSCRIBE_ITEM_EMAIL');?>&nbsp;<span class="star">*</span></span></label>
			<input type="text" class="inputtext email" data-sid="CLIENT_NAME" required="" name="contact[1][user]" value="" aria-required="true">
		</div>
	</div>
	<div class="form_footer">
		<input type="submit" class="btn btn-default" value="<?=\Bitrix\Main\Localization\Loc::getMessage('SUBSCRIBE_SEND');?>" name="web_form_submit">
	</div>
</form>
<script type="text/javascript">
	$('input[name="siteId"]').val(arNextOptions['SITE_ID']);
	$('form.subform').validate({
			highlight: function( element ){
				$(element).parent().addClass('error');
			},
			unhighlight: function( element ){
				$(element).parent().removeClass('error');
			},
			submitHandler: function( form ){
				if( $('form.subform').valid() ){
					setTimeout(function() {
						$(form).find('button[type="submit"]').attr("disabled", "disabled");
					}, 300);

					BX.ajax.submitAjax($('form.subform')[0], {
						method : 'POST',
						url: '/bitrix/components/bitrix/catalog.product.subscribe/ajax.php',
						processData : true,
						onsuccess: function(response){
							resultForm = BX.parseJSON(response, {});
							if(resultForm.success)
							{
								var email = $('form.subform input.email').val();
								$('form.subform .form_body').html('<div class="success">'+resultForm.message+'</div>');
								$('form.subform .form_footer').html('');

								getActualBasket();
								$.ajax({
									url: arNextOptions['SITE_DIR'] + 'ajax/subscribe_sync.php',
									dataType: "json",
									type: "POST",
									data: BX.ajax.prepareData({
										sessid: BX.bitrix_sessid(),
										subscribe: 'Y',
										itemId: '<?=$itemID;?>',
										itemEmail: email,
										siteId: arNextOptions['SITE_ID']
									}),
									success: function(id){
										
									},
								})

								$('.to-subscribe[data-item=<?=$itemID;?>]').hide();
								$('.in-subscribe[data-item=<?=$itemID;?>]').show();
								
							}
							else if(resultForm.error)
							{
								var errorMessage = resultForm.message;
								if(resultForm.hasOwnProperty('typeName'))
								{
									errorMessage = resultForm.message.replace('USER_CONTACT',
										resultForm.typeName);
								}
								$('form.subform .form_body .mess').text(errorMessage);
							}
						}
					});
				}
			},
			errorPlacement: function( error, element ){
				error.insertBefore(element);
			},
			/*messages:{
		      licenses_popup: {
		        required : BX.message('JS_REQUIRED_LICENSES')
		      }
			}*/
		});
</script>
