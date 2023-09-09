<?php
class cetakLapHasilLab extends SimakConf
{
	public function onLoad($param)
	{	
		
		$jnsPasien = $this->Request['jnsPasien'];
		$noReg = $this->Request['noReg'];		
		$noTrans = $this->Request['notrans'];		
		$cm=$this->Request['cm'];	
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		$nmTable=$this->Request['table'];
		$stCetakUlang=$this->Request['stCetakUlang'];
		
		$tglHasil = $this->convertDate(LabHasilRecord::finder()->find('no_trans = ?',$noReg)->tgl,'3');
		$wktHasil = LabHasilRecord::finder()->find('no_trans = ?',$noReg)->wkt.' WIB';
		
		//$cek = substr($noTrans,6,2);
		if ($jnsPasien=='0')
		{
			$noTrans = LabJualRecord::finder()->find('no_reg = ?',$noReg)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;
			$ruangan = 'Poli '.PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
		}
		elseif($jnsPasien=='1')
		{
			$noTrans = LabJualInapRecord::finder()->find('no_reg = ?',$noReg)->no_trans_inap;
			$idJnsKamar = RwtInapRecord::finder()->findByPk($noTrans)->jenis_kamar;
			$idKamar = RwtInapRecord::finder()->findByPk($noTrans)->kamar;
			
			$ruangan = KamarNamaRecord::finder()->findByPk($idJnsKamar)->nama;
			
			if($idJnsKamar == '1')
				$ruangan .= ' ('.RuangRecord::finder()->findByPk($idKamar)->nama.')';
		}	
		elseif($jnsPasien=='2')
		{
			$noTrans = LabJualLainRecord::finder()->find('no_reg = ?',$noReg)->no_trans_pas_luar;
			$ruangan = '-';
		}
		
		$idSatuation = PasienRecord::finder()->findByPk($cm)->satuation;
		$satutation = SatuationRecord::finder()->findByPk($idSatuation)->singkatan;
		
		if($jnsPasien=='0' || $jnsPasien=='1')
		{
			if($stCetakUlang=='01')
				$nmPas = $nmPasien;
			else
				$nmPas = $satutation.' '.$nmPasien;
			
			if($jnsPasien == '0')
				$umur = $this->cariUmur('0',$cm,$idKlinik);
			elseif($jnsPasien=='1')
				$umur = $this->cariUmur('1',$cm,'');
			
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr' ;
			$jKel = $this->cariJkel($cm);
		}
		elseif($jnsPasien=='2')
		{
			$cm = '-';
			$nmPas = $nmPasien;
			$umur = PasienLuarRecord::finder()->findByPk($noTrans)->umur;
			$jKel = PasienLuarRecord::finder()->findByPk($noTrans)->jkel;
		}	
		
		
		
		//$noKwitansi = substr($noTrans,6,6).'/'.'LAB-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
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
		//$pdf=new cetakKartu();
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
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'Telp. (021) 74718440','0',0,'C');	
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'LAPORAN HASIL PEMERIKSAAN LABORATORIUM','0',0,'C');
		$pdf->Ln(5);				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. RM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$cm,0,0,'L');
		
		$pdf->Cell(30,10,'Ruangan',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$ruangan,0,0,'L');
		
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$noReg,0,0,'L');
		
		$pdf->Cell(30,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		if($cm)
		{
			$pdf->Cell(30,10,$dokter,0,0,'L');
		}
		else
		{
			$pdf->Cell(30,10,'-',0,0,'L');
		}
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$nmPas,0,0,'L');
		
		$pdf->Cell(30,10,'Tanggal',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$tglHasil.', '.$wktHasil,0,0,'L');
		$pdf->Ln(5);
		
		$pdf->Cell(30,10,'Umur / Sex',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(70,10,$umur.' / '.$jKel,0,0,'L');
		
		$pdf->Cell(30,10,'Diagnosa',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,'',0,0,'L');
		$pdf->Ln(5);
		
		
					
		//Line break
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(10,5,'No.','1',0,'C');
		$pdf->Cell(60,5,'Jenis Pemeriksaan','1',0,'C');
		$pdf->Cell(40,5,'Hasil','1',0,'C');
		$pdf->Cell(40,5,'Nilai Normal Pria','1',0,'C');
		$pdf->Cell(40,5,'Nilai Normal Wanita','1',0,'C');
		//$pdf->Cell(70,5,'Keterangan','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT 
				  tbt_lab_hasil.id,
				  tbt_lab_hasil.id_lab,
				  tbt_lab_hasil.id_paket,
				  tbt_lab_hasil.kode_item_paket,
				  tbm_lab_tindakan.kode,
				  tbm_lab_tindakan.nama,
				  tbt_lab_hasil.hasil,
				  tbt_lab_hasil.nilai_normal_pria,
				  tbt_lab_hasil.nilai_normal_wanita
				FROM
				  tbt_lab_hasil
				  INNER JOIN tbm_lab_tindakan ON (tbm_lab_tindakan.kode = tbt_lab_hasil.id_lab)
				WHERE 
					tbt_lab_hasil.no_trans = '$noReg' ";
		
		if($stCetakUlang=='01')
			$sql .= " AND tbt_lab_hasil.st_cetak = '1' ";
		else
			$sql .= " AND tbt_lab_hasil.st_cetak = '0' ";
			
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$id = $row['id'];
			$idLab = $row['id_lab'];
			$kode_item_paket = $row['kode_item_paket'];
			$idPaket = $row['id_paket'];
			
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.','1',0,'C');
			
			if($idPaket > 0)
			{
				$sqlPaket = "SELECT * FROM tbm_lab_paket WHERE id_paket='$idPaket' AND kode='$kode_item_paket' ";
				$nmTdk = LabPaketRecord::finder()->findBySql($sqlPaket)->item;
				$pdf->Cell(60,5,$nmTdk,'1',0,'L');
			}
			else
			{
				$pdf->Cell(60,5,$row['nama'],'1',0,'L');
			}
				
			$pdf->Cell(40,5,utf8_decode($row['hasil']),'1',0,'C');
			$pdf->Cell(40,5,utf8_decode(utf8_encode($row['nilai_normal_pria'])),'1',0,'C');
			$pdf->Cell(40,5,utf8_decode(utf8_encode($row['nilai_normal_wanita'])),'1',0,'C');
			//$pdf->Cell(70,5,'','1',0,'L');
			$pdf->Ln(5);
			//$pdf->Ln(5);
			
			if($stCetakUlang != '01')
			{
				$sql = "UPDATE tbt_lab_hasil SET st_cetak = '1' WHERE id = '$id'";
				$this->queryAction($sql,'C');
			}	
		}
		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'Pemeriksa,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		
		if($stCetakUlang=='01')
			$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Lab.LapTrans'));
		else
			$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Lab.cetakHasilLab') . '&purge=1&nmTable=' . $nmTable);		
			
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>
