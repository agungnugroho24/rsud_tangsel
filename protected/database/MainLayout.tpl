<com:TForm>

<com:THead Title="RUMAH SAKIT UMUM DAERAH CICALENGKA" >

	<link href="<%=$this->Page->Theme->BaseUrl.'/css/mainlayout-style.css'%>" rel="stylesheet" type="text/css" />
	<link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery-ui-1.8.5.custom.css'%>" rel="stylesheet" type="text/css" />
    <link href="<%=$this->Page->Theme->BaseUrl.'/css/jquery.loadmask.css'%>" rel="stylesheet" type="text/css" />
    
	<style type="text/css">
		#dialog label, #dialog input { display:block; }
		#dialog label { margin-top: 0.5em; }
		#dialog input, #dialog textarea { width: 95%; }
		#tabs { margin-top: 1em; }
		#tabs li .ui-icon-close { float: left; margin: 0.4em 0.2em 0 0; cursor: pointer; }
		#add_tab { cursor: pointer; }
	</style>
    
        
<script>
	jQuery.noConflict();
	
	var popupWindow=null;
	var myLayout; // a var is required because this page utilizes: myLayout.allowOverflow() method
	var tesFrame; 
	
	jQuery(document).ready(function () {
	
		myLayout = jQuery("body").layout({
			name: "myLayout", // NO FUNCTIONAL USE, but could be used by custom code to 'identify' a layout
			// enable showOverflow on west-pane so popups will overlap north pane
			west__showOverflowOnHover: true,
			west: { 
					 size :265
					, maxSize:265
					, minSize:265
					, togglerLength_open:0
					, togglerLength_closed:18
					, togglerAlign_closed:	"top"		// align to top of resizer
					, spacing_closed:18
					, hideTogglerOnSlide:		true	
					, togglerTip_open:"Hide Menu"
					, resizerTip_open:"Hide Menu"
					, togglerTip_closed:"Open Menu"
					, resizerTip_closed:"Open Menu"
					, slideTrigger_open:"click" 	// default
					, initClosed:false
					, resizerClass:"resizer"	// default = 'ui-layout-resizer'
					, togglerClass:"toggler"
					,	fxSpeed_open:			750
					,	fxSpeed_close:			1500
					,	fxSettings_open:		{ easing: "easeOutBounce" }
					,	fxSettings_close:		{ easing: "easeOutBounce" }	// default = 'ui-layout-toggler'
				},
			east: { 
					 size :200
					, maxSize:200
					, minSize:265
					, togglerLength_open:0
					, togglerLength_closed:18
					, togglerAlign_closed:	"top"		// align to top of resizer
					, spacing_closed:18
					, hideTogglerOnSlide: false	
					, togglerTip_open:"Hide Menu"
					, resizerTip_open:"Hide Menu"
					, togglerTip_closed:"Open Menu"
					, resizerTip_closed:"Open Menu"
					, slideTrigger_open:"click" 	// default
					, initClosed:true
					, resizerClass:"resizer"	// default = 'ui-layout-resizer'
					, togglerClass:"toggler"
					,	fxSpeed_open:			750
					,	fxSpeed_close:			1500
					,	fxSettings_open:		{ easing: "easeOutBounce" }
					,	fxSettings_close:		{ easing: "easeOutBounce" }	// default = 'ui-layout-toggler'
				},
			north: {
						togglerLength_open:		0			// HIDE the toggler button
					,	size:70
					,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
					,	resizable: 				false
					,	slidable:				false
					//	override default effect
					,	fxName:					"none"
				},
			south: {
						maxSize:25
					,	size:25
					/*,	spacing_closed:			0			// HIDE resizer & toggler when 'closed' */
					,	slidable:				false		// REFERENCE - cannot slide if spacing_closed = 0
					,	initClosed:				false
					/*
					, spacing_open:			1			// cosmetic spacing
					,	togglerLength_open:		0			// HIDE the toggler button
					,	togglerLength_closed:	-1			// "100%" OR -1 = full width of pane
					,	resizable: 				false
					,	slidable:				false
					//	override default effect
					,	fxName:					"none"	
					*/
				}		
			});
		
		myLayout.addCloseBtn("#west-closer", "west");
		myLayout.addCloseBtn("#east-closer", "east");
		
		//hover states on the static widgets
		jQuery('#dialog_link, ul#icons li').hover(
			function() {jQuery(this).addClass('ui-state-hover'); }, 
			function() { jQuery(this).removeClass('ui-state-hover'); }
		);
		
		// Accordion
		jQuery("#accordionMenu").accordion({ header: "h3",autoHeight: false,navigation: true });
		
		//hover states on the static widgets
		jQuery('#fotoUpload, ul#icons li').hover(
			function() { jQuery(this).addClass('ui-state-hover'); }, 
			function() { jQuery(this).removeClass('ui-state-hover'); }
		);
		
		jQuery('#dokumenUpload, ul#icons li').hover(
			function() { jQuery(this).addClass('ui-state-hover'); }, 
			function() { jQuery(this).removeClass('ui-state-hover'); }
		);
		
		//jQuery('.tes').click(function tesFrame(urlFrame){ 
		
		
		/*
		jQuery('#dokumenUpload').click(function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var horizontalPadding = 30;
            var verticalPadding = 30;
            jQuery('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
                title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
                autoOpen: true,
                width: 800,
                height: 500,
                modal: true,
                resizable: false,
                autoResize: false,
                overlay: {
                    opacity: 0.5,
                    background: "black"
                }
            }).width(800 - horizontalPadding).height(500 - verticalPadding);     
			
			jQuery( 'a.ui-dialog-titlebar-close' ).remove();       
        });
		*/
		
		//jQuery('.thumb').jclip(60, 60, 70, 70);
		/*
		jQuery('#fotoUpload2').click(function(){  
				var $dialog =
					jQuery.FrameDialog.create({
						url: 'http://google.com',
						loadingClass: 'loading-image',
						title: 'This is the Dialog\'s Title',
						width: 900,
						height: 600,
						autoOpen: false
					});

				$dialog.dialog('open');
				
				return false;
			});

		
		jQuery('#fotoUpload2').click(function(e) {
            e.preventDefault();
            var $this = jQuery(this);
            var horizontalPadding = 30;
            var verticalPadding = 30;
            jQuery('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
                title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
                autoOpen: true,
                width: 800,
                height: 500,
                modal: true,
                resizable: false,
                autoResize: false,
                overlay: {
                    opacity: 0.5,
                    background: "black"
                },
				buttons: {
					"close": function() {
					  jQuery( this ).dialog( "close" );
					}
				  }
            }).width(800 - horizontalPadding).height(500 - verticalPadding);            
			
			jQuery( 'a.ui-dialog-titlebar-close' ).remove();
        });
		*/
		

		//logout clicked
		jQuery( "#<%= $this->logoutButton->getClientID() %>" ).click(function() {
			jQuery.WsGrowl.show({title: '', content:'<p class="msg_question">Logout sekarang<br/><br/></p>',timeout: 30000,dialog:{
				modal: true,
				buttons: {
					"Ya": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						updateFields();
					},
					Tidak: function() {
						jQuery( this ).dialog( "close" );
					}
				}
			}});
		});
 	});
	
	
	function tesFrame(urlFrame,width,height,titleTxt) { 
		var dialogPop = jQuery.FrameDialog.create({
					url: urlFrame,
					loadingClass: "loading-image",
					title: titleTxt,
					width: width,
					height: height,
					autoOpen: false
				});

			dialogPop.dialog("open");
			jQuery( 'a.ui-dialog-titlebar-close' ).remove();
			unmaskContent();	
	}
	
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
	
	function popup(url,title){
		popupWindow = window.open(url,title, 'resizable=no,scrollbars=yes,height=480,width=800,screenX=400,screenY=200,top=100,left=250', false);
	}
	 
	function parent_disable() {
		if(popupWindow && !popupWindow.closed)
		{
			popupWindow.focus();
		}
		else if(popupWindow && popupWindow.closed)
		{
			Element.hide('loading');		
		}
	}
	
	function updateClock ( )
	{
	  var currentTime = new Date ( );
	
	  var currentHours = currentTime.getHours ( );
	  var currentMinutes = currentTime.getMinutes ( );
	  var currentSeconds = currentTime.getSeconds ( );
	  var yy = currentTime.getFullYear( );
	  var mt = currentTime.getMonth( ) + 1;
	  var dd = currentTime.getDate( );
	  var nmHari=["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"];
	  
	  thisDay=currentTime.getDay();	
	  thisDay=nmHari[thisDay];
			
	  // Pad the minutes and seconds with leading zeros, if required
	  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
	  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
	
	  // Choose either "AM" or "PM" as appropriate
	  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
	
	  // Convert the hours component to 12-hour format if needed
	  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
	
	  // Convert an hours component of "0" to "12"
	  currentHours = ( currentHours == 0 ) ? 12 : currentHours;
	
	  // Compose the string for display
	  var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
	  bulan = mt;
	  // Update the time display
	 
	  
	  switch (mt) {
		case 1:
			bulan="Januari";
		break;
		case 2:
			bulan="Februari";
		break;
		case 3:
			bulan="Maret";
		break;
		case 4:
			bulan="April";
		break;
		case 5:
			bulan="Mei";
		break;
		case 6:
			bulan="Juni";
		break;
		case 7:
			bulan="Juli";
		break;
		case 8:
			bulan="Agustus";
		break;
		case 9:
			bulan="September";
		break;
		case 10:
			bulan="Oktober";
		break;
		case 11:
			bulan="November";
		break;
		case 12:
			bulan="Desember";
		break;
		
		}
		
		var thnStr=yy.toString();
	  
	  document.getElementById('tglTxt').innerHTML = thisDay + ", " + dd + " " + bulan + " " + thnStr;	
	  document.getElementById('clock').innerHTML = currentTimeString;
	 }
	 
