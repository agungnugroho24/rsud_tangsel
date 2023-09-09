<?php
class cetakKartuFormBaru extends SimakConf
{
	public function onLoad()
	{	
		$cm=$this->Request['cm'];
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$pas=$this->Request['pas'];
		$pttg=$this->Request['pen'];
		$notran=$this->Request['notrans'];
		
		if($pas=='1'){
			$pasien='Pasien Lama';
		}else{
			$pasien='Pasien Baru';
		}
		
		$wkt=RwtjlnRecord::finder()->find('cm = ?',$cm)->wkt_visit;
		$jam=intval(substr($wkt,0,2));
		if((1 <= $jam) AND ($jam <= 9)){
			$jam='Pagi';
		}elseif((10 <= $jam ) AND ($jam <= 14)){
			$jam='Siang';
		}elseif((15 <= $jam ) AND ($jam <= 18)){
			$jam='Sore';
		}else{
			$jam='Malam';
		}		
		
		if($klinik=='07')
		{ 
			$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM
				  tbd_pegawai,
				   tbm_poliklinik,
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
				AND tbm_poliklinik.id='$klinik'
				AND tbd_pegawai.id='$dokter'
			";		
		}
		else
		{
			$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM
				  tbd_pegawai
				  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id),
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
				AND tbm_poliklinik.id='$klinik'
				AND tbd_pegawai.id='$dokter'
			";		
		}
		
		
		$pdf=new reportKwitansi('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',7);
		$pdf->Cell(0,4,'SLIP PENDAFTARAN RAWAT JALAN','0',0,'C');
		$pdf->Ln(8);						
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,4,'No. cm',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(55,4,$cm.' ('.$pasien.')',0,0,'L');
		
		$arrData=$this->queryAction($sql,'R');
		foreach($arrData as $row)
		{
			$nmDok=$row['nmDok'];
			
			$pdf->Cell(12,4,'Jam',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(25,4,$wkt.' ('.$jam.')',0,0,'L');
			
			/*
			$pdf->Cell(25,10,'Tanggal',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(25,10,date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');		*/
			$pdf->Ln(4);
			$pdf->Cell(10,4,'Nama',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(55,4,$row['nmPas'].', '.$embel,0,0,'L');
			
			$pdf->Cell(12,4,'No Register',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(45,4,$notran,0,0,'L');	
			
			$pdf->Ln(4);		
			$pdf->Cell(10,4,'Dokter',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(55,4,$row['nmDok'],0,0,'L');
			
			$pdf->Cell(12,4,'Poliklinik',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(55,4,$row['poli'],0,0,'L');
			
			$pdf->Ln(4);	
			
			$pdf->Cell(20,4,'Penanggung Jawab',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(25,4,$pttg,0,0,'L');		
		}
		
		$pdf->Ln(8);
		
		$pdf->SetFont('Arial','B',7);		
		$pdf->Cell(10,4,'No ',1,0,'C');
		$pdf->Cell(30,4,'Kode Tindakan',1,0,'C');		
		$pdf->Cell(70,4,'Nama Tindakan',1,0,'C');
		$pdf->Ln(4);	
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,4,'1',1,0,'C');
		$pdf->Cell(30,4,'Laboratorium',1,0,'L');		
		$pdf->Cell(70,4,'[  ] Ya',1,0,'L');
		$pdf->Ln(4);
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,4,'2',1,0,'C');
		$pdf->Cell(30,4,'Radiologi',1,0,'L');		
		$pdf->Cell(70,4,'[  ] Ya',1,0,'L');
		$pdf->Ln(4);
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,4,'3',1,0,'C');
		$pdf->Cell(30,4,'Resep',1,0,'L');		
		$pdf->Cell(70,4,'[  ] Ya',1,0,'L');
		$pdf->Ln(4);
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,4,'4',1,0,'C');
		$pdf->Cell(30,4,'Lain - lain',1,0,'L');		
		$pdf->Cell(70,4,'........',1,0,'L');
		$pdf->Ln(4);
				
		for ($i=5;$i<=9;$i++)
		{
			$pdf->Cell(10,4,$i,1,0,'C');
			$pdf->Cell(30,4,'',1,0,'C');		
			$pdf->Cell(70,4,'',1,0,'C');
			$pdf->Ln(4);	
		}
		$pdf->Ln(2);	
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(160,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(4);
		$pdf->Cell(160,5,'Dokter,',0,0,'C');	
		$pdf->Ln(8);
		$pdf->Cell(160,5,'('.$nmDok.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));
		$pdf->Output();
		
	}
	
}
?>
