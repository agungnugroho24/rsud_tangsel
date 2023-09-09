<?php
class cetakLapPembelian extends SimakConf
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
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$cariNama=$this->Request['cariByNama'];
		$cariID=$this->Request['cariByID'];
		$advance=$this->Request['advance'];
		$cariTipe=$this->Request['cariTipe'];
		$cariGol=$this->Request['cariByGol'];
		$cariJenis=$this->Request['cariByJenis'];
		$cariKlas=$this->Request['cariByKlas'];
		$cariDerivat=$this->Request['cariByDerivat'];
		$cariPbf=$this->Request['cariByPbf'];
		$cariProd=$this->Request['cariByProd'];
		$cariSat=$this->Request['cariBySat'];
		
		
		$session=new THttpSession;
		$session->open();
		$sql = $session['cetakLapBeliSql'];
		$sql .= ' ORDER BY nama';
		
		$noKwitansi = substr($noTrans,6,6).'/'.'APT-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$pdf=new reportKwitansi('L','mm','a4');
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
		$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES','0',0,'C');
		$pdf->Ln(5);	
		
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(12,5,'Kode',1,0,'C');
		$pdf->Cell(45,5,'Obat',1,0,'C');
		
		$pdf->Cell(30,5,'Tgl. Faktur',1,0,'C');
		$pdf->Cell(65,5,'PBF',1,0,'C');
		$pdf->Cell(35,5,'Tgl. Jatuh Tempo',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(30,5,'Total',1,0,'C');
		$pdf->Ln(6);
		
		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$total=$row['beli']*$row['jumlah'];
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(12,5,$row['kode'],1,0,'C');
			$pdf->Cell(45,5,$row['nama'],1,0,'L');
			$pdf->Cell(30,5,$this->convertDate($row['tgl_faktur'],'3'),1,0,'C');
			$pdf->Cell(65,5,$row['nm_pbf'],1,0,'L');				
			$pdf->Cell(35,5,$this->convertDate($row['tgl_jth_tempo'],'3'),1,0,'C');
			$pdf->Cell(20,5,$row['jumlah'],1,0,'R');	
			$pdf->Cell(30,5,'Rp ' . number_format($row['hrg_ppn'],2,',','.'),1,0,'R');
			$pdf->Cell(30,5,'Rp ' . number_format($row['jumlah'] * $row['hrg_ppn'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			$grandTot += $row['jumlah'] * $row['hrg_ppn']	;
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(167,5,'Total ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.LapPembelian'));		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>