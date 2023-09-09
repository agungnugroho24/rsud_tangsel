<?php
class cetakNotaObatNoKop extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];	
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
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
		
		$pdf=new reportKwitansi();
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',10);
		
		$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES','0',0,'C');
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
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$dokter,0,0,'L');			
		//Line break
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(35,5,'Total',1,0,'C');		
		//Line break
		$pdf->Ln(6);		
		$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(80,5,$row['nama'],1,0,'L');
			$pdf->Cell(30,5,'Rp ' . number_format($row['hrg'],2,',','.'),1,0,'R');	
			$pdf->Cell(20,5,$row['jml'],1,0,'R');	
			$pdf->Cell(35,5,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			$grandTot +=$row['total'];
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(60,5,'Total ',0,0,'R');		
		$pdf->Cell(35,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>