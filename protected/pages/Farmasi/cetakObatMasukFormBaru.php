<?php
class cetakObatMasukFormBaru extends SimakConf
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
		$noFak=$this->Request['noFak'];
				
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		$sql="SELECT pbf FROM tbt_obat_beli WHERE no_po='$noPO' GROUP BY  no_po";
		$idPbf = ObatBeliRecord::finder()->findBySql($sql)->pbf;
		$nmPbf = PbfObatRecord::finder()->findByPk($idPbf)->nama;	
		$alamatPbf = PbfObatRecord::finder()->findByPk($idPbf)->alamat;		
		
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
				
		$pdf->txtFooter="Tanda Terima Barang";
		//$pdf=new cetakKartu();
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		$pdf->SetFont('Arial','BU',8);
		$pdf->Cell(0,5,'TANDA TERIMA BARANG','0',0,'C');
		$pdf->Ln(5);				
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(13,5,'No. PO',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(45,5,$noPO,0,0,'L');
		
		$pdf->Cell(10,5,'Dari',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(13,5,'No.Faktur',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(45,5,$noFak,0,0,'L');
		
		$pdf->Cell(45,5,$nmPbf,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(13,5,'Tgl. Faktur',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(45,5,$this->convertDate($tgl,'3'),0,0,'L');
		
		$pdf->Cell(45,5,$alamatPbf,0,0,'L');
			
		//Line break
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',6);
		$pdf->Cell(10,5,'No.','1',0,'C');
		$pdf->Cell(40,5,'Nama Barang','1',0,'C');
		$pdf->Cell(10,5,'Jumlah','1',0,'C');
		$pdf->Cell(17,5,'Harga','1',0,'C');
		$pdf->Cell(10,5,'Disc','1',0,'C');
		$pdf->Cell(23,5,'Total','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT
				id_obat,
				jumlah,
				hrg_ppn,
				hrg_ppn_disc,
				hrg_nett,
				hrg_disc,
				discount 
			FROM 
				tbt_obat_masuk 
			WHERE 
				no_po='$noPO'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		$grandTot=0;
		
		foreach($arrData as $row)
		{
			$j += 1;
			
			
			if($row['discount'] > 0) //jika ada => discount harga ppn discount
			{
				//$total = floor($row['jumlah'] * $row['hrg_disc']);
				$total = floor($row['jumlah'] * $row['hrg_ppn_disc']);
			
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,5,$j.'.','1',0,'C');
				$pdf->Cell(40,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,'1',0,'L');
				$pdf->Cell(10,5,$row['jumlah'],'1',0,'C');
				//$pdf->Cell(10,5,'Rp ' .number_format($row['hrg_nett'],2,',','.'),1,0,'R');
				$pdf->Cell(17,5,'Rp ' .number_format($row['hrg_ppn'],2,',','.'),1,0,'R');
				$pdf->Cell(10,5,$row['discount'].' %',1,0,'C');
				$pdf->Cell(23,5,'Rp ' .number_format($total,2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			else //harga ppn 
			{
				//$total = floor($row['jumlah'] * $row['hrg_nett']);
				$total = floor($row['jumlah'] * $row['hrg_ppn']);
			
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,5,$j.'.','1',0,'C');
				$pdf->Cell(40,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,'1',0,'L');
				$pdf->Cell(10,5,$row['jumlah'],'1',0,'C');
				//$pdf->Cell(10,5,'Rp ' .number_format($row['hrg_nett'],2,',','.'),1,0,'R');
				$pdf->Cell(17,5,'Rp ' .number_format($row['hrg_ppn'],2,',','.'),1,0,'R');
				$pdf->Cell(10,5,$row['discount'].' %',1,0,'C');
				$pdf->Cell(23,5,'Rp ' .number_format($total,2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			
			
			$grandTot = $grandTot + $total;
		}
		
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(37,5,'Total ',0,0,'R');		
		$pdf->Cell(23,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(50,5,'',0,0,'L');				
		$pdf->Cell(60,5,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(3);
		$pdf->Cell(50,5,'',0,0,'L');	
		$pdf->Cell(60,5,'Petugas,',0,0,'C');				
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(8);
		$pdf->SetFont('Arial','BU',7);
		$pdf->Cell(50,5,'',0,0,'L');	
		$pdf->Cell(60,5,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.ObatMasuk'));		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(50,5,'',0,0,'L');	
		$pdf->Cell(60,5,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>