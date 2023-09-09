<?php
class cetakLapJasmedRwtJlnNoKop extends SimakConf
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
		//$nmTable=$this->Request['table'];
		$nip=$this->Request['nip'];
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
		
		$sql = "SELECT 
				  tbt_kasir_rwtjln.no_trans,
					  tbd_pasien.nama,
					  tbm_nama_tindakan.nama AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbm_kelompok.nama AS kelompok,
					  tbt_kasir_rwtjln.tarif,
					  (0.3 * tbt_kasir_rwtjln.tarif) AS kesra,
					  (0.7 * tbt_kasir_rwtjln.tarif) AS jaspel
					FROM
					  tbt_kasir_rwtjln
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
					  INNER JOIN tbm_poliklinik ON (tbm_nama_tindakan.id_klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_rwtjln.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
			
			if($perusahaan)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			//if($nip <> '')
				//$sql .= "AND tbt_kasir_rwtjln.operator = '$nip' ";		
						
			if($poli <> '')			
				$sql .= "AND tbt_kasir_rwtjln.klinik = '$poli' ";	
			
			if($dokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$dokter' ";
			
			if($kelompok <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$kelompok' ";
			
			if($perusahaan <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$perusahaan' ";
			
			if($tgl <> '' && $tglawal <> '' && $tglakhir <> '' && $bln <> '' && $thn <> '')
			{
					
				if($tgl <> '')			
					$sql .= "AND tbt_kasir_rwtjln.tgl = '$tgl' ";
				
				if($tglawal <> '' AND $tglakhir <> '')			
					$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				
				if($bln <> '' AND $thn <> '')			
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$bln' AND YEAR(tbt_kasir_rwtjln.tgl)='$thn' ";	
			}
			else
			{
				$bln=date('m');
				$thn=date('Y');
				
				if($tgl <> '')			
					$sql .= "AND tbt_kasir_rwtjln.tgl = '$tgl' ";
			
				if($tglawal <> '' AND $tglakhir <> '')			
					$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				
						
					$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$bln' AND YEAR(tbt_kasir_rwtjln.tgl)='$thn' ";
			}
			
			
			$sql .= " UNION ALL ";
			$sql .= "SELECT 
				  	  tbt_kasir_pendaftaran.no_trans,
					  tbd_pasien.nama,
					  tbt_kasir_pendaftaran.id_tindakan AS tindakan,
					  tbt_rawat_jalan.cm,
					  tbm_kelompok.nama AS kelompok,
					  tbt_kasir_pendaftaran.tarif,
					  (0.3 * tbt_kasir_pendaftaran.tarif) AS kesra,
					  (0.7 * tbt_kasir_pendaftaran.tarif) AS jaspel
					FROM
					  tbt_kasir_pendaftaran
					  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_pendaftaran.no_trans_pdftr = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_poliklinik ON (tbt_kasir_pendaftaran.klinik = tbm_poliklinik.id)
					  INNER JOIN tbd_pegawai ON (tbt_kasir_pendaftaran.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
			
			if($perusahaan)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			//if($nip <> '')
				//$sql .= "AND tbt_kasir_pendaftaran.operator = '$nip' ";		
						
			if($poli <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.klinik = '$poli' ";	
			
			if($dokter <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.dokter = '$dokter' ";
			
			if($kelompok <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$kelompok' ";
			
			if($perusahaan <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$perusahaan' ";
			
			if($tgl <> '' && $tglawal <> '' && $tglakhir <> '' && $bln <> '' && $thn <> '')
			{
				if($tgl <> '')			
					$sql .= "AND tbt_kasir_pendaftaran.tgl = '$tgl' ";
				
				if($tglawal <> '' AND $tglakhir <> '')			
					$sql .= "AND tbt_kasir_pendaftaran.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				
				if($bln <> '' AND $thn <> '')			
					$sql .= "AND MONTH (tbt_kasir_pendaftaran.tgl)='$bln' AND YEAR(tbt_kasir_pendaftaran.tgl)='$thn' ";
			}
			else
			{
				$bln=date('m');
				$thn=date('Y');
				
				if($tgl <> '')			
					$sql .= "AND tbt_kasir_pendaftaran.tgl = '$tgl' ";
				
				if($tglawal <> '' AND $tglakhir <> '')			
					$sql .= "AND tbt_kasir_pendaftaran.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
				
						
					$sql .= "AND MONTH (tbt_kasir_pendaftaran.tgl)='$bln' AND YEAR(tbt_kasir_pendaftaran.tgl)='$thn' ";
			}
				
		$pdf=new reportKeuangan('L','mm','a4');
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetMargins(10,15,10);
		
		$pdf->Cell(0,7,'LAPORAN JASA MEDIS','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',10);
		
		if($nip)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($nip)->real_name,'0',0,'L');
		}
		
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
		$pdf->SetFont('Arial','B',10);		
		$pdf->Cell(30,5,'No Transaksi',1,0,'C');
		$pdf->Cell(15,5,'CM',1,0,'C');		
		$pdf->Cell(45,5,'Nama',1,0,'C');	
		$pdf->Cell(65,5,'Tindakan',1,0,'C');	
		$pdf->Cell(24,5,'Tarif',1,0,'C');
		$pdf->Cell(24,5,'Sarana RS',1,0,'C');
		$pdf->Cell(24,5,'Jasmed',1,0,'C');
		$pdf->Cell(24,5,'Dokter',1,0,'C');
		$pdf->Cell(24,5,'Paramedis',1,0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		foreach($arrData as $row)
		{
			
			$pdf->SetFont('Arial','',9);	
			//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
			
			$pdf->Cell(30,5,$row['no_trans'],1,0,'C');
			$pdf->Cell(15,5,$row['cm'],1,0,'C');		
			$pdf->Cell(45,5,$row['nama'],1,0,'C');	
			
			if($row['tindakan']=='1')
			{
				$pdf->Cell(65,5,'Spesialis',1,0,'C');
			}
			elseif($row['tindakan']=='2')
			{
				$pdf->Cell(65,5,'Umum',1,0,'C');
			}
			elseif($row['tindakan']=='3')
			{
				$pdf->Cell(65,5,'Kir',1,0,'C');
			}
			else
			{
				$pdf->Cell(65,5,$row['tindakan'],1,0,'C');
			}
			
			//$pdf->Cell(40,5,$row['tindakan'],1,0,'C');			
			$pdf->Cell(24,5,number_format($row['tarif'],2,',','.'),1,0,'R');
			$pdf->Cell(24,5,number_format($row['kesra'],2,',','.'),1,0,'R');
			$pdf->Cell(24,5,number_format($row['jaspel'],2,',','.'),1,0,'R');	
			
			$dok = 0.6 * $row['jaspel'];
			$paramedis = 0.4 * $row['jaspel'];
			
			$pdf->Cell(24,5,number_format($dok,2,',','.'),1,0,'R');
			$pdf->Cell(24,5,number_format($paramedis,2,',','.'),1,0,'R');
			$tot_tarif += $row['tarif'];
			$tot_kesra += $row['kesra'];
			$tot_jaspelkesra += $row['jaspel'];
			$tot_dok += $dok;
			$tot_paramedis += $paramedis;
			//$totProfit += $profit;
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(155,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(24,5,number_format($tot_tarif,2,',','.'),1,0,'R');
		$pdf->Cell(24,5,number_format($tot_kesra,2,',','.'),1,0,'R');
		$pdf->Cell(24,5,number_format($tot_jaspelkesra,2,',','.'),1,0,'R');	
		$pdf->Cell(24,5,number_format($tot_dok,2,',','.'),1,0,'R');	
		$pdf->Cell(24,5,number_format($tot_paramedis,2,',','.'),1,0,'R');	
		//$pdf->Cell(25,5,number_format($totProfit,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
		//$pdf->SetFont('Arial','',9);
		//$pdf->Cell(200,5,'Data Penjualan dari : '.$penjualan,0,0,'L');
		//$pdf->Ln(5);
		//$pdf->Cell(200,5,'Disortir berdasarkan : '.$sortir,0,0,'L');
		//$paramedis=0.4*$tot_jaspelkesra;
		//$dok=0.6*$tot_jaspelkesra;		
		
		$pdf->SetMargins(30,15,10);
		$pdf->AddPage();
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(200,7,'Total Penerimaan',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_tarif,2,',','.'),1,0,'R');
		$pdf->Ln(7);
		$pdf->Cell(170,7,'A. Sarana Rumah Sakit',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_kesra,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'R');
		$pdf->Ln(7);
		$pdf->Cell(170,7,'B. Jasa Medis',1,0,'L');		
		$pdf->Cell(30,7,'Rp. '.number_format($tot_jaspelkesra,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'R');
		/*
		$pdf->Ln(7);		
		$pdf->Cell(140,7,'     '.'a. Jaspel',1,0,'L');
		$pdf->Cell(30,7,'Rp. '.number_format($jaspel,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		*/
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'a1. Dokter',1,0,'L');		
		
		$pdf->Cell(60,7,'Rp. '.number_format($tot_dok,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		
		$pdf->Ln(7);		
		$pdf->Cell(110,7,'          '.'a2. Paramedis',1,0,'L');		
		
		$pdf->Cell(60,7,'Rp. '.number_format($tot_paramedis,2,',','.'),1,0,'R');
		$pdf->Cell(30,7,'',1,0,'C');
		$pdf->Cell(30,7,'',1,0,'R');
		/*
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
		$pdf->Cell(400,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(400,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(400,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapJasmedRwtJln'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(400,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>