<?php
class ambilHasilLab2 extends SimakConf
{   
	private $sortExp = "no_reg";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('5');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDReg->Enabled = false;
			
			$this->showSecond->Display = 'None';
			$this->showTable->Display = 'None';
			
			$this->cetakBtn->Enabled=false;
			
			$this->cariCm->Focus();
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
			
			//$this->showSql->Text = count($arr);
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
			$this->Response->redirect($this->Service->constructUrl('Lab.cetakHasilLab',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
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
	
	private function bindGrid()
    {
	    $this->pageSize = $this->grid->PageSize;
        $this->offset = $this->pageSize * $this->grid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			if($this->jnsAnggota->SelectedValue == '0')
			{
				$tbl = 'tbd_member_sementara';
			}
			else
			{
				$tbl = 'tbd_member';	
			}
				
			$sql = "SELECT * FROM $tbl  ";	
			
			if($this->jnsAnggota->SelectedValue == '0')
			{
				$sql .= " WHERE st_keanggotaan = '01' ";
			}
			else
			{
				$sql .= " WHERE id <> '' ";
			}
			
			if($this->modeVer->SelectedValue == '1') //mode verifikasi
			{
				if($this->DDstatusVer->SelectedValue != '')
				{
					$DDstatusVer = $this->DDstatusVer->SelectedValue - 1;
					$sql .= "AND st_verifikasi = '$DDstatusVer'";
				}
			}
			
			if($this->modeVer->SelectedValue == '2') //mode alih status
			{
				$stAnggotaOperator = MemberRecord::finder()->find('id_user=?',$this->User->IsUserName)->st_keanggotaan;
			
				$sql .= " AND CAST(st_keanggotaan as UNSIGNED) < '$stAnggotaOperator' ";
			}
			
			if($this->DDstAnggota->SelectedValue != '')
			{
				$DDstAnggota = $this->DDstAnggota->SelectedValue;
				$sql .= "AND st_keanggotaan = '$DDstAnggota'";
			}
			
			if($this->id->Text != '')
			{
				$id = $this->id->Text;
				$sql .= "AND id = '$id' ";
			}
			
			if($this->nama->Text != '')
			{
				$nama = $this->nama->Text;
				$sql .= "AND nama_dpn LIKE '%$nama%' ";
			}
			
			if($this->nama2->Text != '')
			{
				$nama2 = $this->nama2->Text;
				$sql .= "AND nama_blk LIKE '%$nama2%' ";
			}
			
			if($this->alamat->Text != '')
			{
				$alamat = $this->alamat->Text;
				$sql .= "AND alamat LIKE '%$alamat%' ";
			}
			
			if($this->telp->Text != '')
			{
				$telp = $this->telp->Text;
				$sql .= "AND tlp = '$telp' ";
			}
			
			if($this->hp->Text != '')
			{
				$hp = $this->hp->Text;
				$sql .= "AND hp = '$hp' ";
			}
			
			if($this->DDProv->SelectedValue != '')
			{
				$DDProv = $this->DDProv->SelectedValue;
				$sql .= "AND propinsi = '$DDProv'";
			}
			
			if($this->DDKotaKab->SelectedValue != '')
			{
				$DDKotaKab = $this->DDKotaKab->SelectedValue;
				$sql .= "AND kab_kota = '$DDKotaKab'";
			}
			
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->grid->DataSource = $data;
            $this->grid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 10)
			{
				$this->pager->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				$this->pager->Display = 'None';
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->grid->DataSource = $session["SomeData"];
            $this->grid->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function grid_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function grid_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();

    }	
	
	protected function grid_ItemCreated($sender,$param)
    {
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$ID = $this->grid->DataKeys[$item->ItemIndex];
			
			
			$nmDpn = $item->DataItem['nama_dpn'];
			$nmBlk = $item->DataItem['nama_blk'];
			$alamat = $item->DataItem['alamat_lengkap'];
			$tlp = $item->DataItem['tlp'];
			$hp = $item->DataItem['hp'];
			$tglReg = $item->DataItem['tgl_registrasi'];
			$stAnggotaMember = $item->DataItem['st_keanggotaan'];			
			
			$item->column1->image->ImageUrl = $this->Application->AssetManager->publishFilePath($this->Application->Service->BasePath.'/member/foto/'.$item->DataItem['foto']);
			$item->column2->idTxt->Text = $ID;
			$item->column2->namaTxt->Text = $nmDpn.' '.$nmBlk;
			$item->column2->alamatTxt->Text = $alamat;
			$item->column2->tglTxt->Text = $this->convertDate($tglReg,'3');	
			$item->column2->tlpTxt->Text = $tlp;
			$item->column2->hpTxt->Text = $hp;
			$item->column2->stTxt->Text = StatusKeanggotaanRecord::finder()->findByPk($stAnggotaMember)->nama;
		}
    }
	
	protected function grid_EditCommand($sender,$param)
    { $this->grid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function grid_CancelCommand($sender,$param)
    { $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function grid_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function gridEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		if($this->jnsAnggota->SelectedValue == '0')
		{
			$tbl = 'tbd_member_sementara';
		}
		else
		{
			$tbl = 'tbd_member';	
		}
		
		$sql = "DELETE FROM $tbl WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGrid();
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
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cariCm->getClientID().'.focus();');	
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //Jika pasien Bebas / Bebas Karyawan
		{
			$this->cariCm->Enabled = false;	
			$this->DDReg->Enabled = true;
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
						tbt_lab_penjualan_lain.st_ambil = '0' 
					GROUP BY tbt_lab_penjualan_lain.no_reg
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
					  tbt_lab_penjualan.tgl,
					  tbt_lab_penjualan.id_tindakan,
					  tbm_poliklinik.nama AS nm_poli
					FROM
					  tbt_lab_penjualan
					  INNER JOIN tbt_rawat_jalan ON (tbt_lab_penjualan.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbm_poliklinik ON (tbt_lab_penjualan.klinik = tbm_poliklinik.id)
					WHERE 
						tbt_lab_penjualan.cm = '$cm' AND 
						tbt_lab_penjualan.st_cetak_hasil = '1' AND 
						tbt_lab_penjualan.st_ambil = '0'  AND
						tbt_rawat_jalan.flag = '1' AND 
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
										 id_tdk VARCHAR(4) NOT NULL,									 
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
							  tbm_lab_tindakan.normal,
							  tbm_lab_tindakan.normal_perempuan
							FROM
							  tbm_lab_paket
							  INNER JOIN tbm_lab_tindakan ON (tbm_lab_paket.kode = tbm_lab_tindakan.kode)
							WHERE
							  tbm_lab_paket.id_paket = '$idTdk' ";
					$arr=$this->queryAction($sql,'R');
					foreach($arr as $row)
					{		 
						$kode=$row['kode'];
						$nama=$row['item'];
						$normal=$row['normal'];
						$normal_perempuan=$row['normal_perempuan'];
						$nilai='';					
					
						$sql="INSERT INTO $nmTable (nama,id_tdk,nilai,normal,normal_perempuan) VALUES ('$nama','$kode','$nilai','$normal','$normal_perempuan')";
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
					
					$sql="INSERT INTO $nmTable (nama,id_tdk,nilai,normal,normal_perempuan) VALUES ('$nama','$idTdk','$nilai','$normal','$normal_perempuan')";
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
		   $item->normal->TextBox->Columns=15;
		   $item->normal2->TextBox->Columns=15;
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
		$nilai = ucwords(trim($item->nilai->TextBox->Text));
		$normal = $item->normal->TextBox->Text;
		$normal2 = $item->normal2->TextBox->Text;
		
		$sql="UPDATE $nmTable SET nilai='$nilai',normal='$normal',normal_perempuan='$normal2' WHERE id='$id'";
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
	
	public function batalClicked($sender,$param)
    {		
		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
				
		$this->Response->Reload();				
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
			$newLabHasil->id_lab=$row['id_tdk'];
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
