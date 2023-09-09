<?php
class TarifOperasiEdit extends SimakConf
{

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$dataRuangPasien=$this->OperasiTarifRecord;
			
			// Populates the input controls with the existing user data			
            $this->ID->Text=$dataRuangPasien->id_opr;
			$this->vip->Text=$dataRuangPasien->vip;	
			$this->kls1->Text=$dataRuangPasien->kelas1;	
			$this->kls2->Text=$dataRuangPasien->kelas2;	
			$this->kls3->Text=$dataRuangPasien->kelas3;			
			
			$this->ID->Focus();
			
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           // $temp=$this->Request['ID'];
			//$dataProdObat = ProdusenObatRecord::finder()->findByPK($temp);
			$dataRuangPasien=$this->OperasiTarifRecord;
			
            $dataRuangPasien->id_opr=$this->ID->Text;
		    $dataRuangPasien->vip=$this->vip->Text;
			$dataRuangPasien->kelas1=$this->kls1->Text;
			$dataRuangPasien->kelas2=$this->kls2->Text;
			$dataRuangPasien->kelas3=$this->kls3->Text;
			
			// saves to the database via Active Record mechanism
            $dataRuangPasien->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasi'));	
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
	
	protected function getOperasiTarifRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$OperasiTarifRecord=OperasiTarifRecord::finder()->findByPk($ID);		
		if(!($OperasiTarifRecord instanceof OperasiTarifRecord))
			throw new THttpException(500,'id tidak benar.');
		return $OperasiTarifRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifOperasi'));			
	}
}
?>