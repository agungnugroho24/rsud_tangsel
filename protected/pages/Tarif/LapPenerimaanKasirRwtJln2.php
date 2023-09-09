<?php
class LapPenerimaanKasirRwtJln2 extends SimakConf
{   
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$this->DDPoli->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDPoli->dataBind();
			/*
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
			$arrDataDokter=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDDokter->DataSource=$arrDataDokter;	
			$this->DDDokter->dataBind();
			
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			*/
			$sql="SELECT real_name, nip, allow FROM tbd_user";
			$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
			foreach($arr as $row)
			{
				$arrApp=array();
				$var=$row['allow'];
				$arrApp = explode(',', $var);
				
				if (in_array('2', $arrApp))
				{
					$data[]=array('nip'=>$row['nip'],'nama'=>$row['real_name']);
				}	
			}
			
			$this->DDKasir->DataSource = $data;
			$this->DDKasir->dataBind();
					
			$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
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
			$operator=$this->getViewState('cariByKasir');				
			$periode = $this->getViewState('pilihPeriode');			
			for($i=0;$i<=8;$i++)
			{
				if (!$this->getViewState('nmTable'))
				{
					$nmTable = $this->setNameTable('nmTable');
					$sql="CREATE TABLE $nmTable (
							id INT (2) auto_increment,
							tanggal date DEFAULT '0000-00-00',
							operator varchar (9) DEFAULT '0',
							biaya_adm int(11) DEFAULT '0',
							biaya_poli int(11) DEFAULT '0',
							tot_obat int(11) DEFAULT '0',
							tot_lab int(11) DEFAULT '0',
							tot_rad int(11) DEFAULT '0',
							PRIMARY KEY (id)
						) ENGINE = MEMORY";					
					$this->queryAction($sql,'C');//Create new tabel bro...
				}
				
				$sql = "SELECT biaya_adm FROM $nmTable WHERE operator='$operator' ";
				if($periode == '1')//Harian
				{
					$tgl=$this->getViewState('cariTgl');
					$sql .= " AND tanggal='$tgl'";
				}
				else if ($periode == '2')//Bulanan
				{
					$tglAwal=$this->getViewState('cariTglAwal');
					$tglAkhir=$this->getViewState('cariTglAkhir');				
					$sql .= " AND tanggal BETWEEN '$tglAwal' AND '$tglAkhir'";
				}
				else if ($periode == '3')//Tahunan
				{
					$tgl=$this->getViewState('cariThn');
					$sql .= " AND YEAR(tanggal)='$tgl'";
				}					
				$stTmp=$this->queryAction($sql,'R');
				
