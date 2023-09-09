<?php
class cetakLapJasmedRwtJlnXls extends XlsGen
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
	{	
		//$nmTable=$this->Request['table'];
		$jnsLap=$this->Request['jnsLap'];
		$kasir=$this->Request['kasir'];
		$poli=$this->Request['poli'];
		$dokter=$this->Request['dokter'];
		$periode=$this->Request['periode'];
		$jns_pasien = $this->Request['tipe_pasien'];
			
		$session=new THttpSession;
		$session->open();
		
		$sqlAwal = $session['cetakLapJasmedRwtJln'];
		$sqlGroupJmlLevel = $sqlAwal." GROUP BY jns_tindakan, tbt_kasir_rwtjln.id_tindakan, tbm_fraksi_jasmed.jml_level ORDER BY tbm_fraksi_jasmed.jml_level, tbm_fraksi_jasmed.jml_fraksi ASC ";		
		$sqlData = $sqlAwal." GROUP BY
					tbt_kasir_rwtjln.no_trans,
					tbt_kasir_rwtjln.no_trans_rwtjln,
					tbt_kasir_rwtjln.id_tindakan ORDER BY no_trans ASC ";
		
		$file = 'LapJasmedRawatJalan.xls';
		
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
		$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
		$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
		
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
		
		$worksheet1->write_string($baris,0,'LAPORAN JASA MEDIS RAWAT JALAN' ,$frmtLeft);
		$baris++;
		$worksheet1->write_string($baris,0,$periode,$frmtLeft);
		$baris++;
		
		/*
		if($kasir)
		{
			$worksheet1->write_string($baris,0,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,$frmtLeft);
			$baris++;
		}
		else
		{
			$worksheet1->write_string($baris,0,'Operator : Semua',$frmtLeft);
			$baris++;
		}
		*/
		
		if($dokter)
		{
			$worksheet1->write_string($baris,0,'Pelaksana : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmtLeft);
			$baris++;
		}
		
		if($poli)
		{
			$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,$frmtLeft);
			$baris++;
		}
		
		$baris++;
		$kolom = 0;
			
		$arrDataGroupJmlLevel = $this->queryAction($sqlGroupJmlLevel,'S');//Select row in tabel bro...	
		foreach($arrDataGroupJmlLevel as $rowGroupJmlLevel)
		{
			$jmlLevel = $rowGroupJmlLevel['jml_level'];
			$idTdk = $rowGroupJmlLevel['id_tindakan'];
			$jnsTdk = $rowGroupJmlLevel['jns_tindakan'];
			
			$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Tanggal",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Rekam Medis",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Nama",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Poliklinik",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Pelaksana",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Tindakan",$center); $kolom++;
			
			for($barisMerge=0;$barisMerge<7;$barisMerge++)
			{
				$worksheet1->merge_cells($baris, $barisMerge, $baris+$jmlLevel-1, $barisMerge);	
			}
			//$baris = $baris + $level;
			
			for($level=1;$level<=$jmlLevel;$level++)
			{				
				//$jmlFraksi = FraksiJasmedRecord::finder()->find('level=? AND id_tindakan=? AND jns_tindakan=?',array($level,$row['id_tindakan'],$row['jns_tindakan']))->sub_level;
				//$jmlFraksi = FraksiJasmedRecord::finder()->find('level=? AND id_tindakan=? AND jns_tindakan=?',array($level,$rowGroupJmlLevel['id_tindakan'],$rowGroupJmlLevel['jns_tindakan']))->jml_fraksi;	
				
				$sqlFraksi = "SELECT * FROM tbm_fraksi_jasmed WHERE jns_tindakan='$jnsTdk' AND id_tindakan='$idTdk' AND level='$level'";
				$arrFraksi = $this->queryAction($sqlFraksi,'S');
				foreach($arrFraksi as $rowFraksi)
				{
					$idPerLevel = $rowFraksi['id_per_level'];
										
					$sqlFraksi2 = "SELECT * FROM tbm_fraksi_jasmed WHERE jns_tindakan='$jnsTdk' AND id_tindakan='$idTdk' AND level='$level' AND id_per_level='$idPerLevel' ORDER BY id_per_level";
					$arrFraksi2 = $this->queryAction($sqlFraksi2,'S');
					foreach($arrFraksi2 as $rowFraksi2)
					{
						$nama = $rowFraksi2['nama'];
						$jmlFraksi = $rowFraksi2['jml_fraksi'];
						
						
						$worksheet1->write_string($baris,$kolom,$nama,$center);
						//$worksheet1->merge_cells($baris, $kolom, $baris, $kolom+$jmlFraksi);	
						$kolom++;
					}
						
				}
				
				$baris++;
				$kolom = 7;
				/*if($jmlLevel>1)
						$baris = $baris + $level;
				*/		
				//$subLevel = FraksiJasmedRecord::finder()->find('level=? AND id_tindakan=? AND jns_tindakan=?',array($level,$idTdk,$jnsTdk))->sub_level;					
				//$nama = FraksiJasmedRecord::finder()->find('level=? AND id_tindakan=? AND jns_tindakan=?',array($level,$idTdk,$jnsTdk))->nama;	
				
				//$worksheet1->write_string($baris,$kolom,$nama,$center); $kolom++;
			}
			
			//$baris++;
			$kolom = 0;
			
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
			
			$no = 1;
			
			$sql = $sqlAwal." AND tbt_kasir_rwtjln.id_tindakan = '$idTdk' 
							  AND tbm_fraksi_jasmed.jml_level = '$jmlLevel' 
							GROUP BY
								tbt_kasir_rwtjln.no_trans,
								tbt_kasir_rwtjln.no_trans_rwtjln,
								tbt_kasir_rwtjln.id_tindakan ORDER BY no_trans ASC ";
					
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$noTransAsal = $row['no_trans'];
				$cm = $row['cm'];
				
				if( $jns_pasien == '0')//rawat jalan
				{
					$nmPas = PasienRecord::finder()->findByPk($cm)->nama;
					$nmPoli = $row['nm_poli'];
					$nmDokter = $row['nm_peg'];
				}
				elseif( $jns_pasien == '2' || $jns_pasien == '3')//umum / umum karyawan
				{
					$nmPas = PasienLuarRecord::finder()->findByPk($noTransAsal)->nama;
					$nmPoli = '-';
					$nmDokter = '-';
				}
				
				$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$cm,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmPas,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmPoli,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmDokter,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$row['tindakan'],$left);	$kolom++;
				
				$total = $row['total'];				
				
				for($level=1;$level<=$jmlLevel;$level++)
				{				
					$sqlFraksi = "SELECT * FROM tbm_fraksi_jasmed WHERE jns_tindakan='$jnsTdk' AND id_tindakan='$idTdk' AND level='$level' ORDER BY id_per_level";
					$arrFraksi = $this->queryAction($sqlFraksi,'S');
					foreach($arrFraksi as $rowFraksi)
					{
						$idPerLevel = $rowFraksi['id_per_level'];
						$jmlFraksi = $rowFraksi['jml_fraksi'];
						//$nama = $rowFraksi['nama'];
						$persentase = $rowFraksi['persentase'];
						//$totalTmp = "$total * $persentase / 100";
						$totalTmp = $total * $persentase / 100;
						//$worksheet1->write_string($baris,$kolom,$totalTmp,$center); $kolom++;
						
						$levelTmp = $level + 1;
						
						if($levelTmp <= $jmlLevel && $jmlLevel > 1 && $jmlFraksi > 0 )
						{
							$sqlFraksi2 = "SELECT * FROM tbm_fraksi_jasmed WHERE jns_tindakan='$jnsTdk' AND id_tindakan='$idTdk' AND level='$levelTmp' AND sub_level='$idPerLevel' ORDER BY id_per_level";
							$arrFraksi2 = $this->queryAction($sqlFraksi2,'S');
							foreach($arrFraksi2 as $rowFraksi2)
							{
								$persentase2 = $rowFraksi2['persentase'];	
								$totalTmp2 = $totalTmp * $persentase2 / 100;
								$worksheet1->write_string($baris,$kolom,$totalTmp2,$right); $kolom++;
							}
						}
						elseif($jmlLevel == 1 && $jmlFraksi == 0 )
						{
							$worksheet1->write_string($baris,$kolom,$totalTmp,$right); $kolom++;	
						}
					}
				}
				
				
				//$worksheet1->write_string($baris,$kolom,number_format($total,2,',','.'),$right);	$kolom++;	
				
				$baris++;
				$kolom = 0;
				$no++;
				$grandTot += $row['total'];
			}
			
			$frmtLeft =  & $workbook->add_format();
			$left= $this->AddFormat($frmtLeft,'b','1','10');
			$left= $this->AddFormat($frmtLeft,'bd','0','');
			$left= $this->AddFormat($frmtLeft,'HA','left','');
			
			$frmtRight =  & $workbook->add_format();
			$right= $this->AddFormat($frmtRight,'b','1','10');
			$right= $this->AddFormat($frmtRight,'bd','1','');
			$right= $this->AddFormat($frmtRight,'HA','right','');
			
			$frmtCenter =  & $workbook->add_format();
			$center= $this->AddFormat($frmtCenter,'b','1','10');
			$center= $this->AddFormat($frmtCenter,'bd','1','');
			$center= $this->AddFormat($frmtCenter,'HA','center','');
			
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
			
			$worksheet1->merge_cells($baris, 0, $baris, 6);
			
			$worksheet1->write_string($baris,$kolom,number_format($grandTot,2,',','.'),$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$no = 1;
			$grandTot =0;
			
			$baris++;
			$kolom = 0;
			
			
			
		}
		
		
		/*
		$baris++;
		$kolom = 0;
		//$worksheet1->set_row(6, 150,0); //set tinngi row ke-7
		
		$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Tanggal",$center);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"Rekam Medis",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Nama",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Poliklinik",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Pelaksana",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Tindakan",$center); $kolom++;
		$worksheet1->write_string($baris,$kolom,"Total",$center); $kolom++;
		
		
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
		
		$baris++;
		$kolom = 0;
		$no = 1;
					
		$arrData=$this->queryAction($sqlData,'S');//Select row in tabel bro...	
		foreach($arrData as $row)
		{
			$noTransAsal = $row['no_trans'];
			$cm = $row['cm'];
			
			if( $jns_pasien == '0')//rawat jalan
			{
				$nmPas = PasienRecord::finder()->findByPk($cm)->nama;
				$nmPoli = $row['nm_poli'];
				$nmDokter = $row['nm_peg'];
			}
			elseif( $jns_pasien == '2' || $jns_pasien == '3')//umum / umum karyawan
			{
				$nmPas = PasienLuarRecord::finder()->findByPk($noTransAsal)->nama;
				$nmPoli = '-';
				$nmDokter = '-';
			}
			
			$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$cm,$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$nmPas,$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$nmPoli,$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$nmDokter,$left);	$kolom++;
			$worksheet1->write_string($baris,$kolom,$row['tindakan'],$left);	$kolom++;
			
			$total = $row['total'];
			$worksheet1->write_string($baris,$kolom,number_format($total,2,',','.'),$right);	$kolom++;
			
			$baris++;
			$kolom = 0;
			$no++;
			$total = 0;
			
			$grandTot += $row['total'];
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
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		$worksheet1->write_string($baris,$kolom,"",$wrap);	$kolom++;
		
		$worksheet1->merge_cells($baris, 0, $baris, 6);
		
		$worksheet1->write_string($baris,$kolom,number_format($grandTot,2,',','.'),$right);	$kolom++;
		
		//$grandTot = $grandTotTunai + $grandTotDebit + $grandTotCredit + $grandTotJamper + $grandTotNv ;
		//$worksheet1->write_string($baris,$kolom,number_format($grandTot,2,',','.'),$right);	$kolom++;
		*/
		$workbook->close(); 
	}
}
?>
