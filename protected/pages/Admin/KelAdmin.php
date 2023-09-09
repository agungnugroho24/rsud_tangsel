<?php
class KelAdmin extends SimakConf
{
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;


	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDProv->DataSource = PropinsiRecord::finder()->findAll($criteria);
			$this->DDProv->DataBind();
			$this->DDProv->SelectedValue = '02';
			$this->DDProvChanged();
			//$this->bindGrid();
			//$this->pagerPanel->Display = 'None';
			
		}
		
	}	
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)  
		{						
				
		}
	}
	
	public function DDProvChanged()
	{
		$this->DDKotaKab->SelectedValue = 'empty';
		
		if($this->DDProv->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			
			$sql = "SELECT id,nama FROM tbm_kabupaten WHERE id_propinsi = '$idProv' ORDER BY nama ";
			
			$this->DDKotaKab->DataSource=KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKotaKab->dataBind();
			$this->DDKotaKab->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKotaKab);
		}
		else
		{	
			$this->DDKotaKab->DataSource='';
			$this->DDKotaKab->dataBind();
			$this->DDKotaKab->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDProv);
		}
		
		$this->DDKotaKabChanged();
	}
	
	public function DDKotaKabChanged()
	{
		$this->DDKec->SelectedValue = 'empty';
		
		if($this->DDKotaKab->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			$idKotaKab = $this->DDKotaKab->SelectedValue;
			$idFilter = $idProv.''.$idKotaKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKec);
		}
		else
		{
			$this->DDKec->DataSource='';
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDKotaKab);
		}
		
		$this->prosesClicked();
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
	    $this->pageSize = $this->grid->PageSize;
        $this->offset = $this->pageSize * $this->grid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$sql = "SELECT 
					   *
					FROM
						tbm_kelurahan 
					WHERE 
						id <> ''	 ";	
			
			if($this->DDProv->SelectedValue != '')
			{
				$DDProv = $this->DDProv->SelectedValue;
				
				if($this->DDKotaKab->SelectedValue != '')
				{
					$DDKotaKab = $this->DDKotaKab->SelectedValue;
					$idCari = $DDProv.''.$DDKotaKab;
					
					if($this->DDKec->SelectedValue != '')
					{
						$DDKec = $this->DDKec->SelectedValue;
						$idCari = $DDKec;
						
						$sql .= "AND SUBSTRING(id,1,7) = $idCari ";
					}
					else
					{
						$sql .= "AND SUBSTRING(id,1,5) = $idCari ";
					}
				}
				else
				{
					$sql .= "AND SUBSTRING(id,1,2) = $DDProv ";
				}
			}			
			
			if($this->nama->Text != '')
			{
				$nama = $this->nama->Text;
				$sql .= "AND nama LIKE '%$nama%' ";
			}			
			
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->grid->DataSource = $data;
            $this->grid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 20)
			{
				$this->pagerPanel->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 20 )
			{
				$this->pagerPanel->Display = 'None';
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->grid->DataSource = $session["SomeData"];
            $this->grid->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function grid_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function grid_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();

    }	
	
	protected function grid_ItemCreated($sender,$param)
    {
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$ID = $this->grid->DataKeys[$item->ItemIndex];
			
			$item->idColumn->Text = $ID;
			$item->namaColumn->Text = $item->DataItem['nama'];
		}
    }
	
	protected function grid_EditCommand($sender,$param)
    { $this->grid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function grid_CancelCommand($sender,$param)
    { $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function grid_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function gridEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('admin.KelEdit',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$sql = "DELETE FROM tbm_kelurahan WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGrid();
	}
		
	
	public function repeaterPanelCallBack($sender,$param)
	{
		$this->pagerPanel->render($param->getNewWriter());
	}
	
	public function prosesClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");        
        $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();
	}
		
    public function baruClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('admin.KelBaru'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
}
?>
