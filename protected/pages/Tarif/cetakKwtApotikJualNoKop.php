<?php
class cetakKwtApotikJualNoKop extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];	
		$jnsPasien=$this->Request['jnsPasien'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		//$nmTable=$this->Request['table'];
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
		
		if($jnsPasien == '04') //jika bukan dari rawat jalan
		{
			$activeRec = ObatJualRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual';
		}
		elseif($jnsPasien == '10') //jika dari rawat inap bayar tunai
		{
			$activeRec = ObatJualInapRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_inap';	
		}
		elseif($jnsPasien == '13') //jika dari pasien rujukan 
		{
			$activeRec = ObatJualLainRecord::finder();
			$tbtObatTbl = 'tbt_obat_jual_lain';
		}
		
		if($jnsPasien != '10') //jika bukan dari rawat inap bayar tunai
		{
			$idKlinik = $activeRec ->find('no_trans = ?',$noTrans)->klinik;			
		}		
		
		$idPetugas = $activeRec ->find('no_trans = ?',$noTrans)->operator;
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
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN APOTIK/FARMASI','0',0,'C');
		$pdf->Ln(7);		
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. :'.$noKwitansi,'0',0,'C');
		$pdf->Ln(7);		

		$pdf->Cell(27,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$noTrans,0,0,'L');
		
		if($jnsPasien == '04' || $jnsPasien == '10')
		{
			$pdf->Cell(15,10,'No. CM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$cm,0,0,'L');
			$pdf->Ln(5);
		}						
	
		$pdf->Cell(27,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$nmPasien,0,0,'L');		
		
		if($jnsPasien != '10') //jika bukan dari rawat inap bayar tunai
		{
			$pdf->Cell(15,10,'Poliklinik',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,PoliklinikRecord::finder()->findByPk($idKlinik)->nama,0,0,'L');
		}
		else
		{
			$pdf->Cell(15,10,'Petugas',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,ucwords(UserRecord::finder()->find('nip = ?',$idPetugas)->real_name),0,0,'L');		
		}	
		
		
		$pdf->Ln(5);	
				
		$pdf->Cell(27,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,ucwords($sayTerbilang),0,0,'L');
		
		if($jnsPasien != '10') //jika bukan dari rawat inap bayar tunai
		{
			$pdf->Cell(15,10,'Petugas',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,ucwords(UserRecord::finder()->find('nip = ?',$idPetugas)->real_name),0,0,'L');		
		}
		
		$pdf->Ln(12);			
				
		$text="Uang sebesar ".$sayTerbilang." untuk pembayaran pembelian obat-obatan dari Apotik/Farmasi dengan rincian terlampir.";
		$pdf->MultiCell(190,7,$text);
		$pdf->Ln(5);
		$pdf->Cell(50,7,'Rp. '.$jmlTagihan,'1',0,'C');
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'K a s i r,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Tarif.KasirApotik') . '&purge=1');		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
	}
}
?>