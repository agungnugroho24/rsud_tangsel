<?php
class cetakNotaRadFormBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{
		$noTrans=$this->Request['notrans'];
		$cm=$this->Request['cm'];		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmPasien=$this->Request['nama'];
		$jnsRujukan = $this->Request['jnsRujukan'];
		$asalPasien = $this->Request['asalPasien'];
		$petugasRad=$this->Request['petugasRad'];
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$tgl = date('Y-m-d');
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		if($jnsRujukan == '0')
		{
			if($asalPasien == '0') //Pasien Rawat Jalan
			{				
				$activeRec = RadJualRecord::finder();
				$nmTbl = 'tbt_rad_penjualan';
			}
			elseif($asalPasien == '1') //Pasien Rawat Inap
			{
				$activeRec = RadJualInapRecord::finder();
				$nmTbl = 'tbt_rad_penjualan_inap';
			}
						
			$sql="SELECT 
					no_trans,
					dokter,
					id_tindakan,
					film_size,
					harga 
				FROM 
					$nmTbl 
				WHERE 
					no_trans='$noTrans' 
					AND cm='$cm' 
					AND flag='1' ";
			
			$idDokter = $activeRec->findBySql($sql)->dokter;	
			$nmDokter =	PegawaiRecord::finder()->findByPk($idDokter)->nama;
		}	
		elseif($jnsRujukan == '1')
		{
			$activeRec = RadJualLainRecord::finder();
			$nmTbl = 'tbt_rad_penjualan_lain';
			
			$sql="SELECT 
					no_trans,
					dokter,
					id_tindakan,
					film_size,
					harga 
				FROM 
					$nmTbl 
				WHERE 
					no_trans='$noTrans' 
					AND flag='1' 
					";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$nmDokter = $row['dokter'];
			}
		}		
		
		
		$noKwitansi = substr($noTrans,6,8).'/'.'RAD-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);
		
		$pdf=new reportKwitansi('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',8);
		$pdf->Cell(0,4,'KWITANSI PEMBAYARAN RADIOLOGI','0',0,'C');
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,4,'No. Kwitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(25,4,'No. Register',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(25,4,$noTrans,0,0,'L');
		$pdf->Ln(4);
		
		if($jnsRujukan == '0')
		{
			$pdf->Cell(25,4,'No. CM',0,0,'L');
			$pdf->Cell(2,4,':  ',0,0,'L');
			$pdf->Cell(25,4,$cm,0,0,'L');
			$pdf->Ln(4);
		}	
		
		
		$pdf->Cell(25,4,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(25,4,$nmPasien,0,0,'L');
		$pdf->Ln(4);
		$pdf->Cell(25,4,'Dokter',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(25,4,$nmDokter,0,0,'L');
		$pdf->Ln(4);
		$pdf->Cell(25,4,'Petugas Radiologi',0,0,'L');
		$pdf->Cell(2,4,':  ',0,0,'L');
		$pdf->Cell(25,4,UserRecord::finder()->find('nip = ?',$petugasRad)->real_name,0,0,'L');			
		//Line break
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(10,4,'No.',1,0,'C');
		$pdf->Cell(25,4,'Ukuran Film',1,0,'C');
		$pdf->Cell(55,4,'Nama Tindakan',1,0,'C');
		$pdf->Cell(20,4,'Harga',1,0,'C');	
		$pdf->Ln(4);
			
		//$sql="SELECT nama, total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$film = $row['film_size'];
			
			switch ($film)
			{
				case '0':
					$nmFilm='18 x 24';
					break;
				case '1':
					$nmFilm='24 x 30';
					break;
				case '2':
					$nmFilm='30 x 40';
					break;
				case '3':
					$nmFilm='35 x 35';
					break;
			}
		
			$j += 1;
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(10,4,$j.'.',1,0,'C');
			$pdf->Cell(25,4,$nmFilm,1,0,'C');	
			
			if($row['id_tindakan']=='RUJ')
			{
				$pdf->Cell(55,4,'Rujukan',1,0,'L');	
			}
			else
			{
				$pdf->Cell(55,4,RadTdkRecord::finder()->find('kode = ?',array($row['id_tindakan']))->nama,1,0,'L');
			}
			
			$pdf->Cell(20,4,'Rp ' . number_format($row['harga'],2,',','.'),1,0,'R');	
			$pdf->Ln(4);
			
		}
					
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(40,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->SetFont('Arial','',7);						
		$pdf->Cell(50,3,'Total ',0,0,'R');		
		$pdf->Cell(20,3,'Rp ' . $jmlTagihan,1,0,'R');
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',7);		
		$pdf->Cell(40,8,'Petugas Kasir,',0,0,'C');	
		$pdf->Ln(10);	
		$pdf->SetFont('Arial','BU',7);	
		$pdf->Cell(40,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.KasirRad'));		
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',7);	
		$pdf->Cell(40,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>