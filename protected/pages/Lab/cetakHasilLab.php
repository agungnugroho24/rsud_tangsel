<?php
class cetakHasilLab extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('5');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDReg->Enabled = false;
			
			$this->showSecond->Display = 'None';
			$this->showTable->Display = 'None';
			
			$this->cetakBtn->Enabled=false;
			
			$this->cariCm->Focus();
			
			$this->tglawal->Text = date('d-m-Y');
			$this->tglakhir->Text = date('d-m-Y');
			
			$this->tglCtrl->Display = 'None';
			$this->tglCtrl->Enabled = false;
			
			$jnsPasien = $this->modeInput->SelectedValue;				
			
			if($this->Request['cm'] !='' && $this->Request['noTrans'] != '' && $this->Request['noReg'] != '' && $this->Request['jnsPasien'] != '')
			{
				$this->modeInput->SelectedValue = $this->Request['jnsPasien'];
				$this->cariCm->Text = $this->Request['cm'];
				$this->DDReg->SelectedValue = $this->Request['noReg'];
				
				$this->checkCm($sender,$param);
				$this->checkRegister($sender,$param);
			}
			
			$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Lab.CariPasienLab&parentPage=Lab.cetakHasilLab&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		}		
		
		
		if ($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql="SELECT * FROM $nmTable WHERE nilai=''";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0)
				$this->cetakBtn->Enabled=false;
			else
				$this->cetakBtn->Enabled=true;
			
			$this->cetakBtn->Enabled=true;
			//$this->showSql->Text = count($arr);
		}
		
		$purge=$this->Request['purge'];
		$nmTable=$this->Request['nmTable'];
		
		if($purge =='1'	)
		{			
			if(!empty($nmTable))
			{		
				$sql = "SHOW TABLES LIKE '$nmTable'";
				if($this->queryAction($sql,'S'))
				{
					$this->queryAction($nmTable,'D');//Droped the table						
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();
					$this->clearViewState('nmTable');//Clear the view state				
				}	
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
			$this->Response->redirect($this->Service->constructUrl('Lab.cetakHasilLab',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function showFirstLock()
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		$this->DDReg->Enabled = false;
	}
	
	public function showFirstCallback($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
		$this->tglCtrl->render($param->getNewWriter());
	}
	
	public function DDRegCallBack($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
	}
	
	
	public function modeInputChanged($sender,$param)
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0' || $jnsPasien == '1') //pasien rawat jalan / inap
		{
			$this->cariCm->Enabled = true;	
			$this->DDReg->Enabled = false;
			
			$this->tglawal->Text = date('d-m-Y');
			$this->tglakhir->Text = date('d-m-Y');
			
			$this->tglCtrl->Display = 'None';
			$this->tglCtrl->Enabled = false;
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cariCm->getClientID().'.focus();');	
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //Jika pasien Bebas / Bebas Karyawan
		{
			$this->cariCm->Enabled = false;	
			$this->DDReg->Enabled = true;
			
			$this->tglCtrl->Display = 'Dynamic';
			$this->tglCtrl->Enabled = true;
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			
			$sql = "SELECT 
					  tbt_lab_penjualan_lain.no_trans,
					  tbt_lab_penjualan_lain.no_trans_pas_luar,
					  tbt_lab_penjualan_lain.no_reg,
					  tbt_lab_penjualan_lain.id_tindakan,
					  tbd_pasien_luar.nama
					FROM
					  tbt_lab_penjualan_lain
					  INNER JOIN tbd_pasien_luar ON (tbt_lab_penjualan_lain.no_trans_pas_luar = tbd_pasien_luar.no_trans)
					WHERE 
						tbt_lab_penjualan_lain.st_cetak_hasil = '0' AND 
						tbt_lab_penjualan_lain.st_ambil = '0' ";
			
			if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
			{
				$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
				$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
				$sql .= "AND tbt_lab_penjualan_lain.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
			}
				
			$sql .=	"GROUP BY tbt_lab_penjualan_lain.no_reg
					ORDER BY tbt_lab_penjualan_lain.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					$nama = $row['nama'];
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nama);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}	
		}
		
		$jnsPasien = $this->modeInput->SelectedValue;
			
		$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Lab.CariPasienLab&parentPage=Lab.cetakHasilLab&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
	}
	
	public function checkCm($sender,$param)
    {
		$cm = $this->formatCm($this->cariCm->Text);
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sql = "SELECT 
					  tbt_lab_penjualan.no_trans,
					  tbt_lab_penjualan.no_trans_rwtjln,
					  tbt_lab_penjualan.no_reg,
					  tbt_lab_penjualan.id_tindakan,
					  tbm_poliklinik.nama AS nm_poli
					FROM
					  tbt_lab_penjualan
					  INNER JOIN tbt_rawat_jalan ON (tbt_lab_penjualan.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbm_poliklinik ON (tbt_lab_penjualan.klinik = tbm_poliklinik.id)
					WHERE 
						tbt_lab_penjualan.cm = '$cm' AND 
						tbt_lab_penjualan.st_cetak_hasil = '0' AND 
						tbt_lab_penjualan.st_ambil = '0'  AND
						tbt_rawat_jalan.st_alih = '0' 
					GROUP BY tbt_lab_penjualan.no_reg 
					ORDER BY tbt_lab_penjualan.no_reg ";
						
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noTransJln = $row['no_trans_rwtjln'];
					$noReg = $row['no_reg'];
					$nmKlinik = $row['nm_poli'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nmKlinik);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$sql = "SELECT 
					  tbt_lab_penjualan_inap.no_trans,
					  tbt_lab_penjualan_inap.no_trans_inap,
					  tbt_lab_penjualan_inap.no_reg,
					  tbt_lab_penjualan_inap.id_tindakan
					FROM
					  tbt_lab_penjualan_inap
					  INNER JOIN tbt_rawat_inap ON (tbt_lab_penjualan_inap.no_trans_inap = tbt_rawat_inap.no_trans)
					WHERE 
						tbt_lab_penjualan_inap.cm = '$cm' AND 
						tbt_lab_penjualan_inap.st_cetak_hasil = '0' AND 
						tbt_lab_penjualan_inap.st_ambil = '0' AND
						tbt_rawat_inap.status = '0' 
					GROUP BY tbt_lab_penjualan_inap.no_reg
					ORDER BY tbt_lab_penjualan_inap.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}		
		}
	}
	
	public function cmNoFound($cm)
    {
		$this->showFirstLock();
		
		$jnsPasien = $this->modeInput->SelectedValue;		
		
		if($jnsPasien == '0') 
			$txt = 'Tidak ada transaksi laboratorium Rawat Jalan yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '1')
			$txt = 'Tidak ada transaksi laboratorium Rawat Inap yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '2')
			$txt = 'Tidak ada transaksi laboratorium Pasien Luar yang bisa diproses';	
		
		$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">'.$txt.'</p>\',timeout: 4000,dialog:{
					modal: true
				}});
				document.all.'.$this->cariCm->getClientID().'.focus();');	
	}
	
		
	public function checkRegister($sender,$param)
    {
		$tmp = $this->DDReg->SelectedValue;
		$jnsPasien = $this->modeInput->SelectedValue;
				
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			if(LabJualRecord::finder()->find('no_reg = ?', $tmp))
			{								
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_lab_penjualan a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg = '$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = LabJualRecord::finder()->findBySql($sql);
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			if(LabJualInapRecord::finder()->find('no_reg = ?', $tmp))
			{						
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_lab_penjualan_inap a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg='$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = LabJualInapRecord::finder()->findBySql($sql);
			}	
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			if(LabJualLainRecord::finder()->find('no_reg = ?', $tmp))
			{					
				$sql ="SELECT 
						  tbt_lab_penjualan_lain.nama AS id,
						  tbt_lab_penjualan_lain.no_trans,
						  tbt_lab_penjualan_lain.id_tindakan
						FROM
						  tbt_lab_penjualan_lain 
						WHERE tbt_lab_penjualan_lain.no_reg='$tmp' ";
						
				$tmpPasien = LabJualLainRecord::finder()->findBySql($sql);
			}
		}
		
		if($tmpPasien)
		{	
			$this->setViewState('notrans',$this->notrans->Text);						
			$this->setViewState('nama',$tmpPasien->id);				
			
			if ($jnsPasien=='0' || $jnsPasien=='1')
			{
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->dokter);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->dokter->Text= $tmpPasien->dokter;
			}
			else
			{
				$this->cm->Text= '-';
				$this->dokter->Text= '-';
			}
						
			
			$this->nama->Text = $tmpPasien->id;			
									
			$this->showFirst->Enabled = false;
			$this->showSecond->Display = 'Dynamic';
			$this->showTable->Display = 'Dynamic';			
			
			$this->errMsg->Text='';					
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_paket INT(11) NOT NULL,
										 id_tdk INT(11) NOT NULL,									 
										 nilai VARCHAR(225) DEFAULT NULL,
										 normal VARCHAR(225) DEFAULT NULL,
										 normal_perempuan VARCHAR(225) DEFAULT NULL,
										 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');						
			}			
			
			$noTrans=$this->getViewState('notrans');
			$cm = $this->formatCm($this->cariCm->Text);
			
			if ($jnsPasien=='0')
			{
				$sql="SELECT * FROM tbt_lab_penjualan WHERE no_reg = '$tmp' ";
			}
			elseif ($jnsPasien=='1')
			{
				$sql="SELECT * FROM tbt_lab_penjualan_inap WHERE no_reg='$tmp'";
			}
			elseif ($jnsPasien=='2')
			{
				$sql="SELECT * FROM tbt_lab_penjualan_lain WHERE no_reg='$tmp' ";
			}
			//LabJualRecord::finder()->findBySql($sql);
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$idTdk = $row['id_tindakan'];
				$stPaket = LabTdkRecord::finder()->findByPk($idTdk)->st_paket;
				
				//if(substr($row['id_tindakan'],0,1)=="Z")
				if($stPaket > 0)
				{
					$sql="SELECT 
							  tbm_lab_paket.kode,
							  tbm_lab_paket.item,
							  tbm_lab_paket.normal,
							  tbm_lab_paket.normal_perempuan
							FROM
							  tbm_lab_paket
							  INNER JOIN tbm_lab_tindakan ON (tbm_lab_paket.id_paket = tbm_lab_tindakan.st_paket)
							WHERE
							  tbm_lab_tindakan.kode = '$idTdk' ";
					$arr=$this->queryAction($sql,'R');
					foreach($arr as $row)
					{		 
						$kode=$row['kode'];
						$nama=$row['item'];
						$normal=$row['normal'];
						$normal_perempuan=$row['normal_perempuan'];
						$nilai='';					
					
						$sql="INSERT INTO $nmTable (nama,id_paket,id_tdk,nilai,normal,normal_perempuan) VALUES ('$nama','$stPaket','$kode','$nilai','$normal','$normal_perempuan')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
					}									 				
					
				}
				else
				{
					$sql="SELECT nama,normal,normal_perempuan
							 FROM tbm_lab_tindakan
							 WHERE kode='$idTdk' ";
					$tmpTdk = LabTdkRecord::finder()->findBySql($sql);					 				
					$nama=$tmpTdk->nama;
					$normal=$tmpTdk->normal;
					$normal_perempuan=$tmpTdk->normal_perempuan;
					$nilai='';					
					
					$sql="INSERT INTO $nmTable (nama,id_paket,id_tdk,nilai,normal,normal_perempuan) VALUES ('$nama','$stPaket','$idTdk','$nilai','$normal','$normal_perempuan')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
				
			}
			$sql="DELETE FROM $nmTable WHERE id_tdk='RUJ'";
			$arr=$this->queryAction($sql,'C');
			$sql="DELETE FROM $nmTable WHERE id_tdk='PDT'";
			$arr=$this->queryAction($sql,'C');
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
           $item->nilai->TextBox->Columns=15;
		   //$item->normal->TextBox->Columns=15;
		   //$item->normal2->TextBox->Columns=15;
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
		
        $item = $param->Item;		
		$id = $this->UserGrid->DataKeys[$item->ItemIndex];
		$nilai = trim(str_replace('\'','`',$item->nilai->TextBox->Text));
		//$normal = $item->normal->TextBox->Text;
		//$normal2 = $item->normal2->TextBox->Text;
		
		//$sql="UPDATE $nmTable SET nilai='$nilai',normal='$normal',normal_perempuan='$normal2' WHERE id='$id'";//Ga peril update ke nilai normal Om� :)
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
    }
	
	public function updateRow($sender,$param)
    {
		//$item = $param->Item;	
		$item = $sender->Parent->getNamingContainer(); 
		$index = $item->getItemIndex();
		
		$id = $this->UserGrid->Items[$index]->nilaiHasilColoumn->idVal->Value;
		$nilai = trim(str_replace('\'','`',$this->UserGrid->Items[$index]->nilaiHasilColoumn->nilaiHasil->Text));
		
		//$this->getPage()->getClientScript()->registerEndScript ('','alert(\''.$nilai.'\');');	
		
		$nmTable = $this->getViewState('nmTable');
		
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
		
		//$sql="UPDATE $nmTable SET nilai='$nilai',normal='$normal',normal_perempuan='$normal2' WHERE id='$id'";//Ga peril update ke nilai normal Om� :)
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
		
		$sql="SELECT COUNT(*) AS jml FROM $nmTable";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			$jml = $row['jml'];
		}
		
		if($index+1 < $jml)
		{
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->UserGrid->Items[$index+1]->nilaiHasilColoumn->nilaiHasil->getClientID().'.focus();');
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
				
		//$this->Response->Reload();				
		$this->Response->redirect($this->Service->constructUrl('Lab.cetakHasilLab'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {	
		$jnsPasien = $this->modeInput->SelectedValue;
		$noReg = $this->DDReg->SelectedValue;		
		$notrans = $this->getViewState('notrans');
		$cm = $this->formatCm($this->cariCm->Text);
		$nama = $this->nama->Text;
		$dokter = $this->dokter->Text;
		$operator = $this->User->IsUserNip;
		$nipTmp = $this->User->IsUserNip;
		$table = $this->getViewState('nmTable');
		
		
		//UPDATE st_cetak_hasil
		if ($jnsPasien=='0')
		{
			$sql="UPDATE tbt_lab_penjualan SET st_cetak_hasil = '1' WHERE no_reg = '$noReg' ";
		}
		elseif ($jnsPasien=='1')
		{
			$sql="UPDATE tbt_lab_penjualan_inap SET st_cetak_hasil = '1' WHERE no_reg = '$noReg' ";
		}
		elseif ($jnsPasien=='2')
		{
			$cm = '';
			$sql="UPDATE tbt_lab_penjualan_lain SET st_cetak_hasil = '1' WHERE no_reg = '$noReg' ";
		}
		
		$this->queryAction($sql,'C');
		
			
		//UPDATE tbt_lab_hasil	
		$sql="SELECT * FROM $table ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$newLabHasil= new LabHasilRecord();
			$newLabHasil->no_trans=$noReg;
			$newLabHasil->cm = $cm;
			$newLabHasil->id_paket=$row['id_paket'];
			
			if($row['id_paket']>0)
			{
				$newLabHasil->id_lab=LabTdkRecord::finder()->find('st_paket=?',$row['id_paket'])->kode;
				$newLabHasil->kode_item_paket=$row['id_tdk'];
			}
			else
			{
				$newLabHasil->id_lab=$row['id_tdk'];
				$newLabHasil->kode_item_paket=$row['id_tdk'];
			}
			
			$newLabHasil->tgl=date('y-m-d');
			$newLabHasil->wkt=date('G:i:s');
			$newLabHasil->operator=$operator;
			$newLabHasil->hasil=$row['nilai'];
			$newLabHasil->nilai_normal_pria=$row['normal'];
			$newLabHasil->nilai_normal_wanita=$row['normal_perempuan'];
			$newLabHasil->flag='0';
			$newLabHasil->tipe_pasien = $jnsPasien;
			$newLabHasil->Save();			
		}
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->Response->redirect($this->Service->constructUrl('Lab.cetakLapHasilLab',array('jnsPasien'=>$jnsPasien,'noReg'=>$noReg,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'table'=>$table)));
		
	}
}
?>
