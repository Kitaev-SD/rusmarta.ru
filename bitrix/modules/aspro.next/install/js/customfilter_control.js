function initAsproNextCustomFilterControl(params)
{
	var data = JSON.parse(params.data);
	if (data)
	{
		window['aspro_next_customfilter_conditions_' + params.propertyID] = new AsproNextCustomFilterControl(data, params);
	}
}

function AsproNextCustomFilterControl(data, params)
{
	var rand = BX.util.getRandomString(5),
		that = this;

	this.params = params || {};
	this.message = JSON.parse(params.propertyParams.JS_MESSAGES) || {};
	this.data = data || {};
	this.nodes = {};
	this.ids = {
		form: 'limit_cond_form_' + this.params.propertyID + '_' + rand,
		container: 'limit_cond_container_' + this.params.propertyID + '_' + rand,
		treeObject: 'limit_cond_obj_' + this.params.propertyID + '_' + rand
	};
	this.path = this.getPath();

	this.buildNodes();

	BX.addCustomEvent('onTreeConditionsInit', BX.proxy(this.modifyTreeParams, this));
	BX.addCustomEvent('onAdminTabsDeleteLevel', BX.proxy(this.onChangeForm, this));
	BX.addCustomEvent('onNextVisualChange', BX.proxy(this.onChangeForm, this));
	BX.addCustomEvent('onTreeCondPopupClose', BX.proxy(this.onChangeForm, this));
	BX.addCustomEvent('onTreeCondDialogSave', BX.proxy(function(e){
		var that = this;
		setTimeout(function(){
			that.onChangeForm();
		}, 100);
	}, this));

	BX.loadScript('/bitrix/js/catalog/core_tree.js', function(){
		BX.ajax({
			timeout: 60,
			method: 'POST',
			dataType: 'html',
			url: that.path,
			data: {
				action: 'init',
				data: that.data,
				property: {
					id: that.params.propertyParams.ID,
					iblockId: that.params.propertyParams.IBLOCK_ID
				},
				condition: that.params.oInput.value,
				ids: that.ids,
				sessid: BX.bitrix_sessid()
			},
			onsuccess: BX.proxy(this.saveData, this)
		})
	});
	BX.loadCSS('/bitrix/panel/catalog/catalog_cond.css');
	// BX.loadCSS(this.path + '/style.css?' + rand);
}

