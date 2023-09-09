<?php
class SinarPas extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	

	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->cm->Focus();
			$this->simpanBtn->Enabled=false;		
		}		
    }
	
	public function checkCM($sender,$param)
    {
        // valid if the cm is not found in the database
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0');
        if($data)
		{
			$this->setViewState('notrans',$data->no_trans);
			$this->simpanBtn->Enabled=true;	
			$this->errMsg->Text='';
			$this->modeSatuan->focus();
		}
		else
		{			
			$this->errMsg->Text='Data tidak ada!!';
			$this->catatan->Text='';
			$this->cm->Focus();
					
			$this->simpanBtn->Enabled=false;	
		}
	}
	
	public function modeSatuanChanged()
    {
		$satuan = $this->collectSelectionResult($this->modeSatuan);
		if($satuan == '0')
		{
			$this->sat->Text = "Hari";
		}
		elseif($satuan == '1')
		{
			$this->sat->Text = "Jam";
		}
		
		$this->koef->focus();
	}
	
	public function lapTrans()
    {	
		$this->mainPanel->Visible = false;
		$this->konfPanel->Visible = true;
		
		$cm = $this->formatCm($this->cm->Text);
		$this->cmKonf->Text= $cm;
		
		$this->simpanBtn->Enabled=false;
		$this->batalBtn->focus();
	}
	
	public function simpanClicked()
    {
		if($this->Page->IsValid)
			{
			$cm = $this->formatCm($this->cm->Text);
			$noTransRwtInap = RwtInapRecord::finder()->find('cm = ? AND status = ?', $cm,'0')->no_trans;
			
			//jika ditemukan perlakuan sama dalam 1 hari 
			if(InapSinarRecord::finder()->find('cm=? AND flag=?',$cm,'0')) //konfirmasi muncul
			{
				$this->lapTrans();
			}
			else
			{
				$satuan = $this->collectSelectionResult($this->modeSatuan);
				if($satuan == '0') //satuan literan
				{
					$tarif = abs($this->koef->Text) * 100000;
				}
				elseif($satuan == '1') //satuan tabung
				{
					$tarif = abs($this->koef->Text) * (100000/24);
				}
				
				$tarifBulat = $this->bulatkan($tarif);
				$sisa = $tarifBulat - $tarif;
				
				if($sisa > 0)
				{			
					//-------- Insert Harga Sisa Pembulatan ke tbt_inap_sinar_sisa -----------------
					$InapSinarSisa= new InapSinarSisaRecord();
					$InapSinarSisa->no_trans=$this->getViewState('notrans');
					$InapSinarSisa->jumlah = $sisa;
					$InapSinarSisa->tgl=date('y-m-d');
					$InapSinarSisa->Save();	
				}
				
				$newInapOksigen= new InapSinarRecord();
				$newInapOksigen->no_trans=$this->getViewState('notrans');
				$newInapOksigen->cm=$this->formatCm($this->cm->Text);
				$newInapOksigen->tgl=date('y-m-d');
				$newInapOksigen->wkt=date('G:i:s');
				$newInapOksigen->operator=$this->User->IsUserNip;
				$newInapOksigen->catatan=$this->catatan->Text;
				$newInapOksigen->tarif=$tarifBulat;
				$newInapOksigen->flag='0';
				$newInapOksigen->Save();			
						
				$this->clearViewState('notrans');
				$this->errMsg->Text='';	
				
				$this->Response->redirect($this->Service->constructUrl('Rawat.SinarPas'));
			}
		}
	}
	
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->cm->Text ='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		$this->modeSatuan->SelectedIndex = 0;			
		$this->sat->Text = "(Liter/menit x Jumlah Menit)";
		$this->simpanBtn->Enabled=false;	
		
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
    {				
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
	
	
}
?>
