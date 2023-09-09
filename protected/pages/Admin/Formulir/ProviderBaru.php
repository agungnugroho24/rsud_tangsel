<?php
class ProviderBaru extends SimakConf
{
	public function onInit($param)
	{		
	   parent::onInit($param);
	   //$this->prosesPageAllow();
	}
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{				
			$id = $this->Request['id'];
			$stDetail = $this->Request['stDetail'];
			$this->setViewState('stDetail',$stDetail);
			
			//$this->plafon_master->Text = floatval(PerusahaanRecord::finder()->findByPk($id)->plafon_master);
			
			$sql = "SELECT * FROM tbm_perusahaan_asuransi ORDER BY nama ";
			$this->DDPerus->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDPerus->dataBind();
			$this->DDPerus->SelectedValue = $id;
			$this->DDPerus->Enabled = false;
			
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoli->dataBind();
			
			$this->DDPoli2->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoli2->dataBind();
			
			$this->DDPoliRetribusi->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoliRetribusi->dataBind();
			
			$this->DDTindakan->Enabled = false;			
			$this->DDDokter->Enabled = false;
			
			$sql = "SELECT * FROM tbm_lab_kategori ORDER BY jenis";
			$this->kategori->DataSource = LabKategRecord::finder()->findAllBySql($sql);
			$this->kategori->dataBind();
			
			
			//Radiologi DropDown
			$dataRad[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_rad_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataRad[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDRad->DataSource = $dataRad;
			$this->DDRad->dataBind();
			if($dataRad)
				$this->setViewState('dataRad',$dataRad);
			
			//Fisio DropDown		
			$dataFisio[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_fisio_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataFisio[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDFisio->DataSource = $dataFisio;
			$this->DDFisio->dataBind();
			if($dataFisio)
				$this->setViewState('dataFisio',$dataFisio);
			
			/*
			//Audiometri DropDown		
			$dataAudiometri[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_audiometri_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataAudiometri[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDAudiometri->DataSource = $dataAudiometri;
			$this->DDAudiometri->dataBind();
			if($dataAudiometri)
				$this->setViewState('dataAudiometri',$dataAudiometri);
			
			//Spirometri DropDown		
			$dataSpirometri[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_spirometri_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataSpirometri[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDSpirometri->DataSource = $dataSpirometri;
			$this->DDSpirometri->dataBind();
			if($dataSpirometri)
				$this->setViewState('dataSpirometri',$dataSpirometri);
				
			//PenunjangJantung DropDown		
			$dataPenunjangJantung[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_penunjangjantung_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataPenunjangJantung[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDPenunjangJantung->DataSource = $dataPenunjangJantung;
			$this->DDPenunjangJantung->dataBind();
			if($dataPenunjangJantung)
				$this->setViewState('dataPenunjangJantung',$dataPenunjangJantung);
			
			//Refraksi DropDown		
			$dataRefraksi[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			$sql = "SELECT * FROM tbm_refraksi_tindakan ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataRefraksi[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDRefraksi->DataSource = $dataRefraksi;
			$this->DDRefraksi->dataBind();
			if($dataRefraksi)
				$this->setViewState('dataRefraksi',$dataRefraksi);
			*/
			
			if($stDetail == '1') //MODE EDIT
			{
				/*$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$id' ";
				$this->plafonObat->Text = ProviderDetailObatRecord::finder()->findBySql($sql)->plafon;
				$this->marginObat->Text = ProviderDetailObatRecord::finder()->findBySql($sql)->margin;
				*/
				
				$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_poli = $row['id_poli'];
					$plafon = $row['plafon'];
					$margin = $row['margin'];	
					$disc = $row['disc'];	
					
					$this->makeTmpTblObat($id_poli,$plafon,$margin,$disc);
				}	
				
				$sql = "SELECT * FROM tbm_provider_detail_retribusi WHERE id_provider = '$id'"; //AND (id_poli = '01')";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_poli = $row['id_poli'];
					$tarif = $row['tarif'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblRetribusi($id_provider,$id_poli,$tarif,$tarifGsm,$stTarif);
				}	
				
				$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = '$id'"; //AND (id_poli = '01')";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_poli = $row['id_poli'];
					$id_tindakan = $row['id_tindakan'];
					$id_dokter = $row['dokter'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}	
				
				$sql = "SELECT * FROM tbm_provider_detail_lab WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarifGsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				$sql = "SELECT * FROM tbm_provider_detail_rad WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				$sql = "SELECT * FROM tbm_provider_detail_fisio WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				/*
				$sql = "SELECT * FROM tbm_provider_detail_audiometri WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				$sql = "SELECT * FROM tbm_provider_detail_spirometri WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				$sql = "SELECT * FROM tbm_provider_detail_penunjangjantung WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				
				$sql = "SELECT * FROM tbm_provider_detail_refraksi WHERE id_provider = '$id' ";
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$id_provider = $row['id_provider'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['tarif'];
					$diskon = $row['diskon'];
					$tarifGsm = $row['tarif_gsm'];
					$stTarif = $row['st_tarif'];
					
					$this->makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
				}
				*/
				
			}
		}
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			if($this->getViewState('cariTdk'))//Jika pencarian aktif
			{
				$idKlinik = $this->DDPoli->SelectedValue;
				$idDokter = $this->DDDokter->SelectedValue;
				$namaTdk = $this->getViewState('namaTdk');
				
				$sql = "SELECT a.* FROM $nmTable a, tbm_nama_tindakan b WHERE a.id_tindakan = b.id ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($idKlinik)
					$sql .= " AND a.id_poli = '$idKlinik' ";
				if($idDokter)	
					$sql .= " AND a.id_dokter = '$idDokter'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{	
				$sql="SELECT a.* FROM $nmTable a, tbm_poliklinik b WHERE a.id_poli=b.id ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGrid->DataSource=$arrData;
			$this->UserGrid->dataBind();	
			
			$this->jmlTdk->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
				//$this->clearViewState('nmTable');
				$this->clearViewState('tmpJml');
			}
		}
		
		if($this->getViewState('nmTableRetribusi'))
		{
			$nmTableRetribusi = $this->getViewState('nmTableRetribusi');
			$sql="SELECT * FROM $nmTableRetribusi ORDER BY id DESC";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGridRetribusi->DataSource=$arrData;
			$this->UserGridRetribusi->dataBind();	
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableRetribusi'),'D');//Droped the table	
				//$this->clearViewState('nmTableRetribusi');
				$this->clearViewState('tmpJmlRetribusi');
			}
		}
		
		if($this->getViewState('nmTableObat'))
		{
			$nmTableObat = $this->getViewState('nmTableObat');
			$sql="SELECT * FROM $nmTableObat ORDER BY id DESC";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGridObat->DataSource=$arrData;
			$this->UserGridObat->dataBind();	
			
			$this->jmlObat->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableObat'),'D');//Droped the table	
				//$this->clearViewState('nmTableObat');
				$this->clearViewState('tmpJmlObat');
			}
		}
		
		if($this->getViewState('nmTableLab'))
		{
			$nmTableLab = $this->getViewState('nmTableLab');
			if($this->getViewState('cariLab'))//Jika pencarian aktif
			{
				$idKateg = $this->kategori->SelectedValue;
				$id = $this->DDLab->SelectedValue;
				$namaTdk = $this->getViewState('namaLab');
				$sql = "SELECT a.* FROM $nmTableLab a, tbm_lab_tindakan b, tbm_lab_kategori c WHERE a.id_tindakan = b.kode AND b.kategori = c.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($idKateg)
					$sql .= " AND b.kategori = '$idKateg' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableLab a, tbm_lab_kategori b, tbm_lab_tindakan c WHERE a.id_tindakan=c.kode AND c.kategori = b.kode ORDER BY b.jenis";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->UserGridLab->DataSource=$arrData;
			$this->UserGridLab->dataBind();
			
			$this->jmlLab->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableLab'),'D');//Droped the table	
				//$this->clearViewState('nmTableLab');
				$this->clearViewState('tmpJmlLab');
			}
		}
		
