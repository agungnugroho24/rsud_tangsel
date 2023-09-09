<?php
class masterDistEdit extends SimakConf
{
	protected function getDistRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$DistRecord=AssetDistributorRecord::finder()->findByPk($ID);		
		if(!($DistRecord instanceof AssetDistributorRecord))
			throw new THttpException(500,'id tidak benar.');
		return $DistRecord;
	}	
	
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		//$tmpVar=$this->authApp('0');
		//if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
		//	$this->Response->redirect($this->Service->constructUrl('menu'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{		
			$dataDistRecord=$this->DistRecord;
			
			// Populates the input controls with the existing user data
			//$this->kode->Text=$dataDistRecord->id;
			$this->nm->Text=$dataDistRecord->nama;		
			$this->almt->Text=$dataDistRecord->alamat;
			$this->noTlp->Text=$dataDistRecord->tlp;
			$this->noFax->Text=$dataDistRecord->fax;
			$this->noNpwp->Text=$dataDistRecord->npwp;
			$this->ket->Text=$dataDistRecord->ket;
						
			$this->nm->Focus();
		}
	} 	 
			
	public function simpanClicked($sender,$param)
	{	
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$dataDistRecord=$this->DistRecord;
						
			$dataDistRecord->nama=$this->nm->Text;			
			$dataDistRecord->alamat=$this->almt->Text;
			$dataDistRecord->tlp=$this->noTlp->Text;
			$dataDistRecord->fax=$this->noFax->Text;
			$dataDistRecord->npwp=$this->noNpwp->Text;
			$dataDistRecord->ket=$this->ket->Text;
			
			// saves to the database via Active Record mechanism
            $dataDistRecord->save(); 		
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterDist'));	
        }			
	}
    
	
	
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterDist'));			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterDist'));			
	}
}
?>
