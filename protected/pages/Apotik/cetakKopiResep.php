<?php
class cetakKopiResep extends SimakConf
{
	/*
     *  @function untuk mencetak kopi resep
     *  @param $param ->
     *  made by PT. GSK @November 2013
     */
	public function onLoad($param)
	{
		$noTrans = $this->Request['noreg'];
		
		$cm = ObatJualRecord::finder()->findByno_reg($noTrans)->cm;
		$nmPembayar = PasienRecord::finder()->findByPk($cm)->nama;
		$tglLahirPasien = PasienRecord::finder()->findByPk($cm)->tgl_lahir;
		
		$dateNow = new DateTime();
		$tglLahir = new DateTime($tglLahirPasien);
		$diffDate = $dateNow -> diff($tglLahir);
		if($diffDate->y == 0)
			$umurPasien = $diffDate->m;
		else
			$umurPasien = $diffDate->y;

		$idKlinik = ObatJualRecord::finder()->findByno_reg($noTrans)->klinik;	
		$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;		
		$idKDokter = ObatJualRecord::finder()->findByno_reg($noTrans)->dokter;	
		$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;	
		
		//$noResep = ObatJualRecord::finder()->findByPk($noTrans)->no_reg;
		
		$pdf = new reportKwitansi('P', 'mm', 'a4');
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		//----------------------------------------------------------------------------//
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
		$pdf->Cell(0,5,'SALINAN RESEP','0',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);

		$pdf->Cell(30,10, 'Dokter', 0, 0, 'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmDokter,0,0,'L');
		$pdf->Cell(50,10,'',0,0,'L');
		$pdf->Cell(30,10, 'Bagian', 0, 0, 'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmKlinik,0,0,'L');
		$pdf->Ln(5);

		$pdf->Cell(30,10, 'Nama', 0, 0, 'L');
		$pdf->Cell(2, 10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPembayar,0,0,'L');
		$pdf->Cell(50,10,'',0,0,'L');
		$pdf->Cell(30,10, 'Tanggal', 0, 0, 'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,date("Y-m-d"),0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10, 'Umur', 0, 0, 'L');
		$pdf->Cell(2, 10,':  ',0,0,'L');
		if($diffDate->y != 0)
			$pdf->Cell(30,10,$umurPasien.' tahun',0,0,'L');
		else
			$pdf->Cell(30,10,$umurPasien.' bulan',0,0,'L');
		$pdf->Cell(50,10,'',0,0,'L');
		$pdf->Cell(30,10, 'No.', 0, 0, 'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$noTrans,0,0,'L');
		$pdf->Ln(10);


		
		
		
		$pdf->Cell(0,0,'','B',1,'C');
		$pdf->Ln(5);
		$pdf->Image('protected/pages/Apotik/logoR.jpg', null, null, 30);

		$pdf->Ln(155);
		
		$pdf->Cell(0,0,'','B',1,'C');
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(30,5,'Obat-obatan tersebut seharga Rp. .......................',0,0,'L');
		$pdf->Ln(10);
		$pdf->Cell(30,5,'Telah diterima,',0,0,'L');
		$pdf->Ln(20);
		$pdf->Cell(30,5,'(                                              )',0,0,'L');
		$pdf->Output();
	}
}
?>