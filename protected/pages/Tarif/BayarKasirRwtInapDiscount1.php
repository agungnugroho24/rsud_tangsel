<?php
class BayarKasirRwtInapDiscount1 extends SimakConf
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
			
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount1',array('goto'=>'1')));
	
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
						   a.status,
						   a.wkt_masuk,
						   a.st_rujukan,
						   a.tipe_rujukan,
						   a.kelas
						   FROM tbt_rawat_inap a, 
								tbd_pasien b							
						   WHERE a.cm='$tmp'
								 AND a.cm=b.cm
								 AND a.status='0'";		 
			$tmpPasien = RwtInapRecord::finder()->findBySql($sql);
		    
			$noTrans = $tmpPasien->no_trans;
			$kelas = $tmpPasien->kelas;
			$this->nama->Text= $tmpPasien->cm;	
			
			$tglMasuk = $tmpPasien->tgl_masuk;
			$tglKeluar = date('Y-m-d');
			
			$wktMasuk = $tmpPasien->wkt_masuk;
			$wktKeluar = date('G:i:s');
			
			/*
			if($tglKeluar==$tglMasuk)
			{
				$this->alasanCtrl->Visible = true;	
				//$this->modeInput->SelectedValue = '0';
			}
			else
			{
			*/
				//$this->alasanCtrl->Visible = true;	
				//$this->modeInput->Enabled = false;
				//$this->modeInput->SelectedValue = '2';
				
				$this->tglMasuk->Text= $this->convertDate($tglMasuk,'3');
				$this->tglKeluar->Text= $this->convertDate($tglKeluar,'3');	
				$this->wktMasuk->Text= $wktMasuk.' WIB';
				$this->wktKeluar->Text= $wktKeluar.' WIB';
				
				$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);	
				$this->lamaInap->Text=$lamaInap.' hari';
				
				//$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
				//$askep = $lamaInap * $tarifAskep;				
				//$this->setViewState('askep',$askep);
				
				
				//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND st='0'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//jika pasien ambil paket
				{
					foreach($arr as $row)
					{
						$stPaket = '1';
						$idPaket=$row['id_opr'];
						$lamaHariPaket=OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',$idPaket,$kelas)->lama_hari;
					}		
				}
				else
				{
					$stPaket = '0';
				}
				