		if($this->getViewState('nmTableRad'))
		{
			$nmTableRad = $this->getViewState('nmTableRad');
			if($this->getViewState('cariRad'))//Jika pencarian aktif
			{
				$id = $this->DDRad->SelectedValue;
				$namaTdk = $this->getViewState('namaRad');
				$sql = "SELECT a.* FROM $nmTableRad a, tbm_rad_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableRad a, tbm_lab_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$this->jmlRad->Text = 'Jumlah Data : '.count($arrData);
			
			$this->UserGridRad->DataSource=$arrData;
			$this->UserGridRad->dataBind();	
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableRad'),'D');//Droped the table	
				//$this->clearViewState('nmTableRad');
				$this->clearViewState('tmpJmlRad');
			}
		}
		
		if($this->getViewState('nmTableFisio'))
		{
			$nmTableFisio = $this->getViewState('nmTableFisio');
			if($this->getViewState('cariFisio'))//Jika pencarian aktif
			{
				$id = $this->DDFisio->SelectedValue;
				$namaTdk = $this->getViewState('namaFisio');
				$sql = "SELECT a.* FROM $nmTableFisio a, tbm_fisio_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableFisio a, tbm_fisio_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$this->jmlFisio->Text = 'Jumlah Data : '.count($arrData);
			
			$this->UserGridFisio->DataSource=$arrData;
			$this->UserGridFisio->dataBind();	
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableFisio'),'D');//Droped the table	
				//$this->clearViewState('nmTableFisio');
				$this->clearViewState('tmpJmlFisio');
			}
		}
		
		if($this->getViewState('nmTableAudiometri'))
		{
			$nmTableAudiometri = $this->getViewState('nmTableAudiometri');
			if($this->getViewState('cariAudiometri'))//Jika pencarian aktif
			{
				$id = $this->DDAudiometri->SelectedValue;
				$namaTdk = $this->getViewState('namaAudiometri');
				$sql = "SELECT a.* FROM $nmTableAudiometri a, tbm_audiometri_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableAudiometri a, tbm_audiometri_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$this->UserGridAudiometri->DataSource=$arrData;
			$this->UserGridAudiometri->dataBind();	
			
			$this->jmlAudiometri->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableAudiometri'),'D');//Droped the table	
				//$this->clearViewState('nmTableAudiometri');
				$this->clearViewState('tmpJmlAudiometri');
			}
		}
		
		if($this->getViewState('nmTableSpirometri'))
		{
			$nmTableSpirometri = $this->getViewState('nmTableSpirometri');
			if($this->getViewState('cariSpirometri'))//Jika pencarian aktif
			{
				$id = $this->DDSpirometri->SelectedValue;
				$namaTdk = $this->getViewState('namaSpirometri');
				$sql = "SELECT a.* FROM $nmTableSpirometri a, tbm_spirometri_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableSpirometri a, tbm_spirometri_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGridSpirometri->DataSource=$arrData;
			$this->UserGridSpirometri->dataBind();	
			
			$this->jmlSpirometri->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableSpirometri'),'D');//Droped the table	
				//$this->clearViewState('nmTableSpirometri');
				$this->clearViewState('tmpJmlSpirometri');
			}
		}
		
		if($this->getViewState('nmTablePenunjangJantung'))
		{
			$nmTablePenunjangJantung = $this->getViewState('nmTablePenunjangJantung');
			if($this->getViewState('cariPenunjangJantung'))//Jika pencarian aktif
			{
				$id = $this->DDPenunjangJantung->SelectedValue;
				$namaTdk = $this->getViewState('namaPenunjangJantung');
				$sql = "SELECT a.* FROM $nmTablePenunjangJantung a, tbm_penunjangjantung_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTablePenunjangJantung a, tbm_penunjangjantung_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGridPenunjangJantung->DataSource=$arrData;
			$this->UserGridPenunjangJantung->dataBind();	
			
			$this->jmlJantung->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTablePenunjangJantung'),'D');//Droped the table	
				//$this->clearViewState('nmTablePenunjangJantung');
				$this->clearViewState('tmpJmlPenunjangJantung');
			}
		}
		
		if($this->getViewState('nmTableRefraksi'))
		{
			$nmTableRefraksi = $this->getViewState('nmTableRefraksi');
			if($this->getViewState('cariSpirometri'))//Jika pencarian aktif
			{
				$id = $this->DDRefraksi->SelectedValue;
				$namaTdk = $this->getViewState('namaRefraksi');
				$sql = "SELECT a.* FROM $nmTableRefraksi a, tbm_refraksi_tindakan b WHERE a.id_tindakan = b.kode ";
				
				if($namaTdk)
					$sql .= " AND b.nama LIKE '$namaTdk%' ";
				if($id)	
					$sql .= " AND a.id_tindakan = '$id'";
				
				$sql .= " GROUP BY a.id ORDER BY b.nama ";
			}else{
				$sql="SELECT a.* FROM $nmTableRefraksi a, tbm_refraksi_tindakan b WHERE a.id_tindakan=b.kode ORDER BY b.nama";
			}
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		
			$this->UserGridRefraksi->DataSource=$arrData;
			$this->UserGridRefraksi->dataBind();	
			
			$this->jmlRefraksi->Text = 'Jumlah Data : '.count($arrData);
			
			if(count($arrData)==0)
			{
				//$this->queryAction($this->getViewState('nmTableRefraksi'),'D');//Droped the table	
				//$this->clearViewState('nmTableRefraksi');
				$this->clearViewState('tmpJmlRefraksi');
			}
		}
	}
		
	public function checkRM($sender,$param)
	{
			// valid if the username is not found in the database
			$param->IsValid=(OperasiNamaRecord::finder()->findByPk($this->ID->Text)===null);		
	}	
	
	public function modeTarifTindakanChanged()
	{
		
		if($this->modeTarifTindakan->SelectedValue == '1')
			$this->tarifTxtTindakan->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtTindakan->Text = 'Tarif Plafond';
	}
	
	public function modeTarifLabChanged()
	{
		
		if($this->modeTarifLab->SelectedValue == '1')
			$this->tarifTxtLab->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtLab->Text = 'Tarif Plafond';
	}
	
	public function modeTarifRadChanged()
	{
		
		if($this->modeTarifRad->SelectedValue == '1')
			$this->tarifTxtRad->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtRad->Text = 'Tarif Plafond';
	}
	
	public function modeTarifFisioChanged()
	{
		
		if($this->modeTarifFisio->SelectedValue == '1')
			$this->tarifTxtFisio->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtFisio->Text = 'Tarif Plafond';
	}
	
	public function modeTarifAudiometriChanged()
	{
		
		if($this->modeTarifAudiometri->SelectedValue == '1')
			$this->tarifTxtAudiometri->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtAudiometri->Text = 'Tarif Plafond';
	}
	
	public function modeTarifSpirometriChanged()
	{
		
		if($this->modeTarifSpirometri->SelectedValue == '1')
			$this->tarifTxtSpirometri->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtSpirometri->Text = 'Tarif Plafond';
	}
	
	public function modeTarifPenunjangJantungChanged()
	{
		
		if($this->modeTarifPenunjangJantung->SelectedValue == '1')
			$this->tarifTxtPenunjangJantung->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtPenunjangJantung->Text = 'Tarif Plafond';
	}
	
	
	public function modeTarifRefraksiChanged()
	{
		
		if($this->modeTarifRefraksi->SelectedValue == '1')
			$this->tarifTxtRefraksi->Text = 'Tarif Kerjasama';
		else
			$this->tarifTxtRefraksi->Text = 'Tarif Plafond';
	}
	
	
