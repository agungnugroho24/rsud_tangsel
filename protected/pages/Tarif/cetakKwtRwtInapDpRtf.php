<?php
class cetakKwtRwtInapDpRtf extends SimakConf
{
	public function onLoad($param)
	{
		parent::onLoad($param);
	
		$cm=$this->Request['cm'];
		$notrans=$this->Request['notrans'];
		$notransInap=$this->Request['notransInap'];
		
		$tgl=$this->convertDate($this->Request['tgl'],'3');
		$wkt=$this->Request['wkt'];
		$jumlah=$this->Request['jumlah'];	
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$bayarTerbilang=ucwords($this->terbilang($jumlah) . ' rupiah');
		
		$nmPasien = PasienRecord::finder()->findByPk($cm)->nama;
		$pjPasien = RwtInapRecord::finder()->findByPk($notransInap)->nama_pgg;
		$kelas = RwtInapRecord::finder()->findByPk($notransInap)->kelas;
		
		$tglMasuk = RwtInapRecord::finder()->findByPk($notransInap)->tgl_masuk;
		$wktMasuk = RwtInapRecord::finder()->findByPk($notransInap)->wkt_masuk;
		
		$tglMasukTxt=$this->convertDate($tglMasuk,'3');
		
		$noKwitansi = substr($notrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($notrans,4,2).'/';
		$noKwitansi .= substr($notrans,0,4);
								
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
		
		$sect->writeText('<b><u>KUITANSI PEMBAYARAN UANG MUKA RAWAT INAP</u></b>', new Font(11,'Arial'), new ParFormat('center'));	
		$sect->writeText('No. Kuitansi: '.$noKwitansi, new Font(11,'Arial'), new ParFormat('center'));					
		
		$parJ = new ParFormat('justify');
		$parJ->setSpaceBetweenLines(1.5);
		
		$parL = new ParFormat('left');
		$parL->setSpaceBetweenLines(1.5);
		
		$parC = new ParFormat('center');
		$parC->setSpaceBetweenLines(1.5);
		
		$sect->writeText('', new Font(12,'Arial'),$parJ);
		
		$table = &$sect->addTable();			
		$table->addRows(3);		
		$table->addColumnsList(array(3.5,0.5,15));
		
		$baris=1; $kolom=1;
		$table->writeToCell($baris, $kolom, 'Telah Terima dari', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, $nmPasien.' / '.$pjPasien, new Font(10,'Arial'), $parJ ); $kolom++;
		$baris++; $kolom=1;
		$table->writeToCell($baris, $kolom, 'Uang Sebesar', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '<b>Rp. '.number_format($jumlah,'2',',','.').'</b>', new Font(11,'Arial'), $parJ ); $kolom++;
		
		$baris++; $kolom=1;
		$table->writeToCell($baris, $kolom, 'Terbilang', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '<b>'.$bayarTerbilang.'</b>', new Font(11,'Arial'), $parJ ); $kolom++;
		
		
		$table = &$sect->addTable();			
		$table->addRows(4);		
		$table->addColumnsList(array(3.5,0.5,7,0.5,7.5));
		
		$baris=2; $kolom=1;
		$table->writeToCell($baris, $kolom, 'Untuk Pembayaran', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '- Uang Muka Biaya Perawatan Pasien', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, $nmPasien, new Font(10,'Arial'), $parJ ); $kolom++;
		$baris++; $kolom=1;
		$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '- Nomor Catatan Medik', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, $cm, new Font(10,'Arial'), $parJ ); $kolom++;
		$baris++; $kolom=1;
		$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, '- Tanggal Masuk', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, ':', new Font(10,'Arial'), $parJ ); $kolom++;
		$table->writeToCell($baris, $kolom, $tglMasukTxt, new Font(10,'Arial'), $parJ ); $kolom++;
		
		
		
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
		$rtf->sendRtf('KwitansiUangMukaRawatInap'.$nmPasien.'-'.$notrans);
	}
	
}
?>