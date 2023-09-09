<?php
class cetakLapPenerimaanKasirRwtInapNoKop extends SimakConf
{
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		//$nip=$this->Request['nip'];
		//$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$kasir=$this->Request['kasir'];
		$kelompok=$this->Request['kelompok'];
		//$perusahaan=$this->Request['perusahaan'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		$modeInput=$this->Request['modeInput'];
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		//$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		//$nmTable=$this->Request['table'];
		
		/*		
		if($modeInput == '0') //mode global
		{
			$mode = "global";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - GLOBAL";
		}
		elseif($modeInput == '1') //mode tunai
		{
			$mode = "tunai";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - TUNAI";
		}
		elseif($modeInput == '2') //mode piutang
		{
			$mode = "piutang";
			$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP - PIUTANG";
		}
		*/
		
		$headTxt = "LAPORAN PENERIMAAN KASIR RAWAT INAP";
		
		 $sql = "SELECT 
						no_trans,
						cm,
						tgl_masuk,
						tgl_keluar,
						nama,
						lama_inap,
						hrg_kamar,
						status,
						jml_jasa_kamar,						
						jml_operasi_billing,
						jml_penunjang,
						jml_obat_alkes_kredit,
						jml_obat_alkes_tunai_lunas,
						jml_obat_alkes_tunai_piutang,
						jml_total_biaya_lab_rad_kredit,
						jml_total_biaya_lab_rad_tunai_lunas,
						jml_total_biaya_lab_rad_tunai_piutang,
						jml_total_biaya_alih,
						jml_biaya_askep,
						jml_biaya_askeb,
						jml_biaya_askep_ok,												
						jml_biaya_adm,
						jml_biaya_oksigen,
						
						(jml_obat_alkes_kredit
							+jml_total_biaya_lab_rad_kredit
							+jml_total_biaya_alih
							+jml_biaya_askep
							+jml_biaya_askeb
							+jml_biaya_askep_ok
						) AS jml_biaya_lain,
						
						'$mode' AS mode
							
					FROM 
						view_lap_terima_rwtinap 
					  WHERE 
					  	view_lap_terima_rwtinap.cm <> ''";				  			
			
			if($cm <> '')			
				$sql .= "AND view_lap_terima_rwtinap.cm = '$cm' ";	
			
			//if($poli <> '')			
				//$sql .= "AND view_lap_terima_rwtinap.id = '$poli' ";	
			
			if($dokter <> '')			
				$sql .= "AND view_lap_terima_rwtinap.dokter = '$dokter' ";
			
			if($kasir <> '')			
				$sql .= "AND view_lap_terima_rwtinap.kasir = '$kasir' ";
				
			if($kelompok <> '')			
				$sql .= "AND view_lap_terima_rwtinap.kelompok = '$kelompok' ";
			
			//if($perusahaan <> '')			
				//$sql .= "AND tbd_pasien.perusahaan = '$perusahaan' ";		
			
			if($tgl <> '')			
				$sql .= "AND view_lap_terima_rwtinap.tgl_keluar = '$tgl' ";
			
			if($tglawal <> '' AND $tglakhir <> '')			
				$sql .= "AND view_lap_terima_rwtinap.tgl_keluar BETWEEN '$tglawal' AND '$tglakhir' ";
			
			if($bln <> '' AND $thn <> '')			
				$sql .= "AND MONTH (view_lap_terima_rwtinap.tgl_keluar)='$bln' AND YEAR(view_lap_terima_rwtinap.tgl_keluar)='$thn' ";
			/*
			if($modeInput <> '0')
			{
				if($modeInput == '1') //mode tunai
				{
					//$sql .= "AND view_lap_terima_rwtinap.status = '1' ";
				}
				elseif($modeInput == '2') //mode piutang
				{
					$sql .= "AND view_lap_terima_rwtinap.status = '0' ";
				}				
			}
			*/
			
			$sql .= "AND view_lap_terima_rwtinap.status = '1' ";
			
		$pdf=new reportKeuangan('L','mm','a4');
		$pdf->SetTopMargin(55);
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		$pdf->SetMargins(10,15,10);
		
	
		$pdf->SetFont('Arial','BU',12);
		
