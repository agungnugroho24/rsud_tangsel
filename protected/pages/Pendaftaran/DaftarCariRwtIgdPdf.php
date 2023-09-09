<?php
class DaftarCariRwtIgdPdf extends SimakConf
{   
	
	public function onLoad($param)
	{	
		
		$cariCM=$this->Request['cariCM'];
		$cariNama=$this->Request['cariNama'];
		$tipeCari=$this->Request['tipeCari'];
		$cariAlamat=$this->Request['cariAlamat'];
		$urutBy=$this->Request['urutBy'];
		$Company=$this->Request['Company'];	
		$cariTgl=$this->Request['cariTgl'];
		$cariBln=$this->Request['cariBln'];
		$cariByDokter=$this->Request['cariByDokter'];
		//$operator=$this->User->IsUserName;
		//$nipTmp=$this->User->IsUserNip;		
		//$nmTable=$this->Request['table'];
		//$noKwitansi = substr($noTrans,6,6).'/'.'LAB-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
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
		
		$pdf=new reportCari('P','mm','a4');
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
		$pdf->Ln(10);		
		
		//$pdf->Cell(0,5,$cariCM.' '.$cariNama.' '.$tipeCari.' '.$cariAlamat.' '.$urutBy.' '.$Company,'0',0,'C');
		//$pdf->Ln(5);
		
		$sql = "SELECT a.cm,
						   a.nama, 
						   a.jkel,
						   g.nama AS cara_masuk, 
						   f.nama AS nm_dokter, 						   
						   b.id AS kontrakID						   
						   FROM tbd_pasien a,
								tbm_kelompok b,
								tbm_kabupaten c,
								tbm_perusahaan d,
								tbt_igd e,	
								tbd_pegawai f, 
								tbm_cara_masuk g  
						   WHERE
								a.kelompok=b.id
								AND a.cm=e.cm															
								AND f.id=e.dokter
								AND e.cr_masuk=g.id
								AND a.kelompok=b.id												
								AND a.kabupaten=c.id ";
			if($cariNama <> '')
							
				if($tipeCari != ''){
					$sql .= "AND a.nama LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND a.nama LIKE '$cariNama%' ";
				}
			
			if($cariAlamat <> '')	
					$sql .= "AND a.alamat LIKE '$cariAlamat%' ";
							
			if($cariCM <> '')			
				$sql .= "AND a.cm = '$cariCM' ";
			
			if($cariByDokter <> '')			
				$sql .= "AND e.dokter = '$cariByDokter' ";		
			
			if($cariTgl <> '')
			{
				$tgl = $this->ConvertDate($cariTgl,'2');//Convert date to mysql
				$sql .= "AND e.tgl_visit = '$tgl' ";
			}	
			
			if($cariBln <> '')
			{
				$blnAkhir = $this->akhirBln($cariBln);
				$blnAwal = date('Y') . '-' . $cariBln . '-01';
				$sql .= "AND e.tgl_visit BETWEEN '$blnAwal' AND '$blnAkhir' ";		
			}
			
			if($urutBy <> '')
				$sql .= "AND a.kelompok = '$urutBy' ";
				
			if($Company <> '')
				$sql .= "AND a.perusahaan = '$Company' AND d.id_kel = '$urutBy'";						
			
			$sql .= " GROUP BY a.cm ";	
			
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
				
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5,'HASIL PENCARIAN DATA PASIEN IGD','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariRwtIgd'));			
			
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(15,10,'No.','1',0,'C');
			$pdf->Cell(20,10,'No. RM','1',0,'C');		
			$pdf->Cell(50,10,'Nama Lengkap','1',0,'C');
			$pdf->Cell(25,10,'Jenis Kelamin','1',0,'C');
			$pdf->Cell(30,10,'Cara Masuk','1',0,'C');
			$pdf->Cell(50,10,'Dokter','1',0,'C');
			$pdf->Ln(10);
			$pdf->Cell(15,0.5,'','1',0,'C');
			$pdf->Cell(20,0.5,'','1',0,'C');
			$pdf->Cell(50,0.5,'','1',0,'C');
			$pdf->Cell(25,0.5,'','1',0,'C');
			$pdf->Cell(30,0.5,'','1',0,'C');
			$pdf->Cell(50,0.5,'','1',0,'C');
			$y=$pdf->GetY();
			$x=$pdf->GetX();
			$pdf->Ln(0.5);
			
			
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',9);
							
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(15,10,$j,'1','C');				
				
				$pdf->setY($y);
				$pdf->setX($x+15);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(20,10,$row['cm'],'1','C');
				
				$pdf->setY($y);
				$pdf->setX($x+20);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['nama']);			
				if($lebarKata<49)
				{
					$pdf->MultiCell(50,10,$row['nama'],'1','L');
				}
				else
				{
					$pdf->MultiCell(50,5,$row['nama'],'1','L');
				}
				
				$pdf->setY($y);
				$pdf->setX($x+50);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(25,10,$row['jkel']==='0'? 'Laki-Laki':'Perempuan','1','C');
				
				$pdf->setY($y);
				$pdf->setX($x+25);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(30,10,$row['cara_masuk'],'1','C');
				
				$pdf->setY($y);
				$pdf->setX($x+30);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['nm_dokter']);			
				if($lebarKata<49)
				{
					$pdf->MultiCell(50,10,$row['nm_dokter'],'1','C');
				}
				else
				{
					$pdf->MultiCell(50,5,$row['nm_dokter'],'1','C');
				}
				
				if($pdf->GetY()>250)
				{
					$pdf->AddPage();
					$pdf->setY(10);
					$pdf->setX(10);
				}
			}
		}
		else
		{
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(0,5,'DATA PASIEN IGD TIDAK DITEMUKAN','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariRwtIgd'));			
			$pdf->Ln(7);
		}
		
		

		/*
		$pdf->y0=$pdf->GetY();
		$pdf->setY($pdf->y0+10);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Bandung, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Lab.cetakHasilLab') . '&purge=1&nmTable=' . $nmTable);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nip,0,0,'C');	
		*/
										
		$pdf->Output();
	}
}
?>
