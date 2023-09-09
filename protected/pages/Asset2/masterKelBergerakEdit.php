<?php
class masterKelBergerakEdit extends SimakConf
{
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		$tmpVar=$this->authApp('0');
		//if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			//$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));
		if(!$this->IsPostBack)  // if the page is initially requested
		{		
			$dataAssetKelBergerakRecord=$this->AssetKelBergerakRecord;
			
			// Populates the input controls with the existing user data
			$this->kode->Text=$dataAssetKelBergerakRecord->id;		
			$this->nama->Text=$dataAssetKelBergerakRecord->nama;
			
			$this->nama->Focus();
		}
	} 	 
			
	public function simpanClicked($sender,$param)
	{	
		
		if($this->IsValid)  // when all validations succeed
        {
            // populates a UserRecord object with user inputs
           	$dataAssetKelBergerakRecord=$this->AssetKelBergerakRecord;
			
			$dataAssetKelBergerakRecord->id=$this->kode->Text;			
			$dataAssetKelBergerakRecord->nama=$this->nama->Text;
			
			// saves to the database via Active Record mechanism
            $dataAssetKelBergerakRecord->save(); 		
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterKelBergerak'));	
        }			
	}
	
	protected function getAssetKelBergerakRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AssetKelBergerakRecord=AssetKelBergerakRecord::finder()->findByPk($ID);		
		if(!($AssetKelBergerakRecord instanceof AssetKelBergerakRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AssetKelBergerakRecord;
	}	
    
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelBergerak'));			
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelBergerak'));			
	}
}
?>
