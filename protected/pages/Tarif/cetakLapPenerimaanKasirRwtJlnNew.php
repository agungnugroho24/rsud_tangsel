<?php
class cetakLapPenerimaanKasirRwtJlnNew extends XlsGen
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
		
		$session=new THttpSession;
		$session->open();
		
		$sqlViewRwtJln = $session['cetakPenerimaanKasirRwtJln'];
		
		if($jnsLap == '1')//berdasarkan poli
		{
			$sqlViewRwtJln .= " ORDER BY no_trans ASC ";
		}
		else
		{
			$sqlViewRwtJln .= " ORDER BY tgl ASC ";
		}
		
		$file = 'LapPenerimaanRawatJalan.xls';
		
		//http headers	
		$this->HeaderingExcel($file);
		
		//membuat workbook
		$workbook=new Workbook("-");
		
		//membuat worksheet pertama
		$worksheet1= & $workbook->add_worksheet('Laporan 1');
		
		$baris=0;
		$kolom=0;
		
		if($jnsLap == '1')//berdasarkan poli
		{
			$sqlPoli = "SELECT * FROM tbm_poliklinik";
			$arrPoli = $this->queryAction($sqlPoli,'S');
			
			//set lebar tiap kolom
			$this->AddWS($worksheet1,'c','8',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','25',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			
			foreach($arrPoli as $row)
			{
				$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			}
			
			
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
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
			
			$worksheet1->write_string($baris,0,'LAPORAN PENERIMAAN RAWAT JALAN' ,$frmtLeft);
			$baris++;
			$worksheet1->write_string($baris,0,$periode,$frmtLeft);
			$baris++;
			
			if($kasir)
			{
				$worksheet1->write_string($baris,0,'Operator : '.UserRecord::finder()->findBy_nip($kasir)->real_name,$frmtLeft);
				$baris++;
			}
			
			if($dokter)
			{
				$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmtLeft);
				$baris++;
			}
			
			if($poli)
			{
				$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,$frmtLeft);
				$baris++;
			}
			
			$baris++;
			$kolom = 0;
			//$worksheet1->set_row(6, 150,0); //set tinngi row ke-7
			
			$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Tanggal",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Operator",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Retribusi",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Administrasi",$center); $kolom++;
			
			foreach($arrPoli as $row)
			{
				$worksheet1->write_string($baris,$kolom,"Poli ".$row['nama'],$wrap); $kolom++;
			}
			
			$worksheet1->write_string($baris,$kolom,"Obat",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Laboratorium",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Radologi",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Fisio",$center); $kolom++;
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
						
			$arrData=$this->queryAction($sqlViewRwtJln,'S');//Select row in tabel bro...	
			foreach($arrData as $row)
			{
				$no_trans = $row['no_trans'];
				$id_kasir = $row['kasir'];
				$jns_pasien = $row['jns_pasien'];
				$id_klinik = $row['id_klinik'];
				
				$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,UserRecord::finder()->find('nip=?',$id_kasir)->real_name,$left);	$kolom++;
				
				
				if($jns_pasien == 'non otc') //pasen rwt jalan biasa
				{					
					//----------- RET ----------// NON OTC
					$sql = "select 
							sum(view_retribusi.tarif) AS tarif
						  from 
							view_retribusi 
						  where 
							view_retribusi.no_trans='$no_trans'  ";				
					$ret = KasirPendaftaranRecord::finder()->findBySql($sql)->tarif;
					$worksheet1->write_string($baris,$kolom,number_format($ret,2,',','.'),$right);	$kolom++;
					
					//----------- ADM ----------// NON OTC
					$sql = "select 
								sum(tbt_kasir_rwtjln.total) AS total
							  from 
								(tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) 
							  where 
								(tbm_nama_tindakan.nama like '%pendaftaran%') 
								AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'AND tbt_kasir_rwtjln.st_flag='1' ";				
					$adm = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
					$worksheet1->write_string($baris,$kolom,number_format($adm,2,',','.'),$right);	$kolom++;
					
					//----------- POLI ----------// NON OTC
					foreach($arrPoli as $row)
					{
						$idPoli = $row['id'];
					
						$sql = "select 
								SUM(tbt_kasir_rwtjln.total) AS total
							  from 
								((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
							  where 
								(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
								AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
								AND tbt_kasir_rwtjln.st_flag='1'
								AND tbt_kasir_rwtjln.klinik='$idPoli' ";
								
						if($kasir!='')		
						{
							$sql .= " AND tbt_kasir_rwtjln.operator='$kasir'  "; 	
						}
						
						$arr = $this->queryAction($sql,'S');//Select row in tabel bro...			
						foreach($arr as $row)
						{	
							if($idPoli == '01'){$poli1 = $row['total'];}
							if($idPoli == '02'){$poli2 = $row['total'];}
							if($idPoli == '03'){$poli3 = $row['total'];}
							if($idPoli == '04'){$poli4 = $row['total'];}
							if($idPoli == '05'){$poli5 = $row['total'];}
							if($idPoli == '06'){$poli6 = $row['total'];}
							if($idPoli == '07'){$poli7 = $row['total'];}
							if($idPoli == '08'){$poli8 = $row['total'];}
							if($idPoli == '09'){$poli9 = $row['total'];}
							if($idPoli == '10'){$poli10 = $row['total'];}
							if($idPoli == '11'){$poli11 = $row['total'];}
							if($idPoli == '12'){$poli12 = $row['total'];}
							if($idPoli == '13'){$poli13 = $row['total'];}
							if($idPoli == '14'){$poli14 = $row['total'];}
							if($idPoli == '15'){$poli15 = $row['total'];}
							if($idPoli == '16'){$poli16 = $row['total'];}
							if($idPoli == '17'){$poli17 = $row['total'];}
							if($idPoli == '18'){$poli18 = $row['total'];}
							if($idPoli == '19'){$poli19 = $row['total'];}
							if($idPoli == '20'){$poli20 = $row['total'];}
							if($idPoli == '21'){$poli21 = $row['total'];}
							if($idPoli == '22'){$poli22 = $row['total'];}
							
						}
						
						$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
						$worksheet1->write_string($baris,$kolom,number_format(($poli),2,',','.'),$right);	$kolom++;
						$totPoliPerRow += $poli; 
					}
					
					
					//----------- OBAT ----------// NON OTC
					$sql = "SELECT sum(total) AS total FROM tbt_obat_jual WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
					$totalObat = ObatJualRecord::finder()->findBySql($sql)->total;
					
					$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_karyawan WHERE no_trans_rwtjln='$no_trans' AND flag='1'";
					$totalObat += ObatJualKaryawanRecord::finder()->findBySql($sql)->total;
					
					$worksheet1->write_string($baris,$kolom,number_format($totalObat,2,',','.'),$right);	$kolom++;
					
					//----------- LAB ----------// NON OTC
					$sql = "SELECT sum(harga) AS harga FROM tbt_lab_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
					$totalLab = LabJualRecord::finder()->findBySql($sql)->harga;
					
					$worksheet1->write_string($baris,$kolom,number_format($totalLab,2,',','.'),$right);	$kolom++;
					
					//----------- RAD ----------// NON OTC
					$sql = "SELECT sum(harga) AS harga FROM tbt_rad_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
					$totalRad = RadJualRecord::finder()->findBySql($sql)->harga;
								
					$worksheet1->write_string($baris,$kolom,number_format($totalRad,2,',','.'),$right);	$kolom++;			
					
					//----------- FISIO ----------// NON OTC
					$sql = "SELECT sum(harga) AS harga FROM tbt_fisio_penjualan WHERE no_trans_rwtjln='$no_trans' AND flag='1' ";
					$totalFisio = FisioJualRecord::finder()->findBySql($sql)->harga;
								
					$worksheet1->write_string($baris,$kolom,number_format($totalFisio,2,',','.'),$right);	$kolom++;
				}			
				elseif($jns_pasien == 'otc') //pasen rwt jalan bebas
				{
					//----------- OBAT ----------//  OTC
					$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
					$totalObat = ObatJualLainRecord::finder()->findBySql($sql)->total;
					
					$sql = "SELECT sum(total) AS total FROM tbt_obat_jual_lain_karyawan WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
					$totalObat += ObatJualLainKaryawanRecord::finder()->findBySql($sql)->total;
					
					
					//----------- LAB ----------//  OTC
					$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_lab_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
					$totalLab = LabJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
					$admLab = LabJualLainRecord::finder()->findBySql($sql)->harga_adm;
								
					
					//----------- RAD ----------//  OTC
					$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_rad_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1'";
					$totalRad = RadJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
					$admRad = RadJualLainRecord::finder()->findBySql($sql)->harga_adm;
								
					
					//----------- FISIO ----------//  OTC
					$sql = "SELECT sum(harga_non_adm) AS harga_non_adm,sum(harga_adm) AS harga_adm FROM tbt_fisio_penjualan_lain WHERE no_trans_rwtjln_lain='$no_trans' AND flag='1' ";
					$totalFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga_non_adm;
					$admFisio = FisioJualLainRecord::finder()->findBySql($sql)->harga_adm;
					
					//----------- ADM ----------//  OTC
					$adm = $admLab+$admRad+$admFisio;
					$worksheet1->write_string($baris,$kolom,number_format($adm,2,',','.'),$right);	$kolom++;
					
					//----------- POLI ----------// NON OTC
					foreach($arrPoli as $row)
					{
						$idPoli = $row['id'];
						
						if($idPoli == '01'){$poli1 = 0;}
						if($idPoli == '02'){$poli2 = 0;}
						if($idPoli == '03'){$poli3 = 0;}
						if($idPoli == '04'){$poli4 = 0;}
						if($idPoli == '05'){$poli5 = 0;}
						if($idPoli == '06'){$poli6 = 0;}
						if($idPoli == '07'){$poli7 = 0;}
						if($idPoli == '08'){$poli8 = 0;}
						if($idPoli == '09'){$poli9 = 0;}
						if($idPoli == '10'){$poli10 = 0;}
						if($idPoli == '11'){$poli11 = 0;}
						if($idPoli == '12'){$poli12 = 0;}
						if($idPoli == '13'){$poli13 = 0;}
						if($idPoli == '14'){$poli14 = 0;}
						if($idPoli == '15'){$poli15 = 0;}
						if($idPoli == '16'){$poli16 = 0;}
						if($idPoli == '17'){$poli17 = 0;}
						if($idPoli == '18'){$poli18 = 0;}
						if($idPoli == '19'){$poli19 = 0;}
						if($idPoli == '20'){$poli20 = 0;}
						if($idPoli == '21'){$poli21 = 0;}
						if($idPoli == '22'){$poli22 = 0;}
						
						$poli = 0;
						$worksheet1->write_string($baris,$kolom,number_format($poli,2,',','.'),$right);	$kolom++;
						$totPoliPerRow = 0;
					}
					
					$worksheet1->write_string($baris,$kolom,number_format($totalObat,2,',','.'),$right);	$kolom++;
					$worksheet1->write_string($baris,$kolom,number_format($totalLab,2,',','.'),$right);	$kolom++;
					$worksheet1->write_string($baris,$kolom,number_format($totalRad,2,',','.'),$right);	$kolom++;
					$worksheet1->write_string($baris,$kolom,number_format($totalFisio,2,',','.'),$right);	$kolom++;
					
				}
				
				$kolomAkhir = $kolom;
				$total = $ret + $adm + $totPoliPerRow + $totalObat + $totalLab + $totalRad + $totalFisio;
				$worksheet1->write_string($baris,$kolom,number_format($total,2,',','.'),$right);	$kolom++;
				
				$baris++;
				$kolom = 0;
				$no++;
				$totPoliPerRow = 0;
				
				$grandTotRet += $ret;
				$grandTotAdm += $adm;
				
				foreach($arrPoli as $row)
				{
					$idPoli = $row['id'];
									
					if($idPoli == '01'){$grandTotPerPoli1 += $poli1;}
					if($idPoli == '02'){$grandTotPerPoli2 += $poli2;}
					if($idPoli == '03'){$grandTotPerPoli3 += $poli3;}
					if($idPoli == '04'){$grandTotPerPoli4 += $poli4;}
					if($idPoli == '05'){$grandTotPerPoli5 += $poli5;}
					if($idPoli == '06'){$grandTotPerPoli6 += $poli6;}
					if($idPoli == '07'){$grandTotPerPoli7 += $poli7;}
					if($idPoli == '08'){$grandTotPerPoli8 += $poli8;}
					if($idPoli == '09'){$grandTotPerPoli9 += $poli9;}
					if($idPoli == '10'){$grandTotPerPoli10 += $poli10;}
					if($idPoli == '11'){$grandTotPerPoli11 += $poli11;}
					if($idPoli == '12'){$grandTotPerPoli12 += $poli12;}
					if($idPoli == '13'){$grandTotPerPoli13 += $poli13;}
					if($idPoli == '14'){$grandTotPerPoli14 += $poli14;}
					if($idPoli == '15'){$grandTotPerPoli15 += $poli15;}
					if($idPoli == '16'){$grandTotPerPoli16 += $poli16;}
					if($idPoli == '17'){$grandTotPerPoli17 += $poli17;}
					if($idPoli == '18'){$grandTotPerPoli18 += $poli18;}
					if($idPoli == '19'){$grandTotPerPoli19 += $poli19;}
					if($idPoli == '20'){$grandTotPerPoli20 += $poli20;}
					if($idPoli == '21'){$grandTotPerPoli21 += $poli21;}
					if($idPoli == '22'){$grandTotPerPoli22 += $poli22;}
				}
				
				/*
				foreach($arrPoli as $row)
				{
					$idPoli = $row['id'];
				
					$sql = "select 
							sum(tbt_kasir_rwtjln.total) AS total
						  from 
							((tbt_kasir_rwtjln join tbm_nama_tindakan on((tbt_kasir_rwtjln.id_tindakan = tbm_nama_tindakan.id))) join tbd_user on((tbt_kasir_rwtjln.operator = tbd_user.nip))) 
						  where 
							(not((tbm_nama_tindakan.nama like '%pendaftaran%')))
							AND tbt_kasir_rwtjln.no_trans_rwtjln='$no_trans'
							AND tbt_kasir_rwtjln.st_flag='1'
							AND tbt_kasir_rwtjln.klinik='$idPoli' ";
							
					$poli = KasirRwtJlnRecord::finder()->findBySql($sql)->total;
					$worksheet1->write_string($baris,$kolom,number_format(($poli),2,',','.'),$right);	$kolom++;
					$totPoliPerRow += $poli; 
				}
				*/
				
				$grandTotObat += $totalObat;
				$grandTotLab += $totalLab;
				$grandTotRad += $totalRad;
				$grandTotFisio += $totalFisio;
				
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
			
			$worksheet1->merge_cells($baris, 0, $baris, 2);
			
			$worksheet1->write_string($baris,$kolom,number_format($grandTotRet,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotAdm,2,',','.'),$right);	$kolom++;
			
			foreach($arrPoli as $row)
			{
				$idPoli = $row['id'];
				//if($idPoli=='01'){$grandTotPerPoli = $grandTotPerPoli.$idPoli;}
				
				if($idPoli == '01'){$grandTotPerPoli = $grandTotPerPoli1;}
				if($idPoli == '02'){$grandTotPerPoli = $grandTotPerPoli2;}
				if($idPoli == '03'){$grandTotPerPoli = $grandTotPerPoli3;}
				if($idPoli == '04'){$grandTotPerPoli = $grandTotPerPoli4;}
				if($idPoli == '05'){$grandTotPerPoli = $grandTotPerPoli5;}
				if($idPoli == '06'){$grandTotPerPoli = $grandTotPerPoli6;}
				if($idPoli == '07'){$grandTotPerPoli = $grandTotPerPoli7;}
				if($idPoli == '08'){$grandTotPerPoli = $grandTotPerPoli8;}
				if($idPoli == '09'){$grandTotPerPoli = $grandTotPerPoli9;}
				if($idPoli == '10'){$grandTotPerPoli = $grandTotPerPoli10;}
				if($idPoli == '11'){$grandTotPerPoli = $grandTotPerPoli11;}
				if($idPoli == '12'){$grandTotPerPoli = $grandTotPerPoli12;}
				if($idPoli == '13'){$grandTotPerPoli = $grandTotPerPoli13;}
				if($idPoli == '14'){$grandTotPerPoli = $grandTotPerPoli14;}
				if($idPoli == '15'){$grandTotPerPoli = $grandTotPerPoli15;}
				if($idPoli == '16'){$grandTotPerPoli = $grandTotPerPoli16;}
				if($idPoli == '17'){$grandTotPerPoli = $grandTotPerPoli17;}
				if($idPoli == '18'){$grandTotPerPoli = $grandTotPerPoli18;}
				if($idPoli == '19'){$grandTotPerPoli = $grandTotPerPoli19;}
				if($idPoli == '20'){$grandTotPerPoli = $grandTotPerPoli20;}
				if($idPoli == '21'){$grandTotPerPoli = $grandTotPerPoli21;}
				if($idPoli == '22'){$grandTotPerPoli = $grandTotPerPoli22;}
				
				$worksheet1->write_string($baris,$kolom,number_format(($grandTotPerPoli),2,',','.'),$right);	$kolom++;
				$grandTotAllPoli += $grandTotPerPoli;
			}
			
			$worksheet1->write_string($baris,$kolom,number_format($grandTotObat,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotLab,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotRad,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotFisio,2,',','.'),$right);	$kolom++;
			
			$grandTot = $grandTotRet + $grandTotAdm + $grandTotAllPoli + $grandTotObat + $grandTotLab + $grandTotRad + $grandTotFisio ;
			$worksheet1->write_string($baris,$kolom,number_format($grandTot,2,',','.'),$right);	$kolom++;
		}

//-------------------------------------- BERDASARKAN CARABAYAR --------------------------------		
		else
		{
			$sqlPoli = "SELECT * FROM tbm_carabayar";
			$arrPoli = $this->queryAction($sqlPoli,'S');
			
			//set lebar tiap kolom
			$this->AddWS($worksheet1,'c','8',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','15',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
			$this->AddWS($worksheet1,'c','20',$baris,$kolom); $baris++; $kolom++;
			
			foreach($arrPoli as $row)
			{
				$this->AddWS($worksheet1,'c','12',$baris,$kolom); $baris++; $kolom++;
			}
			
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
			
			$worksheet1->write_string($baris,0,'LAPORAN PENERIMAAN RAWAT JALAN' ,$frmtLeft);
			$baris++;
			$worksheet1->write_string($baris,0,$periode,$frmtLeft);
			$baris++;
			
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
			
			
			if($dokter)
			{
				$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmtLeft);
				$baris++;
			}
			
			if($poli)
			{
				$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($poli)->nama,$frmtLeft);
				$baris++;
			}
			
			$baris++;
			$kolom = 0;
			//$worksheet1->set_row(6, 150,0); //set tinngi row ke-7
			
			$worksheet1->write_string($baris,$kolom,"No.",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Tanggal",$center);	$kolom++;
			$worksheet1->write_string($baris,$kolom,"Rekam Medis",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Nama",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Poliklinik",$center); $kolom++;
			$worksheet1->write_string($baris,$kolom,"Dokter",$center); $kolom++;
			
			foreach($arrPoli as $row)
			{
				$worksheet1->write_string($baris,$kolom,$row['nama'],$wrap); $kolom++;
			}
			
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
						
			$arrData=$this->queryAction($sqlViewRwtJln,'S');//Select row in tabel bro...	
			foreach($arrData as $row)
			{
				$noTransAsal = $row['no_trans_asal'];
				$jns_pasien = $row['tipe_pasien'];
				$cm = $row['cm'];
				
				if( $jns_pasien == '0')//rawat jalan
				{
					$nmPas = PasienRecord::finder()->findByPk($cm)->nama;
					$nmPoli = $row['nm_poli'];
					$nmDokter = $row['nm_dokter'];
				}
				elseif( $jns_pasien == '2' || $jns_pasien == '3')//umum / umum karyawan
				{
					$nmPas = PasienLuarRecord::finder()->findByPk($noTransAsal)->nama;
					$nmPoli = '-';
					$nmDokter = '-';
				}
				
				$worksheet1->write_string($baris,$kolom,$no,$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$this->convertDate($row['tgl'],'3'),$center);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$cm,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmPas,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmPoli,$left);	$kolom++;
				$worksheet1->write_string($baris,$kolom,$nmDokter,$left);	$kolom++;
				
				$worksheet1->write_string($baris,$kolom,number_format($row['jml_tunai'],2,',','.'),$right);	$kolom++;
				$worksheet1->write_string($baris,$kolom,number_format($row['jml_debitcard'],2,',','.'),$right);	$kolom++;
				$worksheet1->write_string($baris,$kolom,number_format($row['jml_creditcard'],2,',','.'),$right);	$kolom++;
				$worksheet1->write_string($baris,$kolom,number_format($row['jml_jamper'],2,',','.'),$right);	$kolom++;
				$worksheet1->write_string($baris,$kolom,number_format($row['jml_nv'],2,',','.'),$right);	$kolom++;
				
				$total = $row['jml_tunai'] + $row['jml_debitcard'] + $row['jml_creditcard'] + $row['jml_jamper'] + $row['jml_nv'];
				$worksheet1->write_string($baris,$kolom,number_format($total,2,',','.'),$right);	$kolom++;
				
				$baris++;
				$kolom = 0;
				$no++;
				$total = 0;
				
				$grandTotTunai += $row['jml_tunai'];
				$grandTotDebit += $row['jml_debitcard'];
				$grandTotCredit += $row['jml_creditcard'];
				$grandTotJamper += $row['jml_jamper'];
				$grandTotNv += $row['jml_nv'];
				
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
			
			$worksheet1->merge_cells($baris, 0, $baris, 5);
			
			$worksheet1->write_string($baris,$kolom,number_format($grandTotTunai,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotDebit,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotCredit,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotJamper,2,',','.'),$right);	$kolom++;
			$worksheet1->write_string($baris,$kolom,number_format($grandTotNv,2,',','.'),$right);	$kolom++;
			
			$grandTot = $grandTotTunai + $grandTotDebit + $grandTotCredit + $grandTotJamper + $grandTotNv ;
			$worksheet1->write_string($baris,$kolom,number_format($grandTot,2,',','.'),$right);	$kolom++;
		}
		
		
		
		$workbook->close(); 
		
		/*			
		
		
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(95,5,'GRAND TOTAL',1,0,'C');		
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotAdm,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotPoli,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotObat,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotLab,2,',','.'),1,0,'R');	
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotRad,2,',','.'),1,0,'R');
		$pdf->Cell(40,5,'Rp. '.number_format($grandTotFisio,2,',','.'),1,0,'R');	
		$pdf->Ln(10);
				
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(550,8,'Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(550,8,'Kasir,',0,0,'C');	
		
		$pdf->Ln(15);
		
		$pdf->SetFont('Arial','BU',11);	
		$pdf->Cell(550,8,'('.$this->User->IsUserName.')',0,0,'C','',$this->Service->constructUrl('Tarif.LapPenerimaanKasirRwtJln'));
			
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',11);	
		$pdf->Cell(550,8,'NIP. '.$this->User->IsUserNip,0,0,'C');	
		
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
		*/
	}
}
?>
