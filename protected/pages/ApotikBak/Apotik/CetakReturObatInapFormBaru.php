<?php
class CetakReturObatInapFormBaru extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['noTransRetur'];	
		$cm=$this->Request['cm'];
		
		$idDokter = ObatJualInapReturRecord::finder()->find('no_trans = ?',$noTrans)->dokter;
			
		$operator=$this->User->IsUserName;
		$nip=$this->User->IsUserNip;
		
		
		$pdf=new reportRetur('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		$pdf->SetFont('Arial','BU',8);
		$pdf->Cell(0,5,'RETUR OBAT RAWAT INAP','0',0,'C');
		$pdf->Ln(5);	
		
	$pdf->SetFont('Arial','',7);
		$pdf->Cell(15,10,'No. Retur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$noTrans,0,0,'L');
		$pdf->Ln(4);		
		$pdf->Cell(15,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$cm,0,0,'L');
		$pdf->Ln(4);
		$pdf->Cell(15,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(15,10,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
		$pdf->Ln(4);
		$pdf->Cell(15,10,'Nama Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(15,10,PegawaiRecord::finder()->findByPk($idDokter)->nama,0,0,'L');
		
		//Line break
		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',6);
		$pdf->Cell(7,4,'No.',1,0,'C');
		$pdf->Cell(10,4,'Kode',1,0,'C');
		$pdf->Cell(45,4,'Nama Obat',1,0,'C');
		$pdf->Cell(20,4,'Harga',1,0,'C');
		$pdf->Cell(10,4,'Jumlah',1,0,'C');
		$pdf->Cell(20,4,'Total',1,0,'C');
		/*
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(15,5,'Harga',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(35,5,'Total',1,0,'C');	
		*/	
		//Line break
		$pdf->Ln(5);
		
		$sql = "SELECT * FROM tbt_obat_jual_inap_retur WHERE no_trans='$noTrans' AND cm='$cm' ";		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(7,4,$j.'.',1,0,'C');
			$pdf->Cell(10,4,$row['id_obat'],1,0,'L');				
			$pdf->Cell(45,4,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');	
			$pdf->Cell(20,4,'Rp ' . number_format($row['hrg'],2,',','.'),1,0,'R');	
			$pdf->Cell(10,4,$row['jumlah'],1,0,'R');	
			$pdf->Cell(20,4,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
			$pdf->Ln(4);
			$grandTot +=$row['total'];
			
		}		
		
		$pdf->Cell(40,4,'',0,0,'L');
		$pdf->Cell(52,4,'Total ',0,0,'R');		
		$pdf->Cell(20,4,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(50,4,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		
		$pdf->Ln(4);
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(50,4,'Petugas Apotik,',0,0,'C');	
		$pdf->Ln(8);	
		$pdf->SetFont('Arial','BU',7);	
		//$pdf->Cell(53,4,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(50,4,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.returObatInap') . '&purge=1');				
		$pdf->Ln(4);
		$pdf->SetFont('Arial','',7);	
		$pdf->Cell(50,4,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>