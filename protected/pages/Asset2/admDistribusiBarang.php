<?php
class admDistribusiBarang extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			$sql="SELECT id, nama FROM tbd_pegawai ORDER BY nama";
			$arrDataPegawai=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$this->DDTujuan->DataSource=AssetRuangRecord::finder()->findAll();
			$this->DDTujuan->dataBind();
			
			$this->DDpengirim->DataSource=$arrDataPegawai;
			$this->DDpengirim->dataBind();
			
			$this->DDpenerima->DataSource=$arrDataPegawai;
			$this->DDpenerima->dataBind();
			
			$sql="SELECT 
				  tbt_asset_stok_brg.id_brg,
				  tbm_asset_barang.nama,
				  tbt_asset_stok_brg.jml
				FROM
				  tbt_asset_stok_brg
				  INNER JOIN tbm_asset_barang ON (tbt_asset_stok_brg.id_brg = tbm_asset_barang.id)
				WHERE
					tbt_asset_stok_brg.jml <> 0 ";
			$arrDataStokBrg=$this->queryAction($sql,'S');//Select row in tabel bro...
			
			$this->DDbarang->DataSource=$arrDataStokBrg;
			$this->DDbarang->dataBind();
			
			$this->DDTujuan->focus();				
		}
		
		
		if ($this->getViewState('nmTable'))
		{
			$this->bindGrid();
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
			   	   
        } 
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
				
				$this->DDbarang->focus();
			//}	
			
		//}	
    }
	
	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$this->DDTujuan->Enabled= false;
			$this->DDpengirim->Enabled= false;
			$this->DDpenerima->Enabled= false;
			$this->prosesBtn->Enabled= false;
			
			$this->tambahCtrl->Visible = true;
			$this->DDbarang->focus();
		}
	}
	
	public function DDbarangChanged()
	{
		if($this->DDbarang->SelectedValue != '')
		{
			$this->jml->Text = AssetStokBrgRecord::finder()->find('id_brg=?',$this->DDbarang->SelectedValue)->jml;
			$this->jml->focus();
		}
		else
		{
			$this->jml->Text = '';
		}
	}
	
	public function checkJml($sender,$param)
    {   
		if(TPropertyValue::ensureINTEGER($this->jml->Text))
		{
			$jmlStok = AssetStokBrgRecord::finder()->find('id_brg=?',$this->DDbarang->SelectedValue)->jml;
			$param->IsValid=($this->jml->Text <= $jmlStok);
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
						id_ruang int(5) NOT NULL,
						nm_ruang varchar (30) NOT NULL, 
						id_pengirim varchar (4) NOT NULL,
						nm_pengirim varchar (30) NOT NULL, 
						id_penerima varchar (4) NOT NULL,
						nm_penerima varchar (30) NOT NULL,
						ket varchar (100) NOT NULL,
						PRIMARY KEY (id_barang)
					) ENGINE = MEMORY";
					
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$idBrg = $this->DDbarang->SelectedValue;
				$nmBrg = $this->ambilTxt($this->DDbarang);
				$jmlBrg = $this->jml->Text;				
				$idRuang = $this->DDTujuan->SelectedValue;
				$nmRuang = $this->ambilTxt($this->DDTujuan);				
				$idPengirim = $this->DDpengirim->SelectedValue;
				$nmPengirim = $this->ambilTxt($this->DDpengirim);				
				$idPenerima = $this->DDpenerima->SelectedValue;
				$nmPenerima = $this->ambilTxt($this->DDpenerima);
				$ket = $this->ket->Text;
				
				$sql="INSERT INTO 
						$nmTable
						(
							id_barang,
							nm_barang, 
							jml,				
							id_ruang,
							nm_ruang, 
							id_pengirim,
							nm_pengirim, 
							id_penerima,
							nm_penerima,
							ket 
						)
					  VALUES
					  	(
							'$idBrg',
							'$nmBrg',
							'$jmlBrg',
							'$idRuang',
							'$nmRuang',
							'$idPengirim',
							'$nmPengirim',
							'$idPenerima',
							'$nmPenerima',
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
				$idRuang = $this->DDTujuan->SelectedValue;
				$nmRuang = $this->ambilTxt($this->DDTujuan);				
				$idPengirim = $this->DDpengirim->SelectedValue;
				$nmPengirim = $this->ambilTxt($this->DDpengirim);				
				$idPenerima = $this->DDpenerima->SelectedValue;
				$nmPenerima = $this->ambilTxt($this->DDpenerima);
				$ket = $this->ket->Text;
				
				
				
				$sql = "SELECT jml FROM $nmTable WHERE id_barang = '$idBrg' AND id_ruang = '$idRuang'";
				$arr = $this->queryAction($sql,'S');		
				
				if(count($arr) > 0) // UPDATE jika ada id_barang yg dipilih sudah ada  di tmp tabel
				{
					foreach($arr as $row)
					{
						$jmlAwal = $row['jml'];
					}
					
					$jml = $jmlBrg + $jmlAwal;
					
					$jmlStok = AssetStokBrgRecord::finder()->find('id_brg=?',$this->DDbarang->SelectedValue)->jml;
					if($jml <= $jmlStok) //jika stok masih mencukupi update
					{
						$sql = "UPDATE 
								$nmTable 
							SET
								jml = '$jml'
							WHERE
								id_barang = '$idBrg'
								AND id_ruang = '$idRuang' ";
					}
				}
				else //Insert jika belum ada barang yg sama di tmp tabel
				{
					$sql="INSERT INTO 
						$nmTable
						(
							id_barang,
							nm_barang, 
							jml,				
							id_ruang,
							nm_ruang, 
							id_pengirim,
							nm_pengirim, 
							id_penerima,
							nm_penerima,
							ket 
						)
					  VALUES
					  	(
							'$idBrg',
							'$nmBrg',
							'$jmlBrg',
							'$idRuang',
							'$nmRuang',
							'$idPengirim',
							'$nmPengirim',
							'$idPenerima',
							'$nmPenerima',
							'$ket'
						)";
				}
				
				
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				$this->bindGrid();
			}
					
			$this->DDbarang->SelectedIndex = -1;
			$this->jml->Text = '';	
			$this->ket->Text = '';			
			$this->DDbarang->focus();
			$this->cetakBtn->Enabled = true;
			
		}
	} 
	
	public function makeBarcode()
    {	
		$sql = "SELECT kd_barcode FROM tbt_asset_barcode order by kd_barcode desc";
		$no = AssetBarcodeRecord::finder()->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$urut='00000001';
			$kd_barcode=$urut;
		}else{		
			$urut=intval($no->getColumnValue('kd_barcode'));
			$urut=$urut+1;
			$urut=substr('00000000',-8,8-strlen($urut)).$urut;
			$kd_barcode=$urut;
		}
		return $kd_barcode;
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
		$noTrans = $this->numCounter('tbt_asset_dist_brg',AssetDistBrgRecord::finder(),'30');
		$tujuan = $this->DDTujuan->SelectedValue;
		$pengirim = $this->DDpengirim->SelectedValue;
		$penerima = $this->DDpenerima->SelectedValue;
		
		$nmTable=$this->getViewState('nmTable');
		
		$sql="SELECT * FROM $nmTable ORDER BY id_barang";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$idBrg = $row['id_barang'];
			$jml = $row['jml'];
			
			//insert ke tbt_asset_dist_brg
			$newData= new AssetDistBrgRecord();
			$newData->no_trans = $noTrans;
			$newData->id_brg = $row['id_barang'];
			$newData->jml = $row['jml'];
			$newData->tgl = date('Y-m-d');
			$newData->wkt = date('H:m:s');		
			$newData->operator = $this->User->IsUserNip;			
			$newData->tujuan = $tujuan;
			$newData->pengirim = $pengirim;
			$newData->penerima = $penerima;
			$newData->ket = $row['ket'];				
			$newData->st= '0';
			
			$newData->Save();	
			
			//insert ke tbt_asset_stok_lokasi
			$newData= new AssetStokLokasiRecord();			
			$newData->id_brg = $row['id_barang'];
			$newData->jml = $row['jml'];						
			$newData->lokasi = $tujuan;
			
			$newData->Save();	
			
			//UPDATE tbt_asset_stok_brg => pengurangan stok untuk barang yg didistribusikan
			$sql="SELECT jml FROM tbt_asset_stok_brg WHERE id_brg='$idBrg'";
			$arrStok=$this->queryAction($sql,'S');
			
			if(count($arrStok) > 0) //UPDATE jika ada id_brg yg sama
			{
				foreach($arrStok as $rowStok)
				{
					$jmlAwal = $rowStok['jml'];
				}			
				
				//Update tbt_asset_stok_brg
				$jmlTotal = $jmlAwal - $jml;
				$sql="UPDATE 
						tbt_asset_stok_brg 
					SET 
						jml='$jmlTotal' 
					WHERE 
						 id_brg='$idBrg' ";	
			}
			
			$this->queryAction($sql,'C');	
			
			//insert ke tbt_asset_barcode			
			
			for($i=1; $i<=$row['jml']; $i++)
			{
				$kdBarcode=$this->makeBarcode();
				$dataBarcode=new AssetBarcodeRecord;			
				$dataBarcode->no_trans=$noTrans;
				$dataBarcode->kd_barcode=$kdBarcode;
				$dataBarcode->id_brg=$idBrg;
				$dataBarcode->tgl=date("Y-m-d");
				$dataBarcode->wkt=date('H:m:s');
				$dataBarcode->st='0';
				$dataBarcode->save();
			}
				
		}
		
		$this->queryAction($this->getViewState('nmTable'),'D');
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakDistribusiBrg',array('noTrans'=>$noTrans)));
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
