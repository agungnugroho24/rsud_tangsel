<?php
class cetakBarcodePdf extends SimakConf
{
	
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
		
		$pdf=new reportBarcode($orientation='P',$unit='mm',$format='barcode');
		$pdf->AliasNbPages(); 
		$pdf->AddPage();	
		$pdf->SetLeftMargin(4);		
		//-------------------BARCODE-------------------------//		
		$pdf->SetFont('Arial','',9);
		
		$sql="SELECT 
			  tbt_asset_barcode.id,
			  tbt_asset_barcode.no_trans,
			  tbt_asset_barcode.id_brg,
			  tbt_asset_barcode.kd_barcode,
			  tbm_asset_barang.nama AS nm_brg,
			  tbm_asset_ruang.nama AS lokasi  
			FROM
			  tbt_asset_barcode
			  INNER JOIN tbm_asset_barang ON (tbt_asset_barcode.id_brg = tbm_asset_barang.id)
			  INNER JOIN tbt_asset_dist_brg ON (tbt_asset_barcode.no_trans = tbt_asset_dist_brg.no_trans)
			  INNER JOIN tbm_asset_ruang ON (tbt_asset_dist_brg.tujuan = tbm_asset_ruang.id)
			GROUP BY
			  tbt_asset_barcode.id		  
					";
					
		$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
		$jmlData=count($arr);
		
		$j=0;
		$pdf->AddFont('3of9', '', '3of9.php');
		foreach($arr as $row)
		{
				$j += 1;
				
				//$pdf->Ln(3);							
				$pdf->SetFont('Arial','B',9);
				$pdf->SetY(4);
				
				$pdf->SetAutoPageBreak(0,0);
				$pdf->Cell(0,5,'ASSET TAG','0',0,'L','',$this->Service->constructUrl('Asset.cetakBarcode'));				
				$pdf->Ln(3);
				
				$pdf->SetFont('Arial','',9);
				
				$pdf->Cell(22,10,'Ruangan',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(22,10,$row['lokasi'],0,0,'L');
				$pdf->Ln(5);
				$pdf->Cell(22,10,'Nama Barang',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(22,10,ucwords($row['nm_brg']),0,0,'L');
				$pdf->Ln(5);
				
				$idBrg=$row['id_brg'];
				$sql2="SELECT 
						SUM(jml) AS jml 
					   FROM 
					   	tbt_asset_dist_brg 
					   WHERE 
					   	no_trans='$noTrans' 
						AND id_brg='$idBrg' 
					   GROUP BY '$idBrg'";
				//$arr2=$this->queryAction($sql2,'R')
				$jml=AssetDistBrgRecord::finder()->findBySql($sql2)->jml;
				
				$pdf->Cell(22,10,'Jumlah',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(22,10,$jml,0,0,'L');
				
				$pdf->Ln(5);
				$pdf->Cell(22,10,'Kode',0,0,'L');
				$pdf->Cell(2,10,':  ',0,0,'L');
				$pdf->Cell(17,10,$row['kd_barcode'],0,0,'L');
						
				$pdf->SetFont('3of9', '', 30);
				$pdf->Cell(22,10,$row['kd_barcode'],0,0,'L');
				$pdf->Ln(5);
				$pdf->SetFont('Arial','',9);
				
				if($j < $jmlData)
				{
					$pdf->AddPage();
				}
		}	
		
		$pdf->Output();
	}
	
}
?>
