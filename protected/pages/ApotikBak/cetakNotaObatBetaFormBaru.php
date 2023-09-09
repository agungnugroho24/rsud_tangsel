<?php
class cetakNotaObatBetaFormBaru extends SimakConf
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
		
		
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$tblTbtObat = 'tbt_obat_jual';
			$activeRec = ObatJualRecord::finder();
		}
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$tblTbtObat = 'tbt_obat_jual_inap';
			$activeRec = ObatJualInapRecord::finder();
		}
		elseif($jnsPasien == '2') //pasien luar / rujukan
		{
			$tblTbtObat = 'tbt_obat_jual_lain';
			$activeRec = ObatJualLainRecord::finder();
		}		
		
		$pdf=new reportKwitansi('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',7);
		
		$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
		
		if($jnsPasien == '0')
		{
			if($idKlinik=='07'){			
				$pdf->Cell(0,4,'RINCIAN PEMBELIAN OBAT/ALKES IGD','0',0,'C');
			}else{
				$pdf->Cell(0,4,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT JALAN','0',0,'C');
			}
		}
		elseif($jnsPasien == '1')
		{
			if($mode=='0')
			{
				$pdf->Cell(0,4,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( KREDIT )','0',0,'C');
			}else{
				$pdf->Cell(0,4,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( TUNAI )','0',0,'C');
			}
		}
		elseif($jnsPasien == '2')
		{
			$pdf->Cell(0,4,'RINCIAN PEMBELIAN OBAT/ALKES PASIEN LUAR','0',0,'C');
		}		
		
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,4,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(20,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(25,10,$noTrans,0,0,'L');		
		
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(10,10,'Poliklinik',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
			
		if($jnsPasien == '0')
		{
			$noTransRwtjln = $activeRec->find('no_trans=?',$noTrans)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
			$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
			$pdf->Cell(25,10,$nmKlinik,0,0,'L');
		}
		else
		{
			$pdf->Cell(25,10,'-',0,0,'L');
		}		
		
		$pdf->Ln(4);
		$pdf->Cell(20,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		if($jnsPasien != '2')
		{
			$pdf->Cell(25,10,$cm,0,0,'L');
		}		
		else
		{
			$pdf->Cell(25,10,'-',0,0,'L');
		}
		
		
		
		$pdf->Cell(10,10,'',0,0,'L');
		$pdf->Cell(10,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
			
		if($jnsPasien != '2')
		{
			$pdf->Cell(20,10,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'L');
		}
		else
		{
			$pdf->Cell(20,10,$dokter,0,0,'L');
		}
		
		$pdf->Ln(4);
		$pdf->Cell(20,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($jnsPasien != '2')
		{
			$pdf->Cell(25,10,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
			$pdf->Ln(4);
		}
		else
		{
			$pdf->Cell(25,10,$nmPasien,0,0,'L');
			$pdf->Ln(4);
		}
		
				
					
		//Line break
		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(10,4,'No.',1,0,'C');
		$pdf->Cell(60,4,'Obat',1,0,'C');
		//$pdf->Cell(20,4,'Harga',1,0,'C');
		$pdf->Cell(15,4,'Jumlah',1,0,'C');
		$pdf->Cell(25,4,'Harga',1,0,'C');		
		//Line break
		$pdf->Ln(5);	
		
		
		//------------------------------------ non racikan ------------------------------------// 	
		 $sql = "SELECT 
					id_obat, 
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat
				WHERE 
					no_trans = '$noTrans' AND st_racik = 0
				GROUP BY id_obat ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',7);						
			$pdf->Cell(10,4,'',"LTB",0,'L');
			$pdf->Cell(100,4,'Obat Non Racikan','RT',0,'L');
			$pdf->Ln(4);
		
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,4,$j.'.',1,0,'C');
				$pdf->Cell(60,4,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
				//$pdf->Cell(20,4,'Rp ' . number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				$pdf->Cell(15,4,$row['jumlah'],1,0,'C');	
				$pdf->Cell(25,4,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
				$pdf->Ln(4);
				$grandTotNonRacikan +=$row['total'];
			}
			
			$pdf->Ln(1);			
		}
		
		//------------------------------------ racikan ------------------------------------// 	
		 $sql = "SELECT 
					id_obat, 
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat
				WHERE 
					no_trans = '$noTrans' AND st_racik = 1
				GROUP BY id_obat ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',7);						
			$pdf->Cell(10,4,'',"LTB",0,'L');
			$pdf->Cell(100,4,'Obat Racikan','RT',0,'L');
			$pdf->Ln(4);
			
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,4,$j.'.',1,0,'C');
				$pdf->Cell(60,4,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
				//$pdf->Cell(20,4,'Rp ' . number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				$pdf->Cell(15,4,$row['jumlah'],1,0,'C');	
				$pdf->Cell(25,4,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
				$pdf->Ln(4);
				$grandTotRacikan +=$row['total'];
			}			
		}
		
		$grandTot = $grandTotRacikan + $grandTotNonRacikan;
			
		
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(40,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->SetFont('Arial','',7);						
		$pdf->Cell(45,3,'Total ',0,0,'R');		
		$pdf->Cell(25,3,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(40,8,'Petugas Kasir,',0,0,'C');	
		$pdf->Ln(10);	
		$pdf->SetFont('Arial','BU',7);	
		$pdf->Cell(40,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat'));		
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',7);	
		$pdf->Cell(40,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>