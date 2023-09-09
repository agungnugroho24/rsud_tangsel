<?php

class TarifOperasiBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$this->ID->focus();					
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiTarifRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newRuangPasien=new OperasiTarifRecord();
			
			$newRuangPasien->id_opr=$this->ID->Text;			
			$newRuangPasien->vip=$this->vip->Text;
			$newRuangPasien->kelas1=$this->kls1->Text;
			$newRuangPasien->kelas2=$this->kls2->Text;
			$newRuangPasien->kelas3=$this->kls3->Text;
			
			$newRuangPasien->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasi'));						
		}
	}	
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasi'));		
	}
}
?>