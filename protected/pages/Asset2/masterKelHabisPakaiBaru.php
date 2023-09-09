<?php
class masterKelHabisPakaiBaru extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{				
			$this->nama->focus();					
		}
	}	
	
	public function checkRM($sender,$param)
    {       
		// valid if the id kabupaten is not found in the database
		$param->IsValid=(AssetKelHbsPakaiRecord::finder()->findByPk($this->kode->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
			$newKelompok=new AssetKelHbsPakaiRecord();
			
			//$newKelompok->id=$this->kode->Text;			
			$newKelompok->nama=$this->nama->Text;
			$newKelompok->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterKelHabisPakai'));						
		}	
	}
	
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelHabisPakai'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
