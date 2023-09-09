<?php
class MasterObatJual extends SimakConf
{   
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	/**
	* Returns a subset of data.
	* In MySQL database, this can be replaced by LIMIT clause
	* in an SQL select statement.
	* @param integer the starting index of the row
	* @param integer number of rows to be returned
	* @return array subset of data
	*/
	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariGol,$cariKlas,$cariDerivat,$cariPbf,$cariProd,$cariSat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn)
	{
		if($pil == "1")
		{
			$sql = "SELECT 
					  a.kode,
					  a.nama,
					  a.satuan,
					  a.gol,
					  a.klasifikasi,
					  a.derivat,
					  a.produsen,
					  a.pbf,
					  a.tipe,
					  c.hrg_ppn,
					  c.hrg_netto,
					  b.dokter,
					  b.tgl,
					  b.wkt,
					  b.sumber,
					  b.tujuan,
					  b.id_obat,
					  b.jumlah,
					  b.hrg,
					  b.total
					FROM
					  tbt_obat_jual b,
					  tbm_obat a,
					  tbt_obat_harga c
					WHERE
					  a.kode = b.id_obat AND 
					  a.kode = c.kode AND
					  c.sumber=b.sumber ";
			
			if ($tujuan<>'')
				$sql .= "AND b.tujuan = '$tujuan' ";
					
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
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=c.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
				
			if($cariTipe <> '')			
				$sql .= "AND a.tipe = '$cariTipe' ";
				
			if($cariPoli <> '')			
				$sql .= "AND b.poli = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND b.dokter = '$cariDokter' ";
			
			if($cariTgl <> '')			
				$sql .= "AND b.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND b.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (b.tgl)='$cariBln' AND YEAR(b.tgl)='$cariThn' ";					
			
			//$sql .= " GROUP BY a.kode ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT 
					  a.kode,
					  a.nama,
					  a.satuan,
					  a.gol,
					  a.klasifikasi,
					  a.derivat,
					  a.produsen,
					  a.pbf,
					  a.tipe,
					  c.hrg_ppn,
					  c.hrg_netto,
					  b.dokter,
					  b.tgl,
					  b.wkt,
					  b.sumber,
					  b.tujuan,
					  b.id_obat,
					  b.jumlah,
					  b.hrg,
					  b.total
					FROM
					  tbt_obat_jual b,
					  tbm_obat a,
					  tbt_obat_harga c
					WHERE
					  a.kode = b.id_obat AND 
					  a.kode = c.kode AND
					  c.sumber=b.sumber ";
			
			if ($tujuan<>'')
				$sql .= "AND b.tujuan = '$tujuan' ";
					
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
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=c.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
				
			if($cariTipe <> '')			
				$sql .= "AND a.tipe = '$cariTipe' ";
				
			if($cariPoli <> '')			
				$sql .= "AND b.poli = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND b.dokter = '$cariDokter' ";
			
			if($cariTgl <> '')			
				$sql .= "AND b.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND b.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (b.tgl)='$cariBln' AND YEAR(b.tgl)='$cariThn' ";					
			
			//$sql .= " GROUP BY a.kode ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					
		
		$page=$this->queryAction($sql,'S');		 
		//$page=ObatRecord::finder()->findAllBySql($sql);
		//$page = $sql;
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;
		
		/*
		$criteria = new TActiveRecordCriteria;
		$criteria->Limit = $rows;
		$criteria->Offset = $offset;
		$page=KabupatenRecord::finder()->findAll($criteria);
		
		return $page;*/
	}
	
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onPreLoad($param)
	{
		
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{	
			//$this->DDtahun->DataSource=$this->tahun(1980,2051);
			$this->DDtahun->dataBind();
			
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();	
			
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
			
			$this->DDPoli->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDPoli->dataBind();
			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
														
			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');
			$sumber=$this->getViewState('sumber');			
			$tujuan=$this->getViewState('tujuan');			
			$cariTipe=$this->getViewState('cariTipe');
			$cariPoli=$this->getViewState('cariPoli');
			$cariDokter=$this->getViewState('cariDokter');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');		
			
			$this->UserGrid->VirtualItemCount=ObatJualRecord::finder()->count();
			// fetches all data account information 
			//$this->UserGrid->DataSource=$page=ObatJualRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn));
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
			// binds the data to interface components
			$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;		
			//$this->DDTujuan->focus();		
			
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
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');	
		$sumber=$this->getViewState('sumber');	
		$tujuan=$this->getViewState('tujuan');
		$cariTipe=$this->getViewState('cariTipe');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
			
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
		$this->UserGrid->dataBind();
		$this->txtSortir->Text=$dataSortir;		
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
			 $this->UserGrid->EditItemIndex=$param->Item->ItemIndex;

			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');		
			$sumber=$this->getViewState('sumber');
			$tujuan=$this->getViewState('tujuan');
			$cariTipe=$this->getViewState('cariTipe');
			$cariPoli=$this->getViewState('cariPoli');
			$cariDokter=$this->getViewState('cariDokter');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');
			
			$this->UserGrid->VirtualItemCount=ObatRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
			// binds the data to interface components
			$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;
		}	
    }
	
	public function cancelItem($sender,$param)
    {
        $orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');
		$tujuan=$this->getViewState('tujuan');
		$cariTipe=$this->getViewState('cariTipe');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		
		$this->UserGrid->EditItemIndex=-1;
        $this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
		// binds the data to interface components
		$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;		
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

	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');		
		$tujuan=$this->getViewState('tujuan');
		$cariTipe=$this->getViewState('cariTipe');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
		$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;
	}

	
	
	/*public function showMe($sender,$param)
	{		
		$this->showUp->DataSource=KabupatenRecord::finder()->findAll();
		$this->showUp->dataBind();
	}*/
 
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');			
		$tujuan=$this->getViewState('tujuan');
		$cariTipe=$this->getViewState('cariTipe');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$cariTipe,$cariPoli,$cariDokter,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn);
		$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;
	}	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;

		$this->setViewState('orderBy',$orderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');
		$tujuan=$this->getViewState('tujuan');		
		$cariTipe=$this->getViewState('cariTipe');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		/*$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,
									$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,
									$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,
									$this->DDSat->SelectedValue,$sumber,$this->DDTujuan->SelectedValue,$this->DDTipe->SelectedValue,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									$this->getViewState('cariBln'),$this->getViewState('cariThn'));
		$this->UserGrid->dataBind();
$this->txtSortir->Text=$dataSortir;	
	}
	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	
	public function cariClicked($sender,$param)
	{		
		$orderBy=$this->getViewState('orderBy');	
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Tujuan';
			}
			else
			{
				$dataSortir .= ', Tujuan';
			}
		}else{
			$this->clearViewState('tujuan');	
		}
		
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
			
				if($dataSortir=='')
			{
				$dataSortir = 'Kode Obat';
			}
			else
			{
				$dataSortir .= ', Kode Obat';
			}
			
			
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
			if($dataSortir=='')
			{
				$dataSortir = 'Nama Obat';
			}
			else
			{
				$dataSortir .= ', Nama Obat';
			}
				
				
		}else{
			$this->clearViewState('cariByNama');	
		}	
		
				
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDTipe->SelectedValue) {
			$this->setViewState('cariTipe',$this->DDTipe->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Tipe Obat';
			}
			else
			{
				$dataSortir .= ', Tipe Obat';
			}
		}else{
			$this->clearViewState('cariTipe');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
			if($dataSortir=='')
			{
				$dataSortir = 'Sumber Obat';
			}
			else
			{
				$dataSortir .= ', Sumber Obat';
			}
		}else{
			$this->clearViewState('sumber');
		}
		
		if($this->DDGol->SelectedValue) {
			$this->setViewState('cariByGol', $this->DDGol->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Golongan Obat';
			}
			else
			{
				$dataSortir .= ', Golongan Obat';
			}
		}else{
			$this->clearViewState('cariByGol');	
		}
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
			
			if($dataSortir=='')
			{
				$dataSortir = 'Klasifikasi Obat';
			}
			else
			{
				$dataSortir .= ', Klasifikasi Obat';
			}
			
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
			
			if($dataSortir=='')
			{
				$dataSortir = 'Derivat Obat';
			}
			else
			{
				$dataSortir .= ', Derivat Obat';
			}
			
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Sediaan';
			}
			else
			{
				$dataSortir .= ', Sediaan';
			}
		}else{
			$this->clearViewState('cariBySat');	
		}
				
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Produsen';
			}
			else
			{
				$dataSortir .= ', Produsen';
			}
		}else{
			$this->clearViewState('cariByProd');	
		}
				
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'PBF';
			}
			else
			{
				$dataSortir .= ', PBF';
			}
		}else{
			$this->clearViewState('cariByPbf');	
		}
				
		if($this->DDPoli->SelectedValue) {
			$this->setViewState('cariPoli',$this->DDPoli->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Poliklinik';
			}
			else
			{
				$dataSortir .= ', Poliklinik';
			}
		}else{
			$this->clearViewState('cariPoli');	
		}
		
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
			if($dataSortir=='')
			{
				$dataSortir = 'Dokter';
			}
			else
			{
				$dataSortir .= ', Dokter';
			}
		}else{
			$this->clearViewState('cariDokter');	
		}
		
		if($this->tgl->Text){
			$this->setViewState('cariTgl',$this->ConvertDate( $this->tgl->Text,2));
			if($dataSortir=='')
			{
				$dataSortir = 'Periode Harian';
			}
			else
			{
				$dataSortir .= ', Periode Harian';
			}
		}else{
			$this->clearViewState('cariTgl');	
		}	
		
		if($this->tglawal->Text){
			$this->setViewState('cariTglAwal',$this->ConvertDate( $this->tglawal->Text,2));
			if($dataSortir=='')
			{
				$dataSortir = 'Periode Mingguan';
			}
			else
			{
				$dataSortir .= ', Periode Mingguan';
			}
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
			if($this->getViewState('cariThn'))
			{
				if($dataSortir=='')
				{
					$dataSortir = 'Periode Bulanan';
				}
				else
				{
					$dataSortir .= ', Periode Bulanan';
				}
			}			
			
		}else{
			$this->clearViewState('cariBln');
		}		
		
		$i=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,
									$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,
									$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,
									$this->DDSat->SelectedValue,$sumber,$this->DDTujuan->SelectedValue,$this->DDTipe->SelectedValue,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									$this->getViewState('cariBln'),$this->getViewState('cariThn')));
		
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,
									$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,
									$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,
									$this->DDSat->SelectedValue,$sumber,$this->DDTujuan->SelectedValue,$this->DDTipe->SelectedValue,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									$this->getViewState('cariBln'),$this->getViewState('cariThn'));
		$this->UserGrid->dataBind();
		
		$this->dataGrid->Visible=true;
		
		$this->txtSortir->Text=$dataSortir;
		
		if($this->DDTujuan->SelectedValue=='')
		{
			$this->txtPenjualan->Text='';
		}
		else
		{
			$this->txtPenjualan->Text=DesFarmasiRecord::finder()->findByPk($this->DDTujuan->SelectedValue)->nama;
		}
		
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
				$this->txtPeriode->Text='PERIODE : ---';
			}
			
		}
		else
		{
			$this->txtPeriode->Text='PERIODE : ---';
		}
		
	}
	
	public function DDTujuanChanged($sender,$param)
	{
		$this->ID->focus();
	}
	
	public function DDTipeChanged($sender,$param)
	{
		$this->DDSumMaster->focus();
	}
	
	public function DDProdChanged($sender,$param)
	{
		$this->DDPbf->focus();
	}
		
	public function DDPbfChanged($sender,$param)
	{				
		$this->DDPoli->focus();
	}
	
	public function DDPoliChanged($sender,$param)
	{				
		$this->DDDokter->focus();
	}
	

	
	public function DDDokterChanged($sender,$param)
	{				
		$this->DDberdasarkan->focus();
	}
	
	public function DDSatChanged($sender,$param)
	{				
		$this->DDProd->focus();
	}
	
	public function DDDerivatChanged($sender,$param)
	{				
		$this->DDSat->focus();
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
		
		$this->DDSumSekunder->focus();
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	

		$this->DDGol->focus();
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
			$this->DDtahun->DataSource=$this->tahun(1980,2051);
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
			$this->minggu->visible=false;
			$this->hari->visible=false;
			$this->bulan->visible=false;
			
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
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakMasterObatJual',array('nama'=>$this->cariNama->Text,
			'kode'=>$this->ID->Text,'advance'=>$this->Advance->Checked,'gol'=>$this->DDGol->SelectedValue,'klas'=>$this->DDKlas->SelectedValue,
			'derivat'=>$this->DDDerivat->SelectedValue,'pbf'=>$this->DDPbf->SelectedValue,'prod'=>$this->DDProd->SelectedValue,
			'sat'=>$this->DDSat->SelectedValue,'sumber'=>$this->getViewState('sumber'),'tujuan'=>$this->DDTujuan->SelectedValue,
			'tipe'=>$this->DDTipe->SelectedValue,'poli'=>$this->DDPoli->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue,
			'tgl'=>$this->getViewState('cariTgl'),'tglawal'=>$this->getViewState('cariTglAwal'),
			'tglakhir'=>$this->getViewState('cariTglAkhir'),'cariBln'=>$this->getViewState('cariBln'),
			'cariThn'=>$this->getViewState('cariThn'),'periode'=>$this->txtPeriode->Text,'penjualan'=>$this->txtPenjualan->Text,'sortir'=>$this->txtSortir->Text)));
		
	}		
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
