<?php
class cetakRingakasanMasukInap extends SimakConf
{
	public function onLoad($param)
	{	
				
		$cm = $this->Request['cm'];
		$cm = '9999999';
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];			
		
		$pdf=new reportCetak("P","mm","a4");
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		//$pdf->Image('protected/pages/Tarif/logo1.png',40,12,15);	
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang Telp. (021) 74718440 ','0',0,'L');	
		$pdf->Ln(5);
		$pdf->Cell(0,5,'Kota Tangerang Selatan','0',0,'L');
		$pdf->Ln(5);
		
		//$url=$this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$cm,'mode'=>'1'));
		//$url=str_replace('amp;','',$url);
		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,5,'RINGKASAN MASUK','0',0,'C','',$url);
		$pdf->Ln(5);		
						
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'R');
		$pdf->Cell(50,5,'No. CM',0,0,'R');
		$pdf->Cell(2,5,'',0,0,'R');
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,$cm,1,1,'C');
				
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->SetWidths(array(40,5,60,40,5,40));
		$pdf->SetAligns(array('L','C','L','L','C','L'));
		$pdf->Setln('5');		
		$pdf->SetLineSpacing(array('5','5','5','5','5','5'));		
		$pdf->Row(array('Nama Pasien',':','','No. Reg',':',''));
		$pdf->Row(array('Tanggal Lahir',':','','Tipe Pasien',':',''));
		$pdf->Row(array('Umur',':','','Nama Penjamin',':',''));
		
		$pdf->SetWidths(array(40,5,60,85));
		$pdf->SetAligns(array('L','C','L','L'));		
		$pdf->Row(array('Agama',':','','Cara Masuk Dikirim Oleh'));
		
		$pdf->SetWidths(array(40,5,60,85));
		$pdf->SetAligns(array('L','C','L','L'));
		$pdf->SetLineSpacing(array('5','5','5','5'));
		$pdf->Setln('5');
		$pdf->Row(array('Pendidikan',':','',''));
		
		$pdf->SetWidths(array(40,5,60));
		$pdf->SetAligns(array('L','C','L'));
		$pdf->SetLineSpacing(array('5','5','5'));		
		$pdf->Row(array('Pekerjaan',':',''));
		
		$pdf->SetWidths(array(40,5,60,40,5,40));
		$pdf->SetAligns(array('L','C','L','L','C','L'));
		$pdf->Setln('5');		
		$pdf->SetLineSpacing(array('5','5','5','5','5','5'));	
		$pdf->Row(array('Alamat Lengkap',':','','','',''));
		$pdf->Row(array('Status Perkawinan',':','','','',''));
		$pdf->Row(array('Nama Penanggung Jawab',':','','','',''));
		$pdf->Row(array('Nama/Alamat Keluarga Terdekat',':','','','',''));
		$pdf->Row(array('Bag/Spesialis',':','','','',''));
		$pdf->Row(array('Dokter Penanggung Jawab',':','','','',''));
		
		
		
		/*
		$pdf->Cell(40,5,'Nama Pasien',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');	
		
		$pdf->Cell(40,5,'No. Reg',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(40,5,'',1,0,'L');	
		$pdf->Ln(5);
		
		$pdf->Cell(40,5,'Tanggal Lahir',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');	
		
		$pdf->Cell(40,5,'Tipe Pasien',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(40,5,'',1,0,'L');	
		$pdf->Ln(5);
		
		$pdf->Cell(40,5,'Umur',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');
		
		$pdf->Cell(40,5,'Nama Penjamin',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(40,5,'',1,0,'L');		
		$pdf->Ln(5);
		
		$pdf->Cell(40,5,'Agama',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');
		
		$pdf->Cell(85,5,'Cara Masuk Dikirim Oleh',1,0,'L');		
		$pdf->Ln(5);
		
		$x=$pdf->GetX();
		$y=$pdf->GetY();
		$pdf->Cell(40,5,'Pendidikan',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');	
		
		$pdf->MultiCell(85,5,'sad asdja hdhasd ',1,'L');
		$pdf->Ln(0);
		
		$pdf->SetY($y+5);
		$pdf->Cell(40,5,'Pendidikan',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');	
		$pdf->Ln(5);
		
		$pdf->Cell(40,5,'Alamat Lengkap',1,0,'L');		
		$pdf->Cell(5,5,':',1,0,'C');
		$pdf->Cell(60,5,'',1,0,'L');	
		$pdf->Ln(5);
		*/
		
		$pdf->Ln(5);
		
		//$pdf->Cell(300,8,'('.$nmDok.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));
		$pdf->Output();
		
	}
	
}
?>
