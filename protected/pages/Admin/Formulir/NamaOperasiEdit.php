<?php
class NamaOperasiEdit extends SimakConf
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
			$sql="SELECT * FROM tbm_operasi_kamar_nama ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDKamar->DataSource=$arrData;	
			$this->DDKamar->dataBind();
			
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
			
			$data = $this->OperasiNamaRecord;
			
			// Populates the input controls with the existing user data	
			$this->nama->Text = $data->nama;
			$this->DDKamar->SelectedValue = $data->id_kamar_operasi;
			$this->DDKateg->SelectedValue = $data->id_kategori_operasi;
			$this->index1->Text = $data->index_operator;
			$this->index2->Text = $data->index_anastesi;
			$this->index3->Text = $data->index_ast_anastesi;
			$this->index4->Text = $data->index_ast_instrumen;
			$this->index5->Text = $data->index_paramedis;
			$this->index6->Text = $data->index_resusitasi;
			$this->index7->Text = $data->index_pengembang;
			$this->index8->Text = $data->index_penyulit;
			
			$this->index9->Text = $data->persentase_jasa_koordinator;
			$this->index11->Text = $data->persentase_jasa_koordinator_anastesi;
			$this->index12->Text = $data->persentase_jasa_koordinator_ast_anastesi;
			$this->index13->Text = $data->persentase_jasa_koordinator_ast_instrumen;
			$this->index14->Text = $data->persentase_jasa_koordinator_paramedis;
			$this->index15->Text = $data->persentase_jasa_koordinator_resusitasi;
			
			$this->index10->Text = $data->persentase_rs;
			
			$sql="SELECT * FROM tbm_kamar_kelas WHERE persentase = 0 ORDER BY nama";
			$arr = $this->queryAction($sql,'S');
			
			$idKelas = KelasKamarRecord::finder()->findBySql($sql)->id;
			
			//$this->tarifOperator->Text = floatval(OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',array($data->id,$idKelas))->js_dok_obgyn);
			$this->tarifOperator->Text = floatval(OperasiTarifRecord::finder()->find('id_operasi=?',array($data->id))->js_dok_obgyn);
				
			$this->nama->Focus();
			$this->setViewState('namaAwal',strtolower($data->nama));
			$this->setViewState('kamarAwal',strtolower($data->id_kamar_operasi));
			$this->setViewState('kategAwal',strtolower($data->id_kategori_operasi));
		}
	} 	
	
	public function simpanClicked($sender,$param)	
	{	
		if($this->IsValid)  // when all validations succeed
        {
			$nama = strtolower(trim($this->nama->Text));
			$idKamar = $this->DDKamar->SelectedValue;
			$idKateg = $this->DDKateg->SelectedValue;
			
			if($this->getViewState('namaAwal') != $nama && $this->getViewState('kamarAwal') != $idKamar && $this->getViewState('kategAwal') != $idKateg)
			{
				$sql = "SELECT nama FROM tbm_operasi_nama WHERE LOWER(nama) = '$nama' AND id_kamar_operasi='$idKamar' AND id_kategori_operasi='$idKateg' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Operasi dengan nama :<br/> <b>'.ucwords($this->nama->Text).' untuk kamar dan kategori yang dipilh sudah ada dalam database !</p>\',timeout: 4000,dialog:{
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
		$data = $this->OperasiNamaRecord;  	
		$data->nama = ucwords($this->nama->Text);	
		$data->id_kamar_operasi = $this->DDKamar->SelectedValue;			
		$data->id_kategori_operasi = $this->DDKateg->SelectedValue;				
		$data->index_operator = floatval($this->index1->Text);
		$data->index_anastesi = floatval($this->index2->Text);
		$data->index_ast_anastesi = floatval($this->index3->Text);
		$data->index_ast_instrumen = floatval($this->index4->Text);
		$data->index_paramedis = floatval($this->index5->Text);
		$data->index_resusitasi = floatval($this->index6->Text);
		$data->index_pengembang = floatval($this->index7->Text);
		$data->index_penyulit = floatval($this->index8->Text);
		
		$data->persentase_jasa_koordinator = floatval($this->index9->Text);
		$data->persentase_jasa_koordinator = floatval($this->index9->Text);
		$data->persentase_jasa_koordinator_anastesi = floatval($this->index11->Text);
		$data->persentase_jasa_koordinator_ast_anastesi = floatval($this->index12->Text);
		$data->persentase_jasa_koordinator_ast_instrumen = floatval($this->index13->Text);
		$data->persentase_jasa_koordinator_paramedis = floatval($this->index14->Text);
		$data->persentase_jasa_koordinator_resusitasi = floatval($this->index15->Text);
		
		$data->persentase_rs = floatval($this->index10->Text);
		$data->save();	
		
		$idTindakan = $data->id;
		
		$tarifStandar = floatval($this->tarifOperator->Text);
			
		//INSERT tbm_operasi_tarif
		$dataTarif = OperasiTarifRecord::finder()->find('id_operasi=?',array($idTindakan));
		//$dataTarif->js_dok_obgyn = $tarifStandar + ($tarifStandar * $persentaseKenaikan / 100);
		$dataTarif->js_dok_obgyn = $tarifStandar;
		$dataTarif->save(); 	
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tindakan Operasi <b>'.ucwords($this->getViewState('namaAwal')).'</b> telah sukses diperbaharui<br/><br/></p>\',timeout: 600000,dialog:{
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));
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
	
	protected function getOperasiNamaRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$OperasiNamaRecord=OperasiNamaRecord::finder()->findByPk($ID);		
		if(!($OperasiNamaRecord instanceof OperasiNamaRecord))
			throw new THttpException(500,'id tidak benar.');
		return $OperasiNamaRecord;
	}	
	
    
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));			
	}
}
?>