<?php
class KepegawaianAdmin extends SimakConf
{
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$cariNama,$cariNip,$cariID,$urutBy)
	{
		if($pil == "1")
		{
			$sql = "SELECT a.id,
						   a.nip,	 
						   a.nama, 
						   a.no_rek, 
						   a.sip, 
						   a.npwp, 
						   c.nama AS kelompok
						   FROM tbd_pegawai a,
						   		tbm_pegawai_kelompok c
						   WHERE a.kelompok=c.id ";
								
			if($cariNama <> '')			
				$sql .= "AND a.nama LIKE '$cariNama%' ";
						
			if($cariNip <> '')
				$sql .= "AND a.nip = '$cariNip' ";
						
			if($cariID <> '')
				$sql .= "AND a.id = '$cariID' ";
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			$sql .= " GROUP BY a.id";
			
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
			$sql = "SELECT a.id,
						   a.nip,	 
						   a.nama, 
						   a.no_rek, 
						   a.sip, 
						   a.npwp, 
						   c.nama AS kelompok
						   FROM tbd_pegawai a,
						   		tbm_pegawai_kelompok c
						   WHERE a.kelompok=c.id  ";
			if($cariNama <> '')			
				$sql .= "AND a.nama LIKE '$cariNama%' ";
						
			if($cariNip <> '')			
				$sql .= "AND a.nip = '$cariNip' ";
						
			if($cariID <> '')			
				$sql .= "AND a.id = '$cariID' ";				
			
			$sql .= " GROUP BY a.id";
			
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;

		}					 
		$page=PegawaiRecord::finder()->findAllBySql($sql);
		
		$this->showSql->Text=$sql;
		
		return $page;
		/*
		$criteria = new TActiveRecordCriteria;
		$criteria->Limit = $rows;
		$criteria->Offset = $offset;
		$page=PegawaiRecord::finder()->findAll($criteria);
		
		return $page;*/
	} 
		
	/**
     * Populates the datagrid with user lists.
     * This method is invoked by the framework when initializing the page
     * @param mixed event parameter
    */
    public function onPreLoad($param)
	{
		
	}
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('8');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{					
			$this->DDUrut->DataSource=KelompokPegawaiRecord::finder()->findAll();
			$this->DDUrut->dataBind();	
			$orderBy=$this->getViewState('orderBy');		
			$cariByNama=$this->getViewState('cariByNama');
			$cariByNip=$this->getViewState('cariByNip');
			$cariByID=$this->getViewState('cariByID');
			$urutBy=$this->getViewState('urutBy');
			
			$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy);
			// binds the data to interface components
			$this->UserGrid->dataBind();		
			$this->cariNama->focus();
		}		
    }		
	
	/**
     * Deletes a specified user record.
     * This method responds to the datagrid's OnDeleteCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			PegawaiRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianAdmin'));
			
		}	
    }	
	
	/**
     * Paging Control and Properties to specified pages.
     * This method responds to the datagrid's event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	public function changePage($sender,$param)
	{		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$cariByNama,$cariByNip,$cariByID,$urutBy);
		$this->UserGrid->dataBind();		
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function changePagerPosition($sender,$param)
	{		
		$position='TopAndBottom';		
		$this->UserGrid->PagerStyle->Position=$position;
		$this->UserGrid->PagerStyle->Visible=true;		
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
	
	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy);
		$this->UserGrid->dataBind();
	}

	public function useNextPrevPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy);
		$this->UserGrid->dataBind();
	}
	
	/*public function showMe($sender,$param)
	{		
		$this->showUp->DataSource=PegawaiRecord::finder()->findAll();
		$this->showUp->dataBind();
	}*/
 
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy);
		$this->UserGrid->dataBind();
	}
	
	/**
     * Sorting a specified user record.
     * This method responds to the datagrid's OnSortCommand event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */		
	public function sortGrid($sender,$param)
	{		
		$order = $param->SortExpression;
		$this->setViewState('orderBy',$oder);			
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
	/*	$criteria = new TActiveRecordCriteria;
		$criteria->OrdersBy[$param->SortExpression] = 'asc';					
		$criteria->Limit = $limit;
		$criteria->Offset = $offset;*/
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll($criteria);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$order,'','1',$this->cariNama->Text,$Nip,$this->cariID->Text,$this->DDUrut->SelectedValue);	
		$this->UserGrid->dataBind();	
	}
	
	/**
     * Showing a specified user coloumn.
     * This method responds to the datagrid's toggleColumnVisibility event.
     * @param TDataGrid the event sender
     * @param TDataGridCommandEventParameter the event parameter
     */
	public function toggleColumnVisibility($sender,$param)
	{		
		$orderBy=$this->getViewState('orderBy');
		$cariByNama=$this->getViewState('cariByNama');
		$cariByNip=$this->getViewState('cariByNip');
		$cariByID=$this->getViewState('cariByID');
		$urutBy=$this->getViewState('urutBy');
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		//$this->UserGrid->DataSource=PegawaiRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByNama,$cariByNip,$cariByID,$urutBy);
		$this->UserGrid->dataBind();
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianBaru'));		
	}
	
	public function cariClicked($sender,$param)
	{		
		$orderBy=$this->getViewState('orderBy');		
		
		if($this->DDUrut->SelectedValue) 		
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		
		if($this->cariNama->Text)
			$this->setViewState('cariByNama', $this->cariNama->Text);
		
		if($this->cariNip->Text)
		{ 
			$nipTmp = TPropertyValue::ensureString($this->cariNip->Text);
			$Dump = explode('.',$nipTmp,3);			
			foreach($Dump As $value)
			{
				$Nip .= $value;
			}	
			$this->setViewState('cariByNip', $Nip);
		}	
		
		if($this->cariID->Text) 
			$this->setViewState('cariByID', $this->cariID->Text);	
		
		$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$Nip,$this->cariID->Text,$this->DDUrut->SelectedValue));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
			
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$Nip,$this->cariID->Text,$this->DDUrut->SelectedValue);	
		$this->UserGrid->dataBind();
	}
	
	public function selectionChangedUrut($sender,$param)
	{				
		$orderBy=$this->getViewState('orderBy');
		
		if($this->cariNama->Text)
			$this->setViewState('cariByNama', $this->cariNama->Text);
		
		if($this->cariNip->Text) 
		{
			$nipTmp = TPropertyValue::ensureString($this->cariNip->Text);
			$Dump = explode('.',$nipTmp,3);			
			foreach($Dump As $value)
			{
				$Nip .= $value;
			}	
			$this->setViewState('cariByNip', $Nip);
		}	
		
		if($this->cariID->Text) 
			$this->setViewState('cariByID', $this->cariID->Text);	
		
		if($this->DDUrut->SelectedValue)
			$this->setViewState('urutBy', $this->DDUrut->SelectedValue);
		
		$jmlData=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$Nip,$this->cariID->Text,$this->DDUrut->SelectedValue));
			
			$this->jmlData->Text=$jmlData;
			$this->UserGrid->VirtualItemCount=$jmlData;
				
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->cariNama->Text,$Nip,$this->cariID->Text,$this->DDUrut->SelectedValue);	
		$this->UserGrid->dataBind();
	}
}

/*
 $sql = "SELECT a.id,
						   a.nip,	 
						   a.nama, 
						   a.status, 
						   b.nama AS status, 
						   c.nama AS kelompok, 
						   d.nama AS jabatan,
						   e.nama AS spesialis
						   FROM tbd_pegawai a,
						        tbm_pegawai_status b,
						   		tbm_pegawai_kelompok c,
						   		tbm_pegawai_jabatan d,
						   		tbm_spesialis e 
						   WHERE
							    a.status=b.id
								AND a.kelompok=c.id
								AND a.jabatan=d.id ";
								
			if($cariNama <> '')			
				$sql .= "AND a.nama LIKE '$cariNama%' ";
						
			if($cariNip <> '')
				$sql .= "AND a.nip = '$cariNip' ";
						
			if($cariID <> '')
				$sql .= "AND a.id = '$cariID' ";
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";	
			
			$sql .= " GROUP BY a.id";
			
			if($order <> '')							
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
*/
?>
