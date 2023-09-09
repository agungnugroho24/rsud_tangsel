<?php
class cetakKartuStatusGigi extends SimakConf
{
	public function onLoad($param)
	{					
		$cm=$this->Request['cm'];			
		
		$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel AS jkel,
				  tbd_pasien.tgl_lahir AS tgl_lahir,
				  tbd_pasien.umur,
				  tbd_pasien.nm_pj AS penanggung,
				  tbd_pasien.alamat,
					tbd_pasien.agama,
					tbd_pasien.goldar,
					tbd_pasien.wni,
				  (SELECT nama FROM tbm_kelompok WHERE tbd_pasien.kelompok=tbm_kelompok.id) AS penjamin,
					(SELECT nama FROM tbm_pekerjaan WHERE tbd_pasien.pekerjaan=tbm_pekerjaan.id) AS pekerjaan
				FROM	
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
			";			
		
		$pdf=new fpdf('P','mm','A4');
		$pdf->AliasNbPages(); 
		$pdf->setLeftMargin('20'); 
		$pdf->setTopMargin('32'); 
		
		$pdf->AddPage();		
		$pdf->Ln(0);								
		$arrData=$this->queryAction($sql,'R');
		foreach($arrData as $row)
		{
			$cm=$row['cm'];
			$nmPas=$row['nmPas'];
			$umur=$row['umur'];
			$alamat=$row['alamat'];
			$pekerjaan=$row['pekerjaan'];
			$wni=$row['wni'];
			$v=$row['agama'];
			$penanggung = AgamaRecord::finder()->findByPk($v)->nama;
			$penjamin=$row['goldar'];
			$jkel=$row['jkel'];			
			if($jkel == '0')
				$jkel = 'Laki-laki';
			else
				$jkel = 'Perempuan';	
			
			$pengby=$row['pengby'];
			//$penjamin=$row['penjamin'];
		}		
		
		$date = $this->convertDate(RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->tgl_visit,'1');		
		$time = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->wkt_visit;			
		$penanggung_jawab = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->penanggung_jawab;	
		$penjamin = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->penjamin;	
		$penjamin = KelompokRecord::finder()->findByPk($penjamin)->nama;	
		$id_klinik = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->id_klinik;	
		$id_klinik = PoliklinikRecord::finder()->findByPk($id_klinik)->nama;	
		
		$penjamin = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->penjamin;		
		
		if($penjamin == '01')
			$penjamin = KelompokRecord::finder()->findByPk($penjamin)->nama;		
		elseif($penjamin == '02')
		{
			$perus = RwtjlnRecord::finder()->findByPk($this->Request['notrans'])->perus_asuransi;		
			$penjamin = PerusahaanRecord::finder()->findByPk($perus)->nama;		
		}
		
		$pdf->Cell(145,5,'',0,0,'L');
		$pdf->SetFont('Arial','',11);		
		$pdf->Cell(2,12,$date,0,0,'L');
		
		$pdf->Ln(4);
		
		$pdf->SetFont('Arial','B',15);	
		$pdf->Cell(2,12,'',0,0,'L');		
		$pdf->Cell(2,10,$cm,0,0,'L','',$this->Service->constructUrl('Pendaftaran.DaftarBaru'));
		//$pdf->Ln(10);
		
		$pdf->Ln(6);
		
		$pdf->Cell(70,5,'',0,0,'L');
		$pdf->SetFont('Arial','',11);		
		$pdf->Cell(2,12,$id_klinik,0,0,'L');
		$pdf->Ln(14);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(22,5,'',0,0,'L');
		$pdf->Cell(2,5,$nmPas,0,0,'L');		
		$pdf->Cell(97,5,'',0,0,'L');
		$pdf->Cell(2,5,$jkel,0,0,'L');
		$pdf->Ln(10);
		$pdf->Cell(22,5,'',0,0,'L');
		$pdf->Cell(2,5,$umur . ' Tahun',0,0,'L');
		$pdf->Cell(97,5,'',0,0,'L');
		$pdf->Cell(2,5,$penanggung,0,0,'L');		
		$pdf->Ln(8);
		$pdf->Cell(22,5,'',0,0,'L');
		//$pdf->Cell(2,5,$alamat,0,0,'L');
		$pdf->SetFont('Arial','',9);
		$x = $pdf->getY();
		$pdf->MultiCell(60,4,$alamat,0,'L');
		
		$pdf->SetFont('Arial','',11);
		$pdf->setY($x+2);
		$pdf->Cell(128,5,'',0,0,'L');		
		$pdf->Cell(2,5,$penanggung_jawab,0,0,'L');		
		
		$pdf->Ln(10);
		$pdf->Cell(22,5,'',0,0,'L');
		$pdf->Cell(2,5,$pekerjaan,0,0,'L');
		$pdf->Cell(97,5,'',0,0,'L');
		$pdf->Cell(2,5,$penjamin,0,0,'L');	
		
		$pdf->Output();
	}
	
}
?>
