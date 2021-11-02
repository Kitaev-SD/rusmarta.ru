<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($arResult['Activated']){
?>
<div id="mc-review">
    <div id="mc-content">
        <?php if ((strtolower($DB->type)=="mysql")): ?>
        <div id="cackle-reviews">
            <?php

            $obj = $arResult['CACKLE_REVIEWS_OBJ'];

            $sum = 0;
            $count = 0;
            foreach ($obj as $review){
                if (preg_match('#^(CackleReview).#', $review['user_agent'])&&(int)$review['star'] > 0) {
                    $sum += $review['star'];
                    $count += 1;
                }
            }
            ?>
            <?php if (isset($arResult["productRating"])&&$arResult["productRating"]=='1'){ ?>
<div itemscope itemtype="http://schema.org/Product">
    <span itemprop="name"><?php echo $arResult['PRODUCT'] ?></span>
                <?php } ?>
                <?php if ($count > 0 && isset($arResult["aggregateRating"])&&$arResult["aggregateRating"]=='1') { ?>
                    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <div>Rating:
                            <span itemprop="ratingValue"><?php echo round($sum / $count, 1); ?></span> out of
                            <span itemprop="bestRating"><?php $br = 0; if ($br > 0) { echo $br; } else { ?>5<?php } ?></span> with
                            <span itemprop="reviewCount"><?php echo $count; ?></span> ratings
                        </div>
                    </div>
                <?php } ?>
                <?php if (isset($arResult["productRating"])&&$arResult["productRating"]=='1'){ ?>
</div>
            <?php } ?>
            <?php
                foreach ($obj as $review): ?>
                <div  id="cackle-review-<?php echo $review['id']; ?>">
                    <div id="cackle-review-header-<?php echo $review['review_id']; ?>" class="cackle-review-header">
                        <cite id="cackle-cite-<?php echo $review['id']; ?>">
                            <?php if($review['autor']) : ?>
                            <a id="cackle-author-user-<?php echo $review['id']; ?>" href="#" target="_blank" rel="nofollow"><?php echo $review['autor']; ?></a>
                            <?php else : ?>
                            <span id="cackle-author-user-<?php echo $review['id']; ?>"><?php echo $review['name']; ?></span>
                            <?php endif; ?>
                        </cite>
                    </div>
                    <div id="cackle-review-body-<?php echo $review['id']; ?>" class="cackle-review-pros-body">
                        <div id="cackle-review-pros-<?php echo $review['id']; ?>" class="cackle-review-pros">
                            <?php echo $review['pros']; ?>
                        </div>
                    </div>
                    <div id="cackle-review-body-<?php echo $review['id']; ?>" class="cackle-review-cons-body">
                        <div id="cackle-review-cons-<?php echo $review['id']; ?>" class="cackle-review-cons">
                            <?php echo $review['cons']; ?>
                        </div>
                    </div>
                    <div id="cackle-review-body-<?php echo $review['id']; ?>" class="cackle-review-comment-body">
                        <div id="cackle-review-comment-<?php echo $review['id']; ?>" class="cackle-review-comment">
                            <?php echo $review['comment']; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    cackle_widget = window.cackle_widget || [];
    cackle_widget.push({widget: 'Review', ssoPrimary:true, id: '<? echo $arResult['SITE_ID']; ?>', channel: '<? echo $arResult['MC_CHANNEL']; ?>'
    <?php if ($arResult['SSO_PARAM'] == 1) : ?>, ssoAuth: '<?php echo $arResult['SSO']; ?>' <?php endif;?>   });
    (function() {
        var mc = document.createElement('script');
        mc.type = 'text/javascript';
        mc.async = true;
        mc.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://cackle.me/widget.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(mc, s.nextSibling);
    })();


</script>

<?php
}
?>










