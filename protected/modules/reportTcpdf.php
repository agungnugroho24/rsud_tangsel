<?php
Prado::using('Application.modules.tcpdf.tcpdf');
Prado::using('Application.modules.tcpdf.htmlcolors');

class reportTcpdf extends TCPDF
{	
	protected $customTxt = "";
	 
	public function getCustomTxt()
	{
			return $customTxt;
	}

	public function setCustomTxt($data) {
			return $data;
	}
	
	public function setDokterTxt($data2) {
			return $data2;
	}
	
	// Page footer
	public function Footer() 
	{
		//if($this->page == '2')
		//{
			// Position at 15 mm from bottom
			$this->SetY(-25);
			// Set font
			$this->SetFont('helvetica', '', 7);
		
			// Page number
			//$this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
			
			$this->Cell(90, 10, '', 0, false, 'C', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 10, 'Dokter yang memeriksa,', 0, false, 'C', 0, '', 0, false, 'T', 'M');
			$this->Ln(10);
			$this->Cell(90, 10, $this->CustomTxt, 0, false, 'L', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 10, $this->DokterTxt, 0, false, 'C', 0, '', 0, false, 'T', 'M');	
		//}
	}

}

?>
