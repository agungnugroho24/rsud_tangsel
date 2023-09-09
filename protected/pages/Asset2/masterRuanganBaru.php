<?php
class masterRuanganBaru extends SimakConf
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
		$param->IsValid=(AssetRuangRecord::finder()->findByPk($this->kode->Text)===null);
    }
	
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
			$newRuangan=new AssetRuangRecord();
			
			//$newRuangan->id=$this->kode->Text;			
			$newRuangan->nama=$this->nama->Text;
			$newRuangan->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterRuangan'));						
		}	
	}
	
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterRuangan'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
