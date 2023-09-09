<?php
class cetakNotaLabFormBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
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
		$petugasLab=$this->Request['petugasLab'];
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$tgl = date('Y-m-d');
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		if($jnsRujukan == '0')
		{
			if($asalPasien == '0') //Pasien Rawat Jalan
			{				
				$activeRec = LabJualRecord::finder();
				$nmTbl = 'tbt_lab_penjualan';
			}
			elseif($asalPasien == '1') //Pasien Rawat Inap
			{
				$activeRec = LabJualInapRecord::finder();
				$nmTbl = 'tbt_lab_penjualan_inap';
			}
						
			$sql="SELECT 
					no_trans,
					dokter,
					id_tindakan,
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
			$activeRec = LabJualLainRecord::finder();
			$nmTbl = 'tbt_lab_penjualan_lain';
			
			$sql="SELECT 
					no_trans,
					dokter,
					id_tindakan,
					harga 
				FROM 
					$nmTbl 
				WHERE 
					no_trans='$noTrans' 
					AND flag='1' ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$nmDokter = $row['dokter'];
			}
		}		
		
		
		$noKwitansi = substr($noTrans,6,8).'/'.'LAB-';
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
		$pdf->Cell(0,3,'KWITANSI PEMBAYARAN LABORATORIUM','0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,3,'No. Kwitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(20,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(20,10,$noTrans,0,0,'L');
		$pdf->Ln(3);
		
		if($jnsRujukan == '0')
		{
			$pdf->Cell(20,10,'No. CM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(20,10,$cm,0,0,'L');
			$pdf->Ln(3);
		}	
		
		
		$pdf->Cell(20,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(20,10,$nmPasien,0,0,'L');
		$pdf->Ln(3);
		$pdf->Cell(20,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(20,10,$nmDokter,0,0,'L');
		$pdf->Ln(3);
		$pdf->Cell(20,10,'Petugas Lab',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(20,10,UserRecord::finder()->find('nip = ?',$petugasLab)->real_name,0,0,'L');
					
		//Line break
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(10,3,'No.',1,0,'C');
		$pdf->Cell(80,3,'Nama Tindakan',1,0,'C');
		$pdf->Cell(20,3,'Harga',1,0,'C');	
		$pdf->Ln(3);
			
		//$sql="SELECT nama, total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(10,3,$j.'.',1,0,'C');
			
			if($row['id_tindakan']=='RUJ')
			{
				$pdf->Cell(80,3,'Rujukan',1,0,'L');	
			}
			else
			{
				$pdf->Cell(80,3,LabTdkRecord::finder()->findByPk($row['id_tindakan'])->nama,1,0,'L');
			}
			
			$pdf->Cell(20,3,'Rp ' . number_format($row['harga'],2,',','.'),1,0,'R');	
			$pdf->Ln(3);
			
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
		$pdf->Cell(40,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.KasirLab'));		
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',7);	
		$pdf->Cell(40,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>