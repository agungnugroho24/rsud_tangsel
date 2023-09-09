<?php
class PasienPulangRwtInap extends SimakConf
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
		
		if(RwtInapRecord::finder()->findAll('status=1 AND st_pulang =0 AND st_resume=1 AND cm = ?',$cm)) 			
		{
			$this->tglTransCtrl->Visible = true;
			
			$sql = "SELECT 
					  no_trans,	
					  tgl_masuk,
					  wkt_masuk,
					  tgl_keluar,
					  jam_keluar
					FROM
					  tbt_rawat_inap
					WHERE
					  cm = '$cm' AND 
					  status = 1 AND
					  st_pulang =0 AND
					  st_resume=1";
			
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$dataTgl .= $row['tgl_masuk'] .',';
			}	
			
			$v = strlen($dataTgl) - 1;
			$var=substr($dataTgl,0,$v);				
			$temp = explode(',',$var);
			
			$this->DDtglTrans->DataSource=$temp;
			$this->DDtglTrans->dataBind();
			
			$this->errMsg->Text='';	
		}
		else
		{
			$this->tglTransCtrl->Visible=false;
				
			$this->errMsg->Text='Pasien dengan No. Register '.$cm.' tidak ada !';
			$this->notrans->Enabled = true;
			$this->notrans->Text = '';
			$this->notrans->focus();
		}
	}
	
	public function DDtglTransChanged()
	{
		$tglMasuk = $this->ambilTxt($this->DDtglTrans);
		$cm=$this->formatCm($this->notrans->Text);
		
		if($this->DDtglTrans->SelectedValue!='')
		{
			$this->cekCmCtrl->Visible = true;
			
			$sql = "SELECT 
					  no_trans,	
					  tgl_masuk,
					  wkt_masuk,
					  tgl_keluar,
					  jam_keluar
					FROM
					  tbt_rawat_inap
					WHERE
					  cm = '$cm' AND
					  tgl_masuk = '$tglMasuk' AND 
					  status = 1";
					  
			$RwtInap = RwtInapRecord::finder()->findBySql($sql);
			$this->setViewState('noTrans',$RwtInap->no_trans);
			
			$this->tglMasuk->Text = $this->convertDate($RwtInap->tgl_masuk,'3');
			$this->setViewState('tgl_masuk',$RwtInap->tgl_masuk);
			$this->wktMasuk->Text = $RwtInap->wkt_masuk;
			
			$this->tglKeluar->Text = $this->convertDate($RwtInap->tgl_keluar,'3');
			$this->setViewState('tgl_keluar',$RwtInap->tgl_keluar);
			$this->wktKeluar->Text = $RwtInap->jam_keluar;
			
			$this->errMsg->Text='';
			
			$this->notrans->Enabled = false;
			$this->cetakBtn->Enabled = true;
			
			$this->RbCrKeluar->focus();
		}
		else
		{
			$this->cekCmCtrl->Visible=false;
			$this->cetakBtn->Enabled = false;
			//$this->batalClicked();
		}
	}		
	
	public function batalClicked()
    {
		$this->cekCmCtrl->Visible=false;
		$this->tglTransCtrl->Visible=false;
		$this->notrans->Enabled = true;
		$this->notrans->Text ='';
		$this->errMsg->Text='';
		
		$this->tglMasuk->Text='';		
		$this->wktMasuk->Text='';		
		$this->tglKeluar->Text='';		
		$this->wktKeluar->Text='';		
		
		$this->modeInput->SelectedValue = '0';
		$this->RbCrKeluar->SelectedValue = '0';
		$this->RbKeadaanKeluar->SelectedValue = '0';
		$this->RbPembayaran->SelectedValue = '0';
		
		$this->cetakBtn->Enabled = false;
		$this->notrans->focus();
		
		$this->clearViewState('noTrans');
		$this->clearViewState('tgl_masuk');
		$this->clearViewState('tgl_keluar');
	}
	
	public function cetakClicked($sender,$param)
    {	
		if($this->IsValid)
		{
			$cm = $this->formatCm($this->notrans->Text);
			$tglMasuk = $this->ambilTxt($this->DDtglTrans);
			$noTrans = $this->getViewState('noTrans');
			$operator=$this->User->IsUserNip;
			
			$pulangRwtInap= new RwtInapPasPulangRecord();			
			$pulangRwtInap->cm=$this->formatCm($this->notrans->Text);			
			$pulangRwtInap->operator=$operator;				
			$pulangRwtInap->mode = $this->modeInput->SelectedValue;
			$pulangRwtInap->cr_keluar = $this->RbCrKeluar->SelectedValue;
			$pulangRwtInap->keadaan_keluar = $this->RbKeadaanKeluar->SelectedValue;
			$pulangRwtInap->pembayaran = $this->RbPembayaran->SelectedValue ;			
			$pulangRwtInap->tgl_masuk = $this->getViewState('tgl_masuk');
			$pulangRwtInap->wkt_masuk = $this->wktMasuk->Text;
			$pulangRwtInap->tgl_keluar = $this->getViewState('tgl_keluar');
			$pulangRwtInap->wkt_keluar = $this->wktKeluar->Text;
			
			$pulangRwtInap->Save();
			
			//update st_pulang tbt_rawat_inap
			$sql = "UPDATE 
					  	tbt_rawat_inap
					SET 
						st_pulang = '1'
					WHERE
					  cm = '$cm' 
					  AND no_trans = '$noTrans'
					  AND status = 1";
					  
			$this->queryAction($sql,'C');
			
			$this->clearViewState('noTrans');
			$this->clearViewState('tgl_masuk');
			$this->clearViewState('tgl_keluar');
			
			$this->Response->redirect($this->Service->constructUrl('Rawat.PasienPulangRwtInapSukses'));
		}
	}
	
	
	
	public function keluarClicked($sender,$param)
	{	
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
}
?>
