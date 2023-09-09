<?php
class LapPenerimaanKasirRwtInapBaru extends SimakConf
{   
	private $sortExp = "no_trans ";
    private $sortDir = "ASC";
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
		}
				
		$this->bindGrid();									
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
			
			if($this->notrans->Text != '')	
			{
				$cariCm=$this->notrans->Text;
				$sql .= "AND view_lap_terima_rwtinap.cm = '$cariCm' ";		
			}			
			
			if($this->getViewState('cariDokter') <> '')
			{
				$cariDokter=$this->getViewState('cariDokter');
				$sql .= "AND view_lap_terima_rwtinap.dokter = '$cariDokter' ";
			}
			
			if($this->getViewState('urutBy') <> '')
			{
				$urutBy=$this->getViewState('urutBy');
				$sql .= "AND view_lap_terima_rwtinap.kelompok = '$urutBy' ";
			}
			
			
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->getViewState('cariTgl') <> '')
				{
					$cariTgl=$this->getViewState('cariTgl');
					$sql .= "AND view_lap_terima_rwtinap.tgl_keluar = '$cariTgl' ";
				}
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->getViewState('cariTglAwal') <> '' AND $this->getViewState('cariTglAkhir') <>'')
				{
					$cariTglAwal=$this->getViewState('cariTglAwal');
					$cariTglAkhir=$this->getViewState('cariTglAkhir');
					$sql .= "AND view_lap_terima_rwtinap.tgl_keluar BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->getViewState('cariThn') AND $this->getViewState('cariBln'))
				{
					$cariBln=$this->getViewState('cariBln');
					$cariThn=$this->getViewState('cariThn');
					$sql .= "AND MONTH (view_lap_terima_rwtinap.tgl_keluar)='$cariBln' AND YEAR(view_lap_terima_rwtinap.tgl_keluar)='$cariThn' ";
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
			/*
			else
			{
				$cariBln=date('m');
				$cariThn=date('Y');
				$sql .= "AND MONTH (view_lap_terima_rwtinap.tgl_keluar)='$cariBln' AND YEAR(view_lap_terima_rwtinap.tgl_keluar)='$cariThn' ";
			}
			*/
		
			
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
		
		if($this->cariNama->Text){
			$this->setViewState('cariByCm', $this->notrans->Text);			
		}else{
			$this->clearViewState('cariByCm');	
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);			
		}else{
			$this->clearViewState('cariByNama');	
		}					
				
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
	
	public function DDDokterChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
		
		$this->cariClicked();
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->DDUrut->SelectedValue=='05')
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
			
			//$this->cariClicked();
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
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtInap',array('cm'=>$this->notrans->Text,'nip'=>$this->cariNama->Text,
				'poli'=>$this->DDPoli->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue,'kelompok'=>$this->DDUrut->SelectedValue,
				'perusahaan'=>$this->DDKontrak->SelectedValue,'tgl'=>$this->getViewState('cariTgl'),'tglawal'=>$this->getViewState('cariTglAwal'),
				'tglakhir'=>$this->getViewState('cariTglAkhir'),'cariBln'=>$this->getViewState('cariBln'),
				'cariThn'=>$this->getViewState('cariThn'),'periode'=>$this->txtPeriode->Text,'modeInput'=>$this->getViewState('modeInput'))));
		
		}
	}
			
	protected function refreshMe()
	{
		$this->Reload();
	}
}

?>
