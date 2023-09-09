<com:TForm>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<com:THead Title="PEMERINTAH KOTA TANGERANG SELATAN" >

    
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/app_style_blank.css'%>" rel="stylesheet" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/lightwindow.css'%>" rel="lightwindow" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery-ui-1.8.5.custom.css'%>" rel="stylesheet" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery.loadmask.css'%>" rel="stylesheet" type="text/css" />
    
    <!--
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/lightwindow.css'%>" rel="lightwindow" type="text/css" />
    <script type="text/javascript" language="JavaScript1.2" src="<%=$this->Page->Theme->BaseUrl.'/js/lightwindow.js'%>"></script>
    -->
        
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


<body bgcolor="#CACACA">
	<com:TClientScript PradoScripts="prado, ajax, effects" />
    
	
    
   <div id="loading" 
        style="display:none; " 
            align="center" >
        
        <div id="loading2">
        </div>
        
        <div id="loading3" align="center" >        	
            <div id="loading_anim">            	
                processing data...<br/><br/>
                <img src="<%=$this->Page->Theme->BaseUrl.'/images/loading.gif'%>" /> <br/>                    
            </div>            
            <img src="<%=$this->Page->Theme->BaseUrl.'/images/back_loading.png'%>" class="loading_anim_back" />
        </div>
    </div>

   <div id="maskarea" class="blanklayout_content"> 
   	<com:TContentPlaceHolder ID="isi"/>
   </div>
   <br/>
   <com:TJavascriptLogger Visible="true"/>
   
</body>
</html>
</com:TForm>