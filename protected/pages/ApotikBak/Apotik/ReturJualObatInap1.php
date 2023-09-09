<?php
class ReturJualObatInap1 extends SimakConf
{   /*
	protected function getDataRows($offset,$rows,$order,$sort,$pil,$CM,$notrans)
	{
		if($pil == "1")
		{
			$sql = "SELECT 
						tbt_obat_jual_inap.cm AS cm,
						tbd_pasien.nama AS nmPas,
						tbd_pegawai.nama AS nmDok,
						tbm_obat.nama AS nmObat,
						tbt_obat_jual_inap.jumlah AS jml
					FROM
						tbt_obat_jual_inap
						INNER JOIN tbd_pegawai ON (tbt_obat_jual_inap.dokter = tbd_pegawai.id)
						INNER JOIN tbd_pasien ON (tbt_obat_jual_inap.cm = tbd_pasien.cm)
						INNER JOIN tbm_obat ON (tbt_obat_jual_inap.id_obat = tbm_obat.kode)
					WHERE
						tbt_obat_jual_inap.cm != '' ";
			
			if ($CM <> '')
				$sql .=" AND tbt_obat_jual_inap.cm = '$CM' ";
				
			if ($notrans <> '')
				$sql .=" AND tbt_obat_jual_inap.no_trans_inap = '$notrans' ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
		}else{
				$sql = "SELECT 
							tbt_obat_jual_inap.cm AS cm,
							tbd_pasien.nama AS nmPas,
							tbd_pegawai.nama AS nmDok,
							tbm_obat.nama AS nmObat,
							tbt_obat_jual_inap.jumlah AS jml
						FROM
							tbt_obat_jual_inap
							INNER JOIN tbd_pegawai ON (tbt_obat_jual_inap.dokter = tbd_pegawai.id)
							INNER JOIN tbd_pasien ON (tbt_obat_jual_inap.cm = tbd_pasien.cm)
							INNER JOIN tbm_obat ON (tbt_obat_jual_inap.id_obat = tbm_obat.kode)
						WHERE
							tbt_obat_jual_inap.cm != '' ";
			
			if ($CM <> '')
				$sql .=" AND tbt_obat_jual_inap.cm = '$CM' ";
				
			if ($notrans <> '')
				$sql .=" AND tbt_obat_jual_inap.no_trans_inap = '$notrans' ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}					
		
		$page=$this->queryAction($sql,'S');
		
		$this->showSql->Text=$sql;
		$this->showSql->Visible=false;//show SQL Expression broer!
		return $page;
		
	}
		/*
	 public function onPreLoad($param)
	{
		
	}*/
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
						
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
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
		if(!$this->IsPostBack)
		{	/*
			$orderBy=$this->getViewState('orderBy');
			$this->UserGrid->VirtualItemCount=RadJualRecord::finder()->count();
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			// binds the data to interface components
			$this->UserGrid->dataBind();		*/
			$this->noReg->focus();	
		}		
    }		
	
	public function noRegChanged($sender,$param)
	{		
		$this->CM->focus();
	}
	
	public function CMChanged($sender, $param)
	{
		//$orderBy=$this->getViewState('orderBy');
		$this->namaPasien->text=PasienRecord::finder()->findByPk($this->CM->Text)->nama;				
		$dok=ObatJualInapRecord::finder()->find('no_trans_inap = ?',$this->noReg->Text)->dokter;
		$this->namaDokter->text=PegawaiRecord::finder()->findByPk($dok)->nama;
		//$tmp=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text);
		/*
		$this->UserGrid->VirtualItemCount=count($this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text));
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text);	
		$this->UserGrid->dataBind();*/
		///*
		if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
									 nmObat VARCHAR(30) NOT NULL,
									 jml VARCHAR(4) NOT NULL,									 
									 total INT(11) NOT NULL,									 							 
									 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$noTrans=$this->noReg->Text;
				$cm=$this->CM->Text;
				
				$sql = "SELECT 
						tbt_obat_jual_inap.cm AS cm,
						tbd_pasien.nama AS nmPas,
						tbd_pegawai.nama AS nmDok,
						tbm_obat.nama AS nmObat,
						tbt_obat_jual_inap.hrg as hrg,
						tbt_obat_jual_inap.jumlah AS jml,
						tbt_obat_jual_inap.total AS total
					FROM
						tbt_obat_jual_inap
						INNER JOIN tbd_pegawai ON (tbt_obat_jual_inap.dokter = tbd_pegawai.id)
						INNER JOIN tbd_pasien ON (tbt_obat_jual_inap.cm = tbd_pasien.cm)
						INNER JOIN tbm_obat ON (tbt_obat_jual_inap.id_obat = tbm_obat.kode)
					WHERE
						tbt_obat_jual_inap.cm = '$cm' 
						AND tbt_obat_jual_inap.no_trans_inap = '$noTrans' ";
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$nmObat=$row['nmObat'];
					$jml=$row['jml'];
					$total=$row['total'];
				
					$sql="INSERT INTO $nmTable (nmObat,jml,total) VALUES ('$nmObat','$jml','$total')";
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
				$noTrans=$this->noReg->Text;
				$cm=$this->CM->Text;
				
				$sql = "SELECT 
						tbt_obat_jual_inap.cm AS cm,
						tbd_pasien.nama AS nmPas,
						tbd_pegawai.nama AS nmDok,
						tbm_obat.nama AS nmObat,
						tbt_obat_jual_inap.hrg as hrg,
						tbt_obat_jual_inap.jumlah AS jml,
						tbt_obat_jual_inap.total AS total
					FROM
						tbt_obat_jual_inap
						INNER JOIN tbd_pegawai ON (tbt_obat_jual_inap.dokter = tbd_pegawai.id)
						INNER JOIN tbd_pasien ON (tbt_obat_jual_inap.cm = tbd_pasien.cm)
						INNER JOIN tbm_obat ON (tbt_obat_jual_inap.id_obat = tbm_obat.kode)
					WHERE
						tbt_obat_jual_inap.cm = '$cm' 
						AND tbt_obat_jual_inap.no_trans_inap = '$noTrans' ";
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$nmObat=$row['nmObat'];
					$jml=$row['jml'];
					$total=$row['total'];
				
					$sql="INSERT INTO $nmTable (nmObat,jml,total) VALUES ('$nmObat','$jml','$total')";
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
		//}
		
		//}*/		
	}
	
	public function changePage($sender,$param)
	{		
		
		$limit=$this->UserGrid->PageSize;
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		//$offset,$rows,$order,$sort,$pil
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text);	
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
		if($item->ItemType==='editItem')
        {
           $item->jml->TextBox->Columns=40;		  
        }       
    }
	
	public function editItem($sender,$param)
    {
        
		 if ($this->User->IsAdmin)
		{
			/*
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			$orderBy=$this->getViewState('orderBy');
			$this->UserGrid->VirtualItemCount=RadJualInapRecord::finder()->count();
			// fetches all data account information 
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text);	
			// binds the data to interface components
			$this->UserGrid->dataBind();*/
			
			$limit=$this->UserGrid->PageSize;		
			$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
			
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ";
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
        $this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->CM->Text,$this->noReg->Text);		
		// binds the data to interface components
		$this->UserGrid->dataBind();		
		$this->CM->focus();*/
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
		
		$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
		
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ";
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
		/*
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$nmTable = $this->getViewState('nmTable');
		
        $item=$param->Item;		
		$id=$this->UserGrid->DataKeys[$item->ItemIndex];
		$nilai=$item->jml->TextBox->Text;
		$sql="UPDATE $nmTable SET nilai='$nilai' WHERE id='$id'";
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
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
		*/
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
		$nmTable = $this->getViewState('nmTable');
		
		//$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
		
		$item=$param->Item;		
		$id=$this->UserGrid->DataKeys[$item->ItemIndex];
		$jml=$item->jml->TextBox->Text;
		$sql="UPDATE $nmTable SET jml='$jml' WHERE id='$id'";
		$this->queryAction($sql,'C');      
        $this->UserGrid->EditItemIndex=-1;
		$sql="SELECT * FROM $nmTable ";
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
	
	
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			//ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Apotik.ReturJualObatInap'));
			
		}	
    }	

	public function useNumericPager($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->CM->Text,$this->noReg->Text);		
		$this->UserGrid->dataBind();
	}
 
	public function changePageSize($sender,$param)
	{
		$orderBy=$this->getViewState('orderBy');	
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		//$this->UserGrid->DataSource=KabupatenRecord::finder()->findAll();
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$this->CM->Text,$this->noReg->Text);	
		$this->UserGrid->dataBind();
	}	
	
		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text,$this->noReg->Text);		
		$this->UserGrid->dataBind();	
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function simpanClicked($sender,$param)
	{		
		//$this->Response->redirect($this->Service->constructUrl('Apotik.ReturJualObatInap'));
				
	}
/*	
	public function cariClicked($sender,$param)
	{	
		$orderBy=$this->getViewState('orderBy');	
		$this->CM->text=RadJualRecord::finder()->find('no_trans = ?',$this->noReg->Text)->cm;
		$this->namaPasien->text=PasienRecord::finder()->findByPk($this->CM->Text)->nama;				
		$dok=RadJualRecord::finder()->find('no_trans = ?',$this->noReg->Text)->dokter;
		$this->namaDokter->text=PegawaiRecord::finder()->findByPk($dok)->nama;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1',$this->CM->Text);	
		$this->UserGrid->dataBind();
	}
		
	/*	
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			//$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	
	}
	*/
		
		public function batalClicked($sender, $param)
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.ReturJualObatInap'));	
		}
		/*
	protected function refreshMe()
	{
		$this->Response->Reload();
	}
	*/
}
?>
