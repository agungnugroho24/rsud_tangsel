<?php
class cetakLapPenjualan extends SimakConf
{
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$cariNama=$this->Request['cariByNama'];
		$cariID=$this->Request['cariByID'];
		$cariGol=$this->Request['cariByGol'];
		$cariJenis=$this->Request['cariByJenis'];
		$cariKlas=$this->Request['cariByKlas'];
		$cariDerivat=$this->Request['cariByDerivat'];
		$cariPbf=$this->Request['cariByPbf'];
		$cariProd=$this->Request['cariByProd'];
		$cariSat=$this->Request['cariBySat'];
		$sumber=$this->Request['sumber'];			
		$tujuan=$this->Request['tujuan'];
		$klinik=$this->Request['klinik'];
		$dokter=$this->Request['dokter'];
		$kelompok=$this->Request['kelompok'];
		$kontrak=$this->Request['kontrak'];
		$tgl=$this->Request['tgl'];
		$tglAwal=$this->Request['tglAwal'];
		$tglAkhir=$this->Request['tglAkhir'];
		$bulan=$this->Request['bulan'];
		$tahun=$this->Request['tahun'];
		$tipe=$this->Request['tipe'];
		$rawat=$this->Request['rawat'];
		$modeBayar=$this->Request['modeBayar'];		
		$periode=$this->Request['periode'];
		$dummyTbl=$this->Request['tableTmp'];
		$modeNarkotika=$this->Request['modeNarkotika'];
		
		/*
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];	
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		*/
		$noKwitansi = substr($noTrans,6,6).'/'.'APT-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		//Update tabel tbt_pembayaran
		/*
		$byrRecord=BayarRecord::finder()->findByPk($noTrans);
		$byrRecord->status_pembayaran='2';//Sudah dibayar!
		$byrRecord->tgl_bayar=date('Y-m-d h:m:s');
		$byrRecord->no_kwitansi=$noKwitansi;
		$byrRecord->operator=$nipTmp;
		$byrRecord->Save();//Update!
		*/
		
