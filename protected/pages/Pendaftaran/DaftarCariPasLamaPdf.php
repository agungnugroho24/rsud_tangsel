<?php
class DaftarCariPasLamaPdf extends SimakConf
{   
	
	public function onLoad($param)
	{	
		
		$cariCM=$this->Request['cariCM'];
		$cariNama=$this->Request['cariNama'];
		$tipeCari=$this->Request['tipeCari'];
		$cariAlamat=$this->Request['cariAlamat'];
		
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
						   a.jkel AS jenkel,
						   a.alamat,
						   a.kecamatan
						   FROM du_pasien a,
						   		tbm_kecamatan b
						  WHERE a.kecamatan<>'kecamatan' ";
								
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
			
			$sql .= " GROUP BY a.cm ";	
			if($order <> '')			
				$sql .= " ORDER BY a." . $order . ' ' . $sort;
				
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5,'HASIL PENCARIAN DATA PASIEN LAMA','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariPasLama'));			
			
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(15,10,'No.','RTL',0,'C');
			$pdf->Cell(15,10,'No. RM','RTL',0,'C');		
			$pdf->Cell(60,10,'Nama Lengkap','RTL',0,'C');
			$pdf->Cell(20,5,'Jenis','RTL',0,'C');
			$pdf->Cell(80,10,'Alamat','RTL',0,'C');
			$pdf->Ln(5);
			$pdf->Cell(15,5,'','RBL',0,'C');
			$pdf->Cell(15,5,'','RBL',0,'C');		
			$pdf->Cell(60,5,'','RBL',0,'C');
			$pdf->Cell(20,5,'Kelamin','RBL',0,'C');
			$pdf->Cell(80,5,'','RBL',0,'C');
			$pdf->Ln(5);
			$pdf->Cell(15,0.5,'','1',0,'C');
			$pdf->Cell(15,0.5,'','1',0,'C');
			$pdf->Cell(60,0.5,'','1',0,'C');
			$pdf->Cell(20,0.5,'','1',0,'C');
			$pdf->Cell(80,0.5,'','1',0,'C');
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
				$pdf->MultiCell(15,10,$row['cm'],'1','C');
				
				$pdf->setY($y);
				$pdf->setX($x+15);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['nama']);			
				if($lebarKata<59)
				{
					$pdf->MultiCell(60,10,$row['nama'],'1','L');
				}
				else
				{
					$pdf->MultiCell(60,5,$row['nama'],'1','L');
				}
				
				$pdf->setY($y);
				$pdf->setX($x+60);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$pdf->MultiCell(20,10,$row['jkel']==='0'? 'Laki-Laki':'Perempuan','1','C');
				
				$pdf->setY($y);
				$pdf->setX($x+20);
				$y=$pdf->GetY();
				$x=$pdf->GetX();
				$lebarKata=$pdf->GetStringWidth($row['alamat']);			
				if($lebarKata<79)
				{
					$pdf->MultiCell(80,10,$row['alamat'],'1','C');
				}
				else
				{
					$pdf->MultiCell(80,5,$row['alamat'],'1','C');
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
			$pdf->Cell(0,5,'DATA PASIEN LAMA TIDAK DITEMUKAN','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarCariPasLama'));			
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
