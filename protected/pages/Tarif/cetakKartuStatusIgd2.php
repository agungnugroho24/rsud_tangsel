<?php
class cetakKartuStatusIgd extends SimakConf
{
	public function onLoad($param)
	{					
		$cm=$this->Request['cm'];
		$mode=$this->Request['mode'];
		$tipeRawat=$this->Request['tipeRawat'];			
		
		if($mode == '1')
		{
			$url = $this->Service->constructUrl('Pendaftaran.DataPasDetail').'&cm='.$cm.'&tipeRawat='.$tipeRawat;
		}
		else
		{
			$url = $this->Service->constructUrl('Pendaftaran.DaftarBaru');
		}
		
		
		$cm1 = substr($cm,0,2);
		$cm2 = substr($cm,2,2);
		$cm3 = substr($cm,4,2);
		
		$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel AS jkel,
				  tbd_pasien.tgl_lahir AS tgl_lahir,
				  tbd_pasien.umur,
				  tbd_pasien.nm_pj AS penanggung,
				  tbd_pasien.alamat,
				  tbm_kelompok.nama AS penjamin,
				  tbm_pekerjaan.nama AS pekerjaan,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM				  
				  tbd_pegawai
				  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id),
				  tbm_pekerjaan,
				  tbm_kelompok,
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
				AND tbd_pasien.pekerjaan=tbm_pekerjaan.id
				AND tbd_pasien.kelompok=tbm_kelompok.id				
			";			
		
		$pdf=new reportBarcode('P','mm','legal');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();

		$pdf->SetLeftMargin(15);
		
		$pdf->Ln(6);								
		$arrData=$this->queryAction($sql,'R');
		foreach($arrData as $row)
		{
			$cm=$row['cm'];
			$nmPas=$row['nmPas'];
			$umur=$row['umur'];
			$tglLahir=$row['tgl_lahir'];
			$alamat=$row['alamat'];
			$pekerjaan=$row['pekerjaan'];
			$penanggung=$row['penanggung'];
			$penjamin=$row['penjamin'];
			$jkel=$row['jkel'];			
			if($jkel == '0')
				$jkel = 'Laki-laki';
			else
				$jkel = 'Perempuan';	
			
			$pengby=$row['pengby'];
			$penjamin=$row['penjamin'];
		}
		
		//Hitung Umur
		$thn=substr($tglLahir,0,4);
		$bln=substr($tglLahir,5,2);
		$hari=substr($tglLahir,8,2);
		
		$umurTmp = $this->get_age($thn, $bln, $hari);
		$umurTmp = explode('-',$umurTmp);
		
		if($umurTmp['2'] < 1)
		{
			$umur = $umurTmp['2'].' Tahun '.$umurTmp['1'].' Bulan '.$umurTmp['0'].' Hari';
		}
		else
		{
			$umur = $umur . ' Tahun';
		}
		
		$pdf->SetFont('Arial','',11);
		$date = date('d/m/Y');	
		$time = date('G:i:s');		
				
		$pdf->Cell(2,8,$date,0,0,'L','',$url);
		$pdf->Cell(155,5,'',0,0,'L');
		
		$pdf->SetFont('Arial','B',15);		
		$pdf->Cell(2,12,$cm1.' '.$cm2.' '.$cm3,0,0,'L');
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(2,12,$time.' WIB',0,0,'L');
		
		$pdf->Ln(21);
				
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$nmPas,0,0,'L');		
		$pdf->Cell(117,5,'',0,0,'L');
		$pdf->Cell(2,5,$jkel,0,0,'L');
		$pdf->Ln(7.5);
		
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$umur,0,0,'L');
		$pdf->Cell(117,5,'',0,0,'L');
		$pdf->Cell(2,5,$penanggung,0,0,'L');		
		$pdf->Ln(7.5);
		
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$alamat,0,0,'L');
		$pdf->Ln(7.5);
		
		$pdf->Cell(27,5,'',0,0,'L');
		$pdf->Cell(2,5,$pekerjaan,0,0,'L');
		$pdf->Cell(117,5,'',0,0,'L');
		$pdf->Cell(2,5,$penjamin,0,0,'L');		
		$pdf->Ln(199);
		
		/*
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(24,5,'',0,0,'L');
		$pdf->Cell(2,5,$cm,0,0,'L');
		$pdf->Cell(87,5,'',0,0,'L');
		$pdf->Cell(2,5,$cm,0,0,'L');
		$pdf->Ln(10);
		$pdf->Cell(24,5,'',0,0,'L');
		$pdf->Cell(2,5,$nmPas,0,0,'L');		
		$pdf->Cell(87,5,'',0,0,'L');
		$pdf->Cell(2,5,$nmPas,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(24,5,'',0,0,'L');
		$pdf->Code128(35,265,$cm,20,5);		
		$pdf->Cell(87,5,'',0,0,'L');
		$pdf->Code128(124,265,$cm,20,5);
		*/
		$pdf->Output();
		
	}
	
	
	public function get_age($birth_year, $birth_month, $birth_day)
	{
		$birth_month_days = date( t, mktime(0, 0, 0, $birth_month, $birth_day, $birth_year) );
	
		$current_year = date(Y);
		$current_month = date(n);
		$current_day = date(j);
		$current_month_days = date(t);
	
		if($current_month > $birth_month)
		{
			$yy = $current_year - $birth_year;
			$mm = $current_month - $birth_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_month_days;
			}
		}
	
		if($current_month < $birth_month)
		{
			$yy = $current_year - $birth_year - 1;
			$mm = 12 - $birth_month + $current_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_day;
			}
		}
	
		if($current_month==$birth_month)
		{
			if($current_day == $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = 0;
				$dd = 0;
			}
			
			if($current_day < $birth_day)
			{
				$yy = $current_year - $birth_year - 1;
				$mm = $birth_month + $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				
				if($dd > $current_month_days)
				{
					$mm += 1;
					$dd -= $current_day;
				}
			}
			
			if($current_day > $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				if($dd > $current_month_days)
				{
				$mm += 1;
				$mm -= $current_month;
				$dd -= $current_month_days;
				}
			}
		}
	
		$age = $dd.'-'.$mm.'-'.$yy;
		return $age;

    }
}
?>
