<?php
class AdminUser extends SimakConf
{   
	private $sortExp = "real_name";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
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
					  tbd_user.username,
					  tbd_user.real_name,
					  tbd_user.no_hp,
					  tbd_user.nip,
					  tbd_user.role,
					  tbd_user.allow,
					  tbd_user.`status`,
					  tbd_user.tgl_create,
					  tbd_user.wkt_create,
					  tbd_user.tgl_log,
					  tbd_user.wkt_log,
					  tbd_user.catatan,
					  tbd_user.theme,
					  tbm_role.nama AS nm_role,
					  IF(tbd_user.status=0,'BlackList','Aktif') AS st_user
					FROM
					  tbd_user
					  INNER JOIN tbm_role ON (tbd_user.role = tbm_role.id) 
					 WHERE tbd_user.username <> ''  ";
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true)
					$sql .= "AND tbd_user.real_name LIKE '%$nama%' ";
				else
					$sql .= "AND tbd_user.real_name LIKE '$nama%' ";
			}
			
			if($this->cariUsername->Text != '')
			{
				$cariUsername = $this->cariUsername->Text;		
				if($this->Advance2->Checked === true)
					$sql .= "AND tbd_user.username LIKE '%$cariUsername%' ";
				else
					$sql .= "AND tbd_user.username LIKE '$cariUsername%' ";
			}
			
			if($this->DDRole->SelectedValue != '')
			{
				$DDRole = $this->DDRole->SelectedValue;		
				$sql .= "AND tbd_user.role = '$DDRole' ";
			}
			
			if($this->DDAllow->SelectedValue != '')
			{
				$DDAllow = $this->DDAllow->SelectedValue;		
				$sql .= "AND tbd_user.allow = '$DDAllow' ";
			}
			
			/*
			if($this->TLBAllow->SelectedValue != '')
			{
				$TLBAllow = $this->collectSelectionResult($this->TLBAllow);
				$this->showSql->Text=$TLBAllow;
				//$matches = implode(',', '1,2,3,4');	
				$sql .= "AND tbd_user.allow IN ('$TLBAllow') ";
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
		  $nama = UserRecord::finder()->findByPk($ID)->real_name;
		  
		  $sql = "DELETE FROM tbd_user WHERE username = '$ID'";
		  $this->queryAction($sql,'C');
		  
		  $this->cariClicked();
		  
		  $this->getPage()->getClientScript()->registerEndScript
		 ('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">User <b>'.$nama.'</b> telah dihapus dari database.<br/><br/></p>\',timeout: 4000,dialog:{
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
			/*
			$role = $item->DataItem['role'];
			
			if($role == '0')
				$item->role->Text = '';
			elseif($role == '1')
				$item->role->Text = '';
			elseif($role == '2')
				$item->role->Text = 'Super User';	
			*/
			
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);			
		
		if(!$this->IsPostBack)
		{			
			$this->cariUsername->focus();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDRole->DataSource = RoleRecord::finder()->findAll($criteria);
			$this->DDRole->dataBind();
			
			$this->DDAllow->DataSource = AllowRecord::finder()->findAll($criteria);
			$this->DDAllow->dataBind();
			
			$this->TLBAllow->DataSource = AllowRecord::finder()->findAll($criteria);
			$this->TLBAllow->dataBind(); 	
							
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			$this->bindGrid();	
		}		
    }		
	
	public function tes()
	{
		$this->cariUsername->Text = $this->collectSelectionResult($this->TLBAllow);
	}
	
	public function cariClicked()
	{		
		$session = $this->getSession();
        $session->remove("SomeData");
        $this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.NewUser'));		
	}
}
?>
