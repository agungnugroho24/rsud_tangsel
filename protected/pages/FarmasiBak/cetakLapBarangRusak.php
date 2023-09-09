<?php
class cetakLapBarangRusak extends SimakConf
{
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		 if (!isset($this->Session['cetakLapBarangRusak'])) 				
		{
			$this->Response->redirect($this->Service->constructUrl('Farmasi.LapBarangRusak'));
		}
	 }
	 
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapBarangRusak'];
		$sql .= ' ORDER BY no_bbk';
					
		//$session->remove('cetakStokSql');
			
		$asal=$this->Request['asal'];
		$jnsBarang=$this->Request['jnsBarang'];
		$periode=$this->Request['periode'];
		
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
		$pdf->Cell(0,5,'LAPORAN DISTRIBUSI BARANG RUSAK','0',0,'C','',$this->Service->constructUrl('Farmasi.LapBarangRusak'));		
		
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
			
		$pdf->SetWidths(array(15,25,50,30,25,25,25,15,25));
		$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
		
		$col1 = 'No.';
		$col2 = 'Tgl. Distribusi';
		$col3 = 'Nama';
		$col4 = 'Asal';
		$col5 = 'HNA + PPn';
		$col6 = 'Expired';
		$col7 = 'Jumlah';
		
		$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7));
		
		$pdf->Ln(1);
		
		$pdf->SetFont('Arial','',9);
		
		//$pdf->SetWidths(array(15,40,20,20,20,20,30));
		$pdf->SetAligns(array('C','C','L','C','R','C','R'));
		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);	
			
			$col1 = $j;
			$col2 = $this->convertDate($row['tgl'],'1');
			$col3 = $row['nm_obat'];
			$col4 = $row['nm_asal'];
			$col5 = number_format($row['hrg_ppn'],'2',',','.');
			$col6 = $this->convertDate($row['expired'],'1');
			$col7 = number_format($row['jumlah'],'0',',','.');
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7));
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
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.LapBarangRusak'));		
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