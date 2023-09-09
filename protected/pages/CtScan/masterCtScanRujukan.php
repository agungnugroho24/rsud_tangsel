<?php
class masterRadRujukan extends SimakConf
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari)
	{
		if($pil == "1")
		{
			$sql = "SELECT id, 
						   nama
						   FROM tbm_ctscan_rujukan
						   ";
			$sqlAwal=$sql;	
								
			if($cariNama <> '')
				if($sqlAwal==$sql){
					if($tipeCari === true){
						$sql .= "WHERE nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "WHERE nama LIKE '$cariNama%' ";
					}		
				}
				else
				{
					if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
				}
				
			if($cariID <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE id = '$cariID' ";
				}
				else
				{
					$sql .= "AND id = '$cariID' ";
				}			
				
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT id, 
						   nama
						   FROM tbm_ctscan_rujukan
						   ";
			$sqlAwal=$sql;	
								
			if($cariNama <> '')
				if($sqlAwal==$sql){
					if($tipeCari === true){
						$sql .= "WHERE nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "WHERE nama LIKE '$cariNama%' ";
					}		
				}
				else
				{
					if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
				}
				
			if($cariID <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE id = '$cariID' ";
				}
				else
				{
					$sql .= "AND id = '$cariID' ";
				}
			
			$sql .= " GROUP BY id ";	
			if($order <> '')			
				$sql .= " ORDER BY " . $order . ' ' . $sort;

		}					 
		$page=CtScanRujukanRecord::finder()->findAllBySql($sql);
		
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
			
			$this->UserGrid->VirtualItemCount=CtScanRujukanRecord::finder()->count();
			
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy);
			
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
			
			CtScanRujukanRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('CtScan.masterRadRujukan'));
			
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
			
		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy);
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
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
			
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy);
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');		
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
			
		
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy);
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
		$this->setViewState('orderBy',$orderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy);	
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('CtScan.masterRadRujukanBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='id';
		}		
		
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
		
		if($this->Advance->Checked){
			$this->setViewState('elemenBy',$this->Advance->Checked);	
		}else{
			$this->clearViewState('elemenBy');	
		}		
					
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='id';
		}	
		
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
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked);		
		$this->UserGrid->dataBind();
	}
}
?>
