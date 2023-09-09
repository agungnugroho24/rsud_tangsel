<?php
class LapPembelian1 extends SimakConf
{  	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	/**
	* General Public Variable.
	* Holds offset and limit data from array
	* @return array subset of values
	*/
		
	/**
	* Returns a subset of data.
	* In MySQL database, this can be replaced by LIMIT clause
	* in an SQL select statement.
	* @param integer the starting index of the row
	* @param integer number of rows to be returned
	* @return array subset of data
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
			
            $sql = "SELECT * FROM view_pembelian_obat1 WHERE no_po <> ''";				
						   
			if($this->DDJenisBrg->SelectedValue != '')	
			{				
				$kategori = $this->DDJenisBrg->SelectedValue;	
				$sql .= " AND kategori = '$kategori' ";	
			}
			
			if($this->ID->Text != '')	
			{				
				$kode = $this->ID->Text;	
				$sql .= " AND kode = '$kode' ";	
			}
							
			if($this->cariNama->Text != '')	
			{			
				$cariByNama = $this->cariNama->Text;
				if($this->Advance->Checked=== true)
				{
					$sql .= "AND nama LIKE '%$cariByNama%' ";
				}else{
					$sql .= "AND nama LIKE '$cariByNama%' ";
				}
			}
			
			if($this->tglFakAwal->Text != '' && $this->tglFakAkhir->Text != '')	
			{
				$tglFakAwal = $this->convertDate($this->tglFakAwal->Text,'2');
				$tglFakAkhir = $this->convertDate($this->tglFakAkhir->Text,'2');
				
				$sql .= "AND tgl_faktur BETWEEN '$tglFakAwal' AND '$tglFakAkhir' ";	
			}
			elseif($this->tglFakAwal->Text != '' && $this->tglFakAkhir->Text == '')	
			{
				$tglFakAwal = $this->convertDate($this->tglFakAwal->Text,'2');
				$sql .= "AND tgl_faktur = '$tglFakAwal' ";	
			}
			
			if($this->tglPoAwal->Text != '' && $this->tglPoAkhir->Text != '')	
			{
				$tglPoAwal = $this->convertDate($this->tglPoAwal->Text,'2');
				$tglPoAkhir = $this->convertDate($this->tglPoAkhir->Text,'2');
				
				$sql .= "AND tgl_po BETWEEN '$tglPoAwal' AND '$tglPoAkhir' ";	
			}
			elseif($this->tglPoAwal->Text != '' && $this->tglPoAkhir->Text == '')	
			{
				$tglPoAwal = $this->convertDate($this->tglPoAwal->Text,'2');
				$sql .= "AND tgl_po = '$tglPoAwal' ";	
			}
			
			
			if($this->tglTerimaAwal->Text != '' && $this->tglTerimaAkhir->Text != '')	
			{
				$tglTerimaAwal = $this->convertDate($this->tglTerimaAwal->Text,'2');
				$tglTerimaAkhir = $this->convertDate($this->tglTerimaAkhir->Text,'2');
				
				$sql .= "AND tgl_terima BETWEEN '$tglTerimaAwal' AND '$tglTerimaAkhir' ";	
			}
			elseif($this->tglTerimaAwal->Text != '' && $this->tglTerimaAkhir->Text == '')	
			{
				$tglTerimaAwal = $this->convertDate($this->tglTerimaAwal->Text,'2');
				$sql .= "AND tgl_terima = '$tglTerimaAwal' ";	
			}
			
			
			if($this->tglJatuhAwal->Text != '' && $this->tglJatuhAkhir->Text != '')	
			{
				$tglJatuhAwal = $this->convertDate($this->tglJatuhAwal->Text,'2');
				$tglJatuhAkhir = $this->convertDate($this->tglJatuhAkhir->Text,'2');
				
				$sql .= "AND tgl_jth_tempo BETWEEN '$tglJatuhAwal' AND '$tglJatuhAkhir' ";	
			}
			elseif($this->tglJatuhAwal->Text != '' && $this->tglJatuhAkhir->Text == '')	
			{
				$tglJatuhAwal = $this->convertDate($this->tglJatuhAwal->Text,'2');
				$sql .= "AND tgl_jth_tempo = '$tglJatuhAwal' ";	
			}
			
			if($this->RBtipeObat->SelectedValue != '')	
			{				
				$cariTipe = $this->RBtipeObat->SelectedValue;	
				$sql .= " AND tipe = '$cariTipe' ";	
			}
			/*
			if($this->DDGol->SelectedValue != '')	
			{				
				$cariByGol = $this->DDGol->SelectedValue;	
				$sql .= " AND gol = '$cariByGol' ";	
			}
			
			if($this->DDKlas->SelectedValue != '')	
			{				
				$cariByKlas = $this->DDKlas->SelectedValue;	
				$sql .= " AND klasifikasi = '$cariByKlas' ";	
			}
			
			if($this->DDDerivat->SelectedValue != '')	
			{				
				$cariByDerivat = $this->DDDerivat->SelectedValue;	
				$sql .= " AND derivat = '$cariByDerivat' ";	
			}
			*/
			if($this->DDPbf->SelectedValue != '')	
			{				
				$cariByPbf = $this->DDPbf->SelectedValue;	
				$sql .= " AND id_pbf = '$cariByPbf' ";	
			}
			
