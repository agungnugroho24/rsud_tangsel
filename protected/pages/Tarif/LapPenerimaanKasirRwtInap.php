<?php
class LapPenerimaanKasirRwtInap extends SimakConf
{   
	private $sortExp = "no_trans ";
    private $sortDir = "DESC";
    private $offset = 0;
    private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	 public function onPreRender($param)
	{
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack)
		{	
			$modeInput = $this->collectSelectionResult($this->modeInput);
			$this->setViewState('modeInput',$modeInput);	
		}
		
			//$this->notrans->Text = $this->getViewState('modeInput');
	}
		
    public function onLoad($param)
	{
		parent::onLoad($param);		
			
		if(!$this->IsPostBack)
		{		
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$this->DDPoli->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDPoli->dataBind();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
			
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			
			$sql="SELECT real_name, nip, allow FROM tbd_user";
			$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
			foreach($arr as $row)
			{
				$arrApp=array();
				$var=$row['allow'];
				$arrApp = explode(',', $var);
				
				if (in_array('2', $arrApp))
				{
					$data[]=array('nip'=>$row['nip'],'nama'=>$row['real_name']);
				}	
			}
			
			$this->DDKasir->DataSource = $data;
			$this->DDKasir->dataBind();
			
			$this->bindGrid();	
			
			$this->cetakBtn->Enabled = false;
		}
				
										
		//$this->cariCM->focus();					
		$position='TopAndBottom';		
		$this->dtgSomeData->PagerStyle->Position=$position;
		$this->dtgSomeData->PagerStyle->Visible=true;
		
	
	}
	
	public function modeInputChanged($sender, $param)
	{
		$this->clearViewState('modeInput');
		
		$modeInput = $this->collectSelectionResult($this->modeInput);
		if($modeInput == '0') //mode global
		{
			$this->setViewState('modeInput',$modeInput);	
		}
		elseif($modeInput == '1') //mode tunai
		{
			$this->setViewState('modeInput',$modeInput);
		}
		elseif($modeInput == '2') //mode piutang
		{
			$this->setViewState('modeInput',$modeInput);
		}
		
		$this->notrans->focus();
		$this->cariClicked();
	}	
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


    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {	
			$modeInput=$this->getViewState('modeInput');
			if($modeInput == '0') //mode global
			{
				$mode = "global";
			}
			elseif($modeInput == '1') //mode tunai
			{
				$mode = "tunai";
			}
			elseif($modeInput == '2') //mode piutang
			{
				$mode = "piutang";
			}
			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			/*
            $sql = "SELECT 
						no_trans,
						cm,
						tgl_masuk,
						tgl_keluar,
						nama,
						lama_inap,
						hrg_kamar,
						status,
						jml_jasa_kamar,						
						jml_operasi_billing,
						jml_penunjang,
						jml_obat_alkes_kredit,
						jml_obat_alkes_tunai_lunas,
						jml_obat_alkes_tunai_piutang,
						jml_total_biaya_lab_rad_kredit,
						jml_total_biaya_lab_rad_tunai_lunas,
						jml_total_biaya_lab_rad_tunai_piutang,
						jml_total_biaya_alih,
						jml_biaya_askep,
						jml_biaya_askeb,
						jml_biaya_askep_ok,												
						jml_biaya_adm,
						jml_biaya_oksigen,
						
						(jml_obat_alkes_kredit
							+jml_total_biaya_lab_rad_kredit
							+jml_total_biaya_alih
							+jml_biaya_askep
							+jml_biaya_askeb
							+jml_biaya_askep_ok
						) AS jml_biaya_lain,
						
						'$mode' AS mode
							
					FROM 
						view_lap_terima_rwtinap 
					  WHERE 
					  	view_lap_terima_rwtinap.cm <> ''";					  			
			*/
			
			$sql ="SELECT 
					  tbt_rawat_inap.no_trans,
					  tbt_rawat_inap.cm,
					  tbd_pasien.nama AS nm_pasien,
					  tbt_rawat_inap.tgl_masuk,
					  tbt_rawat_inap.tgl_keluar,
					  tbt_rawat_inap.st_rujukan,
					  tbt_rawat_inap.kelas AS id_kelas,
					  tbm_kamar_kelas.nama AS nm_kelas,
					  tbt_rawat_inap.jenis_kamar AS id_kamar,
					  tbm_kamar_nama.nama AS nm_kamar,
					  tbt_rawat_inap.dokter AS id_dokter,
					  tbd_pegawai.nama AS nm_dokter,
					  tbd_pasien.kelompok AS id_kelompok,
					  tbm_kelompok.nama AS nm_kelompok,
					  tbt_rawat_inap.kasir AS id_kasir,
					  tbd_user.real_name AS nm_kasir
					FROM
					  tbt_rawat_inap
					  INNER JOIN tbd_pasien ON (tbt_rawat_inap.cm = tbd_pasien.cm)
					  LEFT OUTER JOIN tbm_kamar_kelas ON (tbt_rawat_inap.kelas = tbm_kamar_kelas.id)
					  LEFT OUTER JOIN tbm_kamar_nama ON (tbt_rawat_inap.jenis_kamar = tbm_kamar_nama.id)
					  LEFT OUTER JOIN tbd_pegawai ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					  LEFT OUTER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id)
					  INNER JOIN tbd_user ON (tbt_rawat_inap.kasir = tbd_user.nip)
					WHERE
					  tbt_rawat_inap.`status` = '1'";
			
			if($this->notrans->Text != '')	
			{
				$cariCm=$this->notrans->Text;
				$sql .= "AND tbt_rawat_inap.cm = '$cariCm' ";		
			}			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND tbt_rawat_inap.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('cariKasir') <> '')
			{
				$cariKasir=$this->getViewState('cariKasir');
				$sql .= "AND tbt_rawat_inap.kasir = '$cariKasir' ";
			}
			
			if($this->getViewState('urutBy') <> '')
			{
				$urutBy=$this->getViewState('urutBy');
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			}
			
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->getViewState('cariTgl') <> '')
				{
					$this->txtPeriode->Text='Periode : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
			
					$cariTgl=$this->getViewState('cariTgl');
					$sql .= "AND tbt_rawat_inap.tgl_keluar = '$cariTgl' ";
				}
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
				{
					$this->txtPeriode->Text='Periode : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				
					$cariTglAwal=$this->getViewState('cariTglAwal');
					$cariTglAkhir=$this->getViewState('cariTglAkhir');
					$sql .= "AND tbt_rawat_inap.tgl_keluar BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
				{
					$this->txtPeriode->Text='Periode : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
					
					$cariBln=$this->getViewState('cariBln');
					$cariThn=$this->getViewState('cariThn');
					$sql .= "AND MONTH (tbt_rawat_inap.tgl_keluar)='$cariBln' AND YEAR(tbt_rawat_inap.tgl_keluar)='$cariThn' ";
				}
				/*
				else
				{
					$cariBln=date('m');
					$cariThn=date('Y');
					$sql .= "AND MONTH (view_lap_terima_rwtinap.tgl_keluar)='$cariBln' AND YEAR(view_lap_terima_rwtinap.tgl_keluar)='$cariThn' ";
				}
				*/
			}
			
			else
			{
				$cariBln=date('m');
				$cariThn=date('Y');
				
				$this->txtPeriode->Text='Periode : '.$this->namaBulan($cariBln).' '.$cariThn;
				$sql .= "AND MONTH (tbt_rawat_inap.tgl_keluar)='$cariBln' AND YEAR(tbt_rawat_inap.tgl_keluar)='$cariThn' ";
			}
			
		
			/*
			if($this->getViewState('modeInput') <> '0')
			{
				$modeInput=$this->getViewState('modeInput');
				if($modeInput == '1') //mode tunai
				{
					//$sql .= "AND view_lap_terima_rwtinap.status = '1' ";
				}
				elseif($modeInput == '2') //mode piutang
				{
					$sql .= "AND view_lap_terima_rwtinap.status = '0' ";
				}				
			}
			*/
			
			//$sql .= "AND view_lap_terima_rwtinap.status = '1' ";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
			
			$this->setViewState('sql',$sql);
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
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
        
        
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function changePagerPosition($sender,$param)
	{		
		$position='TopAndBottom';		
		$this->UserGrid->PagerStyle->Position=$position;
		$this->UserGrid->PagerStyle->Visible=true;		
	}
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$noTrans = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$cm = $item->DataItem['cm'];	
			$kelas = $item->DataItem['id_kelas'];	
			$tglKeluar = $item->DataItem['tgl_keluar'];	
			$stRujukan = $item->DataItem['st_rujukan'];		
			
			$item->tglKeluar->Text = $this->convertDate($tglKeluar,'3');
			
			//----------- Hitung Lama Inap --------------------
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
			$arr = $this->queryAction($sql,'S');
			$jmlDataInapKmr = count($arr);
			$counter = 1;
			foreach($arr as $row)
			{
				$lamaInap += $row['lama_inap'];
				/*
				if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
				{
					$tglMasuk = $row['tgl_awal'];
					$wktMasuk = $row['wkt_masuk'];
						
					$tglKeluar = date('Y-m-d');
					$wktKeluar = date('G:i:s');
					$lamaInap += $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				}
				$counter++;
				*/
			}
			$lamaInapTotal = $lamaInap;	
			$item->lamaInap->Text = $lamaInapTotal.' hr';
			
			
			//------------------------------- BHP KHUSUS RWT INAP ------------------
				if($lamaInapTotal <= 3) //kurang atau sama dengan 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgBhp = 150000;
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgBhp = 125000;
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgBhp = 125000;
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgBhp = 100000;
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgBhp = 70000;
						}
					}
					else //lebih dari 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgBhp = (150000 + (150000 * 0.5));
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgBhp = (125000 + (125000 * 0.5));
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgBhp = (125000 + (125000 * 0.5));
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgBhp = (100000 + (100000 * 0.5));
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgBhp = (70000 + (70000 * 0.5));
						}
					}
				
				$this->setViewState('bhp',$hrgBhp);
				//$this->showSql->Text = $kelas.' - '.$hrgBhp;
				//$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
				//$askep = $lamaInapTotal * $tarifAskep;				
				//$this->setViewState('askep',$askep);
				
				$jmlSisaPlafon = 0;
				
				//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND st='1'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//jika pasien ambil paket
				{
					foreach($arr as $row)
					{
						$stPaket = '1';
						$idPaket=$row['id_opr'];
						$lamaHariPaket=OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',$idPaket,$kelas)->lama_hari;
					}		
				}
				else
				{
					$stPaket = '0';
				}
				
