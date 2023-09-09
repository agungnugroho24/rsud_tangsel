<?php
class cetakKwtRwtJlnDiscountFormBaru extends SimakConf
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
		$pdf->AddPage();
		/*
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
		$pdf->Cell(0,3,'','B',1,'C');
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',6);
		*/
		
		$pdf->Cell(0,3,$txtJudul,'0',0,'C');
		
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(0,3,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','',7);
		$pdf->Cell(20,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$noTrans,0,0,'L');
		
		$pdf->Cell(20,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$sayTerbilang,0,0,'L');
		
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			$pdf->Cell(10,10,'No. CM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(60,10,$cm,0,0,'L');
		}
				
		$pdf->Ln(3);		
		$pdf->Cell(20,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		//$pdf->Cell(105,10,$nmPasien .' / '. $pjPasien,0,0,'L');
		$pdf->Cell(35,10,$nmPasien ,0,0,'L');
		
		if($jnsPasien == '0') //jika pasien rawat jln
		{
			$pdf->Cell(10,10,'Poliklinik',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(60,10,$nmKlinik,0,0,'L');
		}
		/*
		//$pdf->Ln(3);	
				
		$pdf->Cell(20,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(105,10,$sayTerbilang,0,0,'L');
		
		/*
		$pdf->Cell(10,10,'Petugas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(60,10,ucwords(UserRecord::finder()->find('nip = ?',$petugasPoli)->real_name),0,0,'L');					
		*/
		//$pdf->Ln(3);
		
		$pdf->Cell(20,10,'Dokter',0,0,'L');
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
		$tglCetak = $this->convertDate(date('Y-m-d'),'3');
		$wktCetak = date('G:i:s');

		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat", "Sabtu","Minggu");
		$hari = $array_hari[date("N")];

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
		
		
		//$pdf=new reportKwitansi();
		//$pdf=new reportKwitansi('P','mm','a4');
		$pdf=new reportKwitansi('P','mm','kwt_baru');
		$pdf->SetLeftMargin(5);
		$pdf->SetTopMargin(35);
		$pdf->SetRightMargin(5);
		$pdf->AliasNbPages();
		
		//$this->headerKwt($pdf);
		
				
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
		{
			$besarDisc = $this->Request['besarDisc'];
			$stDisc = $this->Request['stDisc'];
			$totBiayaDisc = $this->Request['totBiayaDisc'];
			$totBiayaDiscBulat = $this->Request['totBiayaDiscBulat'];
			$sisaDiscBulat = $this->Request['sisaDiscBulat'];
		
			$pdf->AddPage();	
			//$pdf->Image('protected/pages/Tarif/logo1.jpg',35,8,15);				
			$pdf->SetFont('Arial','B',6);
			//$pdf->Cell(10,10,'','0',0,'C');
			/*
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
			$pdf->Cell(0,3,'','B',1,'C');
			$pdf->Ln(1);
			*/
			$pdf->SetFont('Arial','',5);
			$pdf->Cell(60,3,'Dicetak oleh : '.$operator.', '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			$pdf->Ln(7);		
			
			$pdf->SetFont('Arial','BU',6);
			
			$pdf->Cell(0,3,'KUITANSI PEMBAYARAN RAWAT JALAN','0',0,'C');				
			
			$pdf->Ln(3);		
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(0,3,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
			$pdf->Ln(3);		
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(20,10,'No. Register',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(35,10,$noTrans,0,0,'L');
			
			if($jnsPasien == '0') //jika pasien rawat jln
			{
				$pdf->Cell(10,10,'Poliklinik',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(60,10,$nmKlinik,0,0,'L');
			}
			
			$pdf->Ln(3);
			if($jnsPasien == '0') //jika bukan pasien luar/rujukan
			{
				$pdf->Cell(20,10,'No. CM',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(35,10,$cm,0,0,'L');
			}
			
			$pdf->Cell(10,10,'Dokter',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			if($jnsPasien != '1') //jika bukan pasien luar/rujukan
			{
				$pdf->Cell(60,10,$nmDokter,0,0,'L');
			}
			elseif($jnsPasien == '1')
			{
				if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
				{	
					$pdf->Cell(60,10,LabJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
				}
				elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
				{
					$pdf->Cell(60,10,RadJualLainRecord::finder()->find('no_trans = ?',$noTrans)->dokter,0,0,'L');
				}
				
			}
					
			$pdf->Ln(3);		
			$pdf->Cell(20,10,'Telah Terima dari',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			//$pdf->Cell(105,10,$nmPasien .' / '. $pjPasien,0,0,'L');
			$pdf->Cell(35,10,$nmPasien ,0,0,'L');
			
			$pdf->Ln(7);	
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Keterangan Transaksi','TB',0,'L');
			$pdf->Cell(60,3,'Biaya Rp.','TB',0,'R');
			$pdf->Ln(6);
			
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
		
				$pdf->SetFont('Arial','BIU',6);
				$pdf->Cell(190,3,'Tindakan Rawat Jalan',0,0,'L');				
				$pdf->Ln(3);
				
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,3,'No.',0,0,'C');
				$pdf->Cell(60,3,'Nama Tindakan',0,0,'L');
				$pdf->Cell(60,3,'Dokter',0,0,'L');
				$pdf->Cell(60,3,'Biaya',0,0,'R');	
				$pdf->Ln(3);
			
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',7);						
					$pdf->Cell(10,3,$j.'.',0,0,'C');
					$pdf->Cell(60,3,$row['nama'],0,0,'L');
					
					if(substr($row['nama'],0,11) == "Jasa Dokter")
					{
						$pdf->Cell(60,3,$nmDokter,0,0,'L');
					}
					else
					{
						$pdf->Cell(60,3,'',0,0,'L');
					}
					
					$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),0,0,'R');	
					$pdf->Ln(3);					
				}
				
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(80,3,'Sub Total ',0,0,'R');		
				$pdf->Cell(30,3,'Rp. ' . number_format($jmlBayarTdk,2,',','.'),'T',0,'R');
				$pdf->Ln(3);	
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
		
				$pdf->SetFont('Arial','BIU',6);
				$pdf->Cell(190,3,'Biaya Laboratorium',0,0,'L');				
				$pdf->Ln(3);
				
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',7);						
					$pdf->Cell(10,3,$j.'.',0,0,'C');
					$pdf->Cell(40,3,$row['nama'],0,0,'L');
					$pdf->Cell(60,3,'Rp. ' . number_format($row['total'],2,',','.'),0,0,'R');	
					$pdf->Ln(3);
					
				}
				
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(80,3,'Sub Total ',0,0,'R');		
				$pdf->Cell(30,3,'Rp. ' . number_format($jmlBayarLab,2,',','.'),'T',0,'R');
				$pdf->Ln(3);
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
				
				$pdf->SetFont('Arial','BIU',6);
				$pdf->Cell(190,3,'Biaya Radiologi',0,0,'L');				
				$pdf->Ln(3);
				
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',7);						
					$pdf->Cell(10,3,$j.'.',0,0,'C');
					$pdf->Cell(40,3,$row['nama'],0,0,'L');
					$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),0,0,'R');	
					$pdf->Ln(3);
					
				}
			
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(80,3,'Sub Total ',0,0,'R');		
				$pdf->Cell(30,3,'Rp. ' . number_format($jmlBayarRad,2,',','.'),'T',0,'R');
				$pdf->Ln(3);
			}
			
			//---------------------------- Transaksi Apotik Rawat Jalan ----------------------------
			
			$sql = "SELECT
					no_trans, 
					no_trans_rwtjln, 
					dokter, 
					SUM(total) AS total,
					tgl
				FROM 
					tbt_obat_jual 
				WHERE 
					cm='$cm' 
					AND no_trans_rwtjln='$noTrans'
					AND flag = '1'
				GROUP BY no_trans ";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				foreach($arrData as $row)
				{
					$jmlBayarApotik += $row['total'];
				}
				
				$pdf->SetFont('Arial','BIU',6);
				$pdf->Cell(190,3,'Biaya Resep',0,0,'L');				
				$pdf->Ln(3);
				
				$pdf->SetFont('Arial','',7);						
				$pdf->Cell(10,3,'No.',0,0,'C');
				$pdf->Cell(25,3,'Tanggal',0,0,'C');
				$pdf->Cell(35,3,'No. Resep',0,0,'C');
				$pdf->Cell(60,3,'Dokter',0,0,'L');
				$pdf->Cell(60,3,'Biaya',0,0,'R');	
				$pdf->Ln(3);
					
				$j=0;
				foreach($arrData as $row)
				{
					$j += 1;
					$pdf->SetFont('Arial','',7);						
					$pdf->Cell(10,3,$j.'.',0,0,'C');
					$pdf->Cell(25,3,$this->convertDate($row['tgl'],'3'),0,0,'C');
					$pdf->Cell(35,3,$row['no_trans_rwtjln'],0,0,'C');
					$pdf->Cell(60,3,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,0,0,'L');
					$pdf->Cell(60,3,'Rp. ' . number_format($row['total'],2,',','.'),0,0,'R');	
					$pdf->Ln(3);
					
				}
			
				$pdf->SetFont('Arial','B',7);
				$pdf->Cell(80,3,'Sub Total ',0,0,'R');		
				$pdf->Cell(30,3,'Rp. ' . number_format($jmlBayarApotik,2,',','.'),'T',0,'R');
				$pdf->Ln(3);
			}
			
			$pdf->Cell(0,2,'','B',1,'C');
			$pdf->Ln(1);
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Total Transaksi',0,0,'L');
			
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,3,'Rp. ' . $jmlTagihan,0,0,'R');
			$pdf->Ln(3);
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Discount',0,0,'L');
			
			$pdf->SetFont('Arial','B',7);
			if($besarDisc != '' && $besarDisc != '0' && TPropertyValue::ensureFloat($besarDisc))
			{
				$pdf->Cell(60,3,$besarDisc.' %',0,0,'R');
			}
			else
			{
				$pdf->Cell(60,3,'0 %',0,0,'R');
			}		
			
			$pdf->Ln(3);
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Total Setelah Discount',0,0,'L');
			
			$pdf->SetFont('Arial','B',7);
			if(($totBiayaDiscBulat != ''))
			{
				$pdf->Cell(60,3,'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
			}
			else
			{
				//$pdf->Cell(60,3,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
				$pdf->Cell(60,3,'Rp. '.$jmlTagihan,0,0,'R');
			}	
			
			$pdf->Ln(3);
		
			$pdf->SetFont('Arial','BI',6);
			$pdf->cell(10,3,'',0,0,'L');	
			
			$pdf->SetFont('Arial','B',6);
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(180,3,'Terbilang : '.$sayTerbilang,0,0,'L');		
			$pdf->Ln(7);	
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Jumlah Bayar','TB',0,'L');
			
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,3,'Rp. ' . $jmlBayar,'TB',0,'R');
			$pdf->Ln(3);
			
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'Sisa / Kembali','TB',0,'L');
			
			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(60,3,'Rp. ' . number_format($sisa,2,',','.'),'TB',0,'R');
			$pdf->Ln(7);
			
			$pdf->SetFont('Arial','',6);		
			$pdf->Cell(50,3,'',0,0,'L');
			$pdf->Cell(60,3,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
			
			$pdf->Ln(3);
			$pdf->Cell(50,3,'',0,0,'L');
			$pdf->Cell(60,3,'K a s i r',0,0,'C');
						
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',6);
			$pdf->Cell(50,3,'',0,0,'L');
			$pdf->Cell(60,3,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));	
				
			$pdf->Ln(3);
			$pdf->SetFont('Arial','',7);
			$pdf->Cell(50,3,'',0,0,'L');
			$pdf->Cell(60,3,'NIP. '.$nip,0,0,'C');	
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
					
					$pdf->SetFont('Arial','B',6);
					$pdf->Cell(10,3,'No.',1,0,'C');
					$pdf->Cell(40,3,'Tindakan',1,0,'C');
					$pdf->Cell(60,3,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
				
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',7);						
						$pdf->Cell(10,3,$j.'.',1,0,'C');
						
						if($row['nama'] == '')
						{
							if($row['total'] == '2000')
							{
								$pdf->Cell(40,3,'Rujukan',1,0,'L');
							}
							elseif($row['total'] == '2500')
							{
								$pdf->Cell(40,3,'Pendaftaran',1,0,'L');
							}
						}
						else
						{
							$pdf->Cell(40,3,$row['nama'],1,0,'L');
						}
						
						$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(3);
						
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
					
					$pdf->SetFont('Arial','B',6);
					$pdf->Cell(10,3,'No.',1,0,'C');
					$pdf->Cell(40,3,'Tindakan',1,0,'C');
					$pdf->Cell(60,3,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',7);						
						$pdf->Cell(10,3,$j.'.',1,0,'C');
						
						if($row['nama'] == '')
						{
							if($row['total'] == '2000')
							{
								$pdf->Cell(40,3,'Rujukan',1,0,'L');
							}
							elseif($row['total'] == '2500')
							{
								$pdf->Cell(40,3,'Pendaftaran',1,0,'L');
							}
						}
						else
						{
							$pdf->Cell(40,3,$row['nama'],1,0,'L');
						}
						
						$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(3);
						
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
					
					$pdf->SetFont('Arial','B',6);
					$pdf->Cell(10,3,'No.',1,0,'C');
					$pdf->Cell(40,3,'Tindakan',1,0,'C');
					$pdf->Cell(60,3,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',7);						
						$pdf->Cell(10,3,$j.'.',1,0,'C');
						$pdf->Cell(40,3,$row['nama'],1,0,'L');
						$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(3);
						
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
					
					$pdf->SetFont('Arial','B',6);
					$pdf->Cell(10,3,'No.',1,0,'C');
					$pdf->Cell(40,3,'Tindakan',1,0,'C');
					$pdf->Cell(60,3,'Jumlah',1,0,'C');		
					//Line break
					$pdf->Ln(6);
					
					$j=0;
					foreach($arrData as $row)
					{
						$j += 1;
						$pdf->SetFont('Arial','',7);						
						$pdf->Cell(10,3,$j.'.',1,0,'C');
						$pdf->Cell(40,3,$row['nama'],1,0,'L');
						$pdf->Cell(60,3,'Rp. ' . number_format($this->bulatkan($row['total']),2,',','.'),1,0,'R');	
						$pdf->Ln(3);
						
					}
				}
			}	
		}	
				
		
		if($jnsPasien != '0') //jika bukan pasien luar/rujukan
		{
			$pdf->SetFont('Arial','',7);		
			$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(80,3,'Total ',0,0,'R');		
			$pdf->Cell(60,3,'Rp. ' . $jmlTagihan,1,0,'R');
			$pdf->Ln(3);
			$pdf->SetFont('Arial','',7);		
			$pdf->Cell(80,8,'                       K a s i r,',0,0,'L');				
			$pdf->SetFont('Arial','',7);						
			$pdf->Cell(80,3,'Bayar ',0,0,'R');
			$pdf->Cell(60,3,'Rp. ' . $jmlBayar,1,0,'R');	
			$pdf->Ln(3);			
			$pdf->SetFont('Arial','',7);				
			$pdf->Cell(10,3,'',0,0,'C');
			$pdf->Cell(40,3,'Kembalian ',0,0,'R');
			$pdf->Cell(60,3,'Rp. ' . $sisaBayar,1,0,'R');		
			$pdf->Ln(3);
			$pdf->SetFont('Arial','BU',9);			
			$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));		
			$pdf->Ln(3);
			$pdf->SetFont('Arial','',7);	
			$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');		
		}
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>
