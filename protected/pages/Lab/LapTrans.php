<?php
class LapTrans extends SimakConf
{   
	private $sortExp = "tgl";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('5');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			
		}
				
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
		
		/*
		else
		{
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
		}
		*/
	}
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(date('Y'),date('Y')+20);
			$this->DDtahun->dataBind();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			//$this->DDPoli->Enabled = false;
			
			$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll($criteria);
			$this->DDKelompok->dataBind(); 	
					
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDPerusAsuransi->dataBind();
			$this->DDPerusAsuransi->Enabled=false;
			
			$sql = "SELECT nip,real_name FROM tbd_user ORDER BY real_name";
			$this->DDOperator->DataSource=UserRecord::finder()->findAllBySql($sql);
			$this->DDOperator->dataBind();
			
			$this->DDRadKateg->DataSource=LabKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			
			$modeTindakan = $this->modeTindakan->SelectedValue;
			$sql = "SELECT kode,nama FROM tbm_lab_tindakan WHERE st_paket = '$modeTindakan' ORDER BY nama";
			$this->DDTindakan->DataSource = LabTdkRecord::finder()->findAllBySql($sql);
			$this->DDTindakan->dataBind();
			
			$this->lockPeriode();			
			//$this->cetakBtn->Enabled = false;
			
			$this->DDberdasarkan->SelectedValue = '3';
			$this->ChangedDDberdasarkan();
			$this->setViewState('pilihPeriode','3');
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
		$this->DDtahun->DataSource=$this->tahun(date('Y'),date('Y')+20);
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
			
			$sql = "SELECT * FROM view_lap_lab WHERE view_lap_lab.flag IS NOT NULL ";
			
			if($this->modeBayar->SelectedValue != '')
			{
				$modeBayar = $this->modeBayar->SelectedValue;
				$sql .= "AND flag = '$modeBayar' ";
			}
			if($this->modeInput->SelectedValue < 3) 
			{
				$modeInput = $this->modeInput->SelectedValue;
				$sql .= "AND tipe_pasien = '$modeInput' ";
			}
			
			if($this->cariCm->Text <> '')
			{
				$cm = $this->formatCm($this->cariCm->Text);
				$sql .= "AND cm = '$cm' ";
			}
			
			if($this->cariNama->Text <> '')
			{
				$nama = $this->cariNama->Text;			
				if($this->Advance->Checked === true){
					$sql .= "AND nm_pas LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND nm_pas LIKE '$nama%' ";
				}
			}
			
			if($this->modeTindakan->SelectedValue != '') 
			{
				$modeTindakan = $this->modeTindakan->SelectedValue;
				if($modeTindakan == '0')
					$sql .= "AND st_paket = '0' ";
				elseif($modeTindakan == '1')
					$sql .= "AND st_paket > 0 ";	
				else
					$sql .= "AND (st_paket >= 0 OR st_paket IS NULL) ";		
			}
			
			if($this->DDTindakan->SelectedValue != '') 
			{
				$DDTindakan = $this->DDTindakan->SelectedValue;
				$sql .= "AND id_tindakan = '$DDTindakan' ";
			}
			
			if($this->DDPoli->SelectedValue != '') 
			{
				$DDPoli = $this->DDPoli->SelectedValue;
				$sql .= "AND id_poli = '$DDPoli' ";
			}
			
			if($this->DDDokter->SelectedValue != '') 
			{
				$DDDokter = $this->DDDokter->SelectedValue;
				$sql .= "AND id_dokter = '$DDDokter' ";
			}
			
			if($this->DDKelompok->SelectedValue != '') 
			{
				$DDKelompok = $this->DDKelompok->SelectedValue;
				$sql .= "AND id_kelompok = '$DDKelompok' ";
			}
			
			if($this->DDPerusAsuransi->SelectedValue != '') 
			{
				$DDPerusAsuransi = $this->DDPerusAsuransi->SelectedValue;
				$sql .= "AND id_perus = '$DDPerusAsuransi' ";
			}
			
			if($this->DDOperator->SelectedValue != '') 
			{
				$DDOperator = $this->DDOperator->SelectedValue;
				$sql .= "AND operator = '$DDOperator' ";
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
					$cariBln = date('m');
					$cariThn = date('Y');
					$sql .= "AND MONTH (tgl)='$cariBln' AND YEAR(tgl)='$cariThn' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}		
			}
			else
			{
				$cariBln = date('m');
				$cariThn = date('Y');
				$sql .= "AND MONTH (tgl)='$cariBln' AND YEAR(tgl)='$cariThn' ";
				
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				//$this->txtPeriode->Text='SEMUA PERIODE ';
			}
			
			
			
			//$sql .= "GROUP BY tgl ";
			
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
				$this->ambilBtn->Enabled = true;
				$this->checkAllBtn->Enabled = true;
				$this->uncheckAllBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
				$this->ambilBtn->Enabled = false;
				$this->checkAllBtn->Enabled = false;
				$this->uncheckAllBtn->Enabled = false;
			}
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
			
			$this->clearViewState('sql');
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
			$no_regNow = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			if($index > 0)
			{
				$no_regBefore = $this->dtgSomeData->DataKeys[$item->ItemIndex-1];
			}
			$tipe_pasien = $item->DataItem['tipe_pasien'];			
			$tgl = $item->DataItem['tgl'];	
			$item->tdkColumn->Text = $item->DataItem['nm_tindakan'];	
			if($tipe_pasien == '0')
			{
				$item->jnsPasColumn->Text = 'Rawat Jalan';
				
				$umur = $this->cariUmur('0',$item->DataItem['cm'],$item->DataItem['id_poli']);
				/*
				$tblLabJual = 'tbt_lab_penjualan';
				
				$sql = "SELECT 
						GROUP_CONCAT(tbm_lab_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_lab_tindakan
					  LEFT JOIN $tblLabJual ON ( $tblLabJual.id_tindakan = tbm_lab_tindakan.kode ) 
					WHERE
						$tblLabJual.no_reg = '$no_reg' ";
			
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{		
					$item->tdkColumn->Text = $row['nm_tdk'];
				}
				*/
			}
			elseif($tipe_pasien == '1')
			{
				$item->jnsPasColumn->Text = 'Rawat Inap';	
				$umur = $this->cariUmur('1',$item->DataItem['cm'],'');
				//$item->umurColumn->Text = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
				/*
				$tblLabJual = 'tbt_lab_penjualan_inap';
				
				$sql = "SELECT 
						GROUP_CONCAT(tbm_lab_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_lab_tindakan
					  LEFT JOIN $tblLabJual ON ( $tblLabJual.id_tindakan = tbm_lab_tindakan.kode ) 
					WHERE
						$tblLabJual.no_reg = '$no_reg' ";
			
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{		
					$item->tdkColumn->Text = $row['nm_tdk'];
				}
				*/
			}
			elseif($tipe_pasien == '2')
			{
				$item->jnsPasColumn->Text = 'Pasien Luar';		
				$umur = PasienLuarRecord::finder()->findByPk($item->DataItem['no_trans_asal'])->umur;
				/*
				$tblLabJual = 'tbt_lab_penjualan_lain';
				
				$sql = "SELECT 
						GROUP_CONCAT(tbm_lab_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_lab_tindakan
					  LEFT JOIN $tblLabJual ON ( $tblLabJual.id_tindakan = tbm_lab_tindakan.kode ) 
					WHERE
						$tblLabJual.no_reg = '$no_reg' ";
			
				$arr = $this->queryAction($sql,'S');
				foreach($arr as $row)
				{		
					$item->tdkColumn->Text = $row['nm_tdk'];
				}
				*/
			}	
			
			if($index > 0)
				{
					if($no_regBefore != $no_regNow)
					{
						
						$item->tglColumn->Text = $this->convertDate($tgl,'1');
						$item->operatorColumn->Text = $item->DataItem['nm_operator'];
						$item->cmColumn->Text = $item->DataItem['cm'];
						$item->dokterColumn->Text = $item->DataItem['dokter'];
						$item->poliColumn->Text = $item->DataItem['nm_poli'];
						$item->nmPasColumn->Text = $item->DataItem['nm_pas'];
						$item->umurColumn->Text = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
					}
				}
				else
				{
						$item->tglColumn->Text = $this->convertDate($tgl,'1');
						$item->operatorColumn->Text = $item->DataItem['nm_operator'];
						$item->cmColumn->Text = $item->DataItem['cm'];
						$item->dokterColumn->Text = $item->DataItem['dokter'];
						$item->poliColumn->Text = $item->DataItem['nm_poli'];
						$item->nmPasColumn->Text = $item->DataItem['nm_pas'];
						$item->umurColumn->Text = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
				}
				
			$item->hrgColumn->Text = number_format($item->DataItem['harga'],'2',',','.');
			$item->discColumn->Text = number_format($item->DataItem['disc'],'2',',','.');
			$item->tanggunganColumn->Text = number_format($item->DataItem['tanggungan_asuransi'],'2',',','.');
			
			
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
	
	public function cetakKartuBtnClicked($sender,$param)
	{		
		$noReg = $sender->CommandParameter;
		
		$sql = "SELECT * FROM view_lap_lab WHERE no_reg = '$noReg' ";
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$jnsPasien = $row['tipe_pasien'];
			$notrans = $row['no_trans_asal'];
			$cm= $row['cm'];
			$nama = $row['nm_pas'];
			$dokter = $row['nm_dokter'];
		}
		
		$this->Response->redirect($this->Service->constructUrl('Lab.cetakLapHasilLab',array('jnsPasien'=>$jnsPasien,'noReg'=>$noReg,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'table'=>$table,'stCetakUlang'=>'01')));	
	}
	
	public function checkAllBtnClicked()
	{
		$this->bindGrid();
		
		foreach($this->dtgSomeData->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":
					$item->chekGrid->checkBoxList->Checked = true;
					break;
				case "AlternatingItem":
					$item->chekGrid->checkBoxList->Checked = true;
					break;
				default:
					break;
			}
		}
	}
	
	public function uncheckAllBtnClicked()
	{
		$this->bindGrid();
		
		foreach($this->dtgSomeData->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":
					$item->chekGrid->checkBoxList->Checked = false;
					break;
				case "AlternatingItem":
					$item->chekGrid->checkBoxList->Checked = false;
					break;
				default:
					break;
			}
		}
	}
	
	public function konfirmasiAmbil()
	{
		$this->getPage()->getClientScript()->registerEndScript('', '
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Ambil Hasil Lab Untuk Data Yang Dipilih Sekarang ?<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"Ya": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						prosesKonfirmasi(\'ya\');
					},
					"Tidak": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						prosesKonfirmasi(\'tidak\');
					}
				}
			}});');		
	}
	
	
	public function ambilClicked($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
				
		if($mode == 'ya')
		{	
			$jmlData = 0;
			
			foreach($this->dtgSomeData->getItems() as $item) {
				switch($item->getItemType()) {
					case "Item":
						if($item->chekGrid->checkBoxList->Checked === true)
						{
							$noReg = $item->chekGrid->idVal->Value;
							$this->prosesAmbil($noReg);
							$jmlData++;
						}
						break;
					case "AlternatingItem":
						if($item->chekGrid->checkBoxList->Checked === true)
						{
							$noReg = $item->chekGrid->idVal->Value;
							$this->prosesAmbil($noReg);
							$jmlData++;
						}
						break;
					default:
						break;
				}
			}
			
			$this->prosesClicked();
			
			if($jmlData > 0) //jika ada hasil lab yg diambil
			{
				$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent(); 
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Pengambilan Hasil Lab Sukses <br/><br/></p>\',timeout: 6000,dialog:{
					modal: true,
					buttons: {
						"OK": function() {
							jQuery( this ).dialog( "close" );
						}
					}
				}});');
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Silakan Pilih Data Yang Akan Diambil Terlebih dahulu !<br/><br/></p>\',timeout: 3000,dialog:{
					modal: true,
					buttons: {
						"OK": function() {
							jQuery( this ).dialog( "close" );
						}
					}
				}});');
			}
		}	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();');
			$this->bindGrid();
		}
	}
	
	public function prosesAmbil($noReg)
	{
		//UPDATE tbt_lab_hasil
		$sql = "SELECT id FROM tbt_lab_hasil WHERE no_trans = '$noReg' ";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$id = $row['id'];
			
			$data = LabHasilRecord::finder()->findByPk($id);
			$data->operator_ambil = $this->User->IsUserNip;
			$data->tgl_ambil = date('Y-m-d');
			$data->wkt_ambil = date('G:i:s');
			$data->save();
		}
		
		//$this->test->Text = $noReg;
		$jnsPasien = LabHasilRecord::finder()->find('no_trans = ?',$noReg)->tipe_pasien;
		
		if ($jnsPasien=='0')
		{
			$sql="UPDATE tbt_lab_penjualan SET st_ambil = '1' WHERE no_reg = '$noReg' ";
		}
		elseif ($jnsPasien=='1')
		{
			$sql="UPDATE tbt_lab_penjualan_inap SET st_ambil = '1' WHERE no_reg = '$noReg' ";
		}
		elseif ($jnsPasien=='2')
		{
			$cm = '';
			$sql="UPDATE tbt_lab_penjualan_lain SET st_ambil = '1' WHERE no_reg = '$noReg' ";
		}
		
		$this->queryAction($sql,'C');
		
	}
	
	public function modeInputChanged()
	{
		$jnsPasien = $this->modeInput->SelectedValue;
		
		$this->DDPoli->SelectedValue = 'empty';
		$this->DDDokter->SelectedValue = 'empty';
		$this->DDKelompok->SelectedValue = 'empty';
		$this->DDPerusAsuransi->SelectedValue = 'empty';
		
		$this->DDPerusAsuransi->Enabled=false;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$this->DDPoli->Enabled=true;		
			$this->DDDokter->Enabled=false;
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');
			$this->DDDokter->dataBind();
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$this->DDPoli->Enabled=false;	
			$this->DDDokter->Enabled=true;
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');
			$this->DDDokter->dataBind();
		}
		else
		{
			$this->DDPoli->Enabled=false;
			$this->DDDokter->Enabled=false;
			$this->DDKelompok->Enabled=false;
		}
		
		$this->prosesClicked();
	}
	
	public function modeTindakanChanged()
	{
		$modeTindakan = $this->modeTindakan->SelectedValue;
		$this->DDRadKateg->SelectedValue = 'empty';
		$this->DDRadKateg->Enabled = false;
		$this->DDRadKategChanged();
		
		if($modeTindakan == '0')//Non Paket
		{
			$sql = "SELECT kode,nama FROM tbm_lab_tindakan WHERE st_paket = '0' ORDER BY nama";
			$this->DDRadKateg->Enabled = true;
		}
		else
		{	
			$sql = "SELECT kode,nama FROM tbm_lab_tindakan WHERE st_paket > 1 ORDER BY nama";
		}
		
		$this->DDTindakan->DataSource = LabTdkRecord::finder()->findAllBySql($sql);
		$this->DDTindakan->dataBind();
				
		$this->prosesClicked();
	}
	
	public function modeBayarChanged()
	{
		$this->prosesClicked();
	}
	
	public function DDRadKategChanged()
	{
		$kateg = $this->DDRadKateg->SelectedValue;
		
		if($kateg != '')
		{
			$sql = "SELECT 
				  tbm_lab_tindakan.kode AS kode,
				  tbm_lab_tindakan.nama AS nama
				FROM
				  tbm_lab_tindakan
				  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
				WHERE
					tbm_lab_tindakan.kategori = '$kateg'
				  ORDER BY nama ";
		}
		else
		{
			$sql = "SELECT 
				  tbm_lab_tindakan.kode AS kode,
				  tbm_lab_tindakan.nama AS nama
				FROM
				  tbm_lab_tindakan
				  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
				  ORDER BY nama ";
		}
		
		$this->DDTindakan->DataSource=$this->queryAction($sql,'S');
		$this->DDTindakan->dataBind();
			
		$this->prosesClicked();
	}
	
	public function prosesClicked()
	{
		//if($this->IsValid)  // when all validations succeed
        //{
			$session = $this->getSession();
			$session->remove("SomeData"); 
			
			$this->dtgSomeData->Display='Dynamic';
			
			$this->dtgSomeData->CurrentPageIndex = 0;
			$this->bindGrid();	
			
			$this->gridPanel->Display='Dynamic';
		/*	
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
		*/
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	
	public function prosesPilihGrid()
	{			
		$position='Top'; 
		$this->dtgSomeData->PagerStyle->Position=$position;
				
		$session = $this->getSession();
		$session->remove("SomeData");
				
		$this->dtgSomeData->PagerStyle->Visible=true;
		
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function cariClicked()
	{		
		$this->prosesPilihGrid();
	}
	
	
	public function DDPoliChanged($sender,$param)
	{
		if($this->DDPoli->SelectedValue=='07') //IGD
		{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '08');//kelompok pegawai '1' adalah untuk dokter
		}
		else
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDPoli->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
		
		$this->DDDokter->dataBind();
		
		$klinik=PoliklinikRecord::finder()->findByPk($this->DDPoli->SelectedValue)->nama;		
		$this->DDDokter->Enabled=true;		
		$this->setViewState('klinik',$klinik);
		$this->setViewState('id_klinik',$this->DDPoli->SelectedValue);
		
		$this->prosesPilihGrid();
	}
	
	public function DDDokterChanged($sender,$param)
	{					
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('dktr',$this->DDDokter->SelectedValue);	
		$this->prosesPilihGrid();
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		if($this->DDKelompok->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07')
			{
				$this->DDPerusAsuransi->Enabled = true;	
				$idKelPerus = $this->DDKelompok->SelectedValue;
			
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerusAsuransi->DataSource = $data;
				$this->DDPerusAsuransi->dataBind();
			
				if($this->getViewState('perusAsuransi'))
				{	
					$this->DDPerusAsuransi->SelectedValue = $this->getViewState('perusAsuransi');
				}	
			}
			else
			{
				$this->DDPerusAsuransi->SelectedValue = 'empty';
				$this->DDPerusAsuransi->Enabled = false;
			}
		}
		
		$this->prosesPilihGrid();
	}
		
	public function ChangedDDberdasarkan()
	{		
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
		
		$this->prosesClicked();
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
		$session['cetakLapTransLab'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Lab.cetakLapTransLab',
			array(
				'jnsPasien'=>$this->modeInput->SelectedValue,
				'poli'=>$this->DDPoli->SelectedValue,
				'dokter'=>$this->DDDokter->SelectedValue,
				'kelompok'=>$this->DDKelompok->SelectedValue,
				'perus'=>$this->DDPerusAsuransi->SelectedValue,
				'operatorLab'=>$this->DDOperator->SelectedValue,
				'periode'=>$this->txtPeriode->Text)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
