<?php
class ObatKhususEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
 
	public function onPreLoad($param)
	{/*
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		*/
		if(!$this->IsPostBack)  // if the page is initially requested
		{			
			$dataObatRecord=$this->HrgObatKhususRecord;
			
			// Populates the input controls with the existing user data
			$ID=$this->Request['ID'];
			$IdObat = HrgObatKhususRecord::finder()->findByPk($ID)->id_obat;
			$nama=ObatRecord::finder()->findByPk($IdObat)->nama;			
			$this->nama->Text=$nama;
			//$this->hrgBeli->Text=$dataObatRecord->hrg_beli;
			$this->hrg->Text=$dataObatRecord->hrg_jual;
			//$this->minDepo->Text=$dataObatRecord->min_depo;
			//$this->maxDepo->Text=$dataObatRecord->max_depo;
			$this->hrg->Focus();
		}
	} 	 					
		
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
			if($this->hrg->Text >= $this->hrgBeli->Text) //jika harga jual >= harga beli
			{
				$dataObatRecord=$this->HrgObatKhususRecord; 
				$dataObatRecord->hrg_jual=TPropertyValue::ensureInteger($this->hrg->text);
				// saves to the database via Active Record mechanism
				$dataObatRecord->save(); 		
				//Return to the origin page	
				
				// redirects the browser to the homepage
				$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhusus'));
			}
			else
			{
				$this->errMsg->Visible = true; 
				$this->hrg->focus();
			}
            // populates a UserRecord object with user inputs
           	
        }		
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();');
		}	
	}
	
		
	protected function getHrgObatKhususRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$HrgObatKhususRecord=HrgObatKhususRecord::finder()->findByPk($ID);		
		if(!($HrgObatKhususRecord instanceof HrgObatKhususRecord))
			throw new THttpException(500,'id tidak benar.');
		return $HrgObatKhususRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatKhusus'));		
	}
}
?>