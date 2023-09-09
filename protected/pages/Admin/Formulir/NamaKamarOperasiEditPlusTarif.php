<?php
class NamaKamarOperasiEdit extends SimakConf
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
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
			
			$data = $this->KamarOperasiNamaRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;			
			
			$sql="SELECT * FROM tbm_kamar_kelas WHERE persentase = 0 ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$idKelas = KelasKamarRecord::finder()->findBySql($sql)->id;
			
			$this->tarifOperator->Text = floatval(KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kelas=?',array($data->id,$idKelas))->tarif);
			$this->DDKateg->SelectedValue = KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? ',array($data->id))->id_kategori_operasi;
				
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
				$sql = "SELECT nama FROM tbm_operasi_kamar_nama WHERE LOWER(nama) = '$nama' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Kamar Tindakan Operasi dengan nama :<br/> <b>'.ucwords($this->nama->Text).'</b><br/> sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		$data = $this->KamarOperasiNamaRecord;  	
		$data->nama = ucwords($this->nama->Text);
		$data->save();	
		
		$idTindakan = $data->id;
		
		$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama";
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$idKelas = $row['id'];
			$persentaseKenaikan = $row['persentase'];
			$tarifStandar = floatval($this->tarifOperator->Text);
			
			//INSERT tbm_operasi_kamar_tarif
			$dataTarif = KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kelas=?',array($idTindakan,$idKelas));
			$dataTarif->id_kategori_operasi = $this->DDKateg->SelectedValue;
			$dataTarif->tarif = $tarifStandar + ($tarifStandar * $persentaseKenaikan / 100);
			$dataTarif->save(); 	
		}
		
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Kamar Tindakan Operasi <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamarOperasi'));
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
	
	protected function getKamarOperasiNamaRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KamarOperasiNamaRecord=KamarOperasiNamaRecord::finder()->findByPk($ID);		
		if(!($KamarOperasiNamaRecord instanceof KamarOperasiNamaRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KamarOperasiNamaRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaKamarOperasi'));			
	}
}
?>