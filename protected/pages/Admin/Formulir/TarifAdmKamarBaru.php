<?php

class TarifAdmKamarBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$this->DDKamar->DataSource=KamarKelasRecord::finder()->findAll();
				$this->DDKamar->dataBind();				
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        //$param->IsValid=(KamarHargaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newRuangPasien=new TarifAdmKamarRecord();
			
			$newRuangPasien->id_kls=$this->DDKamar->SelectedValue;
			$newRuangPasien->adm=$this->adm->Text;
			$newRuangPasien->jsp=$this->jsp->Text;
			$newRuangPasien->utk=$this->utk->Text;
			
			$newRuangPasien->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifAdmKamar'));						
		}
	}	
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifAdmKamar'));		
	}
}
?>