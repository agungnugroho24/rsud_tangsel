<?php
class AdminFarmasi extends SimakConf
{   
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
		
	public function onLoad($param)
	{
		parent::onLoad($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('Home'));
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->master->Visible=false;
			$this->rmShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;			
		}		
    }		
	
	protected function chGroup($sender,$param)
	{
		$this->master->Visible=true;	
		switch($this->group->SelectedValue)
		{
			case '0':
			$this->rmShow->Visible=true;
			$this->sysShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;
			break;		
		
			case '1':
			$this->rmShow->Visible=false;
			$this->sysShow->Visible=true;
			$this->poliShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;
			break;
			
			case '2':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=true;
			$this->sysShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;
			break;
			case '3':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->radShow->Visible=false;			
			$this->labShow->Visible=true;
			break;		
			case '4':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->radShow->Visible=true;			
			$this->labShow->Visible=false;
			
	}
	
	protected function chRmForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDRm->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.KelPasien'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PddkPasien'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.KelPasien'));
			break;
		}
	}
	
	protected function chSysForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDSys->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Poliklinik'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasien'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SpesialisDokter'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.AllowedModul'));
			break;
		}
	}
	
	protected function chPoliForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDPoli->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Tarif.TarifTindakan'));
			break;			
		}
	}
	
	protected function chLabForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDLab->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Lab.masterLabKel'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Lab.masterLabKateg'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Lab.tarifLab'));
			break;
		}
	}
	
	protected function chRadForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDRad->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Rad.masterRad'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Rad.masterRadKel'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Rad.masterRadKateg'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Rad.tarifRad'));
			break;
		}
	}
	
	protected function keluarClicked()
	{
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
}
?>
