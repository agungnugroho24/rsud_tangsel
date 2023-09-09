<?php
class cetakStokGudangBaruNoKop extends SimakConf
{
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
		
		//if (!isset($this->Session['cetakStokSql'])) 				
		//{
		//	$this->Response->redirect($this->Service->constructUrl('Apotik.MasterStokGudangBaru'));
		//}
	 }
	 
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		//$session=new THttpSession;
		//$session->open();
		//$sql = $session['cetakStokSql'];
		//$sql .= ' ORDER BY nama';
		//$session->remove('cetakStokSql');
			
		$tujuan=$this->Request['tujuan'];
		$jnsBarang=$this->Request['jnsBarang'];
		$tipe=$this->Request['tipe'];
		$nmTable=$this->Request['nmTable'];
		
		$sql="SELECT * FROM $nmTable  ORDER BY nama";	
		
		$pdf=new reportAdmBarang();
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->Ln(1);
			
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(0,5,'LAPORAN PENERIMAAN & PEMAKAIAN OBAT','0',0,'C');
		$pdf->Ln(5);		
		//$pdf->Cell(0,5,'APOTIK RS. DAA BANJARHARJO','0',0,'C');
		//$pdf->Ln(5);			
		
		$pdf->SetFont('Arial','',8);
		$bln = date('m');
		$bln = BulanRecord::finder()->findByPk($bln)->nama;
		$thn = substr(date('Y'),2,2);
		$pdf->Cell(0,5,$bln.' - '.$thn,'0',0,'C');
		
		//$pdf->MultiCell(0,5,$sql,'0',0,'L');
		
		$pdf->Ln(7);
		
		$pdf->SetFont('Arial','',8);
		
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
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(10,3,'','RTL',0,'C');
		$pdf->Cell(45,3,'','RTL',0,'C');
		$pdf->Cell(20,3,'','RTL',0,'C');
		$pdf->Cell(10,3,'Saldo','RTL',0,'C');
		$pdf->Cell(15,3,'','RTL',0,'C');
		$pdf->Cell(22,6,'Saldo','RTL',0,'C');
		$pdf->Cell(15,3,'Saldo','RTL',0,'C');
		$pdf->Cell(22,6,'Saldo','RTL',0,'C');
		$pdf->Cell(10,3,'','RTL',0,'C');
		$pdf->Cell(22,6,'Selisih','RTL',0,'C');
		
		$pdf->Ln(3);
		$pdf->Cell(10,3,'No.','RL',0,'C');
		$pdf->Cell(45,3,'Nama Alat / Obat','RL',0,'C');
		$pdf->Cell(20,3,'Harga Beli','RL',0,'C');
		$pdf->Cell(10,3,'System','RL',0,'C');
		$pdf->Cell(15,3,'Sat','RL',0,'C');
		$pdf->Cell(22,6,'(Rupiah)','RL',0,'C');
		$pdf->Cell(15,3,'Stok','RL',0,'C');
		$pdf->Cell(22,6,'(Rupiah)','RL',0,'C');
		$pdf->Cell(10,3,'Selisih','RL',0,'C');
		$pdf->Cell(22,6,'(Rupiah)','RL',0,'C');
		
		$pdf->Ln(3);
		$pdf->Cell(10,3,'','RBL',0,'C');
		$pdf->Cell(45,3,'','RBL',0,'C');
		$pdf->Cell(20,3,'','RBL',0,'C');
		$pdf->Cell(10,3,'(Unit)','RBL',0,'C');
		$pdf->Cell(15,3,'','RBL',0,'C');
		$pdf->Cell(22,3,'','RBL',0,'C');
		$pdf->Cell(15,3,'Opname','RBL',0,'C');
		$pdf->Cell(22,3,'','RBL',0,'C');
		$pdf->Cell(10,3,'','RBL',0,'C');
		$pdf->Cell(22,3,'','RBL',0,'C');
		
		$pdf->Ln(4);		
		
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(45,5,$row['nama'],1,0,'L');							
			$pdf->Cell(20,5,number_format($row['hrg'],2,',','.'),1,0,'R');	
			$pdf->Cell(10,5,$row['jumlah'],1,0,'R');	
			$pdf->Cell(15,5,SatuanObatRecord::finder()->findByPk($row['sat'])->nama,1,0,'C');
			
			if($row['jumlah'] <> 0)
			{
				$pdf->Cell(22,5,number_format($row['saldo_rp_sistem'],2,',','.'),1,0,'R');	
			}
			else
			{
				$pdf->Cell(22,5,'-',1,0,'R');	
			}
						
			$pdf->Cell(15,5,$row['jml_fisik'],1,0,'R');	
			
			
			if($row['jumlah'] <> 0)
			{
				$pdf->Cell(22,5,number_format($row['saldo_rp_fisik'],2,',','.'),1,0,'R');
			}
			else
			{
				$pdf->Cell(22,5,'-',1,0,'R');
			}
			
			if($row['jumlah'] <> 0)
			{
				if($row['selisih_jml'] < 0)
				{
					$pdf->Cell(10,5,'('.abs($row['selisih_jml']).')',1,0,'R');	
				}	
				else
				{
					$pdf->Cell(10,5,$row['selisih_jml'],1,0,'R');
				}	
			}
			else
			{
				$pdf->Cell(10,5,'-',1,0,'R');
			}	
			
			
			if($row['jumlah'] <> 0)
			{
				if($row['selisih_rp'] < 0)
				{
					$pdf->Cell(22,5,'('.number_format(abs($row['selisih_rp']),2,',','.').')',1,0,'R');	
				}	
				else
				{
					$pdf->Cell(22,5,number_format($row['selisih_rp'],2,',','.'),1,0,'R');	
				}	
			}
			else
			{
				$pdf->Cell(22,5,'-',1,0,'R');	
			}	
				
			$pdf->Ln(5);
			
			$tot_saldo_rp_sistem +=$row['saldo_rp_sistem'];
			$tot_saldo_rp_fisik +=$row['saldo_rp_fisik'];
			$tot_selisih_rp +=$row['selisih_rp'];
		}				
		
		$pdf->SetFont('Arial','B',7);	
		$pdf->Cell(10,3,'',1,0,'C');
		$pdf->Cell(45,3,'',1,0,'C');
		$pdf->Cell(20,3,'',1,0,'C');
		$pdf->Cell(10,3,'',1,0,'C');
		$pdf->Cell(15,3,'',1,0,'C');
		$pdf->Cell(22,3,number_format($tot_saldo_rp_sistem,2,',','.'),1,0,'R');//
		$pdf->Cell(15,3,'',1,0,'C');
		$pdf->Cell(22,3,number_format($tot_saldo_rp_fisik,2,',','.'),1,0,'R');//
		$pdf->Cell(10,3,'',1,0,'C');
		
		if($tot_selisih_rp < 0)
		{
			$pdf->Cell(22,3,'('.number_format(abs($tot_selisih_rp),2,',','.').')',1,0,'R');//
		}	
		else
		{
			$pdf->Cell(22,3,number_format($tot_selisih_rp,2,',','.'),1,0,'R');//
		}	
		
		
		
		$pdf->SetFont('Arial','',8);	
		$pdf->Cell(100,8,'',0,0,'L');		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(100,8,'',0,0,'L');		
		$pdf->Cell(80,8,'Mengetahui,',0,0,'C');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',8);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(100,8,'',0,0,'L');
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.MasterStokGudangBaru') . '&purge=1&nmTable=' . $nmTable);		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',8);	
		$pdf->Cell(100,8,'',0,0,'L');
		$pdf->Cell(80,8,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
		
	}
	
}
?>