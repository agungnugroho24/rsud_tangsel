<?php
class penjualanObatTunda extends SimakConf
{  	
	private $sortExp = "tgl";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")
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
			$this->DDtahun->DataSource = $this->dataTahun(date('Y')-10,date('Y')+10);
			$this->DDtahun->dataBind();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			$this->DDPoli->Enabled = false;
			
			$criteria->Condition = 'kelompok = :kelompok';
			$criteria->Parameters[':kelompok'] = '1';

			$this->DDDokter->DataSource = PegawaiRecord::finder()->findAll($criteria);
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled = false;
			
			$this->lockPeriode();
			$this->cetakBtn->Enabled = false;
			
			$this->DDberdasarkan->SelectedValue = '3';
			$this->setViewState('pilihPeriode',$pilih);
			$this->ChangedDDberdasarkan();
			
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
		$this->DDtahun->DataSource = $this->dataTahun(date('Y')-10,date('Y')+10);
		$this->DDtahun->dataBind();
		$this->DDtahun->SelectedValue = date('Y');
		$this->prosesBtn->ValidationGroup = '';
		
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
			
			$sql = "SELECT
						id,
						jns_pasien,
						IF(jns_pasien='0','Rawat Jalan',IF(jns_pasien='1' OR jns_pasien='3','Rawat Inap',IF(jns_pasien='2','Pasien Luar/OTC','Unit Internal'))) AS jns_pas_detail,
						nama,
						IF(jns_pasien='2' OR jns_pasien='4','-',cm) AS cm,
						SUM(total) AS jml_tagihan,
						tgl,
						petugas_internal
					FROM tbt_obat_jual_tunda WHERE st = '0' ";
			
			if($this->modeInput->SelectedValue != '')
			{
				$modeInput = $this->modeInput->SelectedValue;
				$sql .= "AND jns_pasien = '$modeInput' ";
			}
			
			if($this->formatCm($this->cariCM->Text) != '')
			{
				$cariCM = $this->formatCm($this->cariCM->Text);
				$sql .= "AND cm LIKE '%$cariCM%' ";
			}
			
