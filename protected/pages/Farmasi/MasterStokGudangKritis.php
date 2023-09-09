<?php
class MasterStokGudangKritis extends SimakConf
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
	
	
	protected function getDataRows($offset,$rows,$order,$cariNama,$cariID,$tipeCari,$cariGol,$cariKlas,$cariDerivat,$cariPbf,$cariProd,$cariSat,$sumber,$tujuan,$tipe,$kategori)
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
				$sql .= "b.jumlah AS jumlah,
						 b.sumber AS sumber ";
			}			   
			/*
			if ($tujuan == '1')
			{
				$sql .=	" ,a.min_gud AS jml_min, 
						  (a.min_gud - b.jumlah) AS defisit 
						  FROM tbm_obat a,							
						  tbt_stok_gudang b
						WHERE	 							
							a.kode=b.id_obat  
							AND b.jumlah < a.min_gud ";
			}else{						   
			*/
/*
				$sql .=	" ,a.min_loket AS jml_min, 
						  (a.min_loket - b.jumlah) AS defisit 
						FROM
							tbm_obat a,
							view_total_stok_pertujuan b
						WHERE
						    a.kode=b.id_obat 
							AND b.jumlah < a.min_loket
							AND b.tujuan='$tujuan' ";			*/
				$sql .=	" ,a.min_2 AS jml_min, 
						  (a.min_2 - b.jumlah) AS defisit 
						FROM
							tbm_obat a,
							view_total_stok_pertujuan b
						WHERE
						    a.kode=b.id_obat 
							AND b.jumlah < a.min_2
							AND b.tujuan='$tujuan' ";				
			//}
			
								
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
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";
			
			if($kategori <> '')			
				$sql .= "AND a.kategori = '$kategori' ";
				
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";				
			
			//$sql .= " GROUP BY b.id_obat";			
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
						
		if ($this->getViewState('sql')) 				
		{
			$this->clearViewState('sql');
		}
		
		$this->setViewState('sql',$sql);
			
		$page=$this->queryAction($sql,'S');
		 
		//$page=ObatRecord::finder()->findAllBySql($sql);
		
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;	
		
	}
	
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
							
			$orderBy=$this->getViewState('orderBy');
			$limit=$this->getViewState('limit');
			$offset=$this->getViewState('offset');			
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
			$tujuan=$this->getViewState('tujuan');
			$tipe=$this->getViewState('tipe');
			$kategori=$this->getViewState('kategori');
			
			if(!$this->IsPostBack)
			{
				$criteria = new TActiveRecordCriteria;												
				$criteria->OrdersBy['nama'] = 'asc';
				$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
				$this->DDTujuan->dataBind();	
				
				$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
				$this->DDGol->dataBind();	
				
				$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
				$this->DDJenisBrg->dataBind(); 
				
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
			}else{
				$jmlData=count($this->getDataRows($offset,$limit,$orderBy,$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$tipe,$kategori));
				
				$this->jmlData->Text=$jmlData;
				$this->UserGrid->VirtualItemCount=$jmlData;
				
				if($jmlData == '0')
				{
					$this->cetakBtn->Enabled = false;
				}
				else
				{
					$this->cetakBtn->Enabled = true;
				}
			
				// fetches all data account information 
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,$cariByNama,$cariByID,$elemenBy,$cariByGol,$cariByKlas,$cariByDerivat,$cariByPbf,$cariByProd,$cariBySat,$sumber,$tujuan,$tipe,$kategori);
				// binds the data to interface components
				$this->UserGrid->dataBind();
			}		
			$this->ID->focus();		
			
			
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
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->Enabled=false;
		}	
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();		
	}
	
	public function DDTujuanChanged($sender,$param)
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
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
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
		$this->setViewState('limit', $this->UserGrid->PageSize);		
			
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;	
		$this->setViewState('offset', $offset);	
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
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
			
		}	
    }
	
	public function cancelItem($sender,$param)
    {        
		$this->UserGrid->EditItemIndex=-1;        
    }
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//ObatRecord::finder()->FindByPk($ID);
		$idObat=$this->UserGrid->DataKeys[$item->ItemIndex];
		//$sumberObat=$item->sumberMaster->TextBox->Text;
		$sumberObat = $this->getViewState('sumber');
		
		$update=StokGudangRecord::finder()->find('id_obat = ? AND sumber = ?', $idObat, $sumberObat);
		
		$update->jumlah=$item->jml->TextBox->Text; 
		$update->save(); 
		
      //  $this->update(
       //     $this->UserGrid->DataKeys[$item->ItemIndex],    
       //     $item->jml->TextBox->Text
       //     );
        $this->UserGrid->EditItemIndex=-1;        
		
    }
	
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

		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);	
		
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
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}				
		
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
					
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}
		
		
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
	
	public function cetakClicked($sender,$param)
	{
		$session=new THttpSession;
		$session->open();
		$session['cetakStokSql'] = $this->getViewState('sql');
		
		$tujuan = $this->DDTujuan->SelectedValue;
		$jnsBarang = $this->DDJenisBrg->SelectedValue;
		$tipe = $this->collectSelectionResult($this->RBtipeObat);
				
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakStokGudangKritis',array('tujuan'=>$tujuan,'jnsBarang'=>$jnsBarang,'tipe'=>$tipe)));
	}
}
?>
