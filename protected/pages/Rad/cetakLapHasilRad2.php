<?php
class cetakLapHasilRad extends SimakConf
{
	public function onLoad($param)
	{	
		$jnsPasien = $this->Request['jnsPasien'];
		$noReg = $this->Request['noReg'];		
		$noTrans = $this->Request['notrans'];		
		$cm = $this->Request['cm'];	
		$nmPasien = $this->Request['nama'];
		$dokter = $this->Request['dokter'];		
		$operator = $this->User->IsUserName;
		$nipTmp = $this->User->IsUserNip;		
		$nmTable = $this->Request['table'];
		
		//$cek = substr($noTrans,6,2);
		if ($jnsPasien=='0')
		{
			$noTrans = RadJualRecord::finder()->find('no_reg = ?',$noReg)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;
			$ruangan = 'Poli '.PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
			$tglHasil = $this->convertDate(RadJualRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = RadJualRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';
		}
		elseif($jnsPasien=='1')
		{
			$noTrans = RadJualInapRecord::finder()->find('no_reg = ?',$noReg)->no_trans_inap;
			$idJnsKamar = RwtInapRecord::finder()->findByPk($noTrans)->jenis_kamar;
			$idKamar = RwtInapRecord::finder()->findByPk($noTrans)->kamar;
			
			$ruangan = KamarNamaRecord::finder()->findByPk($idJnsKamar)->nama;
			
			if($idJnsKamar == '1')
				$ruangan .= ' ('.RuangRecord::finder()->findByPk($idKamar)->nama.')';
				
			$tglHasil = $this->convertDate(RadJualInapRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = RadJualInapRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';	
		}	
		elseif($jnsPasien=='2')
		{
			$noTrans = RadJualLainRecord::finder()->find('no_reg = ?',$noReg)->no_trans_pas_luar;
			$ruangan = '-';
			
			$tglHasil = $this->convertDate(RadJualLainRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = RadJualLainRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';	
		}
		
		$idSatuation = PasienRecord::finder()->findByPk($cm)->satuation;
		$satutation = SatuationRecord::finder()->findByPk($idSatuation)->singkatan;
		
		if($jnsPasien=='0' || $jnsPasien=='1')
		{
			$nmPas = $satutation.' '.$nmPasien;
			
			if($jnsPasien == '0')
				$umur = $this->cariUmur('0',$cm,$idKlinik);
			else
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
		
		if($jnsPasien=='0')
		{
			$sql = "SELECT 
				  tbt_rad_penjualan.id_tindakan,
				  tbm_rad_tindakan.kode,
				  tbm_rad_tindakan.nama,
				  tbt_rad_penjualan.catatan
				FROM
				  tbt_rad_penjualan
				  INNER JOIN tbm_rad_tindakan ON (tbm_rad_tindakan.kode = tbt_rad_penjualan.id_tindakan)
				WHERE 
					tbt_rad_penjualan.no_reg = '$noReg' 
					AND tbt_rad_penjualan.st_cetak_hasil = '1' ";
		}
		elseif($jnsPasien=='1')
		{
			$sql = "SELECT 
				  tbt_rad_penjualan_inap.id_tindakan,
				  tbm_rad_tindakan.kode,
				  tbm_rad_tindakan.nama,
				  tbt_rad_penjualan_inap.catatan
				FROM
				  tbt_rad_penjualan_inap
				  INNER JOIN tbm_rad_tindakan ON (tbm_rad_tindakan.kode = tbt_rad_penjualan_inap.id_tindakan)
				WHERE 
					tbt_rad_penjualan_inap.no_reg = '$noReg' 
					AND tbt_rad_penjualan_inap.st_cetak_hasil = '1' ";
		}	
		elseif($jnsPasien=='2')
		{
			$sql = "SELECT 
				  tbt_rad_penjualan_lain.id_tindakan,
				  tbm_rad_tindakan.kode,
				  tbm_rad_tindakan.nama,
				  tbt_rad_penjualan_lain.catatan
				FROM
				  tbt_rad_penjualan_lain
				  INNER JOIN tbm_rad_tindakan ON (tbm_rad_tindakan.kode = tbt_rad_penjualan_lain.id_tindakan)
				WHERE 
					tbt_rad_penjualan_lain.no_reg = '$noReg' 
					AND tbt_rad_penjualan_lain.st_cetak_hasil = '1' ";
		}
		
		$arrData = $this->queryAction($sql,'S');
		
		$pdf = new reportHtml();
		//$pdf->html2fpdf('P','mm','a4');
		
		//$pdf->SetFont('Arial','',9);
		//$pdf->Cell(10,10,$sql,'0',0,'C');
			
		$j=0;
		foreach($arrData as $row)
		{	
			$pdf->AddPage();
			
			$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
			$pdf->Ln(2);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(10,10,'','0',0,'C');
			$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
			
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(10,10,'','0',0,'C');
			$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
			$pdf->Ln(4);
			$pdf->Cell(0,10,'           Telp. (021) 74718440','0',0,'C');	
			$pdf->Ln(3);
			$pdf->Cell(0,5,'','B',1,'C');
			$pdf->Ln(3);		
			$pdf->SetFont('Arial','BU',10);
			$pdf->Cell(0,5,'RADIOLOGY REPORT / LAPORAN HASIL PEMERIKSAAN RADIOLOGI','0',0,'C','',$this->Service->constructUrl('Rad.beritaRad'));
			$pdf->Ln(5);	
						
			$pdf->SetFont('Arial','',9);
			
			$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$nmPas,0,0,'L');		
			
			$pdf->Cell(30,10,'Umur / Sex',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$umur.' / '.$jKel,0,0,'L');		
			
			$pdf->Ln(5);
			
			$pdf->Cell(30,10,'No. Film',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$noReg,0,0,'L');
			
			$pdf->Cell(30,10,'Ruangan',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$ruangan,0,0,'L');
			
			$pdf->Ln(5);
			
			$pdf->Cell(30,10,'Tanggal',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$tglHasil.', '.$wktHasil,0,0,'L');
					
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
			
			$pdf->Cell(30,10,'Pemeriksaan',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$row['nama'],0,0,'L');
			
			$pdf->Cell(30,10,'No. RM',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(70,10,$cm,0,0,'L');
			
			$pdf->Ln(5);
			$pdf->Cell(0,5,'','B',1,'C');
			$pdf->Ln(2);
			$pdf->SetFont('Arial','',10);
			$pdf->WriteHTML($row['catatan']);
		}
		
		$pdf->Output();
						
	}
	
}
?>