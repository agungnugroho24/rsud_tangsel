<?php
class LapPenjualanBaru extends SimakConf
{  	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
		
	/**
	* Returns a subset of data.
	* In MySQL database, this can be replaced by LIMIT clause
	* in an SQL select statement.
	* @param integer the starting index of the row
	* @param integer number of rows to be returned
	* @return array subset of data
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


    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByJenis=$this->getViewState('cariByJenis');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');
			$sumber=$this->getViewState('sumber');			
			$tujuan=$this->getViewState('tujuan');
			$klinik=$this->getViewState('id_klinik');
			$dokter=$this->getViewState('id_dokter');
			$kelompok=$this->getViewState('kelompok');
			$kontrak=$this->getViewState('kontrak');
			$tgl=$this->getViewState('cariTgl');
			$tglAwal=$this->getViewState('cariTglAwal');
			$tglAkhir=$this->getViewState('cariTglAkhir');
			$tgl=$this->getViewState('cariTgl');
			$bulan=$this->getViewState('bulan');
			$tahun=$this->getViewState('tahun');
			$tipe=$this->getViewState('tipe');
			$rawat=$this->getViewState('rawat');
		
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
            $sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						   d.hrg_ppn AS beli,
						   b.hrg AS jual,
						   SUM(b.jumlah) AS jumlah,
						   (SUM(b.total_real)-(SUM(b.jumlah) * d.hrg_ppn)) AS untung,
						   b.tujuan AS sumber ";				
						   

			if($rawat == '0')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND b.jumlah > 0
								AND d.kode=a.kode ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND
								c.cm=b.cm 
								AND b.jumlah > 0
								AND d.kode=a.kode ";
					}
				}
			}
			elseif($rawat == '1')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND b.jumlah > 0
								AND d.kode=a.kode ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND
								AND b.jumlah > 0
								c.cm=b.cm  
								AND d.kode=a.kode ";
					}
				}
			}
			elseif($rawat == '2')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_lain b,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat
								AND b.tujuan='$tujuan' 
								AND b.jumlah > 0
								AND d.kode=a.kode ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_lain b,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat
								AND b.jumlah > 0
								AND d.kode=a.kode ";
					}
				}
			}	
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.kode = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariJenis <> '')			
				$sql .= "AND a.kategori = '$cariJenis' ";
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";		
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";
			
			if($klinik <> '')			
				$sql .= "AND b.klinik = '$klinik' ";	
			
			if($dokter <> '')			
				$sql .= "AND b.dokter = '$dokter' ";	
			
			if($kelompok <> '')			
				$sql .= "AND c.kelompok = '$kelompok' ";
			
			if($kontrak <> '')			
				$sql .= "AND c.perusahaan = '$kontrak' ";	
			
			if($tgl <> '')			
				$sql .= "AND b.tgl = '$tgl' ";
				
			if($tglAwal <> '' AND $tglAkhir <> '')			
				$sql .= "AND b.tgl BETWEEN '$tglAwal' AND '$tglAkhir' ";							
			
			if($bulan <> '')			
				$sql .= "AND MONTH(b.tgl) = '$bulan' AND YEAR(b.tgl)='$tahun' ";	
			
			$sql .= " GROUP BY a.kode, b.id_obat ";			
			/*
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;		
			*/
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
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
	
	protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	   
        } 
    }
	
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		/*
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $rujukan = $item->rujukan->DropDownList->SelectedValue;
		$kelas = $item->kelas->DropDownList->SelectedValue;
        if ($this->User->IsAdmin)
		{
			
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$oSomeData = RwtInapRecord::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
                        
            // do some changes to your database item/object and then save it
            $oSomeData->st_rujukan = $rujukan;
			$oSomeData->kelas = $kelas;
            $oSomeData->save();
            
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        }
		*/
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
		
		if(!$this->IsPostBack)
		{	
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->DDTujuan->dataBind();
				
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
		
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();	
			
			$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll();
			$this->DDKelompok->dataBind(); 	
					
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			
			$this->DDbulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDbulan->dataBind();							
				
			$this->cetakLapBtn->Enabled=false;
			
	}
	else
	{	
		if($rawat != '' && $tujuan !=''){
			$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
		
		$this->cariClicked();
				
	}	
	
		//$this->UserGrid->dataBind();		
		$this->ID->focus();		
    }		
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		$this->DDGol->focus();		
	}
	
	public function chRawat()
	{
		$tmp=$this->collectSelectionResult($this->RBRawat);
		$this->setViewState('rawat',$tmp);
		$this->DDTujuan->focus();
		if($tmp == '1'){
			$this->DDKlinik->Enabled=false;		
			$this->DDDokter->Enabled=true;
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');
			$this->DDDokter->dataBind();
		}
		elseif($tmp == '2'){
			$this->DDKlinik->Enabled=false;		
			$this->DDDokter->Enabled=false;
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');
			$this->DDDokter->dataBind();
		}
		else{
			$this->DDKlinik->Enabled=true;
		}	
		
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
			$this->cetakLapBtn->Enabled=false;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->DDbulan->focus();
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
			$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tglawal->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
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
			$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tgl->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
		}
		else
		{
			$this->clearViewState('pilihPeriode');			
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			$this->cetakLapBtn->Enabled=false;		
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$this->cariClicked();
		}
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			$this->cetakLapBtn->Enabled=false;
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
			$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariBln');
			//$this->setViewState('idBulan',$pilih);
			
			$cariBln = $this->DDbulan->SelectedValue;
			$this->setViewState('cariBln',$cariBln);
		}
	}
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->Enabled=false;
		}		
		
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		if ($pilih=='')
		{
			$this->cetakLapBtn->Enabled=false;
			////$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
		}			
		else
		{
			$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			//$this->setViewState('idBulan',$pilih);
			
			$cariThn = $pilih;
			$this->setViewState('cariThn',$cariThn);
		}		
		
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else
		{
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			$this->DDKlas->Enabled=false;
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}
		
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		if($this->DDKelompok->SelectedIndex == 4)
		{
			$this->DDKontrak->Enabled=true;
			$this->DDKontrak->focus();
		}
		else
		{
			$this->DDKontrak->Enabled=false;
			$this->tgl->focus();
		}
		
	}
	
	public function showDokter($sender,$param)
	{
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
		$this->DDDokter->dataBind();
		$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
		$this->DDDokter->Enabled=true;		
		$this->setViewState('klinik',$klinik);
		$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
	}
	
	public function showNotrans($sender,$param)
	{					
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);				
		$this->DDKelompok->focus();		
	}
	
	public function changePage($sender,$param)
	{				
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		$this->setViewState('offset',$offset);			
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
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jml->TextBox->Columns=5;
		   //$item->nama->Enabled=false;
		   //$item->sat->Enabled=false;
		   //$item->pbf->Enabled=false;
		   //$item->sumber->Enabled=false;
		   //$item->sumberSekunder->Enabled=false;
        }       
    }
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			 $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;			
		}	
    }
	
	public function cancelItem($sender,$param)
    {
        
		$this->UserGrid->EditItemIndex=-1;       		
		$this->ID->focus();
    }
	
	/**
     * Deletes a specified user record.
     * This method responds to the datagrid's OnDeleteCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);
		
	}
	
	public function cetakClicked($sender,$param)
	{			
		$this->Response->redirect($this->Service->constructUrl('Apotik.cetakLapPenjualan',
		array(
			'cariByID'=>$this->ID->Text,
			'cariByNama'=>$this->cariNama->Text,
			'cariByGol'=>$this->DDGol->SelectedValue,
			'cariByJenis'=>$this->DDJenisBrg->SelectedValue,
			'cariByKlas'=>$this->DDSat->SelectedValue,
			'cariByDerivat'=>$this->DDDerivat->SelectedValue,
			'cariByPbf'=>$this->DDPbf->SelectedValue,
			'cariByProd'=>$this->DDProd->SelectedValue,
			'cariBySat'=>$this->DDSat->SelectedValue,
			'sumber'=>$this->getViewState('sumber'),
			'tujuan'=>$this->DDTujuan->SelectedValue,
			'klinik'=>$this->DDKlinik->SelectedValue,
			'dokter'=>$this->DDDokter->SelectedValue,
			'kelompok'=>$this->DDKelompok->SelectedValue,
			'kontrak'=>$this->DDKontrak->SelectedValue,
			'tgl'=>$this->getViewState('cariTgl'),
			'tglAwal'=>$this->getViewState('cariTglAwal'),
			'tglAkhir'=>$this->getViewState('cariTglAkhir'),
			'bulan'=>$this->DDbulan->SelectedValue,
			'tahun'=>$this->DDtahun->SelectedValue,
			'tipe'=>$this->collectSelectionResult($this->RBtipeObat),
			'rawat'=>$this->collectSelectionResult($this->RBRawat),
			'periode'=>$this->txtPeriode->Text)));
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
		//$offset=$this->getViewState('offset');	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
			
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->DDJenisBrg->SelectedValue) {
			$this->setViewState('cariByJenis', $this->DDJenisBrg->SelectedValue);
		}else{
			$this->clearViewState('cariByJenis');	
		}
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDGol->SelectedValue) {
			$this->setViewState('cariByGol', $this->DDGol->SelectedValue);
		}else{
			$this->clearViewState('cariByGol');	
		}
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}
		
		if($this->DDKlinik->SelectedValue) {
			$this->setViewState('klinik',$this->DDKlinik->SelectedValue);
		}else{
			$this->clearViewState('klinik');	
		}
		
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('dokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('dokter');	
		}
		
		if($this->DDKelompok->SelectedValue) {
			$this->setViewState('kelompok',$this->DDKelompok->SelectedValue);
		}else{
			$this->clearViewState('kelompk');	
		}
		
		if($this->DDKontrak->SelectedValue) {
			$this->setViewState('kontrak',$this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('kontrak');	
		}
		
		if($this->tgl->Text){ 
			$tmp = $this->ConvertDate($this->tgl->Text,'2');
			$this->setViewState('cariTgl', $tmp);	
		}else{
			$this->clearViewState('cariTgl');	
		}
		
		if($this->tglawal->Text){ 
			$tmp = $this->ConvertDate($this->tglawal->Text,'2');
			$this->setViewState('cariTglAwal', $tmp);	
		}else{
			$this->clearViewState('cariTglAwal');	
		}
		
		if($this->tglakhir->Text){ 
			$tmp = $this->ConvertDate($this->tglakhir->Text,'2');
			$this->setViewState('cariTglAkhir', $tmp);	
		}else{
			$this->clearViewState('cariTglAkhir');	
		}
		
		if($this->DDbulan->SelectedValue) {
			$this->setViewState('bulan',$this->DDbulan->SelectedValue);
		}else{
			$this->clearViewState('bulan');	
		}	
		
		if($this->DDtahun->SelectedValue) {
			$this->setViewState('tahun',$this->DDtahun->SelectedValue);
		}else{
			$this->clearViewState('tahun');	
		}	
		
		$rawat=$this->getViewState('rawat');
		$tujuan=$this->getViewState('tujuan');			
			
		if($rawat != '' && $tujuan !='')
		{
			$this->bindGrid();
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				
				if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}
				/*
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}
				*/
			}
			/*
			else
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			*/
			
		}
	}		
	
		
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			//$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	
	}
	
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
