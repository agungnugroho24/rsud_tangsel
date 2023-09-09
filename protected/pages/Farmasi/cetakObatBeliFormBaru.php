<?php
class cetakObatBeliFormBaru extends SimakConf
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
		
		$noPO=$this->Request['noPO'];		
		$tgl=$this->Request['tgl'];		
		$pbf=$this->Request['pbf'];
				
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		
		
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
		
		$pdf=new reportKwitansi('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
				
		$pdf->SetFont('Arial','BU',8);
		$pdf->Cell(0,5,'ORDER PEMBELIAN','0',0,'C');
		$pdf->Ln(5);				
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(10,5,'No. PO',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(55,5,$noPO,0,0,'L');
		
		$pdf->Cell(10,5,'Kepada',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(10,5,'Tanggal',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(55,5,$this->convertDate($tgl,'3'),0,0,'L');
		
		$pdf->Cell(55,5,PbfObatRecord::finder()->findByPk($pbf)->nama,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(10,5,'Dari',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(55,5,'PEMERINTAH KOTA TANGERANG SELATAN',0,0,'L');
		
		$pdf->Cell(55,5,PbfObatRecord::finder()->findByPk($pbf)->alamat,0,0,'L');
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(10,5,'No.','1',0,'C');
		$pdf->Cell(75,5,'Nama Barang','1',0,'C');
		$pdf->Cell(25,5,'Jumlah','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT kode,jumlah FROM tbt_obat_beli WHERE no_po='$noPO'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(10,5,$j.'.','1',0,'C');
			$pdf->Cell(75,5,ObatRecord::finder()->findByPk($row['kode'])->nama,'1',0,'L');
			$pdf->Cell(25,5,$row['jumlah'],'1',0,'C');
			$pdf->Ln(5);
			//$pdf->Ln(5);
			
		}
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(55,5,'',0,0,'L');				
		$pdf->Cell(60,5,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(55,5,'',0,0,'L');	
		$pdf->Cell(60,5,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(5);
		$pdf->SetFont('Arial','BU',7);
		$pdf->Cell(55,5,'',0,0,'L');	
		$pdf->Cell(60,5,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.ObatBeli'));		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(55,5,'',0,0,'L');	
		$pdf->Cell(60,5,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>