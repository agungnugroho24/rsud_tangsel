<?php
class masterLab extends SimakConf
{   	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{		
			$this->cariNama->focus();		
			
			$sql = "SELECT * FROM tbm_lab_kelompok WHERE kode <> '3' ";
			$this->DDLabKel->DataSource = LabKelRecord::finder()->findAllBySql($sql);
			$this->DDLabKel->dataBind();
			
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
					  tbm_lab_tindakan.kode,
					  tbm_lab_tindakan.nama,
					  tbm_lab_tindakan.normal,
					  tbm_lab_tindakan.normal_perempuan,
					  tbm_lab_tarif.tarif AS tarif_umum,
					  tbm_lab_tarif.tarif1 AS tarif_vip,
					  tbm_lab_tarif.tarif2 AS tarif_umum_cito,
					  tbm_lab_tarif.tarif3 AS tarif_vip_cito
					FROM
					  tbm_lab_tindakan
					  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
					  WHERE tbm_lab_tindakan.kode <> ''  ";
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbm_lab_tindakan.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbm_lab_tindakan.nama LIKE '$nama%' ";
				}
			}
			
			if($this->DDLabKel->SelectedValue != '')
			{
				if($this->DDLabKel->SelectedValue > 1)
					$sql .= " AND st_paket > 0 ";
				else	
					$sql .= " AND st_paket = 0 ";
			}	
			
			if($this->DDRujukan->SelectedValue != '')
			{
				$DDRujukan = $this->DDRujukan->SelectedValue;
				$sql .= " AND st_rujukan = '$DDRujukan ' ";
			}
			
			if($this->ID->Text != '')
			{
				$ID = $this->ID->Text;
				//$sql .= "AND tbm_operasi_kamar_tarif.id_kamar_operasi = '$ID' ";
			}
			
			
			//$sql .= " GROUP BY cm ";  
			
			$this->setViewState('sql',$sql);
			 
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
		  $nama = LabTdkRecord::finder()->findByPk($ID)->nama;
		  $stPaket = LabTdkRecord::finder()->findByPk($ID)->st_paket;
		  
		  $sql = "DELETE FROM tbm_lab_tindakan WHERE kode = '$ID'";
		  $this->queryAction($sql,'C');
		  
		  //DELETE tbm_lab_tarif
		   $sql = "DELETE FROM tbm_lab_tarif WHERE id = '$ID'";
		  $this->queryAction($sql,'C');
		  
		  //jika tindakan paket
		  if($stPaket > 0)
		  {
		  		$sql = "DELETE FROM tbm_lab_paket WHERE id_paket = '$stPaket'";
			  	$this->queryAction($sql,'C');
		  }
		  
		  $this->cariClicked();
		  
		  $this->getPage()->getClientScript()->registerEndScript
		 ('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tindakan Lab. <b>'.$nama.'</b> telah dihapus dari database.<br/><br/></p>\',timeout: 4000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery(this).dialog( "close" );
					}
				}
			}});');	
		  
		  //$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariData'));
		}	
	}	
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {	
			$item->tarif1->Text = number_format($item->DataItem['tarif_umum'],'2',',','.');
			$item->tarif2->Text = number_format($item->DataItem['tarif_vip'],'2',',','.');
			$item->tarif3->Text = number_format($item->DataItem['tarif_umum_cito'],'2',',','.');
			$item->tarif4->Text = number_format($item->DataItem['tarif_vip_cito'],'2',',','.');
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	
	public function tarifBtnClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLab'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLabBaru'));		
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
