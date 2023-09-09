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
class DaftarRwtJlnBak extends SimakConf
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
				$cm=$this->Request['cm'];
				$this->setViewState('backTo',$this->Request['mode']);
				if($cm)
				{
					$this->cm->Text=$cm;
				}
				$this->cm->focus();
				$this->crMskLuar->Enabled=false;
				$this->DDDokter->Enabled=false;				
				$this->wktMsk->Text=date("H:i:s");
				$this->tglMsk->Text=date("d-m-Y"); 
				$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
				$this->DDKlinik->dataBind();  
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll();
				$this->DDDokter->dataBind();    
				$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
				$this->DDCaraMsk->dataBind();
				
				$this->suksesMsgCtrl->Visible = false;
				$this->suksesMsg->Text = '' ;
			}
		}
		
	public function chKlinik($sender,$param)
	{
		if($this->DDKlinik->SelectedValue=='')
		{
			$this->DDDokter->SelectedValue=NULL;
			$this->DDDokter->Enabled=false;
		}
		elseif($this->DDKlinik->SelectedValue=='07')
		{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1','08');
			$this->DDDokter->dataBind();
			$klinik=PoliklinikRecord::finder()->findByPk('08')->nama;		
		}else
		{
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);
			$this->DDDokter->dataBind();
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
		}
		$this->DDDokter->Enabled=true;
		$this->doublePasienMsg->Text='';
	}
	
	public function checkRM($sender,$param)
    {
		//$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text)===null;		
		$checkRM=$this->cm->Text;
		//$sql="SELECT * FROM du_pasien WHERE cm='$checkRM'";		
		//$data=$this->queryAction($sql,'S');		
		$dummy=PasienRecord::finder()->findByPk($this->cm->Text);
		$this->setViewState('cm',$checkRM);		
		
		$this->suksesMsgCtrl->Visible = false;
		$this->suksesMsg->Text = '' ;
		
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		/*
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->nama->focus();			
		}
		else */if($dummy===null)
		{	/*				
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
			}else{*/
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));								
			//}			
		}else if ($dummy){
			$this->DDKlinik->Focus();			
		}
		
    }
	
	
	protected function DDDokterChanged ()
	{
		$idDokter = $this->DDDokter->SelectedValue;
		$tgl = date('Y-m-d');		
		$cm = $this->cm->Text;
		if($this->DDDokter->SelectedValue != '')
		{			
			if(RwtjlnRecord::finder()->find('cm = ? AND dokter = ? AND tgl_visit = ? AND flag=0',$this->cm->Text,$idDokter,$tgl))
			{
				$this->doublePasienMsg->Text='Pasien telah terdaftar !';
				$this->simpanBtn->Enabled = false;
				$this->DDDokter->focus();
			}
			else
			{
				$this->doublePasienMsg->Text='';
				$this->simpanBtn->Enabled = true;
				$this->DDCaraMsk->focus();
			}
		}
		else
		{
			$this->DDDokter->focus();
			$this->simpanBtn->Enabled = true;
		}
		
		
	}
	
	protected function caraMasuk ()
	{
		
		if(($this->DDCaraMsk->SelectedValue == '3') || ($this->DDCaraMsk->SelectedValue == '4')|| ($this->DDCaraMsk->SelectedValue == '5'))
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
			$newRwtJln=new RwtjlnRecord();
			$newRwtJln->cm=$this->cm->Text;
			$newRwtJln->no_trans=$this->numCounter('tbt_rawat_jalan',RwtjlnRecord::finder(),'01');
			//key '01' adalah konstanta modul untuk Rawat Jalan
			$newRwtJln->id_klinik=(string)$this->DDKlinik->SelectedValue;
			$newRwtJln->dokter=(string)$this->DDDokter->SelectedValue;
			$newRwtJln->tgl_visit=$this->ConvertDate($this->tglMsk->Text,'2');
			$newRwtJln->wkt_visit=$this->wktMsk->Text;
			$newRwtJln->keluhan=$this->keluhan->Text;
			$newRwtJln->catatan=$this->catatan->Text;
			$newRwtJln->cr_masuk=(string)$this->DDCaraMsk->SelectedValue;
			if($this->DDCaraMsk->SelectedValue == '3' || $this->DDCaraMsk->SelectedValue=='4')
			{
				$newCaraMsk=new SimpanCaraMskRecord();
				$newCaraMsk->no_trans=$this->numCounter('tbt_rawat_jalan',RwtjlnRecord::finder(),'01');
				//key '01' adalah konstanta modul untuk Rawat Jalan
				$newCaraMsk->keterangan=$this->crMskLuar->Text;
				$newCaraMsk->save(); 
			}
			$newRwtJln->save();		
			
			
			$this->suksesMsgCtrl->Visible = true;
			$this->suksesMsg->Text = "Pendaftaran Pasien Rawat Jalan dengan No. Rekam Medis ".$this->cm->Text." <br/> Berhasil Ditambahkan Ke Dalam Database" ;
			$this->setViewState('st_simpan','1');
			$this->batalClicked();
		//$this->msg->Text="Data telah disimpan!";
		//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKartu'));		
		
		//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartu',array('cm'=>$this->cm->Text,'poli'=>$this->DDKlinik->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue)));
		/*	
		if($this->getViewState('backTo')){
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));		
		}else{
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));
		}
		*/	
		}
	}	
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('backTo')){
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));		
		}else{
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		}	
	}

	public function batalClicked()
	{		
		$this->cm->Text=''; 
		$this->crMskLuar->Enabled=false;
		$this->DDDokter->Enabled=false;				
		$this->wktMsk->Text=date("H:i:s");
		$this->tglMsk->Text=date("d-m-Y"); 
		$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
		$this->DDKlinik->dataBind();  
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll();
		$this->DDDokter->dataBind();    
		$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
		$this->DDCaraMsk->dataBind();
		$this->keluhan->Text=''; 
		$this->catatan->Text=''; 
		
		$this->doublePasienMsg->Text='';
		$this->simpanBtn->Enabled = true;
				
		if(!$this->getViewState('st_simpan'))
		{
			$this->suksesMsgCtrl->Visible = false;
			$this->suksesMsg->Text = '' ;
		}
		$this->clearViewState('st_simpan');
				
		$this->cm->focus();
	}

}
?>
