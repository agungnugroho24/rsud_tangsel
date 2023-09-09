<?php
class cetakNotaObatBaru extends SimakConf
{
	public function onLoad($param)
	{	
		
		$jnsPasien=$this->Request['jnsPasien'];		
		$noTrans=$this->Request['notrans'];		
		$dokter=$this->Request['dokter'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];
		$mode=$this->Request['mode'];	
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$noKwitansi = substr($noTrans,6,8).'/'.'APT-';
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
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',10);
		
		if($jnsPasien == '0')
		{
			$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT JALAN','0',0,'C');
		}
		elseif($jnsPasien == '1')
		{
			if($mode=='0')
			{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( KREDIT )','0',0,'C');
			}else{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( TUNAI )','0',0,'C');
			}
		}
		elseif($jnsPasien == '2')
		{
			$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES PASIEN LUAR','0',0,'C');
		}		
		
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(50,10,$noTrans,0,0,'L');		
		
		if($jnsPasien != '2')
		{
			$pdf->Cell(15,10,'',0,0,'L');
			$pdf->Cell(15,10,'No. CM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$cm,0,0,'L');
			//$pdf->Ln(4);
		}		
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($jnsPasien != '2')
		{
			$pdf->Cell(50,10,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
			//$pdf->Ln(5);
			$pdf->Cell(15,10,'',0,0,'L');
			$pdf->Cell(15,10,'Dokter',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'L');
		}
		else
		{
			$pdf->Cell(50,10,$nmPasien,0,0,'L');
			//$pdf->Ln(5);
			$pdf->Cell(50,10,'',0,0,'L');
			$pdf->Cell(15,10,'Dokter',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$dokter,0,0,'L');
		}
				
					
		//Line break
		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(90,5,'Obat',1,0,'C');
		//$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(30,5,'Jumlah',1,0,'C');
		$pdf->Cell(45,5,'Total',1,0,'C');		
		//Line break
		$pdf->Ln(6);	
		
		if($jnsPasien == '0')
		{
			$tblTbtObat= 'tbt_obat_jual';
		}
		elseif($jnsPasien == '1')
		{
			$tblTbtObat= 'tbt_obat_jual_inap';
		}
		elseif($jnsPasien == '2')
		{
			$tblTbtObat= 'tbt_obat_jual_lain';
		}
		
		 $sql = "SELECT 
					id_obat, hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat
				WHERE 
					no_trans = '$noTrans'
				GROUP BY id_obat ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(90,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
			//$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
			$pdf->Cell(30,5,$row['jumlah'],1,0,'C');	
			$pdf->Cell(45,5,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			$grandTot +=$row['total'];
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(50,5,'Total ',0,0,'R');		
		$pdf->Cell(45,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(13);	
		$pdf->SetFont('Arial','BU',9);	
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat'));		
		$pdf->Ln(4);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>