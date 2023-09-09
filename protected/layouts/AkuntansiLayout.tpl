<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<com:THead Title="AkunSuite v1.2 ~ CodeName: Oliver" />
<script type="text/javascript" language="JavaScript1.2" src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/js/stmenu.js'%>"></script>
<link href="<%=$this->Page->Theme->BaseUrl.'/akuntansi/css/app_style.css'%>" rel="stylesheet" type="text/css" />
<body>
<com:TForm>
<div id="page"> 
<div id="header">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#000000"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="1%"><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/logo.png'%>" width="219" height="64" /></td>
            <td width="99%" valign="bottom"><table width="10" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/barleft.png'%>" width="6" height="40"></td>
            <td background="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/barbkg.png'%>"><table width="10" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><table width="88" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/blank.gif'%>",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,88,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/00off.png'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/00on.png'%>",88,30,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,13,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,25,0,0,"#999999","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0," Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0," Rawat IGD","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaRwtIgd') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0," Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i3","p1i1",[0," Instalasi Farmasi","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaFarmasi') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i4","p1i1",[0," Instalasi Penunjang ","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaPenunjang') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i5","p1i1",[0," Sumber Lainnya","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.terimaLainnya') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_ep();
stm_ep();
stm_em();
//-->
</script></td>
                  </tr>
                </table></td>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/bargap.png'%>" width="3" height="30"></td>
                <td><table width="92" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/blank.gif'%>",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,92,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/01off.png'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/01on.png'%>",92,30,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,13,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,25,0,0,"#999999","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0," Medis","","",-1,-1,0,"#","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0," Non Medis ","","",-1,-1,0,"#","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_ep();
stm_ep();
stm_em();
//-->
</script></td>
                  </tr>
                </table></td>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/bargap.png'%>" width="3" height="30"></td>
                <td><table width="75" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,0,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,75,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/02off.png'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/02on.png'%>",75,30,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,13,3,3,0,7,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,25,0,0,"#999999","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0," Penerimaan ","","",-1,-1,0,"","_self","","","","",0,0,0,"<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/grey2-r.gif'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/grey2-r.gif'%>",7,9,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_bpx("p2","p1",[1,2,10,-5,3,3,0,0,50,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100]);
stm_aix("p2i0","p1i0",[0," Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.verifikasiRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p2i1","p2i0",[0," Instalasi Gawat Darurat ","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.verifikasiRwtIgd') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p2i2","p2i0",[0," Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.verifikasiRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p2i3","p2i0",[0," Instalasi Farmasi","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.verifikasiFarmasi') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p2i4","p2i0",[0," Instalasi Penunjang","","",-1,-1,0,"<%= $this->Service->constructUrl('Akuntansi.verifikasiPenunjang') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_ep();
stm_aix("p1i1","p1i0",[0," Pengeluaran"]);
stm_bpx("p3","p2",[]);
stm_aix("p3i0","p2i0",[0," Medis"]);
stm_aix("p3i1","p2i0",[0," Non Medis "]);
stm_ep();
stm_ep();
stm_ep();
stm_em();
//-->
</script></td>
                  </tr>
                </table></td>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/bargap.png'%>" width="3" height="30"></td>
                <td><table width="67" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/blank.gif'%>",0,"","",0,0,250,0,250,1,0,0,"","",0,0,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,67,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/03off.png'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/03on.png'%>",67,30,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,13,3,3,0,7,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,25,0,0,"#999999","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0," Klaim Piutang","","",-1,-1,0,"","_self","","","","",0,0,0,"<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/grey2-r.gif'%>","<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/grey2-r.gif'%>",7,9,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_bpx("p2","p1",[1,2,10,-5,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100]);
stm_aix("p2i0","p1i0",[0," ASKES","","",-1,-1,0,"#","_self","","","","",0,0,0,"","",0,0]);
stm_aix("p2i1","p2i0",[0," JAMSOSTEK "]);
stm_aix("p2i2","p2i0",[0," SKTM"]);
stm_aix("p2i3","p2i0",[0," Kontrak"]);
stm_aix("p2i4","p2i0",[0," Lainnya"]);
stm_ep();
stm_aix("p1i1","p1i0",[0," Penerimaan Tunai "]);
stm_bpx("p3","p2",[]);
stm_aix("p3i0","p2i0",[0," Rawat Jalan "]);
stm_aix("p3i1","p2i0",[0," Rawat IGD"]);
stm_aix("p3i2","p2i0",[0," Rawat Inap"]);
stm_aix("p3i3","p2i0",[0," Instalasi Farmasi"]);
stm_aix("p3i4","p2i0",[0," Instalasi Penunjang "]);
stm_ep();
stm_aix("p1i2","p1i0",[0," Hutang "]);
stm_bpx("p4","p2",[]);
stm_aix("p4i0","p2i0",[0," Medis"]);
stm_aix("p4i1","p2i0",[0," Non Medis "]);
stm_ep();
stm_aix("p1i3","p1i0",[0," Pembayaran"]);
stm_bpx("p5","p2",[]);
stm_aix("p5i0","p2i0",[0," Jasa Medis Dokter"]);
stm_aix("p5i1","p2i0",[0," Jasa Medis Perawat "]);
stm_aix("p5i2","p2i0",[0," Jasa Medis Lainnya"]);
stm_ep();
stm_aix("p1i4","p2i0",[0," Penerimaan Tunai "]);
stm_ep();
stm_ep();
stm_em();
//-->
</script>
</td>
                  </tr>
                </table></td>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/bargap.png'%>" width="3" height="30"></td>
                <td><table width="350" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td>&nbsp;</td>
                  </tr>
                </table></td>
                <td><com:TImageButton
						 		 ID="logoutButton" 
								 CausesValidation="false"
								 ImageUrl="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/btnHome.png'%>"
								 OnClick="logoutClicked" /></td>
              </tr>
            </table></td>
            <td><img src="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/barrght.png'%>" width="6" height="40"></td>
          </tr>
        </table></td>
        </tr>
        </table></td>
      </tr>
      <tr>
        <td height="8" background="<%=$this->Page->Theme->BaseUrl.'/akuntansi/images/bkgline.png'%>"></td>
      </tr>
    </table></td>
  </tr>
</table>
</div>


<div id="Main">
<com:TContentPlaceHolder ID="Main" />
</div>

<div id="footer">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center">&copy; Copyright of Intergo Telematics 2007 </td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
  <tr>
    <td align="center"><%= PRADO::poweredByPrado() %></td>
  </tr>
</table>
</div>

</div>
</com:TForm>
</body>
</html>
