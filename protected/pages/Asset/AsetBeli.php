<?php
class AsetBeli extends SimakConf
{
	private $sortExp = "id";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('12');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
 	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			$this->noPO->focus();
			
			$sql = "SELECT id FROM tbt_aset_beli";		
			$data = AsetBeliRecord::finder()->findBySql($sql);
			if($data==NULL)
			{
				//$this->noPO->Text = '0001/ZHRH-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
				//$this->noPO->Text = 'RSUDPO-00001/'.$this->bulanRomawi(date('m')).'/010/MED/AP';
			}
			else
			{
				//$this->noPO->Text = $this->numUrut('tbt_aset_beli',AsetBeliRecord::finder(),'4').'/ZHRH-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
				//$this->noPO->Text = $this->noUrut().'/ZHRH-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
				//$this->noPO->Text = 'RSUDPO-'.$this->noUrut().'/'.$this->bulanRomawi(date('m')).'/010/MED/AP';
			}
			
			$sql = "SELECT id,nama FROM tbm_aset_supplier ORDER BY nama";
			$this->DDPbf->DataSource = AsetSupplierRecord::finder()->findAllBySql($sql);
			$this->DDPbf->dataBind();	
			
			$this->apotikPanel->Display = 'None';	
			$this->apotikPanel->Enabled = false;	
			
			$this->prosesPanel->Display = 'None';	
			$this->maxErrMsgPanel->Display = 'None';	
			$this->checkJmlMax->Display = 'None';		
			$this->cetakBtn->Enabled = false;	
						
			$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id <> '' ";
			$this->setViewState('sqlBarang',$sqlBarang);
			
