<?php
class masterKelBergerakBaru extends SimakConf
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
		$param->IsValid=(AssetKelBergerakRecord::finder()->findByPk($this->kode->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
			$newKelompok=new AssetKelBergerakRecord();
			
			//$newKelompok->id=$this->kode->Text;			
			$newKelompok->nama=$this->nama->Text;
			$newKelompok->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterKelBergerak'));						
		}	
	}
	
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterKelBergerak'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
