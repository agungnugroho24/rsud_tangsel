<?php
class ObatPaket extends SimakConf
{   
	private $sortExp = "nm_kel ASC, nm_obat";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onLoad($param)
	{				
		parent::onLoad($param);	
			
			if(!$this->IsPostBack && !$this->IsCallBack)
			{	
				/*
				$session=$this->Application->getModule('session');
				if($session['offsetEdit'])
				{
					$offsetEdit = ceil($session['offsetEdit']/10);
					$this->setViewState('offsetEdit',$offsetEdit);
					//$this->cariNama->Text = ceil($session['offsetEdit']/10);
					$session->remove('offsetEdit');
				}
				*/
				
				$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
				$this->DDGol->dataBind();	
				
				$this->DDKlas->Enabled = false;
				$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
				$this->DDKlas->dataBind();	
				
				$this->DDDerivat->Enabled = false;
				$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
				$this->DDDerivat->dataBind();	
				
				$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
				$this->DDPbf->dataBind();	
				
				$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll();
				$this->DDProd->dataBind();	
				
				$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll();
				$this->DDSat->dataBind();
				
				$this->bindGrid();					
				$position='TopAndBottom';		
				$this->dtgSomeData->PagerStyle->Position=$position;
				$this->dtgSomeData->PagerStyle->Visible=true;
			}
				
				$this->ID->focus();
    }		
	
	public function panelRender($sender, $param)
	{
		$this->gridPanel->render($param->getNewWriter());
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			
			if(count(KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol)) > 0)
			{
				$this->DDKlas->dataBind(); 	
				$this->DDKlas->Enabled=true;
			}
			else
			{
				$this->DDKlas->Enabled=false;
			}
			
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
		
