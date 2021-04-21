(function (window) {
	if(!!window.JCCatalogProductSubscribe) {
		return;
	}

    var subscribeButton = function(params) {
		subscribeButton.superclass.constructor.apply(this, arguments);		
		this.buttonNode = BX.create("button", {
			text: params.text,
			attrs: {
				className: params.className
			},
			events : this.contextEvents
		});
	};
	BX.extend(subscribeButton, BX.PopupWindowButton);
	
	window.JCCatalogProductSubscribe = function(params) {
		this.buttonId = params.buttonId;
		this.buttonClass = params.buttonClass;
		this.jsObject = params.jsObject;
		this.ajaxUrl = "/bitrix/components/bitrix/catalog.product.subscribe/ajax.php";
		this.alreadySubscribed = params.alreadySubscribed;
		this.urlListSubscriptions = params.urlListSubscriptions;
		this.listOldItemId = {};
		
		this.elemButtonSubscribe = null;
		this.elemPopupWin = null;
		
		this._elemButtonSubscribeClickHandler = BX.delegate(this.subscribe, this);
		this._elemHiddenClickHandler = BX.delegate(this.checkSubscribe, this);
		
		BX.ready(BX.delegate(this.init,this));
	};

    window.JCCatalogProductSubscribe.prototype.init = function() {
		if(!!this.buttonId) {
			this.elemButtonSubscribe = BX(this.buttonId);
			this.elemHiddenSubscribe = BX(this.buttonId + "_hidden");
		}

        if(!!this.elemButtonSubscribe) {
			BX.bind(this.elemButtonSubscribe, "click", this._elemButtonSubscribeClickHandler);
		}

        if(!!this.elemHiddenSubscribe) {
			BX.bind(this.elemHiddenSubscribe, "click", this._elemHiddenClickHandler);
		}
		
		this.setButton(this.alreadySubscribed);
	};

    window.JCCatalogProductSubscribe.prototype.checkSubscribe = function() {
		if(!this.elemHiddenSubscribe || !this.elemButtonSubscribe) return;
		
		if(this.listOldItemId.hasOwnProperty(this.elemButtonSubscribe.dataset.item)) {
			this.setButton(true);
		} else {
			BX.ajax({
				method: "POST",
				dataType: "json",
				url: this.ajaxUrl,
				data: {
					sessid: BX.bitrix_sessid(),
					checkSubscribe: "Y",
					itemId: this.elemButtonSubscribe.dataset.item
				},
				onsuccess: BX.delegate(function(result) {
					if(result.subscribe) {
						this.setButton(true);
						this.listOldItemId[this.elemButtonSubscribe.dataset.item] = true;
					} else {
						this.setButton(false);
					}
				}, this)
			});
		}
	};

    window.JCCatalogProductSubscribe.prototype.subscribe = function() {
		this.elemButtonSubscribe = BX.proxy_context;
		if(!this.elemButtonSubscribe)
			return false;
		
		BX.ajax({
			method: "POST",
			dataType: "json",
			url: this.ajaxUrl,
			data: {
				sessid: BX.bitrix_sessid(),
				subscribe: "Y",
				itemId: this.elemButtonSubscribe.dataset.item,
				siteId: BX.message("SITE_ID")
			},
			onsuccess: BX.delegate(function(result) {
				if(result.success) {
					this.createSuccessPopup(result);
					this.setButton(true);
					this.listOldItemId[this.elemButtonSubscribe.dataset.item] = true;
				} else if(result.contactFormSubmit) {
					this.initPopupWindow();
					this.elemPopupWin.setTitleBar(BX.message("CPST_SUBSCRIBE_POPUP_TITLE"));
					var form = this.createContentForPopup(result);
					this.elemPopupWin.setContent(form);
					this.elemPopupWin.setButtons([
						new subscribeButton({
							text: BX.message("CPST_SUBSCRIBE_BUTTON_SEND"),
							className: "btn_buy popdef",
							events: {
								click: BX.delegate(function() {
									if(!this.validateContactField(result.contactTypeData)) {
										return false;
									}
									BX.ajax.submitAjax(form, {
										method: "POST",
										url: this.ajaxUrl,
										processData: true,
										onsuccess: BX.delegate(function(resultForm) {
											resultForm = BX.parseJSON(resultForm, {});
											if(resultForm.success) {
												this.createSuccessPopup(resultForm);
												this.setButton(true);
												this.listOldItemId[this.elemButtonSubscribe.dataset.item] = true;
											} else if(resultForm.error) {
												if(resultForm.hasOwnProperty("setButton")) {
													this.listOldItemId[this.elemButtonSubscribe.dataset.item] = true;
													this.setButton(true);
												}
												var errorMessage = resultForm.message;
												if(resultForm.hasOwnProperty("typeName")) {
													errorMessage = resultForm.message.replace("USER_CONTACT", resultForm.typeName);
												}
												var content = BX.create("span", {
													props: {
														className: "alertMsg bad"
													},
													children: [
														BX.create("i", {
															props: {
																className: "fa fa-exclamation-triangle",
																"aria-hidden": "true"
															}
														}),
														BX.create("span", {
															props: {
																className: "text"
															},
															html: errorMessage
														})
													]
												});
												var alert = BX.findChild(BX(this.buttonId + "_form"), {"className": "alert"}, true, false);
												if(!!alert) {
													alert.innerHTML = "";
													alert.appendChild(content);
												}
											}
										}, this)
									});
								}, this)
							}
						})
					]);
					this.elemPopupWin.show();
				} else if(result.error) {
					if(result.hasOwnProperty("setButton")) {
						this.listOldItemId[this.elemButtonSubscribe.dataset.item] = true;
						this.setButton(true);
					}
					this.showWindowWithAnswer({status: "error", message: result.message});
				}
			}, this)
		});
	};

    window.JCCatalogProductSubscribe.prototype.validateContactField = function(contactTypeData) {
		var inputFields = BX.findChildren(BX(this.buttonId + "_form"), {"tag": "input", "attribute": {id: "userContact"}}, true);
        if(!inputFields.length || typeof contactTypeData !== "object") {
			var errorMessage = BX.create("span", {
				props: {
					className: "alertMsg bad"
				},
				children: [
					BX.create("i", {
						props: {
							className: "fa fa-exclamation-triangle",
							"aria-hidden": "true"
						}
					}),
					BX.create("span", {
						props: {
							className: "text"
						},
						text: BX.message("CPST_SUBSCRIBE_VALIDATE_UNKNOW_ERROR")
					})
				]
			});
			var alert = BX.findChild(BX(this.buttonId + "_form"), {"className": "alert"}, true, false);
			if(!!alert) {
				alert.innerHTML = "";
				alert.appendChild(errorMessage);
			}
			return false;
		}
		
		var contactTypeId, contactValue, errors = [];
		for(var k = 0; k < inputFields.length; k++) {
			contactTypeId = inputFields[k].getAttribute("data-id");
			contactValue = inputFields[k].value;			
			if(!contactValue.length) {
				errors.push(BX.message("CPST_SUBSCRIBE_VALIDATE_ERROR_EMPTY_FIELD").replace("#FIELD#", contactTypeData[contactTypeId].contactLable));
			}
		}
		
		if(errors.length) {
			var errorMessage = BX.create("span", {
				props: {
					className: "alertMsg bad"
				},
				children: [
					BX.create("i", {
						props: {
							className: "fa fa-exclamation-triangle",
							"aria-hidden": "true"
						}
					}),
					BX.create("span", {
						props: {
							className: "text"
						},
						html: errors.join("<br />")
					})
				]
			});
			var alert = BX.findChild(BX(this.buttonId + "_form"), {"className": "alert"}, true, false);
			if(!!alert) {
				alert.innerHTML = "";
				alert.appendChild(errorMessage);
			}
			return false;
		}
		
		return true;
	};

    window.JCCatalogProductSubscribe.prototype.reloadCaptcha = function() {
		var form = BX(this.buttonId + "_form"),
			captchaWord = BX.findChild(form, {attribute: {name: "captcha_word"}}, true, false),
			captchaImg = BX.findChild(form, {attribute: {id: "captcha_img"}}, true, false),
			captchaSid = BX.findChild(form, {attribute: {name: "captcha_sid"}}, true, false);
		BX.ajax.get(this.ajaxUrl + "?reloadCaptcha=Y", "", function(captchaCode) {			
			if(!!captchaWord)
				captchaWord.value = "";
			if(!!captchaImg)
				captchaImg.src = "/bitrix/tools/captcha.php?captcha_sid=" + captchaCode + "";
			if(!!captchaSid)
				captchaSid.value = captchaCode;
		});
	};

    window.JCCatalogProductSubscribe.prototype.createContentForPopup = function(responseData) {
		if(!responseData.hasOwnProperty("contactTypeData")) {
			return null;
		}
		
		var contactTypeData = responseData.contactTypeData,
			content = document.createDocumentFragment();
		
		content.appendChild(BX.create("span", {
			props: {
				className: "alert"
			}
		}));
		
		for(var k in contactTypeData) {			
			content.appendChild(BX.create("div", {
				props: {					
					className: "row"
				},
				children: [
					BX.create("div", {
						props: {
							className: "span1"
						},
						text: contactTypeData[k].contactLable
					}),
					BX.create("div", {
						props: {
							className: "span2"
						},
						children: [
							BX.create("input", {
								props: {
									id: "userContact",									
									type: "text",
									name: "contact[" + k + "][user]"
								},
								attrs: {"data-id": k}
							})
						]
					})
				]
			}));
		}
		if(responseData.hasOwnProperty("captchaCode")) {
			content.appendChild(BX.create("div", {
				props: {
					className: "row"
				},
				children: [					
					BX.create("div", {
						props: {className: "span1"},
						children: [
							BX.message("CPST_ENTER_WORD_PICTURE"),
							BX.create("span", {props: {className: "mf-req"}, text: "*"})
						]
					}),					
					BX.create("div", {
						props: {className: "span2"},
						children: [
							BX.create("input", {
								props: {
									id: "captcha_word",									
									type: "text",
									name: "captcha_word"
								},
								attrs: {maxlength: "5"}
							}),							
							BX.create("img", {
								props: {
									id: "captcha_img",
									src: "/bitrix/tools/captcha.php?captcha_sid=" + responseData.captchaCode + ""
								},
								attrs: {
									width: "127",
									height: "30",
									alt: "captcha",
									onclick: this.jsObject + ".reloadCaptcha();"
								}
							}),
							BX.create("input", {
								props: {
									type: "hidden",
									id: "captcha_sid",
									name: "captcha_sid",
									value: responseData.captchaCode
								}
							})
						]
					})
				]
			}));
		}
		var form = BX.create("form", {
			props: {
				id: this.buttonId + "_form"
			},
			children: [
				BX.create("input", {
					props: {
						type: "hidden",
						name: "manyContact",
						value: "N"
					}
				}),
				BX.create("input", {
					props: {
						type: "hidden",
						name: "sessid",
						value: BX.bitrix_sessid()
					}
				}),
				BX.create("input", {
					props: {
						type: "hidden",
						name: "itemId",
						value: this.elemButtonSubscribe.dataset.item
					}
				}),
				BX.create("input", {
					props: {
						type: "hidden",
						name: "siteId",
						value: BX.message("SITE_ID")
					}
				}),
				BX.create("input", {
					props: {
						type: "hidden",
						name: "contactFormSubmit",
						value: "Y"
					}
				})
			]
		});
		
		form.appendChild(content);
		
		return form;
	};
	
	window.JCCatalogProductSubscribe.prototype.createSuccessPopup = function(result) {
		this.initPopupWindow();
		this.elemPopupWin.setTitleBar(BX.message("CPST_SUBSCRIBE_POPUP_TITLE"));
		var content = BX.create("div", {
			props: {
				className: "popup-window-message"
			},
			children: [
                BX.create("span", {
                    props: {
                        className: "alertMsg good"
                    },
					children: [
						BX.create("i", {
							props: {
								className: "fa fa-check",
								"aria-hidden": "true"
							}
						}),
						BX.create("span", {
							props: {
								className: "text"
							},
							text: result.message
						})
					]
				})
			]
		});
		this.elemPopupWin.setContent(content);
		this.elemPopupWin.setButtons(false);
		this.elemPopupWin.show();
	};

    window.JCCatalogProductSubscribe.prototype.initPopupWindow = function() {
		if(!!this.elemPopupWin) {
			return;
		}
		this.elemPopupWin = BX.PopupWindowManager.create("catalogSubscribe_" + this.buttonId, null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up forms short",
			closeIcon: { right : "-10px", top : "-10px"},
			titleBar: true
		});
		
		var close = BX.findChild(BX("catalogSubscribe_" + this.buttonId), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
	};

    window.JCCatalogProductSubscribe.prototype.setButton = function(statusSubscription) {
		this.alreadySubscribed = Boolean(statusSubscription);
		if(this.alreadySubscribed) {
			BX.adjust(this.elemButtonSubscribe, {props: {disabled: true}, html: BX.message("CPST_TITLE_ALREADY_SUBSCRIBED")});
			BX.unbind(this.elemButtonSubscribe, "click", this._elemButtonSubscribeClickHandler);
		} else {
			BX.adjust(this.elemButtonSubscribe, {props: {disabled: false}, html: BX.message("CPST_SUBSCRIBE_BUTTON_NAME")});
			BX.bind(this.elemButtonSubscribe, "click", this._elemButtonSubscribeClickHandler);
		}
	};

    window.JCCatalogProductSubscribe.prototype.showWindowWithAnswer = function(answer) {
		answer = answer || {};
		if(!answer.message) {
			if(answer.status == "success") {
				answer.message = BX.message("CPST_STATUS_SUCCESS");
			} else {
				answer.message = BX.message("CPST_STATUS_ERROR");
			}
		}
		var messageBox = BX.create("div", {
			props: {
				className: "popup-window-message"
			},
			children: [
                BX.create("span", {
                    props: {
                        className: "alertMsg " + (answer.status == "success" ? "good" : "bad")
                    },
					children: [
						BX.create("i", {
							props: {
								className: "fa fa-" + (answer.status == "success" ? "check" : "exclamation-triangle"),
								"aria-hidden": "true"
							}
						}),
						BX.create("span", {
							props: {
								className: "text"
							},
							text: answer.message
						})
					]
				})
			]
		});
		var currentPopup = BX.PopupWindowManager.getCurrentPopup();
		if(currentPopup) {
			currentPopup.destroy();
		}
		
		var idTimeout = setTimeout(function() {
			var w = BX.PopupWindowManager.getCurrentPopup();
			if(!w || w.uniquePopupId != "bx-catalog-subscribe-status-action") {
				return;
			}
			w.close();
			w.destroy();
		}, 3500);
		
		var popupConfirm = BX.PopupWindowManager.create("bx-catalog-subscribe-status-action", null, {			
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			className: "pop-up forms short",
			closeIcon: { right : "-10px", top : "-10px"},			
			onPopupClose: function() {
				this.destroy();
				clearTimeout(idTimeout);
			},
			titleBar: {content: BX.create("span", {html: BX.message("CPST_SUBSCRIBE_POPUP_TITLE")})},
			content: messageBox
		});
		var close = BX.findChild(BX("bx-catalog-subscribe-status-action"), {className: "popup-window-close-icon"}, true, false);
		if(!!close)
			close.innerHTML = "<i class='fa fa-times'></i>";
		popupConfirm.show();
		
		BX("bx-catalog-subscribe-status-action").onmouseover = function(e) {
			clearTimeout(idTimeout);
		};
		BX("bx-catalog-subscribe-status-action").onmouseout = function(e) {
			idTimeout = setTimeout(function() {
				var w = BX.PopupWindowManager.getCurrentPopup();
				if(!w || w.uniquePopupId != "bx-catalog-subscribe-status-action") {
					return;
				}
				w.close();
				w.destroy();
			}, 3500);
		};
	};
})(window);