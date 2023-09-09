<?php
class BayarPbf extends SimakConf
{ 
	private $sortExp = "tgl_fak";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
	 }
	 	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			/*
			$this->tglBayar->Date = date('d-m-Y');
			$this->noFaktur->Focus();
			*/
			
			$sql = "SELECT no_trans FROM tbt_bayar_pbf WHERE st='0' GROUP BY no_trans";
			$this->DDtrans->DataSource = $this->queryAction($sql,'S');
			$this->DDtrans->dataBind();
				
			$this->prosesPanel->Display = "None";
			$this->jmlPanel->Display = "None";
			$this->cetakBtn->Enabled = false;
			
		}
		else
		{
			if($this->DDtrans->SelectedValue != '')
			{
				$this->prosesLock();	
			}
			else
			{
				$this->prosesUnlock();				
			}	
		}
		
	}
	
	public function onLoad($param)
	{	
		parent::onLoad($param);
	}
	
	public function prosesLock()
	{	
		$this->prosesPanel->Display = 'Dynamic';	
				
		$this->bindGridBrg();
		$this->cetakBtn->Enabled = true;
	} 
	
	public function prosesUnlock()
	{	
		$this->cetakBtn->Enabled = false;
		$this->prosesPanel->Display = 'None';
		
		$this->Page->CallbackClient->focus($this->DDtrans);	
		$this->DDtrans->SelectedValue = 'empty';
	} 
	
	public function DDtransChanged($sender,$param)
	{	
		//$this->makeTblTemp();
		
		
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
	
	private function bindGridBrg()
    {
        $this->pageSize = $this->gridBrg->PageSize;
        $this->offset = $this->pageSize * $this->gridBrg->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
			$nmTable = $this->getViewState('nmTable');
			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			/*
			$sql="SELECT 
					tbm_pbf_obat.nama AS nm_supplier, 
					tbt_obat_masuk.tgl_faktur AS tgl_fak,
					tbt_obat_masuk.tgl_jth_tempo,
					$nmTable.* 
				  FROM $nmTable
					 INNER JOIN tbt_obat_beli ON ($nmTable.no_po = tbt_obat_beli.no_po)
					 INNER JOIN tbt_obat_masuk ON ($nmTable.no_faktur = tbt_obat_masuk.no_faktur)
					 INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
				  GROUP BY $nmTable.no_faktur ";
			*/
			
			$noTrans = $this->DDtrans->SelectedValue;
			
			$sql="SELECT 
					tbm_pbf_obat.nama AS nm_supplier, 
					tbt_obat_masuk.tgl_faktur AS tgl_fak,
					tbt_obat_masuk.tgl_jth_tempo,
					tbt_bayar_pbf.* 
				  FROM tbt_bayar_pbf
					 INNER JOIN tbt_obat_beli ON (tbt_bayar_pbf.no_po = tbt_obat_beli.no_po)
					 INNER JOIN tbt_obat_masuk ON (tbt_bayar_pbf.no_faktur = tbt_obat_masuk.no_faktur)
					 INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
				  WHERE
				  	tbt_bayar_pbf.no_trans = '$noTrans'
				  GROUP BY tbt_bayar_pbf.no_faktur ";
				  
			$this->setViewState('sql',$sql);
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridBrg->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->msg->Text=$someDataList->getSomeDataCount($sql).' Barang';  
            $this->gridBrg->DataSource = $data;
            $this->gridBrg->DataBind();
	    	//$this->showSql->Text=$sql;
			/*
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
			*/
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridBrg->DataSource = $session["SomeData"];
            $this->gridBrg->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridBrg_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->gridBrg->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridBrg();
    }

    protected function gridBrg_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->gridBrg->CurrentPageIndex = 0;
        $this->bindGridBrg();

    }	
	
	protected function gridBrg_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$noFaktur = $item->DataItem['no_faktur'];
			$tglFaktur = $item->DataItem['tgl_fak'];
			$tglJthTempo = $item->DataItem['tgl_jth_tempo'];
			$st_tunda = $item->DataItem['st_tunda'];
			$total = $item->DataItem['total'];
			
			$tgl_faktur = explode('-',$tglFaktur );
			$tgl_jth_tempo = explode('-',$tglJthTempo);
			
			
			$item->column1->Text = $noFaktur;
			$item->column2->Text = $tgl_faktur['2'].'-'.$tgl_faktur['1'].'-'.$tgl_faktur['0'];
			$item->column3->Text = $tgl_jth_tempo['2'].'-'.$tgl_jth_tempo['1'].'-'.$tgl_jth_tempo['0'];
			$item->column4->Text = $item->DataItem['nm_supplier'];
			$item->column5->Text = number_format($total,'2',',','.');
			
			if($st_tunda == '0')
			{
				$item->column6->Text = 'Tidak';
			}
			else
			{
				$item->column6->Text = 'Ya';
			}
        } 
    }
	
	protected function gridBrg_EditCommand($sender,$param)
    { $this->gridBrg->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_CancelCommand($sender,$param)
    { 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_UpdateCommand($sender,$param)
    {
        $item = $param->Item; 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	public function gridEditBrgClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
	}
	
	public function gridHapusBrgClicked($sender,$param)
	{
		$no_faktur = $param->CommandParameter;
		
		$nmTable = $this->getViewState('nmTable');
				
		$sql = "DELETE FROM $nmTable WHERE no_faktur='$no_faktur'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr)==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}
		
		$this->Page->CallbackClient->focus($this->noFaktur);
	}
	
	public function cetakClicked($sender,$param)
    {
		$noTrans = $this->DDtrans->SelectedValue;
		
		//Update st di tbt_bayar_pbf
		$sql="UPDATE tbt_bayar_pbf SET st='1' WHERE no_trans='$noTrans' ";
		$this->queryAction($sql,'C');
		
		$sql = "SELECT * FROM tbt_bayar_pbf WHERE no_trans='$noTrans'";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$no_faktur = $row['no_faktur'];
			
			//Update st_keuangan di tbt_obat_masuk
			$sql="UPDATE tbt_obat_masuk SET st_keuangan='2' WHERE no_faktur='$no_faktur' ";
			$this->queryAction($sql,'C');
		}
		
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakBayarPbf',array('noTrans'=>$noTrans)));
	}
	
	public function batalClicked()
	{
		$this->prosesUnlock();
	}
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}	
			
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	}
?>
