<?php
class lapPenerimaanLab extends SimakConf
{   
	private $sortExp = "no_trans ";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	private function rwtJln()
	{
		$sql = "SELECT
					  tbt_lab_penjualan.no_trans AS no_trans,
					  tbt_lab_penjualan.cm AS cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan.harga AS harga
					FROM
					  tbt_lab_penjualan
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan.cm <> ''  ";					  			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND tbt_lab_penjualan.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan.tgl)='$cariThn' ";
			}
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;
				
		$this->setViewState('rwtJln',$sql);
	}
	
	private function rwtInp()
	{
		$sql = "SELECT 
					  tbt_lab_penjualan_inap.no_trans,
					  tbt_lab_penjualan_inap.cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan_inap.harga AS harga
					FROM
					  tbt_lab_penjualan_inap
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan_inap.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan_inap.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan_inap.cm <> ''  ";					  			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND tbt_lab_penjualan_inap.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan_inap.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan_inap.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan_inap.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan_inap.tgl)='$cariThn' ";
			}
		$this->setViewState('rwtInp',$sql);	
	}
	
	private function rwtLn()
	{
		$sql = "SELECT
					  tbt_lab_penjualan_lain.no_trans AS no_trans,
					  tbt_lab_penjualan_lain.id AS cm,
					  tbt_lab_penjualan_lain.cm AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan_lain.harga AS harga
					FROM
					  tbt_lab_penjualan_lain
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan_lain.id <> '' ";
					
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan_lain.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan_lain.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan_lain.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan_lain.tgl)='$cariThn' ";
			}	
		$this->setViewState('rwtLn',$sql);	
	}
		
    public function onLoad($param)
	{
		$this->DDtahun->DataSource=$this->tahun(2008,2051);
		$this->DDtahun->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
		$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDDokter->DataSource=$arrDataDokter;	
		$this->DDDokter->dataBind();
				
		$this->bindGrid();									
		//$this->cariCM->focus();					
		$position='TopAndBottom';		
		$this->dtgSomeData->PagerStyle->Position=$position;
		$this->dtgSomeData->PagerStyle->Visible=true;
	}
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
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
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			///*
			$uni .= " UNION ALL ";
			$this->rwtJln();
			$rwtJln=$this->getViewState('rwtJln');
			$this->rwtInp();
			$rwtInp=$this->getViewState('rwtInp');
			$this->rwtLn();
			$rwtLn=$this->getViewState('rwtLn');
			
			$kel=$this->getViewState('cariKelompok');
			//$this->cariNama->text=$kel;
			if($kel=='1')
			{
				$sql=$rwtJln;
			}elseif($kel=='2')			
			{
				$sql=$rwtInp;
			}elseif($kel=='3')
			{
				$sql=$rwtLn;
			}else
			{
				$sql=$rwtJln.$uni.$rwtInp.$uni.$rwtLn;
			}
			
				//*/
			/*
            $sql = "SELECT
					  tbt_lab_penjualan.no_trans AS no_trans,
					  tbt_lab_penjualan.cm AS cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan.harga AS harga
					FROM
					  tbt_lab_penjualan
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan.cm <> ''  ";					  			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND tbt_lab_penjualan.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan.tgl)='$cariThn' ";
			}
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			/*
			$sql .= " UNION ALL ";
			$sql .= "SELECT 
					  tbt_lab_penjualan_inap.no_trans,
					  tbt_lab_penjualan_inap.cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan_inap.harga
					FROM
					  tbt_lab_penjualan_inap
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan_inap.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan_inap.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan_inap.cm <> ''  ";					  			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND tbt_lab_penjualan_inap.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan_inap.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan_inap.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan_inap.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan_inap.tgl)='$cariThn' ";
			}	
			
				//$sql .= " GROUP BY tbt_kasir_pendaftaran.id_tindakan ";			
			$sql .= " UNION ALL ";
			$sql .= "SELECT
					  tbt_lab_penjualan_lain.no_trans,
					  tbt_lab_penjualan_lain.id AS cm,
					  tbt_lab_penjualan_lain.cm AS cm2,
					  tbm_lab_tindakan.nama AS nmTDk,
					  tbt_lab_penjualan_lain.harga
					FROM
					  tbt_lab_penjualan_lain
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan_lain.id <> '' ";
					
			if($this->getViewState('cariTgl') <> '')
			{
				$cariTgl=$this->getViewState('cariTgl');
				$sql .= "AND tbt_lab_penjualan_lain.tgl = '$cariTgl' ";
			}
			if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
			{
				$cariTglAwal=$this->getViewState('cariTglAwal');
				$cariTglAkhir=$this->getViewState('cariTglAkhir');
				$sql .= "AND tbt_lab_penjualan_lain.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
			
			if($this->getViewState('cariBln') <> '' AND $this->getViewState('cariThn') <>'')
			{
				$cariBln=$this->getViewState('cariBln');
				$cariThn=$this->getViewState('cariThn');
				$sql .= "AND MONTH (tbt_lab_penjualan_lain.tgl)='$cariBln' AND YEAR(tbt_lab_penjualan_lain.tgl)='$cariThn' ";
			}	
					
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
	
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			/*
			KasirRwtJlnRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			*/
		}	
    }	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	public function ChangedDDKelompok($sender, $param)
	{
		//$this->cariNama->text=$this->DDKelompok->SelectedValue;
		if($this->DDKelompok->SelectedValue){
			$this->setViewState('cariKelompok', $this->DDKelompok->SelectedValue);			
		}else{
			$this->clearViewState('cariKelompok');	
		}
		$this->cariClicked();
	}
		
	public function cariClicked()
	{		
		$orderBy=$this->getViewState('orderBy');	
		/*
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);			
		}else{
			$this->clearViewState('cariByNama');	
		}
		*/
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('cariDokter');	
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
	
	
	public function DDDokterChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
		//$this->showSql->text=$this->DDDokter->SelectedValue;
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
	{	/*
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtJln',array('nip'=>$this->cariNama->Text,
			'poli'=>$this->DDPoli->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue,'kelompok'=>$this->DDUrut->SelectedValue,
			'perusahaan'=>$this->DDKontrak->SelectedValue,'tgl'=>$this->getViewState('cariTgl'),'tglawal'=>$this->getViewState('cariTglAwal'),
			'tglakhir'=>$this->getViewState('cariTglAkhir'),'cariBln'=>$this->getViewState('cariBln'),
			'cariThn'=>$this->getViewState('cariThn'),'periode'=>$this->txtPeriode->Text)));*/
			$this->Response->redirect($this->Service->constructUrl('Lab.cetakLapPenerimaanLab',
			array('dokter'=>$this->getViewState('cariDokter'),
			'tgl'=>$this->getViewState('cariTgl'),'tglawal'=>$this->getViewState('cariTglAwal'),
			'tglakhir'=>$this->getViewState('cariTglAkhir'),'cariBln'=>$this->getViewState('cariBln'),
			'cariThn'=>$this->getViewState('cariThn'),'periode'=>$this->txtPeriode->Text)));
	}		
	protected function refreshMe()
	{
		$this->Reload();
	}
}

?>
