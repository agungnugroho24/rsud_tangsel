<?php
class MasterStokGudang2 extends SimakConf
{   	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	

   	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
		if(!$this->IsPostBack)
		{
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();
			
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
			
			$this->bindGrid();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;	
		}
		
	}
	/*
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
							
			$orderBy=$this->getViewState('orderBy');
			$limit=$this->getViewState('limit');
			$offset=$this->getViewState('offset');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');
			//$sumber=$this->getViewState('sumber');			
			$tujuan=$this->getViewState('tujuan');
			$tipe=$this->getViewState('tipe');
			$kategori=$this->getViewState('kategori');
			
			
			if(!$this->IsPostBack)
			{
				$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
				$this->DDTujuan->dataBind();
				
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
					
			}else{
				$jmlData=count($this->getDataRows($offset,$limit,$orderBy,$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$tipe,$kategori));
				
				$this->jmlData->Text=$jmlData;
				$this->UserGrid->VirtualItemCount=$jmlData;
			
				// fetches all data account information 
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$tipe,$kategori);
				// binds the data to interface components
				$this->UserGrid->dataBind();
			}		
			$this->ID->focus();		
			
			
    }*/		
	
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
		$this->cariClicked($sender,$param);
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
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		$this->cariClicked($sender,$param);
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();	
		$this->cariClicked($sender,$param);	
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
		$this->cariClicked($sender,$param);		
	}
	
	public function DDTujuanChanged($sender,$param)
	{
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}	
		// $this->bindGrid();			
		$this->cariClicked($sender,$param);
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
		$this->setViewState('limit', $this->UserGrid->PageSize);		
			
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;	
		$this->setViewState('offset', $offset);	
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
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
		
		if($this->getViewState('offsetEdit')!='')
		{
			$this->dtgSomeData->CurrentPageIndex = $this->getViewState('offsetEdit') - 1;
			$this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;		
			
			$this->clearViewState('offsetEdit');	
		}
		else
		{
			 $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
		}  
		
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();           
			$someDataList->offset = $this->offset;		
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$cariNama=$this->getViewState('cariByNama');
			$cariID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariGol=$this->getViewState('cariByGol');
			$cariKlas=$this->getViewState('cariByKlas');
			$cariDerivat=$this->getViewState('cariByDerivat');
			$cariPbf=$this->getViewState('cariByPbf');
			$cariProd=$this->getViewState('cariByProd');
			$cariSat=$this->getViewState('cariBySat');
			//$sumber=$this->getViewState('sumber');			
			//$tujuan=$this->getViewState('tujuan');
			$tipe=$this->getViewState('tipe');
			$kategori=$this->getViewState('kategori');
			
            $sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						   c.hrg_ppn AS hrg,
						    ";
			if($sumber <> '')
			{
				$sql .= "b.jumlah AS jumlah,
						   b.sumber AS sumber ";				
			}else{
				$sql .= "SUM(b.jumlah) AS jumlah,
						   b.sumber AS sumber ";
			}			   
			//$tujuan=$this->getViewState('tujuan');
			if ($this->getViewState('tujuan') == '1')
			{
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_gudang b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							c.kode=a.kode  ";
			}else{	
				$tujuan=$this->getViewState('tujuan');					   
				//$tujuan='2';
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_lain b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND b.tujuan='$tujuan' 
							AND a.kode=c.kode ";				
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
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";
			
			if($kategori <> '')			
				$sql .= "AND a.kategori = '$kategori' ";		
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
				
			$sql .= " GROUP BY a.kode ";			
			/*
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		
			$page=$this->queryAction($sql,'S');
			
			$sql .= " GROUP BY kode";      */
			 
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
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='EditItem')
        {
			$item->harga->TextBox->Columns=5;
			$item->jml->TextBox->Columns=5;
        }       
    }
	
	public function editItem($sender,$param)
    {
		$this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	public function cancelItem($sender,$param)
    {        
		$this->dtgSomeData->EditItemIndex=-1; 
		$this->bindGrid();       
    }
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//ObatRecord::finder()->FindByPk($ID);
		$idObat=$this->dtgSomeData->DataKeys[$item->ItemIndex];
		//$sumberObat=$item->sumberMaster->TextBox->Text;
		$sumberObat = $this->getViewState('sumber');
		/*
		$update=StokGudangRecord::finder()->find('id_obat = ? AND sumber = ?', $idObat, $sumberObat);
		
		$update->jumlah=$item->jml->TextBox->Text; 
		$update->save();
		*/
		$jumlah = $item->jml->TextBox->Text;
		$harga = $item->harga->TextBox->Text;
		if($harga)
		{
			$hrgNett = $harga/1.1;
			$sql="UPDATE tbt_obat_harga SET hrg_ppn='$harga', hrg_netto='$hrgNett' WHERE kode='$idObat' AND sumber='01' ";
			$this->queryAction($sql,'C');
		}	
		
		if($this->DDTujuan->SelectedValue=='1')
		{
			$sql="UPDATE tbt_stok_gudang SET jumlah='$jumlah' WHERE id_obat='$idObat' ";
		}
		else
		{
			$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah' WHERE id_obat='$idObat' ";
		}
		
		$this->queryAction($sql,'C'); 
		
      //  $this->update(
       //     $this->UserGrid->DataKeys[$item->ItemIndex],    
       //     $item->jml->TextBox->Text
       //     );
        $this->dtgSomeData->EditItemIndex=-1; 
		$this->bindGrid();       
		
    }
	/*
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);
	}*/
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		$orderBy=$this->getViewState('orderBy');	
		
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
		 $this->bindGrid();				
		
	}
		
	public function DDPbfChanged($sender,$param)
	{		
		 $this->cariClicked($sender,$param);
	}
	
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
