<?php
class cetakHonorDokter extends SimakConf
{
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$spesialis=$this->Request['spesialis'];
		$dokter=$this->Request['dokter'];
		//$kelompok=$this->Request['kelompok'];
		//$perusahaan=$this->Request['perusahaan'];
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['cariBln'];
		$thn=$this->Request['cariThn'];
		$periode=$this->Request['periode'];
		
		$subTot1=$this->Request['subTot1'];
		$subTot2=$this->Request['subTot2'];
		$total=$this->Request['total'];
		$pajak=$this->Request['pajak'];
		$potongan=$this->Request['potongan'];
		$grandTot=$this->Request['grandTot'];
			
		$pdf=new reportHonor();
		$pdf->AliasNbPages(); 
		$pdf->AddPage('P');
		
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
		$pdf->Cell(0,7,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'HONOR DOKTER','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		$pdf->Cell(35,5,'Nama Dokter ','0',0,'L');
		$pdf->Cell(2,5,': ','0',0,'L');
		$pdf->Cell(100,5,PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Periode ','0',0,'L');
		$pdf->Cell(2,5,': ','0',0,'L');
		$pdf->Cell(100,5,substr($periode,10,strlen($periode)),'0',0,'L');
		
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(35,10,'Ringkasan Jasa Dokter ','0',0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Sub Total 1 (Rawat Inap)',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($subTot1,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Sub Total 2 (Rawat Jalan)',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($subTot2,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Total',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($total,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Pajak ( 7,5 %)',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($pajak,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Potongan Rumah Sakit ( 5 % )',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($potongan,2,',','.'),0,0,'R');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(5,10,'- ',0,0,'L');
		$pdf->Cell(60,10,'Grand Total',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(3,10,'Rp.  ',0,0,'L');
		$pdf->Cell(30,10,number_format($grandTot,2,',','.'),0,0,'R');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(30,10,'Terbilang : ',0,0,'L');
		$pdf->Ln(10);
		
		$pdf->MultiCell(0,10,ucwords($this->terbilang($grandTot)),1,'L');
		$pdf->Ln(5);
		
		//------------------------------ TTD KASIR KUITANSI --------------------------------- //		
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.honorDokter') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		$pdf->Ln(5);
		
		
		$sql = "SELECT 
					  view_lap_jasmed_dokter.no_trans,
					  view_lap_jasmed_dokter.nama,
					  view_lap_jasmed_dokter.tindakan,
					  view_lap_jasmed_dokter.cm,
					  view_lap_jasmed_dokter.kelompok,
					  view_lap_jasmed_dokter.jasmed_dok,
					  view_lap_jasmed_dokter.total,
					  view_lap_jasmed_dokter.operator,
					  view_lap_jasmed_dokter.waktu,
					  view_lap_jasmed_dokter.tgl,
					  view_lap_jasmed_dokter.id_tindakan,
					  view_lap_jasmed_dokter.dokter,
					  view_lap_jasmed_dokter.klinik,
					  view_lap_jasmed_dokter.no_trans_asal,
					  view_lap_jasmed_dokter.perusahaan,
					  view_lap_jasmed_dokter.tgl
					FROM
					  view_lap_jasmed_dokter ";
					  
			  
			$perus=PasienRecord::finder()->find('cm = ?','view_lap_jasmed_dokter.cm')->perusahaan;
			if($perusahaan)
			{
				$sql .= "INNER JOIN tbm_perusahaan ON (view_lap_jasmed_dokter.perusahaan = tbm_perusahaan.id)
						WHERE view_lap_jasmed_dokter.nama <> '' ";	
			}
			else
			{
				$sql .= "WHERE view_lap_jasmed_dokter.nama <> '' ";	
			}
			
			if($spesialis <> '')			
				$sql .= "AND view_lap_jasmed_dokter.klinik = '$spesialis' ";	
			
			if($dokter <> '')			
				$sql .= "AND view_lap_jasmed_dokter.dokter = '$dokter' ";
			
			if($kelompok <> '')			
				$sql .= "AND view_lap_jasmed_dokter.kelompok = '$kelompok' ";
			
			if($perusahaan <> '')			
				$sql .= "AND view_lap_jasmed_dokter.perusahaan = '$perusahaan' ";		
			
			if($tgl <> '')			
				$sql .= "AND view_lap_jasmed_dokter.tgl = '$tgl' ";
			
			if($tglawal <> '' AND $tglakhir <> '')			
				$sql .= "AND view_lap_jasmed_dokter.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
			
			if($bln <> '' AND $thn <> '')			
				$sql .= "AND MONTH (view_lap_jasmed_dokter.tgl)='$bln' AND YEAR(view_lap_jasmed_dokter.tgl)='$thn' ";
			
			
			
			
				
		$pdf->AliasNbPages(); 
		$pdf->AddPage('L');
		$pdf->SetMargins(40,30,40);
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
		$pdf->Cell(0,7,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'Perincian Jasa Medik Pasien Rawat Jalan / Rawat Inap','0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		/*
		$pdf->Cell(92,5,'',1,0,'C');
		$pdf->Cell(92,5,'',1,0,'C');		
		$pdf->Cell(92,5,'',1,0,'C');	
		$pdf->Ln(5);
		$pdf->Cell(92,5,'',1,0,'C');
		$pdf->Cell(92,5,'',1,0,'C');		
		$pdf->Cell(92,5,'',1,0,'C');
		
		if($nip)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($nip)->real_name,'0',0,'L');
		}
		*/
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		if($kelompok)
		{
			//$pdf->Cell(100,5,'Kelompok Pasien : '.KelompokRecord::finder()->findByPk($kelompok)->nama,'0',0,'L');
		}
		
		$pdf->Ln(5);	
		if($spesialis)
		{
			//$pdf->Cell(100,5,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($spesialis)->nama,'0',0,'L');
		}
		
		if($perusahaan)
		{
			//$pdf->Cell(100,5,'Perusahaan : '.PerusahaanRecord::finder()->findByPk($perusahaan)->nama,'0',0,'L');
		}
				
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(10);	
		
		$pdf->SetFont('Arial','B',11);	
		$pdf->Cell(5,5,'A. ','0',0,'L');
		$pdf->Cell(100,5,'Jasa Medik Rawat Jalan','0',0,'L');
		$pdf->Ln(7);
		//Line break
		
		if($subTot2!=0)
		{
			$pdf->Cell(40,5,'Tanggal',1,0,'C');
			$pdf->Cell(40,5,'No Transaksi',1,0,'C');
			$pdf->Cell(25,5,'CM',1,0,'C');		
			$pdf->Cell(60,5,'Nama',1,0,'C');	
			$pdf->Cell(80,5,'Tindakan',1,0,'C');	
			//$pdf->Cell(35,5,'Tarif',1,0,'C');
			$pdf->Cell(35,5,'Jasmed',1,0,'C');
			//$pdf->Cell(25,5,'Jaspel',1,0,'C');	
			$pdf->Ln(5);
						
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...			
			foreach($arrData as $row)
			{
				
				$pdf->SetFont('Arial','',11);	
				//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
				
				$pdf->Cell(40,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->Cell(40,5,$row['no_trans'],1,0,'C');
				$pdf->Cell(25,5,$row['cm'],1,0,'C');		
				$pdf->Cell(60,5,$row['nama'],1,0,'C');
				
				if($row['tindakan']=='1')
				{
					$pdf->Cell(80,5,'Spesialis',1,0,'C');
				}
				elseif($row['tindakan']=='2')
				{
					$pdf->Cell(80,5,'Umum',1,0,'C');
				}
				elseif($row['tindakan']=='3')
				{
					$pdf->Cell(80,5,'Kir',1,0,'C');
				}
				else
				{
					$pdf->Cell(80,5,$row['tindakan'],1,0,'C');
				}
				
				//$pdf->Cell(35,5,number_format($row['total'],2,',','.'),1,0,'R');
				$pdf->Cell(35,5,number_format($row['jasmed_dok'],2,',','.'),1,0,'R');
				//$pdf->Cell(25,5,number_format($row['jaspel'],2,',','.'),1,0,'R');	
				$tot_tarif += $row['total'];
				$tot_jasmed_dok += $row['jasmed_dok'];
				//$tot_jaspelkesra += $row['jaspel'];
				//$totProfit += $profit;
				$pdf->Ln(5);
			}				
			
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(245,5,'GRAND TOTAL',1,0,'C');		
			//$pdf->Cell(35,5,number_format($tot_tarif,2,',','.'),1,0,'R');
			$pdf->Cell(35,5,number_format($tot_jasmed_dok,2,',','.'),1,0,'R');
			//$pdf->Cell(25,5,number_format($tot_jaspelkesra,2,',','.'),1,0,'R');	
			//$pdf->Cell(25,5,number_format($totProfit,2,',','.'),1,0,'R');	

		}
		else
		{
			$pdf->Cell(280,5,'TIDAK ADA TRANSAKSI',1,0,'C');
		}
		$pdf->Ln(10);
		
		switch ($spesialis) {
			case '02': //spesialis kandungan
				$sqlTarifDktr="SELECT *,tarif_obgyn AS tarif FROM view_inap_operasi_billing WHERE dktr_obgyn='$dokter' ";
				
				$sqlTarifAssDktr="SELECT *,tarif_assobgyn AS tarif FROM view_inap_operasi_billing WHERE ass_obgyn='$dokter '";
				break;

			case '13': //spesialis anestesi
				$sqlTarifDktr="SELECT *,tarif_anastesi AS tarif FROM view_inap_operasi_billing WHERE dktr_anastesi='$dokter' ";
				
				$sqlTarifAssDktr="SELECT *,tarif_assanastesi AS tarif FROM view_inap_operasi_billing WHERE ass_anastesi='$dokter' ";
				break;

			default:
				$sqlTarifDktr="SELECT *,tarif_dktr AS tarif FROM view_inap_operasi_billing WHERE dktr_utama='$dokter' ";
				
				$sqlTarifAssDktr="SELECT *,tarif_assdktr AS tarif FROM view_inap_operasi_billing WHERE ass_dktr='$dokter' ";
		}
		
		if($tgl <> '')	
		{		
			$sqlTarifDktr .= "AND view_inap_operasi_billing.tgl = '$tgl' ";
			$sqlTarifAssDktr .= "AND view_inap_operasi_billing.tgl = '$tgl' ";
		}
		
		if($tglawal <> '' AND $tglakhir <> '')			
		{
			$sqlTarifDktr .= "AND view_inap_operasi_billing.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
			$sqlTarifAssDktr .= "AND view_inap_operasi_billing.tgl BETWEEN '$tglawal' AND '$tglakhir' ";
		}
		
		if($bln <> '' AND $thn <> '')			
		{
			$sqlTarifDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
			$sqlTarifAssDktr .= "AND MONTH (view_inap_operasi_billing.tgl)='$bln' AND YEAR(view_inap_operasi_billing.tgl)='$thn' ";
		}
		
					
		$pdf->SetFont('Arial','B',11);	
		$pdf->Cell(5,5,'B. ','0',0,'L');
		$pdf->Cell(100,5,'Jasa Medik Rawat Inap','0',0,'L');
		$pdf->Ln(7);
		//Line break
		
		if($subTot1!=0)
		{
						$pdf->Cell(40,5,'Tanggal',1,0,'C');
			$pdf->Cell(40,5,'No Transaksi',1,0,'C');
			$pdf->Cell(25,5,'CM',1,0,'C');		
			$pdf->Cell(60,5,'Nama',1,0,'C');	
			$pdf->Cell(80,5,'Tindakan',1,0,'C');	
			//$pdf->Cell(35,5,'Tarif',1,0,'C');
			$pdf->Cell(35,5,'Jasmed',1,0,'C');
			//$pdf->Cell(25,5,'Jaspel',1,0,'C');	
			$pdf->Ln(5);
			
			$arrTarifDktr=$this->queryAction($sqlTarifDktr,'R');//Select row in tabel bro...			
			foreach($arrTarifDktr as $row)
			{
				
				$pdf->SetFont('Arial','',11);	
				//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
				
				$pdf->Cell(40,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->Cell(40,5,$row['no_trans'],1,0,'C');
				$pdf->Cell(25,5,$row['cm'],1,0,'C');		
				$pdf->Cell(60,5,PasienRecord::finder()->findByPk($row['cm'])->nama,1,0,'C');
				$pdf->Cell(80,5,$row['nm_operasi'],1,0,'C');
				
				//$pdf->Cell(35,5,number_format($row['total'],2,',','.'),1,0,'R');
				$pdf->Cell(35,5,number_format($row['tarif'],2,',','.'),1,0,'R');
				//$pdf->Cell(25,5,number_format($row['jaspel'],2,',','.'),1,0,'R');	
				$totDktr += $row['tarif'];
				
				$pdf->Ln(5);
			}		
			
			$arrTarifAssDktr=$this->queryAction($sqlTarifAssDktr,'R');//Select row in tabel bro...			
			foreach($arrTarifAssDktr as $row)
			{
				
				$pdf->SetFont('Arial','',11);	
				//$profit=($row['jumlah']*$row['hrg'])-($row['jumlah']*$row['hrg_ppn']);
				
				$pdf->Cell(40,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->Cell(40,5,$row['no_trans'],1,0,'C');
				$pdf->Cell(25,5,$row['cm'],1,0,'C');		
				$pdf->Cell(60,5,PasienRecord::finder()->findByPk($row['cm'])->nama,1,0,'C');
				$pdf->Cell(80,5,$row['nm_operasi'],1,0,'C');
				
				//$pdf->Cell(35,5,number_format($row['total'],2,',','.'),1,0,'R');
				$pdf->Cell(35,5,number_format($row['tarif'],2,',','.'),1,0,'R');
				//$pdf->Cell(25,5,number_format($row['jaspel'],2,',','.'),1,0,'R');	
				$totAssDktr += $row['tarif'];
				
				$pdf->Ln(5);
			}		
			
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(245,5,'GRAND TOTAL',1,0,'C');
			$pdf->Cell(35,5,number_format($totDktr+$totAssDktr,2,',','.'),1,0,'R');

		}
		else
		{
			$pdf->Cell(280,5,'TIDAK ADA TRANSAKSI',1,0,'C');
		}
		
		$pdf->Ln(10);
		
		
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(450,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(100,8,'Tanda Tangan Penerima,',0,0,'C');	
		$pdf->Cell(250,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(100,8,PegawaiRecord::finder()->findByPk($dokter)->nama,0,0,'C');	
		$pdf->Cell(250,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.honorDokter'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(100,8,'NIP. '.PegawaiRecord::finder()->findByPk($dokter)->nip,0,0,'C');	
		$pdf->Cell(250,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>