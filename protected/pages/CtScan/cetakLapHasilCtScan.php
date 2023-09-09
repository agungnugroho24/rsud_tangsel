<?php
Prado::using('Application.modules.tcpdf.tcpdf');
Prado::using('Application.modules.tcpdf.htmlcolors');

class cetakLapHasilCtScan extends SimakConf
{
	public function onLoad($param)
	{	
		$mode = $this->Request['mode'];
		$jnsPasien = $this->Request['jnsPasien'];
		$noReg = $this->Request['noReg'];		
		$noTrans = $this->Request['notrans'];		
		$cm = $this->Request['cm'];	
		$nmPasien = $this->Request['nama'];
		$dokter = $this->Request['dokter'];		
		$dokterRad = $this->Request['dokterRad'];		
		$operator = $this->User->IsUserName;
		$nipTmp = $this->User->IsUserNip;		
		$nmTable = $this->Request['table'];
		
		//$cek = substr($noTrans,6,2);
		if ($jnsPasien=='0')
		{
			$noTrans = CtScanJualRecord::finder()->find('no_reg = ?',$noReg)->no_trans_rwtjln;
			$idKlinik = RwtjlnRecord::finder()->findByPk($noTrans)->id_klinik;
			$ruangan = 'Poli '.PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
			$tglHasil = $this->convertDate(CtScanJualRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = CtScanJualRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';
		}
		elseif($jnsPasien=='1')
		{
			$noTrans = CtScanJualInapRecord::finder()->find('no_reg = ?',$noReg)->no_trans_inap;
			$idJnsKamar = RwtInapRecord::finder()->findByPk($noTrans)->jenis_kamar;
			$idKamar = RwtInapRecord::finder()->findByPk($noTrans)->kamar;
			
			$ruangan = KamarNamaRecord::finder()->findByPk($idJnsKamar)->nama;
			
			if($idJnsKamar == '1')
				$ruangan .= ' ('.RuangRecord::finder()->findByPk($idKamar)->nama.')';
				
			$tglHasil = $this->convertDate(CtScanJualInapRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = CtScanJualInapRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';	
		}	
		elseif($jnsPasien=='2')
		{
			$noTrans = CtScanJualLainRecord::finder()->find('no_reg = ?',$noReg)->no_trans_pas_luar;
			$ruangan = '-';
			
			$tglHasil = $this->convertDate(CtScanJualLainRecord::finder()->find('no_reg = ?',$noReg)->tgl,'3');
			$wktHasil = CtScanJualLainRecord::finder()->find('no_reg = ?',$noReg)->wkt.' WIB';	
		}
		
		$idSatuation = PasienRecord::finder()->findByPk($cm)->satuation;
		$satutation = SatuationRecord::finder()->findByPk($idSatuation)->singkatan;
		
		if($jnsPasien=='0' || $jnsPasien=='1')
		{
			$nmPas = $satutation.' '.$nmPasien;
			
			if($jnsPasien == '0')
				$umur = $this->cariUmur('0',$cm,$idKlinik);
			else
				$umur = $this->cariUmur('1',$cm,'');
					
			$umur = $umur['years'].' th '.$umur['months'].' bln '.$umur['days'].' hr' ;
			
			$jKel = $this->cariJkel($cm);
		}
		elseif($jnsPasien=='2')
		{
			$cm = '-';
			$nmPas = $nmPasien;
			$umur = PasienLuarRecord::finder()->findByPk($noTrans)->umur;
			$jKel = PasienLuarRecord::finder()->findByPk($noTrans)->jkel;
		}	
		
		//$noKwitansi = substr($noTrans,6,6).'/'.'LAB-';
		//$noKwitansi .= substr($noTrans,4,2).'/';
		//$noKwitansi .= substr($noTrans,0,4);						
		$nip = substr($nipTmp,0,3);
		$nip .= '.';
		$nip .= substr($nipTmp,3,3);
		$nip .= '.';
		$nip .= substr($nipTmp,6,3);
		
		if($jnsPasien=='0')
		{
			$sql = "SELECT 
				  tbt_ctscan_penjualan.id_tindakan,
				  tbm_ctscan_tindakan.kode,
				  tbm_ctscan_tindakan.nama,
				  tbt_ctscan_penjualan.catatan
				FROM
				  tbt_ctscan_penjualan
				  INNER JOIN tbm_ctscan_tindakan ON (tbm_ctscan_tindakan.kode = tbt_ctscan_penjualan.id_tindakan)
				WHERE 
					tbt_ctscan_penjualan.no_reg = '$noReg' 
					AND tbt_ctscan_penjualan.st_cetak_hasil = '1' ";
			
			if($mode == '0')
				$sql .= "AND tbt_ctscan_penjualan.st_medical_checkup = '0' AND tbt_ctscan_penjualan.st_travel_clinic = '0' ";
			elseif($mode == '1')
				$sql .= "AND tbt_ctscan_penjualan.st_medical_checkup = '1' ";
			elseif($mode == '2')
				$sql .= "AND tbt_ctscan_penjualan.st_travel_clinic = '1' ";	
		}
		elseif($jnsPasien=='1')
		{
			$sql = "SELECT 
				  tbt_ctscan_penjualan_inap.id_tindakan,
				  tbm_ctscan_tindakan.kode,
				  tbm_ctscan_tindakan.nama,
				  tbt_ctscan_penjualan_inap.catatan
				FROM
				  tbt_ctscan_penjualan_inap
				  INNER JOIN tbm_ctscan_tindakan ON (tbm_ctscan_tindakan.kode = tbt_ctscan_penjualan_inap.id_tindakan)
				WHERE 
					tbt_ctscan_penjualan_inap.no_reg = '$noReg' 
					AND tbt_ctscan_penjualan_inap.st_cetak_hasil = '1' ";
		}	
		elseif($jnsPasien=='2')
		{
			$sql = "SELECT 
				  tbt_ctscan_penjualan_lain.id_tindakan,
				  tbm_ctscan_tindakan.kode,
				  tbm_ctscan_tindakan.nama,
				  tbt_ctscan_penjualan_lain.catatan
				FROM
				  tbt_ctscan_penjualan_lain
				  INNER JOIN tbm_ctscan_tindakan ON (tbm_ctscan_tindakan.kode = tbt_ctscan_penjualan_lain.id_tindakan)
				WHERE 
					tbt_ctscan_penjualan_lain.no_reg = '$noReg' 
					AND tbt_ctscan_penjualan_lain.st_cetak_hasil = '1' ";
			
			if($mode == '0')
				$sql .= "AND tbt_ctscan_penjualan_lain.st_medical_checkup = '0' AND tbt_ctscan_penjualan_lain.st_travel_clinic = '0' ";
			elseif($mode == '1')
				$sql .= "AND tbt_ctscan_penjualan_lain.st_medical_checkup = '1' ";
			elseif($mode == '2')
				$sql .= "AND tbt_ctscan_penjualan_lain.st_travel_clinic = '1' ";	
		}
		
		$arrData = $this->queryAction($sql,'S');
		
		// create new PDF document
		$pdf = new reportTcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, 'a5', true, 'UTF-8', false);
		
		// set document information
		//$pdf->SetCreator(PDF_CREATOR);
		//$pdf->SetAuthor('Nicola Asuni');
		//$pdf->SetTitle('TCPDF Example 006');
		//$pdf->SetSubject('TCPDF Tutorial');
		//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
		
		// set default header data
		//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
		
		// set header and footer fonts
		//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		
		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		
		//set margins
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetMargins(8, 10, 8);
		
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		
		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		
		//set some language-dependent strings
		$pdf->setLanguageArray($l);
		
		// ---------------------------------------------------------
		
		// set font
		//$pdf->SetFont('helvetica', '', 10);
		
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(true); 
		$pdf->CustomTxt = 'Terima Kasih Atas Kepercayaan T.S';
		$pdf->DokterTxt = $dokterRad;
		//$pdf->SetAutoPageBreak('false');
		
		$j=0;
		foreach($arrData as $row)
		{
			// add a page
			$pdf->AddPage();
			
			// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
			// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
			
			//$urlImg = $this->Application->AssetManager->publishFilePath($this->Application->Service->BasePath.'/Tarif/logo-bdgbarat.eps');
			$url = $this->Service->constructUrl('CtScan.beritaCtScan');
			//$pdf->ImageEps('/home/sonny/web-server/www/simak_garuda/protected/modules/tcpdf/images/logo-gsm.eps', 15, 70, 180);
			
			// create some HTML content
			$html = '
			<style>
				a{
					color:#000;
					text-decoration:none;
				}
					
				.title{
						font-size:11pt;
						font-weight:bold;
					}
				
				.subtitle{font-size:10pt;}
				
				.txt9{
					font-size:9pt;
				}
				
				.txt8{
					font-size:8pt;
				}
				
				.txt7{
					font-size:7pt;
				}
				
				tr.noborder td{
					border:none;
				}
				
				tr.border td{
					border:solid #000 1px;
				}
				
				tr.borderLR td{
					border-left:solid #000 1px;
					border-right:solid #000 1px;
				}
				
				tr.borderLRB td{
					border-left:solid #000 1px;
					border-right:solid #000 1px;
					border-bottom:solid #000 1px;
				}
				
				tr.borderT td{
					border-top:solid #000 1px;
				}
				
				.border{
					border-left:solid #000 1px;
					border-right:solid #000 1px;
					border-bottom:solid #000 1px;
					border-top:solid #000 1px;
				}
				
				table, tr, td{
					padding:5px;	
				}
				
				table.nopadding{
					padding:0px;	
				}
				
				.padding3{
					padding:3px;	
				}
				
				.padding1{
					padding:1px;	
				}
				
			</style>
					
			<table width="100%" class="txt9">
			  <tr>
				<td align="left" width="58%">
					<div>
						<span class="subtitle"><a href="'.$url.'">RSUD Kota Tangerang Selatan</a></span><br/>
						<span class="title"><a href="'.$url.'">INSTALASI RADIOLOGI</a></span><br/>
						<span class="txt7"><a href="'.$url.'">JL. Pajajaran No. 101 Pamulang</a></span><br/>
						<span class="txt7"><a href="'.$url.'">Telp. (021) 74718440</a></span>
					</div>
				</td>
				<td valign="bottom">
					<span class="subtitle"></span><br/>
					<span class="title"></span><br/>
					<span class="txt8">Kepada</span><br/>
					<span class="txt8">Yth :  ';
					
					if($cm)
					{
						$html .= '<b>'.$dokter.'</b></span>';
					}
					else
					{
						$html .=' - </span>';
					}
				
					$html .='
				</td>
			  </tr>
			</table> 
			
			<table width="100%"  class="txt7 nopadding">
			  <tr><td><hr/></td></tr>
			</table>  
			
			<table width="100%" border="0" class="txt7 padding1">
			  <tr>
				<td width="15%" valign="top">Nama Pasien</td>
				<td width="3%" align="center" valign="top">:</td>
				<td width="40%" valign="top"><strong>'.$nmPas.'</strong></td>
				<td width="12%" valign="top">Umur / Sex</td>
				<td width="3%" align="center" valign="top">:</td>
				<td width="27%" valign="top"><strong>'.$umur.' / '.$jKel.'</strong></td>
			  </tr>
			  <tr>
				<td width="15%" valign="top">No. Foto</td>
				<td width="3%" align="center" valign="top">:</td>
				<td width="40%" valign="top"><strong>'.$noReg.'</strong></td>
				<td width="12%" valign="top">Ruangan</td>
				<td width="3%" align="center" valign="top">:</td>
				<td width="27%" valign="top"><strong>'.$ruangan.'</strong></td>
			  </tr>
			  <tr>
				<td width="15%" valign="top">Tanggal</td>
				<td width="3%" align="center" valign="top">:</td>
				<td width="40%" valign="top"><strong>'.$tglHasil.', '.$wktHasil.'</strong></td>
				<td width="12%" valign="top">No. RM</td>
				<td width="3%" align="center" valign="top">:</td> ';
				
				if($cm)
				{
					$html .='
						<td width="27%" valign="top"><strong>'.$cm.'</strong></td>
					  </tr>';
				}
				else
				{
					$html .='
						<td width="27%" valign="top"><strong>-</strong></td>
					  </tr>';
				}
				
				$html .='
				<tr>
					<td width="15%" valign="top">Pemeriksaan</td>
					<td width="3%" align="center" valign="top">:</td>
					<td width="40%" valign="top"><strong>'.$row['nama'].'</strong></td>
					<td width="12%" valign="top"></td>
					<td width="3%" align="center" valign="top"></td>
					<td width="27%" valign="top"><strong></strong></td>
				  </tr>
				</table>
				
				<table width="100%"  class="txt7 nopadding">
				  <tr><td><hr/></td></tr>
				</table>
				
				<table width="100%"  class="txt7 nopadding">
				  <tr><td><strong>Hasil.</strong><br/></td></tr>
				</table>
				
				<table width="100%"  class="txt7 nopadding">
				  <tr><td>'.$row['catatan'].'</td></tr>
				</table>';
			
			// output the HTML content
			$pdf->writeHTML($html, true, false, true, false, '');
			
			/*if($j == (count($arrData)-1))
			{
				// Position at 15 mm from bottom
				$pdf->SetY(-25);
				// Set font
				$pdf->SetFont('helvetica', '', 7);
			
				// Page number
				//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
				
				$pdf->Cell(90, 10, '', 0, false, 'C', 0, '', 0, false, 'T', 'M');
				$pdf->Cell(0, 10, 'Dokter yang memeriksa,', 0, false, 'C', 0, '', 0, false, 'T', 'M');
				$pdf->Ln(10);
				$pdf->Cell(90, 10, $pdf->CustomTxt, 0, false, 'L', 0, '', 0, false, 'T', 'M');
				$pdf->Cell(0, 10, $pdf->DokterTxt, 0, false, 'C', 0, '', 0, false, 'T', 'M');		
			}*/
		}
				
		
		
		
		// reset pointer to the last page
		$pdf->lastPage();
		
		// ---------------------------------------------------------
		
		//Close and output PDF document
		$pdf->Output($noPO.'.pdf', 'I');
						
	}
	
}
?>