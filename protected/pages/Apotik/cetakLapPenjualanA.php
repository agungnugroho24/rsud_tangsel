<?php
class cetakLapPenjualanA extends SimakConf
{
	public function onInit($param)
	{		
		parent::onInit($param);
		$this->getResponse()->setContentType("application/pdf");
	}		 
	 
	public function onPreRender($param)
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
		$modeNarkotika=$this->Request['modeNarkotika'];
		$totalkanJml=$this->Request['totalkanJml'];
		
		
		
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
		
		$pdf=new reportKwitansi('L','mm','a4');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(true,5);
		
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Ln(8);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,10,'           Telp. (021) 7404752','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		
		if($modeLap == '0') //laporan penjualan
		{
			if($modeNarkotika == '1') //laporan narkotika
			{
				$pdf->Cell(0,5,'LAPORAN PENJUALAN OBAT PSYKOTROPIKA/NARKOTIKA','0',0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));	
			}
			else
			{
				$pdf->Cell(0,5,'RINCIAN PENJUALAN OBAT/ALKES','0',0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));	
			}
				
		}
		elseif($modeLap == '1') //laporan /R
		{
			$pdf->Cell(0,5,'LAPORAN JASA RESEP & JASA RACIKAN OBAT','0',0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));	
		}
		
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Cell(100,5,'','0',0,'L');
		
		$pdf->Cell(20,5,'Waktu Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,date('G:i').' WIB','0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(15,5,'Kelompok','0',0,'L');
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
		elseif($rawat == '3')
		{
			$pdf->Cell(80,5,'Unit Internal','0',0,'L');
		}
		
		if($operatorKasir != '')
		{
			$pdf->Cell(100,5,'','0',0,'L');
			$pdf->Cell(20,5,'Operator Kasir','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(50,5,UserRecord::finder()->find('nip=?',$operatorKasir)->real_name,'0',0,'L');		
		}
		
		$pdf->Ln(5);
		
		if($rawat == '1')
		{	
			$pdf->Cell(15,5,'Transaksi','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			
			if($modeBayar == '0')
			{		
				$pdf->Cell(80,5,'Kredit','0',0,'L');
			}
			elseif($modeBayar == '1')
			{		
				$pdf->Cell(80,5,'Tunai','0',0,'L');
			}
			
			$pdf->Cell(120,5,'','0',0,'L');
			$pdf->Cell(20,5,'Tujuan','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(50,5,DesFarmasiRecord::finder()->findByPk($tujuan)->nama,'0',0,'L');	
		}
		else
		{
			$pdf->Cell(15,5,'Tujuan','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,DesFarmasiRecord::finder()->findByPk($tujuan)->nama,'0',0,'L');
		}
		
		if($klinik != '')
		{
			$pdf->Ln(5);
			$pdf->Cell(15,5,'Poliklinik','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,PoliklinikRecord::finder()->findByPk($klinik)->nama,'0',0,'L');
		}
		
		if($dokter != '')
		{
			$pdf->Ln(5);
			$pdf->Cell(15,5,'Dokter','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok != '')
		{
			$pdf->Ln(5);
			$pdf->Cell(15,5,'Kel. Pasien','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
		
		if($kontrak != '')
		{
			$pdf->Ln(5);
			$pdf->Cell(15,5,'Perusahaan','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,PerusahaanRecord::finder()->findByPk($kontrak)->nama,'0',0,'L');
		}
		
		//$klinik=$this->Request['klinik'];
		//$dokter=$this->Request['dokter'];
		//$kelompok=$this->Request['kelompok'];
		//$operatorKasir=$this->Request['operatorKasir'];
		//$kontrak=$this->Request['kontrak'];
		
		
		
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',9);
		
		if($modeLap == '0')//laporan penjualan
		{
			if($modeNarkotika == '1') //laporan narkotika
			{
				$pdf->Cell(10,5,'No.',1,0,'C');
				
				if($rawat == '3')				
					$pdf->Cell(55,5,'Ruangan',1,0,'C');
				else
					$pdf->Cell(55,5,'Pasien',1,0,'C');	
					
				$pdf->Cell(25,5,'No. RM',1,0,'C');
				$pdf->Cell(60,5,'Dokter',1,0,'C');
				$pdf->Cell(46,5,'Tgl.Penjualan ',1,0,'C');
				$pdf->Cell(60,5,'Obat',1,0,'C');
				$pdf->Cell(20,5,'Jumlah',1,0,'C');
				$pdf->Ln(6);
			}
			else
			{
				$pdf->SetWidths(array(10,20,60,35,40,15,25,25,25,25));
				$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
				
				if($rawat == '3')		
					$pdf->Row(array('No.','Tanggal','Obat','Ruangan','Apoteker','Jumlah','HNA','(HNA+PPn) * Jumlah','Total Jual','Pendapatan'));
				else
					$pdf->Row(array('No.','Tanggal','Obat','Pasien','Apoteker','Jumlah','HNA','(HNA+PPn) * Jumlah','Total Jual','Pendapatan'));
					
				//$pdf->Cell(25,5,'Uang /R',1,0,'C');
			}
		}
		elseif($modeLap == '1')//laporan /R
		{
			$pdf->Cell(10,5,'No.',1,0,'C');
			$pdf->Cell(25,5,'Tanggal',1,0,'C');
			$pdf->Cell(20,5,'RM',1,0,'C');
			
			if($rawat == '3')				
				$pdf->Cell(45,5,'Ruangan',1,0,'C');
			else
				$pdf->Cell(45,5,'Nama Pasein',1,0,'C');					
			
			
			if($rawat == '0') //rawat jalan
			{
				$pdf->Cell(30,5,'Poliklinik',1,0,'C');
			}
			
			$pdf->Cell(55,5,'Dokter',1,0,'C');
			$pdf->Cell(30,5,'/R',1,0,'C');
			$pdf->Cell(30,5,'/JR',1,0,'C');
			$pdf->Cell(30,5,'Total',1,0,'C');
			$pdf->Ln(6);
		}
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapPenjualanSql'];
		$sql .= " ORDER BY $namaTabelObat.id DESC";
		
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);		
			
			if($modeLap == '0') //laporan penjualan
			{
				$totalJual = $row['jual']*$row['jumlah'];
				$n =$row['racikan'];
				if($n == '1')
				{
					$flag = ' (Racikan)';
				}
				else if($n == '2')
				{
					$flag = ' (Imunisasi)';
				}	
				
				$t =$row['pasien'];
				/*
				if($t == $nmTemp)
				{
					$nmPas = ' - ';
				}
				else{
					$nmPas = $row['pasien'];
				}		
				*/		
				$nmPas = $row['pasien'];
				
				if($modeNarkotika == '1') //laporan narkotika
				{				
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(55,5,$nmPas,1,0,'L');
					$pdf->Cell(25,5,$row['cm'],1,0,'C');
					$pdf->Cell(60,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,1,0,'L');
					$pdf->Cell(46,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->Cell(60,5,$row['obat'] . $flag,1,0,'L');
					$pdf->Cell(20,5,$row['jumlah'],1,0,'R');
					$pdf->Ln(5);	
				}
				else
				{				
					$pdf->SetAligns(array('C','C','L','L','L','R','R','R','R','R'));
					
					$nmTemp = $row['pasien'];
					$pdf->Row(array($j,
													$this->convertDate($row['tgl'],'1'),
													$row['obat'].$flag,
													$nmPas,
													UserRecord::finder()->find('nip=?',$row['apoteker'])->real_name,
													$row['jumlah'],
													number_format($row['beli'],2,',','.'),
													number_format($row['beli_tot'],2,',','.'),
													number_format($row['jual_tot'],2,',','.'),
													number_format($row['profit'],2,',','.')));
				}		
				
				$grandTotJual +=$row['jual_tot'];
				$grandTot +=$row['profit'];
				$grandTotR +=$row['resep'];
				$grandTotJml += $row['jumlah'];
			}
			elseif($modeLap == '1') //laporan /R
			{				
				$pdf->Cell(10,5,$j.'.',1,0,'C');
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'1'),1,0,'C');
				$pdf->Cell(20,5,$row['cm'],1,0,'C');
				$pdf->Cell(45,5,$row['pasien'],1,0,'C');
				
				if($rawat == '0') //rawat jalan
				{
					$idPoli = RwtjlnRecord::finder()->findByPk($row['no_trans_rawat'])->id_klinik;
					$pdf->Cell(30,5,PoliklinikRecord::finder()->findByPk($idPoli)->nama,1,0,'C');
				}
				
				$pdf->Cell(55,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,1,0,'C');
				$pdf->Cell(30,5,number_format($row['r_item'],'2',',','.'),1,0,'R');
				$pdf->Cell(30,5,number_format($row['r_racik'],'2',',','.'),1,0,'R');
				$pdf->Cell(30,5,number_format($row['r_item'] + $row['r_racik'],'2',',','.'),1,0,'R');
				$pdf->Ln(5);			
				
				$grandTotR += $row['r_item'];
				$grandTotJR += $row['r_racik'];
				$grandTotRJR += $row['r_item'] + $row['r_racik'];
			}
			
			
		}
		/*	
		if ($tableTmp)
		{			
			$sql = "DROP TABLE $tableTmp";
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		*/			
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		
		$pdf->SetFont('Arial','B',9);	
		
		if($modeLap == '0') //laporan penjualan
		{
			if($modeNarkotika != '1') //laporan narkotika
			{
				if($totalkanJml=='1')
				{
					$pdf->Cell(85,5,'GRAND TOTAL',0,0,'R');	
					$pdf->Cell(15,5,number_format($grandTotJml,0,',','.'),1,0,'R');		
					$pdf->Cell(25,5,'',1,0,'R');		
					$pdf->Cell(25,5,'',1,0,'R');		
				}
				else
					$pdf->Cell(150,5,'GRAND TOTAL',0,0,'R');		
					
				//$pdf->Cell(30,5,'Rp ' .number_format($totBeli,2,',','.'),1,0,'R');	
				//$pdf->Cell(30,5,'Rp ' .number_format($totJual,2,',','.'),1,0,'R');		
				$pdf->Cell(25,5,number_format($grandTotJual,2,',','.'),1,0,'R');		
				$pdf->Cell(25,5,number_format( $grandTot,2,',','.'),1,0,'R');
				//$pdf->Cell(25,5,'Rp ' .number_format( $grandTotR,2,',','.'),1,0,'R');	
			}	
		}
		elseif($modeLap == '1') //laporan /R
		{
			if($rawat == '0') //rawat jalan
				$pdf->Cell(105,5,'GRAND TOTAL ',0,0,'R');	
			else
				$pdf->Cell(75,5,'GRAND TOTAL ',0,0,'R');
			
			//$pdf->Cell(30,5,'Rp ' .number_format($totBeli,2,',','.'),1,0,'R');	
			//$pdf->Cell(30,5,'Rp ' .number_format($totJual,2,',','.'),1,0,'R');		
			$pdf->Cell(30,5,number_format($grandTotR,2,',','.'),1,0,'R');		
			$pdf->Cell(30,5,number_format( $grandTotJR,2,',','.'),1,0,'R');
			$pdf->Cell(30,5,number_format( $grandTotRJR,2,',','.'),1,0,'R');	
		}
							
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));		
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