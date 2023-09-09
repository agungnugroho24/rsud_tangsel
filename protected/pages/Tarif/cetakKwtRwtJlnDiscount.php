<?php
class cetakKwtRwtJlnDiscount extends SimakConf
{
	public function onInit($param)
	 {		
	 $this->getResponse()->setContentType("application/pdf");
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		 
	 
	public function headerKwt($pdf,$txtJudul,$noKwitansi,$noTrans,$cm,$nmPasien,$sayTerbilang,$nmDokter) 
	{ 
		$pdf->AddPage();
		
		//$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		
		$pdf->Ln(8);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,5,'           Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(1);
		$pdf->SetFont('Arial','BU',10);
		
		
		$pdf->Cell(0,5,$txtJudul,'0',0,'C');
		
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(27,5,'No. Register',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(50,5,$noTrans,0,0,'L');
		
		$pdf->Cell(27,5,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(105,5,$sayTerbilang,0,0,'L');
		
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			$pdf->Cell(15,5,'No. CM',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(30,5,$cm,0,0,'L');
		}
				
		$pdf->Ln(5);		
		$pdf->Cell(45,5,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		//$pdf->Cell(105,10,$nmPasien .' / '. $pjPasien,0,0,'L');
		$pdf->Cell(70,5,$nmPasien ,0,0,'L');
		
		if($jnsPasien == '0') //jika pasien rawat jln
		{
			$pdf->Cell(15,5,'Poliklinik',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(30,5,$nmKlinik,0,0,'L');
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
		
		$pdf->Cell(27,5,'Dokter',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		
		if($jnsPasien != '1') //jika bukan pasien luar/rujukan
		{
			$pdf->Cell(105,5,$nmDokter,0,0,'L');
		}
		elseif($jnsPasien == '1')
		{
			if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
			{	
				$pdf->Cell(105,5,LabJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
			}
			elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
			{
				$pdf->Cell(105,5,RadJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
			}
			
		}
	}
	
	public function onLoad($param)
	{	
		$this->getResponse()->setContentType("application/pdf");
		//$tipe=$this->Request['tipe'];	
		$noTrans=$this->Request['notrans'];	
		$noTransPasLuar = $this->Request['noTransPasLuar'];	
		$petugasPoli=$this->Request['petugasPoli'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		if($this->Request['jmlTagihan'])
			$jmlTagihanTxt=number_format($this->Request['jmlTagihan'],2,',','.');
		
		$jmlTagihan=$this->Request['jmlTagihan'];
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$cito=$this->Request['cito'];
		$stUlang=$this->Request['stUlang'];
		
		$cm=$this->Request['cm'];
		$dokter=$this->Request['dokter'];
		$jnsPasien=$this->Request['jnsPasien'];
		$jmlBayar=number_format($this->Request['jmlBayar'],2,',','.');
		$sisaBayar=number_format($this->Request['sisaByr'],2,',','.');
		$sisa=$this->Request['sisaByr'];
		$sisaBulat=$this->Request['sisaBulat'];
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
				
			if($idPenjamin == '02') //Kelompok Penjamin Asuransi/jamper
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
			if($idPenjamin == '02') //Kelompok Penjamin Asuransi/jamper
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
		
		if($stUlang == '1')
			$pdf->water = '1';
		
		//$this->headerKwt($pdf);
		
		$pdf->AddPage();	
		//$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',12);
		//$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L');
		$pdf->Ln(8);		
		$pdf->SetFont('Arial','',9);
		//$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'L');	
		$pdf->Ln(4);
		$pdf->Cell(0,4,'Telp. (021) 74718440','0',0,'L');	
		$pdf->Ln(0);
		$pdf->Cell(0,5,'','B',1,'C');
		//$pdf->Ln(1);	
		
		$pdf->SetFont('Arial','BU',10);
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			if($stAsuransi=='0' || $stAsuransi=='')
			{
				$pdf->Cell(0,5,'KUITANSI PEMBAYARAN RAWAT JALAN','0',0,'C');				
			}
			elseif($stAsuransi=='1')
			{
				$pdf->Cell(0,5,'KUITANSI PEMBAYARAN RAWAT JALAN - KREDIT','0',0,'C');				
			}
		}
		
		if($jnsPasien == '1') //pasien rawat inap
		{
			$pdf->Cell(0,5,'KUITANSI PEMBAYARAN RAWAT INAP','0',0,'C');				
		}
		if($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$pdf->Cell(0,5,'KUITANSI PEMBAYARAN PASIEN BEBAS','0',0,'C');				
		}
		
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(7);		
		
		$pdf->SetFont('Arial','',9);
		
		if($jnsPasien != '0') // bukan pasien rawat jalan
		{
			$nmKlinik = '-';
		}
		
		if($jnsPasien == '2' || $jnsPasien == '3') //jika bukan pasien bebas
		{
			$tmp = PasienLuarRecord::finder()->findByPk($noTransPasLuar)->cm;
			$cm = $tmp;
			$nmDokter = '-'; 
		}
		
		if($jnsPasien != '2' && $jnsPasien != '3' ) //jika bukan pasien bebas
		{
			//$nm = $nmPasien.' / '.ucwords($pjPasien);
			$nm = $nmPasien;
		}
		else
		{
			$nm = $nmPasien;
		}
		
		$pdf->SetWidths(array(20,3,35,15,3,45,20,3,50));
		$pdf->SetAligns(array('L','C','L','L','C','L','L','C','L'));
		
		
		if($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
			$pdf->RowNoBorder(array('No. Register',':',$noTransPasLuar,'Poliklinik',':',$nmKlinik,'Terima dari',':',$nm));
		else
			$pdf->RowNoBorder(array('No. Register',':',$noTrans,'Poliklinik',':',$nmKlinik,'Terima dari',':',$nm));
		
		$pdf->RowNoBorder(array('No. CM',':',$cm,'Dokter',':',$nmDokter));
		
		$pdf->SetWidths(array(20,3,150));
		
		if($jnsPasien != '2' && $jnsPasien != '3') //jika bukan pasien bebas
		{
			$pdf->RowNoBorder(array('Penjamin',':',$nmKelPenjamin));
		}
		
		if($this->Request['diagnosa'] !='')
			$pdf->RowNoBorder(array('Diagnosa',':',$this->Request['diagnosa']));
		
		$pdf->Cell(0,2,'','B',1,'C');
		$pdf->Ln(1);
						
		if($jnsPasien == '0') //pasien rawat jalan
		{
			//---------------------------- Transaksi Tindakan Rawat Jalan ----------------------------			
			if($cito=='0')
			{
				$sql = "SELECT
					tgl, 
					nama, 
					total,
					no_trans_asal,
					tanggungan_asuransi,
					disc
				FROM 
					view_biaya_total_rwtjln 
				WHERE 
					cm='$cm' 
					AND no_trans='$noTrans'
					AND flag = '1'
					AND jns_trans = 'tindakan rawat jalan' ";
			}
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
					AND flag = '1'
					AND jns_trans = 'tindakan rawat jalan' ";
			}			
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$sqlDisc = "SELECT disc FROM view_biaya_total_rwtjln WHERE cm='$cm' AND no_trans='$noTrans' AND flag = '1' AND jns_trans = 'tindakan rawat jalan' AND disc > 0 ";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$sqlTanggungan = "SELECT tanggungan_asuransi FROM view_biaya_total_rwtjln WHERE cm='$cm' AND no_trans='$noTrans' AND flag = '1' AND jns_trans = 'tindakan rawat jalan' AND tanggungan_asuransi > 0 ";
				$arrTanggungan = $this->queryAction($sqlTanggungan,'S');
					
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Tindakan Rawat Jalan'));
				
				$pdf->SetFont('Arial','B',9);
				$pdf->SetAligns(array('C','C','C','C','C','C'));
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,50,40,30,30,30));
					$pdf->Row(array('No.','Nama Tindakan','Dokter','Tanggungan Pasien','Discount','Tanggungan Asuransi'));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,50,70,30,30));
					$pdf->Row(array('No.','Nama Tindakan','Dokter','Tanggungan Pasien','Tanggungan Asuransi'));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(10,50,70,30,30));
					$pdf->Row(array('No.','Nama Tindakan','Dokter','Tanggungan Pasien','Discount'));		
				}	
				else
				{	
					$pdf->SetWidths(array(10,80,70,30));
					$pdf->Row(array('No.','Nama Tindakan','Dokter','Tanggungan Pasien'));	
				}	
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','L','R','R','R'));
				
				$j=0;
				$jml=count($arrData);								
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarTdk += $row['total'];
					$jmlBayarTdkDisc += $row['total'];
					$jmlDiscTdk += $row['disc'];
					$jmlBebanAsuransiTdk += $row['tanggungan_asuransi'];
					
					$j += 1;
					
					if(strtolower(substr($row['nama'],0,11)) == "pendaftaran" ? $dok = '-' : $dok = $nmDokter);					
					
					$biaya = number_format($row['total'],2,',','.');
					
					if($row['disc'] > 0)
						$disc = number_format($row['disc'],2,',','.');
					
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					/*
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya,$disc));
					}
					*/
					
					if($arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya,$disc,$bebanAsuransi));
					}	
					elseif(!$arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya,$bebanAsuransi));
					}	
					elseif($arrDisc && !$arrTanggungan)
					{
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya,$disc));	
					}	
					else
					{	
						$pdf->Row(array($j.'.',ucwords(strtolower($row['nama'])),$dok,$biaya));
					}	
					
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarTdkTxt = number_format($jmlBayarTdk,2,',','.');
				$jmlDiscTxt = number_format($jmlDiscTdk,2,',','.');
				$jmlBebanAsuransiTxt = number_format($jmlBebanAsuransiTdk,2,',','.');
				
				$grandTotal += $jmlBayarTdk;
				$grandDisc += $jmlDiscTdk;
				$grandBeban += $jmlBebanAsuransiTdk;
				
				$pdf->SetFont('Arial','B',9);
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt,$jmlDiscTxt,$jmlBebanAsuransiTxt));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt,$jmlBebanAsuransiTxt));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt,$jmlBebanAsuransiTxt));
				}	
				else
				{	
					$pdf->SetWidths(array(160,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt));
				}
			}
			
			$pdf->Cell(0,2,'','B',1,'C');
			
			//---------------------------- Transaksi Laboratorium Rawat Jalan ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbt_lab_penjualan.id_tindakan,
					  tbt_lab_penjualan.disc,
					  tbt_lab_penjualan.tanggungan_asuransi,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			
			if($cito=='0'){$sql .= "tbt_lab_penjualan.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan.harga) AS harga ";}
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{	
				$sqlDisc = "SELECT tbt_lab_penjualan.tanggungan_asuransi FROM tbt_rawat_jalan
					  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_lab_penjualan.disc > 0 ";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$sqlTanggungan = "SELECT tbt_lab_penjualan.tanggungan_asuransi FROM tbt_rawat_jalan
					  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_lab_penjualan.tanggungan_asuransi > 0 ";
				$arrTanggungan = $this->queryAction($sqlTanggungan,'S');
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Laboratorium'));	
				
				$pdf->SetAligns(array('C','C','C','C','C','C'));
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,120,30,30));	
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(10,120,30,30));		
				}	
				else
				{	
					$pdf->SetWidths(array(10,150,30));	
				}	
				
				$pdf->SetAligns(array('C','L','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarLab += $row['harga'];
					$jmlDiscLab += $row['disc'];
					$jmlAsuransiLab += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['harga'],2,',','.');
					
					if($arrDisc)
						$disc = number_format($row['disc'],2,',','.');
						
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc,$bebanAsuransi));
					}	
					elseif(!$arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$bebanAsuransi));		
					}	
					elseif($arrDisc && !$arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc));	
					}	
					else
					{	
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya));	
					}	
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarLabTxt = number_format($jmlBayarLab,2,',','.');
				$jmlDiscLabTxt = number_format($jmlDiscLab,2,',','.');
				$jmlAsuransiLabTxt = number_format($jmlAsuransiLab,2,',','.');
				
				$grandTotal += $jmlBayarLab;
				$grandDisc += $jmlDiscLab;
				$grandBeban += $jmlAsuransiLab;
				
				$pdf->SetFont('Arial','B',9);
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Laboratorium',$jmlBayarLabTxt,$jmlDiscLabTxt,$jmlAsuransiLabTxt));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Laboratorium',$jmlBayarLabTxt,$jmlAsuransiLabTxt));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt,$jmlBebanAsuransiTxt));
				}	
				else
				{	
					$pdf->SetWidths(array(160,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Laboratorium',$jmlBayarLabTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi Radiologi Rawat Jalan ----------------------------			
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan.film_size,
					  tbt_rad_penjualan.disc,
					  tbt_rad_penjualan.tanggungan_asuransi, ";
			
			if($cito=='0'){$sql .= "tbt_rad_penjualan.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$sqlDisc = "SELECT tbt_rad_penjualan.disc FROM tbt_rawat_jalan
					  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_rad_penjualan.disc > 0 ";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$sqlTanggungan = "SELECT tbt_rad_penjualan.tanggungan_asuransi FROM tbt_rawat_jalan
					  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_rad_penjualan.tanggungan_asuransi > 0 ";
				$arrTanggungan = $this->queryAction($sqlTanggungan,'S');
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Radiologi'));	
				
				$pdf->SetAligns(array('C','C','C','C','C','C'));
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,60,30,30,30,30));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));				
				}	
				else
				{	
					$pdf->SetWidths(array(10,90,60,30));
				}	
				
				$pdf->SetAligns(array('C','L','C','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarRad += $row['harga'];
					$jmlDiscRad += $row['disc'];
					$jmlAsuransiRad += $row['tanggungan_asuransi'];
					
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
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc,$bebanAsuransi));
						else
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$bebanAsuransi));	
					}
					else
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc));
						else
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya));	
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarRadTxt = number_format($jmlBayarRad,2,',','.');
				$jmlDiscRadTxt = number_format($jmlDiscRad,2,',','.');
				$jmlAsuransiRadTxt = number_format($jmlAsuransiRad,2,',','.');
				
				$grandTotal += $jmlBayarRad;
				$grandDisc += $jmlDiscRad;
				$grandBeban += $jmlAsuransiRad;
				
				$pdf->SetFont('Arial','B',9);
				
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(100,30,30,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt,$jmlDiscRadTxt,$jmlAsuransiRadTxt));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt,$jmlAsuransiRadTxt));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt,$jmlDiscRadTxt));
				}	
				else
				{	
					$pdf->SetWidths(array(160,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi CT Scan Rawat Jalan ----------------------------			
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbm_ctscan_tindakan.nama AS nm_tdk,
					  tbm_ctscan_kelompok.nama AS kel_tdk,
					  tbm_ctscan_kategori.jenis AS kateg_tdk,  
					  tbt_ctscan_penjualan.film_size,
					  tbt_ctscan_penjualan.disc,
					  tbt_ctscan_penjualan.tanggungan_asuransi, ";
			
			if($cito=='0'){$sql .= "tbt_ctscan_penjualan.harga ";}
			else{$sql .= "(2 * tbt_ctscan_penjualan.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_ctscan_penjualan ON (tbt_rawat_jalan.no_trans = tbt_ctscan_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan.id_tindakan = tbm_ctscan_tindakan.kode)
					  LEFT JOIN tbm_ctscan_kelompok ON (tbm_ctscan_tindakan.kelompok = tbm_ctscan_kelompok.kode)
					  LEFT JOIN tbm_ctscan_kategori ON (tbm_ctscan_tindakan.kategori = tbm_ctscan_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$sqlDisc = "SELECT tbt_ctscan_penjualan.disc FROM tbt_rawat_jalan
					  INNER JOIN tbt_ctscan_penjualan ON (tbt_rawat_jalan.no_trans = tbt_ctscan_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_ctscan_penjualan.disc > 0 ";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$sqlTanggungan = "SELECT tbt_ctscan_penjualan.tanggungan_asuransi FROM tbt_rawat_jalan
					  INNER JOIN tbt_ctscan_penjualan ON (tbt_rawat_jalan.no_trans = tbt_ctscan_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_ctscan_penjualan.tanggungan_asuransi > 0 ";
				$arrTanggungan = $this->queryAction($sqlTanggungan,'S');
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya CT Scan'));	
				
				$pdf->SetAligns(array('C','C','C','C','C','C'));
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,60,30,30,30,30));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));				
				}	
				else
				{	
					$pdf->SetWidths(array(10,90,60,30));
				}	
				
				$pdf->SetAligns(array('C','L','C','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarCtScan += $row['harga'];
					$jmlDiscCtScan += $row['disc'];
					$jmlAsuransiCtScan += $row['tanggungan_asuransi'];
					
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
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc,$bebanAsuransi));
						else
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$bebanAsuransi));	
					}
					else
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc));
						else
							$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya));	
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarCtScanTxt = number_format($jmlBayarCtScan,2,',','.');
				$jmlDiscCtScanTxt = number_format($jmlDiscCtScan,2,',','.');
				$jmlAsuransiCtScanTxt = number_format($jmlAsuransiCtScan,2,',','.');
				
				$grandTotal += $jmlBayarCtScan;
				$grandDisc += $jmlDiscCtScan;
				$grandBeban += $jmlAsuransiCtScan;
				
				$pdf->SetFont('Arial','B',9);
				
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(100,30,30,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt,$jmlAsuransiCtScanTxt));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt,$jmlAsuransiCtScanTxt));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt));
				}	
				else
				{	
					$pdf->SetWidths(array(160,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
						
			
			//---------------------------- Transaksi Fisio Rawat Jalan ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbt_fisio_penjualan.id_tindakan,
					  tbt_fisio_penjualan.disc,
					  tbt_fisio_penjualan.tanggungan_asuransi,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			
			if($cito=='0'){$sql .= "tbt_fisio_penjualan.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan.harga) AS harga ";}
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan.id_tindakan = tbm_fisio_tindakan.kode)
					  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$sqlDisc = "SELECT tbt_fisio_penjualan.disc FROM tbt_rawat_jalan
					  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_fisio_penjualan.disc > 0 ";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$sqlTanggungan = "SELECT tbt_fisio_penjualan.tanggungan_asuransi FROM tbt_rawat_jalan
					  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
					WHERE
					  tbt_rawat_jalan.no_trans = '$noTrans'
					  AND tbt_rawat_jalan.flag = '1'
					  AND  tbt_fisio_penjualan.tanggungan_asuransi > 0 ";
				$arrTanggungan = $this->queryAction($sqlTanggungan,'S');
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Elektromedik'));	
				
				$pdf->SetAligns(array('C','C','C','C','C','C'));
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,90,30,30,30));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(10,120,30,30));	
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(10,120,30,30));		
				}	
				else
				{	
					$pdf->SetWidths(array(10,150,30));	
				}	
				
				$pdf->SetAligns(array('C','L','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarFis += $row['harga'];
					$jmlDiscFis += $row['disc'];
					$jmlAsuransiFis += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['harga'],2,',','.');
					
					if($arrDisc)
						$disc = number_format($row['disc'],2,',','.');
						
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc,$bebanAsuransi));
					}	
					elseif(!$arrDisc && $arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$bebanAsuransi));		
					}	
					elseif($arrDisc && !$arrTanggungan)
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc));	
					}	
					else
					{	
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya));	
					}	
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarFisTxt = number_format($jmlBayarFis,2,',','.');
				$jmlDiscFisTxt = number_format($jmlDiscFis,2,',','.');
				$jmlAsuransiFisTxt = number_format($jmlAsuransiFis,2,',','.');
				
				$grandTotal += $jmlBayarFis;
				$grandDisc += $jmlDiscFis;
				$grandBeban += $jmlAsuransiFis;
				
				$pdf->SetFont('Arial','B',9);
				
				if($arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Elektromedik',$jmlBayarFisTxt,$jmlDiscFisTxt,$jmlAsuransiFisTxt));
				}	
				elseif(!$arrDisc && $arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Elektromedik',$jmlBayarFisTxt,$jmlAsuransiFisTxt));
				}	
				elseif($arrDisc && !$arrTanggungan)
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Tindakan Rawat Jalan',$jmlBayarTdkTxt,$jmlBebanAsuransiTxt));
				}	
				else
				{	
					$pdf->SetWidths(array(160,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Elektromedik',$jmlBayarFisTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}			
			
			//---------------------------- Transaksi Ambulan Rawat Jalan ----------------------------						
			$sql = "SELECT
						tbt_rwtjln_ambulan.id AS id,
						tbt_rwtjln_ambulan.tgl AS tgl,
						tbm_ambulan.nama AS tujuan,
						tbt_rwtjln_ambulan.disc,
						tbt_rwtjln_ambulan.tanggungan_asuransi,
						CONCAT(tbt_rwtjln_ambulan.catatan,'  ',tbt_rwtjln_ambulan.lainnya) AS catatan, ";
			
			if($cito=='0'){$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";}
			else{$sql .= "(2 * tbt_rwtjln_ambulan.tarif) AS tarif ";}
			
			$sql .= "FROM
						tbm_ambulan
						Inner Join tbt_rwtjln_ambulan ON tbt_rwtjln_ambulan.tujuan = tbm_ambulan.id
					WHERE 
						tbt_rwtjln_ambulan.no_trans_rwtjln = '$noTrans'
						AND tbt_rwtjln_ambulan.flag = '1' ";
						
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$sqlDisc = "SELECT tbt_rwtjln_ambulan.disc FROM
						tbm_ambulan
						Inner Join tbt_rwtjln_ambulan ON tbt_rwtjln_ambulan.tujuan = tbm_ambulan.id
					WHERE 
						tbt_rwtjln_ambulan.no_trans_rwtjln = '$noTrans'
						AND tbt_rwtjln_ambulan.flag = '1'
						AND tbt_rwtjln_ambulan.disc > 0";
				$arrDisc = $this->queryAction($sqlDisc,'S');
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Ambulan'));	
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					if($arrDisc)
						$pdf->SetWidths(array(10,90,30,30,30));
					else
						$pdf->SetWidths(array(10,90,60,30));
				}
				else
				{
					if($arrDisc)
						$pdf->SetWidths(array(10,120,30,30));
					else
						$pdf->SetWidths(array(10,150,30));	
				}
				
				$pdf->SetAligns(array('C','L','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
				
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarAmb += $row['tarif'];
					$jmlDiscAmb += $row['disc'];
					$jmlAsuransiAmb += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['tarif'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['tujuan'],$biaya,$disc,$bebanAsuransi));
						else	
							$pdf->Row(array($j.'.',$row['tujuan'],$biaya,$bebanAsuransi));
					}
					else
					{
						if($arrDisc)
							$pdf->Row(array($j.'.',$row['tujuan'],$biaya,$disc));
						else
							$pdf->Row(array($j.'.',$row['tujuan'],$biaya));	
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarAmbTxt = number_format($jmlBayarAmb,2,',','.');
				$jmlDiscAmbTxt = number_format($jmlDiscAmb,2,',','.');
				$jmlAsuransiAmbTxt = number_format($jmlAsuransiAmb,2,',','.');
				
				$grandTotal += $jmlBayarAmb;
				$grandDisc += $jmlDiscAmb;
				$grandBeban += $jmlAsuransiAmb;
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					if($arrDisc)
					{
						$pdf->SetWidths(array(100,30,30,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total Ambulan',$jmlBayarAmbTxt,$jmlDiscAmbTxt,$jmlAsuransiAmbTxt));
					}
					else
					{
						$pdf->SetWidths(array(100,60,30));
						$pdf->SetAligns(array('L','R','R','R'));
						$pdf->Row(array('Sub Total Ambulan',$jmlBayarAmbTxt,$jmlAsuransiAmbTxt));
					}
				}
				else
				{
					if($arrDisc)
					{
						$pdf->SetWidths(array(130,30,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total Ambulan',$jmlBayarAmbTxt,$jmlDiscAmbTxt));
					}
					else
					{
						$pdf->SetWidths(array(160,30));
						$pdf->SetAligns(array('L','R','R'));
						$pdf->Row(array('Sub Total Ambulan',$jmlBayarAmbTxt));
					}	
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi Apotik Rawat Jalan ----------------------------
			$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTrans)->penjamin;
			$stAsuransi = RwtjlnRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
					no_trans, 
					no_trans_rwtjln, 
					dokter, 
					no_reg,
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual_karyawan
				WHERE 
					cm='$cm' 
					AND no_trans_rwtjln='$noTrans'
					AND flag = '1'
				GROUP BY no_reg ";
			}
			else
			{
				$sql = "SELECT
					no_trans, 
					no_trans_rwtjln, 
					dokter, 
					no_reg,
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual 
				WHERE 
					cm='$cm' 
					AND no_trans_rwtjln='$noTrans'
					AND flag = '1'
				GROUP BY no_reg ";
			}
			
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Resep'));
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(10,35,35,80,30));
				$pdf->SetAligns(array('C','C','C','C','C'));
				$pdf->Row(array('No.','Tanggal','No. Resep','Dokter','Biaya'));
				
				$pdf->SetFont('Arial','',9);	
				$pdf->SetAligns(array('C','C','C','L','R'));
						
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;					
					
					$biaya = number_format($row['total'],2,',','.');
					
					$pdf->Row(array($j.'.',$this->convertDate($row['tgl'],'3'),$row['no_reg'],PegawaiRecord::finder()->findByPk($row['dokter'])->nama,$biaya));
					
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarApotik = number_format($jmlBayarApotik,2,'.','');
				$jmlBayarApotikTxt = number_format($jmlBayarApotik,2,',','.');
				
				$grandTotal += $jmlBayarApotik;
				
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(160,30));
				$pdf->SetAligns(array('L','R','R','R'));
				$pdf->Row(array('Sub Total Resep',$jmlBayarApotikTxt));
				
				//jika ada disc u/ obat2an
				if(RwtjlnRecord::finder()->findByPk($noTrans)->disc_obat > 0)
				{
					$jmlBayarApotik = $jmlBayarApotik - RwtjlnRecord::finder()->findByPk($noTrans)->disc_obat;
					$jmlDiscApotik =  RwtjlnRecord::finder()->findByPk($noTrans)->disc_obat;
					$jmlDiscApotikTxt =  number_format(RwtjlnRecord::finder()->findByPk($noTrans)->disc_obat,2,',','.');
					$pdf->Row(array('Discount Resep',$jmlDiscApotikTxt));
					
					$grandDisc += RwtjlnRecord::finder()->findByPk($noTrans)->disc_obat;
				}
				
				//jika ada tanggungan asuransi u/ obat2an
				if(RwtjlnRecord::finder()->findByPk($noTrans)->tanggungan_asuransi_obat > 0)
				{
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$jmlBayarApotik = $jmlBayarApotik - RwtjlnRecord::finder()->findByPk($noTrans)->tanggungan_asuransi_obat;
						$jmlAsuransiApotik =  RwtjlnRecord::finder()->findByPk($noTrans)->tanggungan_asuransi_obat;
						$jmlAsuransiApotikTxt =  number_format(RwtjlnRecord::finder()->findByPk($noTrans)->tanggungan_asuransi_obat,2,',','.');
						
						$grandBeban += RwtjlnRecord::finder()->findByPk($noTrans)->tanggungan_asuransi_obat;
						
						if($idPenjamin == '02')
						{
							/*$pdf->Row(array('Tanggungan Asuransi',$jmlAsuransiApotikTxt));
						}
						else
						{*/
							$pdf->Row(array('Tanggungan Penjamin',$jmlAsuransiApotikTxt));
						}
					}
				}
			}
			
			//$pdf->Cell(0,1,'','B',1,'C');
			//$pdf->Ln(1);
			/*
			$total = $jmlBayarTdk + $jmlBayarLab + $jmlBayarRad + $jmlBayarFis + $jmlBayarAmb + $jmlBayarApotik ;
			
			$pdf->Ln(5);
			
			$pdf->SetWidths(array(40,110,40));
			$pdf->SetAligns(array('L','R','R'));
			$pdf->Row(array('GRAND TOTAL',$sayTerbilang,$jmlTagihanTxt));
			*/
			
			$totalDisc = $jmlDiscTdk + $jmlDiscLab + $jmlDiscRad + $jmlDiscCtScan + $jmlDiscFis + $jmlDiscAmb + $jmlDiscApotik;
			$totalAsuransi = $jmlBebanAsuransiTdk + $jmlAsuransiLab + $jmlAsuransiRad + $jmlAsuransiCtScan + $jmlnAsuransiFis + $jmlAsuransiAmb + $jmlAsuransiApotik;
			$grandTotal = $jmlBayarTdk + $jmlBayarLab + $jmlBayarRad + $jmlBayarCtScan + $jmlBayarFis + $jmlBayarTAmb + $jmlBayarApotik;
			
			$tes = number_format($grandTotal,2,',','.');
			
			$pdf->Cell(0,2,'','B',1,'C');
			$pdf->SetWidths(array(160,30));
			//$pdf->Row(array('Jumlah Bayar',$jmlTagihanTxt));
			$pdf->Row(array('Total Biaya',number_format($grandTotal+$totalDisc+$totalAsuransi,2,',','.')));
			
			if($sisaBulat > 0)
				$pdf->Row(array('Pembulatan',number_format($sisaBulat,2,',','.')));
			
			if($totalDisc > 0)
				$pdf->Row(array('Total Discount',number_format($totalDisc,2,',','.')));
			
			if($totalAsuransi > 0)
				$pdf->Row(array('Total Tanggungan Asuransi',number_format($totalAsuransi,2,',','.')));
			
			$pdf->Ln(2);
			$pdf->Row(array('Jumlah Bayar',number_format($grandTotal+$sisaBulat,2,',','.')));
			
			$sayTerbilang = ucwords($this->terbilang($grandTotal+$sisaBulat) . ' rupiah');
			$pdf->SetFont('Arial','BI',9);
			$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');	
			$pdf->Ln(5);
			
			/*
			$pdf->Ln(5);
			$pdf->Cell(180,5,'Tindakan: '.$jmlBayarTdk.' - '.$jmlDiscTdk.' - '.$jmlBebanAsuransiTdk,0,0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'lab: '.$jmlBayarLab.' - '.$jmlDiscLab.' - '.$jmlAsuransiLab,0,0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'rad: '.$jmlBayarRad.' - '.$jmlDiscRad.' - '.$jmlAsuransiRad,0,0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'fisio: '.$jmlBayarFis.' - '.$jmlDiscFis.' - '.$jmlnAsuransiFis,0,0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'amb: '.$jmlBayarTAmb.' - '.$jmlDiscAmb.' - '.$jmlAsuransiAmb,0,0,'L');	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'apotik: '.$jmlBayarApotik.' - '.$jmlDiscApotik.' - '.$jmlAsuransiApotik,0,0,'L');	;	
			$pdf->Ln(5);
			$pdf->Cell(180,5,'apotik: '.$grandTotal.' - '.$totalDisc.' - '.$totalAsuransi,0,0,'L');	;	
			$pdf->Ln(5);
			//$pdf->Cell(90,5,'('.$sayTerbilang.') 			 '.'Rp. '.$total,0,0,'R');
			*/
			$pdf->Cell(0,0,'','B',1,'C');
			
			$pdf->SetFont('Arial','',8);		
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', Kota Tangerang Selatan, '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			/*
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(230,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(5);*/
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(70,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			
			if($stUlang == '1')
				$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DataPasDetail').'&cm='.$cm.'&tipeRawat='.$jnsPasien);	
			else
				$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');	
		}
		elseif($jnsPasien == '1') //pasien rawat inap 
		{
			$jmlKurangBayar = InapBayarTunai::finder()->find('no_trans_inap=? AND st_lunas_tunai=?',array($noTrans,'0'))->jml_kurang_bayar;
			
			//---------------------------- Transaksi Laboratorium Rawat Inap Tunai----------------------------						
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans AS no_trans_asal,
					  tbt_lab_penjualan_inap.id_tindakan,
					  tbt_lab_penjualan_inap.no_trans,
					  tbt_lab_penjualan_inap.no_reg,
					  tbt_lab_penjualan_inap.disc,
					  tbt_lab_penjualan_inap.tanggungan_asuransi,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			
			if($cito=='0'){$sql .= "tbt_lab_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan_inap.harga) AS harga ";}
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_lab_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_lab_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  no_trans_inap = '$noTrans'
					  AND flag = '1'
					  AND st_lunas_tunai = '0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				foreach($arrData as $row)
				{
					$noRegLab = $row['no_reg'];
				}
					
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Laboratorium'));
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
					$pdf->SetAligns(array('C','C','C','C','C','C'));
					
					if($idPenjamin == '02')	
					{	
						/*$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Asuransi'));
					}
					else
					{*/
						$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Penjamin'));
					}
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));
					$pdf->SetAligns(array('C','C','C','C','C'));
					$pdf->Row(array('No.','Nama Tindakan','Tanggungan Pasien','Discount'));
				}
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','R','R','R','R'));
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransTbl = $row['no_trans'];
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarLab += $row['harga'];
					$jmlDiscLab += $row['disc'];
					$jmlAsuransiLab += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc));
					}
					
					//update st_lunas_tunai=1
					$sql = "UPDATE tbt_lab_penjualan_inap SET st_lunas_tunai = '1' WHERE no_trans='$noTransTbl' ";		
					$this->queryAction($sql,'C');
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarLabTxt = number_format($jmlBayarLab,2,',','.');
				$jmlDiscLabTxt = number_format($jmlDiscLab,2,',','.');
				$jmlAsuransiLabTxt = number_format($jmlAsuransiLab,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Laboratorium',$jmlBayarLabTxt,$jmlDiscLabTxt,$jmlAsuransiLabTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Laboratorium',$jmlBayarLabTxt,$jmlDiscLabTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi Radiologi Rawat Inap Tunai ----------------------------
			
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans AS no_trans_asal,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan_inap.no_trans,
					  tbt_rad_penjualan_inap.film_size,
					  tbt_rad_penjualan_inap.disc,
					  tbt_rad_penjualan_inap.tanggungan_asuransi, ";
			
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
					  AND flag = '1'
					  AND st_lunas_tunai='0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Radiologi'));	
				
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(10,60,30,30,30,30));
					$pdf->SetAligns(array('C','L','C','R','R','R'));
				}
				else
				{
					$pdf->SetWidths(array(10,90,30,30,30));					
					$pdf->SetAligns(array('C','L','C','R','R'));
				}
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{					
					$noTransTbl = $row['no_trans'];
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarRad += $row['harga'];
					$jmlDiscRad += $row['disc'];
					$jmlAsuransiRad += $row['tanggungan_asuransi'];
					
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
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc));
					}
					
					//update st_lunas_tunai=1
					$sql = "UPDATE tbt_rad_penjualan_inap SET st_lunas_tunai = '1' WHERE no_trans='$noTransTbl' ";		
					$this->queryAction($sql,'C');
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarRadTxt = number_format($jmlBayarRad,2,',','.');
				$jmlDiscRadTxt = number_format($jmlDiscRad,2,',','.');
				$jmlAsuransiRadTxt = number_format($jmlAsuransiRad,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt,$jmlDiscRadTxt,$jmlAsuransiRadTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Radiologi',$jmlBayarRadTxt,$jmlDiscRadTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi CT Scan Rawat Inap Tunai ----------------------------
			
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans AS no_trans_asal,
					  tbm_ctscan_tindakan.nama AS nm_tdk,
					  tbm_ctscan_kelompok.nama AS kel_tdk,
					  tbm_ctscan_kategori.jenis AS kateg_tdk,  
					  tbt_ctscan_penjualan_inap.no_trans,
					  tbt_ctscan_penjualan_inap.film_size,
					  tbt_ctscan_penjualan_inap.disc,
					  tbt_ctscan_penjualan_inap.tanggungan_asuransi, ";
			
			if($cito=='0'){$sql .= "tbt_ctscan_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_ctscan_penjualan_inap.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_ctscan_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_ctscan_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_inap.id_tindakan = tbm_ctscan_tindakan.kode)
					  LEFT JOIN tbm_ctscan_kelompok ON (tbm_ctscan_tindakan.kelompok = tbm_ctscan_kelompok.kode)
					  LEFT JOIN tbm_ctscan_kategori ON (tbm_ctscan_tindakan.kategori = tbm_ctscan_kategori.kode)
					WHERE
					  no_trans_inap = '$noTrans'
					  AND flag = '1'
					  AND st_lunas_tunai='0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya CT Scan'));	
				
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(10,60,30,30,30,30));
					$pdf->SetAligns(array('C','L','C','R','R','R'));
				}
				else
				{
					$pdf->SetWidths(array(10,90,30,30,30));					
					$pdf->SetAligns(array('C','L','C','R','R'));
				}
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{					
					$noTransTbl = $row['no_trans'];
					$noTransAsal = $row['no_trans_asal'];
					$jmlBayarCtScan += $row['harga'];
					$jmlDiscCtScan += $row['disc'];
					$jmlAsuransiCtScan += $row['tanggungan_asuransi'];
					
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
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$ukuran,$biaya,$disc));
					}
					
					//update st_lunas_tunai=1
					$sql = "UPDATE tbt_ctscan_penjualan_inap SET st_lunas_tunai = '1' WHERE no_trans='$noTransTbl' ";		
					$this->queryAction($sql,'C');
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarCtScanTxt = number_format($jmlBayarCtScan,2,',','.');
				$jmlDiscCtScanTxt = number_format($jmlDiscCtScan,2,',','.');
				$jmlAsuransiCtScanTxt = number_format($jmlAsuransiCtScan,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt,$jmlAsuransiCtScanTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total CT Scan',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi Fisio Rawat Inap Tunai ----------------------------						
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans AS no_trans_asal,
					  tbt_fisio_penjualan_inap.no_trans,
					  tbt_fisio_penjualan_inap.id_tindakan,
					  tbt_fisio_penjualan_inap.disc,
					  tbt_fisio_penjualan_inap.tanggungan_asuransi,
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
					  AND flag = '1'
					  AND st_lunas_tunai='0'
					  AND st_bayar = '1' ";
					  
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi fisio rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Elektromedik'));	
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));					
				}
				
				$pdf->SetAligns(array('C','L','R','R','R'));
				
				$pdf->SetFont('Arial','',9);
								
				$j=0;
				foreach($arrData as $row)
				{
					$noTransAsal = $row['no_trans_asal'];
					$noTransTbl = $row['no_trans'];
					$jmlBayarFis += $row['harga'];
					$jmlDiscFis += $row['disc'];
					$jmlAsuransiFis += $row['tanggungan_asuransi'];					
					
					$j += 1;	
					
					$biaya = number_format($row['harga'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$row['nm_tdk'],$biaya,$disc));
					}
					
					//update st_lunas_tunai=1
					$sql = "UPDATE tbt_fisio_penjualan_inap SET st_lunas_tunai = '1' WHERE no_trans='$noTransTbl' ";		
					$this->queryAction($sql,'C');
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarFisTxt = number_format($jmlBayarFis,2,',','.');
				$jmlDiscFisTxt = number_format($jmlDiscFis,2,',','.');
				$jmlAsuransiFisTxt = number_format($jmlAsuransiFis,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02') && $stAsuransi == '1')	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Sub Total Elektromedik',$jmlBayarFisTxt,$jmlDiscFisTxt,$jmlAsuransiFisTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Sub Total Elektromedik',$jmlBayarFisTxt,$jmlDiscFisTxt));
				}
				
				$pdf->Cell(0,2,'','B',1,'C');
			}
			
			//---------------------------- Transaksi Apotik Rawat Inap Tunai ----------------------------
			$kelompokPasien = RwtInapRecord::finder()->findByPk($noTrans)->penjamin;
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTrans)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
					no_trans, 
					no_trans_inap, 
					dokter, 
					no_reg,
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual_inap_karyawan
				WHERE 
					cm='$cm' 
					AND no_trans_inap='$noTrans'
					AND flag = '1'
					AND st_lunas_tunai = '0'
					AND st_bayar = '1'
				GROUP BY no_reg ";
			}
			else
			{
				$sql = "SELECT
					no_trans, 
					no_reg,
					no_trans_inap, 
					dokter, 
					SUM(total + bhp) AS total,
					tgl
				FROM 
					tbt_obat_jual_inap
				WHERE 
					cm='$cm' 
					AND no_trans_inap='$noTrans'
					AND flag = '1'
					AND st_lunas_tunai = '0'
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
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Resep'));
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(10,35,35,80,30));
				$pdf->SetAligns(array('C','C','C','C','C'));
				$pdf->Row(array('No.','Tanggal','No. Resep','Dokter','Biaya'));
				
				$pdf->SetFont('Arial','',9);	
				$pdf->SetAligns(array('C','C','C','L','R'));
						
				$j=0;
				foreach($arrData as $row)
				{
					$noTransTbl = $row['no_trans'];
					$noRegObat = $row['no_reg'];
					
					$j += 1;					
					$biaya = number_format($row['total'],2,',','.');
					
					$pdf->Row(array($j.'.',$this->convertDate($row['tgl'],'3'),$row['no_reg'],PegawaiRecord::finder()->findByPk($row['dokter'])->nama,$biaya));
					
					if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
					{
						//update st_lunas_tunai=1
						$sql = "UPDATE tbt_obat_jual_inap_karyawan SET st_lunas_tunai = '1' WHERE no_reg='$noRegObat' ";		
					}
					else
					{
						//update st_lunas_tunai=1
						$sql = "UPDATE tbt_obat_jual_inap SET st_lunas_tunai = '1' WHERE no_reg='$noRegObat' ";
					}
					
					$this->queryAction($sql,'C');
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarApotikTxt = number_format($jmlBayarApotik,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(160,30));
				$pdf->SetAligns(array('L','R','R','R'));
				$pdf->Row(array('Sub Total Resep',$jmlBayarApotikTxt));
				
				$discApotik = InapBayarTunai::finder()->find('no_trans_inap=? AND no_reg_obat=? AND st_lunas_tunai=0',array($noTrans,$noRegObat))->disc_obat_tunai;
				$bebanAsuransiApotik = InapBayarTunai::finder()->find('no_trans_inap=? AND no_reg_obat=? AND st_lunas_tunai=0',array($noTrans,$noRegObat))->tanggungan_asuransi_obat_tunai;
				
				//jika ada disc u/ obat2an
				if($discApotik > 0)
				{
					$jmlBayarApotik = $jmlBayarApotik - $discApotik;
					$jmlDiscApotik =  number_format($discApotik,2,',','.');
					$pdf->Row(array('Discount Resep',$jmlDiscApotik));
					
					//update st_lunas_tunai=1
					$sql = "UPDATE tbt_inap_bayar_tunai SET st_lunas_tunai = '1' WHERE no_trans_inap='$noTrans' AND no_reg_obat='$noRegObat' ";		
					$this->queryAction($sql,'C');
				}
				
				//jika ada tanggungan asuransi u/ obat2an
				if($bebanAsuransiApotik > 0)
				{
					if(($idPenjamin == '02') && $stAsuransi == '1')	
					{
						$jmlBayarApotik = $jmlBayarApotik - $bebanAsuransiApotik;
						$jmlAsuransiApotik =  number_format($bebanAsuransiApotik,2,',','.');
						
						if($idPenjamin == '02')
						{
							/*$pdf->Row(array('Tanggungan Asuransi',$jmlAsuransiApotik));
						}
						else
						{*/
							$pdf->Row(array('Tanggungan Penjamin',$jmlAsuransiApotik));
						}
						
						//update st_lunas_tunai=1
						$sql = "UPDATE tbt_inap_bayar_tunai SET st_lunas_tunai = '1' WHERE no_trans_inap='$noTrans' AND no_reg_obat='$noRegObat' ";		
						$this->queryAction($sql,'C');
					}
				}
			}
			
			/*
			$total = $jmlBayarTdk + $jmlBayarLab + $jmlBayarRad + $jmlBayarFis + $jmlBayarAmb + $jmlBayarApotik ;
			
			$pdf->Ln(5);
			
			
			$pdf->SetWidths(array(40,110,40));
			$pdf->SetAligns(array('L','R','R'));
			$pdf->Row(array('GRAND TOTAL',$sayTerbilang,$jmlTagihanTxt));
			*/
			
			$pdf->Cell(0,2,'','B',1,'L');
			$pdf->SetWidths(array(160,30));
			$pdf->SetAligns(array('L','R'));
			$pdf->Row(array('TOTAL TRANSAKSI',$jmlTagihanTxt));
			$pdf->Cell(0,1,'','B',1,'C');
			$pdf->Row(array('JUMLAH BAYAR',number_format($jmlTagihan - $jmlKurangBayar,2,',','.')));
			
			
			//$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');					
			
			//jika ada disc u/ obat2an
			if($jmlKurangBayar > 0)
			{
				$pdf->Cell(0,1,'','B',1,'C');
				
				$pdf->Row(array('Kekurangan Pembayaran *)',number_format($jmlKurangBayar,2,',','.')));
				
				//update st_lunas_tunai=1
				$sql = "UPDATE tbt_inap_bayar_tunai SET st_lunas_tunai = '1' WHERE no_trans_inap='$noTrans' ";		
				$this->queryAction($sql,'C');
				
				$ketKurang = '*) Kekurangan pembayaran akan diakumulasikan pada saat perawatan pasien selesai.';
			}
			else
			{
				$ketKurang = '';
			}
				
			//$pdf->Cell(90,5,'('.$sayTerbilang.') 			 '.'Rp. '.$total,0,0,'R');
			
			$pdf->Cell(0,0,'','B',1,'C');
			
			$pdf->SetFont('Arial','',8);		
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', Kota Tangerang Selatan, '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			
			$pdf->Ln(3);
			
			$pdf->Cell(30,5,$ketKurang,0,0,'L');
			/*
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(230,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(5);*/
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(70,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			
			if($stUlang == '1')
				$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DataPasDetail').'&cm='.$cm.'&tipeRawat='.$jnsPasien);
			else
				$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');
		}
		//---------------------------- pasien bebas ----------------------------	
		elseif($jnsPasien == '2' || $jnsPasien == '3' )
		{
			if(PasienLuarRecord::finder()->findByPk($noTransPasLuar)->cm != '')
			{
				$cm = PasienLuarRecord::finder()->findByPk($noTransPasLuar)->cm;
				$idPenjamin = PasienRecord::finder()->findByPk($cm)->kelompok;
			}
			//---------------------------- Transaksi Laboratorium Rawat Jalan Lain----------------------------						
			$sql = "SELECT 
					  tbt_lab_penjualan_lain.no_trans,
					  tbt_lab_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_lab_penjualan_lain.tgl,
					  tbt_lab_penjualan_lain.disc,
					  tbt_lab_penjualan_lain.tanggungan_asuransi,
					  tbt_lab_penjualan_lain.harga AS total,
					  tbt_lab_penjualan_lain.id_tindakan,
					  tbm_lab_tindakan.nama
					FROM
					  tbt_lab_penjualan_lain
					  LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					WHERE 
					  tbt_lab_penjualan_lain.no_trans_pas_luar = '$noTransPasLuar'
					  AND tbt_lab_penjualan_lain.flag = '1' ";
					  
			/*$sql = "SELECT
					*
				FROM
					tbt_lab_penjualan_lain 
				WHERE 
					no_trans_rwtjln_lain = '$noTrans'
					AND flag = '1' ";
					*/
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Laboratorium'));
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
					$pdf->SetAligns(array('C','C','C','C','C','C'));
					
					if($idPenjamin == '02')	
					{	
						/*$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Asuransi'));
					}
					else
					{*/
						$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Penjamin'));
					}
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));
					$pdf->SetAligns(array('C','C','C','C','C'));
					$pdf->Row(array('No.','Nama Tindakan','Tanggungan Pasien','Discount'));
				}
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','R','R','R','R'));
				
				foreach($arrData as $row)
				{
					//$jmlBayarLab += $row['harga_non_adm'];
					//$jmlBayarAdmLab += $row['harga_adm'];
					$jmlBayarLab += $row['total'];
					$jmlDiscLab += $row['disc'];
					$jmlAsuransiLab += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['total'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($row['id_tindakan'] != 'PDT') 
					{
						$nm = $row['nama'];
					}
					else
					{
						$nm = 'Biaya Adm. Laboratorium';
					}
					
					
					if(($idPenjamin == '02'))	
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc));
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarLabTxt = number_format($jmlBayarLab,2,',','.');
				$jmlDiscLabTxt = number_format($jmlDiscLab,2,',','.');
				$jmlAsuransiLabTxt = number_format($jmlAsuransiLab,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarLabTxt,$jmlDiscLabTxt,$jmlAsuransiLabTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarLabTxt,$jmlDiscLabTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
				$pdf->SetWidths(array(160,30));
				$pdf->Row(array('Jumlah Bayar',$jmlBayarLabTxt));
			}
			
			//---------------------------- Transaksi Radiologi Rawat Jalan Lain ----------------------------
			$sql = "SELECT 
					  tbt_rad_penjualan_lain.no_trans,
					  tbt_rad_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_rad_penjualan_lain.tgl,
					  tbt_rad_penjualan_lain.disc,
					  tbt_rad_penjualan_lain.tgl,
					  tbt_rad_penjualan_lain.film_size,
					  tbt_rad_penjualan_lain.harga AS total,
					  tbt_rad_penjualan_lain.tanggungan_asuransi AS tanggungan_asuransi,
					  tbt_rad_penjualan_lain.id_tindakan,
					  tbm_rad_tindakan.nama
					FROM
					  tbt_rad_penjualan_lain
					  LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_lain.id_tindakan = tbm_rad_tindakan.kode)
					WHERE 
					  tbt_rad_penjualan_lain.no_trans_pas_luar = '$noTransPasLuar'
					  AND tbt_rad_penjualan_lain.flag = '1' ";
			/* $sql = "SELECT
					*
				FROM 
					tbt_rad_penjualan_lain 
				WHERE 
					no_trans_pas_luar = '$noTrans'
					AND flag = '1' ";
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Radiologi'));
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
					$pdf->SetAligns(array('C','C','C','C','C','C'));
					
					if($idPenjamin == '02')	
					{	
						/*$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Asuransi'));
					}
					else
					{*/
						$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Penjamin'));
					}
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));
					$pdf->SetAligns(array('C','C','C','C','C'));
					$pdf->Row(array('No.','Nama Tindakan','Tanggungan Pasien','Discount'));
				}
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','R','R','R','R'));
				
				foreach($arrData as $row)
				{
					//$jmlBayarRad += $row['harga_non_adm'];
					//$jmlBayarAdmRad += $row['harga_adm'];
					$jmlBayarRad += $row['total'];
					$jmlDiscRad += $row['disc'];
					$jmlAsuransiRad += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['total'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($row['id_tindakan'] != 'PDT') 
					{
						
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
						
						$nm = $row['nama'].' ('.$ukuran.')';
					}
					else
					{
						$nm = 'Biaya Adm. Radiologi';
					}
					
					
					if(($idPenjamin == '02'))	
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc));
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarRadTxt = number_format($jmlBayarRad,2,',','.');
				$jmlDiscRadTxt = number_format($jmlDiscRad,2,',','.');
				$jmlAsuransiRadTxt = number_format($jmlAsuransiRad,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarRadTxt,$jmlDiscRadTxt,$jmlAsuransiRadTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarRadTxt,$jmlDiscRadTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
				$pdf->SetWidths(array(160,30));
				$pdf->Row(array('Jumlah Bayar',$jmlBayarRadTxt));
			}
			
			//---------------------------- Transaksi CT Scan Rawat Jalan Lain ----------------------------
			$sql = "SELECT 
					  tbt_ctscan_penjualan_lain.no_trans,
					  tbt_ctscan_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_ctscan_penjualan_lain.tgl,
					  tbt_ctscan_penjualan_lain.disc,
					  tbt_ctscan_penjualan_lain.tgl,
					  tbt_ctscan_penjualan_lain.film_size,
					  tbt_ctscan_penjualan_lain.harga AS total,
					  tbt_ctscan_penjualan_lain.tanggungan_asuransi,
					  tbt_ctscan_penjualan_lain.id_tindakan,
					  tbm_ctscan_tindakan.nama
					FROM
					  tbt_ctscan_penjualan_lain
					  LEFT JOIN tbm_ctscan_tindakan ON (tbt_ctscan_penjualan_lain.id_tindakan = tbm_ctscan_tindakan.kode)
					WHERE 
					  tbt_ctscan_penjualan_lain.no_trans_pas_luar = '$noTransPasLuar'
					  AND tbt_ctscan_penjualan_lain.flag = '1' ";
			/* $sql = "SELECT
					*
				FROM 
					tbt_ctscan_penjualan_lain 
				WHERE 
					no_trans_pas_luar = '$noTrans'
					AND flag = '1' ";
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya CT Scan'));
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
					$pdf->SetAligns(array('C','C','C','C','C','C'));
					
					if($idPenjamin == '02')	
					{	
						/*$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Asuransi'));
					}
					else
					{*/
						$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Penjamin'));
					}
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));
					$pdf->SetAligns(array('C','C','C','C','C'));
					$pdf->Row(array('No.','Nama Tindakan','Tanggungan Pasien','Discount'));
				}
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','R','R','R','R'));
				
				foreach($arrData as $row)
				{
					//$jmlBayarCtScan += $row['harga_non_adm'];
					//$jmlBayarAdmCtScan += $row['harga_adm'];
					$jmlBayarCtScan += $row['total'];
					$jmlDiscCtScan += $row['disc'];
					$jmlAsuransiCtScan += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['total'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($row['id_tindakan'] != 'PDT') 
					{
						
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
						
						$nm = $row['nama'].' ('.$ukuran.')';
					}
					else
					{
						$nm = 'Biaya Adm. CT Scan';
					}
					
					
					if(($idPenjamin == '02'))	
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc));
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarCtScanTxt = number_format($jmlBayarCtScan,2,',','.');
				$jmlDiscCtScanTxt = number_format($jmlDiscCtScan,2,',','.');
				$jmlAsuransiCtScanTxt = number_format($jmlAsuransiCtScan,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt,$jmlAsuransiCtScanTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarCtScanTxt,$jmlDiscCtScanTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
				$pdf->SetWidths(array(160,30));
				$pdf->Row(array('Jumlah Bayar',$jmlBayarCtScanTxt));
			}
			
			//---------------------------- Transaksi Fisio Rawat Jalan Lain ----------------------------						
			$sql = "SELECT 
					  tbt_fisio_penjualan_lain.no_trans,
					  tbt_fisio_penjualan_lain.no_trans_rwtjln_lain,
					  tbt_fisio_penjualan_lain.tgl,
					  tbt_fisio_penjualan_lain.disc,
					  tbt_fisio_penjualan_lain.tanggungan_asuransi,
					  tbt_fisio_penjualan_lain.harga AS total,
					  tbt_fisio_penjualan_lain.id_tindakan,
					  tbm_fisio_tindakan.nama
					FROM
					  tbt_fisio_penjualan_lain
					  LEFT JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_lain.id_tindakan = tbm_fisio_tindakan.kode)
					WHERE 
					  tbt_fisio_penjualan_lain.no_trans_pas_luar = '$noTransPasLuar'
					  AND tbt_fisio_penjualan_lain.flag = '1' ";
					  
			/* $sql = "SELECT
					*
				FROM 
					tbt_fisio_penjualan_lain 
				WHERE 
					no_trans_pas_luar = '$noTrans'
					AND flag = '1' ";
			*/
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi lab rwtjln
			{
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Elektromedik'));
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(10,90,30,30,30));
					$pdf->SetAligns(array('C','C','C','C','C','C'));
					
					if($idPenjamin == '02')	
					{	
						/*$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Asuransi'));
					}
					else
					{*/
						$pdf->Row(array('No.','Nama Tindakan','Biaya','Discount','Tanggungan Penjamin'));
					}
				}
				else
				{
					$pdf->SetWidths(array(10,120,30,30));
					$pdf->SetAligns(array('C','C','C','C','C'));
					$pdf->Row(array('No.','Nama Tindakan','Tanggungan Pasien','Discount'));
				}
				
				$pdf->SetFont('Arial','',9);
				$pdf->SetAligns(array('C','L','R','R','R','R'));
				
				foreach($arrData as $row)
				{
					//$jmlBayarFisio += $row['harga_non_adm'];
					//$jmlBayarAdmFisio += $row['harga_adm'];
					$jmlBayarFisio += $row['total'];
					$jmlDiscFisio += $row['disc'];
					$jmlAsuransiFisio += $row['tanggungan_asuransi'];
					
					$j += 1;	
					
					$biaya = number_format($row['total'],2,',','.');
					$disc = number_format($row['disc'],2,',','.');
					$bebanAsuransi = number_format($row['tanggungan_asuransi'],2,',','.');
					
					if($row['id_tindakan'] != 'PDT') 
					{
						$nm = $row['nama'];
					}
					else
					{
						$nm = 'Biaya Adm. Elektromedik';
					}
					
					
					if(($idPenjamin == '02'))	
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc,$bebanAsuransi));
					}
					else
					{
						$pdf->Row(array($j.'.',$nm,$biaya,$disc));
					}
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarFisioTxt = number_format($jmlBayarFisio,2,',','.');
				$jmlDiscFisioTxt = number_format($jmlDiscFisio,2,',','.');
				$jmlAsuransiFisioTxt = number_format($jmlAsuransiFisio,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				if(($idPenjamin == '02'))	
				{
					$pdf->SetWidths(array(100,30,30,30));
					$pdf->SetAligns(array('L','R','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarFisioTxt,$jmlDiscFisioTxt,$jmlAsuransiFisioTxt));
				}
				else
				{
					$pdf->SetWidths(array(130,30,30));
					$pdf->SetAligns(array('L','R','R'));
					$pdf->Row(array('Total Transaksi',$jmlBayarFisioTxt,$jmlDiscFisioTxt));
				}
			
				$pdf->Cell(0,2,'','B',1,'C');
				$pdf->SetWidths(array(160,30));
				$pdf->Row(array('Jumlah Bayar',$jmlBayarFisioTxt));
			}
			
			
			//---------------------------- Transaksi Apotik ----------------------------
			$noTransPasLuar = $this->Request['noTransPasLuar'];		
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
				AND flag = '1'
			GROUP BY no_reg ";
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BI',9);
				
				$pdf->SetWidths(array(190));
				$pdf->SetAligns(array('L'));
				$pdf->Row(array('Biaya Resep'));
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(10,35,115,30));
				$pdf->SetAligns(array('C','C','C','C','C'));
				$pdf->Row(array('No.','Tanggal','No. Resep','Biaya'));
				
				$pdf->SetFont('Arial','',9);	
				$pdf->SetAligns(array('C','C','C','R','R'));
						
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;					
					
					$biaya = number_format($row['total'],2,',','.');
					
					$pdf->Row(array($j.'.',$this->convertDate($row['tgl'],'3'),$row['no_reg'],$biaya));
					
				}
				
				$pdf->Cell(0,0.5,'','B',1,'C');
				
				$jmlBayarApotikTxt = number_format($jmlBayarApotik,2,',','.');
				
				$pdf->SetFont('Arial','B',9);
				
				$pdf->SetWidths(array(150,40));
				$pdf->SetAligns(array('L','R','R','R'));
				$pdf->Row(array('Total Resep',$jmlBayarApotikTxt));
				
				//jika ada disc u/ obat2an
				if(PasienLuarRecord::finder()->findByPk($noTransPasLuar)->disc_obat > 0)
				{
					$jmlBayarApotik = $jmlBayarApotik - PasienLuarRecord::finder()->findByPk($noTransPasLuar)->disc_obat;
					$jmlDiscApotik =  number_format(PasienLuarRecord::finder()->findByPk($noTransPasLuar)->disc_obat,2,',','.');
					$pdf->Row(array('Discount Resep',$jmlDiscApotik));
				}
				
				//jika ada tanggungan asuransi u/ obat2an
				if(PasienLuarRecord::finder()->findByPk($noTransPasLuar)->tanggungan_asuransi_obat > 0)
				{
					if(($idPenjamin == '02'))	
					{
						$jmlBayarApotik = $jmlBayarApotik - PasienLuarRecord::finder()->findByPk($noTransPasLuar)->tanggungan_asuransi_obat;
						$jmlAsuransiApotik =  number_format(PasienLuarRecord::finder()->findByPk($noTransPasLuar)->tanggungan_asuransi_obat,2,',','.');
						
						if($idPenjamin == '02')
						{
							/*$pdf->Row(array('Tanggungan Asuransi',$jmlAsuransiApotik));
						}
						else
						{*/
							$pdf->Row(array('Tanggungan Penjamin',$jmlAsuransiApotik));
						}
					}
				}
				
				$pdf->Cell(0,2,'','B',1,'C');
				$pdf->Row(array('Jumlah Bayar',number_format($jmlBayarApotik,2,',','.')));
			}
			
			/*
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
		
			$pdf->SetFont('Arial','BI',9);
			$pdf->cell(10,5,'',0,0,'L');	
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
			*/
			
			$pdf->SetFont('Arial','BI',9);
			$pdf->Cell(180,5,'Terbilang : '.$sayTerbilang,0,0,'L');		
			$pdf->Ln(5);	
			
			/*
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'Jumlah Bayar','TB',0,'L');
			$pdf->Cell(90,5, $jmlBayar,'TB',0,'R');
			$pdf->Ln(5);
			
			$pdf->Cell(100,5,'Sisa / Kembali','TB',0,'L');
			$pdf->Cell(90,5, number_format($sisa,2,',','.'),'TB',0,'R');
			$pdf->Ln(5);
			*/
			
			$pdf->SetFont('Arial','',8);		
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', Kota Tangerang Selatan, '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			/*
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(230,5,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(5);*/
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(70,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			
			if($stUlang == '1')
				$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DataPasDetail').'&cm='.$cm.'&tipeRawat='.$jnsPasien);	
			else
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
