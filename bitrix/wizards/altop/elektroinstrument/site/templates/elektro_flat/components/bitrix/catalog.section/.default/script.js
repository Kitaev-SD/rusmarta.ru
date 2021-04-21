(function() {
	'use strict';

	if (!!window.JCCatalogSectionComponent)
		return;

	window.JCCatalogSectionComponent = function(params) {
		this.formPosting = false;
		this.siteId = params.siteId || '';
		this.ajaxId = params.ajaxId || '';
		this.template = params.template || '';
		this.componentPath = params.componentPath || '';
		this.parameters = BX.message('COMPONENT_PARAMS') || '';

		if(params.navParams) {
			this.navParams = {
				NavNum: params.navParams.NavNum || 1,
				NavPageNomer: parseInt(params.navParams.NavPageNomer) || 1,
				NavPageCount: parseInt(params.navParams.NavPageCount) || 1
			};
		}
		
		this.container = document.querySelector('[data-entity="' + params.container + '"]');
		this.showMoreButton = null;
		this.showMoreButtonMessage = null;
		this.showMoreButtonContainer = null;
		
		if(params.lazyLoad) {
			this.showMoreButton = document.querySelector('[data-use="show-more-' + this.navParams.NavNum + '"]');
			this.showMoreButtonMessage = this.showMoreButton.innerHTML;
			this.showMoreButtonContainer = document.querySelector('[data-entity="show-more-container"]');
			BX.bind(this.showMoreButton, 'click', BX.proxy(this.showMore, this));
		}

		if(params.loadOnScroll) {
			BX.bind(window, 'scroll', BX.proxy(this.loadOnScroll, this));
		}

		if(!!BX.hasClass(this.container, 'catalog-item-table-view')) {
			var itemsTable = this.container.querySelectorAll('[data-entity="item"]');
			if(!!itemsTable) {
				this.adjustItemsHeight(itemsTable);
				BX.bind(window, 'resize', BX.delegate(function() {this.adjustItemsHeight(false)}, this));
			}
		}
	};

	window.JCCatalogSectionComponent.prototype = {
		checkButton: function() {
			if(this.showMoreButton) {
				if(this.navParams.NavPageNomer == this.navParams.NavPageCount) {
					BX.remove(this.showMoreButtonContainer);
				} else {
					this.container.appendChild(this.showMoreButtonContainer);
				}
			}
		},

		enableButton: function() {
			if(this.showMoreButton) {
				BX.adjust(this.showMoreButton, {props: {disabled: false}});	
				this.showMoreButton.innerHTML = this.showMoreButtonMessage;
			}
		},

		disableButton: function() {
			if(this.showMoreButton) {
				BX.adjust(this.showMoreButton, {props: {disabled: true}});	
				this.showMoreButton.innerHTML = BX.message('BTN_MESSAGE_LAZY_LOAD_WAITER');
			}
		},

		loadOnScroll: function() {
			var scrollTop = BX.GetWindowScrollPos().scrollTop,
				containerBottom = BX.pos(this.container).bottom;

			if(scrollTop + window.innerHeight > containerBottom) {
				this.showMore();
			}
		},

		showMore: function() {
			if(this.navParams.NavPageNomer < this.navParams.NavPageCount) {
				var data = {};
				data['action'] = 'showMore';
				data['PAGEN_' + this.navParams.NavNum] = this.navParams.NavPageNomer + 1;

				if(!this.formPosting) {
					this.formPosting = true;
					this.disableButton();
					this.sendRequest(data);
				}
			}
		},

		adjustItemsHeight: function(items) {
			setTimeout(BX.delegate(function() {
				if(!items)
					items = this.container.querySelectorAll('[data-entity="item"]');

				var i, itemHeight, itemMaxHeight = 0;			
				
				for(i = 0; i < items.length; i++) {
					items[i].style.height = 'auto';
					itemHeight = items[i].clientHeight;
					if(itemHeight > itemMaxHeight) {
						itemMaxHeight = itemHeight;
					}
				}
				for(i = 0; i < items.length; i++) {
					items[i].style.height = itemMaxHeight + 'px';
				}
			}, this), 1);
		},
		
		sendRequest: function(data) {
			var defaultData = {
				siteId: this.siteId,
				template: this.template,
				parameters: this.parameters
			};

			if(this.ajaxId) {
				defaultData.AJAX_ID = this.ajaxId;
			}

			BX.ajax({
				url: this.componentPath + '/ajax.php' + (document.location.href.indexOf('clear_cache=Y') !== -1 ? '?clear_cache=Y' : ''),
				method: 'POST',
				dataType: 'json',
				timeout: 60,
				data: BX.merge(defaultData, data),
				onsuccess: BX.delegate(function(result) {
					if(!result || !result.JS)
						return;

					BX.ajax.processScripts(
						BX.processHTML(result.JS).SCRIPT,
						false,
						BX.delegate(function() {
							this.processShowMoreAction(result);
						}, this)
					);
				}, this)
			});
		},
		
		processShowMoreAction: function(result) {
			this.formPosting = false;
			this.enableButton();

			if(result) {
				this.navParams.NavPageNomer++;
				this.processItems(result.items);
				this.processPagination(result.pagination);
				this.checkButton();
			}
		},
		
		processItems: function(itemsHtml) {
			if(!itemsHtml)
				return;

			var container = this.container,
				processed = BX.processHTML(itemsHtml, false),
				temporaryNode = BX.create('DIV'),
				items, k;

			temporaryNode.innerHTML = processed.HTML;
			items = temporaryNode.querySelectorAll('[data-entity="item"]');

			if(items.length) {
				for(k in items) {
					if(items.hasOwnProperty(k)) {
						items[k].style.opacity = 0;
						container.appendChild(items[k]);
					}
				}
				
				new BX.easing({
					duration: 2000,
					start: {opacity: 0},
					finish: {opacity: 100},
					transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
					step: function(state) {
						for(var k in items) {
							if(items.hasOwnProperty(k)) {
								items[k].style.opacity = state.opacity / 100;
							}
						}
					},
					complete: BX.delegate(function() {
						for(var k in items) {
							if(items.hasOwnProperty(k)) {
								items[k].removeAttribute('style');
							}
						}
						if(!!BX.hasClass(container, 'catalog-item-table-view'))
							this.adjustItemsHeight(items);
					}, this)
				}).animate();
			}

			BX.ajax.processScripts(processed.SCRIPT);
		},

		processPagination: function(paginationHtml) {
			if(!paginationHtml)
				return;

			var pagination = document.querySelectorAll('[data-pagination-num="' + this.navParams.NavNum + '"]');
			for(var k in pagination) {
				if(pagination.hasOwnProperty(k)) {
					pagination[k].innerHTML = paginationHtml;
				}
			}
		}
	};
})();