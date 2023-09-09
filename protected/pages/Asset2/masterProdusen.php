<?php
class masterProdusen extends SimakConf
{   

	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariAlamat,$cariID,$tipeCari,$tipeCari2)
	{
		if($pil == "1")
		{
			$sql = "SELECT 
						id,
						nama,
						alamat,
						tlp,
						fax				   
					FROM 
						tbm_asset_produsen
					WHERE
						id <> ''";
			
			if($cariID <> '')				
				$sql .= "AND id = '$cariID' ";				
			
			if($cariNama <> '')
				{
					if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
				}
											
			if($cariAlamat <> '')
				{
					if($tipeCari2 === true){
						$sql .= "AND alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "AND alamat LIKE '$cariAlamat%' ";
					}		
				}
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT 
						id,
						nama,
						alamat,
						tlp,
						fax				   
					FROM 
						tbm_asset_produsen
					WHERE
						id <> ''";
								
			if($cariID <> '')				
				$sql .= "AND id = '$cariID' ";				
			
			if($cariNama <> '')
				{
					if($tipeCari === true){
						$sql .= "AND nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND nama LIKE '$cariNama%' ";
					}		
				}
											
			if($cariAlamat <> '')
				{
					if($tipeCari2 === true){
						$sql .= "AND alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "AND alamat LIKE '$cariAlamat%' ";
					}		
				}
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			$sql .= " GROUP BY id ";	
			
		}					 
		
		$page=$sql;
		
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
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('menu'));
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$orderBy=$this->getViewState('orderBy');
			$cariByNama=$this->getViewState('cariByNama');		
			$cariByAlamat=$this->getViewState('cariByAlamat');
			$cariByID=$this->getViewState('cariByID');			
			$elemenBy=$this->getViewState('elemenBy');
			$elemenBy2=$this->getViewState('elemenBy2');
			
			$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByAlamat,$cariByID,$elemenBy,$elemenBy2),'S'));
			$this->jmlData->Text=$jmlData;
			
			$this->UserGrid->VirtualItemCount=$jmlData;
			
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByAlamat,$cariByID,$elemenBy,$elemenBy2),'S');
			
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->ID->focus();		
			
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
			
			AssetProdusenRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Asset.masterProdusen'));
			
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
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');		
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
		$elemenBy2=$this->getViewState('elemenBy2');
		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		$limit=$this->UserGrid->PageSize;
		
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByAlamat,$cariByID,$elemenBy,$elemenBy2),'S');
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
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
		$elemenBy2=$this->getViewState('elemenBy2');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByAlamat,$cariByID,$elemenBy,$elemenBy2),'S');
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
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');
		$elemenBy2=$this->getViewState('elemenBy2');
		
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByAlamat,$cariByID,$elemenBy,$elemenBy2),'S');
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterProdusenBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
	
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		$limit=$this->UserGrid->PageSize;
		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='ID';
		}		
		
		if($this->nm->Text){
			$this->setViewState('cariByNama', $this->nm->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}		
		
		if($this->Alamat->Text){
			$this->setViewState('cariByAlamat', $this->Alamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
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
		
		if($this->Advance2->Checked){
			$this->setViewState('elemenBy2',$this->Advance2->Checked);	
		}else{
			$this->clearViewState('elemenBy2');	
		}
		
		$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->nm->Text,$this->Alamat->Text,$this->ID->Text,$this->Advance->Checked,$this->Advance2->Checked),'S'));
		$this->jmlData->Text=$jmlData;
		
		$this->UserGrid->VirtualItemCount=$jmlData;
			
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->nm->Text,$this->Alamat->Text,$this->ID->Text,$this->Advance->Checked,$this->Advance2->Checked),'S');	
		$this->UserGrid->dataBind();
	}
	
	
}
?>