<?php
class cetakReturObatPasien extends SimakConf
{
	public function onLoad($param)
	{			
		$noTrans=$this->Request['noTransRetur'];	
		$cm=$this->Request['cm'];
		
		$idDokter = ObatReturJalanRecord::finder()->find('no_trans = ?',$noTrans)->dokter;
			
		$operator=$this->User->IsUserName;
		$nip=$this->User->IsUserNip;
		
		
		$pdf=new reportRetur();
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
		$pdf->Cell(0,5,'RETUR OBAT RAWAT JALAN','0',0,'C');
		$pdf->Ln(5);	
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. Retur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$noTrans,0,0,'L');
		$pdf->Ln(5);		
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$cm,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Nama Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,PegawaiRecord::finder()->findByPk($idDokter)->nama,0,0,'L');
		
		//Line break
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(15,5,'Kode',1,0,'C');
		$pdf->Cell(65,5,'Nama Obat',1,0,'C');
		$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(30,5,'Total',1,0,'C');
		/*
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(35,5,'Total',1,0,'C');	
		*/	
		//Line break
		$pdf->Ln(6);
		
		$sql = "SELECT * FROM tbt_obat_retur_jalan WHERE no_trans='$noTrans' AND cm='$cm' ";		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(15,5,$row['id_obat'],1,0,'L');				
			$pdf->Cell(65,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');	
			$pdf->Cell(30,5,'Rp ' . number_format($row['hrg'],2,',','.'),1,0,'R');	
			$pdf->Cell(20,5,$row['jumlah'],1,0,'R');	
			$pdf->Cell(30,5,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			$grandTot +=$row['total'];
			
		}		
		
		$pdf->Cell(70,8,'',0,0,'L');
		$pdf->Cell(70,5,'Total ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
							
				
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.returObatJalan') . '&purge=1');				
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>