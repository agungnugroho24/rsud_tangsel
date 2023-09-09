<?php

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class tesXActiveTableRow extends SimakConf
{   
	  
 	public function click($sender, $param){ $this->alert('click');}
	public function command($sender, $param){ $this->alert('command: '.$param->getCommandParameter());}
	public function callback($sender, $param){ $this->alert('callback: '.$param->getCallbackParameter());}
	
	private function alert($s){ $this->getClientScript()->registerEndScript($this->getClientID(), "alert('$s');");}
}

?>
