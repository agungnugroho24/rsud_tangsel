<?php
class masterRadKategEdit extends SimakConf
{
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	}

	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$KelurahanRecord = $this->KelPasRecord;		
            $this->ID->Text=$KelurahanRecord->kode;
			$this->nama->Text=$KelurahanRecord->jenis;			
			
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($KelurahanRecord->jenis));
			
		}
	} 	 
	
	public function simpanClicked($sender,$param)	{			
		
		if($this->IsValid)  
        {
			$nama = strtolower(trim($this->nama->Text));
			
			if($this->getViewState('namaAwal') != $nama)
			{
				$sql = "SELECT jenis FROM tbm_rad_kategori WHERE LOWER(jenis) = '$nama' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
						('','unmaskContent();
							jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Kategori Tindakan '.ucwords($this->nama->Text).' sudah ada dalam database !</p>\',timeout: 4000,dialog:{
								modal: true
							}});');	
							
					$this->Page->CallbackClient->focus($this->nama);	
				}
				else
				{
					$this->prosesSimpan();
				}		
			}
			else
			{
				$this->prosesSimpan();
			}
        }		
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}	
	}
	
	public function prosesSimpan()
	{
		$KelurahanRecord=$this->KelPasRecord;
		$KelurahanRecord->jenis = ucwords($this->nama->Text);
		$KelurahanRecord->save(); 
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Nama Kategori Tindakan '.ucwords($this->getViewState('namaAwal')).' telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	
		
		$this->nama->Text = '';
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
			$this->Response->redirect($this->Service->constructUrl('Rad.masterRadKateg'));	
		}
	}
	
	
	/*
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Tarif.NamaTindakan';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}
		return $balik;
	}
	*/
	
	protected function getKelPasRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KelPasRecord=RadKategRecord::finder()->findByPk($ID);		
		if(!($KelPasRecord instanceof RadKategRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KelPasRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Rad.masterRadKateg'));			
	}
}
?>