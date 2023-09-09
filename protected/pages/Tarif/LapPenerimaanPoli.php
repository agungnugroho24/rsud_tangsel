<?php
class LapPenerimaanPoli extends SimakConf
{   
	private $sortExp = "tgl_visit DESC, no_trans_rwtjln";
	private $sortDir = "ASC";
	private $offset = 0;
	private $pageSize = 10;	
	
	public function onPreInit ($param)
	{
		parent::onPreInit($param);
		
		if($this->Request['mode'] == 'frame') 
			$this->setMasterClass('Application.layouts.DialogLayout');
		else
			$this->setMasterClass('Application.layouts.MainLayout');
	}
	
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('7');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		if($this->Request['mode'] == 'frame') 
		{
			$this->jdMain->Visible = false;
			//$this->keluarBtn->Visible = false;
		}
	}	
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$sql="SELECT real_name, nip, allow FROM tbd_user";
			$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
			foreach($arr as $row)
			{
				$arrApp=array();
				$var=$row['allow'];
				$arrApp = explode(',', $var);
				
				if (in_array('2', $arrApp))
				{
					$data[]=array('nip'=>$row['nip'],'nama'=>$row['real_name']);
				}	
			}
			
			$this->DDKasir->DataSource = $data;
			$this->DDKasir->dataBind();
			
			$sql = "SELECT id, nama FROM tbm_kelompok ORDER BY nama";
			$this->DDKelompok->DataSource = KelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind();
			
			$sql = "SELECT id, nama FROM tbm_perusahaan_asuransi ORDER BY nama";
			$this->DDPerus->DataSource = PerusahaanRecord::finder()->findAllBySql($sql);
			$this->DDPerus->dataBind();
			
			$sql = "SELECT id, nama FROM tbm_poliklinik ORDER BY nama";
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDPoli->dataBind();
			//$this->DDPoli->Enabled = false;
			
			$criteria->Condition = 'kelompok = :kelompok';
			$criteria->Parameters[':kelompok'] = '1';

			$this->DDDokter->DataSource = PegawaiRecord::finder()->findAll($criteria);
			$this->DDDokter->dataBind();
			//$this->DDDokter->Enabled = false;
			
			$position='Bottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		
			$this->lockPeriode();
			$this->DDberdasarkan->SelectedValue = '1';	
			$this->ChangedDDberdasarkan();
			$pilih='1';
			//$this->cetakBtn->Enabled = false;
		}
		else
		{
			$pilih = $this->getViewState('pilihPeriode');
		}
		
		if($pilih=='3')
		{
			$this->bulan->Display='Dynamic';			
			$this->prosesBtn->ValidationGroup = 'valBulan';
		}
		elseif ($pilih=='2')
		{
			$this->minggu->Display='Dynamic';
			$this->prosesBtn->ValidationGroup = 'valMinggu';
			
		}
		elseif ($pilih=='1')
		{
			$this->hari->Display='Dynamic';		
			$this->prosesBtn->ValidationGroup = 'valHari';
		}
		else
		{
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
		}
	}
		
	public function onLoad($param)
	{
		
		
		if($this->DDKelompok->SelectedValue == '')
		{
			$this->DDPerus->Enabled = false;
		}
	}
	
	public function panelCallback($sender,$param)
  {
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function lockPeriode()
  {
		$dateNow = date('d-m-Y');
		
		$this->minggu->Display='None';
		$this->hari->Display='None';
		$this->bulan->Display='None';
		
		$this->DDbulan->SelectedIndex=-1;
		$this->DDtahun->SelectedIndex=-1;
		$this->tgl->Text='';
		$this->tglawal->Text='';
		$this->tglakhir->Text='';
		$this->clearViewState('cariThn');
		$this->clearViewState('cariBln');
		
		$this->tglawal->Text = $dateNow;
		$this->tglakhir->Text = $dateNow;
		$this->tgl->Text = $dateNow;
			
		//$this->DDtahun->Enabled=false;
		
		$this->DDbulan->SelectedValue = date('m');
		$this->DDtahun->DataSource=$this->tahun();
		$this->DDtahun->dataBind();
		$this->DDtahun->SelectedValue = date('Y');
		$this->prosesBtn->ValidationGroup = '';
		
	}
	
	public function selectionChangedKelompok()
	{
		if($this->DDKelompok->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->DDPerus->SelectedValue = 'empty';
			$this->DDPerus->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07' || $this->DDKelompok->SelectedValue == '08')
			{
				$idKelPerus = $this->DDKelompok->SelectedValue;
				
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerus->DataSource = $data;
				$this->DDPerus->dataBind();
				
				$this->DDPerus->Enabled = true;	
			}
			else
			{
				$this->DDPerus->SelectedValue = 'empty';
				$this->DDPerus->Enabled = false;
			}
		}
		
		$this->prosesPilihGrid();
	}
	

//----------------------------------------------------------- GRID BERDASARKAN POLI ---------------------------------
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
			
			$sql = "SELECT * FROM view_lap_poli WHERE no_trans_rwtjln IS NOT NULL ";
			
			/*if($this->DDjnsTrans->SelectedValue == '2')
			{
				$sql .= "AND jns_pasien = 'non otc' ";
			}
						
			if($this->DDKasir->SelectedValue != '')
			{
				$kasir = $this->DDKasir->SelectedValue;
				$sql .= "AND kasir = '$kasir' ";
			}*/
			
			if($this->DDPoli->SelectedValue != '')
			{
				$poli = $this->DDPoli->SelectedValue;
				$sql .= "AND id_klinik = '$poli' ";
			}
			
			if($this->DDDokter->SelectedValue != '')
			{
				$dokter = $this->DDDokter->SelectedValue;
				$sql .= "AND id_dokter = '$dokter' ";
			}
			
			if($this->DDKelompok->SelectedValue != '')
			{
				$DDKelompok = $this->DDKelompok->SelectedValue;
				$sql .= "AND id_penjamin = '$DDKelompok' ";
			}
			
			if($this->DDPerus->SelectedValue != '')
			{
				$DDPerus = $this->DDPerus->SelectedValue;
				$sql .= "AND id_perus = '$DDPerus' ";
			}
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND tgl_visit = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND tgl_visit BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (tgl_visit)='$cariBln' AND YEAR(tgl_visit)='$cariThn' ";
				
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}		
			}
			else
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			
			//$sql .= "GROUP BY tgl ";
			
			$this->setViewState('sql',$sql);
			foreach($this->queryAction($sql,'S') as $row)
			{
				$jmlTotal += $row['jml_total'];
			}
			
			$this->jmlTotal->Text = number_format($jmlTotal,2,',','.');
			
			$sqlGroup = $sql.' GROUP BY no_trans_rwtjln';
			$data = $someDataList->getSomeDataPage($sql);
			$this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sqlGroup);    			
			$this->dtgSomeData->DataSource = $data;
			$this->dtgSomeData->DataBind();
			//$this->showSql->Text=$sql;
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
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
		$index = $item->ItemIndex;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
    {
			$no_trans_rwtjln = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$cm = $item->DataItem['cm'];
			
			$tgl_visit = $item->DataItem['tgl_visit'];
			
			$tglLahir = PasienRecord::finder()->findByPk($cm)->tgl_lahir;			
			$umur = $this->dateDifference($tglLahir,$item->DataItem['tgl_visit']);
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
			
			if($index > 0)
				$no_trans_rwtjln2 = $this->dtgSomeData->DataKeys[$item->ItemIndex - 1];
			else
				$no_trans_rwtjln2 = $no_trans_rwtjln;
			
			if($no_trans_rwtjln != $no_trans_rwtjln2 || $index == 0)
			{
				if($this->getViewState('no_trans_rwtjln_last') != $no_trans_rwtjln)
				{
					$item->tglColumn->Text = $this->convertDate($tgl_visit,'1');	
					$item->cmColumn->Text = $item->DataItem['cm'];
					$item->namaColumn->Text = $item->DataItem['nama'];				
					$item->umurColumn->Text = $umur;
					$item->penjaminColumn->Text = $item->DataItem['penjamin'];
					//$item->noColumn->Text = number_format($index+1+$this->offset,0,',','.');
					
					if($index=='0')
					{
						$no = 1;
						$this->setViewState('no',$no);	
					}
					else
					{
						$no = $this->getViewState('no')+1;
						$this->setViewState('no',$no);	
					}
					
					$item->noColumn->Text = number_format($no,0,',','.');
					
					/*$sql = "SELECT kode_icd, st_icd_global FROM tbm_icd WHERE no_trans = '$no_trans_rwtjln' ";
					if($this->queryAction($sql,'S'))
					{
						$diagnosa = '';
						$jml = count($this->queryAction($sql,'S'));
						$i=1;
						foreach($this->queryAction($sql,'S') as $row)
						{
							$st = $row['st_icd_global'];
							$kode = $row['kode_icd'];
							
							if($jml==$i)
								$koma = '';
							else
								$koma = ', ';
								
							if($st == '1')
								$diagnosa .= IcdGlobalRecord::finder()->findByPk($kode)->indonesia.$koma;
							else
								$diagnosa .= IcdRecord::finder()->findByPk($kode)->indonesia.$koma;
								
							$i++;	
						}
						
						$item->diagnosaColumn->Text = $diagnosa;
					}*/
				}
				
				$this->clearViewState('no_trans_rwtjln_last');
			}
			else
			{
				$no = $this->getViewState('no');
				$this->setViewState('no',$no);		
			}
			
			$item->tarifColumn->Text = number_format($item->DataItem['jml_total'],2,',','.');
			//$item->operatorColumn->Text = UserRecord::finder()->find('nip=?',$id_kasir)->real_name;		
			
			if($index+1 == $this->dtgSomeData->PageSize)
			{
				//$item->noColumn->Text = 'tes';
				$no_trans_rwtjln_last = $no_trans_rwtjln;
				$this->setViewState('no_trans_rwtjln_last',$no_trans_rwtjln);
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
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key
				
				KasirRwtJlnRecord::finder()->deleteByPk($ID);	
				$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
				
			}	
    }	




