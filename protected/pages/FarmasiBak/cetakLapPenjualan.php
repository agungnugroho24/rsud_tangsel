<?php
class cetakLapPenjualan extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{	
		
		$noTrans=$this->Request['notrans'];		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$cariNama=$this->Request['cariByNama'];
		$cariID=$this->Request['cariByID'];
		$cariGol=$this->Request['cariByGol'];
		$cariJenis=$this->Request['cariByJenis'];
		$cariKlas=$this->Request['cariByKlas'];
		$cariDerivat=$this->Request['cariByDerivat'];
		$cariPbf=$this->Request['cariByPbf'];
		$cariProd=$this->Request['cariByProd'];
		$cariSat=$this->Request['cariBySat'];
		$sumber=$this->Request['sumber'];			
		$tujuan=$this->Request['tujuan'];
		$klinik=$this->Request['id_klinik'];
		$dokter=$this->Request['id_dokter'];
		$kelompok=$this->Request['kelompok'];
		$kontrak=$this->Request['kontrak'];
		$tgl=$this->Request['cariTgl'];
		$tglAwal=$this->Request['cariTglAwal'];
		$tglAkhir=$this->Request['cariTglAkhir'];
		$bulan=$this->Request['bulan'];
		$tahun=$this->Request['tahun'];
		$tipe=$this->Request['tipe'];
		$rawat=$this->Request['rawat'];
		/*
		$jmlTagihan=number_format($this->Request['jmlTagihan'],2,',','.');
		$nmTable=$this->Request['table'];
		$nmPasien=$this->Request['nama'];
		$dokter=$this->Request['dokter'];
		$cm=$this->Request['cm'];	
		$sayTerbilang=ucwords($this->terbilang($this->Request['jmlTagihan']) . ' rupiah');
		*/
		$noKwitansi = substr($noTrans,6,6).'/'.'APT-';
		$noKwitansi .= substr($noTrans,4,2).'/';
		$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		//Update tabel tbt_pembayaran
		/*
		$byrRecord=BayarRecord::finder()->findByPk($noTrans);
		$byrRecord->status_pembayaran='2';//Sudah dibayar!
		$byrRecord->tgl_bayar=date('Y-m-d h:m:s');
		$byrRecord->no_kwitansi=$noKwitansi;
		$byrRecord->operator=$nipTmp;
		$byrRecord->Save();//Update!
		*/
		
		$pdf=new reportKwitansi();
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
		$pdf->Ln(3);		
		$pdf->SetFont('Arial','BU',10);
		$pdf->Cell(0,5,'RINCIAN PENJUALAN OBAT/ALKES','0',0,'C');
		$pdf->Ln(5);	
		/*	
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(0,5,'No. Kuitansi: '.$noKwitansi,'0',0,'C');
		$pdf->Ln(5);			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. Register',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$noTrans,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'No. CM',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$cm,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Nama Pasien',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$nmPasien,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Dokter',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(30,10,$dokter,0,0,'L');
		*/			
		//Line break
		$pdf->Ln(12);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(20,5,'Beli',1,0,'C');
		$pdf->Cell(20,5,'Jual',1,0,'C');
		$pdf->Cell(30,5,'Pendapatan',1,0,'C');
		/*
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(30,5,'Harga',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(35,5,'Total',1,0,'C');	
		*/	
		//Line break
		$pdf->Ln(6);
		
		$sql = "SELECT a.kode AS kode,
						   a.nama AS nama,						   		  
						   a.pbf AS pbf,						  
						   a.satuan AS sat,
						   d.hrg_ppn AS beli,
						   b.hrg AS jual,
						    ";
			if($sumber <> '')
			{
				$sql .= " b.jumlah AS jumlah,
						 (b.total-(b.jumlah * d.hrg_ppn)) AS untung,
						   b.sumber AS sumber ";				
			}else{
				$sql .= " SUM(b.jumlah) AS jumlah,
						  (b.total - (SUM(b.jumlah) * d.hrg_ppn)) AS untung,					
						   b.sumber AS sumber ";
			}			   