//--------------------------------------- Obat ------------------------------------------
	public function tambahObatClicked()
	{
		if($this->Page->IsValid)
		{
			$plafon = floatval($this->plafonObat->Text);
			$margin = floatval($this->marginObat->Text);
			$disc = floatval($this->discObat->Text);
			
			if($this->DDPoli2->SelectedValue == '')
			{
				$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
				foreach($this->queryAction($sql,'S') as $row)
				{
					$id_poli = $row['id'];			
					$this->makeTmpTblObat($id_poli,$plafon,$margin,$disc);		
				}
			}
			else
			{
				$id_poli = $this->DDPoli2->SelectedValue;			
				$this->makeTmpTblObat($id_poli,$plafon,$margin,$disc);	
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function kategoriLabChanged()
	{
		if($this->kategori->SelectedValue !='')
		{
			$kategori = $this->kategori->SelectedValue;
			$sql = "SELECT 
					  tbm_lab_tindakan.kode AS kode,
					  tbm_lab_tindakan.nama AS nama
					FROM
					  tbm_lab_tindakan
					  LEFT JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
					WHERE
						tbm_lab_tindakan.st_paket > 0
						AND tbm_lab_tarif.tarif > 0
						AND tbm_lab_tindakan.kategori = '$kategori' ";
			
			$dataLab[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
					
			//$sql = "SELECT * FROM tbm_lab_tindakan WHERE st_paket > 0 ORDER BY nama ";
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataLab[]=array('kode'=>$row['kode'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDLab->DataSource = $dataLab;
			$this->DDLab->dataBind();
			$this->Page->CallbackClient->focus($this->DDLab);	
			$this->DDLab->Enabled = true;
			if($dataLab)
				$this->setViewState('dataLab',$dataLab);
		}
		else
		{
			$this->DDLab->SelectedValue = 'empty';
			$this->DDLab->Enabled = false;
			$this->Page->CallbackClient->focus($this->kategori);			
		}
	}
	
	//Auto cAll DropDown DDTindakan
	public function DDChangedTindakan($sender,$param)
	{
		
		if  ($this->DDTindakan->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDTindakan);
			$this->diskonOperator->Text = 0;
			$this->tarifOperator->Text = 0;
		}else
		{
			$PkTindakan = $this->DDTindakan->SelectedValue;
			$this->tarifOperatorView->Text = floatval(TarifTindakanRecord::finder()->findByPK($PkTindakan)->biaya1);
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperator);
			$this->diskonOperator->Text = 0;
			$this->tarifOperator->Text = 0;
			$this->Page->CallbackClient->focus($this->DDDokter);
		}
		
	}
	
	//Hitung Diskon Tindakan
	public function hitungDiskonTindakan()
	{
		
		if($this->diskonOperator->Text > 0)
		{
			$this->tarifOperator->Text=$this->tarifOperatorView->Text - ($this->tarifOperatorView->Text * $this->diskonOperator->Text/100);
		}else{
			$this->tarifOperator->Text = $this->tarifOperator->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtn);
		
	}
	
	//Auto cAll DropDown Lab
	public function DDChangedLab($sender,$param)
	{
		
		if  ($this->DDLab->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDLab);
			$this->diskonOperatorLab->Text = 0;
			$this->tarifOperatorLab->Text = 0;
		}else
		{
			$PkLab = $this->DDLab->SelectedValue;
			$this->tarifOperatorLabView->Text=LabTarifRecord::finder()->findByPK($PkLab)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorLab);
			$this->diskonOperatorLab->Text = 0;
			$this->tarifOperatorLab->Text = 0;
		}
	}
	
	//Hitung Diskon Lab
	public function hitungDiskonLab()
	{
		if($this->diskonOperatorLab->Text > 0)
		{
			$this->tarifOperatorLab->Text=$this->tarifOperatorLabView->Text - ($this->tarifOperatorLabView->Text * $this->diskonOperatorLab->Text/100);
		}else{
			$this->tarifOperatorLab->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnLab);
	}	
	
	//Auto cAll DropDown Rad
	public function DDChangedRad($sender,$param)
	{
		
		if  ($this->DDRad->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDRad);
			$this->diskonOperatorRad->Text = 0;
			$this->tarifOperatorRad->Text = 0;
		}else
		{
			$PkRad = $this->DDRad->SelectedValue;
			$this->tarifOperatorRadView->Text=RadTarifRecord::finder()->findByPK($PkRad)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorRad);
			$this->diskonOperatorRad->Text = 0;
			$this->tarifOperatorRad->Text = 0;
		}
	}
	
	//Hitung Diskon Rad
	public function hitungDiskonRad()
	{
		if($this->diskonOperatorRad->Text > 0)
		{
			$this->tarifOperatorRad->Text=$this->tarifOperatorRadView->Text - ($this->tarifOperatorRadView->Text * $this->diskonOperatorRad->Text/100);
		}else{
			$this->tarifOperatorRad->Text = $this->tarifOperatorRad->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnRad);
		
	}
	
	//Auto cAll DropDown Fisio
	public function DDChangedFisio($sender,$param)
	{
		
		if  ($this->DDFisio->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDFisio);
			$this->diskonOperatorFisio->Text = 0;
			$this->tarifOperatorFisio->Text = 0;
		}else
		{
			$PkFisio = $this->DDFisio->SelectedValue;
			$this->tarifOperatorFisioView->Text=FisioTarifRecord::finder()->findByPK($PkFisio)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorFisio);
			$this->diskonOperatorFisio->Text = 0;
			$this->tarifOperatorFisio->Text = 0;
		}
		
	}
	
	//Hitung Diskon Fisio
	public function hitungDiskonFisio()
	{
		if($this->diskonOperatorFisio->Text > 0)
		{
			$this->tarifOperatorFisio->Text=$this->tarifOperatorFisioView->Text - ($this->tarifOperatorFisioView->Text * $this->diskonOperatorFisio->Text/100);
		}else{
			$this->tarifOperatorFisio->Text = $this->tarifOperatorFisio->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnFisio);
		
	}
	
	//Auto cAll DropDown Audiometri
	public function DDChangedAudiometri($sender,$param)
	{
		if  ($this->DDAudiometri->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDAudiometri);
			$this->diskonOperatorAudiometri->Text = 0;
			$this->tarifOperatorAudiometri->Text = 0;
		}else
		{
			$PkAudiometri = $this->DDAudiometri->SelectedValue;
			$this->tarifOperatorAudiometriView->Text=AudiometriTarifRecord::finder()->findByPK($PkAudiometri)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorAudiometri);
			$this->diskonOperatorAudiometri->Text = 0;
			$this->tarifOperatorAudiometri->Text = 0;
		}
		
	}
	
	//Hitung Diskon Audiometri
	public function hitungDiskonAudiometri()
	{
		
		if($this->diskonOperatorAudiometri->Text > 0)
		{
			$this->tarifOperatorAudiometri->Text=$this->tarifOperatorAudiometriView->Text - ($this->tarifOperatorAudiometriView->Text * $this->diskonOperatorAudiometri->Text/100);
		}else{
			$this->tarifOperatorAudiometri->Text = $this->tarifOperatorAudiometri->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnAudiometri);
		
	}
	
	
	//Auto cAll DropDown Spirometri
	public function DDChangedSpirometri($sender,$param)
	{ 
		
		if  ($this->DDSpirometri->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDSpirometri);
			$this->diskonOperatorSpirometri->Text = 0;
			$this->tarifOperatorSpirometri->Text = 0;
		}else
		{
			$PkSpirometri = $this->DDSpirometri->SelectedValue;
			$this->tarifOperatorSpirometriView->Text=SpirometriTarifRecord::finder()->findByPK($PkSpirometri)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorSpirometri);
			$this->diskonOperatorSpirometri->Text = 0;
			$this->tarifOperatorSpirometri->Text = 0;
		}
		
	}
	
	//Hitung Diskon Spirometri
	public function hitungDiskonSpirometri()
	{
		
		if($this->diskonOperatorSpirometri->Text > 0)
		{
			$this->tarifOperatorSpirometri->Text=$this->tarifOperatorSpirometriView->Text - ($this->tarifOperatorSpirometriView->Text * $this->diskonOperatorSpirometri->Text/100);
		}else{
			$this->tarifOperatorSpirometri->Text = $this->tarifOperatorSpirometri->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnSpirometri);
		
	}
	
	//Auto cAll DropDown PenunjangJantung
	public function DDChangedPenunjangJantung($sender,$param)
	{
		
		if  ($this->DDPenunjangJantung->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDPenunjangJantung);
			$this->diskonOperatorPenunjangJantung->Text = 0;
			$this->tarifOperatorPenunjangJantung->Text = 0;
		}else
		{
			$PkPenunjangJantung = $this->DDPenunjangJantung->SelectedValue;
			$this->tarifOperatorPenunjangJantungView->Text=PenunjangJantungTarifRecord::finder()->findByPK($PkPenunjangJantung)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorPenunjangJantung);
			$this->diskonOperatorPenunjangJantung->Text = 0;
			$this->tarifOperatorPenunjangJantung->Text = 0;
		}
		
	}
	
	//Hitung Diskon PenunjangJantung
	public function hitungDiskonPenunjangJantung()
	{
		
		if($this->diskonOperatorPenunjangJantung->Text > 0)
		{
			$this->tarifOperatorPenunjangJantung->Text=$this->tarifOperatorPenunjangJantungView->Text - ($this->tarifOperatorPenunjangJantungView->Text * $this->diskonOperatorPenunjangJantung->Text/100);
		}else{
			$this->tarifOperatorPenunjangJantung->Text = $this->tarifOperatorPenunjangJantung->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnPenunjangJantung);
		
	}
	
	
	//Auto cAll DropDown Refraksi
	public function DDChangedRefraksi($sender,$param)
	{
		
		if  ($this->DDRefraksi->SelectedValue=='')
		{
			$this->Page->CallbackClient->focus($this->DDRefraksi);
			$this->diskonOperatorRefraksi->Text = 0;
			$this->tarifOperatorRefraksi->Text = 0;
		}else
		{
			$PkRefraksi = $this->DDRefraksi->SelectedValue;
			$this->tarifOperatorRefraksiView->Text=RefraksiTarifRecord::finder()->findByPK($PkRefraksi)->tarif;
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->diskonOperatorRefraksi);
			$this->diskonOperatorRefraksi->Text = 0;
			$this->tarifOperatorRefraksi->Text = 0;
		}
		
	}
	
	//Hitung Diskon Refraksi
	public function hitungDiskonRefraksi()
	{
		
		if($this->diskonOperatorRefraksi->Text > 0)
		{
			$this->tarifOperatorRefraksi->Text=$this->tarifOperatorRefraksiView->Text - ($this->tarifOperatorRefraksiView->Text * $this->diskonOperatorRefraksi->Text/100);
		}else{
			$this->tarifOperatorRefraksi->Text = $this->tarifOperatorRefraksi->Text;
		}
		$this->Page->CallbackClient->focus($this->tambahBtnRefraksi);
		
	}
	
	public function makeTmpTblObat($id_poli,$plafon,$margin,$disc)
	{			
		if (!$this->getViewState('nmTableObat'))
		{
			$nmTableObat = $this->setNameTable('nmTableObat');
			$sql="CREATE TABLE $nmTableObat (id INT (2) auto_increment, 
										 id_poli CHAR(2) DEFAULT NULL,
										 plafon FLOAT DEFAULT '0',
										 margin FLOAT DEFAULT '0',			
										 disc FLOAT DEFAULT '0',			
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableObat = $this->getViewState('nmTableObat');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_obat WHERE id_provider = 'id_provider' AND id_poli = '$id_poli' ";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Plafon Obat untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableObat WHERE id_poli = '$id_poli' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableObat (id_poli,plafon,margin,disc) VALUES ('$id_poli','$plafon','$margin','$disc')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Plafon Obat untuk Asuransi Perusaahan sudah ada sebelumnya.</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableObat (id_poli,plafon,margin,disc) VALUES ('$id_poli','$plafon','$margin','$disc')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDPoli2->SelectedValue = 'empty';
		$this->plafonObat->Text = '0';
		$this->marginObat->Text = '0';
		$this->discObat->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPoli2);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedObat($sender,$param)
    {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->poli2Coloumn->Text = PoliklinikRecord::finder()->findByPk($item->DataItem['id_poli'])->nama;
			$item->plafonColoumn->Text = number_format($item->DataItem['plafon'],'2',',','.');
			$item->marginColoumn->Text = $item->DataItem['margin'].'%';
			$item->discColoumn->Text = $item->DataItem['disc'].'%';
		}	
	}
	
	public function deleteClickedObat($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridObat->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableObat = $this->getViewState('nmTableObat');
		
		$sql = "DELETE FROM $nmTableObat WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	protected function PageIndexChangedObat($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridObat->CurrentPageIndex = $param->NewPageIndex;
	}

