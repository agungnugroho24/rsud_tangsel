<?php
class cetakRincianBiayaPasien extends SimakConf
{
	/*
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('7');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		 
	 */
	
	public function onLoad($param)
	{	
		//$tipe=$this->Request['tipe'];	
		$noTrans=$this->Request['notrans'];	
		$noTransObat = $this->Request['notransObat'];	
		$petugasPoli=$this->Request['petugasPoli'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		if($this->Request['jmlTagihan'])
			$jmlTagihanTxt=number_format($this->Request['jmlTagihan'],2,',','.');
		
		$jmlTagihan=$this->Request['jmlTagihan'];
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$cito=$this->Request['cito'];
		
		$cm=$this->Request['cm'];
		$dokter=$this->Request['dokter'];
		$jnsPasien=$this->Request['jnsPasien'];
		$jmlBayar=number_format($this->Request['jmlBayar'],2,',','.');
		$sisaBayar=number_format($this->Request['sisaByr'],2,',','.');
		$sisa=$this->Request['sisaByr'];
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$tglCetak = $this->convertDate(date('Y-m-d'),'3');
		$wktCetak = date('G:i:s');

		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat", "Sabtu","Minggu");
		$hari = $array_hari[date("N")];

		if($jnsPasien == '0') //pasien rawat jalan
		{
			$pjPasien=RwtjlnRecord::finder()->findByPk($noTrans)->penanggung_jawab;
			
			$idKlinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;	
			$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;		
			$idKDokter = RwtjlnRecord::finder()->findByPk($noTrans)->dokter;	
			$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;	
			
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			$idPenjamin = RwtjlnRecord::finder()->findByPk($noTrans)->penjamin;
			$nmKelPenjamin = KelompokRecord::finder()->findByPk($idPenjamin)->nama;	
				
			if($idPenjamin == '02') //Kelompok Penjamin Asuransi
			{			
				$idPerus = RwtjlnRecord::finder()->findByPk($noTrans)->perus_asuransi;	
				$nmPerus = PerusahaanRecord::finder()->findByPk($idPerus)->nama;
				$nmKelPenjamin .= ' ('.$nmPerus.')';	
			}	
						
			$txtJudulTdk = 'KUITANSI PEMBAYARAN RAWAT JALAN';	
			$txtJudulLab = 'KUITANSI PEMBAYARAN LABORATORIUM RAWAT JALAN';	
			$txtJudulRad = 'KUITANSI PEMBAYARAN RADIOLOGI RAWAT JALAN';	
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{	
			$pjPasien=RwtInapRecord::finder()->findByPk($noTrans)->nama_pgg;
			$idKDokter = RwtInapRecord::finder()->findByPk($noTrans)->dokter;	
			$nmDokter = PegawaiRecord::finder()->findByPk($idKDokter)->nama;
			
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			$idPenjamin = RwtInapRecord::finder()->findByPk($noTrans)->penjamin;
			$nmKelPenjamin = KelompokRecord::finder()->findByPk($idPenjamin)->nama;	
			if($idPenjamin == '02') //Kelompok Penjamin Asuransi
			{			
				$idPerus = RwtInapRecord::finder()->findByPk($noTrans)->perus_asuransi;	
				$nmPerus = PerusahaanRecord::finder()->findByPk($idPerus)->nama;	
				$nmKelPenjamin .= ' ('.$nmPerus.')';
			}	
			
			/*
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
				*/
			
		}
		elseif($jnsPasien == '2') //jika pasien bebas
		{
			$nmPasien = RwtjlnLainRecord::finder()->findByPk($noTrans)->nama;
		}
		
		$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);
		
		
		//$pdf=new reportKwitansi();
		$pdf=new reportKwitansi('P','mm','a4');
		$pdf->AliasNbPages();
		
		//$this->headerKwt($pdf);
		
		$pdf->AddPage();	
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		$pdf->Ln(8);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');
		$pdf->Ln(0);
		$pdf->Cell(0,5,'','B',1,'C');
		//$pdf->Ln(1);	
		
		$pdf->SetFont('Arial','BU',10);
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			if($stAsuransi=='0' || $stAsuransi=='')
			{
				$pdf->Cell(0,5,'RINCIAN BIAYA RAWAT JALAN','0',0,'C');				
			}
			elseif($stAsuransi=='1')
			{
				$pdf->Cell(0,5,'RINCIAN BIAYA RAWAT JALAN - KREDIT','0',0,'C');				
			}
		}
		
		if($jnsPasien == '1') //pasien rawat inap
		{
			$pdf->Cell(0,5,'RINCIAN BIAYA RAWAT INAP','0',0,'C');				
		}
		if($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$pdf->Cell(0,5,'RINCIAN BIAYA PASIEN LUAR','0',0,'C');				
		}
		
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		//$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		//$pdf->Ln(7);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(22,3,'No. Register',0,0,'L');
		$pdf->Cell(2,3,':  ',0,0,'L');
		$pdf->Cell(40,3,$noTrans,0,0,'L');
		
		if($jnsPasien == '0') // pasien rawat jalan
		{
			$pdf->Cell(15,3,'Poliklinik',0,0,'L');
			$pdf->Cell(2,3,':  ',0,0,'L');
			$pdf->Cell(30,3,$nmKlinik,0,0,'L');
		}
		
		$pdf->Ln(5);
		if($jnsPasien != '2' && $jnsPasien != '3') //jika bukan pasien bebas
		{
			$pdf->Cell(22,3,'No. CM',0,0,'L');
			$pdf->Cell(2,3,':  ',0,0,'L');
			$pdf->Cell(40,3,$cm,0,0,'L');
			
			$pdf->Cell(15,3,'Dokter',0,0,'L');
			$pdf->Cell(2,3,':  ',0,0,'L');
			$pdf->Cell(48,3,$nmDokter,0,0,'L');
			//$pdf->Ln(5);
		}
				
		
		
		$pdf->Ln(5);
		
		if($jnsPasien != '2' && $jnsPasien != '3') //jika bukan pasien bebas
		{
			$pdf->Cell(22,3,'Penjamin',0,0,'L');
			$pdf->Cell(2,3,':  ',0,0,'L');
			$pdf->Cell(40,3,$nmKelPenjamin,0,0,'L');
		}
		
		$pdf->Ln(1);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(1);
						
		if($jnsPasien == '0') //pasien rawat jalan
		{
			//---------------------------- Transaksi Tindakan Rawat Jalan ----------------------------			
			//if($cito=='0')
			//{
				$sql = "SELECT
					tgl, 
					nama, 
					total,
					no_trans_asal,
					disc
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '0'
					AND jns_trans = 'tindakan rawat jalan' ";
			/*}
			else
			{
				$sql = "SELECT
					tgl, 
					nama, 
					(2 * total) AS total,
					no_trans_asal,
					disc
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '0'
					AND jns_trans = 'tindakan rawat jalan' ";
			}
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Tindakan Rawat Jalan',1,0,'L');				
				$pdf->Ln(5);
								
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarTdk += $row['total'] + $row['disc'];
					$jmlBayarTdkDisc += $row['total'];
					$jmlDisc += $row['disc'];
				}
				
				$pdf->SetFont('Arial','B',8);						
				$pdf->Cell(10,4,'No.',1,0,'C');
				$pdf->Cell(80,4,'Nama Tindakan',1,0,'C');
				$pdf->Cell(60,4,'Dokter',1,0,'C');
				$pdf->Cell(40,4,'Biaya',1,0,'C');	
				$pdf->Ln(4);
			
				$j=0;
				$jml=count($arrData);
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(80,4,ucwords(strtolower($row['nama'])),1,0,'L');
					
					if(strtolower(substr($row['nama'],0,11)) == "pendaftaran" )
					{
						$pdf->Cell(60,4,'-',1,0,'L');
					}
					else
					{
						//$pdf->Cell(90,5,'-',0,0,'L');
						$pdf->Cell(60,4,$nmDokter,1,0,'L');
					}
					
					$pdf->Cell(40,4,number_format($this->bulatkan($row['total']+$row['disc']),2,',','.'),1,0,'R');	
					$pdf->Ln(4);					
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Tindakan Rawat Jalan',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarTdk,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			$jmlTagihan += $jmlBayarTdk;
			
			//---------------------------- Transaksi Laboratorium Rawat Jalan ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbt_lab_penjualan.id_tindakan,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			/*
			if($cito=='0'){$sql .= "tbt_lab_penjualan.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan.harga) AS harga ";}
			*/
			$sql .= "tbt_lab_penjualan.harga ";
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Laboratorium',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarLab += $row['harga'];
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(140,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(40,4, number_format($row['harga'],2,',','.'),1,0,'R');
					$pdf->Ln(4);
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Laboratorium',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarLab,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			$jmlTagihan += $jmlBayarLab;
			
			//---------------------------- Transaksi Radiologi Rawat Jalan ----------------------------
			
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan.film_size, ";
			/*
			if($cito=='0'){$sql .= "tbt_rad_penjualan.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan.harga) AS harga ";}	
			*/
			$sql .= "tbt_rad_penjualan.harga ";
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Radiologi',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarRad += $row['harga'];
					
					$ukuran = $row['film_size'];
					if($ukuran == '0')
					{
						$ukuran = '18 x 24';
					}
					elseif($ukuran == '1')
					{
						$ukuran = '24 x 30';
					}
					elseif($ukuran == '2')
					{
						$ukuran = '30 x 40';
					}
					elseif($ukuran == '3')
					{
						$ukuran = '35 x 35';	
					}
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(110,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(30,4,$ukuran,1,0,'C');
					$pdf->Cell(40,4, number_format($this->bulatkan($row['harga']),2,',','.'),1,0,'R');
					$pdf->Ln(4);
					
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Radiologi',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarRad,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			$jmlTagihan += $jmlBayarRad;
			
			//---------------------------- Transaksi Fisio Rawat Jalan ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbt_fisio_penjualan.id_tindakan,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			/*
			if($cito=='0'){$sql .= "tbt_fisio_penjualan.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan.harga) AS harga ";}
			*/
			$sql .= "tbt_fisio_penjualan.harga ";
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan.id_tindakan = tbm_fisio_tindakan.kode)
					  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Fisio',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarFis += $row['harga'];
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(140,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(40,4, number_format($row['harga'],2,',','.'),1,0,'R');	
					$pdf->Ln(4);
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Fisio',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarFis,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			$jmlTagihan += $jmlBayarFis;
			
			//---------------------------- Transaksi Ambulan Rawat Jalan ----------------------------						
			$sql = "SELECT
						tbt_rwtjln_ambulan.id AS id,
						tbt_rwtjln_ambulan.tgl AS tgl,
						tbm_ambulan.nama AS tujuan,
						CONCAT(tbt_rwtjln_ambulan.catatan,'  ',tbt_rwtjln_ambulan.lainnya) AS catatan, ";
			/*
			if($cito=='0'){$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";}
			else{$sql .= "(2 * tbt_rwtjln_ambulan.tarif) AS tarif ";}
			*/
			$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";
			
			$sql .= "FROM
						tbm_ambulan
						Inner Join tbt_rwtjln_ambulan ON tbt_rwtjln_ambulan.tujuan = tbm_ambulan.id
					WHERE 
						tbt_rwtjln_ambulan.no_trans_rwtjln = '$noTrans'
						AND tbt_rwtjln_ambulan.flag = '0' ";
						
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Ambulan',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarAmb += $row['tarif'];
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->SetFont('Arial','',8);	
					$pdf->Cell(140,4,$row['tujuan'],1,0,'L');
					$pdf->SetFont('Arial','',9);	
					$pdf->Cell(40,4, number_format($row['tarif'],2,',','.'),1,0,'R');
					$pdf->Ln(4);
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Biaya Ambulan',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarAmb,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			$jmlTagihan += $jmlBayarAmb;
			
			//---------------------------- Transaksi Apotik Rawat Jalan ----------------------------
			$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTrans)->penjamin;
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
					no_trans, 
					no_trans_rwtjln, 
					no_reg,
					dokter, 
					SUM(total + if(bhp!='',bhp,0)) AS total,
					tgl
				FROM 
					tbt_obat_jual_karyawan
				WHERE 
					cm='$cm' 
					AND no_trans_rwtjln='$noTrans'
					AND flag = '0'
				GROUP BY no_reg ";
			}
			else
			{
				$sql = "SELECT
					no_trans, 
					no_trans_rwtjln, 
					no_reg,
					dokter, 
					SUM(total + if(bhp!='',bhp,0)) AS total,
					tgl
				FROM 
					tbt_obat_jual 
				WHERE 
					cm='$cm' 
					AND no_trans_rwtjln='$noTrans'
					AND flag = '0'
				GROUP BY no_reg ";
			}
			
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Resep',1,0,'L');				
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',8);						
				$pdf->Cell(10,4,'No.',1,0,'C');
				$pdf->Cell(35,4,'Tanggal',1,0,'C');
				$pdf->Cell(35,4,'No. Resep',1,0,'C');
				$pdf->Cell(70,4,'Dokter',1,0,'C');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(40,4,'Biaya',1,0,'C');	
				$pdf->Ln(4);
					
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(35,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->Cell(35,5,$row['no_reg'],1,0,'C');
					$pdf->Cell(70,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,1,0,'L');
					
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(40,5,number_format($row['total'],2,',','.'),1,0,'R');	
					$pdf->Ln(5);
				}
				
				/*
				$pdf->SetFont('Arial','B',9);
				//$pdf->Cell(160,5,'Sub Total ',0,0,'R');		
				$pdf->Cell(160,0,' ',0,0,'R');		
				//$pdf->Cell(30,5, number_format($jmlBayarApotik,2,',','.'),'T',0,'R');
				$pdf->Cell(30,0, number_format($jmlBayarApotik,2,',','.'),0,0,'R');
				$pdf->Ln(0);
				*/
			}
			
			$jmlTagihan += $jmlBayarApotik;
			$sayTerbilang=ucwords($this->terbilang($jmlTagihan) . ' rupiah');
			
			$pdf->Cell(0,1,'','B',1,'C');
			//$pdf->Ln(1);
			
			$total = $jmlTagihan + $jmlDisc;
			$totalTxt=number_format($total,2,',','.');
			$jmlDiscTxt=number_format($jmlDisc,2,',','.');
			/*
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			$pdf->Cell(90,5, $totalTxt,0,0,'R');
			$pdf->Ln(5);
			*/
			
			
			//$pdf->Cell(100,5,'Discount',0,0,'L');
			
			//if($jmlDisc != '' && $jmlDisc != '0' && TPropertyValue::ensureFloat($jmlDisc))
			//{
			//	$pdf->Cell(90,5,$jmlDiscTxt,0,0,'R');
			//	$pdf->Ln(5);
			//	$pdf->Cell(100,5,'Total Transaksi Setelah Discount',0,0,'L');
			//}
			//else
			//{
			//	$pdf->Cell(90,5,'Rp. 0',0,0,'R');
			//	$pdf->Ln(5);
				$pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			//}			
			
			//$pdf->Ln(5);
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
	
			//$pdf->Cell(100,5,'Total Setelah Discount',0,0,'L');
			// $pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			if(($totBiayaDiscBulat != ''))
			{
				$pdf->Cell(90,5,'('.$sayTerbilang.') 			'.'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
			}
			else
			{
				//$pdf->Cell(90,5,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
				$pdf->Cell(90,5,'('.$sayTerbilang.') 			 '.'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
			}	
			
			//$pdf->Ln(5);
			/*
			$pdf->SetFont('Arial','BI',8);
			$pdf->cell(10,5,'',0,0,'L');	
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
		
			$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');		
			$pdf->Ln(5);	
			
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Jumlah Bayar','TB',0,'L');
			$pdf->Cell(90,5, $jmlBayar,'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Sisa / Kembali','TB',0,'L');
			$pdf->Cell(90,5, number_format($sisa,2,',','.'),'TB',0,'R');
			$pdf->Ln(5);
			/*
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			$pdf->Ln();	*/
			
			/*
			$pdf->Cell(0,0,'','B',1,'C');
			
			$pdf->SetFont('Arial','',7);		
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', Kota Tangerang Selatan, '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			
			
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(70,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');	
			*/
		}
		
		/*----------------------------------- RAWAT INAP ---------------------------------------------------------- */
		elseif($jnsPasien == '1') //pasien rawat inap 
		{
			//---------------------------- Transaksi Laboratorium Rawat Inap Tunai----------------------------						
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbt_lab_penjualan_inap.id_tindakan,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			/*
			if($cito=='0'){$sql .= "tbt_lab_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan_inap.harga) AS harga ";}
			*/
			$sql .= "tbt_lab_penjualan_inap.harga ";
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_lab_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_lab_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  no_trans_inap = '$noTrans'
					  AND flag = '0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Laboratorium',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarLab += $row['harga'];
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(140,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(40,4, number_format($row['harga'],2,',','.'),1,0,'R');
					$pdf->Ln(4);
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Laboratorium',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarLab,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			//---------------------------- Transaksi Radiologi Rawat Inap Tunai ----------------------------
			
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan_inap.film_size, ";
			
			if($cito=='0'){$sql .= "tbt_rad_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan_inap.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_rad_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_rad_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_inap.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  no_trans_inap = '$noTrans'
					  AND flag = '0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Radiologi',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarRad += $row['harga'];
					
					$ukuran = $row['film_size'];
					if($ukuran == '0')
					{
						$ukuran = '18 x 24';
					}
					elseif($ukuran == '1')
					{
						$ukuran = '24 x 30';
					}
					elseif($ukuran == '2')
					{
						$ukuran = '30 x 40';
					}
					elseif($ukuran == '3')
					{
						$ukuran = '35 x 35';	
					}
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(110,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(30,4,$ukuran,1,0,'C');
					$pdf->Cell(40,4, number_format($this->bulatkan($row['harga']),2,',','.'),1,0,'R');
					$pdf->Ln(4);
					
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Radiologi',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarRad,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			//---------------------------- Transaksi Fisio Rawat Inap Tunai ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbt_fisio_penjualan_inap.id_tindakan,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			
			if($cito=='0'){$sql .= "tbt_fisio_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan_inap.harga) AS harga ";}
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_fisio_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_fisio_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_inap.id_tindakan = tbm_fisio_tindakan.kode)
					  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  no_trans_inap = '$noTrans'
					  AND flag = '0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Fisio',1,0,'L');				
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarFis += $row['harga'];
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,4,$j.'.',1,0,'C');
					$pdf->Cell(140,4,$row['nm_tdk'],1,0,'L');
					$pdf->Cell(40,4, number_format($row['harga'],2,',','.'),1,0,'R');	
					$pdf->Ln(4);
				}
				
				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(150,4,'Sub Total Fisio',1,0,'C');
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(40,4, number_format($jmlBayarFis,2,',','.'),1,0,'R');
				$pdf->Ln(5);	
			}
			
			//---------------------------- Transaksi Apotik Rawat Inap Tunai ----------------------------
			$kelompokPasien = RwtInapRecord::finder()->findByPk($noTrans)->penjamin;
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
					no_trans, 
					no_trans_inap,
					no_reg, 
					dokter, 
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual_inap_karyawan
				WHERE 
					cm='$cm' 
					AND no_trans_inap='$noTrans'
					AND flag = '0'
					AND st_bayar = '1'
				GROUP BY no_reg ";
			}
			else
			{
				$sql = "SELECT
					no_trans, 
					no_trans_inap, 
					no_reg,
					dokter, 
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual_inap
				WHERE 
					cm='$cm' 
					AND no_trans_inap='$noTrans'
					AND flag = '0'
					AND st_bayar = '1'
				GROUP BY no_reg ";
			}
			
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BI',8);
				$pdf->Cell(190,5,'Biaya Resep',1,0,'L');				
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',8);						
				$pdf->Cell(10,4,'No.',1,0,'C');
				$pdf->Cell(35,4,'Tanggal',1,0,'C');
				$pdf->Cell(35,4,'No. Resep',1,0,'C');
				$pdf->Cell(70,4,'Dokter',1,0,'C');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(40,4,'Biaya',1,0,'C');	
				$pdf->Ln(4);
					
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',1,0,'C');
					$pdf->Cell(35,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->Cell(35,5,$row['no_reg'],1,0,'C');
					$pdf->Cell(70,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,1,0,'L');
					
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(40,5,number_format($row['total'],2,',','.'),1,0,'R');	
					$pdf->Ln(5);
				}
				
