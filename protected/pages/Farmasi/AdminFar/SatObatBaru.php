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
class SatObatBaru extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }

	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$sql = "SELECT kode 
						FROM tbm_satuan_obat 
						ORDER BY kode DESC";
				$no = SatuanObatRecord::finder()->findBySql($sql);
				
				if($no==NULL)//jika kosong bikin ndiri
				{	
					$urut='0001';
				}else
				{
					if ($urut==9999)
					{
						$urut='0001';					
					}else
					{
						$urut=intval(substr($no->getColumnValue('kode'),-4,4));
						$urut=$urut+1;
						$urut=substr('0000',-4,4-strlen($urut)).$urut;					
					}
				}
				$this->ID->Text=$urut;		
				//$this->tipe->focus();
				$this->ID->focus();									
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(SatuanObatRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newSatuanObat=new SatuanObatRecord();
			
			$newSatuanObat->kode=$this->ID->Text;			
			$newSatuanObat->nama=$this->nama->Text;
			$newSatuanObat->tipe=$this->collectSelectionListResult($this->tipe);
			$newSatuanObat->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.SatObat'));						
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
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.SatObat'));		
	}
}
?>