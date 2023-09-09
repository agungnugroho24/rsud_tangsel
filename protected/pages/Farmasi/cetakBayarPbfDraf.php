<?php
class cetakBayarPbfDraf extends SimakConf
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
		$noTrans=$this->Request['noTrans'];
				
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;		
		
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);	
		
		$pdf=new reportAdmBarang('P','mm','legal');
		$pdf->footerTxt="Pengajuan Pembayaran PBF";
		//$pdf=new cetakKartu();
		$pdf->AliasNbPages(); 			
		
		$pdf->AddPage();
		
		$pdf->SetFont('Arial','',6);
		$pdf->Cell(0,5,'No. Pengajuan : '.$noTrans,'0',0,'R');
		
		$pdf->Ln(5);
			
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
		$pdf->Cell(0,5,'PEMBAYARAN OBAT KE SUPPLIER','0',0,'C');
		$pdf->Ln(5);				
		//$pdf->Cell(0,5,'PEMBAYARAN OBAT KE SUPPLIER','0',0,'C');
		//$pdf->Ln(5);				
		
		$pdf->Ln(5);
				
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(10,10,'NO','1',0,'C');
		$pdf->Cell(50,5,'FAKTUR','1',0,'C');
		$pdf->Cell(20,5,'JATUH','1',0,'C');
		$pdf->Cell(65,10,'SUPPLIER','1',0,'C');
		$pdf->Cell(25,10,'TOTAL','1',0,'C');
		$pdf->Cell(25,5,'TGL','1',0,'C');
		
		$pdf->Ln(5);
		
		$pdf->Cell(10,5,'','0',0,'C');
		$pdf->Cell(30,5,'NO','1',0,'C');
		$pdf->Cell(20,5,'TGL','1',0,'C');
		$pdf->Cell(20,5,'TEMPO','1',0,'C');
		$pdf->Cell(65,5,'','0',0,'C');
		$pdf->Cell(25,5,'','0',0,'C');
		$pdf->Cell(25,5,'PEMBAYARAN','1',0,'C');
		
		$pdf->Ln(5);
			
		$sql="SELECT 
			  tbt_bayar_pbf.no_faktur,
			  tbt_bayar_pbf.st_tunda, 
			  tbt_bayar_pbf.total,
			  tbt_bayar_pbf.tgl_bayar,
			  tbt_obat_masuk.tgl_faktur,
			  tbt_obat_masuk.tgl_jth_tempo,
			  tbm_pbf_obat.nama AS nm_supplier
			FROM
			  tbt_bayar_pbf
			  INNER JOIN tbt_obat_masuk ON (tbt_bayar_pbf.no_faktur = tbt_obat_masuk.no_faktur)
			  INNER JOIN tbt_obat_beli ON (tbt_obat_masuk.no_po = tbt_obat_beli.no_po)
			  INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
			WHERE tbt_bayar_pbf.no_trans='$noTrans' AND tbt_bayar_pbf.st = '0' AND tbt_bayar_pbf.st_tunda='0'
			GROUP BY
			  tbt_obat_masuk.no_faktur
			  ";
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...				
		$i=0;		
		foreach($arr as $row)
		{
			$i += 1;			
			$tgl_bayar = explode('-',$row['tgl_bayar']);
			$tgl_bayar = $tgl_bayar['2'].'/'.$tgl_bayar['1'].'/'.$tgl_bayar['0'];
			
			$tgl_faktur = explode('-',$row['tgl_faktur']);
			$tgl_faktur = $tgl_faktur['2'].'/'.$tgl_faktur['1'].'/'.$tgl_faktur['0'];
			
			$tgl_jth_tempo = explode('-',$row['tgl_jth_tempo']);
			$tgl_jth_tempo = $tgl_jth_tempo['2'].'/'.$tgl_jth_tempo['1'].'/'.$tgl_jth_tempo['0'];
			
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(10,5,$i,'1',0,'C');
			$pdf->Cell(30,5,$row['no_faktur'],'1',0,'C');
			$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
			$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
			$pdf->Cell(65,5,$row['nm_supplier'],'1',0,'C');
			$pdf->Cell(25,5,number_format($row['total'],2,',','.'),'1',0,'R');
			
			if($i==1)
			{
				$pdf->Cell(25,5,$tgl_bayar,'1',0,'C');
			}
			else
			{
				$pdf->Cell(25,5,'','1',0,'C');
			}
			
			$pdf->Ln(5);
			
			$total += $row['total'];
		}
		
		$pdf->Cell(145,5,'TOTAL','1',0,'C');	
		$pdf->Cell(25,5,number_format( $total,2,',','.'),1,0,'R');	
		$pdf->Cell(25,5,'','1',0,'C');	
		
		$pdf->Ln(5);
		
		$sql="SELECT 
			  tbt_bayar_pbf.no_faktur,
			  tbt_bayar_pbf.st_tunda, 
			  tbt_bayar_pbf.total,
			  tbt_obat_masuk.tgl_faktur,
			  tbt_obat_masuk.tgl_jth_tempo,
			  tbm_pbf_obat.nama AS nm_supplier
			FROM
			  tbt_bayar_pbf
			  INNER JOIN tbt_obat_masuk ON (tbt_bayar_pbf.no_faktur = tbt_obat_masuk.no_faktur)
			  INNER JOIN tbt_obat_beli ON (tbt_obat_masuk.no_po = tbt_obat_beli.no_po)
			  INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)
			WHERE tbt_bayar_pbf.no_trans='$noTrans' AND tbt_bayar_pbf.st = '0' AND tbt_bayar_pbf.st_tunda='1'
			GROUP BY
			  tbt_obat_masuk.no_faktur
			  ";
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
		if(count($arr) > 0)
		{
			
			$pdf->Cell(170,5,'PEMBAYARAN YANG TERTUNDA','1',0,'C');	
			$pdf->Cell(25,5,'','1',0,'C');	
			$pdf->Ln(5);
			
			foreach($arr as $row)
			{
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(10,5,'','1',0,'C');
				$pdf->Cell(30,5,$row['no_faktur'],'1',0,'C');
				$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
				$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
				$pdf->Cell(65,5,$row['nm_supplier'],'1',0,'C');
				$pdf->Cell(25,5,number_format($row['total'],2,',','.'),'1',0,'R');
				$pdf->Cell(25,5,'','1',0,'C');
				
				$pdf->Ln(5);
				
				$totalTertunda += $row['total'];
			}
		
			$pdf->Cell(145,5,'TOTAL PEMBAYARAN YANG TERTUNDA','1',0,'C');	
			$pdf->Cell(25,5,number_format( $totalTertunda,2,',','.'),1,0,'R');	
			$pdf->Cell(25,5,'','1',0,'C');	
			$pdf->Ln(5);
		}				
		
		$pdf->SetFont('Arial','B',9);
		$pdf->Cell(145,5,'TOTAL KESELURUHAN','1',0,'C');	
		$pdf->Cell(25,5,number_format( $total+$totalTertunda,2,',','.'),1,0,'R');	
		$pdf->Cell(25,5,'','1',0,'C');	
		$pdf->Ln(5);
		
		
		$sql = "SELECT 
				  tbt_obat_masuk.id_obat,
				  tbm_obat_kelompok.id AS id_kel_obat,
				  tbt_bayar_pbf.no_po,
				  tbt_bayar_pbf.no_faktur,
				  tbt_obat_masuk.tgl_faktur,
				  tbt_obat_masuk.tgl_jth_tempo,
				  SUM(tbt_obat_masuk.jumlah * if(tbt_obat_masuk.hrg_ppn_disc=tbt_obat_masuk.hrg_nett,tbt_obat_masuk.hrg_ppn,tbt_obat_masuk.hrg_ppn_disc)) AS total
				FROM
				  tbt_bayar_pbf
				  INNER JOIN tbt_obat_masuk ON (tbt_bayar_pbf.no_faktur = tbt_obat_masuk.no_faktur)
				  INNER JOIN tbm_obat ON (tbt_obat_masuk.id_obat = tbm_obat.kode)
				  LEFT OUTER JOIN tbm_obat_kelompok ON (tbm_obat.kel_obat = tbm_obat_kelompok.id)
				WHERE 
					tbt_bayar_pbf.no_trans='$noTrans' 
					AND tbt_obat_masuk.st_keuangan = '0' 
					AND tbm_obat_kelompok.id = '10' 
				GROUP BY no_faktur
				";
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
		if($arr > 0)
		{
			foreach($arr as $row)
			{
				$no_po = $row['no_po'];
				$id_pbf = ObatBeliRecord::finder()->find('no_po=?',$no_po)->pbf;
				$nm_pbf = PbfObatRecord::finder()->findByPk($id_pbf)->nama;
				
				$pdf->SetFont('Arial','',8);
				$pdf->Cell(10,5,'','1',0,'C');
				$pdf->Cell(30,5,$row['no_faktur'],'1',0,'C');
				$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
				$pdf->Cell(20,5,$tgl_jth_tempo,'1',0,'C');
				$pdf->Cell(65,5,$nm_pbf,'1',0,'C');
				$pdf->Cell(25,5,number_format($row['total'],2,',','.'),'1',0,'R');
				$pdf->Cell(25,5,'','1',0,'C');
				
				$pdf->Ln(5);
				
				$totalVaksin += $row['total']; 	
			}
		
			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(145,5,'TOTAL PEMBAYARAN VAKSIN','1',0,'C');	
			$pdf->Cell(25,5,number_format( $totalVaksin,2,',','.'),1,0,'R');	
			$pdf->Cell(25,5,'','1',0,'C');		
		}		
		
		$pdf->Ln(5);
		
		$pdf->Ln(7);
		$pdf->SetFont('Arial','',9);
		
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'MENGETAHUI',0,0,'C');
		$pdf->Ln(5);
				
		$pdf->Cell(65,8,'Dibuat Oleh,',0,0,'C');
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'Manager Keuangan,',0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(65,8,date('d').'/'.date('m').'/'.date('Y'),0,0,'C');
		$pdf->Ln(15);
		$pdf->Cell(65,8,'( '.$operator.' )',0,0,'C','',$this->Service->constructUrl('Farmasi.BayarPbfDraf'));	
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'( Manajer Keuangan )',0,0,'C','',$this->Service->constructUrl('Farmasi.BayarPbfDraf'));
		$pdf->Ln(10);
		
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'MENYETUJUI',0,0,'C');
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Ln(5);
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'DIR. PEMERINTAH KOTA TANGERANG SELATAN',0,0,'C');
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Ln(15);
		$pdf->Cell(65,8,'',0,0,'C');
		$pdf->Cell(65,8,'( Direktur )',0,0,'C','',$this->Service->constructUrl('Farmasi.BayarPbfDraf'));
		$pdf->Cell(65,8,'',0,0,'C');
						
		$pdf->Output();
						
	}
	
}
?>
