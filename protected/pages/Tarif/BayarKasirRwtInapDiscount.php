<?php
class BayarKasirRwtInapDiscount extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	/* 
	public function hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar)
	{
		/* ---------------- RULES --------------------------
			jam masuk > 0 detik = 1 hari
			jam masuk > 24 jam = 2 hari
			jam masuk > 1 menit && jam masuk <= 24 jam = 1 hari
		*/
		       
    /*
	    //convert to unix timestamp		
		list($G,$i,$s) = explode(":",$wktMasuk);
		list($Y,$m,$d) = explode("-",$tglMasuk);
		$wktAwal = mktime($G,$i,$s,$m,$d,$Y);
		
		list($G,$i,$s) = explode(":",$wktKeluar);
		list($Y,$m,$d) = explode("-",$tglKeluar);
		$wktAkhir = mktime($G,$i,$s,$m,$d,$Y);

        $offset = $wktAkhir-$wktAwal;

        $jmlHari = ceil($offset/60/60/24); //pembulatan ke atas
        return $jmlHari;
	}
	*/
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{		
			$this->panel2->Display = 'None';
			$this->panel3->Display = 'None';
			$this->panel4->Display = 'None';
			$this->panel5->Display = 'None';
			$this->panel6->Display = 'None';
			
			$this->previewBtn->Enabled=false;
			$this->cetakBtn->Enabled=false;
			
			$this->modeCetak->SelectedValue = '0';
			//$this->cetakBtn->Visible=false;
			
			$this->notrans->focus();
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
			
			//$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount',array('goto'=>'1')));
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount'));
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function checkRegister2($sender,$param)
    {
		$this->errMsg->Text='';
		
		if($this->CBuangMuka->Checked === true)
			$this->checkRegisterUangMuka();
		else
			$this->checkRegisterNonUangMuka();
	}
	
	public function CBuangMukaChanged()
    {
		if($this->formatCm($this->notrans->Text) != '')
			$this->checkRegister();
		else
			$this->Page->CallbackClient->focus($this->notrans);	
	}
	
	public function rincianBtnClicked()
    {
		$this->getPage()->getClientScript()->registerEndScript('','jQuery("#panel3a").slideToggle("slow");');
		//$this->getPage()->getClientScript()->registerEndScript('','jQuery("'.$this->panel3a->getClientID().'").slideToggle("slow");');
	}
	
	
	public function notransCallback($sender,$param)
    {
		$this->panel1->render($param->getNewWriter());
		$this->panel2->render($param->getNewWriter());	
		$this->panel3->render($param->getNewWriter());	
		$this->panel4->render($param->getNewWriter());	
		$this->panel5->render($param->getNewWriter());	
		$this->panel6->render($param->getNewWriter());	
	}
	
	public function checkRegister()
    {
		$cek = $this->formatCm($this->notrans->Text);
		
		//--------------------------------------- jika pasien ditemukan ---------------------------------------
		if(RwtInapRecord::finder()->findAll('cm = ? AND status = ?',$cek,'0')) 
		{
			$this->panel2->Display = 'Dynamic';
			$this->panel3->Display = 'Dynamic';
			$this->panel4->Display = 'Dynamic';
			$this->panel5->Display = 'Dynamic';
			$this->panel6->Display = 'Dynamic';	
			
			$this->panel1->Enabled = false;
			
			$tmp = $this->formatCm($this->notrans->Text);
			$sql = "SELECT tbd_pasien.nama AS cm,						   
						   tbd_pasien.cm AS cr_masuk, 
						   tbt_rawat_inap.no_trans AS no_trans,
						   tbt_rawat_inap.tgl_masuk,
						   tbt_rawat_inap.status,
						   tbt_rawat_inap.wkt_masuk,
						   tbt_rawat_inap.st_rujukan,
						   tbt_rawat_inap.tipe_rujukan,
						   tbt_rawat_inap.kelas,
						   tbt_rawat_inap.st_bayi
						   FROM 
						   		tbt_rawat_inap 
								INNER JOIN tbd_pasien ON (tbd_pasien.cm = tbt_rawat_inap.cm)
						   WHERE 
						   		tbt_rawat_inap.cm='$tmp'
								AND tbt_rawat_inap.status='0'";		 
			$tmpPasien = RwtInapRecord::finder()->findBySql($sql);
			
			$noTrans = $tmpPasien->no_trans;
			$kelas = $tmpPasien->kelas;
			$stBayi = $tmpPasien->st_bayi;
			
			$this->nama->Text= $tmpPasien->cm;	
			
			$tglMasuk = $tmpPasien->tgl_masuk;
			$tglKeluar = date('Y-m-d');
			
			$wktMasuk = $tmpPasien->wkt_masuk;
			$wktKeluar = date('G:i:s');
			
			$this->tglMasuk->Text= $this->convertDate($tglMasuk,'3');
			$this->tglKeluar->Text= $this->convertDate($tglKeluar,'3');	
			$this->wktMasuk->Text= $wktMasuk.' WIB';
			$this->wktKeluar->Text= $wktKeluar.' WIB';
			
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='0'";
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
					$lamaInapAkhir = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					$this->setViewState('lamaInapAkhir',$lamaInapAkhir);
					$lamaInap += $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				}
				$counter++;
			}
			
			$lamaInapTotal = $lamaInap;	
			$this->lamaInap->Text = $lamaInapTotal.' hari';	
			
			//----------- hitung Jasa Emergency ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_emergency				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jmlEmergency = InapEmergencyRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung Jasa Konsultasi ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_konsultasi				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsKonsul = InapKonsultasiRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung Jasa Visite ---------------------
			$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$tmp'
								   AND no_trans = '$noTrans'
								   AND nm_penunjang = 'visite'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jsVisite=$row['jumlah'];
			}	
			
			//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
			$sql = "SELECT SUM(jml) AS jumlah
						   FROM view_inap_operasi_billing				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jsTdkOperasi=$row['jumlah'];
			}	
			
			//----------- hitung Kamar Operasi ---------------------
			$sql = "SELECT SUM(jml) AS jumlah
						   FROM view_inap_operasi_billing_kamar				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jsKamarOperasi=$row['jumlah'];
			}	
			
			
			//----------- hitung Sewa Alat ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_sewa_alat				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsSewaAlat = InapSewaAlatRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung BHP ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_bhp				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsSewaAlat += InapBhpRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='0'";
			$arr = $this->queryAction($sql,'S');
			$jmlDataInapKmr = count($arr);
			$counter = 1;
			foreach($arr as $row)
			{
				$kelas = $row['id_kmr_awal'];
				$tglMasuk = $row['tgl_awal'];
				$tglKeluar = $row['tgl_kmr_ubah'];
				$wktMasuk = $row['wkt_masuk'];
				$wktKeluar = $row['wkt_keluar'];
				$lamaInap = $row['lama_inap'];
				
				$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
				//$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ?',array('2'))->tarif;
				
				//$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ?',array('1'))->tarif;							
				/*if($stBayi == '1')
					$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('2', $kelas))->tarif;
				else
					$tarifKamarBayi = 0;	
				*/
				//$persetaseKelas = KelasKamarRecord::finder()->findByPk($kelas)->persentase;
				$persetaseKelas = 0;
				
				$tarifKamarIbu = $tarifKamarIbu + ($tarifKamarIbu * $persetaseKelas / 100);
				//$tarifKamarBayi = $tarifKamarBayi + ($tarifKamarBayi * $persetaseKelas / 100);
											
				if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
				{
					$tglKeluar = date('Y-m-d');
					$wktKeluar = date('G:i:s');
					$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				}
				
				$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
				$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 	
				/*
				$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
				$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
				
				$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
				$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1); 
				*/
				//$this->showSql->Text .= $lamaInap.' ';
				
				$counter++;
			}
			
			$sql = "SELECT *, SUM(tarif) AS tarif, SUM(lama_inap) AS lama_inap FROM tbt_inap_kamar_bayi WHERE no_trans_inap = '$noTrans' GROUP BY no_trans_inap";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{				
				$tarifKamarBayi = $row['tarif'];
				$lamaInapBayi = $row['lama_inap'];
			}
			
			$jsKamar = $jmlJsKmrIbu + $tarifKamarBayi;
			
			//----------- hitung ASKEP ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_askep				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsAskep = InapAskepRecord::finder()->findBySql($sql)->tarif;
			
			
			//----------- hitung Biaya Lain-Lain lab & rad & fisio yg st_bayar=0 (bayar kredit) ---------------------
			$sql = "SELECT SUM(jml) AS jumlah
						   FROM view_biaya_lain_lab_rad			
						   WHERE cm='$tmp'
						   AND no_trans_asal = '$noTrans'
						   AND flag = 0
						   AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jsPenunjang=$row['jumlah'];
			}
			
			
			//----------- hitung Sewa Alat Penunjang ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_sewa_alat_penunjang				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsSewaAlatPenunjang = InapSewaAlatPenunjangRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung Jasa Tindakan Kecil ---------------------
			$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$tmp'
								   AND no_trans = '$noTrans'
								   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jsTdkKecil=$row['jumlah'];
			}	
			
			//----------- hitung Tindakan Khusus ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_tdk_khusus				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsTdkKhusus = InapTdkKhususRecord::finder()->findBySql($sql)->tarif;
			
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
				$jsObat=$row['jumlah'];
			}		
			
			
			//----------- hitung Oksigen ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_oksigen				
						   WHERE cm='$tmp'
						   AND no_trans = '$noTrans' ";
			$jsOksigen = InapOksigenRecord::finder()->findBySql($sql)->tarif;
			
			
			//----------- hitung Biaya Ambulan jika ada ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_ambulan			
						   WHERE 
							no_trans_inap = '$noTrans'
							AND flag = '0'";
			$jsAmbulan = AmbulanRecord::finder()->findBySql($sql)->tarif;
			
			//----------- hitung Biaya Lain ---------------------
			$sql = "SELECT SUM(tarif) AS tarif
						   FROM tbt_inap_biaya_lain			
						   WHERE 
							no_trans = '$noTrans'
							AND flag = '0'";
			$jsLainLain = InapBiayaLainRecord::finder()->findBySql($sql)->tarif;
			
			
			//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
			if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'0','1'))				
			{
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_biaya_alih
							   WHERE no_trans_inap = '$noTrans'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jsLainLain += $row['jumlah'];
				}
			}	
			
			
			$this->jsEmergency->Text = 'Rp. '.number_format($jmlEmergency,'2',',','.');
			$this->jsKonsul->Text = 'Rp. '.number_format($jsKonsul,'2',',','.');
			$this->jsVisite->Text = 'Rp. '.number_format($jsVisite,'2',',','.');
			$this->jsTdkOperasi->Text = 'Rp. '.number_format($jsTdkOperasi,'2',',','.');
			$this->jsKamarOperasi->Text = 'Rp. '.number_format($jsKamarOperasi,'2',',','.');
			$this->jsSewaAlat->Text = 'Rp. '.number_format($jsSewaAlat,'2',',','.');
			$this->jsKamar->Text = 'Rp. '.number_format($jsKamar,'2',',','.');
			$this->jsAskep->Text = 'Rp. '.number_format($jsAskep,'2',',','.');
			$this->jsPenunjang->Text = 'Rp. '.number_format($jsPenunjang,'2',',','.');
			$this->jsSewaAlatPenunjang->Text = 'Rp. '.number_format($jsSewaAlatPenunjang,'2',',','.');
			$this->jsTdkKecil->Text = 'Rp. '.number_format($jsTdkKecil,'2',',','.');
			$this->jsTdkKhusus->Text = 'Rp. '.number_format($jsTdkKhusus,'2',',','.');
			$this->jsObat->Text = 'Rp. '.number_format($jsObat,'2',',','.');
			$this->jsOksigen->Text = 'Rp. '.number_format($jsOksigen,'2',',','.');
			$this->jsAmbulan->Text = 'Rp. '.number_format($jsAmbulan,'2',',','.');
				
			$Total = $jmlEmergency + $jsKonsul + $jsVisite + $jsTdkOperasi + $jsKamarOperasi + $jsSewaAlat + $jsKamar + $jsAskep + $jsPenunjang + $jsSewaAlatPenunjang + $jsTdkKecil + $jsTdkKhusus + $jsObat + $jsOksigen + $jsAmbulan + $jsLainLain;	
			
			
			//if(floatval($Total) <= 1000000)
				//$materai = 3000;
			//else
				//$materai = 6000;
			$materai = 0;
			
			$jsLainLain += $materai;
			
			$Total += $jsLainLain;
			
			//$jsAdm = $Total * 5 / 100;
			$jsAdm = 0;
			
			$this->jsLainLain->Text = 'Rp. '.number_format($jsLainLain,'2',',','.');
			$this->jsAdm->Text = 'Rp. '.number_format($jsAdm,'2',',','.');
			
			$jmlTotal = $Total + $jsAdm ;
			
			$this->jmlShow->Text = 'Rp. '.number_format($jmlTotal,'2',',','.');
			
			
			$uangMuka = RwtInapRecord::finder()->findByPk($noTrans)->dp;
			$this->uangMuka->Text='Rp. '.number_format($uangMuka,'2',',','.');
			
			if($jmlTotal > $uangMuka)
			{
				$jmlKurangBayar = $jmlTotal - $uangMuka;
				$this->jmlKurangBayar->Text='Rp. '.number_format($jmlKurangBayar,'2',',','.');
			}
			else
			{
				//$jmlKurangBayar = $uangMuka - $jmlTotal;
				$jmlKurangBayar = 0;
				//$this->jmlKurangBayar->Text = 'Rp. '.number_format($jmlKurangBayar,'2',',','.');
				$this->jmlKurangBayar->Text = 'Rp. '.number_format(0,'2',',','.');
				
				$this->bayar->Text = $jmlTotal;
				$this->sisaByr->Text = 'Rp. '.number_format($uangMuka - $jmlTotal,'2',',','.');
				$this->setViewState('sisa',$this->sisaByr->Text);
			}
			
			$this->setViewState('jsEmergency',$this->jsEmergency->Text);
			$this->setViewState('jsKonsul',$this->jsKonsul->Text);
			$this->setViewState('jsVisite',$this->jsVisite->Text);
			$this->setViewState('jsTdkOperasi',$this->jsTdkOperasi->Text);
			$this->setViewState('jsKamarOperasi',$this->jsKamarOperasi->Text);
			$this->setViewState('jsSewaAlat',$this->jsSewaAlat->Text);
			$this->setViewState('jsKamar',$this->jsKamar->Text);
			$this->setViewState('jsAskep',$this->jsAskep->Text);
			$this->setViewState('jsPenunjang',$this->jsPenunjang->Text);
			$this->setViewState('jsSewaAlatPenunjang',$this->jsSewaAlatPenunjang->Text);
			$this->setViewState('jsTdkKecil',$this->jsTdkKecil->Text);
			$this->setViewState('jsTdkKhusus',$this->jsTdkKhusus->Text);
			$this->setViewState('jsObat',$this->jsObat->Text);
			$this->setViewState('jsOksigen',$this->jsOksigen->Text);
			$this->setViewState('jsAmbulan',$this->jsAmbulan->Text);
			$this->setViewState('jsLainLain',$this->jsLainLain->Text);
			$this->setViewState('jsAdm',$this->jsAdm->Text);
			
			
			$this->setViewState('materai',$materai);
			$this->setViewState('uangMuka',$uangMuka);
			$this->setViewState('jmlKurangBayar',$jmlKurangBayar);
			
			$this->setViewState('cm',$tmpPasien->cr_masuk);
			$this->setViewState('nama',$tmpPasien->cm);
			$this->setViewState('transaksi',$tmpPasien->no_trans);			
			$this->setViewState('notrans',$this->formatCm($this->notrans->Text));
			$this->setViewState('total',$Total);
			$this->setViewState('tmpJml',$jmlTotal);
			$this->setViewState('jmlSisaPlafon',$jmlSisaPlafon);
			
			$this->setViewState('tglMasuk',$tglMasuk);
			$this->setViewState('tglKeluar',$tglKeluar);
			$this->setViewState('wktKeluar',$wktKeluar);
			$this->setViewState('lamaInap',$lamaInapTotal);
			
			
			if($this->CBuangMuka->Checked === true)
			{
				$this->sisaBayarCtrl->Enabled=false;
				$this->sisaBayarCtrl->Visible=false;
				$this->disc->Enabled=false;
				$this->previewBtn->Enabled = false;	
			}
			else
			{
				$this->sisaBayarCtrl->Enabled=true;
				$this->sisaBayarCtrl->Visible=true;
				$this->previewBtn->Enabled = true;	
			}		
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->nmPembayar->getClientID().'.focus();');
		}		
		//--------------------------------------- jika pasien tidak ditemukan ---------------------------------------
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Pasien Rawat Inap dengan No. RM <b>'.$cek.'</b> tidak ditemukan !</p>\',timeout: 3000,dialog:{
				modal: true}}); document.all.'.$this->notrans->getClientID().'.focus();');	
			
			$this->notrans->Text = '';
			
		}
	}
	
	public function discCallBack($sender,$param)
    {
		$this->panel4->render($param->getNewWriter());
	}
	
	
	public function discChanged($sender,$param)
    {
		if($this->disc->text != '' && $this->disc->text >=0 && TPropertyValue::ensureFloat($this->disc->Text) && floatval($this->disc->Text) <= 100)
		{
			if($this->getViewState('jmlKurangBayar') > 0)
			{
				$totBiaya = TPropertyValue::ensureFloat($this->getViewState('jmlKurangBayar'));
				$disc = $this->disc->text;
				
				$totBiayaDisc = $totBiaya - ($totBiaya * $disc / 100);
				$totBiayaDiscBulat = $this->bulatkan($totBiayaDisc);
				$sisaDiscBulat = $totBiayaDiscBulat - $totBiayaDisc;
				
				$this->jmlBiayaDisc->Text = 'Rp. ' . number_format($totBiayaDiscBulat,'2',',','.');
				$this->discCtrl->Visible = true;
			}
			else
			{
				$totBiaya = TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
				$disc = $this->disc->text;
				
				$totBiayaDisc = $totBiaya - ($totBiaya * $disc / 100);
				$totBiayaDiscBulat = $this->bulatkan($totBiayaDisc);
				$sisaDiscBulat = $totBiayaDiscBulat - $totBiayaDisc;
				
				$this->jmlBiayaDisc->Text = 'Rp. ' . number_format($totBiayaDiscBulat,'2',',','.');
				$this->discCtrl->Visible = true;
				$this->bayar->Text = $totBiayaDiscBulat; 	
				$this->sisaByr->Text = 'Rp. '.number_format($this->getViewState('uangMuka') - $totBiayaDiscBulat ,'2',',','.'); 
				$this->setViewState('sisa',$this->sisaByr->Text);		
			}
			
			$this->setViewState('stDisc','1');
			$this->setViewState('totBiayaDisc',$totBiayaDisc);
			$this->setViewState('totBiayaDiscBulat',$totBiayaDiscBulat);
			$this->setViewState('sisaDiscBulat',$sisaDiscBulat);
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->bayar->getClientID().'.focus();');
		}
		else
		{
			$this->disc->Text = '';
			$this->jmlBiayaDisc->Text = '';
			$this->discCtrl->Visible = false;
			$this->clearViewState('stDisc');
			$this->clearViewState('totBiayaDisc');
			$this->clearViewState('totBiayaDiscBulat');
			$this->clearViewState('sisaDiscBulat');
			
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->disc->getClientID().'.focus();');
		}
		
		
	}
	
	public function bayarClicked($sender,$param)
    {
		//$this->showSql->text='-'.$this->getViewState('transaksi');
		$this->sisaByr->Text='';	
		
		
		if($this->CBuangMuka->Checked === true) //Pembayaran Uang MUKA
		{
			$this->bayarBtn->Enabled=false;	
			$this->cetakBtn->Enabled=true;	
			$this->previewBtn->Enabled=false;	
			$this->cetakBtn->Focus();
		}
		else
		{
			if($this->getViewState('stDisc') == '1') //jika ada discount
			{
				if($this->bayar->Text >= $this->getViewState('totBiayaDiscBulat'))
				{
					$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('totBiayaDiscBulat'));
					$hitung = number_format($hitung,'2',',','.');
					
					$this->sisaByr->Text='Rp. ' . $hitung;
					$this->setViewState('sisa',$hitung);
					
					$this->bayarBtn->Enabled=false;	
					$this->cetakBtn->Enabled=true;	
					$this->previewBtn->Enabled=false;	
					$this->refreshBtn->Enabled=false;	
					
					$this->cetakBtn->Focus();	
					$this->errByr->Text='';		
					$this->setViewState('stDelete','1');
					
					$this->panel4->Enabled = false;
					$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cetakBtn->getClientID().'.focus();');
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript
					('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jumlah pembayaran kurang !</p>\',timeout: 3000,dialog:{
						modal: true}}); document.all.'.$this->bayar->getClientID().'.focus();');	
					
					$this->cetakBtn->Enabled=false;
				}
				
			}
			else
			{
				if($this->getViewState('jmlKurangBayar') > 0)
				{
					if($this->bayar->Text >= $this->getViewState('jmlKurangBayar'))
					{
						$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('jmlKurangBayar'));
						$hitung = number_format($hitung,'2',',','.');
						
						$this->sisaByr->Text='Rp. ' . $hitung;
						$this->setViewState('sisa',$hitung);
						
						$this->bayarBtn->Enabled=false;	
						$this->previewBtn->Enabled=false;	
						$this->refreshBtn->Enabled=false;	
						$this->cetakBtn->Enabled=true;	
						$this->cetakBtn->Focus();	
						$this->errByr->Text='';		
						$this->setViewState('stDelete','1');
						$this->panel4->Enabled = false;
						$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cetakBtn->getClientID().'.focus();');
					}
					else
					{
						$this->getPage()->getClientScript()->registerEndScript
						('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Jumlah pembayaran kurang !</p>\',timeout: 3000,dialog:{
							modal: true}}); document.all.'.$this->bayar->getClientID().'.focus();');
						
						$this->cetakBtn->Enabled=false;
					}
				}
				else
				{
					$sisa = $this->sisaByr->Text;
					$this->bayarBtn->Enabled=false;	
					$this->previewBtn->Enabled=false;	
					$this->cetakBtn->Enabled=true;	
					
					$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cetakBtn->getClientID().'.focus();');
					
					$this->errByr->Text='';		
					$this->setViewState('stDelete','1');
					$this->sisaByr->Text = $this->getViewState('sisa');
				}	
			}
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
		
		$this->Response->reload();	
	}
	
	public function previewClicked()
	{
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$notrans=$this->getViewState('transaksi');
		$tglMasuk=$this->getViewState('tglMasuk');
		$tglKeluar=$this->getViewState('tglKeluar');
		$wktKeluar=$this->getViewState('wktKeluar');
		$lamaInapTotal=$this->getViewState('lamaInap');
		
		$nmPembayar = ucwords($this->nmPembayar->Text);	
		
		$jmlTagihan=number_format($this->getViewState('tmpJml'),2,'.','');
		
		$uangMuka=$this->getViewState('uangMuka');	
		$jmlKurangBayar=$this->getViewState('jmlKurangBayar');			
		$besarDisc = $this->disc->Text;
		$stDisc = $this->getViewState('stDisc');
		$totBiayaDisc = $this->getViewState('totBiayaDisc');
		$totBiayaDiscBulat = $this->getViewState('totBiayaDiscBulat');
		$sisaDiscBulat = $this->getViewState('sisaDiscBulat');
		
			
		$jmlBayar=$this->bayar->Text;
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		$materai = 'Rp. '.number_format($this->getViewState('materai'),'2',',','.');
		
		$modeCetak = $this->modeCetak->SelectedValue;
		
		//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		
		if($modeCetak == '0') //mode PDF
		{
			$url = 'Tarif.cetakKwtRwtInapPreviewDiscount';
		}
		else
		{
			$url = 'Tarif.cetakKwtRwtInapPreviewDiscountXls';
		}
			
		$this->Response->redirect($this->Service->constructUrl($url,
		array(
			'cm'=>$cm,
			'notrans'=>$notrans,
			'tglKeluar'=>$tglKeluar,
			'wktKeluar'=>$wktKeluar,
			'lamaInap'=>$lamaInapTotal,
			'nmPembayar'=>$nmPembayar,
			'jsEmergency'=>$this->getViewState('jsEmergency'),
			'jsKonsul'=>$this->getViewState('jsKonsul'),
			'jsVisite'=>$this->getViewState('jsVisite'),
			'jsTdkOperasi'=>$this->getViewState('jsTdkOperasi'),
			'jsKamarOperasi'=>$this->getViewState('jsKamarOperasi'),
			'jsSewaAlat'=>$this->getViewState('jsSewaAlat'),
			'jsKamar'=>$this->getViewState('jsKamar'),
			'jsAskep'=>$this->getViewState('jsAskep'),
			'jsPenunjang'=>$this->getViewState('jsPenunjang'),
			'jsSewaAlatPenunjang'=>$this->getViewState('jsSewaAlatPenunjang'),
			'jsTdkKecil'=>$this->getViewState('jsTdkKecil'),
			'jsTdkKhusus'=>$this->getViewState('jsTdkKhusus'),
			'jsObat'=>$this->getViewState('jsObat'),
			'jsOksigen'=>$this->getViewState('jsOksigen'),
			'jsAmbulan'=>$this->getViewState('jsAmbulan'),
			'jsLainLain'=>$this->getViewState('jsLainLain'),
			'jsAdm'=>$this->getViewState('jsAdm'),
			'jmlBayar'=>$jmlBayar,
			'jmlTagihan'=>$jmlTagihan,
			'totalTnpAdm'=>$totalTnpAdm,
			'uangMuka'=>$uangMuka,
			'sisaByr'=>$sisaByr,					
			'besarDisc'=>$besarDisc,
			'stDisc'=>$stDisc,
			'totBiayaDisc'=>$totBiayaDisc,
			'totBiayaDiscBulat'=>$totBiayaDiscBulat,
			'sisaDiscBulat'=>$sisaDiscBulat,
			'jmlKurangBayar'=>$jmlKurangBayar,
			'materai'=>$materai
			)));
					
	}
	
	
	public function cetakClicked($sender,$param)
  {
		if($this->Page->IsValid)
		{
			if($this->CBuangMuka->Checked === true) //Pembayaran Uang MUKA
				$this->cetakClickedDp();
			else
			{			
				$this->cetakClickedNonDp();
			}
			//$this->cetakBtn->Display = 'None';
		}	
		else
		{
			$this->cetakBtn->Display = 'Dynamic';
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
	}
	
	public function cetakClickedDp()
    {
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$notransInap=$this->getViewState('transaksi');	
		$uangMuka=$this->getViewState('uangMuka');
		
		$jmlBayar=$this->bayar->Text;
		$nmPembayar = ucwords($this->nmPembayar->Text);	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		$tgl = date('Y-m-d');
		$wkt = date('G:i:s');
		
		$notrans = $this->numCounter('tbt_rawat_inap_uang_muka',RwtInapUangMukaRecord::finder(),'33');
		
		//INSERT tbt_rawat_inap_uang_muka
		$data = new RwtInapUangMukaRecord();
		$data->no_trans = $notrans;
		$data->cm = $cm;
		$data->no_trans_inap = $notransInap;	
		$data->tgl = $tgl;
		$data->wkt = $wkt;
		$data->jumlah = $jmlBayar;
		$data->operator = $nipTmp;
		$data->st = '1';
		$data->Save();
		
		//Update Rawat Inap	=> dp			
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->formatCm($this->notrans->Text),'0');
		$data->dp = $uangMuka + $jmlBayar;
		$data->Save();
		
		
		if($this->modeCetak->SelectedValue == '0') //mode PDF
		{
			$url = 'Tarif.cetakKwtRwtInapDp';
		}
		else
		{
			$url = 'Tarif.cetakKwtRwtInapDpRtf';
		}
			
			$this->Response->redirect($this->Service->constructUrl($url,
			array(
				'cm'=>$cm,
				'notrans'=>$notrans,
				'notransInap'=>$notransInap,
				'tgl'=>$tgl,
				'wkt'=>$wkt,
				'nmPembayar'=>$nmPembayar,
				'jumlah'=>$jmlBayar					
				)));
	}
	
	
	public function cetakClickedNonDp()
    {	
		$cm=$this->getViewState('cm');
		$nama=$this->getViewState('nama');
		$notrans=$this->getViewState('transaksi');
		$tglMasuk=$this->getViewState('tglMasuk');
		$tglKeluar=$this->getViewState('tglKeluar');
		$wktKeluar=$this->getViewState('wktKeluar');
		$lamaInapTotal=$this->getViewState('lamaInap');
		
		$nmPembayar = ucwords($this->nmPembayar->Text);	
		
		$jmlTagihan=number_format($this->getViewState('tmpJml'),2,'.','');
		
		$uangMuka=$this->getViewState('uangMuka');	
		$jmlKurangBayar=$this->getViewState('jmlKurangBayar');			
		$besarDisc = $this->disc->Text;
		$stDisc = $this->getViewState('stDisc');
		$totBiayaDisc = $this->getViewState('totBiayaDisc');
		$totBiayaDiscBulat = $this->getViewState('totBiayaDiscBulat');
		$sisaDiscBulat = $this->getViewState('sisaDiscBulat');
		
			
		$jmlBayar=$this->bayar->Text;
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		$materai = 'Rp. '.number_format($this->getViewState('materai'),'2',',','.');
		
		//$pjPasien=$this->pjPasien->Text;
		//$AlmtPjPasien=$this->AlmtPjPasien->Text;
		
		//$notrans=$this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
		
		//Update Rawat Inap				
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->formatCm($this->notrans->Text),'0');
		$data->tgl_keluar=date('Y-m-d');
		$data->jam_keluar=date('G:i:s');
		$data->lama_inap=$lamaInapTotal;
		
		$data->kasir=$nipTmp;
		$data->tgl_kasir=date('Y-m-d');
		$data->wkt_kasir=date('G:i:s');
		$data->status='1';
		
		//UPDATE jml_bed_pakai di tbm_ruang
		$kamar = RuangRecord::finder()->findByPk($data->kamar);
		$kamar->jml_bed = $kamar->jml_bed  + 1;
		$kamar->jml_bed_pakai = $kamar->jml_bed_pakai - 1;
		$kamar->Save();
		
		
		if($stDisc == '1') //jika ada discount
		{
			$data->discount=$besarDisc;
			
			if($sisaDiscBulat != '0' || $sisaDiscBulat != '') //jika ada pembulatan setelah discount
			{
				//-------- Insert Sisa Pembulatan discount ke tbt_rawat_inap_sisa_disc -----------------
				$ObatJualSisa= new RwtInapSisaDiscRecord();
				$ObatJualSisa->no_trans=$notrans;
				$ObatJualSisa->jumlah=$sisaDiscBulat;
				$ObatJualSisa->tgl=date('y-m-d');
				$ObatJualSisa->Save();
			}			
		}
		else
		{
			$data->discount='0';
		}
		
		$data->Save();
		
		//Update jml total_tarif, tarif_rujukan di tbt_inap_adm jika pasien = pasien rujukan
		if($this->getViewState('admRujukan'))
		{
			$admRujukan=$this->getViewState('admRujukan');
			//$dataAdm=InapAdmRecord::finder()->find('no_trans=?',$notrans)->total_tarif;
			$dataAdm=InapAdmRecord::finder()->find('no_trans=?',$notrans);
			$dataAdm->tarif_rujukan=$admRujukan;
			$dataAdm->total_tarif=$dataAdm->tarif_adm + $admRujukan;
			$dataAdm->flag='1';
			$dataAdm->Save();
		}		
		
		//Update jml askep di tbt_inap_askep => tarif dikalikan lama inap		
		//$dataAskep=InapAskepRecord::finder()->find('no_trans=?',$notrans);
		//$dataAskep->tarif = $lamaInapTotal * $dataAskep->tarif;
		//$dataAskep->flag='1';
		//$dataAskep->Save();
		
		
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
				
			//$dataAskepOk=InapAskepOkRecord::finder()->find('no_trans=?',$notrans);
			//$dataAskepOk->flag='1';
			//$dataAskepOk->Save();
		}
				
		//update jml di tbt_inap_askeb => tarif dikalikan lama inap
		if(InapAskebRecord::finder()->find('no_trans=?',$notrans))			
		{
			$dataAskeb=InapAskebRecord::finder()->find('no_trans=?',$notrans);
			$dataAskeb->tarif= $dataAskeb->tarif * $lamaInapTotal;
			$dataAskeb->flag='1';
			$dataAskeb->Save();
		}	
		
		//update flag di tbt_inap_oksigen
		if(InapOksigenRecord::finder()->find('no_trans=?',$notrans))			
		{
			$dataOksigen=InapOksigenRecord::finder()->find('no_trans=?',$notrans);
			$dataOksigen->flag='1';
			$dataOksigen->Save();
		}	
		
		//update flag di tbt_inap_ambulan
		if(AmbulanRecord::finder()->find('no_trans=?',$notrans))			
		{
			$dataAmbulan=AmbulanRecord::finder()->find('no_trans=?',$notrans);
			$dataAmbulan->flag='1';
			$dataAmbulan->Save();
		}	
		
		//update flag di tbt_inap_sinar
		if(InapSinarRecord::finder()->find('no_trans=?',$notrans))			
		{
			$dataSinar=InapSinarRecord::finder()->find('no_trans=?',$notrans);
			$dataSinar->flag='1';
			$dataSinar->Save();
		}	
		
		/*//INSERT tbt_inap_bhp
		$dataBhp = new InapBhpRecord();
		$dataBhp->cm = $cm;
		$dataBhp->no_trans = $notrans;
		$dataBhp->tgl = date('Y-m-d');
		$dataBhp->wkt = date('G:i:s');
		$dataBhp->operator = $nipTmp;
		$dataBhp->flag = '1';
		$dataBhp->tarif = $bhp;
		$dataBhp->Save();*/
		
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
		
		//update status fisio di tbt_fisio_penjualan_inap
		if(FisioJualInapRecord::finder()->findAll('no_trans_inap=? AND flag=? AND st_bayar=?',$notrans,'0','0'))			
		{
			$sql = "UPDATE 
							tbt_fisio_penjualan_inap
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
				elseif($row['jns_trans']=='fisio')
				{
					$nmTable = 'tbt_fisio_penjualan ';
					$field = 'flag ';
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
		
		
		//UPDATE tbt_inap_kamar st_bayar = 1
		$dateNow = date('Y-m-d');
		$wktNow = date('G:i:s');	
		$lamaInapAkhir = $this->getViewState('lamaInapAkhir'); 
		$sql="SELECT MAX(id) AS id FROM tbt_inap_kamar WHERE  no_trans_inap='$notrans' AND st_bayar = 0 ";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$idMax = $row['id'];
			$sql = "UPDATE 
					tbt_inap_kamar
				SET 
					tgl_keluar = '$dateNow',
					wkt_keluar = '$wktNow',					
					lama_inap = '$lamaInapAkhir'
				WHERE 
				id = '$idMax'";	
			$this->queryAction($sql,'C');	
		}
		
		$sql = "UPDATE 
					tbt_inap_kamar
				SET 
					st_bayar = 1
				WHERE 
				no_trans_inap = '$notrans'
				AND st_bayar = 0";	
		$this->queryAction($sql,'C');	
		
		
		//update status di tbt_inap_operasi_billing
		if(OperasiBillingRecord::finder()->find('no_trans=?',$notrans))			
		{
			$sql = "UPDATE 
							tbt_inap_operasi_billing
						SET 
							st = 1
						WHERE 
						no_trans = '$notrans'
						AND st = 0";	
				$this->queryAction($sql,'C');
		}
		
		//INSERT tbt_inap_operasi_sisa_plafon
		if($jmlSisaPlafon > 0)
		{
			$data = new InapOperasiSisaPlafonRecord();
			
			$data->cm = $cm;
			$data->no_trans_inap = $notrans;	
			$data->tgl = date('Y-m-d');
			$data->lab = $this->getViewState('jmlSisaLab');	
			$data->obat = $this->getViewState('jmlSisaObat');	
			$data->rad = 0;
			$data->fisio = 0;
			$data->kamar = $this->getViewState('jmlSisaKamar');	
			$data->jpm = $this->getViewState('jmlSisaJpm');				
			$data->visite = $this->getViewState('jmlSisaVisite');	
			$data->total = $jmlSisaPlafon;
			$data->operator = $nipTmp;
			$data->flag = '1';
			
			$data->Save();
		}
		
		//$this->batalClicked();
		
		$modeCetak = $this->modeCetak->SelectedValue;
		/*
		$this->panel1->Enabled = true;
		$this->panel2->Display = 'None';
		$this->panel3->Display = 'None';
		$this->panel4->Display = 'None';
		$this->panel5->Display = 'None';
		$this->panel6->Display = 'None';
		
		$this->jmlShow->Text = '';
		$this->uangMuka->Text = '';
		$this->jmlKurangBayar->Text = '';
		$this->disc->Text = '';
		$this->jmlBiayaDisc->Text = '';
		$this->bayar->Text = '';
		$this->sisaByr->Text = '';
		
		$this->sisaBayarCtrl->Visible = false;
		$this->discCtrl->Visible = false;
		
		$this->panel4->Enabled=true;
		
		$this->bayarBtn->Enabled=true;
		$this->previewBtn->Enabled=false;
		$this->cetakBtn->Enabled=false;
		$this->notrans->Text = '';
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->notrans->getClientID().'.focus(); unmaskContent();');*/
		//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		
		if($modeCetak == '0') //mode PDF
		{
			$this->getPage()->getClientScript()->registerEndScript('','maskContent();');
			$url = 'Tarif.cetakKwtRwtInapDiscount';
		}
		else
		{
			$url = 'Tarif.cetakKwtRwtInapDiscountXls';
		} 
			
			$this->Response->redirect($this->Service->constructUrl($url,
			array(
				'cm'=>$cm,
				'notrans'=>$notrans,
				'tglKeluar'=>$tglKeluar,
				'wktKeluar'=>$wktKeluar,
				'lamaInap'=>$lamaInapTotal,
				'nmPembayar'=>$nmPembayar,
				'jsEmergency'=>$this->getViewState('jsEmergency'),
				'jsKonsul'=>$this->getViewState('jsKonsul'),
				'jsVisite'=>$this->getViewState('jsVisite'),
				'jsTdkOperasi'=>$this->getViewState('jsTdkOperasi'),
				'jsKamarOperasi'=>$this->getViewState('jsKamarOperasi'),
				'jsSewaAlat'=>$this->getViewState('jsSewaAlat'),
				'jsKamar'=>$this->getViewState('jsKamar'),
				'jsAskep'=>$this->getViewState('jsAskep'),
				'jsPenunjang'=>$this->getViewState('jsPenunjang'),
				'jsSewaAlatPenunjang'=>$this->getViewState('jsSewaAlatPenunjang'),
				'jsTdkKecil'=>$this->getViewState('jsTdkKecil'),
				'jsTdkKhusus'=>$this->getViewState('jsTdkKhusus'),
				'jsObat'=>$this->getViewState('jsObat'),
				'jsOksigen'=>$this->getViewState('jsOksigen'),
				'jsAmbulan'=>$this->getViewState('jsAmbulan'),
				'jsLainLain'=>$this->getViewState('jsLainLain'),
				'jsAdm'=>$this->getViewState('jsAdm'),
				'jmlBayar'=>$jmlBayar,
				'jmlTagihan'=>$jmlTagihan,
				'totalTnpAdm'=>$totalTnpAdm,
				'uangMuka'=>$uangMuka,
				'sisaByr'=>$sisaByr,					
				'besarDisc'=>$besarDisc,
				'stDisc'=>$stDisc,
				'totBiayaDisc'=>$totBiayaDisc,
				'totBiayaDiscBulat'=>$totBiayaDiscBulat,
				'sisaDiscBulat'=>$sisaDiscBulat,
				'jmlKurangBayar'=>$jmlKurangBayar,
				'materai'=>$materai						
				)));
		
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
