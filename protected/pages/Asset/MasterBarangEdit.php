<?php
class MasterBarangEdit extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('12');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	 public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			$data = $this->BarangRecord;
			$id_jenis_barang = $data->id_jenis_barang;	
			
			$sql = "SELECT id,nama FROM tbm_barang_kelompok WHERE id_jenis_barang = '$id_jenis_barang' ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			$this->DDKelompok->Enabled=true;
			
			$this->DDJenis->SelectedValue = $id_jenis_barang;	
			$this->DDKelompok->SelectedValue = $data->id_kelompok_barang;	
			$this->DDStandardisasi->SelectedValue = $data->standardisasi;	
			$this->nama->Text = $data->nama;
			$this->merk->Text = $data->merk;
			$this->spesifikasi->Text = $data->spesifikasi;
			$this->deskripsi->Text = $data->deskripsi;
			$this->thn_pembuatan->Text = $data->thn_pembuatan;
			$this->thn_pengadaan->Text = $data->thn_pengadaan;
			$this->satuanBesar->Text = $data->jml_satuan_besar;
			
			$this->setViewState('idAwal',strtolower($data->id));
			$this->setViewState('namaAwal',strtolower($data->nama));
			//$this->DDKelompok->Enabled=false;
			//$this->nama->focus();
		}
		
	}	
	
	protected function getBarangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$BarangRecord=BarangRecord::finder()->findByPk($ID);		
		if(!($BarangRecord instanceof BarangRecord))
			throw new THttpException(500,'id tidak benar.');
		return $BarangRecord;
	}	
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDJenis->SelectedValue=='')
		{
			
			$gol = $this->DDJenis->SelectedValue;	
			$sql = "SELECT id,nama FROM tbm_barang_kelompok WHERE id_jenis_barang = '$gol' ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			$this->DDKelompok->Enabled=true;
		}
		else
		{
			$this->DDKelompok->DataSource=BarangKelompokRecord::finder()->findAll();
			$this->DDKelompok->dataBind();	
			$this->DDKelompok->Enabled=false;
		}
		
		$this->DDKelompok->SelectedIndex = '';
	}
	
	public function simpanClicked()
	{			
		if($this->Page->IsValid)  // when all validations succeed
        {	
			$idKelompok = $this->DDKelompok->SelectedValue;
			$nama = strtolower(trim($this->nama->Text));
			$idAwal = $this->getViewState('idAwal');
			$namaAwal = $this->getViewState('namaAwal');
			
			$sql = "SELECT nama FROM tbm_barang WHERE LOWER(nama) = '$nama' AND id_kelompok_barang = '$idKelompok' AND id <> '$idAwal' ";
			
			if(BarangRecord::finder()->findBySql($sql))
			{
				$this->Page->CallbackClient->focus($this->nama);
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Nama barang untuk jenis dan kelompok yang dipilih sudah ada. \nSilakan ganti dengan nama yang lain.");
				</script>';
			}
			else
			{
				$data= BarangRecord::finder()->findByPk($idAwal);
				$data->id_jenis_barang = $this->DDJenis->SelectedValue;
				$data->id_kelompok_barang = $idKelompok;
				$data->standardisasi = $this->DDStandardisasi->SelectedValue;
				$data->nama = trim($this->nama->Text);
				$data->merk = trim($this->merk->Text);
				$data->spesifikasi = trim($this->spesifikasi->Text);
				$data->deskripsi = trim($this->deskripsi->Text);
				$data->thn_pembuatan = trim($this->thn_pembuatan->Text);
				$data->thn_pengadaan = trim($this->thn_pengadaan->Text);
				$data->jml_satuan_besar=intval(trim($this->satuanBesar->Text));
				$data->save();	
				/*
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data aset barang sudah diedit.");
				</script>';
				*/
				$this->clear();
				
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Data <b>'.$namaAwal.'</b> telah diedit.</p>\',timeout: 600000,dialog:{
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
		}			
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
		$this->merk->Text = '';
		$this->spesifikasi->Text = '';
		$this->deskripsi->Text = '';
		$this->thn_pembuatan->Text = '';
		$this->thn_pengadaan->Text = '';
		$this->satuanBesar->Text = '1';
		
		$this->DDJenis->SelectedIndex = '';
		$this->DDKelompok->SelectedIndex = '';
		$this->Page->CallbackClient->focus($this->DDJenis);
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			//$this->Page->CallbackClient->focus($this->DDKamar);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->Response->Reload();	
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Asset.MasterBarang'));	
		}
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->clear();
		$this->Page->CallbackClient->focus($this->DDJenis);
		$this->Response->reload();		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterBarang'));		
	}
}
?>