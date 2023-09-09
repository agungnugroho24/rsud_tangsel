a<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<com:TForm>
<com:THead Title="Intergo SIMAK Suite ~ Integrated Hospital Management System">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/login-style.css'%>" rel="stylesheet" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery-ui-1.8.5.custom.css'%>" rel="stylesheet" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery.loadmask.css'%>" rel="stylesheet" type="text/css" />
    
    <link rel="SHORTCUT ICON" href="<%=$this->Page->Theme->BaseUrl.'/images/simak_icon.png'%>">
    
    <script>
		jQuery.noConflict();
		
        function maskContent() { 
            //jQuery("#process").bind("click", function () {
                jQuery("#maskarea").mask("Waiting...");
            //});
        }
        
        function unmaskContent() { 
            //jQuery("#process").bind("click", function () {
                jQuery("#maskarea").unmask();
            //});
        }
        
    </script>
    
</com:THead>

<!-- <body onbeforeunload="return showPopup();" > -->
<body >
<com:TClientScript PradoScripts="prado, ajax, effects" />
    
	<div id="maskarea" style="height:100%; width:100%; display:compact; position:absolute; top:0; left:0;"></div>
	
    <com:TContentPlaceHolder ID="Main" />
    
    <com:TJavascriptLogger Visible="false"/>	

</body>
</html>
</com:TForm> 