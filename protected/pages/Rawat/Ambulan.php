<?php
class Ambulan extends SimakConf
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
			//$this->crMskLuar->Enabled=false;
			$this->DDCaraMsk->DataSource=TarifAmbulanRecord::finder()->findAll();
			$this->DDCaraMsk->dataBind();
			
			$this->koef->ReadOnly = true;
			$this->wktTunggu->Text = '0';
			
			$this->contentCtrl->Enabled = false;
			
			$this->tujuanLainCtrl->Enabled = false;
			$this->tujuanLainCtrl->Visible = false;
			
			$this->simpanBtn->Enabled=false;	
			$this->errMsg->Text='';	
		}		
    }
	
	public function CBtujuanLuarChanged($sender,$param)
    {
		$this->DDCaraMsk->SelectedValue = 'empty';
		$this->crMskLuar->Text = '';
		$this->koef->Text = '';
		
		if($this->CBtujuanLuar->Checked === true)
		{
			$this->tujuanCtrl->Enabled = false;
			$this->tujuanCtrl->Visible = false;
			
			$this->koef->ReadOnly = false;
			$this->tujuanLainCtrl->Enabled = true;
			$this->tujuanLainCtrl->Visible = true;	
			
			$this->crMskLuar->Focus();
		}
		else
		{
			$this->tujuanCtrl->Enabled = true;
			$this->tujuanCtrl->Visible = true;
			
			$this->koef->ReadOnly = true;
			$this->tujuanLainCtrl->Enabled = false;
			$this->tujuanLainCtrl->Visible = false;
			
			$this->DDCaraMsk->Focus();
		}
	}
	
	public function checkCM($sender,$param)
    {
        // valid if the cm is not found in the database
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$this->clearViewState('jnsPasien',$jnsPasien);
		$this->setViewState('jnsPasien',$jnsPasien);
		$dateNow = date('Y-m-d');	
		$cm = $this->formatCm($this->cm->Text);
		
		if ($jnsPasien == '0')
		{
			//$data = RwtjlnRecord::finder()->find('cm = ? AND flag = ? AND st_alih=? AND tgl_visit=?', $this->formatCm($this->cm->Text),'0','0',$dateNow);
			
			$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  flag = '0' AND 
					  st_alih = '0' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 ";
			
			$sqlAlih = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$cm' AND 
					  flag = '0' AND 
					  st_alih = '1' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tbt_rawat_jalan.tgl_visit, tbt_rawat_jalan.wkt_visit))) / 3600 <= 24 ";
					  		  
			//if($data = RwtjlnRecord::finder()->find('cm = ? AND flag = ? AND st_alih=? AND tgl_visit=?', $this->formatCm($this->cm->Text),'0','0',$dateNow))
			if($data = RwtjlnRecord::finder()->findBySql($sql))
			{
				$this->setViewState('notrans',$data->no_trans);
				$this->simpanBtn->Enabled=true;	
				$this->contentCtrl->Enabled=true;
				$this->errMsg->Text='';
				//$this->modeSatuan->focus();
			}
			elseif($data = RwtjlnRecord::finder()->findBySql($sqlAlih))
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->cm->Text).' sudah alih status ke Rawat Inap !");
					document.all.'.$this->cm->getClientID().'.focus();
				');	
				
				$this->cm->Text ='';
				
				$this->catatan->Text='';
				$this->simpanBtn->Enabled=false;
				$this->contentCtrl->Enabled=false;
			}
			else
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->cm->Text).' Tidak Ada !");
					document.all.'.$this->cm->getClientID().'.focus();
				');	
				$this->cm->Text ='';
				
				$this->catatan->Text='';
				$this->simpanBtn->Enabled=false;
				$this->contentCtrl->Enabled=false;	
			}		
		}else{
			$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0');
			
			if($data)
			{
				$this->setViewState('notrans',$data->no_trans);
				$this->simpanBtn->Enabled=true;	
				$this->contentCtrl->Enabled=true;	
				$this->errMsg->Text='';
				//$this->modeSatuan->focus();
			}
			else
			{			
				$this->getPage()->getClientScript()->registerEndScript
				('','alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->cm->Text).' Tidak Ada !");
					document.all.'.$this->cm->getClientID().'.focus();
				');	
				$this->cm->Text ='';
				
				$this->catatan->Text='';
				$this->simpanBtn->Enabled=false;	
				$this->contentCtrl->Enabled=false;	
			}
		}
		
	}
	
	protected function collectTest($input)
    {
        $indices=$input->SelectedIndices;
        $result='';
        foreach($indices as $index)
        {
            $item=$input->Items[$index];
            $result.=$item->Text;
        }     
		 return $result;   
    }
	
	protected function caraMasuk ()
	{
		/*
		if(($this->DDCaraMsk->SelectedValue !== '18') && ($this->DDCaraMsk->SelectedValue !== NULL) )
		{
			$this->crMskLuar->Enabled=false;
		}
		else
		{
			$this->crMskLuar->Enabled=true;		
		}
		
		$this->crMskLuar->Text = '';	
		*/
		
		$this->koef->text = TarifAmbulanRecord::finder()->findByPk($this->DDCaraMsk->SelectedValue)->harga;
		//$this->catatan->Text=$this->collectTest($this->DDCaraMsk).' '.$this->crMskLuar->Text;
		$crMsk = $this->DDCaraMsk->SelectedValue;
		$this->clearViewState('crMsk',$crMsk);
		$this->setViewState('crMsk',$crMsk);	
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
	
	public function simpanClicked2()
    {
		$tarif = $this->hitungTarif();
		$this->koef->Text = $tarif['tarifStandar'] + $tarif['tarifJamPertama'] + $tarif['tarifJamBerikut'];
	}
	
	
	public function simpanClicked()
    {
		if($this->Page->IsValid)
		{
			$jnsPasien = $this->getViewState('jnsPasien');
			$crMsk = $this->getViewState('crMsk');
			$dateNow = date('Y-m-d');
			$cm = $this->formatCm($this->cm->Text);
			
			if ($jnsPasien == '0') //Pasien Rawat Jalan
			{
				//jika ditemukan perlakuan sama dalam 1 hari 
				if(AmbulanRwtJlnRecord::finder()->find('cm=? AND flag=? AND tgl=?',$cm,'0',$dateNow)) //konfirmasi muncul
				{
					$this->lapTrans();
				}
				else
				{
					$tarif = $this->hitungTarif();
					
					$data= new AmbulanRwtJlnRecord();
					$data->no_trans = $this->numCounter('tbt_rwtjln_ambulan',AmbulanRwtJlnRecord::finder(),'19');
					$data->no_trans_rwtjln = $this->getViewState('notrans');
					$data->cm=$this->formatCm($this->cm->Text);
					$data->tgl=date('y-m-d');
					$data->wkt=date('G:i:s');
					$data->operator=$this->User->IsUserNip;
					$data->tarif_dasar = $tarif['tarifStandar'];
					$data->tarif_tunggu = $tarif['tarifJamPertama'] + $tarif['tarifJamBerikut'];
					$data->tarif = $tarif['tarifStandar'] + $tarif['tarifJamPertama'] + $tarif['tarifJamBerikut'];
					$data->flag='0';			
					//$data->tujuan=$this->tujuan->Text;
					if ($this->CBtujuanLuar->Checked === true)
					{
						$data->tujuan=$crMsk;
						$data->lainnya=$this->crMskLuar->Text;
					}else
					{
						$data->tujuan=$crMsk;
					}
					
					$data->jns_pasien=$jnsPasien;
					$data->Save();			
							
					$this->clearViewState('notrans');
					$this->errMsg->Text='';	
					
					$this->getPage()->getClientScript()->registerEndScript
					('','alert("Data Rekam Billing Ambulan Pasien Rawat jalan Telah Masuk Dalam Database.");
						window.location="index.php?page=Rawat.Ambulan";
					');	
					
					
					//$this->Response->redirect($this->Service->constructUrl('Rawat.Ambulan'));
				}
			}
			else //Pasien Rawat Inap
			{
				//jika ditemukan perlakuan sama dalam 1 hari 
				if(AmbulanRecord::finder()->find('cm=? AND flag=?',$cm,'0')) //konfirmasi muncul
				{
					$this->lapTrans();
				}
				else
				{
					$tarif = $this->hitungTarif();
		
					$data= new AmbulanRecord();
					$data->no_trans = $this->numCounter('tbt_inap_ambulan',AmbulanRecord::finder(),'20');
					$data->no_trans_inap = $this->getViewState('notrans');
					$data->cm=$this->formatCm($this->cm->Text);
					$data->tgl=date('y-m-d');
					$data->wkt=date('G:i:s');
					$data->operator=$this->User->IsUserNip;
					$data->catatan=$this->catatan->Text;
					$data->tarif_dasar = $tarif['tarifStandar'];
					$data->tarif_tunggu = $tarif['tarifJamPertama'] + $tarif['tarifJamBerikut'];
					$data->tarif = $tarif['tarifStandar'] + $tarif['tarifJamPertama'] + $tarif['tarifJamBerikut'];
					$data->flag='0';			
					//$data->tujuan=$this->tujuan->Text;
					if ($this->CBtujuanLuar->Checked === true)
					{
						$data->tujuan=$crMsk;
						$data->lainnya=$this->crMskLuar->Text;
					}
					else
					{
						$data->tujuan=$crMsk;
					}
					
					$data->jns_pasien=$jnsPasien;
					$data->Save();			
							
					$this->clearViewState('notrans');
					$this->errMsg->Text='';	
					
					$this->getPage()->getClientScript()->registerEndScript
					('','alert("Data Rekam Billing Ambulan Pasien Rawat Inap Telah Masuk Dalam Database.");
						window.location="index.php?page=Rawat.Ambulan"; 
					');	
					
					//$this->Response->redirect($this->Service->constructUrl('Rawat.Ambulan'));
				}
			}
		}
	}
	
	public function hitungTarif()
    {
		$hitungTarif = array('tarifStandar'  => '',
						   'tarifJamPertama'  => '',
						   'tarifJamBerikut'    => '');
						   
		$tarif = $this->koef->Text;
		$wktTunggu = intval($this->wktTunggu->Text);
		
		if($wktTunggu > 0)
		{
			$tarifJamPertama = TarifAmbulanTungguRecord::finder()->findByPk('1')->tarif_jam_pertama;
			$tarifJamBerikut = TarifAmbulanTungguRecord::finder()->findByPk('1')->tarif_jam_berikutnya;
		
			if($wktTunggu > 1)
			{
				$tarifJamPertama = $tarifJamPertama * 1;
				$tarifJamBerikut = $tarifJamBerikut * ($wktTunggu - 1);
			}
			else
			{
				$tarifJamPertama = $tarifJamPertama * $wktTunggu;
				$tarifJamBerikut = 0;
			}
		}
		else
		{
			$tarifJamPertama = 0;
			$tarifJamBerikut = 0;
		}
		
		$hitungTarif['tarifStandar'] = $tarif;
		$hitungTarif['tarifJamPertama'] = $tarifJamPertama;
		$hitungTarif['tarifJamBerikut'] = $tarifJamBerikut;
				
		return $hitungTarif;
	}
	
	public function batalClicked($sender,$param)
    {				
		$this->clearViewState('notrans');
		$this->cm->Text ='';
		$this->errMsg->Text='';
		$this->catatan->Text='';
		$this->cm->Focus();
		//$this->modeSatuan->SelectedIndex = 0;			
		//$this->sat->Text = "(Liter/menit x Jumlah Menit)";
		$this->simpanBtn->Enabled=false;	
		
		$this->Response->reload();
	}
	
	public function keluarClicked($sender,$param)
    {				
		$this->Response->redirect($this->Service->constructUrl('Rawat.Ambulan'));
	}
}
?>
