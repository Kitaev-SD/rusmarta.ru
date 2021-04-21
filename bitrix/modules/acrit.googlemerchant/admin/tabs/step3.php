<?php
IncludeModuleLangFile(__FILE__);

$types = $obProfileUtils->GetTypes();

$google = array(
    "google",
    "google_online",
);
?>

<tr class="heading" align="center">
    <td colspan="2">
        <b><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORTTYPE" )?></b>
    </td>
</tr>
<tr>
    <td>
        <span id="hint_PROFILE[TYPE]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[TYPE]' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORTTYPE_LABEL_HELP" )?>' );</script>
        <?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORTTYPE_LABEL" )?>        
    </td>
    <td>                                     
        <select name="PROFILE[TYPE]">
            <optgroup label="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORTTYPE_GOOGLE" )?>">
                <?foreach( $google as $typeCode ){?>
                     <?$selected = $arProfile["TYPE"] == $typeCode ? 'selected="selected"' : "";?>
                     <option value="<?=$typeCode?>" <?=$selected?>>&nbsp;&nbsp;&nbsp;<?=$types[$typeCode]["NAME"]?></option>
                <?}?>
            </optgroup>
        </select>
    </td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_REQUIREMENTS" );?></td></tr>
<tr>
    <td colspan="2" id="portal_requirements" style="text-align: center;">
        <a href="<?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?>" target="_blank"><?=$types[$arProfile["TYPE"]]["PORTAL_REQUIREMENTS"];?></a>
    </td>
</tr>
<tr class="heading"><td colspan="2"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_EXPORT_EXAMPLE" )?></td></tr>
<tr>
    <td colspan="2" style="background:#FDF6E3" id="description">
        <?
            if( $siteEncoding[SITE_CHARSET] != "utf8" )
                echo "<pre>",  htmlspecialchars( $types[$arProfile["TYPE"]]["EXAMPLE"], ENT_COMPAT | ENT_HTML401, $siteEncoding[SITE_CHARSET] ), "</pre>";
            else
                echo "<pre>",  htmlspecialchars( $types[$arProfile["TYPE"]]["EXAMPLE"] ), "</pre>";
        ?>
    </td>
</tr>