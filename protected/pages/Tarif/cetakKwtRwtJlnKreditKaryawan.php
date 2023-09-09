<?php
class cetakKwtRwtJlnKreditKaryawan extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }		 
	 
	public function onLoad($param)
	{	
		//$tipe=$this->Request['tipe'];	
		$noTrans=$this->Request['notrans'];		
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		$jmlTagihanTxt=number_format($this->Request['jmlTagihan'],2,',','.');
		$jmlTagihan=$this->Request['jmlTagihan'];
		
		$nmPasien=$this->Request['nama'];
		
		$cm=$this->Request['cm'];
		$bln=$this->Request['bln'];
		$thn=$this->Request['thn'];
		
		$jmlBayar=number_format($this->Request['jmlBayar'],2,',','.');
		$sisaBayar=number_format($this->Request['sisaByr'],2,',','.');
		$sisa=$this->Request['sisaByr'];
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$tglCetak = $this->convertDate(date('Y-m-d'),'3');
		$wktCetak = date('G:i:s');
		
		
		$array_hari = array(1=>"Senin","Selasa","Rabu","Kamis","Jumat", "Sabtu","Minggu");
		$hari = $array_hari[date("N")];
		
		//$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
		
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
		$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(4);
		$pdf->Cell(0,4,'           Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(0);
		$pdf->Cell(0,5,'','B',1,'C');
		//$pdf->Ln(1);	
		
		$pdf->SetFont('Arial','BU',10);
		
		$pdf->Cell(0,5,'KUITANSI PEMBAYARAN KREDIT RAWAT JALAN','0',0,'C');	
		
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		//$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		//$pdf->Ln(5);		
		
		$pdf->SetFont('Arial','',9);
		//$pdf->Cell(22,3,'No. Register',0,0,'L');
		//$pdf->Cell(2,3,':  ',0,0,'L');
		//$pdf->Cell(40,3,$noTrans,0,0,'L');
		
		$pdf->Cell(22,3,'No. CM',0,0,'L');
		$pdf->Cell(2,3,':  ',0,0,'L');
		$pdf->Cell(40,3,$cm,0,0,'L');
		$pdf->Ln(5);
				
		$pdf->Cell(21,3,'Terima dari',0,0,'L');
		$pdf->Cell(2,3,':  ',0,0,'L');
		//$pdf->Cell(70,3,$nmPasien.' / '.ucwords($pjPasien) ,0,0,'L');
		$pdf->Cell(70,3,$nmPasien,0,0,'L');
		
		$pdf->Ln(1);	
		
		$pdf->Cell(0,5,'','B',1,'C');
		
		$sql = "SELECT *
				FROM
				  tbt_rawat_jalan
				WHERE
				  tbt_rawat_jalan.cm = '$cm'
				  AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' 
				  AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
				  AND tbt_rawat_jalan.st_kredit = '2'";
		
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) > 0)
		{
			//---------------------------- Transaksi Tindakan Rawat Jalan ----------------------------			
			$sql = "select 
				tbm_nama_tindakan.nama AS nama,
				tbm_poliklinik.nama AS nama_klinik,
				tbt_kasir_rwtjln.tgl AS tgl,
				tbt_kasir_rwtjln.total AS total,
				tbt_kasir_rwtjln.st_flag AS flag,
				tbt_rawat_jalan.cm AS cm,
				tbt_rawat_jalan.no_trans AS no_trans,
				tbt_kasir_rwtjln.no_trans AS no_trans_asal,
				tbt_kasir_rwtjln.disc AS disc,
				tbt_kasir_rwtjln.id_tindakan AS id_tindakan 
			  from 
			  	tbt_kasir_rwtjln 
				INNER JOIN tbm_nama_tindakan on (tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id)
				INNER JOIN tbm_poliklinik on (tbt_kasir_rwtjln.klinik = tbm_poliklinik.id) 
				INNER JOIN tbt_rawat_jalan on (tbt_kasir_rwtjln.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
			  where
				tbt_rawat_jalan.cm = '$cm'
				AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
				AND tbt_rawat_jalan.st_kredit = '2'
				AND tbt_rawat_jalan.st_alih = 0";
				
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$pdf->SetFont('Arial','BIU',8);					
				$pdf->Cell(60,5,'Tindakan Rawat Jalan',0,0,'L');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(30,5,'Tanggal',0,0,'L');
				$pdf->Cell(60,5,'Nama Tindakan',0,0,'L');
				$pdf->Cell(60,5,'Jumlah',0,0,'R');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(25,5,' ',0,0,'R');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','',9);	
				
				$j=0;
				$jml=count($arrData);
				foreach($arrData as $row)
				{
					$j += 1;				
												
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(30,5,$this->convertDate($row['tgl'],'3'),0,0,'L');
					$pdf->Cell(60,5,$row['nama'],0,0,'L');
					
					$pdf->Cell(60,5,number_format($row['total'],2,',','.'),0,0,'R');	
					$pdf->Ln(5);			
					
					$jmlBayarTdk += $row['total'];		
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,0,' ',0,0,'R');
				$pdf->Cell(30,0,'Rp. ' . number_format($jmlBayarTdk,2,',','.'),0,0,'R');
				$pdf->Ln(0);	
			}
			
			//---------------------------- Transaksi Laboratorium Rawat Jalan ----------------------------						
			$sql = "SELECT 
				  tbt_lab_penjualan.id,
				  tbt_lab_penjualan.no_trans AS no_reg,
				  tbt_lab_penjualan.tgl,
				  tbt_lab_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '2'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_lab_penjualan.id";
				
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$pdf->Ln(1);
				$pdf->Cell(0,5,'','B',1,'C');
		
				$pdf->SetFont('Arial','BIU',8);					
				$pdf->Cell(60,5,'Transaksi Laboratorium',0,0,'L');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(30,5,'Tanggal',0,0,'L');
				$pdf->Cell(60,5,'No. Register',0,0,'L');
				$pdf->Cell(60,5,'Jumlah',0,0,'R');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(25,5,' ',0,0,'R');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','',9);	
				
				$j=0;
				$jml=count($arrData);
				foreach($arrData as $row)
				{
					$j += 1;			
												
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(30,5,$this->convertDate($row['tgl'],'3'),0,0,'L');
					$pdf->Cell(60,5,$row['no_reg'],0,0,'L');
					
					$pdf->Cell(60,5,number_format($row['harga'],2,',','.'),0,0,'R');	
					$pdf->Ln(5);			
					
					$jmlBayarLab += $row['harga'];		
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,0,' ',0,0,'R');
				$pdf->Cell(30,0,'Rp. ' . number_format($jmlBayarLab,2,',','.'),0,0,'R');
				$pdf->Ln(0);	
			}
			
			//---------------------------- Transaksi Radiologi Rawat Jalan ----------------------------
			$sql = "SELECT 
				  tbt_rad_penjualan.id,
				  tbt_rad_penjualan.no_trans AS no_reg,
				  tbt_rad_penjualan.tgl,
				  tbt_rad_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '2'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_rad_penjualan.id";
				
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$pdf->Ln(1);
				$pdf->Cell(0,5,'','B',1,'C');
		
				$pdf->SetFont('Arial','BIU',8);						
				$pdf->Cell(60,5,'Transaksi Radiologi',0,0,'L');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(30,5,'Tanggal',0,0,'L');
				$pdf->Cell(60,5,'No. Register',0,0,'L');
				$pdf->Cell(60,5,'Jumlah',0,0,'R');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(25,5,' ',0,0,'R');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','',9);	
				
				$j=0;
				$jml=count($arrData);
				foreach($arrData as $row)
				{
					$j += 1;			
												
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(30,5,$this->convertDate($row['tgl'],'3'),0,0,'L');
					$pdf->Cell(60,5,$row['no_reg'],0,0,'L');
					
					$pdf->Cell(60,5,number_format($row['harga'],2,',','.'),0,0,'R');	
					$pdf->Ln(5);			
					
					$jmlBayarRad += $row['harga'];		
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,0,' ',0,0,'R');
				$pdf->Cell(30,0,'Rp. ' . number_format($jmlBayarRad,2,',','.'),0,0,'R');
				$pdf->Ln(0);	
			}
			
			//---------------------------- Transaksi Fisio Rawat Jalan ----------------------------						
			$sql = "SELECT 
				  tbt_fisio_penjualan.id,
				  tbt_fisio_penjualan.no_trans AS no_reg,
				  tbt_fisio_penjualan.tgl,
				  tbt_fisio_penjualan.harga
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '2'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY
				  tbt_fisio_penjualan.id";
				
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi tindakan rwtjln
			{
				$pdf->Ln(1);
				$pdf->Cell(0,5,'','B',1,'C');
		
				$pdf->SetFont('Arial','BIU',8);						
				$pdf->Cell(60,5,'Transaksi Fisio',0,0,'L');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','B',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(30,5,'Tanggal',0,0,'L');
				$pdf->Cell(60,5,'No. Register',0,0,'L');
				$pdf->Cell(60,5,'Jumlah',0,0,'R');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(25,5,' ',0,0,'R');	
				$pdf->Ln(5);
				
				$pdf->SetFont('Arial','',9);	
				
				$j=0;
				$jml=count($arrData);
				foreach($arrData as $row)
				{
					$j += 1;				
												
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(30,5,$this->convertDate($row['tgl'],'3'),0,0,'L');
					$pdf->Cell(60,5,$row['no_reg'],0,0,'L');
					
					$pdf->Cell(60,5,number_format($row['harga'],2,',','.'),0,0,'R');	
					$pdf->Ln(5);			
					
					$jmlBayarFisio += $row['harga'];		
				}
				
				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(160,0,' ',0,0,'R');
				$pdf->Cell(30,0,'Rp. ' . number_format($jmlBayarFisio,2,',','.'),0,0,'R');
				$pdf->Ln(0);	
			}
			
			//---------------------------- Transaksi Apotik Rawat Jalan ----------------------------
			$sql = "SELECT 
					tbt_obat_jual_karyawan.id, 
					tbt_obat_jual_karyawan.id_obat, 
					tbt_obat_jual_karyawan.no_trans,
					tbt_obat_jual_karyawan.dokter,  
					tbt_obat_jual_karyawan.jumlah, 
					tbt_obat_jual_karyawan.total, 
					tbt_obat_jual_karyawan.st_racik, 
					tbt_obat_jual_karyawan.tgl, 
					tbt_obat_jual_karyawan.id_kel_racik, 
					tbt_obat_jual_karyawan.st_imunisasi, 
					tbt_obat_jual_karyawan.id_kel_imunisasi
				FROM
				  tbt_rawat_jalan
				  INNER JOIN tbt_obat_jual_karyawan ON (tbt_rawat_jalan.no_trans = tbt_obat_jual_karyawan.no_trans_rwtjln)
				WHERE
				  	tbt_rawat_jalan.cm = '$cm'
					AND MONTH (tbt_rawat_jalan.tgl_kasir)='$bln' AND YEAR(tbt_rawat_jalan.tgl_kasir)='$thn' 
					AND tbt_rawat_jalan.st_kredit = '2'
					AND tbt_rawat_jalan.st_alih = 0	
				GROUP BY				  
				  tbt_obat_jual_karyawan.no_trans";	
			
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...		
			if($arrData) //jika ada transaksi apotik rwtjln
			{
				$pdf->Ln(1);
				$pdf->Cell(0,5,'','B',1,'C');
				
				$pdf->SetFont('Arial','BIU',8);
				$pdf->Cell(190,5,'Biaya Resep',0,0,'L');				
				$pdf->Ln(4);
				
				$pdf->SetFont('Arial','B',9);						
				$pdf->Cell(10,5,'No.',0,0,'C');
				$pdf->Cell(25,5,'Tanggal',0,0,'L');
				$pdf->Cell(35,5,'No. Resep',0,0,'C');
				$pdf->Cell(50,5,'Dokter',0,0,'L');
				//$pdf->Cell(30,5,'Biaya',0,0,'R');	
				$pdf->Cell(20,5,' ',0,0,'R');	
				$pdf->Ln(5);
					
				$j=0;
				foreach($arrData as $row)
				{
					$no_trans = $row['no_trans'];
					
					$sql2 = "SELECT  SUM(total+bhp) AS total
							FROM
							  tbt_obat_jual_karyawan
							WHERE
								no_trans = '$no_trans'";	
					
					$arrData2=$this->queryAction($sql2,'S');//Select row in tabel bro...	
					foreach($arrData2 as $row2)
					{
						$jml = $row2['total'];	
					}
					
					$j += 1;
					$pdf->SetFont('Arial','',9);						
					$pdf->Cell(10,5,$j.'.',0,0,'C');
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),0,0,'C');
					$pdf->Cell(35,5,$row['no_trans'],0,0,'C');
					$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dokter'])->nama,0,0,'L');
					$pdf->Cell(35,5,number_format($jml,2,',','.'),0,0,'R');	
					$pdf->Ln(5);
					
					$jmlBayarApotik += $jml;
				}
			
				$pdf->SetFont('Arial','B',9);
				//$pdf->Cell(160,5,'Sub Total ',0,0,'R');		
				$pdf->Cell(160,0,' ',0,0,'R');		
				//$pdf->Cell(30,5,'Rp. ' . number_format($jmlBayarApotik,2,',','.'),'T',0,'R');
				$pdf->Cell(30,0,'Rp. ' . number_format($jmlBayarApotik,2,',','.'),0,0,'R');
				$pdf->Ln(0);
			}
			
			$pdf->Cell(0,2,'','B',1,'C');
			//$pdf->Ln(1);
			
			$total = $jmlTagihan + $jmlDisc;
			$totalTxt=number_format($total,2,',','.');
			$jmlDiscTxt=number_format($jmlDisc,2,',','.');
			
			/*
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
			
			if($stDisc == '1')
			{
				$sayTerbilang=ucwords($this->terbilang($totBiayaDiscBulat) . ' rupiah');
			}
			*/
			
			//$pdf->Cell(100,5,'Total Setelah Discount',0,0,'L');
			$pdf->Cell(100,5,'Total Transaksi',0,0,'L');
			if(($totBiayaDiscBulat != ''))
			{
				$pdf->Cell(90,5,'('.$sayTerbilang.') 			'.'Rp. '.number_format($totBiayaDiscBulat,2,',','.'),0,0,'R');
			}
			else
			{
				//$pdf->Cell(90,5,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
				$pdf->Cell(90,5,'('.$sayTerbilang.') 			 '.'Rp. '.$jmlTagihanTxt,0,0,'R');
			}	
			
			$pdf->Ln(5);
			$pdf->Cell(0,0,'','B',1,'C');
			
			$pdf->SetFont('Arial','',7);		
			$pdf->Cell(30,5,'Dicetak oleh : '.$operator.', Kota Tangerang Selatan, '.$hari.', '.$tglCetak.', '.$wktCetak,0,0,'L');
			
			$pdf->SetFont('Arial','',9);		
			$pdf->Cell(70,5,'',0,0,'L');
			$pdf->Cell(90,5,'K a s i r',0,0,'C');
						
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtJlnKaryawan'));	
				
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(100,5,'',0,0,'L');
			$pdf->Cell(90,5,'NIP. '.$nip,0,0,'C');	
		}
		
		$pdf->Output();
	}
	
}
?>
