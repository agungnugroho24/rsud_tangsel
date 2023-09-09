<?php
class cetakLapJasmedDokter extends SimakConf
{
	public function onLoad($param)
	{	
		//$nmTable=$this->Request['table'];
		//$nip=$this->Request['nip'];
		$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$kelompok=$this->Request['kelompok'];
		$perusahaan=$this->Request['perusahaan'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		
		$session=new THttpSession;
		$session->open();
		$sql = $session['cetakLapJasmedDok'];
		$sql .= ' ORDER BY cm';
				
		$pdf=new reportKeuangan('L','mm','a4');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetMargins(10,15,10);
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
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'LAPORAN JASA MEDIS DOKTER','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		/*
		$pdf->Cell(92,5,'',1,0,'C');
		$pdf->Cell(92,5,'',1,0,'C');		
		$pdf->Cell(92,5,'',1,0,'C');	
		$pdf->Ln(5);
		$pdf->Cell(92,5,'',1,0,'C');
		$pdf->Cell(92,5,'',1,0,'C');		
		$pdf->Cell(92,5,'',1,0,'C');
		
		if($nip)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($nip)->real_name,'0',0,'L');
		}
		*/
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok)
		{
			$pdf->Cell(100,5,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
		
		$pdf->Ln(5);	
		if($poli)
		{
			$pdf->Cell(100,5,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,'0',0,'L');
		}
		
		if($perusahaan)
		{
			$pdf->Cell(100,5,'Perusahaan : '.PerusahaanRecord::finder()->findByPk($perusahaan)->nama,'0',0,'L');
		}
				
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',11);		
		$pdf->Cell(40,5,'No Transaksi',1,0,'C');
		$pdf->Cell(25,5,'CM',1,0,'C');		
		$pdf->Cell(60,5,'Nama',1,0,'C');	
		$pdf->Cell(80,5,'Tindakan',1,0,'C');	
		$pdf->Cell(35,5,'Tarif',1,0,'C');
		$pdf->Cell(35,5,'Jasmed',1,0,'C');
		//$pdf->Cell(25,5,'Jaspel',1,0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		foreach($arrData as $row)
		{
			
			$pdf->SetFont('Arial','',11);	
			//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
			
			$pdf->Cell(40,5,$row['no_trans'],1,0,'C');
			$pdf->Cell(25,5,$row['cm'],1,0,'C');		
			$pdf->Cell(60,5,$row['nama'],1,0,'C');
			
			if($row['tindakan']=='1')
			{
				$pdf->Cell(80,5,'Spesialis',1,0,'C');
			}
			elseif($row['tindakan']=='2')
			{
				$pdf->Cell(80,5,'Umum',1,0,'C');
			}
			elseif($row['tindakan']=='3')
			{
				$pdf->Cell(80,5,'Kir',1,0,'C');
			}
			else
			{
				$pdf->Cell(80,5,$row['tindakan'],1,0,'C');
			}
			
			$pdf->Cell(35,5,number_format($row['total'],2,',','.'),1,0,'R');
			$pdf->Cell(35,5,number_format($row['jasmed_dok'],2,',','.'),1,0,'R');
			//$pdf->Cell(25,5,number_format($row['jaspel'],2,',','.'),1,0,'R');	
			$tot_tarif += $row['total'];
			$tot_jasmed_dok += $row['jasmed_dok'];
			//$tot_jaspelkesra += $row['jaspel'];
			//$totProfit += $profit;
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(205,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(35,5,number_format($tot_tarif,2,',','.'),1,0,'R');
		$pdf->Cell(35,5,number_format($tot_jasmed_dok,2,',','.'),1,0,'R');
		//$pdf->Cell(25,5,number_format($tot_jaspelkesra,2,',','.'),1,0,'R');	
		//$pdf->Cell(25,5,number_format($totProfit,2,',','.'),1,0,'R');	
		$pdf->Ln(7);
		//$pdf->SetFont('Arial','',9);
		//$pdf->Cell(200,5,'Data Penjualan dari : '.$penjualan,0,0,'L');
		//$pdf->Ln(5);
		//$pdf->Cell(200,5,'Disortir berdasarkan : '.$sortir,0,0,'L');
		//$jaspel=0.8*$tot_jaspelkesra;
		//$struk_admin=0.2*$tot_jaspelkesra;
		
		//$dok=0.6*$jaspel;
		//$paramedis=0.4*$jaspel;
		
		//$direktur=0.2*$struk_admin;
		//$administrasi=0.8*$struk_admin;
		
		//$manajemen=0.5*$administrasi;
		//$pelaksana=0.5*$administrasi;
		/*
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(200,7,'Total Penerimaan',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_tarif,2,',','.'),1,0,'R');
		$pdf->Ln(7);
		$pdf->Cell(170,7,'A. KESRA',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_kesra,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'R');
		$pdf->Ln(7);
		$pdf->Cell(170,7,'B. JASPEL KESRA',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_jaspelkesra,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(140,7,'     '.'a. Jaspel',1,0,'L');
		$pdf->Cell(30,7,'Rp. '.number_format($jaspel,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'a1. Dokter',1,0,'L');		
		
		$pdf->Cell(30,7,'Rp. '.number_format($dok,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'a2. Paramedis',1,0,'L');		
		
		$pdf->Cell(30,7,'Rp. '.number_format($paramedis,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(140,7,'     '.'b. Struktur Admin',1,0,'L');
		
		$pdf->Cell(30,7,'Rp. '.number_format($struk_admin,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'b1. Direktur',1,0,'L');		
		
		$pdf->Cell(30,7,'Rp. '.number_format($direktur,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'b2. Administrasi',1,0,'L');		
		
		$pdf->Cell(30,7,'Rp. '.number_format($administrasi,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(80,7,'                 '.'- Manajemen',1,0,'L');
		
		$pdf->Cell(30,7,'Rp. '.number_format($manajemen,1,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(80,7,'                 '.'- Pelaksana',1,0,'L');
		
		$pdf->Cell(30,7,'Rp. '.number_format($pelaksana,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		*/
		/*
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(30,7,'Total Penerimaan');
		$pdf->Cell(117,7,'...................................................................................................................................');
		$pdf->Cell(100,7,'Rp. '.number_format($tot_tarif,2,',','.'),0,0,'L');
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');		
		$pdf->Cell(23,7,'A. KESRA');
		$pdf->Cell(110,7,'........................................................................................................................');
		$pdf->Cell(100,7,'Rp. '.number_format($tot_kesra,2,',','.'));
		
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(30,7,'B. JASPEL KESRA');
		$pdf->Cell(103,7,'................................................................................................................');
		$pdf->Cell(93,7,'Rp. '.number_format($tot_jaspelkesra,2,',','.'));
		
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(23,7,'a. Jaspel ');
		$pdf->Cell(96,7,'.........................................................................................................');
		$pdf->Cell(100,7,'Rp. '.number_format($jaspel,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'1. Dokter ');
		$pdf->Cell(82,7,'...........................................................................................');
		$pdf->Cell(0,7,'Rp. '.number_format($dok,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'2. Paramedis ');
		$pdf->Cell(82,7,'...........................................................................................');
		$pdf->Cell(0,7,'Rp. '.number_format($paramedis,2,',','.'));
		
		
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(30,7,'b. Struktural Admin ');
		$pdf->Cell(89,7,'..................................................................................................');
		$pdf->Cell(100,7,'Rp. '.number_format($struk_admin,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'1. Direktur ');
		$pdf->Cell(82,7,'...........................................................................................');
		$pdf->Cell(0,7,'Rp. '.number_format($direktur,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'2. Administrasi ');
		$pdf->Cell(82,7,'...........................................................................................');
		$pdf->Cell(0,7,' Rp. '.number_format($administrasi,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'- Manajemen ');
		$pdf->Cell(68,7,'...........................................................................');
		$pdf->Cell(0,7,'Rp. '.number_format($manajemen,2,',','.'));
		$pdf->Ln(7);
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(7,7,'');
		$pdf->Cell(23,7,'- Pelaksana ');
		$pdf->Cell(68,7,'...........................................................................');
		$pdf->Cell(0,7,'Rp. '.number_format($pelaksana,2,',','.'));
		*/
		$pdf->Ln(10);
		
		
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(450,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(450,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapJasMedDokter'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>