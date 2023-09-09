<?php
class penjualanObatNonAutocomplete extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		$session=$this->Application->getModule('session');	
		if($session['stCetakPenjualanObat']=='1')
		{
			$session->remove('stCetakPenjualanObat');
			$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObat'));
		}	
	 }	
	
	public function onLoad($param)
	{		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			
			$this->inapPanel->Display = 'None';
			$this->karyawanPanel->Display = 'None';
			$this->rujukPanel->Display = 'None';
			$this->nmPasienPanel->Display = 'None';
			$this->poliPanel->Display = 'None';	
			$this->dokterLuarPanel->Display = 'None';
			$this->secondPanel->Display = 'None';
			$this->namaKarPanel->Display = 'None';					
			//$this->jnsBayarInapCtrl->Visible = false;
			//$this->jnsPasRujukCtrl->Visible = false;
			
			//$this->poliCtrl->Enabled = false;			
			//$this->showSecond->Enabled = false;			
			$this->showBayar->Enabled = false;			
			
			//$this->jmlBungkus->Enabled = false;
			//$this->inapCtrl->Visible = false;
			////$this->showSecond->Enabled =  false;
			//$this->showBayar->Display = 'Hidden';
			//$this->showStok->Display = 'Hidden';
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->modeStok->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->modeStok->dataBind();
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDKlinik->Enabled=true;
			$crite = new TActiveRecordCriteria;												
			$crite->OrdersBy['nama'] = 'asc';
			$this->DDNamaKar->DataSource=PegawaiRecord::finder()->findAll($crite);
			$this->DDNamaKar->dataBind();
			
			
			$this->DDDokter->Enabled=true;
			
			$this->notrans->Enabled=false;	
			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			//$this->DDObat->dataBind();
			
			$this->DDBhp->DataSource=ObatBhpRecord::finder()->findAll($criteria);
			$this->DDBhp->dataBind();
			
			$this->DDObat->Enabled=true;	
			$this->DDRacik->Enabled = false;
			$this->jmlBungkus->Enabled = false;
			$this->modeByrInap->Enabled=true;
			$this->Tebus->Visible=false;
			//$this->tunaiCB->Enabled=false;
			//$this->panelObat->Display = 'None';
			//$this->panelBhp->Display = 'None';
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
			
			$this->clearViewState('tmpJml');
			$this->getViewState('sisa');			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';			
			$this->errMsg->Text='';			
			//$this->notrans->Focus();
			//$this->showSecond->Enabled =  false;
			$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObat',array('goto'=>'1')));
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{			
			$this->Page->CallbackClient->focus($this->notrans);
		}
		
		if ($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTable ORDER BY id_kel_racik,id_kel_imunisasi, id";
			
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();	
			
			$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->BhpGrid->DataSource = $arr;//Insert new row in tabel bro...
				$this->BhpGrid->dataBind();	
			}
			//$this->panelObat->Display = 'Dynamic';
		}
		else
		{
			//$this->panelObat->Display = 'None';
		}
		
	}
	
	public function onRenderComplete($param)
	{
		parent::onRenderComplete($param);
		if($this->IsPostBack || $this->IsCallBack)
		{	
			/*if($this->embel->SelectedValue=='01')
			{
				$this->Page->CallbackClient->focus($this->dokter);
				$page=$this->Application->getservice('page')->RequestedPage;
				$page->CallbackClient->callClientFunction("Prado.Element.focus",array($this->dokter->getClientID()));
				$this->dokter->focus();
			}else*/if($this->embel->SelectedValue=='02')
			{
				$page=$this->Application->getservice('page')->RequestedPage;
				$page->CallbackClient->callClientFunction("Prado.Element.focus",array($this->nmPas->getClientID()));
				$this->nmPas->focus();
			}
		}
		else
		{
		
			$this->errMsg->Text = '';
		}
		
		
		
	}
				
	public function onPreRender($param)
	{				
		parent::onPreRender($param);
		if($this->IsPostBack || $this->IsCallBack)
		{	
			
			
		}
    }	
	
	public function panelCallback($sender,$param)	
   	{		
		//$this->karyawanPanel->render($param->getNewWriter());	
		$this->jnsPasPanel->render($param->getNewWriter());		
		$this->inapPanel->render($param->getNewWriter());		
		$this->rujukPanel->render($param->getNewWriter());
		$this->poliPanel->render($param->getNewWriter());
		$this->dokterLuarPanel->render($param->getNewWriter());
		$this->secondPanel->render($param->getNewWriter());		
		$this->cmPanel->render($param->getNewWriter());				
		if($this->getViewState('idObat'))
		{
			$this->DDObat->SelectedValue = $this->getViewState('idObat');
		}		
	}
	
	
	
	public function cmCallback($sender,$param)
   	{
		//$this->poliPanel->render($param->getNewWriter());
		$this->secondPanel->render($param->getNewWriter());		
		
		if($this->getViewState('idObat'))
		{
			$this->DDObat->SelectedValue = $this->getViewState('idObat');
		}	
	}
	
	public function secondCallBack($sender,$param)
   	{
		$this->secondPanel->render($param->getNewWriter());
	}

	public function modeInputChanged($sender,$param)
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$this->clearViewState('jnsPasien',$jnsPasien);
		$this->clearViewState('tujuan');			
		$this->setViewState('jnsPasien',$jnsPasien);	
		
		if($jnsPasien == '0') //Rawat jalan 
		{
			$this->rujukPanel->Display = 'None';
			$this->inapPanel->Display = 'None';
			$this->karyawanPanel->Display = 'None';
			$this->namaKarPanel->Display = 'None';
			$this->cmPanel->Visible = true;
			//$this->nmPasienPanel->Display = 'Dynamic';
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;
			$this->dokter->Enabled = false;
			
			$this->notrans->Enabled = true;
			$this->clearViewState('modeByrInap');
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
			$this->clearViewState('modeKryPas');			
		}
		elseif($jnsPasien == '1') //Rawat Inap
		{
			$this->rujukPanel->Display = 'None';
			$this->inapPanel->Display = 'Dynamic';
			$this->karyawanPanel->Display = 'None';
			$this->namaKarPanel->Display = 'None';
			$this->cmPanel->Visible = true;
			
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;
			//$this->tebusPanel->Visible = false;
			$this->Tebus->Visible = false;
			
			$this->notrans->Enabled = true;
			$this->DDKlinik->Enabled=false;
			$this->DDDokter->Enabled=true;
			
			//$this->showStok->Visible=true;
			$this->modeByrInap->Enabled= true;
			//$this->DDUrut->Enabled=true;
			//$this->jnsBayarInapCtrl->Visible = true;
			//$this->jnsPasRujukCtrl->Visible = false;
			//$this->clearViewState('cariPoli');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "true");
						
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
			$this->clearViewState('modeKryPas');
		}
		elseif($jnsPasien == '2') //Pasien Luar
		{
			$this->rujukPanel->Display = 'Dynamic';
			$this->inapPanel->Display = 'None';
			$this->karyawanPanel->Display = 'Dynamic';			
			$this->poliPanel->Display = 'None';
			$this->namaKarPanel->Display = 'None';			
			
			$this->poliPanel->Enabled = false;
			$this->modeKryPas->SelectedValue='3';
			$this->setViewState('modeKryPas',$modeKryPas);
			
			$this->dokterLuarPanel->Enabled = true;
			
			$this->cmPanel->Visible = false;									
			//$this->clearViewState('modeByrInap');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "false");
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->embel->getClientID().'.focus();
				</script>';
				
			$this->Page->getCallbackClient()->scrollTo($this->embel);
		}
		if($jnsPasien == '3') //One Day Service
		{
			$this->rujukPanel->Display = 'None';
			$this->inapPanel->Display = 'None';
			$this->karyawanPanel->Display = 'None';
			$this->cmPanel->Visible = true;
			$this->namaKarPanel->Display = 'None';
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;	
			
			$this->notrans->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->Page->getCallbackClient()->scrollTo($this->notrans);
			//$this->jnsBayarInapCtrl->Visible = false;
			//$this->jnsPasRujukCtrl->Visible = false;
			
			$this->clearViewState('modeByrInap');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "true");
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
			$this->clearViewState('modeKryPas');
		}
			
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
		$this->DDDokter->dataBind();
		$this->dokter->Text = '';
		$this->nmPas->Text = '';
		$this->notrans->Text = '';
		$this->errMsg->Text = '';
		$this->embel->SelectedIndex = -1;
		
		//$this->showFirst->Visible=true;
		//$this->showSecond->Enabled = false;
		$this->Page->CallbackClient->focus($this->notrans);
	}
	
	//private function karyawanPanelDis($notrans)
	private function karyawanAction($notrans)
	{
		$kelompokPasien = PasienRecord::finder()->findByPk($notrans)->kelompok;
		$this->setViewState('kelompokPasien',$kelompokPasien);
		$jnsPasien=$this->getViewState('jnsPasien');
		/*
		if($kelompokPasien == '04' || $jenisPasien=='3')//Bila kelompok pasien adalah karyawan
		{
			$this->karyawanPanel->Display = 'Dynamic';
			$this->modeKryPas->Enabled=true;				
		}
		*/
	}
	
	public function modeTebusChanged($sender,$param)
    {
		if($updateTebus=RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND st_tebus_luar=? AND flag=?',$this->notrans->Text,'0','0','0','0'))
		{	
			$updateTebus->st_tebus_luar = '1';
			$updateTebus->Save();
			$this->batalClicked();
		}else{			
			$this->batalClicked();
		}		
	}
	
	public function checkRegister($sender,$param)
    {
        // valid if the username is found in the database
		$tmp = $this->notrans->Text;
		$this->modeByrInap->Enabled=true;
		$tglSkrg = date('Y-m-d');	
				
		$jnsPasien = $this->collectSelectionResult($this->modeInput);	
		if($jnsPasien == '2')//Bila pasien LUAR/BEBAS/OTC
		{			
			if($this->getViewState('modeKryPas') <> '3')
			{				
				$this->setViewState('kelompokPasien','04');				
				$this->nmPas->Text=PegawaiRecord::finder()->findByPK($this->getViewSate('idKar'))->nama;
				$this->setViewState('cmKryOTC',$this->getViewSate('idKar'));
				$this->errMsg->Text='';
			}
			else
			{
				$this->clearViewState('cmKryOTC');
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				$this->errMsg->Text='Bukan No. CM Karyawan !';
				$this->Page->CallbackClient->focus($this->notrans);				
			}
		}
		else if($jnsPasien == '0') //Bila pasien rawat jalan					
		{
			$dateNow = date('Y-m-d');
			if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND st_tebus_luar=? AND tgl_visit=?',$this->notrans->Text,'0','0','0',$dateNow))
			{				
				//$this->poliCtrl->Enabled=true;
				$this->nmPasienPanel->Display = 'Dynamic';
				$this->karyawanAction($this->notrans->Text);			
				
				$sql = "SELECT 
							tbt_rawat_jalan.cm AS cm,
							tbt_rawat_jalan.no_trans AS no_trans,
							tbd_pasien.nama AS nama							
						FROM
							tbt_rawat_jalan
							INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_jalan.cm='$tmp' 
							AND tbt_rawat_jalan.flag='0'
							AND tbt_rawat_jalan.st_alih='0' ";
				$arrData=$this->queryAction($sql,'R');				
				foreach($arrData as $row)
				{
					$noTransJln = $row['no_trans'];
					$this->nmPas->Text= $row['nama'];
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);							
					$this->setViewState('no_trans_rwtjln',$row['no_trans']);												
				}
				
				$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransJln)->st_asuransi;
				$this->setViewState('stAsuransi',$stAsuransi);				
				//data u/ DDKlinik
				$sql = "SELECT 
					  tbm_poliklinik.id,
					  tbm_poliklinik.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbt_rawat_jalan.cm = '$tmp' 
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'
					  AND tbt_rawat_jalan.tgl_visit = '$dateNow'";
						  
				$this->DDKlinik->Enabled = true;
				$this->DDKlinik->DataSource=$this->queryAction($sql,'S');
				$this->DDKlinik->dataBind();
				/*
				//cek st_asuransi jika = 0 asuransi/askes/karyawan tidak berlaku
				$sql = "SELECT 
							st_asuransi	
						FROM
						  tbt_rawat_jalan
						WHERE
						  cm = '$tmp' AND
						  no_trans = '$noTransJln'";
					  
				
				$stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;
				if($stAsuransi != '0') //asuransi/askes/karyawan berlaku
				{
					//$this->jnsBayarInapCtrl->Visible = true;
				}
				else //asuransi/askes/karyawan tidak berlaku
				{ 
					//$this->jnsBayarInapCtrl->Visible = false;
				}
				*/
				
				$this->errMsg->Text='';
				$this->modeInput->Enabled= false;
				$this->DDObat->enabled=true;
				
				$this->jnsPasPanel->Enabled= false;
				$this->notrans->Enabled= false;					
				$this->Tebus->Visible=true;				
				$this->poliPanel->Display = 'Dynamic';
				$this->secondPanel->Display = 'Dynamic';
				$this->Page->CallbackClient->focus($this->DDKlinik);			
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND tgl_visit=?',$this->notrans->Text,'0','1',$dateNow))
			{
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->notrans->Text.' sudah alih status ke Rawat Inap !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text='';
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND st_tebus_luar=? AND tgl_visit=?',$this->notrans->Text,'0','0','1',$dateNow))
			{
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->notrans->Text.' sudah tebus resep diluar !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text='';
			}
			else
			{
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->notrans->Text.' Belum Masuk Ke Pendaftaran Rawat Jalan !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text='';
				
			}
		}
		elseif($jnsPasien == '1')//Bila pasien rawat inap
		{			
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{			
				//$this->poliCtrl->Enabled=true;
				$this->modeInput->Enabled= false;			
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$noTransInap = $tmprwtinap->no_trans;
				$this->setViewState('notransinap',$noTransInap);
					
				$sql = "SELECT 
							tbt_rawat_inap.cm AS cm,
							 tbt_rawat_inap.tgl_masuk,
							 tbt_rawat_inap.wkt_masuk,
							 tbt_rawat_inap.kelas,
							tbd_pasien.nama AS nama
						FROM
							tbd_pasien 
							INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_inap.cm='$tmp' 
							AND tbt_rawat_inap.status='0' ";
							
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					$tglMasuk = $row['tgl_masuk'];	
					$tglKeluar = date('Y-m-d');
					
					$wktMasuk = $row['wkt_masuk'];	
					$wktKeluar = date('G:i:s');					
					
					$this->nmPas->Text=$row['nama'];	
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);	
					$this->setViewState('kelasInap',$row['kelas']);	
				}
				
				$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTransInap' AND st_bayar='0'";
				$arr = $this->queryAction($sql,'S');
				$jmlDataInapKmr = count($arr);
				$counter = 1;
				foreach($arr as $row)
				{
					$lamaInap += $row['lama_inap'];
					if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
					{
						$tglMasuk = $row['tgl_awal'];
						$wktMasuk = $row['wkt_masuk'];
							
						$tglKeluar = date('Y-m-d');
						$wktKeluar = date('G:i:s');
						$lamaInap += $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					}
					$counter++;
				}					
				
				//$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				
				$this->setViewState('lamaInap',$lamaInap);	
				
				//$this->showSecond->Enabled=true;
				//$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				
				//data u/ DDDokter
				/*$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_inap.cm = '$tmp'
					  AND tbt_rawat_inap.status = 0";*/
				$sql="SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					WHERE
					  tbd_pegawai.kelompok = 1";			  
				$this->DDDokter->DataSource=$this->queryAction($sql,'S');
				//$this->showSql->text=$sql;
				$this->DDDokter->dataBind();
				$this->Page->CallbackClient->focus($this->DDDokter);
				
				/*
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);											
				//$this->showSecond->Enabled=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;												
				$this->DDObat->Focus();
				
				$this->lock();
				*/
				$this->karyawanAction($this->notrans->Text);				
				$this->nmPasienPanel->Display = 'Dynamic';
				$this->poliPanel->Display = 'Dynamic';
				$this->secondPanel->Display = 'Dynamic';
			}
			else
			{
				$this->modeInput->Enabled= true;
				
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				
				//$this->poliCtrl->Enabled = false;	
				//$this->showFirst->Visible=true;
				//$this->showSecond->Enabled =  false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Inap !';
				$this->Page->CallbackClient->focus($this->notrans);
			}
		}       
		elseif($jnsPasien == '3')//Bila pasien one day service
		{			
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{			
				//$this->poliCtrl->Enabled=true;
				$this->modeInput->Enabled= false;
				
				$sql = "SELECT 
							tbt_rawat_inap.cm AS cm,
							 tbt_rawat_inap.tgl_masuk,
							 tbt_rawat_inap.wkt_masuk,
							 tbt_rawat_inap.kelas,
							tbd_pasien.nama AS nama
						FROM
							tbd_pasien 
							INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_inap.cm='$tmp' 
							AND tbt_rawat_inap.status='0' ";
							
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					$tglMasuk = $row['tgl_masuk'];	
					$tglKeluar = date('Y-m-d');
					
					$wktMasuk = $row['wkt_masuk'];	
					$wktKeluar = date('G:i:s');
										
					//$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					
					$this->nmPas->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);	
					$this->setViewState('kelasInap',$row['kelas']);	
					//$this->setViewState('lamaInap',$lamaInap);	
				}
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$noTransInap = $tmprwtinap->no_trans;
				$this->setViewState('notransinap',$noTransInap);
				
				
				$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTransInap' AND st_bayar='0'";
				$arr = $this->queryAction($sql,'S');
				$jmlDataInapKmr = count($arr);
				$counter = 1;
				foreach($arr as $row)
				{
					$lamaInap += $row['lama_inap'];
					if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
					{
						$tglMasuk = $row['tgl_awal'];
						$wktMasuk = $row['wkt_masuk'];
							
						$tglKeluar = date('Y-m-d');
						$wktKeluar = date('G:i:s');
						$lamaInap += $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					}
					$counter++;
				}					
				
				$this->setViewState('lamaInap',$lamaInap);	
				
				//$this->showSecond->Enabled=true;
				$this->errMsg->Text='';	
				
				$sql="SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					WHERE
					  tbd_pegawai.kelompok = 1";			  
				$this->DDDokter->DataSource=$this->queryAction($sql,'S');
				//$this->showSql->text=$sql;
				$this->DDDokter->dataBind();
				$this->Page->CallbackClient->focus($this->DDDokter);
				$this->karyawanAction($this->notrans->Text);
				
				//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
				$noTrans = $tmprwtinap->no_trans;
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND st='0'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//jika pasien ambil paket
				{
					foreach($arr as $row)
					{
						$sql = "SELECT *
							   FROM view_inap_operasi_billing				
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$obat = $row['obat'];
						}
						
						$this->setViewState('hrgObatPaket',$obat);
					}		
				}
				else
				{
					$this->modeInput->Enabled= true;
					//$this->poliCtrl->Enabled = false;	
					//$this->showFirst->Visible=true;
					//$this->showSecond->Enabled =  false;
					$this->errMsg->Text='Pasien Belum mengambil Paket, Silahkan pilih mode Rawat Inap!';
					$this->Page->CallbackClient->focus($this->notrans);
				}
			}
			else
			{
				$this->modeInput->Enabled= true;
				//$this->poliCtrl->Enabled = false;	
				//$this->showFirst->Visible=true;
				//$this->showSecond->Enabled =  false;
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Inap !';
				$this->Page->CallbackClient->focus($this->notrans);
			}
		}
		
    }	
	
	public function cekModeStok($sender,$param)
	{
		 if($param->Value == ($this->modeStok->SelectedValue == ''))
            $param->IsValid=false;
	}
	
	public function showDokter($sender,$param)
	{
		if($this->DDKlinik->SelectedValue!='')
		{
			$tmp = $this->notrans->Text;
			$dateNow = date('Y-m-d');
			
			$idKlinik = $this->DDKlinik->SelectedValue;
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien=='0' || $jnsPasien=='3')
			{	
				//data u/ DDDokter
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_jalan.cm = '$tmp'
					  AND tbt_rawat_jalan.id_klinik = '$idKlinik'
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'
					  AND tbt_rawat_jalan.tgl_visit = '$dateNow'";
			}
			
			$this->DDDokter->DataSource=$this->queryAction($sql,'S');
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled=true;		
			
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
			$this->setViewState('klinik',$klinik);	
			$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
			$this->Page->CallbackClient->focus($this->DDDokter);		
			/*
			if ($this->DDKlinik->SelectedValue=='07')
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '08');
				//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			elseif($this->DDKlinik->SelectedValue=='17')
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', '03');
				//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			else
			{
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
			}
			*/	
		}
		else
		{
			$this->DDDokter->SelectedIndex = -1;
			$this->DDDokter->Enabled = false;
			//$this->batalClicked();
			$this->Page->CallbackClient->focus($this->DDKlinik);
		}		
	}
	
	public function showNamaKar($sender,$param)
	{
		if($this->DDNamaKar->SelectedValue!='')
		{	
			$idKar = $this->DDNamaKar->SelectedValue;			
			$this->setViewState('idKar',$idKar);
			//$this->test4->Text = $this->getViewState('kelompokPasien');//$this->getViewState('idKar');
		}
		else
		{
			$this->DDNamaKar->SelectedIndex = -1;			
		}		
	}
	
	public function showNotrans($sender,$param)
	{			
		if($this->DDDokter->SelectedValue!='')
		{
			$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);	
			$this->setViewState('idDokter',$this->DDDokter->SelectedValue);	
			$this->Page->CallbackClient->focus($this->modeStok);
		}
		else
		{
			$this->clearViewState('dokter');	
			$this->clearViewState('idDokter');	
			$this->Page->CallbackClient->focus($this->DDDokter);
		}	
	}
	
	public function checkRegister2($sender,$param)
    {
        // valid if the username is found in the database
		$this->modeByrInap->Enabled=false;
		$tglSkrg = date('Y-m-d');
		
		$this->DDKateg->DataSource=JenisBrgRecord::finder()->findAll();
		$this->DDKateg->dataBind();
		$this->DDObat->enabled=true;	
		
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0')//Bila pasien rawat jalan
		{
			if(PasienRecord::finder()->findByPk($this->notrans->Text))
			{				
				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql="SELECT a.cm AS cm, 
							a.no_trans AS alamat, 
							b.nama AS nama
				FROM tbt_rawat_jalan a, 
							tbd_pasien b 
				WHERE a.cm = '$tmp' 
							AND a.tgl_visit = '$dateNow' 
							AND a.cm=b.cm";
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				//$this->test3->Visible=false;
				//$this->test3->Text=$sql;//showing query
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);							
				$this->setViewState('no_trans_rwtjln',$tmpPasien->alamat);
				//$this->showSecond->Enabled=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;	
				$this->Page->CallbackClient->focus($this->DDObat);
				
				$this->lock();
			}
			else
			{
				//$this->showFirst->Visible=true;
				//$this->showSecond->Enabled =  false;
				$this->errMsg->Text='Data tidak ada!!';
				$this->Page->CallbackClient->focus($this->notrans);
			}
		}
		elseif($jnsPasien == '1')//Bila pasien rawat inap
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0'))
			{			

				$tmp = $this->notrans->Text;
				$dateNow = date('Y-m-d');
				$sql = "SELECT cm, nama FROM tbd_pasien WHERE cm='$tmp'";					 
				$tmpPasien = PasienRecord::finder()->findBySql($sql);
				$this->nama->Text= $tmpPasien->nama;			
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('nama',$tmpPasien->nama);											
				//$this->showSecond->Enabled=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';
				$this->DDObat->enabled=true;	
				$this->Page->CallbackClient->focus($this->DDObat);	
				
				$this->lock();
			}
			else
			{
				//$this->showFirst->Visible=true;
				//$this->showSecond->Enabled =  false;
				$this->errMsg->Text='Data tidak ada!!';
				$this->Page->CallbackClient->focus($this->notrans);	
			}
		}        
    }	
	
	public function embelChanged()
	{
		$embel = $this->embel->SelectedValue;		
		$this->setViewState('embel',$embel);
		if($this->embel->SelectedValue=='01') //Dengan Resep
		{
			$this->setViewState('jnsPasLuar','01');
			
			$this->dokter->Text=''; 
			
			$this->nmPasienPanel->Display = 'Dynamic';
			$this->dokterLuarPanel->Display = 'Dynamic';
			$this->secondPanel->Display = 'Dynamic';
			
			$this->dokterLuarPanel->Enabled = true;	
			
			if($this->modeKryPas->SelectedValue == '3')
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->nmPas->getClientID().'.focus();
				</script>';
			}
			else 
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->dokter->getClientID().'.focus();
				</script>';
			}
		}
		elseif($this->embel->SelectedValue=='02') //Beli sendiri
		{
			$this->dokter->Text=''; 
			//$this->showFirst->Visible=true;
			//$this->showSecond->Enabled=true;
			$this->setViewState('jnsPasLuar','02');
			//$this->poliCtrl->Enabled=true;
			$this->nmPasienPanel->Display = 'Dynamic';
			$this->dokterLuarPanel->Display = 'Dynamic';
			$this->secondPanel->Display = 'Dynamic';
			
			$this->dokterLuarPanel->Enabled = false;	
			
			if($this->modeKryPas->SelectedValue == '3')
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->nmPas->getClientID().'.focus();
				</script>';
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->modeStok->getClientID().'.focus();
				</script>';
			}
		}
		else
		{
			//$this->showFirst->Visible=true;
			//$this->showSecond->Enabled =  false;
			$this->clearViewState('jnsPasLuar');
			//$this->poliCtrl->Enabled=false;
			$this->nmPasienPanel->Display = 'None';
			$this->dokterLuarPanel->Display = 'None';
			$this->secondPanel->Display = 'None';
			
			$this->dokterLuarPanel->Enabled = false;
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->embel->getClientID().'.focus();
				</script>';	
		}
		
		$this->modeInput->Enabled = false;
		
	}
	
	public function tunaiCBchanged()
	{
		if($this->tunaiCB->Checked==true)
		{
			$this->setViewState('tunaiChecked','tunaiChecked');
		}
		else
		{
			$this->clearViewState('tunaiChecked');
		}
	}
	
	//Jika pasien Rwt Inap
	public function modeByrInapChanged()
	{
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);		
		$this->clearViewState('modeByrInap');
		$this->setViewState('modeByrInap',$modeByrInap);
		
	}
	
	//Jika pasien Karyawan
	public function modeKryPasChanged()
	{
		$modeKryPas = $this->collectSelectionResult($this->modeKryPas);		
		$this->clearViewState('modeKryPas');
		$this->setViewState('modeKryPas',$modeKryPas);	
		
		$this->embel->SelectedValue;
		
		if(($this->getViewState('jnsPasien') == '2') && ($this->getViewState('modeKryPas') <> '3')) //jika pasien Luar/OTC & status pasien bukan UMUM
		{			
			$this->notrans->Enabled = false;	
			$this->inapPanel->Display = 'Dynamic';	
			$this->DDNamaKar->Enabled = true;	
			$this->namaKarPanel->Display = 'Dynamic';
			$this->nmPas->Enabled = false;			
			//$this->Page->CallbackClient->focus($this->notrans);	
			
			if($modeKryPas == '0' )
			{
				$this->setViewState('kelompokPasien','04');	//kelompok pasien karyawan
			}
			elseif($modeKryPas == '1' )
			{
				$this->setViewState('kelompokPasien','05');	//kelompok pasien keluarga inti karyawan
			}
			elseif($modeKryPas == '2' )
			{
				$this->setViewState('kelompokPasien','06');	//kelompok pasien keluarga lain karyawan
			}
			
			$this->setViewState('stAsuransi','1');
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->embel->getClientID().'.focus();
				</script>';	
				
			
		}else{			
			$this->inapPanel->Display = 'None';	
			$this->namaKarPanel->Display = 'None';
			$this->DDNamaKar->Enabled = false;			
			$this->nmPas->Enabled = true;
			//$this->notrans->Text = '';
			
			$this->clearViewState('stAsuransi');
		}	
		
		$this->errMsg->Text='';
	}	
	
	public function modeStokChanged($sender,$param)
	{
		$modeStok = $this->modeStok->SelectedValue;	
		$this->clearViewState('modeStok');
		$this->setViewState('modeStok',$modeStok);
		$this->Page->CallbackClient->focus($this->DDObat);
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$this->setViewState('jnsPasien',$jnsPasien);
		
		if($jnsPasien == '1') //Rawat Inap
		{
			$tujuan = $this->modeStok->SelectedValue;
		}
		elseif ($jnsPasien == '0') // Rawat jalan
		{	
			$klinik=$this->DDKlinik->SelectedValue;		
			if ($klinik=='07'){
				//$tujuan='5';
				$tujuan = $this->modeStok->SelectedValue;
			}elseif ($klinik=='17'){
				$tujuan='6';
			}else{
				$tujuan='14';//pasien rawat jalan selain IGD dan OK ambil stok apotik
			}
		}
		elseif ($jnsPasien == '2') //Pasien Luar
		{
			$tujuan='14';//Pasien luar ambil stok dari apotik
		}
		elseif ($jnsPasien == '3') //One Day Service
		{
			$tujuan = $this->modeStok->SelectedValue;
		}
		
		//$this->test->Text=$tujuan;
		//$this->test->Visible=true;//showing tujuan
		
		
		$sql=	"SELECT	a.kode AS kode,
						a.nama AS nama 
				FROM 	tbm_obat a, 
						tbt_stok_lain b 
				WHERE a.kode=b.id_obat 
					AND b.jumlah >= 1
					AND b.sumber='01' 
				";					
				
		if($tujuan <> '')			
				$sql .= " AND b.tujuan='$tujuan'  ";
		
		$sql .= " GROUP BY a.kode ORDER BY a.nama ASC "; 		
		//$this->showSql->text=$sql;
		
		$arr=$this->queryAction($sql,'S');
		$jmlData=count($arr);	
		if($jmlData>0)
		{
			$this->DDObat->DataSource=$arr;		
			$this->DDObat->dataBind();
			$this->DDObat->Enabled=true;	
			//$this->jml->Enabled=true;	
		}
		else
		{
			//$this->test->text=$sql;
			$this->DDObat->Enabled=false;	
			$this->jml->Enabled=false;	
		}	
	}
	
	public function tipeRacikChanged($sender,$param)
	{
		$this->jmlBungkus->Text = '';
		$tipeRacik = $this->collectSelectionResult($this->RBtipeRacik);
		$this->setViewState('tipeRacik',$tipeRacik);	
		if($tipeRacik == '0') //Non Racikan
		{
			$this->DDRacik->Enabled = false;
			$this->jmlBungkus->Enabled = false;
			//$this->jmlBungkus->Enabled = false;
			//$this->Page->CallbackClient->focus($this->RBtipeObat);
			
		}
		elseif($tipeRacik == '1') //racikan
		{
			$this->setViewState('loaderRacik',0);
			$this->DDRacik->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDRacik);
			
			if (!$this->getViewState('nmTable'))
			{
				$data[]=array('id'=>'0','nama'=>'Buat Kelompok Baru');
				
				$this->DDRacik->DataSource = $data;
				$this->DDRacik->dataBind();
				$this->DDRacik->SelectedValue = '0';
				
				$this->jmlBungkus->Enabled = true;
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');
				$data[]=array('id'=>'0','nama'=>'Buat Kelompok Baru');
				
				$sql = "SELECT id_kel_racik FROM $nmTable WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id_kel_racik ";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//sudah ada obat racikan
				{
					foreach($arr as $row)
					{
						$idKelRacik = $row['id_kel_racik'];
						$data[]=array('id'=>$idKelRacik,'nama'=>'Racikan '.$idKelRacik);
					}
					
					$this->jmlBungkus->Enabled = false;
				}
				else //belum ada obat racikan
				{
					$idKelRacik = '0';
					$this->jmlBungkus->Enabled = true;
				}
								
				$this->DDRacik->DataSource = $data;
				$this->DDRacik->dataBind();
				$this->DDRacik->SelectedValue = $idKelRacik;
			}
		}
		
		else //Imunisasi
		{
			$this->DDRacik->Enabled = true;
			$this->jmlBungkus->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDRacik);
							
			$this->DDRacik->DataSource = ImunisasiRecord::finder()->findAll();
			$this->DDRacik->dataBind();
		}
	}
	
	public function DDRacikChanged($sender,$param)
	{
		if($this->DDRacik->SelectedValue != '')
		{
			if($this->DDRacik->SelectedValue == '0')//bikin racikan baru
			{
				$loaderDDRacik = 
				$this->jmlBungkus->Enabled = true;
				$this->Page->CallbackClient->focus($this->jmlBungkus);
			}
			else
			{
				$this->jmlBungkus->Enabled = false;
				$this->Page->CallbackClient->focus($this->tambahBtn);
			}	
		}
		else
		{
			$this->jmlBungkus->Enabled = false;
			$this->Page->CallbackClient->focus($this->DDRacik);
		}	
		
		$this->jmlBungkus->Text = '';		
	}
	
	public function chTipe()
	{
		//$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		//$this->DDSumMas->focus();		
	}	
	
	public function chKateg()
	{		
		if($this->DDKateg->SelectedValue=='01') //jika kelompok obat yg dipilih
		{			
			$this->Page->CallbackClient->focus($this->RBtipeRacik);
			$this->RBtipeRacik->Enabled=true;
			$this->RBtipeObat->Enabled=true;
		}
		else //jika kelompok alkes atau BHP yg dipilih
		{
			$this->RBtipeRacik->SelectedIndex=-1;
			$this->RBtipeRacik->Enabled=false;
			$this->RBtipeObat->SelectedIndex=-1;
			$this->RBtipeObat->Enabled=false;
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			$this->Page->CallbackClient->focus($this->DDObat);
		}
		
		//$this->DDObat->enabled=true;
		$this->setViewState('kategori',$this->DDKateg->SelectedValue);
		$this->clearViewState('tipe');
		$this->clearViewState('jenis');
	}
	/*
	public function DDSumMasChanged()
	{		
		$sumMas = $this->DDSumMas->SelectedValue;		
		$this->setViewState('sumMas',$sumMas);
		$this->DDObat->Enabled=true;	
	}
	
	public function DDSumSekChanged()
	{
		$sumSek = $this->DDSumSek->SelectedValue;		
		$this->setViewState('sumSek',$sumSek);
		
		$this->DDObat->Enabled=true;
	 
	}*/
	
	public function chJenis()
	{		
		if($this->DDJenis->SelectedValue!='') //jika kelompok obat yg dipilih
		{			
			$jnsObat = $this->DDJenis->SelectedValue;
			$this->setViewState('jenis',$jnsObat);
			$this->DDObat->enabled=true;	
		}
		else 
		{
			$this->clearViewState('jenis');
			$this->DDObat->enabled=false;
		}
		
	}
	
	public function chObat()
	{
		if($this->DDObat->SelectedValue != '')
		{
			$idObat = $this->DDObat->SelectedValue;
			$tujuan = $this->modeStok->SelectedValue;
			//$this->errMsg->Text=$idObat .' - '.$tujuan;
			//Di Non aktifkan karena Alkes juga bisa masuk dalam Imunisasi
			//if(ObatRecord::finder()->findByPk($idObat)->kategori == '01') //jika kelompok obat yg dipilih
			//{	
				$this->RBtipeRacik->Enabled=true;
				
			//}
			//else //jika kelompok alkes atau BHP yg dipilih
			//{
				//$this->RBtipeRacik->SelectedIndex=-1;
				//$this->RBtipeRacik->Enabled=false;
				//$this->DDRacik->Enabled=false;
				//$this->jmlBungkus->Enabled=false;
				//$this->jmlBungkus->Text='';
			//}
			
			//$this->test->text=$this->DDObat->SelectedValue;
			//

			$this->jml->Enabled=true;
			$this->msgStok->Text='';
			//$this->jml->text=$this->getViewState('tujuan');
			$this->setViewState('idObat',$$idObat);
			$this->DDObat->SelectedValue = $idObat ;	
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->jml->getClientID().'.focus();
				</script>';	
		}
		else
		{
			$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled=false;
			$this->DDRacik->Enabled=false;
			$this->DDRacik->SelectedIndex = -1;
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			$this->clearViewState('idObat');
		}
	}
	
	public function lock()
    {
		$this->modeInput->enabled=false;
		$this->modeByrInap->enabled=false;
		$this->embel->enabled=false;
		$this->DDKlinik->enabled=false;
		$this->DDDokter->enabled=false;
		$this->DDDokter->enabled=false;
	}
	
	/*
	public function makeTmpTblBhp($idBhp,$namaBhp,$bhp)
	{
		if (!$this->getViewState('nmTableBhp'))
		{			
			$nmTableBhp = $this->setNameTable('nmTableBhp');
			$sql="CREATE TABLE $nmTableBhp (id INT (2) auto_increment, 										 
										 id_bhp VARCHAR(5) NOT NULL,	
										 nama VARCHAR(100) NOT NULL,								 
										 bhp FLOAT NOT NULL,
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...		
		}
		else//Tabel sudah eksis!!
		{
			$nmTableBhp = $this->getViewState('nmTableBhp');							
		}		
		
		$sql="INSERT INTO $nmTableBhp
				(id_bhp,
				nama,
				bhp) 
			VALUES 
				('$idBhp',
				'$namaBhp',
				'$bhp')";
		//$this->errMsg->Text=$sql;
		$this->queryAction($sql,'C');//Insert new row in tabel bro...			
	}
	*/
	
	public function makeTmpTbl($id_harga,$jmlAmbil)
	{
		$this->showGrid->Visible=true;	
		$this->msgStok->Text='';
		$kelompokPasien = $this->getViewState('kelompokPasien');
		$stAsuransi = $this->getViewState('stAsuransi');
		$tipeRacik = $this->getViewState('tipeRacik');
		$jnsPasien = $this->getViewState('jnsPasien');
		$resepHrg=$this->getViewState('resepHrg');
		$racikHrg=$this->getViewState('racikHrg');
		$hrgTmp = 0;//Make initial value for $hrg
		$jml= $jmlAmbil;
		
		
		if($this->getViewState('modeKryPas') == '')
		{
			$modeKryPas = $this->collectSelectionResult($this->modeKryPas);
			$this->setViewState('modeKryPas',$modeKryPas);
			
		}	
			
			$idObat = $this->DDObat->SelectedValue;
			$tipe =  ObatRecord::finder()->findByPk($idObat)->tipe;
			$kategori = ObatRecord::finder()->findByPk($idObat)->kategori;
			$idKelompok = ObatRecord::finder()->findByPk($idObat)->kel_obat;
			
			if($kelompokPasien == '02'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
			{
				$persenMargin = ObatKelompokRecord::finder()->findByPk($idKelompok)->persentase_asuransi / 100;
			}
			elseif($kelompokPasien == '07'  && $stAsuransi == '1') //kelompok pasien asuransi yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya))
			{
				$persenMargin = ObatKelompokRecord::finder()->findByPk($idKelompok)->persentase_jamper / 100;
			}
			else
			{
				$persenMargin = ObatKelompokRecord::finder()->findByPk($idKelompok)->persentase / 100;
			}
			
			
			//$tujuan = $this->getViewState('tujuan');
			//$tujuan = $this->modeStok->SelectedValue;			
			
			//jika obat dibeli dari APOTIK LUAR => set persenMargin = 15%
			if(HrgObatRecord::finder()->findByPk($id_harga)->st_pembelian == '1')
			{
				$persenMargin = 15 / 100;
			}
			
			
			if($jnsPasien == '2')//Bila pasien luar
			{
				$tujuan = '14';
			}
			else //Bila pasien rawat inap atau rawat jalan
			{
				$tujuan = $this->modeStok->SelectedValue;
			}
			
			/*
			if($this->getViewState('sumMas') == '2'){	
				$sumber = $this->getViewState('sumMas') . $this->getViewState('sumSek');
			}else{
				$sumber = $this->getViewState('sumMas');
			}*/
			
			//$this->errMsg->Text = $kelompokPasien .' - '. $jnsPasien.' - '.$tipeRacik;//showing status kelompok pasien
			
			$jmlObat = $jmlAmbil;
			
			//jika dalam tbt_obat_harga terdapat lebih dari satu harga untuk item yg sama => pilih item yg id nya paling besar
			//$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$idObat'";
			//$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
						
			$sql="SELECT a.kode AS kode, 
					 a.nama AS nama, 
					 c.hrg_ppn AS pbf,
					 c.id AS gol,
					 a.kel_obat AS tipe
				  FROM tbm_obat a, 
					  tbt_stok_lain b, 
					  tbt_obat_harga c, 
					  tbm_satuan_obat d
				  WHERE a.kode=b.id_obat 
						AND a.kode=c.kode 
						AND a.kode='$idObat'
						AND b.sumber=c.sumber 
						AND b.sumber='01' 
						AND b.tujuan='$tujuan'								
						AND b.jumlah >= '$jmlObat'
						AND c.id = '$id_harga' GROUP BY a.kode";
						
			//$this->errMsg->Text=$sql;
			$tmpTarif = ObatRecord::finder()->findBySql($sql);	
			//$this->test->text=$sql;			 		
			$id=$tmpTarif->kode;
			$nama=$tmpTarif->nama;				
			
			//$check=HrgObatKhususRecord::finder()->findByPk($idObat)->hrg_jual;				
			$sql = "SELECT id, hrg_jual, tgl FROM tbm_obat_hrg_khusus WHERE id_obat = '$idObat' ";
			$arr = $this->queryAction($sql,'S');
			
			if (count($arr) > 0)
			{
				$hrg1 = HrgObatKhususRecord::finder()->findBySql($sql)->hrg_jual;
				//$idHarga = HrgObatKhususRecord::finder()->findBySql($sql)->id;
				$this->setViewState('hrg1',$hrg1);
				$idHarga=$id_harga ;
				//$stKhusus='1' ;
				$this->setViewState('stKhusus','1');
				$hrg=$tmpTarif->pbf;
			}else{
				$idHarga=$id_harga ;
				$hrg=$tmpTarif->pbf ;				
				$tipe=$tmpTarif->tipe;
				//$stKhusus='0';
				$this->setViewState('stKhusus','0');
			}
				
		if (!$this->getViewState('nmTable'))
		{			
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(100) NOT NULL,
										 id_obat VARCHAR(5) NOT NULL,									 
										 jml INT(11) NOT NULL,
										 hrg FLOAT(11,2) NOT NULL,
										 hrg_bulat FLOAT(11,2) NOT NULL,
										 total FLOAT(11,2) NOT NULL,	
										 total_real FLOAT(11,2) NOT NULL,
										 id_kel_racik INT(11) NOT NULL,
										 r_item FLOAT(11,2) NOT NULL,
										 bungkus_racik FLOAT(11,2) NOT NULL,
										 tujuan CHAR(2) NOT NULL,								 							 
										 id_harga VARCHAR(20) NOT NULL,
										 st_obat_khusus CHAR(1) DEFAULT '0',
										 st_racik CHAR(1) DEFAULT '0',
										 st_imunisasi CHAR(1) DEFAULT '0',								 							 										 										 id_kel_imunisasi INT(11) NOT NULL,
										 st_bayar CHAR(1) DEFAULT '0',
										 id_bhp INTEGER(11) DEFAULT NULL,
										 nama_bhp VARCHAR(50) DEFAULT NULL,
										 bhp FLOAT DEFAULT '0',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...		
			
			
											
		}
		else//Tabel sudah eksis!!
		{
			$nmTable = $this->getViewState('nmTable');							
		}	
		
		//$this->showSql->Text=$this->getViewState('nmTable');	
		
								
		//Do some calculation for drug
		if(($kelompokPasien == '04' || $kelompokPasien == '05' || $kelompokPasien == '06') && $stAsuransi == '1') //kelompok pasien adalah karyawan atau keluarga inti atau keluarga lain yg tbt_rawat_jalan.st_asuransi = '1' (berlaku status nya)
			{	
				if($kelompokPasien == '04')//karyawan yg bersangkutan lgsg
				{
					$r_item = 0;
					$r_racik = 0;
				}
				else if($kelompokPasien == '05')//Keluarga Inti suami, istri anak
				{
					$r_item = 0;
					$r_racik = 0;
				}
				else if($kelompokPasien == '06')//Diluar keluarga inti
				{
					//$r_item = 1000;
					$r_item = 0;
					//$r_racik = 3000;
				}	
				
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
					{
						$hrgTmp += $hrg;
					}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%							
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
							$hrgTmp += $hrgKhusus;
						}	
					}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
							$hrgTmp += $hrgKhusus;
						}
					}
				}
				else if($jnsPasien == '2')//Obat OTC - Jual Bebas 	
				{			
					if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
					{
						$hrgTmp += $hrg;
					}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
							$hrgTmp += $hrgKhusus;
						}	
					}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
						if ($this->getViewState('stKhusus') == '0')
						{
							//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
							$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
						}else if ($this->getViewState('stKhusus') == '1'){	
							$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
							$hrgTmp += $hrgKhusus;
						}
					}
				}
				else if($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					if($kelas == '1' || $kelas == '2' || $kelas == '3') //kelas VIP atau kelas IA atau IB
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.4); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.4) - (($hrg * 1.4)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.4); //ini aturan main real-nya kurangi 5%
								$hrgTmp += ($hrg * 1.4) - (($hrg * 1.4)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}
					}
					else if($kelas == '4' || $kelas == '5') //kelas II atau kelas III
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5%
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}						
					}					
				}				
				else if($jnsPasien == '3')//One Day Service
				{
					$noTrans = $this->getViewState('notransinap');
					$hrgObatPaket = $this->getViewState('hrgObatPaket');
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$tmp'
								   AND no_trans_inap = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAktual = $row['jumlah'];
					}		
					
					if($jmlObatAktual <= $hrgObatPaket) //jika jml obat aktual belum melebihi plafon jml obat paket
					{ 
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.2); //ini aturan main real-nya kurangi 10%
								$hrgTmp += ($hrg * 1.2) - (($hrg * 1.2)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.2); //ini aturan main real-nya kurangi 5% 
								$hrgTmp += ($hrg * 1.2) - (($hrg * 1.2)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrgTmp += $hrg;
						}else if($kelompokPasien=='05'){//Pasien adalah keluraga inti Karyawan itu sendiri (Harga jual - 10%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 10% jadi pengali menjadi 0.2
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.1);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.1 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 10%
								$hrgTmp += $hrgKhusus;
							}	
						}else if($kelompokPasien=='06'){//Pasien adalah keluraga lain Karyawan itu sendiri (Harga jual - 5%)
							if ($this->getViewState('stKhusus') == '0')
							{
								//$hrgTmp += $hrg +(($hrg* 0.3); //ini aturan main real-nya kurangi 5% jadi pengali menjadi 0.2
								$hrgTmp += ($hrg * 1.3) - (($hrg * 1.3)*0.05);
							}else if ($this->getViewState('stKhusus') == '1'){	
								$hrgKhusus = ($this->getViewState('hrg1') - (0.05 * $this->getViewState('hrg1')));//Harga obat khusus di diskon 5%
								$hrgTmp += $hrgKhusus;
							}
						}	
					}	
				}			
				
				if($tipeRacik == '2') //JIka Paket Imunisasi
				{					
					if($this->getViewState('st_imunisasi') == '1'){ 
						if($this->getViewState('id_kel_imunisasi') == $this->DDRacik->SelectedValue)  {
							$hrg=0;
						}
						else
						{
							$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+500; //ditambah r_item Rp.500 karena karyawan
							if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
							{
								$hrg -= $hrg;
							}else if($kelompokPasien == '05')//Pasien adalah keluarga inti Karyawan itu sendiri (Harga Jual - 10%)
							{
								$hrg -= $hrg * 0.1;
							}else if ($kelompokPasien == '06')//Pasien adalah Keluarga lain Karyawan itu sendiri (harga jual - 5%)
							{
								$hrg -= $hrg * 0.05;
							}else if ($this->getViewState('modeKryPas') == '3')
							{
								$hrg;
							}		
						}								
					}
					else
					{
						$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000						
						if ($kelompokPasien == '04')//Pasien adalah Karyawan itu sendiri (Nett + PPn)
						{
							$hrg -= $hrg;
						}else if($kelompokPasien == '05')//Pasien adalah keluarga inti Karyawan itu sendiri (Harga Jual - 10%)
						{
							$hrg -= $hrg * 0.1;
						}else if ($kelompokPasien == '06')//Pasien adalah Keluarga lain Karyawan itu sendiri (harga jual - 5%)
						{
							$hrg -= $hrg * 0.05;
						}else if ($this->getViewState('modeKryPas') == '3')
						{
							$hrg;
						}
					}				
					$hrgTmp = $hrg;
					$st_imunisasi = 1;							
					$id_kel_imunisasi = $this->DDRacik->SelectedValue;
					$this->setViewState('st_imunisasi','1');
					$this->setViewState('id_kel_imunisasi',$this->DDRacik->SelectedValue);
				}
				else if($tipeRacik == '1')//Jika Racikan
				{
					$idKelRacik = $this->DDRacik->SelectedValue;
					if($idKelRacik == '0')//kelompok baru
					{
						$sql = "SELECT MAX(id_kel_racik) AS id_kel_racik FROM $nmTable WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id ";
						$arr = $this->queryAction($sql,'S');
						if(count($arr) > 0) //sudah ada kel racik
						{
							foreach($arr as $row)
							{
								$idKelRacik = $row['id_kel_racik'] + 1;
							}	
							$r_item = $r_racik;//r_obat racikan untuk karyawan 1000
							$bungkus_racik = 200 * $this->jmlBungkus->Text;						
						}else{
							$idKelRacik = 1;	
							$r_item = $r_racik;//r_obat racikan untuk karyawan 1000
							$bungkus_racik = 200 * $this->jmlBungkus->Text;													
						}	
						
					}
					else
					{
						//Bila ada opsi delete terhadap obat racikan yg hrgnya sdh termasuk 
						//kombinasi /R dan bungkus_racik
						if($this->getViewState('resepHrg') > 0)
						{
							$r_item=$resepHrg;
						}
						else
						{
							$r_item;
						}		
						if($this->getViewState('racikHrg') > 0)
						{
							$bungkus_racik = $racikHrg;
						}
						else
						{
							$bungkus_racik = 0;
						}						
						
					}
									
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;					
					$this->clearViewState('resepHrg');
					$this->clearViewState('racikHrg');
					$this->setViewState('st_racikan','1');
					$this->setViewState('id_kel_racikan',$this->DDRacik->SelectedValue);
				}else{
					$idKelRacik = 0;
					/* Perhitungan /R aktifkan modul ini ini bila OTC tanpa /R*/
					if(($this->getViewState('jnsPasien') == '2') && ($this->getViewState('embel')== '02'))//Obat pasienluar tanpa resep dokter
					{
						$r_item;//r_obat bukan racikan untuk karyawan Rp 0,-
					}else{
						$r_item;//r_obat bukan racikan untuk karyawan Rp 500,-	
					}				
					//*/	
					//$r_item = 500;//Disable nilai /R ini bila modul OTC diatas diaktifkan
					$st_racik = 0;
					$bungkus_racik = 0;					
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;				
				}				
					
			//--BILA PASIEN ADALAH BUKAN KARYAWAN--//	
			
			}
			else
			{// Bila kelompok pasien adalah bukan karyawan!!
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($this->getViewState('stKhusus') == '0')
					{
						//$hrgTmp += $hrg + ($hrg * 0.3);
						$hrgTmp += $hrg + ($hrg * $persenMargin);
					}else if($this->getViewState('stKhusus')=='1'){
						$hrgTmp += $this->getViewState('hrg1');
					}
				}
				else if($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					if($kelas == '1') //kelas VIP 
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.35);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '2') //kelas IA
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.15);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '3') //kelas IB
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.1);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '4') //kelas II
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.05);
						}else if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '5') //kelas III
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0);
						}else  if($this->getViewState('stKhusus')=='1'){
							$hrgTmp += $this->getViewState('hrg1');
						}						
					}
					
					/*
					//Penambahan BHP khusus untuk rawat inap
					$lamaInap = $this->getViewState('lamaInap');
					if($lamaInap <= 3) //kurang atau sama dengan 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgTmp += 150000;
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgTmp += 125000;
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgTmp += 125000;
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgTmp += 100000;
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgTmp += 70000;
						}
					}
					else //lebih dari 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgTmp += (150000 + (150000 * 0.5));
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgTmp += (125000 + (125000 * 0.5));
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgTmp += (125000 + (125000 * 0.5));
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgTmp += (100000 + (100000 * 0.5));
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgTmp += (70000 + (70000 * 0.5));
						}
					}
					*/
				}
				else if($jnsPasien == '2')//Pasien Luar/ODC
				{
					if ($this->getViewState('stKhusus') == '0')
					{
						$hrgTmp += $hrg+($hrg * $persenMargin);
					}else if($this->getViewState('stKhusus') == '1'){
						$hrgTmp = $this->getViewState('hrg1');
					}
				}
				else if($jnsPasien == '3')//One Day Service
				{
					$noTrans = $this->getViewState('notransinap');
					$hrgObatPaket = $this->getViewState('hrgObatPaket');
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$tmp'
								   AND no_trans_inap = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAktual = $row['jumlah'];
					}		
					
					if($jmlObatAktual <= $hrgObatPaket) //jika jml obat aktual belum melebihi plafon jml obat paket
					{ 
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.2);
						}else if($this->getViewState('stKhusus') == '1'){
							$hrgTmp = $this->getViewState('hrg1');
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($this->getViewState('stKhusus') == '0')
						{
							$hrgTmp += $hrg+($hrg* 0.3);
						}else if ($this->getViewState('stKhusus') == '1'){
							$hrgTmp = $this->getViewState('hrg1');
						}	
					}	
				}
				
				if($tipeRacik == '2') //JIka Paket Imunisasi
				{					
					if($this->getViewState('st_imunisasi') == '1'){ 
						if($this->getViewState('id_kel_imunisasi') == $this->DDRacik->SelectedValue)  {
							$hrg=0;
						}
						else
						{
							$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000
						}								
					}
					else
					{
						$hrg = ImunisasiRecord::finder()->findByPk($this->DDRacik->SelectedValue)->harga+1500; //ditambah r_item Rp.1000						
					}				
					$hrgTmp = $hrg;
					$st_imunisasi = 1;							
					$id_kel_imunisasi = $this->DDRacik->SelectedValue;
					$this->setViewState('st_imunisasi','1');
					$this->setViewState('id_kel_imunisasi',$this->DDRacik->SelectedValue);
				}
				else if($tipeRacik == '1')//Jika Racikan
				{
					$idKelRacik = $this->DDRacik->SelectedValue;
					if($idKelRacik == '0')//kelompok baru
					{
						$sql = "SELECT MAX(id_kel_racik) AS id_kel_racik FROM $nmTable WHERE st_racik='1' GROUP BY id_kel_racik ORDER BY id ";
						$arr = $this->queryAction($sql,'S');
						if(count($arr) > 0) //sudah ada kel racik
						{
							foreach($arr as $row)
							{
								$idKelRacik = $row['id_kel_racik'] + 1;
							}	
							//$r_item = 3000;
							$r_item = 0;
							$bungkus_racik = 200 * $this->jmlBungkus->Text;						
						}else{
							$idKelRacik = 1;	
							//$r_item = 3000;
							$r_item = 0;
							$bungkus_racik = 200 * $this->jmlBungkus->Text;													
						}	
						
					}
					else
					{
						//Bila ada opsi delete terhadap obat racikan yg hrgnya sdh termasuk 
						//kombinasi /R dan bungkus_racik
						if($resepHrg > 0)
						{
							$r_item=$resepHrg;
						}
						else
						{
							$r_item;
						}		
						if($racikHrg > 0)
						{
							$bungkus_racik = $racikHrg;
						}
						else
						{
							$bungkus_racik = 0;
						}
						
					}
									
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;	
					$this->clearViewState('resepHrg');
					$this->clearViewState('racikHrg');				
					$this->setViewState('st_racikan','1');
					$this->setViewState('id_kel_racikan',$this->DDRacik->SelectedValue);
				}else{
					$idKelRacik = 0;
					/* Perhitungan /R aktifkan modul ini ini bila OTC tanpa /R*/
					if(($this->getViewState('jnsPasien') == '2') && ($this->getViewState('embel')== '02'))//Obat pasienluar tanpa resep dokter
					{
						$r_item = 0;//r_obat bukan racikan untuk karyawan Rp 500,-
					}else{
						//$r_item = 1000;//r_obat bukan racikan untuk karyawan Rp 500,-	
						$r_item = 0;
					}				
					//*/	
					//$r_item = 1500;//Disable nilai /R ini bila modul OTC diatas diaktifkan
					$st_racik = 0;
					$bungkus_racik = 0;					
					$st_imunisasi = 0;
					$id_kel_imunisasi = 0;				
				}				
					
			}// End of tipe pasien bukan Karyawan	
			
						
		$hrg_bulat = $this->bulatkan($hrgTmp);
		//$hrg_bulat = ($hrgTmp);
		$hrgJual_real = $hrgTmp * $jml;
		$hrgJual_bulat = $this->bulatkan($hrg_bulat * $jml);
		//$hrgJual_bulat = ($hrg_bulat * $jml);
		$total = $hrgJual_real;//$hrgJual_bulat;
		$total = $this->bulatkan($total + $r_item + $bungkus_racik);
		$hrgJual_real = $hrgJual_real + $r_item + $bungkus_racik;						
		if($this->getViewState('totSementara'))
		{
			$totSementara=$this->getViewState('totSementara');
		}else{
			$totSementara=0;
		}			
		$totSementara += $total;
		
		//-----------jika ada BHP --------------
		if($this->DDBhp->SelectedValue != '')
		{
			$idBhp = $this->DDBhp->SelectedValue;
			$namaBhp = $this->ambilTxt($this->DDBhp);
			$hargaBhp = ObatBhpRecord::finder()->findByPk($idBhp)->bhp;
		}
		else
		{
			$idBhp = '';
			$namaBhp = '';
			$hargaBhp = '0';
		}
		
		$totSementara += $hargaBhp;
		
		$this->setViewState('totSementara',$totSementara);
		$this->totSementara->Text = $this->getViewState('totSementara');
		$stBayar=$this->getViewState('modeBayar');	
		//$this->setViewState('total',$total);
		//$this->errMsg->Text=$id.'-'.$nama.'-'.$jml.'-'.$hrg.'-'.$hrg_bulat.'-'.$total.'-'.$hrgJual_real.'-'.$tujuan.'-'.$idHarga.'-'.$stKhusus.'-'.$tipeRacik.'-'.$idKelRacik.'-'.$r_item.'-'.$bungkus_racik.'-'.$st_imunisasi.'-'.$id_kel_imunisasi;
		$sql="INSERT INTO $nmTable
				(id_obat,
				nama,
				jml,
				hrg,
				hrg_bulat,
				total,
				total_real,
				tujuan,
				id_harga,
				st_obat_khusus,
				st_racik,
				id_kel_racik,
				r_item,
				bungkus_racik,
				st_imunisasi,
				id_kel_imunisasi,
				st_bayar,
				id_bhp,
				nama_bhp,
				bhp) 
			VALUES 
				('$id',
				'$nama',
				'$jml',
				'$hrg',
				'$hrg_bulat',
				'$total',
				'$hrgJual_real',
				'$tujuan',
				'$idHarga',
				'$stKhusus',
				'$tipeRacik',
				'$idKelRacik',
				'$r_item',
				'$bungkus_racik',
				'$st_imunisasi',
				'$id_kel_imunisasi',
				'$stBayar',
				'$idBhp',
				'$namaBhp',
				'$hargaBhp')";
		//$this->errMsg->Text=$sql;
		$this->queryAction($sql,'C');//Insert new row in tabel bro...			
			
		$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik,id_kel_racik,st_imunisasi,id_kel_imunisasi FROM $nmTable ORDER BY id_kel_racik,id_kel_imunisasi, id";
			
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();
		
		
		$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";
		$arr = $this->queryAction($sql,'S');
		
		//jika ada BHP
		if (count($arr) > 0)
		{	
			$this->BhpGrid->DataSource = $arr ;//Insert new row in tabel bro...
			$this->BhpGrid->dataBind();
		}
		
		$this->cetakBtn->Enabled = true;
		
		$this->jmlBungkus->Text = '';
		$this->jmlBungkus->Enabled = false;		
		
		if($this->getViewState('total')){
			$t = (int)$this->getViewState('total') + $total;
			$this->setViewState('total',$t);
		}else{
			$this->setViewState('total',$total);
		}	
		
		if($this->getViewState('hrg1'))
		{
			$this->clearViewState('hrg1');
		}
					
		$this->hrgShow->Text='Rp. '.number_format($this->getViewState('total'),'2',',','.');
		
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
		//if($this->getViewState('modeByrInap')){
			if($modeByrInap == 1)
			{
				$this->showBayar->Display = 'Hidden';					
			}
			else
			{
				$this->showBayar->Enabled=true;
				$this->cetakBtn->Enabled = false;	
			}
		//}
		
		
		
		
		$this->jml->Text = '';
		$this->DDObat->SelectedIndex = -1;
		$this->DDBhp->SelectedIndex = -1;
		$this->RBtipeRacik->SelectedIndex = -1;
		$this->RBtipeRacik->Enabled = false;
		$this->DDRacik->Enabled = false;
		$this->DDRacik->SelectedIndex = -1;
		$this->Page->CallbackClient->focus($this->DDObat);
		
		if($this->getViewState('jnsPasLuar'))
		{
			$this->dokter->Text = $this->getViewState('nmDokter') ;
			$this->nmPas->Text =$this->getViewState('nmPasien');
		}
		
		
	}
	
	
	public function normalHargaApotikLuar($idObat)
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
		
		
		$sql = "SELECT * FROM tbt_stok_lain WHERE id_obat = '$idObat' AND id_harga = '$idHargaApotik' ORDER BY id_harga DESC";
		$arr = $this->queryAction($sql,'S');
		
		if($arr)
		{	
			foreach($arr as $row)
			{
				$jmlTambahan = $row['jumlah'];
				$tujuan = $row['tujuan'];
				
				$sql2 = "SELECT * FROM tbt_stok_lain WHERE id_obat = '$idObat' AND id_harga != '$idHargaApotik' AND tujuan = '$tujuan' ORDER BY id_harga DESC";
				$arr2 = $this->queryAction($sql2,'S');
				
				if($arr2)
				{	
					foreach($arr2 as $row2)
					{
						$idHarga = $row2['id_harga'];
						$jmlAwal = $row2['jumlah'];
						$jmlAkhir = $jmlAwal + $jmlTambahan;
						
						$sqlUpdate = "UPDATE tbt_stok_lain SET jumlah = '$jmlAkhir' WHERE id_obat = '$idObat' AND id_harga = '$idHarga' AND tujuan = '$tujuan' ";
						$this->queryAction($sqlUpdate,'C');
						
						$sqlUpdate = "UPDATE tbt_stok_lain SET jumlah = '0' WHERE id_obat = '$idObat' AND id_harga = '$idHargaApotik' AND tujuan = '$tujuan' ";
						$this->queryAction($sqlUpdate,'C');
					}	
				}
			}	
		}
	}
	
	
	
	public function cekStok()
    {
		$jmlObat = $this->jml->Text;	
		$this->setViewState('jmlKekurangan',$jmlObat);				
		//$tujuan = $tujuan;
		//$tujuan=$this->getViewState('tujuan');
		//$tujuan = $this->modeStok->SelectedValue;
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '2')//Bila pasien luar
		{
			$tujuan = '14';
		}
		else //Bila pasien rawat inap atau rawat jalan
		{
			$tujuan = $this->modeStok->SelectedValue;
		}
		
		//$this->test2->Text=$tujuan;
		//$tmpStok = StokLainRecord::finder()->findById_obat($this->DDObat->SelectedValue)->jumlah;
		$idBarang = $this->DDObat->SelectedValue;
		
		$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$idBarang' AND tujuan = '$tujuan' GROUP BY id";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tmpStok += $row['jumlah'];
		}	
				
		//$tmpStok = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$this->DDObat->SelectedValue,$tujuan)->jumlah;
		//$this->test->text = $tmpStock;
		
		if($tmpStok >= $jmlObat)
		{
			//cari jumlah minimal 
			$idTujuan = $this->modeStok->SelectedValue;
			$nmFieldMin = 'min_'.$idTujuan;
			$nmFieldTol = 'tol_'.$idTujuan;
			$sql="SELECT $nmFieldMin, $nmFieldTol  FROM tbm_obat WHERE kode ='$idBarang' ";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlMinimal = $row[$nmFieldMin]; 
				$persenTol = $row[$nmFieldTol];
			}
			
			//Periksa jml toleransi minimal
			$jmlStokTol = ($persenTol / 100) * $jmlMinimal;
			$nmBarang = $this->ambilTxt($this->DDObat);
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($this->modeStok->SelectedValue)->nama;
			
			if( ($tmpStok-$jmlObat) < $jmlStokTol)//jika sudah melewati batas toleransi
			{
				$this->msgStok->Text='<br/><br/>Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' mencapai <b>Batas Toleransi Minimal</b>. <br/>Penambahan Obat Gagal';
				
				$this->jml->Text = '';
			}
			else //belum melewati batas toleransi
			{
				//$sql = "SELECT jumlah, id_harga FROM tbt_stok_lain WHERE id_obat='$idBarang' ORDER BY id_harga";
				$sql = "SELECT jumlah, id_harga FROM tbt_stok_lain WHERE id_obat='$idBarang' AND tujuan = '$tujuan' ORDER BY id_harga DESC"; 
				$arr = $this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					if($row['jumlah'] <> 0)
					{
						if($row['jumlah'] > $this->getViewState('jmlKekurangan'))
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_harga = $row['id_harga'];
								$jmlAmbil = $this->getViewState('jmlKekurangan');
								$this->setViewState('jmlKekurangan','0');
								$this->makeTmpTbl($id_harga,$jmlAmbil);
								//$this->errMsg->Text=$id_harga.'-'.$jmlAmbil;
							}
						}
						else
						{
							if($this->getViewState('jmlKekurangan') > 0)
							{
								$id_harga = $row['id_harga'];
								$jmlAmbil = $row['jumlah'];
								$jmlKekurangan = $this->getViewState('jmlKekurangan') - $jmlAmbil;
								$this->setViewState('jmlKekurangan',$jmlKekurangan);	
								$this->makeTmpTbl($id_harga,$jmlAmbil);
							}
						}
					}
				}
				
				//cek stok krisis => jika lebih kecil dari jml min di tbm_obat, keluarkan peringatan
				if( ($tmpStok-$jmlObat) < $jmlMinimal)
				{
					
					$this->msgStok->Text='Stok <b>'.$nmBarang.'</b> di '.$nmTujuan.' sudah mencapai <b>Stok Kritis</b>.';
				}
			}
			
			/*
			if( $this->modeStok->SelectedValue == '2') //minLoket
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_loket;
				//$nmTujuan = 'Apotik';
			}
			elseif( $this->modeStok->SelectedValue == '5') //minIGD
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_igd;
				//$nmTujuan = 'IGD';
			}
			elseif( $this->modeStok->SelectedValue == '6') //minOK
			{
				$jmlMinimal = ObatRecord::finder()->findByPk($idBarang)->min_ok;
				//$nmTujuan = 'OK';
			}
			*/
			
		}
		else
		{
			if(!$this->getViewState('nmTable'))
			{
				$this->showGrid->Visible=false;				
			}
			$this->msgStok->Text='Stok obat yang ada tidak cukup!!';
			$this->DDObat->SelectedIndex = -1;
			$this->jml->Text = '';
			$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled = false;
			//$this->jmlBungkus->Text = '';
			//$this->jmlBungkus->Enabled = false;
			//$this->msgStok->Text=$tmpStok;
		}	
	}
	
				
	public function prosesClicked($sender,$param)
    {
		if($this->IsValid)  // when all validations succeed
        {
			if($this->modeInput->SelectedValue == '2') //Jika Pasien Luar
			{
				$jnsPasienLuar = $this->getViewState('jnsPasLuar');
										
				if($jnsPasienLuar=='01') //jika jenis pasien luar = Rujukan
				{	
					$this->setViewState('nmDokter',$this->dokter->Text);
					$this->setViewState('nmPasien',$this->nmPas->Text);			
						
				}
				elseif($jnsPasienLuar=='02') //jika jenis pasien luar = beli sendiri
				{
					$this->setViewState('nmDokter',$this->dokter->Text);
					$this->setViewState('nmPasien',$this->nmPas->Text);		
					$this->setViewState('modeBayar',$this->modeByrInap->SelectedValue);					
				}					
				
				if($this->modeKryPas->SelectedValue == '0')
				{
					$this->setViewState('modeBayar',$this->modeByrInap->SelectedValue);	
				}
			}
			
			//$tujuan = $this->getViewState('tujuan');
			$this->cekStok();
			$this->cetakBtn->Enabled = true;	
		}
	}
	
	public function bayarClicked($sender,$param)
    {
		$this->setViewState('stBayar','1');
		
		if($this->bayar->Text >= $this->getViewState('total'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('total'));
			$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
			$this->setViewState('sisa',$hitung);
			
			$this->cetakBtn->Enabled=true;
			$this->Page->CallbackClient->focus($this->cetakBtn);
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
			$this->bayarBtn->Enabled=false;	
			$this->bayar->Enabled=false;	
			$this->DDKateg->Enabled=false;
			$this->RBtipeObat->Enabled=false;
			$this->DDObat->Enabled=false;
			$this->jml->Enabled=false;
			$this->tambahBtn->Enabled=false;
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->Page->CallbackClient->focus($this->bayar);
			$this->cetakBtn->Enabled=false;
			$this->bayarBtn->Enabled=true;	
			$this->bayar->Enabled=true;
			$this->DDKateg->Enabled=true;
			$this->RBtipeObat->Enabled=true;
			$this->DDObat->Enabled=true;
			$this->jml->Enabled=true;
			$this->tambahBtn->Enabled=true;
		}
	}
	
	public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$this->clearViewState('st_imunisasi');
				$this->clearViewState('id_kel_imunisasi');
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT id_kel_racik,total, st_imunisasi, st_racik, r_item, bungkus_racik FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['total'];					
					$t = ($this->getViewState('totSementara') - $n);						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);					
					$stRacik = $row['st_racik'];
					$resepHrg=$row['r_item'];
					$racikHrg=$row['bungkus_racik'];
					
					/*
					if($stRacik == '1' && $resepHrg > 0 && $racikHrg > 0){						
						$this->setViewState('resepHrg',$resepHrg);
						$this->setViewState('racikHrg',$racikHrg);
					}	
					*/
					
					if($stRacik == '1' && $resepHrg > 0 && $racikHrg > 0)//Jika yg didelete itu adalah kelompok obat racikan
					{						
						$idKelRacik = $row['id_kel_racik'];
						
						$sqlRacik="SELECT id_kel_racik FROM $nmTable WHERE id_kel_racik='$idKelRacik'";
						$arrRacik = $this->queryAction($sqlRacik,'S');				
						
						$i = 1;
						if(count($arrRacik) > 1) //Jika dalam kelompok racikan tsb terdapat lebih dari 1 obat racikan
						{
							foreach($arrRacik as $rowRacik)
							{
								if($i == '1')
								{
									//Pindahkan nilai r_item & bungkus_racik ke salah satu obat yg kelompok racikannya sama
									$sqlRacik2 = "SELECT MAX(id) AS id FROM $nmTable WHERE id_kel_racik='$idKelRacik' AND id<>'$ID' GROUP BY id";
									$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
									foreach($arrRacik2 as $rowRacik2)
									{
										$idObatRacik = $rowRacik2['id'];
									}
									
									$sqlRacik2 = "SELECT total, total_real FROM $nmTable WHERE id='$idObatRacik' ";
									$arrRacik2 = $this->queryAction($sqlRacik2,'S');						
									foreach($arrRacik2 as $rowRacik2)
									{
										$totRacikAsal = $rowRacik2['total'];
										$totRacik = $totRacikAsal + $resepHrg + $racikHrg;
										$totReal = $rowRacik2['total_real'] + $resepHrg + $racikHrg;
										
										$sqlUpdateRacik = "UPDATE $nmTable 
														SET r_item='$resepHrg',
															bungkus_racik='$racikHrg',
															total='$totRacik',
															total_real='$totReal'
														WHERE id = '$idObatRacik' ";
										$this->queryAction($sqlUpdateRacik,'C');
									}
								}
								
								$i++;
							}
							
							//update total transaksi sementara
							$t = ($this->getViewState('totSementara') + $resepHrg + $racikHrg);						
							$this->hrgShow->Text = 'Rp '.number_format($t,2,',','.');
							$this->setViewState('totSementara',$t);	
						}
					}
					
					$sql = "DELETE FROM $nmTable WHERE id='$ID'";						
					$this->queryAction($sql,'C');
				}			
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				
				$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";				
				$arrBhp=$this->queryAction($sql,'S');
					
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false;
					
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table		
					$this->clearViewState('nmTable');//Clear the view state				
					
					$t = '0';						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);					
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();
				
				$this->BhpGrid->DataSource = $arrBhp;//Insert new row in tabel bro...
				$this->BhpGrid->dataBind();	
								
				$this->Page->CallbackClient->focus($this->DDObat);	
				$this->msgStok->Text='';				
				$this->totSementara->Text=$this->getViewState('totSementara');				
			//}	
			
		//}	
    }		
	
	public function deleteBhpClicked($sender,$param)
    {
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->BhpGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT bhp FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['bhp'];					
					$t = ($this->getViewState('totSementara') - $n);						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('totSementara',$t);					
					
					$sql = "UPDATE $nmTable SET id_bhp='',nama_bhp='',bhp='0' WHERE id='$ID'";						
					$this->queryAction($sql,'C');
				}			
				
				$sql="SELECT id,nama_bhp,bhp FROM $nmTable WHERE id_bhp <> '' ORDER BY id";				
				
				$this->BhpGrid->DataSource = $this->queryAction($sql,'S');//Insert new row in tabel bro...
				$this->BhpGrid->dataBind();	
				
				
				
				$this->Page->CallbackClient->focus($this->DDObat);	
				$this->msgStok->Text='';				
				$this->totSementara->Text=$this->getViewState('totSementara');				
    }
	
	public function batalClicked()
    {		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			//$this->queryAction($this->getViewState('sisaTmpTable'),'D');						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		if($this->getViewState('nmTableBhp'))
		{		
			$this->queryAction($this->getViewState('nmTableBhp'),'D');//Droped the table
			//$this->queryAction($this->getViewState('sisaTmpTable'),'D');						
			$this->BhpGrid->DataSource='';
            $this->BhpGrid->dataBind();
			$this->clearViewState('nmTableBhp');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('total');
		$this->clearViewState('modeByrInap');
		$this->clearViewState('nmDokter');
		$this->clearViewState('nmPasien');
		$this->clearViewState('st_racikan');
		$this->clearViewState('id_kel_racikan');
		$this->clearViewState('st_imunisasi');
		$this->clearViewState('id_kel_imunisasi');
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		//$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->Page->CallbackClient->focus($this->notrans);
		////$this->showSecond->Enabled =  false;
		//$this->showBayar->Display = 'Hidden';
		
		$this->Response->Reload();
	}
	
	
	
	
	public function checkRM($sender,$param)
    {   		
		$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function cetakClicked($sender,$param)
    {	
		if($this->IsValid)  // when all validations succeed
        {
			$jmlTagihan=$this->getViewState('total');
			$table=$this->getViewState('nmTable');
			$cm=$this->getViewState('cm');			
			$sumber='01';
			$tujuan = $this->getViewState('tujuan');		
			$operator=$this->User->IsUserNip;
			$nipTmp=$this->User->IsUserNip;	
					
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			$kelompokPasien = $this->getViewState('kelPasien');
			
			if($jnsPasien == '0' ) //Pasien Rawat Jalan
			{		
				$cm = $this->notrans->Text;
				$poli = $this->DDKlinik->SelectedValue;
				$dateNow = date('Y-m-d');
				
				$sql = "SELECT no_trans FROM tbt_rawat_jalan WHERE cm='$cm' AND id_klinik='$poli' AND flag='0' AND st_alih='0' AND tgl_visit = '$dateNow' ";
				$noTransRwtJln = RwtjlnRecord::finder()->findBySql($sql)->no_trans;
				
				$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->penjamin;
				$sql = "SELECT st_asuransi FROM tbt_rawat_jalan WHERE cm='$cm' AND id_klinik='$poli' AND flag='0' AND st_alih='0' AND tgl_visit = '$dateNow' ";
				$stAsuransi = RwtjlnRecord::finder()->findBySql($sql)->st_asuransi;
				
				if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
				{
					$noReg = $this->numRegister('tbt_obat_jual_karyawan',ObatJualKaryawanRecord::finder(),'04');
				}
				else
				{
					$noReg = $this->numRegister('tbt_obat_jual',ObatJualRecord::finder(),'04');
				}
										
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
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
					
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jml']))
					{	
						$ObatJual->no_trans = $notransTmp;
						$ObatJual->no_trans_rwtjln = $noTransRwtJln;						
						$ObatJual->cm = $cm;
						$ObatJual->no_reg = $noReg;
						$ObatJual->dokter=$this->getViewState('idDokter');
						$ObatJual->sumber='01';
						$ObatJual->tujuan=$row['tujuan'];
						$ObatJual->klinik = $this->DDKlinik->SelectedValue;
						$ObatJual->id_obat=$row['id_obat'];
						$ObatJual->id_harga=$row['id_harga'];
						$ObatJual->tgl=date('y-m-d');
						$ObatJual->wkt=date('G:i:s');
						$ObatJual->operator=$operator;
						$ObatJual->hrg=$row['hrg'];
						$ObatJual->jumlah=$row['jml'];
						$ObatJual->total=$jmlTotal;
						$ObatJual->total_real=$row['total_real'];
						$ObatJual->flag='0';
						$ObatJual->st_obat_khusus=$row['st_obat_khusus'];
						$ObatJual->st_racik=$row['st_racik'];
						$ObatJual->id_kel_racik=$row['id_kel_racik'];
						$ObatJual->r_item=$row['r_item'];
						$ObatJual->bungkus_racik=$row['bungkus_racik'];
						$ObatJual->st_imunisasi=$row['st_imunisasi'];
						$ObatJual->id_kel_imunisasi=$row['id_kel_imunisasi'];
						$ObatJual->id_bhp=$row['id_bhp'];
						$ObatJual->bhp=$row['bhp'];
						$ObatJual->Save();			
						
						$stokAkhir=$stok->jumlah-$row['jml'];
						$stok->jumlah=$stokAkhir;
						$stok->Save();
						
						$this->normalHargaApotikLuar($row['id_obat']);
					}	
				}	
			}
			elseif($jnsPasien == '1' || $jnsPasien == '3') //Pasien Rawat Inap
			{			
				$cm = $this->notrans->Text;
				
				$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
				$notrans_inap = RwtInapRecord::finder()->find('cm = ? AND status = ?',$cm,'0')->no_trans;
				$kelompokPasien = RwtInapRecord::finder()->findByPk($notrans_inap)->penjamin;
				
				$sql = "SELECT st_asuransi FROM tbt_rawat_inap WHERE cm='$cm' AND status='0' ";
				$stAsuransi = RwtInapRecord::finder()->findBySql($sql)->st_asuransi;						
				
				if($kelompokPasien == '04' && $stAsuransi == '1') //karyawan
				{
					$noReg = $this->numRegister('tbt_obat_jual_inap_karyawan',ObatJualInapKaryawanRecord::finder(),'10');
				}
				else
				{
					$noReg = $this->numRegister('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
				}
					
				
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
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
					
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],$row['tujuan']))
					{	
						
						$ObatJualInap->no_trans=$notransTmp;
						$ObatJualInap->no_trans_inap=$notrans_inap;					
						$ObatJualInap->cm=$cm;
						$ObatJualInap->no_reg=$noReg;	
						$ObatJualInap->dokter=$this->getViewState('idDokter');
						//$ObatJualInap->sumber=$sumber;
						$ObatJualInap->sumber='01';
						$ObatJualInap->tujuan=$row['tujuan'];
						$ObatJualInap->id_obat=$row['id_obat'];
						$ObatJualInap->id_harga=$row['id_harga'];
						$ObatJualInap->tgl=date('y-m-d');
						$ObatJualInap->wkt=date('G:i:s');
						$ObatJualInap->operator=$operator;
						$ObatJualInap->hrg=$row['hrg'];
						$ObatJualInap->jumlah=$row['jml'];
						$ObatJualInap->total=$jmlTotal;
						$ObatJualInap->total_real=$row['total_real'];
						$ObatJualInap->flag='0';
						$ObatJualInap->st_obat_khusus=$row['st_obat_khusus'];
						$ObatJualInap->st_racik=$row['st_racik'];
						$ObatJualInap->id_kel_racik=$row['id_kel_racik'];
						$ObatJualInap->r_item=$row['r_item'];
						$ObatJualInap->bungkus_racik=$row['bungkus_racik'];
						$ObatJualInap->st_imunisasi=$row['st_imunisasi'];
						$ObatJualInap->id_kel_imunisasi=$row['id_kel_imunisasi'];
						$ObatJualInap->id_bhp=$row['id_bhp'];
						$ObatJualInap->bhp=$row['bhp'];
						
						if($modeByrInap == '0') //Pembayaran Kredit
						{
							$ObatJualInap->st_bayar='0';
						}elseif($modeByrInap == '1') //Pembayaran Tunai
						{
							$ObatJualInap->st_bayar='1';
							//key '04' adalah konstanta modul untuk Apotik Sentral
							//$notrans_inap=RwtInapRecord::finder()->find('cm = ? ',$this->notrans->Text)->no_trans;
						}		
						
						$ObatJualInap->Save();			
						
						$stokAkhir=$stok->jumlah-$row['jml'];
						$stok->jumlah=$stokAkhir;
						$stok->Save();
						
						$this->normalHargaApotikLuar($row['id_obat']);
					}	
				}	
				
				
			}
			else //Pasien Rawat Lain
			{	
				$tujuan ='14';//Ambil stok dari apotik langsung!
				
				$nmDokter = $this->dokter->Text;
				$kelompokPasien = $this->getViewState('kelompokPasien');				
				$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
				
				//INSERT data pasien luar ke tbd_pasien_luar
				$notransPas = $this->numCounter('tbd_pasien_luar',PasienLuarRecord::finder(),'30');
				
				$data= new PasienLuarRecord();
				$data->no_trans = $notransPas;
				
				if($this->modeKryPas->SelectedValue == '0') //Pasien Luar berstatus Karyawan
				{
					$idKaryawan = $this->DDNamaKar->SelectedValue;
					$alamat = PegawaiRecord::finder()->findByPk($idKaryawan)->alamat;
					$rt = PegawaiRecord::finder()->findByPk($idKaryawan)->rt;
					$rw = PegawaiRecord::finder()->findByPk($idKaryawan)->rw;
					$kab = PegawaiRecord::finder()->findByPk($idKaryawan)->kabupaten;
					$kec = PegawaiRecord::finder()->findByPk($idKaryawan)->kec_luar;
					$kel = PegawaiRecord::finder()->findByPk($idKaryawan)->kel_luar;
					
					if($rt != ''){$alamat .= ' RT. '.$rt;}
					if($rw != ''){$alamat .= ' RW. '.$rw;}					
					if($kel != ''){$alamat .= ' Kel. '.$kel;}
					if($kec != ''){$alamat .= ' Kec. '.$kec;}
					if($kab != ''){$alamat .= ' '.KabupatenRecord::finder()->findByPk($kab)->nama;}
					
					$data->nama = $this->ambilTxt( $this->DDNamaKar);
					$data->alamat = $alamat;
					$data->id_pas_luar_karyawan = $idKaryawan;
					$data->st_kepegawaian = '1';
					
					$noReg = $this->numRegister('tbt_obat_jual_lain_karyawan',ObatJualLainKaryawanRecord::finder(),'41');
				}
				elseif($this->modeKryPas->SelectedValue == '3') //Pasien Luar berstatus Umum
				{
					$noReg = $this->numRegister('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
					$data->nama = $this->nmPas->Text;
					//$data->alamat = $this->alamatPasLuar->Text;
				}
					
				$data->Save();
						
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					if($this->modeKryPas->SelectedValue == '0') //Pasien Luar berstatus Karyawan
					{
						$notransTmp = $this->numCounter('tbt_obat_jual_lain_karyawan',ObatJualLainKaryawanRecord::finder(),'41');		
						$nmPasien = $this->ambilTxt( $this->DDNamaKar);			
						$ObatJualLain= new ObatJualLainKaryawanRecord();
						
						if($modeByrInap == '0') //Pembayaran Kredit
						{
							$ObatJualLain->st_bayar='0';
						}elseif($modeByrInap == '1') //Pembayaran Tunai
						{
							$ObatJualLain->st_bayar='1';
						}		
					}
					elseif($this->modeKryPas->SelectedValue == '3') //Pasien Luar berstatus Umum
					{	
						$nmPasien = $this->nmPas->Text;
						$notransTmp = $this->numCounter('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
						$ObatJualLain= new ObatJualLainRecord();
						
						$ObatJualLain->st_bayar='1';
					}
					
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],'14'))
					{
						$ObatJualLain->no_trans = $notransTmp;
						$ObatJualLain->no_trans_pas_luar = $notransPas;
						$ObatJualLain->no_reg = $noReg;
						$ObatJualLain->dokter=$this->getViewState('nmDokter');
						$ObatJualLain->sumber='01';
						$ObatJualLain->tujuan='14';
						$ObatJualLain->id_obat=$row['id_obat'];
						$ObatJualLain->id_harga=$row['id_harga'];
						$ObatJualLain->tgl=date('y-m-d');
						$ObatJualLain->wkt=date('G:i:s');
						$ObatJualLain->operator=$operator;
						$ObatJualLain->hrg=$row['hrg'];
						$ObatJualLain->jumlah=$row['jml'];
						$ObatJualLain->total=$jmlTotal;
						$ObatJualLain->total_real=$row['total_real'];
						$ObatJualLain->flag='0';
						$ObatJualLain->st_obat_khusus=$row['st_obat_khusus'];
						$ObatJualLain->st_racik=$row['st_racik'];
						$ObatJualLain->id_kel_racik=$row['id_kel_racik'];
						$ObatJualLain->r_item=$row['r_item'];
						$ObatJualLain->bungkus_racik=$row['bungkus_racik'];
						$ObatJualLain->st_imunisasi=$row['st_imunisasi'];
						$ObatJualLain->id_kel_imunisasi=$row['id_kel_imunisasi'];
						$ObatJualLain->id_bhp=$row['id_bhp'];
						$ObatJualLain->bhp=$row['bhp'];
						//$ObatJualLain->st_bayar=$row['st_bayar'];						
						
						$ObatJualLain->Save();			
						
						$stokAkhir=$stok->jumlah-$row['jml'];
						$stok->jumlah=$stokAkhir;
						$stok->Save();
						
						$this->normalHargaApotikLuar($row['id_obat']);
					}	
				}
			}		
			
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			foreach($arrData as $row)
			{
				$biayaTotal = $row['total'];
				
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
			}
			
			
			
			if($this->getViewState('nmTable'))
			{		
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
				//$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			
			//Register cookies u/ status cetak
			$session=$this->Application->getModule('session');		
			$session['stCetakPenjualanObat'] = '1';
			
			
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
			
			if($jnsPasien == '0') //Pasien Rawat Jalan
			{
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtJln'=>$noTransRwtJln,'cm'=>$cm,'dokter'=>$this->DDDokter->SelectedValue,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg)));
			}
			elseif($jnsPasien == '1') //Pasien Rawat Inap
			{
				if($modeByrInap == '0') //Pembayaran Kredit
				{
					//$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObatBaruSukses'));
					$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtInap'=>$notrans_inap,'cm'=>$cm,'dokter'=>$this->DDDokter->SelectedValue,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg)));
				}
				elseif($modeByrInap == '1') //Pembayaran Tunai
				{
					//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));				
					$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransRwtInap'=>$notrans_inap,'cm'=>$cm,'dokter'=>$this->DDDokter->SelectedValue,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg)));
				}	
			}
			elseif($jnsPasien == '2')//Pasien Luar
			{	
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$this->getViewState('nmPasien'),'dokter'=>$this->getViewState('nmDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'noTransPasLuar'=>$notransPas,'cm'=>$cm,'nama'=>$nmPasien,'dokter'=>$nmDokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg)));
			}
			elseif($jnsPasien == '3')//One day service
			{
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->DDDokter->SelectedValue,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap,'noReg'=>$noReg)));
			}
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
				//$this->test->text='ada';
		}					
	}
}
?>
