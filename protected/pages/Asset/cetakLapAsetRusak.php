<?php
class cetakLapAsetRusak extends SimakConf
{
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		 if (!isset($this->Session['cetakLapAsetRusak'])) 				
		{
			$this->Response->redirect($this->Service->constructUrl('Asset.LapAsetRusak'));
		}
	 }
	 
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapAsetRusak'];
		$sql .= ' ORDER BY no_bbk';
					
		//$session->remove('cetakStokSql');
			
		$asal=$this->Request['asal'];
		$jnsBarang=$this->Request['jnsBarang'];
		$periode=$this->Request['periode'];
		$modeDistribusi=$this->Request['modeDistribusi'];
		
		
		$pdf=new reportAdmBarang('P');
		
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
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		
		$url = $this->Service->constructUrl('Asset.LapAsetRusak');
		
		if($modeDistribusi == '0')
			$pdf->Cell(0,5,'LAPORAN MUTASI ASET','0',0,'C','',$url);
		if($modeDistribusi == '1')
			$pdf->Cell(0,5,'LAPORAN MUTASI PENGHAPUSAN BARANG','0',0,'C','',$url);
		if($modeDistribusi == '2')
			$pdf->Cell(0,5,'LAPORAN MUTASI PELEPASAN HAK','0',0,'C','',$url);
		if($modeDistribusi == '3')
			$pdf->Cell(0,5,'LAPORAN MUTASI PINJAM PAKAI','0',0,'C','',$url);
		if($modeDistribusi == '4')
			$pdf->Cell(0,5,'LAPORAN MUTASI PENYEWAAN','0',0,'C','',$url);
		if($modeDistribusi == '5')
			$pdf->Cell(0,5,'LAPORAN MUTASI PENGGUNA USAHA','0',0,'C','',$url);
		if($modeDistribusi == '6')
			$pdf->Cell(0,5,'LAPORAN MUTASI SWADANA','0',0,'C','',$url);
		
		$pdf->Ln(5);	
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(0,5,$periode,0,0,'C');		
		//$pdf->MultiCell(0,5,$sql,'0',0,'L');
		
		$pdf->Ln(5);
		
		
		if($asal <> '')
		{
			$pdf->Cell(50,5,'Asal Barang  : '.$asal,0,0,'L');
			$pdf->Ln(5);	
		}
		
		if($jnsBarang <> '')
		{
			$pdf->Cell(50,5,'Jenis Barang : '.$jnsBarang,0,0,'L');
			$pdf->Ln(5);	
		}
		/*
		if($tipe == '0')
		{			
			$pdf->Cell(50,5,'Tipe Obat : Generik',0,0,'L');
		}
		elseif($tipe == '1')
		{			
			$pdf->Cell(50,5,'Tipe Obat : Non Generik',0,0,'L');
		}
		*/
			
		$pdf->SetFont('Arial','B',9);
			
		$pdf->SetWidths(array(10,20,40,30,25,25,25,15,15,25));
		$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
		
		$col1 = 'No.';
		$col2 = 'Tgl.Mutasi';
		$col3 = 'Nama';
		$col4 = 'Asal';
		$col5 = 'Tujuan';
		$col6 = 'Petugas';
		$col7 = 'Penerima';
		$col8 = 'Jumlah';
		
		$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8));
		
		$pdf->Ln(1);
		
		$pdf->SetFont('Arial','',8);
		
		//$pdf->SetWidths(array(15,40,20,20,20,20,30));
		$pdf->SetAligns(array('C','C','L','C','C','C','C','R'));
		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			
			$col1 = $j;
			$col2 = $this->convertDate($row['tgl'],'1');
			$col3 = $row['nm_barang'];
			$col4 = $row['nm_asal'];
			$col5 = $row['nm_tujuan'];
			$col6 = $row['petugas'];
			$col7 = $row['penerima'];
			$col8 = number_format($row['jumlah'],'0',',','.');
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8));
			//$grandTot +=$row['untung'];
		}	
		
						
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'Petugas,',0,0,'C');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(125,8,'',0,0,'L');
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$url);		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(125,8,'',0,0,'L');
		$pdf->Cell(80,8,'NIP. '.$nipTmp,0,0,'C');
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>