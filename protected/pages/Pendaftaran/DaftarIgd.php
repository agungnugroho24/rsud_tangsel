<?php
/**
 * DaftarIgd class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: DaftarIgd.php 1001 2008-02-09 10:21:01Z anton $
 */

/**
 * DaftarIgd class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
class DaftarIgd extends SimakConf
{
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onLoad($param)
		{				
			parent::onLoad($param);		
			
			if(!$this->isPostBack)	            		
			{							
				if($this->Request['cm'])
				{
					$this->cm->Text=$this->Request['cm'];
					$this->DDDokter->focus();	
				}
				else
				{		
					$this->cm->focus();	
				}
				
				$this->crMskLuar->Enabled=false;
				$this->cm->focus();
				$this->wktMsk->Text=date("H:i:s");
				$this->tglMsk->Text=date("d-m-Y"); 
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('status = ?', '1');//Status pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
				$this->DDCaraMsk->dataBind();
			}
		}
		
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
        //$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text);		
		$checkRM=$this->cm->Text;
		$sql="SELECT * FROM du_pasien WHERE cm='$checkRM'";		
		$data=$this->queryAction($sql,'S');		
		$dummy=PasienRecord::finder()->findByPk($this->cm->Text);
		$this->setViewState('cm',$checkRM);		
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->nama->focus();			
		}
		else if($dummy===null)
		{					
			if($data)
			{			
				$this->errMsg->text="Harap diedit data lama telah ditemukan!";
				$cm=$this->getViewState('cm');
				foreach($data as $row)
				{
					$nama= $row['nama'];
					$tmp_lahir= $row['tmt_lahir'];
					$tgl_lahir= $row['tgl_lahir'];
					$tgl_lahir = $this->ConvertDate($tgl_lahir,'1');
					$alamat=$row['alamat'];
					$suku=$row['suku'];
					$status=$row['status_kawin'] - 1;
					$jkel=$row['jkel'] - 1;
					$agama=$row['agama'];
					$rt=$row['rt'];
					$rw=$row['rw'];
					$catatan=$row['note'];
				}
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status,'mode'=>'07')));
			}else{
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru',array('cm'=>$this->getViewState('cm'),'mode'=>'09')));								
			}			
		}else if ($dummy){
			$this->DDKlinik->Focus();			
		}
		
    }
	protected function caraMasuk ()
	{
		if(($this->DDCaraMsk->SelectedValue == '3') || ($this->DDCaraMsk->SelectedValue == '4'))
		{
			$this->crMskLuar->Enabled=true;
			$this->crMskLuar->focus();				
		}
		else
		{
			$this->crMskLuar->Enabled=false;			
			$this->keluhan->focus();	
		}
	}		   
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$newIgd=new IgdRecord();
			$newIgd->cm=$this->cm->Text;
			$newIgd->no_trans=$this->numCounter('tbt_igd',IgdRecord::finder(),'03');//key '03' adalah konstanta modul untuk IGD
			$newIgd->dokter=(string)$this->DDDokter->SelectedValue;
			$newIgd->tgl_visit=$this->ConvertDate($this->tglMsk->Text,'2');
			$newIgd->wkt_visit=$this->wktMsk->Text;
			$newIgd->keluhan=$this->keluhan->Text;
			$newIgd->catatan=$this->catatan->Text;
			$newIgd->cr_masuk=(string)$this->DDCaraMsk->SelectedValue;
			if($this->DDCaraMsk->SelectedValue == '3' || $this->DDCaraMsk->SelectedValue=='4')
			{
				$newCaraMsk=new IgdCaraMskRecord();
				$newCaraMsk->no_trans=$this->numCounter('tbt_igd',IgdRecord::finder(),'03');//key '03' adalah konstanta modul untuk IGD
				$newCaraMsk->keterangan=$this->crMskLuar->Text;
				$newCaraMsk->save(); 
			}
			$newIgd->save();		
			$this->msg->Text="Data telah disimpan!";	
			$this->Response->Reload();							
		}
	}	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}

}
?>