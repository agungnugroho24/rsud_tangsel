<?php
class cetakLapPenerimaanKasirRwtJln extends SimakConf
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
		//$nmTable=$this->Request['table'];
		$kasir=$this->Request['kasir'];
		$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$periode=$this->Request['periode'];
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakPenerimaanKasirRwtJln'];
					
		$pdf=new reportKeuangan('L','mm','legal');
	
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
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,7,'LAPORAN PENERIMAAN KASIR RAWAT JALAN','0',0,'C');
		//$pdf->Cell(0,7,$sql,'0',0,'C');
		$pdf->Ln(10);		
		$pdf->SetFont('Arial','',11);
		
		if($kasir)
		{
			$pdf->Cell(100,5,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,'0',0,'L');
		}
		
		if($dokter)
		{
			$pdf->Cell(100,5,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,'0',0,'L');
		}
		
		$pdf->Ln(5);	
		if($poli)
		{
			$pdf->Cell(100,5,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,'0',0,'L');
		}
				
		$pdf->Cell(100,5,$periode,'0',0,'L');
		
		
		$pdf->Ln(7);	
		
		//Line break
		$pdf->SetFont('Arial','B',10);		
		$pdf->Cell(35,5,'Tanggal',1,0,'C');
		$pdf->Cell(60,5,'Operator',1,0,'C');		
		$pdf->Cell(40,5,'Retribusi',1,0,'C');
		$pdf->Cell(40,5,'Administrasi',1,0,'C');
		$pdf->Cell(40,5,'Poliklinik',1,0,'C');	
		$pdf->Cell(40,5,'Obat',1,0,'C');	
		$pdf->Cell(40,5,'Laboratorium',1,0,'C');	
		$pdf->Cell(40,5,'Radologi',1,0,'C');	
		$pdf->Cell(40,5,'Fisio',1,0,'C');	
		
		//$pdf->Cell(40,5,'Total',1,0,'C');	
		$pdf->Ln(5);
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$no_trans = $row['no_trans'];
			$id_kasir = $row['kasir'];
			$jns_pasien = $row['jns_pasien'];
			
			$pdf->SetFont('Arial','',9);	
			//$pdf->Cell(30,5,'30 Desember 2009',1,0,'C');
			$pdf->Cell(35,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->Cell(60,5,UserRecord::finder()->find('nip=?',$id_kasir)->real_name,1,0,'C');
			
			if($jns_pasien == 'non otc') //pasen rwt jalan biasa
			{					
				//----------- RET ----------// NON OTC
				$sql = "select 
							sum(view_retribusi.tarif) AS tarif
						  from 
							view_retribusi 
						  where 
							view_retribusi.no_trans='$no_trans'  ";			
				$ret = KasirPendaftaranRecord::finder()->findBySql($sql)->tarif;
				$pdf->Cell(40,5,'Rp. '.number_format($ret,2,',','.'),1,0,'R');
				
				//----------- ADM ----------// NON OTC
				$sql = "select 
							sum(tbt_kasir_rwtjln.total) AS total
						  from 
							(tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) 
						  where 
							(tbm_nama_tindakan.nama like '%pendaftaran%') 
							AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'AND tbt_kasir_rwtjln.st_flag='1' ";				
				$adm = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
				$pdf->Cell(40,5,'Rp. '.number_format($adm,2,',','.'),1,0,'R');	
				
				//----------- POLI ----------// NON OTC
				$sql = "select 
						sum(tbt_kasir_rwtjln.total) AS total
					  from 
						((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
					  where 
						(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
						AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'AND tbt_kasir_rwtjln.st_flag='1' ";
				$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
				$pdf->Cell(40,5,'Rp. '.number_format($poli,2,',','.'),1,0,'R');	
				
				//----------- OBAT ----------// NON OTC
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalObat = ObatJualRecord::finder()->findBySql($sql)->total;
				
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_karyawan WHERE no_trans_rwtjln='$no_trans' AND flag='1'";
				$totalObat += ObatJualKaryawanRecord::finder()->findBySql($sql)->total;
				
				$pdf->Cell(40,5,'Rp. '.number_format($totalObat,2,',','.'),1,0,'R');	
				
				//----------- LAB ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_lab_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalLab = LabJualRecord::finder()->findBySql($sql)->harga;
							
				$pdf->Cell(40,5,'Rp. '.number_format($totalLab,2,',','.'),1,0,'R');	
				
				//----------- RAD ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_rad_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalRad = RadJualRecord::finder()->findBySql($sql)->harga;
							
				$pdf->Cell(40,5,'Rp. '.number_format($totalRad,2,',','.'),1,0,'R');	
				
				//----------- FISIO ----------// NON OTC
				$sql = "SELECT sum(harga) AS harga FROM tbt_fisio_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
				$totalFisio = FisioJualRecord::finder()->findBySql($sql)->harga;
							
				$pdf->Cell(40,5,'Rp. '.number_format($totalFisio,2,',','.'),1,0,'R');	
			}			
			elseif($jns_pasien == 'otc') //pasen rwt jalan bebas
			{
				//----------- OBAT ----------//  OTC
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalObat = ObatJualLainRecord::finder()->findBySql($sql)->total;
				
				$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain_karyawan WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalObat += ObatJualLainKaryawanRecord::finder()->findBySql($sql)->total;
				
				
				//----------- LAB ----------//  OTC
				$sql = "SELECT sum(harga) AS harga,sum(harga_adm) AS harga_adm FROM tbt_lab_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalLab = LabJualLainRecord::finder()->findBySql($sql)->harga;
				$admLab = LabJualLainRecord::finder()->findBySql($sql)->harga_adm;
							
				
				//----------- RAD ----------//  OTC
				$sql = "SELECT sum(harga) AS harga,sum(harga_adm) AS harga_adm FROM tbt_rad_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1'";
				$totalRad = RadJualLainRecord::finder()->findBySql($sql)->harga;
				$admRad = RadJualLainRecord::finder()->findBySql($sql)->harga_adm;
							
				
				//----------- FISIO ----------//  OTC
				$sql = "SELECT sum(harga) AS harga,sum(harga_adm) AS harga_adm FROM tbt_fisio_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
				$totalFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga;
				$admFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga_adm;
				
				//----------- ADM ----------//  OTC
				$adm = $admLab+$admRad+$admFisio;
				$pdf->Cell(40,5,'Rp. '.number_format($adm,2,',','.'),1,0,'R');
				
				//----------- POLI ----------// NON OTC
				$poli = 0;
				$pdf->Cell(40,5,'Rp. '.number_format($poli,2,',','.'),1,0,'R');
				
				$pdf->Cell(40,5,'Rp. '.number_format($totalObat,2,',','.'),1,0,'R');
				$pdf->Cell(40,5,'Rp. '.number_format($totalLab,2,',','.'),1,0,'R');
				$pdf->Cell(40,5,'Rp. '.number_format($totalRad,2,',','.'),1,0,'R');
				$pdf->Cell(40,5,'Rp. '.number_format($totalFisio,2,',','.'),1,0,'R');
			}
			
			$grandTotRet += $ret;
			$grandTotAdm += $adm;
			$grandTotPoli += $poli;
			$grandTotObat += $totalObat;
			$grandTotLab += $totalLab;
			$grandTotRad += $totalRad;
			$grandTotFiso += $totalFisio;
			
			
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(95,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotRet,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotAdm,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotPoli,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotObat,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotLab,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotRad,2,',','.'),1,0,'R');
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotFiso,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
				
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(550,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(550,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(550,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtJln'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(550,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
}
?>