<?php
class AdminFormulir extends SimakConf
{   
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{			
			$this->DDSys->getItems()->remove($this->DDSys->getItems()->findItemByValue('11'));	
			$this->DDPoli->getItems()->remove($this->DDPoli->getItems()->findItemByValue('1'));	
			
			$this->DDLab->getItems()->remove($this->DDLab->getItems()->findItemByValue('3'));	
			$this->DDLab->getItems()->remove($this->DDLab->getItems()->findItemByValue('5'));	
			
			$this->master->Visible=false;
			$this->rmShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;	
			$this->fisioShow->Visible=false;	
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
			$this->fisioShow->Visible=false;
			break;		
		
			case '1':
			$this->rmShow->Visible=false;
			$this->sysShow->Visible=true;
			$this->poliShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;
			$this->fisioShow->Visible=false;
			break;
			
			case '2':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=true;
			$this->sysShow->Visible=false;
			$this->labShow->Visible=false;
			$this->radShow->Visible=false;
			$this->fisioShow->Visible=false;
			break;
			case '3':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->radShow->Visible=false;			
			$this->labShow->Visible=true;
			$this->fisioShow->Visible=false;
			break;		
			case '4':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->radShow->Visible=true;			
			$this->labShow->Visible=false;
			$this->fisioShow->Visible=false;
			break;
			case '5':
			$this->rmShow->Visible=false;
			$this->poliShow->Visible=false;
			$this->sysShow->Visible=false;
			$this->radShow->Visible=false;			
			$this->labShow->Visible=false;
			$this->fisioShow->Visible=true;
			break;
		}
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
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Admin.KabAdmin'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Admin.KecAdmin'));
			break;
			case '6':
				$this->Response->redirect($this->Service->constructUrl('Admin.KelAdmin'));
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
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamar'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamar'));
			break;
			case '6':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamarOperasi'));
			break;
			case '7':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamarOperasi'));
			break;
			case '8':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));
			break;
			case '9':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasi'));
			break;
			case '10':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.KelasKamar'));
			break;
			case '11':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifAdmKamar'));
			break;
			case '12':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.OksigenTarif'));
			break;
			case '13':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.VisiteTarif'));
			break;
			case '14':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.AmbulanTarif'));
			break;
			case '15':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.AmbulanTungguTarif'));
			break;
			case '16':
				$this->Response->redirect($this->Service->constructUrl('Admin.fraksiJasmed'));
			break;
			case '17':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.masterPerujuk'));
			break;
			
			case '18':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Askep'));
			break;
			
			case '19':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Oksigen'));
			break;
			
			case '20':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TindakanKhusus'));
			break;
			
			case '21':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SewaAlatPenunjang'));
			break;
			
			case '22':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.BhpInap'));
			break;
			
			case '23':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SewaAlat'));
			break;
			
			case '24':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.Emergency'));
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
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaran'));
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
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Lab.masterLabRujukan'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Lab.tarifLabRujukan'));
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
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Rad.masterRadRujukan'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Rad.tarifRadRujukan'));
			break;
		}
	}
	
	protected function chFisioForm($sender,$param)
	{
		//$this->master->Visible=true;	
		switch($this->DDFisio->SelectedValue)
		{
			case '0':
				$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));
			break;	
			case '1':
				$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioKel'));
			break;
			case '2':
				$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioKateg'));
			break;
			case '3':
				$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisio'));
			break;
			case '4':
				$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioRujukan'));
			break;
			case '5':
				$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisioRujukan'));
			break;
		}
	}
	
	protected function keluarClicked()
	{
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
}
?>
