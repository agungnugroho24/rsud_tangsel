<com:TForm>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="refresh" content="60">
<com:THead Title="PEMERINTAH KOTA TANGERANG SELATAN" >
		
    <!-- Le styles -->
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/lib/bootstrap.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/lib/bootstrap-responsive.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/extension.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/boo.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/style.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/boo-coloring.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/boo-utility.css'%>" rel="stylesheet">
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/skyblue.css'%>" rel="stylesheet">
    
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js'%>"></script>
        <script src="<%=$this->Page->Theme->BaseUrl.'/plugins/selectivizr/selectivizr-min.js'%>"></script>
        <script src="<%=$this->Page->Theme->BaseUrl.'/plugins/flot/excanvas.min.js'%>"></script>
    <![endif]-->
    
    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="assets/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<%=$this->Page->Theme->BaseUrl.'/ico/apple-touch-icon-144-precomposed.html'%>">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<%=$this->Page->Theme->BaseUrl.'/ico/apple-touch-icon-114-precomposed.html'%>">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<%=$this->Page->Theme->BaseUrl.'/ico/apple-touch-icon-72-precomposed.html'%>">
    <link rel="apple-touch-icon-precomposed" href="<%=$this->Page->Theme->BaseUrl.'/ico/apple-touch-icon-57-precomposed.html'%>">
            
    <link rel="SHORTCUT ICON" href="<%=$this->Page->Theme->BaseUrl.'/images/simak_icon.png'%>">

</com:THead>


<body >
	<com:TClientScript PradoScripts="prado, ajax, effects" />
  
  <div class="page-container">
  	<div id="main-container">
    	<div id="main-content" class="main-content container-fluid">
  			<com:TContentPlaceHolder ID="isi"/>
      </div>
    </div>  
	
	<footer id="footer-fix">
    	<div id="footer-content" class="footer-content">
      	<section>
          <div class="row-fluid">  
            <div class="span10 offset1" style="padding-top:10px;font-weight:bold; letter-spacing:3px;">
              <marquee scrolldelay="180" class="antiquewhite f20">RUMAH SAKIT KOTA TANGERANG SELATAN - JL. Pajajaran No. 101 Pamulang Telp. (021) 74718440</marquee>
            </div>
          </div>
        </section>  
      </div>
    </footer>
  </div>
  
</body>
</html>
</com:TForm>