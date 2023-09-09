<?php
/**
 * DaftarRwtInap class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: DaftarRwtInap.php 1001 2008-04-28 16:07:03Z xue $
 */

/**
 * DaftarRwtInap class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
class DaftarRwtInapBaru extends SimakConf
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
				$this->cm->Focus();
				$this->crMskLuar->Enabled=false;
				$this->wktMsk->Text=date("H:i:s");
				$this->tglMsk->Text=date("d-m-Y"); 
				$this->DDKelas->DataSource=KelasKamarRecord::finder()->findAll();
				$this->DDKelas->dataBind();
				$this->DDJenKmr->DataSource=KamarNamaRecord::finder()->findAll();
				$this->DDJenKmr->dataBind();
				$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
				$this->DDKamar->dataBind();			
				$this->DDKrjPen->DataSource=PekerjaanRecord::finder()->findAll();
				$this->DDKrjPen->dataBind();  
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ?', '1');//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
				$this->DDCaraMsk->dataBind();
			}
		}
	
	public function modeDaftarChanged($sender,$param)
    {	
		$cm = $this->cm->Text ;
		$jnsDaftar = $this->collectSelectionResult($this->modeDaftar);
		
		if($cm!='')
		{		
			if($jnsDaftar == '0')
			{			
				$this->modeInputCtrl->Visible=true;
				$this->noRegCtrl->Visible=false;
				$this->modeInput->focus();					
				$this->modeInput->SelectedIndex = -1;	
			}	
			elseif($jnsDaftar == '1')
			{
				$this->modeInputCtrl->Visible=false;
				$this->noRegCtrl->Visible=true;
				$this->DDtrans->focus();				
				
				$sql="SELECT 
						no_trans
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND flag='0'";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$data .= $row['no_trans'] .',';		
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$temp;
				$this->DDtrans->dataBind();
			}
			
			$this->errMsg->Text='';
			$this->simpanBtn->Enabled = true;
		}
		else
		{
			$this->cm->focus();
			$this->errMsgCm->Text='Isi No. Rekam Medis Terlebih dahulu';
		}
	}
	
	public function checkRM($sender,$param)
    {
        // valid if the username is not found in the database
		$cm = $this->cm->Text ;
		$sql = "SELECT cm FROM tbt_rawat_inap WHERE status = 0	AND cm='$cm'";		
		$tmp = $this->queryAction($sql,'S');
					
		if(RwtInapRecord::finder()->findAll('cm = ? AND status=0',$cm))
		{
			//$param->IsValid=false;	
			$this->modeDaftar->Enabled = false;
			$this->DDtrans->Enabled = false;
			$this->simpanBtn->Enabled = false;
			$this->errMsgCm->Text='No. RM sudah terdaftar !';			
		}
		else
		{
			//$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text);
			if(PasienRecord::finder()->findByPk($this->cm->Text))
			{
				$this->errMsgCm->Text='';
				$this->modeDaftar->Enabled = true;
				$this->DDtrans->Enabled = true;
				$this->simpanBtn->Enabled = true;
			}
			else
			{
				$this->errMsgCm->Text='Maaf, No. RM tersebut tidak ada!';
				$this->modeDaftar->Enabled = false;
				$this->DDtrans->Enabled = false;
				$this->simpanBtn->Enabled = false;	
			}	
			
		}       
		
		$this->modeInput->SelectedIndex = -1;	
    }			   
	
	protected function caraMasuk ()
	{
		if(($this->DDCaraMsk->SelectedValue == '3') || ($this->DDCaraMsk->SelectedValue == '4') || ($this->DDCaraMsk->SelectedValue == '5'))
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
	
	public function checkRegister($sender,$param)
    {
		$this->errMsg->Text='';
		//$asalPasien = substr($this->notrans->Text,6,2);
		$noTrans = $this->ambilTxt($this->DDtrans);
		
		if($this->DDtrans->SelectedValue!='')
		{
			$cekPasien =RwtjlnRecord::finder()->find('no_trans = ? AND flag = ? AND st_alih = ?',$noTrans,'0','0');
			if($cekPasien == NULL)
			{
				$this->errMsg->Text='No. Register tidak ada !';
				$this->DDtrans->Focus();
				$this->simpanBtn->Enabled = false;	
			}
			else
			{
				$this->cm->focus();		
				$this->simpanBtn->Enabled = true;		
			}
		}
		else
		{
			$this->errMsg->Text='';
			$this->simpanBtn->Enabled = false;		
		}
		/*
		if($asalPasien  == '01' || $asalPasien  == '03') //Peralihan dari pasien rawat jalan
		{
			if($asalPasien  == '01' ) //Peralihan dari pasien rawat jalan
			{
				$activeRec = RwtjlnRecord::finder();
				$tbtObatTbl = 'tbt_rawat_jalan';
			}
			elseif($asalPasien  == '03') //Peralihan dari pasien IGD
			{
				$activeRec = IgdRecord::finder();
				$tbtObatTbl = 'tbt_igd';	
			}
						
			$cekPasien = $activeRec->find('no_trans = ? AND flag = ? AND st_alih = ?',$this->notrans->Text,'0','0');
			if($cekPasien == NULL)
			{
				$this->errMsg->Text='No. Register tidak ada !';
				$this->notrans->Focus();
				$this->simpanBtn->Enabled = false;	
			}
			else
			{
				$this->cm->focus();		
				$this->simpanBtn->Enabled = true;		
			}
		}		
		else
		{
			$this->errMsg->Text='No. Register tidak ada !';
			$this->notrans->Focus();
			$this->simpanBtn->Enabled = false;	
		}
		*/
    }	
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$noTrans = $this->numCounter('tbt_rawat_inap',RwtInapRecord::finder(),'02');//key '02' adalah konstanta modul untuk Rawat Inap
			$nipTmp=$this->User->IsUserNip;
			
			$jnsDaftar = $this->collectSelectionResult($this->modeDaftar);
			if($jnsDaftar == '0') //Jika mode daftar normal
			{			
			
			}	
			elseif($jnsDaftar == '1') //Jika mode daftar alih status
			{
				//Insert ke tbt_rawat_khusus
				$newInapKhusus=new InapKhususRecord();		
				$newInapKhusus->cm = $this->cm->Text;
				$newInapKhusus->no_trans_inap = $noTrans;
				$newInapKhusus->no_trans_jln = $this->ambilTxt($this->DDtrans);
				$newInapKhusus->tgl = $this->ConvertDate($this->tglMsk->Text,'2');
				$newInapKhusus->wkt = $this->wktMsk->Text;
				$newInapKhusus->operator = $nipTmp;
				$newInapKhusus->flag = '0';
				$newInapKhusus->save();
				
				//Update status alih tabel asal pasien => tbt_rawat_jln atau tbt_rawat_jln
				$dataPas = RwtjlnRecord::finder()->find('no_trans = ?',$this->ambilTxt($this->DDtrans));
				$dataPas->st_alih = '1';
				$dataPas->save();
			}
			
			//Insert ke tbt_rawat_inap
			$newRwtInap=new RwtInapRecord();
			$newRwtInap->no_trans = $noTrans;
			$newRwtInap->tgl_masuk = $this->ConvertDate($this->tglMsk->Text,'2');
			$newRwtInap->wkt_masuk = $this->wktMsk->Text;
			$newRwtInap->cm = $this->cm->Text;			
			$newRwtInap->kelas = (string)$this->DDKelas->SelectedValue;
			$newRwtInap->kamar = (string)$this->DDKamar->SelectedValue;
			$newRwtInap->jenis_kamar = (string)$this->DDJenKmr->SelectedValue;
			$newRwtInap->nama_pgg = $this->nmPen->Text;
			$newRwtInap->pekerjaan_pgg = (string)$this->DDKrjPen->SelectedValue;
			$newRwtInap->alamat = $this->almPen->Text;
			$newRwtInap->hub_pasien = (string)$this->DDHubPen->SelectedValue;
			$newRwtInap->dokter = (string)$this->DDDokter->SelectedValue;			
			$newRwtInap->cr_masuk = (string)$this->DDCaraMsk->SelectedValue;
			$newRwtInap->diagnosa_masuk = $this->keluhan->Text;			
			$newRwtInap->catatan = $this->catatan->Text;	
			$newRwtInap->status = '0';//Pasien belum keluar masih dirawat inap...						
			$newRwtInap->st_pulang = '0';
			$newRwtInap->st_resume = '0';
			$newRwtInap->st_rujukan = $this->collectSelectionResult($this->modeInput);
			$newRwtInap->save();
			
			$this->msg->Text="Data telah disimpan!";	
			$this->Response->Reload();							
		}
	}	
	
	public function batalClicked()
    {
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}

}
?>
