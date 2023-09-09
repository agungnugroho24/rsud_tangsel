<?php
class DaftarCariData1 extends SimakConf
{   
	private $sortExp = "a.cm ";
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
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			
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
			
            $sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   a.alamat, 
						   b.nama AS kelompok,						   
						   b.id AS kontrakID,								
						   c.nama AS kabupaten
						   FROM tbd_pasien a,
						        tbm_kelompok b,
						   		tbm_kabupaten c,						   	
								tbm_perusahaan d
						   WHERE
							    a.kelompok=b.id	
								AND a.kelompok=b.id			
								AND a.kabupaten=c.id ";
			/*$sql = "SELECT 
					  tbd_pasien.cm,
					  tbd_pasien.nama,
					  tbd_pasien.jkel,
					  tbd_pasien.alamat,
					  tbm_kabupaten.nama AS kabupaten,
					  tbm_kelompok.nama AS kelompok
					FROM
					  tbd_pasien
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id)
					  INNER JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id),
					  tbm_perusahaan 
					 WHERE tbd_pasien.cm <> '' ";*/
					  
					//GROUP BY tbd_pasien.cm
			
			if($this->getViewState('cariByNama') <> '')	
			{
				$nama = $this->getViewState('cariByNama');		
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND a.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$nama%' ";
				}
			}
						
			if($this->getViewState('cariByAlamat') <> '')
			{
				$alamat = $this->getViewState('cariByAlamat');				
				if($this->getViewState('elemenBy') === true){
					$sql .= "AND a.alamat LIKE '%$alamat%' ";
				}
				else
				{	
					$sql .= "AND a.alamat LIKE '$alamat%' ";
				}
			}
				
			if($this->getViewState('cariByCM') <> '')
			{
				$cm = $this->getViewState('cariByCM');
				$sql .= "AND a.cm = '$cm' ";			
			}	
			
			if($this->getViewState('urutBy') <> '')
			{
				$kel=$this->getViewState('urutBy');
				$sql .= "AND a.kelompok = '$kel' ";	
			}
				
			if($this->getViewState('companyBy') <> '')
			{
				$company=$this->getViewState('companyBy');
				$kel=$this->getViewState('urutBy');
				$sql .= "AND a.perusahaan = '$company' AND d.id_kel = '$kel'";			
			}
			
			$sql .= " GROUP BY a.cm ";      
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
			$this->showSql->Text=$sql;
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
		
	public function cariClicked($sender,$param)
	{		
		if($this->CBpdf->Checked==false)
		{				
			
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
			$urutBy=$this->DDUrut->SelectedValue;
			$Company=$this->DDKontrak->SelectedValue;
			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariDataPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company)));
		}
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='a.cm';
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

		$this->bindGrid();			
	
	/*				
		$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
		$this->DDKontrak->dataBind();

		$this->cariClicked();*/
	}

	public function DDKontrakChanged($sender,$param)
	{				
		if($this->getViewState('orderBy')){
			$orderBy=$this->getViewState('orderBy');
		}else{
			$orderBy='a.cm';
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

		$this->bindGrid();		
	}
}

?>
