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
class PbfObatBaru extends SimakConf
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
				$sql="SELECT id FROM tbm_pbf_obat order by id desc";
				$no = PbfObatRecord::finder()->findBySql($sql);
					
				if($no==NULL)//jika kosong bikin ndiri
				{	
					$urut='0001';
					$urut=$no.$urut;
				}else
				{
					$no1=intval(substr($no->getColumnValue('id'),0,4));
					if ($no1==9999)
					{
						$urut='0001';
					}else
					{
						$urut=$no1+1;
						$urut=substr('0000',-4,4-strlen($urut)).$urut;					
					}
				}
				$this->ID->Text=$urut;
				//$this->ID->focus();					
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(PbfObatRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newPbfObat=new PbfObatRecord();
			$newPbfObat->id=$this->ID->Text;			
			$newPbfObat->nama=$this->nama->Text;
			$newPbfObat->alamat=$this->alamat->Text;
			$newPbfObat->tlp=$this->telp->Text;
			$newPbfObat->npwp=$this->npwp->Text;
			$newPbfObat->npkp=$this->npkp->Text;				
			$newPbfObat->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.PbfObat'));						
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
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.PbfObat'));		
	}
}
?>
