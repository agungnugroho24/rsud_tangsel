<?php
class kunjPas extends SimakConf
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariTgl,$cariBln,$cariByDokter,$cariByKlinik)
	{
		$cm=$this->getViewState('cm');
		if($pil == "1")
		{
			$sql = "SELECT b.tgl_visit,
						   a.cm,
						   a.nama AS wkt_visit, 
						   c.nama AS id_klinik,
						   d.nama AS dokter,
						   b.no_trans						    						   						   
						   FROM tbd_pasien a,
								tbt_rawat_jalan b,
								tbm_poliklinik c,
								tbd_pegawai d	
						   WHERE
								a.cm=b.cm
								AND b.cm='$cm'
								AND b.id_klinik=c.id
								AND b.dokter=d.id ";			
			
			if($cariByDokter <> '')			
				$sql .= "AND b.dokter = '$cariByDokter' ";
			
			if($cariByKlinik <> '')			
				$sql .= "AND b.id_klinik = '$cariByKlinik' ";					
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND b.tgl_visit = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND b.tgl_visit BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}					
			
			if($order <> '')							
				$sql .= " ORDER BY b." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT b.tgl_visit,
						   a.cm,
						   a.nama AS wkt_visit, 
						   c.nama AS id_klinik,
						   d.nama AS dokter,
						   b.no_trans						    						   						   
						   FROM tbd_pasien a,
								tbt_rawat_jalan b,
								tbm_poliklinik c,
								tbd_pegawai d	
						   WHERE
								a.cm=b.cm
								AND b.cm='$cm'
								AND b.id_klinik=c.id
								AND b.dokter=d.id ";		
			
			if($cariByDokter <> '')			
				$sql .= "AND b.dokter = '$cariByDokter' ";
			
			if($cariByKlinik <> '')			
				$sql .= "AND b.id_klinik = '$cariByKlinik' ";		
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND b.tgl_visit = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND b.tgl_visit BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}				
			
			if($order <> '')			
				$sql .= " ORDER BY b." . $order . ' ' . $sort;

		}					 
		$page=RwtjlnRecord::finder()->findAllBySql($sql);
		
		$this->showSql->Text=$sql;//show SQL Expression broer!
		$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;		
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
			
			$cm=$this->Request['cm'];
			$this->setViewState('cm',$cm);			
			$orderBy=$this->getViewState('orderBy');		
			$cariByTgl=$this->getViewState('cariByTgl');
			$cariByBulan=$this->getViewState('cariByBulan');
			$cariByDokter=$this->getViewState('cariByDokter');
			$cariByKlinik=$this->getViewState('cariByKlinik');					
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
			$this->UserGrid->dataBind();			
			
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');//kelompok pegawai '1' adalah untuk dokter
			$this->DDDokter->dataBind();
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->UserGrid->VirtualItemCount=RwtjlnRecord::finder()->count();				
			$this->DDDokter->focus();		
			
			$position='TopAndBottom';		
			$this->UserGrid->PagerStyle->Position=$position;
			$this->UserGrid->PagerStyle->Visible=true;
			
		}		
    }		
	
	
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');		
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
		$this->UserGrid->dataBind();		
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');		
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');		
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);	
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
		$cariByTgl=$this->getViewState('cariByTgl');
		$cariByBulan=$this->getViewState('cariByBulan');
		$cariByDokter=$this->getViewState('cariByDokter');
		$cariByKlinik=$this->getViewState('cariByKlinik');
			
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByTgl,$cariByBulan,$cariByDokter,$cariByKlinik);	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtJln'));		
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
			$orderBy='tgl_visit';
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
		
		if($this->DDDokter->SelectedValue){ 		
			$this->setViewState('cariByDokter', $this->DDDokter->SelectedValue);	
		}else{
			$this->clearViewState('cariByDokter');	
		}
		
		if($this->DDKlinik->SelectedValue){ 		
			$this->setViewState('cariByKlinik', $this->DDKlinik->SelectedValue);	
		}else{
			$this->clearViewState('cariByKlinik');	
		}	
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->tglMsk->Text,$this->DDBulan->SelectedValue,$this->DDDokter->SelectedValue,$this->DDKlinik->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}
?>
