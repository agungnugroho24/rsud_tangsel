<?php

Prado::using('Application.modules.PWCWindow.PWCWindow');

class DaftarCariData extends SimakConf
{   
	private $sortExp = "cm ";
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
			
			$this->DDUrut->DataSource=KelompokRecord::finder()->findAll();
			$this->DDUrut->dataBind();
			
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAll();
			$this->DDKab->dataBind();
			
			//$this->jnsPas->SelectedValue = '1';
			
			//$this->bindGrid();								
			$this->cariPanel->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
		}		
    }		
	
	public function panelCallBack($sender,$param)
	{
		/*
		$this->gridPanel->render($param->getNewWriter());
		$this->gridPanel->render($this->Response->createHtmlWriter(null));
		foreach($this->dtgSomeData->Items as $item)
		{
			$item->cmColumn->tes->render($this->Response->createHtmlWriter(null));
		}
		*/
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
			
            $sql = "SELECT 
					  tbd_pasien.cm,
					  tbd_pasien.nama,
					  tbd_pasien.jkel,
					  tbd_pasien.alamat,
					  tbd_pasien.telp,
					  tbd_pasien.hp,
					  tbd_pasien.tgl_lahir,
					  tbd_pasien.nm_pj,
					  tbd_pasien.kelompok AS id_penjamin,
					  tbm_kelompok.nama AS nm_penjamin,
					  tbm_kabupaten.nama AS nm_kab, ";
					  
			if($this->jnsPas->SelectedValue != '')
			{
				$jnsPasien = $this->jnsPas->SelectedValue;
				if($jnsPasien == '1') //Pasien Rawat Jalan
				{
					$sql .= "'1' AS tipe_rawat ";
				}
				else //Pasien Rawat Inap
				{
					$sql .= "'2' AS tipe_rawat ";
				}
			}
			
			$sql .= "FROM
					  tbd_pasien					  
					  INNER JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id) ";
								
			if($this->jnsPas->SelectedValue != '')
			{
				$jnsPasien = $this->jnsPas->SelectedValue;
				if($jnsPasien == '1') //Pasien Rawat Jalan
				{
					$sql .= "INNER JOIN tbt_rawat_jalan ON (tbd_pasien.cm = tbt_rawat_jalan.cm)
							 LEFT JOIN tbm_kelompok ON (tbt_rawat_jalan.penjamin = tbm_kelompok.id)
							 WHERE 
							 	tbt_rawat_jalan.flag = '1'
								AND  tbt_rawat_jalan.st_alih = '0' ";
				}
				else //Pasien Rawat Inap
				{
					$sql .= "INNER JOIN tbt_rawat_inap ON (tbd_pasien.cm = tbt_rawat_inap.cm)
							 LEFT JOIN tbm_kelompok ON (tbt_rawat_inap.penjamin = tbm_kelompok.id)
							 WHERE 
							 	tbt_rawat_inap.status = '1' ";
				}
			}
			
			if($this->cariCM->Text != '')
			{
				$cariCM = $this->cariCM->Text;
				$sql .= "AND tbd_pasien.cm LIKE '%$cariCM%' ";
			}
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbd_pasien.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbd_pasien.nama LIKE '$nama%' ";
				}
			}
			
			if($this->cariAlamat->Text != '')
			{
				$cariAlamat = $this->cariAlamat->Text;
				$sql .= "AND tbd_pasien.alamat LIKE '%$cariAlamat%' ";
			}
			
			if($this->cariTlp->Text != '')
			{
				$cariTlp = $this->cariTlp->Text;
				$sql .= "AND tbd_pasien.telp LIKE '%$cariTlp%' ";
			}
			
			if($this->cariHp->Text != '')
			{
				$cariHp = $this->cariHp->Text;
				$sql .= "AND tbd_pasien.hp LIKE '%$cariHp%' ";
			}
			
			if($this->cariPj->Text != '')
			{
				$cariPj = $this->cariPj->Text;
				$sql .= "AND tbd_pasien.nm_pj LIKE '%$cariPj%' ";
			}
			
			if($this->DDUrut->SelectedValue != '')
			{
				$DDUrut = $this->DDUrut->SelectedValue;
				$sql .= "AND tbd_pasien.kelompok = '$DDUrut' ";
			}    
			
			if($this->DDKab->SelectedValue != '')
			{
				$DDKab = $this->DDKab->SelectedValue;
				$sql .= "AND tbd_pasien.kabupaten = '$DDKab' ";
			}    
			
			$sql .= " GROUP BY tbd_pasien.cm ";
			 
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
		
	public function cariClicked($sender,$param)
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();			
			
		/*
		$cariCM=$this->cariCM->Text;
		$cariNama=$this->cariNama->Text;
		$tipeCari=$this->Advance->Checked;
		$cariAlamat=$this->cariAlamat->Text;
		$urutBy=$this->DDUrut->SelectedValue;
		$Company=$this->DDKontrak->SelectedValue;
		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariDataPdf',array('cariCM'=>$cariCM,'cariNama'=>$cariNama,'tipeCari'=>$tipeCari,'cariAlamat'=>$cariAlamat,'urutBy'=>$urutBy,'Company'=>$Company)));
		*/
		
	}
	
  
  public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
			$cm = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$item->cmColumn->orderBtn->getAttributes()->add("href", $this->Service->constructUrl('Pendaftaran.DataPasDetail',array('idMeja'=>$idMeja,'stMeja'=>$st_tbd_meja)));
			
			$item->cmColumn->orderBtn->getAttributes()->add("params", "lightwindow_width=800,lightwindow_height=400,lightwindow_type=external");
				
			if(isset($item->DataItem['telp']) && isset($item->DataItem['hp']) )
			{
				$item->telpColumn->Text = $item->DataItem['telp'].' / '.$item->DataItem['hp'];
			}
			elseif(!isset($item->DataItem['telp']))
			{
				$item->telpColumn->Text = $item->DataItem['hp'];
			}
			elseif(!isset($item->DataItem['hp']))
			{
				$item->telpColumn->Text = $item->DataItem['telp'];
			}
			else
			{
				$item->telpColumn->Text = '-';
			}
			
			$item->jaminanColumn->Text =  $item->DataItem['nm_penjamin'];
			$item->kabColumn->Text =  $item->DataItem['nm_kab'];
			/*
			$item->kabColumn->Text =  $item->cmColumn->orderBtn->getClientID();
			
			 $item->cmColumn->lightConstructor->Text = '
			 <script type="text/javascript">
				if (!$(\''.$cm.'\').onclick) {myLightWindow.createWindow(\''.$cm.'\'); exampleCreated = true;}
			</script>';	
			*/
			
			
			
			
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
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
          $sql = "DELETE FROM tbd_pasien WHERE cm='$ID'";
          $this->queryAction($sql,'C');
    			$sql = "DELETE FROM tbt_rawat_jalan WHERE cm='$ID'";
          $this->queryAction($sql,'C');
          $sql = "DELETE FROM tbd_luarkota WHERE cm='$ID'";
          $this->queryAction($sql,'C');
    			//PasienRecord::finder()->deleteByCm($ID);
          //RwtjlnRecord::finder()->deleteBy($ID);	
    			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariData'));
        }	
    }	  
  
}

?>
