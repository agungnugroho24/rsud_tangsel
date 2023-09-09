<?php
class admPenerimaanBarang extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			$this->DDDist->DataSource=AssetDistributorRecord::finder()->findAll();
			$this->DDDist->dataBind();
			
			$this->DDbarang->DataSource=AssetBarangRecord::finder()->findAll();
			$this->DDbarang->dataBind();
			
			$this->noFak->focus();				
		}
	}
	
	private $sortExp = "id_barang";
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
			
            $nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ";
			
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
			$item->noSeriColumn->TextBox->Columns=15;
		    $item->garansiColumn->TextBox->Columns=3;  
			$this->dtgSomeData->focus();      	   
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
	
	public function deleteButtonClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "DELETE FROM $nmTable WHERE id_barang='$ID'";
				$this->queryAction($sql,'C');								
				
				$sql="SELECT * FROM $nmTable ORDER BY id_barang";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				$session = $this->getSession();
				$session->remove("SomeData");        
				
				$this->dtgSomeData->EditItemIndex=-1;
				$this->bindGrid();
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false;
				}
				
				$this->DDDist->focus();
			//}	
			
		//}	
    }
	
	protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        //if ($this->User->IsAdmin)
		//{			
            // i'm using here TActiveRecord for simplicity
            //$oSomeData = SomeDataList::getSomeData('SomeData',$this->dtgSomeData->DataKeys[$item->ItemIndex]);
			$idBrg = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$noSeri = $item->noSeriColumn->TextBox->Text;
			
			if(strlen(abs($item->garansiColumn->TextBox->Text)) > 3)
			{
				$garansi = 0;
			}
			else
			{
				$garansi = abs($item->garansiColumn->TextBox->Text);
			}
			
			
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "UPDATE
						$nmTable
					SET
						no_seri = '$noSeri',
						garansi = '$garansi'
					WHERE
						id_barang = '$idBrg' ";
            $this->queryAction($sql,'C');
			
            // clear data in session because we need to refresh it from db
            // you could also modify the data in session and not clear the data from session!
            $session = $this->getSession();
            $session->remove("SomeData");        
        //}
		
        $this->dtgSomeData->EditItemIndex=-1;
        $this->bindGrid();
		$this->dtgSomeData->focus();
    }
	
	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$this->noFak->ReadOnly = true;
			$this->tglFaktur->ReadOnly= true;
			$this->tglTerima->ReadOnly = true;
			$this->tglJthTempo->ReadOnly = true;
			$this->prosesBtn->Enabled= false;
			
			$this->tambahCtrl->Visible = true;
			$this->DDDist->focus();
		}
	}
	
	public function tambahClicked()
	{	
		if($this->IsValid)  // when all validations succeed
        {
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (
						id_barang varchar (4) NOT NULL,
						nm_barang varchar (50) NOT NULL, 
						jml int(11) NOT NULL,
						no_seri varchar(50) DEFAULT NULL,
						garansi INT(5) DEFAULT NULL,						
						id_distributor int(5) NOT NULL,
						nm_distributor varchar (30) NOT NULL, 
						no_faktur varchar (30) NOT NULL, 
						tgl_faktur date NOT NULL,
						tgl_terima_barang date NOT NULL,
						tgl_jatuh_tempo date DEFAULT '0000-00-00',
						ket varchar(100) DEFAULT NULL,
						PRIMARY KEY (id_barang)
					) ENGINE = MEMORY";
					
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$idBrg = $this->DDbarang->SelectedValue;
				$nmBrg = $this->ambilTxt($this->DDbarang);
				$jmlBrg = $this->jml->Text;
				$idDist = $this->DDDist->SelectedValue;
				$nmDist = $this->ambilTxt($this->DDDist);
				$noFak = $this->noFak->Text;
				$tglFak = $this->convertDate($this->tglFaktur->Text,'2');
				$tglTerima = $this->convertDate($this->tglTerima->Text,'2');
				$ket = $this->ket->Text;
				//if($this->tglJthTempo->Text != '')
					$tglJatuhTempo = $this->convertDate($this->tglJthTempo->Text,'2');
				
				$sql="INSERT INTO 
						$nmTable
						(
							id_barang,
							nm_barang, 
							jml,				
							id_distributor,
							nm_distributor, 
							no_faktur, 
							tgl_faktur,
							tgl_terima_barang,
							tgl_jatuh_tempo,
							ket
						)
					  VALUES
					  	(
							'$idBrg',
							'$nmBrg',
							'$jmlBrg',
							'$idDist',
							'$nmDist',
							'$noFak',
							'$tglFak',
							'$tglTerima',
							'$tglJatuhTempo',
							'$ket'
						)";
				
				$this->queryAction($sql,'C');//Insert new row in tabel bro...				
				$this->bindGrid();
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				
				$idBrg = $this->DDbarang->SelectedValue;
				$nmBrg = $this->ambilTxt($this->DDbarang);
				$jmlBrg = $this->jml->Text;
				$idDist = $this->DDDist->SelectedValue;
				$nmDist = $this->ambilTxt($this->DDDist);
				$noFak = $this->noFak->Text;
				$tglFak = $this->convertDate($this->tglFaktur->Text,'2');
				$tglTerima = $this->convertDate($this->tglTerima->Text,'2');
				$ket = $this->ket->Text;
				
				//if($this->tglJthTempo->Text != '')
					$tglJatuhTempo = $this->convertDate($this->tglJthTempo->Text,'2');
				
				$sql = "SELECT jml FROM $nmTable WHERE id_barang = '$idBrg'";
				$arr = $this->queryAction($sql,'S');		
				
				if(count($arr) > 0) // UPDATE jika ada id_barang yg dipilih sudah ada  di tmp tabel
				{
					foreach($arr as $row)
					{
						$jmlAwal = $row['jml'];
					}
					
					$jml = $jmlBrg + $jmlAwal;
					
					$sql = "UPDATE 
								$nmTable 
							SET
								jml = '$jml'
							WHERE
								id_barang = '$idBrg' ";
				}
				else //Insert jika belum ada barang yg sama di tmp tabel
				{
					$sql="INSERT INTO 
						$nmTable
						(
							id_barang,
							nm_barang, 
							jml,				
							id_distributor,
							nm_distributor, 
							no_faktur, 
							tgl_faktur,
							tgl_terima_barang,
							tgl_jatuh_tempo,
							ket
						)
					  VALUES
					  	(
							'$idBrg',
							'$nmBrg',
							'$jmlBrg',
							'$idDist',
							'$nmDist',
							'$noFak',
							'$tglFak',
							'$tglTerima',
							'$tglJatuhTempo',
							'$ket'
						)";
				}
				
				
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				$this->bindGrid();
			}
			
			$this->DDDist->SelectedIndex = -1 ;			
			$this->DDbarang->SelectedIndex = -1;
			$this->jml->Text = '';	
			$this->ket->Text = '';			
			$this->DDDist->focus();
			$this->cetakBtn->Enabled = true;
			
		}
	} 
	
	public function batalClicked()
	{
		if ($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}	
		
		$this->Response->Reload();
	}
	
	public function cetakClicked()
	{
		$nmTable=$this->getViewState('nmTable');
		
		$sql="SELECT * FROM $nmTable ORDER BY id_barang";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$noFak = $row['no_faktur'];
			$idBrg = $row['id_barang'];
			$jml = $row['jml'];
			
			$newData= new AssetPenerimaanRecord();
			$newData->id_brg = $row['id_barang'];
			$newData->no_seri = $row['no_seri'];
			$newData->garansi = $row['garansi'];
			$newData->no_fak = $row['no_faktur'];
			$newData->tgl_fak = $row['tgl_faktur'];
			$newData->tgl_terima = $row['tgl_terima_barang'];
			$newData->tgl_jatuh_tempo = $row['tgl_jatuh_tempo'];
			$newData->distributor = $row['id_distributor'];							
			$newData->jml = $row['jml'];			
			$newData->tgl = date('Y-m-d');
			$newData->wkt = date('H:m:s');		
			$newData->operator = $this->User->IsUserNip;
			$newData->ket = $row['ket'];				
			$newData->st= '0';
			
			$newData->Save();	
			
			//INSERT / UPDATE tbt_asset_stok_brg			
			$sql="SELECT jml FROM tbt_asset_stok_brg WHERE id_brg='$idBrg'";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0) //UPDATE jika ada id_brg yg sama
			{
				foreach($arr as $row)
				{
					$jmlAwal = $row['jml'];
				}			
				
				//Update tbt_stok_lain
				$jmlTotal = $jml + $jmlAwal;
				$sql="UPDATE 
						tbt_asset_stok_brg 
					SET 
						jml='$jmlTotal' 
					WHERE 
						 id_brg='$idBrg' ";	
			}
			else //INSERT jika tidak ada barang yg sama
			{
				$sql="INSERT INTO tbt_asset_stok_brg (id_brg,jml) VALUES ('$idBrg','$jml')";
			}
			
			$this->queryAction($sql,'C');		
		}
		
		$this->queryAction($this->getViewState('nmTable'),'D');
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakPenerimaanBrg',array('noFak'=>$noFak)));
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
