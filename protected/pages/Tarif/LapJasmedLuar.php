<?php
class LapJasmedLuar extends SimakConf
{   
	
	private $sortExp = "tgl";
    private $sortDir = "DESC";
    private $offset = 0;
    private $pageSize = 10;
	
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
			$cariDokter=$this->getViewState('cariDokter');
			$cariKelompok=$this->getViewState('cariKelompok');
			$Company=$this->getViewState('Company');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');	
		
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
            $sql = "SELECT 
					  view_lap_jasmed_luar.id,
					  view_lap_jasmed_luar.cm,
					  view_lap_jasmed_luar.no_trans,
					  view_lap_jasmed_luar.tgl,
					  view_lap_jasmed_luar.id_opr,
					  view_lap_jasmed_luar.nm_opr,
					  view_lap_jasmed_luar.nm_pasien,					  
					  view_lap_jasmed_luar.id_peg_luar,
					  view_lap_jasmed_luar.posisi,
					  view_lap_jasmed_luar.tarif,
					  view_lap_jasmed_luar.st_bayar,
					  tbd_asisten_luar.nama AS nm_peg_luar,
					  tbd_pasien.kelompok
					FROM 
						view_lap_jasmed_luar
						INNER JOIN tbd_asisten_luar ON (view_lap_jasmed_luar.id_peg_luar = tbd_asisten_luar.id)
						INNER JOIN tbd_pasien ON (view_lap_jasmed_luar.cm = tbd_pasien.cm)
					WHERE
					  ( view_lap_jasmed_luar.tarif <> ''
					  OR view_lap_jasmed_luar.tarif <> NULL )  
					  AND view_lap_jasmed_luar.st_bayar = '0' ";					  	
						
			if($cariDokter <> '')			
				$sql .= "AND view_lap_jasmed_luar.id_peg_luar = '$cariDokter' ";
			
			if($cariKelompok <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$cariKelompok' ";
						
			if($cariTgl <> '')			
				$sql .= "AND view_lap_jasmed_luar.tgl = '$cariTgl' ";
				
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND view_lap_jasmed_luar.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";							
			
			if($cariBln <> '')			
				$sql .= "AND MONTH(view_lap_jasmed_luar.tgl) = '$cariBln' AND YEAR(view_lap_jasmed_luar.tgl)='$cariThn' ";	
			
			//$sql .= " GROUP BY a.kode, b.id_obat ";			
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
			
			$this->setViewState('sql',$sql);
			
			if($someDataList->getSomeDataCount($sql) > 0 && $this->DDDokter->SelectedValue != '' && $this->getViewState('prosesClick') != '')
			{
				$this->cetakBtn->Enabled = true;
			}
			else
			{
				$this->cetakBtn->Enabled = false;
			} 
			
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
	
	protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $item = $param->Item;	
				
		//$ID = $this->dtgSomeData->DataKeys[$item->ItemIndex];
		//$item->tglColumn->Text = $this->convertDate(OperasiBillingLuarRecord::finder()->findByPk($ID)->tgl,'3');
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes           	   
        } 
		
		//$this->bindGrid();
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
		
		if(!$this->IsPostBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$sql="SELECT * FROM tbd_asisten_luar ORDER BY st_kelompok, nama";
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
		
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('cariDokter');	
		}
		
		if($this->DDUrut->SelectedValue) {
			$this->setViewState('cariKelompok',$this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('cariKelompok');	
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
		
		if($this->DDtahun->SelectedValue){
			$cariThn = $this->getViewState('cariThn');
		}else{
			$this->clearViewState('cariThn');
		}		
		
		if($this->DDbulan->SelectedValue){
			$cariBln = $this->getViewState('cariBln');
		}else{
			$this->clearViewState('cariBln');
		}		
			
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
		
		if($this->DDberdasarkan->SelectedValue != '')  // when all validations succeed
        {     
			$this->dataGridCtrl->Visible = true;
			$this->errMsg->Visible = false;
			$this->setViewState('prosesClick','1');
		}
		else
		{
			$this->dataGridCtrl->Visible = false;
			$this->errMsg->Visible = true;
			$this->clearViewState('prosesClick');
		}
	}
	
	public function DDDokterChanged($sender,$param)
	{
		if($this->DDDokter->SelectedValue != '')
		{				
			$this->DDberdasarkan->focus();
			
			if($this->getViewState('prosesClick') != '')
				$this->cariClicked();
		}
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
		
		if($this->getViewState('prosesClick') != '')
			$this->cariClicked();
	}
	
	public function DDKontrakChanged($sender,$param)
	{		
		if($this->getViewState('prosesClick') != '')
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
		
		$this->dataGridCtrl->Visible = false;
		$this->errMsg->Visible = false;
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
		if($this->IsValid)  // when all validations succeed
        {
			if($this->DDberdasarkan->SelectedValue != '')  // when all validations succeed
			{     
				$sql = $this->getViewState('sql');
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$idTbl = $row['id'];
					$idPegLuar = $row['id_peg_luar'];
					$posisi = $row['posisi'];
					
					switch ($posisi)
					{
						case 'Dokter Utama':
							$fieldToUpdate = 'st_byr_dktr';
							break;
						case 'Ass Dokter Utama':
							$fieldToUpdate='st_byr_assdktr';
							break;
						case 'Dokter Anastesi':
							$fieldToUpdate='st_byr_anastesi';
							break;
						case 'Ass Dokter Anastesi':
							$fieldToUpdate='st_byr_assanastesi';
							break;
						
						case 'Dokter Anak':
							$fieldToUpdate = 'st_byr_anak';
							break;
						case 'Dokter Obgyn':
							$fieldToUpdate='st_byr_obgyn';
							break;
						case 'Ass Dokter Obgyn':
							$fieldToUpdate='st_byr_assobgyn';
							break;
						case 'Bidan':
							$fieldToUpdate='st_byr_bidan';
							break;
						
						case 'Ass Bidan':
							$fieldToUpdate = 'st_byr_assbidan';
							break;
						case 'Petugas Resusitasi':
							$fieldToUpdate='st_byr_petugas_resusitasi';
							break;
						case 'Instrumen':
							$fieldToUpdate='st_byr_pm1';
							break;
						case 'Sirkuler':
							$fieldToUpdate='st_byr_pm2';
							break;	
					}
					
					//Update status bayar = 1 di tbt_inap_operasi_billing_luar sesuai dengan posisi pd waktu operasi
					$sql = "UPDATE tbt_inap_operasi_billing_luar SET $fieldToUpdate = '1' WHERE id = '$idTbl'";
					$this->queryAction($sql,'C');
				}
				
				$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapJasmedLuar',
				array(
					'dokter'=>$this->DDDokter->SelectedValue,
					'kelompok'=>$this->DDUrut->SelectedValue,
					'perusahaan'=>$this->DDKontrak->SelectedValue,
					'tgl'=>$this->getViewState('cariTgl'),
					'tglawal'=>$this->getViewState('cariTglAwal'),
					'tglakhir'=>$this->getViewState('cariTglAkhir'),
					'cariBln'=>$this->getViewState('cariBln'),
					'cariThn'=>$this->getViewState('cariThn'),
					'periode'=>$this->txtPeriode->Text)));
			}
			else
			{
				$this->dataGridCtrl->Visible = false;
				$this->errMsg->Visible = true;
			}
		}
	}	
		
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