			$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAllBySql($sqlBarang);
			$this->DDNamaBrg->dataBind(); 	
		}		
		
		if($this->getViewState('nmTable'))
		{
			$this->bindGrid();
		}
	}
	
	public function prosesCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function secondCallBack($sender,$param)
   	{
		$this->prosesPanel->render($param->getNewWriter());
	}
	
	public function noUrut()
    {			
		//Mbikin No Urut
		$find = AsetBeliRecord::finder();//::finder();		
		$sql = "SELECT no_po FROM tbt_aset_beli order by id desc";
		
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			$tmp='00001';
				
		}
		else
		{	
			$urut = substr($num->getColumnValue('no_po'),7,5) + 1;				
			$tmp=substr('00000',0,5-strlen($urut)).$urut;		
		}
		
		return $tmp;
	}
	
	
	public function jnsBeliChanged($sender,$param)
	{
		$this->nmApotik->Text = '';
		$this->DDPbf->SelectedValue = '';
			
		$jnsBeli = $this->jnsBeli->SelectedValue;
		
		if($jnsBeli == '0') //PBF
		{
			$this->nmSupplier->Text = 'Supplier';
			//$this->satuan->Text = '(satuan besar)';
			$this->satuan->Text = '(satuan kecil)';
			
			$this->supplierPanel->Display = 'Dynamic';	
			$this->supplierPanel->Enabled = true;	
			$this->DDPbf->Enabled = true;
			
			$this->apotikPanel->Display = 'None';	
			$this->apotikPanel->Enabled = false;	
			$this->nmApotik->Enabled = false;
		}
		else
		{
			$this->nmSupplier->Text = 'Nama Apotik';
			$this->satuan->Text = '(satuan kecil)';
			
			$this->apotikPanel->Display = 'Dynamic';	
			$this->apotikPanel->Enabled = true;	
			$this->nmApotik->Enabled = true;	
			
			$this->supplierPanel->Display = 'None';	
			$this->supplierPanel->Enabled = false;	
			$this->DDPbf->Enabled = false;
		}
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDJenis->SelectedValue=='')
		{
			
			$idJnsBrg = $this->DDJenis->SelectedValue;	
			$sql = "SELECT id,nama FROM tbm_barang_kelompok WHERE id_jenis_barang = '$idJnsBrg' ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			$this->DDKelompok->Enabled=true;
			
			$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id_jenis_barang = '$idJnsBrg'  ";
			$this->setViewState('sqlBarang',$sqlBarang);
			$sql = $sqlBarang." ORDER BY nama ";
		}
		else
		{
			$this->DDKelompok->DataSource=BarangKelompokRecord::finder()->findAll();
			$this->DDKelompok->dataBind();	
			$this->DDKelompok->Enabled=false;
			
			$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id <> '' ";
			$this->setViewState('sqlBarang',$sqlBarang);
			$sql = $sqlBarang." ORDER BY nama ";
			
		}
		
		$this->DDNamaBrg->DataSource = BarangRecord::finder()->findAllBySql($sql);
		$this->DDNamaBrg->dataBind(); 	
		$this->DDNamaBrg->Enabled=true;
		//$this->DDNamaBrg->focus();
		
		$this->DDKelompok->SelectedIndex = '';
		$this->nama->Text = '';
		$this->Page->CallbackClient->focus($this->DDKelompok);
		
		$this->maxErrMsgPanel->Display = 'None';
		$this->checkJmlMax->Display = 'None';
	}
	
	
	public function DDKelompokChanged($sender,$param)
	{		
		if($this->DDKelompok->SelectedValue != ''){
			$DDKelompok = $this->DDKelompok->SelectedValue;
			$idJnsBrg = $this->DDJenis->SelectedValue;	
			
			if($idJnsBrg && $DDKelompok)
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id_jenis_barang = '$idJnsBrg' AND id_kelompok_barang = '$DDKelompok' ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}
			elseif($idJnsBrg && !$DDKelompok)
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id_jenis_barang = '$idJnsBrg'  ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}
			elseif(!$idJnsBrg && $DDKelompok)
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id_kelompok_barang = '$DDKelompok'  ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}
			else
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id <> '' ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}
			
			//$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAll('kategori = ?', $this->DDJenis->SelectedValue);
			$this->DDNamaBrg->DataSource = BarangRecord::finder()->findAllBySql($sql);
			$this->DDNamaBrg->dataBind(); 	
			$this->DDNamaBrg->Enabled=true;
			//$this->DDNamaBrg->focus();
			
			$this->nama->Text = '';
			$this->nama->focus();
		}
		else
		{
			$this->clearViewState($sqlBarang);
			
			$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id <> '' ";
			$sql = $sqlBarang." ORDER BY nama ";
			
			$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAllBySql($sql);
			$this->DDNamaBrg->dataBind();
			$this->DDNamaBrg->Enabled=false;
			$this->clearViewState('minLoket');
			$this->clearViewState('maxLoket');
			$this->jml->Text = '';
			$this->DDKelompok->focus();
		}
		
		
		$this->maxErrMsgPanel->Display = 'None';
		$this->checkJmlMax->Display = 'None';
	} 
	
	public function DDJenisChanged($sender,$param)
	{		
		if($this->DDJenis->SelectedValue != ''){
			$idJnsBrg = $this->DDJenis->SelectedValue;
			
			if($idJnsBrg)
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id_jenis_barang = '$idJnsBrg'  ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}		
			else
			{
				$sqlBarang = "SELECT id, nama FROM tbm_barang WHERE id <> '' ";
				$this->setViewState('sqlBarang',$sqlBarang);
				$sql = $sqlBarang." ORDER BY nama ";
			}
			
			//$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAll('kategori = ?', $this->DDJenis->SelectedValue);
			$this->DDNamaBrg->DataSource = BarangRecord::finder()->findAllBySql($sql);
			$this->DDNamaBrg->dataBind(); 	
			$this->DDNamaBrg->Enabled=true;
			//$this->DDNamaBrg->focus();
			
			$this->nama->Text = '';
			$this->nama->focus();
		}
		else
		{
			$this->clearViewState($sqlBarang);
			
			$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAll();
			$this->DDNamaBrg->dataBind();
			$this->DDNamaBrg->Enabled=false;
			$this->clearViewState('minLoket');
			$this->clearViewState('maxLoket');
			$this->jml->Text = '';
			$this->DDJenis->focus();
		}
		
		
		$this->maxErrMsgPanel->Display = 'None';
		$this->checkJmlMax->Display = 'None';
	} 
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $sender->DataSource=$this->getDummyData($token);
        $sender->dataBind();                                                                                                     
    }
	
	public function getDummyData($token) 
	{
		if($this->getViewState('sqlBarang'))
		{
			$sql = $this->getViewState('sqlBarang');
			$sql .= " AND tbm_barang.nama LIKE '$token%' "; 
			
			if($this->DDJenis->SelectedValue != '')
			{
				$id_jenis_barang = $this->DDJenis->SelectedValue;	
				$sql .= " AND tbm_barang.id_jenis_barang = '$id_jenis_barang' "; 
			}
			
			if($this->DDKelompok->SelectedValue != '')
			{
				$id_kelompok_barang = $this->DDKelompok->SelectedValue;	
				$sql .= " AND tbm_barang.id_kelompok_barang = '$id_kelompok_barang' "; 
			}
			
			$sql .= " GROUP BY tbm_barang.id ORDER BY tbm_barang.nama "; 
			$arr = $this->queryAction($sql,'S');
		}
		else
		{
			$arr = '';
		}
		
		return $arr;
    }
	
	public function suggestionSelected1($sender,$param) 
	{
        $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
		
		if($id)
			$this->DDNamaBrg->SelectedValue = $id;	
		else
			$this->DDNamaBrg->SelectedValue = 'empty';
		
		$this->DDNamaBrgChanged();
    }
	
	public function DDNamaBrgChanged()
	{
		$idBarang = $this->DDNamaBrg->SelectedValue;
		$this->clearViewState('minLoket');
		$this->clearViewState('maxLoket');
			
		if($this->DDNamaBrg->SelectedValue != '')
		{
			//$minLoket = BarangRecord::finder()->findByPk($idBarang)->min_2;
			//$maxLoket = BarangRecord::finder()->findByPk($idBarang)->max_2;
							
			$jnsBeli = $this->jnsBeli->SelectedValue;
			/*if($jnsBeli == '0') //PBF
			{
				$jmlSatBesar = BarangRecord::finder()->findByPk($idBarang)->jml_satuan_besar;				
				$maxLoketHitung = floor($maxLoket / $jmlSatBesar);
			}
			else
			{*/
				$maxLoketHitung = $maxLoket;
			//}
			
			
			if(($minLoket!=0 ) && ($maxLoket!=0 ))
			{
				$this->setViewState('minLoket',$minLoket);
				$this->setViewState('maxLoket',$maxLoket);
				
				$sql = "SELECT sum(jumlah) AS jumlah FROM tbt_stok_aset WHERE id_barang='$idBarang' AND tujuan='1' ";
				//$jmlStokLain = StokAsetRecord::finder()->find('id_barang = ? AND tujuan = ?',$idBarang,'2')->jumlah;
				$jmlStokLain = StokAsetRecord::finder()->findBySql($sql)->jumlah;
				$this->jml->Text = $maxLoketHitung - ceil($jmlStokLain / $jmlSatBesar);
			}
			else
			{
				$this->jml->Text = '';
			}
		}
		else{
			$this->jml->Text = '';
		}
		
		$this->Page->CallbackClient->focus($this->jml);
	} 
	
	
	public function prosesLock()
	{	
		$this->prosesPanel->Display = 'Dynamic';
		$this->firstPanel->Enabled=false;
		
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->nama->getClientID().'.focus();');
	} 
	
	public function prosesUnlock()
	{	
		$this->Response->Reload();
	} 
	
	public function prosesClicked()
	{	
		if($this->IsValid)  // when all validations succeed
        { 
			$noPO = trim($this->noPO->Text);
			$sql = "SELECT no_po FROM tbt_aset_beli WHERE no_po = '$noPO' AND st_rkbu_rtbu = '0' ";
			if($this->queryAction($sql,'S'))
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">No. Kontrak RKBU/RKPBU:<br/><b>'.$noPO.'</b><br/>sudah ada dalam database!</p>\',timeout: 3000,dialog:{
						modal: true
					}});');		
			}
			else
			{
				$this->prosesLock();
			}
			
		}
	} 
	
	public function checkJmlMax($sender,$param)
    {   
		if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
		{
			$idBarang = $this->DDNamaBrg->SelectedValue;
			$minLoket=$this->getViewState('minLoket');
			$maxLoket=$this->getViewState('maxLoket');
			
			if(($minLoket!=0 ) && ($maxLoket!=0 ))
			{
				$sql = "SELECT sum(jumlah) AS jumlah FROM tbt_stok_aset WHERE id_barang='$idBarang' AND tujuan='1' ";
				//$jmlStokLain = StokAsetRecord::finder()->find('id_barang = ? AND tujuan = ?',$idBarang,'2')->jumlah;
				$jmlStokLain = StokAsetRecord::finder()->findBySql($sql)->jumlah;
				
				$jnsBeli = $this->jnsBeli->SelectedValue;
				/*if($jnsBeli == '0') //PBF
				{
					$jmlSatBesar = BarangRecord::finder()->findByPk($idBarang)->jml_satuan_besar;
					$jml = $jmlSatBesar * $this->jml->Text;
				}
				else
				{*/
					$jml = $this->jml->Text;
				//}
				
				
				//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
				$jmlTotal = $jml + $jmlStokLain;
				
				// valid if the id kabupaten is not found in the database
				$param->IsValid=($jmlTotal <= $maxLoket);
				
			}
		}
		
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
        $this->pageSize = $this->UserGrid->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT * FROM $nmTable  ";
			
			//$sql .= " GROUP BY cm ";  
			
			$this->setViewState('sql',$sql);
			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->UserGrid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->UserGrid->DataSource = $data;
            $this->UserGrid->DataBind();
	    	//$this->showSql->Text=$sql;
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
        }
        else 
		{
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid->DataSource = $session["SomeData"];
            $this->UserGrid->DataBind();
        }
    }
	
	 // ---------- datagrid page and sort events ---------------
    
    protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid->CurrentPageIndex = $param->NewPageIndex;
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
		
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->UserGrid->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	
	public function makeTblTemp()
	{
		$this->maxErrMsgPanel->Display = 'None';
		$this->cetakBtn->Enabled = true;
		$jnsBeli = $this->jnsBeli->SelectedValue;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
					  kode varchar(5) NOT NULL,
					  jml int(11) NOT NULL,
					  jml_kecil int(11) NOT NULL,
					  PRIMARY KEY (id)) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			$kode = $this->DDNamaBrg->SelectedValue;
			$nama =$this->ambilTxt($this->DDNamaBrg);
			
			/*if($jnsBeli == '0') //PBF
			{
				$jml = $this->jml->Text;
				$jmlSatBesar = BarangRecord::finder()->findByPk($kode)->jml_satuan_besar;
				
				$jmlKecil = $jmlSatBesar * $jml;
			}
			else
			{*/
				//$jml = 0;				
				$jmlKecil = $this->jml->Text;
				$jmlSatBesar = BarangRecord::finder()->findByPk($kode)->jml_satuan_besar;
				$jml = $jmlKecil / $jmlSatBesar;
			//}
			
			$sql="INSERT INTO $nmTable (kode,jml,jml_kecil) VALUES ('$kode','$jml','$jmlKecil')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$this->bindGrid();
		}
		else
		{
			$kode = $this->DDNamaBrg->SelectedValue;
			
			$nmTable = $this->getViewState('nmTable');
			
			/*if($jnsBeli == '0') //PBF
			{
				$jml = $this->jml->Text;
				$jmlSatBesar = BarangRecord::finder()->findByPk($kode)->jml_satuan_besar;
				
				$jmlKecil = $jmlSatBesar * $jml;
			}
			else
			{*/
				//$jml = 0;
				$jmlKecil = $this->jml->Text;
				$jmlSatBesar = BarangRecord::finder()->findByPk($kode)->jml_satuan_besar;
				$jml = $jmlKecil / $jmlSatBesar;
			//}
			
			$sql="SELECT * FROM $nmTable WHERE kode='$kode'";
			$arr=$this->queryAction($sql,'S');
			$jmlData=0;
			
			foreach($arr as $row)
			{
				//$jmlAwal = $row['jml'];
				$jmlAwalKecil = $row['jml_kecil'];
				$jmlAwalBesar = $row['jml'];
				$jmlData++;
			}
			
			if($jmlData > 0)
			{
				if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
				{
					$idBarang = $this->DDNamaBrg->SelectedValue;
					$minLoket=$this->getViewState('minLoket');
					$maxLoket=$this->getViewState('maxLoket');
					
					if(($minLoket!=0 ) && ($maxLoket!=0 ))
					{
						$sql = "SELECT sum(jumlah) AS jumlah FROM tbt_stok_aset WHERE id_barang='$idBarang' AND tujuan='1' ";
						//$jmlStokLain = StokAsetRecord::finder()->find('id_barang = ? AND tujuan = ?',$idBarang,'2')->jumlah;
						$jmlStokLain = StokAsetRecord::finder()->findBySql($sql)->jumlah;
						
						//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
						$jmlTotalStok = $jml + $jmlStokLain + $jmlAwal;
						if( $jmlTotalStok <= $maxLoket)
						{
							$jmlTot = $jmlAwalBesar + $jml;
							$jmlKecilTot = $jmlAwalKecil + $jmlKecil;
							$sql="UPDATE $nmTable SET jml='$jmlTot',jml_kecil='$jmlKecilTot' WHERE kode='$kode' ";
							$this->queryAction($sql,'C');//Insert new row in tabel bro...
						}
						else
						{
							$this->DDJenis->focus();
							$this->maxErrMsgPanel->Display = 'Dynamic';	}
					}
				}
				else
				{
					$jmlTot = $jmlAwalBesar + $jml;
					$jmlKecilTot = $jmlAwalKecil + $jmlKecil;
					$sql="UPDATE $nmTable SET jml='$jmlTot',jml_kecil='$jmlKecilTot' WHERE kode='$kode' ";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
			}
			else
			{
				$sql="INSERT INTO $nmTable (kode,jml,jml_kecil) VALUES ('$kode','$jml','$jmlKecil')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$this->bindGrid();
		}
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	public function changePage($sender,$param)
	{				
		$limit=$this->UserGrid->PageSize;		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		$this->bindGrid();
	} 
	
	public function sortGrid($sender,$param)
	{		
		$item = $param->SortExpression;
		
		$nmTable = $this->getViewState('nmTable');
		$this->bindGrid();
	}
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {	
			//$item->tarif->Text = number_format($item->DataItem['tarif_operator'],'2',',','.');
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');								
				
				//$session = $this->getSession();
	    	   // $session->remove("SomeData");
    	    	//$this->UserGrid->CurrentPageIndex = -1;
				
				$this->bindGrid();
				
				$this->DDJenis->focus();
				
				$sql = "SELECT * FROM $nmTable ";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) == 0)
				{
					$this->cetakBtn->Enabled = false;
					if($this->getViewState('nmTable'))
					{
						$this->queryAction($this->getViewState('nmTable'),'D');
						$this->clearViewState('nmTable');
					}
				}
				$this->Page->CallbackClient->focus($this->nama);
				$this->maxErrMsgPanel->Display = 'None';
			//}	
			
		//}	
    }	
	
	public function refreshProsesCtrl()
	{
		//$this->DDJenis->SelectedIndex =-1;
		//$this->DDNamaBrg->DataSource=BarangRecord::finder()->findAll();
		//$this->DDNamaBrg->dataBind();
		$this->DDNamaBrg->Enabled=false;
		$this->DDNamaBrg->SelectedValue='empty';
		
		$this->clearViewState('minLoket');
		$this->clearViewState('maxLoket');
		$this->jml->Text = '';
		$this->nama->Text = '';
		$this->Page->CallbackClient->focus($this->nama);
		
		if($this->getViewState('nmTable'))
		{
			$session = $this->getSession();
			$session->remove("SomeData");
			$this->UserGrid->CurrentPageIndex = -1;
			$this->bindGrid();
		}
	}
						
	public function tambahClicked()
	{	
		$this->checkJmlMax->Display = 'None';
		
		if($this->IsValid)  // when all validations succeed
        { 
			if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
			{
				$idBarang = $this->DDNamaBrg->SelectedValue;
				$minLoket=$this->getViewState('minLoket');
				$maxLoket=$this->getViewState('maxLoket');
				
				if(($minLoket!=0 ) && ($maxLoket!=0 ))
				{
					$sql = "SELECT sum(jumlah) AS jumlah FROM tbt_stok_aset WHERE id_barang='$idBarang' AND tujuan='1' ";
					//$jmlStokLain = StokAsetRecord::finder()->find('id_barang = ? AND tujuan = ?',$idBarang,'2')->jumlah;
					$jmlStokLain = StokAsetRecord::finder()->findBySql($sql)->jumlah;
					
					$jnsBeli = $this->jnsBeli->SelectedValue;
					/*if($jnsBeli == '0') //PBF
					{
						$jmlSatBesar = BarangRecord::finder()->findByPk($idBarang)->jml_satuan_besar;
						$jml = $jmlSatBesar * $this->jml->Text;
					}
					else
					{*/
						$jml = $this->jml->Text;
					//}
				
					//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
					$jmlTotal = $jml + $jmlStokLain;
					if( $jmlTotal <= $maxLoket)
					{
						$this->makeTblTemp();
						$this->refreshProsesCtrl();
						
						$this->checkJmlMax->Display='None';	
						
						//$this->checkJmlMax->Display='Dynamic';	
						//$this->checkJmlMax->Text = $jmlTotal.' '.$maxLoket;	
					}
					else
					{
						//$this->jml->focus();
						$this->checkJmlMax->Display='Dynamic';	
						//$this->checkJmlMax->Text = $jmlTotal.' '.$maxLoket;	
						$this->refreshProsesCtrl();
					}
				}
				else
				{
					$this->makeTblTemp();
					$this->refreshProsesCtrl();
				}
			}
			else
			{
				$this->makeTblTemp();
				$this->refreshProsesCtrl();
			}
		//*/
		}
		
		$this->DDJenis->focus();
	} 
	
	public function batalClicked()
	{
		$this->prosesUnlock();
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
			$this->clearViewState('nmTable');
		}
	} 
		
	public function cetakClicked($sender,$param)
	{		
		$noPO=$this->noPO->Text;
		$tgl=$this->convertDate($this->tglPO->Text,'2');
		
		$wkt=date('G:i:s');
		$catatan=$this->catatan->Text;
		$operator=$this->User->IsUserNip;
		$nmTable=$this->getViewState('nmTable');
		
		$jnsBayar = $this->jnsBayar->SelectedValue;
		
		$jnsBeli = $this->jnsBeli->SelectedValue;
		
		if($jnsBeli == '0') //PBF
		{
			$pbf=$this->DDPbf->SelectedValue;
		}
		else
		{
			$nmApotik = $this->nmApotik->Text;
		}
		
		$sql="SELECT * FROM $nmTable ORDER BY id DESC";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$newData= new AsetBeliRecord();
			$newData->no_po=$noPO;
			$newData->tgl_po=$tgl;
			$newData->waktu=$wkt;
			$newData->pbf=$pbf;
			$newData->kode=$row['kode'];
			$newData->jumlah=$row['jml'];
			$newData->jumlah_kecil=$row['jml_kecil'];
			$newData->catatan=$catatan;
			$newData->petugas=$operator;
			$newData->flag='0';
			$newData->nm_apotik_luar=$nmApotik;
			$newData->st_pembelian=$jnsBeli;
			$newData->st_rkbu_rtbu='0';
			$newData->Save();			
		}
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
			$this->clearViewState('nmTable');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakAsetBeli',array('noPO'=>$noPO,'tgl'=>$tgl,'jnsBayar'=>$jnsBayar,'jnsBeli'=>$jnsBeli,'pbf'=>$pbf,'nmApotik'=>$nmApotik,'stRkbu'=>'0')));
       			
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