//------------------------------ MODE AMBIL PAKET ------------------------------ 				
				if($stPaket == '1')
				{
					$sql = "SELECT *
							   FROM view_inap_operasi_billing				
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$tarif_obgyn = $row['tarif_obgyn'];
						$tarif_anastesi = $row['tarif_anastesi'];
						$tarif_anak = $row['tarif_anak'];
						$tarif_assdktr = $row['tarif_assdktr'];
						$visite_dokter_obgyn = $row['visite_dokter_obgyn'];
						$visite_dokter_anak = $row['visite_dokter_anak'];
						$sewa_ok = $row['sewa_ok'];
						$obat = $row['obat'];
						$ctg = $row['ctg'];
						$jpm = $row['jpm'];
						$lab = $row['lab'];
						$ambulan = $row['ambulan'];
						$kamar_ibu = $row['kamar_ibu'];
						$kamar_bayi = $row['kamar_bayi'];
						$js_bidan_pengirim = $row['js_bidan_pengirim'];
						$adm = $row['adm'];
						$materai = $row['materai'];
					}
					$this->setViewState('metrai',$materai);
					
					
					//bandingkan lama_hari_paket dengan lama hari aktual
					if($lamaInap <= $lamaHariPaket) //ambil harga paket
					{
						$lamaInap = $lamaHariPaket;
						$this->lamaInap->Text = $lamaInap.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$jmlJsKmr = $kamar_ibu + $kamar_bayi;
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, ambil jml paket
						{ 
							$jmlPenunjangPaket = $jmlVisitePaket;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaket = $jmlVisiteAktual;
						}	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
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
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$tmp'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, ambil jml paket
						{ 
							$jmlObatAlkes = $obat - $jmlObatLunas;
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, ambil jml paket
						{ 
							$jmlBiayaLainLab = $lab - $jmlLabLunas;
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}			
		
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						$jnsRujuk = $tmpPasien->st_rujukan;
						if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
						
					}
					else //ambil harga aktual
					{
						$this->lamaInap->Text = $lamaInap.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
						
						$jmlJsKmrIbu = $tarifKamarIbu * $lamaInap; 
						$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInap - 1); 
						
						$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
						
						
						$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
						
						$jmlJpmIbu = $tarifJpmIbu * $lamaInap; 
						$jmlJpmBayi = $tarifJpmBayi * ($lamaInap - 1); 
						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, ambil jml paket
						{ 
							$jmlPenunjangPaket = $jmlVisitePaket;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaket = $jmlVisiteAktual;
						}	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
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
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$tmp'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, ambil jml paket
						{ 
							$jmlObatAlkes = $obat - $jmlObatLunas;
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						}
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, ambil jml paket
						{ 
							$jmlBiayaLainLab = $lab - $jmlLabLunas;
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						//----------- hitung Biaya Sinar jika ada ---------------------
						if(InapSinarRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_sinar			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaSinar = $row['jumlah']; //dikalikan lama hari
							}
						}			
		
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						$jnsRujuk = $tmpPasien->st_rujukan;
						if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
					}
				}
				
				
				//------------------------------ MODE NON PAKET (NORMAL) ------------------------------
				if($stPaket == '0')
				{
					//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
					$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
					$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
					
					$jmlJsKmrIbu = $tarifKamarIbu * $lamaInap; 
					$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInap - 1); 
					
					//$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
					
					$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
					$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
					
					$jmlJpmIbu = $tarifJpmIbu * $lamaInap; 
					$jmlJpmBayi = $tarifJpmBayi * ($lamaInap - 1); 
					
					//$jpm = $jmlJpmIbu + $jmlJpmBayi; 							
									
					$sql = "SELECT *
								FROM view_jasa_kamar1 WHERE cm='$cek' AND no_trans = '$noTrans' ";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{			
						if($row['lama_inap']==0)
						{
							$tglMasuk= $row['tgl_awal'];
							$tglKeluar= date('Y-m-d');	
							$wktMasuk= $row['wkt_masuk'];
							$wktKeluar= date('G:i:s');		
							$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
							$jmlJsKmr1=$row['tarif']*$lamaInap;
							$jpm1=$row['jpm']*$lamaInap;
						}else{
							$jmlJsKmr1=$row['tarif']*$row['lama_inap'];
							$jpm1=$row['jpm']*$row['lama_inap'];
							$lamaInap1=$row['lama_inap'];
						}
						$lamaInap=$lamaInap1+$lamaInap;
						$jmlJsKmr=$jmlJsKmr+$jmlJsKmr1;
						$jpm=$jpm+$jpm1;
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
					
					//----------- hitung Biaya Lain-Lain lab & rad & fisio yg st_bayar=0 (bayar kredit) ---------------------
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
										no_trans = '$noTrans'
										AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
						}
					}			
					
					//----------- hitung Biaya Sinar jika ada ---------------------
						if(InapSinarRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_sinar			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaSinar = $row['jumlah']; //dikalikan lama hari
							}
						}
	
					//----------- hitung Biaya Ambulan jika ada ---------------------
					if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
					{
						$sql = "SELECT tarif AS jumlah
									   FROM tbt_inap_ambulan			
									   WHERE 
										no_trans = '$noTrans'
										AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
				}
				
				//$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaAlih;
				$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaOksigen + $jmlBiayaSinar+  $jmlBiayaAmbulan + $jmlBiayaAlih;
				
				//----------- hitung Biaya TOTAL ---------------------				
				$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jpm+$jmlPenunjang+$jmlBiayaLain;		 	
				
				
				$this->jsRS->Text='Rp. '.number_format($jmlJsKmr,'2',',','.');
				$this->jsAhli->Text='Rp. '.number_format($jmlTngAhli,'2',',','.');
				$this->jpm->Text='Rp. '.number_format($jpm,'2',',','.');
				$this->jsPenunjang->Text='Rp. '.number_format($jmlPenunjang,'2',',','.');
				$this->jsObat->Text='Rp. '.number_format($jmlObatAlkes,'2',',','.');
				$this->jsLain->Text='Rp. '.number_format($jmlBiayaLain,'2',',','.');
				/*
				if($kelas=='5')
				{
					$biayaAdm=118000;
				}elseif($kelas=='4')
				{
					$biayaAdm=143000;
				}elseif($kelas=='3')
				{
					$biayaAdm=193000;
				}elseif($kelas=='2')
				{
					$biayaAdm=193000;
				}elseif($kelas=='1')
				{
					$biayaAdm=293000;
				}				
				*/
				//$biayaAdm=$Total * 0.05;
				
				$adm=TarifAdmKamarRecord::finder()->find('id_kls =?',$kelas)->adm;
				$utk=TarifAdmKamarRecord::finder()->find('id_kls =?',$kelas)->utk;
				$biayaAdm=$adm+$utk;
				$this->biayaAdm->Text='Rp. '.number_format($biayaAdm,'2',',','.');
				$jmlTotalSem=$Total+$biayaAdm;
				if($jmlTotalSem <= 1000000)
				{
					$metrai=3000;
				}else
				{
					$metrai=6000;
				}
				$this->clearViewState('metrai');
				$this->setViewState('metrai',$metrai);
				$this->biayaMetrai->Text='Rp. '.number_format($this->getViewState('metrai'),'2',',','.');
				//$this->biayaMetrai->Text='Rp. '.number_format($metrai,'2',',','.');
				
				$jmlTotal=$Total+$biayaAdm+$this->getViewState('metrai');
				//$jmlTotal=$Total+$biayaAdm+$metrai;
				
				$this->jmlShow->Text='Rp. '.number_format($jmlTotal,'2',',','.');
				
				$uangMuka = RwtInapRecord::finder()->findByPk($noTrans)->dp;
				$this->uangMuka->Text='Rp. '.number_format($uangMuka,'2',',','.');
				
				if($jmlTotal > $uangMuka)
				{
					$jmlKurangBayar = $jmlTotal - $uangMuka;
					$this->jmlKurangBayar->Text='Rp. '.number_format($jmlKurangBayar,'2',',','.');
				}
				else
				{
					$jmlKurangBayar = $uangMuka - $jmlTotal;
					$this->jmlKurangBayar->Text='Rp. '.number_format($jmlKurangBayar,'2',',','.');
				}
				
				$this->setViewState('uangMuka',$uangMuka);
				$this->setViewState('jmlKurangBayar',$jmlKurangBayar);
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				$this->disc->focus();
				
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

			//}			
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
						   a.wkt_masuk,
						   a.st_rujukan,
						   a.tipe_rujukan,
						   a.kelas
						   FROM tbt_rawat_inap a, 
								tbd_pasien b							
						   WHERE a.cm='$tmp'
								 AND a.cm=b.cm
								 AND a.status='0'";		 
			$tmpPasien = RwtInapRecord::finder()->findBySql($sql);
		    
			$noTrans = $tmpPasien->no_trans;
			$kelas = $tmpPasien->kelas;
			$this->nama->Text= $tmpPasien->cm;	
			
			$tglMasuk = $tmpPasien->tgl_masuk;
			$tglKeluar = date('Y-m-d');
			
			$wktMasuk = $tmpPasien->wkt_masuk;
			$wktKeluar = date('G:i:s');
			
			/*
			if($tglKeluar==$tglMasuk)
			{
				$this->alasanCtrl->Visible = true;	
				//$this->modeInput->SelectedValue = '0';
			}
			else
			{
			*/
				//$this->alasanCtrl->Visible = true;	
				//$this->modeInput->Enabled = false;
				//$this->modeInput->SelectedValue = '2';
				
				$this->tglMasuk->Text= $this->convertDate($tglMasuk,'3');
				$this->tglKeluar->Text= $this->convertDate($tglKeluar,'3');	
				$this->wktMasuk->Text= $wktMasuk.' WIB';
				$this->wktKeluar->Text= $wktKeluar.' WIB';
				
				$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);	
				$this->lamaInap->Text=$lamaInap.' hari';
				
				//$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
				//$askep = $lamaInap * $tarifAskep;				
				//$this->setViewState('askep',$askep);
				
				
				//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND st='0'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//jika pasien ambil paket
				{
					foreach($arr as $row)
					{
						$stPaket = '1';
						$idPaket=$row['id_opr'];
						$lamaHariPaket=OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',$idPaket,$kelas)->lama_hari;
					}		
				}
				else
				{
					$stPaket = '0';
				}
				