		$pdf=new reportKwitansi('L','mm','a4');
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
		$pdf->Cell(0,10,'          Telp. (021) 7404752','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'RINCIAN PENJUALAN OBAT/ALKES','0',0,'C');
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Cell(120,5,'','0',0,'L');
		
		$pdf->Cell(20,5,'Tanggal Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,$this->convertDate(date('Y-m-d'),'3'),'0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(15,5,'Kelompok','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		
		if($rawat == '0')
		{			
			$pdf->Cell(80,5,'Rawat Jalan','0',0,'L');
		}
		elseif($rawat == '1')
		{
			$pdf->Cell(80,5,'Rawat Inap','0',0,'L');
		}
		elseif($rawat == '2')
		{
			$pdf->Cell(80,5,'Pasien Luar','0',0,'L');
		}
		
		$pdf->Cell(120,5,'','0',0,'L');
		$pdf->Cell(20,5,'Waktu Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,date('G:i').' WIB','0',0,'L');		
		$pdf->Ln(5);
		
		if($rawat == '1')
		{	
			$pdf->Cell(15,5,'Transaksi','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			
			if($modeBayar == '0')
			{		
				$pdf->Cell(80,5,'Kredit','0',0,'L');
			}
			elseif($modeBayar == '1')
			{		
				$pdf->Cell(80,5,'Tunai','0',0,'L');
			}
			
			$pdf->Cell(120,5,'','0',0,'L');
			$pdf->Cell(20,5,'Tujuan','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(50,5,DesFarmasiRecord::finder()->findByPk($tujuan)->nama,'0',0,'L');	
		}
		else
		{
			$pdf->Cell(15,5,'Tujuan','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,DesFarmasiRecord::finder()->findByPk($tujuan)->nama,'0',0,'L');
		}
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(45,5,'Obat',1,0,'C');
		$pdf->Cell(42,5,'Pasien',1,0,'C');
		$pdf->Cell(42,5,'Operator',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(30,5,'Harga Beli',1,0,'C');
		$pdf->Cell(30,5,'Harga Jual',1,0,'C');
		$pdf->Cell(30,5,'Total Jual',1,0,'C');
		$pdf->Cell(30,5,'Pendapatan',1,0,'C');
		
		$pdf->Ln(6);
		$sql = "SELECT a.kode AS kode,
					   a.nama AS nama,      
					   a.pbf AS pbf,   
					   a.satuan AS sat,
					   b.id AS id,
					   b.hrg AS jual,
					   b.tgl AS tgl_jual,
					   b.operator AS operator, ";
			
			if($rawat != '2')
			{
				$sql .=" c.nama AS nm_pasien,
						";
			}
			else
			{
				$sql .=" b.cm AS nm_pasien,
						";
			}
			
			/*
			if($sumber <> '')
			{
			$sql .= " b.jumlah AS jumlah,
			(SUM(b.total_real)-(b.jumlah * d.hrg_ppn)) AS untung,
			   b.sumber AS sumber "; 
			}else{
			$sql .= " b.jumlah AS jumlah,
			  (b.total_real - (b.jumlah * d.hrg_ppn)) AS untung, 
			   b.sumber AS sumber ";
			} 	
			*/
			
			if($sumber <> '')
			{
			$sql .= " b.jumlah AS jumlah,
			   b.sumber AS sumber "; 
			}else{
			$sql .= " b.jumlah AS jumlah,
			   b.sumber AS sumber ";
			} 	
			
			if($rawat == '0')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c
							WHERE	 							
								a.kode=b.id_obat 
								AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND b.jumlah > 0 ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c
							WHERE	 							
								a.kode=b.id_obat AND
								c.cm=b.cm  
								AND b.jumlah > 0 ";
					}
				}
			}
			elseif($rawat == '1')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c
							WHERE	 							
								a.kode=b.id_obat 
								AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND b.jumlah > 0";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c
							WHERE	 							
								a.kode=b.id_obat AND
								c.cm=b.cm  
								AND b.jumlah > 0 ";
					}
					
					if ($modeBayar == '0')
					{
						$sql .=	" AND b.st_bayar = 0 ";
					}
					elseif ($modeBayar == '1')
					{						   
						$sql .=	" AND b.st_bayar = 1 ";
					}
				}
			}	
			elseif($rawat == '2')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_lain b
							WHERE	 							
								a.kode=b.id_obat 
								AND b.tujuan='$tujuan'
								AND b.jumlah > 0 ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_lain b
							WHERE	 							
								a.kode=b.id_obat 
								AND b.jumlah > 0 ";
					}
				}
			}	
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.kode = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariJenis <> '')			
				$sql .= "AND a.kategori = '$cariJenis' ";
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";		
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";
			
			if($klinik <> '')			
				$sql .= "AND b.klinik = '$klinik' ";	
			
			if($dokter <> '')			
				$sql .= "AND b.dokter = '$dokter' ";	
			
			if($kelompok <> '')			
				$sql .= "AND c.kelompok = '$kelompok' ";
			
			if($kontrak <> '')			
				$sql .= "AND c.perusahaan = '$kontrak' ";	
			
			if($tgl <> '')			
				$sql .= "AND b.tgl = '$tgl' ";
				
			if($tglAwal <> '' AND $tglAkhir <> '')			
				$sql .= "AND b.tgl BETWEEN '$tglAwal' AND '$tglAkhir' ";							
			
			if($bulan <> '')			
				$sql .= "AND MONTH(b.tgl) = '$bulan' AND YEAR(b.tgl)='$tahun' ";	
			
			$sql .= " GROUP BY b.id ORDER BY a.nama ";
					
		
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
			$pdf->Cell(45,5,$row['nama'],1,0,'L');
			$pdf->Cell(42,5,$row['nm_pasien'],1,0,'C');				
			$pdf->Cell(42,5,UserRecord::finder()->find('nip=?',$row['operator'])->real_name,1,0,'C');				
			$pdf->Cell(20,5,$row['jumlah'],1,0,'R');	
			
			$id = $row['id'];
			$idObat = $row['kode'];
			
			$totalBeli = $hrgBeli*$row['jumlah'];
						
			if($rawat == '0')
			{
				$activeRec = ObatJualRecord::finder();
			}
			elseif($rawat == '1')
			{
				$activeRec = ObatJualInapRecord::finder();
			}
			elseif($rawat == '2')
			{
				$activeRec = ObatJualLainRecord::finder();
			}
			
			/*
			$sql = "SELECT * FROM tbm_obat_hrg_khusus WHERE id_obat = '$idObat'";
			$arr = $this->queryAction($sql,'S');
			
			//cek apakah harga obat khusus atau harga obat biasa
			if (count($arr) > 0) //jika ditemukan harga obat khusus
			{
				$idTbtObatHargaKhusus = $activeRec->findByPk($id)->id_harga;
				$hrgBeliObatKhusus = HrgObatKhususRecord::finder()->findByPk($idTbtObatHargaKhusus)->hrg_beli;
				$pdf->Cell(30,5,'Rp ' . number_format($hrgBeliObatKhusus,2,',','.'),1,0,'R');
				
				$pendapatan = ($row['jual'] - $hrgBeliObatKhusus) * $row['jumlah'];
			}
			else //jika ditemukan harga obat biasa
			{
			*/				
				$idTbtObatHarga = $activeRec->findByPk($id)->id_harga;
				
				//cek hrg_ppn_disc
				//jika tidak nol, pakai hrg_ppn_disc untuk menghtung pendapatan
				
				$hrgPpnDisc = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				if($hrgPpnDisc == 0)
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn;					
				}
				else
				{
					$hrgBeli = HrgObatRecord::finder()->findByPk($idTbtObatHarga)->hrg_ppn_disc;
				}
				
				$pdf->Cell(30,5,'Rp ' . number_format($hrgBeli,2,',','.'),1,0,'R');	
				$pendapatan = ($row['jual'] - $hrgBeli) * $row['jumlah'];
			//}	
					
			//$pdf->Cell(30,5,'Rp ' . number_format($row['beli'],2,',','.'),1,0,'R');	
			//$pdf->Cell(30,5,'Rp ' . number_format($hrgBeli,2,',','.'),1,0,'R');	
			$pdf->Cell(30,5,'Rp ' . number_format($row['jual'],2,',','.'),1,0,'R');
			$pdf->Cell(30,5,'Rp ' . number_format($totalJual,2,',','.'),1,0,'R');	
			//$pdf->Cell(30,5,'Rp ' . number_format($row['untung'],2,',','.'),1,0,'R');	
			$pdf->Cell(30,5,'Rp ' . number_format($pendapatan,2,',','.'),1,0,'R');	
			$pdf->Ln(5);			
			
			$grandTotJual +=$totalJual;
			$grandTot +=$pendapatan;
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(139,5,'Total ',0,0,'R');	
		//$pdf->Cell(30,5,'Rp ' .number_format($totBeli,2,',','.'),1,0,'R');	
		//$pdf->Cell(30,5,'Rp ' .number_format($totJual,2,',','.'),1,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format($grandTotJual,2,',','.'),1,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));		
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