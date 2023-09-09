<?php
class SubSumberObatEdit extends SimakConf
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
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->DDSumber->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumber->dataBind(); 
			$SubSbrObtRecord=$this->SubSumberRecord;
			
		// Populates the input controls with the existing user data
			$this->ID->Text=$SubSbrObtRecord->id;
			$this->nama->Text=$SubSbrObtRecord->nama;			
			$this->DDSumber->SelectedValue=$SubSbrObtRecord->id_sumber;
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
           	$SubSbrObtRecord=$this->SubSumberRecord;           
			$SubSbrObtRecord->id=ucwords($this->ID->Text);
            $SubSbrObtRecord->nama=ucwords($this->nama->Text);				
  			$SubSbrObtRecord->id_sumber=(string)$this->DDSumber->SelectedValue;            
			// saves to the database via Active Record mechanism
            $SubSbrObtRecord->save(); 		
			//Return to the origin page	
			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Farmasi.SubSumberObat'));
        }			
	}
	
		
	protected function getSubSumberRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$SubSumberRecord=SubSumberObatRecord::finder()->findByPk($ID);		
		if(!($SubSumberRecord instanceof SubSumberObatRecord))
			throw new THttpException(500,'id tidak benar.');
		return $SubSumberRecord;
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.SubSumberObat'));		
	}
}
?>