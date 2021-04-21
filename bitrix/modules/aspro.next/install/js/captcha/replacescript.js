(function(){
	/**
	 * fet parent form
	 * @param {HTMLElement} childNode
	 * @returns {null|HTMLElement} form or null
	 */
	var getFormNode = function(childNode){
		var c = childNode;
		while(c)
		{
			c = c.parentNode;
			if(c.nodeName.toLowerCase() === 'form')
				return c;
		}
		return null;
	};

	/**
	 * findу input[name=captcha_word] or *[name=captcha_word]
	 * @param {HTMLElement|null} parentNode
	 * @returns {HTMLElement[]} list elements
	 */
	var getCaptchaWords = function(parentNode){
		var captchaWords = [];

		var inputs = null;
		var hasParentNode = false;
		if(typeof parentNode !== "undefined")
			hasParentNode = parentNode !== null;

		if(hasParentNode)
			inputs = parentNode.getElementsByTagName('input');
		else
			inputs = document.getElementsByName('captcha_word');

		for(var i = 0; i < inputs.length; i++)
		{
			if(inputs[i].name === 'captcha_word')
				captchaWords.push(inputs[i]);
		}

		return captchaWords;
	};

	/**
	 * find Bitrix captcha img.
	 * @param {HTMLElement} parentNode
	 * @returns {HTMLElement[]} captcha img
	 */
	var getCaptchaImages = function(parentNode){
		var captchaImages = [];

		var images = parentNode.getElementsByTagName('img');
		for(var i = 0; i < images.length; i++)
		{
			if(/\/bitrix\/tools\/captcha.php\?(captcha_code|captcha_sid)=[^>]*?/i.test(images[i].src)
				|| (images[i].id === "captcha")){
				captchaImages.push(images[i]);
			}
		}

		return captchaImages;
	};

	/**
	 * get form with Bitrix captcha
	 * @returns {HTMLElement[]}
	 */
	var getFormsWithCaptcha = function(){
		var fromDocument = null;
		var captchaWordFields = getCaptchaWords(fromDocument);
		if (captchaWordFields.length === 0)
			return [];

		var forms = [];
		for(var i = 0; i < captchaWordFields.length; i++)
		{
			var f = getFormNode(captchaWordFields[i]);
			if(null !== f)
				forms.push(f);
		}
		return forms;
	};

	/**
	 * replace capcha_word to ReCAPTCHA
	 * @param {HTMLElement} captchaWord (*[name=captcha_word])
	 */
	var replaceCaptchaWordWithReCAPTCHAField = function(captchaWord){
		// generate unic_id
		var recaptchaId = 'recaptcha-dynamic-' + (new Date()).getTime();
		if(document.getElementById(recaptchaId) !== null)
		{
			var elementExists = false;
			var additionalIdParameter = null;
			var maxRandomValue = 65535;
			do
			{
				additionalIdParameter = Math.floor(Math.random() * maxRandomValue);
				elementExists = (document.getElementById(recaptchaId + additionalIdParameter) !== null);
			}
			while(elementExists);
			recaptchaId += additionalIdParameter;
		}

		var cwReplacement = document.createElement('div');
		cwReplacement['id'] = recaptchaId;
		cwReplacement['className'] = 'g-recaptcha';
		cwReplacement['attributes']['data-sitekey'] = window.asproRecaptcha.key;

		if(captchaWord.parentNode)
		{
			captchaWord.parentNode.className += ' recaptcha_text';
			captchaWord.parentNode.replaceChild(cwReplacement, captchaWord);
		}
		renderRecaptchaById(recaptchaId);
	};

	/**
	 * hide catcha image
	 * @param {HTMLImageElement} captchaImage
	 */
	var hideCaptchaImage = function(captchaImage){
		var srcValue = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		captchaImage['attributes']['src'] = srcValue;
		captchaImage['style']['display'] = 'none';
		if('src' in captchaImage)
		{
			if(captchaImage.parentNode)
				captchaImage.parentNode.className += ' recaptcha_tmp_img';
			captchaImage.src = srcValue;
		}
	};

	/**
	 * replace label for input ReCAPTCHA
	 * @param {HTMLElement} form
	 */
	var replaceCaptchaHintMessagesWithReCAPTCHAHintMessages = function(form){
		if(typeof $ == 'function')
		{
			if($(form).find('.captcha-row label > span').length)
			{
				$(form).find('.captcha-row label > span').html(BX.message('RECAPTCHA_TEXT')+' <span class="star">*</span>');
			}
		}
	};

	/**
	 * find and replace Bitrix captcha to ReCAPTCHA in all forms
	 */
	var findAndReplaceBitrixCaptchaWithReCAPTCHA = function(){
		var forms = getFormsWithCaptcha();
		var j;

		for(var i = 0; i < forms.length; i++)
		{
			var form = forms[i];
			var captchaWords = getCaptchaWords(form);
			if(captchaWords.length === 0)
				continue;

			var captchaImages = getCaptchaImages(form);
			if(captchaImages.length === 0)
				continue;
			for(j = 0; j < captchaWords.length; j++)
				replaceCaptchaWordWithReCAPTCHAField(captchaWords[j]);

			for(j = 0; j < captchaImages.length; j++)
				hideCaptchaImage(captchaImages[j]);

			replaceCaptchaHintMessagesWithReCAPTCHAHintMessages(form);
		}
	};

	/**
	 * fill empty field ReCAPTHA
	 */
	var fillEmptyReCAPTCHAFieldsIfLoaded = function(){
		if(typeof renderRecaptchaById !== "undefined")
		{
			var elements = document.getElementsByClassName('g-recaptcha');
			for(var i = 0; i < elements.length; i++)
			{
				var element = elements[i];
				if(element.innerHTML.length === 0)
				{
					var id = element.id;
					if(typeof id === "string")
					{
						if(id.length !== 0)
						{
							if(typeof $ == 'function')
							{
								var captcha_wrapper = $(element).closest('.captcha-row');
								if(captcha_wrapper.length)
								{
									if(window.asproRecaptcha.ver == '3'){
										captcha_wrapper.hide();
									}
									else{
										captcha_wrapper.addClass(window.asproRecaptcha.params.recaptchaSize+' '+'logo_captcha_'+window.asproRecaptcha.params.recaptchaLogoShow+' '+window.asproRecaptcha.params.recaptchaBadge);
										captcha_wrapper.find('.captcha_image').addClass('recaptcha_tmp_img');
										captcha_wrapper.find('.captcha_input').addClass('recaptcha_text');
										if(window.asproRecaptcha.params.recaptchaSize !== 'invisible')
										{
											if(!captcha_wrapper.find('input.recaptcha').length)
												$('<input type="text" class="recaptcha" value="" />').appendTo(captcha_wrapper)
										}
									}
								}
							}
							renderRecaptchaById(id);
						}
					}
				}
			}
		}
	};

	/**
	 * general replace ReCAPTCHA
	 * @returns {boolean}
	 */
	var captchaHandler = function(){
		try{
			fillEmptyReCAPTCHAFieldsIfLoaded();

			if(!window.renderRecaptchaById || !window.asproRecaptcha || !window.asproRecaptcha.key)
			{
				console.error('Bad captcha keys or module error');
				return true;
			}

			findAndReplaceBitrixCaptchaWithReCAPTCHA();
			return true;
		}catch (e){
			console.error(e);
			return true;
		}
	};

	if(!!document.addEventListener)
		document.addEventListener('DOMNodeInserted', captchaHandler, false);
	else
		console.warn('Your browser does not support dynamic ReCaptcha replacement');
})();