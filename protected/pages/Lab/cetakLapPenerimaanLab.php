<?php
class cetakLapPenerimaanLab extends SimakConf
{
	public function onLoad($param)
	{	
		//$nmTable=$this->Request['table'];
		$nip=$this->Request['nip'];
		//$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		//$kelompok=$this->Request['kelompok'];
		//$perusahaan=$this->Request['perusahaan'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		
		$sql = "SELECT
					  tbt_lab_penjualan.no_trans AS no_trans,
					  tbt_lab_penjualan.cm AS cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan.harga AS harga
					FROM
					  tbt_lab_penjualan
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan.cm <> '' ";					  			
			/*
			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($this->getViewState('cariByNama') <> '')	
			{
				$cariNama=$this->getViewState('cariByNama');
				$sql .= "AND tbt_kasir_rwtjln.operator = '$cariNama' ";		
			}			
			if($this->getViewState('cariPoli') <> '')
			{		
				$cariPoli=$this->getViewState('cariPoli');
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";
			}	*/
			if($dokter <> '')
			{				
				$sql .= "AND tbt_lab_penjualan.dokter = '$dokter' ";
			}
			/*
			if($this->getViewState('urutBy') <> '')
			{
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			}
			/*
			if($this->getViewState('Company') <> '')
			{
				$Company=$this->getViewState('Company');
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			}
			*/
			if($tgl <> '')
			{
				$sql .= "AND tbt_lab_penjualan.tgl = '$tgl' ";
			}
			if($tglawal <> '' AND $tglakhir <>'')
			{
				$sql .= "AND tbt_lab_penjualan.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
			}
			
			if($bln <> '' AND $thn <>'')
			{
				$sql .= "AND MONTH (tbt_lab_penjualan.tgl)='$bln' AND YEAR(tbt_lab_penjualan.tgl)='$thn' ";
			}
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;			
			
			$sql .= " UNION ALL ";
			$sql .= "SELECT 
					  tbt_lab_penjualan_inap.no_trans,
					  tbt_lab_penjualan_inap.cm,
					  tbd_pasien.nama AS nmPas,
					  tbm_lab_tindakan.nama AS nmTdk,
					  tbt_lab_penjualan_inap.harga
					FROM
					  tbt_lab_penjualan_inap
					  INNER JOIN tbd_pegawai ON (tbt_lab_penjualan_inap.dokter = tbd_pegawai.id)
					  INNER JOIN tbd_pasien ON (tbt_lab_penjualan_inap.cm = tbd_pasien.cm)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					WHERE tbt_lab_penjualan_inap.cm <> '' ";					  			
			/*
			$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perus)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($cariNama <> '')
				$sql .= "AND tbt_kasir_pendaftaran.operator = '$cariNama' ";		
						
			if($cariPoli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$cariPoli' ";	
			*/
			if($dokter <> '')
			{				
				$sql .= "AND tbt_lab_penjualan_inap.dokter = '$dokter' ";
			}
			/*
			if($this->getViewState('urutBy') <> '')
			{
				$sql .= "AND tbd_pasien.kelompok = '$urutBy' ";
			}
			/*
			if($this->getViewState('Company') <> '')
			{
				$Company=$this->getViewState('Company');
				$sql .= "AND tbd_pasien.perusahaan = '$Company' ";		
			}
			*/
			if($tgl <> '')
			{
				$sql .= "AND tbt_lab_penjualan_inap.tgl = '$tgl' ";
			}
			if($tglawal <> '' AND $tglakhir <>'')
			{
				$sql .= "AND tbt_lab_penjualan_inap.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
			}
			
			if($bln <> '' AND $thn <>'')
			{
				$sql .= "AND MONTH (tbt_lab_penjualan_inap.tgl)='$bln' AND YEAR(tbt_lab_penjualan_inap.tgl)='$thn' ";
			}
			
			
			/*
			
			if($poli <> '')			
				$sql .= "AND tbm_poliklinik.id = '$poli' ";	
			
			if($dokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$dokter' ";
			
			if($kelompok <> '')			
				$sql .= "AND tbd_pasien.kelompok = '$kelompok' ";
			
			if($perusahaan <> '')			
				$sql .= "AND tbd_pasien.perusahaan = '$perusahaan' ";		
			
			if($tgl <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl = '$tgl' ";
			
			if($tgl <> '' AND $tgl <> '')			
				$sql .= "AND tbt_kasir_rwtjln.tgl BETWEEN '$tgl' AND '$tgl' ";
			
			if($bln <> '' AND $thn <> '')			
				$sql .= "AND MONTH (tbt_kasir_rwtjln.tgl)='$bln' AND YEAR(tbt_kasir_rwtjln.tgl)='$thn' ";	
		/*
		$sql = "SELECT 
				  tbt_kasir_rwtjln.no_trans AS no_trans,
				  tbd_pasien.nama AS nama,
				  tbm_nama_tindakan.nama AS tindakan,
				  tbt_rawat_jalan.cm AS cm,
				  tbt_kasir_rwtjln.total AS total,
				  tbm_kelompok.nama AS kelompok
				FROM
				  tbt_kasir_rwtjln
				  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
				  INNER JOIN tbd_user ON (tbt_kasir_rwtjln.operator = tbd_user.nip)
				  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
				  INNER JOIN tbm_nama_tindakan ON (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
				  INNER JOIN tbm_poliklinik ON (tbm_nama_tindakan.id_klinik = tbm_poliklinik.id)
				  INNER JOIN tbd_pegawai ON (tbt_kasir_rwtjln.dokter = tbd_pegawai.id)
				  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) " ;
				
			//$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perusahaan)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($nip <> '')
				$sql .= "AND tbt_kasir_rwtjln.operator = '$nip' ";		
						
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
				  tbt_kasir_pendaftaran.no_trans AS no_trans,
				  tbd_pasien.nama AS nama,
				  tbt_kasir_pendaftaran.id_tindakan AS tindakan,
				  tbt_rawat_jalan.cm AS cm,
				  tbt_kasir_pendaftaran.tarif AS total,
				  tbm_kelompok.nama AS kelompok
				FROM
				  tbt_kasir_pendaftaran
				  INNER JOIN tbt_rawat_jalan ON (tbt_kasir_pendaftaran.no_trans_pdftr = tbt_rawat_jalan.no_trans)
				  INNER JOIN tbd_user ON (tbt_kasir_pendaftaran.operator = tbd_user.nip)
				  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
				  INNER JOIN tbm_poliklinik ON (tbt_kasir_pendaftaran.klinik = tbm_poliklinik.id)
				  INNER JOIN tbd_pegawai ON (tbt_kasir_pendaftaran.dokter = tbd_pegawai.id)
				  INNER JOIN tbm_kelompok ON (tbd_pasien.kelompok = tbm_kelompok.id) ";
						
			//$perus=PasienRecord::finder()->find('cm = ?','tbd_pasien.cm')->perusahaan;
			if($perusahaan)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (tbd_pasien.perusahaan = tbm_perusahaan.id)
						WHERE tbd_pasien.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE tbd_pasien.nama <> '' ";	
			}
			
			if($nip <> '')
				$sql .= "AND tbt_kasir_pendaftaran.operator = '$nip' ";		
						
			if($poli <> '')			
				$sql .= "AND tbt_kasir_pendaftaran.klinik = '$poli' ";	
			
			if($dokter <> '')			
				$sql .= "AND tbt_rawat_jalan.dokter = '$dokter' ";
			
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
		*/		
		$pdf=new reportKasir();
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetMargins(40,30,40);
		
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
		$pdf->Cell(0,7,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'LAPORAN PENERIAMAAN LABORATORIUM','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
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
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',11);		
		$pdf->Cell(40,5,'No Transaksi',1,0,'C');
		$pdf->Cell(25,5,'CM',1,0,'C');		
		$pdf->Cell(70,5,'Nama',1,0,'C');	
		$pdf->Cell(90,5,'Tindakan',1,0,'C');	
		$pdf->Cell(50,5,'Harga',1,0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		foreach($arrData as $row)
		{
			
			$pdf->SetFont('Arial','',11);	
			//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
			
			$pdf->Cell(40,5,$row['no_trans'],1,0,'C');
			$pdf->Cell(25,5,$row['cm'],1,0,'C');		
			$pdf->Cell(70,5,$row['nmPas'],1,0,'C');	
			$pdf->Cell(90,5,$row['nmTdk'],1,0,'C');
			
			//$pdf->Cell(65,5,$row['tindakan'],1,0,'C');			
			$pdf->Cell(50,5,number_format($row['harga'],2,',','.'),1,0,'R');	
			$tot += $row['harga'];
			//$totProfit += $profit;
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(225,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(50,5,number_format($tot,2,',','.'),1,0,'R');	
		//$pdf->Cell(25,5,number_format($totProfit,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
		//$pdf->SetFont('Arial','',9);
		//$pdf->Cell(200,5,'Data Penjualan dari : '.$penjualan,0,0,'L');
		//$pdf->Ln(5);
		//$pdf->Cell(200,5,'Disortir berdasarkan : '.$sortir,0,0,'L');
		
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(450,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(450,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Lab.lapPenerimaanLab'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>