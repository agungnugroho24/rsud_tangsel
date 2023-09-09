<?php
class LapJasmedRwtJlnBeta extends SimakConf
{   

	private $sortExp = "cm";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
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
		 
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$item->tarifCol->Text = number_format($item->DataItem['total'],'2',',','.');
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
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
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
			
			$sql = "SELECT 
					  '1' AS jns_tindakan,
					  tbt_kasir_rwtjln.no_trans,
					  tbd_pasien.nama,
					  tbm_nama_tindakan.nama AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbm_kelompok.nama AS kelompok,
					  tbt_kasir_rwtjln.total,
					  tbt_kasir_rwtjln.operator,
					  tbt_kasir_rwtjln.waktu,
					  tbt_kasir_rwtjln.tgl,
					  tbt_kasir_rwtjln.id_tindakan,
					  tbt_kasir_rwtjln.dokter,
					  tbt_kasir_rwtjln.klinik,
					  tbt_kasir_rwtjln.no_trans_rwtjln AS no_trans_asal,
					  tbd_pasien.kelompok,
					  tbd_pasien.perusahaan,
					  tbm_poliklinik.nama AS nm_poli,
					  tbd_pegawai.nama AS nm_peg,
					  tbm_fraksi_jasmed.jml_level
					FROM
					  tbt_kasir_rwtjln
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_rwtjln.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id)
					  LEFT JOIN tbm_fraksi_jasmed ON (tbm_nama_tindakan.id = tbm_fraksi_jasmed.id_tindakan) ";
					  
			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama  <> '' ";	
			}
			
			if($this->DDPoli->SelectedValue != '')
			{
				$cariPoli = $this->DDPoli->SelectedValue;
				$sql .= "AND tbt_kasir_rwtjln.klinik = '$cariPoli' ";		
			}
			
			if($this->DDDokter->SelectedValue != '')
			{
				$cariDokter = $this->DDDokter->SelectedValue;
				$sql .= "AND tbt_kasir_rwtjln.dokter = '$cariDokter' ";		
			}
						
			if($this->DDUrut->SelectedValue != '')
			{
				$urutBy = $this->DDUrut->SelectedValue;
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";	
			}
			
			if($this->DDKontrak->SelectedValue != '')
			{
				$Company = $this->DDKontrak->SelectedValue;
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";	
			}
			
			if($this->DDberdasarkan->SelectedValue == '1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->convertDate($this->tgl->Text,'2');
					$sql .= "AND tbt_kasir_rwtjln.tgl = '$cariTgl' ";	
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}	
				else
				{
					$cariBln = date('m');
					$cariThn = date('Y');
					
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}
				
			}
			elseif($this->DDberdasarkan->SelectedValue == '2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')	
				{
					$cariTglAwal = $this->convertDate($this->tglawal->Text,'2');
					$cariTglAkhir = $this->convertDate($this->tglakhir->Text,'2');
					
					$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
				
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}		
				else
				{
					$cariBln = date('m');
					$cariThn = date('Y');
					
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}		
			}
			elseif($this->DDberdasarkan->SelectedValue == '3')
			{
				if($this->DDtahun->Text != '' && $this->DDbulan->Text != '')	
				{
					$cariBln = $this->getViewState('cariBln');
					$cariThn = $this->getViewState('cariThn');
						
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
				
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}	
				else
				{
					$cariBln = date('m');
					$cariThn = date('Y');
					
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}
			}
			elseif($this->DDberdasarkan->SelectedValue == '')
			{
				$cariBln = date('m');
				$cariThn = date('Y');
				
				$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			
			$this->setViewState('sql',$sql);
			
			$sql .= "GROUP BY
					tbt_kasir_rwtjln.no_trans,
					tbt_kasir_rwtjln.no_trans_rwtjln,
					tbt_kasir_rwtjln.id_tindakan";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
			
			$this->showSql->Text=$sql;
			
			$this->gridPanel->Display = 'Dynamic';
			
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
			
			$this->clearViewState('sql');
        }
    }
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
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
			
			$this->bulan->Display = 'None';
			$this->minggu->Display = 'None';
			$this->hari->Display = 'None';
			
			$this->gridPanel->Display = 'None';
			$this->cetakBtn->Enabled = false;
		}		
		else
		{
			$this->bindGrid();
		}
    }		
	
	
	
	
	public function panelCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
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
	}
		
	
	public function DDPoliChanged($sender,$param)
	{				
		$this->DDDokter->focus();
	}
	
	public function DDDokterChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
		
		
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->DDUrut->SelectedValue=='02')
		{
			$this->DDKontrak->Enabled=true;
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDKontrak->focus();
		}
		else
		{
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDKontrak->Enabled=false;
		}
		$this->DDKontrak->SelectedValue = -1;
		
	}
	
	public function DDKontrakChanged($sender,$param)
	{		
						
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
			$this->bulan->Display = 'Dynamic';
			$this->minggu->Display = 'None';
			$this->hari->Display = 'None';
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
			$this->minggu->Display = 'Dynamic';
			$this->hari->Display = 'None';
			$this->bulan->Display = 'None';
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tglawal->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->DDbulan->SelectedIndex=-1;
			$this->DDtahun->SelectedIndex=-1;
			$this->minggu->Display = 'None';
			$this->hari->Display = 'Dynamic';
			$this->bulan->Display = 'None';
			$this->tgl->Text='';
			$this->tglawal->Text='';
			$this->tglakhir->Text='';
			//$this->cetakLapBtn->Enabled=true;
			//$this->clearViewState('idBulan');
			$this->clearViewState('cariThn');
			$this->clearViewState('cariBln');
			$this->tgl->focus();
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
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
			$this->minggu->Display = 'None';
			$this->hari->Display = 'None';
			$this->bulan->Display = 'None';
			
			$this->DDtahun->Enabled=false;
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
			
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
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
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
		$session=new THttpSession;
		$session->open();
		$session['cetakLapJasmedRwtJln'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapJasmedRwtJlnXls',array(
			'poli'=>$this->DDPoli->SelectedValue,
			'dokter'=>$this->DDDokter->SelectedValue,
			'kelompok'=>$this->DDUrut->SelectedValue,
			'perusahaan'=>$this->DDKontrak->SelectedValue,
			'tgl'=>$this->getViewState('cariTgl'),
			'tglawal'=>$this->getViewState('cariTglAwal'),
			'tglakhir'=>$this->getViewState('cariTglAkhir'),
			'cariBln'=>$this->getViewState('cariBln'),
			'cariThn'=>$this->getViewState('cariThn'),
			'periode'=>$this->txtPeriode->Text,
			'tipe_pasien'=>'0')));
		
	}	
}
?>