//--------------------------------------- Retribusi ------------------------------------------
	public function DDPoliChangedRetribusi($sender,$param)
	{
		$idKlinik = $this->DDPoliRetribusi->SelectedValue;
	 
		if($idKlinik=='')
		{
			$this->Page->CallbackClient->focus($this->DDPoliRetribusi);
			$this->tarifOperatorRetribusi->Text = 0;
		}
		else
		{
			$this->tarifOperatorViewRetribusi->Text = floatval(TarifPendaftaranRecord::finder()->find('id_klinik=?',$idKlinik)->tarif);
			//$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->tarifOperatorRetribusi);
			$this->tarifOperatorRetribusi->Text = 0;
		}
	}	   
	
	public function makeTmpTblRetribusi($id_provider,$id_poli,$tarif,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableRetribusi'))
		{
			$nmTableRetribusi = $this->setNameTable('nmTableRetribusi');
			$sql="CREATE TABLE $nmTableRetribusi (id INT (2) auto_increment, 
										 id_poli CHAR(2) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableRetribusi = $this->getViewState('nmTableRetribusi');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_retribusi WHERE id_provider = 'id_provider' AND id_poli = '$id_poli' ";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Retribusi untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableRetribusi WHERE id_poli = '$id_poli'";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableRetribusi (id_poli,tarif,tarif_gsm,st_tarif) VALUES ('$id_poli','$tarif','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Retribusi untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableRetribusi (id_poli,tarif,tarif_gsm,st_tarif) VALUES ('$id_poli','$tarif','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDPoliRetribusi->SelectedValue = 'empty';
		$this->tarifOperatorRetribusi->Text = '0';
		$this->tarifOperatorViewRetribusi->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPoliRetribusi);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function tambahClickedRetribusi()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_poli = $this->DDPoliRetribusi->SelectedValue;
			$tarif = floatval($this->tarifOperatorRetribusi->Text);
			$tarifGsm=TarifPendaftaranRecord::finder()->find('id_klinik=?',$id_poli)->tarif;
			
			if($this->getViewState('nmTableRetribusi'))
			{
				$tmpTable = $this->getViewState('nmTableRetribusi');
				$sql = "SELECT id_poli FROM $tmpTable WHERE id_poli = '$id_poli'";
				
				if(!ProviderDetailRetribusiRecord::finder()->findBySql($sql))
					$this->makeTmpTblRetribusi($id_provider,$id_poli,$tarif,$tarifGsm,$stTarif);
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Retribusi Poli yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');
				}
			}else{
				$this->makeTmpTblRetribusi($id_provider,$id_poli,$tarif,$tarifGsm,$stTarif);
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedRetribusi($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$item->poliColoumnRetribusi->Text = PoliklinikRecord::finder()->findByPk($item->DataItem['id_poli'])->nama;
			$item->tarifGsmColoumnRetribusi->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
			$item->tarifColoumnRetribusi->Text = number_format($item->DataItem['tarif'],'2',',','.');	
		}	
	}
	
	public function deleteClickedRetribusi($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridRetribusi->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableRetribusi = $this->getViewState('nmTableRetribusi');
		
		$sql = "DELETE FROM $nmTableRetribusi WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	protected function PageIndexChangedRetribusi($sender,$param)
	{
		// clear data in session because we need to get a new page from db
		$session = $this->getSession();
		$session->remove("SomeData");
		$this->UserGridRetribusi->CurrentPageIndex = $param->NewPageIndex;
	}
		
//--------------------------------------- Tindakan Poli ------------------------------------------
	public function DDPoliChanged($sender,$param)
	{
	   $idKlinik = $this->DDPoli->SelectedValue;
	   if($idKlinik != '')
	   {
	   		$sql = "SELECT id, nama FROM tbm_nama_tindakan WHERE id_klinik = '$idKlinik' ORDER BY nama ";
			$dataTindakan[]=array('kode'=>'xx','nama'=>'SEMUA PEMERIKSAAN');
			$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
			
			if(count($arrData) > 0)
			{	
				foreach($arrData as $row)
				{
					$dataTindakan[]=array('kode'=>$row['id'],'nama'=>$row['nama']);
				}
			}
			
			$this->DDTindakan->DataSource = $dataTindakan;
			$this->DDTindakan->dataBind();
			$this->DDTindakan->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDTindakan);
			if($dataTindakan)
				$this->setViewState('dataTindakan',$dataTindakan);
			
			$this->DDDokter->Enabled = true;
			
			
			/*if($idKlinik == '07'){
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbm_poliklinik ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
					WHERE
					  tbd_pegawai.kelompok = 1 AND
					  '$idKlinik' IN (('".implode("','",explode(',',tbd_pegawai.poliklinik)).'\'))';
			}
			else
			{
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai	
					  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1 AND 
					  tbm_poliklinik.id = '$idKlinik'";
			}*/
			
			//$arr = $this->queryAction($sql,'S');	
			//$this->DDDokter->DataSource = $arr;
			$this->DDDokter->DataSource = $this->dokterList($idKlinik);
			$this->DDDokter->dataBind();
	   }
	   else
	   {
	   		$this->DDTindakan->SelectedValue = 'empty';
	   		$this->DDTindakan->Enabled = false;
			
			$this->DDDokter->SelectedValue = 'empty';
			$this->DDDokter->Enabled = false;
	   }	
    }	   
	
	public function cariNamaTdk()
	{
		if($this->cariNama->Text != '')
		{
			$this->setViewState('cariTdk','1');
			$this->setViewState('namaTdk',$this->cariNama->Text);
		}else
		{
			$this->clearViewState('cariTdk');
			$this->clearViewState('namaTdk');
		}
	}
	
	public function makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 id_poli CHAR(2) DEFAULT NULL,
										 id_tindakan CHAR(4) DEFAULT NULL,
										 id_dokter CHAR(4) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_poli = '$id_poli' AND id_tindakan = '$id_tindakan' AND dokter = '$id_dokter'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTable WHERE id_poli = '$id_poli' AND id_tindakan = '$id_tindakan' AND id_dokter = '$id_dokter'";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTable (id_poli,id_tindakan,id_dokter,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_poli','$id_tindakan','$id_dokter','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTable (id_poli,id_tindakan,id_dokter,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_poli','$id_tindakan','$id_dokter','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDPoli->SelectedValue = 'empty';
		$this->DDTindakan->SelectedValue = 'empty';
		$this->DDDokter->SelectedValue = 'empty';
		$this->tarifOperator->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPoli);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function tambahClicked()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_dokter = $this->DDDokter->SelectedValue;
			$id_poli = $this->DDPoli->SelectedValue;
			$id_tindakan = $this->DDTindakan->SelectedValue;
			$tarif = floatval($this->tarifOperator->Text);
			$diskon = floatval($this->diskonOperator->Text);
			$stTarif = $this->modeTarifTindakan->SelectedValue;
			
			//$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataTindakanTmp = $this->getViewState('dataTindakan');
				unset($dataTindakanTmp[0]);
				foreach($dataTindakanTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=TarifTindakanRecord::finder()->findByPK($id_tindakan)->biaya1;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTable'))
					{
						$tmpTable = $this->getViewState('nmTable');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailTindakanRecord::finder()->findBySql($sql))
							$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
					}
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					$tarifGsm=TarifTindakanRecord::finder()->findByPK($id_tindakan)->biaya1;
					if($this->getViewState('nmTable'))
					{
						$tmpTable = $this->getViewState('nmTable');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailTindakanRecord::finder()->findBySql($sql))
							$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTbl($id_provider,$id_dokter,$id_poli,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);
					}
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreated($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_nama_tindakan WHERE id = '$idTdk' ";
			$item->tindakanColoumn->Text = NamaTindakanRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			$item->poliColoumn->Text = PoliklinikRecord::finder()->findByPk($item->DataItem['id_poli'])->nama;
			$item->dokterColoumn->Text = PegawaiRecord::finder()->findByPk($item->DataItem['id_dokter'])->nama;
			$item->diskonColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.') . '%';
			$item->tarifGsmColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
		}	
	}
	
	public function deleteClicked($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "DELETE FROM $nmTable WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	protected function PageIndexChanged($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGrid->CurrentPageIndex = $param->NewPageIndex;
	}
	
	
