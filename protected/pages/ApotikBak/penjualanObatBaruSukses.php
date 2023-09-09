<?php
class penjualanObatBaruSukses extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('3');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Apotik
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
			$this->kembaliBtn->Focus();
    }
	
	public function kembaliClicked()
	{	
		$this->Response->redirect($this->Service->constructUrl('Apotik.penjualanObat'));
		
	}
}
?>
