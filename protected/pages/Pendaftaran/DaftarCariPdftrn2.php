<?php
class DaftarCariPdftrn extends SimakConf
{   
	
	private $sortExp = "cm";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	  
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		$tmpVar2=$this->authApp('10');
		$tmpVar2=$this->authApp('10');
		if($tmpVar == "False" && $tmpVar2 == "False")//Bila tidak ada hak utk modul aplikasi rekam medis & pendaftaran 
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$this->DDtahun->DataSource=$this->dataTahun(date('Y')-20,date('Y')+5);
			$this->DDtahun->dataBind();
				
			$sql = "SELECT * FROM tbm_propinsi ORDER BY nama";
			$this->DDProp->DataSource = KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDProp->dataBind();
			//$this->DDProp->SelectedValue = '02';
			
			$sql = "SELECT * FROM tbm_kabupaten WHERE id_propinsi='02' ORDER BY nama";
			$this->DDKab->DataSource = KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKab->dataBind();
			//$this->DDKab->SelectedValue = '010';
			
			$idProv = $this->DDProp->SelectedValue;
			$idKab = $this->DDKab->SelectedValue;
			$idFilter = $idProv.''.$idKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			$this->DDKec->DataSource = KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			
			/*
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind(); 
			*/
			
			//$this->DDKec->Enabled=false;				
			$this->DDKel->Enabled=false;			
			$this->DDPerusAsuransi->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			
			$jnsPas = $this->jnsPas->SelectedValue;
			$sql = "SELECT * FROM tbm_perusahaan_asuransi ORDER BY nama WHERE st='$jnsPas' ";
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDPerusAsuransi->dataBind();
			
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama ";
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAllBySql($sql);
			$this->DDDokter->dataBind();
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();			
			
