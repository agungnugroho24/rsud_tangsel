<?php
class cetakKwtRwtInapDp extends SimakConf
{
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		$notrans=$this->Request['notrans'];
		$notransInap=$this->Request['notransInap'];
		$nmPembayar=$this->Request['nmPembayar'];
		
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
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN UANG MUKA RAWAT INAP','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		$pdf->SetFont('Arial','B',9);
		//$pdf->Cell(30,10,$nmPasien.' / '.$pjPasien,0,0,'L');
		$pdf->Cell(30,10,$nmPembayar,0,0,'L');
		$pdf->Ln(5);		
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,10,'Rp. '.number_format($jumlah,'2',',','.'),0,0,'L');	
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(30,5,'Terbilang : ',0,0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',10);
		$pdf->MultiCell(0,5,$bayarTerbilang,1,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'Untuk Pembayaran',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(45,10,'Uang Muka Biaya Perawatan Pasien',0,0,'L');	
		
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Nomor Catatan Medik',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Nama Pasien',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		
		$pdf->Ln(5);
		
		
		//$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Tanggal Masuk',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10, $tglMasukTxt,0,0,'L');
		
		
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
		
}
?>
