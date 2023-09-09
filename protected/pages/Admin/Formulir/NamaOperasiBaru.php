<?php

class NamaOperasiBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{				
			$this->nama->focus();	
			
			$sql="SELECT * FROM tbm_operasi_kamar_nama ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDKamar->DataSource=$arrData;	
			$this->DDKamar->dataBind();
			
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
		}
	}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			$idKamar = $this->DDKamar->SelectedValue;
			$idKateg = $this->DDKateg->SelectedValue;
			
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
				//INSERT tbm_rad_tindakan
				$data = new OperasiNamaRecord();		
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
				$data->persentase_jasa_koordinator_anastesi = floatval($this->index11->Text);
				$data->persentase_jasa_koordinator_ast_anastesi = floatval($this->index12->Text);
				$data->persentase_jasa_koordinator_ast_instrumen = floatval($this->index13->Text);
				$data->persentase_jasa_koordinator_paramedis = floatval($this->index14->Text);
				$data->persentase_jasa_koordinator_resusitasi = floatval($this->index15->Text);
				
				$data->persentase_rs = floatval($this->index10->Text);
				$data->save();	
				
				$sql = "SELECT id FROM tbm_operasi_nama ORDER BY id DESC";
				$kodeTdk = OperasiNamaRecord::finder()->findBySql($sql)->id;
				
				$tarifStandar = floatval($this->tarifOperator->Text);
					
				//INSERT tbm_operasi_tarif
				$dataTarif = new OperasiTarifRecord;
				$dataTarif->id_operasi = $kodeTdk;
				//$dataTarif->id_kelas = $idKelas;	
				$dataTarif->js_dok_obgyn = $tarifStandar;
				$dataTarif->save(); 	
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Tindakan Operasi <b>'.ucwords($this->nama->Text).' untuk kamar dan kategori yang dipilih telah ditambahkan dalam database. <br/><br/> Apakah akan menambah tindakan baru lagi ?</p>\',timeout: 600000,dialog:{
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
				$this->DDKateg->SelectedValue = 'empty';	
				$this->index1->Text = '0';
				$this->index2->Text = '0';
				$this->index3->Text = '0';
				$this->index4->Text = '0';
				$this->index5->Text = '0';
				$this->index6->Text = '0';
				$this->index7->Text = '0';
				$this->index8->Text = '0';
				
				$this->tarifOperator->Text = '0';
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
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));	
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
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.NamaOperasi'));		
	}
}
?>