<?php
class DaftarCariPasLama extends SimakConf
{   
	private $sortExp = "cm";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;		
	  
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		
	
	public function onLoad($param)
	{				
		parent::onLoad($param);				
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   	
			$this->bindGrid();									
			$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
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
            $sql = "SELECT cm,
						   nama, 
						   jkel AS jenkel,
						   alamat,
						   kecamatan
						   FROM du_pasien
						  WHERE nama <> '' ";
								
			
			if($this->getViewState('cariByNama') <> '')	
			{
				$nama = $this->getViewState('cariByNama');		
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND nama LIKE '$nama%' ";
				}
			}
						
			if($this->getViewState('cariByAlamat') <> '')
			{
				$alamat = $this->getViewState('cariByAlamat');				
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND alamat LIKE '%$alamat%' ";
				}
				else
				{	
					$sql .= "AND alamat LIKE '$alamat%' ";
				}
			}
				
			if($this->getViewState('cariByCM') <> '')
			{
				$cm = $this->getViewState('cariByCM');
				$sql .= "AND cm = '$cm' ";			
			}	
			//$sql .= " GROUP BY cm ";      
			
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql);    			
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
		if($this->CBpdf->Checked==false)
		{			
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
			
			$this->bindGrid();			
			
		}
		else
		{
			$cariCM=$this->cariCM->Text;
			$cariNama=$this->cariNama->Text;
			$tipeCari=$this->Advance->Checked;
			$cariAlamat=$this->cariAlamat->Text;						
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariPasLamaPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat)));
		}
	}
	
	
}
?>
