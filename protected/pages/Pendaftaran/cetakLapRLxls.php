<?php
class cetakLapRLxls extends XlsGen
{
	public function onLoad($param)
	{
		parent::onLoad($param);		
		$nmFile=$this->Request['file'];
		
		$tipeRawat=$this->Request['tipeRawat'];	
		$st_baru_lama=$this->Request['st_baru_lama'];	
		$kdIcd=$this->Request['kdIcd'];	
		$klinik=$this->Request['klinik'];	
		$dokter=$this->Request['dokter'];	
		
		$tgl=$this->Request['tgl'];
		$tglawal=$this->Request['tglawal'];
		$tglakhir=$this->Request['tglakhir'];
		$bln=$this->Request['bln'];
		$thn=$this->Request['tahun'];
		$thn2=$this->Request['tahun2'];
		
		$triwulan=$this->Request['triwulan'];
		$thn3=$this->Request['tahun3'];
		$periode=$this->Request['periode'];
		$rangeAwal=$this->Request['rangeAwal'];	
		$rangeAkhir=$this->Request['rangeAkhir'];	
		
		$this->setViewState('nmFile',$nmFile);
		$this->setViewState('tipeRawat',$tipeRawat);
		$this->setViewState('kdIcd',$kdIcd);
		$this->setViewState('klinik',$klinik);
		$this->setViewState('dokter',$dokter);
		$this->setViewState('tgl',$tgl);
		$this->setViewState('tglawal',$tglawal);
		$this->setViewState('tglakhir',$tglakhir);
		$this->setViewState('bln',$bln);
		$this->setViewState('thn',$thn);
		$this->setViewState('thn2',$thn2);
		
		$this->setViewState('triwulan',$triwulan);
		$this->setViewState('thn3',$thn3);
		$this->setViewState('periode',$periode);
		$this->setViewState('rangeAwal',$rangeAwal);
		$this->setViewState('rangeAkhir',$rangeAkhir);
		
		$this->text->Text="Cetak File ".$nmFile.".xls sekarang ?";	
	}
	
	public function cetakClicked($sender,$param)
	{		
		$nmFile=$this->getViewState('nmFile');
		
		$tipeRawat=$this->getViewState('tipeRawat');
		$st_baru_lama=$this->Request['st_baru_lama'];	
		$kdIcd=$this->getViewState('kdIcd');
		$klinik=$this->getViewState('klinik');
		$dokter=$this->getViewState('dokter');
		$tgl=$this->getViewState('tgl');
		$tglawal=$this->getViewState('tglawal');
		$tglakhir=$this->getViewState('tglakhir');
		$bln=$this->getViewState('bln');
		$thn=$this->getViewState('thn');
		$thn2=$this->getViewState('thn2');
		$triwulan=$this->getViewState('triwulan');
		$thn3=$this->getViewState('thn3');
		$periode=$this->getViewState('periode');
		$rangeAwal=$this->getViewState('rangeAwal');
		$rangeAkhir=$this->getViewState('rangeAkhir');
		
		$file = $nmFile . '.xls';
		
		$baris=0;
		
		//http headers	
		$this->HeaderingExcel($file);
		
		//membuat workbook
		$workbook=new Workbook("-");
		
		//membuat worksheet pertama
		$worksheet1= & $workbook->add_worksheet("Laporan 1");
		$this->AddWS($worksheet1,'c','9','0','0');
		$this->AddWS($worksheet1,'c','10','1','1');
		$this->AddWS($worksheet1,'c','15','2','2');
		$this->AddWS($worksheet1,'c','40','3','3');
		$this->AddWS($worksheet1,'c','10','4','4');
		$this->AddWS($worksheet1,'c','12','5','5');
		$this->AddWS($worksheet1,'c','10','6','6');
		$this->AddWS($worksheet1,'c','10','7','7');
		$this->AddWS($worksheet1,'c','10','8','8');
		$this->AddWS($worksheet1,'c','10','9','9');
		$this->AddWS($worksheet1,'c','10','10','10');
		$this->AddWS($worksheet1,'c','10','11','11');
		$this->AddWS($worksheet1,'c','10','12','12');
		$this->AddWS($worksheet1,'c','12','13','13');
		$this->AddWS($worksheet1,'c','12','14','14');
		$this->AddWS($worksheet1,'c','12','15','15');
		$this->AddWS($worksheet1,'c','12','16','16');	
		
		$frmtLeft =  & $workbook->add_format();
		$left= $this->AddFormat($frmtLeft,'b','1','10');
		$left= $this->AddFormat($frmtLeft,'bd','1','');
		$left= $this->AddFormat($frmtLeft,'HA','left','');
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'b','1','10');
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		
		if($tipeRawat == '0') //Rawat Jalan
		{
			$worksheet1->write_string($baris,0,'DATA KEADAAN MORBIDITAS PASIEN RAWAT JALAN RUMAH SAKIT' ,$wrap);
		}
		elseif($tipeRawat == '1') //Rawat Inap
		{
			$worksheet1->write_string($baris,0,'DATA KEADAAN MORBIDITAS PASIEN RAWAT INAP RUMAH SAKIT' ,$wrap);
		}
		
