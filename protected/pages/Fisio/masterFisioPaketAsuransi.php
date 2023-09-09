<?php
class masterFisioPaketAsuransi extends SimakConf
{   	
	private $sortExp = "nm_perus ASC, nm_tindakan ";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack)
		{		
			$this->cariNama->focus();		
			
			$stRawat = $this->stRawat->SelectedValue;
			$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE st = '$stRawat' ORDER BY nama ";
			$this->DDKamar->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDKamar->dataBind();
			
			$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama ";
			$this->DDKelas->DataSource = KamarKelasRecord::finder()->findAllBySql($sql);
			$this->DDKelas->dataBind();
			
			$sql = "SELECT * FROM tbm_operasi_kategori ORDER BY nama ";
			$this->DDKateg->DataSource = OperasiKategoriRecord::finder()->findAllBySql($sql);
			$this->DDKateg->dataBind();
			
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			$this->bindGrid();
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
					  tbm_fisio_tindakan_paket_asuransi.id,
					  tbm_fisio_tindakan_paket_asuransi.nama AS nm_tindakan,
					  tbm_fisio_tindakan_paket_asuransi.id_asuransi,
					  tbm_perusahaan_asuransi.nama AS nm_perus,
					  tbm_fisio_tindakan_paket_asuransi.tarif,
					  tbm_fisio_tindakan_paket_asuransi.st_rawat
					FROM
					  tbm_fisio_tindakan_paket_asuransi
					  INNER JOIN tbm_perusahaan_asuransi ON (tbm_fisio_tindakan_paket_asuransi.id_asuransi = tbm_perusahaan_asuransi.id)					
					WHERE tbm_fisio_tindakan_paket_asuransi.id <> ''  ";
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbm_fisio_tindakan_paket_asuransi.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbm_fisio_tindakan_paket_asuransi.nama LIKE '$nama%' ";
				}
			}
			
			if($this->stRawat->SelectedValue != '')
			{
				$stRawat = $this->stRawat->SelectedValue;
				$sql .= "AND tbm_fisio_tindakan_paket_asuransi.st_rawat = '$stRawat' ";
			} 
			
			if($this->ID->Text != '')
			{
				$ID = $this->ID->Text;
				$sql .= "AND tbm_operasi_kamar_tarif.id_kamar_operasi = '$ID' ";
			}
			/*
			if($this->DDKamar->SelectedValue != '')
			{
				$DDKamar = $this->DDKamar->SelectedValue;
				$sql .= "AND tbm_operasi_kategori.id_kelompok = '$DDKamar' ";
			} 
			
			if($this->DDKelas->SelectedValue != '')
			{
				$DDKelas = $this->DDKelas->SelectedValue;
				$sql .= "AND tbm_operasi_kamar_tarif.id_kelas = '$DDKelas' ";
			} 
			
			if($this->DDKateg->SelectedValue != '')
			{
				$DDKateg = $this->DDKateg->SelectedValue;
				$sql .= "AND tbm_operasi_kamar_tarif.id_kategori_operasi = '$DDKateg' ";

			} 
			
			
			$sql .= " GROUP BY
					  tbm_operasi_kategori.id,
					  tbm_kamar_kelas.id ";  */
			
			$this->setViewState('sql',$sql);
			//$this->showSql->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
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
        else 
		{
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
		
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
    public function deleteButtonClicked($sender,$param)
	{
	  if ($this->User->IsAdmin)
		{
		  // obtains the datagrid item that contains the clicked delete button
		  $item=$param->Item;
		  // obtains the primary key corresponding to the datagrid item
		  $ID = $this->dtgSomeData->DataKeys[$item->ItemIndex];
		  
		  //$idKelas = KamarOperasiTarifRecord::finder()->findByPk($ID)->id_kelas;
		 // $idKateg = KamarOperasiTarifRecord::finder()->findByPk($ID)->id_kategori_operasi;
		  //$nama = KamarOperasiNamaRecord::finder()->findByPk($ID)->nama;
		  
		  $sql = "DELETE FROM tbm_fisio_tindakan_paket_asuransi WHERE id='$ID' ";
		  $this->queryAction($sql,'C');
		  
		  $sql = "SELECT id FROM tbm_fisio_tindakan_paket_asuransi_detail WHERE id_paket='$ID'";
		  if(count($this->queryAction($sql,'S')) == 0);
		  {
				$sql = "DELETE FROM tbm_fisio_tindakan_paket_asuransi_detail WHERE id_paket='$ID' ";
				$this->queryAction($sql,'C');			  
		  }
		  
		  $this->getPage()->getClientScript()->registerEndScript
		 ('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Paket Tindakan Lab telah dihapus dari Data Master Tindakan Paket Asuransi Fisio .</p>\',timeout: 3000,dialog:{
				modal: true
			}});');	
		  
		  $this->cariClicked();
		  //$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariData'));
		}	
	}	
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {	
			$item->tarif->Text = number_format($item->DataItem['tarif'],'2',',','.');
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	public function editRow($sender,$param)
    {
        $ID = $sender->CommandParameter;
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioPaketAsuransiEdit',array('pageParent'=>$this->Page->getPagePath(),'ID'=>$ID)));	
    }	
	
	public function tarifBtnClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioPaketAsuransiBaru'));		
	}
	
	public function cariClicked()
	{		
		$stRawat = $this->stRawat->SelectedValue;
		$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE st = '$stRawat' ORDER BY nama ";
		$this->DDKamar->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
		$this->DDKamar->dataBind();
		
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();	
	}
	
}
?>