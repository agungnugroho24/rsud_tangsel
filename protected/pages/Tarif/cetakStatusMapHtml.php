<?php
//require_once 'Barcode.php';

class cetakStatusMapHtml extends SimakConf
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
		$cm=$this->Request['cm'];
		$nama=$this->Request['nama'];
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];	
		$dateNow = date('Y-m-d');
		
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
		
		$this->txt->Text = $cm;
		$this->txt2->Text = $satuation.' '.$nama;
		$this->txt3->Text = PasienRecord::finder()->findByPk($cm)->tmp_lahir.', '.$this->convertDate(PasienRecord::finder()->findByPk($cm)->tgl_lahir,'3');
		$this->txt4->Text = $jkel;
		$this->txt5->Text = PasienRecord::finder()->findByPk($cm)->alamat;
		$this->txt6->Text = PasienRecord::finder()->findByPk($cm)->telp;
		$this->txt7->Text = PasienRecord::finder()->findByPk($cm)->hp;
		$this->txt8->Text = $status;
		$this->txt9->Text = PasienRecord::finder()->findByPk($cm)->goldar;
		$this->txt10->Text = PasienRecord::finder()->findByPk($cm)->nm_pj;
		$this->txt11->Text = $pekerjaan;
		$this->txt12->Text = $pendidikan;
		$this->txt13->Text = $kelompok;
		$this->txt14->Text = $perusahaan;
		$this->txt15->Text = $noAsuransi;
		
		$this->barcode->Text = '<img src="index.php?page=Pendaftaran.barcodeGen&cm='.$cm.'" />';
		
	}
	
	public function closeBtnClicked($sender,$param)
	{	
		$cm = $this->Request['cm'];
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln').'&cm='.intval($cm));	
	}
}
?>
