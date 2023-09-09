<?php
class cetakAsetBeliEdit extends SimakConf
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
		
		
		$sql = "SELECT * FROM tbt_aset_beli WHERE no_po = '$noPO' ORDER BY id";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tgl = $row['tgl_po'];		
			$pbf = $row['pbf'];
			$nipTmp =  $row['petugas'];
			$operator = UserRecord::finder()->findBy_nip($nipTmp)->real_name;
		}
		
		//$noKwitansi = substr($noTrans,6,6).'/'.'LAB-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$session=new THttpSession;
		$session->open();
		$sql = $session['cetakObatBeliSql'];
		$sql .= ' ORDER BY nama';
		
		$pdf=new reportAdmBarang('P','mm','kwt');
		//$pdf=new cetakKartu();
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
		$pdf->Cell(0,5,'ORDER PEMBELIAN','0',0,'C');
		$pdf->Ln(5);				
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(30,10,'No. PO',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$noPO,0,0,'L');
		
		$pdf->Cell(15,10,'Kepada',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(50,10,'',0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Tanggal',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,$this->convertDate($tgl,'3'),0,0,'L');
		
		$pdf->Cell(75,10,AsetSupplierRecord::finder()->findByPk($pbf)->nama,0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(30,10,'Dari',0,0,'L');
		$pdf->Cell(2,10,':  ',0,0,'L');
		$pdf->Cell(75,10,'PEMERINTAH KOTA TANGERANG SELATAN',0,0,'L');
		
		$pdf->Cell(75,10,AsetSupplierRecord::finder()->findByPk($pbf)->alamat,0,0,'L');
		$pdf->Ln(5);
			
		//Line break
		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(15,5,'No.','1',0,'C');
		$pdf->Cell(130,5,'Nama Barang','1',0,'C');
		$pdf->Cell(35,5,'Jumlah','1',0,'C');
		$pdf->Ln(5);
		
		$sql="SELECT kode,jumlah FROM tbt_aset_beli WHERE no_po='$noPO'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$j=0;
		foreach($arrData as $row)
		{
			$j += 1;
			$pdf->SetFont('Arial','',9);						
			$pdf->Cell(15,5,$j.'.','1',0,'C');
			$pdf->Cell(130,5,BarangRecord::finder()->findByPk($row['kode'])->nama,'1',0,'L');
			$pdf->Cell(35,5,$row['jumlah'],'1',0,'C');
			$pdf->Ln(5);
			//$pdf->Ln(5);
			
		}
		$pdf->Ln(5);
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');				
		$pdf->Cell(60,8,' Kota Tangerang Selatan, '.$this->convertDate($tgl,'3'),0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(65,8,'Direktur,',0,0,'C');
		$pdf->Cell(65,8,'Manajer Keuangan,',0,0,'C');
		$pdf->Cell(60,8,'Petugas,',0,0,'C');				
		$pdf->Ln(5);			
		$pdf->Cell(10,5,'',0,0,'C');	
		$pdf->Ln(10);
		$pdf->SetFont('Arial','BU',9);
		
		
		$pdf->Cell(65,8,'( Direktur )',0,0,'C','',$this->Service->constructUrl('Asset.LapPembelianEdit'));
		$pdf->Cell(65,8,'( Manajer Keuangan )',0,0,'C','',$this->Service->constructUrl('Asset.LapPembelianEdit'));
		$pdf->Cell(60,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Asset.LapPembelianEdit'));	
		
		$pdf->Ln(5);
			
		$pdf->SetFont('Arial','',9);
		$pdf->Cell(130,8,'',0,0,'L');	
		$pdf->Cell(60,8,'NIP. '.$nipTmp,0,0,'C');									
		$pdf->Output();
						
	}
	
}
?>