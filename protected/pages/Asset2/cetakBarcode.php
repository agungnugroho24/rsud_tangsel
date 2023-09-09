<?php
class cetakBarcode extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			$this->DDTujuan->DataSource=AssetRuangRecord::finder()->findAll();
			$this->DDTujuan->dataBind();
			
			$this->DDTujuan->focus();				
		}
	}
	
	public function DDTujuanChanged()
	{
		$idTujuan = $this->DDTujuan->SelectedValue;
		if($idTujuan != '')
		{
			$sql="SELECT 
					no_trans	
				FROM 
					tbt_asset_dist_brg
				WHERE 
					tujuan='$idTujuan' 
				GROUP BY 
					no_trans";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr)>0)
			{
				foreach($arr as $row)
				{
					$data .= $row['no_trans'] .',';		
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$arr;
				$this->DDtrans->dataBind();
				
				$this->errMsg->Text='';
				$this->DDtrans->Enabled = true;
				$this->DDtrans->focus();
			}
			else
			{
				$this->errMsg->Text='Belum ada distribusi barang';
				$this->DDtrans->Enabled = false;
				$this->dtgSomeData->Visible = false;
			}
		}
		else
		{
			$this->DDtrans->Enabled = false;
			$this->errMsg->Text='';
			$this->dtgSomeData->Visible = false;
			$this->cetakBtn->Enabled = false;
		}
	}
	
	public function DDtransChanged()
	{
		$noTrans = $this->DDtrans->SelectedValue;
		if($noTrans != '')
		{
			$session = $this->getSession();
			$session->remove("SomeData");
			
			$this->dtgSomeData->CurrentPageIndex = 0;
			$this->bindGrid();
			$this->dtgSomeData->Visible = true;
			$this->cetakBtn->Enabled = true;
		}
		else
		{
			$this->dtgSomeData->Visible = false;
			$this->cetakBtn->Enabled = false;
		}
	}
	
	
	private $sortExp = "id_brg";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;
	
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
			
			$sql="SELECT 
					  tbt_asset_dist_brg.no_trans,
					  tbt_asset_dist_brg.id_brg,
					  tbt_asset_dist_brg.jml,
					  tbm_asset_barang.nama AS nm_barang
					FROM
					  tbt_asset_dist_brg
					  INNER JOIN tbm_asset_barang ON (tbt_asset_dist_brg.id_brg = tbm_asset_barang.id) ";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
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
	
	public function batalClicked()
	{
		$this->Response->Reload();
	}
	
	public function cetakClicked()
	{
		$noTrans = $this->ambilTxt($this->DDtrans);
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakBarcodePdf',array('noTrans'=>$noTrans)));
	}
	
	
	public function keluarClicked($sender,$param)
	{		
		if ($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}	
		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
	
	
}
?>