		$worksheet1->merge_cells($baris, 0, $baris, 16);
		$baris++;
		
		if($periode==='1')
		{
			$worksheet1->write_string($baris,0,'PERIODE : '.$this->ConvertDate($tgl,'3') ,$wrap);
		}
		elseif($periode==='2')
		{
			$worksheet1->write_string($baris,0,'PERIODE : '.$this->ConvertDate($tglawal,'3').' s.d. '.$this->ConvertDate($tglakhir,'3') ,$wrap);
		}
		elseif($periode==='3')
		{
			$worksheet1->write_string($baris,0,'BULAN : '.$this->namaBulan($bln).' TAHUN : '.$thn ,$wrap);
			
		}
		elseif($periode==='4')
		{	
			$worksheet1->write_string($baris,0,'TAHUN : '.$thn2,$wrap);
		}
		elseif($periode==='5')
		{	
			$worksheet1->write_string($baris,0,'TRIWULAN : '.$triwulan.' TAHUN : '.$thn3 ,$wrap);
		}
		else
		{
			$worksheet1->write_string($baris,0,'PERIODE : '.$this->namaBulan(date('m')).' '.date('Y'),$wrap);
		}
		
		$worksheet1->merge_cells($baris, 0, $baris, 16);
		$baris++;
		
		if($klinik)
		{
			$worksheet1->write_string($baris,0,'Poliklinik : '.PoliklinikRecord::finder()->findByPk($klinik)->nama,$frmt);
		}		
		
		$baris++;
		
