<?php
class MainLayout extends TTemplateControl
{	
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js1', './scripts/jquery.js');	
			
		$this->getPage()->getClientScript()->registerHeadScriptFile('js1', './scripts/jquery-1.4.2.min.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js3', './scripts/jquery.layout.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/jquery-ui-1.8.5.custom.min.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js5', './scripts/jquery.ui.datepicker-id.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js6', './scripts/jquery.loadmask.min.js');
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js7', './scripts/flexigrid.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js8', './scripts/jquery-ui.growl.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js9', './scripts/jquery-framedialog_tes.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js10', './scripts/jclip.js');
		
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css1','./scripts/jquery-ui-1.8.5.custom.css');
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css2','./scripts/jquery.loadmask.css');
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css3','./scripts/css/flexigrid.css');
		
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/niceforms.js');
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/complex.js');
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js2', './scripts/jqFancyTransitions.1.7.min.js');
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css1','./scripts/niceforms-default.css');
		
		/*
		$session=new THttpSession;     
		$session->open();
		$tmp=$session['tmpTheme'];
		if(isset($tmp))
		{  
			$this->Page->Theme=$tmp;
			$this->ThemeName->SelectedValue = $tmp;	
		}
		else
		{						
			$this->Page->Theme='hijau';	
			$this->ThemeName->SelectedValue = 'hijau';	
		}	
		$session->close();
		*/
		
		if(UserRecord::finder()->findByPk($this->User->Name)->theme)
		{
			$tmp = UserRecord::finder()->findByPk($this->User->Name)->theme;
			
			$this->Page->Theme = $tmp;
			$this->ThemeName->SelectedValue = $tmp;	
		}
		else
		{
			$this->Page->Theme='hijau';	
			$this->ThemeName->SelectedValue = 'hijau';	
		}
		
		if(!$this->Page->IsPostBack && !$this->Page->IsCallBack)
		{	
			$allowed = $this->authApp('0');
			if($allowed == "False")
				$this->rekamMedisCtrl->Visible = false;
			
			$allowed = $this->authApp('11');
			if($allowed == "False")
				$this->rekamMedisCtrl2->Visible = false;	
			
			$allowed = $this->authApp('1');
			if($allowed == "False")
				$this->rawatInapCtrl->Visible = false;	
			
			$allowed = $this->authApp('2');
			if($allowed == "False")
				$this->kasirCtrl->Visible = false;	
				
			$allowed = $this->authApp('3');
			if($allowed == "False")
				$this->apotikCtrl->Visible = false;
			
			$allowed = $this->authApp('4');
			if($allowed == "False")
				$this->farmasiCtrl->Visible = false;		
			
			$allowed = $this->authApp('5');
			if($allowed == "False")
				$this->labCtrl->Visible = false;
			
			$allowed = $this->authApp('6');
			if($allowed == "False")
				$this->radCtrl->Visible = false;
			
			$allowed = $this->authApp('12');
			if($allowed == "False")
				$this->fisioCtrl->Visible = false;
			
			$allowed = $this->authApp('13');
			if($allowed == "False")
				$this->CtScanCtrl->Visible = false;
			
			$allowed = $this->authApp('7');
			if($allowed == "False")
				$this->keuanganCtrl->Visible = false;
			
			$allowed = $this->authApp('8');
			if($allowed == "False")
				$this->kepegawaianCtrl->Visible = false;
			
			$allowed = $this->authApp('9');
			if($allowed == "False")
				$this->admSistemCtrl->Visible = false;
			
			$allowed = $this->authApp('10');
			if($allowed == "False")
				$this->pendaftaranCtrl->Visible = false;	
		}
		
	}
	
	protected function authApp($varApp)
    {		
		$arrApp=array();
		$var=$this->User->IsAppAuth;
		$arrApp = explode(',', $var);	
		if (in_array($varApp, $arrApp)) {
    		$value = "True";
		}else{
			$value = "False";
		}		
		return $value;
	}	
		
	
	public function gantiPass($sender,$param)
	{
		$url=$this->Service->constructUrl('Admin.RubahPassword');
        $this->Response->redirect($url);
	}
	
	
	public function doTheme($sender,$param)
	{
		$data = UserRecord::finder()->findByPk($this->User->Name);
		$data->theme = $this->ThemeName->SelectedValue;
		$data->save();
		
		
		$tmpTheme = $this->ThemeName->SelectedValue;
		$session=new THttpSession;     
		$session->CookieMode='Allow';
		$session->open();		
		$session['tmpTheme']=$tmpTheme;		
		
		$session->close();
		
		$name='tmpTheme';
		$cookie=new THttpCookie($name,$tmpTheme);
		$this->Response->Cookies[]=$cookie;
		
		
		//$this->Username->focus();	
		//$this->setControlState('tmpTheme',$tmpTheme,'');	
		
	}
	
	public function logoutClicked($sender,$param)
	{
		$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi Logout\', content:\'<p>Logout sekarang ?</p>\',timeout: 6000,dialog:{
						modal: true,
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
					}});');	
	}
	
		
   	public function queryAction($sql,$mode)
	{		
		$conn = new TDbConnection("mysql:host=localhost;dbname=simak_tangsel","simyantu","jackass");		
		$conn->Persistent=true;
		$conn->Active=true;				
		if($mode == "C")//Use this with INSERT, DELETE and EMPTY operation
		{
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();						
		}
		else if($mode == "S")//Return for select statement
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader->readAll();			
		}		
		else if($mode == "R") //Return set of rows
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader;			
		}
		else if($mode == "D") //Droped table
		{
			$que = "DROP TABLE IF EXISTS " . $sql;
			$comm=$conn->createCommand($que);		
			$dataReader = $comm->query();						
		}
		else if($mode == "E") //Hapus isi table
		{
			$que = "TRUNCATE TABLE " . $sql;
			$comm=$conn->createCommand($que);		
			$dataReader = $comm->query();						
		}
		return $rows;
		$conn->Active=false;				
	}
	
	
   	public function logoutAccept($sender,$param)
    {
				
        $user=$this->User->Name;
		$sql="SELECT nm_table FROM tbx_destroy WHERE user='$user'";
		$arrData=$this->queryAction($sql,'R');		
		if($arrData != '')
		{
			foreach($arrData as $row) 
			{ 
				$sql = $row['nm_table'];				
				$this->queryAction($sql,'D');
			}
			
			$sql="DELETE FROM tbx_destroy WHERE user='$user'";
			$this->queryAction($sql,'C');					
		}
		
		$this->Application->getModule('auth')->logout();
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
}
?>
