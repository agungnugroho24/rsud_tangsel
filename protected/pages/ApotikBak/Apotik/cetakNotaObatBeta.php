<?php
class cetakNotaObatBeta extends SimakConf
{
	public function onLoad($param)
	{	
		
		$jnsPasien=$this->Request['jnsPasien'];
		$kelompokPasien=$this->Request['kelompokPasien'];
		$noTrans=$this->Request['notrans'];		
		$noReg=$this->Request['noReg'];		
		$dokter=$this->Request['dokter'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];
		$mode=$this->Request['mode'];
		$modeUrl=$this->Request['modeUrl'];
		$stCetakUlang=$this->Request['stCetakUlang'];
		
		$ruangan=$this->Request['ruangan'];	
		$petugas=$this->Request['petugas'];	
		
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		
		if($jnsPasien == '0' ) //pasien rwt jalan
		{
			$noTransRwtJln = $this->Request['noTransRwtJln'];	
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->st_asuransi;	
			$noTransKwt = $noTransRwtJln;
			
			if($kelompokPasien == '04' && $stAsuransi == '1' ) //karyawan & st_asuransi berlaku
			{
				$tblTbtObat = 'tbt_obat_jual_karyawan';
				$activeRec = ObatJualKaryawanRecord::finder();
			}
			else
			{
				$tblTbtObat = 'tbt_obat_jual';
				$activeRec = ObatJualRecord::finder();
			}
		}
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$noTransRwtInap = $this->Request['noTransRwtInap'];
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTransRwtInap)->st_asuransi;	
			$noTransKwt = $noTransRwtInap;
			
			if($kelompokPasien == '04' && $stAsuransi == '1' ) //karyawan & st_asuransi berlaku
			{
				$tblTbtObat = 'tbt_obat_jual_inap_karyawan';
				$activeRec = ObatJualInapKaryawanRecord::finder();
			}
			else
			{
				$tblTbtObat = 'tbt_obat_jual_inap';
				$activeRec = ObatJualInapRecord::finder();
			}
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$noTransPasLuar = $this->Request['noTransPasLuar'];
			$stKepegawaian = PasienLuarRecord::finder()->findByPk($noTransPasLuar)->st_kepegawaian;
			$noTransKwt = $noTransPasLuar;
			
