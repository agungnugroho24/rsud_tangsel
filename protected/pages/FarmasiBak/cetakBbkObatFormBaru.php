<?php
class cetakBbkObatFormBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{	
		
		$nobbk=$this->Request['nobbk'];			
		$petugas=$this->Request['petugas'];
		$penerima=$this->Request['penerima'];
		$tujuan=DesFarmasiRecord::finder()->findByPk($this->Request['tujuan'])->nama;
		$nmTable=$this->Request['table'];
		
		
		//Update tabel tbt_pembayaran
		/*
		$byrRecord=BayarRecord::finder()->findByPk($noTrans);
		$byrRecord->status_pembayaran='2';//Sudah dibayar!
		$byrRecord->tgl_bayar=date('Y-m-d h:m:s');
		$byrRecord->no_kwitansi=$noKwitansi;
		$byrRecord->operator=$nipTmp;
		$byrRecord->Save();//Update!
		*/
		
		$pdf=new reportLainnya('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
	
		$pdf->SetFont('Arial','BU',8);
		$pdf->Cell(0,4,'SURAT BUKTI BARANG KELUAR','0',0,'C');
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,4,'No. : '.$nobbk,'0',0,'C');
		$pdf->Ln(8);		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(15,4,'Tanggal',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(30,4,date("d").' '.$this->namaBulan(date("m")).' '.date("Y"),0,0,'L');
		
		$pdf->Cell(15,4,'',0,0,'L');
		
		$pdf->Cell(15,4,'Tujuan',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(30,4,$tujuan,0,0,'L');
		
		$pdf->Ln(4);
		$pdf->Cell(15,4,'Petugas',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(30,4,$petugas,0,0,'L');
		
		$pdf->Cell(15,4,'',0,0,'L');
		
		$pdf->Cell(15,4,'Penerima',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(30,4,$penerima,0,0,'L');		
		$pdf->Ln(8);
		//Line break
		$pdf->SetFont('Arial','',8);		
		$pdf->Cell(10,4,'No',1,0,'C');
		$pdf->Cell(40,4,'Nama Obat',1,0,'C');
		$pdf->Cell(25,4,'Sumber',1,0,'C');
		$pdf->Cell(25,4,'Tujuan',1,0,'C');
		$pdf->Cell(10,4,'Jumlah',1,0,'C');	
		$pdf->Ln(4);
			
		$sql="SELECT kode,sumber,tujuan, SUM(jumlah) AS jumlah FROM $nmTable GROUP BY kode ORDER BY id ";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		$no = 0;
		foreach($arrData as $row)
		{
			$no++;
			$nmObat=ObatRecord::finder()->findByPk($row['kode'])->nama;
			$sumMaster=DesFarmasiRecord::finder()->findByPk($row['sumber'])->nama;
			$sumSekunder=DesFarmasiRecord::finder()->findByPk($row['tujuan'])->nama;
			
			$pdf->SetFont('Arial','',7);		
			$pdf->Cell(10,4,$no,1,0,'C');
			$pdf->Cell(40,4,$nmObat,1,0,'L');
			$pdf->Cell(25,4,$sumMaster,1,0,'C');
			$pdf->Cell(25,4,$sumSekunder,1,0,'C');
			$pdf->Cell(10,4,$row['jumlah'],1,0,'R');	
			$pdf->Ln(4);
			
		}				
		
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','',8);	
		$pdf->Cell(55,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Cell(15,8,'',0,0,'C');		
		$pdf->Cell(30,8,'',0,0,'C');	
		
		$pdf->Ln(4);
		$pdf->Cell(55,8,'Petugas,',0,0,'C');
		$pdf->Cell(15,8,'',0,0,'C');		
		$pdf->Cell(30,8,'Penerima,',0,0,'C');	
		
		$pdf->Ln(7);
		$pdf->Cell(55,8,'( '.$petugas.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);		
		$pdf->Cell(15,8,'',0,0,'C');		
		$pdf->Cell(30,8,'( '.$penerima.' )',0,0,'C');
					
		//$pdf->Ln(4);			
		
		//$pdf->SetFont('Arial','BU',8);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);		
		//$pdf->Ln(4);
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>