<?php
class tarifFisio extends SimakConf
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
	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariKategori,$cariKel)
	{
		if($pil == "1")
		{
			$sql = "SELECT  b.id AS id,
							a.nama AS nama,
							b.tarif AS tarif,
							b.tarif1 AS tarif1,
							b.tarif2 AS tarif2,
							b.tarif3 AS tarif3,
							b.tarif4 AS tarif4,
							b.tarif5 AS tarif5							
						FROM tbm_fisio_tarif b,
							 tbm_fisio_tindakan a
						WHERE	 
							 b.id=a.kode ";
								
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
			
			if($cariKategori <> '')			
				$sql .= "AND a.kategori = '$cariKategori' ";
						
			if($cariKel <> '')			
				$sql .= "AND a.kelompok = '$cariKel' ";
				
			$sql .= " GROUP BY a.kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT  b.id AS id,
							a.nama AS nama,
							b.tarif AS tarif,
							b.tarif1 AS tarif1,
							b.tarif2 AS tarif2,
							b.tarif3 AS tarif3,
							b.tarif4 AS tarif4,
							b.tarif5 AS tarif5							
						FROM tbm_fisio_tarif b,
							 tbm_fisio_tindakan a
						WHERE	 
							 b.id=a.kode ";
								
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
			
			if($cariKategori <> '')			
				$sql .= "AND a.kategori = '$cariKategori' ";
						
			if($cariKel <> '')			
				$sql .= "AND a.kelompok = '$cariKel' ";
				
			$sql .= " GROUP BY a.kode";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					 
		$page=$this->queryAction($sql,'S');
		
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
			$this->DDFisioKateg->DataSource=FisioKategRecord::finder()->findAll();
			$this->DDFisioKateg->dataBind();	
			
			$this->DDFisioKel->DataSource=FisioKelRecord::finder()->findAll();
			$this->DDFisioKel->dataBind();
							
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByKategori=$this->getViewState('cariByKategori'); 
			$cariByKel=$this->getViewState('cariByKel');		
			
			//$this->UserGrid->VirtualItemCount = FisioTdkRecord::finder()->count();
			// fetches all data account information 
			//$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
			// binds the data to interface components
			//$this->UserGrid->dataBind();		
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
			
			FisioTdkRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisio'));
			
		}	
    }	
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			 $this->UserGrid->EditItemIndex=$param->Item->ItemIndex;

			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByKategori=$this->getViewState('cariByKategori'); 
			$cariByKel=$this->getViewState('cariByKel');		
			
			$limit=$this->getViewState('limit');	
			$offset=$this->getViewState('offset');	
			
			
			$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel));
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
			// binds the data to interface components
			$this->UserGrid->dataBind();					
		}	
    }
	
	public function cancelItem($sender,$param)
    {
        $orderBy=$this->getViewState('orderBy');		
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
		$this->UserGrid->EditItemIndex=-1;
        
		$limit=$this->getViewState('limit');	
		$offset=$this->getViewState('offset');
		
		$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel));
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
			// binds the data to interface components
		$this->UserGrid->dataBind();		
		$this->ID->focus();					
    }
	
	public function saveItem($sender,$param)
    {       
		if ($this->User->IsAdmin)
		{
			$item=$param->Item;		
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
							
			$tarifRcd = FisioTarifRecord::finder()->findByPk($ID);
			$tarifRcd->tarif = $item->tarif->TextBox->Text;
			$tarifRcd->tarif1 = $item->tarif1->TextBox->Text;
			$tarifRcd->tarif2 = $item->tarif2->TextBox->Text;
			$tarifRcd->tarif3 = $item->tarif3->TextBox->Text;
			$tarifRcd->tarif4 = $item->tarif4->TextBox->Text;
			$tarifRcd->tarif5 = $item->tarif5->TextBox->Text;
			$tarifRcd->save();
			
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByKategori=$this->getViewState('cariByKategori'); 
			$cariByKel=$this->getViewState('cariByKel');		
			
			$this->UserGrid->EditItemIndex=-1;
			
			$limit=$this->getViewState('limit');	
			$offset=$this->getViewState('offset');
			
			$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel));
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
				// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->ID->focus();	
		}
	}
		
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$this->setViewState('limit',$limit);
		
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		$this->setViewState('offset',$offset);
		
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
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
		if($item->ItemType==='EditItem')
        {
			$item->tarif->TextBox->Columns=5;
			$item->tarif1->TextBox->Columns=5;
			$item->tarif2->TextBox->Columns=5;
			$item->tarif3->TextBox->Columns=5;
			$item->tarif4->TextBox->Columns=5;
			$item->tarif5->TextBox->Columns=5;
		}
    }
	
	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
		$this->UserGrid->dataBind();
	}

	public function useNextPrevPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
		
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
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
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
			
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByKategori,$cariByKel);
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
		$cariByKategori=$this->getViewState('cariByKategori'); 
		$cariByKel=$this->getViewState('cariByKel');		
		
		/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue);
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
		$cariByKategori=$this->getViewState('cariByKategori');
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue);
		$this->UserGrid->dataBind();
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.tarifFisioBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='kode';
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
		
		if($this->DDFisioKateg->SelectedValue) {
			$this->setViewState('cariByKategori', $this->DDFisioKateg->SelectedValue);
		}else{
			$this->clearViewState('cariByKategori');	
		}
		
		$this->UserGrid->CurrentPageIndex = 0;
		
		$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue));
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue);	
		$this->UserGrid->dataBind();
	}
	
	public function DDKategChanged($sender,$param)
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
		
		if($this->DDFisioKateg->SelectedValue) {
			$this->setViewState('cariByKategori', $this->DDFisioKateg->SelectedValue);
		}else{
			$this->clearViewState('cariByKategori');	
		}
		
		if($this->DDFisioKel->SelectedValue) {
			$this->setViewState('cariByKel', $this->DDFisioKel->SelectedValue);
		}else{
			$this->clearViewState('cariByKel');	
		}
		
		$this->UserGrid->CurrentPageIndex = 0;
		
		$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','2',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue));
					
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue);	
		$this->UserGrid->dataBind();
	}
	
	public function DDKelChanged($sender,$param)
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
		
		if($this->DDFisioKateg->SelectedValue) {
			$this->setViewState('cariByKategori', $this->DDFisioKateg->SelectedValue);
		}else{
			$this->clearViewState('cariByKategori');	
		}
		
		if($this->DDFisioKel->SelectedValue) {
			$this->setViewState('cariByKel', $this->DDFisioKel->SelectedValue);
		}else{
			$this->clearViewState('cariByKel');	
		}
		
		$this->UserGrid->CurrentPageIndex = 0;
		
			$this->UserGrid->VirtualItemCount = count($this->getDataRows($offset,$limit,$orderBy,'','2',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue));
						
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDFisioKateg->SelectedValue,$this->DDFisioKel->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}
?>