//------------------------------ MODE AMBIL PAKET ------------------------------ 				
				if($stPaket == '1')
				{
					$sql = "SELECT *
							   FROM view_inap_operasi_billing				
							   WHERE cm='$tmp'
							   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$tarif_obgyn = $row['tarif_obgyn'];
						$tarif_anastesi = $row['tarif_anastesi'];
						$tarif_anak = $row['tarif_anak'];
						$tarif_assdktr = $row['tarif_assdktr'];
						$visite_dokter_obgyn = $row['visite_dokter_obgyn'];
						$visite_dokter_anak = $row['visite_dokter_anak'];
						$sewa_ok = $row['sewa_ok'];
						$obat = $row['obat'];
						$ctg = $row['ctg'];
						$jpm = $row['jpm'];
						$lab = $row['lab'];
						$ambulan = $row['ambulan'];
						$kamar_ibu = $row['kamar_ibu'];
						$kamar_bayi = $row['kamar_bayi'];
						$js_bidan_pengirim = $row['js_bidan_pengirim'];
						$adm = $row['adm'];
						$materai = $row['materai'];
					}
					$this->setViewState('metrai',$materai);
					
					
					
					//bandingkan lama_hari_paket dengan lama hari aktual
					if($lamaInap <= $lamaHariPaket) //ambil harga paket
					{
						$lamaInap = $lamaHariPaket;
						$this->lamaInap->Text = $lamaInap.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$jmlJsKmr = $kamar_ibu + $kamar_bayi;
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, ambil jml paket
						{ 
							$jmlPenunjangPaket = $jmlVisitePaket;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaket = $jmlVisiteAktual;
						}	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
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
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$tmp'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, ambil jml paket
						{ 
							$jmlObatAlkes = $obat - $jmlObatLunas;
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, ambil jml paket
						{ 
							$jmlBiayaLainLab = $lab - $jmlLabLunas;
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}			
		
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						$jnsRujuk = $tmpPasien->st_rujukan;
						if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
						
					}
					else //ambil harga aktual
					{
						$this->lamaInap->Text = $lamaInap.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
						
						$jmlJsKmrIbu = $tarifKamarIbu * $lamaInap; 
						$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInap - 1); 
						
						//$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
						
						$sql = "SELECT SUM(tarif) AS jumlah FROM view_jasa_kamar1 WHERE cm='$cm' AND no_trans = '$notrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{			
							$jmlJsKmr=$row['jumlah'];
						}
						
						
						$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
						
						$jmlJpmIbu = $tarifJpmIbu * $lamaInap; 
						$jmlJpmBayi = $tarifJpmBayi * ($lamaInap - 1); 
						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, ambil jml paket
						{ 
							$jmlPenunjangPaket = $jmlVisitePaket;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaket = $jmlVisiteAktual;
						}	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
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
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$tmp'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, ambil jml paket
						{ 
							$jmlObatAlkes = $obat - $jmlObatLunas;
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						}
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabLunas=$row['jumlah'];
							}
							
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, ambil jml paket
						{ 
							$jmlBiayaLainLab = $lab - $jmlLabLunas;
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;
						}	
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$tmp'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
							}
						}			
		
						//----------- hitung Biaya Ambulan jika ada ---------------------
						if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_ambulan			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '0'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
						
						//----------- hitung Biaya Adm ---------------------
						$biayaAdm = $adm;// +  $materai;
				
						$jnsRujuk = $tmpPasien->st_rujukan;
						if($jnsRujuk=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
					}
				}
				
				
				
				if($stPaket == '0')
				{
					//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
					$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
					$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
					
					$jmlJsKmrIbu = $tarifKamarIbu * $lamaInap; 
					$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInap - 1); 
					
					//$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
					$sql = "SELECT SUM(tarif) AS jumlah FROM view_jasa_kamar1 WHERE cm='$cm' AND no_trans = '$notrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{			
						$jmlJsKmr=$row['jumlah'];
					}
					
					$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
					$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
					
					$jmlJpmIbu = $tarifJpmIbu * $lamaInap; 
					$jmlJpmBayi = $tarifJpmBayi * ($lamaInap - 1); 
					
					$jpm = $jmlJpmIbu + $jmlJpmBayi; 
					
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
					
					//----------- hitung Biaya Lain-Lain lab & rad & fisio yg st_bayar=0 (bayar kredit) ---------------------
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
										no_trans = '$noTrans'
										AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaOksigen = $row['jumlah']; //dikalikan lama hari
						}
					}			
	
					//----------- hitung Biaya Ambulan jika ada ---------------------
					if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
					{
						$sql = "SELECT tarif AS jumlah
									   FROM tbt_inap_ambulan			
									   WHERE 
										no_trans = '$noTrans'
										AND flag = '0'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
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
				}
				
				//$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaAlih;
				$jmlBiayaLain = $jmlBiayaLainLabRad + $askep + $jmlBiayaAskepOk + $jmlBiayaAskeb + $jmlBiayaOksigen +  $jmlBiayaAmbulan + $jmlBiayaAlih;
				
				//----------- hitung Biaya TOTAL ---------------------				
				$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jpm+$jmlPenunjang+$jmlBiayaLain;		 	
				
				
				$this->jsRS->Text='Rp. '.number_format($jmlJsKmr,'2',',','.');
				$this->jsAhli->Text='Rp. '.number_format($jmlTngAhli,'2',',','.');
				$this->jpm->Text='Rp. '.number_format($jpm,'2',',','.');
				$this->jsPenunjang->Text='Rp. '.number_format($jmlPenunjang,'2',',','.');
				$this->jsObat->Text='Rp. '.number_format($jmlObatAlkes,'2',',','.');
				$this->jsLain->Text='Rp. '.number_format($jmlBiayaLain,'2',',','.');
				
				if($kelas=='5')
				{
					$biayaAdm=118000;
				}elseif($kelas=='4')
				{
					$biayaAdm=143000;
				}elseif($kelas=='3')
				{
					$biayaAdm=193000;
				}elseif($kelas=='2')
				{
					$biayaAdm=193000;
				}elseif($kelas=='1')
				{
					$biayaAdm=293000;
				}				

				//$biayaAdm=$Total * 0.05;
				$this->biayaAdm->Text='Rp. '.number_format($biayaAdm,'2',',','.');
				$this->biayaMetrai->Text='Rp. '.number_format($this->getViewState('metrai'),'2',',','.');
				$jmlTotal=$Total+$biayaAdm+$this->getViewState('metrai');
				
				$this->jmlShow->Text='Rp. '.number_format($jmlTotal,'2',',','.');
				
				$uangMuka = RwtInapRecord::finder()->findByPk($noTrans)->dp;
				$this->uangMuka->Text='Rp. '.number_format($uangMuka,'2',',','.');
				
				if($jmlTotal > $uangMuka)
				{
					$jmlKurangBayar = $jmlTotal - $uangMuka;
					$this->jmlKurangBayar->Text='Rp. '.number_format($jmlKurangBayar,'2',',','.');
				}
				else
				{
					$jmlKurangBayar = $uangMuka - $jmlTotal;
					$this->jmlKurangBayar->Text='Rp. '.number_format($jmlKurangBayar,'2',',','.');
				}
				
				$this->setViewState('uangMuka',$uangMuka);
				$this->setViewState('jmlKurangBayar',$jmlKurangBayar);
				
				$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				$this->disc->focus();
				
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
	
	public function discChanged($sender,$param)
    {
		if($this->disc->text != '' && $this->disc->text != '0' && TPropertyValue::ensureFloat($this->disc->Text))
		{
			$totBiaya = TPropertyValue::ensureFloat($this->getViewState('jmlKurangBayar'));
			$disc = $this->disc->text;
			
			$totBiayaDisc = $totBiaya - ($totBiaya * $disc / 100);
			$totBiayaDiscBulat = $this->bulatkan($totBiayaDisc);
			$sisaDiscBulat = $totBiayaDiscBulat - $totBiayaDisc;
			
			$this->jmlBiayaDisc->Text = 'Rp. ' . number_format($totBiayaDiscBulat,'2',',','.');
			$this->discCtrl->Visible = true;
			
			$this->setViewState('stDisc','1');
			$this->setViewState('totBiayaDisc',$totBiayaDisc);
			$this->setViewState('totBiayaDiscBulat',$totBiayaDiscBulat);
			$this->setViewState('sisaDiscBulat',$sisaDiscBulat);
		}
		else
		{
			$this->jmlBiayaDisc->Text = '';
			$this->discCtrl->Visible = false;
			$this->clearViewState('stDisc');
			$this->clearViewState('totBiayaDisc');
			$this->clearViewState('totBiayaDiscBulat');
			$this->clearViewState('sisaDiscBulat');
		}
		
		$this->bayar->focus();
	}
	
	public function bayarClicked($sender,$param)
    {
		$this->showSql->text='-'.$this->getViewState('transaksi');
		$this->sisaByr->Text='';	
		
		if($this->getViewState('stDisc') == '1') //jika ada discount
		{
			if($this->bayar->Text >= $this->getViewState('totBiayaDiscBulat'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('totBiayaDiscBulat'));
				$hitung = number_format($hitung,'2',',','.');
				
				$this->sisaByr->Text='Rp. ' . $hitung;
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
		else
		{
			if($this->bayar->Text >= $this->getViewState('jmlKurangBayar'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('jmlKurangBayar'));
				$hitung = number_format($hitung,'2',',','.');
				
				$this->sisaByr->Text='Rp. ' . $hitung;
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
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
			
		$this->notrans->Text='';
		$this->notrans->Enabled=true;				
		$this->notrans->focus();
		
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->disc->Text='';
		$this->jmlBiayaDisc->Text='';
		
		$this->bayar->Text='';
		$this->sisaByr->Text='';
		
		$this->pjPasien->Text='';
		
		$this->discCtrl->Visible=false;
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
		$jpm=$this->jpm->Text;
		$jsPenunjang=$this->jsPenunjang->Text;
		$jsObat=$this->jsObat->Text;
		$jsLain=$this->jsLain->Text;
		$biayaAdm=$this->biayaAdm->Text;
		$askep=$this->getViewState('askep');
		$admRujukan=$this->getViewState('admRujukan');;

		
		$totalTnpAdm=$this->getViewState('total');
		$jmlTagihan=$this->getViewState('tmpJml');
		
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
		$metrai = 'Rp. '.number_format($this->getViewState('metrai'),'2',',','.');
	
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtInapPreviewDiscount1',
			array(
				'cm'=>$cm,
				'notrans'=>$notrans,
				'tglKeluar'=>$tglKeluar,
				'wktKeluar'=>$wktKeluar,
				'lamaInap'=>$lamaInap,
				'jsRS'=>$jsRS,
				'jsAhli'=>$jsAhli,
				'jpm'=>$jpm,
				'jsPenunjang'=>$jsPenunjang,
				'jsObat'=>$jsObat,
				'jsLain'=>$jsLain,
				'askep'=>$askep,
				'biayaAdm'=>$biayaAdm,
				'biayaMetrai'=>$metrai,
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
				'jmlKurangBayar'=>$jmlKurangBayar						
				)));
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
		$jpm=$this->jpm->Text;
		$jsPenunjang=$this->jsPenunjang->Text;
		$jsObat=$this->jsObat->Text;
		$jsLain=$this->jsLain->Text;
		$biayaAdm=$this->biayaAdm->Text;
		$askep=$this->getViewState('askep');
		$admRujukan=$this->getViewState('admRujukan');;

		
		$totalTnpAdm=$this->getViewState('total');
		$jmlTagihan=$this->getViewState('tmpJml');
		
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
		
		//$pjPasien=$this->pjPasien->Text;
		//$AlmtPjPasien=$this->AlmtPjPasien->Text;
		
		//$notrans=$this->numCounter('tbt_kasir_rwtjln',KasirRwtJlnRecord::finder(),'08');//key '08' adalah konstanta modul untuk Kasir Rawat Jalan
		
		//Update Rawat Inap				
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->notrans->Text,'0');
		$data->tgl_keluar=date('Y-m-d');
		$data->jam_keluar=date('G:i:s');
		$data->lama_inap=$lamaInap;
		
		$data->kasir=$nipTmp;
		$data->tgl_kasir=date('Y-m-d');
		$data->wkt_kasir=date('G:i:s');
		$data->status='1';
		
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
		//$dataAskep->tarif = $lamaInap * $dataAskep->tarif;
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
			$dataAskeb->tarif= $dataAskeb->tarif * $lamaInap;
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
		
		
		
		$this->batalClicked();
		
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtInapDiscount',
			array(
				'cm'=>$cm,
				'notrans'=>$notrans,
				'tglKeluar'=>$tglKeluar,
				'wktKeluar'=>$wktKeluar,
				'lamaInap'=>$lamaInap,
				'jsRS'=>$jsRS,
				'jsAhli'=>$jsAhli,
				'jpm'=>$jpm,
				'jsPenunjang'=>$jsPenunjang,
				'jsObat'=>$jsObat,
				'jsLain'=>$jsLain,
				'askep'=>$askep,
				'biayaAdm'=>$biayaAdm,
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
				'jmlKurangBayar'=>$jmlKurangBayar						
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
