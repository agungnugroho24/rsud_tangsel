<?php
class cetakObatMasuk extends SimakConf
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
		$mat=$this->Request['mat'];
				
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
		
		$pdf=new reportAdmBarang('P','mm','a4');
		$pdf->txtFooter="Tanda Terima Barang";
		//$pdf=new cetakKartu();
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
		$pdf->Cell(0,5,'TANDA TERIMA BARANG','0',0,'C');
		$pdf->Ln(5);				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. PO',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$noPO,0,0,'L');
		
		$pdf->Cell(15,10,'Dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(50,10,'',0,0,'L');
		$pdf->Ln(5);/*
		$pdf->Cell(30,10,'No.Faktur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$noFak,0,0,'L');*/
		$pdf->Cell(30,10,'Tgl. Faktur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$this->convertDate($tgl,'3'),0,0,'L');
		
		$pdf->Cell(75,10,$nmPbf,0,0,'L');
		$pdf->Ln(5);/*
		$pdf->Cell(30,10,'Tgl. Faktur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$this->convertDate($tgl,'3'),0,0,'L');*/
		$pdf->Cell(30,10,' ',0,0,'L');
		$pdf->Cell(2,10,' ',0,0,'L');
		$pdf->Cell(75,10,' ',0,0,'L');
		
		$pdf->Cell(75,10,$alamatPbf,0,0,'L');
			
		//Line break
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',9);
		//$pdf->Cell(15,5,'No.','1',0,'C');
		//$pdf->Cell(80,5,'Nama Barang','1',0,'C');
		$pdf->Cell(30,5,'No. Faktur','1',0,'C');
		$pdf->Cell(65,5,'Nama Barang','1',0,'C');
		$pdf->Cell(20,5,'Jumlah','1',0,'C');
		$pdf->Cell(30,5,'Harga','1',0,'C');
		//$pdf->Cell(15,5,'Disc','1',0,'C');
		$pdf->Cell(30,5,'Total','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT
				no_faktur,
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
				$total = floor($row['jumlah'] * $row['hrg_disc']);
				//$total = floor($row['jumlah'] * $row['hrg_ppn_disc']);
			
				$pdf->SetFont('Arial','',9);						
				//$pdf->Cell(15,5,$j.'.','1',0,'C');
				$pdf->Cell(30,5,$row['no_faktur'],'1',0,'C');
				$pdf->Cell(65,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,'1',0,'L');
				$pdf->Cell(20,5,$row['jumlah'],'1',0,'C');
				$pdf->Cell(30,5,'Rp ' .number_format($row['hrg_disc'],2,',','.'),1,0,'R');
				//$pdf->Cell(30,5,'Rp ' .number_format($row['hrg_ppn'],2,',','.'),1,0,'R');
				//$pdf->Cell(15,5,$row['discount'].' %',1,0,'C');
				$pdf->Cell(30,5,'Rp ' .number_format($total,2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			else //harga ppn 
			{
				$total = floor($row['jumlah'] * $row['hrg_nett']);
				//$total = floor($row['jumlah'] * $row['hrg_ppn']);
			
				$pdf->SetFont('Arial','',9);						
				//$pdf->Cell(15,5,$j.'.','1',0,'C');
				$pdf->Cell(30,5,$row['no_faktur'],'1',0,'C');
				$pdf->Cell(65,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,'1',0,'L');
				$pdf->Cell(20,5,$row['jumlah'],'1',0,'C');
				$pdf->Cell(30,5,'Rp ' .number_format($row['hrg_nett'],2,',','.'),1,0,'R');
				//$pdf->Cell(30,5,'Rp ' .number_format($row['hrg_ppn'],2,',','.'),1,0,'R');
				//$pdf->Cell(15,5,$row['discount'].' %',1,0,'C');
				$pdf->Cell(30,5,'Rp ' .number_format($total,2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			
			
			$grandTot = $grandTot + $total;
		}
		
		$jmlPpn = 0.1 * $grandTot;
		
		$pdf->Cell(110,8,'',0,0,'L');
		$pdf->Cell(35,5,'Total ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(5);	
		$pdf->Cell(110,8,'',0,0,'L');
		$pdf->Cell(35,5,'PPN 10% ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $jmlPpn,2,',','.'),1,0,'R');
		$pdf->Ln(5);	
		
		if ($mat==1)
		{
			$pdf->Cell(110,8,'',0,0,'L');
			$pdf->Cell(35,5,'Materai ',0,0,'R');		
			$pdf->Cell(30,5,'Rp ' .number_format( '3000',2,',','.'),1,0,'R');
			$pdf->Ln(5);	
		}elseif ($mat==2)
		{
			$pdf->Cell(110,8,'',0,0,'L');
			$pdf->Cell(35,5,'Materai ',0,0,'R');		
			$pdf->Cell(30,5,'Rp ' .number_format( '7000',2,',','.'),1,0,'R');
			$pdf->Ln(5);	
		}
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(110,8,'',0,0,'L');
		$pdf->Cell(35,5,'Grand Total',0,0,'R');		
		if ($mat==1)
		{
			$pdf->Cell(30,5,'Rp ' .number_format( $grandTot + $jmlPpn + 3000,2,',','.'),1,0,'R');	
		}elseif ($mat==2)
		{
			$pdf->Cell(30,5,'Rp ' .number_format( $grandTot + $jmlPpn + 7000,2,',','.'),1,0,'R');	
		}else
		{
			$pdf->Cell(30,5,'Rp ' .number_format( $grandTot + $jmlPpn,2,',','.'),1,0,'R');	
		}
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(65,8,'Direktur,',0,0,'C');
		$pdf->Cell(65,8,'Manajer Keuangan,',0,0,'C');
		$pdf->Cell(60,8,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		
		
		$pdf->Cell(65,8,'( Direktur )',0,0,'C','',$this->Service->constructUrl('Farmasi.ObatBeli'));
		$pdf->Cell(65,8,'( Manajer Keuangan )',0,0,'C','',$this->Service->constructUrl('Farmasi.ObatBeli'));
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.ObatBeli'));	
		
		$pdf->Ln(5);
			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>