				if($i == 0)//Do tbt_kasir_rwtjln
				{				
					$sql = "SELECT total, tgl, operator FROM tbt_kasir_rwtjln WHERE operator = '$operator' ";
					if($periode == '1')//Harian
					{
						$tgl=$this->getViewState('cariTgl');
						$sql .= " AND tanggal='$tgl'";
					}
					else if ($periode == '2')//Bulanan
					{
						$tglAwal=$this->getViewState('cariTglAwal');
						$tglAkhir=$this->getViewState('cariTglAkhir');				
						$sql .= " AND tanggal BETWEEN '$tglAwal' AND '$tglAkhir'";
					}
					else if ($periode == '3')//Tahunan
					{
						$tgl=$this->getViewState('cariThn');
						$sql .= " AND YEAR(tanggal)='$tgl'";
					}					
					$arr=$this->queryAction($sql,'R');					
					
					foreach($arr as $row)
					{
						$opr = $row['operator'];
						$tgl = $row['tgl'];
						if($stTmp)
						{													
							foreach($stTmp as $brs)
							{
								$totTmp = $brs['biaya_adm'] + $row['total'];
							}
							$sql="UPDATE $nmTable SET biaya_adm = '$totTmp'WHERE operator='$operator' ";							
						}
						else
						{
							$totTmp = $row['total'];
							$sql="INSERT INTO $nmTable (biaya_adm,tanggal,operator) ('','','$operator')";
						}	
						$this->queryAction($sql,'C');
					}
				}
			}
			else if($ == 1)//Do tbt_obat_jual
			{
			
			}
			$sql="SELECT * FROM $nmTable ";
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$a.$adm.' '.$b.$poli.' '.$c.$obat.' '.$d.$lab.' '.$e.$rad;
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
    /*
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
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
														
			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');			
			$cariPoli=$this->getViewState('cariPoli');
			$cariDokter=$this->getViewState('cariDokter');
			$urutBy=$this->getViewState('urutBy');
			$Company=$this->getViewState('Company');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');		
			
			$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S'));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;			
			
			
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S');
		
		
		$this->UserGrid->dataBind();
				
			//$this->cariClicked();
		}		
    }		
	
	/**
     * Paging Control and Properties to specified pages.
     * This method responds to the datagrid's event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	 /*
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');		
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$urutBy=$this->getViewState('urutBy');
		$Company=$this->getViewState('Company');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
			
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		if($this->getViewState('cariTgl')=='' && $this->getViewState('cariTglAwal')=='' && $this->getViewState('cariTglAkhir')=='' && $this->getViewState('cariBln')=='' && $this->getViewState('cariThn')=='')
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,
									$cariPoli,$cariDokter,$urutBy,$Company,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S');
			
		}
		else
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S');
		}
		
		$this->UserGrid->dataBind();	
	} 
	*/
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
	/*
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			 $this->UserGrid->EditItemIndex=$param->Item->ItemIndex;

			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');			
			$cariPoli=$this->getViewState('cariPoli');
			$cariDokter=$this->getViewState('cariDokter');
			$urutBy=$this->getViewState('urutBy');
			$Company=$this->getViewState('Company');
			$cariTgl=$this->getViewState('cariTgl');
			$cariTglAwal=$this->getViewState('cariTglAwal');
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariBln=$this->getViewState('cariBln');
			$cariThn=$this->getViewState('cariThn');
			
			$this->UserGrid->VirtualItemCount=KasirRwtJlnRecord::finder()->count();
			// fetches all data account information 
			if($this->getViewState('cariTgl')=='' && $this->getViewState('cariTglAwal')=='' && $this->getViewState('cariTglAkhir')=='' && $this->getViewState('cariBln')=='' && $this->getViewState('cariThn')=='')
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,
									$cariPoli,$cariDokter,$urutBy,$Company,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S');
			
		}
		else
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S');
		}
		
		$this->UserGrid->dataBind();
		}	
    }
	
	public function cancelItem($sender,$param)
    {
        $orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$urutBy=$this->getViewState('urutBy');
			$Company=$this->getViewState('Company');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		
		$this->UserGrid->EditItemIndex=-1;
     
	 if($this->getViewState('cariTgl')=='' && $this->getViewState('cariTglAwal')=='' && $this->getViewState('cariTglAkhir')=='' && $this->getViewState('cariBln')=='' && $this->getViewState('cariThn')=='')
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,
									$cariPoli,$cariDokter,$urutBy,$Company,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S');
			
		}
		else
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S');
		}
		
		$this->UserGrid->dataBind();
			
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
			
			KasirRwtJlnRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	
/*
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;

		$this->setViewState('orderBy',$orderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariPoli=$this->getViewState('cariPoli');
		$cariDokter=$this->getViewState('cariDokter');
		$urutBy=$this->getViewState('urutBy');
			$Company=$this->getViewState('Company');
		$cariTgl=$this->getViewState('cariTgl');
		$cariTglAwal=$this->getViewState('cariTglAwal');
		$cariTglAkhir=$this->getViewState('cariTglAkhir');
		$cariBln=$this->getViewState('cariBln');
		$cariThn=$this->getViewState('cariThn');
		/*$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		/*
		if($this->getViewState('cariTgl')=='' && $this->getViewState('cariTglAwal')=='' && $this->getViewState('cariTglAkhir')=='' && $this->getViewState('cariBln')=='' && $this->getViewState('cariThn')=='')
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,
									$cariPoli,$cariDokter,$urutBy,$Company,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S');
			
		}
		else
		{
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn),'S');
		}
		
		$this->UserGrid->dataBind();
	}
	*/
	
	
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
		$orderBy=$this->getViewState('orderBy');	
		
		/*
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);			
		}else{
			$this->clearViewState('cariByNama');	
		}					
		*/
		if($this->DDKasir->SelectedValue) {
			$this->setViewState('cariByKasir',$this->DDKasir->SelectedValue);
		}else{
			$this->clearViewState('cariByKasir');	
		}
				
		if($this->DDPoli->SelectedValue) {
			$this->setViewState('cariPoli',$this->DDPoli->SelectedValue);
		}else{
			$this->clearViewState('cariPoli');	
		}
		/*
		if($this->DDDokter->SelectedValue) {
			$this->setViewState('cariDokter',$this->DDDokter->SelectedValue);
		}else{
			$this->clearViewState('cariDokter');	
		}*/		
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
		
		
		/*
		if($this->getViewState('cariTgl')=='' && $this->getViewState('cariTglAwal')=='' && $this->getViewState('cariTglAkhir')=='' && $this->getViewState('cariBln')=='' && $this->getViewState('cariThn')=='')
		{
			$i=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->DDUrut->SelectedValue,
									$this->DDKontrak->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S'));
			$this->jmlData->Text=$i;
			$this->UserGrid->VirtualItemCount=$i;
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->DDUrut->SelectedValue,
									$this->DDKontrak->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									date('m'),date('Y')),'S');
			
		}
		else
		{
			$i=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->DDUrut->SelectedValue,
									$this->DDKontrak->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									$this->getViewState('cariBln'),$this->getViewState('cariThn')),'S'));
			$this->jmlData->Text=$i;
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,
									$this->DDPoli->SelectedValue,$this->DDDokter->SelectedValue,$this->DDUrut->SelectedValue,
									$this->DDKontrak->SelectedValue,$this->getViewState('cariTgl'),
									$this->getViewState('cariTglAwal'),$this->getViewState('cariTglAkhir'),
									$this->getViewState('cariBln'),$this->getViewState('cariThn')),'S');
		}
		*/
		
		//$this->UserGrid->dataBind();
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
		
	public function DDKasirChanged($sender,$param)
	{				
		$this->DDPoli->focus();
		
		$this->cariClicked();
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
	
	public function cetakClicked()
	{		
			$cariByKasir=$this->getViewState('cariByKasir');			
			$cariPoli=$this->getViewState('cariPoli');		
			$cariDokter=$this->getViewState('cariDokter');	
			$cariTgl=$this->getViewState('cariTgl');	
			$cariTglAwal=$this->getViewState('cariTglAwal');		
			$cariTglAkhir=$this->getViewState('cariTglAkhir');
			$cariThn = $this->getViewState('cariThn');
			$cariBln = $this->getViewState('cariBln');
			
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtJln',
			array(
				'kasir'=>$cariByKasir,
				'poli'=>$cariPoli,
				'dokter'=>$cariDokter,
				'kelompok'=>$this->DDUrut->SelectedValue,
				'perusahaan'=>$this->DDKontrak->SelectedValue,
				'tgl'=>$cariTgl,
				'tglawal'=>$cariTglAwal,
				'tglakhir'=>$cariTglAkhir,
				'cariBln'=>$cariBln,
				'cariThn'=>$cariThn,
				'periode'=>$this->txtPeriode->Text)));
		
	}		
	protected function refreshMe()
	{
		$this->Reload();
	}
}
/*

	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariPoli,$cariDokter,$urutBy,$Company,$cariTgl,$cariTglAwal,$cariTglAkhir,$cariBln,$cariThn)
	{
		
		if($pil == "1")
		{
			$sql = "SELECT 
					  tbt_kasir_rwtjln.no_trans,
					  tbd_pasien.nama,
					  tbm_nama_tindakan.nama AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbt_kasir_rwtjln.total,
					  tbm_kelompok.nama AS kelompok
					FROM
					  tbt_kasir_rwtjln
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_user ON (tbt_kasir_rwtjln.operator = tbd_user.nip)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_poliklinik ON (tbt_kasir_rwtjln.klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_rwtjln.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
					  			

			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($cariNama <> '')
				$sql .= "AND tbt_kasir_rwtjln.operator = '$cariNama' ";		
						
			if($cariPoli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$cariDokter' ";
			
			if($urutBy <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			
			if($Company <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			
			if($cariTgl <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
			
			//$sql .= " GROUP BY tbt_kasir_rwtjln.id_tindakan ";			
			
			//if($order <> '')							
			//$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
			$sql .= " UNION ALL ";
			$sql .= "SELECT 
					  tbt_kasir_pendaftaran.no_trans,
					  tbd_pasien.nama,
					  tbt_kasir_pendaftaran.id_tindakan AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbt_kasir_pendaftaran.tarif AS total,
					  tbm_kelompok.nama AS kelompok
					FROM
					  tbt_kasir_pendaftaran
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_pendaftaran.no_trans_pdftr = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_user ON (tbt_kasir_pendaftaran.operator = tbd_user.nip)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_poliklinik ON (tbt_kasir_pendaftaran.klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_pendaftaran.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
					  			

			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($cariNama <> '')
				$sql .= "AND tbt_kasir_pendaftaran.operator = '$cariNama' ";		
						
			if($cariPoli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$cariDokter' ";
			
			if($urutBy <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			
			if($Company <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			
			if($cariTgl <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (tbt_kasir_pendaftaran.tgl)='$cariBln' AND YEAR(tbt_kasir_pendaftaran.tgl)='$cariThn' ";	
			
				//$sql .= " GROUP BY tbt_kasir_pendaftaran.id_tindakan ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
				
		}else{
			$sql = "SELECT 
					  tbt_kasir_rwtjln.no_trans,
					  tbd_pasien.nama,
					  tbm_nama_tindakan.nama AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbt_kasir_rwtjln.total,
					  tbm_kelompok.nama AS kelompok
					FROM
					  tbt_kasir_rwtjln
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_user ON (tbt_kasir_rwtjln.operator = tbd_user.nip)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_poliklinik ON (tbt_kasir_rwtjln.klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_rwtjln.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
					  			

			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($cariNama <> '')
				$sql .= "AND tbt_kasir_rwtjln.operator = '$cariNama' ";		
						
			if($cariPoli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$cariDokter' ";
			
			if($urutBy <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			
			if($Company <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			
			if($cariTgl <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$cariBln' AND YEAR(tbt_kasir_rwtjln.tgl)='$cariThn' ";	
			
				//$sql .= " GROUP BY tbt_kasir_rwtjln.id_tindakan ";			
			
			//if($order <> '')							
				//$sql .= " ORDER BY " . $order . ' ' . $sort;
		
			$sql .= " UNION ALL ";
			$sql .= "SELECT 
					  tbt_kasir_pendaftaran.no_trans,
					  tbd_pasien.nama,
					  tbt_kasir_pendaftaran.id_tindakan AS tindakan,
					  tbt_rawat_jalan.cm,
					 tbt_kasir_pendaftaran.tarif AS total,
					  tbm_kelompok.nama AS kelompok
					FROM
					  tbt_kasir_pendaftaran
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_pendaftaran.no_trans_pdftr = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_user ON (tbt_kasir_pendaftaran.operator = tbd_user.nip)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_poliklinik ON (tbt_kasir_pendaftaran.klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_pendaftaran.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
					  			

			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($cariNama <> '')
				$sql .= "AND tbt_kasir_pendaftaran.operator = '$cariNama' ";		
						
			if($cariPoli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";	
			
			if($cariDokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$cariDokter' ";
			
			if($urutBy <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			
			if($Company <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			
			if($cariTgl <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.tgl = '$cariTgl' ";
			
			if($cariTglAwal <> '' AND $cariTglAkhir <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			
			if($cariBln <> '' AND $cariThn <> '')			
				$sql .= "AND MONTH (tbt_kasir_pendaftaran.tgl)='$cariBln' AND YEAR(tbt_kasir_pendaftaran.tgl)='$cariThn' ";	
			
				//$sql .= " GROUP BY tbt_kasir_pendaftaran.id_tindakan ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					
		
		$page=$sql;		 
		//$page=KasirRwtJlnRecord::finder()->findAllBySql($sql);
		//$page = $sql;
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;
	}
	
*/
?>
