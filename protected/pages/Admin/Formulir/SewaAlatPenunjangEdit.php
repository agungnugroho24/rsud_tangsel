<?php
class SewaAlatPenunjangEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)        
		{
			$data = $this->SewaAlatPenunjangRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;	
			$this->tarifOperator->Text = $data->tarif;
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			
			if($this->getViewState('namaAwal') != $nama)
			{
				$sql = "SELECT nama FROM tbm_sewa_alat_penunjang WHERE LOWER(nama) = '$nama' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jasa Sewa Alat Penunjang dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
	
	protected function prosesSimpan()
	{
		//UPDATE tbm_rad_tindakan
		$data = $this->SewaAlatPenunjangRecord;  	
		$data->nama = ucwords($this->nama->Text);
		$data->tarif = floatval($this->tarifOperator->Text);
		$data->save();	
		
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Jasa Sewa Alat Penunjang  <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SewaAlatPenunjang'));
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
	
	protected function getSewaAlatPenunjangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$SewaAlatPenunjangRecord=SewaAlatPenunjangRecord::finder()->findByPk($ID);		
		if(!($SewaAlatPenunjangRecord instanceof SewaAlatPenunjangRecord))
			throw new THttpException(500,'id tidak benar.');
		return $SewaAlatPenunjangRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.SewaAlatPenunjang'));			
	}
}
?>