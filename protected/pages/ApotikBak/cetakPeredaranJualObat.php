<?php
class cetakPeredaranJualObat extends SimakConf
{
	public function onLoad($param)
	{		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$tglAwal=$this->Request['tglAwal'];
		$tglAkhir=$this->Request['tglAkhir'];
		$kateg=$this->Request['kateg'];
		$nmTable=$this->Request['table'];
		
		$periode = 'Periode : '.$this->ConvertDate($tglAwal,'3').' s.d. '.$this->ConvertDate($tglAkhir,'3');
		
		if($kateg =='1')
		{
			$nmKateg = 'Slow Moving';
		}
		elseif($kateg =='2')
		{
			$nmKateg = 'Fast Moving';
		}
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$pdf=new reportKwitansi('P','mm','a4');
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
		$pdf->Cell(0,5,'RINCIAN JUMLAH PENJUALAN OBAT/ALKES','0',0,'C');
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(20,5,'Tanggal Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,$this->convertDate(date('Y-m-d'),'3'),'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(20,5,'Kategori','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,$nmKateg,'0',0,'L');
		
		
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(20,5,'Kode Obat',1,0,'C');
		$pdf->Cell(65,5,'Nama Obat',1,0,'C');
		$pdf->Cell(45,5,'Jumlah Penjualan',1,0,'C');
		$pdf->Cell(45,5,'Persentase Penjualan (%)',1,0,'C');
		
		$pdf->Ln(6);
		$sql = "SELECT 
				  id_obat ,
				  nama,
				  id_harga,
				  jumlah,
				  tot_jml,
				  tgl,
				  persentase
				FROM
				 $nmTable 
				WHERE
					tgl BETWEEN '$tglAwal' AND '$tglAkhir' ";
					
			if($kateg <> '')
			{
				if($kateg == '1') //slow moving
				{
					$sql .= " AND persentase <= 20 "; 
				}
				else
				{
					$sql .= " AND persentase >= 80 ";
				}
			}
					
			$sql .= "	GROUP BY id_obat ORDER BY nama";
					
		
		//$pdf->MultiCell(53,8,$sql,0,0,'C');			
		//$pdf->Ln(5);	   
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$totalJual = $row['jual']*$row['jumlah'];
			
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(20,5,$row['id_obat'],1,0,'L');
			$pdf->Cell(65,5,$row['nama'],1,0,'L');
			$pdf->Cell(45,5,$row['jumlah'],1,0,'C');	
			$pdf->Cell(45,5,$row['persentase'],1,0,'C');
			$pdf->Ln(5);
			
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);			
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPeredaranJualObat'). '&purge=1'.'&table='.$nmTable);		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		//$pdf->MultiCell(53,8,$sql,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>