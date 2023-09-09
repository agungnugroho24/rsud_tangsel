<?php
class ambilCtScan extends SimakConf
{   
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('13');
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
			$this->Response->redirect($this->Service->constructUrl('CtScan.ambilCtScan',array('goto'=>'1')));
	
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
		$tmp = $this->notrans->Text;
		$this->setViewState('notrans',$this->notrans->Text);
		$cek=substr($this->notrans->Text,6,2);//Cek dari no registrasi 50=rawat jalan, 51	= rawat inap
		//if ($cek=='50')
		if ($cek=='01')
		{
			if(CtScanJualRecord::finder()->find('no_trans_rwtjln = ? AND st_ambil=?', $this->notrans->Text,'0'))
			{				
				$sql ="SELECT 
						  tbt_ctscan_penjualan.cm AS cm,
						  tbd_pasien.nama as nama,
						  tbd_pegawai.nama as dokter
						FROM
						  tbt_ctscan_penjualan
						  INNER JOIN tbd_pasien ON (tbt_ctscan_penjualan.cm = tbd_pasien.cm)
						  INNER JOIN tbd_pegawai ON (tbt_ctscan_penjualan.dokter = tbd_pegawai.id)
						WHERE tbt_ctscan_penjualan.no_trans_rwtjln='$tmp' AND tbt_ctscan_penjualan.st_ambil='0' ";
				$arr = $this->queryAction($sql,'R');
			}			
		}
		//elseif($cek=='51')
		elseif($cek=='02')
		{
			if(CtScanJualInapRecord::finder()->find('no_trans_inap = ? AND st_ambil=?', $this->notrans->Text,'0'))
			{				
				$sql ="SELECT 
						  tbt_ctscan_penjualan_inap.cm AS cm,
						  tbd_pasien.nama as nama,
						  tbd_pegawai.nama as dokter
						FROM
						  tbt_ctscan_penjualan_inap
						  INNER JOIN tbd_pasien ON (tbt_ctscan_penjualan_inap.cm = tbd_pasien.cm)
						  INNER JOIN tbd_pegawai ON (tbt_ctscan_penjualan_inap.dokter = tbd_pegawai.id)
						WHERE tbt_ctscan_penjualan_inap.no_trans_inap='$tmp' AND tbt_ctscan_penjualan_inap.st_ambil='0' ";
				$arr = $this->queryAction($sql,'R');
			}	
		}
		else
		{
			if(CtScanJualLainRecord::finder()->find('no_trans_rwtjln_lain = ? AND st_ambil=?', $this->notrans->Text,'0'))
			{				
				$sql ="SELECT 
						  '-' AS cm,	
						  tbt_ctscan_penjualan_lain.nama AS nama,
						  '-' AS dokter
						FROM
						  tbt_ctscan_penjualan_lain
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_lain.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_lain.no_trans_rwtjln_lain='$tmp' AND tbt_ctscan_penjualan_lain.st_ambil='0' ";
				$arr = $this->queryAction($sql,'R');
			}	
		}
				
		if ($arr)
		{
			foreach($arr as $row)
			{	
				$this->setViewState('cm',$row['cm']);
				$this->setViewState('nama',$row['nama']);
				$this->setViewState('dokter',$row['dokter']);
				$this->cm->Text= $row['cm'];
				$this->nama->Text= $row['nama'];
				$this->dokter->Text= $row['dokter'];
			}	
			
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
				
				$noTrans=$tmp;//$this->getViewState('notrans');
				$cm=$this->getViewState('cm');
				if ($cek=='01')
				{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan.film_size
						FROM
						  tbt_ctscan_penjualan
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan.no_trans_rwtjln='$noTrans' AND cm='$cm'";
					$arr=$this->queryAction($sql,'R');
				}elseif ($cek=='02')
				{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan_inap.film_size
						FROM
						  tbt_ctscan_penjualan_inap
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_inap.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_inap.no_trans_inap='$noTrans' AND cm='$cm'";
					$arr=$this->queryAction($sql,'R');
				}else{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan_lain.film_size
						FROM
						  tbt_ctscan_penjualan_lain
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_lain.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_lain.no_trans_rwtjln_lain='$noTrans' ";
					$arr=$this->queryAction($sql,'R');
				}
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
				if ($cek=='01')
				{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan.film_size
						FROM
						  tbt_ctscan_penjualan
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_inap.no_trans_rwtjln='$noTrans' AND cm='$cm'";
					$arr=$this->queryAction($sql,'R');
				}elseif ($cek=='02')
				{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan_inap.film_size
						FROM
						  tbt_ctscan_penjualan_inap
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_inap.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_inap.no_trans_inap='$noTrans' AND cm='$cm'";
					$arr=$this->queryAction($sql,'R');
				}else{
					$sql="SELECT 
						  tbm_ctscan_tindakan.nama,
						  tbt_ctscan_penjualan_lain.film_size
						FROM
						  tbt_ctscan_penjualan_lain
						  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_lain.id_tindakan = tbm_ctscan_tindakan.kode)
						WHERE tbt_ctscan_penjualan_lain.no_trans_rwtjln_lain='$noTrans' ";
					$arr=$this->queryAction($sql,'R');
				}/*
				$sql="SELECT 
					  tbm_ctscan_tindakan.nama,
					  tbt_ctscan_penjualan.film_size
					FROM
					  tbt_ctscan_penjualan
					  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan.id_tindakan = tbm_ctscan_tindakan.kode)
					WHERE no_trans='$noTrans' AND cm='$cm'";
				$arr=$this->queryAction($sql,'R');*/
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
			}//end if nmTable	
		}else{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
		}
    }
	
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
	
	public function keluarClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Home'));		
		//$this->MultiView->ActiveViewIndex='1';
	}	
	
	
	public function cetakClicked($sender,$param)
    {
		$noTrans=$this->getViewState('notrans');
		$cm=$this->getViewState('cm');
		$cek=substr($this->notrans->Text,6,2);//Cek dari no registrasi 50=rawat jalan, 51	= rawat inap
		if ($cek=='01')
		{
			$sql="UPDATE tbt_ctscan_penjualan SET st_ambil='1' WHERE no_trans_rwtjln='$noTrans' AND cm='$cm'";
			$this->queryAction($sql,'C');
		}elseif ($cek=='02')
		{	
			$sql="UPDATE tbt_ctscan_penjualan_inap SET st_ambil='1' WHERE no_trans_inap='$noTrans' AND cm='$cm'";
			$this->queryAction($sql,'C');
		}else{
			$sql="UPDATE tbt_ctscan_penjualan_lain SET st_ambil='1' WHERE no_trans_rwtjln_lain='$noTrans'";
			$this->queryAction($sql,'C');
		}
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
		
		$this->Response->redirect($this->Service->constructUrl('CtScan.cetakLapHasilLab',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'table'=>$table)));*/
		
	}
	
	public function viewChanged($sender,$param)
	{
		if($this->MultiView->ActiveViewIndex===1)
        {
            $this->kembaliBtn->Focus();
        }
	}
	public function kembaliClicked()
	{	
		$this->Response->Reload();
	}
}
?>
