<?php
class cetakStokOpnameXls extends XlsGen
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{	
		//$nmTable=$this->Request['table'];
		$tujuan=$this->Request['tujuan'];
		$idObat=$this->Request['idObat'];
		
		$periode=$this->Request['periode'];
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakStokOpname'];
		$sql .= " ORDER BY nm_obat ASC, tujuan ASC ";
		
		$file = 'LapStokOpname.xls';
		
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
		$this->AddWS($worksheet1,'c','35',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','10',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		
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
		
		$worksheet1->write_string($baris,0,'Stock Opname Obat & Alkes' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,'PEMERINTAH KOTA TANGERANG SELATAN' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,$periode,$frmtLeft);
		$baris++;
		
		if($tujuan)
		{
			$worksheet1->write_string($baris,0,'Stok Dari : '.DesFarmasiRecord::finder()->findByPk($tujuan)->nama,$frmtLeft);
			$baris++;
		}
		
		if($idObat)
		{
			$worksheet1->write_string($baris,0,'Kode Obat : '.idObat,$frmtLeft);
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
		$worksheet1->write_string($baris,$kolom,"Kode Obat / Alkes",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Nama Obat / Alkes",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Saldo Awal",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"M",$centerHeader); $kolom++;		
		$worksheet1->write_string($baris,$kolom,"K",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"S",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"SON",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Selisih",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Harga Beli",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Harga Jual",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Persediaan",$centerHeader); $kolom++;
		
		$baris++;
		$kolom = 0;
		$no = 1;
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$selisih = $row['jml_son'] - $row['saldo'] ;
			$persediaan = $row['jml_son'] * $row['hrg_jual'] ;
			
			$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['id_obat'],$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['nm_obat'],$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['saldo_awal'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['jml_masuk'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['jml_keluar'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['saldo'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['jml_son'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($selisih,'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['hrg_jual'],'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['hrg_jual'] * 1.3,'0',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($persediaan,'0',',','.'),$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$no++;
			
			$totSedia += $persediaan;
			
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
		
		
		$worksheet1->write_string($baris,$kolom,"TOTAL HARGA PEMBELIAN/PERSEDIAAN",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		
		$worksheet1->merge_cells($baris, 0, $baris, 10);
		
		$worksheet1->write_string($baris,$kolom,number_format($totSedia,0,',','.'),$right); $kolom++;
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$baris++;$baris++;
		$worksheet1->write_string($baris,'9',' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$center);	$kolom++;
		$baris++;$baris++;$baris++;
		$worksheet1->write_string($baris,'9',$operator,$center);	$kolom++;
		$workbook->close(); 
		
	}
}
?>
