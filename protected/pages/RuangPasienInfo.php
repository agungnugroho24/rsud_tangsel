<?php
class RuangPasienInfo extends SimakConf
{   
	private $sortExp = "nm_kelas, nm_ruang";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{		
			/*
			$sql = "SELECT * FROM tbm_kamar_kelas ORDER BY nama ";
			$this->DDKelas->DataSource = KamarKelasRecord::finder()->findAllBySql($sql);
			$this->DDKelas->dataBind();
			*/
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
						tbm_ruang.id,
						tbm_ruang.nama AS nm_ruang,
						tbm_ruang.jml_bed AS jml_bed,
						tbm_ruang.jml_bed_pakai AS jml_bed_pakai,
						tbm_kamar_kelas.nama AS nm_kelas,
						tbm_kamar_nama.nama AS nm_kamar
					FROM 
						tbm_ruang 
						INNER JOIN tbm_kamar_kelas ON (tbm_kamar_kelas.id = tbm_ruang.id_kelas)
						INNER JOIN tbm_kamar_nama ON (tbm_kamar_nama.id = tbm_ruang.id_jns_kamar)
					WHERE tbm_ruang.id <> ''  ";
			
			/*
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbm_ruang.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbm_ruang.nama LIKE '$nama%' ";
				}
			}
			
			if($this->DDKelas->SelectedValue != '')
			{
				$DDKelas = $this->DDKelas->SelectedValue;
				$sql .= "AND tbm_ruang.id_kelas = '$DDKelas' ";
			}
			
			if($this->ID->Text != '')
			{
				$ID = $this->ID->Text;
				$sql .= "AND tbm_ruang.id = '$ID' ";
			}
			*/
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
		  $nama = RuangRecord::finder()->findByPk($ID)->nama;
		  
		  $sql = "DELETE FROM tbm_ruang WHERE id = '$ID'";
		  $this->queryAction($sql,'C');
		  
		  $this->cariClicked();
		  
		  $this->getPage()->getClientScript()->registerEndScript
		 ('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Ruang dengan nama <b>'.$nama.'</b> telah dihapus dari database.<br/><br/></p>\',timeout: 4000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
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
			$item->ruang->Text = $item->DataItem['nm_ruang'];	
			$item->kelas->Text = $item->DataItem['nm_kelas'];
			$item->jmlBed->Text = $item->DataItem['jml_bed'];
			$item->jmlBedPakai->Text = $item->DataItem['jml_bed_pakai'];
			$item->jmlTersedia->Text = $item->DataItem['jml_bed'] - $item->DataItem['jml_bed_pakai'];
			
			if($item->DataItem['jml_bed_pakai'] >= $item->DataItem['jml_bed'])
				$item->status->Text = '<div class="f_status label label-important block f24" style="padding-top:20px;padding-bottom:20px;text-align:center;">Penuh</div>';
			else
				$item->status->Text = '<div class="f_status label label-success block f24" style="padding-top:20px;padding-bottom:20px;text-align:center;">Tersedia</div>';
				
			//$item->persentase->Text = number_format($item->DataItem['persentase'],'2',',','.').' %';
			
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
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.AdminFormulir'));		
	}
		
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.RuangPasienBaru'));		
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
