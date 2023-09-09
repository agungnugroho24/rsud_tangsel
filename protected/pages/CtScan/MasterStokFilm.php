<?php
class MasterStokFilm extends SimakConf
{   	
	private $sortExp = "nm_barang";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	

   	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('13');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			//$this->DDTujuan->DataSource=DestinasiAsetRecord::finder()->findAll($criteria);
			//$this->DDTujuan->dataBind();
			
			$sql = "SELECT id,nama FROM tbm_barang_kelompok ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			
			/*$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolFilmRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiFilmRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatFilmRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfFilmRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenFilmRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanFilmRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberFilmRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberFilmRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();			
			*/
			$this->bindGrid();									
			$this->cariNama->Focus();						
		}
	}		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDJenis->SelectedValue=='')
		{
			
			$gol = $this->DDJenis->SelectedValue;	
			$sql = "SELECT id,nama FROM tbm_barang_kelompok WHERE id_jenis_barang = '$gol' ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			//$this->DDKelompok->Enabled=true;
		}
		else
		{
			$this->DDKelompok->DataSource=BarangKelompokRecord::finder()->findAll();
			$this->DDKelompok->dataBind();	
			//$this->DDKelompok->Enabled=false;
		}
		
		$this->cariClicked();
		$this->DDKelompok->SelectedValue = 'empty';
	}
	
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeBarang->Enabled=true;
		}
		else
		{
			$this->RBtipeBarang->Enabled=false;
		}	
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		$this->cariClicked();
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeBarang);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();	
		$this->cariClicked();
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatFilmRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatFilmRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		$this->cariClicked();
	}
	
	public function DDTujuanChanged($sender,$param)
	{
		$this->cariClicked();
	}
	
	// ---------- datagrid helper functions --------
    
    public function getSortExpression() {
        if ($this->getViewState('sortExpression',null)!==null) {
            return $this->getViewState('sortExpression');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('sortExpression', $this->sortExp);
        return $this->sortExp;
    }

    public function setSortExpression($sort) {
        $this->setViewState('sortExpression',$sort);
    }

    public function getSortDirection() {
        if ($this->getViewState('sortDirection',null)!==null) {
            return $this->getViewState('sortDirection');
        }
        // set default in case there's no 'sortDirection' key in viewstate
        $this->setViewState('sortDirection', $this->sortDir);
        return $this->sortDir;
    }

    public function setSortDirection($sort) {
        $this->setViewState('sortDirection',$sort);
    }
    
	public function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			if($this->groupingIdharga->Checked === true)
			{
				if($this->DDTujuan->SelectedValue=='')
					$this->DDTujuan->SelectedIndex = '0';
			}	
			
			$tujuan = $this->DDTujuan->SelectedValue;
			$thnNow = date('Y');
			
			$sql = "select 
						tbm_film.id AS id,
						tbm_film.nama AS nm_barang,
						tbm_film.jumlah
					  from
					  	tbm_film
					WHERE
						tbm_film.id <> '' ";
					
							
			if($this->cariNama->Text != '')	
			{			
				$cariByNama = $this->cariNama->Text;
				if($this->Advance->Checked=== true)
				{
					$sql .= "AND tbm_film.nama LIKE '%$cariByNama%' ";
				}else{
					$sql .= "AND tbm_film.nama LIKE '$cariByNama%' ";
				}
			}
				
			//$sql .= " GROUP BY tbt_stok_aset.id_harga ";			
			
			//if($this->groupingIdharga->Checked === true)
				$sql .= " GROUP BY tbm_film.id";
			//else
				//$sql .= " GROUP BY tbm_barang.id ";	
			
			//if($tujuan <> '')
				//$sql .= ",tbt_stok_aset.thn_pengadaan,tbt_stok_aset.tujuan,tbt_stok_aset.id_harga, tbt_stok_aset.depresiasi ";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
			//$this->showSql->Visible=true;
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			if($someDataList->getSomeDataCount($sql) == '0')
			{
				$this->cetakBtn->Enabled = false;
			}
			else
			{
				$this->cetakBtn->Enabled = true;
			}
			
			
			if ($this->getViewState('sql')) 				
			{
				$this->clearViewState('sql');
			}
			
			$this->setViewState('sql',$sql);
			
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
        }
    }
	
	
    // ---------- datagrid page and sort events ---------------
    
     protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
		
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jml->TextBox->Columns=10;
		  // $item->hargaNett->TextBox->Columns=5;	
		  // $item->harga->TextBox->Columns=5;	
		   
		   /*$item->gudang->TextBox->Columns=3;
		   $item->icu->TextBox->Columns=3;
		   $item->ugd->TextBox->Columns=3;
		   $item->ok->TextBox->Columns=3;
		   $item->vk->TextBox->Columns=3;
		   $item->anak->TextBox->Columns=3;
		   $item->poli5->TextBox->Columns=3;
		   $item->kandungan->TextBox->Columns=3;
		   $item->bayi->TextBox->Columns=3;
		   $item->fisio->TextBox->Columns=3;
		   $item->nurse_a->TextBox->Columns=3;
		   $item->nurse_b->TextBox->Columns=3;
		   $item->apotik->TextBox->Columns=3;
		   
		   if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
		   {
			   	$item->expired->expAwal->Value = $this->convertDate($item->DataItem['expired'],'1');
				$item->expired->txtExpEdit->Text = $this->convertDate($item->DataItem['expired'],'1');
		   }
			else
			{
				$item->expired->expAwal->Value = '';
				$item->expired->txtExpEdit->Text = '';			
			}*/
			
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{		
			$idBarang = $item->DataItem['id'];
			//$item->hargaNett->Text = number_format($item->DataItem['hrg_netto'],'2',',','.');
		}
    }
	
	public function editItem($sender,$param)
    {
		if ($this->User->IsAdmin)
		{
			$this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
			$this->bindGrid(); 
		}	
    }
	
	public function cancelItem($sender,$param)
    {        
		$this->dtgSomeData->EditItemIndex=-1;  
		$this->bindGrid();
    }
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
			
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//FilmRecord::finder()->FindByPk($ID);
		$id = $this->dtgSomeData->DataKeys[$item->ItemIndex];	
		$tujuan = $this->DDTujuan->SelectedValue;
		$jumlah = $item->jml->TextBox->Text;
		
		if(intval($jumlah) || $jumlah == 0)
		{
			$sql="UPDATE 
					tbm_film 
				SET 
					jumlah='$jumlah' 
				WHERE 
					id='$id'  ";
					
			$this->queryAction($sql,'C');
			$this->dtgSomeData->EditItemIndex=-1;  
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Jumlah belum di isi !</p>\',timeout: 3000,dialog:{modal: true}});');	
		}
		
		$this->bindGrid();	
    }
	
    public function deleteButtonClicked($sender,$param)

    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			FilmRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterBarang'));
			
		}	
    }	

		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);	
		$this->bindGrid();
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterBarangBaru'));		
	}
	
	public function cariClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
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
		
		$this->bindGrid();
	}
		
	public function DDPbfChanged($sender,$param)
	{			
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
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
			//$this->DDSumSekunder->DataSource=SubSumberFilmRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberFilmRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
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
		$jnsBarang = $this->DDJenis->SelectedValue;
		$kelompokBarang = $this->DDKelompok->SelectedValue;
		
		//$tipe = $this->collectSelectionResult($this->RBtipeBarang);
		
		if($this->semuaStok->Checked === true){
			$tipeCetak = '1';
		}
		else
		{
			$tipeCetak = '0';
		}
				
		$this->Response->redirect($this->Service->constructUrl('CtScan.cetakStokFilm',array('tujuan'=>$tujuan,'jnsBarang'=>$jnsBarang,'kelompokBarang'=>$kelompokBarang,'tipeCetak'=>$tipeCetak)));
	}
	
}
?>
