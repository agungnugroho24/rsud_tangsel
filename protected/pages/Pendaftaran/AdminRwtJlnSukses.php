<?php
class AdminRwtJlnSukses extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('11');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		
			$this->kembaliBtn->Focus();
    }
	
	public function kembaliClicked()
	{	
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.AdminRwtJln'));
		
	}
}
?>
