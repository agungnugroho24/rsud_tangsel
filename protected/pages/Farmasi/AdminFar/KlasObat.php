<?php
class KlasObat extends SimakConf
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
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariGol)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.id,
						   a.jenis,
						   a.gol_id,
						   b.nama AS gol_id
						FROM tbm_klasifikasi_obat a,
						     tbm_golongan_obat b
						WHERE	 
							 a.gol_id=b.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.jenis LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.jenis LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.id = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND a.gol_id = '$cariGol' ";
				
			$sql .= " GROUP BY id";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.id,
						   a.jenis,
						   a.gol_id,
						   b.nama AS gol_id
						FROM tbm_klasifikasi_obat a,
						     tbm_golongan_obat b
						WHERE	 
							 a.gol_id=b.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.jenis LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.jenis LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.id = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND a.gol_id = '$cariGol' ";
				
			$sql .= " GROUP BY id";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					 
		$page=KlasifikasiObatRecord::finder()->findAllBySql($sql);
		
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
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
							
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');		
			
			$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->ID->focus();		
			
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
			
			KlasifikasiObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObat'));
			
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
		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByGol);
		$this->UserGrid->dataBind();		
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
		$cariByGol=$this->getViewState('cariByGol');		
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol);
		$this->UserGrid->dataBind();
	}

	public function useNextPrevPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');		
		
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol);
		$this->UserGrid->dataBind();
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
			
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol);
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
		$cariByGol=$this->getViewState('cariByGol');		
		
		/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue);
		$this->UserGrid->dataBind();	
	}
	
	/**
     * Showing a specified user coloumn.
     * This method responds to the datagrid's toggleColumnVisibility event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	public function toggleColumnVisibility($sender,$param)
	{		
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');
		$cariByGol=$this->getViewState('cariByGol');
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue);
		$this->UserGrid->dataBind();
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFarmasi'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.KlasObatBaru'));		
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
		
		$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
					
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedKec($sender,$param)
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
					
		$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
						
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}
?>
