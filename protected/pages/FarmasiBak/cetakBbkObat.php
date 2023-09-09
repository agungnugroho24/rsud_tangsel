<?php
class cetakBbkObat extends SimakConf
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
		
		$pdf=new reportLainnya();
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
		$pdf->Cell(0,5,'SURAT BUKTI BARANG KELUAR','0',0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. : '.$nobbk,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(15,10,'Tanggal',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,date("d").' '.$this->namaBulan(date("m")).' '.date("Y"),0,0,'L');
		
		$pdf->Cell(85,10,'',0,0,'L');
		
		$pdf->Cell(15,10,'Tujuan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$tujuan,0,0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(15,10,'Petugas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$petugas,0,0,'L');
		
		$pdf->Cell(85,10,'',0,0,'L');
		
		$pdf->Cell(15,10,'Penerima',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$penerima,0,0,'L');		
		$pdf->Ln(5);
		//Line break
		$pdf->Ln(6);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(15,5,'No',1,0,'C');
		$pdf->Cell(80,5,'Nama Obat',1,0,'C');
		$pdf->Cell(35,5,'Sumber',1,0,'C');
		$pdf->Cell(35,5,'Tujuan',1,0,'C');
		$pdf->Cell(25,5,'Jumlah',1,0,'C');	
		$pdf->Ln(5);
			
		$sql="SELECT kode,sumber,tujuan, SUM(jumlah) AS jumlah FROM $nmTable GROUP BY kode ORDER BY id ";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		$no = 0;
		foreach($arrData as $row)
		{
			$no++;
			$nmObat=ObatRecord::finder()->findByPk($row['kode'])->nama;
			$sumMaster=DesFarmasiRecord::finder()->findByPk($row['sumber'])->nama;
			$sumSekunder=DesFarmasiRecord::finder()->findByPk($row['tujuan'])->nama;
			
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(15,5,$no,1,0,'C');
			$pdf->Cell(80,5,$nmObat,1,0,'L');
			$pdf->Cell(35,5,$sumMaster,1,0,'C');
			$pdf->Cell(35,5,$sumSekunder,1,0,'C');
			$pdf->Cell(25,5,$row['jumlah'],1,0,'R');	
			$pdf->Ln(5);
			
		}				
		
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'',0,0,'C');	
		
		$pdf->Ln(5);
		$pdf->Cell(80,8,'Petugas,',0,0,'C');
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'Penerima,',0,0,'C');	
		
		$pdf->Ln(10);
		$pdf->Cell(80,8,'( '.$petugas.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);		
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'( '.$penerima.' )',0,0,'C');
					
		//$pdf->Ln(5);			
		
		//$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);		
		//$pdf->Ln(5);
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>