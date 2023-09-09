<?php
class cetakHistoriPasien extends SimakConf
{   
	public function onLoad($param)
	{	
		
		$cm=$this->Request['cm'];
		$tipeRawat=$this->Request['tipeRawat'];
		
		$url = $this->Service->constructUrl('Pendaftaran.DataPasDetail').'&cm='.$cm.'&tipeRawat='.$tipeRawat;
						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);		
				
		
		$pdf=new reportCari('P','mm','a4');
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
		//$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(3);		
		
		$pdf->SetFont('Arial','B',10);
			
		if($tipeRawat == '1')//Rawat Jalan
		{
			$pdf->Cell(0,5,'HISTORY PASIEN RAWAT JALAN','0',0,'C','',$url);
		}
		elseif($tipeRawat == '2') //rawat inap
		{
			$pdf->Cell(0,5,'HISTORY PASIEN RAWAT INAP','0',0,'C','',$url);
		}
		else
		{
			$pdf->Cell(0,5,'HISTORY PASIEN','0',0,'C','',$url);
		}
					
		$pdf->Ln(7);
		
		$sql = "SELECT * FROM tbd_pasien WHERE cm = '$cm'";
		$data = PasienRecord::finder()->findBySql($sql);
		
		$jk = $data->jkel;
		if($jk=='0')
		{
			$jk = 'Laki-Laki';	
		}
		else
		{
			$jk = 'Perempuan';	
		}
		
		$agama = $data->agama;
		if($agama=='0' || $agama=='')
		{
			$agama = '-';
		}
		else
		{
			$agama = AgamaRecord::finder()->findByPk($agama)->nama;
		}
		
		if($data->status == '1'){$status = 'Kawin';}
		elseif($data->status == '2'){$goldar = 'Belum Kawin';}
		elseif($data->status == '3'){$status = 'Duda';}
		elseif($data->status == '4'){$status = 'Janda';}
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,5,'Data Pasien : ','0',0,'L','',$url);
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,5,'No. Rekam Medis  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$cm,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Nama  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->nama,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Tempat Tgl. Lahir  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->tmp_lahir.', '.$this->convertDate($data->tgl_lahir,'3'),'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Jenis Kelamin ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$jk,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Agama  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$agama,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Alamat  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->alamat,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Telepon ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->telp,'0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Hp  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->hp,'0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Status Perkawinan  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$status,'0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Golongan Darah  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,$data->goldar,'0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Pekerjaan  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,PekerjaanRecord::finder()->findByPk($data->pekerjaan)->nama,'0',0,'L');
		
		$pdf->Ln(5);
		$pdf->Cell(30,5,'Pendidikan  ','0',0,'L');
		$pdf->Cell(5,5,':','0',0,'C');
		$pdf->Cell(155,5,PendidikanRecord::finder()->findByPk($data->pendidikan)->nama,'0',0,'L');
		
		$pdf->Ln(10);
		
		if($tipeRawat == '1')//Rawat Jalan
		{
			$this->bindGridJalan($pdf);
		}
		elseif($tipeRawat == '2') //rawat inap
		{
			$this->bindGridInap($pdf);	
		}
		else
		{
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5,'Rawat Jalan ','0',0,'C','',$url);
			$pdf->Ln(5);
			
			$this->bindGridJalan($pdf);	
			
			$pdf->Ln(5);
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(0,5,'Rawat Inap ','0',0,'C','',$url);
			$pdf->Ln(5);
			$this->bindGridInap($pdf);	
		}
				
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,5,'Daftar Obat / Alkes ','0',0,'C','',$url);
		$pdf->Ln(5);
		$this->bindGridObat($pdf);	
								
		$pdf->Output();
						
	}
	
	public function bindGridJalan($pdf)
	{
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakHistoriPasienJalan'];		
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			
			$pdf->SetFont('Arial','B',8);
			
			$pdf->SetWidths(array(20,25,25,50,25,25,25));
			$pdf->SetAligns(array('C','C','C','C','C','C','C'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4','4','4','4','4'));
						
			$pdf->Row(array(
						'Tgl. Kunjungan',
						'Poli',
						'Umur',
						'Diagnosa',
						'Dokter',
						'Kel. Penjamin',
						'Perusahaan Asuransi'
					));
			
			
			$pdf->Ln(0.5);
			
			
			$pdf->SetWidths(array(20,25,25,50,25,25,25));
			$pdf->SetAligns(array('C','C','C','L','C','C','C'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4','4','4','4','4'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',8);
				
				$cm = $row['cm'];
				$dataPas = PasienRecord::finder()->findByPk($cm);
				$tglLahir = $dataPas->tgl_lahir;			
				$umur = $this->dateDifference($tglLahir,$row['tgl_visit']);
				$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
			
				$tglVisit = $this->ConvertDate($row['tgl_visit'],'1');
				
				$pdf->Row(array(
						$tglVisit,
						$row['nm_poli'],
						$umur,
						$row['diagnosa'],
						$row['nm_dokter'],
						$row['kel_penjamin'],
						$row['nm_perus']
					));
				
				if($pdf->GetY()>250)
				{
					$pdf->AddPage();
					$pdf->setY(10);
					$pdf->setX(10);
				}
			}

		}
	}
	
	
	public function bindGridInap($pdf)
	{
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakHistoriPasienInap'];		
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			
			$pdf->SetFont('Arial','B',8);
			
			$pdf->SetWidths(array(20,25,75,25,25,25));
			$pdf->SetAligns(array('C','C','C','C','C','C','C'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4','4','4','4'));
						
			$pdf->Row(array(
						'Tgl. Kunjungan',
						'Umur',
						'Diagnosa',
						'Dokter',
						'Kel. Penjamin',
						'Perusahaan Asuransi'
					));
			
			
			$pdf->Ln(0.5);
			
			
			$pdf->SetWidths(array(20,25,75,25,25,25));
			$pdf->SetAligns(array('C','C','L','C','C','C'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4','4','4','4'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',8);
				
				$cm = $row['cm'];
				$dataPas = PasienRecord::finder()->findByPk($cm);
				$tglLahir = $dataPas->tgl_lahir;			
				$umur = $this->dateDifference($tglLahir,$row['tgl_visit']);
				$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
			
				$tglVisit = $this->ConvertDate($row['tgl_visit'],'1');
				
				$pdf->Row(array(
						$tglVisit,
						$umur,
						$row['diagnosa'],
						$row['nm_dokter'],
						$row['kel_penjamin'],
						$row['nm_perus']
					));
				
				if($pdf->GetY()>250)
				{
					$pdf->AddPage();
					$pdf->setY(10);
					$pdf->setX(10);
				}
			}

		}
	}
	
	
	public function bindGridObat($pdf)
	{
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakHistoriObat'];		
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			
			$pdf->SetFont('Arial','B',8);
			
			$pdf->SetWidths(array(135,25,35));
			$pdf->SetAligns(array('C','C','C'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4'));
						
			$pdf->Row(array(
						'Nama Obat/Alkes',
						'Jumlah',
						'Total'
					));
			
			
			$pdf->Ln(0.5);
			
			
			$pdf->SetWidths(array(135,25,35));
			$pdf->SetAligns(array('L','R','R'));
			$pdf->Setln('4');
			$pdf->SetLineSpacing(array('4','4','4'));
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',8);
				
				$pdf->Row(array(
						$row['nm_obat'],
						$row['jumlah'],
						number_format($row['total'],'2',',','.')
					));
				
				if($pdf->GetY()>250)
				{
					$pdf->AddPage();
					$pdf->setY(10);
					$pdf->setX(10);
				}
			}

		}
	}
}
?>
