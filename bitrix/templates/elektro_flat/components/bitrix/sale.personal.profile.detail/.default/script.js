BX.namespace("BX.Sale.PersonalProfileComponent");

(function() {
	BX.Sale.PersonalProfileComponent.PersonalProfileDetail = {
		init: function ()
		{
			var propertyFileList = document.getElementsByClassName("sale-profile-detail-form-property-file");
			Array.prototype.forEach.call(propertyFileList, function(propertyFile) {
				var deleteFileElement = propertyFile.getElementsByClassName("sale-profile-detail-form-input-delete-file")[0];
				
				BX.bindDelegate(propertyFile, "click", {className: "sale-profile-detail-form-check-file-checkbox" }, BX.proxy(function(event)
				{
					if(deleteFileElement.value != "") {
						idList = deleteFileElement.value.split(";");
						if(idList.indexOf(event.target.value) === -1) {
							deleteFileElement.value = deleteFileElement.value + ";" + event.target.value;
						} else {
							idList.splice(idList.indexOf(event.target.value), 1);
							deleteFileElement.value = idList.join(";");
						}
					} else {
						deleteFileElement.value = event.target.value;
					}
				}, this));
			});
		}
	}
})();
