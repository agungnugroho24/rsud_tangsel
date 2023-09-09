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
class masterFisioRujukanBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$this->ID->focus();				
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
		$data = count(FisioRujukanRecord::finder()->findByPk($this->ID->Text));
		
		if($param->Value == ($data > 0))
            $param->IsValid=false;
			
       // $param->IsValid=(FisioRujukanRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newPoliklinik=new FisioRujukanRecord();
			
			$newPoliklinik->id=$this->ID->Text;			
			$newPoliklinik->nama=$this->nama->Text;
			
			$newPoliklinik->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioRujukan'));						
		}
	}	
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		$this->alamat->Text='';		
		$this->telp->Text='';		
		$this->npwp->Text='';		
		$this->npkp->Text='';				
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Fisio.masterFisioRujukan'));		
	}
}
?>