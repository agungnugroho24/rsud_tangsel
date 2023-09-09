<?php
//MODUL PENCARIAN PASIEN LAB 

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class CariPasienLab extends SimakConf
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
						   tbt_rawat_jalan.dokter AS id_dokter,
						   tbd_pegawai.nama AS dokter,
						   tbm_poliklinik.nama AS klinik,
						   tbt_rawat_jalan.id_klinik AS id_klinik,
						   tbm_kabupaten.nama AS kab,
						   tbm_kelompok.nama AS kelompok,
						   tbt_lab_penjualan.no_reg,
						   tbt_lab_penjualan.tgl
						FROM
						  tbt_rawat_jalan
						  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
						  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
						  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
						  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
						  INNER JOIN tbm_kelompok ON (tbt_rawat_jalan.penjamin = tbm_kelompok.id)
						  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
						WHERE 
							tbt_lab_penjualan.st_cetak_hasil = '0'";
			}
			elseif($jnsPasien == '1') //pasien rawat inap
			{
				$sql = "SELECT tbd_pasien.cm AS cm,
							   tbd_pasien.nama AS nama,
							   tbd_pasien.jkel AS jkel,
							   tbd_pasien.tgl_lahir,
							   tbt_rawat_inap.no_trans AS no_trans,
							   tbt_rawat_inap.dokter AS id_dokter,
							   tbt_rawat_inap.kelas AS id_kelas,
							   tbm_kamar_kelas.nama AS nm_kelas,
							   tbt_rawat_inap.kamar AS id_ruang,
							   tbm_ruang.nama AS nm_ruang,
							   tbt_rawat_inap.jenis_kamar AS id_jns_kamar,
							   tbd_pegawai.nama AS dokter,
							   tbm_kabupaten.nama AS kab,
							   tbm_kelompok.nama AS kelompok,
							   tbt_lab_penjualan_inap.no_reg,
							   tbt_lab_penjualan_inap.tgl
							FROM
							  tbt_rawat_inap
							  INNER JOIN tbd_pasien ON (tbt_rawat_inap.cm = tbd_pasien.cm)
							  INNER JOIN tbd_pegawai ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
							  LEFT JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id)
							  INNER JOIN tbm_kelompok ON (tbt_rawat_inap.penjamin = tbm_kelompok.id)
							  INNER JOIN tbm_kamar_kelas ON (tbt_rawat_inap.kelas = tbm_kamar_kelas.id)	
							  INNER JOIN tbm_ruang ON (tbt_rawat_inap.kamar = tbm_ruang.id)
							  INNER JOIN tbt_lab_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_lab_penjualan_inap.no_trans_inap)
							WHERE 
								tbt_rawat_inap.status = '0' 
								AND tbt_lab_penjualan_inap.st_cetak_hasil = '0'";
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
					$sql .= "AND tbt_lab_penjualan.tgl = '$tgl' ";
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					$sql .= "AND tbt_lab_penjualan_inap.tgl = '$tgl' ";
				}
				elseif($jnsPasien == '2') //pasien luar
				{
					$sql .= "AND tbt_lab_penjualan_lain.tgl = '$tgl' ";
				}
			}	
			
			if($this->DDBulan->SelectedValue != '')
			{
				$bln = $this->DDBulan->SelectedValue;				
				
				if($jnsPasien == '0') //pasien rawat jalan
				{
					$sql .= "AND MONTH(tbt_lab_penjualan.tgl) = $bln";	
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					$sql .= "AND MONTH(tbt_lab_penjualan_inap.tgl) = $bln";	
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					$sql .= "AND MONTH(tbt_lab_penjualan_lain.tgl) = $bln";	
				}
			}
			
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$sql .= " GROUP BY tbt_lab_penjualan.no_reg ";
			}
			elseif($jnsPasien == '1') //pasien rawat inap
			{
				$sql .= " GROUP BY tbt_lab_penjualan_inap.no_reg ";
			}
			elseif($jnsPasien == '1') //pasien rawat inap
			{
				$sql .= " GROUP BY tbt_lab_penjualan_lain.no_reg ";
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
		
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
			$noReg = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$noTrans = $item->DataItem['no_trans'];
		
			$item->tglColumn->Text =  $this->convertDate($item->DataItem['tgl'],'3');
			
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
								
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
						  tbt_lab_penjualan.no_reg = '$noReg'
						  AND tbt_lab_penjualan.st_cetak_hasil = '0'
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
						  tbt_lab_penjualan_inap.no_reg = '$noReg' 
						  AND tbt_lab_penjualan_inap.st_cetak_hasil = '0'";
						  
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					foreach($arrData as $row)
					{
						$jmlBayarLab += $row['harga'];
					}
				}
				
				$jmlTagihan += $jmlBayarLab;
			}
						
			$item->totalColumn->Text = number_format($jmlTagihan,2,',','.');	
			
			if($jmlTagihan > 0 )
			{
				$item->prosesColumn->gridEditBrg->Enabled=true;
			}
			else
			{
				$item->prosesColumn->gridEditBrg->Enabled=false;
			}
			
        }
    }
	
	
	public function pilihClicked($sender,$param)
	{
		$jnsPasien = $this->Request['jnsPasien'];
		$notransUpdate = $this->Request['notransUpdate'];		
		
		$noReg = $param->CommandParameter;
		
		$jnsPasien = $this->Request['jnsPasien'];
				
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$sql = "SELECT * FROM tbt_lab_penjualan WHERE no_reg = '$noReg' ";	
			$noTrans = LabJualRecord::finder()->findBySql($sql)->no_trans_rwtjln;	
			$cm = LabJualRecord::finder()->findBySql($sql)->cm;	
			//$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$sql = "SELECT * FROM tbt_lab_penjualan_inap WHERE no_reg = '$noReg' ";	
			$noTrans = LabJualInapRecord::finder()->findBySql($sql)->no_trans_inap;	
			$cm = LabJualInapRecord::finder()->findBySql($sql)->cm;	
			//$cm = RwtInapRecord::finder()->findByPk($noTrans)->cm;
		}
		
		if($notransUpdate != '' )
		{
			$this->msg->Text = '    
				<script type="text/javascript">
					window.opener.location="index.php?page=Lab.cetakHasilLab&cm='.$cm.'&noTrans='.$noTrans.'&noReg='.$noReg.'&jnsPasien='.$jnsPasien.'"; 
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