			$this->jmlDataPas->Text = '0';
			$this->gridPanel->Display = 'None';
			//$this->bindGrid();									
			$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			$this->lockPeriode();	
			$this->DDberdasarkan->SelectedValue = '3';	
			$this->ChangedDDberdasarkan();
			$pilih='3';				
		}
		else
		{
			$this->cariCM->focus();
			$pilih = $this->getViewState('pilihPeriode');
		}	       
		
		
		if ($pilih=='3')
		{
			$this->bulan->Display='Dynamic';			
			$this->prosesBtn->ValidationGroup = 'valBulan';
		}
		elseif ($pilih=='2')
		{
			$this->minggu->Display='Dynamic';
			$this->prosesBtn->ValidationGroup = 'valMinggu';
			
		}
		elseif ($pilih=='1')
		{
			$this->hari->Display='Dynamic';		
			$this->prosesBtn->ValidationGroup = 'valHari';
		}
    }		
	
	public function lockPeriode()
   	{
		$dateNow = date('d-m-Y');
		
		$this->minggu->Display='None';
		$this->hari->Display='None';
		$this->bulan->Display='None';
		
		$this->DDbulan->SelectedIndex=-1;
		$this->DDtahun->SelectedIndex=-1;
		$this->tgl->Text='';
		$this->tglawal->Text='';
		$this->tglakhir->Text='';
		$this->clearViewState('cariThn');
		$this->clearViewState('cariBln');
		
		$this->tglawal->Text = $dateNow;
		$this->tglakhir->Text = $dateNow;
		$this->tgl->Text = $dateNow;
			
		//$this->DDtahun->Enabled=false;
		
		$this->DDbulan->SelectedValue = date('m');
		$this->DDtahun->DataSource=$this->dataTahun(date('Y')-20,date('Y')+5);
		$this->DDtahun->dataBind();
		$this->DDtahun->SelectedValue = date('Y');
		$this->prosesBtn->ValidationGroup = '';
		
	}
	
	public function panelCallback($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function ChangedDDberdasarkan()
	{	
		$this->lockPeriode();
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->DDbulan);
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->tglawal);		
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);		
		}
		else
		{
			$this->clearViewState('pilihPeriode');
			$this->Page->CallbackClient->focus($this->DDberdasarkan);
			$this->Page->CallbackClient->focus($this->tgl);	
		}
		
		$this->cariClicked();
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			$this->Page->CallbackClient->focus($this->DDbulan);
		}			
		else
		{
			$this->Page->CallbackClient->focus($this->DDtahun);
		}
		
		$this->cariClicked();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$this->cariClicked();
	}
		
	public function checkTgl($sender,$param)
	{
		$pecahTglAwal=explode('-',$this->tglawal->Text);
		$pecahTglAkhir=explode('-',$this->tglakhir->Text);
		$tglAwal=$pecahTglAwal['0'];
		$cariBln=$pecahTglAwal['1'];
		$thnAwal=$pecahTglAwal['2'];
		$tglAkhir=$pecahTglAkhir['0'];
		$cariThn=$pecahTglAkhir['1'];
		$thnAkhir=$pecahTglAkhir['2'];
		
		if($thnAkhir<$thnAwal) 
		{
			$hasil='0';
		}
		else
		{
			if($cariThn<$cariBln) 
			{
				$hasil='0';
			}
			else
			{
				if($tglAkhir<$tglAwal) 
				{
					$hasil='0';
				}
				else
				{
					//jika tgl akhir benar
					//$id_ijin=$this->getViewState('id');
					//$this->Response->redirect($this->Service->constructUrl('Lap'.$id_ijin,array('idIjin'=>$id_ijin)));
					$hasil='1';
				}
			}
		}	
		
		$param->IsValid=($hasil==='1');
	}
	
	public function getRecords()
	{
		$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy['nama'] = 'asc';
		$data=PoliklinikRecord::finder()->findAll($criteria);
		return $data;
	}
	
	
	public function getRecordsDok()
	{
		$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' ORDER BY nama";
		$data=PegawaiRecord::finder()->findAllBySql($sql);
		//$data=PegawaiRecord::finder()->findAll('kelompok = 1');
		return $data;
	}
	
	public function getRecordsKelas()
	{
		$data=KelasKamarRecord::finder()->findAll();
		return $data;
	}
	
	public function getRecordsKamar()
	{
		$data=RuangRecord::finder()->findAll();
		return $data;
	}
	
	public function jnsPasChanged($sender,$param)
	{
		$this->cariClicked();
	}
	
	public function DDPropChanged()
	{
		$this->DDKab->SelectedValue = 'empty';
		
		if($this->DDProp->SelectedValue != ''){
			$idProv = $this->DDProp->SelectedValue;
			
			$sql = "SELECT id,nama FROM tbm_kabupaten WHERE id_propinsi = '$idProv' ORDER BY nama ";
			
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKab->dataBind();
			$this->DDKab->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKab);
		}
		else
		{	
			$this->DDKab->DataSource='';
			$this->DDKab->dataBind();
			//$this->DDKab->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDProp);
		}
		
		$this->selectionChangedKab();		
		$this->selectionChangedKec();
		
		$this->cariClicked();
	}
		
	public function selectionChangedKab($sender,$param)
	{
		$this->DDKec->SelectedValue = 'empty';
		
		if($this->DDKab->SelectedValue != ''){
			$idProv = $this->DDProp->SelectedValue;
			$idKab = $this->DDKab->SelectedValue;
			$idFilter = $idProv.''.$idKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKec);
			$this->selectionChangedKec();
		}
		else
		{
			$this->DDKec->DataSource='';
			$this->DDKec->dataBind();
			//$this->DDKec->Enabled = false;	
			
			if($this->DDProp->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDProp);
			elseif($this->DDKab->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDKab);
			else
				$this->Page->CallbackClient->focus($this->DDKec);
		}	
		
		$this->cariClicked();	
			
	} 
	
	public function selectionChangedKec($sender,$param)
	{
		$this->DDKel->SelectedValue = 'empty';
		
		if($this->DDKec->SelectedValue != ''){
			$idKec = $this->DDKec->SelectedValue;
			$idFilter = $idKec;
			
			$sql = "SELECT id,nama FROM tbm_kelurahan WHERE SUBSTRING(id,1,7) = '$idFilter' ORDER BY nama ";
			
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAllBySql($sql);
			$this->DDKel->dataBind();
			$this->DDKel->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKel);
		}
		else
		{
			$this->DDKel->DataSource='';
			$this->DDKel->dataBind();
			//$this->DDKel->Enabled = false;	
			
			if($this->DDProp->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDProp);
			elseif($this->DDKab->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDKab);
			else
				$this->Page->CallbackClient->focus($this->DDKec);
			
		}
		
		$this->cariClicked();	
	}
	
	public function selectionChangedKel($sender,$param)
	{
		$this->cariClicked();
	}
	
	public function tglMskChanged($sender,$param)
	{
		$this->cariClicked();
	}	
	
	
	/*
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			PasienRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtInap'));
			
		}	
    }	
	
	/**
     * Paging Control and Properties to specified pages.
     * This method responds to the datagrid's event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	// ---------- datagrid helper functions --------
    
    public function getSortExpression() {
        if ($this->getViewState('sortExpression',null)!==null) {
            return $this->getViewState('sortExpression');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('sortExpression', $this->sortExp);
        return $this->sortExp;
    }

    public function setSortExpression($sort) {
        $this->setViewState('sortExpression',$sort);
    }

    public function getSortDirection() {
        if ($this->getViewState('sortDirection',null)!==null) {
            return $this->getViewState('sortDirection');
        }
        // set default in case there's no 'sortDirection' key in viewstate
        $this->setViewState('sortDirection', $this->sortDir);
        return $this->sortDir;
    }

    public function setSortDirection($sort) {
        $this->setViewState('sortDirection',$sort);
    }


    // get data and bind it to datagrid
	private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			
           if($this->jnsPas->SelectedValue == '0' )
			{
				$sql = "SELECT 
							   '0' AS tipe_rawat,
							   tbd_pasien.cm AS cm,
							   tbd_pasien.nama AS nama,
							   tbd_pasien.jkel AS jkel,
							   tbd_pasien.tgl_lahir,
							   tbt_rawat_jalan.no_trans AS no_trans,
							   tbt_rawat_jalan.tgl_visit AS tgl,
							   tbt_rawat_jalan.wkt_visit AS waktu,
							   tbt_rawat_jalan.dokter AS id_dokter,
							   tbt_rawat_jalan.tgl_kasir AS tgl_pulang,
							   tbd_pegawai.nama AS dokter,
							   tbm_poliklinik.nama AS klinik,
							   tbt_rawat_jalan.id_klinik AS id_klinik,
							   tbm_kabupaten.nama AS kab,
							   tbm_kelompok.nama AS kelompok,
							   count(tbt_rawat_jalan.cm) AS count						   
							FROM
							   tbt_rawat_jalan
							  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
							  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
							  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
							  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
							  INNER JOIN tbm_kelompok ON (tbt_rawat_jalan.penjamin = tbm_kelompok.id)						  								
							WHERE tbt_rawat_jalan.st_alih='0' ";
				
				if($this->modeBayar->SelectedValue <> '2')
				{
					$modeBayar = $this->modeBayar->SelectedValue;
					$sql .= "AND tbt_rawat_jalan.flag = '$modeBayar' ";
				}
				
				if($this->tglLahir->Text != '')
				{
					$tglLahir = $this->convertDate($this->tglLahir->Text,'2');
					$sql .= "AND tbd_pasien.tgl_lahir = '$tglLahir' ";
				}
							
				if($this->getViewState('cariByKlinik') <> '')			
				{
					$klinik = $this->getViewState('cariByKlinik');
					$sql .= "AND tbt_rawat_jalan.id_klinik = '$klinik' ";		
				}	
									
				if($this->getViewState('cariByDokter') <> '')			
				{
					$dokter = $this->getViewState('cariByDokter');
					$sql .= "AND tbt_rawat_jalan.dokter = '$dokter' ";		
				}
				
				/*
				if($this->getViewState('cariByTgl') <> '')
				{
					$tgl = $this->ConvertDate($this->getViewState('cariByTgl'),'2');//Convert date to mysql
					$sql .= "AND tbt_rawat_jalan.tgl_visit = '$tgl' ";
				}	
				if($this->getViewState('cariByBln') <> '')
				{
					$bln = $this->getViewState('cariByBln');				
					$sql .= "AND MONTH(tbt_rawat_jalan.tgl_visit) = $bln";		
				}
				*/
				
				if($this->DDUrut->SelectedValue != '')
				{
					$DDUrut = $this->DDUrut->SelectedValue;
					$sql .= "AND tbt_rawat_jalan.penjamin = '$DDUrut' ";
				}  
				
				if($this->DDPerusAsuransi->SelectedValue != '')
				{
					$DDPerusAsuransi = $this->DDPerusAsuransi->SelectedValue;
					$sql .= "AND tbt_rawat_jalan.perus_asuransi = '$DDPerusAsuransi' ";
				} 
				
				if($this->cariPj->Text != '')
				{
					$cariPj = $this->cariPj->Text;
					$sql .= "AND tbt_rawat_jalan.penanggung_jawab LIKE '%$cariPj%' ";
				}
				
				if($this->getViewState('pilihPeriode')==='1')
				{
					if($this->tgl->Text != '')
					{
						$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
						$sql .= "AND tbt_rawat_jalan.tgl_visit = '$cariTgl' ";
						
						$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
					}
					
				}
				elseif($this->getViewState('pilihPeriode')==='2')
				{
					if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
					{
						$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
						$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
						$sql .= "AND tbt_rawat_jalan.tgl_visit BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
						
						$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
					}
				}
				elseif($this->getViewState('pilihPeriode')==='3')
				{
					if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
					{
						$cariBln=$this->DDbulan->SelectedValue;
						$cariThn=$this->ambilTxt($this->DDtahun);
						$sql .= "AND MONTH (tbt_rawat_jalan.tgl_visit)='$cariBln' AND YEAR(tbt_rawat_jalan.tgl_visit)='$cariThn' ";
					
						$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
					}
					else
					{
						$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
					}		
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}
						
			}
			elseif($this->jnsPas->SelectedValue == '1' )
			{
				$sql = "SELECT '1' AS tipe_rawat,
							   tbd_pasien.cm AS cm,
							   tbd_pasien.nama AS nama,
							   tbd_pasien.jkel AS jkel,
							   tbd_pasien.tgl_lahir,
							   tbt_rawat_inap.no_trans AS no_trans,
							   tbt_rawat_inap.tgl_masuk AS tgl,
							   tbt_rawat_inap.dokter AS id_dokter,
							   tbt_rawat_inap.kelas AS id_kelas,
							   tbm_kamar_kelas.nama AS nm_kelas,
							   tbt_rawat_inap.kamar AS id_ruang,
							   tbt_rawat_inap.tgl_keluar AS tgl_pulang,
							   tbm_ruang.nama AS nm_ruang,
							   tbt_rawat_inap.jenis_kamar AS id_jns_kamar,
							   tbd_pegawai.nama AS dokter,
							   tbm_kabupaten.nama AS kab,
							   tbm_kelompok.nama AS kelompok,
							   count(tbt_rawat_inap.cm) AS count						   
							FROM
							  tbt_rawat_inap
							  INNER JOIN tbd_pasien ON (tbt_rawat_inap.cm = tbd_pasien.cm)
							  INNER JOIN tbd_pegawai ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
							  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
							  INNER JOIN tbm_kelompok ON (tbt_rawat_inap.penjamin = tbm_kelompok.id)
							  INNER JOIN tbm_kamar_kelas ON (tbt_rawat_inap.kelas = tbm_kamar_kelas.id)	
							  INNER JOIN tbm_ruang ON (tbt_rawat_inap.kamar = tbm_ruang.id)						  								
							WHERE tbt_rawat_inap.cm <> '' ";
				
				if($this->modeBayar->SelectedValue <> '2')
				{
					$modeBayar = $this->modeBayar->SelectedValue;
					$sql .= "AND tbt_rawat_inap.status = '$modeBayar' ";
				}
				
				if($this->tglLahir->Text != '')
				{
					$tglLahir = $this->convertDate($this->tglLahir->Text,'2');
					$sql .= "AND tbd_pasien.tgl_lahir = '$tglLahir' ";
				}
							
				if($this->getViewState('cariByDokter') <> '')			
				{
					$dokter = $this->getViewState('cariByDokter');
					$sql .= "AND tbt_rawat_inap.dokter = '$dokter' ";		
				}
				/*
				if($this->getViewState('cariByTgl') <> '')
				{
					$tgl = $this->ConvertDate($this->getViewState('cariByTgl'),'2');//Convert date to mysql
					$sql .= "AND tbt_rawat_inap.tgl_masuk = '$tgl' ";
				}	
				if($this->getViewState('cariByBln') <> '')
				{
					$bln = $this->getViewState('cariByBln');				
					$sql .= "AND MONTH(tbt_rawat_inap.tgl_masuk) = $bln";		
				}
				*/
				
				if($this->DDUrut->SelectedValue != '')
				{
					$DDUrut = $this->DDUrut->SelectedValue;
					$sql .= "AND tbt_rawat_inap.penjamin = '$DDUrut' ";
				}
				
				if($this->DDPerusAsuransi->SelectedValue != '')
				{
					$DDPerusAsuransi = $this->DDPerusAsuransi->SelectedValue;
					$sql .= "AND tbt_rawat_inap.perus_asuransi = '$DDPerusAsuransi' ";
				} 
				
				if($this->cariPj->Text != '')
				{
					$cariPj = $this->cariPj->Text;
					$sql .= "AND tbt_rawat_inap.nama_pgg LIKE '%$cariPj%' ";
				}
				
				if($this->getViewState('pilihPeriode')==='1')
				{
					if($this->tgl->Text != '')
					{
						$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
						$sql .= "AND tbt_rawat_inap.tgl_masuk = '$cariTgl' ";
						
						$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
					}
					
				}
				elseif($this->getViewState('pilihPeriode')==='2')
				{
					if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
					{
						$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
						$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
						$sql .= "AND tbt_rawat_inap.tgl_masuk BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
						
						$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
					}
				}
				elseif($this->getViewState('pilihPeriode')==='3')
				{
					if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
					{
						$cariBln=$this->DDbulan->SelectedValue;
						$cariThn=$this->ambilTxt($this->DDtahun);
						$sql .= "AND MONTH (tbt_rawat_inap.tgl_masuk)='$cariBln' AND YEAR(tbt_rawat_inap.tgl_masuk)='$cariThn' ";
					
						$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
					}
					else
					{
						$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
					}		
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}
			}
						
			if($this->getViewState('cariByNama') <> '')
			{
				$nama = $this->getViewState('cariByNama');			
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND tbd_pasien.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbd_pasien.nama LIKE '$nama%' ";
				}
			}
			if($this->getViewState('cariByAlamat') <> '')			
			{
				$alamat = $this->getViewState('cariByAlamat');
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND tbd_pasien.alamat LIKE '%$alamat%' ";
				}
				else
				{	
					$sql .= "AND tbd_pasien.alamat LIKE '$alamat%' ";
				}
			}			
				
			if($this->getViewState('cariByCM') <> '')			
			{
				$cm = $this->getViewState('cariByCM');
				$sql .= "AND tbd_pasien.cm = '$cm' ";	
			}
			
			if($this->cariTlp->Text != '')
			{
				$cariTlp = $this->cariTlp->Text;
				$sql .= "AND telp LIKE '%$cariTlp%' ";
			}
			
			if($this->cariHp->Text != '')
			{
				$cariHp = $this->cariHp->Text;
				$sql .= "AND hp LIKE '%$cariHp%' ";
			}
			
			if($this->DDProp->SelectedValue != '')
			{
				$DDProp = $this->DDProp->SelectedValue;
				$sql .= "AND tbd_pasien.propinsi = '$DDProp' ";
			}
			
			if($this->DDKab->SelectedValue != '')
			{
				$DDKab = $this->DDKab->SelectedValue;
				$sql .= "AND tbd_pasien.kabupaten = '$DDKab' ";
			}
			
			if($this->DDKec->SelectedValue != '')
			{
				$DDKec = $this->DDKec->SelectedValue;
				$sql .= "AND tbd_pasien.kecamatan = '$DDKec' ";
			}
			
			if($this->DDKel->SelectedValue != '')
			{
				$DDKel = $this->DDKel->SelectedValue;
				$sql .= "AND tbd_pasien.kelurahan = '$DDKel' ";
			}
			
			//menetukan pasien lama atau pasien baru
			if($this->kategPas->SelectedValue == '0')
				$sql .= " AND tbd_pasien.st_baru_lama = '0'  ";
		    else if($this->kategPas->SelectedValue == '1')
		   		$sql .= " AND tbd_pasien.st_baru_lama = '1'  ";
			
			if($this->jnsPas->SelectedValue == '0' )
			{
				$sql .= " GROUP BY tbt_rawat_jalan.no_trans "; 
			}
			elseif($this->jnsPas->SelectedValue == '1' )
			{
				$sql .= " GROUP BY tbt_rawat_inap.no_trans ";
			}		   			
			
			/*
			//menetukan pasien lama atau pasien baru
			if($this->getViewState('cariByKategPas') == '0')
			{
				$sql .= " HAVING count  < 2  ";
		    }else if($this->getViewState('cariByKategPas') == '1'){
		   		$sql .= " HAVING count > 1 ";
		    }*/
			
			//$this->showSql->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->gridPanel->Display = 'Dynamic';
			}
			else
			{
				$this->gridPanel->Display = 'None';
			} 
			
			$this->setViewState('sql',$sql);
			//$this->sqlData->Text=$sql;//Show sql syntax    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();			
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();	
			$this->clearViewState('sql');		
        }
    }
    
    // ---------- datagrid page and sort events ---------------
    
    protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
        
        // many people won't set this to the first page. this can lead to usability problems.
        // think in what happens if the user is on the 3rd page and changes the sorting field. 
        // you will sort the items on that page if you are using cached data (either in session or "true" cache). 
        // imagine now that the user moves on to page 4. the data on page 4 will be sorted out but it will be 
        // sorted disregarding the other items in other pages. other pages could have items that are "lower" or 
        // "bigger" than the ones displayed. You could have items with the sorting field starting with letter "C" 
        // on page 3 and on page 4 items with the sorting field starting with letter "A". 
        // you could sort all the cached data to solve this but then what page you will show to the user? stick with page 3?
        // I find it better to refresh the data and allways move on to the first page.
        
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	protected function DDKelasChanged($sender,$param)
    {	
		$item = $this->dtgSomeData->EditItem;
		$idKelas = $item->kelas2->DDKelas->SelectedValue;
		 
		//$this->showSql->Text = $item->kelas2->DDKelas->SelectedValue;
		//$this->showSql->Text = $idKelas;
		$this->setViewState('idKelasTmp',$idKelas);
		$this->bindGrid();
	}
	
	protected function DDKlinikChanged2($sender,$param)
    {	
		$item = $this->dtgSomeData->EditItem;
		$idKlinik = $item->klinik2->DDKlinik->SelectedValue;
		 
		//$this->showSql->Text = $item->kelas2->DDKelas->SelectedValue;
		//$this->showSql->Text = $idKelas;
		$this->setViewState('idKlinikTmp',$idKlinik);
		$this->bindGrid();
	}
	
	 //  ------------ datagrid edit related events -------------
	 protected function dtgSomeData_ItemCreated2($sender,$param)
    {
       $item=$param->Item;
	   
	   if($item->ItemType==='EditItem' )
        {	
			if($this->jnsPas->SelectedValue == '0' )
			{
				if($this->getViewState('idKlinikTmp'))
				{
					$idKlinik = $this->getViewState('idKlinikTmp');
				}
				else
				{
					$idKlinik = $item->DataItem['id_klinik'];
				}
				
				$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama";
				$data=PoliklinikRecord::finder()->findAllBySql($sql);
				$item->klinik2->DDKlinik->setDataSource($data);
				$item->klinik2->DDKlinik->databind();
				
				if($idKlinik == '07')
				{
					$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' AND poliklinik='08' ORDER BY nama";
				}	
				else
				{
					$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' AND poliklinik='$idKlinik' ORDER BY nama";
				}	
						
				$data=PegawaiRecord::finder()->findAllBySql($sql);			
				//$item->dokter->DropDownList->setDataSource($data);
				$item->dokter2->DDDokter2->setDataSource($data);
				$item->dokter2->DDDokter2->databind();
				
				if($this->getViewState('idKlinikTmp'))
				{
					$item->klinik2->DDKlinik->SelectedValue = $this->getViewState('idKlinikTmp');					
				}
				else
				{
					$item->klinik2->DDKlinik->SelectedValue = $item->DataItem['id_klinik'];
					$item->dokter2->DDDokter2->SelectedValue = $item->DataItem['id_dokter'];
					//$item->dokter->DropDownList->SelectedValue = $item->DataItem['id_dokter'];
				}
				$item->dokter2->DDDokter2->SelectedValue = $item->DataItem['id_dokter'];
			}
			elseif($this->jnsPas->SelectedValue == '1' )
			{
				
				
			}
        }
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			
		}
	}
	    
    protected function dtgSomeData_ItemCreated($sender,$param)
    {
       $item=$param->Item;
				
        if($item->ItemType==='EditItem' )
        {	
			if($this->jnsPas->SelectedValue == '0' )
			{
				if($this->getViewState('idKlinikTmp'))
				{
					$idKlinik = $this->getViewState('idKlinikTmp');
				}
				else
				{
					$idKlinik = $item->DataItem['id_klinik'];
				}
				
				$data=PoliklinikRecord::finder()->findAll();
				$item->klinik2->DDKlinik->setDataSource($data);
				$item->klinik2->DDKlinik->databind();
				
				if($idKlinik == '07')
				{
					$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' AND poliklinik='08' ORDER BY nama";
				}	
				else
				{
					$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' AND poliklinik='$idKlinik' ORDER BY nama";
				}	
						
				$data=PegawaiRecord::finder()->findAllBySql($sql);			
				//$item->dokter->DropDownList->setDataSource($data);
				$item->dokter2->DDDokter2->setDataSource($data);
				$item->dokter2->DDDokter2->databind();
				
				if($this->getViewState('idKlinikTmp'))
				{
					$item->klinik2->DDKlinik->SelectedValue = $this->getViewState('idKlinikTmp');					
				}
				else
				{
					$item->klinik2->DDKlinik->SelectedValue = $item->DataItem['id_klinik'];
					$item->dokter2->DDDokter2->SelectedValue = $item->DataItem['id_dokter'];
					//$item->dokter->DropDownList->SelectedValue = $item->DataItem['id_dokter'];
				}
				$item->dokter2->DDDokter2->SelectedValue = $item->DataItem['id_dokter'];
			}
			elseif($this->jnsPas->SelectedValue == '1' )
			{
				$this->dtgSomeData->Columns[6]->Visible = true;
				
				if($this->getViewState('idKelasTmp'))
				{
					$idKelas = $this->getViewState('idKelasTmp');
				}
				else
				{
					$idKelas = $item->DataItem['id_kelas'];
				}
				
				$id_jns_kamar = $item->DataItem['id_jns_kamar'];
				
				$data=KelasKamarRecord::finder()->findAll();
				$item->kelas2->DDKelas->setDataSource($data);
				$item->kelas2->DDKelas->databind();
				
				
				$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '1' ORDER BY nama";
						
				$data=PegawaiRecord::finder()->findAllBySql($sql);			
				//$item->dokter->DropDownList->setDataSource($data);
				$item->dokter2->DDDokter2->setDataSource($data);
				$item->dokter2->DDDokter2->databind();
				
				$item->dokter2->DDDokter2->SelectedValue = $item->DataItem['id_dokter'];
				 
				$sql = "SELECT * FROM tbm_ruang WHERE id_kelas = '$idKelas' AND id_jns_kamar = '$id_jns_kamar' ORDER BY nama";
				$data=RuangRecord::finder()->findAllBySql($sql);			
				$item->kamar->DropDownList->setDataSource($data);
				
				$item->kamar2->DDKamar->setDataSource($data);
				$item->kamar2->DDKamar->databind();
				
				if($this->getViewState('idKelasTmp'))
				{
					$item->kelas2->DDKelas->SelectedValue = $this->getViewState('idKelasTmp');					
				}
				else
				{
					$item->kelas2->DDKelas->SelectedValue = $item->DataItem['id_kelas'];
					$item->kamar2->DDKamar->SelectedValue = $item->DataItem['id_ruang'];
				}
				
			}
        }
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{	
			if($this->jnsPas->SelectedValue == '0' )
			{				
				//$this->showSql->Text = $this->getViewState('idKlinikAwal');
				//$item->klinik->Visible=true;
				//$item->klinik->Enabled=true;
				//$item->kelas->Enabled=false;
				//$item->kamar->Enabled=false;
				
				/*
				$this->dtgSomeData->Columns[7]->Visible = false;
				$this->dtgSomeData->Columns[8]->Visible = true;
				$this->dtgSomeData->Columns[9]->Visible = false;
				$this->dtgSomeData->Columns[10]->Visible = false;
				$this->dtgSomeData->Columns[14]->Visible = true;
				$this->dtgSomeData->Columns[15]->Visible = false;
				*/
				$this->dtgSomeData->Columns[8]->Visible = false;
				$this->dtgSomeData->Columns[9]->Visible = true;
				$this->dtgSomeData->Columns[10]->Visible = false;
				$this->dtgSomeData->Columns[11]->Visible = false;
				$this->dtgSomeData->Columns[15]->Visible = true;
				$this->dtgSomeData->Columns[16]->Visible = false;
			}
			elseif($this->jnsPas->SelectedValue == '1' )
			{
				//$item->klinik->Visible=false;
				$item->klinik->Enabled=false;
				$item->kelas->Enabled=true;
				$item->kamar->Enabled=true;
				/*
				$this->dtgSomeData->Columns[6]->Visible = true;
				$this->dtgSomeData->Columns[7]->Visible = false;
				$this->dtgSomeData->Columns[8]->Visible = false;
				$this->dtgSomeData->Columns[9]->Visible = true;
				$this->dtgSomeData->Columns[10]->Visible = true;
				$this->dtgSomeData->Columns[14]->Visible = false;
				$this->dtgSomeData->Columns[15]->Visible = true;
				*/
				$this->dtgSomeData->Columns[7]->Visible = true;
				$this->dtgSomeData->Columns[8]->Visible = false;
				$this->dtgSomeData->Columns[9]->Visible = false;
				$this->dtgSomeData->Columns[10]->Visible = true;
				$this->dtgSomeData->Columns[11]->Visible = true;
				$this->dtgSomeData->Columns[15]->Visible = false;
				$this->dtgSomeData->Columns[16]->Visible = true;
			}
			
			if($this->jnsPas->SelectedValue == '0') //Rawat Jalan
			{
				$item->RincianColumn->rincianBtn->Attributes->onclick = 'popup(\'index.php?page=Pendaftaran.cetakRincianBiayaPasien&cm='.$item->DataItem['cm'].'&notrans='.$item->DataItem['no_trans'].'&jmlTagihan='.$item->DataItem['jml'].'&jnsPasien='.$item->DataItem['tipe_rawat'].'\',\'tes\')';
				
			}
			else
			{
				$item->RincianColumn->rincianBtn->Attributes->onclick = 'popup(\'index.php?page=Pendaftaran.cetakRincianBiayaPasien&cm='.$item->DataItem['cm'].'&notrans='.$item->DataItem['no_trans'].'&jmlTagihan='.$item->DataItem['jml'].'&jnsPasien='.$item->DataItem['tipe_rawat'].'\',\'tes\')';
			}
			
			if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				//$item->Edit->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin merubah data ini..? jangan lupa entry lagi tidakan Dokter pengganti!\')) return false;';		
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';			
			}else{
				$item->Edit->Visible='0';
				$item->Hapus->Visible='0';
			}	
		}
    }

    protected function dtgSomeData_EditCommand($sender,$param)
    {		
		$this->clearViewState('idKelasTmp');
		$this->clearViewState('idKlinikTmp');
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();		
    }

    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
		//JIKA RAWAT INAP
		if($this->jnsPas->SelectedValue == '1' )
		{
			//$rujukan = $item->rujukan->DropDownList->SelectedValue;
			$kelas = $item->kelas2->DDKelas->SelectedValue;
			$kamar = $item->kamar->DropDownList->SelectedValue;
			$dokter = $item->dokter2->DDDokter2->SelectedValue;
			
			if ($this->User->IsAdmin)
			{
				
				// i'm using here TActiveRecord for simplicity
				//$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
				$oSomeData = RwtInapRecord::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
				
				//Save Dokter
				$dokterAwal = $oSomeData->dokter;
				$oSomeData->dokter = $dokter;
				$oSomeData->save();
				
				$kelasAwal = $oSomeData->kelas;
				$kamarAwal = $oSomeData->kamar;
							
				// do some changes to your database item/object and then save it
				//$oSomeData->st_rujukan = $rujukan;
				$oSomeData->kelas = $kelas;
				$oSomeData->kamar = $kamar;
				
				
				$notrans=$this->dtgSomeData->DataKeys[$item->ItemIndex];
				$sql="SELECT * FROM tbt_inap_kamar WHERE  no_trans_inap='$notrans' ORDER BY id DESC";
				//$kmrInap=InapKamarRecord::finder()->find('no_trans_inap=? AND st_rubah=?',$this->dtgSomeData->DataKeys[$item->ItemIndex],'0');
				$kmrInap=InapKamarRecord::finder()->findBySql($sql);
				$kmrInap->tgl_kmr_ubah=date('Y-m-d');
				$kmrInap->wkt_rubah=date('G:i:s');
				$kmrInap->id_kmr_ubah=$kelas;
				
				$tglMasuk= $kmrInap->tgl_awal;
				$tglKeluar= date('Y-m-d');	
				$wktMasuk= $kmrInap->wkt_masuk;
				$wktKeluar= date('G:i:s');		
				$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				$kmrInap->lama_inap=$lamaInap;
				$kmrInap->tgl_keluar=$tglKeluar;
				$kmrInap->wkt_keluar=$wktKeluar;
				
				$dataKamar=new InapKamarRecord();
				$dataKamar->cm=$kmrInap->cm;
				$dataKamar->no_trans_inap=$kmrInap->no_trans_inap;
				$dataKamar->tgl_awal=date('Y-m-d');
				$dataKamar->wkt_masuk=date('G:i:s');
				$dataKamar->id_kmr_awal=$kelas;
				$dataKamar->id_kmr_skrg=$kelas;
				
				$this->showSql->Text = $kelasAwal.' - '.$kelas.' - '.$kamarAwal.' - '.$kamar.' - '.$dokterAwal.' - '.$dokter;
				
				//Tidak ada perubahan kamar / kelas
				if($kelasAwal == $kelas && $kamarAwal == $kamar)
				{
					//Ada perubahan dokter
					if($dokterAwal != $dokter)
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Ganti Dokter Rawat Inap Sukses.<br/><br/></p>\',timeout: 6000,dialog:{
							modal: true}});');		
					}
				}
				else
				{
					if ($kmrInap->tgl_awal != date('Y-m-d'))
					{
						$dataKamar->save();
						$oSomeData->save();           
						$kmrInap->save();
						
						//UPDATE kelas di tbt_rawat_inap
						$sql="SELECT * FROM tbt_rawat_inap WHERE  no_trans='$notrans' AND status='0'";
						$RwtInap = RwtInapRecord::finder()->findBySql($sql);
						$RwtInap->kelas = $kelas;
						$RwtInap->kamar = $kamar;
						$RwtInap->save();
						
						//Ada perubahan dokter
						if($dokterAwal != $dokter)
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Pindah Kelas/Kamar & Ganti Dokter Rawat Inap Sukses.<br/><br/></p>\',timeout: 6000,dialog:{
								modal: true}});');		
						}
						else
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Pindah Kelas dan Kamar Rawat Inap Sukses.<br/><br/></p>\',timeout: 6000,dialog:{
								modal: true}});');
						}							
					}
					else
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Pindah Kelas dan Kamar Rawat Inap Gagal.<br/>Perpindahan Kelas dan Kamar diperbolehkan jika lama inap sudah melebihi 1 hari.</p>\',timeout: 6000,dialog:{
							modal: true}});');	
								
					}
				}	
				// clear data in session because we need to refresh it from db
				// you could also modify the data in session and not clear the data from session!
				$session = $this->getSession();
				$session->remove("SomeData");        
			}
		}
		
		//JIKA RAWAT JALAN
		if($this->jnsPas->SelectedValue == '0' )
		{
			//$rujukan = $item->rujukan->DropDownList->SelectedValue;
			$no_trans_rwtjln = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$idKlinikAwal = RwtjlnRecord::finder()->findByPk($no_trans_rwtjln)->id_klinik;
			$idDokterAwal = RwtjlnRecord::finder()->findByPk($no_trans_rwtjln)->dokter;
			
			$poliklinik = $item->klinik2->DDKlinik->SelectedValue;
			$dokter = $item->dokter2->DDDokter2->SelectedValue;
			//$dokter = $item->dokter->DropDownList->SelectedValue;
			
			//$this->showSql->Text = $idKlinikAwal.' - '.$poliklinik;
			
			if ($this->User->IsAdmin)
			{
				if( $idKlinikAwal == $poliklinik ) //klinik tidak pindah
				{
					if( $idDokterAwal != $dokter ) //ganti dokter
					{
						$oSomeData = RwtjlnRecord::finder()->findByPk($no_trans_rwtjln);
						$oSomeData->dokter = $dokter;
						$oSomeData->save();
						
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Ganti Dokter Rawat Jalan Sukses.</p>\',timeout: 4000,dialog:{
							modal: true}});');		
					}
				}
				else //klinik pindah
				{
					//cek tbt_kasir_rwtjln => jika sudah ada transaksi maka pindah klinik tidak bisa dilakukan
					$sql = "SELECT * FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$no_trans_rwtjln' AND klinik = '$idKlinikAwal' AND st_flag='0'";	
					$arr = $this->queryAction($sql,'S');
					
					if(count($arr) > 0)
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Ganti Poliklinik tidak bisa dilakukan karena masih ada transaksi yang belum diselesaikan di Poliklinik sebelumnya !</p>\',timeout: 4000,dialog:{
							modal: true}});');
					}
					else
					{
						$oSomeData = RwtjlnRecord::finder()->findByPk($no_trans_rwtjln);
						$oSomeData->id_klinik = $poliklinik;
						$oSomeData->dokter = $dokter;
						$oSomeData->save();
					
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Ganti Poliklinik sukses.</p>\',timeout: 4000,dialog:{
							modal: true}});');
					}
				}
				
				$session = $this->getSession();
				$session->remove("SomeData");        
			}
		}
		
        $this->dtgSomeData->EditItemIndex = -1;
        $this->bindGrid();
    }

    protected function dtgSomeData_CancelCommand($sender,$param)
    {
		$this->clearViewState('idKelasTmp');
		$this->clearViewState('idKlinikTmp');
		
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
    
    protected function dtgSomeData_DeleteCommand($sender,$param)
    {
        //$oSomeData = SomeDataList::getSomeData($this->dtgSomeData->DataKeys[$param->Item->ItemIndex]);		
        //$oSomeData->delete();
		$item=$param->Item;
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
		
		if ($this->User->IsAdmin)
		{
			RwtjlnRecord::finder()->deleteByPk($ID);	
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>Anda tidak mempunyai akses untuk menghapus transaksi ini.</p>\',timeout: 4000,dialog:{
				modal: true}});');
		}	

        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
                
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function rincianClicked($sender,$param)
	{		
		$this->cariClicked();		
	}
	
	public function cetakKartuBtnClicked($sender,$param)
	{		
		$noTrans = $sender->CommandParameter;
		$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
		$id_klinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;
		$dokter= RwtjlnRecord::finder()->findByPk($noTrans)->dokter;
		$pen = PasienRecord::finder()->findByPk($cm)->nama;
		$shift = RwtjlnRecord::finder()->findByPk($noTrans)->shift;
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKartu',array('cm'=>$cm,'notrans'=>$noTrans,'poli'=>$id_klinik,'dokter'=>$dokter,'pen'=>$pen,'shift'=>$shift,'notrans'=>$noTrans,'mode'=>'01')));		
	}
	
	public function cetakRingkasanBtnClicked($sender,$param)
	{		
		$noTrans = $sender->CommandParameter;
		$cm = RwtInapRecord::finder()->findByPk($noTrans)->cm;
		$jmlBed = RwtInapRecord::finder()->findByPk($noTrans)->bed;
		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakRingakasanMasukInapHtml',array('cm'=>$cm,'noTrans'=>$noTrans,'jmlBed'=>$jmlBed,'mode'=>'01')));		
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	/*
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianBaru'));		
	}*/
	
	public function cariClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
		if($this->CBpdf->Checked==false)
		{		
			if($this->cariNama->Text){
				$this->setViewState('cariByNama', $this->cariNama->Text);
			}else{
				$this->clearViewState('cariByNama');	
			}
		
			if($this->cariAlamat->Text){
				$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
			}else{
				$this->clearViewState('cariByAlamat');	
			}
		
			if($this->tglMsk->Text){
				$this->setViewState('cariByTgl', $this->tglMsk->Text);
			}else{
				$this->clearViewState('cariByTgl');	
			}
		
			if($this->DDBulan->SelectedValue){ 		
				$this->setViewState('cariByBln', $this->DDBulan->SelectedValue);	
			}else{
				$this->clearViewState('cariByBln');	
			}		
		
			if($this->DDKlinik->SelectedValue){ 		
				$this->setViewState('cariByKlinik', $this->DDKlinik->SelectedValue);	
			}else{
				$this->clearViewState('cariByKlinik');	
			}
			
			if($this->DDDokter->SelectedValue){ 		
				$this->setViewState('cariByDokter', $this->DDDokter->SelectedValue);	
			}else{
				$this->clearViewState('cariByDokter');	
			}
			
			if($this->kategPas){ 		
				$kategPas = $this->collectSelectionResult($this->kategPas);
				$this->setViewState('cariByKategPas', $kategPas);	
			}else{
				$this->clearViewState('cariByKategPas');	
			}
					
			if($this->formatCm($this->cariCM->Text)){ 
				$this->setViewState('cariByCM', $this->formatCm($this->cariCM->Text));	
			}else{
				$this->clearViewState('cariByCM');	
			}	
		
			if($this->Advance->Checked) {
				$this->setViewState('elemenBy',$this->Advance->Checked);
			}else{
				$this->clearViewState('elemenBy');	
			}
		
			if($this->DDKab->SelectedValue){ 		
				$this->setViewState('cariByKab', $this->DDKab->SelectedValue);	
			}else{
				$this->clearViewState('cariByKab');	
			}
		
			if($this->DDKec->SelectedValue){ 		
				$this->setViewState('cariByKec', $this->DDKec->SelectedValue);	
			}else{
				$this->clearViewState('cariByKec');	
			}
		
			if($this->DDKel->SelectedValue){ 		
				$this->setViewState('cariByKel', $this->DDKel->SelectedValue);	
			}else{
				$this->clearViewState('cariByKel');	
			}
		
			if($this->DDPerusAsuransi->SelectedValue){ 		
				$this->setViewState('cariByCompany', $this->DDPerusAsuransi->SelectedValue);
			}else{
				$this->clearViewState('cariByCompany');	
			}
				
			if($this->DDUrut->SelectedValue){
				$this->setViewState('cariByUrut', $this->DDUrut->SelectedValue);
			}else{
				$this->clearViewState('cariByUrut');	
			}
			/*
			if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
			{	
				$this->DDPerusAsuransi->Enabled=true;	
			}else{
				$this->DDPerusAsuransi->Enabled=false;	
			}
			*/
			$this->bindGrid();		
			
			//$this->dtgSomeData->Columns->indexOf($this->tesColumn)->Visible = false;
			//$this->dtgSomeData->Items->tesColumn->setVisible(false);
			/*
			foreach ($this->dtgSomeData->getItems() as $item) {
				foreach ($item->getChildren() as $child) {
					$child->setVisible(false);
				}
			}
			*/		
		}
		else
		{
			$cariCM=$this->formatCm($this->cariCM->Text);
			$cariNama=$this->cariNama->Text;
			$tipeCari=$this->Advance->Checked;
			$cariAlamat=$this->cariAlamat->Text;
			$urutBy=$this->DDUrut->SelectedValue;
			$Company=$this->DDPerusAsuransi->SelectedValue;
			$cariTgl=$this->tglMsk->Text;
			$cariBln=$this->DDBulan->SelectedValue;
			$cariByDokter=$this->DDDokter->SelectedValue;
			$cariByKlinik=$this->DDKlinik->SelectedValue;
			
			$cariByKab=$this->DDKab->SelectedValue;
			$cariByKec=$this->DDKec->SelectedValue;
			$cariByKel=$this->DDKel->SelectedValue;
			$modeBayar=$this->modeBayar->SelectedValue;
			
			$session=new THttpSession;
			$session->open();
			$session['cetakDaftarCariPendaftaran'] = $this->getViewState('sql');
		
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariPdftrnPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company,'cariTgl'=>$cariTgl,'cariBln'=>$cariBln,'cariByDokter'=>$cariByDokter,'cariByKlinik'=>$cariByKlinik,'cariByKab'=>$cariByKab,'cariByKec'=>$cariByKec,'cariByKel'=>$cariByKel,'modeBayar'=>$modeBayar)));
		}	
	}
	
	public function selectionChangedUrut()
	{
		if($this->DDUrut->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			if($this->DDUrut->SelectedValue == '02' || $this->DDUrut->SelectedValue == '07')
			{
				$idKelPerus = $this->DDUrut->SelectedValue;
				$jnsPas = $this->jnsPas->SelectedValue;
							
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' AND st='$jnsPas' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerusAsuransi->DataSource = $data;
				$this->DDPerusAsuransi->dataBind();
				
				$this->DDPerusAsuransi->Enabled = true;	
			}
			else
			{
				$this->DDPerusAsuransi->SelectedValue = 'empty';
				$this->DDPerusAsuransi->Enabled = false;
			}
		}
		
		$this->cariClicked();
	}
	
	public function DDKlinikChanged($sender,$param)
	{				
		$this->cariClicked();
	}
	
	public function DDDokterChanged($sender,$param)
	{		
		$this->cariClicked();
	}
	
	public function KategPasChanged($sender,$param)
	{		
		$this->cariClicked();
	}
	
	public function DDBulanChanged($sender,$param)
	{				
		$this->cariClicked();
	}

	public function DDPerusAsuransiChanged($sender,$param)
	{				
		$this->cariClicked();
	}
}
?>
