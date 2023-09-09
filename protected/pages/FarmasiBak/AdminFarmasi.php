<?php
class AdminFarmasi extends SimakConf
{   
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->master->Visible=true;			
		}		
    }		
	
	protected function chGroupForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDGroup->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.PbfObat'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.ProdObat'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.JenisBrg'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.SumberObat'));
			break;
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.SubSumberObat'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.SatObat'));
			break;
			case '6':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.GolObat'));
			break;
			case '7':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));
			break;
			case '8':
				$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.DerivatObat'));
			break;
		}
	}
	
	
	protected function keluarClicked()
	{
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
}
?>
