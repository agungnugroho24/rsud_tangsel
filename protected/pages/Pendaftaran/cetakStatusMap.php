<?php
class cetakStatusMap extends SimakConf
{
	 public function onInit($param)
	 {		
			parent::onInit($param);
			$tmpVar=$this->authApp('0');
			if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
				$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{	
		$mode=$this->Request['mode'];
		$cm=$this->Request['cm'];
		$nama=$this->Request['nama'];
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];	
		$dateNow = date('Y-m-d');
		
		if($mode == '01')
			$url = $this->Service->constructUrl('Pendaftaran.DaftarCariData');
		else
			$url = $this->Service->constructUrl('Pendaftaran.DaftarRwtJln').'&cm='.intval($cm);	
		
		$pdf = new reportBarcode('L','mm','a5');		
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
		$pdf->SetFont('Arial','B',10);
		
		$pdf->Cell(0,5,'DATA IDENTITAS PASIEN','0',0,'C','',$url);	
		$pdf->Ln(5);
		/*
		$pdf->SetY(5);
		$pdf->SetTopMargin(5);
		$pdf->SetLeftMargin(5);
		$pdf->SetRightMargin(5);
		*/
		
		//$pdf->footerTxt="*** KARTU PASIEN ***";
		
		$nama  = PasienRecord::finder()->findByPk($cm)->nama;
		
		$jkel = PasienRecord::finder()->findByPk($cm)->jkel;
		if($jkel == '0')
			$jkel = 'Laki-Laki';	
		else
			$jkel = 'Perempuan';	
			
		$idSatuation  = PasienRecord::finder()->findByPk($cm)->satuation;
		$satuation = SatuationRecord::finder()->findByPk($idSatuation)->singkatan;
		
		$pekerjaan  = PasienRecord::finder()->findByPk($cm)->pekerjaan;
		$pekerjaan = PekerjaanRecord::finder()->findByPk($pekerjaan)->nama;
		
		$pendidikan  = PasienRecord::finder()->findByPk($cm)->pendidikan;
		$pendidikan = PendidikanRecord::finder()->findByPk($pendidikan)->nama;
		
		$idkelompok  = PasienRecord::finder()->findByPk($cm)->kelompok;
		$kelompok = KelompokRecord::finder()->findByPk($idkelompok)->nama;
		
		if($idkelompok == '02' || $idkelompok == '07')//asuransi / jamper
		{
			$perusahaan = PasienRecord::finder()->findByPk($cm)->perusahaan;
			$perusahaan = PerusahaanRecord::finder()->findByPk($perusahaan)->nama;	
		}
		else
			$perusahaan = '-';
		
		$noAsuransi = PasienRecord::finder()->findByPk($cm)->no_asuransi;
		if($noAsuransi == '')
			$noAsuransi = '-';	
			
			
		
		
		$status = PasienRecord::finder()->findByPk($cm)->status;
		if($status == '0')
			$status = 'Kawin';	
		elseif($status == '1')
			$status = 'Belum Kawin';		
		elseif($status == '2')
			$status = 'Duda';		
		elseif($status == '3')
			$status = 'Janda';				
		
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(10,5,'Data Pasien','0',0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',11);
		
		$pdf->Cell(35,5,'No. Rekam Medis','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$cm,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Nama Pasien','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(0,5,$satuation.' '.$nama,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',11);
		
		$pdf->Cell(35,5,'Tempat/Tgl. Lahir','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,PasienRecord::finder()->findByPk($cm)->tmp_lahir.', '.$this->convertDate(PasienRecord::finder()->findByPk($cm)->tgl_lahir,'3'),'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$tglLahir = PasienRecord::finder()->findByPk($cm)->tgl_lahir;
		$umur = $this->dateDifference($tglLahir, $dateNow);
		$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr ';
		
		$pdf->Cell(35,5,'Umur','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$umur,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Jenis Kelamin','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$jkel,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Alamat','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->MultiCell(0,5,PasienRecord::finder()->findByPk($cm)->alamat,'0','L');
		$pdf->Ln(0);
		
		$pdf->Cell(35,5,'No. Telp. / HP','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,PasienRecord::finder()->findByPk($cm)->telp.' / '.PasienRecord::finder()->findByPk($cm)->hp,'0',0,'L','',$url);
		$pdf->Ln(5);
		/*
		$pdf->Cell(35,5,'HP','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,PasienRecord::finder()->findByPk($cm)->hp,'0',0,'L','',$url);
		$pdf->Ln(5);
		*/
		$pdf->Cell(35,5,'Status','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$status,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Golongan Darah','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,PasienRecord::finder()->findByPk($cm)->goldar,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Nama Keluarga','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,PasienRecord::finder()->findByPk($cm)->nm_pj,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Pekerjaan','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$pekerjaan,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Cell(35,5,'Pendidikan','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(0,5,$pendidikan,'0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->Ln(2);
		
		$pdf->Cell(63,7,'Kelompok Pasien','TLR',0,'C');
		$pdf->Cell(63,7,'Pemegang Asuransi','TLR',0,'C');
		$pdf->Cell(63,7,'No. Asuransi','TLR',0,'C');
		$pdf->Ln(7);
		$pdf->Cell(63,7,$kelompok,'BLR',0,'C');
		$pdf->Cell(63,7,$perusahaan,'BLR',0,'C');
		$pdf->Cell(63,7,$noAsuransi,'BLR',0,'C');
		
		
		$pdf->Code128(170,33,$cm,30,10);	
		
		$pdf->Output();
	}
}
?>
