<?php
class BayarKasirRwtInapRev extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function hitTgl($dateStart,$dateEnd)
	{
        // dateStart and dateEnd in the form of 'YYYY-MM-DD'
        $dateStartArray = explode("-",$dateStart);
        $dateEndArray = explode("-",$dateEnd);
       
        $startYear = $dateStartArray[0];
        $startMonth = $dateStartArray[1];
        $startDay = $dateStartArray[2];
       
        $endYear = $dateEndArray[0];
        $endMonth = $dateEndArray[1];
        $endDay = $dateEndArray[2];
       
        //first convert to unix timestamp
        $init_date = mktime(12,0,0,$startMonth,$startDay,$startYear);
        $dest_date = mktime(12,0,0,$endMonth,$endDay,$endYear);

        $offset = $dest_date-$init_date;

        $days = floor($offset/60/60/24);
        return $days;
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{				
			$this->showSecond->Visible=false;
			
			$this->cetakBtn->Enabled=false;
			//$this->cetakBtn->Visible=false;			
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
			$this->bayar->Text;			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('id_klinik');
			$this->clearViewState('id_dokter');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
			$this->clearViewState('jmlBayar');
			
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';
			$this->errByr->Text='';
			$this->errMsg->Text='';
			$this->bayar->Text='';
			$this->pjPasien->Text='';
			$this->pjPasien->Text='';
			$this->notrans->focus();
			$this->showSecond->Visible=false;
			
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtInap',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->focus();
		}
    }
	
		
	public function checkRegister($sender,$param)
    {
        // valid if the username is found in the database
		//$tglSkrg = date('Y-m-d');
		$cek=$this->notrans->Text;
		
		$this->tdkPanel->Visible=true;
		$this->cetakBtn->Visible=true;
			
		if(RwtInapRecord::finder()->findAll('cm = ? AND status = ?',$cek,'0')) //jika pasien ditemukan
		{			
			$tmp = $this->notrans->Text;
			$sql = "SELECT b.nama AS cm,						   
						   b.cm AS cr_masuk, 
						   a.no_trans AS no_trans,
						   a.tgl_masuk,
						   a.wkt_masuk,
						   a.status,
						   a.st_rujukan						   
						   FROM tbt_rawat_inap a, 
								tbd_pasien b							
						   WHERE a.cm='$tmp'
								 AND a.cm=b.cm
								 AND a.status='0'";		 
			$tmpPasien = RwtInapRecord::finder()->findBySql($sql);
		    
			$noTrans = $tmpPasien->no_trans;
			$this->nama->Text= $tmpPasien->cm;	
			
			$tglMasuk=$tmpPasien->tgl_masuk;
			$wktMasuk=$tmpPasien->wkt_masuk;
			$tglKeluar = date('Y-m-d');
			$wktKeluar = date('G:i');
			
			//-------------			
			$dateStart=$tglMasuk;
			$dateEnd=$tglKeluar;
				
			$dateStartArray = explode("-",$dateStart);
			$dateEndArray = explode("-",$dateEnd);
		   
			$startYear = $dateStartArray[0];
			$startMonth = $dateStartArray[1];
			$startDay = $dateStartArray[2];
		   
			$endYear = $dateEndArray[0];
			$endMonth = $dateEndArray[1];
			$endDay = $dateEndArray[2];
			
			$init_date = mktime(12,0,0,$startMonth,$startDay,$startYear);
			$dest_date = mktime(12,0,0,$endMonth,$endDay,$endYear);
	
			$offset = $dest_date-$init_date;
	
			$days = floor($offset/60/60/24);
			$test=$offset/60/60/24;
			$this->showSql->visible=true;
			$this->showSql->text=$wktKeluar;
			//-------------
				
			if($tglKeluar==$tglMasuk)
			{
				$this->alasanCtrl->Visible = true;	
				//$this->modeInput->SelectedValue = '0';
			}
			else
			{
				$this->alasanCtrl->Visible = true;	
				$this->modeInput->Enabled = false;
				$this->modeInput->SelectedValue = '2';
				
				$this->tglMasuk->Text= $this->convertDate($tglMasuk,'3');
				$this->tglKeluar->Text= $this->convertDate($tglKeluar,'3');		
				
				$lamaInap=$this->hitTgl($tglMasuk,$tglKeluar);			


				$this->lamaInap->Text=$lamaInap.' hari';
				
				$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
				$askep = $lamaInap * $tarifAskep;
				
				$this->setViewState('askep',$askep);
				
				//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
				$sql = "SELECT jml
							   FROM view_jasa_kamar					
							   WHERE 
								cm='$tmp'
								AND no_trans = '$noTrans'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jmlJsKmr=$lamaInap * $row['jml'];
				}
				
				//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_obat_alkes				
							   WHERE cm='$tmp'
							   AND no_trans_inap = '$noTrans'
							   AND flag = 0
							   AND st_bayar = 0 ";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jmlObatAlkes=$row['jumlah'];
				}		
				
				//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_inap_operasi_billing				
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jmlTngAhli=$row['jumlah'];
				}	
				
				//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_penunjang				
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jmlPenunjang=$row['jumlah'];
				}	
				
				//----------- hitung Biaya Lain-Lain lab & rad yg st_bayar=0 (bayar kredit) ---------------------
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_biaya_lain_lab_rad			
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'
							   AND flag = 0
							   AND st_bayar = 0";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jmlBiayaLainLabRad=$row['jumlah'];
				}
				
				//----------- hitung Biaya Askep OK jika ada ---------------------
				if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
				{
					$sql = "SELECT SUM(tarif) AS jumlah
								   FROM tbt_inap_askep_ok			
								   WHERE cm='$tmp'
								   AND no_trans = '$noTrans'
								   AND flag = '0'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaAskepOk=$row['jumlah'];
					}
				}
				
				//----------- hitung Biaya Askeb jika ada ---------------------
				if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
				{
					$sql = "SELECT SUM(tarif) AS jumlah
								   FROM tbt_inap_askeb			
								   WHERE cm='$tmp'
								   AND no_trans = '$noTrans'
								   AND flag = '0'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaAskeb= $lamaInap * $row['jumlah']; //dikalikan lama hari
					}
				}			
				
				//----------- hitung Biaya Oksigen jika ada ---------------------
				if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
				{
					$sql = "SELECT tarif AS jumlah
								   FROM tbt_inap_oksigen			
								   WHERE 
								   	no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
					}
				}			


				$jmlBiayaAlih = 0;
		
				//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
				if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'0','1'))				
				{
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_alih
								   WHERE no_trans_inap = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaAlih=$row['jumlah'];
					}
				}	
				
				//$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaAlih;
				$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaOksigen + $jmlBiayaAlih;
				
				//----------- hitung Biaya TOTAL ---------------------				
				$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jmlPenunjang+$jmlBiayaLain;		 	
				
				
				$this->jsRS->Text='Rp '.number_format($jmlJsKmr,'2',',','.');
				$this->jsAhli->Text='Rp '.number_format($jmlTngAhli,'2',',','.');
				$this->jsPenunjang->Text='Rp '.number_format($jmlPenunjang,'2',',','.');
				$this->jsObat->Text='Rp '.number_format($jmlObatAlkes,'2',',','.');
				$this->jsLain->Text='Rp '.number_format($jmlBiayaLain,'2',',','.');				
				
				//$biayaAdm=$Total * 0.05;
				//$this->biayaAdm->Text='Rp '.number_format($biayaAdm,'2',',','.');
				$biayaAdm = InapAdmRecord::finder()->find('no_trans=? AND flag<>?',$noTrans,'1')->total_tarif;
				
				$jnsRujuk = $tmpPasien->st_rujukan;
				if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
				{
					if($Total > 500000)
					{
						$biayaAdm = $biayaAdm + 30000;	
						$this->setViewState('admRujukan','30000');
					}
					else
					{
						$biayaAdm = $biayaAdm + 25000;	
						$this->setViewState('admRujukan','25000');
					}
				}
				
				//$biayaAdm=$Total * 0.05;
				$this->biayaAdm->Text='Rp '.number_format($biayaAdm,'2',',','.');
				
				$jmlTotal=$Total+$biayaAdm;
				
				$this->jmlShow->Text='Rp '.number_format($jmlTotal,'2',',','.');
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				
				$this->setViewState('cm',$tmpPasien->cr_masuk);
				$this->setViewState('nama',$tmpPasien->cm);
				$this->setViewState('transaksi',$tmpPasien->no_trans);			
				$this->setViewState('notrans',$this->notrans->Text);
				$this->setViewState('total',$Total);
				$this->setViewState('tmpJml',$jmlTotal);
				
				$this->setViewState('tglMasuk',$tglMasuk);
				$this->setViewState('tglKeluar',$tglKeluar);
				$this->setViewState('wktKeluar',$wktKeluar);
				$this->setViewState('lamaInap',$lamaInap);				

			}			
		}
		else //jika pasien tidak ditemukan
		{
			$this->showFirst->Visible=true;
			$this->tdkPanel->Visible=false;
			$this->showSecond->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->focus();
		}
    }	
	
	public function modeInputChanged($sender,$param)
    {	
		$tmp = $this->notrans->Text;
		$sql = "SELECT b.nama AS cm,						   
				   b.cm AS cr_masuk, 
				   a.no_trans AS no_trans,
				   a.tgl_masuk,
				   a.status,
				   a.st_rujukan
			   FROM tbt_rawat_inap a, 
					tbd_pasien b							
			   WHERE a.cm='$tmp'
					 AND a.cm=b.cm
					 AND a.status='0'";		 
		$tmpPasien = RwtInapRecord::finder()->findBySql($sql);
		
		$noTrans = $tmpPasien->no_trans;
		$this->nama->Text= $tmpPasien->cm;	
		
		$tglMasuk=$tmpPasien->tgl_masuk;
		$tglKeluar = date('Y-m-d');
		$wktKeluar = date('G:i');
		
		
		$this->tglMasuk->Text= $this->convertDate($tglMasuk,'3');
		$this->tglKeluar->Text= $this->convertDate($tglKeluar,'3');		
		
		$alasan =$this->modeInput->SelectedValue;
		if($alasan == '0')
		{
			$lamaInap='0';			
			$this->lamaInap->Text=$lamaInap.' hari';	
		}	
		elseif($alasan == '1')
		{
			$lamaInap='1';			
			$this->lamaInap->Text=$lamaInap.' hari';				
		}
		else
		{
			$lamaInap=$this->hitTgl($tglMasuk,$tglKeluar);			
			$this->lamaInap->Text=$lamaInap.' hari';	
		}		
		
		$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
		$askep = $lamaInap * $tarifAskep;
		
		$this->setViewState('askep',$askep);
		
		//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
		$sql = "SELECT jml
					   FROM view_jasa_kamar					
					   WHERE 
						cm='$tmp'
						AND no_trans = '$noTrans'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlJsKmr=$lamaInap * $row['jml'];
		}
		
		//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
		$sql = "SELECT SUM(jml) AS jumlah
					   FROM view_obat_alkes				
					   WHERE cm='$tmp'
					   AND no_trans_inap = '$noTrans'
					   AND flag = 0
					   AND st_bayar = 0 ";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlObatAlkes=$row['jumlah'];
		}		
		
		//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
		$sql = "SELECT SUM(jml) AS jumlah
					   FROM view_inap_operasi_billing				
					   WHERE cm='$tmp'
					   AND no_trans = '$noTrans'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlTngAhli=$row['jumlah'];
		}	
		
		//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
		$sql = "SELECT SUM(jml) AS jumlah
					   FROM view_penunjang				
					   WHERE cm='$tmp'
					   AND no_trans = '$noTrans'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlPenunjang=$row['jumlah'];
		}	
		
		//----------- hitung Biaya Lain-Lain lab & rad yg st_bayar=0 (bayar kredit) ---------------------
		$sql = "SELECT SUM(jml) AS jumlah
					   FROM view_biaya_lain_lab_rad			
					   WHERE cm='$tmp'
					   AND no_trans = '$noTrans'
					   AND flag = 0
					   AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlBiayaLainLabRad=$row['jumlah'];
		}
		
		//----------- hitung Biaya Askep OK jika ada ---------------------
		if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
		{
			$sql = "SELECT SUM(tarif) AS jumlah
						   FROM tbt_inap_askep_ok			
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlBiayaAskepOk=$row['jumlah'];
			}
		}
		
		//----------- hitung Biaya Askeb jika ada ---------------------
		if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
		{
			$sql = "SELECT SUM(tarif) AS jumlah
						   FROM tbt_inap_askeb			
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlBiayaAskeb= $lamaInap * $row['jumlah']; //dikalikan lama hari
			}
		}			
		
		//----------- hitung Biaya Oksigen jika ada ---------------------
		if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
		{
			$sql = "SELECT tarif AS jumlah
						   FROM tbt_inap_oksigen			
						   WHERE 
							no_trans = '$noTrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
			}
		}
				
		$jmlBiayaAlih = 0;

		//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
		if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'0','1'))				
		{
			$sql = "SELECT SUM(jml) AS jumlah
						   FROM view_biaya_alih
						   WHERE no_trans_inap = '$noTrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlBiayaAlih=$row['jumlah'];
			}
		}	
		
		$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaAlih + $jmlBiayaOksigen;
		
		//----------- hitung Biaya TOTAL ---------------------				
		$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jmlPenunjang+$jmlBiayaLain;		 	
		
		
		$this->jsRS->Text='Rp '.number_format($jmlJsKmr,'2',',','.');
		$this->jsAhli->Text='Rp '.number_format($jmlTngAhli,'2',',','.');
		$this->jsPenunjang->Text='Rp '.number_format($jmlPenunjang,'2',',','.');
		$this->jsObat->Text='Rp '.number_format($jmlObatAlkes,'2',',','.');
		$this->jsLain->Text='Rp '.number_format($jmlBiayaLain,'2',',','.');				
		
		//$biayaAdm=$Total * 0.05;
		//$this->biayaAdm->Text='Rp '.number_format($biayaAdm,'2',',','.');
		$biayaAdm = InapAdmRecord::finder()->find('no_trans=? AND flag<>?',$noTrans,'1')->total_tarif;
		
		$jnsRujuk = $tmpPasien->st_rujukan;
		if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
		{
			if($Total > 500000)
			{
				$biayaAdm = $biayaAdm + 30000;	
				$this->setViewState('admRujukan','30000');
			}
			else
			{
				$biayaAdm = $biayaAdm + 25000;	
				$this->setViewState('admRujukan','25000');
			}
		}
		
		//$biayaAdm=$Total * 0.05;
		$this->biayaAdm->Text='Rp '.number_format($biayaAdm,'2',',','.');
		
		$jmlTotal=$Total+$biayaAdm;
		
		$this->jmlShow->Text='Rp '.number_format($jmlTotal,'2',',','.');
		$this->showSecond->Visible=true;
		$this->notrans->Enabled=false;
		$this->errMsg->Text='';	
		
		$this->setViewState('cm',$tmpPasien->cr_masuk);
		$this->setViewState('nama',$tmpPasien->cm);
		$this->setViewState('transaksi',$tmpPasien->no_trans);			
		$this->setViewState('notrans',$this->notrans->Text);
		$this->setViewState('total',$Total);
		$this->setViewState('tmpJml',$jmlTotal);
		
		$this->setViewState('tglMasuk',$tglMasuk);
		$this->setViewState('tglKeluar',$tglKeluar);
		$this->setViewState('wktKeluar',$wktKeluar);
		$this->setViewState('lamaInap',$lamaInap);
	}
	
	public function bayarClicked($sender,$param)
    {
		$this->showSql->text='-'.$this->getViewState('transaksi');
		$this->sisaByr->Text='';	
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$hitung = number_format($hitung,'2',',','.');
			$this->sisaByr->Text='Rp ' . $hitung;
			$this->setViewState('sisa',$hitung);
			
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->focus();
			$this->cetakBtn->Enabled=false;
		}
	}
	
	public function batalClicked()
    {			
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('jmlBayar');
		$this->clearViewState('tglMasuk');
		$this->clearViewState('tglKeluar');
		$this->clearViewState('wktKeluar');
		$this->clearViewState('lamaInap');
		$this->clearViewState('admRujukan');
		$this->clearViewState('askep');
		
		$this->notrans->Text='';
		$this->notrans->Enabled=true;				
		$this->notrans->focus();
		
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text='';
		
		$this->pjPasien->Text='';
	
		$this->tdkPanel->Visible=false;
		
		$this->showSecond->Visible=false;
		
		$this->alasanCtrl->Visible = false;	
		$this->modeInput->Enabled = true;
		$this->modeInput->SelectedIndex = -1;
	}
	
	public function previewClicked()
	{
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$notrans=$this->getViewState('transaksi');
		$tglMasuk=$this->getViewState('tglMasuk');
		$tglKeluar=$this->getViewState('tglKeluar');
		$wktKeluar=$this->getViewState('wktKeluar');
		$lamaInap=$this->getViewState('lamaInap');
		
		$jsRS=$this->jsRS->Text;
		$jsAhli=$this->jsAhli->Text;
		$jsPenunjang=$this->jsPenunjang->Text;
		$jsObat=$this->jsObat->Text;
		$jsLain=$this->jsLain->Text;
		$biayaAdm=$this->biayaAdm->Text;
		$askep=$this->getViewState('askep');
		$admRujukan=$this->getViewState('admRujukan');;

		
		$totalTnpAdm=$this->getViewState('total');
		$jmlTagihan=$this->getViewState('tmpJml');
		$jmlBayar=$this->bayar->Text;
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtInapPreview',array('cm'=>$cm,'notrans'=>$notrans,'tglKeluar'=>$tglKeluar,'wktKeluar'=>$wktKeluar,'lamaInap'=>$lamaInap,'jsRS'=>$jsRS,'jsAhli'=>$jsAhli,'jsPenunjang'=>$jsPenunjang,'jsObat'=>$jsObat,'jsLain'=>$jsLain,'askep'=>$askep,'biayaAdm'=>$biayaAdm,'jmlBayar'=>$jmlBayar,'jmlTagihan'=>$jmlTagihan,'totalTnpAdm'=>$totalTnpAdm,'uangMuka'=>$uangMuka,'sisaByr'=>$sisaByr)));	
	}
	
	public function cetakClicked($sender,$param)
    {	
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$notrans=$this->getViewState('transaksi');
		$tglMasuk=$this->getViewState('tglMasuk');
		$tglKeluar=$this->getViewState('tglKeluar');
		$wktKeluar=$this->getViewState('wktKeluar');
		$lamaInap=$this->getViewState('lamaInap');
		
		$jsRS=$this->jsRS->Text;
		$jsAhli=$this->jsAhli->Text;
		$jsPenunjang=$this->jsPenunjang->Text;
		$jsObat=$this->jsObat->Text;
		$jsLain=$this->jsLain->Text;
		$biayaAdm=$this->biayaAdm->Text;
		$askep=$this->getViewState('askep');

		
		$totalTnpAdm=$this->getViewState('total');
		$jmlTagihan=$this->getViewState('tmpJml');
		$jmlBayar=$this->bayar->Text;
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		//$pjPasien=$this->pjPasien->Text;
		//$AlmtPjPasien=$this->AlmtPjPasien->Text;
		
		//$notrans=$this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
		
		//Update Rawat Inap				
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
		$data->tgl_keluar=date('Y-m-d');
		$data->jam_keluar=date('G:i:s');
		$data->lama_inap=$lamaInap;
		$data->status='1';
		$data->Save();
		
		//Update jml total_tarif, tarif_rujukan di tbt_inap_adm jika pasien = pasien rujukan
		if($this->getViewState('admRujukan'))
		{
			$admRujukan=$this->getViewState('admRujukan');;
		
			$dataAdm=InapAdmRecord::finder()->find('no_trans=?',$notrans)->total_tarif;
			$dataAdm->tarif_rujukan=$admRujukan;
			$dataAdm->total_tarif=$dataAdm->tarif_adm + $admRujukan;
			$dataAdm->flag='1';
			$dataAdm->Save();
		}		
		
		//Update jml askep di tbt_inap_askep => tarif dikalikan lama inap
		$dataAskep=InapAskepRecord::finder()->find('no_trans=?',$notrans);
		$dataAskep->tarif=$askep;
		$dataAskep->flag='1';
		$dataAskep->Save();
		
		
		//update status di tbt_inap_askep_ok
		if(InapAskepOkRecord::finder()->find('no_trans=?',$notrans))			
		{
			$sql = "UPDATE 
							tbt_inap_askep_ok
						SET 
							flag = 1
						WHERE 
						no_trans = '$notrans'
						AND flag = 0";	
				$this->queryAction($sql,'C');
				
			$dataAskepOk=InapAskepOkRecord::finder()->find('no_trans=?',$notrans);
			$dataAskepOk->flag='1';
			$dataAskepOk->Save();
		}
				
		//update jml di tbt_inap_askeb => tarif dikalikan lama inap
		if(InapAskebRecord::finder()->find('no_trans=?',$notrans))			
		{
			$dataAskeb=InapAskebRecord::finder()->find('no_trans=?',$notrans);
			$dataAskeb->tarif= $dataAskeb->tarif * $lamaInap;
			$dataAskeb->flag='1';
			$dataAskeb->Save();
		}		
		
		//update status obat-alkes di tbt_obat_jual_inap
		if(ObatJualInapRecord::finder()->find('no_trans_inap=? AND flag=? AND st_bayar=?',$notrans,'0','0'))			
		{
			$sql = "UPDATE 
							tbt_obat_jual_inap
						SET 
							flag = 1
						WHERE 
						no_trans_inap = '$notrans'
						AND flag = 0
						AND st_bayar = 0";	
				$this->queryAction($sql,'C');
		}
		
		//update status lab di tbt_lab_penjualan_inap
		if(LabJualInapRecord::finder()->find('no_trans_inap=? AND flag=? AND st_bayar=?',$notrans,'0','0'))			
		{
			$sql = "UPDATE 
							tbt_lab_penjualan_inap
						SET 
							flag = 1
						WHERE 
						no_trans_inap = '$notrans'
						AND flag = 0
						AND st_bayar = 0";	
				$this->queryAction($sql,'C');
		}
		
		//update status rad di tbt_rad_penjualan_inap
		if(RadJualInapRecord::finder()->findAll('no_trans_inap=? AND flag=? AND st_bayar=?',$notrans,'0','0'))			
		{
			$sql = "UPDATE 
							tbt_rad_penjualan_inap
						SET 
							flag = 1
						WHERE 
						no_trans_inap = '$notrans'
						AND flag = 0
						AND st_bayar = 0";	
				$this->queryAction($sql,'C');
		}
		
		//jika pasien peralihan => update status flag di masing-masing tabel untuk transaksi ketika status pasien msh di rawat jln
		if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
		{
			$sql = "SELECT
						jns_trans,
						no_trans_jln
					FROM 
						view_biaya_alih 
					WHERE 
						no_trans_jln='$notrans' ";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$noTransJln = $row['no_trans_jln'];
				
				if($row['jns_trans']=='tindakan rawat jalan')
				{
					//Update tbt_kasir_rwtjln
					$nmTable = 'tbt_kasir_rwtjln ';
					$field = 'st_flag ';
					//$data=KasirRwtJlnRecord::finder()->findAll('no_trans_rwtjln = ? AND st_flag = ?',$row['no_trans_jln'],'0');
					//$data->st_flag='1';
					//$data->Save();
				}
				elseif($row['jns_trans']=='pembelian obat')
				{
					//Update tbt_obat_jual		
					$nmTable = 'tbt_obat_jual ';
					$field = 'flag ';
					//$data=ObatJualRecord::finder()->findAll('no_trans_rwtjln = ? AND flag  = ?',$row['no_trans_jln'],'0');
					//$data->flag ='1';
					//$data->Save();
				}
				elseif($row['jns_trans']=='laboratorium')
				{
					//Update tbt_lab_penjualan
					$nmTable = 'tbt_lab_penjualan ';
					$field = 'flag ';
					//$data=LabJualRecord::finder()->findAll('no_trans_rwtjln = ? AND flag = ?',$row['no_trans_jln'],'0');
					//$data->flag ='1';
					//$data->Save();
				}
				elseif($row['jns_trans']=='radiologi')
				{
					//Update tbt_rad_penjualan	
					$nmTable = 'tbt_rad_penjualan ';
					$field = 'flag ';	
					//$data=RadJualRecord::finder()->findAll('no_trans_rwtjln = ? AND flag = ?',$row['no_trans_jln'],'0');
					//$data->flag ='1';
					//$data->Save();
				}
				
				$sql = "UPDATE 
							$nmTable
						SET 
							$field=1 
						WHERE 
							no_trans_rwtjln = '$noTransJln'
							AND $field = 0";	
				$this->queryAction($sql,'C');
			}
		}
		
		
		$this->batalClicked();
		
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtInap',array('cm'=>$cm,'notrans'=>$notrans,'tglKeluar'=>$tglKeluar,'wktKeluar'=>$wktKeluar,'lamaInap'=>$lamaInap,'jsRS'=>$jsRS,'jsAhli'=>$jsAhli,'jsPenunjang'=>$jsPenunjang,'jsObat'=>$jsObat,'jsLain'=>$jsLain,'askep'=>$askep,'biayaAdm'=>$biayaAdm,'jmlBayar'=>$jmlBayar,'jmlTagihan'=>$jmlTagihan,'totalTnpAdm'=>$totalTnpAdm,'uangMuka'=>$uangMuka,'sisaByr'=>$sisaByr)));
		
	}
	
	
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
			$this->clearViewState('jmlBayar');
		}
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
