<?php
class cetakStokGudangKritis extends SimakConf
{
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		 if (!isset($this->Session['cetakStokSql'])) 				
		{
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterStokGudangKritis'));
		}
	 }
	 
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakStokSql'];
					
		//$session->remove('cetakStokSql');
			
		$tujuan=$this->Request['tujuan'];
		$jnsBarang=$this->Request['jnsBarang'];
		$tipe=$this->Request['tipe'];
		
		$pdf=new reportAdmBarang();
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
		$pdf->Cell(0,5,'LAPORAN STOK KRITIS OBAT DAN ALKES','0',0,'C');
		
		//$pdf->Ln(5);			
		//$pdf->MultiCell(0,5,$sql,'0',0,'L');
		
		$pdf->Ln(12);
		
		$pdf->SetFont('Arial','',9);
		
		if($tujuan <> '')
		{
			$pdf->Cell(50,5,'Tujuan : '.DesFarmasiRecord::finder()->findByPk($tujuan)->nama,0,0,'L');
		}
		
		if($jnsBarang <> '')
		{
			$pdf->Cell(50,5,'Jenis Barang : '.JenisBrgRecord::finder()->findByPk($jnsBarang)->nama,0,0,'L');
		}
		
		if($tipe == '0')
		{			
			$pdf->Cell(50,5,'Tipe Obat : Generik',0,0,'L');
		}
		elseif($tipe == '1')
		{			
			$pdf->Cell(50,5,'Tipe Obat : Non Generik',0,0,'L');
		}
		
		$pdf->Ln(10);		
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');		
		$pdf->Cell(30,5,'Batas Minimum',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(20,5,'Defisit',1,0,'C');
		$pdf->Cell(30,5,'Satuan',1,0,'C');
		
		$pdf->Ln(6);		
		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(80,5,$row['nama'],1,0,'L');							
			$pdf->Cell(30,5,$row['jml_min'],1,0,'C');	
			$pdf->Cell(20,5,$row['jumlah'],1,0,'C');	
			$pdf->Cell(20,5,$row['defisit'],1,0,'C');	
			$pdf->Cell(30,5,SatuanObatRecord::finder()->findByPk($row['sat'])->nama,1,0,'C');	
			$pdf->Ln(5);
			$grandTot +=$row['untung'];
			
		}				
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(125,8,'',0,0,'L');		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(125,8,'',0,0,'L');
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.MasterStokGudangKritis'));		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(125,8,'',0,0,'L');
		$pdf->Cell(53,8,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>