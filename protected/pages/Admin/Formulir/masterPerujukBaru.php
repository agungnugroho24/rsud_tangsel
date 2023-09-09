<?php
/**
 * DaftarRwtJln class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: DaftarRwtJln.php 1001 2008-03-18 12:03:51Z xue $
 */

/**
 * DaftarRwtJln class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
class masterPerujukBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				//$this->nama->focus();					
			}
		}  
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newBP=new BidanPerujukRecord();
			
			$newBP->nama=$this->nama->Text;
			$newBP->alamat=$this->alamat->Text;
			
			$newBP->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.masterPerujuk'));						
		}
	}	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Admin.Formulir.masterPerujuk'));		
	}
}
?>
