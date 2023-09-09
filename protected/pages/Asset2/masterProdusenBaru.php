<?php
class masterProdusenBaru extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{				
			$this->nm->focus();					
		}
	}	
	
	
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
					
			$newDist=new AssetProdusenRecord();
			
			//$newDist->id=$this->kode->Text;
			$newDist->nama=$this->nm->Text;			
			$newDist->alamat=$this->almt->Text;
			$newDist->tlp=$this->noTlp->Text;
			$newDist->fax=$this->noFax->Text;
			$newDist->npwp=$this->noNpwp->Text;
			$newDist->ket=$this->ket->Text;
						
			$newDist->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Asset.masterProdusen'));						
		}	
	}
	
	public function batalClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterProdusen'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