			if($rawat == '0')
			{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND d.kode=a.kode ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND
								c.cm=b.cm  
								AND d.kode=a.kode ";
					}
				}
			}else{
				if($tujuan<>'')
				{
					if ($tujuan<>'1')
					{
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND b.tujuan='$tujuan' 
								AND c.cm=b.cm
								AND d.kode=a.kode ";
					}else{						   
					$sql .=	" FROM tbm_obat a,							
								tbt_obat_jual_inap b,
								tbd_pasien c,
								tbt_obat_harga d
							WHERE	 							
								a.kode=b.id_obat AND
								c.cm=b.cm  
								AND d.kode=a.kode ";
					}
				}
			}	
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND a.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND a.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND a.kode = '$cariID' ";
						
			if($cariGol <> '')			
				$sql .= "AND a.gol = '$cariGol' ";
			
			if($cariJenis <> '')			
				$sql .= "AND a.kategori = '$cariJenis' ";
			
			if($tipe <> '')			
				$sql .= "AND a.tipe = '$tipe' ";		
			
			if($cariKlas <> '')					
				$sql .= "AND a.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND a.derivat = '$cariDerivat' ";
				$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND a.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND a.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND a.satuan = '$cariSat' ";
			
			if($sumber <> '')			
				$sql .= "AND b.sumber = '$sumber' ";
			
			if($klinik <> '')			
				$sql .= "AND b.klinik = '$klinik' ";	
			
			if($dokter <> '')			
				$sql .= "AND b.dokter = '$dokter' ";	
			
			if($kelompok <> '')			
				$sql .= "AND c.kelompok = '$kelompok' ";
			
			if($kontrak <> '')			
				$sql .= "AND c.perusahaan = '$kontrak' ";	
			
			if($tgl <> '')			
				$sql .= "AND b.tgl = '$tgl' ";
				
			if($tglAwal <> '' AND $tglAkhir <> '')			
				$sql .= "AND b.tgl BETWEEN '$tglAwal' AND '$tglAkhir' ";							
			
			if($bulan <> '')			
				$sql .= "AND MONTH(b.tgl) = '$bulan' AND YEAR(b.tgl)='$tahun' ";	
			
			$sql .= " GROUP BY a.kode ";
					
		/*$sql="SELECT nama, hrg,jml,total FROM $nmTable ORDER BY id";
		$pdf->Cell(10,5,'No.',1,0,'C');
		$pdf->Cell(80,5,'Obat',1,0,'C');
		$pdf->Cell(20,5,'Jumlah',1,0,'C');
		$pdf->Cell(20,5,'Beli',1,0,'C');
		$pdf->Cell(20,5,'Jual',1,0,'C');
		$pdf->Cell(20,5,'Pendapatan',1,0,'C');
		SELECT a.kode AS kode,
			   a.nama AS nama,						   		  
			   a.pbf AS pbf,						  
			   a.satuan AS sat,
			   d.hrg_ppn AS beli,
			   b.hrg AS jual,	*/
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		$j=0;
		$n=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(10,5,$j.'.',1,0,'C');
			$pdf->Cell(80,5,$row['nama'],1,0,'L');				
			$pdf->Cell(20,5,$row['jumlah'],1,0,'R');	
			$pdf->Cell(20,5,'Rp ' . number_format($row['beli'],2,',','.'),1,0,'R');	
			$pdf->Cell(20,5,'Rp ' . number_format($row['jual'],2,',','.'),1,0,'R');	
			$pdf->Cell(30,5,'Rp ' . number_format($row['untung'],2,',','.'),1,0,'R');	
			$pdf->Ln(5);
			$grandTot +=$row['untung'];
			
		}				
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'            Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'L');
		$pdf->SetFont('Arial','',9);						
		$pdf->Cell(70,5,'Total ',0,0,'R');		
		$pdf->Cell(30,5,'Rp ' .number_format( $grandTot,2,',','.'),1,0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);		
		$pdf->Cell(80,8,'                Petugas Apotik,',0,0,'L');	
		$pdf->Ln(15);	
		$pdf->SetFont('Arial','BU',9);	
		//$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.penjualanObat') . '&purge=1&nmTable=' . $nmTable);
		$pdf->Cell(53,8,'('.$operator.')',0,0,'C','',$this->Service->constructUrl('Apotik.LapPenjualan'));		
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);	
		$pdf->Cell(53,8,'NIP. '.$nip,0,0,'C');									
		$pdf->Output();
		//Purge data on temporary table
		//$this->queryAction($nmTable,'D');//Droped the table
	}
	
}
?>