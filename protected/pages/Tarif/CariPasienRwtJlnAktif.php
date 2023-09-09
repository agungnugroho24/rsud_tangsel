<?php
//MODUL PENCARIAN PASIEN RAWAT JALAN YANG BERSTATUS OPEN/BELUM MEMBAYAR

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class CariPasienRwtJlnAktif extends SimakConf
{   
	private $sortExp = "no_trans";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
		
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
			
	public function onPreRender($param)
	{				
		parent::onPreRender($param);
						
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAll();
			$this->DDKab->dataBind();
			
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDPerusAsuransi->dataBind();
			$this->DDPerusAsuransi->Enabled = false;
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			
			if($this->Request['jnsPasien'] != '')
			{
				$jnsPasien = $this->Request['jnsPasien'];
				
				if($jnsPasien == '0') //pasien rawat jalan
				{
					$this->DDPoli->Enabled = true;
					$this->DDDokter->Enabled = false;
				}
				else
				{
					$this->DDPoli->Enabled = false;
					
					
					if($jnsPasien == '1') //pasien rawat jalan
					{
						$sql = "SELECT 
									  tbd_pegawai.id,
									  tbd_pegawai.nama
									FROM
									  tbd_pegawai
									  INNER JOIN tbm_poliklinik ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
									WHERE
									  tbd_pegawai.kelompok = 1 
									ORDER BY nama ASC  ";
						
						$this->DDDokter->DataSource = $this->queryAction($sql,'S');
						$this->DDDokter->dataBind();
						$this->DDDokter->Enabled = true;
					}
					else //pasien luar
					{
						$this->DDDokter->Enabled = false;
					}
				}
			}
			
			
			
			//$this->bindGrid();								
			$this->cariPanel->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
		}		
    }		
	
	public function panelCallBack($sender,$param)
	{
		/*
		$this->gridPanel->render($param->getNewWriter());
		$this->gridPanel->render($this->Response->createHtmlWriter(null));
		foreach($this->dtgSomeData->Items as $item)
		{
			$item->cmColumn->tes->render($this->Response->createHtmlWriter(null));
		}
		*/
	}
	
	public function selectionChangedKelompok()
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
				$this->DDPerusAsuransi->Enabled = true;	
				$idKelPerus = $this->DDUrut->SelectedValue;
				
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerusAsuransi->DataSource = $data;
				$this->DDPerusAsuransi->dataBind();
			}
			else
			{
				$this->DDPerusAsuransi->SelectedValue = 'empty';
				$this->DDPerusAsuransi->Enabled = false;
			}
		}
		
		$this->cariClicked();
	}
	
	public function DDPoliChanged($sender,$param)
	{
		$this->DDDokter->Enabled = true;
		$this->DDDokter->focus();
		$idKlinik = $this->DDPoli->SelectedValue;
		
		if($idKlinik == '07'){
		$sql = "SELECT 
				  tbd_pegawai.id,
				  tbd_pegawai.nama
				FROM
				  tbd_pegawai
				WHERE
				  tbd_pegawai.kelompok = 1 
				ORDER BY nama ASC ";
		}
		else
		{
			$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbm_poliklinik ON (tbm_poliklinik.id = tbd_pegawai.poliklinik)
					WHERE
					  tbd_pegawai.kelompok = 1 AND 
					  tbm_poliklinik.id = '$idKlinik' 
					ORDER BY nama ASC  ";
		}
		
		$this->DDDokter->DataSource = $this->queryAction($sql,'S');
		$this->DDDokter->dataBind();
		
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
			
			$dateNow = date('Y-m-d');
			$jnsPasien = $this->Request['jnsPasien'];
				
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$sql = "SELECT 
						   tbd_pasien.cm AS cm,
						   tbd_pasien.nama AS nama,
						   tbd_pasien.jkel AS jkel,
						   tbd_pasien.tgl_lahir,
						   tbt_rawat_jalan.no_trans AS no_trans,
						   tbt_rawat_jalan.tgl_visit AS tgl,
						   tbt_rawat_jalan.dokter AS id_dokter,
						   tbd_pegawai.nama AS dokter,
						   tbm_poliklinik.nama AS klinik,
						   tbt_rawat_jalan.id_klinik AS id_klinik,
						   tbm_kabupaten.nama AS kab,
						   tbm_kelompok.nama AS kelompoks					   
						FROM
						  tbt_rawat_jalan
						  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
						  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
						  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
						  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
						  INNER JOIN tbm_kelompok ON (tbt_rawat_jalan.penjamin = tbm_kelompok.id)						  								
						WHERE 
							tbt_rawat_jalan.flag = '0'
							AND tbt_rawat_jalan.st_alih= '0'
							AND tbt_rawat_jalan.tgl_visit = '$dateNow' ";
			}
			elseif($jnsPasien == '1') //pasien rawat inap
			{
				$sql = "SELECT tbd_pasien.cm AS cm,
							   tbd_pasien.nama AS nama,
							   tbd_pasien.jkel AS jkel,
							   tbd_pasien.tgl_lahir,
							   tbt_rawat_inap.no_trans AS no_trans,
							   tbt_rawat_inap.tgl_masuk AS tgl,
							   tbt_rawat_inap.dokter AS id_dokter,
							   tbt_rawat_inap.kelas AS id_kelas,
							   tbm_kamar_kelas.nama AS nm_kelas,
							   tbt_rawat_inap.kamar AS id_ruang,
							   tbm_ruang.nama AS nm_ruang,
							   tbt_rawat_inap.jenis_kamar AS id_jns_kamar,
							   tbd_pegawai.nama AS dokter,
							   tbm_kabupaten.nama AS kab,
							   tbm_kelompok.nama AS kelompok						   
							FROM
							  tbt_rawat_inap
							  INNER JOIN tbd_pasien ON (tbt_rawat_inap.cm = tbd_pasien.cm)
							  INNER JOIN tbd_pegawai ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
							  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
							  INNER JOIN tbm_kelompok ON (tbt_rawat_inap.penjamin = tbm_kelompok.id)
							  INNER JOIN tbm_kamar_kelas ON (tbt_rawat_inap.kelas = tbm_kamar_kelas.id)	
							  INNER JOIN tbm_ruang ON (tbt_rawat_inap.kamar = tbm_ruang.id)						  								
							WHERE tbt_rawat_inap.status = '0' ";
			}
			
				
			if($this->formatCm($this->cariCM->Text) != '')
			{
				$cariCM = $this->formatCm($this->cariCM->Text);
				$sql .= "AND tbd_pasien.cm LIKE '%$cariCM%' ";
			}
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbd_pasien.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbd_pasien.nama LIKE '$nama%' ";
				}
			}
			
			if($this->cariAlamat->Text != '')
			{
				$cariAlamat = $this->cariAlamat->Text;
				$sql .= "AND tbd_pasien.alamat LIKE '%$cariAlamat%' ";
			}
			
			if($this->cariTlp->Text != '')
			{
				$cariTlp = $this->cariTlp->Text;
				$sql .= "AND tbd_pasien.telp LIKE '%$cariTlp%' ";
			}
			
			if($this->cariHp->Text != '')
			{
				$cariHp = $this->cariHp->Text;
				$sql .= "AND tbd_pasien.hp LIKE '%$cariHp%' ";
			}
			
			if($this->cariPj->Text != '')
			{
				$cariPj = $this->cariPj->Text;
				$sql .= "AND tbd_pasien.nm_pj LIKE '%$cariPj%' ";
			}
			
			if($this->DDUrut->SelectedValue != '')
			{
				$DDUrut = $this->DDUrut->SelectedValue;
				$sql .= "AND tbd_pasien.kelompok = '$DDUrut' ";
			}    
			
			if($this->DDPerusAsuransi->SelectedValue != '')
			{
				$DDPerusAsuransi = $this->DDPerusAsuransi->SelectedValue;
				$sql .= "AND tbd_pasien.perusahaan = '$DDPerusAsuransi' ";
			}    
			
			if($this->DDKab->SelectedValue != '')
			{
				$DDKab = $this->DDKab->SelectedValue;
				$sql .= "AND tbd_pasien.kabupaten = '$DDKab' ";
			}    
			
			if($this->DDPoli->SelectedValue != '')
			{
				$DDPoli = $this->DDPoli->SelectedValue;
				$sql .= "AND tbt_rawat_jalan.id_klinik = '$DDPoli' ";
			}   
			
			if($this->DDDokter->SelectedValue != '')
			{
				$DDDokter = $this->DDDokter->SelectedValue;
				$sql .= "AND tbt_rawat_jalan.dokter = '$DDDokter' ";
			}    
			
			if(trim($this->tglMsk->Text) != '')
			{
				$tgl = $this->ConvertDate(trim($this->tglMsk->Text),'2');//Convert date to mysql
				
				if($jnsPasien == '0') //pasien rawat jalan
				{
					$sql .= "AND tbt_rawat_jalan.tgl_visit = '$tgl' ";
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					$sql .= "AND tbt_rawat_inap.tgl_masuk = '$tgl' ";
				}
			}	
			
			if($this->DDBulan->SelectedValue != '')
			{
				$bln = $this->DDBulan->SelectedValue;				
				
				if($jnsPasien == '0') //pasien rawat jalan
				{
					$sql .= "AND MONTH(tbt_rawat_jalan.tgl_visit) = $bln";	
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					$sql .= "AND MONTH(tbt_rawat_inap.tgl_masuk) = $bln";	
				}
			}
			
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$sql .= " GROUP BY tbt_rawat_jalan.no_trans ";
			}
			elseif($jnsPasien == '1') //pasien rawat inap
			{
				$sql .= " GROUP BY tbt_rawat_inap.no_trans ";
			}
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
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
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
		
	public function cariClicked()
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();			
			
		/*
		$cariCM=$this->formatCm($this->cariCM->Text);
		$cariNama=$this->cariNama->Text;
		$tipeCari=$this->Advance->Checked;
		$cariAlamat=$this->cariAlamat->Text;
		$urutBy=$this->DDUrut->SelectedValue;
		$Company=$this->DDKontrak->SelectedValue;
		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariDataPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company)));
		*/
		
	}
	
  
  	public function itemCreated($sender,$param)
    {
		$jnsPasien = $this->Request['jnsPasien'];
		$mode = $this->Request['mode'];
		
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
			$noTrans = $this->dtgSomeData->DataKeys[$item->ItemIndex];			
		
			$item->tglColumn->Text =  $this->convertDate($item->DataItem['tgl'],'3');
			
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
				
				//---------------------------- Transaksi Tindakan Rawat Jalan ----------------------------	
				$sql = "SELECT
					tgl, 
					nama, 
					total,
					no_trans_asal,
					disc
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '0'
					AND jns_trans = 'tindakan rawat jalan' ";
				
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi tindakan rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarTdk += $row['total'] + $row['disc'];
					}
				}
				
				$jmlTagihan += $jmlBayarTdk;	
				
				//---------------------------- Transaksi Laboratorium Rawat Jalan ----------------------------						
				$sql = "SELECT 
						  tbt_rawat_jalan.no_trans,
						  tbt_lab_penjualan.id_tindakan,
						  tbm_lab_kelompok.nama AS kel_tdk,
						  tbm_lab_kategori.jenis AS kateg_tdk,
						  tbm_lab_tindakan.nama AS nm_tdk, ";
				/*
				if($cito=='0'){$sql .= "tbt_lab_penjualan.harga ";}
				else{$sql .= "(2 * tbt_lab_penjualan.harga) AS harga ";}
				*/
				$sql .= "tbt_lab_penjualan.harga ";
				
				$sql .= "FROM
						  tbt_rawat_jalan
						  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
						  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
						  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
						  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
						WHERE
						  tbt_rawat_jalan.no_trans = '$noTrans'
						  AND tbt_rawat_jalan.flag = '0' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarLab += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarLab;
				
				
				//---------------------------- Transaksi Radiologi Rawat Jalan ----------------------------
			
				$sql = "SELECT 
						  tbt_rawat_jalan.no_trans,
						  tbm_rad_tindakan.nama AS nm_tdk,
						  tbm_rad_kelompok.nama AS kel_tdk,
						  tbm_rad_kategori.jenis AS kateg_tdk,  
						  tbt_rad_penjualan.film_size, ";
				/*
				if($cito=='0'){$sql .= "tbt_rad_penjualan.harga ";}
				else{$sql .= "(2 * tbt_rad_penjualan.harga) AS harga ";}	
				*/
				$sql .= "tbt_rad_penjualan.harga ";
				
				$sql .= "FROM
						  tbt_rawat_jalan
						  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
						  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
						  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
						  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
						WHERE
						  tbt_rawat_jalan.no_trans = '$noTrans'
						  AND tbt_rawat_jalan.flag = '0' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarRad += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarRad;
				
				//---------------------------- Transaksi Fisio Rawat Jalan ----------------------------						
				$sql = "SELECT 
						  tbt_rawat_jalan.no_trans,
						  tbt_fisio_penjualan.id_tindakan,
						  tbm_fisio_kelompok.nama AS kel_tdk,
						  tbm_fisio_kategori.jenis AS kateg_tdk,
						  tbm_fisio_tindakan.nama AS nm_tdk, ";
				/*
				if($cito=='0'){$sql .= "tbt_fisio_penjualan.harga ";}
				else{$sql .= "(2 * tbt_fisio_penjualan.harga) AS harga ";}
				*/
				$sql .= "tbt_fisio_penjualan.harga ";
				
				$sql .= "FROM
						  tbt_rawat_jalan
						  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
						  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan.id_tindakan = tbm_fisio_tindakan.kode)
						  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
						  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
						WHERE
						  tbt_rawat_jalan.no_trans = '$noTrans'
						  AND tbt_rawat_jalan.flag = '0' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi fisio rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarFis += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarFis;
				
				//---------------------------- Transaksi Ambulan Rawat Jalan ----------------------------						
				$sql = "SELECT
							tbt_rwtjln_ambulan.id AS id,
							tbt_rwtjln_ambulan.tgl AS tgl,
							tbm_ambulan.nama AS tujuan,
							CONCAT(tbt_rwtjln_ambulan.catatan,'  ',tbt_rwtjln_ambulan.lainnya) AS catatan, ";
				/*
				if($cito=='0'){$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";}
				else{$sql .= "(2 * tbt_rwtjln_ambulan.tarif) AS tarif ";}
				*/
				$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";
				
				$sql .= "FROM
							tbm_ambulan
							Inner Join tbt_rwtjln_ambulan ON tbt_rwtjln_ambulan.tujuan = tbm_ambulan.id
						WHERE 
							tbt_rwtjln_ambulan.no_trans_rwtjln = '$noTrans'
							AND tbt_rwtjln_ambulan.flag = '0' ";
							
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi fisio rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarAmb += $row['tarif'];
					}
				}
				
				$jmlTagihan += $jmlBayarAmb;
				
				//---------------------------- Transaksi Apotik Rawat Jalan ----------------------------
				$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTrans)->penjamin;
				$stAsuransi = RwtjlnRecord::finder()->findByPk($noTrans)->st_asuransi;
				
				if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
				{
					$sql = "SELECT
						no_trans, 
						no_trans_rwtjln, 
						dokter, 
						SUM(total + if(bhp!='',bhp,0)) AS total,
						tgl
					FROM 
						tbt_obat_jual_karyawan
					WHERE 
						cm='$cm' 
						AND no_trans_rwtjln='$noTrans'
						AND flag = '0'
					GROUP BY no_trans_rwtjln ";
				}
				else
				{
					$sql = "SELECT
						no_trans, 
						no_trans_rwtjln, 
						dokter, 
						SUM(total + if(bhp!='',bhp,0)) AS total,
						tgl
					FROM 
						tbt_obat_jual 
					WHERE 
						cm='$cm' 
						AND no_trans_rwtjln='$noTrans'
						AND flag = '0'
					GROUP BY no_trans_rwtjln ";
				}				
				
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi apotik rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarApotik += $row['total'];
					}
				}
				
				$jmlTagihan += $jmlBayarApotik;
				$this->dtgSomeData->Columns[3]->Visible = true;	
			}
			elseif($jnsPasien == '1') //pasien rawat inap bayar tunai
			{
				$this->dtgSomeData->Columns[3]->Visible = false;
				
				$cm = RwtInapRecord::finder()->findByPk($noTrans)->cm;
				
				//---------------------------- Transaksi Laboratorium Rawat Inap Tunai----------------------------						
				$sql = "SELECT 
						  tbt_rawat_inap.no_trans,
						  tbt_lab_penjualan_inap.id_tindakan,
						  tbm_lab_kelompok.nama AS kel_tdk,
						  tbm_lab_kategori.jenis AS kateg_tdk,
						  tbm_lab_tindakan.nama AS nm_tdk, ";
				/*
				if($cito=='0'){$sql .= "tbt_lab_penjualan_inap.harga ";}
				else{$sql .= "(2 * tbt_lab_penjualan_inap.harga) AS harga ";}
				*/
				$sql .= "tbt_lab_penjualan_inap.harga ";
				
				$sql .= "FROM
						  tbt_rawat_inap
						  INNER JOIN tbt_lab_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_lab_penjualan_inap.no_trans_inap)
						  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
						  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
						  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
						WHERE
						  no_trans_inap = '$noTrans'
						  AND flag = '0'
						  AND st_bayar = '1' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarLab += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarLab;
				
				//---------------------------- Transaksi Radiologi Rawat Inap Tunai ----------------------------
				$sql = "SELECT 
						  tbt_rawat_inap.no_trans,
						  tbm_rad_tindakan.nama AS nm_tdk,
						  tbm_rad_kelompok.nama AS kel_tdk,
						  tbm_rad_kategori.jenis AS kateg_tdk,  
						  tbt_rad_penjualan_inap.film_size, ";
				
				/*
				if($cito=='0'){$sql .= "tbt_rad_penjualan_inap.harga ";}
				else{$sql .= "(2 * tbt_rad_penjualan_inap.harga) AS harga ";}	
				*/
				$sql .= "tbt_rad_penjualan_inap.harga ";
				
				$sql .= "FROM
						  tbt_rawat_inap
						  INNER JOIN tbt_rad_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_rad_penjualan_inap.no_trans_inap)
						  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_inap.id_tindakan = tbm_rad_tindakan.kode)
						  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
						  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
						WHERE
						  no_trans_inap = '$noTrans'
						  AND flag = '0'
						  AND st_bayar = '1' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarRad += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarRad;
				
				//---------------------------- Transaksi Fisio Rawat Inap Tunai ----------------------------						
				$sql = "SELECT 
						  tbt_rawat_inap.no_trans,
						  tbt_fisio_penjualan_inap.id_tindakan,
						  tbm_fisio_kelompok.nama AS kel_tdk,
						  tbm_fisio_kategori.jenis AS kateg_tdk,
						  tbm_fisio_tindakan.nama AS nm_tdk, ";
				/*
				if($cito=='0'){$sql .= "tbt_fisio_penjualan_inap.harga ";}
				else{$sql .= "(2 * tbt_fisio_penjualan_inap.harga) AS harga ";}
				*/
				$sql .= "tbt_fisio_penjualan_inap.harga ";
				
				$sql .= "FROM
						  tbt_rawat_inap
						  INNER JOIN tbt_fisio_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_fisio_penjualan_inap.no_trans_inap)
						  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_inap.id_tindakan = tbm_fisio_tindakan.kode)
						  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
						  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
						WHERE
						  no_trans_inap = '$noTrans'
						  AND flag = '0'
						  AND st_bayar = '1' ";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi fisio rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarFis += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarFis;
				
				
				//---------------------------- Transaksi Apotik Rawat Inap Tunai ----------------------------
				$kelompokPasien = RwtInapRecord::finder()->findByPk($noTrans)->penjamin;
				$stAsuransi = RwtInapRecord::finder()->findByPk($noTrans)->st_asuransi;
				
				if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
				{
					$sql = "SELECT
						no_trans, 
						no_trans_inap, 
						dokter, 
						SUM(total + bhp) AS total,
						tgl
					FROM 
						tbt_obat_jual_inap_karyawan
					WHERE 
						cm='$cm' 
						AND no_trans_inap='$noTrans'
						AND flag = '0'
						AND st_bayar = '1'
					GROUP BY no_trans_inap ";
				}
				else
				{
					$sql = "SELECT
						no_trans, 
						no_trans_inap, 
						dokter, 
						SUM(total + bhp) AS total,
						tgl
					FROM 
						tbt_obat_jual_inap
					WHERE 
						cm='$cm' 
						AND no_trans_inap='$noTrans'
						AND flag = '0'
						AND st_bayar = '1'
					GROUP BY no_trans_inap ";
				}
				
				
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi apotik rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarApotik += $row['total'];
					}
				}
				
				$jmlTagihan += $jmlBayarApotik;
			}
						
			$item->totalColumn->Text = number_format($jmlTagihan,2,',','.');	
			
			if($mode!=1)
			{
				if($jmlTagihan > 0 )
				{
					$item->prosesColumn->gridEditBrg->Enabled=true;
				}
				else
				{
					$item->prosesColumn->gridEditBrg->Enabled=false;
				}
				
				$item->prosesColumn2->gridEditBrg2->Enabled=false;
				
				$this->dtgSomeData->Columns[6]->Visible = true;
				$this->dtgSomeData->Columns[7]->Visible = true;
				$this->dtgSomeData->Columns[8]->Visible = false;
			}
			else
			{
				$item->prosesColumn->gridEditBrg->Enabled=false;
				$item->prosesColumn2->gridEditBrg2->Enabled=true;
				$this->dtgSomeData->Columns[6]->Visible = false;
				$this->dtgSomeData->Columns[7]->Visible = false;
				$this->dtgSomeData->Columns[8]->Visible = true;
			}
        }
    }
	
	
	public function pilihClicked($sender,$param)
	{
		$jnsPasien = $this->Request['jnsPasien'];
		$notransUpdate = $this->Request['notransUpdate'];		
		
		$noTrans = $param->CommandParameter;
		
		$jnsPasien = $this->Request['jnsPasien'];
				
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$cm = RwtInapRecord::finder()->findByPk($noTrans)->cm;
		}
		
		if($notransUpdate != '' )
		{
			$this->msg->Text = '    
				<script type="text/javascript">
					window.opener.location="index.php?page=Tarif.BayarKasirRwtJlnDiscount&cm='.$cm.'&noTrans='.$noTrans.'&jnsPasien='.$jnsPasien.'"; 
					self.close();
				</script>';			
		}
				
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();		
	}
	
	public function pilihClicked2($sender,$param)
	{
		$jnsPasien = $this->Request['jnsPasien'];
		$notransUpdate = $this->Request['notransUpdate'];		
		
		$noTrans = $param->CommandParameter;
		
		$jnsPasien = $this->Request['jnsPasien'];
				
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$cm = RwtInapRecord::finder()->findByPk($noTrans)->cm;
		}
		
		if($notransUpdate != '' )
		{
			$this->msg->Text = '    
				<script type="text/javascript">
					window.opener.location="index.php?page=Apotik.penjualanObat&cm='.$cm.'&noTrans='.$noTrans.'&jnsPasien='.$jnsPasien.'"; 
					self.close();
				</script>';			
		}
				
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();		
	}
	
	public function tutupClicked()
	{		
		$this->msg->Text = '    
		<script type="text/javascript">
			self.close();
			window.opener.Element.hide(\'loading\');
		</script>';		
	}
  
}

?>
