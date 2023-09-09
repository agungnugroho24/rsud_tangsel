<?php
class masterKelHabisPakaiEdit extends SimakConf
{
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		//if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			//$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{		
			$dataAssetKelHbsPakaiRecord=$this->AssetKelHbsPakaiRecord;
			
			// Populates the input controls with the existing user data
			$this->kode->Text=$dataAssetKelHbsPakaiRecord->id;		
			$this->nama->Text=$dataAssetKelHbsPakaiRecord->nama;
			
			$this->nama->Focus();
		}
	} 	 
			
	public function simpanClicked($sender,$param)
	{	
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$dataAssetKelHbsPakaiRecord=$this->AssetKelHbsPakaiRecord;
			
			$dataAssetKelHbsPakaiRecord->id=$this->kode->Text;			
			$dataAssetKelHbsPakaiRecord->nama=$this->nama->Text;
			
			// saves to the database via Active Record mechanism
            $dataAssetKelHbsPakaiRecord->save(); 		
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterKelHabisPakai'));	
        }			
	}
	
	protected function getAssetKelHbsPakaiRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AssetKelHbsPakaiRecord=AssetKelHbsPakaiRecord::finder()->findByPk($ID);		
		if(!($AssetKelHbsPakaiRecord instanceof AssetKelHbsPakaiRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AssetKelHbsPakaiRecord;
	}	
    
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelHabisPakai'));			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelHabisPakai'));			
	}
}
?>
