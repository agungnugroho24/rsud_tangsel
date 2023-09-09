<?php
class cetakKwtRwtInapPreviewDiscountRtf extends SimakConf
{
	public function onLoad($param)
	{
		parent::onLoad($param);
	
		$cm=$this->Request['cm'];
		$notrans=$this->Request['notrans'];
		
		$tglKeluarTxt=$this->convertDate($this->Request['tglKeluar'],'3');
		$wktKeluar=$this->Request['wktKeluar'];
		$lamaInap=$this->Request['lamaInap'];
		
		$nmPembayar=$this->Request['nmPembayar'];				
		$jsRS=$this->Request['jsRS'];	
		$jsAhli=$this->Request['jsAhli'];	
		$jpm=$this->Request['jpm'];	
		$jsPenunjang=$this->Request['jsPenunjang'];	
		$jsObat=$this->Request['jsObat'];	
		$jsLain=$this->Request['jsLain'];	
		$totalTnpAdm=$this->Request['totalTnpAdm'];
		$biayaAdm=$this->Request['biayaAdm'];
		$biayaMetrai=$this->Request['biayaMetrai'];
		$askep=$this->Request['askep'];
		$bhp=$this->Request['bhp'];
		
		$jmlSisaPlafon = $this->Request['jmlSisaPlafon'];
			
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
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
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
		
		$tglMasuk = RwtInapRecord::finder()->findByPk($notrans)->tgl_masuk;
		$tglKeluar = date('Y-m-d');
		
		$wktMasuk = RwtInapRecord::finder()->findByPk($notrans)->wkt_masuk;
		$wktKeluar = date('G:i:s');
		$lamaInapAktual = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
		
		if($notrans!='')
		{
			//----------- CEK APAKAH PASIEN AMBIL PAKET ATAU TIDAK ke tbt_inap_operasi_billing ---------------------
		$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$notrans' AND st='0'";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) > 0 )//jika pasien ambil paket
		{
			foreach($arr as $row)
			{
				$stPaket = '1';
				$idPaket=$row['id_opr'];
				//$lamaHariPaket=OperasiTarifRecord::finder()->find('id_operasi=? AND id_kelas=?',$idPaket,$kelas)->lama_hari;
				$lamaHariPaket = 0;
			}		
		}
		else
		{
			$stPaket = '0';
		}
		
		//mode Ambil paket
		if($stPaket == '1')
		{
			$sql = "SELECT *
					   FROM view_inap_operasi_billing				
					   WHERE cm='$cm'
					   AND no_trans = '$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$tgl_operasi = $row['tgl'];
				$dktr_obgyn = $row['dktr_obgyn'];
				$dktr_anak = $row['dktr_anak'];
				$tarif_obgyn = $row['tarif_obgyn'];
				$tarif_anastesi = $row['tarif_anastesi'];
				$tarif_anak = $row['tarif_anak'];
				$tarif_assdktr = $row['tarif_assdktr'];
				
				$tarif_resusitasi = $row['tarif_resusitasi'];
						$tarif_paramedis = $row['tarif_paramedis'];
						$tarif_pengembang = $row['tarif_pengembang'];
						$tarif_penyulit = $row['tarif_penyulit'];
						
				$visite_dokter_obgyn = $row['visite_dokter_obgyn'];
				$visite_dokter_anak = $row['visite_dokter_anak'];
				$sewa_ok = $row['sewa_ok'];
				$rs = $row['rs'];
				$jasaKoordinator = $row['jasa_koordinator'];
				$obat = $row['obat'];
				$ctg = $row['ctg'];
				//$jpm = $row['jpm'];
				$lab = $row['lab'];
				$ambulan = $row['ambulan'];
				$kamar_ibu = $row['kamar_ibu'];
				$kamar_bayi = $row['kamar_bayi'];
				$js_bidan_pengirim = $row['js_bidan_pengirim'];
				$adm = $row['adm'];
				$materai = $row['materai'];
			}
		/*	
			//bandingkan lama_hari_paket dengan lama hari aktual
			if($lamaInapAktual <= $lamaHariPaket) //ambil harga paket
			{
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
					
					$tarifKamarIbu = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('1', $kelas))->tarif;
					$tarifKamarBayi = KamarHargaRecord::finder()->find('id_kmr = ? AND id_kls = ?',array('4', $kelas))->tarif;
											
					if($jmlDataInapKmr === $counter) //row pada status kelas terakhir
					{
						$tglKeluar = date('Y-m-d');
						$wktKeluar = date('G:i:s');
						$lamaInapPindah = $this->hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar);
					}
					
					$jmlJsKmrIbu += $tarifKamarIbu * $lamaInapPindah; 
					$jmlJsKmrBayi += $tarifKamarBayi * ($lamaInapPindah - 1); 						
					
					
					//$tarifJpmIbu = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('1', $kelas))->tarif;
					//$tarifJpmBayi = JpmHargaRecord::finder()->find('id_person = ? AND id_kls = ?',array('2', $kelas))->tarif;
					
					//$jmlJpmIbu += $tarifJpmIbu * $lamaInap; 
					//$jmlJpmBayi += $tarifJpmBayi * ($lamaInap - 1);
					
					$counter++;
				}
				
				$jmlJsKmr = $jmlJsKmrIbu + $jmlJsKmrBayi; 
				//$jpm = $jmlJpmIbu + $jmlJpmBayi; 
			}
			else //ambil harga aktual
			{
		*/
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
			//}
		}
		else //mode tidak Ambil paket
		{
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
		}
		
		
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
			
			
			$rtf = new reportNaskahRtf();
			
			//--------------------------KWITANSI-------------------------------
			$sect = &$rtf->addSection();
			$sect->setPaperWidth(21.5);
			$sect->setPaperHeight(33);
			$sect->setMargins(1.27, 1, 1.27, 1.27);
			
			$parHead = new ParFormat('center');
			$parHead->setSpaceBefore(1);
			$parHead->setSpaceAfter(1);
			
			$parLeft = new ParFormat('left');
			$parLeft->setSpaceBefore(1);
			$parLeft->setSpaceAfter(1);
			
						
			$table = &$sect->addTable();
			$table->addRows(1);		
			$table->addColumnsList(array(2, 17));
			
			$cell = &$table->getCell(1, 1);	
			$cell->addImage('protected/pages/Tarif/logo1.jpg', $null,2);
			
			$table->writeToCell(1, 2, '', new Font(5,'Arial'), $parHead );
			$table->writeToCell(1, 2, '<b>PEMERINTAH KOTA TANGERANG SELATAN</b>', new Font(10,'Arial'), $parHead );
			

			$table->writeToCell(1, 2, 'JL. Pajajaran No. 101 Pamulang', new Font(10,'Arial'), $parHead );
$table->writeToCell(1, 2, 'Telp. (021) 74718440', new Font(10,'Arial'), $parHead );
			$table->setBordersOfCells(new BorderFormat(2, '#000000'), 1, 2, 1, 1, 0, 0, 0, 1);
			
			$sect->writeText('<b><u>KUITANSI</u></b>', new Font(11,'Arial'), new ParFormat('center'));	
			$sect->writeText('No. Kuitansi: '.$noKwitansi, new Font(11,'Arial'), new ParFormat('center'));					
			
			$parJ = new ParFormat('justify');
			$parJ->setSpaceBetweenLines(1.5);
			
			$parL = new ParFormat('left');
			$parL->setSpaceBetweenLines(1.5);
			
			$parC = new ParFormat('center');
			$parC->setSpaceBetweenLines(1.5);
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(2);		
			$table->addColumnsList(array(3.5,0.5,15));
			
			$baris=1; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Telah Terima dari', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			//$table->writeToCell($baris, $kolom, $nmPasien.' / '.$pjPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $nmPembayar, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Uang Sebesar', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $tagihanTerbilang, new Font(10,'Arial'), $parJ ); $kolom++;
			
			
			$table = &$sect->addTable();			
			$table->addRows(3);		
			$table->addColumnsList(array(3.5,0.5,5,0.5,9.5));
			
