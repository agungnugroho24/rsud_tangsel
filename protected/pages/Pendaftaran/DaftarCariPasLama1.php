<?php
class DaftarCariPasLama1 extends SimakConf
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
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariCM,$tipeCari,$cariAlamat)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   a.alamat,
						   a.kecamatan
						   FROM du_pasien a,
						   		tbm_kecamatan b
						   WHERE a.kecamatan=b.id ";
								
			if($cariNama <> '')			
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
						
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
				
			if($cariCM <> '')
				$sql .= "AND a.cm = '$cariCM' ";
			/*
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";
			*/
			$sql .= " GROUP BY a.cm ";
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   a.alamat,
						   a.kecamatan
						   FROM du_pasien a,
						   		tbm_kecamatan b
						   WHERE a.kecamatan=b.id ";
						   
			if($cariNama <> '')
				if($tipeCari === true){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
			
			if($cariAlamat <> '')			
				if($tipeCari === true){
					$sql .= "AND a.alamat LIKE '%$cariAlamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
				}
							
			if($cariCM <> '')			
				$sql .= "AND a.cm = '$cariCM' ";
				
			/*
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";
			*/					
			
			$sql .= " GROUP BY a.cm ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		//$page=DUPasienRecord::finder()->findAllBySql($sql);
		$page = $sql;
		$this->showSql->Text=$sql;//show SQL Expression broer!
		$this->showSql->Visible=false;//disable SQL Expression broer!
		return $page;
		/*
		$criteria = new TActiveRecordCriteria;
		$criteria->Limit = $rows;
		$criteria->Offset = $offset;
		$page=PegawaiRecord::finder()->findAll($criteria);
		
		return $page;*/
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
			$cariByCM=$this->getViewState('cariByCM');
			//$urutBy=$this->getViewState('urutBy');
			//$companyBy=$this->getViewState('companyBy');
			$elemenBy=$this->getViewState('elemenBy');
			$cariByAlamat=$this->getViewState('cariByAlamat');
			//$this->DDKontrak->Enabled=false;
			//$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			//$this->DDUrut->dataBind();
			//$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			//$this->DDKontrak->dataBind();	
			$this->UserGrid->VirtualItemCount=DUPasienRecord::finder()->count();
			// fetches all data account information 
			//$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat),'S');

			$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat));

			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->cariCM->focus();		
			
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
			
			DUPasienRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarAdmin'));
			
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
		$cariByCM=$this->getViewState('cariByCM');
		//$urutBy=$this->getViewState('urutBy');
		//$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat));
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
		$cariByCM=$this->getViewState('cariByCM');
		//$urutBy=$this->getViewState('urutBy');
		//$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;		
		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat));
		$this->UserGrid->dataBind();
	}	
	
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		//$cariByCM=$this->getViewState('cariByCM');
		//$urutBy=$this->getViewState('urutBy');
		$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat));	
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
		$cariByNama=$this->getViewState('cariByNama');
		$cariByCM=$this->getViewState('cariByCM');
		//$urutBy=$this->getViewState('urutBy');
		//$companyBy=$this->getViewState('companyBy');
		$elemenBy=$this->getViewState('elemenBy');
		$cariByAlamat=$this->getViewState('cariByAlamat');
	/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByCM,$elemenBy,$cariByAlamat));	
		$this->UserGrid->dataBind();	
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}
	/*
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianBaru'));		
	}*/
	
	public function cariClicked($sender,$param)
	{		
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='cm';
		}		
		
		/*
		if($this->DDUrut->SelectedValue){ 		
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);	
		}else{
			$this->clearViewState('companyBy');	
		}
		*/
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->cariAlamat->Text){
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
		}
			
		if($this->cariCM->Text){ 
			$this->setViewState('cariByCM', $this->cariCM->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}
		
		if($this->Advance->Checked){
			$this->setViewState('elemenBy',$this->Advance->Checked);	
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		$arr=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'R');
		$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
		$this->UserGrid->VirtualItemCount=$i;
		
		//$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'S');

		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text));

		$this->UserGrid->dataBind();
	}
	
	/*
	public function selectionChangedUrut($sender,$param)
	{				
		$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
		$this->DDKontrak->dataBind();

		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='cm';
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->cariAlamat->Text){
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
		}
			
		if($this->cariCM->Text){ 
			$this->setViewState('cariByCM', $this->cariCM->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('companyBy');	
		}
				
		if($this->DDUrut->SelectedValue){
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
		{	
			$this->DDKontrak->Enabled=true;	
		}else{
			$this->DDKontrak->Enabled=false;	
		}

		$arr=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'R');
		$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
		$this->UserGrid->VirtualItemCount=$i;

		//$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'S');

		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text));
		$this->UserGrid->dataBind();
	}

	public function DDKontrakChanged($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='cm';
		}	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->cariAlamat->Text){
			$this->setViewState('cariByAlamat', $this->cariAlamat->Text);
		}else{
			$this->clearViewState('cariByAlamat');	
		}
			
		if($this->cariCM->Text){ 
			$this->setViewState('cariByCM', $this->cariCM->Text);	
		}else{
			$this->clearViewState('cariByCM');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDKontrak->SelectedValue){ 		
			$this->setViewState('companyBy', $this->DDKontrak->SelectedValue);
		}else{
			$this->clearViewState('companyBy');	
		}
				
		if($this->DDUrut->SelectedValue){
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		}else{
			$this->clearViewState('urutBy');	
		}
			
		if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
		{	
			$this->DDKontrak->Enabled=true;	
		}else{
			$this->DDKontrak->Enabled=false;	
		}

		$arr=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'R');
		$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
		$this->UserGrid->VirtualItemCount=$i;

		//$this->UserGrid->DataSource=$this->queryAction($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text),'S');

		$this->UserGrid->DataSource=$page=DUPasienRecord::finder()->findAllBySql($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->cariCM->Text,$this->Advance->Checked,$this->cariAlamat->Text));
		$this->UserGrid->dataBind();
	}
	*/
}
?>