//------------------------------ MODE AMBIL PAKET ------------------------------ 				
				if($stPaket == '1')
				{
					$sql = "SELECT *
							   FROM view_inap_operasi_billing				
							   WHERE cm='$cm'
							   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$tarif_obgyn = $row['tarif_obgyn'];
						$tarif_anastesi = $row['tarif_anastesi'];
						$tarif_anak = $row['tarif_anak'];
						$tarif_assdktr = $row['tarif_assdktr'];
						$visite_dokter_obgyn = $row['visite_dokter_obgyn'];
						$visite_dokter_anak = $row['visite_dokter_anak'];
						$sewa_ok = $row['sewa_ok'];
						$obat = $row['obat'];
						$ctg = $row['ctg'];
						$jpm = $row['jpm'];
						$lab = $row['lab'];
						$ambulan = $row['ambulan'];
						$kamar_ibu = $row['kamar_ibu'];
						$kamar_bayi = $row['kamar_bayi'];
						$js_bidan_pengirim = $row['js_bidan_pengirim'];
						$adm = $row['adm'];
						$materai = $row['materai'];
					}
					$this->setViewState('metrai',$materai);
					
					
					//bandingkan lama_hari_paket dengan lama hari aktual
					if($lamaInapTotal <= $lamaHariPaket) //ambil harga paket
					{
						//$lamaInapTotal = $lamaHariPaket;
						//$this->lamaInap->Text = $lamaInapTotal.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$jmlJsKmrPaket = $kamar_ibu + $kamar_bayi;						
						$jpmPaket = $jpm;
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
						$arr = $this->queryAction($sql,'S');
						$jmlDataInapKmr = count($arr);
						$counter = 1;
						foreach($arr as $row)
						{
							$kelas = $row['id_kmr_awal'];
							$tglMasuk = $row['tgl_awal'];
							$tglKeluar = $row['tgl_kmr_ubah'];
							$wktMasuk = $row['wkt_masuk'];
							$wktKeluar = $row['wkt_keluar'];
							$lamaInap = $row['lama_inap'];
							
							$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
							/*						
							if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
							{
								$tglKeluar = date('Y-m-d');
								$wktKeluar = date('G:i:s');
								$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
							}
							*/
							$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
							$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
							
							
							$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
							
							$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
							$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
							
							//$this->showSql->Text .= $lamaInap.' ';
							
							$counter++;
						}
						
						$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						if($jmlJsKmr <= $jmlJsKmrPaket)
						{
							$jmlJsKmrSisa = $jmlJsKmrPaket - $jmlJsKmr;
						}
						else
						{
							$jmlJsKmrSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlJsKmrSisa;
						$this->setViewState('jmlSisaKamar',$jmlJsKmrSisa);	
						
						
						if($jpm <= $jpmPaket)
						{
							$jpmSisa = $jpmPaket - $jpm;
						}
						else
						{
							$jpmSisa = 0;
						}
						
						$jmlSisaPlafon += $jpmSisa;
						$this->setViewState('jmlSisaJpm',$jpmSisa);	
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						$jmlPenunjangPaket = $jmlVisiteAktual;
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, masukan ke sisaPlafon
						{ 
							//$jmlPenunjangPaket = $jmlVisitePaket;							
							$jmlPenunjangPaketSisa = $jmlVisitePaket - $jmlVisiteAktual;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaketSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlPenunjangPaketSisa;
						$this->setViewState('jmlSisaVisite',$jmlPenunjangPaketSisa);
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
						//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatAktual = $row['jumlah'];
						}		
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, masukan ke sisaPlafon
						{ 
							//$jmlObatAlkes = $obat - $jmlObatLunas;
							$jmlObatAlkesSisa = ($obat - $jmlObatLunas) - ($jmlObatAktual - $jmlObatLunas);
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkesSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlObatAlkesSisa;
						$this->setViewState('jmlSisaObat',$jmlObatAlkesSisa);
						
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;						
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, masukan ke sisaPlafon
						{ 
							//$jmlBiayaLainLab = $lab - $jmlLabLunas;
							$jmlBiayaLainLabSisa = ($lab - $jmlLabLunas) - ($jmlLabAktual - $jmlLabLunas);
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLabSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlBiayaLainLabSisa;
						$this->setViewState('jmlSisaLab',$jmlBiayaLainLabSisa);
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}			
						
						//----------- hitung Biaya Sinar jika ada ---------------------
						if(InapSinarRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_sinar			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaSinar = $row['jumlah']; //dikalikan lama hari
							}
						}			
						
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						$jmlBiayaAlih = 0;
			
						//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
						if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
						{
							$sql = "SELECT SUM(jml) AS jumlah
										   FROM view_biaya_alih
										   WHERE no_trans_inap = '$noTrans'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAlih=$row['jumlah'];
							}
						}	
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						//$jnsRujuk = $tmpPasien->st_rujukan;
						if($stRujukan=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
						
					}
					else //ambil harga aktual
					{
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
						$arr = $this->queryAction($sql,'S');
						$jmlDataInapKmr = count($arr);
						$counter = 1;
						foreach($arr as $row)
						{
							$kelas = $row['id_kmr_awal'];
							$tglMasuk = $row['tgl_awal'];
							$tglKeluar = $row['tgl_kmr_ubah'];
							$wktMasuk = $row['wkt_masuk'];
							$wktKeluar = $row['wkt_keluar'];
							$lamaInap = $row['lama_inap'];
							
							$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
							/*						
							if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
							{
								$tglKeluar = date('Y-m-d');
								$wktKeluar = date('G:i:s');
								$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
							}
							*/
							$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
							$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
							
							
							$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
							
							$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
							$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
							
							//$this->showSql->Text .= $lamaInap.' ';
							
							$counter++;
						}
						
						$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						$jmlPenunjangPaket = $jmlVisiteAktual;
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, masukan ke sisaPlafon
						{ 
							//$jmlPenunjangPaket = $jmlVisitePaket;							
							$jmlPenunjangPaketSisa = $jmlVisitePaket - $jmlVisiteAktual;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaketSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlPenunjangPaketSisa;
						$this->setViewState('jmlSisaVisite',$jmlPenunjangPaketSisa);	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
						//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatAktual = $row['jumlah'];
						}		
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, masukan ke sisaPlafon
						{ 
							//$jmlObatAlkes = $obat - $jmlObatLunas;
							$jmlObatAlkesSisa = ($obat - $jmlObatLunas) - ($jmlObatAktual - $jmlObatLunas);
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkesSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlObatAlkesSisa;
						$this->setViewState('jmlSisaObat',$jmlObatAlkesSisa);
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
						
						$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;						
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, masukan ke sisaPlafon
						{ 
							//$jmlBiayaLainLab = $lab - $jmlLabLunas;
							$jmlBiayaLainLabSisa = ($lab - $jmlLabLunas) - ($jmlLabAktual - $jmlLabLunas);
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLabSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlBiayaLainLabSisa;
						$this->setViewState('jmlSisaLab',$jmlBiayaLainLabSisa);
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')

									   AND st_bayar = '0'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						//----------- hitung Biaya Sinar jika ada ---------------------
						if(InapSinarRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_sinar			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaSinar = $row['jumlah']; //dikalikan lama hari
							}
						}			
		
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						$jmlBiayaAlih = 0;
			
						//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
						if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
						{
							$sql = "SELECT SUM(jml) AS jumlah
										   FROM view_biaya_alih
										   WHERE no_trans_inap = '$noTrans'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAlih=$row['jumlah'];
							}
						}	
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						$jnsRujuk = $tmpPasien->st_rujukan;
						if($stRujukan=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
					}
				}
				
				
				
				if($stPaket == '0')
				{
					//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
					$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
					$arr = $this->queryAction($sql,'S');
					$jmlDataInapKmr = count($arr);
					$counter = 1;
					foreach($arr as $row)
					{
						$kelas = $row['id_kmr_awal'];
						$tglMasuk = $row['tgl_awal'];
						$tglKeluar = $row['tgl_kmr_ubah'];
						$wktMasuk = $row['wkt_masuk'];
						$wktKeluar = $row['wkt_keluar'];
						$lamaInap = $row['lama_inap'];
						
						$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
												/*
						if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
						{
							$tglKeluar = date('Y-m-d');
							$wktKeluar = date('G:i:s');
							$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
						}
						*/
						$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
						$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
						
						
						$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
						
						$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
						$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
						
						//$this->showSql->Text .= $lamaInap.' ';
						
						$counter++;
					}
					
					$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
					
					$jpm = $jmlJpmIbu + $jmlJpmBayi; 
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$cm'
								   AND no_trans_inap = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0 ";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAlkes=$row['jumlah'];
					}		
					
					//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_inap_operasi_billing				
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlTngAhli=$row['jumlah'];
					}	
					
					//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlPenunjang=$row['jumlah'];
					}	
					
					//----------- hitung Biaya Lain-Lain lab & rad & fisio yg st_bayar=0 (bayar kredit) ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_lain_lab_rad			
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaLainLabRad=$row['jumlah'];
					}
					
					//----------- hitung Biaya Askep OK jika ada ---------------------
					if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
					{
						$sql = "SELECT SUM(tarif) AS jumlah
									   FROM tbt_inap_askep_ok			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAskepOk=$row['jumlah'];
						}
					}
					
					//----------- hitung Biaya Askeb jika ada ---------------------
					if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
					{
						$sql = "SELECT SUM(tarif) AS jumlah
									   FROM tbt_inap_askeb			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAskeb= $lamaInapTotal * $row['jumlah']; //dikalikan lama hari
						}
					}			
					
					//----------- hitung Biaya Oksigen jika ada ---------------------
					if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
					{
						$sql = "SELECT tarif AS jumlah
									   FROM tbt_inap_oksigen			
									   WHERE 
										no_trans = '$noTrans'
										AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
						}
					}			
					
					//----------- hitung Biaya Sinar jika ada ---------------------
						if(InapSinarRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_sinar			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaSinar = $row['jumlah']; //dikalikan lama hari
							}
						}
	
					//----------- hitung Biaya Ambulan jika ada ---------------------
					if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
					{
						$sql = "SELECT tarif AS jumlah
									   FROM tbt_inap_ambulan			
									   WHERE 
										no_trans = '$noTrans'
										AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
						}
					}
					
					
					$jmlBiayaAlih = 0;
			
					//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
					if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
					{
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_alih
									   WHERE no_trans_inap = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAlih=$row['jumlah'];
						}
					}	
				}
				
				//$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaAlih;
				$jmlBiayaLain = $jmlBiayaLainLabRad + 
								$askep + 
								$jmlBiayaAskepOk + 
								$jmlBiayaAskeb + 
								$jmlBiayaOksigen + 
								$jmlBiayaSinar+  
								$jmlBiayaAmbulan + 
								$jmlBiayaAlih +
								$hrgBhp
								;
				
				//Sisa Plafon
				$jmlTngAhli += $jmlSisaPlafon;
								
				//----------- hitung Biaya TOTAL ---------------------				
				$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jpm+$jmlPenunjang+$jmlBiayaLain;		 	
				$jmlTotal=$Total+$biayaAdm+$this->getViewState('metrai');
				
				$item->biayaKamar->Text = number_format($jmlJsKmr,'2',',','.');
				$item->biayaObat->Text = number_format($jmlObatAlkes,'2',',','.');
				$item->biayaOperasi->Text = number_format($jmlTngAhli,'2',',','.');
				
				$item->biayaTotal->Text = number_format($jmlTotal,'2',',','.');
				
				/*
				$this->jsRS->Text='Rp. '.number_format($jmlJsKmr,'2',',','.');
				$this->jsAhli->Text='Rp. '.number_format($jmlTngAhli,'2',',','.');
				$this->jpm->Text='Rp. '.number_format($jpm,'2',',','.');
				$this->jsPenunjang->Text='Rp. '.number_format($jmlPenunjang,'2',',','.');
				$this->jsObat->Text='Rp. '.number_format($jmlObatAlkes,'2',',','.');
				$this->jsLain->Text='Rp. '.number_format($jmlBiayaLain,'2',',','.');
				
				$this->biayaAdm->Text='Rp. '.number_format($biayaAdm,'2',',','.');
				$this->biayaMetrai->Text='Rp. '.number_format($this->getViewState('metrai'),'2',',','.');
				
				$jmlTotal=$Total+$biayaAdm+$this->getViewState('metrai');
				*/
		}
    }
	
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			KasirRwtJlnRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	
	public function cariClicked()
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		
		$orderBy=$this->getViewState('orderBy');	
		
		if($this->notrans->Text){
			$this->setViewState('cariByCm', $this->notrans->Text);			
		}else{
			$this->clearViewState('cariByCm');	
		}	
		
		/*
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);			
		}else{
			$this->clearViewState('cariByNama');	
		}					
		*/
				
		if($this->DDPoli->SelectedValue) {
			$this->setViewState('cariPoli',$this->DDPoli->SelectedValue);
		}else{
			$this->clearViewState('cariPoli');	
		}
		
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('cariDokter');	
		}
		
		if($this->DDKasir->SelectedValue) {
			$this->setViewState('cariKasir',$this->DDKasir->SelectedValue);
		}else{
			$this->clearViewState('cariKasir');	
		}
		
		if($this->DDUrut->SelectedValue) {
			$this->setViewState('urutBy',$this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
		
		if($this->tgl->Text){
			$this->setViewState('cariTgl',$this->ConvertDate( $this->tgl->Text,2));
		}else{
			$this->clearViewState('cariTgl');	
		}	
		
		if($this->tglawal->Text){
			$this->setViewState('cariTglAwal',$this->ConvertDate( $this->tglawal->Text,2));
		}else{
			$this->clearViewState('cariTglAwal');	
		}	
		
		if($this->tglakhir->Text){
			$this->setViewState('cariTglAkhir',$this->ConvertDate( $this->tglakhir->Text,2));
		}else{
			$this->clearViewState('cariTglAkhir');	
		}	
		
		if($this->getViewState('cariThn')){
			$cariThn = $this->getViewState('cariThn');
		}else{
			$this->clearViewState('cariThn');
		}		
		
		if($this->getViewState('cariBln')){
			$cariBln = $this->getViewState('cariBln');
		}else{
			$this->clearViewState('cariBln');
		}		
		
		
		$this->bindGrid();	
		
		$this->dataGrid->Visible=true;
		
		if($this->getViewState('pilihPeriode')==='1')
		{
			$this->txtPeriode->Text='Periode : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
		}
		elseif($this->getViewState('pilihPeriode')==='2')
		{
			$this->txtPeriode->Text='Periode : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
		}
		elseif($this->getViewState('pilihPeriode')==='3')
		{
			if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
			{
				$this->txtPeriode->Text='Periode : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
			}
			/*
			else
			{
				$this->txtPeriode->Text='Periode : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			*/
			
		}
		/*
		else
		{
			$this->txtPeriode->Text='Periode : '.$this->namaBulan(date('m')).' '.date('Y');
		}
		*/
	}
		
	
	public function DDPoliChanged($sender,$param)
	{				
		$this->DDDokter->focus();
		
		$this->cariClicked();
	}
	
	public function DDKasirChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
		
		$this->cariClicked();
	}
	
	public function DDDokterChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
		
		$this->cariClicked();
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->DDUrut->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->DDKontrak->SelectedValue = '';
			$this->DDKontrak->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			if($this->DDUrut->SelectedValue == '02' || $this->DDUrut->SelectedValue == '07')
			{
				$idKelPerus = $this->DDUrut->SelectedValue;
				
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDKontrak->DataSource = $data;
				$this->DDKontrak->dataBind();
				
				$this->DDKontrak->Enabled = true;	
			}
			else
			{
				$this->DDKontrak->SelectedValue = '';
				$this->DDKontrak->Enabled = false;
			}
		}
		$this->cariClicked();						
	}
	
	public function DDKontrakChanged($sender,$param)
	{		
		$this->cariClicked();				
	}
	
	public function ChangedDDberdasarkan($sender,$param)
	{
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->bulan->visible=true;
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->DDbulan->focus();
			//$this->cetakBtn->Enabled = true;
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->minggu->visible=true;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tglawal->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			//$this->cetakBtn->Enabled = true;			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->minggu->visible=false;
			$this->hari->visible=true;
			$this->bulan->visible=false;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tgl->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			//$this->cetakBtn->Enabled = true;
		}
		else
		{
			$this->clearViewState('pilihPeriode');			
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=false;		
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			//$this->cetakBtn->Enabled = false;
			
			$this->txtPeriode->Text='';
			$this->cariClicked();
		}
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariBln');
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
		}			
		else
		{
			$this->DDtahun->Enabled=true;
			$this->DDtahun->focus();
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariBln');
			//$this->setViewState('idBulan',$pilih);
			
			$cariBln = $this->DDbulan->SelectedValue;
			$this->setViewState('cariBln',$cariBln);
		}
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		if ($pilih=='')
		{
			//$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
		}			
		else
		{
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			//$this->setViewState('idBulan',$pilih);
			
			$cariThn = $pilih;
			$this->setViewState('cariThn',$cariThn);
		}
		
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
	
	public function cetakClicked($sender,$param)
	{	
		if($this->IsValid)
		{
			
			$session=new THttpSession;
			$session->open();
			$session['cetakPenerimaanKasirRwtInap'] = $this->getViewState('sql');
		
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtInapXls',
				array(
					'cm'=>$this->notrans->Text,
					'dokter'=>$this->DDDokter->SelectedValue,
					'kasir'=>$this->DDKasir->SelectedValue,
					'kelompok'=>$this->DDUrut->SelectedValue,
					'perusahaan'=>$this->DDKontrak->SelectedValue,
					'tgl'=>$this->getViewState('cariTgl'),
					'tglawal'=>$this->getViewState('cariTglAwal'),
					'tglakhir'=>$this->getViewState('cariTglAkhir'),
					'cariBln'=>$this->getViewState('cariBln'),
					'cariThn'=>$this->getViewState('cariThn'),
					'periode'=>$this->txtPeriode->Text,
					'modeInput'=>$this->getViewState('modeInput'))));
		
		}
	}
			
	protected function refreshMe()
	{
		$this->Reload();
	}
	/*
	public function hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar)
	{
		/* ---------------- RULES --------------------------
			jam masuk > 0 detik = 1 hari
			jam masuk > 24 jam = 2 hari
			jam masuk > 1 menit && jam masuk <= 24 jam = 1 hari
		*/
	/*	       
        //convert to unix timestamp		
		list($G,$i,$s) = explode(":",$wktMasuk);
		list($Y,$m,$d) = explode("-",$tglMasuk);
		$wktAwal = mktime($G,$i,$s,$m,$d,$Y);
		
		list($G,$i,$s) = explode(":",$wktKeluar);
		list($Y,$m,$d) = explode("-",$tglKeluar);
		$wktAkhir = mktime($G,$i,$s,$m,$d,$Y);

        $offset = $wktAkhir-$wktAwal;

        $jmlHari = ceil($offset/60/60/24); //pembulatan ke atas
        return $jmlHari;
	}
	*/
}

?>