</script>

<!--
<link href="<%=$this->Page->Theme->BaseUrl.'/css/lightwindow.css'%>" rel="lightwindow" type="text/css" />
<script type="text/javascript" language="JavaScript1.2" src="<%=$this->Page->Theme->BaseUrl.'/js/lightwindow.js'%>"></script>
-->
    
<link rel="SHORTCUT ICON" href="<%=$this->Page->Theme->BaseUrl.'/images/simak_icon.png'%>">

</com:THead>

<body onclick="parent_disable();" onLoad="updateClock(); setInterval('updateClock()', 1000 )">
<com:TClientScript PradoScripts="prado, ajax, effects" />


<div id="maskarea" style="height:100%; width:100%; display:compact; position:absolute; top:0; left:0;"></div>

<!-- manually attach allowOverflow method to pane -->
<div class="ui-layout-north" onMouseOver="myLayout.allowOverflow('north')" onMouseOut="myLayout.resetOverflow(this)">
	<!--
    <div style="float:left; padding-right:10px;"><img src="<%=$this->Page->Theme->BaseUrl.'/images/Logo-PMI.png'%>" height="60px" /></div>
    <div style="float:left; padding-right:20px; font-size:20px; font-weight:bold;">RUMAH SAKIT TANGGERANG</div>
    -->
    
    <div style="float:left; padding-top:5px; padding-left:3px; padding-right:20px; " >
    	<a href="<%= $this->Service->constructUrl('Simak') %>"><img src="<%=$this->Page->Theme->BaseUrl.'/images/logo.png'%>" /></a>
   	</div>
    
    <div class="content">
    	<strong>Pengguna : <%= $this->User->IsUserName %> &nbsp; | &nbsp; 
            <com:TActiveLinkButton
                 ID="gantiPassButton"
                 CausesValidation="false"
                 OnClick="gantiPass"
                 Text="Ganti Password"
                 Attributes.onclick="maskContent()"/>
            &nbsp; | &nbsp;
            <com:TActiveLinkButton
                 ID="logoutButton"
                 CausesValidation="false"
                 Text="Logout"/>     
            &nbsp;&nbsp;&nbsp;
            Theme 
            <com:TDropDownList 
            	ID="ThemeName" 
                OnSelectedIndexChanged="doTheme" 
                AutoPostBack="true" 
                CausesValidation="false"
                Width="120px;"
                Style="font-size:11px">
                <com:TListItem Text="Green" Value="hijau" />
                <com:TListItem Text="Blue" Value="biru" />
                <com:TListItem Text="Grey" Value="black" />
                <com:TListItem Text="Grey - Blue" Value="aristo" />
                <com:TListItem Text="Orange" Value="orange" />                
			</com:TDropDownList>          
        </strong>
    </div>
    
	<!--
	
    	
	<ul>
		<li>
			<ul>
				<li>one</li>
				<li>two</li>
				<li>three</li>
				<li>four</li>
				<li>five</li>
			</ul>
			Drop-Down <!-- put this below so IE and FF render the same! -->
    <!--        
		</li>
	</ul>
	-->
