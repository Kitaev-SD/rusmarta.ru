(function() {
	'use strict';

	if(!!window.JCCatalogBigdataSectionComponent)
		return;

	window.JCCatalogBigdataSectionComponent = function(params) {		
		this.siteId = params.siteId || '';
		this.ajaxId = params.ajaxId || '';
		this.template = params.template || '';
		this.componentPath = params.componentPath || '';
		this.parameters = BX.message('BIGDATA_COMPONENT_PARAMS') || '';
		
		this.bigData = params.bigData || {enabled: false};
		this.container = document.querySelector('[data-entity="' + params.container + '"]');		

		if(this.bigData.enabled && BX.util.object_keys(this.bigData.rows).length > 0) {
			BX.cookie_prefix = this.bigData.js.cookiePrefix || '';
			BX.cookie_domain = this.bigData.js.cookieDomain || '';
			BX.current_server_time = this.bigData.js.serverTime;

			BX.ready(BX.delegate(this.bigDataLoad, this));
		}
	};

	window.JCCatalogBigdataSectionComponent.prototype = {
		bigDataLoad: function() {
			var url = 'https://analytics.bitrix.info/crecoms/v1_0/recoms.php',
				data = BX.ajax.prepareData(this.bigData.params);

			if(data) {
				url += (url.indexOf('?') !== -1 ? '&' : '?') + data;
			}
			
			var onReady = BX.delegate(function(result) {
				this.sendRequest({
					action: 'deferredLoad',
					bigData: 'Y',
					items: result && result.items || [],
					rid: result && result.id,
					count: this.bigData.count,
					rowsRange: this.bigData.rowsRange,
					shownIds: this.bigData.shownIds
				});
			}, this);

			BX.ajax({
				method: 'GET',
				dataType: 'json',
				url: url,
				timeout: 3,
				onsuccess: onReady,
				onfailure: onReady
			});
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
				onsuccess: BX.delegate(function(result){
					if(!result || !result.JS)
						return;

					BX.ajax.processScripts(
						BX.processHTML(result.JS).SCRIPT,
						false,
						BX.delegate(function() {							
							this.processDeferredLoadAction(result);
						}, this)
					);
				}, this)
			});
		},

		processDeferredLoadAction: function(result) {
			if(!result)
				return;
			
			this.processItems(result.items);
		},
		
		processItems: function(itemsHtml) {
			if(!itemsHtml)
				return;

			var processed = BX.processHTML(itemsHtml, false),
				temporaryNode = BX.create('DIV'),
				origItems, items, k;

			temporaryNode.innerHTML = processed.HTML;

			origItems = this.container.querySelectorAll('[data-entity="item"]');
			if(origItems.length) {
				BX.cleanNode(this.container);
				this.showHeader(false);
			} else {
				this.showHeader(true);
			}

			items = temporaryNode.querySelectorAll('[data-entity="item"]');
			for(k in items) {
				if(items.hasOwnProperty(k)) {
					items[k].style.opacity = 0;
					this.container.appendChild(items[k]);
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
				complete: function() {
					for(var k in items) {
						if(items.hasOwnProperty(k)) {
							items[k].removeAttribute('style');								
						}
					}
					var bigdataItemsTable = $('.bigdata-items [data-entity="item"]');
					if(!!bigdataItemsTable && bigdataItemsTable.length > 0) {
						$(window).resize(function() {
							adjustItemHeight(bigdataItemsTable);
						});
						adjustItemHeight(bigdataItemsTable);
					}
				}
			}).animate();

			BX.ajax.processScripts(processed.SCRIPT);
		},

		showHeader: function(animate) {
			var parentNode = BX.findParent(this.container, {attr: {'data-entity': 'parent-container'}}),
				header;

			if(parentNode && BX.type.isDomNode(parentNode)) {
				header = parentNode.querySelector('[data-entity="header"]');

				if(header && header.getAttribute('data-showed') != 'true') {
					header.style.display = '';
					
					if(animate) {
						this.animation = new BX.easing({
							duration: 2000,
							start: {opacity: 0},
							finish: {opacity: 100},
							transition: BX.easing.makeEaseOut(BX.easing.transitions.quad),
							step: function(state) {
								header.style.opacity = state.opacity / 100;
							},
							complete: function() {
								header.removeAttribute('style');
								header.setAttribute('data-showed', 'true');
							}
						});
						this.animation.animate();
					} else {
						header.style.opacity = 100;
					}
				}
			}
		}
	};
})();