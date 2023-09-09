<?php
class cetakStokAset extends SimakConf
{
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		$tmpVar2=$this->authApp('7');
		if($tmpVar == "False" && $tmpVar2 == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		 if (!isset($this->Session['cetakStokSql'])) 				
		{
			$this->Response->redirect($this->Service->constructUrl('Asset.MasterStokAset'));
		}
	 }
	 
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakStokSql'];
		
		$sql .= ' ORDER BY nm_barang';
					
		//$session->remove('cetakStokSql');
			
		$tujuan=$this->Request['tujuan'];
		$jnsBarang=$this->Request['jnsBarang'];
		$kelompokBarang=$this->Request['kelompokBarang'];
		
		//$tipe=$this->Request['tipe'];
		$tipeCetak=$this->Request['tipeCetak'];
		
		
		if($tipeCetak != '1')
		{
			$pdf=new reportAdmBarang('P','mm','f4');
		}
		else
		{
			$pdf=new reportAdmBarang('L','mm','f4');
		}
		
		
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
		$pdf->Cell(0,5,'LAPORAN STOK ASET','0',0,'C','',$this->Service->constructUrl('Asset.MasterStokAset'));		
		
		//$pdf->Ln(5);			
		//$pdf->MultiCell(0,5,$sql,'0',0,'L');
		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		
		if($tujuan <> '')
		{
			$pdf->Cell(50,5,'Tujuan : '.DestinasiAsetRecord::finder()->findByPk($tujuan)->nama,0,0,'L');
		}
		
		if($jnsBarang <> '')
		{
			if($jnsBarang == '1')
				$pdf->Cell(50,5,'Jenis Barang : Barang Bergerak',0,0,'L');
			if($jnsBarang == '2')
				$pdf->Cell(50,5,'Jenis Barang : Barang Tidak Bergerak',0,0,'L');	
		}
		
		if($kelompokBarang <> '')
		{
			$pdf->Ln(5);
			$pdf->Cell(50,5,'Kelompok : '.AssetKelompokRecord::finder()->findByPk($kelompokBarang)->nama,0,0,'L');
		}
		
