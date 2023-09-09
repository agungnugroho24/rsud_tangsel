<?php
class masterRadRujukanEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$dataRadRujukan=$this->RadRujukanRecord;
			
			// Populates the input controls with the existing user data			
            $this->ID->Text=$dataRadRujukan->id;
			$this->nama->Text=$dataRadRujukan->nama;			
			
			$this->ID->Focus();
			
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           // $temp=$this->Request['ID'];
			//$dataProdObat = ProdusenObatRecord::finder()->findByPK($temp);
			$dataRadRujukan=$this->RadRujukanRecord;
			
            $dataRadRujukan->id=ucwords($this->ID->Text);
		    $dataRadRujukan->nama=ucwords($this->nama->Text);
			
			// saves to the database via Active Record mechanism
            $dataRadRujukan->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Rad.masterRadRujukan'));	
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
	
	protected function getRadRujukanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$RadRujukanRecord=RadRujukanRecord::finder()->findByPk($ID);		
		if(!($RadRujukanRecord instanceof RadRujukanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $RadRujukanRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Rad.masterRadRujukan'));			
	}
}
?>