<?php
class CaraMasukPasienEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$dataCaraMasuk=$this->caramasukRecord;
			
			// Populates the input controls with the existing user data			
            $this->ID->Text=$dataCaraMasuk->id;
			$this->nama->Text=$dataCaraMasuk->nama;			
			
			$this->ID->Focus();
			
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           // $temp=$this->Request['ID'];
			//$dataProdObat = ProdusenObatRecord::finder()->findByPK($temp);
			$dataCaraMasuk=$this->caramasukRecord;
			
            $dataCaraMasuk->id=ucwords($this->ID->Text);
		    $dataCaraMasuk->nama=ucwords($this->nama->Text);
			
			// saves to the database via Active Record mechanism
            $dataCaraMasuk->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.CaraMasukPasien'));	
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
	
	protected function getcaramasukRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$caramasukRecord=caramasukRecord::finder()->findByPk($ID);		
		if(!($caramasukRecord instanceof caramasukRecord))
			throw new THttpException(500,'id tidak benar.');
		return $caramasukRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.CaraMasukPasien'));			
	}
}
?>