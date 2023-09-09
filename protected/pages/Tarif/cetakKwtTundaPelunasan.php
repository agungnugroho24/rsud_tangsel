<?php
class cetakKwtTundaPelunasan extends SimakConf
{
	public function onLoad($param)
	{	
		$cm = $this->Request['cm'];
		$notrans = $this->Request['notrans'];
		$nmPembayar = $this->Request['nama'];
		
		$jmlTagihan = $this->Request['jmlTagihan'];
		$sisaByr = $this->Request['sisaByr'];
		$jmlBayar = $this->Request['jmlBayar'];
		
		$tgl = $this->convertDate($this->Request['tgl'],'3');
		$wkt = $this->Request['wkt'];
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$bayarTerbilang = ucwords($this->terbilang($jmlBayar) . ' rupiah');
		
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
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN RAWAT JALAN','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		//$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		//$pdf->Ln(5);		
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		$pdf->SetFont('Arial','B',11);
		//$pdf->Cell(30,10,$nmPasien.' / '.$pjPasien,0,0,'L');
		$pdf->Cell(30,10,$nmPembayar,0,0,'L');
				
		if($this->Request['diagnosa'] !='')
		{
			$pdf->Ln(10);	
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(30,5,'Diaognosa',0,0,'L');
			$pdf->Cell(2,5,':',0,0,'L');
			$pdf->SetFont('Arial','B',11);
			$pdf->MultiCell(0,5,$this->Request['diagnosa'],0,'L');
		}
		else
			$pdf->Ln(7);	
				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,10,'Rp. '.number_format($jmlBayar,'2',',','.'),0,0,'L');	
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Terbilang',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(0,10,' '.$bayarTerbilang,1,'L');
		//$pdf->Ln(7);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Untuk pembayaran',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Transaksi Rawat Jalan No: '.$notrans,0,0,'L');	
		$pdf->Ln(7);
		
		$pdf->Cell(0,5,'','B',1,'C');
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Jumlah Tagihan',0,0,'L');
		$pdf->Cell(2,10,': Rp. ',0,0,'L');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,10,number_format($jmlTagihan,'2',',','.'),0,0,'R');	
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Telah Dibayar',0,0,'L');
		$pdf->Cell(2,10,': Rp. ',0,0,'L');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,10,number_format($jmlBayar,'2',',','.'),0,0,'R');	
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'Sisa Pembayaran ',0,0,'L');
		$pdf->Cell(2,10,': Rp. ',0,0,'L');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(30,10,number_format($sisaByr,'2',',','.'),0,0,'R');	
		$pdf->Ln(7);
		
		$pdf->Ln(5);
		//------------------------------ TTD KASIR KUITANSI --------------------------------- //		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.$tgl,0,0,'C');
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
