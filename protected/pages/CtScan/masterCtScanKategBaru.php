<?php

class masterRadKategBaru extends SimakConf
{
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('13');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
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
        // valid if the username is not found in the database
        $param->IsValid=(CtScanKategRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT jenis FROM tbm_ctscan_kategori WHERE LOWER(jenis) = '$nama' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama kategori Tindakan '.ucwords($this->nama->Text).' sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
				$this->Page->CallbackClient->focus($this->nama);	
			}
			else
			{
				//INSERT tbm_ctscan_kategori
				$newRadKel=new CtScanKategRecord();		
				$newRadKel->jenis = ucwords($this->nama->Text);
				$newRadKel->save();		
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Nama kategori '.ucwords($this->nama->Text).' telah ditambahkan dalam database. <br/><br/> Apakah akan menambah tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'tidak\');
							}
						}
					}});');	
				
				$this->nama->Text = '';
			}			
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('CtScan.masterRadKateg'));	
		}
	}
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('CtScan.masterRadKateg'));		
	}
}
?>