<?php
class cetakKwtRwtInapPreviewNoKop extends SimakConf
{
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		$notrans=$this->Request['notrans'];
		
		$tglKeluar=$this->convertDate($this->Request['tglKeluar'],'3');
		$wktKeluar=$this->Request['wktKeluar'];
		$lamaInap=$this->Request['lamaInap'];
				
		$jsRS=$this->Request['jsRS'];	
		$jsAhli=$this->Request['jsAhli'];	
		$jsPenunjang=$this->Request['jsPenunjang'];	
		$jsObat=$this->Request['jsObat'];	
		$jsLain=$this->Request['jsLain'];	
		$totalTnpAdm=$this->Request['totalTnpAdm'];
		$biayaAdm=$this->Request['biayaAdm'];
		$askep=$this->Request['askep'];
			
		$jmlBayar=$this->Request['jmlBayar'];
		$jmlTagihan=$this->Request['jmlTagihan'];
		
		$uangMuka=$this->Request['uangMuka'];
		$sisaByr=$this->Request['sisaByr'];			
			
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		//$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		//$nmTable=$this->Request['table'];
		
		
		//$jmlBayar=number_format($jmlBayar,2,',','.');
		$sisaBayar=number_format($this->Request['sisa'],2,',','.');
		$sisa=$this->Request['sisa'];
		
