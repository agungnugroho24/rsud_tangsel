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
class TarifTindakanBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$this->DDKlinik->focus();				
				$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
				$this->DDKlinik->dataBind();
				$this->DDNmTdk->DataSource=NamaTindakanRecord::finder()->findAll();
				$this->DDNmTdk->dataBind();    				
			}
		}
		
	public function cekNama($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(TarifTindakanRecord::finder()->findByPk($this->DDNmTdk->SelectedValue)===null);	
		$this->biaya1->focus();	
    }	   
	
	public function cekKlinik($sender,$param)
    {
        $this->DDNmTdk->DataSource=NamaTindakanRecord::finder()->findAll('id_klinik= ?',$this->DDKlinik->SelectedValue);
		$this->DDNmTdk->dataBind();        
    }
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newTarifTdk=new TarifTindakanRecord();
			$newTarifTdk->id=(string)$this->DDNmTdk->SelectedValue;
			$newTarifTdk->biaya1=$this->biaya1->Text;	
			$newTarifTdk->biaya2=$this->biaya2->Text;
			$newTarifTdk->biaya3=$this->biaya3->Text;
			$newTarifTdk->biaya4=$this->biaya4->Text;				
			$newTarifTdk->save();	
			$this->Response->redirect($this->Service->constructUrl('Tarif.TarifTindakan'));									
		}
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Tarif.TarifTindakan'));		
	}

}
?>