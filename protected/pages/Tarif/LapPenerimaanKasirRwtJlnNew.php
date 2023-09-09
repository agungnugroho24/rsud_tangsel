<?php
class LapPenerimaanKasirRwtJlnNew extends SimakConf
{   
	private $sortExp = "no_trans";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
				
		$pilih = $this->getViewState('pilihPeriode');
		if ($pilih=='3')
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
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			$this->DDPoli->Enabled = false;
			
			$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled = false;
			
			$this->lockPeriode();
					
			//$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			$this->cetakBtn->Enabled = false;
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
			
			$sql = "SELECT * FROM view_penerimaan_rwtjln_new WHERE no_trans <> '' ";
			
			if($this->DDjnsTrans->SelectedValue == '2')
			{
				$sql .= "AND jns_pasien = 'non otc' ";
			}
						
			if($this->DDKasir->SelectedValue != '')
			{
				$kasir = $this->DDKasir->SelectedValue;
				$sql .= "AND kasir = '$kasir' ";
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
			
			$sql .= "GROUP BY no_trans";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
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
		
		 
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$no_trans = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$jns_pasien = $item->DataItem['jns_pasien'];			
			$tgl_visit = $item->DataItem['tgl'];	
			$id_kasir = $item->DataItem['kasir'];	
			$id_klinik = $item->DataItem['id_klinik'];	
			$id_dokter = $item->DataItem['dokter'];	
			
			$item->tglColumn->Text = $this->convertDate($tgl_visit,'3');
			$item->operatorColumn->Text = UserRecord::finder()->find('nip=?',$id_kasir)->real_name;			
			
			if($jns_pasien == 'non otc') //pasen rwt jalan biasa
			{					
				//----------- ADM ----------// NON OTC
				$sql = "select 
							sum(tbt_kasir_rwtjln.total) AS total
						  from 
							(tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) 
						  where 
							(tbm_nama_tindakan.nama like '%pendaftaran%') 
							AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'AND tbt_kasir_rwtjln.st_flag='1' ";
				$adm = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
				$item->admColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');			
				 
				
				//----------- POLI ----------// NON OTC
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='01'";

				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliDalamColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='02'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliKandunganColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='03'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliBedahColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='04'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliAnakColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='05'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliThtColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='06'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliGimulColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='07'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliIgdColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='08'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliUmumColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
						AND tbt_kasir_rwtjln.st_flag='1'
						AND tbt_kasir_rwtjln.klinik='19'";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;		
				$item->poliKosmetikColumn->Text = number_format(KasirRwtJlnRecord::finder()->findBySql($sql)->total,0,',','.');				
				
				//----------- OBAT ----------// NON OTC
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalObat = ObatJualRecord::finder()->findBySql($sql)->total;
				
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_karyawan WHERE no_trans_rwtjln='$no_trans' AND flag='1'";
				$totalObat += ObatJualKaryawanRecord::finder()->findBySql($sql)->total;
				
				$item->obatColumn->Text = number_format($totalObat,0,',','.');
				
				//----------- LAB ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_lab_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalLab = LabJualRecord::finder()->findBySql($sql)->harga;
							
				$item->labColumn->Text = number_format($totalLab,0,',','.');
				
				//----------- RAD ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_rad_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalRad = RadJualRecord::finder()->findBySql($sql)->harga;
							
				$item->radColumn->Text = number_format($totalRad,0,',','.');
				
				//----------- FISIO ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_fisio_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalFisio = FisioJualRecord::finder()->findBySql($sql)->harga;
							
				$item->fisioColumn->Text = number_format($totalFisio,0,',','.');
				
				
				$total = $adm + $poli + $totalObat + $totalLab + $totalRad + $totalFisio; 
				$item->totalColumn->Text = number_format($total,0,',','.');
			}			
			elseif($jns_pasien == 'otc') //pasen rwt jalan bebas
			{
				//----------- OBAT ----------//  OTC
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalObat = ObatJualLainRecord::finder()->findBySql($sql)->total;
				
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain_karyawan WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalObat += ObatJualLainKaryawanRecord::finder()->findBySql($sql)->total;
				
				$item->obatColumn->Text = number_format($totalObat,0,',','.');
				
				//----------- LAB ----------//  OTC
				$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_lab_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalLab = LabJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
				$admLab = LabJualLainRecord::finder()->findBySql($sql)->harga_adm;
							
				$item->labColumn->Text = number_format($totalLab,0,',','.');
				
				//----------- RAD ----------//  OTC
				$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_rad_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1'";
				$totalRad = RadJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
				$admRad = RadJualLainRecord::finder()->findBySql($sql)->harga_adm;
							
				$item->radColumn->Text = number_format($totalRad,0,',','.');
				
				//----------- FISIO ----------//  OTC
				$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_fisio_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
				$admFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga_adm;
							
				$item->fisioColumn->Text = number_format($totalFisio,0,',','.');
				
				//----------- ADM ----------//  OTC
				$item->admColumn->Text = number_format($admLab+$admRad+$admFisio,0,',','.');
				
				//----------- POLI ----------// NON OTC
				$item->poliDalamColumn->Text = number_format('0',0,',','.');
				
				$total = $admLab+$admRad+$admFisio + $totalObat + $totalLab + $totalRad + $totalFisio; 
				$item->totalColumn->Text = number_format($total,0,',','.');
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

	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$session = $this->getSession();
			$session->remove("SomeData");        
			$this->dtgSomeData->CurrentPageIndex = 0;
			$this->bindGrid();
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
	
	
	public function cariClicked()
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
		
	}
	
	public function DDjnsTransChanged($sender,$param)
	{	
		$criteria = new TActiveRecordCriteria;												
		$criteria->OrdersBy['nama'] = 'asc';
				
		$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
		$this->DDPoli->dataBind();	
		
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
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
		
	public function DDKasirChanged($sender,$param)
	{	
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function DDPoliChanged($sender,$param)
	{			
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function DDDokterChanged($sender,$param)
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function ChangedDDberdasarkan($sender,$param)
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
			
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
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
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
		$session['cetakPenerimaanKasirRwtJln'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtJlnNew',
			array(
				'kasir'=>$this->DDKasir->SelectedValue,
				'poli'=>$this->DDPoli->SelectedValue,
				'dokter'=>$this->DDDokter->SelectedValue,
				'periode'=>$this->txtPeriode->Text,
				'jnsTrans'=>$this->DDjnsTrans->SelectedValue)));
	}
				
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
