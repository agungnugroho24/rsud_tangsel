<?php
class TarifTindakan extends SimakConf
{   
	private $sortExp = "a.id ";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariByKlinik)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.id, 
						   b.nama, 
						   a.biaya1,
						   a.biaya2,
						   a.biaya3,
						   a.biaya4,
						   c.nama AS klinik,
						   (a.biaya1 + a.biaya2 + a.biaya3) AS total
						   FROM tbm_tarif_tindakan a,
						   		tbm_nama_tindakan b,
								tbm_poliklinik c						        						   										
						   WHERE a.id=b.id AND b.id_klinik=c.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND b.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND b.nama LIKE '$cariNama%' ";
				}		
				
			if($cariID <> '')
				$sql .= "AND a.id = '$cariID' ";			
			
			if($cariByKlinik <> '')			
				$sql .= "AND b.id_klinik = '$cariByKlinik' ";					
				
			$sql .= " GROUP BY a.id ";
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.id, 
						   b.nama, 
						   a.biaya1,
						   a.biaya2,
						   a.biaya3,
						   a.biaya4,
						   c.nama AS klinik,
						   (a.biaya1 + a.biaya2 + a.biaya3) AS total
						   FROM tbm_tarif_tindakan a,
						   		tbm_nama_tindakan b,
								tbm_poliklinik c						        						   										
						   WHERE a.id=b.id AND b.id_klinik=c.id ";
						   
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND b.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND b.nama LIKE '$cariNama%' ";
				}		
				
			if($cariID <> '')
				$sql .= "AND a.id = '$cariID' ";			
			
			if($cariByKlinik <> '')			
				$sql .= "AND b.id_klinik = '$cariByKlinik' ";										
			
			$sql .= " GROUP BY a.id ";	
			if($order <> '')			
				$sql .= " ORDER BY " . $order . ' ' . $sort;

		}					 
		$page=TarifTindakanRecord::finder()->findAllBySql($sql);
		
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
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');			
			$elemenBy=$this->getViewState('elemenBy');			
			$cariByKlinik=$this->getViewState('cariByKlinik');			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			
			$this->nama->focus();		
			
			$this->bindGrid();									
			$this->ID->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
		}		
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
          $ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
          // deletes the user record with the specified username primary key
          $sql = "DELETE FROM tbm_tarif_tindakan WHERE id='$ID'";
          $this->queryAction($sql,'C');
    			//PasienRecord::finder()->deleteByCm($ID);
          //RwtjlnRecord::finder()->deleteBy($ID);	
    	 $this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));
        }	
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


    // get data and bind it to datagrid
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
			
			$sql = "SELECT a.id, 
						   b.nama, 
						   a.biaya1,
						   a.biaya2,
						   a.biaya3,
						   a.biaya4,
						   c.nama AS klinik,
						   (a.biaya1 + a.biaya2 + a.biaya3) AS total
						   FROM tbm_tarif_tindakan a,
						   		tbm_nama_tindakan b,
								tbm_poliklinik c						        						   										
						   WHERE a.id=b.id AND b.id_klinik=c.id ";
								
			if($this->getViewState('cariByNama') <> '')	
			{	
				$cariNama = $this->getViewState('cariByNama');		
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND b.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND b.nama LIKE '$cariNama%' ";
				}		
			}
			
			if($this->getViewState('cariByID') <> '')
			{
				$cariID = $this->getViewState('cariByID');
				$sql .= "AND a.id = '$cariID' ";			
			}	
				
						
			if($this->getViewState('cariByKlinik') <> '')
			{
				$cariByKlinik = $this->getViewState('cariByKlinik');			
				$sql .= "AND b.id_klinik = '$cariByKlinik' ";					
			}	
			
			$sql .= " GROUP BY a.id ";		
		
			 
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
        
        // many people won't set this to the first page. this can lead to usability problems.
        // think in what happens if the user is on the 3rd page and changes the sorting field. 
        // you will sort the items on that page if you are using cached data (either in session or "true" cache). 
        // imagine now that the user moves on to page 4. the data on page 4 will be sorted out but it will be 
        // sorted disregarding the other items in other pages. other pages could have items that are "lower" or 
        // "bigger" than the ones displayed. You could have items with the sorting field starting with letter "C" 
        // on page 3 and on page 4 items with the sorting field starting with letter "A". 
        // you could sort all the cached data to solve this but then what page you will show to the user? stick with page 3?
        // I find it better to refresh the data and allways move on to the first page.
        
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Tarif.TarifTindakanBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='id';
		}		
		
		if($this->nama->Text){
			$this->setViewState('cariByNama', $this->nama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		
		if($this->DDKlinik->SelectedValue){ 		
			$this->setViewState('cariByKlinik', $this->DDKlinik->SelectedValue);	
		}else{
			$this->clearViewState('cariByKlinik');	
		}
				
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}
		
		if($this->Advance->Checked){
			$this->setViewState('elemenBy',$this->Advance->Checked);	
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		$this->bindGrid();	
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='id';
		}	
		
		if($this->nama->Text){
			$this->setViewState('cariByNama', $this->nama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		
		if($this->DDKlinik->SelectedValue){ 		
			$this->setViewState('cariByKlinik', $this->DDKlinik->SelectedValue);	
		}else{
			$this->clearViewState('cariByKlinik');	
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
		
		$this->bindGrid();	
	}
}
?>
