<?php
class cetakLapHutangXls extends XlsGen
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
		$pbf=$this->Request['pbf'];
		$noFak=$this->Request['noFak'];
		$periode=$this->Request['periode'];
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		$session=new THttpSession;
		$session->open();
		
		$sql = $session['cetakLapHutang'];
		$sql .= " ORDER BY tgl_faktur ASC ";
		
		$file = 'LapHutangObat.xls';
		
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
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','30',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
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
		
		$worksheet1->write_string($baris,0,'LAPORAN HUTANG OBAT' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,'PEMERINTAH KOTA TANGERANG SELATAN' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,$periode,$frmtLeft);
		$baris++;
		
		if($pbf)
		{
			$worksheet1->write_string($baris,0,'PBF : '.PbfObatRecord::finder()->findByPk($pbf)->nama,$frmtLeft);
			$baris++;
		}
		
		if($noFak)
		{
			$worksheet1->write_string($baris,0,'No. Faktur : '.noFak,$frmtLeft);
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
		$worksheet1->write_string($baris,$kolom,"Tanggal",$centerHeader);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Supplier",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"No. Faktur",$centerHeader); $kolom++;		
		$worksheet1->write_string($baris,$kolom,"Saldo Awal",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Total",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Potongan",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"PPn",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Materai",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Keseluruhan",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Pembayaran",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Saldo Akhir",$centerHeader); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Keterangan / Tanggal Bayar",$centerHeader); $kolom++;
		
		$baris++;
		$kolom = 0;
		$no = 1;
					
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$noFak = $row['no_faktur'];
			$noPo = $row['no_po'];
			
			if($row['ppn'] == '1')
			{
				$ppn = 0.1 * $row['total'];
			}
			else
			{
				$ppn = 0;
			}
			
			if($row['materai'] == '1')
			{
				$mat = 3000;
			}
			elseif($row['materai'] == '2')
			{
				$mat = 6000;
			}
			else
			{
				$mat = 0;
			}
			
			$keseluruhan = ($row['total'] - $row['pot']) + $ppn + $mat;
			
			if($row['st_keuangan'] == '2')
			{				
				$jmlBayar = $keseluruhan;
				$pembayaran = number_format($keseluruhan,'2',',','.');
			}
			else
			{
				$jmlBayar = '0';
				$pembayaran = '';
			}
			
			if(BayarPbfRecord::finder()->find('no_po=? AND no_faktur=?',$noPo,$noFak)->tgl_bayar)
			{		
				$tglBayar = BayarPbfRecord::finder()->find('no_po=? AND no_faktur=?',$noPo,$noFak)->tgl_bayar;
				$tglBayar = $this->convertDate($tglBayar,'3');
			}			
			else
			{
				$tglBayar = '';
			}
			
			$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl_faktur'],'3'),$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['nm_pbf'],$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['no_faktur'],$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$saldoAwal,$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['total'],'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($row['pot'],'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($ppn,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($mat,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($keseluruhan,'2',',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$pembayaran,$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$saldoAkhir,$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$tglBayar,$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$no++;
			
			$totHutang += $row['total'];
			$totPotongan += $row['pot'];
			$totPpn += $ppn;
			$totMaterai += $mat;
			$totKeseluruhan += $keseluruhan;
			$totPembayaran += $jmlBayar;
			
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
		
		
		$worksheet1->write_string($baris,$kolom,"GRAND TOTAL",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		
		$worksheet1->merge_cells($baris, 0, $baris, 3);
		
		$worksheet1->write_string($baris,$kolom,'',$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totHutang,2,',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totPotongan,2,',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totPpn,2,',','.'),$right);	$kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totMaterai,2,',','.'),$right); $kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totKeseluruhan,2,',','.'),$right); $kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totPembayaran,2,',','.'),$right); $kolom++;
		$worksheet1->write_string($baris,$kolom,number_format($totKeseluruhan - $totPembayaran,2,',','.'),$right); $kolom++;
		$worksheet1->write_string($baris,$kolom,'',$right);	$kolom++;
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$baris++;$baris++;
		$worksheet1->write_string($baris,'9',' Bekasi, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),$center);	$kolom++;
		$baris++;$baris++;$baris++;
		$worksheet1->write_string($baris,'9',$operator,$center);	$kolom++;
		$workbook->close(); 
		
	}
}
?>
