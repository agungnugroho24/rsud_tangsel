<?php
class beritaCtScan extends SimakConf
{   
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		}
		else
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}
			
		
		$page=$this->queryAction($sql,'S');		 
		
		return $page;
		
	}
	
    public function onPreLoad($param)
	{
		
	}
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{	/*
			$orderBy=$this->getViewState('orderBy');			
			$this->UserGrid->VirtualItemCount=CtScanJualRecord::finder()->count();
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			// binds the data to interface components
			$this->UserGrid->dataBind();		*/
			
			$this->CM->focus();	
			$this->show->Visible=false;
			
			$purge=$this->Request['purge'];
			//$nmTable=$this->Request['nmTable'];
			
			if($purge =='1'	)
			{			
				if(!empty($nmTable))
				{		
					$this->queryAction($nmTable,'D');//Droped the table						
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();
					$this->clearViewState('nmTable');//Clear the view state				
				}
					
				$this->clearViewState('nmTable');
				$this->clearViewState('cm');
				$this->clearViewState('notrans');
				$this->clearViewState('nama');
				$this->clearViewState('dokter');
				$this->noReg->Enabled=true;		
				$this->noReg->Text='';			
				$this->errMsg->Text='';			
				$this->noReg->Focus();
				$this->show->Visible=false;
				$this->Response->redirect($this->Service->constructUrl('CtScan.beritaCtScan',array('goto'=>'1')));
			}
		}		
    }		
	
	public function noRegChanged($sender,$param)
	{
		//$orderBy=$this->getViewState('orderBy');
		$cek=substr($this->noReg->Text,6,2);//Cek dari no registrasi 50=rawat jalan, 51	= rawat inap
		//if($cek=='50')
		if($cek=='01')
		{
			if(CtScanJualRecord::finder()->find('no_trans_rwtjln = ? AND flag = ?',$this->noReg->Text,'1'))
			{
				$this->CM->text=CtScanJualRecord::finder()->find('no_trans_rwtjln = ? AND flag = ?',$this->noReg->Text,'1')->cm;
				$this->show->Visible=true;
				$this->namaPasien->text=PasienRecord::finder()->findByPk($this->CM->Text)->nama;				
				$dok=CtScanJualRecord::finder()->find('no_trans_rwtjln = ?',$this->noReg->Text)->dokter;
				$this->namaDokter->text=PegawaiRecord::finder()->findByPk($dok)->nama;
			}	
		}
		//elseif($cek=='51')
		elseif($cek=='02')
		{
			if(CtScanJualInapRecord::finder()->find('no_trans_inap = ?AND flag = ?',$this->noReg->Text,'1'))
			{
				$this->CM->text=CtScanJualInapRecord::finder()->find('no_trans_inap = ?',$this->noReg->Text)->cm;
				$this->show->Visible=true;
				$this->namaPasien->text=PasienRecord::finder()->findByPk($this->CM->Text)->nama;				
				$dok=CtScanJualInapRecord::finder()->find('no_trans_inap = ?',$this->noReg->Text)->dokter;
				$this->namaDokter->text=PegawaiRecord::finder()->findByPk($dok)->nama;
			}
		}else{
			if(CtScanJualLainRecord::finder()->find('no_trans_rwtjln_lain = ?AND flag = ?',$this->noReg->Text,'1'))
			{
				$this->CM->text='-';//CtScanJualInapRecord::finder()->find('no_trans = ?',$this->noReg->Text)->cm;
				$this->show->Visible=true;
				$this->namaPasien->text=CtScanJualLainRecord::finder()->find('no_trans_rwtjln_lain = ?',$this->noReg->Text)->nama;		
				//$dok=CtScanJualInapRecord::finder()->find('no_trans = ?',$this->noReg->Text)->dokter;
				$this->namaDokter->text='-';
				//$this->namaDokter->text=CtScanJualLainRecord::finder()->find('no_trans_rwtjln_lain = ?',$this->noReg->Text)->dokter;		
				//PegawaiRecord::finder()->findByPk($dok)->nama;
			}
		}
			/*
			$this->UserGrid->VirtualItemCount=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->noReg->Text));		
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->noReg->Text);	
			$this->UserGrid->dataBind();				
			*/
			//if
			$this->errMsg->Text="";			
			$this->setViewState('notrans',$this->noReg->Text);
			$this->setViewState('cm',$this->CM->Text);
			$this->setViewState('nama',$this->namaPasien->text);
			$this->setViewState('dokter',$this->namaDokter->text);
			$this->noReg->Enabled=false;
			
			
			//$no_trans=$this->noReg->Text;
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 id_ctscan INT (2) NOT NULL,
										 nama VARCHAR(30) NOT NULL,
										 catatan VARCHAR(30) NOT NULL,									 
										 film_size VARCHAR(30) DEFAULT 0,
										 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$no_trans=$this->getViewState('notrans');
				//if($cek=='50')
				if($cek=='01')
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan b 
						WHERE b.no_trans_rwtjln='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				//elseif($cek=='51')
				elseif($cek=='02')
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan_inap b 
						WHERE b.no_trans_inap='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				else
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan_lain b 
						WHERE b.no_trans_rwtjln_lain='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$id_ctscan=$row['id'];
					$nama=$row['nama'];
					$catatan=$row['catatan'];
					$film=$row['film_size'];
					
					$sql="INSERT INTO $nmTable (id_ctscan,nama,catatan,film_size) VALUES ('$id_ctscan','$nama','$catatan','$film')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				
				$arr=$this->queryAction($sql,'R');
				$i=0;
				foreach($arr as $row)
				{
					$i=$i+1;
				}
				$this->UserGrid->VirtualItemCount=$i;				
				
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
				$this->UserGrid->dataBind();					
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				$no_trans=$this->getViewState('notrans');
				//if($cek=='50')
				if($cek=='01')
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan b 
						WHERE b.no_trans_rwtjln='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				//elseif($cek=='51')
				elseif($cek=='02')
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan_inap b 
						WHERE b.no_trans_inap='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				else
				{
					$sql="SELECT b.id,a.nama, b.catatan, b.film_size 
						FROM tbm_ctscan_tindakan a,tbt_ctscan_penjualan_lain b 
						WHERE b.no_trans_rwtjln_lain='$no_trans' AND 
							b.id_tindakan=a.kode";
				}
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$id_ctscan=$row['id'];
					$nama=$row['nama'];
					$catatan=$row['catatan'];
					$film=$row['film_size'];
					
					$sql="INSERT INTO $nmTable (id_ctscan,nama,catatan,film_size) VALUES ('$id_ctscan','$nama','$catatan','$film')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				
				$arr=$this->queryAction($sql,'R');
				$i=0;
				foreach($arr as $row)
				{
					$i=$i+1;
				}
				$this->UserGrid->VirtualItemCount=$i;				
				
				$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
				$this->UserGrid->dataBind();							
			}
		/*}else{
			$this->errMsg->Text="Data tersebut tidak ada!";
		}*/
	}
	
	public function changePage($sender,$param)
	{		
		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		/*$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text);
		$this->UserGrid->dataBind();		*/
		$nmTable = $this->getViewState('nmTable');		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
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
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->catatan->TextBox->Columns=40;
		  // $item->fim_size->TextBox->Columns=10;
		   //$item->nama->Enabled=false;
		   //$item->sat->Enabled=false;
		   //$item->pbf->Enabled=false;
		   //$item->sumber->Enabled=false;
		   //$item->sumberSekunder->Enabled=false;
        }       
    }
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{/*
			 $this->UserGrid->EditItemIndex=$param->Item->ItemIndex;

			$orderBy=$this->getViewState('orderBy');
			$this->UserGrid->VirtualItemCount=CtScanJualRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->noReg->Text);	
			// binds the data to interface components
			$this->UserGrid->dataBind();*/
			$limit=$this->UserGrid->PageSize;		
			$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
			
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();	
		}	
    }
	
	public function cancelItem($sender,$param)
    {/*
        $orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->EditItemIndex=-1;
        $this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->noReg->Text);	
		// binds the data to interface components
		$this->UserGrid->dataBind();		
		$this->CM->focus();*/
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$this->UserGrid->EditItemIndex=-1;
		
       	$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
    }
	
	public function saveItem($sender,$param)
    {
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$nmTable = $this->getViewState('nmTable');
		
        $item=$param->Item;		
		$id=$this->UserGrid->DataKeys[$item->ItemIndex];
		$nilai=$item->catatan->TextBox->Text;
		$sql="UPDATE $nmTable SET catatan='$nilai' WHERE id='$id'";
		$this->queryAction($sql,'C');
      
        $this->UserGrid->EditItemIndex=-1;		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
//		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->noReg->Text);	
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
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
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
		}	
    }	

	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		/*$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->CM->Text);	
		$this->UserGrid->dataBind();*/
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
 
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		/*$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->CM->Text);	
		$this->UserGrid->dataBind();*/
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}	
	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text);	
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
		$this->Response->redirect($this->Service->constructUrl('CtScan.beritaCtScan'));		
	}
	
	public function simpanClicked($sender,$param)
	{		
		//$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));	
		$noTrans=$this->getViewState('notrans');
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');		
		$cek=substr($this->noReg->Text,6,2);
		
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			$id_ctscan=$row['id_ctscan'];
			$cat=$row['catatan'];
			//if($cek=='50')
			if($cek=='01')
			{
				$sql="UPDATE tbt_ctscan_penjualan SET catatan='$cat' WHERE no_trans_rwtjln='$noTrans' AND id='$id_ctscan' ";			
				$this->queryAction($sql,'C');	
			}
			//elseif($cek=='51')
			elseif($cek=='02')
			{
				$sql="UPDATE tbt_ctscan_penjualan_inap SET catatan='$cat' WHERE no_trans_inap='$noTrans' AND id='$id_ctscan' ";			
				$this->queryAction($sql,'C');
			}else{
				$sql="UPDATE tbt_ctscan_penjualan_lain SET catatan='$cat' WHERE no_trans_rwtjln_lain='$noTrans' AND id='$id_ctscan' ";			
				$this->queryAction($sql,'C');
			}
		}
		
		//$this->Response->Reload();			
		$this->Response->redirect($this->Service->constructUrl('CtScan.cetakBeritaCtScan',array('notrans'=>$noTrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'table'=>$nmTable)));		
	}
	
	public function cariClicked($sender,$param)
	{	
		$orderBy=$this->getViewState('orderBy');	
		$this->CM->text=CtScanJualRecord::finder()->find('no_trans = ?',$this->noReg->Text)->cm;
		$this->namaPasien->text=PasienRecord::finder()->findByPk($this->CM->Text)->nama;				
		$dok=CtScanJualRecord::finder()->find('no_trans = ?',$this->noReg->Text)->dokter;
		$this->namaDokter->text=PegawaiRecord::finder()->findByPk($dok)->nama;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text);	
		$this->UserGrid->dataBind();
	}
		
	protected function refreshMe()
	{
		$this->Reload();
	}
}
?>
