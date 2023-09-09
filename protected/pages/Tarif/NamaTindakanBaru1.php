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
class NamaTindakanBaru extends SimakConf
{
	public function onLoad($param)
		{				
			parent::onLoad($param);		
				
			if(!$this->isPostBack)	            		
			{				
				$this->ID->focus();				
				$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
				$this->DDKlinik->dataBind();
				$sql="update tbm_obat_new set kode=";
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        $param->IsValid=(NamaTindakanRecord::finder()->findByPk($this->ID->Text)===null);		
    }	   
	
	public function chKlinik()
	{
		$id=$this->DDKlinik->SelectedValue;
		$this->ID->Text=$id;
		switch ($id)
		{
			case "01":$kode='PD';break;
			case "02":$kode='KD';break;
			case "03":$kode='BD';break;
			case "04":$kode='AN';break;
			case "05":$kode='TH';break;
			case "06":$kode='GI';break;
			case "07":$kode='IG';break;
			case "08":$kode='UM';break;
			case "09":$kode='MT';break;
			case "10":$kode='KK';break;
			case "11":$kode='DT';break;
			case "12":$kode='PR';break;
			case "13":$kode='PG';break;
			case "14":$kode='PP';break;
			case "15":$kode='RD';break;
			case "16":$kode='PT';break;
			case "17":$kode='OK';break;
		}
		$sql="SELECT 
			  	tbm_nama_tindakan.id AS id				  
			  FROM
				tbm_nama_tindakan 
			  WHERE
			  	tbm_nama_tindakan.id_klinik='$id' order by id desc";
		$no = NamaTindakanRecord::finder()->findBySql($sql);
			
		if($no==NULL)//jika kosong bikin ndiri
		{	
			$urut='01';
			$urut=$kode.$urut;
		}else
		{
			
			//$urut=intval(substr($no->getColumnValue('id'),-6,6));
			//$no1=intval(substr($no->getColumnValue('id'),0,2));
			$no1=intval(substr($no->getColumnValue('id'),2,2));
			//$no3=intval(substr($no->getColumnValue('cm'),4,2));
			
			if ($no1==99)
			{
				$urut='01';					
				$urut=$kode.$urut;
			}else
			{
				//$urut=intval(substr($no->getColumnValue('cm'),-6,6));
				$urut=$no1+1;
				$urut=$kode.substr('00',-2,2-strlen($urut)).$urut;					
			}
			//$urut=$no2;
		}
		$this->ID->Text=$urut;
	}
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{		
			$newNamaTdk=new NamaTindakanRecord();
			$newNamaTdk->id=$this->ID->Text;				
			$newNamaTdk->id_klinik=(string)$this->DDKlinik->SelectedValue;
			$newNamaTdk->nama=$this->nama->Text;				
			$newNamaTdk->save();	
			$this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));									
		}
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Tarif.NamaTindakan'));		
	}

}
?>