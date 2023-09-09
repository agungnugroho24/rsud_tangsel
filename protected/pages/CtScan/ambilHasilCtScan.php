<?php
class ambilHasilCtScan extends SimakConf
{   
	private $sortExp = "tgl ASC, no_reg";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	
	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('13');
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
			
			$this->lockPeriode();			
			$this->cetakBtn->Enabled = false;
			
			$this->DDberdasarkan->SelectedValue = '1';
			$this->ChangedDDberdasarkan();
			$this->setViewState('pilihPeriode','1');
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
			
			$sql = "SELECT * FROM view_ambil_hasil_ctscan WHERE no_reg <> '' ";
			
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
					$sql .= "AND nm_pasien LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND nm_pasien LIKE '$nama%' ";
				}
			}
			
			if($this->DDPoli->SelectedValue != '') 
			{
				$DDPoli = $this->DDPoli->SelectedValue;
				$sql .= "AND id_klinik = '$DDPoli' ";
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
				//$cariBln = date('m');
				//$cariThn = date('Y');
				//$sql .= "AND MONTH (tgl)='$cariBln' AND YEAR(tgl)='$cariThn' ";
				
				//$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				$this->txtPeriode->Text='SEMUA PERIODE ';
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
			$no_reg = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$tipe_pasien = $item->DataItem['tipe_pasien'];			
			$tgl = $item->DataItem['tgl'];	
			
			if($tipe_pasien == '0')
				$item->jnsPasColumn->Text = 'Rawat Jalan';
			elseif($tipe_pasien == '1')
				$item->jnsPasColumn->Text = 'Rawat Inap';	
			elseif($tipe_pasien == '2')
				$item->jnsPasColumn->Text = 'Pasien Luar';		
						
			$item->tglColumn->Text = $this->convertDate($tgl,'3');
			
			$sql = "SELECT * FROM tbm_ukuran_film ORDER BY nama";
			$data = $this->queryAction($sql,'S');
			$item->ukuran->DDukuran->setDataSource($data);
			$item->ukuran->DDukuran->databind();
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
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Ambil Hasil Rad Untuk Data Yang Dipilih Sekarang ?<br/><br/></p>\',timeout: 600000,dialog:{
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
							$idTdk = $item->chekGrid->idTdk->Value;
							$idFilm = $item->ukuran->DDukuran->SelectedValue;
							$jmlFilm = intval($item->jml->jmlFilm->Text);
							$this->prosesAmbil($noReg,$idTdk,$idFilm,$jmlFilm);
							$jmlData++;
						}
						break;
					case "AlternatingItem":
						if($item->chekGrid->checkBoxList->Checked === true)
						{
							$noReg = $item->chekGrid->idVal->Value;
							$idTdk = $item->chekGrid->idTdk->Value;
							$idFilm = $item->ukuran->DDukuran->SelectedValue;
							$jmlFilm = intval($item->jml->jmlFilm->Text);
							$this->prosesAmbil($noReg,$idTdk,$idFilm,$jmlFilm);
							$jmlData++;
						}
						break;
					default:
						break;
				}
			}
			
			$this->prosesClicked();
			
			if($jmlData > 0) //jika ada hasil rad yg diambil
			{
				$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent(); 
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Pengambilan Hasil CT Scan Sukses <br/><br/></p>\',timeout: 6000,dialog:{
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
	
	public function prosesAmbil($noReg,$idTdk,$idFilm,$jmlFilm)
	{	
		$operator = $this->User->IsUserNip;
		$tgl = date('Y-m-d');
		$wkt = date('G:i:s');
		 
		//$this->test->Text = $noReg;
		//$jnsPasien = RadHasilRecord::finder()->find('no_trans = ?',$noReg)->tipe_pasien;
		
		if(substr($noReg,6,2) == '50')
		{
			//$sql="UPDATE tbt_ctscan_penjualan SET film_size = '$idFilm',jml_film = '$jmlFilm',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk'";
			$sql="UPDATE tbt_ctscan_penjualan SET jml_film = '1',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk'";
		}
		elseif(substr($noReg,6,2) == '51')
		{
			//$sql="UPDATE tbt_ctscan_penjualan_inap SET film_size = '$idFilm',jml_film = '$jmlFilm',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk' ";
			$sql="UPDATE tbt_ctscan_penjualan_inap SET jml_film = '1',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk' ";
		}
		elseif(substr($noReg,6,2) == '52')
		{
			$cm = '';
			//$sql="UPDATE tbt_ctscan_penjualan_lain SET film_size = '$idFilm',jml_film = '$jmlFilm',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk' ";
			$sql="UPDATE tbt_ctscan_penjualan_lain SET jml_film = '1',st_ambil = '1',operator_ambil = '$operator',tgl_ambil = '$tgl',wkt_ambil = '$wkt' WHERE no_reg = '$noReg' AND id_tindakan='$idTdk' ";
		}
		
		$this->queryAction($sql,'C');
		
	}
	
	public function modeInputChanged()
	{
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$this->DDPoli->Enabled = true;
		}
		else
		{
			$this->DDPoli->Enabled = false;
			$this->DDPoli->SelectedValue = 'empty';
		}
		
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
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
