<?php
class cetakKwtRwtInapPreviewDiscount extends SimakConf
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
		$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$notrans' AND st_bayar='0'";
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
			//if($stBayi == '1')
				//$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ?',array('2'))->tarif;
			//else
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
		
		
		$sql = "SELECT *, SUM(tarif) AS tarif, SUM(lama_inap) AS lama_inap FROM tbt_inap_kamar_bayi WHERE no_trans_inap = '$notrans' GROUP BY no_trans_inap";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{				
			$tarifKamarBayi = $row['tarif'];
			$lamaInapBayi = $row['lama_inap'];
		}
		
		$jmlJsKmr = $jmlJsKmrIbu + $tarifKamarBayi; 
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
		
		$pdf=new reportKwitansi('P','mm','a4');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		//------------------------------- KUITANSI ----------------------------------//
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Ln(8);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'KUITANSI','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		
		/*
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$noTrans,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);
		*/
		
		$pdf->Cell(30,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		//$pdf->Cell(30,10,$nmPasien.' / '.$pjPasien,0,0,'L');
		$pdf->Cell(30,10,$nmPembayar,0,0,'L');
		$pdf->Ln(5);		
		$pdf->Cell(30,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B',9);
		$pdf->MultiCell(0,5,$tagihanTerbilang,0,'L');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Untuk Pembayaran',0,0,'L');
		$pdf->Cell(5,10,':  ',0,0,'L');
		$pdf->Cell(45,10,'- Biaya Perawatan Pasien',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');	
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Nomor Catatan Medik',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		
		$pdf->Ln(5);
		
		$i=0;
		$sqlOperasiBil = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans' ";
		$arrOperasiBil=$this->queryAction($sqlOperasiBil,'S');
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(5,10,'  ',0,0,'L');
					$pdf->Cell(45,10,'- Tindakan',0,0,'L');	
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,'- '.$row['nm_opr'],0,0,'L');
				}
				else
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(5,10,'  ',0,0,'L');
					$pdf->Cell(45,10,'',0,0,'L');	
					$pdf->Cell(2,10,' ',0,0,'L');
					$pdf->Cell(30,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(30,10,'',0,0,'L');
				$pdf->Cell(5,10,'  ',0,0,'L');
				$pdf->Cell(45,10,'- Tindakan',0,0,'L');	
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,'-',0,0,'L');
				$pdf->Ln(5);
			}
			
			$i++;
		}
		
		
		//$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Dirawat Tanggal',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10, $tgl_masuk.'  s.d.  '.$tglKeluarTxt,0,0,'L');
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Ln(7);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'Total Transaksi',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'Uang Muka',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($uangMuka,2,',','.'),0,0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'Sisa Pembayaran',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlKurangBayar,2,',','.'),0,0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'Discount',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$pdf->Cell(30,10,$besarDisc.' %',0,0,'L');
		}
		else
		{
			$pdf->Cell(30,10,'0 %',0,0,'L');
		}
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'Total Setelah Discount',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		
		if($totBiayaDiscBulat != '')
		{
			$pdf->Cell(30,10,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'L');
		}
		else
		{
			$pdf->Cell(30,10,'Rp. '.number_format($jmlKurangBayar,2,',','.'),0,0,'L');
		}		
		
		$pdf->Ln(10);
		
		//------------------------------ TTD KASIR KUITANSI --------------------------------- //		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
			
		//$pdf->Cell(80,5,'Total ',0,0,'R');		
		//pdf->Cell(30,5,'Rp ' . $jmlTagihan,1,0,'R');					
		//$pdf->Cell(80,5,'Bayar ',0,0,'R');
		//$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');
		//$pdf->Cell(150,5,'Kembalian ',0,0,'R');
		//$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');	
		
		
		
		//------------------------------- RINCIAN BIAYA PERAWATAN ----------------------------------//
		//$pdf=new reportKwitansi('P','mm','a4');
		$pdf->AddPage();
		
		
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Ln(8);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'RINCIAN BIAYA PERAWATAN','0',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'No. Nota Penagihan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'-',0,0,'L');
		$pdf->Ln(5);	
		/*	
		$pdf->Cell(30,10,'Rekening Untuk',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$pjPasien,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Alamat Penanggung',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$AlmtPjPasien,0,0,'L');
		$pdf->Ln(10);
		*/
		$pdf->Cell(5,10,'A. ',0,0,'L');
		$pdf->Cell(25,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		$pdf->Ln(5);
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(5,10,'B. ',0,0,'L');
					$pdf->Cell(25,10,'Tindakan',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(85,10,'- '.$row['nm_opr'],0,0,'L');
			
					$pdf->Cell(20,10,'No. Kamar',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$nm_kmr,0,0,'L');
				}
				else
				{
					$pdf->Cell(5,10,' ',0,0,'L');
					$pdf->Cell(25,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(85,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(5,10,'B. ',0,0,'L');
				$pdf->Cell(25,10,'Tindakan',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(85,10,'-',0,0,'L');
		
				$pdf->Cell(20,10,'No. Kamar',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$nm_kmr,0,0,'L');
				$pdf->Ln(5);
			}
			
			$i++;
		}
		
		$pdf->Cell(5,10,'C. ',0,0,'L');
		$pdf->Cell(25,10,'Tanggal Masuk',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$tgl_masuk,0,0,'L');
		$pdf->Cell(15,10,'-  Jam   : ',0,0,'L');
		$pdf->Cell(35,10,$wkt_masuk.' WIB',0,0,'L');
		$pdf->Cell(20,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);	
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$tglKeluarTxt,0,0,'L');
		$pdf->Cell(15,10,'-  Jam   : ',0,0,'L');
		$pdf->Cell(35,10,$wktKeluar.' WIB',0,0,'L');
		$pdf->Cell(20,10,'D. Kelas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$kls_kmr,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(25,10,'Jumlah Hari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$lamaInap.' Hari',0,0,'L');
		$pdf->Cell(15,10,'',0,0,'L');
		$pdf->Cell(35,10,'',0,0,'L');
		//$pdf->Cell(20,10,'E. Tarif',0,0,'L');
		//$pdf->Cell(2,10,':  ',0,0,'L');
		//$pdf->Cell(30,10,'Rp. '.number_format($jmlJsKmr,2,',','.'),0,0,'L');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->Cell(30,10,'Uraian Biaya Perawatan :',0,0,'L');
		$pdf->Ln(5);	
			
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(5,10,'1. ',0,0,'L');
		$pdf->Cell(45	,10,'Emergency',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsEmergency,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'2. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Konsultasi ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsKonsul,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'3. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Visite ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsVisite,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'4. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Tindakan ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsTdkOperasi,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'5. ',0,0,'L');
		$pdf->Cell(45	,10,'Akomodasi Kamar Tindakan ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsKamarOperasi,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'6. ',0,0,'L');
		$pdf->Cell(45	,10,'Sewa Alat Tindakan dan Bhp ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsSewaAlat,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'7. ',0,0,'L');
		$pdf->Cell(45	,10,'Kamar Perawatan ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsKamar,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'8. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Asuhan Keperawatan ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsAskep,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'9. ',0,0,'L');
		$pdf->Cell(45	,10,'Pemeriksaan Penunjang Medik ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsPenunjang,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'10. ',0,0,'L');
		$pdf->Cell(45	,10,'Sewa Alat Penunjang Medik ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsSewaAlatPenunjang,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'11. ',0,0,'L');
		$pdf->Cell(45	,10,'Tindakan Kecil ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsTdkKecil,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'12. ',0,0,'L');
		$pdf->Cell(45	,10,'Tindakan Khusus ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsTdkKhusus,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'13. ',0,0,'L');
		$pdf->Cell(45	,10,'Obat dan Alkes',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsObat,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'14. ',0,0,'L');
		$pdf->Cell(45	,10,'Oksigen',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsOksigen,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'15. ',0,0,'L');
		$pdf->Cell(45	,10,'Ambulance',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsAmbulan,0,0,'R');
		/*$pdf->Ln(5);	
		
		$pdf->Cell(5,10,'16. ',0,0,'L');
		$pdf->Cell(45	,10,'Biaya Lain-Lain',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsLainLain,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'17. ',0,0,'L');
		$pdf->Cell(45	,10,'Biaya Administrasi',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsAdm,'B',0,'R');*/
		
		$pdf->Ln(10);	
		
		/*
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'TOTAL ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($totalTnpAdm,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Biaya Administrasi',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$biayaAdm,'0',0,'R');
		$pdf->Cell(3,10,'+','0',0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Biaya Metrai',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$biayaMaterai,'B',0,'R');
		$pdf->Cell(3,10,'+','B',0,'R');
				
		$pdf->Ln(10);
		*/
		
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Total Transaksi',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Uang Muka',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($uangMuka,2,',','.'),0,0,'R');
		$pdf->Ln(7);	
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Sisa Pembayaran',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlKurangBayar,2,',','.'),0,0,'R');
		
		$pdf->Ln(5);
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Discount',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$pdf->Cell(30,10,$besarDisc.' %',0,0,'R');
		}
		else
		{
			$pdf->Cell(30,10,'0 %',0,0,'R');
		}
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Ln(5);
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Jumlah Yang Masih Harus Dibayar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($totBiayaDiscBulat != '')
		{
			$pdf->Cell(30,10,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
		}
		else
		{
			$pdf->Cell(30,10,'Rp. '.number_format($jmlKurangBayar,2,',','.'),0,0,'R');
		}
		
		$pdf->Ln(7);
		
			
			//------------------------------- terbilang ----------------------------------//
		$pdf->Cell(30,5,'Terbilang : ',0,0,'L');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','BI',9);
		$pdf->MultiCell(0,5,$tagihanTerbilang,1,'L');
		$pdf->Ln(5);
		
		
			//------------------------------ TTD KASIR RINCIAN BIAYA PERAWATAN --------------------------------- //
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
		
		//------------------------------- NOTA PENAGIHAN PERAWATAN ----------------------------------//
		$pdf->AddPage();
		
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Ln(8);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'NOTA PENAGIHAN PERAWATAN','0',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
				
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$nmPasien,0,0,'L');

		$pdf->Cell(25,10,'Kelas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$kls_kmr,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Nomor CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$cm,0,0,'L');

		$pdf->Cell(25,10,'Kamar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$kode_ruang,0,0,'L');
		$pdf->Ln(5);
		
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(30,10,'Tanggal Masuk',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(75,10,$tgl_masuk,0,0,'L');
					
					$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$tglKeluarTxt,0,0,'L');
					$pdf->Ln(5);
					
					$pdf->Cell(30,10,'Tindakan',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
					
					$pdf->Cell(25,10,'Dokter',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,''.PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,0,0,'L');
				}
				elseif($i==1)
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
			
					$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$tglKeluarTxt,0,0,'L');
				}
				else
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(30,10,'Tindakan',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(75,10,'-',0,0,'L');
		
				$pdf->Cell(25,10,'Tanggal Masuk',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$tgl_masuk,0,0,'L');
				$pdf->Ln(5);
				
				$pdf->Cell(30,10,'',0,0,'L');
				$pdf->Cell(2,10,'  ',0,0,'L');
				$pdf->Cell(75,10,'',0,0,'L');
		
				$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$tglKeluarTxt,0,0,'L');
				$pdf->Ln(10);
			}
			
			$i++;
		}
		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',9);
		//$pdf->SetWidths(array(30,50,47,35,30));
		//$pdf->SetAligns(array('C','C','C','C','C'));
		$pdf->SetWidths(array(80,47,35,30));
		$pdf->SetAligns(array('C','C','C','C'));
		
		$pdf->Row(array(
						'Uraian Biaya',
						'Pelaksana',
						'Keterangan',
						'Biaya' ));
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			/*
		$jsRS=$this->Request['jsRS'];	
		$jsAhli=$this->Request['jsAhli'];	
		$jsPenunjang=$this->Request['jsPenunjang'];	
		$jsObat=$this->Request['jsObat'];	
		$jsLain=$this->Request['jsLain'];	
		$totalTnpAdm=$this->Request['totalTnpAdm'];
		$biayaAdm=$this->Request['biayaAdm'];
			
		$jmlBayar=$this->Request['jmlBayar'];
		$jmlTagihan=$this->Request['jmlTagihan'];
		*/	
		
		$pdf->Setln('5');
		
		$noUrut = 0;
		//------------------------------- Jasa Emergency ----------------------------------//
		if($this->textToNumber($jsEmergency) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Emergency',$jsEmergency));
					
			$pdf->SetFont('Arial','',9);
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_emergency WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						EmergencyRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Konsultasi ----------------------------------//
		if($this->textToNumber($jsKonsul) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Jasa Konsultasi',$jsKonsul));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_konsultasi WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						NamaTindakanRecord::finder()->findByPk($row['id_tdk'])->nama,
						PegawaiRecord::finder()->findByPk($row['dokter'])->nama,
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Visite ----------------------------------//
		if($this->textToNumber($jsVisite) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Jasa Visite',$jsVisite));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R')); 
						
			$sql = "SELECT *, SUM(jml) AS jml FROM view_penunjang WHERE cm='$cm' AND no_trans ='$notrans' AND nm_penunjang = 'visite' GROUP BY nm_penunjang, no_trans, nm_pegawai, nm_tdk ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						$row['nm_tdk'],
						$row['nm_pegawai'],
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Tenaga Ahli / Operasi Billing ----------------------------------//
		if($this->textToNumber($jsTdkOperasi) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Jasa Tindakan',$jsTdkOperasi));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
			
			$sql = "SELECT 
					*, (tarif_obgyn + jasa_koordinator + 
					 tarif_anastesi + jasa_koordinator_anastesi + 
					 tarif_anak + jasa_koordinator_ast_anastesi + 
					 tarif_assdktr + jasa_koordinator_ast_instrumen + 
					 tarif_paramedis + jasa_koordinator_paramedis + 
					 tarif_resusitasi + jasa_koordinator_resusitasi + 
					 rs + tarif_pengembang + tarif_penyulit) AS jumlah,
					 (tarif_obgyn + jasa_koordinator + rs + tarif_pengembang + tarif_penyulit) AS operator,
					 (tarif_anastesi + jasa_koordinator_anastesi) AS anastesi,
					 (tarif_assdktr + jasa_koordinator_ast_instrumen) AS ast_operator,
					 (tarif_anak + jasa_koordinator_ast_anastesi) AS ast_anastesi,
					 (tarif_paramedis + jasa_koordinator_paramedis) AS tarif_paramedis,
					 (tarif_resusitasi + jasa_koordinator_resusitasi) AS resusitasi
					 FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
					
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				if($row['operator'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Operator',
						PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,
						'',
						'Rp. '.number_format($row['operator'],2,',','.')
					));	
				}
				
				if($row['anastesi'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Dokter Anastesi',
						PegawaiRecord::finder()->findByPk($row['dktr_anastesi'])->nama,
						'',
						'Rp. '.number_format($row['anastesi'],2,',','.')
					));	
				}
				
				if($row['resusitasi'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Resusitasi Sp. Anak',
						PegawaiRecord::finder()->findByPk($row['dktr_resusitasi'])->nama,
						'',
						'Rp. '.number_format($row['resusitasi'],2,',','.')
					));	
				}
				
				if($row['ast_anastesi'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Penata Anastesi',
						PegawaiRecord::finder()->findByPk($row['dktr_anak'])->nama,
						'',
						'Rp. '.number_format($row['ast_anastesi'],2,',','.')
					));	
				}
				
				if($row['ast_operator'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Asst. Operator',
						PegawaiRecord::finder()->findByPk($row['ass_dktr'])->nama,
						'',
						'Rp. '.number_format($row['ast_operator'],2,',','.')
					));	
				}
				
				if($row['tarif_paramedis'] > 0)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'Paramedis',
						PegawaiRecord::finder()->findByPk($row['paramedis'])->nama,
						'',
						'Rp. '.number_format($row['tarif_paramedis'],2,',','.')
					));	
				}
				/*
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						$row['nm_opr'],
						PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,
						'',
						'Rp. '.number_format($row['jumlah'],2,',','.')
					));
				*/
			}
				
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Kamar Operasi ----------------------------------//
		if($this->textToNumber($jsKamarOperasi) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Akomodasi Kamar Tindakan',$jsKamarOperasi));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
			
			$sql = "SELECT 
					*, (sewa_ok) AS jumlah
					 FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
					
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$idOperasi = $row['id_opr'];
				$idKamarOperasi = OperasiNamaRecord::finder()->findByPk($row['id_opr'])->id_kamar_operasi;
				$idKategOperasi = OperasiNamaRecord::finder()->findByPk($row['id_opr'])->id_kategori_operasi;
				
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						//KamarOperasiNamaRecord::finder()->findByPk($idKamarOperasi)->nama,
						OperasiKategoriRecord::finder()->findByPk($idKategOperasi)->nama,
						'',
						'',
						'Rp. '.number_format($row['jumlah'],2,',','.')
					));
			}
				
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Sewa Alat ----------------------------------//
		if($this->textToNumber($jsSewaAlat) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Jasa Sewa Alat & BHP',$jsSewaAlat));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_sewa_alat WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			if($arr)
			{
				foreach($arr as $row)
				{
					$pdf->Row(array(
							//$this->convertDate($row['tgl'],'3'),
							SewaAlatRecord::finder()->findByPk($row['id_tdk'])->nama,
							'',
							'',
							'Rp. '.number_format($row['tarif'],2,',','.')
						));
				}
			}
			
			$sql = "SELECT * FROM tbt_inap_bhp WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			if($arr)
			{
				foreach($arr as $row)
				{
					$pdf->Row(array(
							//$this->convertDate($row['tgl'],'3'),
							BhpTindakanInapRecord::finder()->findByPk($row['id_tdk'])->nama,
							'',
							'',
							'Rp. '.number_format($row['tarif'],2,',','.')
						));
				}
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Biaya Fasilitas Rumah Sakit ----------------------------------//
		if($this->textToNumber($jsKamar) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Kamar Perawatan',$jsKamar));
			
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
			
			$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$notrans' AND st_bayar = '0'";
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
				//if($stBayi == '1')
					//$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
				//else
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
				
				$pdf->Row(array(
						//$this->convertDate($row['tgl_awal'],'3'),
						$nmKamar.' '.$nmKelas,
						'',
						$lamaInapPindah.' Hari',
						'Rp. '.number_format($jmlJsKmrIbu+$jmlJsKmrBayi,2,',','.')
					));
			}
			
			$sql = "SELECT id_kamar,nama_kamar, SUM(tarif) AS tarif, SUM(lama_inap) AS lama_inap FROM tbt_inap_kamar_bayi WHERE no_trans_inap = '$noTrans' GROUP BY no_trans_inap, id_kamar";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{				
				$tarifKamarBayi = $row['tarif'];
				$lamaInapBayi = $row['lama_inap'];
				$nmKamar = KamarNamaRecord::finder()->findByPk('2')->nama;
				
				$pdf->Row(array(
						//$this->convertDate($row['tgl_awal'],'3'),
						$row['nama_kamar'],
						'',
						$lamaInapBayi.' Hari',
						'Rp. '.number_format($tarifKamarBayi,2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Askep ----------------------------------//
		if($this->textToNumber($jsAskep) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Jasa Asuhan Keperawatan',$jsAskep));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT *, SUM(tarif) AS tarif FROM tbt_inap_askep WHERE cm='$cm' AND no_trans ='$notrans' GROUP BY  no_trans, id_tdk ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						AskepRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Pemeriksaan Penunjang Medik ----------------------------------//
		if($this->textToNumber($jsPenunjang) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Pemeriksaan Penunjang Medik',$jsPenunjang));
					
			$pdf->SetFont('Arial','',9);
			
			$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$pdf->SetFont('Arial','B',9);
				$pdf->SetWidths(array(192));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('      Laboratorium'));
				
				$pdf->SetFont('Arial','',9);
			
				//$pdf->SetWidths(array(30,50,47,35,30));
				//$pdf->SetAligns(array('C','L','L','L','R'));
				$pdf->SetWidths(array(80,47,35,30));
				$pdf->SetAligns(array('L','L','L','R'));
				
				foreach($arr as $row)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						//$row['jenis'],
						LabTdkRecord::finder()->findByPk($row['id_tindakan'])->nama,
						'',
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
				}
			}
			
			$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$pdf->SetFont('Arial','B',9);
				$pdf->SetWidths(array(192));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('      Radiologi'));
				
				$pdf->SetFont('Arial','',9);
			
				//$pdf->SetWidths(array(30,50,47,35,30));
				//$pdf->SetAligns(array('C','L','L','L','R'));
				$pdf->SetWidths(array(80,47,35,30));
				$pdf->SetAligns(array('L','L','L','R'));
				
				foreach($arr as $row)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						//$row['jenis'],
						RadTdkRecord::finder()->findByPk($row['id_tindakan'])->nama,
						'',
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
				}
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		
		//------------------------------- Jasa Sewa Alat Penunjang ----------------------------------//
		if($this->textToNumber($jsSewaAlatPenunjang) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Sewa Alat Penunjang Medik',$jsSewaAlatPenunjang));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_sewa_alat_penunjang WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						SewaAlatPenunjangRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Tindakan Kecil ----------------------------------//
		if($this->textToNumber($jsTdkKecil) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Tindakan Kecil',$jsTdkKecil));
				
			$pdf->SetFont('Arial','',9);
			
			$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 AND nm_penunjang='tindakan dokter'";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$pdf->SetFont('Arial','B',9);
				$pdf->SetWidths(array(192));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('      Tindakan Dokter'));
				
				$pdf->SetFont('Arial','',9);
			
				//$pdf->SetWidths(array(30,50,47,35,30));
				//$pdf->SetAligns(array('C','L','L','L','R'));
				$pdf->SetWidths(array(80,47,35,30));
				$pdf->SetAligns(array('L','L','L','R'));
				
				foreach($arr as $row)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						$row['nm_tdk'],
						$row['nm_pegawai'],
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
				}
			}
			
			$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2 AND nm_penunjang='tindakan paramedis'";
			$arr=$this->queryAction($sql,'S');
			
			if($arr)
			{
				$pdf->SetFont('Arial','B',9);
				$pdf->SetWidths(array(192));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('      Tindakan Paramedis'));
				
				$pdf->SetFont('Arial','',9);
			
				//$pdf->SetWidths(array(30,50,47,35,30));
				//$pdf->SetAligns(array('C','L','L','L','R'));
				$pdf->SetWidths(array(80,47,35,30));
				$pdf->SetAligns(array('L','L','L','R'));
				
				foreach($arr as $row)
				{
					$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						$row['nm_tdk'],
						$row['nm_pegawai'],
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
				}
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}
		
		//------------------------------- Jasa Tindakan Khusus ----------------------------------//
		if($this->textToNumber($jsTdkKhusus) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Tindakan Khusus',$jsTdkKhusus));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_tdk_khusus WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						TindakanKhususRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Biaya Obat-obatan & Alkes ----------------------------------//
		if($this->textToNumber($jsObat) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Obat & Alat Kesehatan',$jsObat));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
			
			$sql = "SELECT *, SUM(jml) AS jml FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=0 AND st_bayar=0 GROUP BY no_reg ORDER BY tgl";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						'No. Resep '.$row['no_reg'],
						'',
						'',
						'Rp. '.number_format($row['jml'],2,',','.')
					));
			}
			
			/*$this->biayaObatAktual($cm,$notrans,$pdf);
			
			if($this->textToNumber($bhp) > 0)
			{
				//----------- BHP Rawat Inap ---------------------
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,'BHP',1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($bhp,2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}*/
				
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}
		
		
		//------------------------------- Jasa Oksigen ----------------------------------//
		if($this->textToNumber($jsOksigen) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Oksigen',$jsOksigen));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_oksigen WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						InapTarifOksigenRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Ambulan ----------------------------------//
		if($this->textToNumber($jsAmbulan) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Ambulan',$jsAmbulan));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_ambulan WHERE cm='$cm' AND no_trans_inap ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						TarifAmbulanRecord::finder()->findByPk($row['tujuan'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Biaya Lain ----------------------------------//
		if($this->textToNumber($jsLainLain) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Biaya Lain',$jsLainLain));
					
			$pdf->SetFont('Arial','',9);
			
			$pdf->SetWidths(array(80,47,35,30));
			$pdf->SetAligns(array('L','L','L','R'));
						
			$sql = "SELECT * FROM tbt_inap_biaya_lain WHERE cm='$cm' AND no_trans ='$notrans' ORDER BY id ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->Row(array(
						//$this->convertDate($row['tgl'],'3'),
						BiayaLainRecord::finder()->findByPk($row['id_tdk'])->nama,
						'',
						'',
						'Rp. '.number_format($row['tarif'],2,',','.')
					));
			}
			
			if($this->textToNumber($biayaMaterai) > 0)
			{
				$pdf->Row(array(
						//$this->convertDate(date('Y-m-d'),'3'),
						'Biaya Materai',
						'',
						'',
						$biayaMaterai
					));
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
				
				$pdf->Row(array(
					//$this->convertDate($row['tgl'],'3'),
					'Biaya selama rawat jalan',
					'',
					'',
					'Rp. '.number_format($jml,2,',','.')
				));
			}	
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		//------------------------------- Jasa Administrasi ----------------------------------//
		if($this->textToNumber($jsAdm) > 0)
		{	
			$pdf->SetFont('Arial','B',9);
			$pdf->SetWidths(array(162,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array($this->numberToRoman($noUrut+1).'. Administrasi Rumah Sakit',$jsAdm));
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}	
		
		/*
		//------------------------------- Biaya Lain-Lain fisio yg st_bayar=0 ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		
		$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'fisio' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totFisio += $row['jml'];
		}
		
		if($totFisio > 0)
		{			
			$pdf->Cell(162,5,$this->numberToRoman($noUrut+1).'. Biaya Fisio',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($totFisio,2,',','.'),1,0,'R');
			$pdf->Ln(5);
				
			$pdf->SetFont('Arial','',9);
			
			$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'fisio' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['jenis'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);	
			$noUrut ++;
		}
		
		//------------------------------- Biaya Lain-Lain ----------------------------------//
		$pdf->SetFont('Arial','B',9);
			
			//-------------- Tarif Askep -------------- //
		if($lamaInap > 0)				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askep
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskep += $row['tarif'];
			}
			
			$totAskep = $lamaInap * $totAskep;
		}
		
			//-------------- Tarif Askep OK -------------- //
		if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askep_ok			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskepOK += $row['tarif'];
			}
		}
		
			//-------------- Tarif Askeb -------------- //
		if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askeb			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskeb += $row['tarif'];
			}
		}
		
			//-------------- Tarif Oksigen -------------- //
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
				$totOksigen += $row['jumlah'];
			}
		}
		
		//-------------- Tarif Ambulan -------------- //
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
				$totAmbulan += $row['jumlah'];
			}
		}
		
		//-------------- Tarif Sinar -------------- //
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
				$totSinar += $row['jumlah'];
			}
		}
		
		//$totLain = $totAskep + $totAskepOK + $totAskeb + $totOksigen + $totAmbulan + $totSinar + $bhp;
		$totLain = $totAskep + $totAskepOK + $totAskeb + $totAmbulan + $totSinar ;
		
		if($totLain > 0)
		{			
			$pdf->Cell(162,5,$this->numberToRoman($noUrut+1).'. Biaya Lain-Lain',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($totLain,2,',','.'),1,0,'R');
			$pdf->Ln(5);
			
			//------------------------------- Biaya Askep ----------------------------------//
			if($lamaInap > 0)				
			{
				$sql = "SELECT nama,tgl,tarif 
							   FROM tbt_inap_askep
							   WHERE no_trans = '$notrans'
							   AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
			}
			
			//------------------------------- Biaya Askep OK jika ada ----------------------------------//
			if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
			{
				$sql = "SELECT nama,tgl,tarif 
							   FROM tbt_inap_askep_ok			
							   WHERE no_trans = '$notrans'
							   AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
			}
			
			//------------------------------- Biaya Askeb jika ada ----------------------------------//
			if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
			{
				$sql = "SELECT nama,tgl,tarif 
							   FROM tbt_inap_askeb			
							   WHERE no_trans = '$notrans'
							   AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
			}
			
			//----------- Biaya Ambulan jika ada ---------------------
			if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
			{
				$sql = "SELECT tgl, tarif AS jumlah, tujuan
							   FROM tbt_inap_ambulan			
							   WHERE 
								no_trans = '$noTrans'
								AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,'Ambulance',1,0,'L');
					$pdf->Cell(50,5,$row['tujuan'],1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
			}
			
			
			//----------- Biaya SInar jika ada ---------------------
			if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
			{
				$sql = "SELECT tgl, tarif AS jumlah
							   FROM tbt_inap_sinar
							   WHERE 
								no_trans = '$noTrans'
								AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,'Blue Light',1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
			}
			
			
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut ++;
		}
		
		//------------------------------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		
		if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
		{
			//-------------------- Biaya Alih Tindakan Rawat jalan ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Tindakan Rawat Jalan'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totRwtJln += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Obat Alkes ---------------------	
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Obat Alkes'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAlkes += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Radiologi ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Rad'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totRadAlih += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Laboratorium ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Lab'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totLabAlih += $row['jumlah'];
			}
			
			$totBiayaAlih = $totRwtJln + $totAlkes + $totRadAlih + $totLabAlih;
		}
		
		
		if($totBiayaAlih > 0)
		{		
			$pdf->Cell(162,5,$this->numberToRoman($noUrut+1).'. Biaya Selama Rawat Jalan',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($totBiayaAlih,2,',','.'),1,0,'R');
			$pdf->Ln(5);
			
			if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
			{
				/*
				$sql = "SELECT 
							SUM(jml) AS jumlah
						FROM 
							view_biaya_alih 
						WHERE  
							no_trans_inap='$notrans'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,'-',1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,'Biaya selama rawat jalan',1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				*//*
				//-------------------- Biaya Alih Tindakan Rawat jalan ---------------------		
				$sql = "SELECT 
							jml AS jumlah, jns_trans, nama, no_trans_jln
						FROM 
							view_biaya_alih 
						WHERE  
							no_trans_inap='$notrans'
							AND jns_trans = 'Tindakan Rawat Jalan'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					//$idDokter = KasirRwtJlnRecord::finder()->find('no_trans_rwtjln',$row['no_trans_jln'])->dokter;
					$noTransJln = $row['no_trans_jln'];
					$sql = "SELECT dokter FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJln' AND st_flag = '0'";
					$idDokter = KasirRwtJlnRecord::finder()->findBySql($sql)->dokter;
					$nmDoker = PegawaiRecord::finder()->findByPk($idDokter)->nama;
					//$idTdk = KasirRwtjlnRecord::finder()->find('no_trans_rwtjln',$row['no_trans_jln'])->id_tindakan;
					
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,'-',1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					
					if(substr($row['nama'],0,11) == 'Jasa Dokter')
					{
						$pdf->Cell(50,5,$nmDoker,1,0,'L');
					}
					else
					{
						$pdf->Cell(50,5,'',1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				//-------------------- Biaya Alih Obat Alkes ---------------------		
				$sql = "SELECT 
							jml AS jumlah, jns_trans, nama
						FROM 
							view_biaya_alih 
						WHERE  
							no_trans_inap='$notrans'
							AND jns_trans = 'Obat Alkes'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,'-',1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				//-------------------- Biaya Alih Radiologi ---------------------		
				$sql = "SELECT 
							jml AS jumlah, jns_trans, nama
						FROM 
							view_biaya_alih 
						WHERE  
							no_trans_inap='$notrans'
							AND jns_trans = 'Rad'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,'-',1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				//-------------------- Biaya Alih Laboratorium ---------------------		
				$sql = "SELECT 
							jml AS jumlah, jns_trans, nama
						FROM 
							view_biaya_alih 
						WHERE  
							no_trans_inap='$notrans'
							AND jns_trans = 'Lab'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,'-',1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nama'],1,0,'L');
					$pdf->Cell(50,5,'',1,0,'L');
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
	
			}
			
			/*
			$pdf->Cell(25,5,'',1,0,'C');
			$pdf->Cell(45,5,'Askep',1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,$lamaInap.' Hari',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($askep,2,',','.'),1,0,'R');
			$pdf->Ln(5);
			*/	/*
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut++;
		}
				
		//------------------------------- JPM ----------------------------------//
		/*
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(162,5,'X. Jasa Pelayanan Medis',1,0,'L');
		$pdf->Cell(30,5,$jpm,1,0,'R');
		$pdf->Ln(5);
		*//*
		
		//------------------------------- Biaya Administrasi ----------------------------------//
		if($this->textToNumber($biayaAdm) > 0)
		{
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(162,5,$this->numberToRoman($noUrut+1).'. Biaya Administrasi',1,0,'L');
			$pdf->Cell(30,5,$biayaAdm,1,0,'R');
			$pdf->Ln(5);
			$noUrut++;
		}
				
		//------------------------------- Biaya Metrai ----------------------------------//
		if($this->textToNumber($biayaMaterai) > 0)
		{
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(162,5,$this->numberToRoman($noUrut+1).'. Biaya Materai',1,0,'L');
			$pdf->Cell(30,5,$biayaMaterai,1,0,'R');
			/*
			$pdf->Ln(5);
					
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(25,5,'',1,0,'L');
			$pdf->Cell(45,5,'',1,0,'L');
			$pdf->Cell(40,5,'',1,0,'L');
			$pdf->Cell(40,5,'',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
			*//*
			$pdf->Ln(5);
			$pdf->Cell(0,1,'',0,0,'C');
			$pdf->Ln(1);
			$noUrut++;
		}
		*/
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'Total Transaksi',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($jmlTagihan,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'Uang Muka',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($uangMuka,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'Sisa Pembayaran',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($jmlKurangBayar,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		
		
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'Discount',1,0,'L');
		
		if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
		{
			$pdf->Cell(30,5,$besarDisc.' %',1,0,'R');
		}
		else
		{
			$pdf->Cell(30,5,'0 %',1,0,'R');
		}
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->Ln(5);
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'TOTAL',1,0,'L');
		
		if($totBiayaDiscBulat != '')
		{
			$pdf->Cell(30,5,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),1,0,'R');
		}
		else
		{
			$pdf->Cell(30,5,'Rp. '.number_format($jmlKurangBayar,2,',','.'),1,0,'R');
		}		
		
		
		
		$pdf->Ln(10);
		
			//------------------------------- terbilang ----------------------------------//
		$pdf->Cell(30,5,'Terbilang : ',0,0,'L');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','BI',9);
		$pdf->MultiCell(0,5,$tagihanTerbilang,1,'L');
		
		
		$pdf->Ln(10);	
		
			//------------------------------ TTD KASIR NOTA PENAGIHAN PERAWATAN --------------------------------- //
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInapDiscount') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
			
		$pdf->Output();
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
	public function biayaPenunjangAktual($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 ";
		$arr=$this->queryAction($sql,'S');
		if(count($arr) > 0)
		{
			$pdf->Cell(192,5,'Dokter',1,0,'C');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',8);		
			//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=1";
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2";
		$arr=$this->queryAction($sql,'S');
		if(count($arr) > 0)
		{
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(192,5,'Paramedis',1,0,'C');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',8);
			//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=2";
			
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}	
	} 
	
	public function biayaPenunjangPaket($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 ";
		$arr=$this->queryAction($sql,'S');
		if(count($arr) > 0)
		{
			$pdf->Cell(192,5,'Dokter',1,0,'C');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',8);		
			//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=1";
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				//$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Cell(30,5,'-',1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2";
		$arr=$this->queryAction($sql,'S');
		if(count($arr) > 0)
		{
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(192,5,'Paramedis',1,0,'C');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',8);
			//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=2";
			
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				//$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Cell(30,5,'-',1,0,'R');
				$pdf->Ln(5);
			}
		}	
	} 
	
	
	public function biayaObatAktual($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=0 AND st_bayar=0 ORDER BY tgl";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['nm_obat_alkes'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,$row['jml_obt_alkes'].' '.$row['satuan'],1,0,'C');
			$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Ln(5);
		}
		
		//----------- Biaya Oksigen jika ada ---------------------
		if(InapOksigenRecord::finder()->find('no_trans=?',$notrans))				
		{
			$sql = "SELECT tgl, tarif AS jumlah
						   FROM tbt_inap_oksigen			
						   WHERE 
							no_trans = '$notrans'
							AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,'Oksigen',1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
	}
	
	public function biayaObatPaket($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=0 AND st_bayar=0 ORDER BY tgl";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['nm_obat_alkes'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,$row['jml_obt_alkes'].' '.$row['satuan'],1,0,'C');
			//$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Cell(30,5,'-',1,0,'R');
			$pdf->Ln(5);
		}
	}
	
	public function biayaLabAktual($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['jenis'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,'',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Ln(5);
		}
	}
	
	public function biayaLabPaket($cm,$notrans,$pdf)
	{
		$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans_asal='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['jenis'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,'',1,0,'L');
			//$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Cell(30,5,'-',1,0,'R');
			$pdf->Ln(5);
		}
	}
	
}
?>
