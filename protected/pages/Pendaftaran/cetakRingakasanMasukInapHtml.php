<?php
//require_once 'Barcode.php';

class cetakRingakasanMasukInapHtml extends SimakConf
{
	 public function onInit($param)
	 {		
			parent::onInit($param);
			$tmpVar=$this->authApp('0');
			if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
				$this->Response->redirect($this->Service->constructUrl('login'));
			
			
	 }
	 
	 public function onPreFlushOutput($sender,$param) 
	 {
		if (($content=$this->Response->getContents())!='') 
		{
			require("html2fpdf.php");
		  // Do something with content
		  file_put_contents('../../tmp/test.html', $content);
		  
		  $cm = $this->Request['cm'];
		
			$htmlFile = '../../tmp/test.html';
			$file = fopen($htmlFile,"r");
			$size_of_file = filesize($htmlFile);
			$buffer = fread($file, $size_of_file);
			fclose($file);
			
			$pdf=new HTML2FPDF();
			$pdf->AddPage();
			$pdf->WriteHTML($htmlbuffer);
			$pdf->Output('doc.pdf','I');
		
		  // If you don't want to send anything to browser, remove buffer, and redirect to other page
		  //$this->Response->clear ();
		  //$this->Response->redirect($this->constructUrl('...'));
		}
	  }
	  
	public function onLoad($param)
	{	
		$this->cetakBtn->focus();
		
		$cm = $this->Request['cm'];
		$noTrans = $this->Request['noTrans'];
		$jmlBed = $this->Request['jmlBed'];
		
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
		
		$umur = $this->cariUmur('1',$cm,'');
		$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr' ;
		
		$pj = RwtInapRecord::finder()->findByPk($noTrans)->nama_pgg;
		$alamatPj = RwtInapRecord::finder()->findByPk($noTrans)->alamat_pj;
		
		$dokter = RwtInapRecord::finder()->findByPk($noTrans)->dokter;
		$nmDokter = PegawaiRecord::finder()->findByPk($dokter)->nama;
		$idPoli = PegawaiRecord::finder()->findByPk($dokter)->poliklinik;
		$spDokter = PoliklinikRecord::finder()->findByPk($idPoli)->nama;
		
		$kelas = RwtInapRecord::finder()->findByPk($noTrans)->kelas;
		$nmKelas = KelasKamarRecord::finder()->findByPk($kelas)->nama;
		
		$ruang = RwtInapRecord::finder()->findByPk($noTrans)->kamar;
		$nmRuang = RuangRecord::finder()->findByPk($ruang)->nama;
		
		$kelPas = RwtInapRecord::finder()->findByPk($noTrans)->penjamin;
		$tipePasien = KelompokRecord::finder()->findByPk($kelPas)->nama;
		
		if($kelPas == '02' || $kelPas == '07')//Asuransi / Jamper
		{
			$perus = RwtInapRecord::finder()->findByPk($noTrans)->perus_asuransi;
			$nmPerus = PerusahaanRecord::finder()->findByPk($perus)->nama;
		}
		else
			$nmPerus = '-';
		
		$caraMasuk = RwtInapRecord::finder()->findByPk($noTrans)->cr_masuk;
		$caraMasuk = caramasukRecord::finder()->findByPk($caraMasuk)->nama;
		
		$tglMasuk = $this->convertDate(RwtInapRecord::finder()->findByPk($noTrans)->tgl_masuk,'1');
		$jamMasuk = RwtInapRecord::finder()->findByPk($noTrans)->wkt_masuk;
		
		$this->txt->Text = $cm;		
		$this->txt2->Text = $satuation.' '.$nama;
		$this->txt3->Text = $this->convertDate(PasienRecord::finder()->findByPk($cm)->tgl_lahir,'3');
		$this->txt4->Text = $umur;
		$this->txt5->Text = AgamaRecord::finder()->findByPk(PasienRecord::finder()->findByPk($cm)->agama)->nama;
		$this->txt6->Text = $pendidikan;
		$this->txt7->Text = $pekerjaan;
		$this->txt8->Text = PasienRecord::finder()->findByPk($cm)->alamat;
		$this->txt9->Text = $status;
		$this->txt10->Text = $pj;
		$this->txt11->Text = $alamatPj;
		$this->txt12->Text = $spDokter;
		$this->txt13->Text = $nmDokter;
		$this->txt14->Text = $nmKelas;
		$this->txt15->Text = $nmRuang;
		
		$this->txt16->Text = '';
		//$this->txt17->Text = $jmlBed;
		$this->txt17->Text = '';
		$this->txt18->Text = $noTrans;
		$this->txt19->Text = $tipePasien;
		$this->txt20->Text = $nmPerus;
		$this->txt21->Text = $caraMasuk;
		$this->txt22->Text = $tglMasuk;
		$this->txt23->Text = $jamMasuk;
		
		
		
		/*
		$this->barcode->Text = '<img src="index.php?page=Pendaftaran.barcodeGen&cm='.$cm.'" />';
		*/
	}
	
	public function closeBtnClicked($sender,$param)
	{	
		$cm = $this->Request['cm'];
		$mode = $this->Request['mode'];
		
		if($mode == '01')
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarCariPdftrn'));
		else
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtInap'));	
	}
	
	public function tesClicked($sender,$param)
	{	
		$this->getApplication()->attachEventHandler('OnPreFlushOutput', array ($this, 'onPreFlushOutput'));	
		/*
		$cm = $this->Request['cm'];
		
		$htmlFile = $this->Service->constructUrl('Pendaftaran.DaftarRwtJln').'&cm='.intval($cm);
		$file = fopen($htmlFile,"r");
		$size_of_file = filesize($htmlFile);
		$buffer = fread($file, $size_of_file);
		fclose($file);
		
		$pdf=new reportHtml();
		$pdf->AddPage();
		$pdf->WriteHTML($buffer);
		$pdf->Output('doc.pdf','I');	
			
		
		ob_start();
		include '../../../index.php?page=Pendaftaran.barcodeGen&cm=0000035';
		$htmlbuffer = ob_get_contents();
		ob_end_clean();
		
		$pdf=new reportHtml();
		$pdf->AddPage();
		$pdf->WriteHTML($htmlbuffer);
		$pdf->Output('doc.pdf','I');
		*/
	}
	
	
	
}
?>
