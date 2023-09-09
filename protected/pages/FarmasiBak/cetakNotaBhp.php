<?php
class cetakNotaBhp extends SimakConf
{
	public function onLoad($param)
	{	
		$noReg=$this->Request['noReg'];
		
		$session=new THttpSession;
		$session->open();
		
		if($session['noRegArr'] != '')
		{
			$i=1;
			foreach( $session['noRegArr'] as $data=>$arrNoReg)
			{
				if($i == 1)
				{
					$pdf=new reportKwitansi("P","mm","kwt_gsm");
					$this->prosesCetak($arrNoReg,'1','0',$pdf);
				}
				else
					$this->prosesCetak($arrNoReg,'1','1',$pdf);
				$i++;
			}
			$pdf->Output();
		}
		else
		{
			$pdf=new reportKwitansi("P","mm","kwt_gsm");
			$this->prosesCetak($noReg,'0','0',$pdf);
			$pdf->Output();
		}
	}
	
	public function prosesCetak($noReg,$stMultiFfs,$resepBaru,$pdf)
	{			
		$jnsPasien=$this->Request['jnsPasien'];
		$kelompokPasien=$this->Request['kelompokPasien'];
		$noTrans=$this->Request['notrans'];		
		//$noReg=$this->Request['noReg'];		
		$dokter=$this->Request['dokter'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$cm=$this->Request['cm'];
		$mode=$this->Request['mode'];
		$modeUrl=$this->Request['modeUrl'];
		$stCetakUlang=$this->Request['stCetakUlang'];
		
		if($this->Request['stAdmRawatJalan']=='1')
		{
				$nmTableTdk=$this->Request['nmTableTdk'];
				$nmTableIcd=$this->Request['nmTableIcd'];
				$nmTableDiagnosa=$this->Request['nmTableDiagnosa'];
				$poli=$this->Request['poli'];
		}
		
		$ruangan=$this->Request['ruangan'];	
		$petugas=$this->Request['petugas'];	
		
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$tblTbtObat = 'tbt_obat_jual_poli';
		$activeRec = ObatJualPoliRecord::finder();
		$depo = $activeRec->find('no_reg=?',$noReg)->tujuan;		
		$nmDepo = strtoupper(DesFarmasiRecord::finder()->findByPk($depo)->nama);
		$ket = $activeRec->find('no_reg=?',$noReg)->ket;
		
		if($jnsPasien == '0' ) //pasien rwt jalan
		{
			$noTransRwtJln = $this->Request['noTransRwtJln'];	
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->st_asuransi;	
			$idPenjamin = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->penjamin;
			
			$nipJpk = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->nip_jkp;
			$nmJpk = JpkPegawaiRecord::finder()->findByPk($nipJpk)->nama;
			
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
			
			if($idPenjamin=='08' && $stAsuransi=='1') //pasien JPK
			{
				$idDiagnosa = DiagnosaRecord::finder()->find('no_trans=?',$noTransRwtJln)->id_diagnosa;
				$kelDiagnosa = NamaDiagnosaRecord::finder()->findByPk($idDiagnosa)->kel_tindakan;
				
				$sql = "SELECT st_ffs FROM $tblTbtObat WHERE no_reg = '$noReg' GROUP BY st_ffs";
				$st_ffs = $activeRec->findBySql($sql)->st_ffs;
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
			$id_pas_luar_karyawan = PasienLuarRecord::finder()->findByPk($noTransPasLuar)->id_pas_luar_karyawan;
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
		/*
		if($resepBaru == '0')
			$pdf=new reportKwitansiGsm("P","mm","kwt_gsm");
		elseif($resepBaru == '1')
			$pdf->AddPage();
		*/	
		$pdf->AliasNbPages(); 
		
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(5);
		$pdf->SetRightMargin(5);
		
		if($stCetakUlang == '01')
			$pdf->water = '1';
		
		$pdf->stResepBaru = $st_ffs;
		
		if($nipJpk){
			if($kelDiagnosa != '0' && $st_ffs == '1'){
				$pdf->diagnosaLabel = '1';
			}
		}	
			
		$pdf->AddPage();
		
		$pdf->Image('protected/pages/Tarif/logo1.jpg',5,5,30);	
		$pdf->SetFont('Arial','B',8);
		
		$pdf->Cell(20,5,'','0',0,'C');
		$pdf->Cell(0,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L');
		$pdf->Ln(4);		
		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(20,5,'','0',0,'C');
		$pdf->Cell(0,5,'                 JL. Pajajaran No. 101 Pamulang','0',0,'L');	
		$pdf->Ln(4);
		
		$pdf->Cell(20,5,'','0',0,'C');
		$pdf->Cell(0,4,'                     Telp. (021) 74718440','0',0,'L');	
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(0,5,'........................................................................................................................................................................','',0,'C');
		//$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(4);	
		
		$pdf->SetFont('Arial','B',8);
		$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
		
		$pdf->Cell(0,5,'PEMAKAIAN BHP '.$nmDepo,'0',0,'C');
		
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',8);
		/*$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(4);		
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(20,5,'No. Resep',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(25,5,$noReg,0,0,'L');*/
		
		/*
		if($jnsPasien == '0' ) //pasien rwt jalan
		{
			$pdf->Cell(25,5,$noTransRwtJln,0,0,'L');
		}
		elseif($jnsPasien == '1') //pasien rwt inap
		{
			$pdf->Cell(25,5,$noTransRwtInap,0,0,'L');
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			$pdf->Cell(25,5,$noTransPasLuar,0,0,'L');
		}	
		*/
		
		/*
		$pdf->Cell(15,5,'',0,0,'L');
		if($jnsPasien == '4')
			$pdf->Cell(15,5,'Ruangan',0,0,'L');
		else
			$pdf->Cell(15,5,'Poliklinik',0,0,'L');
			
		$pdf->Cell(2,5,':  ',0,0,'L');
			
		if($jnsPasien == '0')
		{
			$noTransRwtjln = $activeRec->find('no_trans=?',$noTrans)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->find('no_trans=?',$noTransRwtjln)->id_klinik;
			$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
			$pdf->Cell(15,5,$nmKlinik,0,0,'L');
		}
		elseif($jnsPasien == '4')
		{
			$pdf->Cell(15,5,$ruangan,0,0,'L');
		}
		else
		{
			$pdf->Cell(15,5,'-',0,0,'L');
		}		
		
		$pdf->Ln(4);
		$nip = PasienRecord::finder()->findByPk($cm)->nip_jkp;
		if($nip){
			$pdf->Cell(20,5,'No. RM / NIP',0,0,'L');
		}else{
			if($id_pas_luar_karyawan)
				$pdf->Cell(20,5,'NIP',0,0,'L');
			else
				$pdf->Cell(20,5,'No. RM',0,0,'L');
		}	
		$pdf->Cell(2,5,':  ',0,0,'L');
		if($jnsPasien != '2' && $jnsPasien != '4')
		{
			if($nip){
				$pdf->Cell(25,5,$cm. ' / ' .$nip,0,0,'L');
			}else{
				$pdf->Cell(25,5,$cm,0,0,'L');
			}
		}		
		else
		{
			if($id_pas_luar_karyawan)
				$pdf->Cell(25,5,$id_pas_luar_karyawan,0,0,'L');
			else
				$pdf->Cell(25,5,'-',0,0,'L');
		}
		
		$pdf->Cell(15,5,'',0,0,'L');
		if($jnsPasien == '4')
			$pdf->Cell(15,5,'Petugas',0,0,'L');
		else
			$pdf->Cell(15,5,'Dokter',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		
		if($jnsPasien == '4')
		{
			$pdf->Cell(20,5,$petugas,0,0,'L');
		}	
		if($jnsPasien != '2')
		{
			$pdf->Cell(20,5,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'L');
		}		
		else
		{
			$pdf->Cell(20,5,$dokter,0,0,'L');
		}
		
		$pdf->Ln(4);
		
		if($jnsPasien != '4')//bukan unit internal
		{
			$pdf->Cell(20,5,'Nama Pasien',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			
			if($jnsPasien != '2' && $jnsPasien != '4')
			{
				$nmPasien = PasienRecord::finder()->findByPk($cm)->nama;
				$pdf->Cell(25,5,$nmPasien,0,0,'L');
				$pdf->Ln(4);
			}
			else
			{
				$pdf->Cell(25,5,$nmPasien,0,0,'L');
				$pdf->Ln(4);
			}
		}
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(0,2,'........................................................................................................................................................................','',0,'C');
		//$pdf->Cell(0,5,'','B',1,'C');
		*/
		
		//Line break
		$pdf->Ln(3);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(10,5,'No.',0,0,'C');
		$pdf->Cell(90,5,'Nama BHP',0,0,'L');
		//$pdf->Cell(15,5,'Harga',0,0,'C');
		//$pdf->Cell(15,5,'Jumlah',0,0,'C');
		//$pdf->Cell(25,5,'Harga',0,0,'R');
		$pdf->Cell(15,5,'Jumlah',0,0,'R');
		//Line break
		$pdf->Ln(5);	
		
		
		//------------------------------------ non racikan ------------------------------------// 	
		if($kelompokPasien == '08' && $stAsuransi == '1' ) //JPK
		{
			if($kelDiagnosa != '0' && $st_ffs == '1')
			{
				$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";	
			}
			else
			{
				$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah
				FROM
				 $tblTbtObat ";
			}
		}
		else
		{
			$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";
		}
		
		$sql .= " WHERE st_racik = 0 AND st_imunisasi = 0 AND st_paket = 0 AND no_reg = '$noReg' AND st_paket = 0 GROUP BY id_obat ORDER BY id_kel_racik, id_kel_imunisasi, id ";
			
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',8.5);						
			////$pdf->Cell(10,5,'',"LTB",0,'L');
			//$pdf->Cell(115,5,'Obat Non Racikan','',0,'L');
			//$pdf->Ln(4);
		
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',8.5 );						
				$pdf->Cell(10,4,$j.'.',0,0,'C');
				$pdf->Cell(90,4,ObatRecord::finder()->findByPk($row['id_obat'])->nama,0,0,'L');
				//$pdf->Cell(15,4, number_format($this->bulatkan($row['hrg']),2,',','.'),0,0,'R');	
				$pdf->Cell(15,4,$row['jumlah'],0,0,'R');	
				
				/*if($kelompokPasien == '08' && $stAsuransi == '1' && $row['st_costsharing'] == '0' && $kelDiagnosa == '0') //JPK tanpa costsharing
					$pdf->Cell(25,4, '-',0,0,'R');
				elseif($kelompokPasien == '08' && $stAsuransi == '1' && $row['st_costsharing'] == '0' && $kelDiagnosa != '0') //JPK costsharing
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
				else
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');		
				*/	
				$pdf->Ln(4);
				$grandTotNonRacikan +=$row['total'];
			}
			
			$pdf->Ln(1);			
		}
		
		//------------------------------------ racikan ------------------------------------// 
		if($kelompokPasien == '08' && $stAsuransi == '1' ) //JPK
		{
			if($kelDiagnosa != '0')
			{
				$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";	
			}
			else
			{
				$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah
				FROM
				 $tblTbtObat ";
			}
		}
		else
		{
			$sql = "SELECT 
					id_obat, 
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";
		}
					
		$sql .= " WHERE st_racik = '1' AND st_paket = '0' AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_kel_racik,id_obat ORDER BY id_kel_racik, id_kel_imunisasi, id ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',8.5);						
			//$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(105,5,'Obat Racikan','',0,'L');
			$pdf->Ln(4);
			
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',8);				
				$pdf->Cell(10,4,$j.'.',0,0,'C');
				$pdf->Cell(65,4,ObatRecord::finder()->findByPk($row['id_obat'])->nama,0,0,'L');
				//$pdf->Cell(15,4, number_format($this->bulatkan($row['hrg']),2,',','.'),0,0,'R');	
				$pdf->Cell(15,4,$row['jumlah'],0,0,'C');
				
				if($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa == '0') //JPK tanpa costsharing
					$pdf->Cell(25,4, '-',0,0,'R');
				elseif($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa != '0') //JPK costsharing
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
				else
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
					
				$pdf->Ln(4);
				$grandTotRacikan +=$row['total'];
			}			
			
			$pdf->Ln(1);
		}
		
		
		//------------------------------------ paket ------------------------------------// 
		if($kelompokPasien == '08' && $stAsuransi == '1' ) //JPK
		{
			if($kelDiagnosa != '0')
			{
				$sql = "SELECT 
					id_obat, 
					id_kel_paket,
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";	
			}
			else
			{
				$sql = "SELECT 
					id_obat, 
					id_kel_paket,
					hrg,
					SUM(jumlah) AS jumlah
				FROM
				 $tblTbtObat ";
			}
		}
		else
		{
			$sql = "SELECT 
					id_obat, 
					id_kel_paket,
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";
		}
					
		$sql .= " WHERE st_racik = 0 AND st_imunisasi = 0 AND st_paket = 1 AND no_reg = '$noReg' AND st_paket = 1 GROUP BY id_kel_paket ORDER BY id_kel_racik, id_kel_imunisasi, id ";
			
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok non racikan
		{
			$pdf->SetFont('Arial','BI',8.5);						
			//$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(105,5,'Obat Paket','',0,'L');
			$pdf->Ln(4);
		
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',8);						
				$pdf->Cell(10,4,$j.'.',0,0,'C');
				$pdf->Cell(65,4,ObatPaketKelompokRecord::finder()->findByPk($row['id_kel_paket'])->nama,0,0,'L');
				//$pdf->Cell(15,4, number_format($this->bulatkan($row['hrg']),2,',','.'),0,0,'R');	
				$pdf->Cell(15,4,$row['jumlah'],0,0,'C');	
				
				if($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa == '0') //JPK tanpa costsharing
					$pdf->Cell(25,4, '-',0,0,'R');
				elseif($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa != '0') //JPK costsharing
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
				else
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
					
				$pdf->Ln(4);
				$grandTotNonRacikan +=$row['total'];
			}
			
			$pdf->Ln(1);			
		}
		
		
		//$pdf->Ln(15);
		
