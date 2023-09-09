<?php
class DaftarCariDataPdf extends SimakConf
{   
	public function onLoad($param)
	{	
		
		$cariCM=$this->Request['cariCM'];
		$cariNama=$this->Request['cariNama'];
		$tipeCari=$this->Request['tipeCari'];
		$cariAlamat=$this->Request['cariAlamat'];
		$urutBy=$this->Request['urutBy'];
		$Company=$this->Request['Company'];	
		
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
				   a.alamat, 
				   b.nama AS kelompok, 						   
				   b.id AS kontrakID,						   
				   c.nama AS kabupaten
				   FROM tbd_pasien a,
						tbm_kelompok b,
						tbm_kabupaten c,
						tbm_perusahaan d 
				   WHERE
						a.kelompok=b.id
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
				
			if($Company <> '' )
				$sql .= "AND a.perusahaan = '$Company' ";	
			
			if($urutBy <> '' )
				$sql .= "AND a.kelompok = '$urutBy'";						
			
			$sql .= " GROUP BY a.cm ";	
			
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
				
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5,'HASIL PENCARIAN DATA UMUM PASIEN','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariData'));			
			
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(14,10,'No.','RLT',0,'C');
			$pdf->Cell(17,10,'No. RM','RLT',0,'C');		
			$pdf->Cell(40,10,'Nama Lengkap','RLT',0,'C');
			$pdf->Cell(45,10,'Alamat','RLT',0,'C');
			$pdf->Cell(25,10,'Jenis Kelamin','RLT',0,'C');
			$pdf->Cell(20,5,'Kelompok','RLT',0,'C');
			$pdf->Cell(32,10,'Kabupaten/Kota','RLT',0,'C');
			$pdf->Ln(5);
			$pdf->Cell(14,5,'','RLB',0,'C');
			$pdf->Cell(17,5,'','RLB',0,'C');
			$pdf->Cell(40,5,'','RLB',0,'C');
			$pdf->Cell(45,5,'','RLB',0,'C');
			$pdf->Cell(25,5,'','RLB',0,'C');
			$pdf->Cell(20,5,'Pasien','RLB',0,'C');
			$pdf->Cell(32,5,'','RLB',0,'C');
			$pdf->Ln(5);
			$pdf->Cell(14,0.5,'','RLB',0,'C');
			$pdf->Cell(17,0.5,'','RLB',0,'C');
			$pdf->Cell(40,0.5,'','RLB',0,'C');
			$pdf->Cell(45,0.5,'','RLB',0,'C');
			$pdf->Cell(25,0.5,'','RLB',0,'C');
			$pdf->Cell(20,0.5,'','RLB',0,'C');
			$pdf->Cell(32,0.5,'','RLB',0,'C');
			$pdf->Ln(0.5);
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',9);
								
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$pdf->MultiCell(14,10,$j,'1','C');				
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+14);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$pdf->MultiCell(17,10,$row['cm'],'1','C');
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+17);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['nama']);			
				if($lebarKata<39)
				{
					$pdf->MultiCell(40,10,$row['nama'],'1','L');
				}
				else
				{
					$pdf->MultiCell(40,5,$row['nama'],'1','L');
				}
				
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+40);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['alamat']);			
				if($lebarKata<44)
				{
					$pdf->MultiCell(45,10,$row['alamat'],'1','L');
				}
				else
				{
					$pdf->MultiCell(45,5,$row['alamat'],'1','L');
				}
				
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+45);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$pdf->MultiCell(25,10,$row['jkel']==='0'? 'Laki-Laki':'Perempuan','1','C');
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+25);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$pdf->MultiCell(20,10,$row['kelompok'],'1','C');
				
				$pdf->setY($pdf->y0);
				$pdf->setX($pdf->x0+20);
				$pdf->y0=$pdf->GetY();
				$pdf->x0=$pdf->GetX();
				$pdf->MultiCell(32,10,$row['kabupaten'],'1','C');
				
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
			$pdf->Cell(0,5,'DATA UMUM PASIEN TIDAK DITEMUKAN','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariData'));			
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
