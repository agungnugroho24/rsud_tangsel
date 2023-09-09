<?php
class cetakDaftarLapRLXls extends XlsGen
{
	public function onLoad($param)
	{
		parent::onLoad($param);		
		$nmFile=$this->Request['file'];
		$cariByAnggaran=$this->Request['cariByAnggaran'];
		$cariByThn=$this->Request['cariByThn'];
		$cariByWil=$this->Request['cariByWil'];
		$cariByBag=$this->Request['cariByBag'];
		$cariByLok=$this->Request['cariByLok'];
		$cariByKel=$this->Request['cariByKel'];
		$cariByJns=$this->Request['cariByJns'];
		$cariByDet=$this->Request['cariByDet'];			
		
		$this->setViewState('nmFile',$nmFile);
		$this->setViewState('cariByAnggaran',$cariByAnggaran);
		$this->setViewState('cariByThn',$cariByThn);
		$this->setViewState('cariByWil',$cariByWil);
		$this->setViewState('cariByBag',$cariByBag);
		$this->setViewState('cariByLok',$cariByLok);
		$this->setViewState('cariByKel',$cariByKel);
		$this->setViewState('cariByJns',$cariByJns);
		$this->setViewState('cariByDet',$cariByDet);
		
		$this->text->Text="Cetak File ".$nmFile.".xls sekarang ?";	
	}
	
