<?php

class masterFisioPaketAsuransiBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('11');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{				
			$stRawat = $this->stRawat->SelectedValue;
			$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE st = '$stRawat' ORDER BY nama ";
			$this->DDKamar->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDKamar->dataBind();
			
			$sql = "SELECT * FROM tbm_fisio_kategori ORDER BY jenis ";
			//$this->DDKateg->DataSource = FisioKategRecord::finder()->findAllBySql($sql);
			//$this->DDKateg->dataBind();
			//$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama ";
			$this->DDKelas->DataSource = FisioKategRecord::finder()->findAllBySql($sql);
			$this->DDKelas->dataBind();
			
			$sql = "SELECT 
				  tbm_fisio_tindakan.kode AS kode,
				  tbm_fisio_tindakan.nama AS nama
				FROM
				  tbm_fisio_tindakan
				  INNER JOIN tbm_fisio_tarif ON (tbm_fisio_tindakan.kode = tbm_fisio_tarif.id)
				WHERE
				  tbm_fisio_tindakan.st_rujukan = '0' 
				ORDER BY tbm_fisio_tindakan.nama";	
			
			$this->DDTindakan->DataSource = $this->queryAction($sql,'S');
			$this->DDTindakan->dataBind();
			
			$this->btnBatalTambah->Display = 'None';
			$this->nama->Focus();
		}
	}
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->Page->IsPostBack && !$this->Page->IsCallBack)  
		{
		}
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable WHERE st_delete='0' ORDER BY id";
			
			if($this->queryAction($sql,'S'))
			{
				$this->gridTarif->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->gridTarif->dataBind();	
			}
			else
			{
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
				$this->gridTarif->DataSource='';//Insert new row in tabel bro...
				$this->gridTarif->dataBind();	
				$this->clearViewState('nmTable');	
			}
		}	
	}
	
	
	public function stRawatChanged($sender,$param)
	{
		$stRawat = $this->stRawat->SelectedValue;
		$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE st = '$stRawat' ORDER BY nama ";
		$this->DDKamar->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
		$this->DDKamar->dataBind();
	}
	
	public function DDKelasChanged($sender,$param)
	{
		$sql = "SELECT 
				  tbm_fisio_tindakan.kode AS kode,
				  tbm_fisio_tindakan.nama AS nama
				FROM
				  tbm_fisio_tindakan
				WHERE
				  tbm_fisio_tindakan.st_rujukan = '0' ";
				  
		if($this->DDKelas->SelectedValue != '')
		{
			$kateg = $this->DDKelas->SelectedValue;
			$sql .= " AND tbm_fisio_tindakan.kategori = '$kateg'  ";	
		}
		
		$sql .= " ORDER BY tbm_fisio_tindakan.nama ";
				
		$this->DDTindakan->DataSource = $this->queryAction($sql,'S');
		$this->DDTindakan->dataBind();	
	}
	
	public function TambahClicked($sender,$param)
	{
		if($this->Page->IsValid)
		{
			$kateg = $this->DDKamar->SelectedValue;
			$kelas = $this->DDTindakan->SelectedValue;
			$nmKelas = $this->ambilTxt($this->DDTindakan);
			//$nmTarif = trim($this->nmTarif->Text);
			//$tarif = trim($this->tarifOperator->Text);
			$stRawat = $this->stRawat->SelectedValue;
			
			$sql = "SELECT * FROM tbm_fisio_tindakan_paket_asuransi_detail WHERE id_tindakan = '$kelas' AND id_asuransi = '$kateg' AND st_rawat = '$stRawat' AND id_paket <> '0'";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan untuk Asuransi dan Status Perawatan yang dipilih sudah ada dalam Paket Lain !</p>\',timeout: 3000,dialog:{
						modal: true
					}});');	
						
				$this->Page->CallbackClient->focus($this->DDKelas);	
			}
			else
			{	
				$this->makeTmpTablePangkat($kelas,$nmKelas,$nmTarif,$tarif,'');
			}
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'Tambah Data Gagal\', content:\'<p class="msg_error" >Pastikan pengisian data sudah sesuai !</p>\',timeout: 3000,dialog:{modal: true}});');	
		}
	}
	
	/*public function cekDb($kelas,$nmKelas,$nmTarif,$tarif,$idAwal)
	{
		$nmTarifCek = strtolower($nmTarif);
		$kateg = $this->DDKamar->SelectedValue;
		
		if($kateg=='')
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'jQuery.WsGrowl.show({title: \'Tambah Data Gagal\', content:\'<p class="msg_error">Pilih Kelompok terlebih dahulu!</p>\',timeout: 3000,dialog:{modal: true}}); unmaskContent();');	
			$this->Page->CallbackClient->focus($this->DDKateg);
		}
		else
		{
			//CEK tbm_operasi_kamar_tarif
			$sql="SELECT id FROM tbm_operasi_kamar_tarif WHERE id_kelas='$kelas' AND LOWER(nama)='$nmTarifCek' AND id_kategori_operasi = '$kateg' ";
			if($this->queryAction($sql,'S'))
			{
				$this->getPage()->getClientScript()->registerEndScript('', 'jQuery.WsGrowl.show({title: \'Tambah Data Gagal\', content:\'<p class="msg_error">Data untuk Kelompok, Kelas dan Nama Komponen sudah ada dalam database!</p>\',timeout: 5000,dialog:{modal: true}}); unmaskContent();');	
				$this->Page->CallbackClient->focus($this->nmTarif);
			}
			else
			{
				$this->makeTmpTablePangkat($kelas,$nmKelas,$nmTarif,$tarif,$idAwal);
			}
		}
	}*/
	
	public function makeTmpTablePangkat($kelas,$nmKelas,$nmTarif,$tarif,$idAwal)
	{
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
					id INT (2) auto_increment,
					id_awal int(11) DEFAULT NULL,
					kelas int(2) DEFAULT NULL,
					nm_kelas varchar(30) DEFAULT NULL,
					nama varchar(50) DEFAULT NULL,
					tarif float DEFAULT 0,
					st_delete char(1) DEFAULT '0',
					PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');								
		}	
		
		$nmTarifCek = strtolower($nmTarif);
		if($this->btnTambah->Text == 'Tambah')
			$sql="SELECT id FROM $nmTable WHERE kelas='$kelas' AND st_delete='0' ";
		else
		{
			$idEdit = $this->getViewState('idEdit');
			$sql="SELECT id FROM $nmTable WHERE kelas='$kelas' AND st_delete='0' AND id<>'$idEdit'";
		}
		
		if($this->queryAction($sql,'S'))
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'jQuery.WsGrowl.show({title: \'Tambah Data Gagal\', content:\'<p class="msg_error">Data Tindakan sudah ditambahkan sebelumnya!</p>\',timeout: 3000,dialog:{modal: true}}); unmaskContent();');	
			$this->Page->CallbackClient->focus($this->nmTarif);
		}
		else
		{
			if($this->btnTambah->Text == 'Tambah')
			{
				$sql="INSERT INTO $nmTable (
					kelas,
					nm_kelas,
					nama,
					tarif) 
				VALUES (
					'$kelas',
					'$nmKelas',
					'$nmTarif',
					'$tarif')";
			}
			else
			{
				$sql="UPDATE $nmTable SET
						kelas ='$kelas',
						nm_kelas ='$nmKelas',
						nama ='$nmTarif',
						tarif ='$tarif'
					WHERE 
						id = '$idEdit' ";
			}
			
			$this->queryAction($sql,'C');
			$this->batalEditClicked();
		}
	}
	
	public function itemCreatedPangkat($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem' || $item->ItemType==='DeleteItem')
        {
			$id = $this->gridTarif->DataKeys[$item->ItemIndex];
			
			$item->tarifColumn->Text = number_format($item->DataItem['tarif'],2,',','.');
        }
    }
	
	public function editRow($sender,$param)
    {
		$this->btnBatalTambah->Display = 'Dynamic';
		$this->btnTambah->Text = 'Simpan';
		
        $ID=$sender->CommandParameter;
		$this->setViewState('idEdit',$ID);			
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "SELECT * FROM $nmTable WHERE id='$ID'";
		if($this->queryAction($sql,'S'))
		{
			foreach($this->queryAction($sql,'S') as $row)
			{
				$sql = "SELECT 
						  tbm_fisio_tindakan.kode AS kode,
						  tbm_fisio_tindakan.nama AS nama
						FROM
						  tbm_fisio_tindakan
						WHERE
						  tbm_fisio_tindakan.st_rujukan = '0' ";
				
				$sql .= " ORDER BY tbm_fisio_tindakan.nama ";
						
				$this->DDTindakan->DataSource = $this->queryAction($sql,'S');
				$this->DDTindakan->dataBind();	
				
				$this->DDKelas->SelectedValue = 'empty';
				$this->DDTindakan->SelectedValue = $row['kelas'];
				//$this->nmTarif->Text = $row['nama'];
				//$this->tarifOperator->Text = $row['tarif'];
				
				$this->Page->CallbackClient->focus($this->DDTindakan);
			}
		}
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
    }	
	
	public function batalEditClicked()
    {
		$this->btnBatalTambah->Display = 'None';
		$this->btnTambah->Text = 'Tambah';
		$this->clearTmp();
    }	
	
	public function deleteRow($sender,$param)
    {
        $ID=$sender->CommandParameter;
		// deletes the user record with the specified username primary key				
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "DELETE FROM $nmTable WHERE id='$ID'";
		//$sql = "UPDATE $nmTable SET st_delete='1' WHERE id='$ID'";
		$this->queryAction($sql,'C');		
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
    }	
	
	public function clearTmp()
    {
		//$this->DDKelas->SelectedValue = 'empty';
		$this->nmTarif->Text = '';
		$this->tarifOperator->Text = '';
		$this->Page->CallbackClient->focus($this->DDKelas);
		$this->clearViewState('idEdit');	
    }	
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(KamarOperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$nama = strtolower(trim($this->nama->Text));
			$tarif = floatval($this->tarif->Text);
			$kateg = $this->DDKamar->SelectedValue;
			$stRawat = $this->stRawat->SelectedValue;
			
			$sql = "SELECT * FROM tbm_fisio_tindakan_paket_asuransi WHERE LOWER(nama) = '$nama' AND id_asuransi = '$kateg' AND st_rawat = '$stRawat'";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Nama Paket untuk Asuransi dan Status Perawatan yang dipilih sudah ada dalam database !</p>\',timeout: 3000,dialog:{
						modal: true
					}});');	
						
				$this->Page->CallbackClient->focus($this->DDKamar);	
			}
			else
			{
				
				if($this->getViewState('nmTable'))
				{
					$nmTable = $this->getViewState('nmTable');
					
					//INSERT tbm_operasi_kategori
					$data = new FisioTdkPaketAsuransiRecord();
					$data->nama = trim($this->nama->Text);	
					$data->id_asuransi = $kateg;	
					$data->tarif = $tarif;	
					$data->st_rawat = $stRawat;	
					$data->save();		
					
					$sql = "SELECT id FROM tbm_fisio_tindakan_paket_asuransi ORDER BY id DESC";
					$id_paket = FisioTdkPaketAsuransiRecord::finder()->findBySql($sql)->id;
					
					$sql = "SELECT * FROM $nmTable ";
					foreach($this->queryAction($sql,'S') as $row)
					{
						//INSERT tbm_operasi_kamar_tarif
						$dataTarif = new FisioTdkPaketAsuransiDetailRecord;
						$dataTarif->id_tindakan = $row['kelas'];
						$dataTarif->id_paket = $id_paket;
						$dataTarif->id_asuransi = $kateg;	
						$dataTarif->tarif = 0;	
						$dataTarif->st_rawat = $stRawat;	
						$dataTarif->save();	
					}
					
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
					$this->gridTarif->DataSource='';//Insert new row in tabel bro...
					$this->gridTarif->dataBind();	
					$this->clearViewState('nmTable');	
					
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Paket <b>'.ucwords($this->nama->Text).'</b> telah ditambahkan dalam database. <br/><br/> Apakah akan menambah Paket baru lagi ?</p>\',timeout: 600000,dialog:{
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
					 	
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Belum ada data tindakan yang akan ditambah dalam Paket ini !</p>\',timeout: 3000,dialog:{
							modal: true
						}});');			
				}
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
			//$this->Page->CallbackClient->focus($this->DDKamar);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->Response->Reload();	
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioPaketAsuransi'));	
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
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioPaketAsuransi'));		
	}
}
?>