<?php

class IcdAdmin extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)        		
		{		
			$this->bindGrid();
			
			if($this->Request['nmTable'])
			{
				$this->setViewState('nmTable',$this->Request['nmTable']);		
				$this->bindGridIcd();
			}
			elseif($this->Request['noTrans']) //edit ICD
			{
				$noTrans = $this->Request['noTrans'];
				$sql="SELECT kode_icd FROM tbt_icd WHERE no_trans = '$noTrans' ";
				$arrData=$this->queryAction($sql,'S');
				
				if(count($arrData) > 0 )
				{
					foreach($arrData as $row)
					{
						$ID = $row['kode_icd'];
						if (!$this->getViewState('nmTable'))
						{
							$nmTable = $this->setNameTable('nmTable');
							$sql="CREATE TABLE $nmTable (
									id INT (2) auto_increment,									
									kode VARCHAR(50) DEFAULT NULL,
									st_tambahan CHAR(1) DEFAULT '1',
									PRIMARY KEY (id)) ENGINE = MEMORY";
							
							$this->queryAction($sql,'C');//Create new tabel bro...
						}
						else
						{
							$nmTable = $this->getViewState('nmTable');								
						}	
						
						$sql = "SELECT * FROM $nmTable WHERE kode = '$ID' ";
						$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
						
						if($arrData)
						{
							$this->getPage()->getClientScript()->registerEndScript
							('','unmaskContent();
								jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">ICD dengan KODE :<br/> <b>'.ucwords($ID).'</b><br/> sudah ditambahkan sebelumnya!</p>\',timeout: 4000,dialog:{
									modal: true
								}});');	
						}
						else
						{
							$sql="INSERT INTO $nmTable (kode,st_tambahan) VALUES ('$ID','0')";
							$this->queryAction($sql,'C');	
						}
					}
					
					$session = $this->getSession();
					$session->remove("SomeData"); 
					$this->UserGrid->CurrentPageIndex = 0;
					$this->bindGridIcd();
				}
			}
		}
		
		if($this->getViewState('nmTable'))
		{
			$this->simpanBtn->Enabled = true;	
			$this->icdPanel->Display = 'Dynamic';				
		}
		else
		{
			$this->simpanBtn->Enabled = false;	
			$this->icdPanel->Display = 'None';				
		}
	}
	
	private $sortExp = "kode";
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
						dtd, 
						kode, 
						indonesia, 
						inggris 
					FROM tbm_icd 
					WHERE kode <> ''	";
			
			if($this->nmIndo->Text != '')
			{
				$cariNmIndo = $this->nmIndo->Text;
				if($this->Advance->Checked === true){
					$sql .= "AND indonesia LIKE '%$cariNmIndo%' ";
				}
				else
				{	
					$sql .= "AND indonesia LIKE '$cariNmIndo%' ";
				}
			}	
			
			if($this->nmEng->Text != '')
			{
				$cariNmEng = $this->nmEng->Text;
				if($this->Advance->Checked === true){
					$sql .= "AND inggris LIKE '%$cariNmEng%' ";
				}
				else
				{	
					$sql .= "AND inggris LIKE '$cariNmEng%' ";
				}
			}	
			
			if($this->dtd->Text != '')
			{
				$dtd = $this->dtd->Text;
				if($this->AdvanceDtd->Checked === true){
					$sql .= "AND dtd LIKE '$dtd%' ";
				}else{
					$sql .= "AND dtd = '$dtd' ";
				}	
			}
				
			if($this->kat->Text != '')
			{
				$kat = $this->kat->Text;
				$sql .= "AND kat = '$kat' ";
			}
			
			if($this->kode->Text != '')
			{
				$kode = $this->kode->Text;
				if($this->AdvanceIcdKode->Checked === true){
					$sql .= "AND kode LIKE '$kode%' ";
				}else{
					$sql .= "AND kode = '$kode' ";
				}
			}
			
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->grid->DataSource = $data;
            $this->grid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 10)
			{
				//$this->pagerPanel->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				//$this->pagerPanel->Display = 'None';
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
			
			//$nmDpn = $item->DataItem['nama_dpn'];
			//$nmBlk = $item->DataItem['nama_blk'];
			
			//$item->telpColumn->telpTxt->Text = $tlp;
			//$item->telpColumn->hpTxt->Text = $hp;
			
			
		
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
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
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
		
		$sql = "DELETE FROM tbd_member WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGrid();
	}
	
	
	private function bindGridIcd()
    {
	    $this->pageSize = $this->UserGrid->PageSize;
        $this->offset = $this->pageSize * $this->UserGrid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = 'DESC';
            $someDataList->sortExpression = 'id';
			
			$nmTable = $this->getViewState('nmTable');	
			
			$sql="SELECT tbm_icd.kode, tbm_icd.indonesia AS nama FROM $nmTable INNER JOIN tbm_icd ON (tbm_icd.kode = $nmTable.kode) ";
		
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->UserGrid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->UserGrid->DataSource = $data;
            $this->UserGrid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 10)
			{
				//$this->pagerPanel->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				//$this->pagerPanel->Display = 'None';
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->UserGrid->DataSource = $session["SomeData"];
            $this->UserGrid->DataBind();
			
			//$this->clearViewState('sql');
        }
    }
	
	protected function changePage($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridIcd();
    }
	
	public function gridMasukanClicked($sender,$param)
    {
		$ID = $param->CommandParameter;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
					id INT (2) auto_increment,
					kode VARCHAR(50) DEFAULT NULL,
					st_tambahan CHAR(1) DEFAULT '1',
					PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');								
		}	
		
		$sql = "SELECT * FROM $nmTable WHERE kode = '$ID' ";
		$arrData = $this->queryAction($sql,'S');//Select row in tabel bro...
		
		if($arrData)
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">ICD dengan KODE :<br/> <b>'.ucwords($ID).'</b><br/> sudah ditambahkan sebelumnya!</p>\',timeout: 4000,dialog:{
					modal: true
				}});');	
		}
		else
		{
			$sql="INSERT INTO $nmTable (kode) VALUES ('$ID')";
			$this->queryAction($sql,'C');	
		}
		
		$session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = 0;
		$this->bindGridIcd();
    }	
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem' || $item->ItemType==='DeleteItem')
        {
			/*$cm = $this->UserGrid->DataKeys[$item->ItemIndex];
			
			$conTgl = $this->ConvertDate($item->DataItem['tgl_lahir'],'3');
			$item->tglColumn->Text = $conTgl;
			
			if($item->DataItem['jns_kelamin'] == '0')
				$item->jnsKelColumn->Text =  "Laki-Laki";
			else
				$item->jnsKelColumn->Text =  "Perempuan";	*/
        }
    }
    
	public function deleteClicked($sender,$param)
    {
       $item=$param->Item;				
		// obtains the primary key corresponding to the datagrid item
		$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		// deletes the user record with the specified username primary key				
		$nmTable = $this->getViewState('nmTable');
		
		$sql = "DELETE FROM $nmTable WHERE kode='$ID'";
		$this->queryAction($sql,'C');								
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert("'.$sql.'");');	
			
		$sql="SELECT tbm_icd.kode, tbm_icd.indonesia AS nama FROM $nmTable INNER JOIN tbm_icd ON (tbm_icd.kode = $nmTable.kode)ORDER BY $nmTable.id DESC";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$jmlData=0;
		foreach($arrData as $row)
		{
			$jmlData++;
		}
		
		$session = $this->getSession();
        $session->remove("SomeData"); 
		$this->UserGrid->CurrentPageIndex = 0;
		$this->bindGridIcd();
		
		if($jmlData==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
			$this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
			
			//$this->Page->CallbackClient->focus($this->namaAnak);
		}
    }	
	
	public function cariClicked($sender,$param)
	{			
		
		$session = $this->getSession();
        $session->remove("SomeData");        
        $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();
	}	
	
	protected function deleteRow($sender,$param)
    {	
		$item=$param->Item;
		
		$id = $sender->CommandParameter;
		//$nama = JpkPegawaiRecord::finder()->findByPk($id)->nama;
		IcdRecord::finder()->deleteByPk($id);
		
		//$offset = $this->dtgSomeData->pageSize * $this->dtgSomeData->CurrentPageIndex;		
		//$sql = $this->getViewState('sql')." LIMIT ".$offset.','.$this->dtgSomeData->pageSize;		
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$this->dtgSomeData->pageSize.' - '.$this->dtgSomeData->CurrentPageIndex.' - '.$tes.' telah dihapus dari database.\'); unmaskContent();');		
		
		$this->getPage()->getClientScript()->registerEndScript
				('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p>ICD dengan Kode : '.$id.' telah dihapus dari database.</p>\',timeout: 3000,dialog:{
					modal: true}});');	
		
       	$session = $this->getSession();
    	$session->remove("SomeData");
                
        $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();
    }
	
	public function editRow($sender,$param)
	{
		$id = $sender->CommandParameter;
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.IcdMasterEdit',array('ID'=>$id)));		
		//$url = "index.php?page=Admin.Formulir.JpkPegawaiEdit&id=".$id;
		//$this->getPage()->getClientScript()->registerEndScript('',"tesFrame('$url',jQuery(window).width()-100,jQuery(window).height()-50,'Edit Data Pegawai JPK')");
	}
	
	public function tambahClicked($sender,$param)
	{
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.IcdBaru'));	
	}
	
	public function simpanClicked()
	{
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Proses penambahan ICD ?</p>\',timeout: 6000000,dialog:{
				modal: true,
				buttons: {
					"Ya": function() {
						jQuery( this ).dialog( "close" );
						konfirmasi(\'ya\');
					},
					Tidak: function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'tidak\');
					}
				}
			}});');	
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$nmTable = $this->getViewState('nmTable');
			
			if($this->Request['noTrans']) //edit ICD
			{
				$noTrans = $this->Request['noTrans'];
				
				$sql="SELECT * FROM $nmTable WHERE st_tambahan = '1' ORDER BY id";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$data= new IcdPasienRecord();
					$data->no_trans = $noTrans;
					$data->kode_icd = $row['kode'];
					$data->Save();			
				}
				
				if($this->getViewState('nmTable'))
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
					$this->clearViewState('nmTable');//Clear the view state	
				}			
			}
					
			$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.modalTambahCallback("'.$nmTable.'"); jQuery.FrameDialog.closeDialog();');	
			
			//$this->Page->CallbackClient->focus($this->nama);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			//$this->Response->reload();
		}
		else
		{
			if($this->getViewState('nmTable'))
			{
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state	
			}
			
			$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.modalTambahCallback('.$id.'); jQuery.FrameDialog.closeDialog();');	
		}
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
			$this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalTambahCallback('.$id.'); jQuery.FrameDialog.closeDialog();');
		
		//$this->Response->reload();
		//$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.JpkPegawai'));		
	}
}
?>