		//$pdf->Cell(0,7,'LAPORAN PENERIAMAAN KASIR RAWAT INAP','0',0,'C');
		$pdf->Cell(0,7,$headTxt,'0',0,'C');		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(0,5,$periode,'0',0,'C');
		
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		if($kasir)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,'0',0,'L');
		}
		
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok)
		{
			$pdf->Cell(100,5,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
		/*
		$pdf->Ln(5);	
		if($poli)
		{
			$pdf->Cell(100,5,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,'0',0,'L');
		}
		
		if($perusahaan)
		{
			$pdf->Cell(100,5,'Perusahaan : '.PerusahaanRecord::finder()->findByPk($perusahaan)->nama,'0',0,'L');
		}				
		*/
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',9);		
		$pdf->Cell(28,10,'No Transaksi','RTL',0,'C');
		$pdf->Cell(13,10,'CM','RTL',0,'C');		
		$pdf->Cell(50,10,'Nama','RTL',0,'C');	
		$pdf->Cell(27,5,'Fasilitas RS','RTL',0,'C');	
		$pdf->Cell(27,5,'Obat-Obatan &','RTL',0,'C');
		$pdf->Cell(27,5,'Tenaga','RTL',0,'C');
		$pdf->Cell(27,5,'Pemeriksaan','RTL',0,'C');
		$pdf->Cell(27,5,'Biaya Lain','RTL',0,'C');
		$pdf->Cell(26,5,'Biaya','RTL',0,'C');	
		$pdf->Cell(27,10,'Total',1,0,'C');	
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',9);		
		$pdf->Cell(28,5,'','RBL',0,'C');
		$pdf->Cell(13,5,'','RBL',0,'C');		
		$pdf->Cell(50,5,'','RBL',0,'C');	
		$pdf->Cell(27,5,'Rumah Sakit','RBL',0,'C');	
		$pdf->Cell(27,5,'Alat Kesehatan','RBL',0,'C');
		$pdf->Cell(27,5,'Ahli','RBL',0,'C');
		$pdf->Cell(27,5,'Penunjang','RBL',0,'C');
		$pdf->Cell(27,5,'Lain - Lain','RBL',0,'C');	
		$pdf->Cell(26,5,'Administrasi','RBL',0,'C');	
		$pdf->Cell(27,5,'','RBL',0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...	
		
		foreach($arrData as $row)
		{	
			//----------------------------------- Jasa Kamar -----------------------------------
			if($row['mode']=='global' || $row['mode']=='')
			{
				if($row['status']==0 )
				{
					$lamaInap=$this->hitTgl($row['tgl_masuk'],date('Y-m-d'));				
					$biayaKmr = $lamaInap * $row['hrg_kamar'];				
				}
				else
				{												
					$biayaKmr = $row['lama_inap'] * $row['hrg_kamar'];
				}
			}
			elseif($row['mode']=='tunai')
			{
				if($row['status']=='1')
				{
					$biayaKmr = $row['lama_inap'] * $row['hrg_kamar'];
				}
				else
				{
					$biayaKmr = 0;
				}
			}		
			elseif($row['mode']=='piutang')
			{												
				$lamaInap=$this->hitTgl($row['tgl_masuk'],date('Y-m-d'));			
				$biayaKmr = $lamaInap * $row['hrg_kamar'];
			}		
			
			//----------------------------------- Obat Alkes -----------------------------------
			if($row['mode']=='global' || $row['mode']=='')
			{
				$biayaObat = $row['jml_obat_alkes_kredit'] 
							+ $row['jml_obat_alkes_tunai_lunas']
							+ $row['jml_obat_alkes_tunai_piutang'];
			}
			elseif($row['mode']=='tunai')
			{
				$biayaObat =  $row['jml_obat_alkes_tunai_lunas'];														
			
				if($row['status']=='1')
				{
					$biayaObat = $biayaObat
								+ $row['jml_obat_alkes_kredit']
								+ $row['jml_obat_alkes_tunai_piutang'];
				}
			}		
			elseif($row['mode']=='piutang')
			{
				$biayaObat = $row['jml_obat_alkes_kredit'] 
							+ $row['jml_obat_alkes_tunai_piutang'];
			}
			
			//----------------------------------- Jasa Tenaga Ahli -----------------------------------
			if($row['mode']=='global' || $row['mode']=='')
			{
				if($row['jml_operasi_billing'] == NULL)
				{
					$biayaOperasi = 0;
				}
				else
				{
					$biayaOperasi = $row['jml_operasi_billing'];													
				}
			}
			elseif($row['mode']=='tunai')
			{
				if($row['status']=='1')
				{
					if($row['jml_operasi_billing'] == NULL)
					{
						$biayaOperasi = 0;
					}
					else
					{
						$biayaOperasi = $row['jml_operasi_billing'];													
					}
				}
				else
				{
					$biayaOperasi = 0;
				}
			}		
			elseif($row['mode']=='piutang')
			{
				if($row['jml_operasi_billing'] == NULL)
				{
					$biayaOperasi = 0;
				}
				else
				{
					$biayaOperasi = $row['jml_operasi_billing'];													
				}
			}
			
			//----------------------------------- Jasa Penunjang -----------------------------------
			if($row['mode']=='global' || $row['mode']=='')
			{
				if($row['jml_penunjang'] == NULL)
				{
					$biayaPenunjang = 0;
				}
				else
				{
					$biayaPenunjang = $row['jml_penunjang'];													
				}
			}
			elseif($row['mode']=='tunai')
			{
				if($row['status']=='1')
				{
					if($row['jml_penunjang'] == NULL)
					{
						$biayaPenunjang = 0;
					}
					else
					{
						$biayaPenunjang = $row['jml_penunjang'];													
					}
				}
				else
				{
					$biayaPenunjang = 0;
				}
			}		
			elseif($row['mode']=='piutang')
			{
				if($row['jml_penunjang'] == NULL)
				{
					$biayaPenunjang = 0;
				}
				else
				{
					$biayaPenunjang = $row['jml_penunjang'];													
				}
			}
			
			
			//----------------------------------- Biaya Lain -----------------------------------
			if($row['mode']=='global' || $row['mode']=='')
			{
				$biayaLain =  $row['jml_total_biaya_lab_rad_tunai_lunas']
							+ $row['jml_total_biaya_lab_rad_kredit']
							+ $row['jml_total_biaya_lab_rad_tunai_piutang']
							+ $row['jml_total_biaya_alih']
							+ $row['jml_biaya_askep']
							+ $row['jml_biaya_askeb']
							+ $row['jml_biaya_askep_ok']
							+ $row['jml_biaya_oksigen'];							
			}
			elseif($row['mode']=='tunai')
			{
				$biayaLain =  $row['jml_total_biaya_lab_rad_tunai_lunas'];															
			
				if($row['status']=='1')
				{
					$biayaLain = $biayaLain
								+ $row['jml_total_biaya_lab_rad_kredit']
								+ $row['jml_total_biaya_lab_rad_tunai_piutang']
								+ $row['jml_total_biaya_alih']
								+ $row['jml_biaya_askep']
								+ $row['jml_biaya_askeb']
								+ $row['jml_biaya_askep_ok']
								+ $row['jml_biaya_oksigen'];
				}
			}		
			elseif($row['mode']=='piutang')
			{
				$biayaLain = $row['jml_total_biaya_lab_rad_kredit'] 
							+ $row['jml_total_biaya_lab_rad_tunai_piutang']
							+ $row['jml_total_biaya_alih']
							+ $row['jml_biaya_askep']
							+ $row['jml_biaya_askeb']
							+ $row['jml_biaya_askep_ok']
							+ $row['jml_biaya_oksigen'];
			}
			
			
			$pdf->SetFont('Arial','',9);	
			//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
			
			$pdf->Cell(28,5,$row['no_trans'],1,0,'C');
			$pdf->Cell(13,5,$row['cm'],1,0,'C');		
			$pdf->Cell(50,5,$row['nama'],1,0,'C');	
			
			$pdf->Cell(27,5,number_format($biayaKmr,2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($biayaObat,2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($biayaOperasi,2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($biayaPenunjang,2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($biayaLain,2,',','.'),1,0,'R');
			$pdf->Cell(26,5,number_format($row['jml_biaya_adm'],2,',','.'),1,0,'R');	
			
			$total = $biayaKmr + $biayaObat + $biayaOperasi + $biayaPenunjang+ $biayaLain + $row['jml_biaya_adm'] ;
			/*
			$biayaLain = $row['jml_total_biaya_lab_rad_kredit'] 
						+ $row['jml_biaya_askep']
						+ $row['jml_biaya_askeb']
						+ $row['jml_biaya_askep_ok'];
															
			$pdf->Cell(27,5,number_format($row['jml_jasa_kamar'],2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($row['jml_obat_alkes_kredit'],2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($row['jml_operasi_billing'],2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($row['jml_penunjang'],2,',','.'),1,0,'R');	
			$pdf->Cell(27,5,number_format($biayaLain,2,',','.'),1,0,'R');
			$pdf->Cell(26,5,number_format($row['jml_biaya_adm'],2,',','.'),1,0,'R');	
						
			$total = $row['jml_jasa_kamar'] + $row['jml_obat_alkes_kredit'] + $row['jml_operasi_billing'] + $row['jml_penunjang'] + $biayaLain + $row['jml_biaya_adm'] ;
			*/
				
			//$pdf->Cell(65,5,$row['tindakan'],1,0,'C');			
			$pdf->Cell(27,5,number_format($total,2,',','.'),1,0,'R');	
			$tot += $total;
			//$totProfit += $profit;
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(252,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(27,5,number_format($tot,2,',','.'),1,0,'R');	
		//$pdf->Cell(25,5,number_format($totProfit,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
		//$pdf->SetFont('Arial','',9);
		//$pdf->Cell(200,5,'Data Penjualan dari : '.$penjualan,0,0,'L');
		//$pdf->Ln(5);
		//$pdf->Cell(200,5,'Disortir berdasarkan : '.$sortir,0,0,'L');
		
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'Bandung, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(450,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(450,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtInap'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
	public function hitTgl($dateStart,$dateEnd)
	{
        // dateStart and dateEnd in the form of 'YYYY-MM-DD'
        $dateStartArray = explode("-",$dateStart);
        $dateEndArray = explode("-",$dateEnd);
       
        $startYear = $dateStartArray[0];
        $startMonth = $dateStartArray[1];
        $startDay = $dateStartArray[2];
       
        $endYear = $dateEndArray[0];
        $endMonth = $dateEndArray[1];
        $endDay = $dateEndArray[2];
       
        //first convert to unix timestamp
        $init_date = mktime(12,0,0,$startMonth,$startDay,$startYear);
        $dest_date = mktime(12,0,0,$endMonth,$endDay,$endYear);

        $offset = $dest_date-$init_date;

        $days = floor($offset/60/60/24);
        return $days;
	}
}
?>