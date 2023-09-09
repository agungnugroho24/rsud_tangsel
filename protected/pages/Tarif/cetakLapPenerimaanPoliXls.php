<?php
class cetakLapPenerimaanPoliXls extends XlsGen
{
	public function onLoad($param)
	{	
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapPenerimaanPoli'];
		$sql .= ' ORDER BY tgl_visit DESC, no_trans_rwtjln';
			
		$periode = $this->Request['periode'];
		$poli = $this->Request['poli'];
		$dokter = $this->Request['dokter'];
		$penjamin = $this->Request['penjamin'];
		$perus = $this->Request['perus'];
		
		$file = 'Lap.PenerimaanPoli.xls';
		
		//http headers	
		$this->HeaderingExcel($file);
		
		//membuat workbook
		$workbook=new Workbook("-");
		
		//membuat worksheet pertama
		$worksheet1= & $workbook->add_worksheet('Laporan 1');
		
		$baris=0;
		$kolom=0;
		
		//set lebar tiap kolom
		$this->AddWS($worksheet1,'c','8',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','30',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		//$this->AddWS($worksheet1,'c','30',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','45',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		
		$frmtTitleLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtTitleLeft,'b','1','12');
		$left= $this->AddFormat($frmtTitleLeft,'bd','0','');
		$left= $this->AddFormat($frmtTitleLeft,'HA','left','');
		
		$frmtTitleLeft10 =  & $workbook->add_format();
		$left= $this->AddFormat($frmtTitleLeft10,'b','1','10');
		$left= $this->AddFormat($frmtTitleLeft10,'bd','0','');
		$left= $this->AddFormat($frmtTitleLeft10,'HA','left','');
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'b','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','0','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');		
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'bd','1','');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		$wrap= $this->AddFormat($frmtWrap,'WR','1','');
		
		
		$baris=0;
		$kolom=0;
		
		$worksheet1->write_string($baris,0,'LAPORAN PENERIMAAN POLI' ,$frmtTitleLeft);
		$baris++;
		$worksheet1->write_string($baris,0,$periode ,$frmtTitleLeft10);
		$baris++;
		
		if($poli)
		{
			$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama ,$frmtTitleLeft10);
			$baris++;
		}
		
		if($dokter)
		{
			$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama ,$frmtTitleLeft10);
			$baris++;
		}
		
		if($penjamin)
		{
			$customer = KelompokRecord::finder()->findByPk($penjamin)->nama;
			
			if($penjamin != '01')
			{
				if($perus && PerusahaanRecord::finder()->findByPk($perus))
					$customer .= ' ('.PerusahaanRecord::finder()->findByPk($perus)->nama.')';
			}
			
			$worksheet1->write_string($baris,0,'Customer : '.$customer ,$frmtTitleLeft10);
			$baris++;
		}
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','1','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');
		$left= $this->AddFormat($frmtLeft,'WR','1','');
		
		$frmtCenterHeader =  & $workbook->add_format();
		$centerHeader= $this->AddFormat($frmtCenterHeader,'b','1','10');
		$centerHeader= $this->AddFormat($frmtCenterHeader,'bd','1','');
		$centerHeader= $this->AddFormat($frmtCenterHeader,'HA','center','');
		$centerHeader= $this->AddFormat($frmtCenterHeader,'WR','1','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		$center= $this->AddFormat($frmtCenter,'WR','1','');
		
		$frmtRight =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRight,'','1','10');
		$right= $this->AddFormat($frmtRight,'bd','1','');
		$right= $this->AddFormat($frmtRight,'HA','right','');
		
		$baris++;
		$kolom = 0;
		//$worksheet1->set_row(6, 150,0); //set tinngi row ke-7
			
		$worksheet1->write_string($baris,$kolom,"No.",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"TANGGAL",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"NO MR",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"NAMA",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"USIA",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"CUSTOMER",$centerHeader);	$kolom++;
		//$worksheet1->write_string($baris,$kolom,"DIAGNOSA",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"TINDAKAN",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"TARIF",$centerHeader);	$kolom++;
		
		$baris++;
		$kolom = 0;
		$no = 1;
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$cm = $row['cm'];
			$no_trans_rwtjln = $row['no_trans_rwtjln'];
			$tgl_visit = $this->convertDate($row['tgl_visit'],'1');
			
			$tglLahir = PasienRecord::finder()->findByPk($cm)->tgl_lahir;			
			$umur = $this->dateDifference($tglLahir,$item->DataItem['tgl_visit']);
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr';
			
			if($this->getViewState('no_trans_rwtjln'))
			{
				if($no_trans_rwtjln != $this->getViewState('no_trans_rwtjln'))
				{
					$stWrite = '1';
				}
				else
				{
					$stWrite = '0';
				}
				$this->setViewState('no_trans_rwtjln',$no_trans_rwtjln);	
			}
			else
			{
				$stWrite = '1';
				$this->setViewState('no_trans_rwtjln',$no_trans_rwtjln);
			}
			
			if($stWrite == '1')
			{
				$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$tgl_visit,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$row['cm'],$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$row['nama'],$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$umur,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$row['penjamin'],$center);	$kolom++;
				//$worksheet1->write_string($baris,$kolom,$this->ambilDiagnosa($no_trans_rwtjln),$left);	$kolom++;
				$no++;	
			}
			else
			{
				$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,'',$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,'',$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,'',$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,'',$left);	$kolom++;
				//$worksheet1->write_string($baris,$kolom,'',$center);	$kolom++;
			}
			
			$worksheet1->write_string($baris,$kolom,$row['nm_tindakan'],$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['jml_total'],2,',','.'),$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$grandTot +=$row['jml_total'];
		}
		
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtRight =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRight,'b','1','10');
		$right= $this->AddFormat($frmtRight,'bd','1','');
		$right= $this->AddFormat($frmtRight,'HA','right','');
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'bd','1','');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		$wrap= $this->AddFormat($frmtWrap,'WR','1','');
		
		
		$worksheet1->write_string($baris,$kolom,"TOTAL",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		//$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		
		$worksheet1->merge_cells($baris, 0, $baris, 6);
		
		$worksheet1->write_string($baris,$kolom,number_format($grandTot,0,',','.'),$right); $kolom++;
		
		
		/*$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$baris++;$baris++;
		$worksheet1->write_string($baris,'9',' '.$this->kotaRs().', '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$center);	$kolom++;
		$baris++;$baris++;$baris++;
		$worksheet1->write_string($baris,'9',$operator,$center);	$kolom++;*/
		$workbook->close(); 
	}
	
	public function ambilDiagnosa($no_trans_rwtjln)
	{
		$sql = "SELECT kode_icd, st_icd_global FROM tbt_icd WHERE no_trans = '$no_trans_rwtjln' ";
		if($this->queryAction($sql,'S'))
		{
			$diagnosa = '';
			$jml = count($this->queryAction($sql,'S'));
			$i=1;
			foreach($this->queryAction($sql,'S') as $row)
			{
				$st = $row['st_icd_global'];
				$kode = $row['kode_icd'];
				
				if($jml==$i)
					$koma = '';
				else
					$koma = ', ';
					
				if($st == '1')
					$diagnosa .= IcdGlobalRecord::finder()->findByPk($kode)->indonesia.$koma;
				else
					$diagnosa .= IcdRecord::finder()->findByPk($kode)->indonesia.$koma;
					
				$i++;	
			}	
		}
		
		return $diagnosa;
	}
	
}
?>