			if($stKepegawaian == '0') //Pasien Luar berstatus Umum
			{
				$tblTbtObat = 'tbt_obat_jual_lain';
				$activeRec = ObatJualLainRecord::finder();
			}
			else //Pasien Luar berstatus Karyawan
			{
				$tblTbtObat = 'tbt_obat_jual_lain_karyawan';
				$activeRec = ObatJualLainKaryawanRecord::finder();
			}
			
		}		
		elseif($jnsPasien == '4' ) //unit internal
		{
			$noTransRwtJln = '';	
			//$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->st_asuransi;	
			$noTransKwt = $noReg;
			
			$tblTbtObat = 'tbt_obat_jual_internal';
			$activeRec = ObatJualInternalRecord::finder();
		}
		
		$noKwitansi = substr($noTransKwt,6,8).'/'.'APT-';
		$noKwitansi .= substr($noTransKwt,4,2).'/';
		$noKwitansi .= substr($noTransKwt,0,4);	
		
		/*
		$noKwitansi = substr($noReg,6,8).'/'.'APT-';
		$noKwitansi .= substr($noReg,4,2).'/';
		$noKwitansi .= substr($noReg,0,4);	
		*/
		
		$pdf=new reportKwitansi("P","mm","a4");
		$pdf->AliasNbPages(); 
		
		if($stCetakUlang == '01')
			$pdf->water = '1';
			
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
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',10);
		
		$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
		
		if($jnsPasien == '0')
		{
			if($idKlinik=='07'){			
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES IGD','0',0,'C');
			}else{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT JALAN','0',0,'C');
			}
		}
		elseif($jnsPasien == '1')
		{
			if($mode=='0')
			{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( KREDIT )','0',0,'C');
			}else{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES RAWAT INAP ( TUNAI )','0',0,'C');
			}
		}
		elseif($jnsPasien == '2')
		{
			if($mode=='0')
			{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES PASIEN LUAR ( KREDIT )','0',0,'C');
			}else{
				$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES PASIEN LUAR','0',0,'C');
			}
		}		
		elseif($jnsPasien == '4')
		{
			$pdf->Cell(0,5,'RINCIAN PEMBELIAN OBAT/ALKES UNIT INTERNAl','0',0,'C');
		}		
		
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. Resep',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(50,10,$noReg,0,0,'L');
		/*
		if($jnsPasien == '0' ) //pasien rwt jalan
		{
			$pdf->Cell(50,10,$noTransRwtJln,0,0,'L');
		}
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$pdf->Cell(50,10,$noTransRwtInap,0,0,'L');
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$pdf->Cell(50,10,$noTransPasLuar,0,0,'L');
		}	
		*/
		
		$pdf->Cell(15,10,'',0,0,'L');
		if($jnsPasien == '4')
			$pdf->Cell(15,10,'Ruangan',0,0,'L');
		else
			$pdf->Cell(15,10,'Poliklinik',0,0,'L');
			
		$pdf->Cell(2,10,':  ',0,0,'L');
			
		if($jnsPasien == '0')
		{
			$noTransRwtjln = $activeRec->find('no_trans=?',$noTrans)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
			$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
			$pdf->Cell(30,10,$nmKlinik,0,0,'L');
		}
		elseif($jnsPasien == '4')
		{
			$pdf->Cell(30,10,$ruangan,0,0,'L');
		}
		else
		{
			$pdf->Cell(30,10,'-',0,0,'L');
		}		
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		if($jnsPasien != '2' && $jnsPasien != '4')
		{
			$pdf->Cell(50,10,$cm,0,0,'L');
		}		
		else
		{
			$pdf->Cell(50,10,'-',0,0,'L');
		}
		
		
		
		$pdf->Cell(15,10,'',0,0,'L');
		if($jnsPasien == '4')
			$pdf->Cell(15,10,'Petugas',0,0,'L');
		else
			$pdf->Cell(15,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($jnsPasien == '4')
		{
			$pdf->Cell(30,10,$petugas,0,0,'L');
		}	
		if($jnsPasien != '2')
		{
			$pdf->Cell(30,10,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'L');
		}		
		else
		{
			$pdf->Cell(30,10,$dokter,0,0,'L');
		}
		
		$pdf->Ln(5);
		
		if($jnsPasien != '4')//bukan unit internal
		{
			$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			
			if($jnsPasien != '2' && $jnsPasien != '4')
			{
				$pdf->Cell(50,10,PasienRecord::finder()->findByPk($cm)->nama,0,0,'L');
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(50,10,$nmPasien,0,0,'L');
				$pdf->Ln(5);
			}
		}
				
					
		//Line break
		$pdf->Ln(3);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(90,5,'Obat',1,0,'C');
		//$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(30,5,'Jumlah',1,0,'C');
		$pdf->Cell(45,5,'Harga',1,0,'C');		
		//Line break
		$pdf->Ln(6);	
		
		
		//------------------------------------ non racikan ------------------------------------// 	
		$sql = "SELECT 
					id_obat, 
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat ";
					
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$sql .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 0 ";
		}		
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$sql .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 0 ";
		}		
		elseif($jnsPasien == '2') //pasien luar
		{
			$sql .= " WHERE no_trans_pas_luar = '$noTransPasLuar'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 0 ";
		}
		
		if($jnsPasien == '4') //Unit Internal
		{
			$sql .= " WHERE no_reg = '$noReg' AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 0 GROUP BY id_obat";
		}
		else
		{		
			$sql .= " AND no_reg = '$noReg' AND st_paket = 0 GROUP BY id_obat ";						
		}
			
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',10);						
			$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(165,5,'Obat Non Racikan','RT',0,'L');
			$pdf->Ln(5);
		
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',9 );						
				$pdf->Cell(10,5,$j.'.',1,0,'C');
				$pdf->Cell(90,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
				//$pdf->Cell(30,5, number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				$pdf->Cell(30,5,$row['jumlah'],1,0,'C');	
				$pdf->Cell(45,5, number_format($row['total'],2,',','.'),1,0,'R');	
				$pdf->Ln(5);
				$grandTotNonRacikan +=$row['total'];
			}
			
			$pdf->Ln(1);			
		}
		
		
		
		//------------------------------------ racikan ------------------------------------// 	
		$sql = "SELECT 
					id_obat, 
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat ";
					
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$sql .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND st_racik = '1' AND st_paket = '0' ";
		}		
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$sql .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND st_racik = '1' AND st_paket = '0' ";
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$sql .= " WHERE no_trans_pas_luar = '$noTransPasLuar'  AND st_racik = '1' AND st_paket = '0' ";
		}
		
		if($jnsPasien == '4') //Unit Internal
		{
			$sql .= " WHERE no_reg = '$noReg' AND st_racik = '1' AND st_paket = '0' GROUP BY id_kel_racik,id_obat ";
		}
		else
		{		
			$sql .= " AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_kel_racik,id_obat ";					
		}
										
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',10);						
			$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(165,5,'Obat Racikan','RT',0,'L');
			$pdf->Ln(5);
			
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',9 );				
				$pdf->Cell(10,5,$j.'.',1,0,'C');
				$pdf->Cell(90,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
				//$pdf->Cell(30,5, number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				$pdf->Cell(30,5,$row['jumlah'],1,0,'C');	
				$pdf->Cell(45,5, number_format($row['total'],2,',','.'),1,0,'R');	
				$pdf->Ln(5);
				$grandTotRacikan +=$row['total'];
			}			
			
			$pdf->Ln(1);
		}
		
		
		//------------------------------------ paket ------------------------------------// 	
		$sql = "SELECT 
					id_obat, id_kel_paket,
					hrg,jml_paket AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat ";
					
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$sql .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 1 ";
		}		
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$sql .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 1 ";
		}		
		elseif($jnsPasien == '2') //pasien luar
		{
			$sql .= " WHERE no_trans_pas_luar = '$noTransPasLuar'  AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 1 ";
		}
		
		if($jnsPasien == '4') //Unit Internal
		{
			$sql .= " WHERE no_reg = '$noReg' AND st_racik = 0 AND st_imunisasi = 0 AND st_paket = 1 GROUP BY id_obat";
		}
		else
		{		
			$sql .= " AND no_reg = '$noReg' AND st_paket = 1 GROUP BY id_kel_paket ";
		}
			
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',10);						
			$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(165,5,'Obat Paket','RT',0,'L');
			$pdf->Ln(5);
		
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',9 );						
				$pdf->Cell(10,5,$j.'.',1,0,'C');
				$pdf->Cell(90,5,ObatPaketKelompokRecord::finder()->findByPk($row['id_kel_paket'])->nama,1,0,'L');
				//$pdf->Cell(30,5, number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				$pdf->Cell(30,5,$row['jumlah'],1,0,'C');	
				$pdf->Cell(45,5, number_format($row['total'],2,',','.'),1,0,'R');	
				$pdf->Ln(5);
				$grandTotNonRacikan +=$row['total'];
			}
			
			$pdf->Ln(1);			
		}
		
		
		//$pdf->Ln(15);
		
		//------------------------------------ imunisasi ------------------------------------// 	
		$sql = "SELECT 
					id_obat, id_kel_imunisasi,
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat ";
					
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$sql .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND st_imunisasi = '1' AND st_paket = '0' ";
		}		
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$sql .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND st_imunisasi = '1' AND st_paket = '0' ";
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$sql .= " WHERE no_trans_pas_luar = '$noTransPasLuar'  AND st_imunisasi = '1' AND st_paket = '0' ";
		}
		
		if($jnsPasien == '4') //Unit Internal
		{
			$sql .= " WHERE no_reg = '$noReg' AND st_imunisasi = '1' AND st_paket = '0' GROUP BY id_kel_imunisasi
				ORDER BY id_kel_imunisasi ";
		}
		else
		{		
			$sql .= " AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_kel_imunisasi
				ORDER BY id_kel_imunisasi ";					
		}
		
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok imunisasi
		{
			$pdf->SetFont('Arial','BI',10);						
			$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(165,5,'Paket Imunisasi','RT',0,'L');
			$pdf->Ln(5);
			
			foreach($arrData as $row)
			{
				$id_kel_imunisasi = $row['id_kel_imunisasi'];
				
				$pdf->SetFont('Arial','BI',9);						
				$pdf->Cell(10,5,'',"LTB",0,'L');
				$pdf->Cell(120,5,'Imunisasi '.ImunisasiRecord::finder()->findByPk($id_kel_imunisasi)->nama,'RBT',0,'L');
				$pdf->Cell(45,5, number_format($row['total'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
				$grandTotImunisasi +=$row['total'];
				
				$sql2 = "SELECT 
					id_obat, 
					hrg,SUM(jumlah) AS jumlah,SUM(total) AS total
				FROM
				 $tblTbtObat ";
					
				if($jnsPasien == '0') //pasien rwt jalan
				{
					$sql2 .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND st_imunisasi = '1' AND id_kel_imunisasi = '$id_kel_imunisasi'  AND st_paket = '0'";
				}		
				elseif($jnsPasien == '1') //pasien rwt inap
				{
					$sql2 .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND st_imunisasi = '1' AND id_kel_imunisasi = '$id_kel_imunisasi'  AND st_paket = '0'";
				}
				elseif($jnsPasien == '2') //pasien luar
				{
					$sql2 .= " WHERE no_trans_pas_luar = '$noTransPasLuar' AND cm='$nmPasien'  AND st_imunisasi = '1' AND id_kel_imunisasi = '$id_kel_imunisasi'  AND st_paket = '0'";
				}
				
				if($jnsPasien == '4') //Unit Internal
				{
					$sql2 .= " WHERE no_reg = '$noReg' AND st_imunisasi = '1' AND id_kel_imunisasi = '$id_kel_imunisasi'  AND st_paket = '0' GROUP BY id_obat 
							ORDER BY id ";
				}
				else
				{		
					$sql2 .= " AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_obat 
							ORDER BY id ";					
				}
				
											
				//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
				$arrData2=$this->queryAction($sql2,'S');//Select row in tabel bro...		
			
				$j=0;
				$n=0;
				foreach($arrData2 as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',8);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(90,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,1,0,'L');
					//$pdf->Cell(30,5, number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
					$pdf->Cell(30,5,$row['jumlah'],1,0,'C');	
					//$pdf->Cell(45,5, number_format($row['total'],2,',','.'),1,0,'R');	
					$pdf->Cell(45,5,'-',1,0,'R');	
					$pdf->Ln(5);
					$grandTotImunisasi +=$row['total'];
				}
			}			
		}
		
		
		
		//------------------------------------ BHP ------------------------------------// 	
		$sql = "SELECT 
					id_bhp, 
					bhp
				FROM
				 $tblTbtObat ";
					
		if($jnsPasien == '0') //pasien rwt jalan
		{
			$sql .= " WHERE no_trans_rwtjln = '$noTransRwtJln' AND cm='$cm'  AND bhp <> '' ";
		}		
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$sql .= " WHERE no_trans_inap = '$noTransRwtInap' AND cm='$cm'  AND bhp <> '' ";
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$sql .= " WHERE no_trans_pas_luar = '$noTransPasLuar' AND cm='$nmPasien'  AND bhp <> '' ";
		}
		
		if($jnsPasien == '4') //Unit Internal
		{
			$sql .= " WHERE no_reg = '$noReg' AND bhp <> '' ORDER BY id ";	
		}
		else
		{		
			$sql .= " AND no_reg = '$noReg' ORDER BY id ";					
		}
		
		
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada bhp
		{
			$pdf->SetFont('Arial','BI',8);						
			$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(165,5,'BHP','RT',0,'L');
			$pdf->Ln(5);
			
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',10);						
				$pdf->Cell(10,5,$j.'.',1,110,'C');
				$pdf->Cell(120,5,ObatBhpRecord::finder()->findByPk($row['id_bhp'])->nama,1,0,'L');
				//$pdf->Cell(30,5, number_format($this->bulatkan($row['hrg']),2,',','.'),1,0,'R');	
				//$pdf->Cell(30,5,$row['bhp'],1,0,'C');	
				$pdf->Cell(45,5, number_format($row['bhp'],2,',','.'),1,0,'R');	
				$pdf->Ln(5);
				$grandTotBhp +=$row['bhp'];
			}			
			
			$pdf->Ln(1);
		}
		
		
		
		$grandTot = $grandTotRacikan + $grandTotNonRacikan + $grandTotImunisasi + $grandTotBhp;
		//$grandTot = $grandTotImunisasi  ;
			
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->SetFont('Arial','B',9);						
		$pdf->Cell(50,5,'Total ',0,0,'R');		
		$pdf->Cell(45,5,number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(3);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'Petugas Apotik,',0,0,'C');	
		$pdf->Ln(13);	
		$pdf->SetFont('Arial','BU',9);	
		
		if($modeUrl == '01')
			$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObatTunda'));		
		elseif($stCetakUlang == '01')
			$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));		
		else
			$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat'));		
				
		$pdf->Ln(4);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>
