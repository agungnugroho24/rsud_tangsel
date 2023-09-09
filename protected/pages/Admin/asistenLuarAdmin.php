<?php
class asistenLuarAdmin extends SimakConf
{  	
	private $sortExp = "id";
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
	
    // get data and bind it to datagrid
    private function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
		
		if($this->getViewState('offsetEdit')!='')
		{
			$this->dtgSomeData->CurrentPageIndex = $this->getViewState('offsetEdit') - 1;
			$this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;		
			
			$this->clearViewState('offsetEdit');
		}
		else
		{
			 $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
		}  
		
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();           
			$someDataList->offset = $this->offset;		
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$cariID = $this->getViewState('cariByID');
			$cariNama = $this->getViewState('cariByNama');	
			
            $sql = "SELECT 
					  id,
					  nama,
					  alamat,
					  telpon,
					  keterangan
					FROM
					  tbd_asisten_luar
					WHERE							
					  id <> 0 ";
			
			if($cariNama <> '')	
			{
				if($this->getViewState('elemenBy') === true){
						$sql .= "AND tbd_asisten_luar.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND tbd_asisten_luar.nama LIKE '$cariNama%' ";
					}		
			}			
			
			if($cariID <> '')	
			{
				$sql .= "AND tbd_asisten_luar.id = '$cariID' ";
			}
		    			 
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
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
	
	public function viewChanged($sender,$param)
	{
		if($this->MultiView->ActiveViewIndex===1)
        {
            //$this->simpanNewBtn->Focus();
        }
	}
	/*
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }*/
	 
	public function onLoad($param)
	{				
		parent::onLoad($param);		
		//$mode=$this->getViewState('baru');
		
		if(!$this->IsPostBack)
		{				
			$session=$this->Application->getModule('session');
			if($session['offsetEdit'])
			{
				$offsetEdit = ceil($session['offsetEdit']/10);
				$this->setViewState('offsetEdit',$offsetEdit);
				//$this->cariNama->Text = ceil($session['offsetEdit']/10);
				$session->remove('offsetEdit');
			}
			$this->bindGrid();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			//$asistenRecord=$this->AsistenRecord;
			
			$id=$this->Request['id'];
			if($id===NULL)
			{
				$this->MultiView->ActiveViewIndex='0';
			}elseif($id==='')
			{
				$this->MultiView->ActiveViewIndex='0';
			}elseif ($id=='0')
			{
				$this->Response->redirect($this->Service->constructUrl('Admin.asistenLuarAdmin'));
			}else
			{	
				$this->clearViewState('baru');	
				$this->MultiView->ActiveViewIndex='1';
				$this->ased->visible=true;
				$this->setViewState('baru','2');	
				
				$asistenRecord=$this->AsistenRecord;
				$this->IdAsisten->Text=$asistenRecord->id;
				$this->Nama->Text=$asistenRecord->nama;
				$this->Alm->Text=$asistenRecord->alamat;
				$this->Tlp->Text=$asistenRecord->telpon;
				$this->ket->Text=$asistenRecord->keterangan;
				$this->DDKelompok->SelectedValue=$asistenRecord->st_kelompok;
			}
			
			//$mode=$this->getViewState('baru');
			$this->showSql->Text=$mode;
			if ($mode==NULL)
			{
				$sql = "SELECT id FROM tbd_asisten_luar order by id desc";
				$num = AsistenLuarRecord::finder()->findBySql($sql);
				if($num==NULL)//jika kosong bikin ndiri
				{
					$urut='1';
				}else{			
						$urut = $num->getColumnValue('id') + 1;	
				}
				$this->IdAsisten->text=$urut;
			}
		}
		
		$this->cariIDAs->focus();
		
		
    }		
	
	public function cariClicked($sender,$param)
	{
		$session = $this->getSession();
        $session->remove("SomeData");
		
		$this->dtgSomeData->CurrentPageIndex = 0;
		
		if($this->cariIDAs->Text){ 
			$this->setViewState('cariByID', $this->cariIDAs->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->cariNamaAs->Text){
			$this->setViewState('cariByNama', $this->cariNamaAs->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		$this->bindGrid();
	}
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='DeleteItem')
        {
            if($this->User->IsAdmin){
				// add an aleart dialog to delete buttons
				$item->Hapus->Button->Attributes->onclick='if(!confirm(\'Apakah anda yakin ingin menghapus data ini..?\')) return false;';		
			}else{
				$item->Hapus->Button->Visible='0';
			}	
        }
    }
	
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$id=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			AsistenLuarRecord::finder()->deleteByPk($id);	
			$this->Response->redirect($this->Service->constructUrl('Admin.asistenLuarAdmin'));
		}	
    }
	
	public function baruClicked($sender,$param)
	{
		
		$this->MultiView->ActiveViewIndex='1';
		$this->asbar->visible=true;
		$this->clearViewState('baru');	
		$this->setViewState('baru','1');	
	}
	
	public function keluarViewClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Admin.asistenLuarAdmin',array('id'=>'0')));
	}	
		
	public function batalClicked($sender,$param)
	{		
			
		$this->Response->reload();
		$this->baruClicked($sender,$param);
	}
	
	public function simpanClicked($sender,$param)
	{		
		$mode=$this->getViewState('baru');
		//$this->showSql->Text=$mode;
		if ($mode==='1')
		{
			$asistenRecord=new AsistenLuarRecord;		            
			$asistenRecord->nama=ucwords($this->Nama->Text);
			$asistenRecord->alamat=ucwords($this->Alm->Text);
			$asistenRecord->telpon=$this->Tlp->Text;		
			$asistenRecord->keterangan=ucwords($this->ket->Text);
			$asistenRecord->st_kelompok=TPropertyValue::ensureString($this->DDKelompok->SelectedValue);
			$asistenRecord->save();
			$this->keluarViewClicked($sender,$param);
		}else
		{
			$asistenRecord=$this->AsistenRecord;
			$asistenRecord->nama=ucwords($this->Nama->Text);
			$asistenRecord->alamat=ucwords($this->Alm->Text);
			$asistenRecord->telpon=$this->Tlp->Text;		
			$asistenRecord->keterangan=ucwords($this->ket->Text);
			$asistenRecord->st_kelompok=TPropertyValue::ensureString($this->DDKelompok->SelectedValue);
			$asistenRecord->save();
			$this->keluarViewClicked($sender,$param);
		}
	}
	
	public function keluarClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}	
	
	protected function getAsistenRecord()
	{
		// use Active Record to look for the specified username		
		$id=$this->Request['id'];
		$asistenRecord=AsistenLuarRecord::finder()->findByPk($id);
		if(!($asistenRecord instanceof AsistenLuarRecord))
			throw new THttpException(500,'id tidak benar.');			
		return $asistenRecord;
		
	}
	
}
?>
