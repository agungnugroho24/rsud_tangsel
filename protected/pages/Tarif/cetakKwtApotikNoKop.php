<?php
class cetakKwtApotikNoKop extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$cm=$this->Request['cm'];
		$jmlBayar=number_format($this->Request['jmlBayar'],2,',','.');
		$sisaBayar=number_format($this->Request['sisa'],2,',','.');
		$sisa=$this->Request['sisa'];
		$sayTerbilang=$this->terbilang($this->Request['jmlTagihan']) . ' rupiah';
		$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
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
		
		$pdf=new reportKwitansi();
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN APOTEK','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$noTrans,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		$pdf->Ln(5);		
		$pdf->Cell(30,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$sayTerbilang,0,0,'L');		
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(150,5,'Tindakan',1,0,'C');
		$pdf->Cell(30,5,'Jumlah',1,0,'C');		
		//Line break
		$pdf->Ln(6);
		$sql="SELECT nama, jml FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(150,5,$row['nama'],1,0,'L');
			$pdf->Cell(30,5,'Rp ' . number_format($row['jml'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(80,5,'Total ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' . $jmlTagihan,1,0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(80,5,'Bayar ',0,0,'R');
		$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');	
		$pdf->Ln(5);			
		$pdf->SetFont('Arial','',9);				
		$pdf->Cell(10,5,'',0,0,'C');
		$pdf->Cell(150,5,'Kembalian ',0,0,'R');
		$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','BU',9);	
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirIgd') . '&purge=1');		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
	}
	
}
?>