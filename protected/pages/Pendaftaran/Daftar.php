<?php

//Prado::using('System.Web.UI.ActiveControls.*');

class Daftar extends TPage
{
    private $sortExp = "cm";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;
    
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


    // get data and bind it to datagrid
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
             $sql ="SELECT cm, 
					   	   nama,
					   	   alamat,
					   	   tgl_lahir
					   	   FROM du_pasien";       
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount('du_pasien');
            
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
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
    
    //  ------------ datagrid edit related events -------------
    
    protected function dtgSomeData_ItemCreated($sender,$param)
    {
        $type = $param->Item->ItemType;
        if($type === 'Item' || $type === 'AlternatingItem' || $type === 'EditItem' || $type === 'SelectedItem') 
        {
            // add an alert dialog to delete buttons
            $param->Item->dtgColDelete->Button->Attributes->onClick = "return confirm('Are you sure you want to delete this item?');";
        }
    }

    protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;

        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $col2 = $item->dtgCol2->col2Edit->Text;
        if ( doSomeValidation() )
		{
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$oSomeData = SomeData::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
                        
            // do some changes to your database item/object and then save it
            $oSomeData->nama = $col2;
            $oSomeData->save();
            
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        }

        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }

    protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
    
    protected function dtgSomeData_DeleteCommand($sender,$param)
    {
        $oSomeData = SomeDataList::getSomeData($this->dtgSomeData->DataKeys[$param->Item->ItemIndex]);
        $oSomeData->delete();

        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
                
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
}
?>
