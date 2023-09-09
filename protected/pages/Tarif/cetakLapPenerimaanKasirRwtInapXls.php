<?php
class cetakLapPenerimaanKasirRwtInapXls extends XlsGen
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		$dokter=$this->Request['dokter'];
		$kasir=$this->Request['kasir'];
		$kelompok=$this->Request['kelompok'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		$modeInput=$this->Request['modeInput'];
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		//$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		//$nmTable=$this->Request['table'];
		
		/*		
		if($modeInput == '0') //mode global
		{
			$mode = "global";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - GLOBAL";
		}
		elseif($modeInput == '1') //mode tunai
		{
			$mode = "tunai";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - TUNAI";
		}
		elseif($modeInput == '2') //mode piutang
		{
			$mode = "piutang";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - PIUTANG";
		}
		*/
		
		$session=new THttpSession;
		$session->open();
		
		$sqlViewRwtInap = $session['cetakPenerimaanKasirRwtInap'];
		$sqlViewRwtInap .= " ORDER BY no_trans DESC ";
		
		$file = 'LapPenerimaanRawatInap.xls';
		
		//http headers	
		$this->HeaderingExcel($file);
		
		//membuat workbook
		$workbook=new Workbook("-");
		
		//membuat worksheet pertama
		$worksheet1= & $workbook->add_worksheet('Laporan 1');		
		
		//set lebar tiap kolom
		$this->AddWS($worksheet1,'c','8',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'b','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','0','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'bd','1','');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		$wrap= $this->AddFormat($frmtWrap,'WR','1','');
		
		$baris=0;
		$kolom=0;
		
		$worksheet1->write_string($baris,0,'LAPORAN PENERIMAAN RAWAT INAP' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,$periode,$frmtLeft);
		$baris++;
		
		if($kasir)
		{
			$worksheet1->write_string($baris,0,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,$frmtLeft);
			$baris++;
		}
		
		if($dokter)
		{
			$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmtLeft);
			$baris++;
		}
		
		if($kelompok)
		{
			$worksheet1->write_string($baris,0,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,$frmtLeft);
			$baris++;
		}
		
		$baris++;
		
		$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"No. Transaksi",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"CM",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Nama Pasien",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Kelas",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Tanggal Keluar",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Lama Rawat",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Kamar",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Obat Alkes",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Visite",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Tindakan Dokter",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Tindakan Paramedis",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Operasi",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Laboratorium",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Radiologi",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Fisio",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya BHP",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya JPM",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Askep",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Askep OK",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Askeb",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Oksigen",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Sinar",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Ambulan",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Alih Rawat Jalan",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Administrasi",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Materai",$wrap); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Biaya Total",$center); $kolom++;
		
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','1','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtRight =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRight,'','1','10');
		$right= $this->AddFormat($frmtRight,'bd','1','');
		$right= $this->AddFormat($frmtRight,'HA','right','');
		
		$baris++;
		$kolom = 0;
		$no = 1;
		
		$arrData=$this->queryAction($sqlViewRwtInap,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$noTrans = $row['no_trans'];
			$cm = $row['cm'];	
			$nmPasien = $row['nm_pasien'];	
			$kelas = $row['id_kelas'];	
			$nmKelas= $row['nm_kelas'];	
			$tglKeluar = $row['tgl_keluar'];	
			$stRujukan = $row['st_rujukan'];	
			
			//----------- Hitung Lama Inap --------------------
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
			$arr = $this->queryAction($sql,'S');
			$jmlDataInapKmr = count($arr);
			$counter = 1;
			foreach($arr as $row)
			{
				$lamaInap += $row['lama_inap'];
				/*
				if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
				{
					$tglMasuk = $row['tgl_awal'];
					$wktMasuk = $row['wkt_masuk'];
						
					$tglKeluar = date('Y-m-d');
					$wktKeluar = date('G:i:s');
					$lamaInap += $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				}
				$counter++;
				*/
			}
			$lamaInapTotal = $lamaInap;
			
			
			//------------------------------- BHP KHUSUS RWT INAP ------------------
				if($lamaInapTotal <= 3) //kurang atau sama dengan 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgBhp = 150000;
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgBhp = 125000;
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgBhp = 125000;
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgBhp = 100000;
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgBhp = 70000;
						}
					}
					else //lebih dari 3 hari
					{
						if($kelas == '1' ) //kelas VIP
						{
							$hrgBhp = (150000 + (150000 * 0.5));
						}
						else if($kelas == '2' ) //kelas IA
						{
							$hrgBhp = (125000 + (125000 * 0.5));
						}
						else if($kelas == '3' ) //kelas IB
						{
							$hrgBhp = (125000 + (125000 * 0.5));
						}
						else if($kelas == '4' ) //kelas II
						{
							$hrgBhp = (100000 + (100000 * 0.5));
						}
						else if($kelas == '5' ) //kelas III
						{
							$hrgBhp = (70000 + (70000 * 0.5));
						}
					}
				
				$this->setViewState('bhp',$hrgBhp);
				//$this->showSql->Text = $kelas.' - '.$hrgBhp;
				//$tarifAskep = InapAskepRecord::finder()->find('no_trans=?',$noTrans)->tarif;
				//$askep = $lamaInapTotal * $tarifAskep;				
				//$this->setViewState('askep',$askep);
				
				$jmlSisaPlafon = 0;
				
				//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND st='1'";
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
							   WHERE cm='$cm'
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
					if($lamaInapTotal <= $lamaHariPaket) //ambil harga paket
					{
						//$lamaInapTotal = $lamaHariPaket;
						//$this->lamaInap->Text = $lamaInapTotal.' hari';
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$jmlJsKmrPaket = $kamar_ibu + $kamar_bayi;						
						$jpmPaket = $jpm;
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
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
							$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
							/*						
							if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
							{
								$tglKeluar = date('Y-m-d');
								$wktKeluar = date('G:i:s');
								$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
							}
							*/
							$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
							$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
							
							
							$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
							
							$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
							$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
							
							//$this->showSql->Text .= $lamaInap.' ';
							
							$counter++;
						}
						
						$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						if($jmlJsKmr <= $jmlJsKmrPaket)
						{
							$jmlJsKmrSisa = $jmlJsKmrPaket - $jmlJsKmr;
						}
						else
						{
							$jmlJsKmrSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlJsKmrSisa;
						$this->setViewState('jmlSisaKamar',$jmlJsKmrSisa);	
						
						
						if($jpm <= $jpmPaket)
						{
							$jpmSisa = $jpmPaket - $jpm;
						}
						else
						{
							$jpmSisa = 0;
						}
						
						$jmlSisaPlafon += $jpmSisa;
						$this->setViewState('jmlSisaJpm',$jpmSisa);	
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						$jmlPenunjangPaket = $jmlVisiteAktual;
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, masukan ke sisaPlafon
						{ 
							//$jmlPenunjangPaket = $jmlVisitePaket;							
							$jmlPenunjangPaketSisa = $jmlVisitePaket - $jmlVisiteAktual;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaketSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlPenunjangPaketSisa;
						$this->setViewState('jmlSisaVisite',$jmlPenunjangPaketSisa);
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
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
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatAktual = $row['jumlah'];
						}		
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, masukan ke sisaPlafon
						{ 
							//$jmlObatAlkes = $obat - $jmlObatLunas;
							$jmlObatAlkesSisa = ($obat - $jmlObatLunas) - ($jmlObatAktual - $jmlObatLunas);
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkesSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlObatAlkesSisa;
						$this->setViewState('jmlSisaObat',$jmlObatAlkesSisa);
						
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
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
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '1'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
							
						$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;						
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, masukan ke sisaPlafon
						{ 
							//$jmlBiayaLainLab = $lab - $jmlLabLunas;
							$jmlBiayaLainLabSisa = ($lab - $jmlLabLunas) - ($jmlLabAktual - $jmlLabLunas);
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLabSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlBiayaLainLabSisa;
						$this->setViewState('jmlSisaLab',$jmlBiayaLainLabSisa);
						
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '1'";
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
											AND flag = '1'";
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
											AND flag = '1'";
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
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						$jmlBiayaAlih = 0;
			
						//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
						if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
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
				
						//$jnsRujuk = $tmpPasien->st_rujukan;
						if($stRujukan=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
						
					}
					else //ambil harga aktual
					{
						
						//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
						$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
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
							$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
													/*
							if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
							{
								$tglKeluar = date('Y-m-d');
								$wktKeluar = date('G:i:s');
								$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
							}
							*/
							$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
							$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
							
							
							$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
							$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
							
							$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
							$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
							
							//$this->showSql->Text .= $lamaInap.' ';
							
							$counter++;
						}
						
						$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
						
						$jpm = $jmlJpmIbu + $jmlJpmBayi; 
						
						
						//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
						$jmlTngAhli = $tarif_obgyn + $tarif_anastesi + $tarif_anak + $tarif_assdktr + $sewa_ok + $ctg ;
						
						
						//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
						$jmlVisitePaket = $visite_dokter_obgyn + $visite_dokter_anak;
						
						//----------- hitung Biaya Visite  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'visite')";
									   
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlVisiteAktual = $row['jumlah'];
						}	
						
						$jmlPenunjangPaket = $jmlVisiteAktual;
						$jmlVisite = $jmlPenunjangPaket;
						
						if($jmlVisiteAktual <= $jmlVisitePaket) //jika jml visite aktual <= jml visite paket, masukan ke sisaPlafon
						{ 
							//$jmlPenunjangPaket = $jmlVisitePaket;							
							$jmlPenunjangPaketSisa = $jmlVisitePaket - $jmlVisiteAktual;
						}
						else //ambil jml visite aktual
						{
							$jmlPenunjangPaketSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlPenunjangPaketSisa;
						$this->setViewState('jmlSisaVisite',$jmlPenunjangPaketSisa);	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (nm_penunjang = 'tindakan paramedis' OR nm_penunjang = 'tindakan dokter')";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlPenunjangTindakan = $row['jumlah'];
						}	
						
						$jmlPenunjang = $jmlPenunjangPaket + $jmlPenunjangTindakan;
						
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND nm_penunjang = 'tindakan dokter'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlTdkDokter = $row['jumlah'];
						}	
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_penunjang				
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND nm_penunjang = 'tindakan paramedis'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlTdkParamedis = $row['jumlah'];
						}	
						
						//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatAktual = $row['jumlah'];
						}		
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_obat_alkes				
									   WHERE cm='$cm'
									   AND no_trans_inap = '$noTrans'
									   AND flag = 1
									   AND st_bayar = 1 ";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlObatLunas = $row['jumlah'];
						}		
						
						$jmlObatAlkes = $jmlObatAktual - $jmlObatLunas;
						
						if($jmlObatAktual <= $obat) //jika jml obat aktual <= jml obat paket, masukan ke sisaPlafon
						{ 
							//$jmlObatAlkes = $obat - $jmlObatLunas;
							$jmlObatAlkesSisa = ($obat - $jmlObatLunas) - ($jmlObatAktual - $jmlObatLunas);
						}
						else //ambil jml obat aktual
						{
							$jmlObatAlkesSisa = 0;
						}
						
						$jmlSisaPlafon += $jmlObatAlkesSisa;
						$this->setViewState('jmlSisaObat',$jmlObatAlkesSisa);
						
						//----------- hitung Biaya Lain-Lain lab  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlLabAktual=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
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
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'lab'
									   AND st_bayar = '0'
									   AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlLabBlmBayar=$row['jumlah'];
							}
						
						$jmlBiayaLainLab = $jmlLabAktual - $jmlLabLunas;						
						$jmlBiayaLab = $jmlBiayaLainLab;						
							
						if($jmlLabAktual <= $lab) //jika jml lab aktual <= jml lab paket, masukan ke sisaPlafon
						{ 
							//$jmlBiayaLainLab = $lab - $jmlLabLunas;
							$jmlBiayaLainLabSisa = ($lab - $jmlLabLunas) - ($jmlLabAktual - $jmlLabLunas);
						}
						else //ambil jml lab aktual yg belum dibayar
						{
							$jmlBiayaLainLabSisa = 0;
						}	
						
						$jmlSisaPlafon += $jmlBiayaLainLabSisa;
						$this->setViewState('jmlSisaLab',$jmlBiayaLainLabSisa);
						
						//----------- hitung Biaya Lain-Lain rad & Fiiso  ---------------------
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND (jenis = 'rad' OR jenis = 'fisio')
									   AND st_bayar = '0'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaLainRadFisio=$row['jumlah'];
						}
						
						$jmlBiayaLainLabRad = $jmlBiayaLainLab + $jmlBiayaLainRadFisio;
						
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'rad'
									   AND st_bayar = '0'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaRad=$row['jumlah'];
						}
						
						$sql = "SELECT SUM(jml) AS jumlah
									   FROM view_biaya_lain_lab_rad			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND jenis = 'fisio'
									   AND st_bayar = '0'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaFisio=$row['jumlah'];
						}
						
						//----------- hitung Biaya Oksigen jika ada ---------------------
						if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
						{
							$sql = "SELECT tarif AS jumlah
										   FROM tbt_inap_oksigen			
										   WHERE 
											no_trans = '$noTrans'
											AND flag = '1'";
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
											AND flag = '1'";
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
											AND flag = '1'";
							$arr=$this->queryAction($sql,'S');
							foreach($arr as $row)
							{
								$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
							}
						}
						
						$jmlBiayaAlih = 0;
			
						//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
						if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
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
						if($stRujukan=='1') //jika pasien rujukan ada tambahan u/ biaya administrasi
						{
							$biayaAdm = $biayaAdm + $js_bidan_pengirim;	
							$this->setViewState('admRujukan',$js_bidan_pengirim);
						}
					}
				}
				
				
				
				if($stPaket == '0')
				{
					//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
					$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$noTrans' AND st_bayar='1'";
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
						$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
												/*
						if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
						{
							$tglKeluar = date('Y-m-d');
							$wktKeluar = date('G:i:s');
							$lamaInap = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
						}
						*/
						$jmlJsKmrIbu += $tarifKamarIbu * $lamaInap; 
						$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInap - 1); 						
						
						
						$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
						$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
						
						$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
						$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
						
						//$this->showSql->Text .= $lamaInap.' ';
						
						$counter++;
					}
					
					$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
					
					$jpm = $jmlJpmIbu + $jmlJpmBayi; 
					
					//----------- hitung Obat-Obatan & Alat Kesehatan ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_obat_alkes				
								   WHERE cm='$cm'
								   AND no_trans_inap = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0 ";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlObatAlkes=$row['jumlah'];
					}		
					
					//----------- hitung Jasa Tenaga Ahli / Operasi Billing ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_inap_operasi_billing				
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlTngAhli=$row['jumlah'];
					}	
					
					//----------- hitung Jasa Pemeriksaan Penunjang ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlPenunjang=$row['jumlah'];
					}	
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$cm'
								   AND nm_penunjang = 'visite'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlVisite=$row['jumlah'];
					}	
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$cm'
								   AND nm_penunjang = 'tindakan paramedis'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlTdkParamedis=$row['jumlah'];
					}	
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_penunjang				
								   WHERE cm='$cm'
								   AND nm_penunjang = 'tindakan dokter'
								   AND no_trans = '$noTrans'";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlTdkDokter=$row['jumlah'];
					}	
					
					//----------- hitung Biaya Lain-Lain lab & rad & fisio yg st_bayar=0 (bayar kredit) ---------------------
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_lain_lab_rad			
								   WHERE cm='$cm'
								   AND no_trans = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaLainLabRad=$row['jumlah'];
					}
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_lain_lab_rad			
								   WHERE cm='$cm'
								   AND jenis = 'lab'
								   AND no_trans = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaLab=$row['jumlah'];
					}
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_lain_lab_rad			
								   WHERE cm='$cm'
								   AND jenis = 'rad'
								   AND no_trans = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaRad=$row['jumlah'];
					}
					
					$sql = "SELECT SUM(jml) AS jumlah
								   FROM view_biaya_lain_lab_rad			
								   WHERE cm='$cm'
								   AND jenis = 'fisio'
								   AND no_trans = '$noTrans'
								   AND flag = 1
								   AND st_bayar = 0";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$jmlBiayaFisio=$row['jumlah'];
					}
					
					//----------- hitung Biaya Askep OK jika ada ---------------------
					if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$noTrans,'0'))				
					{
						$sql = "SELECT SUM(tarif) AS jumlah
									   FROM tbt_inap_askep_ok			
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND flag = '1'";
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
									   WHERE cm='$cm'
									   AND no_trans = '$noTrans'
									   AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAskeb= $lamaInapTotal * $row['jumlah']; //dikalikan lama hari
						}
					}			
					
					//----------- hitung Biaya Oksigen jika ada ---------------------
					if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
					{
						$sql = "SELECT tarif AS jumlah
									   FROM tbt_inap_oksigen			
									   WHERE 
										no_trans = '$noTrans'
										AND flag = '1'";
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
											AND flag = '1'";
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
										AND flag = '1'";
						$arr=$this->queryAction($sql,'S');
						foreach($arr as $row)
						{
							$jmlBiayaAmbulan = $row['jumlah']; //dikalikan lama hari
						}
					}
					
					
					$jmlBiayaAlih = 0;
			
					//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
					if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'1','1'))				
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
				$jmlBiayaLain = $jmlBiayaLainLabRad + 
								$askep + 
								$jmlBiayaAskepOk + 
								$jmlBiayaAskeb + 
								$jmlBiayaOksigen + 
								$jmlBiayaSinar+  
								$jmlBiayaAmbulan + 
								$jmlBiayaAlih +
								$hrgBhp
								;
				
				//Sisa Plafon
				$jmlTngAhli += $jmlSisaPlafon;
								
				//----------- hitung Biaya TOTAL ---------------------				
				$Total=$jmlJsKmr+$jmlObatAlkes+$jmlTngAhli+$jpm+$jmlPenunjang+$jmlBiayaLain;		 	
				$jmlTotal=$Total+$biayaAdm+$this->getViewState('metrai');
				
				
			$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$noTrans,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$cm,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$nmPasien,$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$nmKelas,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$this->convertDate($tglKeluar,'3'),$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$lamaInapTotal.' hari',$center);	$kolom++;
			
			$worksheet1->write_string($baris,$kolom,number_format($jmlJsKmr,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlObatAlkes,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlVisite,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlTdkDokter,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlTdkParamedis,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlTngAhli,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaLab,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaRad,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaFisio,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($hrgBhp,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jpm,'2',',','.'),$right);	$kolom++;
			
			$worksheet1->write_string($baris,$kolom,number_format($askep,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaAskepOk,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaAskeb,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaOksigen,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaSinar,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaAmbulan,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlBiayaAlih,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($biayaAdm,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($this->getViewState('metrai'),'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($jmlTotal,'2',',','.'),$right);	$kolom++;
			
			$totKamar += $jmlJsKmr ;
			$totObat += $jmlObatAlkes ;
			$totVisite += $jmlVisite ;
			$totTdkDokter += $jmlTdkDokter ;
			$totTdkParamedis += $jmlTdkParamedis ;
			$totOperasi += $jmlTngAhli ;
			$totLab += $jmlBiayaLab ;
			$totRad += $jmlBiayaRad ;
			$totFisio += $jmlBiayaFisio ;
			$totBhp += $hrgBhp ;
			$totJpm += $jpm ;
			$totAskep += $askep ;
			$totAskepOk += $jmlBiayaAskepOk ;
			$totAskeb += $jmlBiayaAskeb ;
			$totOksigen += $jmlBiayaOksigen ;
			$totSinar += $jmlBiayaSinar ;
			$totAmbulan += $jmlBiayaAmbulan ;
			$totAlih += $jmlBiayaAlih ;
			$totAdm += $biayaAdm;
			$totMaterai += $this->getViewState('metrai');
			$totGrand += $jmlTotal;
			
			$baris++;
			$kolom = 0;
			$no++;
		}
		
		
		$frmtRight =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRight,'b','1','10');
		$right= $this->AddFormat($frmtRight,'bd','1','');
		$right= $this->AddFormat($frmtRight,'HA','right','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$worksheet1->write_string($baris,$kolom,'GRAND TOTAL',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
		$worksheet1->merge_cells($baris, 0, $baris, 6);
		
		$worksheet1->write_string($baris,$kolom,number_format($totKamar,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totObat,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totVisite,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totTdkDokter,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totTdkParamedis,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totOperasi,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totLab,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totRad,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totFisio,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totBhp,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totJpm,'2',',','.'),$right);	$kolom++;
		
		$worksheet1->write_string($baris,$kolom,number_format($totAskep,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totAskepOk,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totAskeb,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totOksigen,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totSinar,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totAmbulan,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totAlih,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totAdm,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totMaterai,'2',',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totGrand,'2',',','.'),$right);	$kolom++;
			
		$workbook->close(); 
		
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
}
?>