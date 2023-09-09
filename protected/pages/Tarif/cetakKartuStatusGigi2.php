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
				  tbd_pasien.umur,
				  tbd_pasien.nm_pj AS penanggung,
				  tbd_pasien.alamat,
				  tbd_pasien.agama,
				  tbd_pasien.goldar,
				  tbm_kelompok.nama AS penjamin,
				  tbm_pekerjaan.nama AS pekerjaan,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM				  
				  tbd_pegawai
				  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id),
				  tbm_pekerjaan,
				  tbm_kelompok,
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
				AND tbd_pasien.pekerjaan=tbm_pekerjaan.id
				AND tbd_pasien.kelompok=tbm_kelompok.id				
			";			
		
		$pdf=new fpdf('P','mm','legal');
		$pdf->AliasNbPages(); 
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
		$date = date('d/m/Y');	
		$pdf->SetFont('Arial','',15);	
		$pdf->Ln(2);
		$pdf->Cell(2,12,'',0,0,'L');		
		$pdf->Cell(2,10,$cm,0,0,'L','',$this->Service->constructUrl('Pendaftaran.DaftarBaru'));
		$pdf->Ln(10);
		$pdf->Cell(116,5,'',0,0,'L');
		$pdf->SetFont('Arial','',11);		
		$pdf->Cell(2,12,$date,0,0,'L');
		$pdf->Ln(21);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$nmPas,0,0,'L');		
		$pdf->Cell(78,5,'',0,0,'L');
		$pdf->Cell(2,5,$jkel,0,0,'L');
		$pdf->Ln(9);
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$umur . ' Tahun',0,0,'L');
		$pdf->Cell(78,5,'',0,0,'L');
		$pdf->Cell(2,5,$penanggung,0,0,'L');		
		$pdf->Ln(7);
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$alamat,0,0,'L');
		$pdf->Ln(7);
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$pekerjaan,0,0,'L');
		$pdf->Cell(78,5,'',0,0,'L');
		$pdf->Cell(2,5,$penjamin,0,0,'L');	
		
		$pdf->Output();
		
	}
	
}
?>
