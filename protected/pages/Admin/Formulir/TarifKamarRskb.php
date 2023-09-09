<?php
class TarifKamarRskb extends SimakConf
{   	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari)
	{
		if($pil == "1")
		{
		
			$sql = "SELECT 
					  tbm_kamar_harga.id_kmr AS id_kmr,
					  tbm_kamar_nama.nama AS nama
					  
					FROM
					  tbm_kamar_harga
					  INNER JOIN tbm_kamar_nama ON (tbm_kamar_harga.id_kmr = tbm_kamar_nama.id)
						   ";
						   /*
		tbm_kamar_harga.vip AS vip,
					  tbm_kamar_harga.kelas1 AS kelas1,
					  tbm_kamar_harga.kelas2 AS kelas2,
					  tbm_kamar_harga.kelas3 AS kelas3
		*/
			$sqlAwal=$sql;	
								
			if($cariNama <> '')
				if($sqlAwal==$sql){
					if($tipeCari === true){
						$sql .= "WHERE tbm_kamar_nama.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "WHERE tbm_kamar_nama.nama LIKE '$cariNama%' ";
					}		
				}
				else
				{
					if($tipeCari === true){
						$sql .= "AND tbm_kamar_nama.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND tbm_kamar_nama.nama LIKE '$cariNama%' ";
					}		
				}
				
			if($cariID <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE tbm_kamar_harga.id_kmr = '$cariID' ";
				}
				else
				{
					$sql .= "AND tbm_kamar_harga.id_kmr = '$cariID' ";
				}			
				
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT 
					  tbm_kamar_harga.id_kmr AS id_kmr,
					  tbm_kamar_nama.nama AS nama
					  
					FROM
					  tbm_kamar_harga
					  INNER JOIN tbm_kamar_nama ON (tbm_kamar_harga.id_kmr = tbm_kamar_nama.id)
						   ";
						   /*
						   tbm_kamar_harga.vip AS vip,
					  tbm_kamar_harga.kelas1 AS kelas1,
					  tbm_kamar_harga.kelas2 AS kelas2,
					  tbm_kamar_harga.kelas3 AS kelas3
					  */
			$sqlAwal=$sql;	
								
			if($cariNama <> '')
				if($sqlAwal==$sql){
					if($tipeCari === true){
						$sql .= "WHERE tbm_kamar_nama.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "WHERE tbm_kamar_nama.nama LIKE '$cariNama%' ";
					}		
				}
				else
				{
					if($tipeCari === true){
						$sql .= "AND tbm_kamar_nama.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND tbm_kamar_nama.nama LIKE '$cariNama%' ";
					}		
				}
				
			if($cariID <> '')
				if($sqlAwal==$sql){
					$sql .= "WHERE tbm_kamar_harga.id_kmr = '$cariID' ";
				}
				else
				{
					$sql .= "AND tbm_kamar_harga.id_kmr = '$cariID' ";
				}
			
			$sql .= " GROUP BY tbm_kamar_harga.id_kmr ";	
			if($order <> '')			
				$sql .= " ORDER BY " . $order . ' ' . $sort;

		}					 
		//$page=KamarHargaRecord::finder()->findAllBySql($sql);
		$page = $sql;
		//$this->showSql->Text=$sql;//show SQL Expression broer!
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
			
			//$this->UserGrid->VirtualItemCount=KamarHargaRecord::finder()->count();
			$jmlData=count($this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy),'S'));
			$this->UserGrid->VirtualItemCount=$jmlData;
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy),'S');
			
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
			
			KamarHargaRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamar'));
			
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
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy);
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy),'S');
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
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy);
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy),'S');
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
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy);
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy),'S');
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
		
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy);	
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy),'S');
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifKamarBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='tbm_kamar_harga.id_kmr';
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
					
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked);	
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked),'S');
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='tbm_kamar_harga.id_kmr';
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
		
		//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked);		
		$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked),'S');
		$this->UserGrid->dataBind();
	}
}
?>
