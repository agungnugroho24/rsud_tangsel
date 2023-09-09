<?php
Prado::Using('Application.modules.reportSimak');
class lapKgbSimpeg extends SimakConf
{   
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariNIP,$urutBy,$tipeCari,$tipeData)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.nip, a.nama, a.gol_akhir, a.tmt, b.nm_jabatan, b.tgl_mulai					   
						   FROM tbd_simpeg a,
								tbd_simpeg_jabatan b,
								tbd_simpeg_pangkat c								 
						   WHERE
								a.nip=b.nip
								AND a.nip=c.nip ";
								
			if($cariNama <> '')
							
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}		
							
			if($cariNIP <> '')			
				$sql .= "AND a.nip = '$cariNIP' ";									
			
			$sql .= " GROUP BY a.nip ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;						
			
		}else{
			$sql = "SELECT a.nip, a.nama, a.gol_akhir, a.tmt, b.nm_jabatan, b.tgl_mulai					   
						   FROM tbd_simpeg a,
								tbd_simpeg_jabatan b,
								tbd_simpeg_pangkat c								 
						   WHERE
								a.nip=b.nip
								AND a.nip=c.nip ";
								
			if($cariNama <> '')
							
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}		
							
			if($cariNIP <> '')			
				$sql .= "AND a.nip = '$cariNIP' ";									
			
			$sql .= " GROUP BY a.nip ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		
		if($tipeData == "0"){
			$page=SimpegRecord::finder()->findAllBySql($sql);
		}else{	
			$page=$this->queryAction($sql,'R');
		}	
		
		$this->showSql->Text=$sql;//show SQL Expression broer!
		$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;
		/*
		$criteria = new TActiveRecordCriteria;
		$criteria->Limit = $rows;
		$criteria->Offset = $offset;
		$page=PegawaiRecord::finder()->findAll($criteria);
		
		return $page;*/
	} 
	
	protected function createReport($sender,$param)
	{
		$pdf=new reportSimak();
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetFont('Arial','',10);
		$data=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariNip->Text,$DDUrut,$this->Advance->Checked,'1');
		foreach($data as $item){
		$pdf->Cell(20,10,$item['nip'],1,0,'C');
		$pdf->Cell(60,10,$item['nama'],1,0,'L');
		$pdf->Cell(20,10,$item['gol_akhir'],1,0,'C');
		$pdf->Cell(20,10,$item['tmt'],1,0,'C');
		$pdf->Cell(40,10,$item['nm_jabatan'],1,0,'C');
		$pdf->Cell(30,10,$item['tgl_mulai'],1,0,'C');	
		$pdf->Ln();	
		}
		$pdf->Output();

 	}	
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */   
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByCM=$this->getViewState('cariByCM');
			$urutBy=$this->getViewState('urutBy');
			$companyBy=$this->getViewState('companyBy');
			$elemenBy=$this->getViewState('elemenBy');
			$cariByAlamat=$this->getViewState('cariByAlamat');
			$cariByTgl=$this->getViewState('cariByTgl');
			$cariByBulan=$this->getViewState('cariByBulan');
			$cariByDokter=$this->getViewState('cariByDokter');
			$cariByKlinik=$this->getViewState('cariByKlinik');						
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');//kelompok pegawai '1' adalah untuk dokter
			$this->DDDokter->dataBind();
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->UserGrid->VirtualItemCount=SimpegRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->cariNip->focus();		
			
			$position='TopAndBottom';		
			$this->UserGrid->PagerStyle->Position=$position;
			$this->UserGrid->PagerStyle->Visible=true;
			
		}		
    }		
	
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
		$this->UserGrid->dataBind();		
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);	
		$this->UserGrid->dataBind();
	}
	
	/**
     * Sorting a specified user record.
     * This method responds to the datagrid's OnSortCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$oderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
	/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simpeg.menuSimpeg'));		
	}
	/*
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianBaru'));		
	}*/
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='nip';
		}				
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}		
				
		if($this->cariNip->Text){ 
			$this->setViewState('cariByNip', $this->cariNip->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}
		
		if($this->Advance->Checked){
			$this->setViewState('elemenBy',$this->Advance->Checked);	
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariNip->Text,$DDUrut,$this->Advance->Checked,'0');	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='nip';
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}		
		
		if($this->DDBulan->SelectedValue){ 		
			$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
		}else{
			$this->clearViewState('cariByBulan');	
		}	
			
		if($this->cariNip->Text){ 
			$this->setViewState('cariByNip', $this->cariNip->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariNip->Text,$this->DDUrut->SelectedValue,$this->Advance->Checked,$this->DDKontrak->SelectedValue,$this->cariAlamat->Text,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDDokter->SelectedValue,$this->DDKlinik->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}
?>
