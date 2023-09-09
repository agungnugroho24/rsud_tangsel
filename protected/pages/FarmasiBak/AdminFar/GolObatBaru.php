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
class GolObatBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$sql="SELECT id FROM tbm_golongan_obat order by id desc";
				$no = GolObatRecord::finder()->findBySql($sql);
					
				if($no==NULL)//jika kosong bikin ndiri
				{	
					$urut='001';
					$urut=$no.$urut;
				}else
				{
					$no1=intval(substr($no->getColumnValue('id'),1,4));
					if ($no1==999)
					{
						$urut='G001';
					}else
					{
						$urut=$no1+1;
						$urut='G'.substr('000',-3,3-strlen($urut)).$urut;					
					}
				}
				$this->ID->Text=$urut;
				//$this->ID->focus();					
			}
		}
		
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Farmasi
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	 
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(GolObatRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newGolObat=new GolObatRecord();
			$newGolObat->id=$this->ID->Text;			
			$newGolObat->nama=ucwords($this->nama->Text);
			
			$newGolObat->save();				
			
			$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.GolObat'));						
		}
	}	
	
	/*
	public function batalClicked($sender,$param)
	{		
		$this->ID->Text='';			
		$this->nama->Text='';		
		
	}
	*/
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.AdminFar.GolObat'));		
	}
}
?>