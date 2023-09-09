<?php
class masterLabKelEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$KelurahanRecord=$this->LabKelRecord;
			
			// Populates the input controls with the existing user data			
            $this->ID->Text=$KelurahanRecord->kode;
			$this->nama->Text=$KelurahanRecord->nama;			
			
			$this->ID->Focus();
			
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           // $temp=$this->Request['ID'];
			//$dataProdObat = ProdusenObatRecord::finder()->findByPK($temp);
			$KelurahanRecord=$this->LabKelRecord;
			
            $KelurahanRecord->kode=ucwords($this->ID->Text);
		    $KelurahanRecord->nama=ucwords($this->nama->Text);
			
			// saves to the database via Active Record mechanism
            $KelurahanRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Lab.masterLabKel'));	
        }			
	}
	
	/*
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.NamaTindakan';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	*/
	
	protected function getLabKelRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$LabKelRecord=LabKelRecord::finder()->findByPk($ID);		
		if(!($LabKelRecord instanceof LabKelRecord))
			throw new THttpException(500,'id tidak benar.');
		return $LabKelRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLabKel'));			
	}
}
?>