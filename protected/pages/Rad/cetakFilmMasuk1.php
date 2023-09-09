<?php
class cetakFilmMasuk1 extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('6');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{	
		$noPO=$this->Request['noPO'];		
		$tgl=$this->Request['tgl'];		
		$noFak=$this->Request['noFak'];
		$mat=$this->Request['mat'];
		$ppn=$this->Request['ppn'];
		$pot=$this->Request['pot'];
		$modeTerima=$this->Request['modeTerima'];
		$nmTable=$this->Request['nmTable'];
		$st_rkbu_rtbu=$this->Request['st_rkbu_rtbu'];
				
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		if($modeTerima=='0')
		{
			$table = 'tbt_film_beli';
			$activeRecord = AsetBeliRecord::finder();
		}
		elseif($modeTerima=='1')
		{
			$table = 'tbt_film_beli_tunda';
			$activeRecord = AsetBeliTundaRecord::finder();
		}
		
		$sql = "SELECT st_pembelian FROM $table WHERE no_po='$noPO' GROUP BY  no_po";		
		$jnsBeli = $activeRecord->findBySql($sql)->st_pembelian;
		
		if($jnsBeli == '0')//PDF
		{
			$sql="SELECT pbf FROM $table WHERE no_po='$noPO' GROUP BY  no_po";
			$idPbf = $activeRecord->findBySql($sql)->pbf;
			$nmPbf = AsetSupplierRecord::finder()->findByPk($idPbf)->nama;	
			$alamatPbf = AsetSupplierRecord::finder()->findByPk($idPbf)->alamat;		
		}
		else
		{
			$sql="SELECT nm_apotik_luar FROM $table WHERE no_po='$noPO' GROUP BY  no_po";
			$nmApotik = $activeRecord->findBySql($sql)->nm_apotik_luar;
		}
		
		
		//$noKwitansi = substr($noTrans,6,6).'/'.'LAB-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
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
		
		//$pdf=new reportAdmBarang('L','mm','legal');
		//$pdf->txtFooter="Tanda Terima Barang";
		
		
		$pdf=new fpdf('L','mm','a4');
		//$pdf=new cetakKartu();
		$pdf->AliasNbPages(); 
		
