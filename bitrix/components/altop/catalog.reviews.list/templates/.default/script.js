BX.namespace("BX.Catalog.Reviews");

BX.Catalog.Reviews = (function() {
	var CatalogReviews = function(params) {
		this.iblockType = params.iblockType || "";
		this.iblockId = params.iblockId || 0;
		this.elementId = params.elementId || 0;
		this.jsId = params.jsId || "";
		this.commentUrl = params.commentUrl || "";
		this.cacheType = params.cacheType || "";
		this.cacheTime = params.cacheTime || 0;		
		this.popupPath = params.popupPath || "";
		this.messages = params.messages;
		
		this.catalogReviewBtn = BX("catalogReviewAnch");
		if(!!this.catalogReviewBtn)
			BX.bind(this.catalogReviewBtn, "click", BX.proxy(this.OpenFormPopup, this));	
	};
	
	CatalogReviews.prototype.OpenFormPopup = function() {	
		var iblockType = this.iblockType,
			iblockId = this.iblockId,
			elementId = this.elementId,
			visualId = "catalog_review_" + this.jsId,
			commentUrl = this.commentUrl,
			cacheType = this.cacheType,
			cacheTime = this.cacheTime,			
			popupPath = this.popupPath;
		BX.PopupForm =
		{						
			arParams: {}
		};
		BX.PopupForm.popup = BX.PopupWindowManager.create(visualId, null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,			
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up forms review",
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: {content: BX.create("span", {html: this.messages.POPUP_TITLE})},
			content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",			
			events: {
				onAfterPopupShow: function()
				{
					if(!BX(visualId + "_form")) {
						BX.ajax.post(
							popupPath,
							{							
								sessid: BX.bitrix_sessid(),
								arParams: {
									IBLOCK_TYPE: iblockType,
									IBLOCK_ID: iblockId,
									ELEMENT_ID: elementId,
									ELEMENT_AREA_ID: visualId,
									COMMENT_URL: commentUrl,
									CACHE_TYPE: cacheType,
									CACHE_TIME: cacheTime
								}
							},
							BX.delegate(function(result)
							{
								this.setContent(result);
								var windowSize =  BX.GetWindowInnerSize(),
								windowScroll = BX.GetWindowScrollPos(),
								popupHeight = BX(visualId).offsetHeight;
								BX(visualId).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
							},
							this)
						);
					}
				}
			}			
		});
		
		var close = BX.findChild(BX(visualId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";

		BX.PopupForm.popup.show();		
	};
	
	return CatalogReviews;
})();