				/*
				$pdf->SetFont('Arial','B',9);
				//$pdf->Cell(160,5,'Sub Total ',0,0,'R');		
				$pdf->Cell(160,0,' ',0,0,'R');		
				//$pdf->Cell(30,5, number_format($jmlBayarApotik,2,',','.'),'T',0,'R');
				$pdf->Cell(30,0, number_format($jmlBayarApotik,2,',','.'),0,0,'R');
				$pdf->Ln(0);
				*/
			}
			
			
			$pdf->Cell(0,2,'','B',1,'C');
			$pdf->Ln(1);
			
			$total = $jmlTagihan + $jmlDisc;
			$totalTxt=number_format($total,2,',','.');
			$jmlDiscTxt=number_format($jmlDisc,2,',','.');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			$pdf->Cell(90,5, $totalTxt,0,0,'R');
			$pdf->Ln(5);
			
			
			$pdf->Cell(100,5,'Discount',0,0,'L');
			
			if($jmlDisc != '' && $jmlDisc != '0' && TPropertyValue::ensureFloat($jmlDisc))
			{
				$pdf->Cell(90,5,$jmlDiscTxt,0,0,'R');
			}
			else
			{
				$pdf->Cell(90,5,'Rp. 0',0,0,'R');
			}		
			
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Total Setelah Discount',0,0,'L');
			if(($totBiayaDiscBulat != ''))
			{
				$pdf->Cell(90,5,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
			}
			else
			{
				//$pdf->Cell(90,5,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
				$pdf->Cell(90,5,'Rp. '.$jmlTagihanTxt,0,0,'R');
			}	
			
			$pdf->Ln(5);
		
			$pdf->SetFont('Arial','BI',8);
			$pdf->cell(10,5,'',0,0,'L');	
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
		
			$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');		
			$pdf->Ln(5);	
			
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Jumlah Bayar','TB',0,'L');
			$pdf->Cell(90,5, $jmlBayar,'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Sisa / Kembali','TB',0,'L');
			$pdf->Cell(90,5, number_format($sisa,2,',','.'),'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(5);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');
		}
		//---------------------------- pasien bebas ----------------------------	
		elseif($jnsPasien == '2' || $jnsPasien == '3' )
		{
			//---------------------------- Transaksi Laboratorium Rawat Jalan Lain----------------------------						
			$sql = "SELECT 
					  tbt_lab_penjualan_lain.no_trans,
					  tbt_lab_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_lab_penjualan_lain.tgl,
					  tbt_lab_penjualan_lain.harga AS total,
					  tbt_lab_penjualan_lain.id_tindakan,
					  tbm_lab_tindakan.nama
					FROM
					  tbt_lab_penjualan_lain
					  LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					WHERE 
					  tbt_lab_penjualan_lain.no_trans_rwtjln_lain = '$noTrans'
					  AND tbt_lab_penjualan_lain.flag = '0' ";
					  
			/*$sql = "SELECT
					*
				FROM
					tbt_lab_penjualan_lain 
				WHERE 
					no_trans_rwtjln_lain = '$noTrans'
					AND flag = '0' ";
					*/
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				foreach($arrData as $row)
				{
					//$jmlBayarLab += $row['harga_non_adm'];
					//$jmlBayarAdmLab += $row['harga_adm'];
					$jmlBayarTotal += $row['total'];
					
					if($row['id_tindakan'] == 'PDT') 
					{
						$jmlBayarAdmLab += $row['total'];
					}
					else
					{
						$jmlBayarLab += $row['total'];
					}
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Laboratorium',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarLab,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					if($row['id_tindakan'] != 'PDT') 
					{
						$j += 1;
						$pdf->SetFont('Arial','',8);						
						$pdf->Cell(10,4,$j.'.',0,0,'C');
						$pdf->Cell(145,4,$row['nama'],0,0,'L');
						$pdf->Cell(35,4,number_format($row['total'],2,',','.'),0,0,'R');	
						//$pdf->SetFont('Arial','B',9);		
						//$pdf->Cell(35,4,'Rp. ' . number_format($jmlBayarLab,2,',','.'),0,0,'R');
						$pdf->Ln(4);
					}
				}
				
				$pdf->Ln(3);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Adm. Laboratorium',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarAdmLab,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
			}
			
			//---------------------------- Transaksi Radiologi Rawat Jalan Lain ----------------------------
			$sql = "SELECT 
					  tbt_rad_penjualan_lain.no_trans,
					  tbt_rad_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_rad_penjualan_lain.tgl,
					  tbt_rad_penjualan_lain.harga AS total,
					  tbt_rad_penjualan_lain.id_tindakan,
					  tbm_rad_tindakan.nama
					FROM
					  tbt_rad_penjualan_lain
					  LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_lain.id_tindakan = tbm_rad_tindakan.kode)
					WHERE 
					  tbt_rad_penjualan_lain.no_trans_rwtjln_lain = '$noTrans'
					  AND tbt_rad_penjualan_lain.flag = '0' ";
			/* $sql = "SELECT
					*
				FROM 
					tbt_rad_penjualan_lain 
				WHERE 
					no_trans_pas_luar = '$noTrans'
					AND flag = '0' ";
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				foreach($arrData as $row)
				{
					//$jmlBayarRad += $row['harga_non_adm'];
					//$jmlBayarAdmRad += $row['harga_adm'];
					$jmlBayarTotal += $row['total'];
					
					if($row['id_tindakan'] == 'PDT') 
					{
						$jmlBayarAdmRad += $row['total'];
					}
					else
					{
						$jmlBayarRad += $row['total'];
					}
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Radiologi',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarRad,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					if($row['id_tindakan'] != 'PDT') 
					{
						$j += 1;
						$pdf->SetFont('Arial','',8);						
						$pdf->Cell(10,4,$j.'.',0,0,'C');
						$pdf->Cell(145,4,$row['nama'],0,0,'L');
						$pdf->Cell(35,4,number_format($row['total'],2,',','.'),0,0,'R');	
						//$pdf->SetFont('Arial','B',9);		
						//$pdf->Cell(35,4,'Rp. ' . number_format($jmlBayarRad,2,',','.'),0,0,'R');
						$pdf->Ln(4);
					}
				}
				
				$pdf->Ln(3);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Adm. Radiologi',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarAdmRad,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
			}
			
			//---------------------------- Transaksi Fisio Rawat Jalan Lain ----------------------------						
			$sql = "SELECT 
					  tbt_fisio_penjualan_lain.no_trans,
					  tbt_fisio_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_fisio_penjualan_lain.tgl,
					  tbt_fisio_penjualan_lain.harga AS total,
					  tbt_fisio_penjualan_lain.id_tindakan,
					  tbm_fisio_tindakan.nama
					FROM
					  tbt_fisio_penjualan_lain
					  LEFT JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_lain.id_tindakan = tbm_fisio_tindakan.kode)
					WHERE 
					  tbt_fisio_penjualan_lain.no_trans_rwtjln_lain = '$noTrans'
					  AND tbt_fisio_penjualan_lain.flag = '0' ";
					  
			/* $sql = "SELECT
					*
				FROM 
					tbt_fisio_penjualan_lain 
				WHERE 
					no_trans_pas_luar = '$noTrans'
					AND flag = '0' ";
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				foreach($arrData as $row)
				{
					//$jmlBayarFisio += $row['harga_non_adm'];
					//$jmlBayarAdmFisio += $row['harga_adm'];
					$jmlBayarTotal += $row['total'];
					
					if($row['id_tindakan'] == 'PDT') 
					{
						$jmlBayarAdmFisio += $row['total'];
					}
					else
					{
						$jmlBayarFisio += $row['total'];
					}
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Fisio',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarFisio,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
				
				$j=0;
				foreach($arrData as $row)
				{
					if($row['id_tindakan'] != 'PDT') 
					{
						$j += 1;
						$pdf->SetFont('Arial','',8);						
						$pdf->Cell(10,4,$j.'.',0,0,'C');
						$pdf->Cell(145,4,$row['nama'],0,0,'L');
						$pdf->Cell(35,4,number_format($row['total'],2,',','.'),0,0,'R');	
						//$pdf->SetFont('Arial','B',9);		
						//$pdf->Cell(35,4,'Rp. ' . number_format($jmlBayarFisio,2,',','.'),0,0,'R');
						$pdf->Ln(4);
					}
				}
				
				$pdf->Ln(3);
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'- Biaya Adm. Fisio',0,0,'L');				
				$pdf->SetFont('Arial','B',9);		
				$pdf->Cell(30,5, number_format($jmlBayarAdmFisio,2,',','.'),'0',0,'R');
				$pdf->Ln(5);
			}
			
			
			//---------------------------- Transaksi Apotik ----------------------------
			$noTransObat = $this->Request['notransObat'];		
			$sql = "SELECT
				no_trans,
				no_trans_pas_luar,
				no_reg,
				SUM(total + bhp) AS total,
				tgl
			FROM ";
			
			if($jnsPasien == '2')
			{ $sql .="tbt_obat_jual_lain "; }
			elseif($jnsPasien == '3')
			{ $sql .="tbt_obat_jual_lain_karyawan ";}
			
			$sql .=" WHERE 
				no_trans_rwtjln_lain = '$noTrans'
				AND flag = '0'
			GROUP BY no_reg ";
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BIU',8);
				$pdf->Cell(190,5,'Biaya Resep',0,0,'L');				
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(25,5,'Tanggal',0,0,'C');
				$pdf->Cell(35,5,'No. Resep',0,0,'C');
				$pdf->Cell(90,5,'',0,0,'L');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(30,5,' ',0,0,'R');	
				$pdf->Ln(5);
					
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),0,0,'C');
					$pdf->Cell(35,5,$row['no_reg'],0,0,'C');
					$pdf->Cell(90,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,0,0,'L');
					$pdf->Cell(30,5, number_format($row['total'],2,',','.'),0,0,'R');	
					$pdf->Ln(5);
					
				}
			
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,5,'Sub Total ',0,0,'R');		
				$pdf->Cell(30,5, number_format($jmlBayarApotik,2,',','.'),'T',0,'R');
				$pdf->Ln(5);
			}
			
			$pdf->Cell(0,2,'','B',1,'C');
			$pdf->Ln(1);
			
			$total = $jmlTagihan + $jmlDisc;
			$totalTxt=number_format($total,2,',','.');
			$jmlDiscTxt=number_format($jmlDisc,2,',','.');
			
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			$pdf->Cell(90,5, $totalTxt,0,0,'R');
			$pdf->Ln(5);
			
			
			$pdf->Cell(100,5,'Discount',0,0,'L');
			
			if($jmlDisc != '' && $jmlDisc != '0' && TPropertyValue::ensureFloat($jmlDisc))
			{
				$pdf->Cell(90,5,$jmlDiscTxt,0,0,'R');
			}
			else
			{
				$pdf->Cell(90,5,'Rp. 0',0,0,'R');
			}		
			
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Total Setelah Discount',0,0,'L');
			if(($totBiayaDiscBulat != ''))
			{
				$pdf->Cell(90,5,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
			}
			else
			{
				//$pdf->Cell(90,5,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
				$pdf->Cell(90,5,'Rp. '.$jmlTagihanTxt,0,0,'R');
			}	
			
			$pdf->Ln(5);
		
			$pdf->SetFont('Arial','BI',8);
			$pdf->cell(10,5,'',0,0,'L');	
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
		
			$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');		
			$pdf->Ln(5);	
			
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Jumlah Bayar','TB',0,'L');
			$pdf->Cell(90,5, $jmlBayar,'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Sisa / Kembali','TB',0,'L');
			$pdf->Cell(90,5, number_format($sisa,2,',','.'),'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(5);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');	
		}
				
		/*
		if($jnsPasien != '0') //jika bukan pasien luar/rujukan
		{
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(80,5,'Total ',0,0,'R');		
			$pdf->Cell(30,5, $jmlTagihan,1,0,'R');
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(80,5,'Bayar ',0,0,'R');
			$pdf->Cell(30,5, $jmlBayar,1,0,'R');	
			$pdf->Ln(5);			
			$pdf->SetFont('Arial','',9);				
			$pdf->Cell(10,5,'',0,0,'C');
			$pdf->Cell(150,5,'Kembalian ',0,0,'R');
			$pdf->Cell(30,5, $sisaBayar,1,0,'R');		
			$pdf->Ln(5);
			$pdf->SetFont('Arial','BU',9);			
			$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));		
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);	
			$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');		
		}
		*/
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>
