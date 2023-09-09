<?php
class cetakExpired extends SimakConf
{
	public function onLoad($param)
	{		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$tujuan=$this->Request['tujuan'];
		
		//$periode = 'Periode : '.$this->ConvertDate($tglAwal,'3').' s.d. '.$this->ConvertDate($tglAkhir,'3');
		$dateNow = date('Y-m-d');
		if($tujuan != '')
		{
			$nmTujuan = DesFarmasiRecord::finder()->findByPk($tujuan)->nama;
		}
		else
		{
			$nmTujuan = '-';
		}
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		//$pdf=new reportKwitansi('P','mm','a4');
		$pdf=new reportAdmBarang('L');
		
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
		$pdf->Cell(0,5,'RINCIAN OBAT/ALKES YANG AKAN KADALUARSA 3 BULAN KEDEPAN','0',0,'C');
		
		$pdf->SetFont('Arial','',9);
		$pdf->Ln(5);
		$pdf->Cell(100,5,$periode,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(20,5,'Tanggal Cetak','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,$this->convertDate(date('Y-m-d'),'3'),'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(20,5,'Stok di','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(50,5,$nmTujuan,'0',0,'L');
		
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(17,5,'Kode Obat',1,0,'C');
		$pdf->Cell(40,5,'Nama Obat',1,0,'C');
		$pdf->Cell(35,5,'Lokasi',1,0,'C');
		$pdf->Cell(25,5,'No Batch',1,0,'C');
		$pdf->Cell(15,5,'Jumlah',1,0,'C');
		$pdf->Cell(30,5,'Exp.',1,0,'C');
		$pdf->Cell(30,5,'No. Faktur',1,0,'C');
		$pdf->Cell(30,5,'Tgl. Faktur',1,0,'C');
		$pdf->Cell(45,5,'PBF',1,0,'C');
		
		$pdf->Ln(6);
		$sql = "SELECT 
					  tbt_obat_harga.kode,
					  tbt_stok_lain.tujuan,
					  tbm_destinasi_farmasi.nama AS nm_tujuan,
					  tbt_stok_lain.jumlah,
					  tbt_obat_masuk.tgl_exp AS tgl,
					  tbt_obat_masuk.no_po,
					  tbt_obat_masuk.no_batch,
					  tbt_obat_masuk.no_faktur,
					  tbt_obat_masuk.tgl_faktur AS tgl_faktur,
					  (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) AS bln,
					  tbm_obat.nama
					FROM
					  tbt_obat_harga
					  INNER JOIN tbt_stok_lain ON (tbt_obat_harga.id = tbt_stok_lain.id_harga)
					  INNER JOIN tbm_destinasi_farmasi ON (tbt_stok_lain.tujuan = tbm_destinasi_farmasi.id)
					  INNER JOIN tbt_obat_masuk ON (tbt_obat_harga.tgl = tbt_obat_masuk.tgl_terima)
					  AND (tbt_obat_harga.kode = tbt_obat_masuk.id_obat)
					  INNER JOIN tbm_obat ON (tbt_obat_harga.kode = tbm_obat.kode)
					WHERE
					  tbt_obat_masuk.tgl_exp <> 0000-00-00
					  AND tbt_stok_lain.jumlah > 0
					  AND tbt_obat_masuk.tgl_exp > '$dateNow'
					  AND (YEAR(tbt_obat_masuk.tgl_exp) - YEAR(CURDATE())) * 12 + (MONTH(tbt_obat_masuk.tgl_exp) - MONTH(CURDATE())) - IF(DAYOFMONTH(tbt_obat_masuk.tgl_exp) < DAYOFMONTH(CURDATE()), 1, 0) <= 3 ";
			
		if($tujuan<>'')
		{
			$sql .= " AND tbt_stok_lain.tujuan = '$tujuan' ";
		}								
		
		$pdf->SetWidths(array(10,17,40,35,25,15,30,30,30,45));
		$pdf->SetAligns(array('C','C','L','L','C','R','C','C','C','L'));
			
		//$pdf->MultiCell(53,8,$sql,0,0,'C');			
		//$pdf->Ln(5);	   
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$totalJual = $row['jual']*$row['jumlah'];
			
			$j += 1;
			$pdf->SetFont('Arial','',8);
			
			$noPO = $row['no_po'];
			$kode = $row['kode'];
			
			$sql = "SELECT pbf FROM tbt_obat_beli WHERE no_po = '$noPO' AND kode = '$kode'";
			$idPbf = ObatBeliRecord::finder()->findBySql($sql)->pbf;
			$nmPbf = PbfObatRecord::finder()->findByPk($idPbf)->nama;
			
			$col1 = $j;
			$col2 = $row['kode'];
			$col3 = $row['nama'];
			$col4 = $row['nm_tujuan'];
			$col5 = $row['no_batch'];
			$col6 = number_format($row['jumlah'],'0',',','.');
			$col7 = $this->convertDate($row['tgl'],'3');
			$col8 = $row['no_faktur'];
			$col9 = $this->convertDate($row['tgl_faktur'],'3');
			$col10 = $nmPbf;
			
			$pdf->Row(array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col10));
			//$pdf->Ln(5);
		}
						
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);			
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.Expired'));		
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