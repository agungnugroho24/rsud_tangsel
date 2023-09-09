<?php
class cetakKwtRwtJlnNoKop extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		 
	 
	public function headerKwt($pdf,$txtJudul,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter) 
	{ 
		$pdf->SetTopMargin(55);
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',10);
		
		
		$pdf->Cell(0,5,$txtJudul,'0',0,'C');
		
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(27,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$noTrans,0,0,'L');
		
		$pdf->Cell(27,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$sayTerbilang,0,0,'L');
		
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			$pdf->Cell(15,10,'No. CM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$cm,0,0,'L');
		}
				
		$pdf->Ln(5);		
		$pdf->Cell(27,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		//$pdf->Cell(105,10,$nmPasien .' / '. $pjPasien,0,0,'L');
		$pdf->Cell(70,10,$nmPasien ,0,0,'L');
		
		if($jnsPasien == '0') //jika pasien rawat jln
		{
			$pdf->Cell(15,10,'Poliklinik',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$nmKlinik,0,0,'L');
		}
		/*
		//$pdf->Ln(5);	
				
		$pdf->Cell(27,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$sayTerbilang,0,0,'L');
		
		/*
		$pdf->Cell(15,10,'Petugas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,ucwords(UserRecord::finder()->find('nip = ?',$petugasPoli)->real_name),0,0,'L');					
		*/
		//$pdf->Ln(5);
		
		$pdf->Cell(27,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		
		if($jnsPasien != '1') //jika bukan pasien luar/rujukan
		{
			$pdf->Cell(105,10,$nmDokter,0,0,'L');
		}
		elseif($jnsPasien == '1')
		{
			if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
			{	
				$pdf->Cell(105,10,LabJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
			}
			elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
			{
				$pdf->Cell(105,10,RadJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
			}
			
		}
	}
	
	public function onLoad($param)
	{	
		//$tipe=$this->Request['tipe'];	
		$noTrans=$this->Request['notrans'];		
		$petugasPoli=$this->Request['petugasPoli'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$pjPasien=$this->Request['pjPasien'];
		$cm=$this->Request['cm'];
		$dokter=$this->Request['dokter'];
		$jnsPasien=$this->Request['jnsPasien'];
		$jnsTrans=$this->Request['jnsTrans'];
		$jmlBayar=number_format($this->Request['jmlBayar'],2,',','.');
		$sisaBayar=number_format($this->Request['sisaByr'],2,',','.');
		$sisa=$this->Request['sisaByr'];
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			$idKlinik = RwtjlnRecord::finder()->find('no_trans = ?',$noTrans)->id_klinik;	
			$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;		
			$idKDokter = RwtjlnRecord::finder()->find('no_trans = ?',$noTrans)->dokter;	
			$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;	
						
			$txtJudulTdk = 'KUITANSI PEMBAYARAN RAWAT JALAN';	
			$txtJudulLab = 'KUITANSI PEMBAYARAN LABORATORIUM RAWAT JALAN';	
			$txtJudulRad = 'KUITANSI PEMBAYARAN RADIOLOGI RAWAT JALAN';	
		}
		elseif($jnsPasien == '1') //jika pasien luar/rujukan
		{	
			$txtJudulLab = 'KUITANSI PEMBAYARAN LABORATORIUM';	
			$txtJudulRad = 'KUITANSI PEMBAYARAN RADIOLOGI';	
			
			if($jnsTrans == '0') //Jika pasien rwt inap & pilih transaksi lab
			{	
				$nmDokter = LabJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter;
			}
			elseif($jnsTrans == '1') //Jika pasien rwt inap & pilih transaksi rad
			{
				$nmDokter = RadJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter;
			}	
				
			
		}
		elseif($jnsPasien == '2') //jika pasien rwt inap => bayar tunai
		{
			$txtJudulLab = 'KUITANSI PEMBAYARAN LABORATORIUM RAWAT INAP';
			$txtJudulRad = 'KUITANSI PEMBAYARAN RADIOLOGI RAWAT INAP';	
			
			if($jnsTrans == '0') //Jika pasien rwt inap & pilih transaksi lab
			{	
				$idKDokter = LabJualInapRecord::finder()->find('no_trans = ?',$noTrans)->dokter;	
				$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;
			}
			elseif($jnsTrans == '1') //Jika pasien rwt inap & pilih transaksi rad
			{
				$idKDokter = RadJualInapRecord::finder()->find('no_trans = ?',$noTrans)->dokter;	
				$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;
			}	
		}
		
		$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);
		
		
		$pdf=new reportKwitansi();
		$pdf->AliasNbPages();
		
		//$this->headerKwt($pdf);
		
				
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			//---------------------------- Transaksi Tindakan Rawat Jalan ----------------------------			
			$sql = "SELECT
					tgl, 
					nama, 
					total,
					no_trans_asal
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '1'
					AND jns_trans = 'tindakan rawat jalan' ";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarTdk += $row['total'];
				}
				
				$sayTerbilang=ucwords($this->terbilang($jmlBayarTdk) . ' rupiah');
				
				$noKwitansi = substr($noTransAsal,6,8).'/'.'KW-';
				$noKwitansi .= substr($noTransAsal,4,2).'/';
				$noKwitansi .= substr($noTransAsal,0,4);
		
				$this->headerKwt($pdf,$txtJudulTdk,$noKwitansi,$noTransAsal,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
				
				$pdf->Ln(12);	
		
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(10,5,'No.',1,0,'C');
				$pdf->Cell(150,5,'Tindakan',1,0,'C');
				$pdf->Cell(30,5,'Jumlah',1,0,'C');		
				//Line break
				$pdf->Ln(6);
			
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(150,5,$row['nama'],1,0,'L');
					$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
					$pdf->Ln(5);
					
				}
				
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
				$pdf->SetFont('Arial','',9);						
				$pdf->Cell(80,5,'Total ',0,0,'R');		
				$pdf->Cell(30,5,'Rp ' . number_format($jmlBayarTdk,2,',','.'),1,0,'R');
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
				$pdf->SetFont('Arial','',9);						
				//$pdf->Cell(80,5,'Bayar ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');	
				$pdf->Ln(5);			
				$pdf->SetFont('Arial','',9);				
				$pdf->Cell(10,5,'',0,0,'C');
				//$pdf->Cell(150,5,'Kembalian ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','BU',9);			
				$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJln'));		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);	
				$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');	
				
			}
			
			//---------------------------- Transaksi Laboratorium Rawat Jalan ----------------------------						
			$sql = "SELECT
					tgl, nama, total, no_trans_asal
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '1'
					AND jns_trans = 'laboratorium' ";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarLab += $row['total'];
				}
				
				$sayTerbilang=ucwords($this->terbilang($jmlBayarLab) . ' rupiah');
				
				$noKwitansi = substr($noTransAsal,6,8).'/'.'KW-';
				$noKwitansi .= substr($noTransAsal,4,2).'/';
				$noKwitansi .= substr($noTransAsal,0,4);
		
				$this->headerKwt($pdf,$txtJudulLab,$noKwitansi,$noTransAsal,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
				
				$pdf->Ln(12);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(10,5,'No.',1,0,'C');
				$pdf->Cell(150,5,'Tindakan',1,0,'C');
				$pdf->Cell(30,5,'Jumlah',1,0,'C');		
				//Line break
				$pdf->Ln(6);
				
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(150,5,$row['nama'],1,0,'L');
					$pdf->Cell(30,5,'Rp ' . number_format($row['total'],2,',','.'),1,0,'R');	
					$pdf->Ln(5);
					
				}
				
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
				$pdf->SetFont('Arial','',9);						
				$pdf->Cell(80,5,'Total ',0,0,'R');		
				$pdf->Cell(30,5,'Rp ' . number_format($jmlBayarLab,2,',','.'),1,0,'R');
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
				$pdf->SetFont('Arial','',9);						
				//$pdf->Cell(80,5,'Bayar ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');	
				$pdf->Ln(5);			
				$pdf->SetFont('Arial','',9);				
				$pdf->Cell(10,5,'',0,0,'C');
				//$pdf->Cell(150,5,'Kembalian ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','BU',9);			
				$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJln'));		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);	
				$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');
			}
			
			//---------------------------- Transaksi Radiologi Rawat Jalan ----------------------------
			
			$sql = "SELECT
					tgl, nama, total, no_trans_asal
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '1'
					AND jns_trans = 'radiologi' ";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarRad += $row['total'];
				}
				
				$sayTerbilang=ucwords($this->terbilang($jmlBayarRad) . ' rupiah');
				
				$noKwitansi = substr($noTransAsal,6,8).'/'.'KW-';
				$noKwitansi .= substr($noTransAsal,4,2).'/';
				$noKwitansi .= substr($noTransAsal,0,4);
		
				$this->headerKwt($pdf,$txtJudulRad,$noKwitansi,$noTransAsal,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
				
				$pdf->Ln(12);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(10,5,'No.',1,0,'C');
				$pdf->Cell(150,5,'Tindakan',1,0,'C');
				$pdf->Cell(30,5,'Jumlah',1,0,'C');		
				//Line break
				$pdf->Ln(6);
				
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(150,5,$row['nama'],1,0,'L');
					$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
					$pdf->Ln(5);
					
				}
			
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
				$pdf->SetFont('Arial','',9);						
				$pdf->Cell(80,5,'Total ',0,0,'R');		
				$pdf->Cell(30,5,'Rp ' . number_format($jmlBayarRad,2,',','.'),1,0,'R');
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);		
				$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
				$pdf->SetFont('Arial','',9);						
				//$pdf->Cell(80,5,'Bayar ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');	
				$pdf->Ln(5);			
				$pdf->SetFont('Arial','',9);				
				$pdf->Cell(10,5,'',0,0,'C');
				//$pdf->Cell(150,5,'Kembalian ',0,0,'R');
				//$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','BU',9);			
				$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJln'));		
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);	
				$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');	
			}
		}
		elseif($jnsPasien == '1') //jika pasien luar/rujukan
		{
			$dateNow = date('Y-m-d');
			if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
			{	
				$sql="SELECT 
						tbt_lab_penjualan_lain.tgl,
						tbt_lab_penjualan_lain.cm,
						tbt_lab_penjualan_lain.harga AS total,
						tbm_lab_tindakan.nama
					FROM 
						tbt_lab_penjualan_lain 
						LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					WHERE 
						tbt_lab_penjualan_lain.no_trans='$noTrans'
						AND tbt_lab_penjualan_lain.flag='1'
						AND tbt_lab_penjualan_lain.tgl='$dateNow' "	;
						
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					$this->headerKwt($pdf,$txtJudulLab,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
					
					$pdf->Ln(12);					
					
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(10,5,'No.',1,0,'C');
					$pdf->Cell(150,5,'Tindakan',1,0,'C');
					$pdf->Cell(30,5,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
				
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',9);						
						$pdf->Cell(10,5,$j.'.',1,0,'C');
						
						if($row['nama'] == '')
						{
							if($row['total'] == '2000')
							{
								$pdf->Cell(150,5,'Rujukan',1,0,'L');
							}
							elseif($row['total'] == '2500')
							{
								$pdf->Cell(150,5,'Pendaftaran',1,0,'L');
							}
						}
						else
						{
							$pdf->Cell(150,5,$row['nama'],1,0,'L');
						}
						
						$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(5);
						
					}
				}
			}
			elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
			{
				$sql="SELECT 
						tbt_rad_penjualan_lain.tgl,
						tbt_rad_penjualan_lain.cm,
						tbt_rad_penjualan_lain.harga AS total,
						tbm_rad_tindakan.nama
					FROM 
						tbt_rad_penjualan_lain 
						LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_lain.id_tindakan = tbm_rad_tindakan.kode)
					WHERE 
						tbt_rad_penjualan_lain.no_trans='$noTrans'
						AND tbt_rad_penjualan_lain.flag='1'
						AND tbt_rad_penjualan_lain.tgl='$dateNow'"	;
						
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi rad rwtjln
				{
					$this->headerKwt($pdf,$txtJudulRad,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
					
					$pdf->Ln(12);	
					
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(10,5,'No.',1,0,'C');
					$pdf->Cell(150,5,'Tindakan',1,0,'C');
					$pdf->Cell(30,5,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',9);						
						$pdf->Cell(10,5,$j.'.',1,0,'C');
						
						if($row['nama'] == '')
						{
							if($row['total'] == '2000')
							{
								$pdf->Cell(150,5,'Rujukan',1,0,'L');
							}
							elseif($row['total'] == '2500')
							{
								$pdf->Cell(150,5,'Pendaftaran',1,0,'L');
							}
						}
						else
						{
							$pdf->Cell(150,5,$row['nama'],1,0,'L');
						}
						
						$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(5);
						
					}
				}	
			}
		}
		elseif($jnsPasien == '2') //jika pasien rwt inap => bayar tunai
		{
			if($jnsTrans == '0') //Jika pasien rwt inap & pilih transaksi lab
			{	
				$sql="SELECT 
						tbt_lab_penjualan_inap.tgl,
						tbt_lab_penjualan_inap.cm,
						tbt_lab_penjualan_inap.harga AS total,
						tbm_lab_tindakan.nama
					FROM 
						tbt_lab_penjualan_inap 
						LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					WHERE 
						tbt_lab_penjualan_inap.no_trans='$noTrans'
						AND tbt_lab_penjualan_inap.flag='1' "	;
						
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					$this->headerKwt($pdf,$txtJudulLab,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
					
					$pdf->Ln(12);
					
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(10,5,'No.',1,0,'C');
					$pdf->Cell(150,5,'Tindakan',1,0,'C');
					$pdf->Cell(30,5,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',9);						
						$pdf->Cell(10,5,$j.'.',1,0,'C');
						$pdf->Cell(150,5,$row['nama'],1,0,'L');
						$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(5);
						
					}
				}
			}
			elseif($jnsTrans == '1') //Jika pasien rwt inap & pilih transaksi rad
			{
				$sql="SELECT 
						tbt_rad_penjualan_inap.tgl,
						tbt_rad_penjualan_inap.cm,
						tbt_rad_penjualan_inap.harga AS total,
						tbm_rad_tindakan.nama
					FROM 
						tbt_rad_penjualan_inap 
						LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_inap.id_tindakan = tbm_rad_tindakan.kode)
					WHERE 
						tbt_rad_penjualan_inap.no_trans='$noTrans'
						AND tbt_rad_penjualan_inap.flag='1' "	;
						
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
				if($arrData) //jika ada transaksi lab rwtjln
				{
					$this->headerKwt($pdf,$txtJudulLab,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter); 
					
					$pdf->Ln(12);	
					
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(10,5,'No.',1,0,'C');
					$pdf->Cell(150,5,'Tindakan',1,0,'C');
					$pdf->Cell(30,5,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',9);						
						$pdf->Cell(10,5,$j.'.',1,0,'C');
						$pdf->Cell(150,5,$row['nama'],1,0,'L');
						$pdf->Cell(30,5,'Rp ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(5);
						
					}
				}
			}	
		}	
				
		
		if($jnsPasien != '0') //jika bukan pasien luar/rujukan
		{
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(80,5,'Total ',0,0,'R');		
			$pdf->Cell(30,5,'Rp ' . $jmlTagihan,1,0,'R');
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(80,5,'Bayar ',0,0,'R');
			$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');	
			$pdf->Ln(5);			
			$pdf->SetFont('Arial','',9);				
			$pdf->Cell(10,5,'',0,0,'C');
			$pdf->Cell(150,5,'Kembalian ',0,0,'R');
			$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');		
			$pdf->Ln(5);
			$pdf->SetFont('Arial','BU',9);			
			$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJln'));		
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);	
			$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');		
		}
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>
