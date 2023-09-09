<?php
class masterRuanganEdit extends SimakConf
{
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		//if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			//$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{		
			$dataAssetRuangRecord=$this->AssetRuangRecord;
			
			// Populates the input controls with the existing user data
			$this->kode->Text=$dataAssetRuangRecord->id;		
			$this->nama->Text=$dataAssetRuangRecord->nama;
			
			$this->nama->Focus();
		}
	} 	 
			
	public function simpanClicked($sender,$param)
	{	
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$dataAssetRuangRecord=$this->AssetRuangRecord;
			
			$dataAssetRuangRecord->id=$this->kode->Text;			
			$dataAssetRuangRecord->nama=$this->nama->Text;
			
			// saves to the database via Active Record mechanism
            $dataAssetRuangRecord->save(); 		
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterRuangan'));	
        }			
	}
	
	protected function getAssetRuangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AssetRuangRecord=AssetRuangRecord::finder()->findByPk($ID);		
		if(!($AssetRuangRecord instanceof AssetRuangRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AssetRuangRecord;
	}	
    
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterRuangan'));			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterRuangan'));			
	}
}
?>
