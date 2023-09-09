<?php
class PbfObatEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		/*
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		*/
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$temp=$this->Request['ID'];
			$dataPbfObat = PbfObatRecord::finder()->findByPK($temp);
			
			// Populates the input controls with the existing user data
			$this->ID->Text=$dataPbfObat->getColumnValue('id');
			$this->nama->Text=$dataPbfObat->getColumnValue('nama');
			$this->alamat->Text=$dataPbfObat->getColumnValue('alamat');
			$this->telp->Text=$dataPbfObat->getColumnValue('tlp');
			$this->npwp->Text=$dataPbfObat->getColumnValue('npwp');
			$this->npkp->Text=$dataPbfObat->getColumnValue('npkp');
					
			$this->ID->Focus();
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
             $temp=$this->Request['ID'];
			$dataPbfObat = PbfObatRecord::finder()->findByPK($temp);
			
            $dataPbfObat->nama=ucwords($this->nama->Text);
			$dataPbfObat->alamat=$this->alamat->Text;
			$dataPbfObat->tlp=$this->telp->Text;
			$dataPbfObat->npwp=$this->npwp->Text;				  
			$dataPbfObat->npkp=$this->npkp->Text;
			// saves to the database via Active Record mechanism
            $dataPbfObat->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.PbfObat'));	
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
	
	
	protected function getnmTindakanRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$nmTindakanRecord=NamaTindakanRecord::finder()->findByPk($ID);		
		if(!($nmTindakanRecord instanceof NamaTindakanRecord))
			throw new THttpException(500,'id tidak benar.');
		return $nmTindakanRecord;
	}	
	*/
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.PbfObat'));
	}
}
?>