		$sql1="SELECT
				tgl_jth_tempo
			FROM 
				tbt_film_masuk 
			WHERE 
				no_po='$noPO' GROUP BY tgl_jth_tempo";
		$arrData1=$this->queryAction($sql1,'R');//Select row in tabel bro...				
		$i=0;		
		foreach($arrData1 as $row)
		{
			
			$tglJatuh=$row['tgl_jth_tempo'];
			
			$pdf->AddPage();
			
			$pdf->SetLeftMargin(20);
						
			//$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
			$pdf->SetFont('Arial','B',10);
			$pdf->Cell(100,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L');
			//$pdf->Cell(0,5,'No. '.$noPO,'0',0,'R');
			$pdf->Ln(5);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'L');
			$pdf->Ln(5);
			$pdf->Cell(0,5,'Telp. (021) 74718440','0',0,'L');
			$pdf->Ln(10);
			
			$pdf->SetFont('Arial','B',12);
			
			//if($st_rkbu_rtbu == '0')
				$pdf->Cell(230,5,'TANDA TERIMA PENGADAAN BARANG RADIOLOGI','0',0,'C','',$this->Service->constructUrl('Rad.FilmMasuk'));
			//elseif($st_rkbu_rtbu == '1')
				//$pdf->Cell(230,5,'TANDA TERIMA ASET RTBU/RTPBU','0',0,'C','',$this->Service->constructUrl('Rad.FilmMasuk'));
	
			
			$pdf->Ln(8);		
			$pdf->SetFont('Arial','',9);
			/*
			$pdf->Cell(30,10,'No. PO',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(75,10,$noPO,0,0,'L');		
			
			$pdf->Cell(15,10,'Kepada',0,0,'L');
			$pdf->Cell(2,10,':  ',0,0,'L');
			$pdf->Cell(50,10,'',0,0,'L');
			$pdf->Ln(5);
			*/
			
			//$pdf->Cell(20,5,'P/O Date',0,0,'L');
			$pdf->Cell(20,5,'Date',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(140,5,$this->convertDate($tgl,'3'),0,0,'L');
			
			//$pdf->Cell(30,5,'Tgl. Jatuh Tempo',0,0,'L');
			//$pdf->Cell(2,5,':  ',0,0,'L');
			//$pdf->Cell(75,5,$this->convertDate($tglJatuh,'3'),0,0,'L');
			
			$pdf->Ln(5);
			
			if($jnsBeli == '0') //PBF
			{
				$pdf->Cell(20,5,'Vendor',0,0,'L');
				$pdf->Cell(2,5,':  ',0,0,'L');
				$pdf->Cell(140,5,$nmPbf,0,0,'L');
			}
			else
			{
				$pdf->Cell(20,5,'Apotik',0,0,'L');
				$pdf->Cell(2,5,':  ',0,0,'L');
				$pdf->Cell(140,5,$nmApotik,0,0,'L');
			}
			
			//if($st_rkbu_rtbu == '0')
				$pdf->Cell(30,5,'No. Kontrak',0,0,'L');
			//elseif($st_rkbu_rtbu == '1')
				//$pdf->Cell(30,5,'No. Kontrak',0,0,'L');	
				
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(75,5,$noPO,0,0,'L');
			
			$pdf->Ln(8);
				
			$pdf->Cell(20,5,'Divisi',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(140,5,'Radiologi',0,0,'L');
			
			//$pdf->Cell(30,5,'Transportation',0,0,'L');
			//$pdf->Cell(2,5,':  ',0,0,'L');
			//$pdf->Cell(75,5,'',0,0,'L');
				
			//Line break
			$pdf->Ln(8);
			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(10,5,'No.','1',0,'C');
			$pdf->Cell(215,5,'DESCRIPTION','1',0,'C');
			//$pdf->Cell(20,5,'UNIT','1',0,'C');
			$pdf->Cell(30,5,'Qty','1',0,'C');
			//$pdf->Cell(20,5,'Satuan Kecil','1',0,'C');
			//$pdf->Cell(20,5,'Qty Pending','1',0,'C');
			//$pdf->Cell(55,5,'UNIT PRICE','1',0,'C');
			//$pdf->Cell(55,5,'AMOUNT','1',0,'C');
			//$pdf->Cell(15,5,'% DISC.','1',0,'C');
			//$pdf->Cell(25,5,'DISC.','1',0,'C');
			//$pdf->Cell(50,5,'TOTAL','1',0,'C');
			//$pdf->Cell(35,5,'PABRIK','1',0,'C');
			
			$pdf->Ln(5);
			
			/*
			$sql="SELECT 
					  $table.id,
					  $table.no_po,
					  $table.tgl_po,
					  $table.waktu,
					  $table.pbf,
					  $table.kode,
					  tbt_film_masuk.jumlah AS jumlah_kecil,
					  tbt_film_masuk.jumlah_tunda_sisa,
					  tbt_film_masuk.discount,
					  $table.jumlah,
					  $table.catatan,
					  $table.petugas,
					  $table.flag,
					  $table.nm_apotik_luar,
					  $table.st_pembelian
					FROM
					  tbt_film_masuk
					  INNER JOIN $table ON (tbt_film_masuk.no_po = $table.no_po)
					WHERE $table.no_po='$noPO'  
					GROUP BY
					  tbt_film_masuk.tgl_exp
					ORDER BY tbt_film_masuk.id     ";
			*/
			
			$sql="SELECT 
					*, jml_terima AS jumlah_kecil, jml_terima_tunda AS jumlah_tunda_sisa
					FROM
					  $nmTable
					WHERE jml_terima > 0  
					GROUP BY
					  id, thn_pengadaan, depresiasi
					ORDER BY id     ";
					
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
			$j=0;
			foreach($arrData as $row)
			{
				$idBarang = $row['kode'];
				//$idSatuan = FilmRecord::finder()->findByPk($row['kode'])->satuan;
				//$satuan = SatuanFilmRecord::finder()->findByPk($idSatuan)->nama;
				$jmlSatBesar = FilmRecord::finder()->findByPk($idBarang)->jml_satuan_besar;
				
				//$idProdusen = FilmRecord::finder()->findByPk($row['kode'])->produsen;	
				//$produsen = ProdusenFilmRecord::finder()->findByPk($idProdusen)->nama;
				
				$stBeli = $row['st_pembelian'] ;
				
				if($stBeli == '0') //pembelian dari PBF
				{
					//$jml = $row['jumlah_kecil'] / $jmlSatBesar;
					//$jmlTunda = $row['jumlah_tunda_sisa'] / $jmlSatBesar;
					$jml = $row['jumlah_kecil'];
					$jmlTunda = $row['jumlah_tunda_sisa'];
				}
				else
				{
					$jml = $row['jumlah_kecil'];
					$jmlTunda = $row['jumlah_tunda_sisa'];
				}
				
				$disc = $row['disc'];
				
				//cari hrg,disc dari pembelian terakhir
				$sql2="SELECT 
						  tbt_film_masuk.no_po,
						  tbm_film.id,
						  tbm_film.nama,
						  tbt_film_masuk.jumlah,
						  tbt_film_masuk.jumlah_tunda_sisa,
						  tbt_film_masuk.hrg_nett,
						  tbt_film_masuk.hrg_disc,
						  tbt_film_masuk.hrg_ppn,
						  tbt_film_masuk.hrg_ppn_disc
						FROM
						  tbm_film
						  INNER JOIN tbt_film_masuk ON (tbm_film.id = tbt_film_masuk.id_barang)
						WHERE 
							tbm_film.id='$idBarang' 
							AND tbt_film_masuk.no_po='$noPO'  
							AND tbt_film_masuk.st_rkbu_rtbu='$st_rkbu_rtbu'  
						GROUP BY
						  tbt_film_masuk.tgl_exp, tbt_film_masuk.no_po
						ORDER BY
						  tbt_film_masuk.id DESC ";
				$arrData2 = $this->queryAction($sql2,'S');
				
				if($arrData2)
				{
					foreach($arrData2 as $row2)
					{
						if($disc > 0)
						{
							if($stBeli == '0') //pembelian dari PBF
							{	
								/*----------- FORMAT SATUAN BESAR -----------
								$hrgSatuan = $row2['hrg_nett'] * $jmlSatBesar;
								*/
								
								/*----------- FORMAT SATUAN BESAR -----------*/
								$hrgSatuan = $row2['hrg_nett'];
							}
							else
							{
								$hrgSatuan = $row2['hrg_nett'];
							}
							
						}
						else
						{
							if($stBeli == '0') //pembelian dari PBF
							{
								/*----------- FORMAT SATUAN BESAR -----------
								$hrgSatuan = $row2['hrg_nett'] * $jmlSatBesar;
								*/
								
								/*----------- FORMAT SATUAN BESAR -----------*/
								$hrgSatuan = $row2['hrg_nett'];
							}
							else
							{
								$hrgSatuan = $row2['hrg_nett'];
							}
						}
						
						if($stBeli == '0') //pembelian dari PBF
						{
							//$hrgTotal = $hrgSatuan * ($row['jumlah_kecil'] / $jmlSatBesar);
							$hrgTotal = $hrgSatuan * ($row['jumlah_kecil']);
						}
						else
						{
							$hrgTotal = $hrgSatuan * $row['jumlah_kecil'];
						}
						
						
						$jmlDisc = $hrgTotal * $disc / 100;
						
						$total = $hrgTotal - $jmlDisc;	
					}
				}
				else
				{
					$disc = '0';
					$hrgSatuan = '0';
					$hrgTotal = '0';
					$jmlDisc = 0;
					$total = 0;
				}
				
				$j += 1;
				$pdf->SetFont('Arial','',9);						
				$pdf->Cell(10,5,$j.'.','1',0,'C');
				$pdf->Cell(215,5,FilmRecord::finder()->findByPk($row['kode'])->nama,'1',0,'L');
				//$pdf->Cell(20,5,$satuan,'1',0,'C');
				
				
				if($stBeli == '0') //pembelian dari PBF
				{
					//$pdf->Cell(15,5,($row['jumlah_kecil'] / $jmlSatBesar),'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_kecil']),'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_tunda_sisa'] / $jmlSatBesar),'1',0,'C');
					$pdf->Cell(30,5,(number_format($row['jumlah_kecil'],'0',',','.')),'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_kecil'] * $jmlSatBesar),'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_tunda_sisa']),'1',0,'C');
				}
				else
				{
					$pdf->Cell(30,5,$row['jumlah_kecil'],'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_kecil']),'1',0,'C');
					//$pdf->Cell(20,5,($row['jumlah_tunda_sisa']),'1',0,'C');
				}
							
				//$pdf->Cell(55,5,number_format($hrgSatuan,'2',',','.'),'1',0,'R');
				//$pdf->Cell(55,5,number_format($hrgTotal,'2',',','.'),'1',0,'R');
				//$pdf->Cell(15,5,number_format($disc,'2',',','.').'%','1',0,'R');
				//$pdf->Cell(25,5,number_format($jmlDisc,'2',',','.'),'1',0,'R');
				//$pdf->Cell(50,5,number_format($total,'2',',','.'),'1',0,'R');
				//$pdf->Cell(35,5,$produsen,'1',0,'C');
				
				$pdf->Ln(5);
				
				$grandTot += $total;
				//$pdf->Ln(5);
				
			}
			
			if($ppn == '1') //jika ada ppn
			{
				$jmlPpn = $grandTot * 0.1;
			}
			else
			{
				$jmlPpn = 0;
			}
			
			if($mat == '0') //materai = 0
			{
				$jmlMat = 0;
			}
			elseif($mat == '1') //materai = 3000
			{
				$jmlMat = 3000;
			}
			elseif($mat == '2') //materai = 6000
			{
				$jmlMat = 6000;
			}
			
			
			$grandTotPpn = $grandTot + $jmlPpn + $jmlMat;
		
			$pdf->Ln(1);
			
			//$pdf->SetFont('Arial','B',9);	
			//$pdf->Cell(65,5,'TOTAL','1',0,'R');
			//$pdf->Cell(20,5,'','1',0,'C');
			//$pdf->Cell(15,5,'','1',0,'C');
			//$pdf->Cell(20,5,'','1',0,'C');
			//$pdf->Cell(30,5,'','1',0,'C');
			//$pdf->Cell(55,5,'','1',0,'R');
			//$pdf->Cell(55,5,'','1',0,'R');
			//$pdf->Cell(15,5,'','1',0,'R');
			//$pdf->Cell(25,5,'','1',0,'R');
			//$pdf->Cell(50,5,number_format($grandTot,'2',',','.'),'1',0,'R');
			//$pdf->Ln(5);
			
			/*$pdf->Cell(65,5,'PPN 10%','1',0,'R');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(15,5,'','1',0,'C');
			//$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(15,5,'','1',0,'R');
			$pdf->Cell(25,5,'','1',0,'R');
			$pdf->Cell(30,5,number_format($jmlPpn,'2',',','.'),'1',0,'R');
			$pdf->Ln(5);
			
			$pdf->Cell(65,5,'Materai','1',0,'R');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(15,5,'','1',0,'C');
			//$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(15,5,'','1',0,'R');
			$pdf->Cell(25,5,'','1',0,'R');
			$pdf->Cell(30,5,number_format($jmlMat,'2',',','.'),'1',0,'R');
			$pdf->Ln(5);
			
			$pdf->SetFont('Arial','B',9);	
			$pdf->Cell(65,5,'GRAND TOTAL','1',0,'R');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(15,5,'','1',0,'C');
			//$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(20,5,'','1',0,'C');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(35,5,'','1',0,'R');
			$pdf->Cell(15,5,'','1',0,'R');
			$pdf->Cell(25,5,'','1',0,'R');
			$pdf->Cell(30,5,number_format($grandTotPpn,'2',',','.'),'1',0,'R');
			*/
			$pdf->Ln(8);
			
			$pdf->SetFont('Arial','',9);
			$pdf->Cell(210,5,'',0,0,'C');
			$pdf->Cell(45,5,'Logistik',1,0,'C');
			$pdf->Ln(5);
			$pdf->Cell(210,5,'',0,0,'C');
			$pdf->Cell(45,15,'',1,0,'C');
			
		}
				
		/*
		$pdf->Ln(25);
		$pdf->SetFont('Arial','',9);	
		$pdf->MultiCell(0,5,$sql,0,'L');		
			*/						
		$pdf->Output();
						
	}
	
}
?>
