<?php
require_once 'Barcode.php';

class barcodeGen extends SimakConf
{
	public function onLoad($param)
	{	
		$cm=$this->Request['cm'];
		Image_Barcode::draw($cm, 'code128', 'png','false');
		
	}
}
?>
