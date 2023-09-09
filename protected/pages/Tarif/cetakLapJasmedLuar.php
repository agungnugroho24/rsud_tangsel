<?php
class cetakLapJasmedLuar extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{	
		//$nmTable=$this->Request['table'];
		$dokter=$this->Request['dokter'];
		$kelompok=$this->Request['kelompok'];
		$perusahaan=$this->Request['perusahaan'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		
		$sql = "SELECT 
				  view_lap_jasmed_luar.cm,
				  view_lap_jasmed_luar.no_trans,
				  view_lap_jasmed_luar.tgl,
				  view_lap_jasmed_luar.id_opr,
				  view_lap_jasmed_luar.nm_opr,
				  view_lap_jasmed_luar.nm_pasien,					  
				  view_lap_jasmed_luar.id_peg_luar,
				  view_lap_jasmed_luar.posisi,
				  view_lap_jasmed_luar.tarif,
				  view_lap_jasmed_luar.st_bayar,
				  tbd_asisten_luar.nama AS nm_peg_luar,
				  tbd_pasien.kelompok
				FROM 
					view_lap_jasmed_luar
					INNER JOIN tbd_asisten_luar ON (view_lap_jasmed_luar.id_peg_luar = tbd_asisten_luar.id)
					INNER JOIN tbd_pasien ON (view_lap_jasmed_luar.cm = tbd_pasien.cm)
				WHERE
				  ( view_lap_jasmed_luar.tarif <> ''
				  OR view_lap_jasmed_luar.tarif <> NULL ) ";
			
		if($dokter <> '')			
			$sql .= "AND view_lap_jasmed_luar.id_peg_luar = '$dokter' ";
		
		if($kelompok <> '')			
			$sql .= "AND tbd_pasien.kelompok = '$kelompok' ";
					
		if($tgl <> '')			
			$sql .= "AND view_lap_jasmed_luar.tgl = '$tgl' ";
			
		if($tglawal <> '' AND $tglakhir <> '')			
			$sql .= "AND view_lap_jasmed_luar.tgl BETWEEN '$tglawal' AND '$tglakhir' ";							
		
		if($bln <> '')			
			$sql .= "AND MONTH(view_lap_jasmed_luar.tgl) = '$bln' AND YEAR(view_lap_jasmed_luar.tgl)='$thn' ";	
			
				
		$pdf=new reportKeuangan('L','mm','a4');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetMargins(10,15,10);
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
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'LAPORAN JASA MEDIS','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',10);
				
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.AsistenLuarRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok)
		{
			$pdf->Cell(100,5,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
				
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',10);		
		$pdf->Cell(50,5,'Nama',1,0,'C');
		$pdf->Cell(50,5,'Jabatan',1,0,'C');		
		$pdf->Cell(20,5,'CM',1,0,'C');	
		$pdf->Cell(40,5,'Tanggal',1,0,'C');	
		$pdf->Cell(50,5,'Nama Operasi',1,0,'C');
		$pdf->Cell(40,5,'Tarif',1,0,'C');
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		foreach($arrData as $row)
		{
			
			$pdf->SetFont('Arial','',9);	
			//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
			
			$pdf->Cell(50,5,$row['nm_peg_luar'],1,0,'C');
			$pdf->Cell(50,5,$row['posisi'],1,0,'C');		
			$pdf->Cell(20,5,$row['cm'],1,0,'C');	
			$pdf->Cell(40,5,$this->convertDate($row['tgl'],'3'),1,0,'C');	
			$pdf->Cell(50,5,$row['nm_opr'],1,0,'C');
			$pdf->Cell(40,5,'Rp. '.number_format($row['tarif'],'2',',','.'),1,0,'R');
			
			$tot_tarif += $row['tarif'];
			//$totProfit += $profit;
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(210,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(40,5,'Rp. '.number_format($tot_tarif,2,',','.'),1,0,'R');
		
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(400,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(100,8,'Penerima,',0,0,'C');
		$pdf->Cell(200,8,'Keuangan,',0,0,'C');	
		
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','BU',9);	
		$pdf->Cell(100,8,'('.AsistenLuarRecord::finder()->findByPk($dokter)->nama.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapJasmedLuar'));
		$pdf->Cell(200,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapJasmedLuar'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(100,8,'',0,0,'C');
		$pdf->Cell(200,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
	}
}
?>