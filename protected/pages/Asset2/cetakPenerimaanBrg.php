<?php
class cetakPenerimaanBrg extends SimakConf
{	 
	public function onLoad($param)
	{	
		
		$noFak=$this->Request['noFak'];		
		
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
		
		$pdf=new reportAdmBarang('P','mm','kwt');
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
		$pdf->Cell(0,5,'PENERIMAAN BARANG','0',0,'C');
		$pdf->Ln(5);				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(20,10,'No. Faktur',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$noFak,0,0,'L');
		
		$pdf->Cell(40,10,'',0,0,'L');
		
		$tglTerima = AssetPenerimaanRecord::finder()->find('no_fak=?',$noFak)->tgl_terima;
		$pdf->Cell(20,10,'Tgl. Terima',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$this->convertDate($tglTerima,'3'),0,0,'L');
		
		$pdf->Ln(5);
			
		//Line break
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(10,5,'No.','1',0,'C');
		$pdf->Cell(15,5,'Kode','1',0,'C');
		$pdf->Cell(55,5,'Nama Barang','1',0,'C');
		$pdf->Cell(20,5,'Jumlah','1',0,'C');
		$pdf->Cell(30,5,'No. Seri','1',0,'C');
		$pdf->Cell(20,5,'Garansi','1',0,'C');
		$pdf->Cell(45,5,'Distributor','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT * FROM tbt_asset_penerimaan_brg WHERE no_fak='$noFak'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.','1',0,'C');
			$pdf->Cell(15,5,$row['id_brg'],'1',0,'C');
			$pdf->Cell(55,5,AssetBarangRecord::finder()->findByPk($row['id_brg'])->nama,'1',0,'L');
			$pdf->Cell(20,5,$row['jml'],'1',0,'C');
			
			if($row['no_seri'] == NULL || $row['no_seri'] == '')
			{
				$noSeri = '-';
			}
			else
			{
				$noSeri = $row['no_seri'];
			}
			
			if($row['garansi'] == NULL || $row['garansi'] == '')
			{
				$garansi = '-';
			}
			else
			{
				$garansi = $row['garansi'].' thn';
			}
			
			$pdf->Cell(30,5,$noSeri,'1',0,'C');
			$pdf->Cell(20,5,$garansi,'1',0,'C');
			$pdf->Cell(45,5,AssetDistributorRecord::finder()->findByPk($row['distributor'])->nama,'1',0,'C');
			$pdf->Ln(5);
			//$pdf->Ln(5);
			
		}
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Asset.admPenerimaanBarang'));		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>