			if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND nama LIKE '$nama%' ";
				}
			}
							
			if($this->DDPoli->SelectedValue != '')
			{
				$poli = $this->DDPoli->SelectedValue;
				$sql .= "AND klinik = '$poli' ";
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
			
			$sql .= "GROUP BY jns_pasien, no_trans_rawat, petugas_internal ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
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
		$this->dtgSomeData->PagerStyle->Position=$position;
		$this->dtgSomeData->PagerStyle->Visible=true;		
	}
	
	public function dtgSomeData_ItemCreated($sender,$param)
    {
		$item=$param->Item;	
		
		 
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			//$no_trans = $this->dtgSomeData->DataKeys[$item->ItemIndex];			
			$jnsPas = $item->DataItem['jns_pasien'];
			
			$item->tglColoumn->Text = $this->convertDate($item->DataItem['tgl'],'3');
			$item->jmlTagihanColoumn->Text = number_format($item->DataItem['jml_tagihan'],'2',',','.');			
			
			if($jnsPas == '4') //unit internal
				$item->nmColoumn->Text = UserRecord::finder()->find('nip=?',$item->DataItem['petugas_internal'])->real_name;
			else
				$item->nmColoumn->Text = $item->DataItem['nama'];	
			
			
        } 	
    }
	
    public function deleteBtnClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
		{
			$item=$param->Item;
			
			//$ID = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$ID = $sender->CommandParameter;
			
			$jnsPasien = ObatJualTundaRecord::finder()->findByPk($ID)->jns_pasien;	
			$noTransRawat = ObatJualTundaRecord::finder()->findByPk($ID)->no_trans_rawat;
			$petugasInternal = ObatJualTundaRecord::finder()->findByPk($ID)->petugas_internal;
					
			if($jnsPasien != '4')
			{
				$sql = "DELETE FROM tbt_obat_jual_tunda WHERE jns_pasien='$jnsPasien' AND no_trans_rawat = '$noTransRawat' ";
				$this->queryAction($sql,'C');
				
				if($jnsPasien == '2')//Pasien Luar
				{
					//DELETE pasien luar di tbd_pasien_luar	
					$sql = "DELETE FROM tbd_pasien_luar WHERE no_trans = '$noTransRawat' ";
					$this->queryAction($sql,'C');
				}	
			}	
			else
			{
				$sql = "DELETE FROM tbt_obat_jual_tunda WHERE jns_pasien='$jnsPasien' AND petugas_internal = '$petugasInternal' ";
				$this->queryAction($sql,'C');
			}
			//$this->tes->Text = $ID;
			$this->cariClicked();
		}	
    }	
	
	public function detailBtnClicked($sender,$param)
	{
		$id = $sender->CommandParameter;
		
		$jns_pasien = ObatJualTundaRecord::finder()->findByPk($id)->jns_pasien;
		$no_trans_rawat = ObatJualTundaRecord::finder()->findByPk($id)->no_trans_rawat;
		$petugas_internal = ObatJualTundaRecord::finder()->findByPk($id)->petugas_internal;
		
		$url = "index.php?page=Apotik.detailObatJualTundaModal&id=".$id."&jns_pasien=".$jns_pasien."&no_trans_rawat=".$no_trans_rawat."&petugas_internal=".$petugas_internal;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'RINCIAN PEMBELIAN OBAT / ALKES')");		
	}	
	
	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$session = $this->getSession();
			$session->remove("SomeData"); 
			
			$this->dtgSomeData->Display='Dynamic';
				
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
	
	public function normalHargaApotikLuar($idObat,$expired)
	{
		//NORMALKAN HARGA OBAT YG DIBELI DARI APOTIK LUAR SESUAI DENGAN HARGA TERAKHIR OBAT YG DIBELI DARI PBF
		$sql = "SELECT * FROM tbt_obat_harga WHERE kode = '$idObat' AND st_pembelian = '1' ORDER BY id DESC";
		$arr = $this->queryAction($sql,'S');
		
		if($arr)
		{	
			foreach($arr as $row)
			{
				$idHargaApotik = $row['id'];
			}	
		}
		
		
		$sql = "SELECT * FROM tbt_stok_lain WHERE id_obat = '$idObat' AND id_harga = '$idHargaApotik' AND expired = '$expired' ORDER BY id_harga DESC";
		$arr = $this->queryAction($sql,'S');
		
		if($arr)
		{	
			foreach($arr as $row)
			{
				$jmlTambahan = $row['jumlah'];
				$tujuan = $row['tujuan'];
				
				$sql2 = "SELECT * FROM tbt_stok_lain WHERE id_obat = '$idObat' AND id_harga != '$idHargaApotik' AND tujuan = '$tujuan' AND expired = '$expired' ORDER BY id_harga DESC";
				$arr2 = $this->queryAction($sql2,'S');
				
				if($arr2)
				{	
					foreach($arr2 as $row2)
					{
						$idHarga = $row2['id_harga'];
						$jmlAwal = $row2['jumlah'];
						$jmlAkhir = $jmlAwal + $jmlTambahan;
						
						$sqlUpdate = "UPDATE tbt_stok_lain SET jumlah = '$jmlAkhir' WHERE id_obat = '$idObat' AND id_harga = '$idHarga' AND tujuan = '$tujuan' AND expired = '$expired'";
						$this->queryAction($sqlUpdate,'C');
						
						$sqlUpdate = "UPDATE tbt_stok_lain SET jumlah = '0' WHERE id_obat = '$idObat' AND id_harga = '$idHargaApotik' AND tujuan = '$tujuan' AND expired = '$expired'";
						$this->queryAction($sqlUpdate,'C');
					}	
				}
			}	
		}
	}
	
	public function prosesModalDetail($sender,$param)
	{
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		$this->Page->CallbackClient->focus($this->cariCM);
	}
	
	public function prosesModalCetak($sender,$param)
	{
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;
			
		$Id = $param->CallbackParameter->Id;
		$jnsPasien = $param->CallbackParameter->jnsPasien;
		$noTransRawat = $param->CallbackParameter->noTransRawat;
		$petugasInternal = $param->CallbackParameter->petugasInternal;		
		
		
		$sql = "SELECT * FROM tbt_obat_jual_tunda WHERE jns_pasien='$jnsPasien' ";
					
		if($jnsPasien != '4')
			$sql .= " AND no_trans_rawat = '$noTransRawat' ";
		elseif($jnsPasien == '4')
			$sql .= " AND petugas_internal = '$petugasInternal' ";	
		
		$sql .= " AND st = '0' ";
		
		$arr = $this->queryAction($sql,'S');	
		
		if($jnsPasien == '0' ) //Pasien Rawat Jalan
		{	
			$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTransRawat)->penjamin;
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransRawat)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
				$noReg = $this->numRegister('tbt_obat_jual_karyawan',ObatJualKaryawanRecord::finder(),'04');
			else
				$noReg = $this->numRegister('tbt_obat_jual',ObatJualRecord::finder(),'04');
		}
		elseif($jnsPasien == '1' || $jnsPasien == '3') //Pasien Rawat Inap
		{			
			$kelompokPasien = RwtInapRecord::finder()->findByPk($noTransRawat)->penjamin;
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTransRawat)->st_asuransi;				
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //karyawan
				$noReg = $this->numRegister('tbt_obat_jual_inap_karyawan',ObatJualInapKaryawanRecord::finder(),'10');
			else
				$noReg = $this->numRegister('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
		}		
		elseif($jnsPasien == '4' ) //Unit Internal
		{
			$noReg = $this->numRegister('tbt_obat_jual_internal',ObatJualInternalRecord::finder(),'43');
		}
		else //Pasien Rawat Lain
		{
			if($petugasInternal != '')//pasien luar berstatus karyawan
				$noReg = $this->numRegister('tbt_obat_jual_lain_karyawan',ObatJualLainKaryawanRecord::finder(),'41');
			else //Pasien Luar berstatus Umum
				$noReg = $this->numRegister('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
		}
		
		foreach($arr as $row)
		{
			$id = $row['id'];
			$cm = $row['cm'];
			$dokter = $row['dokter'];
			$st_bayar = $row['st_bayar'];
			$jmlTagihan = $row['jml_tagihan'];
			$nama = $row['nama'];
			$ruangan = $row['ruangan'];
			$sumber = $row['sumber'];
			
			if($ruangan=='1')
				$nmRuang = 'OK';
			elseif($ruangan=='2')
				$nmRuang = 'VK';
			elseif($ruangan=='3')
				$nmRuang = 'UGD';
						
			$petugas = UserRecord::finder()->find('nip=?',$row['petugas_internal'])->real_name;
			
			if($jnsPasien == '0' ) //Pasien Rawat Jalan
			{		
				$dateNow = date('Y-m-d');	
				
				if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_karyawan',ObatJualKaryawanRecord::finder(),'04');
					$ObatJual= new ObatJualKaryawanRecord();
				}
				else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
					$ObatJual= new ObatJualRecord();
				}
				
				//if(($kelompokPasien == '02' || $kelompokPasien == '07') && $stAsuransi == '1')//Asuransi/jamper berlaku
					$jmlTotal = $row['total'];	
				//else
					//$jmlTotal = $this->bulatkan($row['total']);
				
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND expired = ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jumlah'],$row['expired']))
				{	
					$ObatJual->no_trans = $notransTmp;
					$ObatJual->no_trans_rwtjln = $noTransRawat;						
					$ObatJual->cm = $row['cm'];
					$ObatJual->no_reg = $noReg;
					$ObatJual->dokter=$row['dokter'];
					$ObatJual->sumber=$row['sumber'];
					$ObatJual->tujuan=$row['tujuan'];
					$ObatJual->klinik = $row['klinik'];
					$ObatJual->id_obat=$row['id_obat'];
					$ObatJual->id_harga=$row['id_harga'];
					$ObatJual->tgl=date('Y-m-d');
					$ObatJual->wkt=date('G:i:s');
					$ObatJual->operator=$operator;
					$ObatJual->hrg_nett=$row['hrg_nett'];
					$ObatJual->hrg_ppn=$row['hrg_ppn'];
					$ObatJual->hrg_nett_disc=$row['hrg_nett_disc'];
					$ObatJual->hrg_ppn_disc=$row['hrg_ppn_disc'];
					$ObatJual->hrg=$row['hrg'];
					$ObatJual->jumlah=$row['jumlah'];
					$ObatJual->total=$jmlTotal;
					$ObatJual->total_real=$row['total_real'];
					$ObatJual->flag='0';
					$ObatJual->st_obat_khusus=$row['st_obat_khusus'];
					$ObatJual->st_racik=$row['st_racik'];
					$ObatJual->id_kel_racik=$row['id_kel_racik'];
					$ObatJual->r_item=$row['r_item'];
					$ObatJual->r_racik=$row['r_racik'];
					$ObatJual->bungkus_racik=$row['bungkus_racik'];
					$ObatJual->id_kemasan=$row['id_kemasan'];
					$ObatJual->st_imunisasi=$row['st_imunisasi'];
					$ObatJual->id_kel_imunisasi=$row['id_kel_imunisasi'];
					$ObatJual->id_bhp=$row['id_bhp'];
					$ObatJual->bhp=$row['bhp'];
					$ObatJual->expired=$row['expired'];
					$ObatJual->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
					$ObatJual->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jumlah'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
					
					$this->normalHargaApotikLuar($row['id_obat'],$row['expired']);
				}	
			}
			elseif($jnsPasien == '1' || $jnsPasien == '3') //Pasien Rawat Inap
			{		
				if($kelompokPasien == '04' && $stAsuransi == '1') //karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_inap_karyawan',ObatJualInapKaryawanRecord::finder(),'10');	
					$ObatJualInap= new ObatJualInapKaryawanRecord();
				}
				else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
					$ObatJualInap= new ObatJualInapRecord();
				}
				
				//if(($kelompokPasien == '02' || $kelompokPasien == '07') && $stAsuransi == '1')//Asuransi/jamper berlaku
					$jmlTotal = $row['total'];
				//else
					//$jmlTotal = $this->bulatkan($row['total']);
				
				if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ? AND expired = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jumlah'],$row['tujuan'],$row['expired']))
				{						
					$ObatJualInap->no_trans=$notransTmp;
					$ObatJualInap->no_trans_inap=$noTransRawat;					
					$ObatJualInap->cm=$row['cm'];
					$ObatJualInap->no_reg=$noReg;	
					$ObatJualInap->dokter=$row['dokter'];
					$ObatJualInap->sumber=$row['sumber'];
					$ObatJualInap->tujuan=$row['tujuan'];
					$ObatJualInap->id_obat=$row['id_obat'];
					$ObatJualInap->id_harga=$row['id_harga'];
					$ObatJualInap->tgl=date('Y-m-d');
					$ObatJualInap->wkt=date('G:i:s');
					$ObatJualInap->operator=$operator;
					$ObatJualInap->hrg_nett=$row['hrg_nett'];
					$ObatJualInap->hrg_ppn=$row['hrg_ppn'];
					$ObatJualInap->hrg_nett_disc=$row['hrg_nett_disc'];
					$ObatJualInap->hrg_ppn_disc=$row['hrg_ppn_disc'];
					$ObatJualInap->hrg=$row['hrg'];
					$ObatJualInap->jumlah=$row['jumlah'];
					$ObatJualInap->total=$jmlTotal;
					$ObatJualInap->total_real=$row['total_real'];
					$ObatJualInap->flag='0';
					$ObatJualInap->st_obat_khusus=$row['st_obat_khusus'];
					$ObatJualInap->st_racik=$row['st_racik'];
					$ObatJualInap->id_kel_racik=$row['id_kel_racik'];
					$ObatJualInap->r_item=$row['r_item'];
					$ObatJualInap->r_racik=$row['r_racik'];
					$ObatJualInap->bungkus_racik=$row['bungkus_racik'];
					$ObatJualInap->id_kemasan=$row['id_kemasan'];
					$ObatJualInap->st_imunisasi=$row['st_imunisasi'];
					$ObatJualInap->id_kel_imunisasi=$row['id_kel_imunisasi'];
					$ObatJualInap->id_bhp=$row['id_bhp'];
					$ObatJualInap->bhp=$row['bhp'];
					$ObatJualInap->expired=$row['expired'];
					$ObatJualInap->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
					$ObatJualInap->st_bayar=$row['st_bayar'];
					
					$ObatJualInap->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jumlah'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
					
					$this->normalHargaApotikLuar($row['id_obat'],$row['expired']);
				}										
			}			
			elseif($jnsPasien == '4' ) //Unit Internal
			{	
				$notransTmp = $this->numCounter('tbt_obat_jual_internal',ObatJualKaryawanRecord::finder(),'43');
				$ObatJual= new ObatJualInternalRecord();
				
				$jmlTotal = $row['total'];	
				
				if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND expired = ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jumlah'],$row['expired']))
				{	
					$ObatJual->no_trans = $notransTmp;
					$ObatJual->no_trans_rwtjln = $noTransRawat;						
					$ObatJual->cm = $row['cm'];
					$ObatJual->no_reg = $noReg;
					$ObatJual->dokter=$row['dokter'];
					$ObatJual->sumber=$row['sumber'];
					$ObatJual->tujuan = $row['tujuan'];
					$ObatJual->klinik = '';
					$ObatJual->id_obat=$row['id_obat'];
					$ObatJual->id_harga=$row['id_harga'];
					$ObatJual->tgl=date('Y-m-d');
					$ObatJual->wkt=date('G:i:s');
					$ObatJual->operator=$operator;
					$ObatJual->hrg_nett=$row['hrg_nett'];
					$ObatJual->hrg_ppn=$row['hrg_ppn'];
					$ObatJual->hrg_nett_disc=$row['hrg_nett_disc'];
					$ObatJual->hrg_ppn_disc=$row['hrg_ppn_disc'];
					$ObatJual->hrg=$row['hrg'];
					$ObatJual->jumlah=$row['jumlah'];
					$ObatJual->total=$jmlTotal;
					$ObatJual->total_real=$row['total_real'];
					$ObatJual->flag='0';
					$ObatJual->st_obat_khusus=$row['st_obat_khusus'];
					$ObatJual->st_racik=$row['st_racik'];
					$ObatJual->id_kel_racik=$row['id_kel_racik'];
					$ObatJual->r_item=$row['r_item'];
					$ObatJual->r_racik=$row['r_racik'];
					$ObatJual->bungkus_racik=$row['bungkus_racik'];
					$ObatJual->id_kemasan=$row['id_kemasan'];
					$ObatJual->st_imunisasi=$row['st_imunisasi'];
					$ObatJual->id_kel_imunisasi=$row['id_kel_imunisasi'];
					$ObatJual->id_bhp=$row['id_bhp'];
					$ObatJual->bhp=$row['bhp'];
					$ObatJual->expired=$row['expired'];
					$ObatJual->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
					$ObatJual->ruangan = $row['ruangan'];
					$ObatJual->petugas = $row['petugas_internal'];
					$ObatJual->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jumlah'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
					
					$this->normalHargaApotikLuar($row['id_obat'],$row['expired']);
				}	
			}
			else //Pasien Rawat Lain
			{	
				if($petugasInternal != '')//pasien luar berstatus karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_lain_karyawan',ObatJualLainKaryawanRecord::finder(),'41');	
					$ObatJualLain= new ObatJualLainKaryawanRecord();
				}
				else//Pasien Luar berstatus Umum
				{	
					$notransTmp = $this->numCounter('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
					$ObatJualLain= new ObatJualLainRecord();
				}
				
				//$jmlTotal = $this->bulatkan($row['total']);
				$jmlTotal = $row['total'];
				
				if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ? AND expired = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jumlah'],'14',$row['expired']))
				{
					$ObatJualLain->no_trans = $notransTmp;
					$ObatJualLain->no_trans_pas_luar = $noTransRawat;
					$ObatJualLain->no_reg = $noReg;
					$ObatJualLain->dokter=$row['dokter'];
					$ObatJualLain->sumber=$row['sumber'];
					$ObatJualLain->tujuan=$row['tujuan'];
					$ObatJualLain->id_obat=$row['id_obat'];
					$ObatJualLain->id_harga=$row['id_harga'];
					$ObatJualLain->tgl=date('Y-m-d');
					$ObatJualLain->wkt=date('G:i:s');
					$ObatJualLain->operator=$operator;
					$ObatJualLain->hrg_nett=$row['hrg_nett'];
					$ObatJualLain->hrg_ppn=$row['hrg_ppn'];
					$ObatJualLain->hrg_nett_disc=$row['hrg_nett_disc'];
					$ObatJualLain->hrg_ppn_disc=$row['hrg_ppn_disc'];
					$ObatJualLain->hrg=$row['hrg'];
					$ObatJualLain->jumlah=$row['jumlah'];
					$ObatJualLain->total=$jmlTotal;
					$ObatJualLain->total_real=$row['total_real'];
					$ObatJualLain->flag='0';
					$ObatJualLain->st_obat_khusus=$row['st_obat_khusus'];
					$ObatJualLain->st_racik=$row['st_racik'];
					$ObatJualLain->id_kel_racik=$row['id_kel_racik'];
					$ObatJualLain->r_item=$row['r_item'];
					$ObatJualLain->r_racik=$row['r_racik'];
					$ObatJualLain->bungkus_racik=$row['bungkus_racik'];
					$ObatJualLain->id_kemasan=$row['id_kemasan'];
					$ObatJualLain->st_imunisasi=$row['st_imunisasi'];
					$ObatJualLain->id_kel_imunisasi=$row['id_kel_imunisasi'];
					$ObatJualLain->id_bhp=$row['id_bhp'];
					$ObatJualLain->bhp=$row['bhp'];
					$ObatJualLain->expired=$row['expired'];
					$ObatJualLain->persentase_dokter=ObatRecord::finder()->findByPk($row['id_obat'])->persentase_dokter;
					$ObatJualLain->st_bayar=$row['st_bayar'];						
					
					$ObatJualLain->Save();			
					
					$stokAkhir=$stok->jumlah-$row['jumlah'];
					$stok->jumlah=$stokAkhir;
					$stok->Save();
					
					$this->normalHargaApotikLuar($row['id_obat'],$row['expired']);
				}	
			}		
			
			
			$biayaTotal = $row['total'];
			if($this->getViewState('stAsuransi') == '1')
				$biayaBulat = $biayaTotal;
			else
				$biayaBulat = $this->bulatkan($biayaTotal);
			
			$sisa = $biayaBulat - $biayaTotal;
			if($sisa > 0)
			{			
				//-------- Insert Harga Sisa Pembulatan ke tbt_obat_jual_sisa -----------------
					$ObatJualSisa= new ObatJualSisaRecord();
					$ObatJualSisa->no_trans=$notransTmp;
					$ObatJualSisa->jumlah = $sisa;
					$ObatJualSisa->tgl=date('y-m-d');
					$ObatJualSisa->Save();	
			}
			
			/*
			//DELETE data di tbt_stok_lain yg jumlah stok = 0
			$sql = "SELECT id FROM tbt_stok_lain WHERE jumlah = 0";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$idStok = $row['id'];
				StokLainRecord::finder()->deleteByPk($idStok);
			}
			*/
			
			$data = ObatJualTundaRecord::finder()->findByPk($id);
			$data->st = '1';
			$data->save();
		}
		
		//UPDATE tgl_cetak & wkt_cetak tbt_obat_jual_tunda
		$sql = "SELECT * FROM tbt_obat_jual_tunda WHERE jns_pasien='$jnsPasien' ";					
		if($jnsPasien != '4')
			$sql .= " AND no_trans_rawat = '$noTransRawat' ";
		elseif($jnsPasien == '4')
			$sql .= " AND petugas_internal = '$petugasInternal' ";	
		
		$sql .= " AND st = '0' ";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$data = ObatJualTundaRecord::finder()->findByPk($row['id']);
			$data->tgl_cetak=date('Y-m-d');
			$data->wkt_cetak=date('G:i:s');
			$data->operator_cetak=$operator;	
			$data->save();
		}
			
			
		if($jnsPasien == '0') //Pasien Rawat Jalan
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtJln'=>$noTransRawat,'cm'=>$cm,'dokter'=>$dokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
		}
		elseif($jnsPasien == '1') //Pasien Rawat Inap
		{
			if($st_bayar == '0') //Pembayaran Kredit
			{
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtInap'=>$noTransRawat,'cm'=>$cm,'dokter'=>$dokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
			}
			elseif($st_bayar == '1') //Pembayaran Tunai
			{
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtInap'=>$noTransRawat,'cm'=>$cm,'dokter'=>$dokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
			}	
		}
		elseif($jnsPasien == '2')//Pasien Luar
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransPasLuar'=>$noTransRawat,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
		}
		elseif($jnsPasien == '3')//One day service
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$dokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
		}
		elseif($jnsPasien == '4') //Unit Internal
		{
			$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtJln'=>$noTransRawat,'cm'=>$cm,'dokter'=>$dokter,'ruangan'=>$nmRuang,'petugas'=>$petugas,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg,'modeUrl'=>'01')));
		}
			//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
			//$this->test->text='ada';
			
		//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		//$this->Page->CallbackClient->focus($this->cariCM);	
		//$this->cariCM->Text = $Id.' - '.$jnsPasien.' - '.$noTransRawat.' - '.$petugasInternal;
		
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
		$position='Top'; 
		$this->dtgSomeData->PagerStyle->Position=$position;
				
		$session = $this->getSession();
		$session->remove("SomeData");
				
		$this->dtgSomeData->PagerStyle->Visible=true;
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
		
	public function DDKasirChanged($sender,$param)
	{	
		$this->cariClicked();
	}
	
	public function DDPoliChanged($sender,$param)
	{			
		if($this->DDPoli->SelectedValue != '')
		{
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$criteria->Condition = 'kelompok = :kelompok AND poliklinik = :poli';
			$criteria->Parameters[':kelompok'] = '1';
			$criteria->Parameters[':poli'] = $this->DDPoli->SelectedValue;
			
			if($this->DDPoli->SelectedValue == '07')//IGD
			{
				$criteria->Parameters[':poli'] = '08'; //ambil dokter poli umum
			}
			
			$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
			$this->DDDokter->dataBind();
		}
		
		$this->cariClicked();
	}
	
	public function DDDokterChanged($sender,$param)
	{		
		$this->cariClicked();
	}
	
	public function ChangedDDberdasarkan()
	{		
		$this->cariClicked();
			
		$this->lockPeriode();
		$pilih = $this->DDberdasarkan->SelectedValue;
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
		
		$this->cariClicked();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$this->cariClicked();
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
				'jnsTrans'=>$this->DDjnsTrans->SelectedValue,
				'jnsLap'=>$this->jnsLap->SelectedValue)));
		
	}
	
}
?>
