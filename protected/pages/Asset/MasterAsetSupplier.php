<?php
class MasterAsetSupplier extends SimakConf
{   
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onLoad($param)
	{				
		parent::onLoad($param);	
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$session=$this->Application->getModule('session');
			if($session['offsetEdit'])
			{
				$offsetEdit = ceil($session['offsetEdit']/10);
				$this->setViewState('offsetEdit',$offsetEdit);
				//$this->cariNama->Text = ceil($session['offsetEdit']/10);
				$session->remove('offsetEdit');
			}
			
			//$this->DDKelompok->Enabled = false;
			//$this->DDKelompok->DataSource = AsetSupplierRecord::finder()->findAll();
			//$this->DDKelompok->dataBind();	
			
			$this->bindGrid();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
			
		$this->cariNama->focus();
    }		
	
	public function panelRender($sender, $param)
	{
		$this->gridPanel->render($param->getNewWriter());
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDJenis->SelectedValue=='')
		{
			
			$gol = $this->DDJenis->SelectedValue;	
			$sql = "SELECT id,nama FROM tbm_aset_supplier WHERE id_jenis_barang = '$gol' ORDER BY nama )";
			$this->DDKelompok->DataSource = AsetSupplierRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			$this->DDKelompok->Enabled=true;
		}
		else
		{
			$this->DDKelompok->DataSource=AsetSupplierRecord::finder()->findAll();
			$this->DDKelompok->dataBind();	
			$this->DDKelompok->Enabled=false;
		}
		
		$this->cariClicked();
		$this->DDKelompok->SelectedIndex = '';
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
			
			AsetSupplierRecord::finder()->deleteByPk($ID);	
			//StokLainRecord::finder()->deleteById_barang($ID);
			//HrgBarangRecord::finder()->deleteByKode($ID);
			
			$this->bindGrid();
			//$this->Response->redirect($this->Service->constructUrl('Asset.MasterAsetSupplier'));
		}	
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
	
	public function editRow($sender,$param)
    {
        $ID = $sender->CommandParameter;
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterAsetSupplierEdit',array('pageParent'=>$this->Page->getPagePath(),'ID'=>$ID)));	
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
					  tbm_aset_supplier.id,
					  tbm_aset_supplier.nama,
					  tbm_aset_supplier.alamat,
					  tbm_aset_supplier.tlp
					FROM
					  tbm_aset_supplier						
					WHERE 
						tbm_aset_supplier.id<>'' ";
			
			if($this->cariNama->Text <> '')	
			{
				$cariNama = $this->cariNama->Text;		
				
				if($this->Advance->Checked === true)
					$sql .= "AND tbm_aset_supplier.nama LIKE '%$cariNama%' ";
				else
					$sql .= "AND tbm_aset_supplier.nama LIKE '$cariNama%' ";
			}			
			
			if($this->alamat->Text != '')	
			{
				$alamat = $this->alamat->SelectedValue;	
				$sql .= "AND tbm_aset_supplier.alamat LIKE '%$alamat%' ";
			}
			
			if($this->tlp->Text != '')	
			{
				$tlp = $this->tlp->SelectedValue;	
				$sql .= "AND tbm_aset_supplier.tlp LIKE '%$tlp%' ";
			}
			
			if($this->DDJenis->SelectedValue != '')	
			{
				$DDJenis = $this->DDJenis->SelectedValue;	
				$sql .= "AND tbm_aset_supplier.id_jenis_barang = '$DDJenis' ";
			}
			
			$sql .= " GROUP BY id";      
			 
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
		$this->Response->redirect($this->Service->constructUrl('Asset.MasterAsetSupplierBaru'));		
	}
	
	public function cariClicked()
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();	
	}
	
}
?>
