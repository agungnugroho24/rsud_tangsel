<?php
class masterBarang extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
			$this->DDsubHabisPakai->dataBind();	
			
			$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
			$this->DDsubBergerak->dataBind();	
			
			$this->DDVen->DataSource=AssetProdusenRecord::finder()->findAll();
			$this->DDVen->dataBind();	
			
			$this->DDDist->DataSource=AssetDistributorRecord::finder()->findAll();
			$this->DDDist->dataBind();
							
			$this->modeJenis->focus();	
			
			$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;				
		}
	}	
	
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
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

    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
			$cariKelPakai=$this->getViewState('cariKelPakai');
			$cariSubHabisPakai=$this->getViewState('cariSubHabisPakai');
			$cariKelGerak=$this->getViewState('cariKelGerak');
			$cariSubBergerak=$this->getViewState('cariSubBergerak');
			$cariKelMedis=$this->getViewState('cariKelMedis');
			$cariKelElektrik=$this->getViewState('cariKelElektrik');
			$cariByID=$this->getViewState('cariByID');
			$cariByNama=$this->getViewState('cariByNama');				
			$elemenBy=$this->getViewState('elemenBy');
			$cariProdusen=$this->getViewState('cariProdusen');
			$cariDistributor=$this->getViewState('cariDistributor');			
		
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
            $sql = "SELECT 
					  tbm_asset_barang.id,
					  tbm_asset_barang.nama AS nm_brg,
					  tbm_asset_produsen.nama AS nm_produsen,
					  tbm_asset_distributor.nama AS nm_distributor,
					  tbm_asset_barang.st_kel_habis_pakai,
					  tbm_asset_barang.st_sub_kel_habis_pakai,
					  tbm_asset_barang.st_bergerak,
					  tbm_asset_barang.st_sub_bergerak,
					  tbm_asset_barang.st_medis,
					  tbm_asset_barang.st_elektrik,
					  tbm_asset_kel_bergerak.nama AS nm_kel_bergerak,
					  tbm_asset_kel_hbs_pakai.nama AS nm_kel_hbs_pakai
					FROM
					  tbm_asset_barang
					  INNER JOIN tbm_asset_produsen ON (tbm_asset_barang.prod = tbm_asset_produsen.id)
					  INNER JOIN tbm_asset_distributor ON (tbm_asset_barang.dist = tbm_asset_distributor.id)
					  LEFT OUTER JOIN tbm_asset_kel_bergerak ON (tbm_asset_barang.st_sub_bergerak = tbm_asset_kel_bergerak.id)
					  LEFT OUTER JOIN tbm_asset_kel_hbs_pakai ON (tbm_asset_barang.st_sub_kel_habis_pakai = tbm_asset_kel_hbs_pakai.id)
					WHERE
					   tbm_asset_barang.id <> ''	";
					   
			if($cariKelPakai <> '')
				$sql .= "AND tbm_asset_barang.st_kel_habis_pakai ='$cariKelPakai' ";
			
			if($cariSubHabisPakai <> '')
				$sql .= "AND tbm_asset_barang.st_sub_kel_habis_pakai ='$cariSubHabisPakai' ";
			
			if($cariKelGerak <> '')
				$sql .= "AND tbm_asset_barang.st_bergerak ='$cariKelGerak' ";
			
			if($cariSubBergerak <> '')
				$sql .= "AND tbm_asset_barang.st_sub_bergerak ='$cariSubBergerak' ";
			
			if($cariKelMedis <> '')
				$sql .= "AND tbm_asset_barang.st_medis ='$cariKelMedis' ";
			
			if($cariKelElektrik <> '')
				$sql .= "AND tbm_asset_barang.st_elektrik ='$cariKelElektrik' ";
			
			if($cariByID <> '')
				$sql .= "AND tbm_asset_barang.id ='$cariByID' ";
										
			if($cariByNama <> '')
		 	{
				if($elemenBy === true){
						$sql .= "AND tbm_asset_barang.nama LIKE '%$cariByNama%' ";
					}
					else
					{	
						$sql .= "AND tbm_asset_barang.nama LIKE '$cariByNama%' ";
					}		
			}
			
			if($cariProdusen <> '')
				$sql .= "AND tbm_asset_barang.prod ='$cariProdusen' ";
			
			if($cariDistributor <> '')
				$sql .= "AND tbm_asset_barang.dist ='$cariDistributor' ";
			
			$sql .= " GROUP BY id ";	
			
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
        
        
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }	
	
	protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	   
        } 
    }
	
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		/*
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $rujukan = $item->rujukan->DropDownList->SelectedValue;
		$kelas = $item->kelas->DropDownList->SelectedValue;
        if ($this->User->IsAdmin)
		{
			
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$oSomeData = RwtInapRecord::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
                        
            // do some changes to your database item/object and then save it
            $oSomeData->st_rujukan = $rujukan;
			$oSomeData->kelas = $kelas;
            $oSomeData->save();
            
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        }
		*/
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function modeJenisChanged($sender, $param)
	{
		$jnsBrg = $this->collectSelectionResult($this->modeJenis);
		if($jnsBrg == '1')
		{
			$this->hbsPakaiCtrl->Visible = true;
			$this->TdkHbsPakaiCtrl->Visible = false;			
		}
		elseif($jnsBrg == '2')
		{
			$this->hbsPakaiCtrl->Visible = false;
			$this->TdkHbsPakaiCtrl->Visible = true;			
		}
		
		$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
		$this->DDsubHabisPakai->dataBind();	
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
			
		$this->modeTdkHabisPakai->SelectedIndex = -1 ;
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->tdkBergerakCtrl->Visible = false;
		$this->bergerakCtrl->Visible = false;
		$this->nonMedisCtrl->Visible = false;
		
		$this->cariClicked();
	}
	
	public function modeTdkHabisPakaiChanged($sender, $param)
	{
		$subTdkHabis = $this->collectSelectionResult($this->modeTdkHabisPakai);
		if($subTdkHabis == '1')
		{
			$this->bergerakCtrl->Visible = true;
			$this->tdkBergerakCtrl->Visible = false;			
		}
		elseif($subTdkHabis == '2')
		{
			$this->bergerakCtrl->Visible = false;
			$this->tdkBergerakCtrl->Visible = true;			
		}
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
		
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->nonMedisCtrl->Visible = false;
		$this->cariClicked();
	}
	
	public function modeTdkBergerakChanged($sender, $param)
	{
		$subTdkbergerak = $this->collectSelectionResult($this->modeTdkBergerak);
		if($subTdkbergerak == '1')
		{			
			$this->nmBarang->focus();
			$this->nmBarang->Text = '';
			$this->nonMedisCtrl->Visible = false;			
		}
		elseif($subTdkbergerak == '2')
		{			
			$this->nonMedisCtrl->Visible = true;			
		}
		
		$this->modeNonMedis->SelectedIndex = -1 ;
		$this->cariClicked();
	}
	
	public function modeNonMedisChanged($sender, $param)
	{
		$nonMedis = $this->collectSelectionResult($this->modeNonMedis);
		if($nonMedis == '1')
		{
			//$this->nonElektrikalCtrl->Visible = false;			
		}
		elseif($nonMedis == '2')
		{
			//$this->elektrikalCtrl->Visible = false;
			//$this->nonElektrikalCtrl->Visible = true;			
		}
		$this->nmBarang->focus();
		$this->nmBarang->Text = '';
		$this->cariClicked();
	}
	
	public function DDsubHabisPakaiChanged($sender, $param)
	{
		if($this->DDsubHabisPakai->SelectedValue != '')
		{
			$this->cariClicked();
		}
		else
		{
			$this->clearViewState('cariSubHabisPakai');	
		}
	}
	
	public function DDsubBergerakChanged($sender, $param)
	{
		if($this->DDsubBergerak->SelectedValue != '')
		{
			$this->cariClicked();
		}
		else
		{
			$this->clearViewState('cariSubBergerak');
		}
	}
	
	
	
	public function cariClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;			
		
		$cariKelPakai=$this->getViewState('cariKelPakai');
		$cariSubHabisPakai=$this->getViewState('cariSubHabisPakai');
		$cariKelGerak=$this->getViewState('cariKelGerak');
		$cariSubBergerak=$this->getViewState('cariSubBergerak');
		$cariKelMedis=$this->getViewState('cariKelMedis');
		$cariKelElektrik=$this->getViewState('cariKelElektrik');
		$cariByID=$this->getViewState('cariByID');
		$cariByNama=$this->getViewState('cariByNama');				
		$elemenBy=$this->getViewState('elemenBy');
		$cariProdusen=$this->getViewState('cariProdusen');
		$cariDistributor=$this->getViewState('cariDistributor');
		
		if($this->collectSelectionResult($this->modeJenis)){
			$this->setViewState('cariKelPakai', $this->collectSelectionResult($this->modeJenis));
		}else{
			$this->clearViewState('cariKelPakai');	
		}
		
		if($this->DDsubHabisPakai->SelectedValue) {
			$this->setViewState('cariSubHabisPakai', $this->DDsubHabisPakai->SelectedValue);
		}else{
			$this->clearViewState('cariSubHabisPakai');	
		}
		
		if($this->collectSelectionResult($this->modeTdkHabisPakai)){
			$this->setViewState('cariKelGerak', $this->collectSelectionResult($this->modeTdkHabisPakai));
		}else{
			$this->clearViewState('cariKelGerak');	
		}
		
		if($this->DDsubBergerak->SelectedValue) {
			$this->setViewState('cariSubBergerak', $this->DDsubBergerak->SelectedValue);
		}else{
			$this->clearViewState('cariSubBergerak');	
		}
		
		if($this->collectSelectionResult($this->modeTdkBergerak)){
			$this->setViewState('cariKelMedis', $this->collectSelectionResult($this->modeTdkBergerak));
		}else{
			$this->clearViewState('cariKelMedis');	
		}
		
		if($this->collectSelectionResult($this->modeNonMedis)){
			$this->setViewState('cariKelElektrik', $this->collectSelectionResult($this->modeNonMedis));
		}else{
			$this->clearViewState('cariKelElektrik');	
		}
		
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
			
		if($this->nmBarang->Text){
			$this->setViewState('cariByNama', $this->nmBarang->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDVen->SelectedValue) {
			$this->setViewState('cariProdusen', $this->DDVen->SelectedValue);
		}else{
			$this->clearViewState('cariProdusen');	
		}
		
		if($this->DDDist->SelectedValue) {
			$this->setViewState('cariDistributor', $this->DDDist->SelectedValue);
		}else{
			$this->clearViewState('cariDistributor');	
		}
		
		$this->bindGrid();
	}
	
	public function baruClicked()
	{		
		//$this->Response->reload();		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterBarangBaru'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
