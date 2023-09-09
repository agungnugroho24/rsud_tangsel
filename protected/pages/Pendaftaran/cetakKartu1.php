<?php
class cetakKartu1 extends SimakConf
{
	public function onLoad($param)
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
		
		
		//$pdf=new FPDF('P','mm','a5');
		$pdf=new FPDF;
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
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
		$pdf->Ln(4);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(1);		
		//$url=$this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$cm,'mode'=>'1'));
		$url=str_replace('amp;','',$url);
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'SLIP PENDAFTARAN RAWAT JALAN','0',0,'C','',$url);
		$pdf->Ln(6);						
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(15,6,'No. cm',0,0,'L');
		$pdf->Cell(2,6,':  ',0,0,'L');
		$pdf->Cell(45,6,$cm.' ('.$pasien.')',0,0,'L');
		
		$arrData=$this->queryAction($sql,'R');
		foreach($arrData as $row)
		{
			$nmDok=$row['nmDok'];
			$pdf->Cell(13,6,'Poliklinik',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(55,6,$row['poli'],0,0,'L');
			$pdf->Cell(10,6,'Jam',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(25,6,$wkt.' ('.$jam.')',0,0,'L');
			/*
			$pdf->Cell(25,10,'Tanggal',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(25,10,date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');		*/
			$pdf->Ln(5);
			$pdf->Cell(15,6,'Nama',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(45,6,$row['nmPas'].', '.$embel,0,0,'L');
			//$pdf->Ln(5);		
			$pdf->Cell(13,6,'Dokter',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(55,6,$row['nmDok'],0,0,'L');
			$pdf->Cell(32,6,'Penanggung Jawab',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(25,6,$pttg,0,0,'L');		
			$pdf->Ln(5);	
			$pdf->Cell(20,6,'No Register',0,0,'L');
			$pdf->Cell(2,6,':  ',0,0,'L');
			$pdf->Cell(45,6,$notran,0,0,'L');	
			$pdf->Ln(5);	
		}
		
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','B',10);		
		$pdf->Cell(10,5,'No ',1,0,'C');
		$pdf->Cell(30,5,'Kode Tindakan',1,0,'C');		
		$pdf->Cell(150,5,'Nama Tindakan',1,0,'C');
		$pdf->Ln(5);	
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,5,'1',1,0,'C');
		$pdf->Cell(30,5,'Laboratorium',1,0,'L');		
		$pdf->Cell(150,5,'[  ] Ya',1,0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,5,'2',1,0,'C');
		$pdf->Cell(30,5,'Radiologi',1,0,'L');		
		$pdf->Cell(150,5,'[  ] Ya',1,0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,5,'3',1,0,'C');
		$pdf->Cell(30,5,'Resep',1,0,'L');		
		$pdf->Cell(150,5,'[  ] Ya',1,0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,5,'4',1,0,'C');
		$pdf->Cell(30,5,'Lain - lain',1,0,'L');		
		$pdf->Cell(150,5,'........',1,0,'L');
		$pdf->Ln(5);
				
		for ($i=5;$i<=9;$i++)
		{
			$pdf->Cell(10,5,$i,1,0,'C');
			$pdf->Cell(30,5,'',1,0,'C');		
			$pdf->Cell(150,5,'',1,0,'C');
			$pdf->Ln(5);	
		}
		$pdf->Ln(2);	
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(300,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(4);
		$pdf->Cell(300,8,'Dokter,',0,0,'C');	
		$pdf->Ln(13);
		$pdf->Cell(300,8,'('.$nmDok.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));
		$pdf->Output();
		
	}
	
}
?>
