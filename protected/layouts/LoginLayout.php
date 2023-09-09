<?php
class LoginLayout extends TTemplateControl
{
	public function onInit($param)
	{
		parent::onInit($param);
	}
		
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js1', './scripts/jquery.js');	
			
		$this->getPage()->getClientScript()->registerHeadScriptFile('js1', './scripts/jquery-1.4.2.min.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js2', './scripts/jquery.backstretch.js');
		
		$this->getPage()->getClientScript()->registerHeadScriptFile('js3', './scripts/jquery-ui.growl.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/jquery-ui-1.8.5.custom.min.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js5', './scripts/jquery.ui.datepicker-id.js');
		$this->getPage()->getClientScript()->registerHeadScriptFile('js6', './scripts/jquery.loadmask.min.js');
		
		
		
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css1','./scripts/jquery-ui-1.8.5.custom.css');
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css2','./scripts/jquery.loadmask.css');
		
		
		
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/niceforms.js');
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js4', './scripts/complex.js');
		//$this->getPage()->getClientScript()->registerHeadScriptFile('js2', './scripts/jqFancyTransitions.1.7.min.js');
		//$this->getPage()->getClientScript()->registerStyleSheetFile('css1','./scripts/niceforms-default.css');
	}
		
	public function onLoad($param)
	{
		parent::onLoad($param);	
	}
	
}
?>
