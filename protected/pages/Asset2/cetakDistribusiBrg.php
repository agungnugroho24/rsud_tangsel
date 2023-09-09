<?php
class cetakDistribusiBrg extends SimakConf
{
	 
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['noTrans'];	
		
		$idTujuan = AssetDistBrgRecord::finder()->find('no_trans=?',$noTrans)->tujuan; 
		$tujuan = AssetRuangRecord::finder()->findByPk($idTujuan)->nama;
		
		$idPengirim = AssetDistBrgRecord::finder()->find('no_trans=?',$noTrans)->pengirim; 
		$pengirim = PegawaiRecord::finder()->findByPk($idPengirim)->nama;
		
		$idPenerima = AssetDistBrgRecord::finder()->find('no_trans=?',$noTrans)->penerima; 
		$penerima = PegawaiRecord::finder()->findByPk($idPenerima)->nama;
		
		$pdf=new reportAdmBarang('P','mm','kwt');
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
		$pdf->Cell(0,5,'SURAT BUKTI DISTRIBUSI BARANG/ASSET','0',0,'C','',$this->Service->constructUrl('Asset.admDistribusiBarang'));
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. : '.$noTrans,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(15,10,'Tanggal',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,date("d").' '.$this->namaBulan(date("m")).' '.date("Y"),0,0,'L');
		
		$pdf->Cell(75,10,'',0,0,'L');
		
		$pdf->Cell(15,10,'Tujuan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$tujuan,0,0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(15,10,'Pengirim',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$pengirim,0,0,'L');
		
		$pdf->Cell(75,10,'',0,0,'L');
		
		$pdf->Cell(15,10,'Penerima',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$penerima,0,0,'L');		
		$pdf->Ln(5);
		//Line break
		$pdf->Ln(6);
		$pdf->SetFont('Arial','B',9);		
		$pdf->Cell(10,5,'No',1,0,'C');
		$pdf->Cell(15,5,'Kode',1,0,'C');
		$pdf->Cell(140,5,'Nama Barang',1,0,'C');
		$pdf->Cell(25,5,'Jumlah',1,0,'C');	
		$pdf->Ln(5);
			
		$sql="SELECT id_brg,jml FROM tbt_asset_dist_brg  WHERE no_trans = '$noTrans'";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		$no = 0;
		foreach($arrData as $row)
		{	
			$no++;
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(10,5,$no.'.',1,0,'C');
			$pdf->Cell(15,5,$row['id_brg'],1,0,'C');
			$pdf->Cell(140,5,AssetBarangRecord::finder()->findByPk($row['id_brg'])->nama,1,0,'L');
			$pdf->Cell(25,5,$row['jml'],1,0,'C');	
			$pdf->Ln(5);
		}				
		
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'',0,0,'C');	
		
		$pdf->Ln(5);
		$pdf->Cell(80,8,'Petugas,',0,0,'C');
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'Penerima,',0,0,'C');	
		
		$pdf->Ln(10);
		$pdf->Cell(80,8,'( '.$pengirim.' )',0,0,'C','',$this->Service->constructUrl('Asset.admDistribusiBarang'));		
		$pdf->Cell(30,8,'',0,0,'C');		
		$pdf->Cell(80,8,'( '.$penerima.' )',0,0,'C','',$this->Service->constructUrl('Asset.admDistribusiBarang'));
					
		//$pdf->Ln(5);			
		
		//$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.bbkObat') . '&purge=1&nmTable=' . $nmTable);		
		//$pdf->Ln(5);
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>