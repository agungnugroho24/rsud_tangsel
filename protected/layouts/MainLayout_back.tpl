<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<com:THead Title="RS. ZAHIRAH" />
<script type="text/javascript" language="JavaScript1.2" src="<%=$this->Page->Theme->BaseUrl.'/js/stmenu.js'%>"></script>
<link href="<%=$this->Page->Theme->BaseUrl.'/css/app_style.css'%>" rel="stylesheet" type="text/css" />
<body>
<com:TClientScript PradoScripts="prado, ajax, effects" />
<com:TForm>
<div id="page">
<div id="header">
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td bgcolor="#000000"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="1%"><img src="<%=$this->Page->Theme->BaseUrl.'/images/logo.png'%>" width="240" height="64" /></td>
            <td width="99%" valign="bottom"><table width="10" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><table width="220" border="0" align="right" cellpadding="0" cellspacing="2">
                  <tr>
                    <td class="text_06">Pengguna :</td>
                    <td class="text_06"><%= $this->User->IsUserName %></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table width="10" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/barleft.png'%>" width="6" height="40" /></td>
                    <td background="<%=$this->Page->Theme->BaseUrl.'/images/barbkg.png'%>"><table width="10" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><table width="85" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,85,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/01off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/01on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Pasien Baru","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarBaru') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i1",[0,"Admin Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i1",[0,"Admin Rawat Jalan Edit","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.AdminRwtJlnEdit') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Cari Data Pendaftaran","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariPdftrn') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Cari Data Umum Pasien","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariData') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