//----------------------------------------------------------- GRID BERDASARKAN CARA BAYAR ---------------------------------
	private function bindGridCaraBayar()
    {
        $this->pageSize = $this->gridCaraBayar->PageSize;
        $this->offset = $this->pageSize * $this->gridCaraBayar->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$sql = "SELECT 
					  tbt_kasir_rwtjln_angsuran.tipe_pasien,
					  tbt_kasir_rwtjln_angsuran.cm,
					  tbt_kasir_rwtjln_angsuran.no_trans,
					  tbt_kasir_rwtjln_angsuran.no_trans_asal,
					  tbt_kasir_rwtjln_angsuran.tgl,
					  sum(if((tbt_kasir_rwtjln_angsuran.st_carabayar = '1'), tbt_kasir_rwtjln_angsuran.tarif, 0)) AS jml_tunai,
					  sum(if((tbt_kasir_rwtjln_angsuran.st_carabayar = '2'), tbt_kasir_rwtjln_angsuran.tarif, 0)) AS jml_debitcard,
					  sum(if((tbt_kasir_rwtjln_angsuran.st_carabayar = '3'), tbt_kasir_rwtjln_angsuran.tarif, 0)) AS jml_creditcard,
					  sum(if((tbt_kasir_rwtjln_angsuran.st_carabayar = '4'), tbt_kasir_rwtjln_angsuran.tarif, 0)) AS jml_jamper,
					  sum(if((tbt_kasir_rwtjln_angsuran.st_carabayar = '5'), tbt_kasir_rwtjln_angsuran.tarif, 0)) AS jml_nv,
					  tbt_rawat_jalan.id_klinik,
					  tbm_poliklinik.nama AS nm_poli,
					  tbt_rawat_jalan.dokter,
					  tbd_pegawai.nama AS nm_dokter
					FROM
					  tbt_kasir_rwtjln_angsuran
					  LEFT OUTER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln_angsuran.no_trans_asal = tbt_rawat_jalan.no_trans)
					  LEFT JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					  LEFT JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)					
					WHERE tbt_kasir_rwtjln_angsuran.tipe_pasien <> '1'  ";
			
			if($this->DDjnsTrans->SelectedValue == '2')
			{
				$sql .= "AND tipe_pasien = '0' ";
			}
						
			if($this->DDKasir->SelectedValue != '')
			{
				$kasir = $this->DDKasir->SelectedValue;
				$sql .= "AND operator = '$kasir' ";
			}
			
			if($this->DDPoli->SelectedValue != '')
			{
				$poli = $this->DDPoli->SelectedValue;
				$sql .= "AND id_klinik = '$poli' ";
			}
			
			if($this->DDDokter->SelectedValue != '')
			{
				$dokter = $this->DDDokter->SelectedValue;
				$sql .= "AND dokter = '$dokter' ";
			}
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND tgl = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (tgl)='$cariBln' AND YEAR(tgl)='$cariThn' ";
				
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}		
			}
			else
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			
			$sql .= "GROUP BY tbt_kasir_rwtjln_angsuran.no_trans_asal ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridCaraBayar->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->gridCaraBayar->DataSource = $data;
            $this->gridCaraBayar->DataBind();
	    	$this->showSql->Text=$sql;
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridCaraBayar->DataSource = $session["SomeData"];
            $this->gridCaraBayar->DataBind();
        }
    }
    
    // ---------- datagrid page and sort events ---------------
    
    protected function gridCaraBayar_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->gridCaraBayar->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridCaraBayar();
    }

    protected function gridCaraBayar_SortCommand($sender,$param)
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
        
        $this->gridCaraBayar->CurrentPageIndex = 0;
        $this->bindGridCaraBayar();

    }
	
	public function gridCaraBayar_itemCreated($sender,$param)
    {
		$item=$param->Item;	
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$no_trans = $this->gridCaraBayar->DataKeys[$item->ItemIndex];
			
			$cm = $item->DataItem['cm'];
			$noTransAsal = $item->DataItem['no_trans_asal'];
			$tgl = $item->DataItem['tgl'];
			
			$item->tglColumn2->tglTxt2->Text = $this->convertDate($tgl,'3');
			
			if( $item->DataItem['tipe_pasien'] == '0')//rawat jalan
			{
				$item->nmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
				$item->poli->Text = $item->DataItem['nm_poli'];
				$item->dokter->Text = $item->DataItem['nm_dokter'];
			}
			elseif( $item->DataItem['tipe_pasien'] == '2' || $item->DataItem['tipe_pasien'] == '3')//umum / umum karyawan
			{
				$item->nmPas->Text = PasienLuarRecord::finder()->findByPk($noTransAsal)->nama;
				$item->poli->Text = '-';
				$item->dokter->Text = '-';
			}
			
			$item->jml1->Text =number_format($item->DataItem['jml_tunai'],0,',','.');
			$item->jml2->Text =number_format($item->DataItem['jml_debitcard'],0,',','.');
			$item->jml3->Text =number_format($item->DataItem['jml_creditcard'],0,',','.');
			$item->jml4->Text =number_format($item->DataItem['jml_jamper'],0,',','.');
			$item->jml5->Text =number_format($item->DataItem['jml_nv'],0,',','.');
        } 	
    }
	
	
	
	
	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$session = $this->getSession();
			$session->remove("SomeData"); 
			
			if($this->jnsLap->SelectedValue == '1') //berdasarkan poli
			{
				$this->dtgSomeData->Display='Dynamic';
				$this->gridCaraBayar->Display='None';
				
				$this->dtgSomeData->CurrentPageIndex = 0;
				$this->bindGrid();				
			}       
			else
			{
				$this->dtgSomeData->Display='None';
				$this->gridCaraBayar->Display='Dynamic';
				
				$this->gridCaraBayar->CurrentPageIndex = 0;
				$this->bindGridCaraBayar();	
			}
			
			$this->gridPanel->Display='Dynamic';
		}
		else
		{
			$this->gridPanel->Display='None';
		}
		
		if(!$this->getViewState('pilihPeriode'))
		{
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
		}
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	
	public function prosesPilihGrid()
	{			
		$this->clearViewState('no_trans_rwtjln_last');
		
		$position='Bottom'; 
		$this->dtgSomeData->PagerStyle->Position=$position;
		$this->gridCaraBayar->PagerStyle->Position=$position;
				
		$session = $this->getSession();
		$session->remove("SomeData");
				
		if($this->jnsLap->SelectedValue == '1') //berdasarkan poli
		{			       
			
			$this->dtgSomeData->PagerStyle->Visible=true;
			$this->gridCaraBayar->PagerStyle->Visible=false;
			
			$this->dtgSomeData->CurrentPageIndex = 0;
			$this->bindGrid();
		}
		elseif($this->jnsLap->SelectedValue == '2') //berdasarkan carabayar
		{
			$this->gridCaraBayar->PagerStyle->Visible=true;
			$this->dtgSomeData->PagerStyle->Visible=false;
				
			$this->gridCaraBayar->CurrentPageIndex = 0;
			$this->bindGridCaraBayar();
		}		
	}
	
	public function cariClicked()
	{	
		$this->prosesPilihGrid();
	}
	
	public function DDjnsTransChanged($sender,$param)
	{	
		$criteria = new TActiveRecordCriteria;												
		$criteria->OrdersBy['nama'] = 'asc';
				
		$sql = "SELECT id, nama FROM tbm_poliklinik ORDER BY nama";
		$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
		$this->DDPoli->dataBind();
		
		$criteria->Condition = 'kelompok = :kelompok';
		$criteria->Parameters[':kelompok'] = '1';
			
		$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
		$this->DDDokter->dataBind();
					
		if($this->DDjnsTrans->SelectedValue == 1)
		{	
			$this->DDPoli->Enabled = false;			
			$this->DDDokter->Enabled = false;
		}
		else
		{
			$this->DDPoli->Enabled = true;			
			$this->DDDokter->Enabled = true;
		}
		
		$this->prosesPilihGrid();
	}
		
	public function DDKasirChanged($sender,$param)
	{	
		$this->prosesPilihGrid();
	}
	
	public function DDPoliChanged($sender,$param)
	{			
		$idKlinik = $this->DDPoli->SelectedValue;
		
		if($idKlinik != '')
		{
			if($this->dokterList($idKlinik))
			{
				$this->DDDokter->Enabled=true;
				$this->DDDokter->DataSource = $this->dokterList($idKlinik);
			  $this->DDDokter->dataBind();
			}	
		}
		
		$this->prosesPilihGrid();
	}
	
	public function DDDokterChanged($sender,$param)
	{		
		$this->prosesPilihGrid();
	}
	
	public function ChangedDDberdasarkan()
	{		
		$this->prosesPilihGrid();
			
		$this->lockPeriode();
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->DDbulan);
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->tglawal);		
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);		
		}
		else
		{
			$this->clearViewState('pilihPeriode');
			$this->Page->CallbackClient->focus($this->DDberdasarkan);
			$this->Page->CallbackClient->focus($this->tgl);	
		}
		
		$this->bindGrid();
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			$this->Page->CallbackClient->focus($this->DDbulan);
		}			
		else
		{
			$this->Page->CallbackClient->focus($this->DDtahun);
		}
		
		$this->prosesPilihGrid();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$this->prosesPilihGrid();
	}
		
	public function checkTgl($sender,$param)
	{
		$pecahTglAwal=explode('-',$this->tglawal->Text);
		$pecahTglAkhir=explode('-',$this->tglakhir->Text);
		$tglAwal=$pecahTglAwal['0'];
		$cariBln=$pecahTglAwal['1'];
		$thnAwal=$pecahTglAwal['2'];
		$tglAkhir=$pecahTglAkhir['0'];
		$cariThn=$pecahTglAkhir['1'];
		$thnAkhir=$pecahTglAkhir['2'];
		
		if($thnAkhir<$thnAwal) 
		{
			$hasil='0';
		}
		else
		{
			if($cariThn<$cariBln) 
			{
				$hasil='0';
			}
			else
			{
				if($tglAkhir<$tglAwal) 
				{
					$hasil='0';
				}
				else
				{
					//jika tgl akhir benar
					//$id_ijin=$this->getViewState('id');
					//$this->Response->redirect($this->Service->constructUrl('Lap'.$id_ijin,array('idIjin'=>$id_ijin)));
					$hasil='1';
				}
			}
		}	
		
		$param->IsValid=($hasil==='1');
	}
	
	public function cetakClicked()
	{		
		$session=new THttpSession;
		$session->open();
		$session['cetakLapPenerimaanPoli'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanPoliXls',
			array(
				'poli'=>$this->DDPoli->SelectedValue,
				'dokter'=>$this->DDDokter->SelectedValue,
				'penjamin'=>$this->DDKelompok->SelectedValue,
				'perus'=>$this->DDPerus->SelectedValue,
				'periode'=>$this->txtPeriode->Text)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