		$pdf->Ln(5);		
		
		
		if($tujuan <> '')
		{
			$pdf->SetFont('Arial','B',9);
			
			$pdf->SetWidths(array(10,55,35,35,20,20,20));
			$pdf->SetAligns(array('C','C','C','C','C','C','C'));
			
			$col1 = 'No.';
			$col2 = 'Nama Aset';
			$col3 = 'Harga';
			$col4 = 'Harga Depresiasi';
			$col5 = 'Depresiasi (%)';
			$col6 = 'Tahun Pengadaan';
			$col7 = 'Jumlah';
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7));
			
			$pdf->Ln(1);
			
			$pdf->SetFont('Arial','',9);	
			$pdf->SetAligns(array('C','L','R','R','R','C','R'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',9);	
				
				$col1 = $j;
				$col2 = $row['nm_barang'];
				$col3 = number_format(floatval($row['hrg_netto']),2,',','.');
				$col4 = number_format(floatval($row['hrg_depresiasi']),2,',','.');
				$col5 = $row['depresiasi'];
				$col6 = $row['thn_pengadaan'];
				$col7 = number_format($row['jumlah'],0,',','.');
				
				$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7));
			}	
		}
		else
		{
			$pdf->SetFont('Arial','B',9);
			
			$pdf->SetWidths(array(15,150,30));
			$pdf->SetAligns(array('C','C','C'));
			
			$col1 = 'No.';
			$col2 = 'Nama Aset';
			$col3 = 'Jumlah Global';
			
			$pdf->Row(array($col1,$col2,$col3));
			
			$pdf->Ln(1);
			
			$pdf->SetFont('Arial','',9);		
			$pdf->SetAligns(array('C','L','R'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',9);	
				
				$col1 = $j;
				$col2 = $row['nm_barang'];
				$col3 = number_format($row['jumlah'],0,',','.');
				
				$pdf->Row(array($col1,$col2,$col3));
			}	
		}
		
		/*
		if($tipeCetak != '1')
		{
			$pdf->SetFont('Arial','B',9);
			
			$pdf->SetWidths(array(15,40,20,20,20,20,20,15,25));
			$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
			
			$col1 = 'No.';
			$col2 = 'Nama Aset';
			$col3 = 'Harga Netto';
			$col4 = 'Harga PPn';
			$col5 = 'Hrg. Jual (Umum)';
			$col6 = 'Hrg. Jual (Asuransi)';
			$col7 = 'Hrg. Jual (Jamper)';
			$col8 = 'Jumlah';
			$col9 = 'Satuan';
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9));
			
			$pdf->Ln(1);
			
			$pdf->SetFont('Arial','',9);
			
			//$pdf->SetWidths(array(15,40,20,20,20,20,30));
			$pdf->SetAligns(array('C','L','R','R','R','R','R','R','L'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$idBarang = $row['kode'];
				$hrgPpn = $row['hrg'];
				
				$idKelompokMargin = ObatRecord::finder()->findByPk($idBarang)->kel_margin;
				
				$persenMarginAsuransi = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_asuransi / 100;
				$persenMarginJamper = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_jamper / 100;
				$persenMarginUmum = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase / 100;
				
				$hargaJualUmum = $hrgPpn * (1 + $persenMarginUmum);
				$hargaJualAsuransi = $hrgPpn * (1 + $persenMarginAsuransi);
				$hargaJualJamper = $hrgPpn * (1 + $persenMarginJamper);
			
				$j += 1;
				$pdf->SetFont('Arial','',9);	
				
				$col1 = $j;
				$col2 = $row['nama'];
				$col3 = number_format($row['hrg_netto'],2,',','.');
				$col4 = number_format($row['hrg'],2,',','.');
				$col5 = number_format($hargaJualUmum,2,',','.');
				$col6 = number_format($hargaJualAsuransi,2,',','.');
				$col7 = number_format($hargaJualJamper,2,',','.');
				$col8 = $row['jml_stok_total'];	
				$col9 = SatuanObatRecord::finder()->findByPk($row['sat'])->nama;	
				
				$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9));
				$grandTot +=$row['untung'];
				
			}	
		}
		else
		{
			$pdf->SetFont('Arial','B',8);
			
			$pdf->SetWidths(array(15,27,17,17,17,17,17,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,12,16));
			$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
			
			$col1 ='No.';
			$col2 ='Obat';
			$col3 ='Hrg. Netto';
			$col4 ='Hrg. PPn';
			$col5 ='Hrg. Jual (Umum)';
			$col6 ='Hrg. Jual (Asr)';
			$col7 ='Hrg. Jual (Jamper)';
			$col8 ='Jml Global';
			$col9 ='Gud. Farmasi';
			$col10 ='Apotik Sentral';
			$col11 ='Poli 1';
			$col12 ='Poli 2';
			$col13 ='Poli 3';
			$col14 ='Poli 4';
			$col15 ='Poli 5';
			$col16 ='UGD';
			$col17 ='OK';
			$col18 ='vk';
			$col19 ='R.Bayi';
			$col20 ='Nurse St.A';
			$col21 ='Nurse St.B';
			$col22 ='Satuan';
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12,$col13,$col14,$col15,$col16,$col17,$col18,$col19,$col20,$col21,$col22));
			
			//$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',7);
			
			//$pdf->SetWidths(array(15,30,20,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,20));
			$pdf->SetAligns(array('C','L','R','R','R','R','R','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$idBarang = $row['kode'];
				$hrgPpn = $row['hrg'];
				
				$idKelompokMargin = ObatRecord::finder()->findByPk($idBarang)->kel_margin;
				
				$persenMarginAsuransi = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_asuransi / 100;
				$persenMarginJamper = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_jamper / 100;
				$persenMarginUmum = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase / 100;
				
				$hargaJualUmum = $hrgPpn * (1 + $persenMarginUmum);
				$hargaJualAsuransi = $hrgPpn * (1 + $persenMarginAsuransi);
				$hargaJualJamper = $hrgPpn * (1 + $persenMarginJamper);
				
				$j += 1;
				$pdf->SetFont('Arial','',7);	
				
				$col1 = $j;
				$col2 = $row['nama'];
				$col3 = number_format($row['hrg_netto'],2,',','.');
				$col4 = number_format($row['hrg'],2,',','.');
				$col5 = number_format($hargaJualUmum,2,',','.');
				$col6 = number_format($hargaJualAsuransi,2,',','.');
				$col7 = number_format($hargaJualJamper,2,',','.');
				$col8 = $row['jml_stok_total'];	
				$col9 = $row['stok_gudang'];	
				$col10 = $row['stok_apotik'];	
				$col11 = $row['stok_poliklinik1'];	
				$col12 = $row['stok_poliklinik2'];	
				$col13 = $row['stok_poliklinik3'];	
				$col14 = $row['stok_poliklinik4'];	
				$col15 = $row['stok_poliklinik5'];	
				$col16 = $row['stok_ugd'];	
				$col17 = $row['stok_ok'];	
				$col18 = $row['stok_vk'];	
				$col19 = $row['stok_ruang_bayi'];	
				$col20 = $row['stok_nurse_a'];	
				$col21 = $row['stok_nurse_b'];
				$col22 = SatuanObatRecord::finder()->findByPk($row['sat'])->nama;	
				
				$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12,$col13,$col14,$col15,$col16,$col17,$col18,$col19,$col20,$col21,$col22));
				$grandTot +=$row['untung'];
				
			}	
		}
		*/
						
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'Petugas,',0,0,'C');	
		$pdf->Ln(10);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(125,8,'',0,0,'L');
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Asset.MasterStokAset'));		
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
