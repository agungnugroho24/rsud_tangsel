<?php
class MasterStokGudang extends SimakConf
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
	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariID,$tipeCari,$cariGol,$cariKlas,$cariDerivat,$cariPbf,$cariProd,$cariSat,$sumber)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						    ";
			if($sumber <> '')
			{
				$sql .= "b.jumlah AS jumlah,
						   b.sumber AS sumber ";				
			}else{
				$sql .= "SUM(b.jumlah) AS jumlah,
						   b.sumber AS sumber ";
			}			   
						   
				$sql .=	"	FROM tbm_obat a,							
							tbt_stok_gudang b
						WHERE	 							
							a.kode=b.id_obat  ";
								
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
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";				
			
			$sql .= " GROUP BY a.kode ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						    ";
			if($sumber <> '')
			{
				$sql .= "b.jumlah AS jumlah,
						   b.sumber AS sumber ";				
			}else{
				$sql .= "SUM(b.jumlah) AS jumlah,
						   b.sumber AS sumber ";
			}			   
						   
				$sql .=	"	FROM tbm_obat a,							
							tbt_stok_gudang b
						WHERE	 							
							a.kode=b.id_obat  ";
								
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
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";					
			
			$sql .= " GROUP BY a.kode ";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					
		
		$page=$this->queryAction($sql,'S');
		 
		//$page=ObatRecord::finder()->findAllBySql($sql);
		
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
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll();
			$this->DDTujuan->dataBind();	
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();	
							
			$orderBy=$this->getViewState('orderBy');			
			$cariByNama=$this->getViewState('cariByNama');
			$cariByID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');
			$sumber=$this->getViewState('sumber');			
			
			$this->UserGrid->VirtualItemCount=ObatRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->ID->focus();		
			
		}		
    }		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else
		{
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			$this->DDKlas->Enabled=false;
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}
		
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
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
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');	
		$sumber=$this->getViewState('sumber');	
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
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
            // set column width of textboxes
           $item->jml->TextBox->Columns=5;
		   //$item->nama->Enabled=false;
		   //$item->sat->Enabled=false;
		   //$item->pbf->Enabled=false;
		   //$item->sumber->Enabled=false;
		   //$item->sumberSekunder->Enabled=false;
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
			$cariByGol=$this->getViewState('cariByGol');
			$cariByKlas=$this->getViewState('cariByKlas');
			$cariByDerivat=$this->getViewState('cariByDerivat');
			$cariByPbf=$this->getViewState('cariByPbf');
			$cariByProd=$this->getViewState('cariByProd');
			$cariBySat=$this->getViewState('cariBySat');		
			$sumber=$this->getViewState('sumber');
			$this->UserGrid->VirtualItemCount=ObatRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
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
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');
		
		$this->UserGrid->EditItemIndex=-1;
        $this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		// binds the data to interface components
		$this->UserGrid->dataBind();		
		$this->ID->focus();
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
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	

	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
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
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');			
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber);
		$this->UserGrid->dataBind();
	}	
	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByID=$this->getViewState('cariByID');	
		$elemenBy=$this->getViewState('elemenBy');
		$cariByGol=$this->getViewState('cariByGol');
		$cariByKlas=$this->getViewState('cariByKlas');
		$cariByDerivat=$this->getViewState('cariByDerivat');
		$cariByPbf=$this->getViewState('cariByPbf');
		$cariByProd=$this->getViewState('cariByProd');
		$cariBySat=$this->getViewState('cariBySat');
		$sumber=$this->getViewState('sumber');		
		/*$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);
		$this->UserGrid->dataBind();	
	}
	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
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
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function DDPbfChanged($sender,$param)
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
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}	
					
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$this->ID->Text,$this->Advance->Checked,$this->DDGol->SelectedValue,$this->DDKlas->SelectedValue,$this->DDDerivat->SelectedValue,$this->DDPbf->SelectedValue,$this->DDProd->SelectedValue,$this->DDSat->SelectedValue,$sumber);	
		$this->UserGrid->dataBind();
	}
	
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			//$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	
	}
	
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
