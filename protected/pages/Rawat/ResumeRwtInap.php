<?php
class ResumeRwtInap extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi Rawat Inap
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{	
			$this->cetakBtn->Enabled=false;
			$this->notrans->focus();
		}	
    }
	
	public function checkRegister($sender,$param)
    {
		$cm=$this->formatCm($this->notrans->Text);
		
		if(RwtInapRecord::finder()->findAll('cm = ? AND status=0 AND st_resume=0',$cm)) 			
		{
			$this->cekCmCtrl->Visible = true;
			
			$sql = "SELECT 
					  no_trans,
					  diagnosa_masuk,
					  dokter
					FROM
					  tbt_rawat_inap
					WHERE
					  cm = '$cm' AND 
					  status = 0 AND
					  st_resume=0";
			
			$this->setViewState('noTrans',RwtInapRecord::finder()->findBySql($sql)->no_trans);
			
			$this->tgl->Text = $this->convertDate(date('Y-m-d'),'3');				  
			$this->diagnosaMasuk->Text = RwtInapRecord::finder()->findBySql($sql)->diagnosa_masuk;		  
			
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');//kelompok pegawai '1' adalah untuk dokter
			$this->DDDokter->dataBind();
			$this->DDDokter->SelectedValue = RwtInapRecord::finder()->findBySql($sql)->dokter;
			$this->errMsg->Text='';
			
			$this->notrans->Enabled = false;
			$this->cetakBtn->Enabled = true;
			$this->diagnosaKeluar->focus();
			
			$this->errMsg->Text='';
		}
		else
		{
			$this->cekCmCtrl->Visible=false;
				
			$this->errMsg->Text='Pasien dengan No. Register '.$cm.' tidak ada !';
			$this->notrans->Enabled = true;
			$this->notrans->Text = '';
			$this->notrans->focus();
		}
	}
	
	
	public function DDdokterChanged($sender,$param)
    {
		
    }	
	
	public function batalClicked()
    {
		$this->cekCmCtrl->Visible=false;
		$this->notrans->Enabled = true;
		$this->notrans->Text ='';
		$this->errMsg->Text='';
		$this->tgl->Text='';		
		$this->diagnosaMasuk->Text='';	
		$this->diagnosaKeluar->Text='';	
		$this->bedah->Text='';	
		$this->tdkKhusus->Text='';	
		$this->anjuran->Text='';	
		
		$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');//kelompok pegawai '1' adalah untuk dokter
		$this->DDDokter->dataBind();
		
		$this->cetakBtn->Enabled = false;
		$this->notrans->focus();
		
		$this->clearViewState('noTrans');
	}
	
	public function cetakClicked($sender,$param)
    {	
		if($this->Page->IsValid)
		{
			$noTrans = $this->getViewState('noTrans');	
			$cm=$this->formatCm($this->notrans->Text);		
			$operator=$this->User->IsUserNip;
			
			$resumeRwtInap= new RwtInapResumeRecord();
			$resumeRwtInap->cm=$this->formatCm($this->notrans->Text);
			$resumeRwtInap->tgl=date('Y-m-d');
			$resumeRwtInap->wkt=date('G:i:s');
			$resumeRwtInap->operator=$operator;		
			$resumeRwtInap->diagnosa_masuk=$this->diagnosaMasuk->Text;
			$resumeRwtInap->diagnosa_keluar=$this->diagnosaKeluar->Text;
			$resumeRwtInap->bedah=$this->bedah->Text;
			$resumeRwtInap->tdk_khusus=$this->tdkKhusus->Text;
			$resumeRwtInap->anjuran=$this->anjuran->Text;
			$resumeRwtInap->dokter=$this->DDDokter->SelectedValue;
			$resumeRwtInap->Save();
			
			$sql = "UPDATE 
					  	tbt_rawat_inap
					SET 
						st_resume = '1'
					WHERE
					  cm = '$cm' 
					  AND no_trans = '$noTrans'
					  AND status = 0";
					  
			$this->queryAction($sql,'C');
			
			$this->clearViewState('noTrans');
			
			$this->Response->redirect($this->Service->constructUrl('Rawat.ResumeRwtInapSukses'));
		}
	}
	
	
	
	public function keluarClicked($sender,$param)
	{	
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
