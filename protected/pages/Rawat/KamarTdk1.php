<?php
class KamarTdk1 extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('1');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));		
	 }
	 	 	
	public function clearDD()
	{
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='02'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDobgyn->DataSource=$arrData;	
		$this->DDobgyn->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='13'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDanastesi->DataSource=$arrData;	
		$this->DDanastesi->dataBind();
				
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='06'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDdokter->DataSource=$arrData;	
		$this->DDdokter->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDAsDokUtama->DataSource=$arrData;	
		$this->DDAsDokUtama->dataBind();
		
		$idOperasi = $this->DDtindakan->SelectedValue;
		$idKelas = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->kelas;
		
		$sql = "SELECT sewa_ok,ctg,lab FROM tbm_operasi_tarif WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$tarifOK = $row['sewa_ok'];
			$tarifCtg = $row['ctg'];
			$tarifLab = $row['lab'];
		}
		
		//$this->tarifOK->Text=number_format($tarifOK,0,',','.');	
		//$this->ctg->Text=number_format($tarifCtg,0,',','.');	
		$this->tarifOK->Text = $tarifOK;	
		$this->ctg->Text = $tarifCtg;	
		$this->tarifLab->Text = $tarifLab;			
		$this->setViewState('tarifKamarOK',$tarifOK);
		$this->setViewState('tarifCtg',$tarifCtg);
		$this->setViewState('tarifLab',$tarifLab);
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
		$cm=$this->Request['cm'];		
		$mode=$this->Request['goto'];
		$this->cm->text=$cm;
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			if ($mode=='2')
			{
				//$cm = $this->getViewState('cm');	
				$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
				if($data)
				{
					$kateg=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->st_rujukan;
					$this->setViewState('kategST',$kateg);
					
					$tipeRujukan=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->tipe_rujukan;
					$this->setViewState('tipeRujukan',$tipeRujukan);
					
					$this->setViewState('notrans',$data->no_trans);			
					
					$this->DDtindakan->Enabled=true;
					$this->errMsg->Text='';
					if($kateg == '0')
					{
						$this->kategTxt->Text = 'Non Rujukan';
					}
					else
					{
						$this->kategTxt->Text = 'Rujukan';
					}
					
					$this->Page->CallbackClient->focus($this->DDtindakan);
						
					$cm=$this->Request['cm'];
					$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE cm='$cm' AND st='0'";
					$arr = $this->queryAction($sql,'S');
					foreach($arr as $row)
					{				
						$this->cm->text=$row['cm'];
						$this->DDtindakan->SelectedValue=$row['id_opr'];
						$this->ChangedTdk();
					}
					
				}
				else
				{			
					$this->clearViewState('cm');
					$this->errMsg->Text='Pasien rawat inap dengan No. RM <b>'.$cm.'</b> tidak ditemukan !';
					$this->cm->Text = '';
					$this->Page->CallbackClient->focus($this->cm);
					$this->DDtindakan->Enabled=false;
					$this->simpanBtn->Enabled=false;
					
				}
			}
			else
			{
				$this->kategTxt->Text = '-';
				$this->DDtindakan->Enabled = false;
				$this->secondPanel->Display = 'None';
				$this->warningPanel->Display = 'None';
				$this->simpanBtn->Enabled=false;
				
				$sql="SELECT * FROM tbm_operasi_nama";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->DDtindakan->DataSource=$arrData;	
				$this->DDtindakan->dataBind();
				
				$this->clearDD();
			}
		}/*
		else
		{
			//------------------------- Cek No. RM -------------------------
			$cm = $this->getViewState('cm');	
			$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
			if($data)
			{
				$kateg=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->st_rujukan;
				$this->setViewState('kategST',$kateg);
				
				$tipeRujukan=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->tipe_rujukan;
				$this->setViewState('tipeRujukan',$tipeRujukan);
				
				$this->setViewState('notrans',$data->no_trans);			
				
				$this->DDtindakan->Enabled=true;
				$this->errMsg->Text='';
				if($kateg == '0')
				{
					$this->kategTxt->Text = 'Non Rujukan';
				}
				else
				{
					$this->kategTxt->Text = 'Rujukan';
				}
				
				$this->Page->CallbackClient->focus($this->DDtindakan);
			}
			else
			{			
				$this->clearViewState('cm');
				$this->errMsg->Text='Pasien rawat inap dengan No. RM <b>'.$cm.'</b> tidak ditemukan !';
				$this->cm->Text = '';
				$this->Page->CallbackClient->focus($this->cm);
				$this->DDtindakan->Enabled=false;
				$this->simpanBtn->Enabled=false;
				
			}
		}*/		
    }
	
	public function checkCM($sender,$param)
    {
		$this->setViewState('cm',$this->cm->Text);	
	}
	
	public function DDTdkCallBack($sender,$param)
	{	
		$this->firstPanel->render($param->getNewWriter());
		$this->secondPanel->render($param->getNewWriter());
	}
	
	
	public function ChangedTdk()
	{
		if($this->DDtindakan->SelectedValue!='')
		{
			$noTrans = $this->getViewState('notrans');
			$idOpr = $this->DDtindakan->SelectedValue;
			$this->setViewState('idOpr',$idOpr);
			//CEK APA SUDAH ADA OPERASI YG SAMA SEBELUMNYA
			$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND id_opr='$idOpr' AND st='0'";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0 )//jika sudah ada operasi yg sama sebelmnya
			{
				$this->firstPanel->Display = 'None';
				$this->warningPanel->Display = 'None';
				$this->simpanBtn->Enabled = false;
			}
			else
			{
				if($idOpr == '10')//operasi curet
				{
					$this->DDdokter->Enabled = false;
				}
				else
				{
					$this->DDdokter->Enabled = true;
				}
				
				$this->firstPanel->Enabled = false;
				$this->secondPanel->Display = 'Dynamic';
				
				$st=OperasiNamaRecord::finder()->findByPK($this->DDtindakan->SelectedValue)->st;
				if($st==1)
				{
					$this->clearDD();
					$this->simpanBtn->Enabled=true;	
				}
			}
		}
		else
		{
			$this->secondPanel->Display = 'None';
			$this->CBcito->Enabled = true;
		}	
		
		$this->Page->CallbackClient->focus($this->nmOperasi);
	}
	
	public function cekObgyn($sender,$param)
	{
		$param->IsValid = $this->DDobgyn->SelectedValue != '';
	}
	
	public function cekAnastesi($sender,$param)
	{
		$st_operasi=OperasiNamaRecord::finder()->findByPK($this->getViewState('idOpr'))->st_operasi;
		if($st_operasi == '1')
		{
			$param->IsValid = $this->DDanastesi->SelectedValue != '';
		}else{	
			$param->IsValid = $this->DDanastesi->SelectedValue == '';
		}
	}
	
	public function cekAnak($sender,$param)
	{
		$st_operasi=OperasiNamaRecord::finder()->findByPK($this->getViewState('idOpr'))->st_operasi;
		if($st_operasi == '1')
		{
			$param->IsValid = $this->DDdokter->SelectedValue != '';
		}else{	
			$param->IsValid = $this->DDdokter->SelectedValue == '';
		}
	}
	
	
	
	public function simpanClicked($sender,$param)
    {	
		if($this->Page->IsValid)
		{
			$idOperasi = $this->DDtindakan->SelectedValue;
			$idKelas = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->kelas;
			$stRujuk = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->st_rujukan;
			
			$tglNow=date('Y-m-d');
			$wktNow=date('G:i:s');
			$nipTmp=$this->User->IsUserNip;
			
			
			$sql = "SELECT * FROM tbm_operasi_tarif WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{	
				$newOperasi= new OperasiBillingRecord();
				
				$newOperasi->no_trans=$this->getViewState('notrans');
				$newOperasi->cm=$this->cm->Text;
				$newOperasi->id_opr=$this->DDtindakan->SelectedValue;
				$newOperasi->nm_opr=$this->nmOperasi->Text;
				$newOperasi->tgl=$tglNow;		
				
				$newOperasi->dktr_obgyn=$this->DDobgyn->SelectedValue;
				$newOperasi->tarif_obgyn=$row['js_dok_obgyn'];	
				
				$newOperasi->dktr_anastesi=$this->DDanastesi->SelectedValue;
				$newOperasi->tarif_anastesi=$row['js_dok_anestesi'];
								
				$newOperasi->dktr_anak=$this->DDdokter->SelectedValue;
				$newOperasi->tarif_anak=$row['js_dok_anak'];
				
				$newOperasi->ass_dktr=$this->DDAsDokUtama->SelectedValue;
				$newOperasi->tarif_assdktr=$row['asisten'];						
				
				$newOperasi->visite_dokter_obgyn=$row['visit_dok_obgyn'];
				$newOperasi->visite_dokter_anak=$row['visit_dok_anak'];
				$newOperasi->sewa_ok=$row['sewa_ok'];
				$newOperasi->obat=$row['obat'];
				$newOperasi->ctg=$row['ctg'];
				$newOperasi->jpm=$row['jpm'];
				$newOperasi->lab=$this->tarifLab->Text;
				$newOperasi->ambulan=$row['ambulan'];
				
				
				if($stRujuk == '1')//pasien rujukan
				{
					$newOperasi->js_bidan_pengirim=$row['js_bidan_pengirim'];
					
					//INSERT ke tbt_komisi_trans
					$nmPerujuk = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->nm_perujuk;
					
					$newKomisi= new KomisiTransRecord();
					$newKomisi->nama = $nmPerujuk;
					$newKomisi->tgl = $tglNow;
					$newKomisi->wkt = $wktNow;
					$newKomisi->komisi = $row['js_bidan_pengirim'];
					$newKomisi->st_rawat = '1';
					$newKomisi->no_trans_rwt_jln = '';
					$newKomisi->no_trans_rwt_inap = $this->getViewState('notrans');
					$newKomisi->st_dibyr='0';
					//$newKomisi->Save();
					
				}
				else
				{
					$newOperasi->kamar_ibu = '0';
					$newOperasi->kamar_bayi = '0';
					$newOperasi->js_bidan_pengirim='0';
				}
				
				$newOperasi->adm=$row['adm'];
				$newOperasi->materai=$row['materai'];
				$newOperasi->st='0';
				$newOperasi->operator=$nipTmp;
											
				$newOperasi->save();
			
			}
			
			$this->clearViewState('notrans');
			$this->errMsg->Text='';	
			
			$this->Response->redirect($this->Service->constructUrl('Rawat.KamarTdk'));
		}
	}
	
	
	public function batalClicked($sender,$param)
    {	
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
    {	
		$this->Response->redirect($this->Service->constructUrl('Simak'));
	}
	
}
?>
