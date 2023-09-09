<?php
class DerivatObatEdit extends SimakConf
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
			$derivatRecord=$this->derivatObatRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$derivatRecord->id;
			$this->nama->Text=$derivatRecord->nama;			
			$this->DDGol->SelectedValue=$derivatRecord->gol_id;
			$this->DDKlas->SelectedValue=$derivatRecord->klas_id;
			$this->ID->Focus();
		}
	} 	 					
	
	public function onLoad($param)
	{
		/*
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		*/
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$id_gol = $this->DDGol->SelectedValue;	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $id_gol);
			$this->DDKlas->dataBind(); 
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
	
	public function selectionChangedGol($sender,$param)
	{		
		if($this->DDGol->SelectedValue != ''){
			$id_gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $id_gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else{
			$this->DDKlas->Enabled=false;
		}
	} 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$derivatRecord=$this->derivatObatRecord;          
			$derivatRecord->id=ucwords($this->ID->Text);
            $derivatRecord->nama=ucwords($this->nama->Text);				
  			$derivatRecord->gol_id=(string)$this->DDGol->SelectedValue;
			$derivatRecord->klas_id=(string)$this->DDKlas->SelectedValue;            
			// saves to the database via Active Record mechanism
            $derivatRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.DerivatObat'));
        }			
	}
	
		
	protected function getderivatObatRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$derivatObatRecord=DerivatObatRecord::finder()->findByPk($ID);		
		if(!($derivatObatRecord instanceof DerivatObatRecord))
			throw new THttpException(500,'id tidak benar.');
		return $derivatObatRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.DerivatObat'));		
	}
}
?>