<!--stm_aix("p1i13","p1i1",[0,"Cari Data Umum Pasien Lama","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariPasLama') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);-->
stm_aix("p1i5","p1i1",[0,"Cari Data Pasien Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i6","p1i1",[0,"Cari Data Pasien Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i7","p1i1",[0,"Cari Data Pasien IGD","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarCariRwtIgd') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i10","p1i1",[0,"Laporan RL","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.DaftarLapRL') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i11","p1i1",[0,"Diagnosa Pasien & ICD","","",-1,-1,0,"<%= $this->Service->constructUrl('Pendaftaran.IcdRM') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="85" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,45,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/10off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/10on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Kasir Rawat Jalan / UGD","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i1","p1i0",[0,"Kasir Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i1","p1i0",[0,"Kasir Rawat Jalan - Karyawan","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnKaryawan') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

<!---
stm_aix("p1i1","p1i0",[0,"Kasir Farmasi","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i0",[0,"Kasir Lab/Fisio","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i1",[0,"Kasir Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Kasir Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
--->
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="48" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,45,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/00off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/00on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Kasir Rawat Jalan / UGD","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i1","p1i0",[0,"Kasir Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i1","p1i0",[0,"Kasir Rawat Jalan - Karyawan","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnKaryawan') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

<!---
stm_aix("p1i1","p1i0",[0,"Kasir Farmasi","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i0",[0,"Kasir Lab/Fisio","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i1",[0,"Kasir Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Kasir Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
--->
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="74" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,85,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/02off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/02on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Rekam Kunjungan Dokter","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.VisiteDokter') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Rekam Tindakan Dokter","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.TdkDokter') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Rekam Tindakan Paramedis","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.TdkParamedis') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Rekam Penggunaan Oksigen","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.OksigenPas') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Rekam Penggunaan Blue Light","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.SinarPas') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Rekam Penggunaan Ambulance","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.Ambulan') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Rekam Billing Kamar Tindakan","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.KamarTdk') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Edit Billing Kamar Tindakan","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.KmrTdkEdit') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i5","p1i1",[0,"Rekam Biaya Lain-lain Pasien","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.BiayaLain') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i6","p1i1",[0,"Resume Pasien Keluar","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.ResumeRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i7","p1i1",[0,"Administrasi Pasien Pulang","","",-1,-1,0,"<%= $this->Service->constructUrl('Rawat.PasienPulangRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i8","p1i1",[0,"Laporan Bulanan"]);
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="62" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,62,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/03off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/03on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);

stm_aix("p1i1","p0i0",[0,"Administrasi Penjualan Obat","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.penjualanObat') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Retur Obat Rwt. Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.returObatJalan') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Retur Obat Rwt. Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.returObatInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Laporan Penjualan","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.LapPenjualan') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Cek Obat Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.BayarKasirRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Cek Obat Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.BayarKasirRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);


stm_aix("p1i2","p1i1",[0,"Slow/Fast Moving","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.LapPeredaranJualObat') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Obat Kadaluarsa","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.expired') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Pencarian Pasien","","",-1,-1,0,"<%= $this->Service->constructUrl('Apotik.DaftarCariData') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);



stm_ep();
stm_ep();
stm_em();
//-->
<!---
                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="46" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="left"><script type="text/javascript" language="JavaScript1.2">

stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,99,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/05off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/05on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Rekam Billing Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.bayarLab') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Laporan Hasil Pemeriksaan","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.cetakHasilLab') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Manajemen Stok Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Laporan Transaksi","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.LapTrans') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Info Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_ep();
stm_ep();
stm_em();

                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="45" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">

stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,76,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/06off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/06on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Rekam Billing Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.bayarRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Rekam Analisa Foto","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.beritaRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Pengambilan Hasil Foto","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.ambilRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Laporan Transaksi","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.LapTrans') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Info Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_ep();
stm_ep();
stm_em();
--->
                </script></td>
                              </tr>
                          </table></td>
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="45" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,78,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/04off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/04on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);

stm_aix("p1i1","p0i0",[0,"Informasi Stok Obat","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.MasterStokGudang') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Informasi Stok Obat Kritis","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.MasterStokGudangKritis') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Administrasi Gudang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.AdminFarmasi') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Rekam Data Obat & Alkes","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.MasterObat') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Rekam Data Obat Khusus","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.ObatKhusus') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Pemesanan Barang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.ObatBeli') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);


stm_aix("p1i2","p1i1",[0,"Edit Pemesanan Barang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.LapPembelianEdit') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Penerimaan Barang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.ObatMasuk') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Edit Penerimaan Barang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.LapPenerimaanEdit') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

<!-- -->
stm_aix("p1i2","p1i1",[0,"Distribusi Barang","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.bbkObat') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Pengembalian Obat","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.ReturBeliObat') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Laporan Pembelian","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.LapPembelian') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Pengajuan Pembayaran PBF","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.BayarPbfDraf') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_aix("p1i2","p1i1",[0,"Pembayaran PBF","","",-1,-1,0,"<%= $this->Service->constructUrl('Farmasi.BayarPbf') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="45" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,78,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/05off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/05on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);

stm_aix("p1i1","p0i0",[0,"Rekam Billing Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.bayarLab') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Laporan Hasil Pemeriksaan","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.cetakHasilLab') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Manajemen Stok Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Laporan Transaksi","","",-1,-1,0,"<%= $this->Service->constructUrl('Lab.LapTrans') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Info Laboratorium","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="45" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,78,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/06off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/06on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);

stm_aix("p1i1","p0i0",[0,"Rekam Billing Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.bayarRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Rekam Analisa Foto","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.beritaRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Pengambilan Hasil Foto","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.ambilRad') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i2","p1i1",[0,"Laporan Transaksi","","",-1,-1,0,"<%= $this->Service->constructUrl('Rad.LapTrans') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_aix("p1i2","p1i1",[0,"Info Radiologi","","",-1,-1,0,"<%= $this->Service->constructUrl('under') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);

stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="78" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,78,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/07off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/07on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Laporan Penerimaan Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Laporan Penerimaan Rawat Inap","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtInap') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Laporan Jasmed Rawat Jalan","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.LapJasmedRwtJln') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i3","p1i0",[0,"Buat Honor Pegawai Luar","","",-1,-1,0,"<%= $this->Service->constructUrl('Tarif.LapJasmedLuar') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          
                          
                          
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="98" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td align="center"><script type="text/javascript" language="JavaScript1.2">
<!--
stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,98,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/08off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/08on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,3,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Administrasi Data","","",-1,-1,0,"<%= $this->Service->constructUrl('Kepegawaian.KepegawaianAdmin') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
<!---
stm_aix("p1i1","p1i0",[0,"Rekam Data Dokter","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Edit Data Pegawai"]);
stm_aix("p1i3","p1i0",[0,"Edit Data Dokter"]);
stm_aix("p1i4","p1i1",[0,"Print Data Dokter"]);
--->
stm_ep();
stm_ep();
stm_em();
//-->
                </script></td>
                              </tr>
                          </table></td>
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="90" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td><table width="90" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                      <td align="center"><script type="text/javascript" language="JavaScript1.2">

stm_bm(["menu797a",720,"","blank.gif",0,"","",0,0,250,0,250,1,0,0,"","",0,1,1,2,"default","hand",""],this);
stm_bp("p0",[1,4,0,0,0,0,90,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#999999","transparent","",3,0,0,"#000000"]);
stm_ai("p0i0",[0,"","","",-1,-1,0,"","_self","","","<%=$this->Page->Theme->BaseUrl.'/images/09off.png'%>","<%=$this->Page->Theme->BaseUrl.'/images/09on.png'%>",-1,-1,0,"","",-1,-1,0,0,1,"#FFFFF7",1,"#B5BED6",1,"","",3,3,0,0,"#FFFFF7","#000000","#000000","#000000","8pt Verdana","8pt Verdana",0,0]);
stm_bpx("p1","p0",[1,4,-90,11,3,3,0,0,100,"stEffect(\"slip\")",-2,"stEffect(\"slip\")",-2,100,0,0,"#333333","#333333","",3,1,2]);
stm_aix("p1i0","p0i0",[0,"Administrasi Pengguna","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminUser') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Administrasi Formulir","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminFormulir') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);

<!---
stm_aix("p1i0","p0i0",[0,"Rekam Tarif Paramedis","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#222222",1,"#222222",1,"","",3,3,0,0,"#FFFFF7","#000000","#999999","#FFFFFF","bold 11px 'Arial','Verdana'","bold 11px 'Arial','Verdana'"]);
stm_aix("p1i1","p1i0",[0,"Rekam Tarif Kamar Perawatan","","",-1,-1,0,"","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i2","p1i1",[0,"Rekam Nama Pemeriksaan","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminFormulir') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i3","p1i0",[0,"Rekam Tarif Pemeriksaan","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminFormulir') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i4","p1i1",[0,"Edit Tarif Pemeriksaan"]);
stm_aix("p1i5","p1i1",[0,"Rekam Tarif Kamar Tindakan"]);
stm_aix("p1i6","p1i1",[0,"Edit Tarif Kamar Tindakan"]);
stm_aix("p1i7","p1i1",[0,"Edit Pembayaran"]);
stm_aix("p1i8","p1i1",[0,"Administrasi Pengguna","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminUser') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i9","p1i1",[0,"Administrasi Formulir","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.AdminFormulir') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_aix("p1i10","p1i1",[0,"Rekam ICD10"]);
stm_aix("p1i9","p1i1",[0,"Administrasi Asset","","",-1,-1,0,"<%= $this->Service->constructUrl('Asset.menuAsset') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
--->
stm_aix("p1i11","p1i1",[0,"Ganti Password","","",-1,-1,0,"<%= $this->Service->constructUrl('Admin.RubahPassword') %>","_self","","","","",0,0,0,"","",0,0,0,0,1,"#333333",0,"#333333",0]);
stm_ep();
stm_ep();
stm_em();
//-->
                </script>
                                      </td>
                                    </tr>
                                </table></td>
                              </tr>
                          </table></td>
                          <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/bargap.png'%>" width="3" height="30" /></td>
                          <td><table width="35" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td><com:TImageButton
						 		 ID="logoutButton"
								 CausesValidation="false"
								 ImageUrl="<%=$this->Page->Theme->BaseUrl.'/images/btnHome.png'%>"
								 OnClick="logoutClicked" /></td>
                              </tr>
                          </table></td>
                        </tr>
                    </table></td>
                    <td><img src="<%=$this->Page->Theme->BaseUrl.'/images/barrght.png'%>" width="6" height="40" /></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
        </tr>
        </table></td>
      </tr>
      <tr>
        <td height="8" background="<%=$this->Page->Theme->BaseUrl.'/images/bkgline.png'%>"></td>
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
    <td align="center" style="color:#FFFFFF;">&copy; Copyright of Intergo Telematics 2003 - <%= date('Y') %></td>
  </tr>
  <tr>
    <td height="10"></td>
  </tr>
  <tr>
    <td align="center"><%= PRADO::poweredByPrado(1) %></td>
  </tr>
</table>
</div>

</div>
</com:TForm>
</body>
</html>