AsproNextCustomFilterControl.prototype =
{
	getPath: function()
	{
		return this.params.propertyParams.AJAX_FILE;
	},

	deleteFromArray: function(keys, array)
	{
		if (!BX.type.isArray(keys) || !BX.type.isArray(array))
			return;

		for (var i = array.length; --i;)
		{
			if (!!array[i] && array.hasOwnProperty(i))
			{
				if (BX.util.in_array(i, keys))
				{
					array.splice(i, 1);
				}
			}
		}
	},

	onChangeForm: function()
	{
		if (!this.nodes.form)
			return;

		BX.fireEvent(this.nodes.form, 'change');
	},

	modifyTreeParams: function(arParams, obTree, obControls)
	{
		if (!obControls)
			return;

		if(arParams.formName !== this.ids.form){
			return;
		}

		var i, control, keysToDelete = [];

		for (i in obControls)
		{
			if (obControls.hasOwnProperty(i))
			{
				control = obControls[i];
				if (control.group)
				{
					this.modifyCondGroup(control);
				}
				else
				{
					if (this.modifyCondValueGroup(control))
					{
						keysToDelete.push(i);
					}
				}
			}
		}

		this.deleteFromArray(keysToDelete, obControls);
	},

	modifyCondGroup: function(ctrl)
	{
		var k;

		if (ctrl.visual)
		{
			for (k in ctrl.visual.values)
			{
				if (ctrl.visual.values.hasOwnProperty(k))
				{
					if (ctrl.visual.values[k].True === 'False')
					{
						ctrl.visual.values.splice(k, 1);
						ctrl.visual.logic.splice(k, 1);
					}
				}
			}
		}

		if (ctrl.control)
		{
			for (k in ctrl.control)
			{
				if (ctrl.control.hasOwnProperty(k))
				{
					ctrl.control[k].dontShowFirstOption = true;

					if (ctrl.control[k].id === 'True')
					{
						delete ctrl.control[k].values.False;
					}
				}
			}
		}
	},

	modifyCondValueGroup: function(ctrl)
	{
		if (!ctrl || !ctrl.children || !ctrl.children.length)
			return;

		var propertyPrefix = 'CondIBProp',
			propertyCrossalesPrefix = 'CondCrossIBProp',
			allowedFields = [
				'CondIBXmlID', 'CondIBIBlock', 'CondIBCode', 'CondIBName', 'CondIBElement', 'CondIBSection', 'CondIBDateActiveFrom', 'CondIBDateActiveTo',
				'CondIBSort', 'CondIBDateCreate', 'CondIBCreatedBy', 'CondIBTimestampX', 'CondIBModifiedBy',
				'CondIBTags', 'CondCatQuantity', 'CondCatWeight'
			],
			del, current, name;

		for (var k in ctrl.children)
		{
			if (ctrl.children.hasOwnProperty(k))
			{
				current = ctrl.children[k];
				del = true;

				if (BX.util.in_array(current.controlId, allowedFields))
				{
					del = false;
				}
				else
				{
					name = current.controlId.split(':');
					if (name[1] && name[1] != this.data.iblockId && name[1] != this.data.offersIblockId)
					{
						return true;
					}

					if ((name[0] === propertyPrefix || name[0] === propertyCrossalesPrefix) && name[2])
					{
						del = false;
					}
				}

				if (del)
				{
					delete ctrl.children[k];
				}
			}
		}

		ctrl.children = ctrl.children.filter(function(val){return val});

		return false;
	},

	buildNodes: function()
	{
		this.nodes.warning = BX.create('DIV', {
			props: {className: 'bx-filter-conditions-warning dm-info-message-wrap adm-info-message-red'},
			//text: this.message.invalid,
			html: '<div class="adm-info-message"><div class="adm-info-message-title">' + this.message.invalid + '</div><div class="adm-info-message-icon"></div></div>',
			style: {display: 'none'}
		});
		this.nodes.container = BX.create('DIV', {props: {id: this.ids.container}});
		this.nodes.form = BX.create('FORM', {
			props: {id: this.ids.form, name: this.ids.form},
			children: [this.nodes.container],
			events: {
				change: BX.proxy(function(){
					this.saveData();
				}, this)
			}
		});

		this.params.oCont.appendChild(
			BX.create('DIV', {
				children: [
					this.nodes.warning,
					this.nodes.form
				]
			})
		);

		BX.bindDelegate(this.nodes.form, 'mousedown', {className: 'condition-item-del'},
	        BX.proxy(function(){
	        	var that = this;
	        	setTimeout(function(){
		        	that.onChangeForm();
	        	}, 100);
			}, this)
		);
	},

	saveData: function()
	{
		var systemData = {
			action: 'save',
			ids: this.ids,
			data: this.data,
			property: {
				id: this.params.propertyParams.ID,
				iblockId: this.params.propertyParams.IBLOCK_ID
			},
			sessid: BX.bitrix_sessid()
		};

		BX.ajax({
			timeout: 60,
			method: 'POST',
			dataType: 'json',
			url: this.path,
			data: BX.merge(this.getAllFormData(), systemData),
			onsuccess: BX.proxy(function(result){
				if (result === '')
				{
					this.nodes.warning.style.display = 'block';
				}
				else
				{
					this.nodes.warning.style.display = 'none';
					this.params.oInput.value = JSON.stringify(result);
				}
			}, this)
		});
	},

	getAllFormData: function()
	{
		var prepared = BX.ajax.prepareForm(this.nodes.form);

		for (var i in prepared.data)
		{
			if (prepared.data.hasOwnProperty(i) && i == '')
			{
				delete prepared.data[i];
			}
		}

		return !!prepared && prepared.data ? prepared.data : {};
	}
};