			if($this->DDProd->SelectedValue != '')	
			{				
				$cariByProd = $this->DDProd->SelectedValue;	
				$sql .= " AND produsen = '$cariByProd' ";	
			}
			/*
			if($this->DDSat->SelectedValue != '')	
			{				
				$cariBySat = $this->DDSat->SelectedValue;	
				$sql .= " AND id_sat = '$cariBySat' ";	
			}
			*/
			$this->setViewState('sql',$sql);
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakLapBtn->Enabled = true;
        	}
			else
			{
				$this->cetakLapBtn->Enabled = false;
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
			
			$this->clearViewState('sql');
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
		 
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$tglFak = $this->convertDate($item->DataItem['tgl_faktur'],'3');
			$tglJatuh = $this->convertDate($item->DataItem['tgl_jth_tempo'],'3');
			$hrgSat = $item->DataItem['hrg_ppn'];
			
            $item->fakColumn->tglFakLabel->Text = $tglFak;
			$item->jatuhColumn->tglJatuhLabel->Text = $tglJatuh;
			$item->hrgSatColumn->hrgSatLabel->Text = number_format($hrgSat,'2',',','.');;
			$item->totColumn->totLabel->Text = number_format($hrgSat * $item->DataItem['jumlah'],'2',',','.');
           	   
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
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
    }
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			//$jam = date('h:m');
			//$this->cariNama->Text = $jam ;
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
		
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			/*
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			*/
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll($criteria);
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll($criteria);
			$this->DDProd->dataBind();	
			/*
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll($criteria);
			$this->DDSat->dataBind();				
			*/
			$this->RBtipeObat->Enabled=false;	
			$this->cetakLapBtn->Enabled=false;
			
			$this->tglFakAkhir->Enabled = false;
			$this->tglPoAkhir->Enabled = false;
			$this->tglTerimaAkhir->Enabled = false;
			$this->tglJatuhAkhir->Enabled = false;
		}
		else
		{
			if($this->tglFakAwal->Text != '')	
			{
				$this->tglFakAkhir->Enabled = true;
			}
			else
			{	
				$this->tglFakAkhir->Text = '';
				$this->tglFakAkhir->Enabled = false;
			}
			
			if($this->tglPoAwal->Text != '')	
			{
				$this->tglPoAkhir->Enabled = true;
			}
			else
			{	
				$this->tglPoAkhir->Text = '';
				$this->tglPoAkhir->Enabled = false;
			}
			
			if($this->tglJatuhAwal->Text != '')	
			{
				$this->tglJatuhAkhir->Enabled = true;
			}
			else
			{	
				$this->tglJatuhAkhir->Text = '';
				$this->tglJatuhAkhir->Enabled = false;
			}
			
			if($this->tglTerimaAwal->Text != '')	
			{
				$this->tglTerimaAkhir->Enabled = true;
			}
			else
			{	
				$this->tglTerimaAkhir->Text = '';
				$this->tglTerimaAkhir->Enabled = false;
			}
			
		}
    }		
	
	public function panelCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function chTipe()
	{
		$this->cariClicked();
	}
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->Enabled=false;
			$this->RBtipeObat->SelectedIndex = -1;
		}		
		
		$this->cariClicked();
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
	
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			 $this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;			
		}	
    }
	
	public function cancelItem($sender,$param)
    {   
		$this->UserGrid->EditItemIndex=-1;       		
		$this->ID->focus();
    }
	
	/**
     * Deletes a specified user record.
     * This method responds to the datagrid's OnDeleteCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			//ObatRecord::finder()->deleteByPk($ID);	
			//$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);
		
	}
	
	public function cetakClicked($sender,$param)
	{	
		$session=new THttpSession;
		$session->open();
		$session['cetakLapBeliSql'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakLapPembelian'));
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
	}		
}
?>
