<?php
class cetakAsetBeli extends SimakConf
{

	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
	public function onLoad($param)
	{	
		
		$noPO=$this->Request['noPO'];		
		$tgl=$this->Request['tgl'];		
		$jnsBayar=$this->Request['jnsBayar'];
		$jnsBeli=$this->Request['jnsBeli'];
		$pbf=$this->Request['pbf'];
		$nmApotik=$this->Request['nmApotik'];
		$stRkbu=$this->Request['stRkbu'];
		
		if($this->Request['stRkbu'] == '0')
			$url = $this->Service->constructUrl('Asset.AsetBeli');
		elseif($this->Request['stRkbu'] == '1')	
			$url = $this->Service->constructUrl('Asset.AsetBeliRtpbu');
		
		$st_rkbu_rtbu = $this->Request['stRkbu'];
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
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
		
		$pdf=new fpdf('L','mm','a4');
		//$pdf=new cetakKartu();
		$pdf->AliasNbPages(); 
		$pdf->AddPage();
		
		//$pdf->Image('protected/pages/Tarif/logo1.jpg',10,12,45);	
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(100,5,'PEMERINTAH KOTA TANGERANG SELATAN','0',0,'L');
		$pdf->Cell(0,5,'No. '.$noPO,'0',0,'R');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(0,5,'JL. Pajajaran No. 101 Pamulang','0',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(0,5,'Telp. (021) 74718440','0',0,'L');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',12);
		
		if($stRkbu == '0')
			$pdf->Cell(0,5,'RKBU / RKPBU','0',0,'C','',$url);
		elseif($stRkbu == '1')	
			$pdf->Cell(0,5,'RTBU / RTPBU ','0',0,'C','',$url);
			
		

		
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
		$pdf->Cell(20,5,'Date',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		
		if($stRkbu == '0')
			$pdf->Cell(75,5,$this->convertDate($tgl,'3'),0,0,'L');
		elseif($stRkbu == '1')	
			$pdf->Cell(75,5,substr($tgl,0,4),0,0,'L');
			
		/*$pdf->Cell(25,5,'Payment',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		
		if($jnsBayar == '0') //COD
		{
			$pdf->Cell(75,5,'COD',0,0,'L');
		}
		else //Kredit
		{
			$pdf->Cell(75,5,'Kredit',0,0,'L');
		}*/
		
		
		
		$pdf->Ln(5);
		
		if($jnsBeli == '0') //PBF
		{
			$pdf->Cell(20,5,'Vendor',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(75,5,AsetSupplierRecord::finder()->findByPk($pbf)->nama,0,0,'L');
		}
		else
		{
			$pdf->Cell(20,5,'Apotik',0,0,'L');
			$pdf->Cell(2,5,':  ',0,0,'L');
			$pdf->Cell(75,5,$nmApotik,0,0,'L');
		}
		
		/*$pdf->Cell(25,5,'Delivery Date',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(75,5,'',0,0,'L');*/
		
		$pdf->Ln(5);
			
		$pdf->Cell(20,5,'Divisi',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(75,5,'Gudang Umum',0,0,'L');
		
		/*$pdf->Cell(25,5,'Transportation',0,0,'L');
		$pdf->Cell(2,5,':  ',0,0,'L');
		$pdf->Cell(75,5,'',0,0,'L');*/
			
		//Line break
		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(10,5,'No.','1',0,'C');
		$pdf->Cell(95,5,'DESCRIPTION','1',0,'C');
		$pdf->Cell(35,5,'Qty','1',0,'C');
		//$pdf->Cell(20,5,'UNIT','1',0,'C');
		$pdf->Cell(45,5,'UNIT PRICE','1',0,'C');
		$pdf->Cell(45,5,'AMOUNT','1',0,'C');
		//$pdf->Cell(20,5,'% DISC.','1',0,'C');
		//$pdf->Cell(25,5,'DISC.','1',0,'C');
		$pdf->Cell(50,5,'TOTAL','1',0,'C');
		//$pdf->Cell(35,5,'PABRIK','1',0,'C');
		
		$pdf->Ln(5);
		
		$sql="SELECT * FROM tbt_aset_beli WHERE no_po='$noPO' AND st_rkbu_rtbu = '$st_rkbu_rtbu' ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$idBarang = $row['kode'];
			//$idSatuan = BarangRecord::finder()->findByPk($row['kode'])->satuan;
			//$satuan = SatuanBarangRecord::finder()->findByPk($idSatuan)->nama;
			$jmlSatBesar = BarangRecord::finder()->findByPk($idBarang)->jml_satuan_besar;
			
			//$idProdusen = BarangRecord::finder()->findByPk($row['kode'])->produsen;	
			//$produsen = ProdusenBarangRecord::finder()->findByPk($idProdusen)->nama;
			
			$stBeli = $row['st_pembelian'];
			
			if($jnsBeli == '0') //Pembelian dari PBF
			{
				$jml = $row['jumlah'] * $jmlSatBesar;
			}
			else
			{
				$jml = $row['jumlah_kecil'];
			}
				
			
			//cari hrg,disc dari pembelian terakhir
			$sql2="SELECT 
					  tbt_aset_masuk.no_po,
					  tbm_barang.id,
					  tbm_barang.nama,
					  tbt_aset_masuk.discount,
					  tbt_aset_masuk.hrg_nett,
					  tbt_aset_masuk.hrg_disc,
					  tbt_aset_masuk.hrg_ppn,
					  tbt_aset_masuk.hrg_ppn_disc
					FROM
					  tbm_barang
					  INNER JOIN tbt_aset_masuk ON (tbm_barang.id = tbt_aset_masuk.id_barang)
					WHERE 
						tbm_barang.id='$idBarang' 
					GROUP BY
					  tbt_aset_masuk.no_po
					ORDER BY
					  tbt_aset_masuk.id ASC ";
			$arrData2 = $this->queryAction($sql2,'S');
			
			if($arrData2)
			{
				foreach($arrData2 as $row2)
				{
					$disc = $row2['discount'];
					
					if($disc > 0)
					{
						if($jnsBeli == '0') //Pembelian dari PBF
						{
							$hrgSatuan = $row2['hrg_disc'] * $jmlSatBesar;
						}
						else
						{
							$hrgSatuan = $row2['hrg_disc'];
						}						
					}
					else
					{
						if($jnsBeli == '0') //Pembelian dari PBF
						{
							$hrgSatuan = $row2['hrg_nett'] * $jmlSatBesar;
						}
						else
						{
							$hrgSatuan = $row2['hrg_nett'];
						}
					}
					
					if($jnsBeli == '0') //Pembelian dari PBF
					{
						$hrgTotal = $hrgSatuan * $row['jumlah'];
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
			$pdf->Cell(95,5,BarangRecord::finder()->findByPk($row['kode'])->nama,'1',0,'L');
			
			if($jnsBeli == '0') //Pembelian dari PBF
			{
				$pdf->Cell(35,5,number_format($row['jumlah'],'0',',','.'),'1',0,'C');
			}
			else
			{
				$pdf->Cell(35,5,number_format($row['jumlah_kecil'],'0',',','.'),'1',0,'C');
			}
			
			
			//$pdf->Cell(20,5,$satuan,'1',0,'C');
			$pdf->Cell(45,5,number_format($hrgSatuan,'2',',','.'),'1',0,'R');
			$pdf->Cell(45,5,number_format($hrgTotal,'2',',','.'),'1',0,'R');
			//$pdf->Cell(20,5,number_format($disc,'2',',','.').'%','1',0,'R');
			//$pdf->Cell(25,5,number_format($jmlDisc,'2',',','.'),'1',0,'R');
			$pdf->Cell(50,5,number_format($total,'2',',','.'),'1',0,'R');
			//$pdf->Cell(35,5,$produsen,'1',0,'C');
			
			$pdf->Ln(5);
			
			$grandTot += $total;
			//$pdf->Ln(5);
			
		}
		
		$ppn = $grandTot * 0.1;
		$grandTotPpn = $grandTot + $ppn;
		
	
		$pdf->Ln(1);
		
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(105,5,'TOTAL','1',0,'R');
		$pdf->Cell(35,5,'','1',0,'C');
		//$pdf->Cell(20,5,'','1',0,'C');
		$pdf->Cell(45,5,'','1',0,'R');
		//$pdf->Cell(25,5,'','1',0,'R');
		//$pdf->Cell(20,5,'','1',0,'R');
		$pdf->Cell(45,5,'','1',0,'R');
		$pdf->Cell(50,5,number_format($grandTot,'2',',','.'),'1',0,'R');
		//$pdf->Cell(35,5,'','1',0,'C');
		//$pdf->Ln(5);
		
		/*$pdf->Cell(105,5,'PPN 10%','1',0,'R');
		$pdf->Cell(30,5,'','1',0,'C');
		$pdf->Cell(20,5,'','1',0,'C');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(20,5,'','1',0,'R');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(30,5,number_format($ppn,'2',',','.'),'1',0,'R');
		//$pdf->Cell(35,5,'','1',0,'C');
		$pdf->Ln(5);
		
		$pdf->SetFont('Arial','B',9);	
		$pdf->Cell(105,5,'GRAND TOTAL','1',0,'R');
		$pdf->Cell(30,5,'','1',0,'C');
		$pdf->Cell(20,5,'','1',0,'C');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(20,5,'','1',0,'R');
		$pdf->Cell(25,5,'','1',0,'R');
		$pdf->Cell(30,5,number_format($grandTotPpn,'2',',','.'),'1',0,'R');
		//$pdf->Cell(35,5,'','1',0,'C');
		*/
		$pdf->Ln(10);
		
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(40,5,'PURCHASE',1,0,'C');
		$pdf->Cell(40,5,'MANAGER KEU',1,0,'C');
		$pdf->Cell(40,5,'DIREKTUR KEU',1,0,'C');
		$pdf->Cell(40,5,'KOMISARIS',1,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(40,20,'',1,0,'C');
		$pdf->Cell(40,20,'',1,0,'C');
		$pdf->Cell(40,20,'',1,0,'C');
		$pdf->Cell(40,20,'',1,0,'C');
		
		$pdf->Ln(25);
		/*$pdf->SetFont('Arial','B',8);
		$pdf->Cell(100,5,'Note :','TRL',0,'L');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(100,5,'1. No. PO harap dicantumkan  pada surat jalan dan faktur penagihan :','RL',0,'L');
		$pdf->Ln(5);
		$pdf->Cell(100,5,'2. Faktur dan kwitansi harap dilampiri surat jalan dan PO','BRL',0,'L');
		*/
		//$pdf->Ln(10);
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(85,5,'Distribute :','TRL',0,'L');
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',8);
		$pdf->Cell(25,5,'1. Supplier','BL',0,'L');
		$pdf->Cell(30,5,'2. Accounting','B',0,'L');
		$pdf->Cell(30,5,'3. Purchasing','BR',0,'L');
		
		
		
		/*
		$pdf->Ln(5);
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.date('d') . ' ' . $this->namaBulan(date('m')) . ' ' . date('Y'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(65,8,'Direktur,',0,0,'C');
		$pdf->Cell(65,8,'Manajer Keuangan,',0,0,'C');
		$pdf->Cell(60,8,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		
		
		$pdf->Cell(65,8,'( Direktur )',0,0,'C','',$url);
		$pdf->Cell(65,8,'( Manajer Keuangan )',0,0,'C','',$url);
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$url);	
		
		$pdf->Ln(5);
			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nipTmp,0,0,'C');									
		*/
		
		$pdf->Output();
						
	}
	
}
?>
