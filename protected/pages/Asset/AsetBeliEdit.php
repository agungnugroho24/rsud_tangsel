<?php
class AsetBeliEdit extends SimakConf
{
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 30;
	
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
	
	public function getTindakanRecords()
	{/*
		$sql="SELECT 
				  tbm_nama_tindakan.id,
				  tbm_nama_tindakan.nama
				FROM
				  tbm_tarif_tindakan
				  INNER JOIN tbm_nama_tindakan ON (tbm_tarif_tindakan.id = tbm_nama_tindakan.id)
				WHERE
				  (tbm_tarif_tindakan.biaya1 + tbm_tarif_tindakan.biaya2 + tbm_tarif_tindakan.biaya3 + tbm_tarif_tindakan.biaya4) > 0
				ORDER BY
				  nama ASC";				*/
		$sql="SELECT
					tbm_barang.id AS kode,
					tbm_barang.nama
				FROM
					tbm_barang
					Inner Join tbt_stok_aset ON (tbm_barang.id = tbt_stok_aset.id_barang)
				WHERE
					tbt_stok_aset.tujuan =  '1' AND
					tbt_stok_aset.sumber =  '01'
				GROUP BY
					tbm_barang.id
				ORDER BY tbm_barang.nama ASC";
		//$data=ObatRecord::finder()->findAllBySql($sql);
		$data=$this->queryAction($sql,'R');
		return $data;
	}
	
	private function bindGrid()
    {
		
        $this->pageSize = $this->EditGrid->PageSize;
        $this->offset = $this->pageSize * $this->EditGrid->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$nmTable = $this->getViewState('nmTable');
			
            $sql = "SELECT * FROM $nmTable ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->EditGrid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
					
            $this->EditGrid->DataSource = $data;
            $this->EditGrid->DataBind();
	    	//$this->showSql->Text=$sql;
			
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
            
            $this->EditGrid->DataSource = $session["SomeData"];
            $this->EditGrid->DataBind();
			
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
	
	protected function itemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           	$item->jmlColumn->TextBox->Width='150px';
		   	$item->kode->DropDownList->Width = '150px';
		   
		   	$this->EditGrid->Columns[0]->Visible = true;	
			$this->EditGrid->Columns[1]->Visible = false;	
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{
			$this->EditGrid->Columns[0]->Visible = false;	
			$this->EditGrid->Columns[1]->Visible = true;	
		}
    }
	
	protected function editItem($sender,$param)
    {
        $this->EditGrid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function cancelItem($sender,$param)
    {
        $this->EditGrid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function saveItem($sender,$param)
    {
		
		$nmTable = $this->getViewState('nmTable');
		
        $item = $param->Item;
		
		$id = $this->EditGrid->DataKeys[$item->ItemIndex];	
		
		$sql="SELECT * FROM $nmTable WHERE id='$id' ";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			//$kode=$row['kode'];	
			$jml_awal=$row['jml'];
		}		
		
		$jml = $item->jmlColumn->TextBox->Text;
		$kode= $item->kode->DropDownList->SelectedValue;
		//if($jml_awal <> $jml)
		//{
			//$sql="UPDATE $nmTable SET jml='$jml' AND kode='$kode' WHERE id='$id'";
$sql="UPDATE $nmTable SET jml='$jml', kode='$kode' WHERE id='$id'";
			$this->queryAction($sql,'C');

		//}
			
        $this->EditGrid->EditItemIndex = -1;
        $this->bindGrid();
    }
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('12');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
 	public function onLoad($param)
	{
		parent::onLoad($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack){
			if($this->Request['noPO'])
			{
				$noPo=$this->Request['noPO'];	
				$this->setViewState('noPO',$noPo);
				$sql = "SELECT * FROM tbt_aset_beli WHERE no_po = '$noPo' ORDER BY id";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					if (!$this->getViewState('nmTable'))
					{
						$nmTable = $this->setNameTable('nmTable');
						$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
								  id_awal int(11) NOT NULL,
								  kode varchar(5) NOT NULL,
								  jml int(11) NOT NULL,
								  jml_edit int(11) DEFAULT 0,
								  PRIMARY KEY (id)) ENGINE = MEMORY";
							
						$this->queryAction($sql,'C');//Create new tabel bro...
						
						$idAwal = $row['id'];
						$kode = $row['kode'];
						$jml = $row['jumlah'];
						
						$sql="INSERT INTO $nmTable (id_awal,kode,jml) VALUES ('$idAwal','$kode','$jml')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
						
						$sql="SELECT * FROM $nmTable ORDER BY id";
						$arr=$this->queryAction($sql,'R');				
						
						$this->bindGrid();
						//$this->EditGrid->DataSource = $arr;
						//$this->EditGrid->dataBind();
					}
					else
					{
						$nmTable = $this->getViewState('nmTable');
						
						$idAwal = $row['id'];
						$kode = $row['kode'];
						$jml = $row['jumlah'];
						
						$sql="INSERT INTO $nmTable (id_awal,kode,jml) VALUES ('$idAwal','$kode','$jml')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
						
						$sql="SELECT * FROM $nmTable ORDER BY id";
						$arr=$this->queryAction($sql,'R');				
						
						$this->bindGrid();
						//$this->EditGrid->DataSource = $arr;
						//$this->EditGrid->dataBind();
					}
				}
			}				
		}	
		
		
	}
	
	
	
	
	public function batalClicked()
	{
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Asset.LapPembelianEdit'));
	} 
		
	public function cetakClicked($sender,$param)
	{		
		$noPO = $this->getViewState('noPO');
		//update tbt_aset_beli
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$id =  $row['id_awal'];
			$kode =  $row['kode'];
			$jml =  $row['jml'];
			$sql="UPDATE 
					tbt_aset_beli 
				  SET 
				  	jumlah='$jml', 
					jumlah_kecil='$jml', 
					kode = '$kode'
				  WHERE id='$id'";
			$this->queryAction($sql,'C');
		}
		
		$this->queryAction($this->getViewState('nmTable'),'D');
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakAsetBeliEdit',array('noPO'=>$noPO)));
       			
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
