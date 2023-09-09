<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<com:THead Title="SIM-Asset v1.0 Suite ~ CodeName: Oliver" >
<meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Cache-Control" content="no-cache"/>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta http-equiv="content-language" content="en"/>
</com:THead>
<link href="<%=$this->Page->Theme->BaseUrl.'/asset/css/style.css'%>" rel="stylesheet" type="text/css">
<script type="text/javascript" language="JavaScript1.2" src="<%=$this->Page->Theme->BaseUrl.'/asset/js/stmenu.js'%>"></script>
<body>
<div id="page2">
<com:TForm>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
  <div id="header">
    <td class="table_01a"><!-- <img src="<%=$this->Page->Theme->BaseUrl.'/asset/images/logo.png'%>" width="300" height="85">--></td>
  </div><!-- end of header -->  	
  </tr>
  <tr>
    <td height="5" background="<%=$this->Page->Theme->BaseUrl.'/asset/images/bkg03.png'%>"></td>
  </tr>
  <tr>
    <td height="30" background="<%=$this->Page->Theme->BaseUrl.'/asset/images/bkg04.png'%>">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="1%" valign="top" class="table_01"><table width="250" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td><table width="215" border="0" align="right" cellpadding="0" cellspacing="0">
              <tr>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/asset/images/menu_topbar.png'%>" width="215" height="15"></td>
              </tr>
              <tr>
                <td background="<%=$this->Page->Theme->BaseUrl.'/asset/images/menu_bkg.png'%>">
				<div id="sidebar">			
					<com:Application.Portlets.SidePortletAsset />								
				</div><!-- end of sidebar -->	</td>
              </tr>
              <tr>
                <td><img src="<%=$this->Page->Theme->BaseUrl.'/asset/images/menu_btmbar.png'%>" width="215" height="15"></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        </table></td>
        <td width="99%" valign="top" background="<%=$this->Page->Theme->BaseUrl.'/asset/images/bkg04.png'%>"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td class="table_02"><com:TContentPlaceHolder ID="Main" /></td>
          </tr>
        </table></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td height="5" background="<%=$this->Page->Theme->BaseUrl.'/asset/images/bkg03.png'%>"></td>
  </tr>
  <tr>
    <td valign="top" class="table_01b"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
	  <div id="footer">
        <td class="text_05"><div align="center">&copy; Copyright of Intergo Telematics, Inc. 2008. All Right Reserved </div></td>
	  </div>
      </tr>
    </table></td>
  </tr>
</table>
</com:TForm>
</div><!-- end of page -->
</body>
</html>