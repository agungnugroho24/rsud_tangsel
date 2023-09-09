<?php
class SpesialisDokterBaru extends SimakConf
{
 public function onLoad($param)
	{
		parent::onLoad($param);
		/*
		$tmpVar=$this->authApp('8');//ID aplikasi keKaban
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
		{
			$this->Response->redirect($this->Service->constructUrl('Home'));
		
		}
		*/
			
		if(!$this->IsPostBack){
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
						
			$this->ID->focus();								
		}		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(SpesialisRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
           	//$idGol=$this->DDKlinik->SelectedValue;
           // $sql = "SELECT * FROM tbm_golongan_obat WHERE id = '$idGol'";
        	//$cariNamaGol=PoliklinikRecord::finder()->findBySql($sql);
			
		    // populates a UserRecord object with user inputs
			$newSpesialisRecord=new SpesialisRecord;
			$newSpesialisRecord->id_klinik=TPropertyValue::ensureString($this->DDKlinik->SelectedValue);
			$newSpesialisRecord->id=ucwords($this->ID->Text);		            
            $newSpesialisRecord->nama=ucwords($this->nama->Text);
			//$SpesialisRecord->nama_kab=ucwords($cariNamaKab->getColumnValue('nama'));
			
			// saves to the database via Active Record mechanism
            $newSpesialisRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SpesialisDokter'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SpesialisDokter'));		
	}
}
?>
