<?IncludeModuleLangFile( __FILE__ );?>

<tr class="heading" align="center">
    <td colspan="2"><b><?=GetMessage( "ACRIT_GOOGLEMERCHANT_OP_GOOGLE_TITLE" )?></b></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_GOOGLEMERCHANT_OP_GOOGLE_NOTE" );?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detail-content-cell-l">
        <span id="hint_PROFILE[GOOGLE_GOOGLEFEED]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[GOOGLE_GOOGLEFEED]' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_OP_GOOGLE_GOOGLEFEED_HELP" )?>' );</script>
        <label for="PROFILE[GOOGLE_GOOGLEFEED]"><b><?=GetMessage( "ACRIT_GOOGLEMERCHANT_OP_GOOGLE_GOOGLEFEED" )?></b> </label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="text" size="30" name="PROFILE[GOOGLE_GOOGLEFEED]" value="<?=$arProfile["GOOGLE_GOOGLEFEED"];?>" />
    </td>
</tr>