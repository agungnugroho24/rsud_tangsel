<?php
class DaftarCariPdftrnBak extends SimakConf
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
			
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAll($criteria);
			$this->DDKab->dataBind();
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind(); 
			$this->DDKec->Enabled=false;				
			$this->DDKel->Enabled=false;			
			$this->DDKontrak->Enabled=false;
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
			$this->DDKontrak->dataBind();
			$this->DDBulan->DataSource=BulanRecord::finder()->findAll();
			$this->DDBulan->dataBind();
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');//kelompok pegawai '1' adalah untuk dokter
			$this->DDDokter->dataBind();
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();			
			
			$this->bindGrid();									
			$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
		}else{
			$this->cariCM->focus();
		}	
    }		
	
	public function selectionChangedKab($sender,$param)
	{
		if($this->DDKab->SelectedValue=='')
		{
			$this->DDKec->SelectedIndex='-1';
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 

			$this->DDKel->SelectedIndex='-1';
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind(); 
					
			$this->DDKec->Enabled=false;
			$this->DDKel->Enabled=false;
			$this->cariClicked();
		}
		else
		{
			$kab = $this->DDKab->SelectedValue;	
			$this->setViewState('idKab',$kab,'');	
			if ($kab == '01'){ //Bila kota bandung				

				$criteria = new TActiveRecordCriteria;
				$criteria->Condition = 'id_kab = :idkab';
				$criteria->Parameters[':idkab'] = $kab;
				$criteria->OrdersBy['nama'] = 'asc';

				$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
				$this->DDKec->dataBind(); 
			
				$this->DDKec->Enabled=true;
				$this->DDKec->focus();	
				$this->cariClicked();			
			
			}else{			
				$this->DDKec->Enabled=false;
				$this->DDKel->Enabled=false;
				$this->cariClicked();
			}	
		}		
			
	} 
	
	public function selectionChangedKec($sender,$param)
	{
		if($this->DDKec->SelectedValue=='')
		{			
			$this->DDKel->SelectedIndex='-1';
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind(); 
			
			$this->DDKel->Enabled=false;
			$this->cariClicked();
		}
		else
		{		
			$kec = $this->DDKec->SelectedValue;	
			$this->setViewState('idKec',$kec,'');
		
			$kec = $this->DDKec->SelectedValue;
			$kab = $this->getViewState('idKab');
		
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = 'id_kab = :idkab AND id_kec = :idkec';
			$criteria->Parameters[':idkab'] = $kab;
			$criteria->Parameters[':idkec'] = $kec;
			$criteria->OrdersBy['nama'] = 'asc';

			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind(); 	
			$this->DDKel->Enabled=true;
			$this->DDKel->focus();		
			
			$this->cariClicked();
		}	
	}
	
	public function selectionChangedKel($sender,$param)
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
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			PasienRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariRwtInap'));
			
		}	
    }	
	
	/**
     * Paging Control and Properties to specified pages.
     * This method responds to the datagrid's event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
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
						   e.no_trans AS trans,
						   count(a.nama) AS status,
						   f.nama AS alamat,
						   e.id_klinik AS rt,
						   b.id AS kontrakID,
						   e.tgl_visit AS suku
						   FROM tbd_pasien a,
						        tbm_kelompok b,						   		
								tbm_kabupaten c,						   	
								tbm_perusahaan d,
								tbt_rawat_jalan e,								
								tbm_poliklinik f								
						   WHERE
							    a.kelompok=b.id
								AND a.cm=e.cm								
								AND f.id=e.id_klinik								
								AND a.kelompok=b.id									
								AND a.kabupaten=c.id ";
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
			if($this->getViewState('cariByKlinik') <> '')			
			{
				$klinik = $this->getViewState('cariByKlinik');
				$sql .= "AND e.id_klinik = '$klinik' ";		
			}
			if($this->getViewState('cariByTgl') <> '')
			{
				$tgl = $this->ConvertDate($this->getViewState('cariByTgl'),'2');//Convert date to mysql
				$sql .= "AND e.tgl_visit = '$tgl' ";
			}	
			
			if($this->getViewState('cariByKab') <> '')			
			{
				$kab = $this->getViewState('cariByKab');
				$sql .= "AND a.kabupaten = '$kab' ";
			}
			if($this->getViewState('cariByKec') <> '')			
			{
				$kec = $this->getViewState('cariByKec');
				$sql .= "AND a.kecamatan = '$kec' ";	
			}
			if($this->getViewState('cariByKel') <> '')			
			{
				$kel = $this->getViewState('cariByKel');
				$sql .= "AND a.kelurahan = '$kel' ";				
			}	
			if($this->getViewState('cariByBln') <> '')
			{
				$bln = $this->getViewState('cariByBln');				
				$sql .= "AND MONTH(e.tgl_visit) = $bln";		
			}
			
			if($this->getViewState('cariByUrut') <> '')
			{
				$urut = $this->getViewState('cariByUrut');
				$sql .= "AND a.kelompok = '$urut' ";
			}	
			if($this->getViewState('cariByCompany') <> '')
			{
				$company = $this->getViewState('cariByCompany');
				$urut = $this->getViewState('cariByUrut');
				$sql .= "AND a.perusahaan = '$company' AND d.id_kel = '$urut'";						
			}
			$sql .= " GROUP BY e.no_trans ";      
			
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlDataPas->Text=$someDataList->getSomeDataCount($sql); 
			//$this->sqlData->Text=$sql;//Show sql syntax    			
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

    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;

        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $poliklinik = $item->klinik->DropDownList->SelectedValue;
        if ($this->User->IsAdmin)
		{
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$oSomeData = RwtjlnRecord::finder()->findByPk($this->dtgSomeData->DataKeys[$item->ItemIndex]);
                        
            // do some changes to your database item/object and then save it
            $oSomeData->id_klinik = $poliklinik;
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
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	/*
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianBaru'));		
	}*/
	
	public function cariClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
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
		
			if($this->tglMsk->Text){
				$this->setViewState('cariByTgl', $this->tglMsk->Text);
			}else{
				$this->clearViewState('cariByTgl');	
			}
		
			if($this->DDBulan->SelectedValue){ 		
				$this->setViewState('cariByBln', $this->DDBulan->SelectedValue);	
			}else{
				$this->clearViewState('cariByBln');	
			}
		
			if($this->DDDokter->SelectedValue){ 		
				$this->setViewState('cariByDokter', $this->DDDokter->SelectedValue);	
			}else{
				$this->clearViewState('cariByDokter');	
			}
		
			if($this->DDKlinik->SelectedValue){ 		
				$this->setViewState('cariByKlinik', $this->DDKlinik->SelectedValue);	
			}else{
				$this->clearViewState('cariByKlinik');	
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
		
			if($this->DDKab->SelectedValue){ 		
				$this->setViewState('cariByKab', $this->DDKab->SelectedValue);	
			}else{
				$this->clearViewState('cariByKab');	
			}
		
			if($this->DDKec->SelectedValue){ 		
				$this->setViewState('cariByKec', $this->DDKec->SelectedValue);	
			}else{
				$this->clearViewState('cariByKec');	
			}
		
			if($this->DDKel->SelectedValue){ 		
				$this->setViewState('cariByKel', $this->DDKel->SelectedValue);	
			}else{
				$this->clearViewState('cariByKel');	
			}
		
			if($this->DDKontrak->SelectedValue){ 		
				$this->setViewState('cariByCompany', $this->DDKontrak->SelectedValue);
			}else{
				$this->clearViewState('cariByCompany');	
			}
				
			if($this->DDUrut->SelectedValue){
				$this->setViewState('cariByUrut', $this->DDUrut->SelectedValue);
			}else{
				$this->clearViewState('cariByUrut');	
			}
			
			if($this->DDUrut->SelectedValue == '05')//Kalo pilihannya adalah pasien kontrak	
			{	
				$this->DDKontrak->Enabled=true;	
			}else{
				$this->DDKontrak->Enabled=false;	
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
			$cariTgl=$this->tglMsk->Text;
			$cariBln=$this->DDBulan->SelectedValue;
			$cariByDokter=$this->DDDokter->SelectedValue;
			$cariByKlinik=$this->DDKlinik->SelectedValue;
			
			$cariByKab=$this->DDKab->SelectedValue;
			$cariByKec=$this->DDKec->SelectedValue;
			$cariByKel=$this->DDKel->SelectedValue;
			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariPdftrnPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company,'cariTgl'=>$cariTgl,'cariBln'=>$cariBln,'cariByDokter'=>$cariByDokter,'cariByKlinik'=>$cariByKlinik,'cariByKab'=>$cariByKab,'cariByKec'=>$cariByKec,'cariByKel'=>$cariByKel)));
		}	
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		
		$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll();
		$this->DDKontrak->dataBind();
		
		$this->cariClicked();
				
		
	}
	
	public function DDKlinikChanged($sender,$param)
	{				
		$this->cariClicked();
	}
	
	public function DDDokterChanged($sender,$param)
	{				
		$this->cariClicked();
	}

	public function DDBulanChanged($sender,$param)
	{				
		$this->cariClicked();
	}

	public function DDKontrakChanged($sender,$param)
	{				
		$this->cariClicked();
	}
}
?>