			$baris=2; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Untuk Pembayaran', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '- Biaya Perawatan Pasien', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $nmPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '- Nomor Catatan Medik', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $cm, new Font(10,'Arial'), $parJ ); $kolom++;
			
			$i=0;
			$sqlOperasiBil = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans' ";
			$arrOperasiBil=$this->queryAction($sqlOperasiBil,'S');
			
			$table = &$sect->addTable();			
			$table->addRows(count($arrOperasiBil));		
			$table->addColumnsList(array(3.5,0.5,5,0.5,9.5));
			$baris=1; $kolom=1;
			
			foreach($arrOperasiBil AS $row )
			{
				if($row['nm_opr']!='')
				{
					if($i==0)
					{
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '- Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						
					}
					else
					{
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						
					}
					$baris++; $kolom=1;
				}
				else
				{
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, '- Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, '- ', new Font(10,'Arial'), $parJ ); $kolom++;
					$baris++; $kolom=1;
				}
				
				$i++;
			}
			
			
			$table = &$sect->addTable();			
			$table->addRows(7);		
			$table->addColumnsList(array(3.5,0.5,5,0.5,9.5));
			
			$baris=2; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Dirawat Tanggal', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $tgl_masuk.'  s.d.  '.$tglKeluarTxt, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Total Transaksi', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlTagihan,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Uang Muka', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($uangMuka,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Sisa Pembayaran', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlKurangBayar,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Discount', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			
			if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
			{
				$table->writeToCell($baris, $kolom, $besarDisc.' %', new Font(10,'Arial'), $parJ ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, '0 %', new Font(10,'Arial'), $parJ ); $kolom++;
			}
			
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Total Setelah Discount', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			if($totBiayaDiscBulat != '')
			{
				$table->writeToCell($baris, $kolom, 'Rp. '.number_format($totBiayaDiscBulat,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlKurangBayar,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			}		
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();
			$table->addRows(5);
			$table->addColumnsList(array(6,4, 9));
			
			$baris=1; $kolom=3;
			$table->writeToCell($baris, $kolom, 'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'), new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'K a s i r', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'a', new Font(20,'Arial','#ffffff'), $parC);
			$baris++; 
			$table->writeToCell($baris, $kolom, '('.$operator.')', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'NIP. '.$nip, new Font(10,'Arial'), $parC );
			
			//--------------------------RINCIAN BIAYA PERAWATAN-------------------------------
			$sect = &$rtf->addSection();
			$sect->setPaperWidth(21.5);
			$sect->setPaperHeight(33);
			$sect->setMargins(1.27, 1, 1.27, 1.27);
			
			$parHead = new ParFormat('center');
			$parHead->setSpaceBefore(1);
			$parHead->setSpaceAfter(1);
			
			$parLeft = new ParFormat('left');
			$parLeft->setSpaceBefore(1);
			$parLeft->setSpaceAfter(1);
			
						
			$table = &$sect->addTable();
			$table->addRows(1);		
			$table->addColumnsList(array(2, 17));
			
			$cell = &$table->getCell(1, 1);	
			$cell->addImage('protected/pages/Tarif/logo1.jpg', $null,2);
			
			$table->writeToCell(1, 2, '', new Font(5,'Arial'), $parHead );
			$table->writeToCell(1, 2, '<b>PEMERINTAH KOTA TANGERANG SELATAN</b>', new Font(10,'Arial'), $parHead );
			

			$table->writeToCell(1, 2, 'JL. Pajajaran No. 101 Pamulang', new Font(10,'Arial'), $parHead );
$table->writeToCell(1, 2, 'Telp. (021) 74718440', new Font(10,'Arial'), $parHead );
			$table->setBordersOfCells(new BorderFormat(2, '#000000'), 1, 2, 1, 1, 0, 0, 0, 1);
			
			$sect->writeText('<b><u>NOTA PENAGIHAN PERAWATAN</u></b>', new Font(11,'Arial'), new ParFormat('center'));
			
			$parJ = new ParFormat('justify');
			$parJ->setSpaceBetweenLines(1.5);
			
			$parL = new ParFormat('left');
			$parL->setSpaceBetweenLines(1.5);
			
			$parC = new ParFormat('center');
			$parC->setSpaceBetweenLines(1.5);
			
			$parR = new ParFormat('right');
			$parR->setSpaceBetweenLines(1.5);
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(5);		
			$table->addColumnsList(array(3.5,0.5,15));
			
			$baris=1; $kolom=1;
			$table->writeToCell($baris, $kolom, 'No. Nota Penagihan', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parJ ); $kolom++;
			/*
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Rekening Untuk', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $pjPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Alamat Penanggung', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $AlmtPjPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			*/
			$baris++; $baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Nama Pasien', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, $nmPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			
			
			$table = &$sect->addTable();			
			$table->addRows(count($arrOperasiBil));		
			$table->addColumnsList(array(3.5,0.5,3.5,2.5,3,2.5,0.5,3));
			
			$baris=1; $kolom=1;
			
			$i=0;
			foreach($arrOperasiBil AS $row )
			{
				if($row['nm_opr']!='')
				{
					if($i==0)
					{
						$table->writeToCell($baris, $kolom, 'Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
						$table->writeToCell($baris, $kolom, ' ', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, 'No. Kamar', new Font(10,'Arial'), $parR ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $nm_kmr, new Font(10,'Arial'), $parJ ); $kolom++;
					}
					else
					{
						$pdf->Cell(5,10,' ',0,0,'L');
						$pdf->Cell(25,10,'',0,0,'L');
						$pdf->Cell(2,10,'  ',0,0,'L');
						$pdf->Cell(85,10,'- '.$row['nm_opr'],0,0,'L');
						
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
						$table->writeToCell($baris, $kolom, ' ', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
					}
					$baris++; $kolom=1;
				}
				else
				{
					$table->writeToCell($baris, $kolom, 'Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, '- ', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
					$table->writeToCell($baris, $kolom, ' ', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, 'No. Kamar', new Font(10,'Arial'), $parR ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, $nm_kmr, new Font(10,'Arial'), $parJ ); $kolom++;
					$baris++; $kolom=1;
				}
				
				$i++;
			}
			
			$table = &$sect->addTable();			
			$table->addRows(3);		
			$table->addColumnsList(array(3.5,0.5,3.5,2.5,3,2.5,0.5,3));
			
			$baris=1; $kolom=1;
			
			$table->writeToCell($baris, $kolom, 'Tanggal Masuk', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $tgl_masuk, new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '-  Jam : ', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ' '.$wkt_masuk.' WIB', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'No. CM', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $cm, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Tanggal Keluar', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $tglKeluarTxt, new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '-  Jam : ', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ' '.$wktKeluar.' WIB', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Kelas', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $kls_kmr, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Jumlah Hari', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $lamaInap.' Hari', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ' ', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Tarif', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlJsKmr,2,',','.'), new Font(10,'Arial'), $parJ ); $kolom++;
			
			
			$sect->writeText('', new Font(11,'Arial'),$parJ);
			$sect->writeText('<b>Uraian Biaya Perawatan :</b>', new Font(11,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(14);		
			$table->addColumnsList(array(0.5,5,0.5,6,0.5,4,2.5));
			
			$baris=1; $kolom=1;
			
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Jasa Fasilitas Rumah Sakit', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, $jsRS, new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Jasa Tenaga Ahli', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, $jsAhli, new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'JPM', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, $jpm, new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Jasa Pemeriksaan Penunjang', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, $jsPenunjang, new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Obat-Obatan & Alat Kesehatan', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, $jsObat, new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;			
			$table->writeToCell($baris, $kolom, $baris.'.', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Biaya Lain-Lain', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parR ); $kolom++;
			
			$table->setBordersOfCells(new BorderFormat(1, '#000000'), $baris, $kolom, $baris, $kolom, 0, 0, 0, 1);
			$table->writeToCell($baris, $kolom, $jsLain, new Font(10,'Arial'), $parR ); $kolom++;		
			$table->writeToCell($baris, $kolom, ' +', new Font(10,'Arial'), $parL ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'TOTAL', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($totalTnpAdm,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Biaya Administrasi', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			$table->writeToCell($baris, $kolom, $biayaAdm, new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ' +', new Font(10,'Arial'), $parL ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Biaya Metrai', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			
			$table->setBordersOfCells(new BorderFormat(1, '#000000'), $baris, $kolom, $baris, $kolom, 0, 0, 0, 1);
			$table->writeToCell($baris, $kolom, $biayaMetrai, new Font(10,'Arial'), $parR ); $kolom++;
			$table->writeToCell($baris, $kolom, ' +', new Font(10,'Arial'), $parL ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Total Transaksi', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlTagihan,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Uang Muka', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($uangMuka,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Sisa Pembayaran', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlKurangBayar,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Discount', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
			{
				$table->writeToCell($baris, $kolom, $besarDisc.' %', new Font(10,'Arial'), $parR ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, '0 %', new Font(10,'Arial'), $parR ); $kolom++;
			}
			
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>Jumlah Yang Masih Harus Dibayar</b>', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parS ); $kolom++;
			if($totBiayaDiscBulat != '')
			{
				$table->writeToCell($baris, $kolom, '<b>Rp. '.number_format($totBiayaDiscBulat,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, '<b>Rp. '.number_format($jmlKurangBayar,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
			}
			
			
			$sect->writeText('', new Font(11,'Arial'),$parJ);
			$sect->writeText('<b>Terbilang :</b>', new Font(11,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(1);		
			$table->addColumnsList(array(19));
			
			$baris=1; $kolom=1;
			$table->setBordersOfCells(new BorderFormat(1, '#000000'), $baris, $kolom, $baris, $kolom, 1, 1, 1, 1);
			$table->writeToCell($baris, $kolom, '<b>'.$tagihanTerbilang.'</b>', new Font(11,'Arial'), $parJ ); $kolom++;
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();
			$table->addRows(5);
			$table->addColumnsList(array(6,4, 9));
			
			$baris=1; $kolom=3;
			$table->writeToCell($baris, $kolom, 'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'), new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'K a s i r', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'a', new Font(20,'Arial','#ffffff'), $parC);
			$baris++; 
			$table->writeToCell($baris, $kolom, '('.$operator.')', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'NIP. '.$nip, new Font(10,'Arial'), $parC );
			
			
			
			//--------------------------NOTA PENAGIHAN PERAWATAN-------------------------------
			$sect = &$rtf->addSection();
			$sect->setPaperWidth(21.5);
			$sect->setPaperHeight(33);
			$sect->setMargins(1.27, 1, 1.27, 1.27);
			
			$parHead = new ParFormat('center');
			$parHead->setSpaceBefore(1);
			$parHead->setSpaceAfter(1);
			
			$parLeft = new ParFormat('left');
			$parLeft->setSpaceBefore(1);
			$parLeft->setSpaceAfter(1);
			
						
			$table = &$sect->addTable();
			$table->addRows(1);		
			$table->addColumnsList(array(2, 17));
			
			$cell = &$table->getCell(1, 1);	
			$cell->addImage('protected/pages/Tarif/logo1.jpg', $null,2);
			
			$table->writeToCell(1, 2, '', new Font(5,'Arial'), $parHead );
			$table->writeToCell(1, 2, '<b>PEMERINTAH KOTA TANGERANG SELATAN</b>', new Font(10,'Arial'), $parHead );
			

			$table->writeToCell(1, 2, 'JL. Pajajaran No. 101 Pamulang', new Font(10,'Arial'), $parHead );
$table->writeToCell(1, 2, 'Telp. (021) 74718440', new Font(10,'Arial'), $parHead );
			$table->setBordersOfCells(new BorderFormat(2, '#000000'), 1, 2, 1, 1, 0, 0, 0, 1);
			
			$sect->writeText('<b><u>NOTA PENAGIHAN PERAWATAN</u></b>', new Font(11,'Arial'), new ParFormat('center'));
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(2);		
			$table->addColumnsList(array(3,0.5,8,2,0.5,5));
			
			$baris=1; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Nama Pasien', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $nmPasien, new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Kelas', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $kls_kmr, new Font(10,'Arial'), $parJ ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, 'Nomor CM', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $cm, new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Kamar', new Font(10,'Arial'), $parJ ); $kolom++;
			$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, $kode_ruang, new Font(10,'Arial'), $parJ ); $kolom++;
			
			
			$table = &$sect->addTable();			
			$table->addRows(count($arrOperasiBil));		
			$table->addColumnsList(array(3,0.5,8,2,0.5,5));
			
			$i=0;
			foreach($arrOperasiBil AS $row )
			{
				if($row['nm_opr']!='')
				{
					if($i==0)
					{
						$table->writeToCell($baris, $kolom, 'Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Tanggal Masuk', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $tgl_masuk, new Font(10,'Arial'), $parJ ); $kolom++;
			
					}
					elseif($i==1)
					{
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Tanggal Keluar', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $tglKeluarTxt, new Font(10,'Arial'), $parJ ); $kolom++;
					}
					else
					{
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, '- '.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
					}
					$baris++; $kolom=1;
				}
				else
				{
					$table->writeToCell($baris, $kolom, 'Tindakan', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, 'Tanggal Masuk', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, $tgl_masuk, new Font(10,'Arial'), $parJ ); $kolom++;
					
					$baris++; $kolom=1;
					
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, ''.$row['nm_opr'], new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, 'Tanggal Keluar', new Font(10,'Arial'), $parJ ); $kolom++;
					$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, $tglKeluarTxt, new Font(10,'Arial'), $parJ ); $kolom++;
					$baris++; $kolom=1;
				}
				
				$i++;
			}
			
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(2);		
			$table->addColumnsList(array(3,4.5,5,3,3.5));
			
			$baris=1; $kolom=1;
			$table->writeToCell($baris, $kolom, '<b>Tanggal</b>', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>Uraian Biaya</b>', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>Pelaksana</b>', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>Keterangan</b>', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>Biaya</b>', new Font(10,'Arial'), $parC ); $kolom++;
			$baris++; $kolom=1;
			
			$noUrut = 0;
			if($this->textToNumber($jsRS) > 0)
			{	
				$table->mergeCells($baris, $kolom, $baris, 4);
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Fasilitas Rumah Sakit</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom+3, '<b>'.$jsRS.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				//------------------------------- Biaya Fasilitas Rumah Sakit ----------------------------------//
				$sql = "SELECT * FROM tbt_inap_kamar WHERE no_trans_inap = '$notrans' AND st_bayar='0'";
				$arr = $this->queryAction($sql,'S');
				$jmlDataInapKmr = count($arr);
				$counter = 1;
				
				$table = &$sect->addTable();			
				$table->addRows(count($arr));		
				$table->addColumnsList(array(3,4.5,5,3,3.5));
				$baris=1; $kolom=1;
				
				foreach($arr as $row)
				{
					$idJnsKamar = RwtInapRecord::finder()->findByPk($notrans)->jenis_kamar;
					$nmKamar = KamarNamaRecord::finder()->findByPk($idJnsKamar)->nama;
					
					$kelas = $row['id_kmr_awal'];
					$nmKelas = KamarKelasRecord::finder()->findByPk($kelas)->nama;
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
					
					$jmlJsKmrIbu = $tarifKamarIbu * $lamaInapPindah; 
					$jmlJsKmrBayi = $tarifKamarBayi * ($lamaInapPindah - 1); 	
					$counter++;
					
					$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl_awal'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, $nmKamar.' '.$nmKelas, new Font(10,'Arial'), $parL ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
					$table->writeToCell($baris, $kolom, $lamaInapPindah.' Hari', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlJsKmrIbu+$jmlJsKmrBayi,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
					$baris++; $kolom=1;
				}
				
				$noUrut++;
			}
			
			//------------------------------- Biaya Tenaga Ahli ----------------------------------//
			if($this->textToNumber($jsAhli) > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Tenaga Ahli</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$jsAhli.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				
				$sql = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
					$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{						
					foreach($arr as $row)
					{
						if($row['dktr_obgyn']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
					
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							if($jmlSisaPlafon!=0 || $jmlSisaPlafon!='')					
							{
								$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_obgyn'] + $row['jasa_koordinator'] + $jmlSisaPlafon,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							}
							else
							{
								$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_obgyn'] + $row['jasa_koordinator'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							}
							
							$baris++; $kolom=1;
						}
						
						if($row['dktr_anastesi']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
							
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['dktr_anastesi'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_anastesi'] + $row['jasa_koordinator_anastesi'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
						
						if($row['dktr_anak']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
							
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['dktr_anak'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_anak'] + $row['jasa_koordinator_ast_anastesi'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
						
						if($row['ass_dktr']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
							
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['ass_dktr'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_assdktr'] + $row['jasa_koordinator_ast_instrumen'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
						
						if($row['paramedis']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
							
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['paramedis'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_paramedis'] + $row['jasa_koordinator_paramedis'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
						
						if($row['dktr_resusitasi']!==NULL)
						{
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
							
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, PegawaiRecord::finder()->findByPk($row['dktr_resusitasi'])->nama, new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif_resusitasi'] + $row['jasa_koordinator_resusitasi'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
						
						
						if($row['sewa_ok']!==NULL)
						{
							$sewaOk = $row['sewa_ok']+$row['ctg']+$row['rs']+$row['tarif_pengembang'];
							
							$table = &$sect->addTable();			
							$table->addRows(count($arr));		
							$table->addColumnsList(array(3,4.5,5,3,3.5));
							$baris=1; $kolom=1;
									
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nm_opr'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Kamar Operasi/OK', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($sewaOk,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
							
					}
				}
				
				$noUrut++;
			}			
			
			
			//------------------------------- Biaya Pemeriksaan Penunjang ----------------------------------//
			if($this->textToNumber($jsPenunjang) > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Pemeriksaan Penunjang</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$jsPenunjang.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				
				$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 ";
				$arr=$this->queryAction($sql,'S');
				
				if(count($arr) > 0)
				{
					$table = &$sect->addTable();			
					$table->addRows(1);		
					$table->addColumnsList(array(19));
					$baris=1; $kolom=1;
					$table->writeToCell($baris, $kolom, '<b>Dokter</b>', new Font(10,'Arial'), $parL ); $kolom++;
					
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
	
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['nm_tdk'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['nm_pegawai'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}
				
				
				
				$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2";
				$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{
					$table = &$sect->addTable();			
					$table->addRows(1);		
					$table->addColumnsList(array(19));
					$baris=1; $kolom=1;
					$table->writeToCell($baris, $kolom, '<b>Paramedis</b>', new Font(10,'Arial'), $parL ); $kolom++;
					
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['nm_tdk'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['nm_pegawai'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}	
				
				$noUrut++;
			}
			
			//------------------------------- Biaya Obat-obatan & Alkes ----------------------------------//
			if($this->textToNumber($jsObat) > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Obat-obatan & Alat Kesehatan</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$jsObat.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				$sql = "SELECT * FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=0 AND st_bayar=0 ORDER BY tgl";
				$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{	
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['nm_obat_alkes'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['jml_obt_alkes'].' '.$row['satuan'], new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}			
				
				//----------- Biaya Oksigen jika ada ---------------------
				if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
				{
					$sql = "SELECT tgl, tarif AS jumlah
								   FROM tbt_inap_oksigen			
								   WHERE 
									no_trans = '$noTrans'
									AND flag = '0'";
					$arr=$this->queryAction($sql,'S');
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Oksigen', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
					}
				}
			
				//----------- BHP Rawat Inap ---------------------
				if($this->textToNumber($bhp) > 0)
				{
					$table = &$sect->addTable();			
					$table->addRows(1);		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, 'BHP', new Font(10,'Arial'), $parL ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
					$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
					$table->writeToCell($baris, $kolom, 'Rp. '.number_format($bhp,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
					$baris++; $kolom=1;
				}
							
				$noUrut++;
			}
			
			//------------------------------- Biaya Lain-Lain lab yg st_bayar=0 ----------------------------------//
			$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totLab += $row['jml'];
			}
			
			if($totLab > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Laboratorium</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.'Rp. '.number_format($totLab,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				
				$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
				$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{	
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['jenis'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}
				
				$noUrut++;
			}
			
			//------------------------------- Biaya Lain-Lain rad yg st_bayar=0 ----------------------------------//
			$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totRad += $row['jml'];
			}
			
			if($totRad > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Radiologi</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.'Rp. '.number_format($totRad,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				
				$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
				$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{	
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['jenis'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}
				$noUrut++;
			}
			
			//------------------------------- Biaya Lain-Lain fisio yg st_bayar=0 ----------------------------------//
			$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'fisio' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totFisio += $row['jml'];
			}
			
			if($totFisio > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Fisio</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.'Rp. '.number_format($totFisio,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'fisio' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
				$arr=$this->queryAction($sql,'S');
				if(count($arr) > 0)
				{	
					$table = &$sect->addTable();			
					$table->addRows(count($arr));		
					$table->addColumnsList(array(3,4.5,5,3,3.5));
					$baris=1; $kolom=1;
					
					foreach($arr as $row)
					{
						$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, $row['jenis'], new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
						$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
						$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jml'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
						$baris++; $kolom=1;
					}
				}
				$noUrut++;
			}
			
			//------------------------------- Biaya Lain-Lain ----------------------------------//
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
			if(AmbulanRecord::finder()->find('no_trans=?',$noTrans))				
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
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Lain-Lain</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.'Rp. '.number_format($totLain,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				//------------------------------- Biaya Askep ----------------------------------//
				if($lamaInap > 0)				
				{
					$sql = "SELECT nama,tgl,tarif 
								   FROM tbt_inap_askep
								   WHERE no_trans = '$notrans'
								   AND flag = '0'";
					$arr=$this->queryAction($sql,'S');
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['tarif'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Ambulance', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['tujuan'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, $this->convertDate($row['tgl'],'3'), new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Blue Light', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
					}
				}
			
			$noUrut++;
		}
			
			
			
			//------------------------------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ----------------------------------//
			
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
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Selama Rawat Jalan</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.'Rp. '.number_format($totBiayaAlih,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				
				
				if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
				{
					//-------------------- Biaya Alih Tindakan Rawat jalan ---------------------		
					$sql = "SELECT 
								jml AS jumlah, jns_trans, nama, no_trans_jln
							FROM 
								view_biaya_alih 
							WHERE  
								no_trans_inap='$notrans'
								AND jns_trans = 'Tindakan Rawat Jalan'";
				
					$arr=$this->queryAction($sql,'S');
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$noTransJln = $row['no_trans_jln'];
							$sql = "SELECT dokter FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJln' AND st_flag = '0'";
							$idDokter = KasirRwtJlnRecord::finder()->findBySql($sql)->dokter;
							$nmDoker = PegawaiRecord::finder()->findByPk($idDokter)->nama;
						
							$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							if(substr($row['nama'],0,11) == 'Jasa Dokter')
							{
								$table->writeToCell($baris, $kolom, $nmDoker, new Font(10,'Arial'), $parL ); $kolom++;
							}
							else
							{
								$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							}
						
							
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
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
					if(count($arr) > 0)
					{	
						$table = &$sect->addTable();			
						$table->addRows(count($arr));		
						$table->addColumnsList(array(3,4.5,5,3,3.5));
						$baris=1; $kolom=1;
						
						foreach($arr as $row)
						{
							$table->writeToCell($baris, $kolom, '-', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, $row['nama'], new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parL ); $kolom++;
							$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
							$table->writeToCell($baris, $kolom, 'Rp. '.number_format($row['jumlah'],2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
							$baris++; $kolom=1;
						}
					}
		
				}
				
				$noUrut++;
			}
			
			//------------------------------- JPM ----------------------------------//
			if($this->textToNumber($jpm) > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. JPM</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$jpm.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				$noUrut++;
			}			
			
			//------------------------------- Biaya Administrasi ----------------------------------//
			if($this->textToNumber($biayaAdm) > 0)
			{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Administrasi</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$biayaAdm.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				$noUrut++;
			}
			
			//------------------------------- Biaya Metrai ----------------------------------//
			if($this->textToNumber($biayaMetrai) > 0)
				{
				$table = &$sect->addTable();			
				$table->addRows(1);		
				$table->addColumnsList(array(15.5,3.5));
				
				$baris=1; $kolom=1;
				$table->writeToCell($baris, $kolom, '<b>'.$this->numberToRoman($noUrut+1).'. Biaya Metrai</b>', new Font(10,'Arial'), $parL ); $kolom++;
				$table->writeToCell($baris, $kolom, '<b>'.$biayaMetrai.'</b>', new Font(10,'Arial'), $parR ); $kolom++;
				$noUrut++;
			}
			
			$table = &$sect->addTable();			
			$table->addRows(5);		
			$table->addColumnsList(array(3,4.5,5,3,3.5));
			
			$baris=1; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Total Transaksi', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlTagihan,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Uang Muka', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($uangMuka,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Sisa Pembayaran', new Font(10,'Arial'), $parL ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Rp. '.number_format($jmlKurangBayar,2,',','.'), new Font(10,'Arial'), $parR ); $kolom++;
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, 'Discount', new Font(10,'Arial'), $parL ); $kolom++;
			if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
			{
				$table->writeToCell($baris, $kolom, $besarDisc.' %', new Font(10,'Arial'), $parR ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, '0 %', new Font(10,'Arial'), $parR ); $kolom++;
			}
			
			$baris++; $kolom=1;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parC ); $kolom++;
			$table->writeToCell($baris, $kolom, '<b>TOTAL</b>', new Font(10,'Arial'), $parL ); $kolom++;
			
			if($totBiayaDiscBulat != '')
			{;
				$table->writeToCell($baris, $kolom, '<b>Rp. '.number_format($totBiayaDiscBulat,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
			}
			else
			{
				$table->writeToCell($baris, $kolom, '<b>Rp. '.number_format($jmlKurangBayar,2,',','.').'</b>', new Font(10,'Arial'), $parR ); $kolom++;
			}		
			
			
			$sect->writeText('', new Font(11,'Arial'),$parJ);
			$sect->writeText('<b>Terbilang :</b>', new Font(11,'Arial'),$parJ);
			
			$table = &$sect->addTable();			
			$table->addRows(1);		
			$table->addColumnsList(array(19));
			
			$baris=1; $kolom=1;
			$table->setBordersOfCells(new BorderFormat(1, '#000000'), $baris, $kolom, $baris, $kolom, 1, 1, 1, 1);
			$table->writeToCell($baris, $kolom, '<b>'.$tagihanTerbilang.'</b>', new Font(11,'Arial'), $parJ ); $kolom++;
			
			$sect->writeText('', new Font(12,'Arial'),$parJ);
			
			$table = &$sect->addTable();
			$table->addRows(5);
			$table->addColumnsList(array(6,4, 9));
			
			$baris=1; $kolom=3;
			$table->writeToCell($baris, $kolom, 'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'), new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'K a s i r', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'a', new Font(20,'Arial','#ffffff'), $parC);
			$baris++; 
			$table->writeToCell($baris, $kolom, '('.$operator.')', new Font(10,'Arial'), $parC );
			$baris++; 
			$table->writeToCell($baris, $kolom, 'NIP. '.$nip, new Font(10,'Arial'), $parC );
			
		
			$rtf->prepare();
			$rtf->sendRtf('PreviewKwitansiRawatInap'.$nmPasien);
		}
	}
	
}
?>