		$this->cariClicked();
		$this->DDKlas->SelectedIndex = -1;
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			
			if(count(DerivatObatRecord::finder()->findAll('klas_id = ?', $klas)) > 0)
			{
				$this->DDDerivat->dataBind(); 	
				$this->DDDerivat->Enabled=true;
			}
			else
			{
				$this->DDDerivat->Enabled=false;
			}
			
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		
		$this->cariClicked();
		$this->DDDerivat->SelectedIndex = -1;
	}
	
	public function selectionChangedDerivat($sender,$param)
	{	
		$this->cariClicked();
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
			
			if($this->CBdetail->Checked === false)
			{
				$idKelPaket = ObatPaketRecord::finder()->findByPk($ID)->id_kelompok_paket;
				ObatPaketKelompokRecord::finder()->deleteByPk($idKelPaket);
				
				$sql = "DELETE FROM tbm_obat_paket WHERE id_kelompok_paket = '$idKelPaket'";
				$this->queryAction($sql,'C');
			}
			else
			{
				ObatPaketRecord::finder()->deleteByPk($ID);	
			}
			
			$this->cariClicked();
		}	
    }
	
		
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
			if($this->CBdetail->Checked === false)
			{
				$this->dtgSomeData->Columns[1]->Visible = false;
				$this->dtgSomeData->Columns[2]->Visible = false;
			}
			else
			{
				$this->dtgSomeData->Columns[1]->Visible = true;
				$this->dtgSomeData->Columns[2]->Visible = true;
			}
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
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
	
    // get data and bind it to datagrid
    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
		
		if($this->getViewState('offsetEdit')!='')
		{
			$this->dtgSomeData->CurrentPageIndex = $this->getViewState('offsetEdit') - 1;
			$this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;		
			
			$this->clearViewState('offsetEdit');
		}
		else
		{
			 $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
		}  
		
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();           
			$someDataList->offset = $this->offset;		
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
            $sql = "SELECT 
					  tbm_obat_paket_kelompok.id AS id_kel_paket,
					  tbm_obat_paket_kelompok.nama AS nm_kel,
					  tbm_obat_paket.id AS id_paket,
					  tbm_obat_paket.id_obat,
					  tbm_obat.nama AS nm_obat,
					  CONCAT_WS(' ',tbm_obat_paket.jumlah,tbm_satuan_obat.nama) AS jumlah
					FROM
					  tbm_obat_paket_kelompok
					  INNER JOIN tbm_obat_paket ON (tbm_obat_paket_kelompok.id = tbm_obat_paket.id_kelompok_paket)
					  INNER JOIN tbm_obat ON (tbm_obat_paket.id_obat = tbm_obat.kode)
					  LEFT JOIN tbm_satuan_obat ON (tbm_obat.satuan = tbm_satuan_obat.kode)
					WHERE							
					  tbm_obat_paket_kelompok.id <> '' ";
			
			if($this->cariNamaPaket->Text <> '')	
			{
				$cariNamaPaket = $this->cariNamaPaket->Text;		
				
				if($this->Advance2->Checked === true)
					$sql .= "AND tbm_obat_paket_kelompok.nama LIKE '%$cariNamaPaket%' ";
				else
					$sql .= "AND tbm_obat_paket_kelompok.nama LIKE '$cariNamaPaket%' ";
			}		
			
			if($this->cariNama->Text <> '')	
			{
				$cariNamaPaket = $this->cariNama->Text;		
				
				if($this->Advance->Checked === true)
					$sql .= "AND tbm_obat.nama LIKE '%$cariNama%' ";
				else
					$sql .= "AND tbm_obat.nama LIKE '$cariNama%' ";
			}		
			
			if($this->cariNama->Text <> '')	
			{
				$cariID = $this->getViewState('cariByID');	
				$sql .= "AND tbm_obat.kode = '$cariID' ";
			}
		
			if($this->getViewState('cariByGol') <> '')	
			{
				$cariGol = $this->getViewState('cariByGol');		
				$sql .= "AND tbm_obat.gol = '$cariGol' ";
			}
			
			if($this->getViewState('cariByKlas') <> '')	
			{
				$cariKlas = $this->getViewState('cariByKlas');		
				$sql .= "AND tbm_obat.klasifikasi = '$cariKlas' ";
			}
			
			if($this->getViewState('cariByDerivat') <> '')	
			{
				$cariDerivat = $this->getViewState('cariByDerivat');			
				$sql .= "AND tbm_obat.derivat = '$cariDerivat' ";
			}
			
			if($this->getViewState('cariByPbf') <> '')	
			{
				$cariPbf = $this->getViewState('cariByPbf');			
				$sql .= "AND tbm_obat.pbf = '$cariPbf' ";
			}
			
			if($this->getViewState('cariByProd') <> '')	
			{
				$cariProd = $this->getViewState('cariByProd');		
				$sql .= "AND tbm_obat.produsen = '$cariProd' ";
			}
			
			if($this->getViewState('cariBySat') <> '')	
			{
				$cariSat = $this->getViewState('cariBySat');			
				$sql .= "AND tbm_obat.satuan = '$cariSat' ";
			}	
			
			if($this->CBdetail->Checked === false)
				$sql .= " GROUP BY tbm_obat_paket_kelompok.id";	
			
			//if($order <> '')							
				//$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			//if($offset <> '')	
				//$sql .= " LIMIT " . $offset . ', ' . $rows;
				
			
			//$sql .= " GROUP BY kode";      
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
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
        
        // many people won't set this to the first page. this can lead to usability problems.
        // think in what happens if the user is on the 3rd page and changes the sorting field. 
        // you will sort the items on that page if you are using cached data (either in session or "true" cache). 
        // imagine now that the user moves on to page 4. the data on page 4 will be sorted out but it will be 
        // sorted disregarding the other items in other pages. other pages could have items that are "lower" or 
        // "bigger" than the ones displayed. You could have items with the sorting field starting with letter "C" 
        // on page 3 and on page 4 items with the sorting field starting with letter "A". 
        // you could sort all the cached data to solve this but then what page you will show to the user? stick with page 3?
        // I find it better to refresh the data and allways move on to the first page.
        
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.ObatPaketBaru'));
	}
	
	public function cariClicked()
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		
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
		
		$this->bindGrid();	
	}
	
}
?>
