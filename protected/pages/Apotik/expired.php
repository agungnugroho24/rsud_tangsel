<?php
class expired extends SimakConf
{  	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
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
			
			$dateNow = date('Y-m-d');
			$tujuan = $this->modeStok->SelectedValue;
				
				/*
			$sql = "SELECT 
					  tbt_obat_harga.kode,
					  tbt_stok_lain.tujuan,
					  tbm_destinasi_farmasi.nama AS nm_tujuan,
					  tbt_stok_lain.jumlah,
					  tbt_obat_masuk.tgl_exp AS tgl,
					  tbt_obat_masuk.no_batch,
					  tbt_obat_masuk.no_faktur,
					  tbt_obat_masuk.tgl_faktur AS tgl_faktur,
					  (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) AS bln,
					  tbm_obat.nama
					FROM
					  tbt_obat_harga
					  INNER JOIN tbt_stok_lain ON (tbt_obat_harga.id = tbt_stok_lain.id_harga)
					  INNER JOIN tbm_destinasi_farmasi ON (tbt_stok_lain.tujuan = tbm_destinasi_farmasi.id)
					  INNER JOIN tbt_obat_masuk ON (tbt_obat_harga.tgl = tbt_obat_masuk.tgl_terima)
					  AND (tbt_obat_harga.kode = tbt_obat_masuk.id_obat)
					  INNER JOIN tbm_obat ON (tbt_obat_harga.kode = tbm_obat.kode)
					WHERE
					  tbt_obat_masuk.tgl_exp <> 0000-00-00
					  AND tbt_stok_lain.jumlah > 0
					  AND tbt_obat_masuk.tgl_exp > '$dateNow'
					  AND (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) <= 3 ";
					  */
				
				$sql = "SELECT 
					  tbt_obat_harga.kode,
					  tbt_stok_lain.tujuan,
					  tbm_destinasi_farmasi.nama AS nm_tujuan,
					  tbt_stok_lain.jumlah,
					  tbt_stok_lain.expired AS tgl,
					  tbt_obat_masuk.no_batch,
					  tbt_obat_masuk.no_faktur,
					  tbt_obat_masuk.tgl_faktur AS tgl_faktur,
					  (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) AS bln,
					  tbm_obat.nama
					FROM
					  tbt_obat_harga
					  INNER JOIN tbt_stok_lain ON (tbt_obat_harga.id = tbt_stok_lain.id_harga)
					  INNER JOIN tbm_destinasi_farmasi ON (tbt_stok_lain.tujuan = tbm_destinasi_farmasi.id)
					  LEFT JOIN tbt_obat_masuk ON (tbt_stok_lain.expired = tbt_obat_masuk.tgl_exp)
					  AND (tbt_obat_harga.kode = tbt_obat_masuk.id_obat)
					  INNER JOIN tbm_obat ON (tbt_obat_harga.kode = tbm_obat.kode)
					WHERE
					  tbt_stok_lain.expired <> 0000-00-00
					  AND tbt_stok_lain.jumlah > 0
					  AND (YEAR(tbt_stok_lain.expired) - YEAR(CURDATE())) * 12 + (MONTH(tbt_stok_lain.expired) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_stok_lain.expired) < DAYOFMONTH(CURDATE()), 1, 0) <= 3 ";
					  
					  //AND tbt_stok_lain.expired > '$dateNow'
					  //AND (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) <= 3 ";
					  	  
			if($tujuan<>'')
			{
				$sql .= " AND tbt_stok_lain.tujuan = '$tujuan' ";
			}				  
				
			
			//$this->showSql->Text=$sql;  
			
			$data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();	
			
			//$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglAwal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglAkhir->Text,'2'),'3');
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakLapBtn->Enabled = true;
			}
			else
			{
				$this->cetakLapBtn->Enabled = false;
			}
			
		}
        else
		{
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
        $item=$param->Item;	
		 
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	   
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
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
		
		if(!$this->IsPostBack)
		{	
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->modeStok->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->modeStok->dataBind();
			
			$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
		
    }
	
	public function modeInputChanged($sender,$param)
	{
		$this->cariClicked();
	}
	
	public function cariClicked()
	{	
			
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}		
	
	public function cetakClicked()
	{	
		
		$tujuan = $this->modeStok->SelectedValue;
		
		$this->Response->redirect($this->Service->constructUrl('Apotik.cetakExpired',array('tujuan'=>$tujuan)));
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->dtgSomeData->DataSource='';
			$this->dtgSomeData->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
}
?>
