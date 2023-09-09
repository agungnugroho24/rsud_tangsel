<?php
class menuAsset extends SimakConf
{
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False"){
			$this->Response->redirect($this->Service->constructUrl('login'));
		}else{
			$this->Response->redirect($this->Service->constructUrl('Asset.masterProdusen'));		
		}
	}
}
?>