</div>

<!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
<div class="ui-layout-west">
    
    <div class="header ui-widget-header ui-corner-all"><strong>Menu</strong>
    	<span id="west-closer">
        	<span class="ui-icon ui-icon-circle-triangle-w"></span>
        </span>
    </div>
    
    <div class="ui-layout-west-content">
    	
        <div id="accordionMenu">
        	
            <com:TControl ID="pendaftaranCtrl">
            <div>
                <h3 class="head"><a href="#">Pendaftaran</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pasien Baru" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarBaru') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarRwtJln') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rawat Inap" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarRwtInap') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Cari Data Pendaftaran" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarCariPdftrn') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pencarian History Data Pasien" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarCariData') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Daftar Ruang Rawat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Admin.Formulir.RuangPasien') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>            
            </com:TControl>
            
            <com:TControl ID="rekamMedisCtrl">
            <div>
                <h3><a href="#">Rekam Medis</a></h3>
                <div>
                    <!--
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Admin Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJln') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Admin Rawat Jalan Edit" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJlnEdit') %>" Attributes.onclick="maskContent()"/>
                    -->
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Cari Data Pendaftaran" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarCariPdftrn') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pencarian History Data Pasien" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.DaftarCariData') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan RL" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.lapRL') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Diagnosa Pasien & ICD" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.IcdRM') %>" Attributes.onclick="maskContent()"/>
                     <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Efisiensi Kamar" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.EfisiensiBed') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekap ICD" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.RekapIcd') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="kasirCtrl">
            <div>
                <h3><a href="#">Kasir</a></h3>
                <div>
                	<span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Admin Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJln') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Admin Rawat Jalan Edit" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJlnEdit') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Kasir Retribusi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnRetribusi') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Kasir Rawat Jalan / UGD" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Kasir Rawat Inap" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Kasir Rawat Jalan - Karyawan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnKaryawan') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="rawatInapCtrl">
            <div>
                <h3><a href="#">Rawat Inap</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Jasa Emergency" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.Emergency') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Jasa Konsultasi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.Konsultasi') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Visite Dokter" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.VisiteDokter') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Billing Kamar Tindakan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.KamarTdk') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Edit Billing Kamar Tindakan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.KmrTdkEdit') %>" Attributes.onclick="maskContent()"/>
                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Sewa Alat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.SewaAlat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam BHP" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.Bhp') %>" Attributes.onclick="maskContent()"/>
                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Jasa Askep" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.Askep') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Sewa Alat Penunjang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.SewaAlatPenunjang') %>" Attributes.onclick="maskContent()"/>
                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Tindakan Dokter" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.TdkDokter') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Tindakan Paramedis" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.TdkParamedis') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Tindakan Khusus" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.TdkKhusus') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Penggunaan Oksigen" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.OksigenPas') %>" Attributes.onclick="maskContent()"/>
                                        
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Penggunaan Ambulan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.Ambulan') %>" Attributes.onclick="maskContent()"/>
                    <!--<span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Penggunaan Blue Light" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.SinarPas') %>" Attributes.onclick="maskContent()"/>-->
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Biaya Lain-lain" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.BiayaLainLain') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Kamar Bayi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.KamarBayi') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Resume Pasien Keluar" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.ResumeRwtInap') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Administrasi Pasien Pulang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rawat.PasienPulangRwtInap') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="apotikCtrl">
            <div>
                <h3><a href="#">Apotik</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Adm. Penjualan Obat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.penjualanObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Adm. Penjualan Obat Tunda" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.penjualanObatTunda') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Retur Obat Rwt. Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.returObatJalan') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Retur Obat Rwt. Inap" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.returObatInap') %>" Attributes.onclick="maskContent()"/>                   
                    <!--
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Cek Obat Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.BayarKasirRwtJln') %>" Attributes.onclick="maskContent()"/>                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Cek Obat Rawat Inap" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.BayarKasirRwtInap') %>" Attributes.onclick="maskContent()"/>
                    -->
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Slow/Fast Moving" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.LapPeredaranJualObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Obat Kadaluarsa" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.expired') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Margin Kelompok Obat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.PersenMarginObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Jasa Resep & Jasa Racikan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.JasaResepRacikan') %>" Attributes.onclick="maskContent()"/>
                     <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Penjualan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.LapPenjualan') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Jasa Resep Dokter" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Apotik.LapPenjualanFeeDokter') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="farmasiCtrl">
            <div>
                <h3><a href="#">Farmasi</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Informasi Stok Obat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.MasterStokGudang') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Informasi Stok Obat Kritis" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.MasterStokGudangKritis') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Administrasi Gudang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.AdminFarmasi') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Data Obat & Alkes" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.MasterObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Data Obat Khusus" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.ObatKhusus') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Data Obat Paket" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.ObatPaket') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pemesanan Barang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.ObatBeli') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Edit Pemesanan Barang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.LapPembelianEdit') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Penerimaan Barang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.ObatMasuk') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Edit Penerimaan Barang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.LapPenerimaanEdit') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Distribusi Barang" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.bbkObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pengembalian Obat" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.ReturBeliObat') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Pembelian" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.LapPembelian') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Lap. Distribusi Barang Rusak" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.LapBarangRusak') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pengajuan Pembayaran PBF" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.BayarPbfDraf') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pembayaran PBF" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.BayarPbf') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Stok Opname Obat & Alkes" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Farmasi.StokOpname') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="labCtrl">
            <div>
                <h3><a href="#">Laboratorium</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Billing Laboratorium" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Lab.bayarLab') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Hasil Pemeriksaan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Lab.cetakHasilLab') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pengambilan Hasil Pemeriksaan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Lab.ambilHasilLab') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Transaksi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Lab.LapTrans') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="radCtrl">
            <div>
                <h3><a href="#">Radiologi</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Billing Radiologi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.bayarRad') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Rekam Analisa Foto" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.beritaRad') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pengambilan Hasil Foto" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.ambilHasilRad') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Laporan Transaksi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.LapTrans') %>" Attributes.onclick="maskContent()"/>
                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Pengadaan Barang Radiologi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.FilmBeli') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Penerimaan Barang Radiologi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.FilmMasuk') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Info Stok Barang Radiologi" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Rad.MasterStokFilm') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="keuanganCtrl">
            <div>
                <h3><a href="#">Keuangan</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Lap. Penerimaan Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtJln') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Lap. Penerimaan Rawat Inap" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtInap') %>" Attributes.onclick="maskContent()"/>
                    <!--
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Lap. Jasmed Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.LapJasmedRwtJln') %>" Attributes.onclick="maskContent()"/>
                    -->
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Lap. Jasmed Rawat Jalan" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.LapJasmedRwtJlnBeta') %>" Attributes.onclick="maskContent()"/>
                    
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Buat Honor Pegawai Luar" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Tarif.LapJasmedLuar') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="kepegawaianCtrl">
            <div>
                <h3><a href="#">Kepegawaian</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Administrasi Data" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Kepegawaian.KepegawaianAdmin') %>" Attributes.onclick="maskContent()"/>
                </div>
            </div>
            </com:TControl>
            
            <com:TControl ID="admSistemCtrl">
            <div>
                <h3><a href="#">Adm. Sistem</a></h3>
                <div>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Administrasi Pengguna" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Admin.AdminUser') %>" Attributes.onclick="maskContent()"/>
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Administrasi Formulir" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Admin.AdminFormulir') %>" Attributes.onclick="maskContent()"/>
                    
                    <!--
                    <span class="ui-icon ui-icon-carat-1-e"></span><com:TActiveHyperLink Text="Ganti Password" CssClass="menu-kiri" NavigateUrl="<%= $this->Service->constructUrl('Admin.RubahPassword') %>" Attributes.onclick="maskContent()"/>
                    -->
                </div>
            </div>
            </com:TControl>
            
        </div>
        
        <div style="display:none;">
        <br/>
        <com:TJavascriptLogger Visible="true"/>	
        </div>
    	<!--
        <ul>
            <li>
                <ul>
                    <li>one</li>
                    <li>two</li>
                    <li>three</li>
                    <li>four</li>
                    <li>five</li>
                </ul>
                
            </li>
        </ul>
    
        <p><button onClick="myLayout.close('west')">Close Me</button></p>
    	
        <p><a href="#" onClick="showOptions(myLayout,'defaults.fxSettings_open');showOptions(myLayout,'west.fxSettings_close')">Show Options.Defaults</a></p>
        -->
	</div>
    
    <!--<div class="header"><strong>Pencarian Pasien</strong></div>-->
