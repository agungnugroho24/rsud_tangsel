<?php
class cetakKartuPas extends SimakConf
{
	 public function onInit($param)
	 {		
			parent::onInit($param);
			$tmpVar=$this->authApp('0');
			/*if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
				$this->Response->redirect($this->Service->constructUrl('login'));*/
	 }
	 
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		$nama=$this->Request['nama'];
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];	
		$mode=$this->Request['mode'];	
		$dateNow = date('Y-m-d');
		
		//UPDATE st_cetak_kartupasien di tbd_pasien
		//$sql = "UPDATE tbd_pasien SET st_cetak_kartupasien = '1' WHERE cm = '$cm' ";
		//$this->queryAction($sql,'C');
		
		
		
		//$url = $this->Service->constructUrl('Pendaftaran.DaftarRwtJln').'&cm='.$cm;
		if($mode == '01')
			$url = $this->Service->constructUrl('Pendaftaran.DaftarCariData');
		else
			$url = $this->Service->constructUrl('Pendaftaran.cetakStatusMap').'&cm='.$cm;	
		
		$pdf = new reportBarcode('L','mm','kwt_kartu');		
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetY(5);
		$pdf->SetTopMargin(5);
		$pdf->SetLeftMargin(5);
		$pdf->SetRightMargin(5);
		$pdf->SetAutoPageBreak(true, $bottomMargin - 4 );
		//$pdf->footerTxt="*** KARTU PASIEN ***";
		
		//$pdf->Image('protected/pages/Tarif/logo1.jpg',5,6,25);	
		
		/*
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(6,5,'','0',0,'C');
		$pdf->Cell(0,5,'','0',0,'L'); 
		$pdf->Ln(3);		
		*/
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,2,35);	
		
		
		$pdf->SetFont('Arial','B',5);
		$pdf->Cell(0,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Image('protected/pages/Tarif/bakti-husada.png',65,2,18);
		
		$pdf->Ln(0);			
		$pdf->SetFont('Arial','',4);
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(2);
		$pdf->Cell(0,10,'Telp. (021) 74718440','0',0,'C');	
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(3,5,'','0',0,'C');
		//$pdf->Cell(0,5,'RSUD ARIFIN ACHMAD','0',0,'L','',$url);
		$pdf->Ln(1);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(8,5,'','0',0,'C');
		//$pdf->Cell(0,5,'Jl. Diponegoro No. 2 Pekanbaru','0',0,'L');	
		$pdf->Ln(2);
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(8,5,'','0',0,'C');
		//$pdf->Cell(0,5,'Telp.(0761) - 23418, 21618, 21657, Fax (0761) - 20253','0',0,'L');
		$pdf->Ln(1);
		//$pdf->Cell(0,4,'','B',1,'C');
		$pdf->Ln(7);	
		
		
		//$pdf->SetFont('Arial','B',8);		
		//$pdf->Cell(0,5,'KARTU PASIEN',0,0,'C');
		$pdf->Ln(4);
		
		
		$jkel = PasienRecord::finder()->findByPk($cm)->jkel;
		
		if($jkel == '0')
		{
			$jkel = 'L';	
		}
		else
		{
			$jkel = 'P';	
		}
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(5,5,'','0',0,'L');
		$pdf->Cell(2,5,'','0',0,'C');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(0,5,$nama,'0',0,'L','',$url);
		$pdf->Ln(4);
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(5,5,'','0',0,'L');
		$pdf->Cell(2,5,'','0',0,'C');
		$pdf->Cell(0,5,$jkel,'0',0,'L','',$url);
		$pdf->Ln(6);
		
		$pdf->Cell(49,5,'','0',0,'L');
		$pdf->Cell(0,5,$cm,'0',0,'L','',$url);
		$pdf->Code128(50,38,$cm,25,5);	
		
		//$pdf->Ln(5);
		
		$pdf->Output();
	}
}
?>
