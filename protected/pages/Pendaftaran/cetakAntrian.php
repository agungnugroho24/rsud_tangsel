<?php
class cetakAntrian extends SimakConf
{
	public function onLoad($param)
	{	
				
		$cm=$this->Request['cm'];
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];	
		$dateNow = date('Y-m-d');
		$url =$this->Service->constructUrl('Pendaftaran.DaftarRwtJln');
		$shift=$this->Request['shift'];	
		
		
		//$sql = "SELECT COUNT(*) AS jml_pas FROM tbt_rawat_jalan WHERE id_klinik = '$klinik' AND shift = '$shift' AND dokter = '$dokter' AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
		
		$sql = "SELECT COUNT(*) AS jml_pas FROM tbt_rawat_jalan WHERE id_klinik = '$klinik' AND shift = '$shift' AND dokter = '$dokter' AND tgl_visit = '$dateNow' ";
		
		$arr = $this->queryAction($sql,'S');
		
		foreach($arr as $row)
		{
			$jmlPas = $row['jml_pas'];
		}
		
		if(strlen($jmlPas) == 1)
		{
			$jmlPas = '00'.$jmlPas;
		}
		elseif(strlen($jmlPas) == 2)
		{
			$jmlPas = '0'.$jmlPas;
		}
		
                // Query mendapatkan Status
                $sql = "SELECT penjamin, perus_asuransi FROM tbt_rawat_jalan WHERE cm = '".$cm."' AND tgl_visit = '".$dateNow."' ORDER BY wkt_visit DESC";
                $arr = $this->queryAction($sql,'S');
                
                foreach( $arr as $row )
                {
                    $sql_penjamin = "SELECT nama FROM tbm_kelompok WHERE id = '".$row['penjamin']."'";
                    $arr_penjamin = $this->queryAction($sql_penjamin,'S');
                    
                    foreach( $arr_penjamin as $row1 )
                        $penjamin = $row1['nama'];
                    
                    $sql_perus_asuransi = "SELECT nama FROM tbm_perusahaan_asuransi WHERE id = '".$row['perus_asuransi']."'";
                    $arr_perus_asuransi = $this->queryAction($sql_perus_asuransi,'S');
                    
                    foreach( $arr_perus_asuransi as $row1 )
                        $perus_asuransi = $row1['nama'];
                    
                    break;
                }
                
		//$pdf=new reportAntrian('P','mm','kwt_antrian');		
		$pdf=new reportAntrian('P','mm',array(76,297));     // PT. GSK @2014
                $pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		$pdf->SetLeftMargin(5);
		$pdf->SetRightMargin(5);
		$pdf->SetY(5);
		
		//$pdf->Image('protected/pages/Tarif/logo1.jpg',5,4,25);	
		
		/*
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(15,5,'','0',0,'C');
		$pdf->Cell(0,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L'); */
		//$pdf->Ln(5);		
		
		$pdf->SetFont('Arial','',5);
		$pdf->Cell(25,5,'','0',0,'C');
		//$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'L');	
		$pdf->Ln(3);
		$pdf->Cell(25,5,'','0',0,'C');
		//$pdf->Cell(0,5,'Telp. (021) 74718440','0',0,'L');
		$pdf->Ln(1);
		$pdf->Cell(0,5,'','B',1,'C');
		//$pdf->Ln(3);	
		
		$pdf->SetFont('Arial','',7);
		
		$pdf->Cell(10,5,'Tanggal',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(68,5,$this->convertDate($dateNow,'3').', '.date('G:i:s').' WIB',0,0,'L');
		$pdf->Ln(3);
		
		$pdf->Cell(10,5,'Nama',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(68,5,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
		$pdf->Ln(3);
		
		$pdf->Cell(10,5,'Poli',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(68,5,PoliklinikRecord::finder()->findByPk($klinik)->nama,0,0,'L');
		$pdf->Ln(3);
		
		$pdf->Cell(10,5,'Dokter',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(68,5,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'L');
                $pdf->Ln(3);
                
                $pdf->Cell(10,5,'Status',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
                
                if( $perus_asuransi )
                    $pdf->Cell(68,5,$penjamin.' / '.$perus_asuransi,0,0,'L');
                
                else
                    $pdf->Cell(68,5,$penjamin,0,0,'L');
                    
		$pdf->Ln(3);
		
		$pdf->Cell(10,5,'','B',0,'C');	
		$pdf->Cell(46,5,'','0',0,'C');	
		$pdf->Cell(10,5,'','B',0,'C');	
		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',10);	
		//$pdf->Cell(0,0,'NOMOR ANTRIAN','0',0,'C','',$url);
		$pdf->Cell(0,0,'NOMOR REKAM MEDIS','0',0,'C','',$url);
		
		$pdf->Ln(3);						
		
		$pdf->SetFont('Arial','B',40);
		//$pdf->Cell(15,20,'','0',0,'C','',$url);
		$pdf->Cell(0,15,$cm,'0',0,'C','',$url);
		
		//$pdf->SetFont('Arial','B',100);
		//$pdf->Cell(15,20,'','0',0,'C','',$url);
		//$pdf->Cell(0,35,$jmlPas,'0',0,'C','',$url);
		//$pdf->Cell(15,20,'','0',0,'C','',$url);
				
		$pdf->Output();
		
	}
	
}
?>