//--------------------------------------- Lab ------------------------------------------
	public function cariNamaLab()
	{
		if($this->cariNamaLab->Text != '')
		{
			$this->setViewState('cariLab','1');
			$this->setViewState('namaLab',$this->cariNamaLab->Text);
		}else
		{
			$this->clearViewState('cariLab');
			$this->clearViewState('namaLab');
		}
	}
	
	public function makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableLab'))
		{
			$nmTableLab = $this->setNameTable('nmTableLab');
			$sql="CREATE TABLE $nmTableLab (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarifGsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableLab = $this->getViewState('nmTableLab');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Lab untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableLab WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableLab (id_tindakan,tarif, diskon, tarifGsm, st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Lab untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableLab (id_tindakan,tarif,diskon,tarifGsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$sql="SELECT * FROM $nmTableLab ORDER BY id";
		$arrData=$this->queryAction($sql,'S');
		
		$this->UserGridLab->DataSource = $arrData;
		$this->UserGridLab->dataBind();	
		
		$this->DDLab->SelectedValue = 'empty';
		$this->tarifOperatorLab->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDLab);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedLab()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDLab->SelectedValue;
			$tarif = floatval($this->tarifOperatorLab->Text);
			$diskon = floatval($this->diskonOperatorLab->Text);
			$tarifGsm=LabTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifLab->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataLabTmp = $this->getViewState('dataLab');
				unset($dataLabTmp[0]);
				foreach($dataLabTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=LabTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					//Checking Duplikasi on Temporary Table first!
					if($this->getViewState('nmTableLab'))
					{
						$tmpTable = $this->getViewState('nmTableLab');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailLabRecord::finder()->findBySql($sql))
							$this->makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);	
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);	
					}
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					//Checking Duplikasi on Temporary Table first!
					if($this->getViewState('nmTableLab'))
					{
						$tmpTable = $this->getViewState('nmTableLab');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailLabRecord::finder()->findBySql($sql))
							$this->makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);	
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblLab($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);	
					}	
				}
			}
			
			
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		$this->diskonOperatorLab->Text = 0;
	}
	
	public function itemCreatedLab($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_lab_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanLabColoumn->Text = LabTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifLabColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifLabColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
			
			$item->diskonLabColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.') . '%';
			$item->tarifGsmLabColoumn->Text = number_format($item->DataItem['tarifGsm'],'2',',','.');
		}	
	}	
	
	public function deleteClickedLab($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridLab->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableLab = $this->getViewState('nmTableLab');
		
		$sql = "DELETE FROM $nmTableLab WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	protected function PageIndexChangedLab($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridLab->CurrentPageIndex = $param->NewPageIndex;
	}
	

