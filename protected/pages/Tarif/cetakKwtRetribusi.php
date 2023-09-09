<?php
class cetakKwtRetribusi extends SimakConf
{
	public function onLoad($param)
	{	
		$noTrans=$this->Request['notrans'];	
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		$cm = RwtjlnRecord::finder()->findByPk($noTrans)->cm;	
		$nmPembayar = PasienRecord::finder()->findByPk($cm)->nama;
		
		$jumlah = KasirPendaftaranRecord::finder()->find('no_trans=?',$noTrans)->tarif;	
		$tgl_kasir = KasirPendaftaranRecord::finder()->find('no_trans=?',$noTrans)->tgl_kasir;	
		
		$idKlinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;	
		$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;		
		$idKDokter = RwtjlnRecord::finder()->findByPk($noTrans)->dokter;	
		$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;	
		$stAsuransi = RwtjlnRecord::finder()->findByPk($noTrans)->st_asuransi;
		
		$idPenjamin = RwtjlnRecord::finder()->findByPk($noTrans)->penjamin;
		$nmKelPenjamin = KelompokRecord::finder()->findByPk($idPenjamin)->nama;	
			
		if($idPenjamin == '02' && $stAsuransi == '1') //Kelompok Penjamin Asuransi/jamper
		{			
			$idPerus = RwtjlnRecord::finder()->findByPk($noTrans)->perus_asuransi;	
			$nmPerus = PerusahaanRecord::finder()->findByPk($idPerus)->nama;
			$nmKelPenjamin .= ' ('.$nmPerus.')';	
		}	
		
		$bayarTerbilang=ucwords($this->terbilang($jumlah) . ' rupiah');
		
		/*
		$noKwitansi = substr($notrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($notrans,4,2).'/';
		$noKwitansi .= substr($notrans,0,4);
		*/
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$pdf=new reportKwitansi('L','mm','a5');
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
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN RETRIBUSI','0',0,'C');
		$pdf->Ln(5);		
		
		/*$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		*/
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'No. Rekam Medis',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Poliklinik',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmKlinik,0,0,'L');
		$pdf->Ln(5);
		
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
		
		$pdf->SetFont('Arial','B',12);
		$pdf->MultiCell(0,10,$bayarTerbilang,1,'L');
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'Untuk Pembayaran',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($idKlinik == '06')
			$pdf->Cell(45,10,'Retribusi Pelayanan Kesehatan Darurat Medik',0,0,'L');	
		else
			$pdf->Cell(45,10,'Retribusi Pelayanan Kesehatan Rawat Jalan',0,0,'L');	
		
		$pdf->Ln(5);
		
		//------------------------------ TTD KASIR KUITANSI --------------------------------- //		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, ',0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(175,8,substr($tgl_kasir,8,2) . ' ' . $this->namaBulan(substr($tgl_kasir,5,2)) . ' ' . substr($tgl_kasir,0,4).' '.date("H:i"),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(10);
		
		if($this->Request['layout']=='modal')
			$url = "";
		else
			$url = $this->Service->constructUrl('Tarif.BayarKasirRwtJlnRetribusi');
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$url);	
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
