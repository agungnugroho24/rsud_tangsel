<?php
class KamarTdkBaru extends SimakConf
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
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis<>'13' AND spesialis<>'02' AND spesialis<>'06' ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDdokter->DataSource=$arrData;	
		$this->DDdokter->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDAsDokUtama->DataSource=$arrData;	
		$this->DDAsDokUtama->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='13'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDanastesi->DataSource=$arrData;	
		$this->DDanastesi->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDAsDokAnastesi->DataSource=$arrData;	
		$this->DDAsDokAnastesi->dataBind();	
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='02'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDobgyn->DataSource=$arrData;	
		$this->DDobgyn->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDAsDokObgyn->DataSource=$arrData;	
		$this->DDAsDokObgyn->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1'";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDbidan->DataSource=$arrData;	
		$this->DDbidan->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDAsBid->DataSource=$arrData;	
		$this->DDAsBid->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDresusitasi->DataSource=$arrData;	
		$this->DDresusitasi->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->DDparam1->DataSource=$arrData;	
		$this->DDparam1->dataBind();
		
		$this->DDparam2->DataSource=$arrData;	
		$this->DDparam2->dataBind();
		
		$kelas = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->kelas;		
		if($kelas == '1' || $kelas == '2'){
			$tarifOK=KamarOperasiTarifRecord::finder()->find('id_opr = ?',$this->getViewState('jenisOpr'))->kelas1;
		}else if($kelas == '3'){
			$tarifOK=KamarOperasiTarifRecord::finder()->find('id_opr = ?',$this->getViewState('jenisOpr'))->kelas2;
		}else if($kelas == '4'){
			$tarifOK=KamarOperasiTarifRecord::finder()->find('id_opr = ?',$this->getViewState('jenisOpr'))->kelas3;
		}
		$this->tarifOK->Text=number_format($tarifOK,2,',','.');			
		$this->setViewState('tarifKamarOK',$tarifOK);
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->cm->Focus();
			$this->simpanBtn->Enabled=false;
			
			$sql="SELECT * FROM tbm_operasi_nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDtindakan->DataSource=$arrData;	
			$this->DDtindakan->dataBind();
			
			$this->clearDD();
		}		
    }
	
	public function checkCM($sender,$param)
    {
        // valid if the cm is not found in the database
		$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
        if($data)
		{
			$kateg=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0')->st_rujukan;
			$this->setViewState('kategST',$kateg);
			$this->setViewState('notrans',$data->no_trans);			
			$this->simpanBtn->Enabled=true;	
			$this->DDtindakan->Enabled=true;
			$this->errMsg->Text='';
			$this->showKateg->Visible=true;
			if($kateg == '0')
			{
				$this->kategTxt->Text = 'Non Rujukan';
			}
			else
			{
				$this->kategTxt->Text = 'Rujukan';
			}
		}
		else
		{			
			$this->errMsg->Text='Data tidak ada!!';
			$this->cm->Focus();
			
			$this->DDtindakan->Enabled=false;
			$this->simpanBtn->Enabled=false;
			
		}
	}
	
	public function ChangedTdk()
	{
		if($this->DDtindakan->SelectedValue!='')
		{
			//$this->cm->ReadOnly=true;
			//$this->DDtindakan->Enabled=false;
			$b = $this->DDtindakan->SelectedValue;
			$this->setViewState('jenisOpr',$b);
			
			$this->satu->Visible=true;
			$this->DDdokter->Enabled=true;
			
			$st=OperasiNamaRecord::finder()->findByPK($this->DDtindakan->SelectedValue)->st;
			if($st==0)
			{
				$this->nonSalinCtrl->Visible=true;
				$this->SalinCtrl->Visible=false;
				$this->resusitasiCtrl->Visible=false;
			}
			else
			{
				$this->nonSalinCtrl->Visible=false;
				$this->SalinCtrl->Visible=true;
				
				//if($b == '04')
				//{
					$this->resusitasiCtrl->Visible=true;
				//}
				//else
				//{
				//	$this->resusitasiCtrl->Visible=false;
				//}
			}
		}
		else
		{
			$this->satu->Visible=false;
			$this->DDdokter->Enabled=false;
			
		}	
		
		$this->nmOperasi->focus();
		$this->clearDD();
		$this->clearCB();
	}
	
	public function dataDokLuar($componen)
    {
		$sql="SELECT * FROM tbd_asisten_luar WHERE st_kelompok='1' ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		//return $arrData;
		$componen->DataSource=$arrData;	
		$componen->dataBind();
	}
	
	public function dataMedisLuar($componen)
    {
		$sql="SELECT * FROM tbd_asisten_luar WHERE st_kelompok='0' ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		//return $arrData;
		$componen->DataSource=$arrData;	
		$componen->dataBind();
	}
	
	public function dataBidanLuar($componen)
    {
		$sql="SELECT * FROM tbd_asisten_luar WHERE st_kelompok='2' ";
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		//return $arrData;
		$componen->DataSource=$arrData;	
		$componen->dataBind();
	}
	
	
	public function CBdokterCheck()
    {
		if($this->CBdokter->Checked==true) //dokter operator luar dipilih
		{
			$this->dataDokLuar($this->DDdokter);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis<>'13' AND spesialis<>'02' AND spesialis<>'06' ";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDdokter->DataSource=$arrData;	
			$this->DDdokter->dataBind();			
		}
	}
	
	public function CBAsDokUtamaCheck()
    {		
		if($this->CBAsDokUtama->Checked==true) //asisten operator luar dipilih
		{
			$this->dataMedisLuar($this->DDAsDokUtama);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDAsDokUtama->DataSource=$arrData;	
			$this->DDAsDokUtama->dataBind();	
		}
	}

	public function CBanastesiCheck()
    {		
		if($this->CBanastesi->Checked==true) //dokter Anastesi luar dipilih
		{
			$this->dataDokLuar($this->DDanastesi);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='13'";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDanastesi->DataSource=$arrData;	
			$this->DDanastesi->dataBind();
		}
	}
	
	public function CBAsDokAnastesiCheck()
    {
		if($this->CBAsDokAnastesi->Checked==true) //asisten Anastesi luar dipilih
		{
			$this->dataMedisLuar($this->DDAsDokAnastesi);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDAsDokAnastesi->DataSource=$arrData;	
			$this->DDAsDokAnastesi->dataBind();		
		}
	}
	
	public function CBobgynCheck()
    {
		if($this->CBobgyn->Checked==true) //dokter Obgyn luar dipilih
		{
			$this->dataDokLuar($this->DDobgyn);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='02'";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDobgyn->DataSource=$arrData;	
			$this->DDobgyn->dataBind();
		}
	}
	
	public function CBAsDokObgynCheck()
    {
		if($this->CBAsDokObgyn->Checked==true) //asisten Obgyn luar dipilih
		{
			$this->dataMedisLuar($this->DDAsDokObgyn);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDAsDokObgyn->DataSource=$arrData;	
			$this->DDAsDokObgyn->dataBind();	
		}
	}
	
	public function CBbidanCheck()
    {
		if($this->CBbidan->Checked==true) //Bidan luar dipilih
		{
			$this->dataBidanLuar($this->DDbidan);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='5'";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDbidan->DataSource=$arrData;	
			$this->DDbidan->dataBind();	
		}
	}
	
	public function CBAsBidCheck()
    {
		if($this->CBAsBid->Checked==true) //Asisten Bidan luar dipilih
		{
			$this->dataMedisLuar($this->DDAsBid);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDAsBid->DataSource=$arrData;	
			$this->DDAsBid->dataBind();
		}
	}
	
	public function CBresusitasiCheck()
    {
		if($this->CBresusitasi->Checked==true) //Asisten Bidan luar dipilih
		{
			$this->dataDokLuar($this->DDresusitasi);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDresusitasi->DataSource=$arrData;	
			$this->DDresusitasi->dataBind();
		}
	}
	
	public function CBparam1Check()
    {		
		if($this->CBparam1->Checked==true) //Instrumen luar dipilih
		{
			$this->dataMedisLuar($this->DDparam1);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDparam1->DataSource=$arrData;	
			$this->DDparam1->dataBind();
		}
	}
	
	public function CBparam2Check()
    {
		if($this->CBparam2->Checked==true) //Sirkuler luar dipilih
		{
			$this->dataMedisLuar($this->DDparam2);	
		}
		else
		{			
			$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->DDparam2->DataSource=$arrData;	
			$this->DDparam2->dataBind();
		}
	}
	
	public function clearCB()
    {
		$this->CBdokter->Checked=false;
		$this->CBAsDokUtama->Checked=false;
		$this->CBanastesi->Checked=false;
		$this->CBAsDokAnastesi->Checked=false;
		$this->CBobgyn->Checked=false;
		$this->CBAsDokObgyn->Checked=false;
		$this->CBbidan->Checked=false;
		$this->CBAsBid->Checked=false;
		$this->CBparam1->Checked=false;
		$this->CBparam2->Checked=false;
	}
	
	public function simpanClicked($sender,$param)
    {	
		$id_kelas=RwtInapRecord::finder()->find('cm = ?',$this->cm->Text)->kelas;	
		$id_opr=$this->DDtindakan->SelectedValue;			
		$nipTmp=$this->User->IsUserNip;
		
		switch ($id_kelas) {
			case 1:
				$tarif=OperasiTarifRecord::finder()->find('id_opr = ?',$id_opr)->vip;
				break;
			case 2:
				$tarif=OperasiTarifRecord::finder()->find('id_opr = ?',$id_opr)->kelas1;
				break;
			case 3:
				$tarif=OperasiTarifRecord::finder()->find('id_opr = ?',$id_opr)->kelas2;
				break;
			case 4:
				$tarif=OperasiTarifRecord::finder()->find('id_opr = ?',$id_opr)->kelas3;
				break;	
		}
		
		$tarifDok=$tarif;	
		$tarifAssDok=0.15 * $tarif;	
		$tarifDokAnastesi=0.50 * $tarif;
		if($this->DDanastesi->SelectedValue!= ''){
			$tarifAssDokAnastesi=0.15 * $tarif;	
		}else{
			$tarifAssDokAnastesi=0.25 * $tarif;
		}	
		$tarifBidan=0.25 * $tarif;
		$tarifAssBid=0.15 * $tarifBidan;
		if($this->DDparam1)
			$tarifInstrumen=0.1 * $tarif;	
		if($this->DDparam2)	
			$tarifSirkuler=0.05 * $tarif;
		
		$newOperasi= new OperasiBillingRecord();
		
		$newOperasi->no_trans=$this->getViewState('notrans');
		$newOperasi->cm=$this->cm->Text;
		$newOperasi->id_opr=$this->DDtindakan->SelectedValue;
		$newOperasi->nm_opr=$this->nmOperasi->Text;
		$newOperasi->tgl=date('Y-m-d');
		
		$st=OperasiNamaRecord::finder()->findByPK($this->DDtindakan->SelectedValue)->st;
			if($st==0)
			{
				$newOperasi->dktr_utama=$this->DDdokter->SelectedValue;
				$newOperasi->tarif_dktr=$tarifDok;
				
				if($this->DDAsDokUtama->SelectedValue!='')
				{
					$newOperasi->ass_dktr=$this->DDAsDokUtama->SelectedValue;
					$newOperasi->tarif_assdktr=$tarifAssDok;
				}				
			}
			else
			{
				$newOperasi->dktr_obgyn=$this->DDobgyn->SelectedValue;
				$newOperasi->tarif_obgyn=$tarifDok;
				
				if($this->DDAsDokObgyn->SelectedValue!='')
				{
					$newOperasi->ass_obgyn=$this->DDAsDokObgyn->SelectedValue;
					$newOperasi->tarif_assobgyn=$tarifAssDok;
				}			
				
				if($this->DDbidan->SelectedValue!='')
				{
					$newOperasi->bidan=$this->DDbidan->SelectedValue;
					$newOperasi->tarif_bidan=$tarifBidan;
				}	
				
				if($this->DDAsBid->SelectedValue!='')
				{
					$newOperasi->ass_bidan=$this->DDAsBid->SelectedValue;
					$newOperasi->tarif_assbidan=$tarifAssBid;
				}	
				
				if($this->DDresusitasi->SelectedValue!='')
				{
					$newOperasi->dktr_resusitasi=$this->DDresusitasi->SelectedValue;
					//$newOperasi->tarif_bidan=$tarifBidan;
				}
				
			}
			
		if($this->DDanastesi->SelectedValue!='')
		{
			$newOperasi->dktr_anastesi=$this->DDanastesi->SelectedValue;
			$newOperasi->tarif_anastesi=$tarifDokAnastesi;
		}	
		
		if($this->DDAsDokAnastesi->SelectedValue!='')
		{
			$newOperasi->ass_anastesi=$this->DDAsDokAnastesi->SelectedValue;
			$newOperasi->tarif_assanastesi=$tarifAssDokAnastesi;
		}
		
		$newOperasi->pm1=$this->DDparam1->SelectedValue;
		$newOperasi->tarif_pm1=$tarifInstrumen;
		$newOperasi->pm2=$this->DDparam2->SelectedValue;
		$newOperasi->tarif_pm2=$tarifSirkuler;
		$newOperasi->pm3='9999';
		
		if($this->getViewState('kategST') == "0"){
			$tarifSewaOK = $this->getViewState('tarifKamarOK');
		}
		else
		{
			$oprST=OperasiNamaRecord::finder()->findByPk($this->DDtindakan->SelectedValue)->st;
			$oprID = $this->DDtindakan->SelectedValue;
			$tarifSewaOK = $this->getViewState('tarifKamarOK');
			if($oprST == '0')//Non Kebidanan atau Persalinan
			{				
				$tarifSewaOK += 150000;
			}
			else
			{
				switch ($oprID) 
				{
					case '04':
						$tarifSewaOK += 250000;
					break;
					case '05':
						$tarifSewaOK += 150000;
					break;
					case '06':
						$tarifSewaOK += 100000;
					break;
				}	
			}	
		}
		
		//Hitung biaya resusitasi jika operasi => operasi persalinan
		$oprST=OperasiNamaRecord::finder()->findByPk($this->DDtindakan->SelectedValue)->st;
		$oprID = $this->DDtindakan->SelectedValue;		
		if($oprST != '0')//Operasi Kebidanan atau Persalinan
		{				
			//if($oprID == '04')//Operasi Caessar
			//{
				if($this->DDresusitasi->SelectedValue != '' && $this->DDbidan->SelectedValue == '')
				{
					switch ($id_kelas) 
					{
						case 1:
							$tarifResus=150000;
							break;
						case 2:
							$tarifResus=150000;
							break;
						case 3:
							$tarifResus=100000;
							break;
						case 4:
							$tarifResus=75000;
							break;
					}
				}
				elseif($this->DDresusitasi->SelectedValue == '' && $this->DDbidan->SelectedValue != '')
				{
					switch ($id_kelas) 
					{
						case 1:
							$tarifResus=0.8 * 150000;
							break;
						case 2:
							$tarifResus=0.8 * 150000;
							break;
						case 3:
							$tarifResus=0.8 * 100000;
							break;
						case 4:
							$tarifResus=0.8 * 75000;
							break;
					}
				}			
			//}
			
			$tarifSewaOK = $tarifSewaOK + $tarifResus;
		}
			
		
		$newOperasi->tarif_pm3=$tarifSewaOK;
		
		//Jika dokter luar / asisten luar / bidan luar / ass. bidan luar / paramedis 1 / paramedis 2 tidak dipilih => st_asisten_luar='0'
		//Jika salah satu ada yg dipilih => st_asisten_luar='1'
		if($this->CBdokter->Checked==false
			&& $this->CBAsDokUtama->Checked==false
			&& $this->CBanastesi->Checked==false
			&& $this->CBAsDokAnastesi->Checked==false
			&& $this->CBobgyn->Checked==false
			&& $this->CBAsDokObgyn->Checked==false
			&& $this->CBbidan->Checked==false
			&& $this->CBAsBid->Checked==false
			&& $this->CBparam1->Checked==false
			&& $this->CBparam2->Checked==false)
		{
			$newOperasi->st_asisten_luar='0';
		}
		else
		{
			$newOperasi->st_asisten_luar='1';
		}
		
		$newOperasi->Save();			
		
		
		//Insert jml askeb ke tbt_inap_askeb jika yg dipakai = kamar bersalin
		//if(OperasiNamaRecord::finder()->findByPk($this->DDtindakan->SelectedValue)->st == '1')
		//{
			$dataAskeb=new InapAskebRecord;
			$dataAskeb->cm=$this->cm->Text;
			$dataAskeb->no_trans=$this->getViewState('notrans');	
			$dataAskeb->tgl=date('Y-m-d');
			$dataAskeb->wkt=date('G:i:s');
			$dataAskeb->operator=$nipTmp;
			$dataAskeb->flag='0';
			$dataAskeb->catatan='';
			$dataAskeb->tarif=7500;
			$dataAskeb->Save();
		//}
		
		//Insert jml askep_ok ke tbt_inap_askep_ok
		$dataAskep_ok=new InapAskepOkRecord;
		$dataAskep_ok->cm=$this->cm->Text;
		$dataAskep_ok->no_trans=$this->getViewState('notrans');	
		$dataAskep_ok->tgl=date('Y-m-d');
		$dataAskep_ok->wkt=date('G:i:s');
		$dataAskep_ok->operator=$nipTmp;
		$dataAskep_ok->flag='0';
		$dataAskep_ok->catatan='';
		$dataAskep_ok->tarif=20000;
		$dataAskep_ok->Save();
		
		//$updateRwtInap=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->cm->Text,'0');
		//$updateRwtInap->status='1';
		//$updateRwtInap->Save();	
		
		$this->clearViewState('notrans');
		$this->errMsg->Text='';	
		
		$this->Response->redirect($this->Service->constructUrl('Rawat.KamarTdk'));
	}
	
	public function batalClicked($sender,$param)
    {	
		$this->Response->Reload();
	}
}
?>