		//------------------------------------ imunisasi ------------------------------------// 
		if($kelompokPasien == '08' && $stAsuransi == '1' ) //JPK
		{
			if($kelDiagnosa != '0')
			{
				$sql = "SELECT 
					id_obat, 
					id_kel_imunisasi,
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";	
			}
			else
			{
				$sql = "SELECT 
					id_obat, 
					id_kel_imunisasi,
					hrg,
					SUM(jumlah) AS jumlah
				FROM
				 $tblTbtObat ";
			}
		}
		else
		{
			$sql = "SELECT 
					id_obat, 
					id_kel_imunisasi,
					hrg,
					SUM(jumlah) AS jumlah,
					SUM(total) AS total
				FROM
				 $tblTbtObat ";
		}
					
		$sql .= " WHERE st_imunisasi = '1' AND st_paket = '0' AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_kel_imunisasi
				ORDER BY id_kel_imunisasi ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada kelompok imunisasi
		{
			$pdf->SetFont('Arial','BI',8.5);						
			//$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(115,5,'Paket Imunisasi','',0,'L');
			$pdf->Ln(4);
			$j=0;	
			foreach($arrData as $row)
			{
				$id_kel_imunisasi = $row['id_kel_imunisasi'];
				
				$j += 1;	
				$pdf->SetFont('Arial','',8);						
				$pdf->Cell(10,5,$j.'.',0,0,'C');
				//$pdf->Cell(10,5,'',"LTB",0,'L');
				$pdf->Cell(80,5,'Imunisasi '.ImunisasiRecord::finder()->findByPk($id_kel_imunisasi)->nama,'0',0,'L');
				
				if($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa == '0') //JPK tanpa costsharing
					$pdf->Cell(25,4, '-',0,0,'R');
				elseif($kelompokPasien == '08' && $stAsuransi == '1' && $kelDiagnosa != '0') //JPK costsharing
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
				else
					$pdf->Cell(25,4, number_format($row['total'],2,',','.'),0,0,'R');
					
				$pdf->Ln(4);
				$grandTotImunisasi +=$row['total'];
				
				if($kelompokPasien == '08' && $stAsuransi == '1' ) //JPK
				{
					if($kelDiagnosa != '0')
					{
						$sql2 = "SELECT 
							id_obat, 
							id_kel_imunisasi,
							hrg,
							SUM(jumlah) AS jumlah,
							SUM(total) AS total,
						FROM
						 $tblTbtObat ";	
					}
					else
					{
						$sql2 = "SELECT 
							id_obat, 
							hrg,
							SUM(jumlah) AS jumlah
						FROM
						 $tblTbtObat ";
					}
				}
				else
				{
					$sql2 = "SELECT 
							id_obat, 
							hrg,
							SUM(jumlah) AS jumlah,
							SUM(total) AS total
						FROM
						 $tblTbtObat ";
				}
					
				$sql2 .= " WHERE t_imunisasi = '1' AND id_kel_imunisasi = '$id_kel_imunisasi'  AND st_paket = '0' AND no_reg = '$noReg' AND st_paket = '0' GROUP BY id_obat 
							ORDER BY id";
				
											
				//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
				$arrData2=$this->queryAction($sql2,'S');//Select row in tabel bro...		
			
				//$j=0;
				$n=0;
				foreach($arrData2 as $row)
				{
					$n += 1;
					$pdf->SetFont('Arial','',7);						
					//$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(10,5,'',0,0,'C');
					$pdf->Cell(65,5,ObatRecord::finder()->findByPk($row['id_obat'])->nama,0,0,'L');
					//$pdf->Cell(15,5, number_format($this->bulatkan($row['hrg']),2,',','.'),0,0,'R');	
					$pdf->Cell(15,5,$row['jumlah'],0,0,'C');	
					//$pdf->Cell(25,5, number_format($row['total'],2,',','.'),0,0,'R');	
					$pdf->Cell(25,5,'-',0,0,'R');	
					$pdf->Ln(4);
					//$grandTotImunisasi +=$row['total'];
				}
			}			
		}
		
		
		//------------------------------------ BHP ------------------------------------// 	
		$sql = "SELECT 
					id_bhp, 
					bhp
				FROM
				 $tblTbtObat ";
		
