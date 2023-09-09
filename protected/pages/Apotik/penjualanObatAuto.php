<?php
class penjualanObatAuto extends SimakConf
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
			$this->RBtipeRacik->SelectedValue = '0';	
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
	
	public function suggestNames($sender,$param) {
        // Get the token
        $token=$param->getToken();
        // Sender is the Suggestions repeater
        $sender->DataSource=$this->getDummyData($token);
        $sender->dataBind();                                                                                                     
    }
 
    public function suggestionSelected1($sender,$param) {
        $id = $sender->Suggestions->DataKeys[ $param->selectedIndex ];
        
		
		if($id)
		{
			$this->kodeObat->Text=$id;
			$this->chObat2();
		}
		else
		{
			$this->kodeObat->Text = '';
		}
		
    }
 
    public function getDummyData($token) 
	{
		if($this->getViewState('sqlObat'))
		{
			$sql = $this->getViewState('sqlObat');
			$sql .= " AND a.nama LIKE '$token%' GROUP BY a.kode ORDER BY a.nama ASC "; 
			
			$arr = $this->queryAction($sql,'S');
		}
		else
		{
			$arr = '';
		}
		
		return $arr;
    }
	
	public function onRenderComplete($param)
	{
		parent::onRenderComplete($param);
		if($this->IsPostBack && $this->IsCallBack)
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
				//$this->Page->CallbackClient->focus($this->DDKlinik);
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->DDKlinik->getClientID().'.focus();
				</script>';	
							
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
				//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);
				
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
				//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);	
				
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
		//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);
		
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
		
		$sqlObat = $sql;
		
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
			$this->setViewState('sqlObat',$sqlObat);
		}
		else
		{
			//$this->test->text=$sql;
			$this->DDObat->Enabled=false;	
			$this->jml->Enabled=false;	
			$this->clearViewState('sqlObat');
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
			//$this->Page->CallbackClient->focus($this->DDObat);
 $this->Page->CallbackClient->focus($this->AutoComplete);
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
	
	public function chObat2()
	{
		if($this->kodeObat->Text != '')
		{
			$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
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
					document.all.'.$this->RBtipeRacik->getClientID().'.focus();
				</script>';	
		}
		else
		{
			$this->RBtipeRacik->SelectedIndex = -1;
			$this->RBtipeRacik->Enabled=false;
			$this->DDRacik->Enabled=false;
			$this->DDRacik->SelectedValue = 'empty';
			//$this->jmlBungkus->Enabled=false;
			//$this->jmlBungkus->Text='';
			$this->clearViewState('idObat');
			
			$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->AutoComplete->getClientID().'.focus();
				</script>';	
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
			
			$idObat = $this->DDObat->SelectedValue; $idObat = $this->kodeObat->Text;
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
						$h