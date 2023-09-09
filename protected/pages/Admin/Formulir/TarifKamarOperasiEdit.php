<?php
class TarifKamarOperasiEdit extends SimakConf
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
			$sql = "SELECT * FROM tbm_operasi_kamar_nama ORDER BY nama ";
			$this->DDKamar->DataSource = KamarOperasiNamaRecord::finder()->findAllBySql($sql);
			$this->DDKamar->dataBind();
			
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
			
			$data = $this->KamarOperasiTarifRecord;
			
			// Populates the input controls with the existing user data	
			$this->DDKamar->SelectedValue = $data->id_kamar_operasi;			
			$this->DDKateg->SelectedValue = $data->id_kategori_operasi;			
			
			$sql="SELECT * FROM tbm_kamar_kelas WHERE persentase = 0 ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$idKelas = KelasKamarRecord::finder()->findBySql($sql)->id;
			
			//$this->tarifOperator->Text = floatval(KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kelas=?',array($data->id_kamar_operasi,$idKelas))->tarif);
			$this->tarifOperator->Text = floatval(KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kategori_operasi=?',array($data->id_kamar_operasi,$data->id_kategori_operasi))->tarif);
				
			$this->DDKamar->Focus();
			$this->setViewState('namaAwal',strtolower($data->id_kamar_operasi));
			$this->setViewState('kategAwal',strtolower($data->id_kategori_operasi));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			//$nama = strtolower(trim($this->nama->Text));
			$nama = strtolower($this->DDKamar->SelectedValue);
			$kateg = strtolower($this->DDKateg->SelectedValue);
			
			if($this->getViewState('namaAwal') != $nama || $this->getViewState('kategAwal') != $kateg)
			{
				$sql = "SELECT * FROM tbm_operasi_kamar_tarif WHERE id_kamar_operasi = '$nama' AND id_kategori_operasi = '$kateg'";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tarif Kamar Tindakan Operasi untuk kamar dan kategori yang dipilih sudah ada dalam database !</p>\',timeout: 4000,dialog:{
							modal: true
						}});');	
						
					$this->Page->CallbackClient->focus($this->DDKamar);		
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
		$data = $this->KamarOperasiTarifRecord;  	
		$idKamar = $data->id_kamar_operasi;
		$idKateg = $data->id_kategori_operasi;
			
		$tarifStandar = floatval($this->tarifOperator->Text);
			
		//UPDATE tbm_operasi_kamar_tarif
		$dataTarif = KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kategori_operasi=?',array($idKamar,$idKateg));
		$dataTarif->id_kamar_operasi = $this->DDKamar->SelectedValue;
		$dataTarif->id_kategori_operasi = $this->DDKateg->SelectedValue;
		$dataTarif->tarif = $tarifStandar ;
		$dataTarif->save(); 	
		
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tarif Kamar Tindakan Operasi telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Page->CallbackClient->focus($this->DDKamar);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamarOperasi'));
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
	
	protected function getKamarOperasiTarifRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$KamarOperasiTarifRecord=KamarOperasiTarifRecord::finder()->findByPk($ID);		
		if(!($KamarOperasiTarifRecord instanceof KamarOperasiTarifRecord))
			throw new THttpException(500,'id tidak benar.');
		return $KamarOperasiTarifRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamarOperasi'));			
	}
}
?>