<?php
class SubSumberObatBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
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
			$this->DDSumber->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumber->dataBind();
						
			$this->ID->focus();								
		}		
	}
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(SubSumberObatRecord::finder()->findByPk($this->ID->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{			
		if($this->IsValid)  // when all validations succeed
        {
           	//$idGol=$this->DDSumber->SelectedValue;
           // $sql = "SELECT * FROM tbm_golongan_obat WHERE id = '$idGol'";
        	//$cariNamaGol=SumberObatRecord::finder()->findBySql($sql);
			
		    // populates a UserRecord object with user inputs
			$SubSumberObatRecord=new SubSumberObatRecord;
			$SubSumberObatRecord->id_sumber=TPropertyValue::ensureString($this->DDSumber->SelectedValue);
			$SubSumberObatRecord->id=ucwords($this->ID->Text);		            
            $SubSumberObatRecord->nama=ucwords($this->nama->Text);
			//$SubSumberObatRecord->nama_kab=ucwords($cariNamaKab->getColumnValue('nama'));
			
			// saves to the database via Active Record mechanism
            $SubSumberObatRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.SubSumberObat'));
        }			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.SubSumberObat'));		
	}
}
?>