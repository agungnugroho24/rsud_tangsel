<?php
class ambilRad extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			//$this->showBayar->Visible=false;
			$this->cetakBtn->Enabled=false;
			
		}		
		
		$purge=$this->Request['purge'];
		$nmTable=$this->Request['nmTable'];
		
		if($purge =='1'	)
		{			
			if(!empty($nmTable))
			{		
				$this->queryAction($nmTable,'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->clearViewState('notrans');
			$this->clearViewState('cm');			
			$this->clearViewState('nama');
			$this->clearViewState('nmTable');
			
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';			
			$this->errMsg->Text='';
			
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Rad.ambilRad',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function batalClicked($sender,$param)
    {		
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
				
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errMsg->Text='';
		
		$this->notrans->Focus();
		$this->showSecond->Visible=false;
		$this->showTable->Visible=false;		
	}
		
	public function checkRegister($sender,$param)
    {
        // valid if the username is not found in the database
        if(RadJualRecord::finder()->find('no_trans = ? AND st_ambil=?', $this->notrans->Text,'0'))
		{			
			$tmp = $this->notrans->Text;			
			$sql ="SELECT 
					  tbt_rad_penjualan.cm AS cm,
					  tbd_pasien.nama as nama,
					  tbd_pegawai.nama as dokter
					FROM
					  tbt_rad_penjualan
					  INNER JOIN tbd_pasien ON (tbt_rad_penjualan.cm = tbd_pasien.cm)
					  INNER JOIN tbd_pegawai ON (tbt_rad_penjualan.dokter = tbd_pegawai.id)
			  		WHERE tbt_rad_penjualan.no_trans='$tmp' ";
					 
			/*
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$jmlHarga += $row['harga'];				
			}
			*/
					
			//$tmpPasien = RadJualRecord::finder()->findBySql($sql);
			$this->setViewState('notrans',$this->notrans->Text);
			$arr = $this->queryAction($sql,'R');
			foreach($arr as $row)
			{	
				$this->setViewState('cm',$row['cm']);
				$this->setViewState('nama',$row['nama']);
				$this->setViewState('dokter',$row['dokter']);
				
				$this->cm->Text= $row['cm'];
				$this->nama->Text= $row['nama'];
				$this->dokter->Text= $row['dokter'];
			}
			/*
									
			*/
			$this->showSecond->Visible=true;
			$this->showTable->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';		
			$this->cetakBtn->Enabled=true;
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_film VARCHAR(1) NOT NULL,									 
										 nm_film VARCHAR(30) DEFAULT 0,
										 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');//Create new tabel bro...
				
				$noTrans=$this->getViewState('notrans');
				$cm=$this->getViewState('cm');
				$sql="SELECT 
					  tbm_rad_tindakan.nama,
					  tbt_rad_penjualan.film_size
					FROM
					  tbt_rad_penjualan
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
					WHERE no_trans='$noTrans' AND cm='$cm'";
				//LabJualRecord::finder()->findBySql($sql);
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$nama=$row['nama'];
					$id_film=$row['film_size'];
					switch ($id_film)
					{
						case '0':
							$nmFilm='18 x 24';
							break;
						case '1':
							$nmFilm='24 x 30';
							break;
						case '2':
							$nmFilm='30 x 40';
							break;
						case '3':
							$nmFilm='35 x 35';
							break;
					}
					$sql="INSERT INTO $nmTable (nama,id_film,nm_film) VALUES ('$nama','$id_film','$nmFilm')";
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
				$cm=$this->getViewState('cm');
				$sql="SELECT 
					  tbm_rad_tindakan.nama,
					  tbt_rad_penjualan.film_size
					FROM
					  tbt_rad_penjualan
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
					WHERE no_trans='$noTrans' AND cm='$cm'";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$nama=$row['nama'];
					$id_film=$row['film_size'];
					switch ($id_film)
					{
						case '0':
							$nmFilm='18 x 24';
							break;
						case '1':
							$nmFilm='24 x 30';
							break;
						case '2':
							$nmFilm='30 x 40';
							break;
						case '3':
							$nmFilm='35 x 35';
							break;
					}
					$sql="INSERT INTO $nmTable (nama,id_film,nm_film) VALUES ('$nama','$id_film','$nmFilm')";
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
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
		}
    }	
	
	/*
	public function checkRM($sender,$param)
    {   		
		$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
    }
	*/	
	
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
	
	/*
	protected function getDataRows($offset,$rows)
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
        $data=$this->queryAction($sql,'R');
        $page=array();	
		
        for($i=0;$i<$rows;++$i)
        {
            if($offset+$i<$this->getRowCount())
                $page[$i]=$data[$offset+$i];
        }
		
		
		
        return $page;
    }
	
	
	protected function getRowCount()
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$jmlRows=0;
		foreach($arr as $row)
		{
			$jmlRows=$jmlRows+1;
		}
        return $jmlRows;
    }
	*/
	
	public function changePage($sender,$param)
	{				
		$limit=$this->UserGrid->PageSize;		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
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
				
		//$this->DataGrid->DataKeys[$param->Item->ItemIndex]
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->nm_film->TextBox->Columns=15;
		   //$item->normal->TextBox->Columns=15;
        }       
    }
	
	public function useNumericPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
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
	
	public function useNextPrevPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
	
	public function changePageSize($sender,$param)
	{
		
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		
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
		$item = $param->SortExpression;
		
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
	
	public function toggleColumnVisibility($sender,$param)
	{			
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
		$this->UserGrid->dataBind();
	}	
	
	public function editItem($sender,$param)
    {
        
		if ($this->User->IsAdmin)
		{
		
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
    {
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
		$nilai=$item->nm_film->TextBox->Text;
		//$normal=$item->normal->TextBox->Text;
		$sql="UPDATE $nmTable SET nm_film='$nilai' WHERE id='$id'";
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
    }
	
	public function keluarClicked($sender,$param)
	{		
		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {
		$noTrans=$this->getViewState('notrans');
		$cm=$this->getViewState('cm');
		$sql="UPDATE tbt_rad_penjualan SET st_ambil='1' WHERE no_trans='$noTrans' AND cm='$cm'";
		$this->queryAction($sql,'C');	
		$this->Response->Reload();		
	/*
		$notrans=$this->getViewState('notrans');
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;
		$table=$this->getViewState('nmTable');
		
		$sql="SELECT * FROM $table ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$newLabHasil= new LabHasilRecord();
			$newLabHasil->no_trans=$notrans;
			$newLabHasil->cm=$cm;
			$newLabHasil->id_lab=$row['id_tdk'];
			$newLabHasil->tgl=date('y-m-d');
			$newLabHasil->wkt=date('G:i:s');
			$newLabHasil->operator=$operator;
			$newLabHasil->hasil=$row['nilai'];
			$newLabHasil->flag='0';
			$newLabHasil->Save();			
		}
		
		$this->Response->redirect($this->Service->constructUrl('Rad.cetakLapHasilLab',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'table'=>$table)));*/
		
	}
}
?>
