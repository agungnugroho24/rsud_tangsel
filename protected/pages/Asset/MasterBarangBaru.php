<?php
class MasterBarangBaru extends SimakConf
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
			
			$this->DDKelompok->Enabled=false;
			//$this->nama->focus();
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
				
		}
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
			$idUser = $this->User->IsUserNip;
			$idKelompok = $this->DDKelompok->SelectedValue;
			$nama = strtolower(trim($this->nama->Text));
			
			$sql = "SELECT nama FROM tbm_barang WHERE LOWER(nama) = '$nama' AND id_kelompok_barang = '$idKelompok'";
			
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
				$data= new BarangRecord();	
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
				
				$sql = "SELECT MAX(id) AS id FROM tbm_barang ORDER BY id DESC";
				$idMax = BarangRecord::finder()->findBySql($sql)->id;
				$tglNow = date('Y-m-d');
				
				//INSERT tbt_aset_harga
				if(!HrgAsetRecord::finder()->findByKode($kodeObat))			
				{
					$HrgAsetRecord = new HrgAsetRecord;					
					$HrgAsetRecord->kode=$idMax;
					$HrgAsetRecord->sumber='01';
					$HrgAsetRecord->hrg_netto=0;
					$HrgAsetRecord->hrg_ppn=0;
					$HrgAsetRecord->hrg_netto_disc='0';
					$HrgAsetRecord->hrg_ppn_disc='0';
					$HrgAsetRecord->tgl=$tglNow;
					// saves to the database via Active Record mechanism
					$HrgAsetRecord->save();
				}
				
				//INSERT tbt_aset_harga
				if(!StokAsetRecord::finder()->find('id_barang = ? AND tujuan = ?',$idMax,'1'))//searching stok			
				{
					$sql = "SELECT id FROM tbt_aset_harga WHERE kode = '$idMax' ORDER BY id desc";
					$id_harga = HrgAsetRecord::finder()->findBySql($sql);
					$idHarga = $id_harga->id;
				
					// populates a UserRecord object with user inputs
					$StokRecord=new StokAsetRecord;	
					$StokRecord->id_barang=$idMax;
					$StokRecord->jumlah='0';
					$StokRecord->sumber='01';
					$StokRecord->id_harga=$idHarga;					
					$StokRecord->tujuan='1';
					// saves to the database via Active Record mechanism
					$StokRecord->save();
				}
				
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data aset barang baru telah dimasukan dalam database!");
				</script>';
				
				$this->clear();
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
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->clear();
		$this->Page->CallbackClient->focus($this->DDJenis);
		//$this->Response->redirect($this->Service->constructUrl('listBiblio'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterBarang'));		
	}
}
?>