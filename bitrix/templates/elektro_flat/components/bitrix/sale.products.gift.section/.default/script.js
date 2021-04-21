(function() {
	'use strict';

	if(!!window.JCSaleProductsGiftSectionComponent)
		return;

	window.JCSaleProductsGiftSectionComponent = function(params) {
		this.container = document.querySelector('[data-entity="' + params.container + '"]');

		if(params.initiallyShowHeader) {
			BX.ready(BX.delegate(this.showHeader, this));
		}
	};

	window.JCSaleProductsGiftSectionComponent.prototype = {
		showHeader: function() {
			var parentNode = BX.findParent(this.container, {attr: {'data-entity': 'parent-container'}}),
				header;

			if(parentNode && BX.type.isDomNode(parentNode)) {
				header = parentNode.querySelector('[data-entity="header"');

				if(header && header.getAttribute('data-showed') != 'true') {
					header.style.display = '';
					header.style.opacity = 100;
				}
			}
		}
	}
})();