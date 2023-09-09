<?php
class TarifPendaftaran extends SimakConf
{   	
	private $sortExp = "id";
	private $sortDir = "ASC";
	private $offset = 0;
	private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{		
			//$this->cariNama->focus();	
			$sql="SELECT * FROM tbm_poliklinik ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDPoliklinik->DataSource=$arrData;	
			$this->DDPoliklinik->dataBind();
			
			$position='Bottom';		
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
						tbm_tarif_pendaftaran.id,
						tbm_tarif_pendaftaran.tarif,
						tbm_tarif_pendaftaran.shift,
						tbm_tarif_pendaftaran.id_klinik,
						(SELECT tbm_poliklinik.nama FROM tbm_poliklinik WHERE tbm_poliklinik.id = tbm_tarif_pendaftaran.id_klinik ORDER BY tbm_poliklinik.id DESC LIMIT 0,1) AS nm_poliklinik
					FROM tbm_tarif_pendaftaran WHERE id IS NOT NULL ";
					
			if($this->DDPoliklinik->SelectedValue != '')
			{
				$DDPoliklinik = $this->DDPoliklinik->SelectedValue;
				$sql .= "AND tbm_tarif_pendaftaran.id_klinik = '$DDPoliklinik' ";
			} 
			
			$shift = $this->shift->SelectedValue;
			$sql .= "AND tbm_tarif_pendaftaran.shift = '$shift' ";
				
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
		  //$nama = KamarOperasiNamaRecord::finder()->findByPk($ID)->nama;
		  
		  $sql = "DELETE FROM tbm_tarif_pendaftaran WHERE id = '$ID'";
		  $this->queryAction($sql,'C');
		  
		  $this->cariClicked();
		  
		  $this->getPage()->getClientScript()->registerEndScript
		 ('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Tarif Retribusi telah dihapus dari database.<br/><br/></p>\',timeout: 4000,dialog:{
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
			//$item->poliklinik->Text = $item->DataItem['nm_poliklinik'];
			$item->tarif->Text = number_format($item->DataItem['tarif'],'2',',','.');
			
			if($item->DataItem['shift']=='1')
				$item->shiftColumn->Text = 'Pagi';
			elseif($item->DataItem['shift']=='2')
				$item->shiftColumn->Text = 'Sore';	
			
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
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.TarifPendaftaranBaru'));		
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
