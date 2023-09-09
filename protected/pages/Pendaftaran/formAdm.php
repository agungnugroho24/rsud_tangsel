<?php
class formAdm extends SimakConf
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariID,$cariNm,$tipeCari)
	{
		if($pil == "1")
		{
			$sql = "SELECT id, nama  FROM tbm_kabupaten ";
								
			if($cariID != '' || $cariNm != '')
				$sql .= "WHERE ";
				
			if($cariID <> '')
				$sql .= "id = '$cariID' ";
						
			if($cariNm <> '')			
				if($tipeCari === true){
					$sql .= "nama LIKE '%$cariNm%' ";
				}
				else
				{	
					$sql .= "nama LIKE '$cariNm%' ";
				}	
			
			if($order <> '')							
				$sql .= " ORDER BY id";
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT id, nama  FROM tbm_kabupaten ";
			
			if($cariID != '' || $cariNm != '')
				$sql .= "WHERE ";
				
			if($cariID <> '')
				$sql .= "id = '$cariID' ";
						
			if($cariNm <> '')			
				if($tipeCari === true){
					$sql .= "nama LIKE '%$cariNm%' ";
				}
				else
				{	
					$sql .= "nama LIKE '$cariNm%' ";
				}	
			
			if($order <> '')							
				$sql .= " ORDER BY id";

		}					 
		$page=KabupatenRecord::finder()->findAllBySql($sql);
		
		$this->showSql->Text=$sql;//show SQL Expression broer!
		$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;		
	} 
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */   
	public function onLoad($param)
	{				
		parent::onLoad($param);				
		if(!$this->IsPostBack)
		{					
			if($this->Request['mode'] == '1')
			{
				$kabRecord=$this->KabupatenRecord;
				$this->idKab->Text=$kabRecord->id;
				$this->nmKab->Text=$kabRecord->nama;
			}
			$orderBy=$this->getViewState('orderBy');		
			$cariNm=$this->getViewState('cariNm');
			$cariID=$this->getViewState('cariID');
			$elemenBy=$this->getViewState('elemenBy');
			
			$this->UserGrid->VirtualItemCount=KabupatenRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariID,$cariNm,$elemenBy);	
			$this->UserGrid->dataBind();			
			$this->idKab->focus();		
			
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
			
			KabupatenRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.formAdm'));
			
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
		$cariNm=$this->getViewState('cariNm');
		$cariID=$this->getViewState('cariID');
		$elemenBy=$this->getViewState('elemenBy');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariID,$cariNm,$elemenBy);	
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
		$cariNm=$this->getViewState('cariNm');
		$cariID=$this->getViewState('cariID');
		$elemenBy=$this->getViewState('elemenBy');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariID,$cariNm,$elemenBy);	
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariNm=$this->getViewState('cariNm');
		$cariID=$this->getViewState('cariID');
		$elemenBy=$this->getViewState('elemenBy');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariID,$cariNm,$elemenBy);	
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
		$cariNm=$this->getViewState('cariNm');
		$cariID=$this->getViewState('cariID');
		$elemenBy=$this->getViewState('elemenBy');
	
	
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariID,$cariNm,$elemenBy);	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	protected function simpanClicked()
	{		
		if($this->Request['id'] || $this->getViewState('id'))
		{
			$kabRecord=$this->KabupatenRecord;
			$kabRecord->id=$this->idKab->Text;
			$kabRecord->nama=$this->nmKab->Text;
			$kabRecord->Save();
		}else{
			$kabRecord=new KabupatenRecord();
			$kabRecord->id=$this->numUrut('tbm_kabupaten',KabupatenRecord::finder(),'2');
			$kabRecord->nama=$this->nmKab->Text;
			$kabRecord->Save();
		}
		$this->clearViewState('id');
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.formAdm'));
	}
	
	protected function batalClicked()
	{
		$this->clearViewState('id');
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.formAdm'));
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='id';
		}		
		
		if($this->idKab->Text){
			$this->setViewState('cariID', $this->idKab->Text);
		}else{
			$this->clearViewState('cariID');	
		}
			
		if($this->nmKab->Text){
			$this->setViewState('cariNm', $this->nmKab->Text);
		}else{
			$this->clearViewState('cariNm');	
		}
		
		if($this->Advance->Checked){
			$this->setViewState('elemenBy',$this->Advance->Checked);	
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->idKab->Text,$this->nmKab->Text,$this->Advance->Checked);	
		$this->UserGrid->dataBind();
	}
	
	protected function getKabupatenRecord()
	{
		// use Active Record to look for the specified username
		$id=$this->Request['id'];
		$this->setViewState('id',$id);
		$kabRecord=KabupatenRecord::finder()->findByPk($id);		
		if(!($kabRecord instanceof KabupatenRecord))
			throw new THttpException(500,'id tidak benar.');
		return $kabRecord;
	}	
}
?>
