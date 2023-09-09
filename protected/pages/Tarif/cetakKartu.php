<!-- --><?php
class cetakKartu extends SimakConf
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
		$klinik=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$shift=$this->Request['shift'];		
		$notrans=$this->Request['notrans'];		
		$mode=$this->Request['mode'];
		$dateNow = date('Y-m-d');
		
		//$sql = "SELECT COUNT(*) AS jml_pas FROM tbt_rawat_jalan WHERE id_klinik = '$klinik' AND shift = '$shift' AND dokter = '$dokter' AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24";
		$sql = "SELECT COUNT(*) AS jml_pas FROM tbt_rawat_jalan WHERE id_klinik = '$klinik' AND shift = '$shift' AND dokter = '$dokter' AND tgl_visit = '$dateNow' ";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$jmlPas = $row['jml_pas'];
		}
		
		if(strlen($jmlPas) == 1)
		{
			$jmlPas = '00'.$jmlPas;
		}
		elseif(strlen($jmlPas) == 2)
		{
			$jmlPas = '0'.$jmlPas;
		}
		
		/*($klinik=='07')
		{ 
			$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM
				  tbd_pegawai
				  INNER JOIN tbm_poliklinik ON (tbd_pegawai.poliklinik = tbm_poliklinik.id),
				  tbd_pasien
				WHERE tbd_pasien.cm='$cm'
				AND tbm_poliklinik.id='08'
				AND tbd_pegawai.id='$dokter' ";
		}*/
		/*else
		{*/
			$sql="SELECT 
				  tbd_pasien.cm,
				  tbd_pasien.nama AS nmPas,
				  tbd_pasien.jkel,
				  tbd_pegawai.nama AS nmDok,
				  tbm_poliklinik.nama AS poli
				FROM
				  tbd_pegawai,
				  tbd_pasien,
				  tbm_poliklinik
				WHERE tbd_pasien.cm='$cm'
				AND tbm_poliklinik.id='$klinik'
				AND tbd_pegawai.id='$dokter'
				AND FIND_IN_SET(tbm_poliklinik.id,tbd_pegawai.poliklinik) ";			
		//}
		
		$pdf=new fpdf('L','mm','a5');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',14);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'C');
		$pdf->Ln(5);		
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'JL. Pajajaran No. 101 Pamulang','0',0,'C');	
		$pdf->Ln(5);
		$pdf->Cell(10,10,'','0',0,'C');
		$pdf->Cell(0,10,'Telp. (021) 74718440','0',0,'C');
		$pdf->Ln(3);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		//$url=$this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$cm,'mode'=>'1'));
		
		//$url=str_replace('amp;','',$url);
		if($mode == '01')
			$url = $this->Service->constructUrl('Pendaftaran.DaftarCariPdftrn');
		else
			$url = $this->Service->constructUrl('Pendaftaran.cetakAntrian').'&poli='.$klinik.''.'&dokter='.$dokter.'&shift='.$shift.'&cm='.$cm	;
		
		
		$idPenjamin = RwtjlnRecord::finder()->findByPk($notrans)->penjamin;
		$idPerusahaan = RwtjlnRecord::finder()->findByPk($notrans)->perus_asuransi;
		$kelompokPenjamin = KelompokRecord::finder()->findByPk($idPenjamin)->nama;	
			
		if($idPenjamin == '02' || $idPenjamin == '07') //kelompok pasien ASURANSI / jamper
			{
				$idPerusAsuransi = RwtjlnRecord::finder()->findByPk($notrans)->perus_asuransi;
				$namaPerus = PerusahaanRecord::finder()->findByPk($idPerusAsuransi)->nama;
				$kelompokPenjamin .= ' / '.$namaPerus;
				$jdForm = "FORMULIR PENGAJUAN KLAIM RAWAT JALAN / ".$namaPerus;
			}
			else
			{
				$jdForm = "FORM TINDAKAN";
			}
				
		$pdf->SetFont('Arial','BU',12);
		$pdf->Cell(0,5,$jdForm,'0',0,'C','',$url);
		$pdf->Ln(0);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(0,5,'No. Urut : '.$jmlPas,'0',0,'R','',$url);
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(30,5,'No. cm',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(60,5,$cm,0,0,'L');
		
		$arrData=$this->queryAction($sql,'R');
		
		foreach($arrData as $row)
		{
			$nmDok=$row['nmDok'];
			$pdf->Cell(20,5,'Poliklinik',0,0,'L');
			$pdf->Cell(5,5,':  ',0,0,'L');
			
			/*if($klinik=='07')
				$poli='UGD';
			elseif($klinik=='08')
				$poli=$row['poli'].' / IGD';
			else*/
				$poli=$row['poli'];
				
			$pdf->Cell(30,5,$poli,0,0,'L');
			/*
			$pdf->Cell(25,10,'Tanggal',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(25,10,date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');		*/
			$pdf->Ln(5);
			$pdf->Cell(30,5,'Nama',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(60,5,$row['nmPas'].', '.$embel,0,0,'L');
			//$pdf->Ln(5);		
			$pdf->Cell(20,5,'Dokter',0,0,'L');
			$pdf->Cell(5,5,':  ',0,0,'L');
			$pdf->Cell(25,5,$row['nmDok'],0,0,'L');		
			$pdf->Ln(5);
			$pdf->Cell(30,5,'Umur/Jns Kel.',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			
			$umur = $this->cariUmur('0',$cm,$klinik);
			$umur = $umur['years'].' thn '.$umur['months'].' bln '.$umur['days'].' hr';
			
			$pdf->Cell(60,5,$umur.' / '.$this->cariJkel($cm),0,0,'L');
			
			$pdf->Cell(20,5,'Penjamin',0,0,'L');
			$pdf->Cell(5,5,':  ',0,0,'L');
			$pdf->Cell(25,5,$kelompokPenjamin,0,0,'L');	
			
			$pdf->Ln(5);		
		}
		
		$pdf->Ln(2);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(10,5,'1.',0,0,'C');
		$pdf->Cell(30,5,'Apotik',0,0,'L');		
		$pdf->Cell(30,5,'[  ] Ya',0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(10,5,'2.',0,0,'C');
		$pdf->Cell(30,5,'Laboratorium',0,0,'L');		
		$pdf->Cell(30,5,'[  ] Ya',0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(10,5,'3.',0,0,'C');
		$pdf->Cell(30,5,'Radiologi',0,0,'L');		
		$pdf->Cell(30,5,'[  ] Ya',0,0,'L');
		$pdf->Ln(5);
		
		if($idPenjamin == '02' || $idPenjamin == '07') //kelompok pasien ASURANSI / jamper
			{
				$pdf->Cell(10,5,'4.',0,0,'C');
				$pdf->Cell(150,5,'Diagnosa Utama : ',0,0,'L');
				$pdf->Ln(13);
				$pdf->Cell(10,5,'5.',0,0,'C');
				$pdf->Cell(150,5,'Diagnosa Sekunder : ',0,0,'L');
				$pdf->Ln(13);
				$pdf->Cell(10,5,'6.',0,0,'C');
				$pdf->Cell(150,5,'Tindakan : ',0,0,'L');	
				$pdf->Ln(1);
			}	
			else
			{
				$pdf->Cell(10,5,'4.',0,0,'C');
				$pdf->Cell(150,5,'Tindakan : ',0,0,'L');
				$pdf->Ln(25);
			}
			
		//$pdf->SetFont('Arial','',8);
		$pdf->Cell(300,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(300,8,'Dokter,',0,0,'C');	
		$pdf->Ln(15);
		//$pdf->Cell(300,8,'('.$nmDok.')',0,0,'C','',$this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));		
		$pdf->Cell(300,8,'('.$nmDok.')',0,0,'C','',$url);		
		$pdf->Output();
	}
	
}
?>