//--------------------------------------- Rad ------------------------------------------
	public function cariNamaRad()
	{
		if($this->cariNamaRad->Text != '')
		{
			$this->setViewState('cariRad','1');
			$this->setViewState('namaRad',$this->cariNamaRad->Text);
		}else
		{
			$this->clearViewState('cariRad');
			$this->clearViewState('namaRad');
		}
	}
	
	public function makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableRad'))
		{
			$nmTableRad = $this->setNameTable('nmTableRad');
			$sql="CREATE TABLE $nmTableRad (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableRad = $this->getViewState('nmTableRad');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rad untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableRad WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableRad (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rad untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableRad (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDRad->SelectedValue = 'empty';
		$this->tarifOperatorRad->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDRad);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedRad()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDRad->SelectedValue;
			$tarif = floatval($this->tarifOperatorRad->Text);
			$diskon = floatval($this->diskonOperatorRad->Text);
			$tarifGsm=RadTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifRad->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataRadTmp = $this->getViewState('dataRad');
				unset($dataRadTmp[0]);
				foreach($dataRadTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=RadTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					//Checking Duplikasi on Temporary Table first!
					if($this->getViewState('nmTableRad'))
					{
						$tmpTable = $this->getViewState('nmTableRad');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailRadRecord::finder()->findBySql($sql))
							$this->makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTableRad'))
					{
						$tmpTable = $this->getViewState('nmTableRad');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailRadRecord::finder()->findBySql($sql))
							$this->makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblRad($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedRad($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_rad_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanRadColoumn->Text = RadTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifRadColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifRadColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');					
			
			$item->diskonRadColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.') . '%';
			$item->tarifGsmRadColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedRad($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridRad->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableRad = $this->getViewState('nmTableRad');
		
		$sql = "DELETE FROM $nmTableRad WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	protected function PageIndexChangedRad($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridRad->CurrentPageIndex = $param->NewPageIndex;
	}
	
	
//--------------------------------------- Fisio ------------------------------------------
	public function cariNamaFisio()
	{
		if($this->cariNamaFisio->Text != '')
		{
			$this->setViewState('cariFisio','1');
			$this->setViewState('namaFisio',$this->cariNamaFisio->Text);
		}else
		{
			$this->clearViewState('cariFisio');
			$this->clearViewState('namaFisio');
		}
	}
	
	public function makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableFisio'))
		{
			$nmTableFisio = $this->setNameTable('nmTableFisio');
			$sql="CREATE TABLE $nmTableFisio (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableFisio = $this->getViewState('nmTableFisio');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Fisio untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableFisio WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableFisio (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Fisio untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableFisio (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDFisio->SelectedValue = 'empty';
		$this->tarifOperatorFisio->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDFisio);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedFisio()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDFisio->SelectedValue;
			$tarif = floatval($this->tarifOperatorFisio->Text);
			$diskon = floatval($this->diskonOperatorFisio->Text);
			$tarifGsm=FisioTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifFisio->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataFisioTmp = $this->getViewState('dataFisio');
				unset($dataFisioTmp[0]);
				foreach($dataFisioTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=FisioTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTableFisio'))
					{
						$tmpTable = $this->getViewState('nmTableFisio');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailFisioRecord::finder()->findBySql($sql))
							$this->makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTableFisio'))
					{
						$tmpTable = $this->getViewState('nmTableFisio');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailFisioRecord::finder()->findBySql($sql))
							$this->makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblFisio($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedFisio($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_fisio_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanFisioColoumn->Text = FisioTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifFisioColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifFisioColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
				
			$item->diskonFisioColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.').'%';
			$item->tarifGsmFisioColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedFisio($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridFisio->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableFisio = $this->getViewState('nmTableFisio');
		
		$sql = "DELETE FROM $nmTableFisio WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}	
	
	protected function PageIndexChangedFisio($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridFisio->CurrentPageIndex = $param->NewPageIndex;
	}
	
//--------------------------------------- Audiometri ------------------------------------------
	public function cariNamaAudiometri()
	{
		if($this->cariNamaAudiometri->Text != '')
		{
			$this->setViewState('cariAudiometri','1');
			$this->setViewState('namaAudiometri',$this->cariNamaAudiometri->Text);
		}else
		{
			$this->clearViewState('cariAudiometri');
			$this->clearViewState('namaAudiometri');
		}
	}
	
	public function makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableAudiometri'))
		{
			$nmTableAudiometri = $this->setNameTable('nmTableAudiometri');
			$sql="CREATE TABLE $nmTableAudiometri (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableAudiometri = $this->getViewState('nmTableAudiometri');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Audiometri untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableAudiometri WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableAudiometri (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Audiometri untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableAudiometri (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDAudiometri->SelectedValue = 'empty';
		$this->tarifOperatorAudiometri->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDAudiometri);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedAudiometri()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDAudiometri->SelectedValue;
			$tarif = floatval($this->tarifOperatorAudiometri->Text);
			$diskon = floatval($this->diskonOperatorAudiometri->Text);
			$tarifGsm=AudiometriTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifAudiometri->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataAudiometriTmp = $this->getViewState('dataAudiometri');
				unset($dataAudiometriTmp[0]);
				foreach($dataAudiometriTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=AudiometriTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTableAudiometri'))
					{
						$tmpTable = $this->getViewState('nmTableAudiometri');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailAudiometriRecord::finder()->findBySql($sql))
							$this->makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTableAudiometri'))
					{
						$tmpTable = $this->getViewState('nmTableAudiometri');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailAudiometriRecord::finder()->findBySql($sql))
							$this->makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblAudiometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedAudiometri($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_audiometri_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanAudiometriColoumn->Text = AudiometriTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifAudiometriColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifAudiometriColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
				
			$item->diskonAudiometriColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.').'%';
			$item->tarifGsmAudiometriColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedAudiometri($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridAudiometri->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableAudiometri = $this->getViewState('nmTableAudiometri');
		
		$sql = "DELETE FROM $nmTableAudiometri WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}	
	
	protected function PageIndexChangedAudiometri($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridAudiometri->CurrentPageIndex = $param->NewPageIndex;
	}
	
//--------------------------------------- Spirometri ------------------------------------------
	public function cariNamaSpiro()
	{
		if($this->cariNamaSpiro->Text != '')
		{
			$this->setViewState('cariSpirometri','1');
			$this->setViewState('namaSpirometri',$this->cariNamaSpiro->Text);
		}else
		{
			$this->clearViewState('cariSpirometri');
			$this->clearViewState('namaSpirometri');
		}
	}
	
	public function makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableSpirometri'))
		{
			$nmTableSpirometri = $this->setNameTable('nmTableSpirometri');
			$sql="CREATE TABLE $nmTableSpirometri (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableSpirometri = $this->getViewState('nmTableSpirometri');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Spirometri untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableSpirometri WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableSpirometri (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','diskon','$tarifGsm')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Spirometri untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableSpirometri (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDSpirometri->SelectedValue = 'empty';
		$this->tarifOperatorSpirometri->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDSpirometri);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function tambahClickedSpirometri()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDSpirometri->SelectedValue;
			$tarif = floatval($this->tarifOperatorSpirometri->Text);
			$diskon = floatval($this->diskonOperatorSpirometri->Text);
			$tarifGsm=SpirometriTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifSpirometri->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataSpirometriTmp = $this->getViewState('dataSpirometri');
				unset($dataSpirometriTmp[0]);
				foreach($dataSpirometriTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=SpirometriTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTableSpirometri'))
					{
						$tmpTable = $this->getViewState('nmTableSpirometri');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailSpirometriRecord::finder()->findBySql($sql))
							$this->makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTableSpirometri'))
					{
						$tmpTable = $this->getViewState('nmTableSpirometri');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailSpirometriRecord::finder()->findBySql($sql))
							$this->makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblSpirometri($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedSpirometri($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_spirometri_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanSpirometriColoumn->Text = SpirometriTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifSpirometriColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifSpirometriColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
				
			$item->diskonSpirometriColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.').'%';
			$item->tarifGsmSpirometriColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedSpirometri($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridSpirometri->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableSpirometri = $this->getViewState('nmTableSpirometri');
		
		$sql = "DELETE FROM $nmTableSpirometri WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}
	
	protected function PageIndexChangedSpirometri($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridSpirometri->CurrentPageIndex = $param->NewPageIndex;
	}
	
//--------------------------------------- PenunjangJantung ------------------------------------------
	public function cariNamaJantung()
	{
		if($this->cariNamaJantung->Text != '')
		{
			$this->setViewState('cariPenunjangJantung','1');
			$this->setViewState('namaPenunjangJantung',$this->cariNamaJantung->Text);
		}else
		{
			$this->clearViewState('cariPenunjangJantung');
			$this->clearViewState('namaPenunjangJantung');
		}
	}
	
	public function makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTablePenunjangJantung'))
		{
			$nmTablePenunjangJantung = $this->setNameTable('nmTablePenunjangJantung');
			$sql="CREATE TABLE $nmTablePenunjangJantung (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTablePenunjangJantung = $this->getViewState('nmTablePenunjangJantung');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan PenunjangJantung untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTablePenunjangJantung WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTablePenunjangJantung (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan PenunjangJantung untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTablePenunjangJantung (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDPenunjangJantung->SelectedValue = 'empty';
		$this->tarifOperatorPenunjangJantung->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDPenunjangJantung);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedPenunjangJantung()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDPenunjangJantung->SelectedValue;
			$tarif = floatval($this->tarifOperatorPenunjangJantung->Text);
			$diskon = floatval($this->diskonOperatorPenunjangJantung->Text);
			$tarifGsm=PenunjangJantungTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifPenunjangJantung->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataPenunjangJantungTmp = $this->getViewState('dataPenunjangJantung');
				unset($dataPenunjangJantungTmp[0]);
				foreach($dataPenunjangJantungTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=PenunjangJantungTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTablePenunjangJantung'))
					{
						$tmpTable = $this->getViewState('nmTablePenunjangJantung');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailPenunjangJantungRecord::finder()->findBySql($sql))
							$this->makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTablePenunjangJantung'))
					{
						$tmpTable = $this->getViewState('nmTablePenunjangJantung');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailPenunjangJantungRecord::finder()->findBySql($sql))
							$this->makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblPenunjangJantung($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedPenunjangJantung($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_penunjangjantung_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanPenunjangJantungColoumn->Text = PenunjangJantungTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifPenunjangJantungColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifPenunjangJantungColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
				
			$item->diskonPenunjangJantungColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.').'%';
			$item->tarifGsmPenunjangJantungColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedPenunjangJantung($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridPenunjangJantung->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTablePenunjangJantung = $this->getViewState('nmTablePenunjangJantung');
		
		$sql = "DELETE FROM $nmTablePenunjangJantung WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}	
	
	protected function PageIndexChangedPenunjangJantung($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridPenunjangJantung->CurrentPageIndex = $param->NewPageIndex;
	}
	
//--------------------------------------- Refraksi ------------------------------------------
	
	public function cariNamaJRefraksi()
	{
		if($this->cariNamaRefraksi->Text != '')
		{
			$this->setViewState('cariRefraksi','1');
			$this->setViewState('namaRefraksi',$this->cariNamaRefraksi->Text);
		}else
		{
			$this->clearViewState('cariRefraksi');
			$this->clearViewState('namaRefraksi');
		}
	}
	
	public function makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif)
	{			
		if (!$this->getViewState('nmTableRefraksi'))
		{
			$nmTableRefraksi = $this->setNameTable('nmTableRefraksi');
			$sql="CREATE TABLE $nmTableRefraksi (id INT (2) auto_increment, 
										 id_tindakan CHAR(30) DEFAULT NULL,
										 tarif FLOAT DEFAULT '0',
										 diskon FLOAT DEFAULT '0',
										 tarif_gsm FLOAT DEFAULT '0',
										 st_tarif CHAR(1) DEFAULT '1',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTableRefraksi = $this->getViewState('nmTableRefraksi');
		}	
		
		if($this->getViewState('stDetail') == '0')
		{
			$sql="SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = 'id_provider' AND id_tindakan = '$id_tindakan'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > '0')
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Refraksi untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );
							},
						}
					}});');	
			}	
			else
			{
				$sql="SELECT * FROM $nmTableRefraksi WHERE id_tindakan = '$id_tindakan' ";
				$arr = $this->queryAction($sql,'S');			 
				
				if(count($arr) == '0')
				{
					$sql="INSERT INTO $nmTableRefraksi (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
					$this->queryAction($sql,'C');				
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','unmaskContent();
						jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Refraksi untuk Asuransi Perusaahan sudah dipilih..</p>\',timeout: 4000,dialog:{
							modal: true,
							buttons: {
								"OK": function() {
									jQuery( this ).dialog( "close" );
								},
							}
						}});');	
				}
			}
		}
		else
		{
			$sql="INSERT INTO $nmTableRefraksi (id_tindakan,tarif,diskon,tarif_gsm,st_tarif) VALUES ('$id_tindakan','$tarif','$diskon','$tarifGsm','$stTarif')";
			$this->queryAction($sql,'C');
		}
		
		$this->DDRefraksi->SelectedValue = 'empty';
		$this->tarifOperatorRefraksi->Text = '0';
		
		$this->Page->CallbackClient->focus($this->DDRefraksi);
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	
	public function tambahClickedRefraksi()
	{
		if($this->Page->IsValid)
		{
			$id_provider = $this->DDPerus->SelectedValue;
			$id_tindakan = $this->DDRefraksi->SelectedValue;
			$tarif = floatval($this->tarifOperatorRefraksi->Text);
			$diskon = floatval($this->diskonOperatorRefraksi->Text);
			$tarifGsm=RefraksiTarifRecord::finder()->findByPK($id_tindakan)->tarif;
			$stTarif = $this->modeTarifRefraksi->SelectedValue;
			
			if($id_tindakan == 'xx' && $diskon >= 0)
			{
				$dataRefraksiTmp = $this->getViewState('dataRefraksi');
				unset($dataRefraksiTmp[0]);
				foreach($dataRefraksiTmp as $row)
				{
					$id_tindakan = $row['kode'];
					$tarifGsm=RefraksiTarifRecord::finder()->findByPK($id_tindakan)->tarif;
					$tarif = $tarifGsm - ($tarifGsm*$diskon/100);
					if($this->getViewState('nmTableRefraksi'))
					{
						$tmpTable = $this->getViewState('nmTableRefraksi');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailRefraksiRecord::finder()->findBySql($sql))
							$this->makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}
				}
			}else{
				if($id_tindakan <> 'xx')
				{
					if($this->getViewState('nmTableRefraksi'))
					{
						$tmpTable = $this->getViewState('nmTableRefraksi');
						$sql = "SELECT id_tindakan FROM $tmpTable WHERE id_tindakan = '$id_tindakan'";
						
						if(!ProviderDetailRefraksiRecord::finder()->findBySql($sql))
							$this->makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Tindakan Rawat Jalan yang dipilih untuk Asuransi Perusaahan sudah ada.</p>\',timeout: 4000,dialog:{
									modal: true,
									buttons: {
										"OK": function() {
											jQuery( this ).dialog( "close" );
										},
									}
								}});');
						}
					}else{
						$this->makeTmpTblRefraksi($id_provider,$id_tindakan,$tarif,$diskon,$tarifGsm,$stTarif);		
					}	
				}
			}
		}	
		else
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
	}
	
	public function itemCreatedRefraksi($sender,$param)
  {
		$item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			$idTdk = $item->DataItem['id_tindakan'];
			$sql = "SELECT nama FROM tbm_refraksi_tindakan WHERE kode = '$idTdk' ";
			$item->tindakanRefraksiColoumn->Text = RefraksiTdkRecord::finder()->findByPk($item->DataItem['id_tindakan'])->nama;
			
			if($item->DataItem['st_tarif'] == '1')
				$item->tarifRefraksiColoumn->Text = number_format($item->DataItem['tarif'],'2',',','.');
			elseif($item->DataItem['st_tarif'] == '2')
				$item->tarifRefraksiColoumn2->Text = number_format($item->DataItem['tarif'],'2',',','.');	
				
			$item->diskonRefraksiColoumn->Text = number_format($item->DataItem['diskon'],'2',',','.').'%';
			$item->tarifGsmRefraksiColoumn->Text = number_format($item->DataItem['tarif_gsm'],'2',',','.');
		}	
	}
	
	public function deleteClickedRefraksi($sender,$param)
	{
		$item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGridRefraksi->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTableRefraksi = $this->getViewState('nmTableRefraksi');
		
		$sql = "DELETE FROM $nmTableRefraksi WHERE id='$ID'";
		$this->queryAction($sql,'C');	
	}	
	
	protected function PageIndexChangedRefraksi($sender,$param)
	{
			// clear data in session because we need to get a new page from db
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->UserGridRefraksi->CurrentPageIndex = $param->NewPageIndex;
	}
	
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Simpan data sekarang ?<br/><br/><br/></p>\',timeout: 1000000,dialog:{
					modal: true,
					buttons: {
						"Ya": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							konfirmasi(\'ya\');
						},
						Tidak: function() {
							jQuery( this ).dialog( "close" );
							konfirmasi(\'tidak\');
						}
					}
				}});');	
        }	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}	
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		$idProv = $this->DDPerus->SelectedValue;
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			//UPDATE st_detail di tbt_perusahaan_asuransi
			$data = PerusahaanRecord::finder()->findByPk($idProv);
			$data->st_detail = '1';
			
			if(floatval($this->plafon_master->Text) && trim($this->plafon_master->Text)!='')
				$data->plafon_master = floatval($this->plafon_master->Text);
			else
				$data->plafon_master = '0';
				
			$data->Save();			
			
			$sql = "SELECT * FROM tbm_provider_detail_tindakan WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_tindakan WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "SELECT * FROM $nmTable ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_tindakan
					$data = new ProviderDetailTindakanRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_poli = $row['id_poli'];
					$data->id_tindakan = $row['id_tindakan'];
					$data->dokter = $row['id_dokter'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_retribusi
			$sql = "SELECT * FROM tbm_provider_detail_retribusi WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_retribusi WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableRetribusi'))
			{
				$nmTableRetribusi = $this->getViewState('nmTableRetribusi');
				
				$sql = "SELECT * FROM $nmTableRetribusi ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_retribusi
					$data = new ProviderDetailRetribusiRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_poli = $row['id_poli'];
					$data->tarif = $row['tarif'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_obat
			$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableObat'))
			{
				$nmTableObat = $this->getViewState('nmTableObat');
				
				$sql = "SELECT * FROM $nmTableObat ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_obat
					$data = new ProviderDetailObatRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_poli = $row['id_poli'];
					$data->plafon = $row['plafon'];
					$data->margin = $row['margin'];
					$data->disc = $row['disc'];
					$data->Save();			
				}
			}
			
			
			//INSERT tbm_provider_detail_lab
			$sql = "SELECT * FROM tbm_provider_detail_lab WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_lab WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableLab'))
			{
				$nmTableLab = $this->getViewState('nmTableLab');
				
				$sql = "SELECT * FROM $nmTableLab ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_lab
					$data = new ProviderDetailLabRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarifGsm = $row['tarifGsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_rad
			$sql = "SELECT * FROM tbm_provider_detail_rad WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_rad WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableRad'))
			{
				$nmTableRad = $this->getViewState('nmTableRad');
				
				$sql = "SELECT * FROM $nmTableRad ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_rad
					$data = new ProviderDetailRadRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_fisio
			$sql = "SELECT * FROM tbm_provider_detail_fisio WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_fisio WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableFisio'))
			{
				$nmTableFisio = $this->getViewState('nmTableFisio');
				
				$sql = "SELECT * FROM $nmTableFisio ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_fisio
					$data = new ProviderDetailFisioRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			/*
			//INSERT tbm_provider_detail_audiometri
			$sql = "SELECT * FROM tbm_provider_detail_audiometri WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_audiometri WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableAudiometri'))
			{
				$nmTableAudiometri = $this->getViewState('nmTableAudiometri');
				
				$sql = "SELECT * FROM $nmTableAudiometri ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_audiometri
					$data = new ProviderDetailAudiometriRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_spirometri
			$sql = "SELECT * FROM tbm_provider_detail_spirometri WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_spirometri WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableSpirometri'))
			{
				$nmTableSpirometri = $this->getViewState('nmTableSpirometri');
				
				$sql = "SELECT * FROM $nmTableSpirometri ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_spirometri
					$data = new ProviderDetailSpirometriRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_penunjangjantung
			$sql = "SELECT * FROM tbm_provider_detail_penunjangjantung WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_penunjangjantung WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTablePenunjangJantung'))
			{
				$nmTablePenunjangJantung = $this->getViewState('nmTablePenunjangJantung');
				
				$sql = "SELECT * FROM $nmTablePenunjangJantung ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_penunjangjantung
					$data = new ProviderDetailPenunjangJantungRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			
			//INSERT tbm_provider_detail_refraksi
			$sql = "SELECT * FROM tbm_provider_detail_refraksi WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$sql = "DELETE FROM tbm_provider_detail_refraksi WHERE id_provider = '$idProv' ";
				$this->queryAction($sql,'C');
			}
			
			if($this->getViewState('nmTableRefraksi'))
			{
				$nmTableRefraksi = $this->getViewState('nmTableRefraksi');
				
				$sql = "SELECT * FROM $nmTableRefraksi ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					//INSERT tbm_provider_detail_refraksi
					$data = new ProviderDetailRefraksiRecord();
					$data->id_provider = $this->DDPerus->SelectedValue;
					$data->id_tindakan = $row['id_tindakan'];
					$data->tarif = $row['tarif'];
					$data->diskon = $row['diskon'];
					$data->tarif_gsm = $row['tarif_gsm'];
					$data->st_tarif = $row['st_tarif'];
					$data->Save();			
				}
			}
			*/
			
			/*
			//INSERT tbm_provider_detail_obat
			$sql = "SELECT * FROM tbm_provider_detail_obat WHERE id_provider = '$idProv' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$data = ProviderDetailObatRecord::finder()->findBySql($sql);
			}
			else
			{
				$data = new ProviderDetailObatRecord();
				$data->id_provider = $idProv;
			}
				
			$data->plafon = floatval($this->plafonObat->Text);
			$data->margin = floatval($this->marginObat->Text);
			$data->Save();	
			*/
			
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->clearTmpTable();
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->redirect($this->Service->constructUrl('Tarif.BhpTindakan'));	
		}
	}
	
	public function clearTmpTable()
	{
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');				
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableObat'))
		{		
			$this->queryAction($this->getViewState('nmTableObat'),'D');				
			$this->UserGridObat->DataSource='';
            $this->UserGridObat->dataBind();
			$this->clearViewState('nmTableObat');//Clear the view state				
		}		
		
		if($this->getViewState('nmTableLab'))
		{		
			$this->queryAction($this->getViewState('nmTableLab'),'D');				
			$this->UserGridLab->DataSource='';
			$this->UserGridLab->dataBind();
			$this->clearViewState('nmTableLab');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableRad'))
		{		
			$this->queryAction($this->getViewState('nmTableRad'),'D');				
			$this->UserGridRad->DataSource='';
			$this->UserGridRad->dataBind();
			$this->clearViewState('nmTableRad');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableFisio'))
		{		
			$this->queryAction($this->getViewState('nmTableFisio'),'D');				
			$this->UserGridFisio->DataSource='';
			$this->UserGridFisio->dataBind();
			$this->clearViewState('nmTableFisio');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableAudiometri'))
		{		
			$this->queryAction($this->getViewState('nmTableAudiometri'),'D');				
			$this->UserGridAudiometri->DataSource='';
			$this->UserGridAudiometri->dataBind();
			$this->clearViewState('nmTableAudiometri');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableSpirometri'))
		{		
			$this->queryAction($this->getViewState('nmTableSpirometri'),'D');				
			$this->UserGridSpirometri->DataSource='';
			$this->UserGridSpirometri->dataBind();
			$this->clearViewState('nmTableSpirometri');//Clear the view state				
		}	
		
		
		if($this->getViewState('nmTablePenunjangJantung'))
		{		
			$this->queryAction($this->getViewState('nmTablePenunjangJantung'),'D');				
			$this->UserGridPenunjangJantung->DataSource='';
			$this->UserGridPenunjangJantung->dataBind();
			$this->clearViewState('nmTablePenunjangJantung');//Clear the view state				
		}	
		
		if($this->getViewState('nmTableRefraksi'))
		{		
			$this->queryAction($this->getViewState('nmTableRefraksi'),'D');				
			$this->UserGridRefraksi->DataSource='';
			$this->UserGridRefraksi->dataBind();
			$this->clearViewState('nmTableRefraksi');//Clear the view state				
		}		
	}
	
	public function batalClicked($sender,$param)
	{		
		$this->clearTmpTable();
		$this->Response->Reload();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->clearTmpTable();
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.PerusPasien'));		
	}

}
?>