		$tagihanTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		$bayarTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		
		$sql = "SELECT * FROM view_pasien_rwt_inap WHERE cm='$cm' AND no_trans='$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$noTrans=$row['no_trans'];
				$nmPasien=ucwords($row['nm_pasien']);
				$pjPasien=ucwords($row['nm_pj']);
				$AlmtPjPasien=ucwords($row['almt_pj']);
				$kls_kmr=$row['nm_kls_kamar'];
				$nm_kmr=$row['nm_kamar'];
				$tarif_kamar=$row['tarif'];
				$tgl_masuk=$this->convertDate($row['tgl_masuk'],'3');
				$wkt_masuk=$row['wkt_masuk'];
			}	
		
		$noKwitansi = substr($noTrans,6,8).'/'.'KW-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);		
		//Update tabel tbt_pembayaran
		/*
		$byrRecord=BayarRecord::finder()->findByPk($noTrans);
		$byrRecord->status_pembayaran='2';//Sudah dibayar!
		$byrRecord->tgl_bayar=date('Y-m-d h:m:s');
		$byrRecord->no_kwitansi=$noKwitansi;
		$byrRecord->operator=$nipTmp;
		$byrRecord->Save();//Update!
		*/
		
		$pdf=new reportKwitansi('P','mm','a4');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'KUITANSI','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',9);
		
		/*
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$noTrans,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);
		*/
		
		$pdf->Cell(30,10,'Telah Terima dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien.' / '.$pjPasien,0,0,'L');
		$pdf->Ln(5);		
		$pdf->Cell(30,10,'Uang Sebesar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$tagihanTerbilang,0,0,'L');	
		$pdf->Ln(10);
		
		$pdf->Cell(30,10,'Untuk Pembayaran',0,0,'L');
		$pdf->Cell(5,10,':  ',0,0,'L');
		$pdf->Cell(45,10,'- Biaya Perawatan Pasien',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');	
		
		$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Nomor Catatan Medik',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		
		$pdf->Ln(5);
		
		$i=0;
		$sqlOperasiBil = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans' ";
		$arrOperasiBil=$this->queryAction($sqlOperasiBil,'S');
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(5,10,'  ',0,0,'L');
					$pdf->Cell(45,10,'- Tindakan',0,0,'L');	
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,'- '.$row['nm_opr'],0,0,'L');
				}
				else
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(5,10,'  ',0,0,'L');
					$pdf->Cell(45,10,'',0,0,'L');	
					$pdf->Cell(2,10,' ',0,0,'L');
					$pdf->Cell(30,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(30,10,'',0,0,'L');
				$pdf->Cell(5,10,'  ',0,0,'L');
				$pdf->Cell(45,10,'- Tindakan',0,0,'L');	
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,'-',0,0,'L');
				$pdf->Ln(5);
			}
			
			$i++;
		}
		
		
		//$pdf->Ln(5);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'- Dirawat Tanggal',0,0,'L');	
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10, $tgl_masuk.'  s.d.  '.$tglKeluar,0,0,'L');
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Ln(7);
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Cell(5,10,'  ',0,0,'L');
		$pdf->Cell(45,10,'JUMLAH',0,0,'L');	
		$pdf->Cell(2,10,':',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'L');
		
		$pdf->Ln(10);
		/*	
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(150,5,'Tindakan',1,0,'C');
		$pdf->Cell(30,5,'Jumlah',1,0,'C');		
		*/
		
		//Line break
		//$pdf->Ln(6);
		/*
		$sql="SELECT nama, jml FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(150,5,$row['nama'],1,0,'L');
			$pdf->Cell(30,5,'Rp ' . number_format($row['jml'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			
		}	
		*/	
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
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInap') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
			
		//$pdf->Cell(80,5,'Total ',0,0,'R');		
		//pdf->Cell(30,5,'Rp ' . $jmlTagihan,1,0,'R');					
		//$pdf->Cell(80,5,'Bayar ',0,0,'R');
		//$pdf->Cell(30,5,'Rp ' . $jmlBayar,1,0,'R');
		//$pdf->Cell(150,5,'Kembalian ',0,0,'R');
		//$pdf->Cell(30,5,'Rp ' . $sisaBayar,1,0,'R');	
		
		
		
		//------------------------------- RINCIAN BIAYA PERAWATAN ----------------------------------//
		//$pdf=new reportKwitansi('P','mm','a4');
		$pdf->AddPage('P','mm','a4');
		
		
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
		$pdf->Cell(0,5,'RINCIAN BIAYA PERAWATAN','0',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(30,10,'No. Nota Penagihan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'-',0,0,'L');
		$pdf->Ln(5);	
			
		$pdf->Cell(30,10,'Rekening Untuk',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$pjPasien,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Alamat Penanggung',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$AlmtPjPasien,0,0,'L');
		$pdf->Ln(10);
		
		$pdf->Cell(5,10,'A. ',0,0,'L');
		$pdf->Cell(25,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		$pdf->Ln(5);
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(5,10,'B. ',0,0,'L');
					$pdf->Cell(25,10,'Tindakan',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(85,10,'- '.$row['nm_opr'],0,0,'L');
			
					$pdf->Cell(20,10,'No. Kamar',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$nm_kmr,0,0,'L');
				}
				else
				{
					$pdf->Cell(5,10,' ',0,0,'L');
					$pdf->Cell(25,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(85,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(5,10,'B. ',0,0,'L');
				$pdf->Cell(25,10,'Tindakan',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(85,10,'-',0,0,'L');
		
				$pdf->Cell(20,10,'No. Kamar',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$nm_kmr,0,0,'L');
				$pdf->Ln(5);
			}
			
			$i++;
		}
		
		$pdf->Cell(5,10,'C. ',0,0,'L');
		$pdf->Cell(25,10,'Tanggal Masuk',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$tgl_masuk,0,0,'L');
		$pdf->Cell(15,10,'-  Jam   : ',0,0,'L');
		$pdf->Cell(35,10,$wkt_masuk.' WIB',0,0,'L');
		$pdf->Cell(20,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);	
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$tglKeluar,0,0,'L');
		$pdf->Cell(15,10,'-  Jam   : ',0,0,'L');
		$pdf->Cell(35,10,$wktKeluar.' WIB',0,0,'L');
		$pdf->Cell(20,10,'D. Kelas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$kls_kmr,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(25,10,'Jumlah Hari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(35,10,$lamaInap.' Hari',0,0,'L');
		$pdf->Cell(15,10,'',0,0,'L');
		$pdf->Cell(35,10,'',0,0,'L');
		$pdf->Cell(20,10,'E. Tarif',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($tarif_kamar,2,',','.'),0,0,'L');
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->Cell(30,10,'Uraian Biaya Perawatan :',0,0,'L');
		$pdf->Ln(5);	
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(5,10,'1. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Fasilitas Rumah Sakit',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsRS,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'2. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Tenaga Ahli',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsAhli,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'3. ',0,0,'L');
		$pdf->Cell(45	,10,'Jasa Pemeriksaan Penunjang',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsPenunjang,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'4. ',0,0,'L');
		$pdf->Cell(45	,10,'Obat-Obatan & Alat Kesehatan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsObat,0,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(5,10,'5. ',0,0,'L');
		$pdf->Cell(45	,10,'Biaya Lain-Lain',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(65,10,'',0,0,'L');
		$pdf->Cell(30,10,$jsLain,'B',0,'R');
		$pdf->Cell(3,10,'+','B',0,'R');
		$pdf->Ln(10);	
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'TOTAL ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($totalTnpAdm,2,',','.'),0,0,'R');
		$pdf->Ln(5);	
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'Biaya Administrasi = ',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$biayaAdm,'B',0,'R');
		$pdf->Cell(3,10,'+','B',0,'R');
		$pdf->Ln(10);	
		
		$pdf->SetFont('Arial','B',9);
		
		$pdf->Cell(5,10,'',0,0,'L');
		$pdf->Cell(45,10,'',0,0,'L');
		$pdf->Cell(2,10,'',0,0,'L');
		$pdf->Cell(63,10,'JUMLAH TOTAL',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'Rp. '.number_format($jmlTagihan,2,',','.'),0,0,'R');
		$pdf->Ln(5);	
		
		$pdf->SetFont('Arial','',9);
		
		if($uangMuka=='')
		{
			$pdf->Cell(5,10,'',0,0,'L');
			$pdf->Cell(45,10,'',0,0,'L');
			$pdf->Cell(2,10,'',0,0,'L');
			$pdf->Cell(63,10,'Uang Muka ',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,'-',0,0,'C');
			$pdf->Ln(5);	
			
			$pdf->Cell(5,10,'',0,0,'L');
			$pdf->Cell(45,10,'',0,0,'L');
			$pdf->Cell(2,10,'',0,0,'L');
			$pdf->Cell(63,10,'Jumlah Yang Masih Harus Dibayar ',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,'-',0,0,'C');
			$pdf->Ln(7);	
		}
		else
		{
			$pdf->Cell(5,10,'',0,0,'L');
			$pdf->Cell(45,10,'',0,0,'L');
			$pdf->Cell(2,10,'',0,0,'L');
			$pdf->Cell(63,10,'Uang Muka ',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$uangMuka,0,0,'R');
			$pdf->Ln(5);	
			
			$pdf->Cell(5,10,'',0,0,'L');
			$pdf->Cell(45,10,'',0,0,'L');
			$pdf->Cell(2,10,'',0,0,'L');
			$pdf->Cell(63,10,'Jumlah Yang Masih Harus Dibayar ',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(30,10,$sisaByr,0,0,'R');
			$pdf->Ln(7);	
		}
				
			
			//------------------------------- terbilang ----------------------------------//
		$pdf->Cell(30,5,'Terbilang : ',0,0,'L');
		$pdf->Ln(5);
		
		$pdf->MultiCell(0,5,$tagihanTerbilang,1,'L');
		$pdf->Ln(5);
		
		
			//------------------------------ TTD KASIR RINCIAN BIAYA PERAWATAN --------------------------------- //
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInap') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
		
		//------------------------------- NOTA PENAGIHAN PERAWATAN ----------------------------------//
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
		$pdf->Cell(0,5,'NOTA PENAGIHAN PERAWATAN','0',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
				
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$nmPasien,0,0,'L');

		$pdf->Cell(25,10,'Kelas',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$kls_kmr,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Nomor CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$cm,0,0,'L');

		$pdf->Cell(25,10,'Kamar',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nm_kmr,0,0,'L');
		$pdf->Ln(5);
		
		$i=0;
		foreach($arrOperasiBil AS $row )
		{
			if($row['nm_opr']!='')
			{
				if($i==0)
				{
					$pdf->Cell(30,10,'Tindakan',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
			
					$pdf->Cell(25,10,'Tanggal Masuk',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$tgl_masuk,0,0,'L');
				}
				elseif($i==1)
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
			
					$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
					$pdf->Cell(2,10,':  ',0,0,'L');
					$pdf->Cell(30,10,$tglKeluar,0,0,'L');
				}
				else
				{
					$pdf->Cell(30,10,'',0,0,'L');
					$pdf->Cell(2,10,'  ',0,0,'L');
					$pdf->Cell(75,10,'- '.$row['nm_opr'],0,0,'L');
				}
				$pdf->Ln(5);
			}
			else
			{
				$pdf->Cell(30,10,'Tindakan',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(75,10,'-',0,0,'L');
		
				$pdf->Cell(25,10,'Tanggal Masuk',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$tgl_masuk,0,0,'L');
				$pdf->Ln(5);
				
				$pdf->Cell(30,10,'',0,0,'L');
				$pdf->Cell(2,10,'  ',0,0,'L');
				$pdf->Cell(75,10,'',0,0,'L');
		
				$pdf->Cell(25,10,'Tanggal Keluar',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(30,10,$tglKeluar,0,0,'L');
				$pdf->Ln(10);
			}
			
			$i++;
		}
		
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(25,5,'Tanggal',1,0,'C');
		$pdf->Cell(45,5,'Uraian Biaya',1,0,'C');
		$pdf->Cell(50,5,'Pelaksana',1,0,'C');
		$pdf->Cell(42,5,'Keterangan',1,0,'C');
		$pdf->Cell(30,5,'Biaya',1,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			/*
		$jsRS=$this->Request['jsRS'];	
		$jsAhli=$this->Request['jsAhli'];	
		$jsPenunjang=$this->Request['jsPenunjang'];	
		$jsObat=$this->Request['jsObat'];	
		$jsLain=$this->Request['jsLain'];	
		$totalTnpAdm=$this->Request['totalTnpAdm'];
		$biayaAdm=$this->Request['biayaAdm'];
			
		$jmlBayar=$this->Request['jmlBayar'];
		$jmlTagihan=$this->Request['jmlTagihan'];
		*/	
		
		
			//------------------------------- Biaya Fasilitas Rumah Sakit ----------------------------------//
		$pdf->Cell(162,5,'I. Biaya Fasilitas Rumah Sakit',1,0,'L');
		$pdf->Cell(30,5,$jsRS,1,0,'R');
		$pdf->Ln(5);
		
		$sql = "SELECT * FROM view_jasa_kamar WHERE cm='$cm' AND no_trans='$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl_masuk'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_kamar'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,$lamaInap.' Hari',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
		/*		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',1,0,'L');
		$pdf->Cell(45,5,'Kamar Pasien',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',1,0,'L');
		$pdf->Cell(45,5,'Kamar Bayi',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',1,0,'L');
		$pdf->Cell(45,5,'Kamar Bersalin',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',1,0,'L');
		$pdf->Cell(45,5,'Kamar Operasi',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
		$pdf->Ln(5);
		*/
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			//------------------------------- Biaya Tenaga Ahli ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(162,5,'II. Biaya Tenaga Ahli',1,0,'L');
		$pdf->Cell(30,5,$jsAhli,1,0,'R');
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','',8);
		
		$sql = "SELECT * FROM view_inap_operasi_billing WHERE cm='$cm' AND no_trans='$notrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				if($row['dktr_utama']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['dktr_utama'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_utama'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['dktr_utama'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_utama'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_dktr'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['ass_dktr']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['ass_dktr'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_dktr'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['ass_dktr'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_dktr'])->nama,1,0,'L');
					}					
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_assdktr'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['dktr_anastesi']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['dktr_anastesi'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_anastesi'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['dktr_anastesi'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_anastesi'])->nama,1,0,'L');
					}						
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_anastesi'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['ass_anastesi']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['ass_anastesi'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_anastesi'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['ass_anastesi'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_anastesi'])->nama,1,0,'L');
					}		
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_assanastesi'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['dktr_anak']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['dktr_anak'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_anak'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['dktr_anak'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_anak'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_anak'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['dktr_obgyn']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['dktr_obgyn'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['dktr_obgyn'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['dktr_obgyn'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_obgyn'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['ass_obgyn']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['ass_obgyn'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_obgyn'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['ass_obgyn'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_obgyn'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_assobgyn'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				
				if($row['id_opr'] != '04') //jika bukan operasi caessar 
				{
					if($row['bidan']!==NULL)
					{
						$pdf->SetFont('Arial','',8);
						$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
						
						if($row['ass_luar']=='1') //jika ada asisten luar
						{
							if(strlen($row['bidan'])==4)//jika id = 4 karakter => bukan dari luar
							{
								$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['bidan'])->nama,1,0,'L');
							}
							else
							{
								$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['bidan'])->nama,1,0,'L');
							}
						}
						elseif($row['ass_luar']=='0') //jika ada asisten luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['bidan'])->nama,1,0,'L');
						}
						
						$pdf->Cell(42,5,'',1,0,'L');
						$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_bidan'],2,',','.'),1,0,'R');
						$pdf->Ln(5);
					}
					
					if($row['ass_bidan']!==NULL)
					{
						$pdf->SetFont('Arial','',8);
						$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
						
						if($row['ass_luar']=='1') //jika ada asisten luar
						{
							if(strlen($row['ass_bidan'])==4)//jika id = 4 karakter => bukan dari luar
							{
	
								$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_bidan'])->nama,1,0,'L');
							}
							else
							{
								$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['ass_bidan'])->nama,1,0,'L');
							}
						}
						elseif($row['ass_luar']=='0') //jika ada asisten luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['ass_bidan'])->nama,1,0,'L');
						}
						
						$pdf->Cell(42,5,'',1,0,'L');
						$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_assbidan'],2,',','.'),1,0,'R');
						$pdf->Ln(5);
					}
				}
				
				
				
				if($row['pm1']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['pm1'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm1'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['pm1'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm1'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm1'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['pm2']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['pm2'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm2'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['pm2'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm2'])->nama,1,0,'L');
					}
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm2'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				
				if($row['id_opr'] == '04') //jika operasi caessar, tarif sewa kamar + tarif petugas resusitasi  
				{
					if($row['pm3']!==NULL)
					{
						$pdf->SetFont('Arial','',8);
						$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
						$pdf->Cell(50,5,'Kamar Operasi/OK',1,0,'L');
						$pdf->Cell(42,5,'',1,0,'L');
						//$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm3'],2,',','.'),1,0,'R');
						$pdf->Cell(30,5,'Rp. '.number_format(($row['tarif_petugas_resusitasi'] + $row['tarif_pm3']),2,',','.'),1,0,'R');
						$pdf->Ln(5);
					}
				}
				else
				{
					if($row['pm3']!==NULL)
					{
						$pdf->SetFont('Arial','',8);
						$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
						$pdf->SetFont('Arial','',9);
						$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
						$pdf->Cell(50,5,'Kamar Operasi/OK',1,0,'L');
						$pdf->Cell(42,5,'',1,0,'L');
						$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm3'],2,',','.'),1,0,'R');
						//$pdf->Cell(30,5,'Rp. '.number_format(($row['tarif_petugas_resusitasi'] + $row['tarif_pm3']),2,',','.'),1,0,'R');
						$pdf->Ln(5);
					}
				}
				
				if($row['pm4']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['pm4'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm4'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['pm4'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm4'])->nama,1,0,'L');
					}					
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm4'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
				if($row['pm5']!==NULL)
				{
					$pdf->SetFont('Arial','',8);
					$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
					$pdf->SetFont('Arial','',9);
					$pdf->Cell(45,5,$row['nm_opr'],1,0,'L');
					
					if($row['ass_luar']=='1') //jika ada asisten luar
					{
						if(strlen($row['pm5'])==4)//jika id = 4 karakter => bukan dari luar
						{
							$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm5'])->nama,1,0,'L');
						}
						else
						{
							$pdf->Cell(50,5,AsistenLuarRecord::finder()->findByPk($row['pm5'])->nama,1,0,'L');
						}
					}
					elseif($row['ass_luar']=='0') //jika ada asisten luar
					{
						$pdf->Cell(50,5,PegawaiRecord::finder()->findByPk($row['pm5'])->nama,1,0,'L');
					}		
					
					$pdf->Cell(42,5,'',1,0,'L');
					$pdf->Cell(30,5,'Rp. '.number_format($row['tarif_pm5'],2,',','.'),1,0,'R');
					$pdf->Ln(5);
				}
				
					
			}
			
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
			
			//------------------------------- Biaya Pemeriksaan Penunjang ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(162,5,'III. Biaya Pemeriksaan Penunjang',1,0,'L');
		$pdf->Cell(30,5,$jsPenunjang,1,0,'R');
		$pdf->Ln(5);
		
		$pdf->Cell(192,5,'Dokter',1,0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',8);		
		//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=1";
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=1 ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(192,5,'Paramedis',1,0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',8);
		//$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND kelompok=2";
		$sql = "SELECT * FROM view_penunjang WHERE cm='$cm' AND no_trans='$notrans' AND kelompok=2";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
				$pdf->Cell(50,5,$row['nm_pegawai'],1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
				
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			//------------------------------- Biaya Obat-obatan & Alkes ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(162,5,'IV. Biaya Obat-obatan & Alat Kesehatan',1,0,'L');
		$pdf->Cell(30,5,$jsObat,1,0,'R');
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','',9);
		
		$sql = "SELECT * FROM view_obat_alkes WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag=0 AND st_bayar=0";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nm_obat_alkes'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,$row['jml_obt_alkes'].' '.$row['satuan'],1,0,'C');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			//------------------------------- Biaya Lain-Lain lab yg st_bayar=0 ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		
		$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totLab += $row['jml'];
		}
		
		$pdf->Cell(162,5,'V. Biaya Laboratorium',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($totLab,2,',','.'),1,0,'R');
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','',9);
		
		$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'lab' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,'',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Ln(5);
		}
		
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			//------------------------------- Biaya Lain-Lain rad yg st_bayar=0 ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		
		$sql = "SELECT jml FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totRad += $row['jml'];
		}
				
		$pdf->Cell(162,5,'VII. Biaya Radiologi',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($totRad,2,',','.'),1,0,'R');
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','',9);
		
		$sql = "SELECT * FROM view_biaya_lain_lab_rad WHERE jenis = 'rad' AND cm='$cm' AND no_trans='$notrans' AND flag = 0 AND st_bayar = 0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(45,5,$row['nm_tdk'],1,0,'L');
			$pdf->Cell(50,5,'',1,0,'L');
			$pdf->Cell(42,5,'',1,0,'L');
			$pdf->Cell(30,5,'Rp. '.number_format($row['jml'],2,',','.'),1,0,'R');
			$pdf->Ln(5);
		}
		
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
		//------------------------------- Biaya Lain-Lain ----------------------------------//
		$pdf->SetFont('Arial','B',9);
			
			//-------------- Tarif Askep -------------- //
		if($lamaInap > 0)				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askep
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskep += $row['tarif'];
			}
			
			$totAskep = $lamaInap * $totAskep;
		}
		
			//-------------- Tarif Askep OK -------------- //
		if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askep_ok			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskepOK += $row['tarif'];
			}
		}
		
			//-------------- Tarif Askeb -------------- //
		if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT tarif 
						   FROM tbt_inap_askeb			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAskeb += $row['tarif'];
			}
		}
		
			//-------------- Tarif Oksigen -------------- //
		if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
		{
			$sql = "SELECT tarif AS jumlah
						   FROM tbt_inap_oksigen			
						   WHERE 
							no_trans = '$noTrans'
							AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totOksigen += $row['jumlah'];
			}
		}
		
		$totLain = $totAskep + $totAskepOK + $totAskeb + $totOksigen;
		
		$pdf->Cell(162,5,'VIII. Biaya Lain-Lain',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($totLain,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		
		//------------------------------- Biaya Askep ----------------------------------//
		if($lamaInap > 0)				
		{
			$sql = "SELECT nama,tgl,tarif 
						   FROM tbt_inap_askep
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		//------------------------------- Biaya Askep OK jika ada ----------------------------------//
		if(InapAskepOkRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT nama,tgl,tarif 
						   FROM tbt_inap_askep_ok			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['tarif'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		//------------------------------- Biaya Askeb jika ada ----------------------------------//
		if(InapAskebRecord::finder()->find('no_trans=? AND flag=?',$notrans,'0'))				
		{
			$sql = "SELECT nama,tgl,tarif 
						   FROM tbt_inap_askeb			
						   WHERE no_trans = '$notrans'
						   AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format(($lamaInap*$row['tarif']),2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		//----------- Biaya Oksigen jika ada ---------------------
		if(InapOksigenRecord::finder()->find('no_trans=?',$noTrans))				
		{
			$sql = "SELECT tgl, tarif AS jumlah
						   FROM tbt_inap_oksigen			
						   WHERE 
							no_trans = '$noTrans'";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,$this->convertDate($row['tgl'],'3'),1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,'Oksigen',1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
		}
		
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
		//------------------------------- Biaya Lain-lain pada saat status pasien = pasien rwt jln/IGD, Jika peralihan dari pasien rwt jln ke rwt inap ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		
		if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
		{
			//-------------------- Biaya Alih Tindakan Rawat jalan ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Tindakan Rawat Jalan'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totRwtJln += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Obat Alkes ---------------------	
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Obat Alkes'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totAlkes += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Radiologi ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Rad'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totRadAlih += $row['jumlah'];
			}
			
			//-------------------- Biaya Alih Laboratorium ---------------------		
			$sql = "SELECT 
						jml AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Lab'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$totLabAlih += $row['jumlah'];
			}
			
			//$totBiayaAlih = $totRwtJln + $totAlkes + $totRad + $totLab ;
			$totBiayaAlih = $totRwtJln + $totAlkes + $totRadAlih + $totLabAlih;
		}
		
		
				
		$pdf->Cell(162,5,'IX. Biaya Selama Rawat Jalan',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($totBiayaAlih,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		
		if(RwtInapRecord::finder()->find('no_trans=? AND st_alih=?',$notrans,'1'))			
		{
			/*
			$sql = "SELECT 
						SUM(jml) AS jumlah
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,'Biaya selama rawat jalan',1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			*/
			//-------------------- Biaya Alih Tindakan Rawat jalan ---------------------		
			$sql = "SELECT 
						jml AS jumlah, jns_trans, nama, no_trans_jln
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Tindakan Rawat Jalan'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				//$idDokter = KasirRwtJlnRecord::finder()->find('no_trans_rwtjln',$row['no_trans_jln'])->dokter;
				$noTransJln = $row['no_trans_jln'];
				$sql = "SELECT dokter FROM tbt_kasir_rwtjln WHERE no_trans_rwtjln = '$noTransJln' AND st_flag = '0'";
				$idDokter = KasirRwtJlnRecord::finder()->findBySql($sql)->dokter;
				$nmDoker = PegawaiRecord::finder()->findByPk($idDokter)->nama;
				//$idTdk = KasirRwtjlnRecord::finder()->find('no_trans_rwtjln',$row['no_trans_jln'])->id_tindakan;
				
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				
				if(substr($row['nama'],0,11) == 'Jasa Dokter')
				{
					$pdf->Cell(50,5,$nmDoker,1,0,'L');
				}
				else
				{
					$pdf->Cell(50,5,'',1,0,'L');
				}
				
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			//-------------------- Biaya Alih Obat Alkes ---------------------		
			$sql = "SELECT 
						jml AS jumlah, jns_trans, nama
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Obat Alkes'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			//-------------------- Biaya Alih Radiologi ---------------------		
			$sql = "SELECT 
						jml AS jumlah, jns_trans, nama
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Rad'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}
			
			//-------------------- Biaya Alih Laboratorium ---------------------		
			$sql = "SELECT 
						jml AS jumlah, jns_trans, nama
					FROM 
						view_biaya_alih 
					WHERE  
						no_trans_inap='$notrans'
						AND jns_trans = 'Lab'";
		
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(25,5,'-',1,0,'C');
				$pdf->SetFont('Arial','',9);
				$pdf->Cell(45,5,$row['nama'],1,0,'L');
				$pdf->Cell(50,5,'',1,0,'L');
				$pdf->Cell(42,5,'',1,0,'L');
				$pdf->Cell(30,5,'Rp. '.number_format($row['jumlah'],2,',','.'),1,0,'R');
				$pdf->Ln(5);
			}

		}
		
		/*
		$pdf->Cell(25,5,'',1,0,'C');
		$pdf->Cell(45,5,'Askep',1,0,'L');
		$pdf->Cell(50,5,'',1,0,'L');
		$pdf->Cell(42,5,$lamaInap.' Hari',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($askep,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		*/	
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
			//------------------------------- Biaya Administrasi ----------------------------------//
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(162,5,'X. Biaya Administrasi',1,0,'L');
		$pdf->Cell(30,5,$biayaAdm,1,0,'R');
		/*
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(25,5,'',1,0,'L');
		$pdf->Cell(45,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(40,5,'',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.'',1,0,'R');
		*/
		$pdf->Ln(5);
		$pdf->Cell(0,1,'',0,0,'C');
		$pdf->Ln(1);
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(25,5,'',0,0,'L');
		$pdf->Cell(45,5,'',0,0,'L');
		$pdf->Cell(50,5,'',0,0,'L');
		$pdf->Cell(42,5,'TOTAL',1,0,'L');
		$pdf->Cell(30,5,'Rp. '.number_format($jmlTagihan,2,',','.'),1,0,'R');
		
		$pdf->Ln(10);
		
			//------------------------------- terbilang ----------------------------------//
		$pdf->Cell(30,5,'Terbilang : ',0,0,'L');
		$pdf->Ln(5);
		
		$pdf->MultiCell(0,5,$tagihanTerbilang,1,'L');
		
		
		$pdf->Ln(10);	
		
			//------------------------------ TTD KASIR NOTA PENAGIHAN PERAWATAN --------------------------------- //
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'K a s i r,',0,0,'C');
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Tarif.BayarKasirRwtInap') . '&purge=1');	
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(120,5,'',0,0,'C');	
		$pdf->Cell(80,8,'NIP. '.$nip,0,0,'C');	
		$pdf->Ln(5);
		
			
		$pdf->Output();
	}
	
}
?>