		$sql .= " WHERE bhp <> '' AND no_reg = '$noReg' ORDER BY id ";
								
		//$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		
		if(count($arrData)>0) //jika ada bhp
		{
			$pdf->SetFont('Arial','BI',7);						
			//$pdf->Cell(10,5,'',"LTB",0,'L');
			$pdf->Cell(105,5,'BHP','',0,'L');
			$pdf->Ln(4);
			
			$j=0;
			$n=0;
			foreach($arrData as $row)
			{
				$j += 1;
				$pdf->SetFont('Arial','',8.5);						
				$pdf->Cell(10,5,$j.'.',1,110,'C');
				$pdf->Cell(120,5,ObatBhpRecord::finder()->findByPk($row['id_bhp'])->nama,0,0,'L');
				//$pdf->Cell(15,5, number_format($this->bulatkan($row['hrg']),2,',','.'),0,0,'R');	
				//$pdf->Cell(15,5,$row['bhp'],0,0,'C');	
				$pdf->Cell(25,5, number_format($row['bhp'],2,',','.'),0,0,'R');	
				$pdf->Ln(4);
				$grandTotBhp +=$row['bhp'];
			}			
			
			$pdf->Ln(1);
		}
		
		
		
		$grandTot = $grandTotRacikan + $grandTotNonRacikan + $grandTotImunisasi + $grandTotBhp;
		//$grandTot = $grandTotImunisasi  ;
		$grandTotBulat = $this->bulatkan($grandTot);
		
