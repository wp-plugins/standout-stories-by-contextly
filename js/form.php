<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="cso">
<head>
<title>Standout Stories by Contextly</title>
<link rel="stylesheet" href="../css/contextly-standout.css">
<script type="text/javascript" src="tiny_mce_popup.js"></script>
<script type="text/javascript">
var csoSavedURL = {
    frm : window.parent.document.forms['post'],
	 init : function () {
	 	var txtval = "http://";
		if (this.frm.elements['cso_url'].value != "") {
			txtval = this.frm.elements['cso_url'].value;
		}
		document.forms['csoForm'].elements['cso_urltext'].value = txtval;
    },
	 callpmethod : function(frmobj) {
	 	if (frmobj.elements['cso_urltext'].value != "http://") {
			this.frm.elements['cso_url'].value = frmobj.elements['cso_urltext'].value;
   		this.frm.elements['cso_checkbox'].checked = false;
         tinyMCE.execCommand('mceInsertLink',false,{href:this.frm.elements['cso_url'].value, target:'_blank'});
		}
   	tinyMCEPopup.close();
	 }
}
tinyMCEPopup.onInit.add(csoSavedURL.init, csoSavedURL);
</script>
</head>
<body>
<form id="csoForm" name="csoForm" onsubmit="csoSavedURL.callpmethod(this)">
   <div id="cso-onecol">
   	<p>Enter the destination URL</p>
      <p><label for="cso_urltext">URL</label>
         <input id="cso_urltext" type="text" name="cso_urltext" value="http://"></p>
      <p style="text-align:center">Give credit to other sites for their great work. Do this as often as you like.</p>
   </div>
   <div class="cso-twocol">
      <div id="cso-cancel"> <a href="javascript:tinyMCEPopup.close();">Cancel</a> </div>
      <div id="cso-update"> <input type="submit" name="cso-submit" id="cso-submit" value="Add Link"> </div>
      <br style="clear:both" />
   </div>
   <p class="powerby"> <a href="http://contextly.com" title="Powered by Contextly" target="_blank">
   Powered by Contextly</a>, a related links service.</p>
</form>
</body>
</html>