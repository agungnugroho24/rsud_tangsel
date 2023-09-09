<?php
class cetakLapPenjualanFeeDokter extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$cariNama=$this->Request['cariByNama'];
		$cariID=$this->Request['cariByID'];
		$cariGol=$this->Request['cariByGol'];
		$cariJenis=$this->Request['cariByJenis'];
		$cariKlas=$this->Request['cariByKlas'];
		$cariDerivat=$this->Request['cariByDerivat'];
		$cariPbf=$this->Request['cariByPbf'];
		$cariProd=$this->Request['cariByProd'];
		$cariSat=$this->Request['cariBySat'];
		$sumber=$this->Request['sumber'];			
		$tujuan=$this->Request['tujuan'];
		$klinik=$this->Request['klinik'];
		$dokter=$this->Request['dokter'];
		$kelompok=$this->Request['kelompok'];
		$operatorKasir=$this->Request['operatorKasir'];
		$kontrak=$this->Request['kontrak'];
		$tgl=$this->Request['tgl'];
		$tglAwal=$this->Request['tglAwal'];
		$tglAkhir=$this->Request['tglAkhir'];
		$bulan=$this->Request['bulan'];
		$tahun=$this->Request['tahun'];
		$tipe=$this->Request['tipe'];
		$rawat=$this->Request['rawat'];
		$modeBayar=$this->Request['modeBayar'];		
		$modeLap=$this->Request['modeLap'];		
		$periode=$this->Request['periode'];
		$tableTmp=$this->Request['tableTmp'];
		$namaTabelObat=$this->Request['namaTabelObat'];
		
		
		
		/*
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];	
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		*/
		$noKwitansi = substr($noTrans,6,6).'/'.'APT-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		//Update tabel tbt_pembayaran
		/*
		$byrRecord=BayarRecord::finder()->findByPk($noTrans);
		$byrRecord->status_pembayaran='2';//Sudah dibayar!
		$byrRecord->tgl_bayar=date('Y-m-d h:m:s');
		$byrRecord->no_kwitansi=$noKwitansi;
		$byrRecord->operator=$nipTmp;
		$byrRecord->Save();//Update!
		*/
		
		$pdf=new reportKwitansi('P','mm','a4');
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
		
		$pdf->Cell(0,5,'LAPORAN JASA RESEP DOKTER','0',0,'C','',$this->Service->constructUrl('Apotik.LapPenjualanFeeDokter'));	
		
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Cell(30,5,'','0',0,'L');
		
		$pdf->Cell(20,5,'Waktu Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,date('G:i').' WIB','0',0,'L');
		
		$pdf->Ln(5);
		
		$pdf->Cell(15,5,'Dokter','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
			
		if($dokter)
			$pdf->Cell(80,5,PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');			
		else
			$pdf->Cell(80,5,'-','0',0,'L');			
		
		$pdf->Cell(30,5,'','0',0,'L');
		
		$pdf->Cell(20,5,'Pasien','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		
		if($rawat == '0')
		{			
			$pdf->Cell(80,5,'Rawat Jalan','0',0,'L');
		}
		elseif($rawat == '1')
		{
			$pdf->Cell(80,5,'Rawat Inap','0',0,'L');
		}
		elseif($rawat == '2')
		{
			$pdf->Cell(80,5,'Pasien Luar','0',0,'L');
		}
		
		$pdf->Ln(5);
		
		$pdf->Cell(15,5,'Tujuan','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(80,5,DesFarmasiRecord::finder()->findByPk($tujuan)->nama,'0',0,'L');
		
		$pdf->Cell(30,5,'','0',0,'L');
		$pdf->Cell(20,5,'Kel. Pasien','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		
		if($kelompok)
			$pdf->Cell(80,5,KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');			
		else
			$pdf->Cell(80,5,'-','0',0,'L');	
			
		/*
		if($operatorKasir != '')
		{
			$pdf->Cell(30,5,'','0',0,'L');
			$pdf->Cell(20,5,'Operator Kasir','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(50,5,UserRecord::finder()->find('nip=?',$operatorKasir)->real_name,'0',0,'L');
			$pdf->Ln(5);
		}		
		*/
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',9);
		
		$pdf->SetWidths(array(10,20,30,50,50,30,25));
		$pdf->SetAligns(array('C','C','C','C','C','C','C'));
		
		$col1 = 'No.';
		$col2 = 'Tanggal';
		$col3 = 'No. Resep';
		$col4 = 'Nama Obat/Alkes';
		$col5 = 'Dokter';
		$col6 = 'Fee Dokter';
		//$col7 = 'Fee RS';
		
		$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6));
		
		$pdf->Ln(1);
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapPenjualanFeeDokterSql'];
		$sql .= " ORDER BY tgl ";
		
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		$n=0;
		
		$pdf->SetAligns(array('C','C','C','C','C','R','R'));
		
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);	
			
			
		
			$col1 = 'No.';
			$col2 = 'Tanggal';
			$col3 = 'No. Resep';
			$col4 = 'Nama Obat/Alkes';
			$col5 = 'Dokter';
			$col6 = 'Fee Dokter';
			//$col7 = 'Fee RS';
			
			//$pdf->Row(array($j,$this->convertDate($row['tgl'],'1'),$row['no_reg'],$row['nm_obat'],$row['nm_dokter'],number_format($row['jml_fee_dokter'],'2',',','.'),number_format($row['jml_fee_rs'],'2',',','.')));
			$pdf->Row(array($j,$this->convertDate($row['tgl'],'1'),$row['no_reg'],$row['nm_obat'],$row['nm_dokter'],number_format($row['jml_fee_dokter'],'2',',','.')));	
			
			$grandTotFeeDokter += $row['jml_fee_dokter'];
			$grandTotFeeRs += $row['jml_fee_rs'];
			//$grandTotRJR += $row['r_item'] + $row['r_racik'];
		}
		/*	
		if ($tableTmp)
		{			
			$sql = "DROP TABLE $tableTmp";
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		*/			
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(65,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		
		$pdf->SetFont('Arial','B',9);	
		
		$pdf->Cell(95,5,'GRAND TOTAL ',0,0,'R');
			
		//$pdf->Cell(30,5,'Rp ' .number_format($totBeli,2,',','.'),1,0,'R');	
		//$pdf->Cell(30,5,'Rp ' .number_format($totJual,2,',','.'),1,0,'R');		
		$pdf->Cell(30,5,number_format($grandTotFeeDokter,2,',','.'),1,0,'R');		
		//$pdf->Cell(25,5,number_format( $grandTotFeeRs,2,',','.'),1,0,'R');
		//$pdf->Cell(30,5,number_format( $grandTotRJR,2,',','.'),1,0,'R');
							
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPenjualanFeeDokter'));		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		//$pdf->MultiCell(53,8,$sql,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>