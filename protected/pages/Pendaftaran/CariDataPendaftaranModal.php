<?php

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class CariDataPendaftaranModal extends SimakConf
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
			
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDPerusAsuransi->dataBind();
			$this->DDPerusAsuransi->Enabled = false;
				
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
	
	public function selectionChangedKelompok()
	{
		if($this->DDUrut->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			
			if($this->DDUrut->SelectedValue == '02')
			{
				$this->DDPerusAsuransi->Enabled = true;	
			}
			else
			{
				$this->DDPerusAsuransi->SelectedValue = 'empty';
				$this->DDPerusAsuransi->Enabled = false;
			}
		}
		
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
					  tbm_perusahaan_asuransi.nama AS nm_perus_asuransi,
					  tbm_kabupaten.nama AS nm_kab ";
			
			$sql .= "FROM
					  tbd_pasien					  
					  INNER JOIN tbm_kabupaten ON (tbd_pasien.kabupaten = tbm_kabupaten.id) 
					  LEFT JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id)
					  LEFT JOIN tbm_perusahaan_asuransi ON (tbd_pasien.perusahaan = tbm_perusahaan_asuransi.id)
					 WHERE
					  tbd_pasien.cm <> ''";
			
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
			
			if($this->DDPerusAsuransi->SelectedValue != '')
			{
				$DDPerusAsuransi = $this->DDPerusAsuransi->SelectedValue;
				$sql .= "AND tbd_pasien.perusahaan = '$DDPerusAsuransi' ";
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
		
	public function cariClicked()
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
				
        }
    }
	
	public function pilihClicked($sender,$param)
	{
		$tipeRawat = $this->Request['tipeRawat'];
		
		$ID = $param->CommandParameter;
		
		
		if($tipeRawat=='2') //dari Pendaftaran Rawat Inap
		{
			$this->msg->Text = '   
			<script type="text/javascript">
				window.parent.location="index.php?page=Pendaftaran.DaftarRwtInap&cm='.$ID.'"; window.close();
			</script>';	
		}
		else //dari Pendaftaran Rawat jalan
		{
			$this->msg->Text = '   
			<script type="text/javascript">
				window.parent.location="index.php?page=Pendaftaran.DaftarRwtJln&cm='.$ID.'"; window.close();
			</script>';	
		}
		
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
		
	}
  
}

?>
