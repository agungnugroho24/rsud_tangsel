<?php
class cetakHistoriPasienAll extends SimakConf
{   
	public function onLoad($param)
	{	
		
		$cm=$this->Request['cm'];
		$tipeRawat=$this->Request['tipeRawat'];
		
		$url = $this->Service->constructUrl('Pendaftaran.DaftarCariData').'&tipeRawat='.$tipeRawat;
						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$session=new THttpSession;
		$session->open();
				
		
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
		$pdf->Cell(0,5,'','B',1,'C');
		$pdf->Ln(1);		
		$pdf->SetFont('Arial','BU',10);
		
		$pdf->SetFont('Arial','B',10);
			
		if($tipeRawat == '1')//Rawat Jalan
		{
			$pdf->Cell(0,5,'DATA PASIEN RAWAT JALAN','0',0,'C','',$url);
		}
		elseif($tipeRawat == '2') //rawat inap
		{
			$pdf->Cell(0,5,'DATA PASIEN RAWAT INAP','0',0,'C','',$url);
		}
		else
		{
			$pdf->Cell(0,5,'DATA PASIEN','0',0,'C','',$url);
		}
					
		$pdf->Ln(7);
		
		
		$sql = $session['cetakHistoriPasienAll'];
		//$sql .= " tgl_visit DESC ";		
		$jmlData=count($arrData=$this->queryAction($sql,'S'));
		
		if($jmlData!=0)
		{
			
			$pdf->SetFont('Arial','B',8);
			
			if($tipeRawat != '3')//Semua Perawatan
			{
				$pdf->SetWidths(array(15,30,30,20,40,10,25,20));
				$pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
				$pdf->Setln('4');
				$pdf->SetLineSpacing(array('4','4','4','4','4','4','4','4'));
							
				$pdf->Row(array(
							'No. RM',
							'Nama',
							'Penanggung',
							'Tgl. Lahir',
							'Alamat',
							'JK',
							'Kelompok Penjamin',
							'Kunjungan Pertama'
						));
			}
			else
			{
				$pdf->SetWidths(array(15,30,30,20,60,10,25));
				$pdf->SetAligns(array('C','C','C','C','C','C','C'));
				$pdf->Setln('4');
				$pdf->SetLineSpacing(array('4','4','4','4','4','4','4'));
							
				$pdf->Row(array(
							'No. RM',
							'Nama',
							'Penanggung',
							'Tgl. Lahir',
							'Alamat',
							'JK',
							'Kelompok Penjamin'
						));
			}
			
			$pdf->Ln(0.5);
			
			
			if($tipeRawat != '3')//Semua Perawatan
			{
				$pdf->SetWidths(array(15,30,30,20,40,10,25,20));
				$pdf->SetAligns(array('C','L','L','C','L','C','C','C'));
				$pdf->Setln('4');
				$pdf->SetLineSpacing(array('4','4','4','4','4','4','4','4'));
			}
			else
			{
				$pdf->SetWidths(array(15,30,30,20,60,10,25));
				$pdf->SetAligns(array('C','L','L','C','L','C','C'));
				$pdf->Setln('4');
				$pdf->SetLineSpacing(array('4','4','4','4','4','4','4'));
			}
			
			
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$j++;
				$pdf->SetFont('Arial','',8);
				
				$cm = $row['cm'];
				
				if(isset($row['tgl_lahir']))
				{
					$conTgl = $this->ConvertDate($row['tgl_lahir'],'1');
					$tglLahir = $conTgl;
				}
				else
				{
					$tglLahir = '-';
				}			
				
				if($row['jkel'] == '0')
				{
					$jkel = 'L';
				}
				else
				{
					$jkel = 'P';
				}			
					
				
				if($tipeRawat != '')
				{
					$jnsPasien = $tipeRawat;
					if($jnsPasien == '1') //Pasien Rawat Jalan
					{
						$sql = "SELECT tgl_visit FROM tbt_rawat_jalan WHERE cm = '$cm' ORDER BY tgl_visit";
						$tglvisit = RwtjlnRecord::finder()->findBySql($sql)->tgl_visit;
						$tglvisit = $this->ConvertDate($tglvisit,'1');
					}
					else //Pasien Rawat Inap
					{
						$sql = "SELECT tgl_masuk FROM tbt_rawat_inap WHERE cm = '$cm' ORDER BY tgl_masuk";
						$tglvisit = RwtInapRecord::finder()->findBySql($sql)->tgl_masuk;
						$tglvisit = $this->ConvertDate($tglvisit,'1');
					}
				}	
				
				if($tipeRawat != '3')
				{
					$pdf->Row(array(
						$row['cm'],
						$row['nama'],
						$row['nm_pj'],
						$tglLahir,
						$row['alamat'],
						$jkel,
						$row['nm_penjamin'],
						$tglvisit
					));
				}
				else //Semua Perawatan
				{
					$pdf->Row(array(
						$row['cm'],
						$row['nama'],
						$row['nm_pj'],
						$tglLahir,
						$row['alamat'],
						$jkel,
						$row['nm_penjamin']
					));
				}
				
				
				if($pdf->GetY()>250)
				{
					$pdf->AddPage();
					$pdf->setY(10);
					$pdf->setX(10);
				}
			}

		}
		else
		{
			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(0,5,'DATA HISTORY PASIEN TIDAK DITEMUKAN','0',0,'C','',$this->Service->constructUrl('Pendaftaran.DataPasDetail'));			
			$pdf->Ln(7);
		}
		
		

		
										
		$pdf->Output();
						
	}
}
?>
