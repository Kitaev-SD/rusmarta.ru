var bxAuthWnd = false;
function BxShowAuthFloatNew(id, suffix) {
	var bCreated = false;
	if(!bxAuthWnd) {
		bxAuthWnd = new BX.CDialog({
			'content':'<div id="bx_auth_float_container"></div>', 
			'width': 640,
			'height': 400,
			'resizable': false
		});
		bCreated = true;
	}
	bxAuthWnd.Show();

	if(bCreated) {
		BX('bx_auth_float_container').appendChild(BX('bx_auth_float'));
		BX.addClass(BX('bx-admin-prefix'), 'popup-auth');
	}
			
	BxShowAuthService(id, suffix);
}