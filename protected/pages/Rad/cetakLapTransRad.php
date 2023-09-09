<?php
class cetakLapTransRad extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
							
		$jnsPasien=$this->Request['jnsPasien'];
		$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$kelompok=$this->Request['kelompok'];
		$perus=$this->Request['perus'];
		$operatorRad=$this->Request['operatorRad'];
		$periode=$this->Request['periode'];
		
		$pdf=new reportKwitansi('L','mm','legal');
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
		$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		
		$pdf->Cell(0,5,'LAPORAN TRANSAKSI RADIOLOGI','0',0,'C','',$this->Service->constructUrl('Rad.LapTrans'));	
		
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Cell(170,5,'','0',0,'L');
		
		$pdf->Cell(20,5,'Waktu Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,date('G:i').' WIB','0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(20,5,'Jenis Pasien','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		
		if($jnsPasien == '0')
		{			
			$pdf->Cell(80,5,'Rawat Jalan','0',0,'L');
		}
		elseif($jnsPasien == '1')
		{
			$pdf->Cell(80,5,'Rawat Inap','0',0,'L');
		}
		elseif($jnsPasien == '2')
		{
			$pdf->Cell(80,5,'Pasien Luar','0',0,'L');
		}
		else
		{
			$pdf->Cell(80,5,'Semua Pasien','0',0,'L');
		}
		
		if($kelompok)
		{			
			$kel = KelompokRecord::finder()->findByPk($kelompok)->nama;
			
			if($kelompok == '02' || $kelompok == '07')
			{
				if($perus)
				{
					$kel .= ' ('.PerusahaanRecord::finder()->findByPk($perus)->nama.')';
				}
			}
			
			$pdf->Cell(165,5,'','0',0,'L');
			$pdf->Cell(20,5,'Kelompok','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,$kel,'0',0,'L');
		}
		
		$pdf->Ln(5);
		
		if($poli)
		{			
			$pdf->Cell(20,5,'Poliklinik','0',0,'L');
			$pdf->Cell(5,5,':','0',0,'C');
			$pdf->Cell(80,5,PoliklinikRecord::finder()->findByPk($poli)->nama,'0',0,'L');
			
			if($dokter)
			{			
				$pdf->Cell(165,5,'','0',0,'L');
				$pdf->Cell(20,5,'Dokter','0',0,'L');
				$pdf->Cell(5,5,':','0',0,'C');
				$pdf->Cell(80,5,PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
			}
			
			$pdf->Ln(5);
		}
		else
		{
			if($dokter)
			{			
				$pdf->Cell(20,5,'Dokter','0',0,'L');
				$pdf->Cell(5,5,':','0',0,'C');
				$pdf->Cell(80,5,PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
			}
			
			$pdf->Ln(5);
		}
		
		
		
		$pdf->Ln(2);
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->SetWidths(array(15,20,30,20,50,30,30,25,34,27,27,27));
		$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C'));
		
		$col1 = 'No.';
		$col2 = 'Tanggal';
		$col3 = 'Operator';
		$col4 = 'No. RM';
		$col5 = 'Nama Pasien';
		$col6 = 'Umur';
		$col7 = 'Dokter';
		$col8 = 'Ruangan';
		$col9 = 'Pemeriksaan';
		$col10 = 'Rupiah';
		$col11 = 'Disc';
		$col12 = 'Tanggungan Asuransi';
		
		$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12));
		
		$pdf->Ln(1);
		
		$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','R','R','R'));
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapTransRad'];
		$sql .= " ORDER BY tgl ";
		
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			
			$no_reg = $row['no_reg'];
			$tipe_pasien = $row['tipe_pasien'];
			
			if($tipe_pasien == '0')
			{
				$umur = $this->cariUmur('0',$row['cm'],$row['id_poli']);
				$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
				/*
				$tblRadJual = 'tbt_rad_penjualan';
				
				$sql2 = "SELECT 
						GROUP_CONCAT(tbm_rad_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_rad_tindakan
					  LEFT JOIN $tblRadJual ON ( $tblRadJual.id_tindakan = tbm_rad_tindakan.kode ) 
					WHERE
						$tblRadJual.no_reg = '$no_reg' ";
			
				$arr2 = $this->queryAction($sql2,'S');
				foreach($arr2 as $row2)
				{		
					$nmTdk = $row2['nm_tdk'];
				}
				*/
			}
			elseif($tipe_pasien == '1')
			{
				$umur = $this->cariUmur('1',$row['cm'],'');
				$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
				/*
				$tblRadJual = 'tbt_rad_penjualan_inap';
				
				$sql2 = "SELECT 
						GROUP_CONCAT(tbm_rad_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_rad_tindakan
					  LEFT JOIN $tblRadJual ON ( $tblRadJual.id_tindakan = tbm_rad_tindakan.kode ) 
					WHERE
						$tblRadJual.no_reg = '$no_reg' ";
			
				$arr2 = $this->queryAction($sql2,'S');
				foreach($arr2 as $row2)
				{		
					$nmTdk = $row2['nm_tdk'];
				}
				*/
			}
			elseif($tipe_pasien == '2')
			{	
				$umur = PasienLuarRecord::finder()->findByPk($row['no_trans_asal'])->umur;
				/*
				$tblRadJual = 'tbt_rad_penjualan_lain';
				
				$sql2 = "SELECT 
						GROUP_CONCAT(tbm_rad_tindakan.nama SEPARATOR ', ') AS nm_tdk
					FROM
					  tbm_rad_tindakan
					  LEFT JOIN $tblRadJual ON ( $tblRadJual.id_tindakan = tbm_rad_tindakan.kode ) 
					WHERE
						$tblRadJual.no_reg = '$no_reg' ";
			
				$arr2 = $this->queryAction($sql2,'S');
				foreach($arr2 as $row2)
				{		
					$nmTdk = $row2['nm_tdk'];
				}
				*/
			}	
			
			$col1 = $j;
			$col2 = $this->convertDate($row['tgl'],'1');
			$col3 = $row['nm_operator'];
			$col4 = $row['cm'];
			$col5 = $row['nm_pas'];
			$col6 = $umur;
			$col7 = $row['nm_dokter'];
			$col8 = $row['nm_poli'];
			$col9 = $row['nm_tindakan'];
			$col10 = number_format($row['harga'],'2',',','.');
			$col11 = number_format($row['disc'],'2',',','.');
			$col12 = number_format($row['tanggungan_asuransi'],'2',',','.');
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10,$col11,$col12));		
				
			$totHarga += $row['harga'];
			$totDisc += $row['disc'];
			$totAsuransi += $row['tanggungan_asuransi'] ;
			
		}
		/*	
		if ($tableTmp)
		{			
			$sql = "DROP TABLE $tableTmp";
			$this->queryAction($sql,'C');//Create new tabel bro...
		}
		*/			
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		
		$pdf->SetFont('Arial','B',9);	
		
		$pdf->Cell(174,5,'GRAND TOTAL',0,0,'R');	
				
		$pdf->Cell(27,5,number_format($totHarga,2,',','.'),1,0,'R');		
		$pdf->Cell(27,5,number_format($totDisc,2,',','.'),1,0,'R');		
		$pdf->Cell(27,5,number_format( $totAsuransi,2,',','.'),1,0,'R');
							
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'Petugas Rad.,',0,0,'C');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Rad.LapTrans'));		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		//$pdf->MultiCell(53,8,$sql,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>