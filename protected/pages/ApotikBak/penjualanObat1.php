<?php
class penjualanObat1 extends SimakConf
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
			$this->rujukPanel->Display = 'None';
			$this->nmPasienPanel->Display = 'None';
			$this->poliPanel->Display = 'None';	
			$this->dokterLuarPanel->Display = 'None';
			$this->secondPanel->Display = 'None';		
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
			
			$this->DDDokter->Enabled=true;
			
			$this->notrans->Enabled=false;	
			
			$this->DDObat->DataSource=ObatRecord::finder()->findAll();
			//$this->DDObat->dataBind();
			
			$this->DDObat->Enabled=true;	
			$this->DDRacik->Enabled = false;
			$this->jmlBungkus->Enabled = false;
			$this->modeByrInap->Enabled=true;
			//$this->tunaiCB->Enabled=false;
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
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik, id_kel_racik FROM $nmTable ORDER BY id_kel_racik, id";
			
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();	
		}
	}
	
	public function onRenderComplete($param)
	{
		parent::onRenderComplete($param);
		if($this->IsPostBack || $this->IsCallBack)
		{	
			if($this->embel->SelectedValue=='01')
			{
				$this->Page->CallbackClient->focus($this->dokter);
				$page=$this->Application->getservice('page')->RequestedPage;
				$page->CallbackClient->callClientFunction("Prado.Element.focus",array($this->dokter->getClientID()));
				$this->dokter->focus();
			}elseif($this->embel->SelectedValue=='02')
			{
				$page=$this->Application->getservice('page')->RequestedPage;
				$page->CallbackClient->callClientFunction("Prado.Element.focus",array($this->nmPas->getClientID()));
				$this->nmPas->focus();
			}
		}
	}
				
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);
		if($this->IsPostBack || $this->IsCallBack)
		{	
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			
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
					$tujuan='2';//pasien rawat jalan selain IGD dan OK ambil stok apotik
				}
			}
			elseif ($jnsPasien == '2') //Pasien Luar
			{
				$tujuan='2';//Pasien luar ambil stok dari apotik
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
			//$this->test->text=$sql;
			
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
    }	
	
	public function panelCallback($sender,$param)
   	{
		$this->jnsPasPanel->render($param->getNewWriter());
		//$this->cmPanel->render($param->getNewWriter());
		$this->inapPanel->render($param->getNewWriter());
		$this->rujukPanel->render($param->getNewWriter());
		$this->poliPanel->render($param->getNewWriter());
		$this->dokterLuarPanel->render($param->getNewWriter());
		$this->secondPanel->render($param->getNewWriter());
		
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
			
			//$this->nmPasienPanel->Display = 'Dynamic';
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;
			$this->dokter->Enabled = false;
			
			$this->notrans->Enabled = true;
			$this->clearViewState('modeByrInap');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "true");
			
			$this->Page->CallbackClient->focus($this->notrans);
			$this->Page->getCallbackClient()->scrollTo($this->notrans);
		}
		elseif($jnsPasien == '1') //Rawat Inap
		{
			$this->rujukPanel->Display = 'None';
			$this->inapPanel->Display = 'Dynamic';
			
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;
			
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
						
			$this->Page->CallbackClient->focus($this->notrans);
			$this->Page->getCallbackClient()->scrollTo($this->notrans);
		}
		elseif($jnsPasien == '2') //Pasien Luar
		{
			$this->rujukPanel->Display = 'Dynamic';
			$this->inapPanel->Display = 'None';
			$this->poliPanel->Display = 'None';
			
			$this->poliPanel->Enabled = false;
			$this->dokterLuarPanel->Enabled = true;
			
			$this->notrans->Enabled = false;			
			$this->clearViewState('modeByrInap');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "false");
			
			//$this->Page->CallbackClient->setFocus($this->embel);
			$this->Page->CallbackClient->focus($this->embel);
			$this->Page->getCallbackClient()->scrollTo($this->embel);
		}
		if($jnsPasien == '3') //One Day Service
		{
			$this->rujukPanel->Display = 'None';
			$this->inapPanel->Display = 'None';
			
			$this->poliPanel->Enabled = true;
			$this->dokterLuarPanel->Enabled = false;	
			
			$this->notrans->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->Page->getCallbackClient()->scrollTo($this->notrans);
			//$this->jnsBayarInapCtrl->Visible = false;
			//$this->jnsPasRujukCtrl->Visible = false;
			
			$this->clearViewState('modeByrInap');
			
			//$this->getPage()->getCallbackClient()->setAttribute($this->nmPas->getClientID(), "readonly", "true");
			
			$this->Page->CallbackClient->focus($this->notrans);
			$this->Page->getCallbackClient()->scrollTo($this->notrans);
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
	}
	
	public function checkRegister($sender,$param)
    {
        // valid if the username is found in the database
		$tmp = $this->notrans->Text;
		$this->modeByrInap->Enabled=true;
		$tglSkrg = date('Y-m-d');	
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0')//Bila pasien rawat jalan
		{
			if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->notrans->Text,'0','0'))
			{				
				//$this->poliCtrl->Enabled=true;
				$this->nmPasienPanel->Display = 'Dynamic';
				
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
					  AND tbt_rawat_jalan.st_alih='0'";
						  
				$this->DDKlinik->Enabled = true;
				$this->DDKlinik->DataSource=$this->queryAction($sql,'S');
				$this->DDKlinik->dataBind();
				
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
				
				$this->errMsg->Text='';
				$this->modeInput->Enabled= false;
				$this->DDObat->enabled=true;
				
				$this->jnsPasPanel->Enabled= false;
				$this->cmPanel->Enabled= false;
				
				$this->poliPanel->Display = 'Dynamic';
				$this->secondPanel->Display = 'Dynamic';
				$this->Page->CallbackClient->focus($this->DDKlinik);
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->notrans->Text,'0','1'))
			{
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				$this->errMsg->Text='Pasien sudah alih status ke Rawat Inap !';
				$this->Page->CallbackClient->focus($this->notrans);
			}
			else
			{
				$this->modeInput->Enabled= true;
				$this->nmPasienPanel->Display = 'None';
				$this->poliPanel->Display = 'None';
				$this->secondPanel->Display = 'None';
				$this->errMsg->Text='Data Pasien Belum Masuk Ke Pendaftaran Rawat Jalan !';
				$this->Page->CallbackClient->focus($this->notrans);
			}
		}
		elseif($jnsPasien == '1')//Bila pasien rawat inap
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
										
					$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					
					$this->nmPas->Text=$row['nama'];	
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);	
					$this->setViewState('kelasInap',$row['kelas']);	
					$this->setViewState('lamaInap',$lamaInap);	
				}
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				
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
										
					$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					
					$this->nmPas->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);	
					$this->setViewState('kelasInap',$row['kelas']);	
					$this->setViewState('lamaInap',$lamaInap);	
				}
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				
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
					  AND tbt_rawat_jalan.st_alih='0'";
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
		//$this->test->text=$this->embel->selectedValue;
		$embel = $this->embel->SelectedValue;		
		$this->setViewState('embel',$embel);
		if($this->embel->SelectedValue=='01')
		{
			//$this->showSecond->Enabled=true;
			$this->setViewState('jnsPasLuar','01');
			//$this->poliCtrl->Enabled=true;
			
			$this->dokter->Text=''; 
			
			//$this->showFirst->Visible=true;
			$this->nmPasienPanel->Display = 'Dynamic';
			$this->dokterLuarPanel->Display = 'Dynamic';
			$this->secondPanel->Display = 'Dynamic';
			
			$this->dokterLuarPanel->Enabled = true;	
		}
		elseif($this->embel->SelectedValue=='02')
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
	
	public function modeByrInapChanged()
	{
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);		
		$this->clearViewState('modeByrInap');
		$this->setViewState('modeByrInap',$modeByrInap);
		
	}	
	
	public function modeStokChanged($sender,$param)
	{
		$modeStok = $this->modeStok->SelectedValue;	
		$this->clearViewState('modeStok');
		$this->setViewState('modeStok',$modeStok);
		$this->Page->CallbackClient->focus($this->DDObat);
	}
	
	public function tipeRacikChanged($sender,$param)
	{
		$this->jmlBungkus->Text = '';
		$tipeRacik = $this->collectSelectionResult($this->RBtipeRacik);	
		if($tipeRacik == '1') //racikan
		{
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
		else
		{
			$this->DDRacik->Enabled = false;
			$this->jmlBungkus->Enabled = false;
			//$this->jmlBungkus->Enabled = false;
			//$this->Page->CallbackClient->focus($this->RBtipeObat);
		}
	}
	
	public function DDRacikChanged($sender,$param)
	{
		if($this->DDRacik->SelectedValue != '')
		{
			if($this->DDRacik->SelectedValue == '0')//bikin racikan baru
			{
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
			
			if(ObatRecord::finder()->findByPk($idObat)->kategori == '01') //jika kelompok obat yg dipilih
			{	
				$this->RBtipeRacik->Enabled=true;
			}
			else //jika kelompok alkes atau BHP yg dipilih
			{
				$this->RBtipeRacik->SelectedIndex=-1;
				$this->RBtipeRacik->Enabled=false;
				$this->DDRacik->Enabled=false;
				//$this->jmlBungkus->Enabled=false;
				//$this->jmlBungkus->Text='';
			}
			
			//$this->test->text=$this->DDObat->SelectedValue;
			//

			$this->jml->Enabled=true;
			$this->msgStok->Text='';
			//$this->jml->text=$this->getViewState('tujuan');
			$this->setViewState('idObat',$$idObat);
			$this->DDObat->SelectedValue = $idObat ;
			
			//$this->Page->CallbackClient->focus($this->jml);
			//$this->Page->getCallbackClient()->scrollTo($this->jml);
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
	
	public function makeTmpTbl($id_harga,$jmlAmbil)
	{
		$this->showGrid->Visible=true;	
		$this->msgStok->Text='';
				
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_obat VARCHAR(5) NOT NULL,									 
										 jml INT(11) NOT NULL,
										 hrg FLOAT(11,2) NOT NULL,
										 hrg_bulat FLOAT(11,2) NOT NULL,
										 total FLOAT(11,2) NOT NULL,	
										 total_real FLOAT(11,2) NOT NULL,
										 id_kel_racik INT(11) NOT NULL,
										 r_item FLOAT(11,2) NOT NULL,
										 bungkus_racik FLOAT(11,2) NOT NULL,
										 tujuan CHAR(1) NOT NULL,								 							 
										 id_harga VARCHAR(20) NOT NULL,
										 st_obat_khusus CHAR(1) DEFAULT '0',
										 st_racik CHAR(1) DEFAULT '0',								 							 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			$jml= $jmlAmbil;
			
			$idObat = $this->DDObat->SelectedValue;
			$tipe =  ObatRecord::finder()->findByPk($idObat)->tipe;
			$kategori = ObatRecord::finder()->findByPk($idObat)->kategori;
			//$tujuan = $this->getViewState('tujuan');
			//$tujuan = $this->modeStok->SelectedValue;
			
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien == '2')//Bila pasien luar
			{
				$tujuan = '2';
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
						AND c.id = '$id_harga'";
						
			//$this->test->Text=$sql;
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
				$stKhusus='1' ;
			}else{
				$idHarga=$id_harga ;
				$hrg=$tmpTarif->pbf ;				
				$tipe=$tmpTarif->tipe;
				$stKhusus='0';
			}
			
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			if($kelompokPasien != '04') //kelompok pasien bukan karyawan
			{
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($stKhusus=='0')
					{
						$hrg += ($hrg* 0.3);
					}else{
						$hrg += $this->getViewState('hrg1');
					}
				}
				elseif($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					if($kelas == '1' || $kelas == '2') //kelas VIP atau kelas I
					{
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.4);
						}else{
							$hrg += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '3' || $kelas == '4') //kelas II atau kelas III
					{
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.3);
						}else{
							$hrg += $this->getViewState('hrg1');
						}						
					}
					
					//Penambahan BHP khusus untuk rawat inap
					$lamaInap = $this->getViewState('lamaInap');
					if($lamaInap <= 3) //kurang atau sama dengan 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrg += 150000;
						}
						elseif($kelas == '2' ) //kelas I
						{
							$hrg += 125000;
						}
						elseif($kelas == '3' ) //kelas II
						{
							$hrg += 100000;
						}
						elseif($kelas == '4' ) //kelas III
						{
							$hrg += 70000;
						}
					}
					else //lebih dari 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrg += (150000 + (150000 * 0.5));
						}
						elseif($kelas == '2' ) //kelas I
						{
							$hrg += (125000 + (125000 * 0.5));
						}
						elseif($kelas == '3' ) //kelas II
						{
							$hrg += (100000 + (100000 * 0.5));
						}
						elseif($kelas == '4' ) //kelas III
						{
							$hrg += (70000 + (70000 * 0.5));
						}
					}
				}
				elseif($jnsPasien == '2')//Pasien Luar/ODC
				{
					if ($stKhusus=='0')
					{
						$hrg += ($hrg* 0.2);
					}else{
						$hrg = $this->getViewState('hrg1');
					}
				}
				elseif($jnsPasien == '3')//One Day Service
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
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.2);
						}else{
							$hrg = $this->getViewState('hrg1');
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.3);
						}else{
							$hrg = $this->getViewState('hrg1');
						}	
					}	
				}
			}
			
			//$hrg_bulat=$this->bulatkan($hrg);
			$hrg_bulat = $hrg;
			$hrgJual_real = $hrg * $jml;
			$hrgJual_bulat = $hrg_bulat * $jml;
			$total = $hrgJual_bulat;
			
			/*
			//---------- Tabel Temp u/ tbt_obat_jual_sisa --------
			$sisa=$total-$hrgJual_real;
			$sisaTmpTable = $this->setNameTable('sisaTmpTable');
			$sql="CREATE TABLE $sisaTmpTable (id INT (2) auto_increment, 
										 jumlah FLOAT(11,2) NOT NULL,							 							 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			if($sisa > 0)
			{			
				$sql="INSERT INTO $sisaTmpTable (jumlah) VALUES ('$sisa')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			*/
			
			if($kategori == '01') //jika kelompok obat yg dipilih
			{			
				$stRacik = $this->collectSelectionResult($this->RBtipeRacik);
				if($stRacik == '0') //non racikan
				{
					$idKelRacik = 0;
					$r_item = 1000;
					$bungkus_racik = 0;
				}
				else //racikan
				{	
					$idKelRacik = 1;
					$r_item = 1000;
					$bungkus_racik = 200 * $this->jmlBungkus->Text;
				}
			}
			else //jika kelompok alkes atau BHP yg dipilih
			{
				$stRacik = '0';
				$idKelRacik = 0;
				$r_item = 1000;
				$bungkus_racik = 0;
			}
			
			$total = $total + $r_item + $bungkus_racik;
			$hrgJual_real = $hrgJual_real + $r_item + $bungkus_racik;
			
			//$this->setViewState('total',$total);
			$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,hrg_bulat,total,total_real,tujuan,id_harga,st_obat_khusus,st_racik,id_kel_racik,r_item,bungkus_racik) VALUES ('$id','$nama','$jml','$hrg','$hrg_bulat','$total','$hrgJual_real','$tujuan','$idHarga','$stKhusus','$stRacik','$idKelRacik','$r_item','$bungkus_racik')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			
			
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik, id_kel_racik FROM $nmTable ORDER BY id_kel_racik, id";
			
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();	
			
			
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
			$jml=$jmlAmbil;
			
			$idObat = $this->DDObat->SelectedValue;
			$tipe =  ObatRecord::finder()->findByPk($idObat)->tipe;
			$kategori = ObatRecord::finder()->findByPk($idObat)->kategori;
			//$tujuan = $this->getViewState('tujuan');
			//$tujuan = $this->modeStok->SelectedValue;
			
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien == '2')//Bila pasien luar
			{
				$tujuan = '2';
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
						AND c.id = '$id_harga'";
						
			//$this->test->Text=$sql;
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
				$stKhusus='1' ;
			}else{
				$idHarga=$id_harga ;
				$hrg=$tmpTarif->pbf ;				
				$tipe=$tmpTarif->tipe;
				$stKhusus='0';
			}
			
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			if($kelompokPasien != '04') //kelompok pasien bukan karyawan
			{
				if($jnsPasien == '0')//Rawat Jalan 
				{
					if ($stKhusus=='0')
					{
						$hrg += ($hrg* 0.3);
					}else{
						$hrg += $this->getViewState('hrg1');
					}					
				}
				elseif($jnsPasien == '1')//Rawat Inap
				{
					$kelas = $this->getViewState('kelasInap');
					if($kelas == '1' || $kelas == '2') //kelas VIP atau kelas I
					{
						if ($stKhusus=='0')
						{	
							$hrg += ($hrg* 0.4);
						}else{
							$hrg += $this->getViewState('hrg1');
						}
					}
					elseif($kelas == '3' || $kelas == '4') //kelas II atau kelas III
					{
						if ($stKhusus=='0')
						{	
							$hrg += ($hrg* 0.3);
						}else{
							$hrg += $this->getViewState('hrg1');
						}
					}
					
					//Penambahan BHP khusus untuk rawat inap
					$lamaInap = $this->getViewState('lamaInap');
					if($lamaInap <= 3) //kurang atau sama dengan 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrg += 150000;
						}
						elseif($kelas == '2' ) //kelas I
						{
							$hrg += 125000;
						}
						elseif($kelas == '3' ) //kelas II
						{
							$hrg += 100000;
						}
						elseif($kelas == '4' ) //kelas III
						{
							$hrg += 70000;
						}
					}
					else //lebih dari 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrg += (150000 + (150000 * 0.5));
						}
						elseif($kelas == '2' ) //kelas I
						{
							$hrg += (125000 + (125000 * 0.5));
						}
						elseif($kelas == '3' ) //kelas II
						{
							$hrg += (100000 + (100000 * 0.5));
						}
						elseif($kelas == '4' ) //kelas III
						{
							$hrg += (70000 + (70000 * 0.5));
						}
					}
				}
				elseif($jnsPasien == '2')//Pasien Luar/ODC
				{
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.2);
						}else{
							$hrg += $this->getViewState('hrg1');
						}
				}
				elseif($jnsPasien == '3')//One Day Service
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
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.2);
						}else{
							$hrg += $this->getViewState('hrg1');
						}	
					}
					else //jika jml obat aktual sudah melebihi plafon jml obat paket
					{
						if ($stKhusus=='0')
						{
							$hrg += ($hrg* 0.3);
						}else{
							$hrg += $this->getViewState('hrg1');
						}	
					}	
				}
			}
			
			//$hrg_bulat=$this->bulatkan($hrg);
			$hrg_bulat = $hrg;
			$hrgJual_real = $hrg * $jml;
			$hrgJual_bulat = $hrg_bulat * $jml;
			$total = $hrgJual_bulat;
			
			/*
			//---------- Tabel Temp u/ tbt_obat_jual_sisa --------
			$sisa=$total-$hrgJual_real;
			$sisaTmpTable = $this->setNameTable('sisaTmpTable');
			$sql="CREATE TABLE $sisaTmpTable (id INT (2) auto_increment, 
										 jumlah FLOAT(11,2) NOT NULL,							 							 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			if($sisa > 0)
			{			
				$sql="INSERT INTO $sisaTmpTable (jumlah) VALUES ('$sisa')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			*/
			
			if($kategori == '01') //jika kelompok obat yg dipilih
			{			
				$stRacik = $this->collectSelectionResult($this->RBtipeRacik);
				if($stRacik == '0') //non racikan
				{
					$idKelRacik = 0;
					$r_item = 1000;
					$bungkus_racik = 0;
				}
				else //racikan
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
							
							$r_item = 1000;
							$bungkus_racik = 200 * $this->jmlBungkus->Text;
						} 
						else //belum ada kel racik
						{
							$idKelRacik = 1;
							$r_item = 1000;
							$bungkus_racik = 200 * $this->jmlBungkus->Text;
						}
					}
					else //bukan kelompok baru
					{
						$r_item = 0;
						$bungkus_racik = 0;
					}
				}
			}
			else //jika kelompok alkes atau BHP yg dipilih
			{
				$stRacik = '0';
				$idKelRacik = 0;
				$r_item = 1000;
				$bungkus_racik = 0;
			}
			
			$total = $total + $r_item + $bungkus_racik;
			$hrgJual_real = $hrgJual_real + $r_item + $bungkus_racik;
			
			//$this->setViewState('total',$total);
			//$this->setViewState('total',$total);
			$sql="INSERT INTO $nmTable (id_obat,nama,jml,hrg,hrg_bulat,total,total_real,tujuan,id_harga,st_obat_khusus,st_racik,id_kel_racik,r_item,bungkus_racik) VALUES ('$id','$nama','$jml','$hrg','$hrg_bulat','$total','$hrgJual_real','$tujuan','$idHarga','$stKhusus','$stRacik','$idKelRacik','$r_item','$bungkus_racik')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			
			
			$sql="SELECT id,nama,hrg_bulat,jml,total,st_racik, id_kel_racik FROM $nmTable ORDER BY id_kel_racik, id";
			
			$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
			$this->UserGrid->dataBind();	
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
			$tujuan = '2';
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
				$sql = "SELECT jumlah, id_harga FROM tbt_stok_lain WHERE id_obat='$idBarang' AND tujuan = '$tujuan' ORDER BY id_harga"; 
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
		$jnsPasienLuar = $this->getViewState('jnsPasLuar');
		//$this->test->Text = $jnsPasien;
								
		if($jnsPasienLuar=='01') //jika jenis pasien luar = Rujukan
		{	
			$this->setViewState('nmDokter',$this->dokter->Text);
			$this->setViewState('nmPasien',$this->nmPas->Text);			
				
		}
		elseif($jnsPasienLuar=='02') //jika jenis pasien luar = beli sendiri
		{
			$this->setViewState('nmDokter',$this->dokter->Text);
			$this->setViewState('nmPasien',$this->nmPas->Text);				
		}					
		//$tujuan = $this->getViewState('tujuan');
		$this->cekStok();
		$this->cetakBtn->Enabled = true;	
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
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT total FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['total'];					
					$t = ($this->getViewState('total') - $n);						
					$this->hrgShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('total',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');								
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false;
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();			
				$this->Page->CallbackClient->focus($this->DDObat);	
				$this->msgStok->Text='';
			//}	
			
		//}	
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
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('total');
		$this->clearViewState('modeByrInap');
		$this->clearViewState('nmDokter');
		$this->clearViewState('nmPasien');
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
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			if($jnsPasien == '0' ) //Pasien Rawat Jalan
			{		
				
				if($kelompokPasien != '04') //bukan karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04');
				}
				else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_karyawan',ObatJualRecord::finder(),'04');
				}
						
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('tujuan= ? AND id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ?',$row['tujuan'],$row['id_obat'],$row['id_harga'],$sumber,$row['jml']))
					{	
					
						if($kelompokPasien != '04') //bukan karyawan
						{
							$ObatJual= new ObatJualRecord();
						}
						else
						{
							$ObatJual= new ObatJualKaryawanRecord();
						}
						
						$ObatJual->no_trans=$notransTmp;
						$ObatJual->no_trans_rwtjln=$this->getViewState('no_trans_rwtjln');
						$ObatJual->cm=$cm;
						$ObatJual->dokter=$this->getViewState('idDokter');
						//$transRwtJln->sumber=$sumber;
						$ObatJual->sumber='01';
						$ObatJual->tujuan=$row['tujuan'];
						$ObatJual->klinik=$this->DDKlinik->SelectedValue;
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
						$ObatJual->Save();			
						
						$stokAkhir=$stok->jumlah-$row['jml'];
						$stok->jumlah=$stokAkhir;
						$stok->Save();
					}	
				}	
			}
			elseif($jnsPasien == '1' || $jnsPasien == '3') //Pasien Rawat Inap
			{			
				$modeByrInap = $this->collectSelectionResult($this->modeByrInap);
				$notrans_inap = RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0')->no_trans;
				
				if($kelompokPasien != '04') //bukan karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_inap',ObatJualInapRecord::finder(),'10');
				}
				else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_inap_karyawan',ObatJualInapRecord::finder(),'10');
				}
						
				
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],$row['tujuan']))
					{	
						if($kelompokPasien != '04') //bukan karyawan
						{
							$ObatJualInap= new ObatJualInapRecord();
						}
						else
						{
							$ObatJualInap= new ObatJualInapKaryawanRecord();
						}
						
						$ObatJualInap->no_trans=$notransTmp;
						$ObatJualInap->no_trans_inap=$notrans_inap;					
						$ObatJualInap->cm=$cm;
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
					}	
				}	
				
				
			}
			else //Pasien Rawat Lain
			{	
				$tujuan ='2';//Ambil stok dari apotik langsung!
				
				$nmPasien = $this->nmPas->Text;
				$nmDokter = $this->dokter->Text;
				
				if($kelompokPasien != '04') //bukan karyawan
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_lain',ObatJualLainRecord::finder(),'13');
				}
				else
				{
					$notransTmp = $this->numCounter('tbt_obat_jual_lain_karyawan',ObatJualLainRecord::finder(),'13');
				}
						
				$sql="SELECT * FROM $table ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$jmlTotal = $this->bulatkan($row['total']);
					
					if($stok=StokLainRecord::finder()->find('id_obat = ? AND id_harga = ? AND sumber = ? AND jumlah >= ? AND tujuan = ?',$row['id_obat'],$row['id_harga'],$sumber,$row['jml'],'2'))
					{
						
						if($kelompokPasien != '04') //bukan karyawan
						{
							$ObatJualLain= new ObatJualLainRecord();
						}
						else
						{
							$ObatJualLain= new ObatJualLainKaryawanRecord();
						}
				
						$ObatJualLain->no_trans=$notransTmp;
						$ObatJualLain->cm=$this->getViewState('nmPasien');
						$ObatJualLain->dokter=$this->getViewState('nmDokter');
						//$ObatJualLain->sumber=$sumber;
						$ObatJualLain->sumber='01';
						$ObatJualLain->tujuan='2';
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
						$ObatJualLain->Save();			
						
						$stokAkhir=$stok->jumlah-$row['jml'];
						$stok->jumlah=$stokAkhir;
						$stok->Save();
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
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap)));
			}
			elseif($jnsPasien == '1') //Pasien Rawat Inap
			{
				if($modeByrInap == '0') //Pembayaran Kredit
				{
					//$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObatBaruSukses'));
					$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap)));
				}
				elseif($modeByrInap == '1') //Pembayaran Tunai
				{
					//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));				
					$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap)));
				}	
			}
			elseif($jnsPasien == '2')//Pasien Rawat Lain
			{	
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$this->getViewState('nmPasien'),'dokter'=>$this->getViewState('nmDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nmPasien,'dokter'=>$nmDokter,'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap)));
			}
			elseif($jnsPasien == '3')//One day service
			{
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'jmlTagihan'=>$jmlTagihan)));
				$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObatBeta',array('notrans'=>$notransTmp,'cm'=>$cm,'dokter'=>$this->getViewState('idDokter'),'jnsPasien'=>$jnsPasien,'kelompokPasien'=>$kelompokPasien,'jmlTagihan'=>$jmlTagihan,'mode'=>$modeByrInap)));
			}
				//$this->Response->redirect($this->Service->constructUrl('Apotik.cetakNotaObat',array('notrans'=>$notransTmp,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'table'=>$table)));
				//$this->test->text='ada';
		}					
	}
}
?>