</div>

<!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
<div class="ui-layout-east">
    
    <div class="header ui-widget-header ui-corner-all"><strong>Info</strong>
    	<span id="east-closer">
        	<span class="ui-icon ui-icon-circle-triangle-e"></span>
        </span>
    </div>
    
    <div class="ui-layout-east-content">
    
    </div>
</div>

<div class="ui-layout-center">

	<div class="header ui-widget-header ui-corner-all"><com:TContentPlaceHolder ID="jdMain" /></div>
    
    <div class="ui-layout-center-content">    
		<com:TContentPlaceHolder ID="Main" />	
    </div>
</div>

<div class="ui-layout-south">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
    	<tr>
        	<td width="40%" style="padding-left:10px;">
            	<marquee scrolldelay="180"><strong>RUMAH SAKIT UMUM DAERAH CICALENGKA</strong> - Jl. H. Darham No. 35 - Cikopo Desa Tenjolaya Cicalengka Telp. 022-7949734 / 022-7949743 Fax 022-7952204 &nbsp;&nbsp; &copy; Copyright of Intergo Telematics 2003 - <%= date('Y') %> </marquee>
            </td>
            <td align="center" width="20%">
            	<%= PRADO::poweredByPrado(1) %>
            </td>
            <td align="right" width="40%" style="padding-right:10px;">
            	<div style="padding-right:7px; float:right; font-weight:bold; font-size:11px; ">
                    <a id="tglTxt" style="padding-right:7px;"/><a id="clock"/>
                </div>
            </td>
        </tr>
    </table> 
    <!--<button onClick="myLayout.toggle('north')">Toggle North Pane</button>-->
    
    
</div>

<com:TActiveLabel ID="msg"/>
<com:TCallback ID="UpdateCallback" OnCallback="logoutAccept" ActiveControl.CausesValidation="false"/>
	<script type="text/javascript">
		//<![CDATA[
		function updateFields(){
			var request= <%= $this->UpdateCallback->ActiveControl->Javascript %>;
				//request.setCallbackParameter({
					//Id:personId
				//});
				request.dispatch();
		}
		//]]>
	</script>
    
</body>
</html>

</com:TForm>