	public function cetakClicked($sender,$param)
	{		
		//$nmTable=$this->getViewState('nmTable');								
		//$file = $this->getViewState('nmFile') . '.xls';
		
		
		$nmFile=$this->getViewState('nmFile');
		$cariByAnggaran=$this->getViewState('cariByAnggaran');
		$cariByThn=$this->getViewState('cariByThn');
		$cariByWil=$this->getViewState('cariByWil');
		$cariByBag=$this->getViewState('cariByBag');
		$cariByLok=$this->getViewState('cariByLok');
		$cariByKel=$this->getViewState('cariByKel');
		$cariByJns=$this->getViewState('cariByJns');
		$cariByDet=$this->getViewState('cariByDet');
		
		$file = $nmFile . '.xls';
		
		//http headers	
		$this->HeaderingExcel($file);
		
		//membuat workbook
		$workbook=new Workbook("-");			
		
		
		//membuat worksheet pertama
		$worksheet1= & $workbook->add_worksheet("Laporan 1");
		$this->AddWS($worksheet1,'c','5','0','0');
		$this->AddWS($worksheet1,'c','15','1','1');
		$this->AddWS($worksheet1,'c','15','2','2');
		$this->AddWS($worksheet1,'c','15','3','3');
		$this->AddWS($worksheet1,'c','15','4','4');
		$this->AddWS($worksheet1,'c','20','5','5');
		$this->AddWS($worksheet1,'c','30','6','6');
		$this->AddWS($worksheet1,'r','12','1','');		
		
		if($cariByAnggaran!='')
		{
			//$worksheet1->merge_cells(0,0,0,3);												
			$worksheet1->write_string(0,0,$cariByAnggaran==='0'? 'Jenis Anggaran : APBD':'Jenis Anggaran : Perubahan',$frmt);
		}
		
		if($cariByThn!='')
		{
			//$worksheet1->merge_cells(1,0,1,3);												
			$worksheet1->write_string(1,0,"Tahun Anggaran : ".$cariByThn,$frmt);	
		}
		
		if($cariByWil!='')
		{
			//$worksheet1->merge_cells(2,0,2,3);												
			$worksheet1->write_string(2,0,"Wilayah : ".WilRecord::finder()->findByPk($cariByWil)->nama,$frmt);	
		}
		
		if($cariByBag!='')
		{
			//$worksheet1->merge_cells(3,0,3,3);												
			$worksheet1->write_string(3,0,"Bagian : ".BagRecord::finder()->findByPk($cariByBag)->nama,$frmt);
		}
		
		if($cariByLok!='')
		{
			//$worksheet1->merge_cells(4,0,4,3);												
			$worksheet1->write_string(4,0,"Lokasi : ".LokasiRecord::finder()->findByPk($cariByLok)->nama." ".LokasiRecord::finder()->findByPk($cariByLok)->alamat,$frmt);	
		}
		
		
		$format =  & $workbook->add_format();
		$frmt= $this->AddFormat($format,'b','1','12');
		$frmt= $this->AddFormat($format,'bd','1','');
		$frmt= $this->AddFormat($format,'HA','center','');
		
		$worksheet1->write_string(6,0,"No.",$frmt);	
		$worksheet1->write_string(6,1,"ID Barang",$frmt);
		$worksheet1->write_string(6,2,"Kelompok",$frmt);
		$worksheet1->write_string(6,3,"Jenis",$frmt);
		$worksheet1->write_string(6,4,"Jenis Detail",$frmt);
		$worksheet1->write_string(6,5,"Nama Barang",$frmt);	
		$worksheet1->write_string(6,6,"Keterangan",$frmt);	
		
		$sql = "SELECT *,
						a.id AS id_1,
						a.ket AS keterangan,
						b.nama AS wil,
						c.nama AS bag,
						d.nama AS lok,
						e.nama AS brg,
						f.nama AS kel_brg,
						g.nama AS jns,
						h.nama AS det					   
				   FROM tbt_asset_plan_brg a,
						tbm_asset_wilayah b,
						tbm_asset_bagian c,
						tbm_asset_lokasi d,
						tbm_asset_barang e,
						tbm_asset_kelompok f,
						tbm_asset_jenis g,
						tbm_asset_jenis_detil h
				   WHERE 
						a.wilayah=b.id AND
						a.bagian=c.id AND
						a.lokasi=d.id AND
						a.id_brg=e.id AND
						e.kel=f.id AND						
						g.id=h.id AND
						a.st=1
						 ";
						 
			if($cariByAnggaran <> '')	
						$sql .= "AND jns_anggaran = '$cariByAnggaran' ";
			
			if($cariByThn <> '')
					$sql .= "AND thn_anggaran = '$cariByThn' ";
					
			if($cariByWil <> '')
					$sql .= "AND wilayah = '$cariByWil' ";
				
			if($cariByBag <> '')
					$sql .= "AND bagian = '$cariByBag' ";
				
			if($cariByLok <> '')
					$sql .= "AND lokasi = '$cariByLok' ";
			
			if($cariByKel <> '')
					$sql .= "AND kel = '$cariByKel' ";
				
			if($cariByJns <> '')
					$sql .= "AND jenis = '$cariByJns' ";
			
			if($cariByDet <> '')
					$sql .= "AND jenis_detil = '$cariByDet' ";
			
			$sql .= " GROUP BY id_1 ORDER BY id_1";
			
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		
		$format2 =  & $workbook->add_format();				
		$frmt= $this->AddFormat($format2,'bd','1','');
		$frmt2= $this->AddFormat($format2,'HA','Left','');
		$frmt2= $this->AddFormat($format2,'HA','top','');
		$frmt2= $this->AddFormat($format2,'WR','1','');
						
		$format3 =  & $workbook->add_format();
		$frmt= $this->AddFormat($format3,'bd','1','');
		$frmt3= $this->AddFormat($format3,'HA','Center','');
		$frmt3= $this->AddFormat($format3,'HA','top','');
		
		$format4 =  & $workbook->add_format();
		$frmt= $this->AddFormat($format4,'bd','1','');
		$frmt4= $this->AddFormat($format4,'HA','Right','');
		$frmt4= $this->AddFormat($format4,'HA','top','');
		
		
		$i=7;
		$no=1;				
		foreach($arrData as $row)
		{													
			$worksheet1->write_string($i,0,$no,$frmt3);
			$worksheet1->write_string($i,1,$row['id_brg'],$frmt3);
			$worksheet1->write_string($i,2,$row['kel_brg'],$frmt3);
			
			if($row['kel']==2)
			{
				$worksheet1->write_string($i,3,$row['jns'],$frmt3);
				$worksheet1->write_string($i,4,$row['det'],$frmt3);
				
				$worksheet1->write_string($i,3,JenisRecord::finder()->findByPk($row['jenis'])->nama,$frmt3);
				$worksheet1->write_string($i,4,JenisDetilRecord::finder()->find('id_jen = ?',$row['jenis_detil'])->nama,$frmt3);
			
			}
			else
			{
				$worksheet1->write_string($i,3,'-',$frmt3);
				$worksheet1->write_string($i,4,'-',$frmt3);
			}
			
			$worksheet1->write_string($i,5,$row['brg'],$frmt2);
			$worksheet1->write_string($i,6,$row['keterangan'],$frmt2);
			$i++;	
			$no++;			
		}	
		
		$workbook->close(); 
	}	
	
	public function kembaliClicked($sender,$param)
	{	
		$this->Response->redirect($this->Service->constructUrl('rencana.laporanDkbmd'));
	}	
	
}
?>
