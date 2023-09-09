<?php
class cetakRekapIcdXls extends XlsGen
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{	
		$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$periode=$this->Request['periode'];
		$tipeRawat=$this->Request['tipeRawat'];
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakRekapIcd'];
		$file = 'RekapIcd.xls';
		
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
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','70',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		
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
		
		if($tipeRawat == '1')
		{		
			$worksheet1->write_string($baris,0,'REKAPITULASI ICD RAWAT JALAN' ,$frmtLeft);
		}
		elseif($tipeRawat == '2')
		{		
			$worksheet1->write_string($baris,0,'REKAPITULASI ICD RAWAT INAP' ,$frmtLeft);
		}
		else
		{		
			$worksheet1->write_string($baris,0,'REKAPITULASI ICD' ,$frmtLeft);
		}
		
		$baris++;
		$worksheet1->write_string($baris,0,$periode,$frmtLeft);
		$baris++;
		
		if($poli)
		{
			$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,$frmtLeft);
			$baris++;
		}

		if($dokter)
		{
			$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmtLeft);
			$baris++;
		}
				
		$baris++;
		$kolom = 0;
		//$worksheet1->set_row(6, 150,0); //set tinngi row ke-7
		
		$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Kode ICD",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Keterangan ICD",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Jumlah",$center); $kolom++;
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','1','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtRight =  & $workbook->add_format();
		$right= $this->AddFormat($frmtRight,'','1','10');
		$right= $this->AddFormat($frmtRight,'bd','1','');
		$right= $this->AddFormat($frmtRight,'HA','right','');
		
		$frmtLeftWrap =  & $workbook->add_format();
		$leftWrap= $this->AddFormat($frmtLeftWrap,'','1','10');
		$leftWrap= $this->AddFormat($frmtLeftWrap,'bd','1','');
		$leftWrap= $this->AddFormat($frmtLeftWrap,'HA','left','');
		$leftWrap= $this->AddFormat($frmtLeftWrap,'WR','1','');
		
		$baris++;
		$kolom = 0;
		$no = 1;
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['icd'],$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['inggris'],$leftWrap);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['jml'],$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$no++;
		}
		
		
		$workbook->close(); 
		
	}
}
?>
