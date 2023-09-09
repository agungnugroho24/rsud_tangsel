<?php
class cetakKwtRwtInapDiscountXls extends XlsGen
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
		$notrans=$this->Request['notrans'];
		
		$tglKeluarTxt=$this->convertDate($this->Request['tglKeluar'],'3');
		$wktKeluar=$this->Request['wktKeluar'];
		$lamaInap=$this->Request['lamaInap'];
		
		$nmPembayar=$this->Request['nmPembayar'];
		
		$jmlSisaPlafon = $this->Request['jmlSisaPlafon'];
		
		$jsEmergency = $this->Request['jsEmergency'];
		$jsKonsul = $this->Request['jsKonsul'];
		$jsVisite = $this->Request['jsVisite'];
		$jsTdkOperasi = $this->Request['jsTdkOperasi'];
		$jsKamarOperasi = $this->Request['jsKamarOperasi'];
		$jsSewaAlat = $this->Request['jsSewaAlat'];
		$jsKamar = $this->Request['jsKamar'];
		$jsAskep = $this->Request['jsAskep'];
		$jsPenunjang = $this->Request['jsPenunjang'];
		$jsSewaAlatPenunjang = $this->Request['jsSewaAlatPenunjang'];
		$jsTdkKecil = $this->Request['jsTdkKecil'];
		$jsTdkKhusus = $this->Request['jsTdkKhusus'];
		$jsObat = $this->Request['jsObat'];
		$jsOksigen = $this->Request['jsOksigen'];
		$jsAmbulan = $this->Request['jsAmbulan'];
		$jsLainLain = $this->Request['jsLainLain'];
		$jsAdm = $this->Request['jsAdm'];
		
			
		$jmlBayar=$this->Request['jmlBayar'];
		$jmlTagihan=$this->Request['jmlTagihan'];
		
		$besarDisc = $this->Request['besarDisc'];
		$stDisc = $this->Request['stDisc'];
		$totBiayaDisc = $this->Request['totBiayaDisc'];
		$totBiayaDiscBulat = $this->Request['totBiayaDiscBulat'];
		$sisaDiscBulat = $this->Request['sisaDiscBulat'];
		
		$uangMuka=$this->Request['uangMuka'];
		$sisaByr=$this->Request['sisaByr'];	
		$jmlKurangBayar=$this->Request['jmlKurangBayar'];		
		$biayaMaterai=$this->Request['materai'];		
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		//$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		//$nmTable=$this->Request['table'];
		
		
		//$jmlBayar=number_format($jmlBayar,2,',','.');
		$sisaBayar=number_format($this->Request['sisa'],2,',','.');
		$sisa=$this->Request['sisa'];
		
		if($stDisc == '1')
		{
			$tagihanTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
		}
		else
		{
			//$tagihanTerbilang=ucwords($this->terbilang($jmlKurangBayar) . ' rupiah');
			$tagihanTerbilang=ucwords($this->terbilang($jmlTagihan) . ' rupiah');
		}
		
		$bayarTerbilang=ucwords($this->terbilang($jmlTagihan) . ' rupiah');
		
		$kelas = RwtInapRecord::finder()->findByPk($notrans)->kelas;
		$stBayi = RwtInapRecord::finder()->findByPk($notrans)->st_bayi;
		
		$tglMasuk = RwtInapRecord::finder()->findByPk($notrans)->tgl_masuk;
		$tglKeluar = date('Y-m-d');
		
		$wktMasuk = RwtInapRecord::finder()->findByPk($notrans)->wkt_masuk;
		$wktKeluar = date('G:i:s');
		$lamaInapAktual = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);	
		
		
		//----------- hitung Jasa Fasilitas Rumah Sakit / jasa kamar ---------------------
		$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$notrans' AND st_bayar='1'";
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
			$lamaInapPindah = $row['lama_inap'];
			
			//$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
			//$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
			
			$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ?',array('1'))->tarif;							
			if($stBayi == '1')
				$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ?',array('2'))->tarif;
			else
				$tarifKamarBayi = 0;	
			
			$persetaseKelas = KelasKamarRecord::finder()->findByPk($kelas)->persentase;
			
			$tarifKamarIbu = $tarifKamarIbu + ($tarifKamarIbu * $persetaseKelas / 100);
			$tarifKamarBayi = $tarifKamarBayi + ($tarifKamarBayi * $persetaseKelas / 100);
										
			if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
			{
				$tglKeluar = date('Y-m-d');
				$wktKeluar = date('G:i:s');
				$lamaInapPindah = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
			}
			
			$jmlJsKmrIbu += $tarifKamarIbu * $lamaInapPindah; 
			$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInapPindah - 1); 						
			
			/*
			$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
			$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
			
			$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
			$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
			*/
			$counter++;
		}
		
		$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
		//$jpm = $jmlJpmIbu + $jmlJpmBayi; 
		
		
		$sql = "SELECT * FROM view_pasien_rwt_inap WHERE cm='$cm' AND no_trans='$notrans'";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$noTrans=$row['no_trans'];
			$nmPasien=ucwords($row['nm_pasien']);
			$pjPasien=ucwords($row['nm_pj']);
			$AlmtPjPasien=ucwords($row['almt_pj']);
			$kls_kmr=$row['nm_kls_kamar'];
			$nm_kmr=$row['nm_kamar'];
			$kode_ruang=$row['kode_ruang'];
			$tarif_kamar=$row['tarif'];
			$tgl_masuk=$this->convertDate($row['tgl_masuk'],'3');
			$wkt_masuk=$row['wkt_masuk'];
		}	
		
		$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);
		
		$file = 'KwtRawatInap-'.$notrans.'.xls';
		
		$this->HeaderingExcel($file);	
		
		$workbook = new Workbook("-");
		
		//------------------------------------------- KWITANSI -------------------------------------------	
		$worksheet1= & $workbook->add_worksheet('Kwitansi');
		
		//--------------------- FORMAT LEFT ---------------------------
		$frmtLeftNoBorder =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftNoBorder,'','0','10');
		$left= $this->AddFormat($frmtLeftNoBorder,'bd','0','');
		$left= $this->AddFormat($frmtLeftNoBorder,'HA','left','');
		
		$frmtLeftNoBorder12 =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftNoBorder12,'','0','12');
		$left= $this->AddFormat($frmtLeftNoBorder12,'bd','0','');
		$left= $this->AddFormat($frmtLeftNoBorder12,'HA','left','');
		
		$frmtLeftNoBorderBold =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftNoBorderBold,'b','1','10');
		$left= $this->AddFormat($frmtLeftNoBorderBold,'bd','0','');
		$left= $this->AddFormat($frmtLeftNoBorderBold,'HA','left','');
		
		$frmtLeftNoBorderBold12 =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftNoBorderBold12,'b','1','12');
		$left= $this->AddFormat($frmtLeftNoBorderBold12,'bd','0','');
		$left= $this->AddFormat($frmtLeftNoBorderBold12,'HA','left','');
		
		
		$frmtLeftBorder =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftBorder,'','0','10');
		$left= $this->AddFormat($frmtLeftBorder,'bd','1','');
		$left= $this->AddFormat($frmtLeftBorder,'HA','left','');
		
		$frmtLeftBorder12 =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftBorder12,'','0','12');
		$left= $this->AddFormat($frmtLeftBorder12,'bd','1','');
		$left= $this->AddFormat($frmtLeftBorder12,'HA','left','');
		
		$frmtLeftBorderBold =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftBorderBold,'b','1','10');
		$left= $this->AddFormat($frmtLeftBorderBold,'bd','1','');
		$left= $this->AddFormat($frmtLeftBorderBold,'HA','left','');
		
		$frmtLeftBorderBold12 =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeftBorderBold12,'b','1','12');
		$left= $this->AddFormat($frmtLeftBorderBold12,'bd','1','');
		$left= $this->AddFormat($frmtLeftBorderBold12,'HA','left','');
		
		//--------------------- FORMAT RIGHT ---------------------------
		$frmtRightNoBorder =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightNoBorder,'','0','10');
		$right= $this->AddFormat($frmtRightNoBorder,'bd','0','');
		$right= $this->AddFormat($frmtRightNoBorder,'HA','right','');
		
		$frmtRightNoBorder12 =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightNoBorder12,'','0','12');
		$right= $this->AddFormat($frmtRightNoBorder12,'bd','0','');
		$right= $this->AddFormat($frmtRightNoBorder12,'HA','right','');
		
		$frmtRightNoBorderBold =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightNoBorderBold,'b','1','10');
		$right= $this->AddFormat($frmtRightNoBorderBold,'bd','0','');
		$right= $this->AddFormat($frmtRightNoBorderBold,'HA','right','');
		
		$frmtRightNoBorderBold12 =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightNoBorderBold12,'b','1','12');
		$right= $this->AddFormat($frmtRightNoBorderBold12,'bd','0','');
		$right= $this->AddFormat($frmtRightNoBorderBold12,'HA','right','');
		
		
		$frmtRightBorder =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightBorder,'','0','10');
		$right= $this->AddFormat($frmtRightBorder,'bd','1','');
		$right= $this->AddFormat($frmtRightBorder,'HA','right','');
		
		$frmtRightBorder12 =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightBorder12,'','0','12');
		$right= $this->AddFormat($frmtRightBorder12,'bd','1','');
		$right= $this->AddFormat($frmtRightBorder12,'HA','right','');
		
		$frmtRightBorderBold =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightBorderBold,'b','1','10');
		$right= $this->AddFormat($frmtRightBorderBold,'bd','1','');
		$right= $this->AddFormat($frmtRightBorderBold,'HA','right','');
		
		$frmtRightBorderBold12 =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRightBorderBold12,'b','1','12');
		$right= $this->AddFormat($frmtRightBorderBold12,'bd','1','');
		$right= $this->AddFormat($frmtRightBorderBold12,'HA','right','');
		
		
		//--------------------- FORMAT CENTER ---------------------------
		$frmtCenterNoBorder =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterNoBorder,'','0','10');
		$center= $this->AddFormat($frmtCenterNoBorder,'bd','0','');
		$center= $this->AddFormat($frmtCenterNoBorder,'HA','center','');
		
		$frmtCenterNoBorder12 =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterNoBorder12,'','0','12');
		$center= $this->AddFormat($frmtCenterNoBorder12,'bd','0','');
		$center= $this->AddFormat($frmtCenterNoBorder12,'HA','center','');
		
		$frmtCenterNoBorderBold =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterNoBorderBold,'b','1','10');
		$center= $this->AddFormat($frmtCenterNoBorderBold,'bd','0','');
		$center= $this->AddFormat($frmtCenterNoBorderBold,'HA','center','');
		
		$frmtCenterNoBorderBold12 =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterNoBorderBold12,'b','1','12');
		$center= $this->AddFormat($frmtCenterNoBorderBold12,'bd','0','');
		$center= $this->AddFormat($frmtCenterNoBorderBold12,'HA','center','');
		
		
		$frmtCenterBorder =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterBorder,'','0','10');
		$center= $this->AddFormat($frmtCenterBorder,'bd','1','');
		$center= $this->AddFormat($frmtCenterBorder,'HA','center','');
		
		$frmtCenterBorder12 =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterBorder12,'','0','12');
		$center= $this->AddFormat($frmtCenterBorder12,'bd','1','');
		$center= $this->AddFormat($frmtCenterBorder12,'HA','center','');
		
		$frmtCenterBorderBold =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterBorderBold,'b','1','10');
		$center= $this->AddFormat($frmtCenterBorderBold,'bd','1','');
		$center= $this->AddFormat($frmtCenterBorderBold,'HA','center','');
		
		$frmtCenterBorderBold12 =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenterBorderBold12,'b','1','12');
		$center= $this->AddFormat($frmtCenterBorderBold12,'bd','1','');
		$center= $this->AddFormat($frmtCenterBorderBold12,'HA','center','');
		
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'bd','1','');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		$wrap= $this->AddFormat($frmtWrap,'WR','1','');
		
		$baris=0;
		$kolom=0;
		
		//set lebar tiap kolom
		$this->AddWS($worksheet1,'c','18',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','40',$baris,$kolom); $baris++; $kolom++;
		
		$baris=0;
		$kolom=0;
		
		$worksheet1->write_string($baris,0,'PEMERINTAH KOTA TANGERANG SELATAN' ,$frmtLeftNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+4);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'JL. Pajajaran No. 101 Pamulang' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+4);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'Telp. (021) 74718440' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+4);
		
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,0,'KUITANSI' ,$frmtCenterNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+4);
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,0,'No. Kuitansi : '.$noKwitansi ,$frmtCenterNoBorderBold);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+4);
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'Telah Terima dari',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$nmPembayar,$frmtLeftNoBorder); 
		$worksheet1->merge_cells($baris, $kolom, $baris, 4);
		$kolom++;
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,'Uang Sebesar',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tagihanTerbilang,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, 4);
		$kolom++;
		$baris++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,'Untuk Pembayaran',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'- Biaya Perawatan Pasien',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$nmPasien,$frmtLeftNoBorder); $kolom++;
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'- Nomor Catatan Medik',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$cm,$frmtLeftNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		$i=0;
		$sqlOperasiBil = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans' ";
		$arrOperasiBil=$this->queryAction($sqlOperasiBil,'S');
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'- Tindakan',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder); $kolom++;
				}
				else
				{
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder); $kolom++;
				}
				$baris++; $kolom = 0;
			}
			else
			{
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'- Tindakan',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder); $kolom++;
				$baris++; $kolom = 0;
			}
			
			$i++;
		}
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'- Dirawat Tanggal',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tgl_masuk.'  s.d.  '.$tglKeluarTxt,$frmtLeftNoBorder); $kolom++;
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Total Transaksi',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlTagihan,2,',','.'),$frmtLeftNoBorderBold); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Uang Muka',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($uangMuka,2,',','.'),$frmtLeftNoBorderBold); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Sisa Pembayaran',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtLeftNoBorderBold); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Discount',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$worksheet1->write_string($baris,$kolom,$besarDisc.' %',$frmtLeftNoBorderBold); $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'0 %',$frmtLeftNoBorderBold); $kolom++;
		}
		
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Total Setelah Discount',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		
		if($totBiayaDiscBulat != '')
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),$frmtLeftNoBorderBold); $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtLeftNoBorderBold); $kolom++;
		}		
		
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'K a s i r,',$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 0;
		$baris++;
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'('.$operator.')',$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'NIP. '.$nip,$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		
		//------------------------------------------- RINCIAN BIAYA PERAWATAN -------------------------------------------		
		$worksheet1= & $workbook->add_worksheet('Rincian Biaya Perawatan');
		
		$baris=0;
		$kolom=0;
		
		//set lebar tiap kolom
		$this->AddWS($worksheet1,'c','3',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','7',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		
		$baris=0;
		$kolom=0;
		
		$worksheet1->write_string($baris,0,'PEMERINTAH KOTA TANGERANG SELATAN' ,$frmtLeftNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'JL. Pajajaran No. 101 Pamulang' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'Telp. (021) 74718440' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,0,'RINCIAN BIAYA PERAWATAN' ,$frmtCenterNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		$baris++; $baris++; $kolom = 0;
		
		
		$worksheet1->write_string($baris,$kolom,'No. Nota Penagihan',$frmtLeftNoBorder); 
		$worksheet1->merge_cells($baris, $kolom, $baris, 1); $kolom++; $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'-',$frmtLeftNoBorder); 
		$worksheet1->merge_cells($baris, $kolom, $baris, 9); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'A.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Nama Pasien',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$nmPasien,$frmtLeftNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$worksheet1->write_string($baris,$kolom,'B.',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'Tindakan',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder);  
					$worksheet1->merge_cells($baris, $kolom, $baris, 9); $kolom++;
				}
				else
				{
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder);  
					$worksheet1->merge_cells($baris, $kolom, $baris, 9); $kolom++;
				}
				$baris++; $kolom = 0;
			}
			else
			{
				$worksheet1->write_string($baris,$kolom,'B.',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Tindakan',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder);  $kolom++;
				$worksheet1->merge_cells($baris, $kolom, $baris, 6); $kolom++;
				
				$worksheet1->write_string($baris,$kolom,'Kamar',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,$nm_kmr,$frmtLeftNoBorder);  $kolom++;
				$baris++; $kolom = 0;
			}
			
			$i++;
		}
		
		
		$worksheet1->write_string($baris,$kolom,'C.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Tanggal Masuk',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tgl_masuk,$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jam',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$wkt_masuk.' WIB',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kamar',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$nm_kmr,$frmtLeftNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Tanggal Keluar',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tglKeluarTxt,$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jam',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$wktKeluar.' WIB',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kelas',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$kls_kmr,$frmtLeftNoBorder); $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jumlah Hari',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$lamaInap.' Hari',$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'No. RM',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$cm,$frmtLeftNoBorder); $kolom++;
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'Uraian Biaya Perawatan :',$frmtLeftNoBorderBold); 
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		$baris++; $kolom = 0;
		
		$no = 1;
		
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Emergency',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsEmergency,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;		
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jasa Konsultasi',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsKonsul,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jasa Visite',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsVisite,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jasa Tindakan',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsTdkOperasi,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Akomodasi Kamar Tindakan',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsKamarOperasi,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Sewa Alat Tindakan dan Bhp',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsSewaAlat,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kamar Perawatan',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsKamar,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jasa Asuhan Keperawatan',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsAskep,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Pemeriksaan Penunjang Medik',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsPenunjang,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Sewa Alat Penunjang Medik',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsSewaAlatPenunjang,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Tindakan Kecil',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsTdkKecil,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Tindakan Khusus',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsTdkKhusus,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Obat dan Alkes',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsObat,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Oksigen',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsOksigen,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Ambulance',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsAmbulan,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Biaya Lain-Lain',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsLainLain,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$no.'.',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Biaya Administrasi',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$jsAdm,$frmtRightNoBorder);  $kolom++;
		$no++; $baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Total Transaksi',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlTagihan,2,',','.'),$frmtRightNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Uang Muka',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($uangMuka,2,',','.'),$frmtRightNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Sisa Pembayaran',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtRightNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Discount',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$worksheet1->write_string($baris,$kolom,$besarDisc.' %',$frmtRightNoBorder);  $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'0 %',$frmtRightNoBorder);  $kolom++;
		}
		$baris++; $kolom = 0;
		
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Jumlah bayar',$frmtLeftNoBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorderBold); $kolom++;
		
		if($totBiayaDiscBulat != '')
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),$frmtRightNoBorderBold);  $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtRightNoBorderBold);  $kolom++;
		}
		
		
		
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Terbilang :',$frmtLeftNoBorderBold); 
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tagihanTerbilang,$frmtLeftNoBorderBold); 
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+9);
		
		$baris++; $baris++; $kolom = 8;
		
		$worksheet1->write_string($baris,$kolom,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$frmtCenterNoBorder); 
		$baris++; $$kolom = 8;
		
		$worksheet1->write_string($baris,$kolom,'K a s i r,',$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 8;
		$baris++;
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,$kolom,'('.$operator.')',$frmtCenterNoBorder);
		$baris++; $kolom = 8;
		
		$worksheet1->write_string($baris,$kolom,'NIP. '.$nip,$frmtCenterNoBorder); 
		$baris++; $kolom = 8;
		
		
		//------------------------------------------- NOTA PENAGIHAN PERAWATAN -------------------------------------------		
		$worksheet1= & $workbook->add_worksheet('Nota Penagihan Perawatan');
		
		$baris=0;
		$kolom=0;
		
		//set lebar tiap kolom
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','30',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','2',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		
		$baris=0;
		$kolom=0;
		
		$worksheet1->write_string($baris,0,'PEMERINTAH KOTA TANGERANG SELATAN' ,$frmtLeftNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'JL. Pajajaran No. 101 Pamulang' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
		$baris++;
		$kolom = 0;
		$worksheet1->write_string($baris,0,'Telp. (021) 74718440' ,$frmtLeftNoBorder);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
		
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,0,'NOTA PENAGIHAN PERAWATAN' ,$frmtCenterNoBorderBold12);
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
		$baris++; $baris++; $kolom = 0;
		
		
		$worksheet1->write_string($baris,$kolom,'Nama Pasien',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$nmPasien,$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kelas',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$kls_kmr,$frmtLeftNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,'No. Rekam Medis',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$cm,$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Kamar',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$kode_ruang,$frmtLeftNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'Tanggal Masuk',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tgl_masuk,$frmtLeftNoBorder);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Tanggal Keluar',$frmtLeftNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
		$worksheet1->write_string($baris,$kolom,$tglKeluarTxt,$frmtLeftNoBorder);  $kolom++;
		$baris++; $kolom = 0;
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($i==0)
			{
				$worksheet1->write_string($baris,$kolom,'Tindakan',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,':',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder); 
				$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
			}
			else
			{
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtCenterNoBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'- '.$row['nm_opr'],$frmtLeftNoBorder); 
				$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+5);
			}
			
			$baris++; $kolom = 0;
			$i++;
		}
		
		$baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'Tanggal',$frmtCenterBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Uraian Biaya',$frmtCenterBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Pelaksana',$frmtCenterBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$frmtCenterBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Keterangan',$frmtCenterBorderBold); $kolom++;
		$worksheet1->write_string($baris,$kolom,'Biaya',$frmtCenterBorderBold); $kolom++;
		$worksheet1->merge_cells($baris, 0, $baris, 1); 
		$worksheet1->merge_cells($baris, 3, $baris, 4); 
		$baris++; $kolom = 0;
		
		$noUrut = 0;
		//------------------------------- Jasa Emergency ----------------------------------//
		if($this->textToNumber($jsEmergency) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Emergency',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsEmergency,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_emergency WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,EmergencyRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Konsultasi ----------------------------------//
		if($this->textToNumber($jsKonsul) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Jasa Konsultasi',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsKonsul,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_konsultasi WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,NamaTindakanRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Visite ----------------------------------//
		if($this->textToNumber($jsVisite) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Jasa Visite',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsVisite,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans ='$notrans' AND nm_penunjang = 'visite' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,$row['nm_tdk'],$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,$row['nm_pegawai'],$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Tenaga Ahli / Operasi Billing ----------------------------------//
		if($this->textToNumber($jsTdkOperasi) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Jasa Tindakan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsTdkOperasi,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT 
					*, (tarif_obgyn + jasa_koordinator + 
					 tarif_anastesi + jasa_koordinator_anastesi + 
					 tarif_anak + jasa_koordinator_ast_anastesi + 
					 tarif_assdktr + jasa_koordinator_ast_instrumen + 
					 tarif_paramedis + jasa_koordinator_paramedis + 
					 tarif_resusitasi + jasa_koordinator_resusitasi + 
					 rs + tarif_pengembang + tarif_penyulit) AS jumlah
					 FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,$row['nm_opr'],$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jumlah'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Kamar Operasi ----------------------------------//
		if($this->textToNumber($jsKamarOperasi) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Akomodasi Kamar Tindakan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsKamarOperasi,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT 
					*, (sewa_ok) AS jumlah
					 FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$idOperasi = $row['id_opr'];
				$idKamarOperasi = OperasiNamaRecord::finder()->findByPk($row['id_opr'])->id_kamar_operasi;
				$idKategOperasi = OperasiNamaRecord::finder()->findByPk($row['id_opr'])->id_kategori_operasi;
				
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,OperasiKategoriRecord::finder()->findByPk($idKategOperasi)->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jumlah'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Sewa Alat ----------------------------------//
		if($this->textToNumber($jsSewaAlat) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Jasa Sewa Alat & BHP',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsSewaAlat,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_sewa_alat WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,SewaAlatRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$sql = "SELECT * FROM tbt_inap_bhp WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,BhpTindakanInapRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		//------------------------------- Biaya Fasilitas Rumah Sakit ----------------------------------//
		if($this->textToNumber($jsKamar) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Kamar Perawatan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsKamar,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
			
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$notrans' AND st_bayar = '1'";
			//$sql = "SELECT * FROM view_jasa_kamar WHERE cm='$cm' AND no_trans='$notrans'";
			$arr = $this->queryAction($sql,'S');
			$jmlDataInapKmr = count($arr);
			$counter = 1;
			foreach($arr as $row)
			{
				$idJnsKamar = RwtInapRecord::finder()->findByPk($notrans)->jenis_kamar;
				$nmKamar = KamarNamaRecord::finder()->findByPk($idJnsKamar)->nama;
				
				$kelas = $row['id_kmr_awal'];
				$nmKelas = KamarKelasRecord::finder()->findByPk($kelas)->nama;
				$lamaInapPindah = $row['lama_inap'];
				
				$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
				//$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ?',array('2'))->tarif;
				
				//$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ?',array('1'))->tarif;							
				if($stBayi == '1')
					$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
				else
					$tarifKamarBayi = 0;	
				
				//$persetaseKelas = KelasKamarRecord::finder()->findByPk($kelas)->persentase;
				$persetaseKelas = 0;
				
				$tarifKamarIbu = $tarifKamarIbu + ($tarifKamarIbu * $persetaseKelas / 100);
				$tarifKamarBayi = $tarifKamarBayi + ($tarifKamarBayi * $persetaseKelas / 100);
										
				if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
				{
					$tglKeluar = date('Y-m-d');
					$wktKeluar = date('G:i:s');
					$lamaInapPindah = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
				}
				
				$jmlJsKmrIbu = $tarifKamarIbu * $lamaInapPindah; 
				$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInapPindah - 1); 							
				
				/*
				$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
				$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
				
				$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
				$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
				*/
				$counter++;
				
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl_awal'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,$nmKamar.' '.$nmKelas,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,$lamaInapPindah.' Hari',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlJsKmrIbu+$jmlJsKmrBayi,2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Askep ----------------------------------//
		if($this->textToNumber($jsAskep) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Jasa Asuhan Keperawatan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsAskep,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_askep WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,AskepRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Pemeriksaan Penunjang Medik ----------------------------------//
		if($this->textToNumber($jsPenunjang) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Pemeriksaan Penunjang Medik',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsPenunjang,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
			
			$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 1 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$worksheet1->write_string($baris,$kolom,'      Laboratorium',$frmtLeftBorderBold);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtRightBorderBold);  $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 6); 
				$baris++; $kolom = 0;
				
				foreach($arr as $row)
				{
					$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,LabTdkRecord::finder()->findByPk($row['id_tindakan'])->nama,$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
					$worksheet1->merge_cells($baris, 0, $baris, 1); 
					$worksheet1->merge_cells($baris, 3, $baris, 4); 
					$baris++; $kolom = 0;
				}
			}
			
			$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 1 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$worksheet1->write_string($baris,$kolom,'      Radiologi',$frmtLeftBorderBold);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtRightBorderBold);  $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 6); 
				$baris++; $kolom = 0;
				
				foreach($arr as $row)
				{
					$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,RadTdkRecord::finder()->findByPk($row['id_tindakan'])->nama,$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
					$worksheet1->merge_cells($baris, 0, $baris, 1); 
					$worksheet1->merge_cells($baris, 3, $baris, 4); 
					$baris++; $kolom = 0;
				}
			} 
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Sewa Alat Penunjang ----------------------------------//
		if($this->textToNumber($jsSewaAlatPenunjang) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Sewa Alat Penunjang Medik',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsSewaAlatPenunjang,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_sewa_alat_penunjang WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,SewaAlatPenunjangRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Tindakan Kecil ----------------------------------//
		if($this->textToNumber($jsTdkKecil) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Tindakan Kecil',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsTdkKecil,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
			
			$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 AND nm_penunjang='tindakan dokter'";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$worksheet1->write_string($baris,$kolom,'      Tindakan Dokter',$frmtLeftBorderBold);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtRightBorderBold);  $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 6); 
				$baris++; $kolom = 0;
				
				foreach($arr as $row)
				{
					$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,$row['nm_tdk'],$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,$row['nm_pegawai'],$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
					$worksheet1->merge_cells($baris, 0, $baris, 1); 
					$worksheet1->merge_cells($baris, 3, $baris, 4); 
					$baris++; $kolom = 0;
				}
			}
			
			$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2 AND nm_penunjang='tindakan paramedis'";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$worksheet1->write_string($baris,$kolom,'      Tindakan Paramedis',$frmtLeftBorderBold);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtRightBorderBold);  $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 6); 
				$baris++; $kolom = 0;
				
				foreach($arr as $row)
				{
					$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,$row['nm_tdk'],$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,$row['nm_pegawai'],$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
					$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
					$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
					$worksheet1->merge_cells($baris, 0, $baris, 1); 
					$worksheet1->merge_cells($baris, 3, $baris, 4); 
					$baris++; $kolom = 0;
				}
			}
			
			$noUrut ++;
		}
		
		//------------------------------- Jasa Tindakan Khusus ----------------------------------//
		if($this->textToNumber($jsTdkKhusus) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Tindakan Khusus',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsTdkKhusus,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_tdk_khusus WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,TindakanKhususRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Biaya Obat-obatan & Alkes ----------------------------------//
		if($this->textToNumber($jsObat) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Obat & Alat Kesehatan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsObat,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
			
			$sql = "SELECT *, SUM(jml) AS jml FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=1 AND st_bayar=0 GROUP BY no_reg ORDER BY tgl";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'No. Resep '.$row['no_reg'],$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['jml'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
				
			$noUrut ++;
		}
		
		//------------------------------- Jasa Oksigen ----------------------------------//
		if($this->textToNumber($jsOksigen) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Oksigen',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsOksigen,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_oksigen WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,InapTarifOksigenRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Ambulan ----------------------------------//
		if($this->textToNumber($jsAmbulan) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Ambulan',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsAmbulan,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_ambulan WHERE cm='$cm' AND no_trans_inap ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,TarifAmbulanRecord::finder()->findByPk($row['tujuan'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Biaya Lain ----------------------------------//
		if($this->textToNumber($jsLainLain) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Biaya Lain',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsLainLain,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
						
			$sql = "SELECT * FROM tbt_inap_biaya_lain WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,BiayaLainRecord::finder()->findByPk($row['id_tdk'])->nama,$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($row['tarif'],2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
			
			if($this->textToNumber($biayaMaterai) > 0)
			{
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'Biaya Materai',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,$biayaMaterai,$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}
		
			//----------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ---------------------
			if(RwtInapRecord::finder()->find('no_trans=? AND status=? AND st_alih=?',$noTrans,'0','1'))				
			{
				$sql = "SELECT SUM(jml) AS jumlah
							   FROM view_biaya_alih
							   WHERE no_trans_inap = '$noTrans'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$jml += $row['jumlah'];
				}
				
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$frmtCenterBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'Biaya selama rawat jalan',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
				$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder); $kolom++;
				$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jml,2,',','.'),$frmtRightBorder); $kolom++;
				$worksheet1->merge_cells($baris, 0, $baris, 1); 
				$worksheet1->merge_cells($baris, 3, $baris, 4); 
				$baris++; $kolom = 0;
			}	
			
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Administrasi ----------------------------------//
		if($this->textToNumber($jsAdm) > 0)
		{	
			$worksheet1->write_string($baris,$kolom,$this->numberToRoman($noUrut+1).'. Administrasi Rumah Sakit',$frmtLeftBorderBold);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,'',$frmtLeftBorder);  $kolom++;
			$worksheet1->write_string($baris,$kolom,$jsAdm,$frmtRightBorderBold);  $kolom++;
			$worksheet1->merge_cells($baris, 0, $baris, 5); 
			$baris++; $kolom = 0;
			
			$noUrut ++;
		}	
		
		$kolom = 5;
		
		$worksheet1->write_string($baris,$kolom,'Total Transaksi',$frmtLeftBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlTagihan,2,',','.'),$frmtRightBorderBold);  $kolom++;
		$baris++; $kolom = 5;
		$worksheet1->write_string($baris,$kolom,'Uang Muka',$frmtLeftBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($uangMuka,2,',','.'),$frmtRightBorderBold);  $kolom++;
		$baris++; $kolom = 5;
		$worksheet1->write_string($baris,$kolom,'Sisa Pembayaran',$frmtLeftBorderBold);  $kolom++;
		$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtRightBorderBold);  $kolom++;
		$baris++; $kolom = 5;
		$worksheet1->write_string($baris,$kolom,'Discount',$frmtLeftBorderBold);  $kolom++;
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$worksheet1->write_string($baris,$kolom,$besarDisc.' %',$frmtRightBorderBold);  $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'0 %',$frmtRightBorderBold);
		}
		
		$baris++; $kolom = 5;
		$worksheet1->write_string($baris,$kolom,'TOTAL',$frmtLeftBorderBold);  $kolom++;
		
		if($totBiayaDiscBulat != '')
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),$frmtRightBorderBold);  $kolom++;
		}
		else
		{
			$worksheet1->write_string($baris,$kolom,'Rp. '.number_format($jmlKurangBayar,2,',','.'),$frmtRightBorderBold);  $kolom++;
		}		
		
		$baris++; $baris++; $kolom = 0;
		
		$worksheet1->write_string($baris,$kolom,'Terbilang :',$frmtLeftNoBorderBold); 
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+6);
		$baris++; $kolom = 0;
		$worksheet1->write_string($baris,$kolom,$tagihanTerbilang,$frmtLeftNoBorderBold); 
		$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+6);
		
		$baris++; $baris++; $kolom = 5;
		
		$worksheet1->write_string($baris,$kolom,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$frmtCenterNoBorder); 
		$baris++; $$kolom = 5;
		
		$worksheet1->write_string($baris,$kolom,'K a s i r,',$frmtCenterNoBorder); $kolom++;
		$baris++; $kolom = 5;
		$baris++;
		$baris++;
		$baris++;
		
		$worksheet1->write_string($baris,$kolom,'('.$operator.')',$frmtCenterNoBorder);
		$baris++; $kolom = 5;
		
		$worksheet1->write_string($baris,$kolom,'NIP. '.$nip,$frmtCenterNoBorder); 
		
		$workbook->close(); 
	}
}
?>