		if($dokter)
		{
			$worksheet1->write_string($baris,0,'Dokter : '.PegawaiRecord::finder()->findByPk($dokter)->nama,$frmt);
		}
		
		
		$frmtWrap =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrap,'b','1','10');
		$wrap= $this->AddFormat($frmtWrap,'bd','1','');
		$wrap= $this->AddFormat($frmtWrap,'HA','center','');
		$wrap= $this->AddFormat($frmtWrap,'WR','1','');
		
		$baris += 3;
		$worksheet1->set_row(6, 63,0);
				
		$worksheet1->write_string($baris,0,"NO URUT",$center);	
		$worksheet1->write_string($baris,1,"NO DTD",$center);
		$worksheet1->write_string($baris,2,"NO. DAFTAR TERPERINCI",$wrap);
		$worksheet1->write_string($baris,3,"GOLONGAN SEBAB-SEBAB SAKIT",$center);
		
		if($tipeRawat == '0') //Rawat Jalan
		{
			$worksheet1->write_string($baris,4,"KASUS BARU MENURUT GOLONGAN UMUR",$wrap);
		}
		elseif($tipeRawat == '1') //Rawat Inap
		{
			$worksheet1->write_string($baris,4,"PASIEN KELUAR (HIDUP & MATI) MENURUT GOLONGAN UMUR",$wrap);
		}
		
		
		$worksheet1->write_string($baris,5,"",$center);	
		$worksheet1->write_string($baris,6,"",$center);	
		$worksheet1->write_string($baris,7,"",$center);	
		$worksheet1->write_string($baris,8,"",$center);	
		$worksheet1->write_string($baris,9,"",$center);	
		$worksheet1->write_string($baris,10,"",$center);	
		$worksheet1->write_string($baris,11,"",$center);
		
		if($tipeRawat == '0') //Rawat Jalan
		{
			$worksheet1->write_string($baris,12,"KASUS BARU MENURUT SEX",$wrap);
			$worksheet1->write_string($baris,13,"",$center);		
			$worksheet1->write_string($baris,14,"JUMLAH KASUS BARU (13+14)",$wrap);
			$worksheet1->write_string($baris,15,"JUMLAH KUNJUNGAN",$wrap);
			$worksheet1->write_string($baris,16,"NO URUT",$center);		
		}
		elseif($tipeRawat == '1') //Rawat Inap
		{
			$worksheet1->write_string($baris,12,"PASIEN KELUAR (HIDUP & MATI) MENURUT SEKS",$wrap);	
			$worksheet1->write_string($baris,13,"",$center);		
			$worksheet1->write_string($baris,14,"JUMLAH PASIEN KELUAR (13+14)",$wrap);
			$worksheet1->write_string($baris,15,"JUMLAH PASIEN KELUAR MATI",$wrap);
			$worksheet1->write_string($baris,16,"NO URUT",$center);	
		}
		
		$worksheet1->merge_cells(6, 0, 7, 0);
		$worksheet1->merge_cells(6, 1, 7, 1);
		$worksheet1->merge_cells(6, 2, 7, 2);
		$worksheet1->merge_cells(6, 3, 7, 3);
		
		$worksheet1->merge_cells(6, 4, 6, 11);
		$worksheet1->merge_cells(6, 12, 6, 13);
		$worksheet1->merge_cells(6, 14, 7, 14);
		$worksheet1->merge_cells(6, 15, 7, 15);
		$worksheet1->merge_cells(6, 16, 7, 16);
		
		$baris++;
		$worksheet1->write_string($baris,4,"0 - 28 Hari",$center);
		$worksheet1->write_string($baris,5,"28 Hr - <1 Th",$center);
		$worksheet1->write_string($baris,6,"1 - 4 Th",$center);
		$worksheet1->write_string($baris,7,"5 - 14 Th",$center);
		$worksheet1->write_string($baris,8,"15 - 24 Th",$center);
		$worksheet1->write_string($baris,9,"25 - 44 Th",$center);
		$worksheet1->write_string($baris,10,"45 - 64 Th",$center);
		$worksheet1->write_string($baris,11,"+65 Th",$center);
		
		$worksheet1->write_string($baris,12,"Lk",$center);
		$worksheet1->write_string($baris,13,"Pr",$center);
		$worksheet1->write_string($baris,14,"",$center);
		$worksheet1->write_string($baris,15,"",$center);
		$worksheet1->write_string($baris,16,"",$center);
		
		$baris++;
		
		for($i=1; $i<18; $i++){
			$worksheet1->write_string($baris,$i-1,$i,$center);
		}
		
		$baris++;
		/*
		 $sql="SELECT 
					tbm_icd.kode,
					tbm_icd.dtd,
					tbm_icd.indonesia,
					tbt_rawat_jalan.id_klinik,
					tbt_rawat_jalan.dokter,
					tbt_rawat_jalan.tgl_visit,
					tbd_pasien.tgl_lahir
				FROM
					tbd_pasien
					INNER JOIN tbt_rawat_jalan ON (tbd_pasien.cm = tbt_rawat_jalan.cm)
					INNER JOIN tbm_icd ON (tbt_rawat_jalan.icd = tbm_icd.kode)
				WHERE
					tbm_icd.kode <> '' ";
		
		if($kdIcd)
			$sql .= "AND tbm_icd.kode = '$kdIcd' ";	
		
		if($klinik)
			$sql .= "AND tbt_rawat_jalan.id_klinik = '$klinik' ";	
		
		if($dokter)
			$sql .= "AND tbt_rawat_jalan.dokter = '$dokter' ";
		
		if($periode!='')	
		{
			if($tgl <> '')			
				$sql .= "AND tbt_rawat_jalan.tgl_visit = '$tgl' ";
			
			if($tglawal <> '' AND $tglakhir <> '')			
				$sql .= "AND tbt_rawat_jalan.tgl_visit BETWEEN '$tglawal' AND '$tglakhir' ";
			
			if($bln <> '' AND $thn <> '')			
				$sql .= "AND MONTH (tbt_rawat_jalan.tgl_visit)='$bln' AND YEAR(tbt_rawat_jalan.tgl_visit)='$thn' ";
			
			if($thn2 <> '' )			
				$sql .= "AND YEAR(tbt_rawat_jalan.tgl_visit)='$thn2' ";	
		}
		else
		{
			$sql .= "AND MONTH (tbt_rawat_jalan.tgl_visit)=MONTH(NOW()) AND YEAR(tbt_rawat_jalan.tgl_visit)=YEAR(NOW()) ";
		}							
		*/
		
		if($periode != '3') //Bukan Periode Bulanan
		{
			$sql="SELECT DISTINCT tbm_icd.dtd,tbm_icd.kode, tbm_icd.indonesia 
				  FROM `tbm_icd`
				  GROUP BY tbm_icd.dtd ";
						/*if($periode!='')	
						{
							if($tgl <> '')			
								$sql .= "AND tbt_rawat_jalan.tgl_visit = '$tgl' ";
								
							if($tglawal <> '' AND $tglakhir <> '')			
								$sql .= "AND tbt_rawat_jalan.tgl_visit BETWEEN '$tglawal' AND '$tglakhir' ";
							
							if($bln <> '' AND $thn <> '')			
								$sql .= "AND MONTH (tbt_rawat_jalan.tgl_visit)='$bln' AND YEAR(tbt_rawat_jalan.tgl_visit)='$thn' ";
							
							if($thn2 <> '' )			
								$sql .= "AND YEAR(tbt_rawat_jalan.tgl_visit)='$thn2' ";	
							
							if($triwulan <> '' AND $thn3 <> '')			
							{
								if($triwulan == '1')
								{
									$sql .= "AND (MONTH (tbt_rawat_jalan.tgl_visit)='01' 
												OR MONTH (tbt_rawat_jalan.tgl_visit)='02'
												OR MONTH (tbt_rawat_jalan.tgl_visit)='03') ";
								}
								
								if($triwulan == '2')
								{
									$sql .= "AND (MONTH (tbt_rawat_jalan.tgl_visit)='04' 
												OR MONTH (tbt_rawat_jalan.tgl_visit)='05'
												OR MONTH (tbt_rawat_jalan.tgl_visit)='06') ";
								}
								
								if($triwulan == '3')
								{
									$sql .= "AND (MONTH (tbt_rawat_jalan.tgl_visit)='07' 
												OR MONTH (tbt_rawat_jalan.tgl_visit)='08'
												OR MONTH (tbt_rawat_jalan.tgl_visit)='09') ";
								}
								
								if($triwulan == '4')
								{
									$sql .= "AND (MONTH (tbt_rawat_jalan.tgl_visit)='10' 
												OR MONTH (tbt_rawat_jalan.tgl_visit)='11'
												OR MONTH (tbt_rawat_jalan.tgl_visit)='12') ";
								}
								
								$sql .= "AND YEAR(tbt_rawat_jalan.tgl_visit)='$thn3' ";
							}
						}
						else
						{
							$sql .= "AND MONTH (tbt_rawat_jalan.tgl_visit)=MONTH(NOW()) AND YEAR(tbt_rawat_jalan.tgl_visit)=YEAR(NOW()) ";
						}
			$sql .= " GROUP BY tbm_icd.dtd ";
			$sql .= " ORDER BY dtd ";*/
			//LIMIT $rangeAwal,$rangeAkhir";
		}
		else
		{
			$sql="SELECT 
				  tbm_icd.kode,
				  tbm_icd.dtd,
				  tbm_icd.indonesia
				FROM
				  tbm_icd
				  INNER JOIN tbm_icd_list_bulanan ON (tbm_icd.dtd = tbm_icd_list_bulanan.dtd_id)
				GROUP BY
				  tbm_icd.dtd
				ORDER BY
				  tbm_icd.dtd";	
		}
		
		$varTmp=$this->queryAction($sql,"R");
		
		$i=0;
		$totUmur1=0;
		$totUmur2=0;
		$totUmur3=0;
		$totUmur4=0;
		$totUmur5=0;
		$totUmur6=0;
		$totUmur7=0;
		$totUmur8=0;			
		
		
		$frmtCenter =  & $workbook->add_format();
		$center= $this->AddFormat($frmtCenter,'bd','1','');
		$center= $this->AddFormat($frmtCenter,'HA','center','');
		
		$frmtWrapLeft =  & $workbook->add_format();
		$wrap= $this->AddFormat($frmtWrapLeft,'bd','1','');
		$wrap= $this->AddFormat($frmtWrapLeft,'HA','left','');
		$wrap= $this->AddFormat($frmtWrapLeft,'WR','1','');
		
		//$j=$baris; //baris ke-7
		//if(count($varTmp)!=0)
		//{
			foreach($varTmp as $row)
			{
				$i++;
				
				$dtd=$row['dtd'];
				$kode=$row['kode'];
				
				/*$sqlIcd="SELECT 
						tbm_icd.kode
					FROM
						tbm_icd
					WHERE
						tbm_icd.dtd = '$dtd' ";
				$arrIcd = $this->queryAction($sqlIcd,"R");
				foreach($arrIcd as $rowIcd){
					$icdList .= $rowIcd['kode'].', ';
				}*/
				
				$worksheet1->write_string($baris,0,$i,$frmtCenter);
				$worksheet1->write_string($baris,1,$row['dtd'],$frmtCenter);
				//$worksheet1->write_string($baris,2,$icdList,$frmtWrapLeft);
				$worksheet1->write_string($baris,2,$kode,$frmtWrapLeft);
				$worksheet1->write_string($baris,3,$row['indonesia'],$frmtWrapLeft);	
				//$worksheet1->write_string($baris,3,'',$frmtWrapLeft);	
				
				//for($kolomJml=4; $kolomJml<17; $kolomJml++){
					//$worksheet1->write_string($baris,$kolomJml,'',$center);
				//}
				
				if($tipeRawat == '0') //Rawat Jalan
				{
							//$i++;
							
							//$worksheet1->write_string($baris,0,$i,$frmtCenter);
							//$worksheet1->write_string($baris,1,$row['dtd'],$frmtCenter);
							//$worksheet1->write_string($baris,2,'',$frmt3);
							//$worksheet1->write_string($baris,3,$row['indonesia'],$wrap);						
							
							
							$sql="SELECT COUNT(tbb_rawat_jalan.kd_dtd) AS jml
								  FROM tbb_rawat_jalan,tbd_pasien
								  WHERE tbb_rawat_jalan.kd_dtd='$dtd'
                                  AND tbd_pasien.cm = tbb_rawat_jalan.cm ";
							if($st_baru_lama != '3')//pilih pasein baru atau lama
							$sql .= "AND tbd_pasien.st_baru_lama = '$st_baru_lama' ";
							
						
						if($periode!='')	
						{
							if($tgl <> '')			
								$sql .= "AND tbb_rawat_jalan.tgl_trans_rwtjln = '$tgl' ";
							
							if($tglawal <> '' AND $tglakhir <> '')			
								$sql .= "AND tbb_rawat_jalan.tgl_trans_rwtjln BETWEEN '$tglawal' AND '$tglakhir' ";
							
							if($bln <> '' AND $thn <> '')			
								$sql .= "AND MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='$bln' AND YEAR(tbb_rawat_jalan.tgl_trans_rwtjln)='$thn' ";
							
							if($thn2 <> '' )			
								$sql .= "AND YEAR(tbb_rawat_jalan.tgl_trans_rwtjln)='$thn2' ";	
							
							if($triwulan <> '' AND $thn3 <> '')			
							{
								if($triwulan == '1')
								{
									$sql .= "AND (MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='01' 
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='02'
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='03') ";
								}
								
								if($triwulan == '2')
								{
									$sql .= "AND (MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='04' 
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='05'
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='06') ";
								}
								
								if($triwulan == '3')
								{
									$sql .= "AND (MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='07' 
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='08'
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='09') ";
								}
								
								if($triwulan == '4')
								{
									$sql .= "AND (MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='10' 
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='11'
												OR MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)='12') ";
								}
								
								$sql .= "AND YEAR(tbb_rawat_jalan.tgl_trans_rwtjln)='$thn3' ";
							}
						}
						else
						{
							$sql .= "AND MONTH (tbb_rawat_jalan.tgl_trans_rwtjln)=MONTH(NOW()) AND YEAR(tbb_rawat_jalan.tgl_trans_rwtjln)=YEAR(NOW()) ";
						}	
							
							$sql1 = $sql."AND tbb_rawat_jalan.st_umur = '1' ";		
							$umur1=0;
							$arrUmur=$this->queryAction($sql1,"S");
							foreach($arrUmur as $rowUmur){
								$umur1=$rowUmur['jml'];
							}
							
							$sql2 = $sql."AND tbb_rawat_jalan.st_umur = '2' ";
							$umur2=0;
							$arrUmur=$this->queryAction($sql2,"S");
							foreach($arrUmur as $rowUmur){
								$umur2=$rowUmur['jml'];
							}
							
							$sql3 = $sql."AND tbb_rawat_jalan.st_umur = '3' ";
							$umur3=0;
							$arrUmur=$this->queryAction($sql3,"S");
							foreach($arrUmur as $rowUmur){
								$umur3=$rowUmur['jml'];;
							}
							
							$sql4 = $sql."AND tbb_rawat_jalan.st_umur = '4' ";	
							$umur4=0;
							$arrUmur=$this->queryAction($sql4,"S");
							foreach($arrUmur as $rowUmur){
								$umur4=$rowUmur['jml'];;
							}
							
							$sql5 = $sql."AND tbb_rawat_jalan.st_umur = '5' ";	
							$umur5=0;
							$arrUmur=$this->queryAction($sql5,"S");
							foreach($arrUmur as $rowUmur){
								$umur5=$rowUmur['jml'];
							}
							
							$sql6 = $sql."AND tbb_rawat_jalan.st_umur = '6' ";	
							$umur6=0;
							$arrUmur=$this->queryAction($sql6,"S");
							foreach($arrUmur as $rowUmur){
								$umur6=$rowUmur['jml'];
							}
							
							$sql7 = $sql."AND tbb_rawat_jalan.st_umur = '7' ";	
							$umur7=0;
							$arrUmur=$this->queryAction($sql7,"S");
							foreach($arrUmur as $rowUmur){
								$umur7=$rowUmur['jml'];
							}
							
							$sql8 = $sql."AND tbb_rawat_jalan.st_umur = '8' ";	
							$umur8=0;
							$arrUmur=$this->queryAction($sql8,"S");
							foreach($arrUmur as $rowUmur){
								$umur8=$rowUmur['jml'];
							}
							
							$sql9 = $sql."AND tbd_pasien.jkel = '0' ";	
							$jmlLaki=0;
							$arrUmur=$this->queryAction($sql9,"S");
							foreach($arrUmur as $rowUmur){
								$jmlLaki=$rowUmur['jml'];
							}
							
							$sql10 = $sql."AND tbd_pasien.jkel = '1' ";	
							$jmlPerempuan=0;
							$arrUmur=$this->queryAction($sql10,"S");
							foreach($arrUmur as $rowUmur){
								$jmlPerempuan=$rowUmur['jml'];
							}
							
							$jmlSex = $jmlLaki + $jmlPerempuan;
						 
				}
				elseif($tipeRawat == '1') //Rawat Inap
				{
				
							//$i++;
							
							//$worksheet1->write_string($baris,0,$i,$frmtCenter);
							//$worksheet1->write_string($baris,1,$row['dtd'],$frmtCenter);
							//$worksheet1->write_string($baris,2,'',$frmt3);
							//$worksheet1->write_string($baris,3,$row['indonesia'],$wrap);						
							
							
							$sql="SELECT COUNT(tbt_rawat_inap.icd) AS jml
								  FROM `tbm_icd`,`tbt_rawat_inap`,tbd_pasien
								  WHERE tbm_icd.dtd='$dtd' AND tbt_rawat_inap.icd = tbm_icd.kode
								  AND tbd_pasien.cm = `tbt_rawat_inap`.cm AND
								  tbd_pasien.tgl_lahir <> '' AND 
								  tbd_pasien.tgl_lahir <> '0000-00-00' AND 
								  tbt_rawat_inap.tgl_masuk > tbd_pasien.tgl_lahir ";
							
							if($st_baru_lama != '3')//pilih pasein baru atau lama
							$sql .= "AND tbd_pasien.st_baru_lama = '$st_baru_lama' ";
							
						if($dokter)
							$sql .= "AND tbt_rawat_inap.dokter = '$dokter' ";
						
						if($periode!='')	
						{
							if($tgl <> '')			
								$sql .= "AND tbt_rawat_inap.tgl_masuk = '$tgl' ";
							
							if($tglawal <> '' AND $tglakhir <> '')			
								$sql .= "AND tbt_rawat_inap.tgl_masuk BETWEEN '$tglawal' AND '$tglakhir' ";
							
							if($bln <> '' AND $thn <> '')			
								$sql .= "AND MONTH (tbt_rawat_inap.tgl_masuk)='$bln' AND YEAR(tbt_rawat_inap.tgl_masuk)='$thn' ";
							
							if($thn2 <> '' )			
								$sql .= "AND YEAR(tbt_rawat_inap.tgl_masuk)='$thn2' ";	
							
							if($triwulan <> '' AND $thn3 <> '')			
							{
								if($triwulan == '1')
								{
									$sql .= "AND (MONTH (tbt_rawat_inap.tgl_masuk)='01' 
												OR MONTH (tbt_rawat_inap.tgl_masuk)='02'
												OR MONTH (tbt_rawat_inap.tgl_masuk)='03') ";
								}
								
								if($triwulan == '2')
								{
									$sql .= "AND (MONTH (tbt_rawat_inap.tgl_masuk)='04' 
												OR MONTH (tbt_rawat_inap.tgl_masuk)='05'
												OR MONTH (tbt_rawat_inap.tgl_masuk)='06') ";
								}
								
								if($triwulan == '3')
								{
									$sql .= "AND (MONTH (tbt_rawat_inap.tgl_masuk)='07' 
												OR MONTH (tbt_rawat_inap.tgl_masuk)='08'
												OR MONTH (tbt_rawat_inap.tgl_masuk)='09') ";
								}
								
								if($triwulan == '4')
								{
									$sql .= "AND (MONTH (tbt_rawat_inap.tgl_masuk)='10' 
												OR MONTH (tbt_rawat_inap.tgl_masuk)='11'
												OR MONTH (tbt_rawat_inap.tgl_masuk)='12') ";
								}
								
								$sql .= "AND YEAR(tbt_rawat_inap.tgl_masuk)='$thn3' ";
							}
						}
						else
						{
							$sql .= "AND MONTH (tbt_rawat_inap.tgl_masuk)=MONTH(NOW()) AND YEAR(tbt_rawat_inap.tgl_masuk)=YEAR(NOW()) ";
						}		
						
							$sql1 = $sql."AND to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir) BETWEEN 0 AND 28";		
							$umur1=0;
							$arr=$this->queryAction($sql1,"S");
							foreach($arr as $row3)
							{
							$umur1=$row3['jml'];
							}
							
							$sql2 = $sql."AND to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir) > 28 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) < 1";
							$umur2=0;
							$arr=$this->queryAction($sql2,"S");
							foreach($arr as $row3)
							{
							$umur2=$row3['jml'];
							}
							
							$sql3 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >=1 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) <= 4";
							$umur3=0;
							$arr=$this->queryAction($sql3,"S");
							foreach($arr as $row3)
							{
							$umur3=$row3['jml'];
							}
							
							$sql4 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >=5 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) <= 14";	
							$umur4=0;
							$arr=$this->queryAction($sql4,"S");
							foreach($arr as $row3)
							{
							$umur4=$row3['jml'];
							}
							
							$sql5 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >=15 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) <= 24";	
							$umur5=0;
							$arr=$this->queryAction($sql5,"S");
							foreach($arr as $row3)
							{
							$umur5=$row3['jml'];
							}
							
							$sql6 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >=25 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) <= 44";	
							$umur6=0;
							$arr=$this->queryAction($sql6,"S");
							foreach($arr as $row3)
							{
							$umur6=$row3['jml'];
							}
							
							$sql7 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >=45 AND 
											(date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) <= 64";	
							$umur7=0;
							$arr=$this->queryAction($sql7,"S");
							foreach($arr as $row3)
							{
							$umur7=$row3['jml'];
							}
							
							$sql8 = $sql."AND (date_format(from_days((to_days(tbt_rawat_inap.tgl_masuk) - to_days(tbd_pasien.tgl_lahir))), '%Y') + 0) >= 65";	
							$umur8=0;
							$arr=$this->queryAction($sql8,"S");
							foreach($arr as $row3)
							{
							$umur8=$row3['jml'];
							}
							
							$sql9 = $sql."AND tbd_pasien.jkel = '0'";	
							$jmlLaki=0;
							$arr=$this->queryAction($sql9,"S");
							foreach($arr as $row3)
							{
							$jmlLaki=$row3['jml'];
							}
							
							$sql10 = $sql."AND tbd_pasien.jkel = '1'";	
							$jmlPerempuan=0;
							$arr=$this->queryAction($sql10,"S");
							foreach($arr as $row3)
							{
							$jmlPerempuan=$row3['jml'];
							}
							$jmlSex = $jmlLaki + $jmlPerempuan; 
				}
				
				
				if($umur1==0)
					$umur1='';
				$worksheet1->write_string($baris,4,$umur1,$frmtCenter);
				
				if($umur2==0)
					$umur2='';
				$worksheet1->write_string($baris,5,$umur2,$frmtCenter);
				
				if($umur3==0)
					$umur3='';
				$worksheet1->write_string($baris,6,$umur3,$frmtCenter);
				
			
				if($umur4==0)
					$umur4='';
				$worksheet1->write_string($baris,7,$umur4,$frmtCenter);						
				
				if($umur5==0)
					$umur5='';
				$worksheet1->write_string($baris,8,$umur5,$frmtCenter);
			
				if($umur6==0)
					$umur6='';
				$worksheet1->write_string($baris,9,$umur6,$frmtCenter);
				
				if($umur7==0)
					$umur7='';
				$worksheet1->write_string($baris,10,$umur7,$frmtCenter);
				
				if($umur8==0)
					$umur8='';
				$worksheet1->write_string($baris,11,$umur8,$frmtCenter);
				
				if($jmlLaki==0)
					$jmlLaki='';
				$worksheet1->write_string($baris,12,$jmlLaki,$frmtCenter);
				
				if($jmlPerempuan==0)
					$jmlPerempuan='';
				$worksheet1->write_string($baris,13,$jmlPerempuan,$frmtCenter);
				
				if($jmlSex==0)
					$jmlSex='';
					
				$worksheet1->write_string($baris,14,$jmlSex,$frmtCenter);
				$worksheet1->write_string($baris,15,$jmlSex,$frmtCenter);
				$worksheet1->write_string($baris,16,$i,$frmtCenter);
				
				$totUmur1 += $umur1;
				$totUmur2 += $umur2;
				$totUmur3 += $umur3;
				$totUmur4 += $umur4;
				$totUmur5 += $umur5;
				$totUmur6 += $umur6;
				$totUmur7 += $umur7;
				$totUmur8 += $umur8;
				
				$totJmlLaki += $jmlLaki;
				$totJmlPerempuan += $jmlPerempuan;
				$totJmlSex += $jmlSex;
				
				$umur1 = 0;	
				$umur2 = 0;	
				$umur3 = 0;	
				$umur4 = 0;	
				$umur5 = 0;	
				$umur6 = 0;	
				$umur7 = 0;	
				$umur8 = 0;	
				$jmlLaki = 0;	
				$jmlPerempuan = 0;	
				$jmlSex = 0;	
					
				$baris++;					
			}			
			
		//}
		
		
		$worksheet1->set_row($baris, 25,0);
		
		$worksheet1->write_string($baris,0,'',$center);
		$worksheet1->write_string($baris,1,'JUMLAH',$frmtCenter);
		$worksheet1->write_string($baris,2,'',$frmtCenter);
		$worksheet1->write_string($baris,3,'',$frmtCenter);
		$worksheet1->write_string($baris,4,$totUmur1,$frmtCenter);
		$worksheet1->write_string($baris,5,$totUmur2,$frmtCenter);
		$worksheet1->write_string($baris,6,$totUmur3,$frmtCenter);
		$worksheet1->write_string($baris,7,$totUmur4,$frmtCenter);
		$worksheet1->write_string($baris,8,$totUmur5,$frmtCenter);
		$worksheet1->write_string($baris,9,$totUmur6,$frmtCenter);
		$worksheet1->write_string($baris,10,$totUmur7,$frmtCenter);
		$worksheet1->write_string($baris,11,$totUmur8,$frmtCenter);
		$worksheet1->write_string($baris,12,$totJmlLaki,$frmtCenter);
		$worksheet1->write_string($baris,13,$totJmlPerempuan,$frmtCenter);
		$worksheet1->write_string($baris,14,$totJmlSex,$frmtCenter);
		$worksheet1->write_string($baris,15,$totJmlSex,$frmtCenter);
		$worksheet1->write_string($baris,16,'',$frmtCenter);
		
		$worksheet1->merge_cells($baris, 1, $baris, 3);
		
		$workbook->close(); 
	}	
	
	public function kembaliClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.lapRL'));
	}	
		
	
}
?>
