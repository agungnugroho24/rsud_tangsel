<?php
class KlasObatEdit extends SimakConf
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
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind(); 
			$klasRecord=$this->klasObatRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$klasRecord->id;
			$this->nama->Text=$klasRecord->jenis;			
			$this->DDGol->SelectedValue=$klasRecord->gol_id;
			$this->ID->Focus();
		}
	} 	 					
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}		
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$klasRecord=$this->klasObatRecord;           
			$klasRecord->id=ucwords($this->ID->Text);
            $klasRecord->jenis=ucwords($this->nama->Text);				
  			$klasRecord->gol_id=(string)$this->DDGol->SelectedValue;            
			// saves to the database via Active Record mechanism
            $klasRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));
        }			
	}
	
		
	protected function getklasObatRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$klasRecord=KlasifikasiObatRecord::finder()->findByPk($ID);		
		if(!($klasRecord instanceof KlasifikasiObatRecord))
			throw new THttpException(500,'id tidak benar.');
		return $klasRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));		
	}
}
?>