		$pdf->SetFont('Arial','B',7);
		$pdf->Cell(0,2,'........................................................................................................................................................................','',0,'C');
		$pdf->Ln(3);
		
		$pdf->SetFont('Arial','B',8);					
		/*$pdf->Cell(90,4,'Pembulatan',0,0,'R');		
		$pdf->Cell(25,4,number_format($grandTotBulat-$grandTot,2,',','.'),0,0,'R');
		$pdf->Ln(4);*/
		//$pdf->Cell(90,4,'Total ',0,0,'R');		
		//$pdf->Cell(25,4,number_format( $grandTotBulat,2,',','.'),0,0,'R');
		//$pdf->Cell(25,4,number_format( $grandTot,2,',','.'),0,0,'R');
		//$pdf->Ln(4);
		$pdf->SetFont('Arial','',8);		
		if($ket)
			$pdf->MultiCell(110,4,'Keterangan : '.$ket,0,'L');
			
		$pdf->Cell(70,4,'',0,0,'C');
		$pdf->Cell(40,4,'TANGERANG SELATAN, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(4);
		
		$pdf->Cell(70,4,'',0,0,'C');
		$pdf->Cell(40,4,'Petugas,',0,0,'C');
		$pdf->Ln(7);	
		
		$pdf->SetFont('Arial','B',8);	
		$pdf->Cell(70,4,'',0,0,'C');
		$pdf->Cell(40,4,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Farmasi.PengeluaranBhp'));
		
		return $pdf;
	}
	
}
?>
