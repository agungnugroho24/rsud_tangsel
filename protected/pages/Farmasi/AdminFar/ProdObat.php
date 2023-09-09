<?php
class ProdObat extends SimakConf
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
	 
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariAlamat,$tipeCari2,$cariTlp)
	{
		if($pil == "1")
		{
			$sql = "SELECT id, 
						   nama,
						   alamat,
						   tlp
						   FROM tbm_produsen_obat
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
			
			if($cariAlamat <> '')
				if($sqlAwal==$sql){
					if($tipeCari2 === true){
						$sql .= "WHERE alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "WHERE alamat LIKE '$cariAlamat%' ";
					}		
				}
				else
				{
					if($tipeCari2 === true){
						$sql .= "AND alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "AND alamat LIKE '$cariAlamat%' ";
					}		
				}
			
			if($cariTlp <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE tlp = '$cariTlp' ";
				}
				else
				{
					$sql .= "AND tlp = '$cariTlp' ";
				}
				
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT id, 
						   nama,
						   alamat,
						   tlp
						   FROM tbm_produsen_obat
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
			
			if($cariAlamat <> '')
				if($sqlAwal==$sql){
					if($tipeCari2 === true){
						$sql .= "WHERE alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "WHERE alamat LIKE '$cariAlamat%' ";
					}		
				}
				else
				{
					if($tipeCari2 === true){
						$sql .= "AND alamat LIKE '%$cariAlamat%' ";
					}
					else
					{	
						$sql .= "AND alamat LIKE '$cariAlamat%' ";
					}		
				}
			
			if($cariTlp <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE tlp = '$cariTlp' ";
				}
				else
				{
					$sql .= "AND tlp = '$cariTlp' ";
				}
				
			$sql .= " GROUP BY id ";	
			if($order <> '')			
				$sql .= " ORDER BY " . $order . ' ' . $sort;

		}					 
		$page=ProdusenObatRecord::finder()->findAllBySql($sql);
		
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
			$cariByAlamat=$this->getViewState('cariByAlamat');
			$elemen2By=$this->getViewState('elemen2By');		
			$cariByTlp=$this->getViewState('cariByTlp');
			
			$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
			
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp);
			
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
			
			ProdusenObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.ProdObat'));
			
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
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$elemen2By=$this->getViewState('elemen2By');	
		$cariByTlp=$this->getViewState('cariByTlp');
		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp);
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
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$elemen2By=$this->getViewState('elemen2By');	
		$cariByTlp=$this->getViewState('cariByTlp');
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp);
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');		
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');			
		$elemenBy=$this->getViewState('elemenBy');			
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$elemen2By=$this->getViewState('elemen2By');
		$cariByTlp=$this->getViewState('cariByTlp');
		
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp);
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
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$elemen2By=$this->getViewState('elemen2By');		
		$cariByTlp=$this->getViewState('cariByTlp');
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByAlamat,$elemen2By,$cariByTlp);	
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFarmasi'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.ProdObatBaru'));		
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
		
		if($this->cariAlamat->Text){ 
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);	
		}else{
			$this->clearViewState('cariByAlamat');	
		}
		
		if($this->Advance2->Checked){
			$this->setViewState('elemen2By',$this->Advance2->Checked);	
		}else{
			$this->clearViewState('elemen2By');	
		}
		
		if($this->tlp->Text){ 
			$this->setViewState('cariByTlp', $this->tlp->Text);	
		}else{
			$this->clearViewState('cariByTlp');	
		}
		
		$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->cariAlamat->Text,$this->Advance2->Checked,$this->tlp->Text));
			
		$this->jmlData->Text=$jmlData;
		$this->UserGrid->VirtualItemCount=$jmlData;
						
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->cariAlamat->Text,$this->Advance2->Checked,$this->tlp->Text);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='ID';
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
		
		if($this->cariAlamat->Text){ 
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);	
		}else{
			$this->clearViewState('cariByAlamat');	
		}
		
		if($this->Advance2->Checked){
			$this->setViewState('elemen2By',$this->Advance2->Checked);	
		}else{
			$this->clearViewState('elemen2By');	
		}
		
		if($this->tlp->Text){ 
			$this->setViewState('cariByTlp', $this->tlp->Text);	
		}else{
			$this->clearViewState('cariByTlp');	
		}
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->cariAlamat->Text,$this->Advance2->Checked,$this->tlp->Text);		
		$this->UserGrid->dataBind();
	}
}
?>
