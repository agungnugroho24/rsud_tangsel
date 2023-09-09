<?php
class NamaKamarEdit extends SimakConf
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
			$data = $this->KamarNamaRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;	
			
			$this->tarifOperator->Text = floatval(KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($data->id, '5'))->tarif);
			$this->tarifOperator2->Text = floatval(KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($data->id, '4'))->tarif);
			$this->tarifOperator3->Text = floatval(KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($data->id, '1'))->tarif);
			$this->tarifOperator4->Text = floatval(KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($data->id, '3'))->tarif);
			$this->tarifOperator5->Text = floatval(KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array($data->id, '2'))->tarif);
				
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
				$sql = "SELECT nama FROM tbm_kamar_nama WHERE LOWER(nama) = '$nama' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Kamar Rawat Inap dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		$data = $this->KamarNamaRecord;  	
		$data->nama = ucwords($this->nama->Text);
		$data->save();	
		
		$idKamar = $data->id;
		
		$tarifStandar = floatval($this->tarifOperator->Text);
			
		//INSERT tbm_kamar_tarif
		if(floatval($this->tarifOperator->Text))
		{
			$dataTarif = KamarHargaRecord::finder()->find('id_kmr=? AND id_kls=?',array($idKamar,'5'));
			
			if(!$dataTarif)
			{
				$dataTarif = new KamarHargaRecord;
				$dataTarif->id_kmr = $idKamar;
				$dataTarif->id_kls = '5';
			}
			
			$dataTarif->tarif = $tarifStandar;
			$dataTarif->save(); 
		}
		
		if(floatval($this->tarifOperator2->Text)) //kelas II
		{
			$dataTarif = KamarHargaRecord::finder()->find('id_kmr=? AND id_kls=?',array($idKamar,'4'));
			
			if(!$dataTarif)
			{
				$dataTarif = new KamarHargaRecord;
				$dataTarif->id_kmr = $idKamar;
				$dataTarif->id_kls = '4';
			}
				
			$dataTarif->tarif = $this->tarifOperator2->Text;
			$dataTarif->save();
		}
		
		if(floatval($this->tarifOperator3->Text)) //kelas I
		{
			$dataTarif = KamarHargaRecord::finder()->find('id_kmr=? AND id_kls=?',array($idKamar,'1'));
			
			if(!$dataTarif)
			{
				$dataTarif = new KamarHargaRecord;
				$dataTarif->id_kmr = $idKamar;
				$dataTarif->id_kls = '1';
			}
				
			$dataTarif->tarif = $this->tarifOperator3->Text;
			$dataTarif->save();
		}
		
		if(floatval($this->tarifOperator4->Text)) //kelas I Utama
		{
			$dataTarif = KamarHargaRecord::finder()->find('id_kmr=? AND id_kls=?',array($idKamar,'3'));
			
			if(!$dataTarif)
			{
				$dataTarif = new KamarHargaRecord;
				$dataTarif->id_kmr = $idKamar;
				$dataTarif->id_kls = '3';
			}
				
			$dataTarif->tarif = $this->tarifOperator4->Text;
			$dataTarif->save();
		}
		
		if(floatval($this->tarifOperator5->Text)) //kelas VIP
		{
			$dataTarif = KamarHargaRecord::finder()->find('id_kmr=? AND id_kls=?',array($idKamar,'2'));
			
			if(!$dataTarif)
			{
				$dataTarif = new KamarHargaRecord;
				$dataTarif->id_kmr = $idKamar;
				$dataTarif->id_kls = '2';
			}
				
			$dataTarif->tarif = $this->tarifOperator5->Text;
			$dataTarif->save();
		}
				
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Kamar Rawat Inap  <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamar'));
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
	
	protected function getKamarNamaRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KamarNamaRecord=KamarNamaRecord::finder()->findByPk($ID);		
		if(!($KamarNamaRecord instanceof KamarNamaRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KamarNamaRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamar'));			
	}
}
?>