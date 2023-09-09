<?php
class PersenMarginObat extends SimakConf
{   
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$this->bindGrid();					
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
    
	private function bindGrid()
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
			
			$sql = "SELECT 
					  tbm_obat_kelompok_margin.id,
					  tbm_obat_kelompok_margin.nama,
					  tbm_obat_kelompok_margin.persentase,
					  tbm_obat_kelompok_margin.persentase_asuransi,
					  tbm_obat_kelompok_margin.persentase_jamper,
					  tbm_obat_kelompok_margin.persentase_unit_internal
					FROM
					  tbm_obat_kelompok_margin ";				
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
			//$this->showSql->Text=$sql;
			//$this->showSql->Visible=false;
			
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			/*
			if($someDataList->getSomeDataCount($sql) == '0')
			{
				$this->cetakBtn->Enabled = false;
			}
			else
			{
				$this->cetakBtn->Enabled = true;
			}
			*/
			
			if ($this->getViewState('sql')) 				
			{
				$this->clearViewState('sql');
			}
			
			$this->setViewState('sql',$sql);
			
        }
        else
		{
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
		   $item->umum->TextBox->Columns=3;
		   $item->asuransi->TextBox->Columns=3;
		   $item->jamper->TextBox->Columns=3;
		   $item->unitInternal->TextBox->Columns=3;
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem')
		{	
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
		//ObatRecord::finder()->FindByPk($ID);
		$id = $this->dtgSomeData->DataKeys[$item->ItemIndex];
		
		$update = ObatKelompokMarginRecord::finder()->findByPk($id);
		$update->persentase = $item->umum->TextBox->Text; 
		$update->persentase_asuransi = $item->asuransi->TextBox->Text; 
		$update->persentase_jamper = $item->jamper->TextBox->Text; 
		$update->persentase_unit_internal = $item->unitInternal->TextBox->Text; 
		
		$update->save();
		
        $this->dtgSomeData->EditItemIndex=-1;  
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
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Apotik.PersenMarginObat'));
			
		}	
    }	
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Apotik.PersenMarginObatBaru'));		
	}
	
}
?>
