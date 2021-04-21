BX.ready(function(){
	var workarea = BX('adm-workarea');
	if(workarea){
		var divs = BX.findChild(workarea, {tag: 'div', class: 'aspro_property_service--hidden'}, true, true);
		if(divs){
			for(var i in divs){
				var a = BX.findPreviousSibling(divs[i], {tag: 'a'});
				if(a){
					BX.bind(a, 'click', function(e){
						BX.PreventDefault(e);
						var a = e.target;
						if(div = BX.findNextSibling(a, {tag: 'div'})){
							BX.removeClass(div, 'aspro_property_service--hidden');
							BX.cleanNode(a, true);
						}
					});
				}
			}
		}
	}
});



BX.ready(function(){
	function getQueryExplanation(input){
		function __get(input){
			var item = BX.findParent(input, {className: 'aspro_property_yadirectquery_item'});
			if(item){
				var ic = BX.findChild(item, {tag: 'div', class: 'query_explanation_content'}, true);
				if(ic){
					if(!BX.hasClass(ic, 'hidden')){
						var v = input.value;
						var timerWaiter = setTimeout(function(){
							BX.adjust(ic, {html: '<div class="aspro-admin-waiter"></div>'});
							var w = BX.findChild(item, {tag: 'div', class: 'aspro-admin-waiter'}, true);
							timerWaiter = false;
						}, 200);

						BX.ajax({
					        url: location.href,
					        data: {
					        	action: 'getQueryExplanation',
					        	query: v
					        },
					        method: 'POST',
					        dataType: 'html',
					        processData: true,
					        emulateOnload: true,
					        start: true,
					        onsuccess: function(response){
					        	if(timerWaiter){
					        		clearTimeout(timerWaiter);
					        	}
					            ic.innerHTML = response;
					        },
					        onfailure: function(response){
					        	if(timerWaiter){
					        		clearTimeout(timerWaiter);
					        	}
					        	else{
						        	BX.cleanNode(ic);
					        	}
					        }
					    });
					}
				}
			}
		}

		if(explanationTimer){
			clearTimeout(explanationTimer);
		}
		explanationTimer = setTimeout(function(){
			explanationTimer = false;
			__get(input);
		}, 500)
	}

	var explanationTimer = false;
	var workarea = BX('adm-workarea');
	if(workarea){
		var items = BX.findChild(workarea, {tag: 'div', className: 'aspro_property_yadirectquery_item'}, true, true);
		if(items){
			for(var i in items){
				var input = BX.findChild(items[i], {tag: 'input'}, true);
				var btn = BX.findChild(items[i], {tag: 'a'}, true);

				if(input){
					BX.bind(input, 'change', function(e){
						var input = e.target;
						getQueryExplanation(input);
					});

					BX.bind(input, 'keyup', function(e){
						var input = e.target;
						getQueryExplanation(input);
					});
				}

				if(btn){
					BX.bind(btn, 'click', function(e){
						var btn = e.target;
						var item = BX.findParent(btn, {className: 'aspro_property_yadirectquery_item'});
						if(item){
							var ic = BX.findChild(item, {tag: 'div', class: 'query_explanation_content'}, true);
							if(ic){
								if(BX.hasClass(ic, 'hidden')){
									// hide link
									BX.addClass(btn, 'hidden');

									// show waiter and explanation content
									BX.adjust(ic, {html: '<div class="aspro-admin-waiter"></div>'});
									var w = BX.findChild(item, {tag: 'div', class: 'aspro-admin-waiter'}, true);

									// fire event
									BX.removeClass(ic, 'hidden');
									iv = BX.findChild(item, {tag: 'input', attribute: {type: 'text'}});
									if(iv){
										BX.fireEvent(iv, 'change');
									}
								}
								else{
									// hide explanation content and clear
									BX.addClass(ic, 'hidden');
									BX.cleanNode(ic);

									// remove send request by timer to get explanation
									if(explanationTimer){
										clearTimeout(explanationTimer);
									}
								}
							}
						}
					});
				}
			}
		}
	}
});
