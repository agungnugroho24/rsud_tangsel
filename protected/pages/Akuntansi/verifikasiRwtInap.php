<?php
class verifikasiRwtInap extends SimakConf
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariCM,$urutBy,$tipeCari,$Company,$cariAlamat,$cariTgl,$cariBln,$cariByKelas,$cariByKamar)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   f.nama AS alamat, 
						   h.nama AS kelompok,						   
						   b.id AS kontrakID,								
						   g.nama AS kabupaten
						   FROM tbd_pasien a,
						        tbm_kelompok b,						   		
								tbm_kabupaten c,						   	
								tbm_perusahaan d,
								tbt_rawat_inap e,
								tbm_ruang f,
								tbm_kelas_kamar g,
								tbd_pegawai h
						   WHERE
							    a.kelompok=b.id
								AND a.cm=e.cm
								AND e.status = '0'
								AND f.id=e.kamar
								AND g.id=e.kelas
								AND h.id=e.dokter
								AND a.kelompok=b.id									
								AND a.kabupaten=c.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
						
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
				
			if($cariCM <> '')
				$sql .= "AND a.cm = '$cariCM' ";
			
			if($cariByKelas <> '')			
				$sql .= "AND e.kelas = '$cariByKelas' ";
			
			if($cariByKamar <> '')			
				$sql .= "AND e.kamar = '$cariByKamar' ";		
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_masuk = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_masuk BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}	
				
			$sql .= " GROUP BY a.cm ";
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						  f.nama AS alamat, 
						   h.nama AS kelompok, 						   
						   b.id AS kontrakID,						   
						   g.nama AS kabupaten
						   FROM tbd_pasien a,
								tbm_kelompok b,
								tbm_kabupaten c,
								tbm_perusahaan d,
								tbt_rawat_inap e,
								tbm_ruang f,
								tbm_kelas_kamar g,
								tbd_pegawai h 
						   WHERE
								a.kelompok=b.id
								AND a.cm=e.cm
								AND e.status = '0'
								AND f.id=e.kamar
								AND g.id=e.kelas
								AND h.id=e.dokter
								AND a.kelompok=b.id												
								AND a.kabupaten=c.id ";
			if($cariNama <> '')
							
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
			
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
							
			if($cariCM <> '')			
				$sql .= "AND a.cm = '$cariCM' ";
			
			if($cariByKelas <> '')			
				$sql .= "AND e.kelas = '$cariByKelas' ";
			
			if($cariByKamar <> '')			
				$sql .= "AND e.kamar = '$cariByKamar' ";		
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_masuk = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_masuk BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}
				
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";						
			
			$sql .= " GROUP BY a.cm ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		$page=PasienRecord::finder()->findAllBySql($sql);
		
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
			$cariByKelas=$this->getViewState('cariByKelas');
			$cariByKamar=$this->getViewState('cariByKamar');
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			$this->DDKelas->DataSource=KelasKamarRecord::finder()->findAll();
			$this->DDKelas->dataBind();
			$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
			$this->DDKamar->dataBind();
			$this->UserGrid->VirtualItemCount=RwtInapRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->cariNama->focus();		
			
			$position='TopAndBottom';		
			$this->UserGrid->PagerStyle->Position=$position;
			$this->UserGrid->PagerStyle->Visible=true;
			
		}		
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
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar);
		$this->UserGrid->dataBind();		
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
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
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar);
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
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar);	
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
		$cariByKelas=$this->getViewState('cariByKelas');
		$cariByKamar=$this->getViewState('cariByKamar');
	/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$urutBy,$elemenBy,$companyBy,$cariByAlamat,$cariByTgl,$cariByBulan,$cariByKelas,$cariByKamar);	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Akuntansi.menuAkun'));		
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
			$orderBy='cm';
		}		
		
		if($this->DDUrut->SelectedValue){ 		
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);	
		}else{
			$this->clearViewState('companyBy');	
		}	
		
		if($this->tglMsk->Text){
			$this->setViewState('cariByTgl', $this->tglMsk->Text);
		}else{
			$this->clearViewState('cariByTgl');	
		}
		
		if($this->DDBulan->SelectedValue){ 		
			$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
		}else{
			$this->clearViewState('cariByBulan');	
		}	
		
		if($this->DDKelas->SelectedValue){ 		
			$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
		}else{
			$this->clearViewState('cariByKelas');	
		}
		
		if($this->DDKamar->SelectedValue){ 		
			$this->setViewState('cariByKamar', $this->DDKamar->SelectedValue);	
		}else{
			$this->clearViewState('cariByKamar');	
		}		
		
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariNama,$cariCM,$this->DDUrut->SelectedValue,$Advance,$this->DDKontrak->SelectedValue,$cariAlamat,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDKelas->SelectedValue,$this->DDKamar->SelectedValue);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='cm';
		}		
		
		if($this->tglMsk->Text){
			$this->setViewState('cariByTgl', $this->tglMsk->Text);
		}else{
			$this->clearViewState('cariByTgl');	
		}
		
		if($this->DDBulan->SelectedValue){ 		
			$this->setViewState('cariByBulan', $this->DDBulan->SelectedValue);	
		}else{
			$this->clearViewState('cariByBulan');	
		}
		
		if($this->DDKelas->SelectedValue){ 		
			$this->setViewState('cariByKelas', $this->DDKelas->SelectedValue);	
		}else{
			$this->clearViewState('cariByKelas');	
		}
		
		if($this->DDKamar->SelectedValue){ 		
			$this->setViewState('cariByKamar', $this->DDKamar->SelectedValue);	
		}else{
			$this->clearViewState('cariByKamar');	
		}	
		
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('companyBy');	
		}
				
		if($this->DDUrut->SelectedValue){
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
		{	
			$this->DDKontrak->Enabled=true;	
		}else{
			$this->DDKontrak->Enabled=false;	
		}
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariNama,$cariCM,$this->DDUrut->SelectedValue,$Advance,$this->DDKontrak->SelectedValue,$cariAlamat,$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDKelas->SelectedValue,$this->DDKamar->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}
?>
