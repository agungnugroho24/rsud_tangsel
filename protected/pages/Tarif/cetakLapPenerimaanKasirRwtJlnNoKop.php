<?php
class cetakLapPenerimaanKasirRwtJlnNoKop extends SimakConf
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
		$kasir=$this->Request['kasir'];
		$poli=$this->Request['poli'];
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
					  view_lap_terima_rwtjln.cm,
					  view_lap_terima_rwtjln.no_trans,
					  view_lap_terima_rwtjln.nm_pasien,
					  view_lap_terima_rwtjln.nm_poli,
					  view_lap_terima_rwtjln.nm_dokter,
					  view_lap_terima_rwtjln.id_poli,
					  view_lap_terima_rwtjln.id_dokter,
					  view_lap_terima_rwtjln.kelompok,
					  SUM(view_lap_terima_rwtjln.biaya) AS total,
					  view_lap_terima_rwtjln.kasir,
					  view_lap_terima_rwtjln.tgl_kasir,
					  view_lap_terima_rwtjln.discount
					FROM
					  view_lap_terima_rwtjln ";					  			

			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE view_lap_terima_rwtjln.cm <> '' ";	
			}
			else
			{
				$sql .= "WHERE view_lap_terima_rwtjln.cm <> '' ";	
			}
			
			/*
			if($this->getViewState('cariByNama') <> '')	
			{
				$cariNama=$this->getViewState('cariByNama');
				$sql .= "AND tbt_kasir_rwtjln.operator = '$cariNama' ";		
			}
			*/
			
			if($kasir <> '')	
			{
				$sql .= "AND view_lap_terima_rwtjln.kasir = '$kasir' ";		
			}	
						
			if($poli <> '')
			{	
				$sql .= "AND view_lap_terima_rwtjln.id_poli = '$poli' ";
			}	
			if($dokter <> '')
			{
				$sql .= "AND view_lap_terima_rwtjln.id_dokter = '$dokter' ";
			}
			
			if($kelompok <> '')
			{
				$sql .= "AND view_lap_terima_rwtjln.kelompok = '$kelompok' ";
			}
			if($perushaan <> '')
			{
				$sql .= "AND tbd_pasien.perusahaan = '$perushaan' ";		
			}
			if($tgl <> '')
			{
				$sql .= "AND view_lap_terima_rwtjln.tgl_kasir = '$tgl' ";
			}
			if($tglawal <> '' AND $tglakhir <>'')
			{
				$sql .= "AND view_lap_terima_rwtjln.tgl_kasir BETWEEN '$tglawal' AND '$tglakhir' ";
			}
			
			if($bln <> '' AND $thn <>'')
			{
				$sql .= "AND MONTH (view_lap_terima_rwtjln.tgl_kasir)='$bln' AND YEAR(view_lap_terima_rwtjln.tgl_kasir)='$thn' ";
			}
			
			$sql .= " GROUP BY view_lap_terima_rwtjln.no_trans ";	
					
		$pdf=new reportKeuangan('L','mm','a4');
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'LAPORAN PENERIMAAN KASIR RAWAT JALAN','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		if($kasir)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,'0',0,'L');
		}
		
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok)
		{
			$pdf->Cell(100,5,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
		
		$pdf->Ln(5);	
		if($poli)
		{
			$pdf->Cell(100,5,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,'0',0,'L');
		}
		
		if($perusahaan)
		{
			$pdf->Cell(100,5,'Perusahaan : '.PerusahaanRecord::finder()->findByPk($perusahaan)->nama,'0',0,'L');
		}
				
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',10);		
		$pdf->Cell(30,5,'Tanggal',1,0,'C');
		$pdf->Cell(30,5,'No Transaksi',1,0,'C');		
		$pdf->Cell(15,5,'CM',1,0,'C');		
		$pdf->Cell(50,5,'Nama',1,0,'C');
		$pdf->Cell(30,5,'Poliklinik',1,0,'C');	
		$pdf->Cell(50,5,'Dokter',1,0,'C');	
		$pdf->Cell(30,5,'Biaya',1,0,'C');	
		$pdf->Cell(15,5,'Disc',1,0,'C');	
		$pdf->Cell(30,5,'Total',1,0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			
			$pdf->SetFont('Arial','',9);	
			//$pdf->Cell(30,5,'30 Desember 2009',1,0,'C');
			$pdf->Cell(30,5,$this->convertDate($row['tgl_kasir'],'3'),1,0,'C');
			$pdf->Cell(30,5,$row['no_trans'],1,0,'C');						
			$pdf->Cell(15,5,$row['cm'],1,0,'C');		
			$pdf->Cell(50,5,$row['nm_pasien'],1,0,'C');		
			$pdf->Cell(30,5,$row['nm_poli'],1,0,'C');		
			$pdf->Cell(50,5,$row['nm_dokter'],1,0,'C');		
			$pdf->Cell(30,5,'Rp. '.number_format($row['total'],2,',','.'),1,0,'R');		
			$pdf->Cell(15,5,$row['discount'].' %',1,0,'C');
			
			if($row['discount'] > 0)
			{
				$biaya = $row['total'];
				$disc = $row['discount'];
				$biayaDisc = $biaya - ($biaya * $disc / 100);
				
				$pdf->Cell(30,5,'Rp. '.number_format($this->bulatkan($biayaDisc),2,',','.'),1,0,'R');	
				
				$tot += $this->bulatkan($biayaDisc);
			}
			else
			{
				$pdf->Cell(30,5,'Rp. '.number_format($row['total'],2,',','.'),1,0,'R');	
				
				$tot += $row['total'];
			}
			
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(250,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(30,5,'Rp. '.number_format($tot,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
				
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(450,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(450,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtJln'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>