<?php
class SpesialisDokterEdit extends SimakConf
{
 
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind(); 
			$dataSpesialisRecord=$this->SpesialisRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$dataSpesialisRecord->id;
			$this->nama->Text=$dataSpesialisRecord->nama;			
			$this->DDKlinik->SelectedValue=$dataSpesialisRecord->id_klinik;
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
           	$dataSpesialisRecord=$this->SpesialisRecord;           
			$dataSpesialisRecord->id=ucwords($this->ID->Text);
            $dataSpesialisRecord->nama=ucwords($this->nama->Text);				
  			$dataSpesialisRecord->id_klinik=(string)$this->DDKlinik->SelectedValue;            
			// saves to the database via Active Record mechanism
            $dataSpesialisRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SpesialisDokter'));
        }			
	}
	
		
	protected function getSpesialisRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$SpesialisRecord=SpesialisRecord::finder()->findByPk($ID);		
		if(!($SpesialisRecord instanceof SpesialisRecord))
			throw new THttpException(500,'id tidak benar.');
		return $SpesialisRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SpesialisDokter'));		
	}
}
?>