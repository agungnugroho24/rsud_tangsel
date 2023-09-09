<?php

class DaftarBaru extends SimakConf
{

	 public function onInit($param)
	 {		
			parent::onInit($param);
			$tmpVar=$this->authApp('10');
			if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
				$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	 public function onLoad($param)
	{
		parent::onLoad($param);		
		$kab = $this->getViewState('idKab');		
			
		if(!$this->IsPostBack && !$this->IsCallBack)    
		{	
			$this->cmCtrl->Visible = false;
			
			$this->valPerusAsuransi->Enabled = false;
			$this->valNoAsuransi->Enabled = false;
			$this->valKeluarga->Enabled = false;
			$this->valKabLain->Enabled = false;
			
			$sql = "SELECT cm FROM tbd_pasien order by cm desc";
			$no = PasienRecord::finder()->findBySql($sql);
			
			if($no==NULL)//jika kosong bikin ndiri
			{				
				//$urut='010000';
				$urut='000001';
			}else
			{
				/*
				$urut=intval(substr($no->getColumnValue('cm'),-6,6));
				$no1=intval(substr($no->getColumnValue('cm'),0,2));
				$no2=intval(substr($no->getColumnValue('cm'),2,2));
				$no3=intval(substr($no->getColumnValue('cm'),4,2));
				*/
				if ($urut==999999)
				{
					$urut='000001';					
				}else
				{/*
					if ($no1==99)
					{
						$urut1='99';
					}else
					{
						$urut1=$no1+1;
						$urut1=substr('00',0,0-strlen($urut1)).$urut1;	
					}
					
					if ($no2==99)
					{
						$urut2='99';
					}elseif (($no1==98)  OR ($no1<>99))
					{
						$urut2='00';
					}else
					{
						$urut2=$no2+1;
						$urut2=substr('00',0,0-strlen($urut2)).$urut2;	
					}
					
					if ($no3==99)
					{
						$urut3='99';
					}elseif (($no2==98) OR ($no2<>99)) 
					{
						$urut3='00';
					}else
					{
						$urut3=$no3+1;
						$urut3=substr('00',0,0-strlen($urut3)).$urut3;	
					}
					
					$urut=$urut1.$urut2.$urut3;*/
					$urut=intval(substr($no->getColumnValue('cm'),-6,6));
					$urut=$urut+1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;					
				}
			}
			
			$this->cm->Text = $urut;	
			$this->cm->ReadOnly = true;		
			$this->setViewState('cm',$urut);
			
			
			if($this->Request['cm'] && $this->Request['mode']=='09' )
			{
				$this->cm->Text=$this->Request['cm'];
				$mode=$this->Request['mode'];
				$this->setViewState('mode',$mode);
				$this->nama->focus();	
				//$this->nama->Text=$mode;
			}
			else
			{		
				$this->RBstDaftar->focus();	
			}
			
			//$this->tmp_lahir->Text="Brebes";
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->embel->DataSource=SatuationRecord::finder()->findAll($criteria);
			$this->embel->dataBind(); 
			
			$this->DDKerja->DataSource=PekerjaanRecord::finder()->findAll($criteria);
			$this->DDKerja->dataBind(); 
			$this->DDPdk->DataSource=PendidikanRecord::finder()->findAll($criteria);
			$this->DDPdk->dataBind(); 			
			$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll($criteria);
			$this->DDKelompok->dataBind();
			$this->DDKelompok->SelectedValue='01';
			
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDPerusAsuransi->dataBind();
			
			$this->DDPerusAsuransi->Enabled = false;
			$this->noAsuransi->Enabled = false;
			
			//$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			//$this->DDKontrak->dataBind();
			$sql = "SELECT * FROM tbm_propinsi ORDER BY nama";
			$this->DDProv->DataSource = KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDProv->dataBind();
			$this->DDProv->SelectedValue = '28';
			
			$sql = "SELECT * FROM tbm_kabupaten WHERE id_propinsi='28' ORDER BY nama";
			$this->DDKab->DataSource = KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKab->dataBind();
			$this->DDKab->SelectedValue = '040';
			
			$idProv = $this->DDProv->SelectedValue;
			$idKab = $this->DDKab->SelectedValue;
			$idFilter = $idProv.''.$idKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			$this->DDKec->DataSource = KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			
			/*
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind();
			*/
			$this->DDAgama->DataSource=AgamaRecord::finder()->findAll($criteria);
			$this->DDAgama->dataBind();
			$this->DDAgama->SelectedValue='1';
			/*
			$this->DDKontrak->Enabled=false;
			$this->DDKec->Enabled=false;	
			$this->KecLuar->Enabled=false;
			$this->DDKel->Enabled=false;	
			$this->KelurahanLuar->Enabled=false;
			*/
			
			$this->namaKarPanel->Enabled = false;
			$this->namaKarPanel->Display = 'None';
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDNamaKar->DataSource=PegawaiRecord::finder()->findAll($criteria);
			$this->DDNamaKar->dataBind();
			
			$this->hubPasien->Enabled = false;
			$this->nama->focus();
		}
					
	}
	
	public function RBstDaftarCallBack($sender,$param)
	{
		$this->panel->render($param->getNewWriter());
	}
	
	public function tlpValidate($sender,$param)
	{
		if(isset($this->telp->Text) || isset($this->hp->Text)){
			$this->$param = true;
		}else{
			$this->$param = false;
		}	
	}
	
	public function RBstDaftarChanged($sender,$param)
	{
		$stDaftar = $this->collectSelectionResult($this->RBstDaftar);
		if($stDaftar == '1' )//pasien Lama
		{
			$this->cmCtrl->Visible = true;
			$this->cm->Text = '';		
			$this->cm->ReadOnly = false;
						
			$this->valCm1->Enabled = true;
			$this->valCm2->Enabled = true;
			
			//$this->Page->CallbackClient->focus($this->cm);
			$this->msg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
		}
		else //pasien baru
		{
			$this->cmCtrl->Visible = false;
			$this->valCm1->Enabled = false;
			$this->valCm2->Enabled = false;
			
			$sql = "SELECT cm FROM tbd_pasien order by cm desc";
			$no = PasienRecord::finder()->findBySql($sql);
			
			if($no==NULL)//jika kosong bikin ndiri
			{	
				$urut='000001';
			}else
			{
				if ($urut==999999)
				{
					$urut='000001';					
				}else
				{
					$urut=intval(substr($no->getColumnValue('cm'),-6,6));
					$urut=$urut+1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;					
				}
			}
			
			$this->cm->Text = $urut;	
			$this->cm->ReadOnly = true;	
			$this->setViewState('cm',$urut);
			$this->simpanBtn->Enabled = true;
			$this->msg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->nama->getClientID().'.focus();
				</script>';
		}
	}
	
	
	public function cekCM($sender,$param)
	{
		if($this->cm->Text != '')
		{
			$arr = PasienRecord::finder()->findByPk($this->cm->Text);
			 if(count($arr) > 0)
			 {
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("No. Rekam  '.$this->cm->Text.' sudah ada. \nSilakan isi dengan No. Rekam Medis yang lain.");
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
				$this->cm->Text = '';
				$this->simpanBtn->Enabled = false;
				//$param->IsValid=false;
			}
			else
			{
				$this->simpanBtn->Enabled = true;
				//$param->IsValid=true;
			}
		}
	}
	
	public function DDKelompokCallback($sender,$param)
	{
		$this->namaKarPanel->render($param->getNewWriter());
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		$this->DDNamaKar->SelectedValue = 'empty';
		$this->DDPerusAsuransi->SelectedValue = 'empty';
		
		$this->valPerusAsuransi->Enabled = false;
		$this->valNoAsuransi->Enabled = false;
		$this->valKeluarga->Enabled = false;
		
		$this->noAsuransi->Text = '';
			
		//if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07') //Asuransi / jamper
		if($this->DDKelompok->SelectedValue == '02') //NON UMUM
		{
			//if($this->DDKelompok->SelectedValue == '02' ) //Asuransi
			//{
				$this->valNoAsuransi->Enabled = true;
			//}
			
			$this->valPerusAsuransi->Enabled = true;
			
			$this->namaKarPanel->Enabled = false;
			$this->namaKarPanel->Display = 'None';
			
			$idKelPerus = $this->DDKelompok->SelectedValue;
			
			$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' AND st = '0' ORDER BY nama";
			$data = $this->queryAction($sql,'S');
			
			$this->DDPerusAsuransi->DataSource = $data;
			$this->DDPerusAsuransi->dataBind();
			
			$this->DDPerusAsuransi->Enabled = true;
			$this->noAsuransi->Enabled = true;
		}
		/*elseif($this->DDKelompok->SelectedValue == '05' || $this->DDKelompok->SelectedValue == '06') //Keluarga Pasien
		{
			$this->valKeluarga->Enabled = true;
			
			$this->namaKarPanel->Enabled = true;
			$this->namaKarPanel->Display = 'Dynamic';
			$this->DDPerusAsuransi->Enabled = false;
			$this->noAsuransi->Enabled = false;
		}
		else
		{
			$this->namaKarPanel->Enabled = false;
			$this->namaKarPanel->Display = 'None';
			$this->DDPerusAsuransi->Enabled = false;
			$this->noAsuransi->Enabled = false;
			$this->umur->focus();
		}*/
	}	
	
	public function DDProvChanged()
	{
		$this->DDKab->SelectedValue = 'empty';
		
		if($this->DDProv->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			
			$sql = "SELECT id,nama FROM tbm_kabupaten WHERE id_propinsi = '$idProv' ORDER BY nama ";
			
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKab->dataBind();
			$this->DDKab->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKab);
		}
		else
		{	
			$this->DDKab->DataSource='';
			$this->DDKab->dataBind();
			//$this->DDKab->Enabled = false;	
			
			$this->Page->CallbackClient->focus($this->DDProv);
		}
		
		$this->DDKabChanged();		
		$this->DDKecChanged();
	}
	
	public function DDKabChanged()
	{
		$this->DDKec->SelectedValue = 'empty';
		
		if($this->DDKab->SelectedValue != ''){
			$idProv = $this->DDProv->SelectedValue;
			$idKab = $this->DDKab->SelectedValue;
			$idFilter = $idProv.''.$idKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			$this->DDKec->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKec);
			$this->DDKecChanged();
		}
		else
		{
			$this->DDKec->DataSource='';
			$this->DDKec->dataBind();
			//$this->DDKec->Enabled = false;	
			
			if($this->DDProv->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDProv);
			elseif($this->DDKab->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDKab);
			else
				$this->Page->CallbackClient->focus($this->DDKec);
		}
		
	}
	
	public function DDKecChanged()
	{
		$this->DDKel->SelectedValue = 'empty';
		
		if($this->DDKec->SelectedValue != ''){
			$idKec = $this->DDKec->SelectedValue;
			$idFilter = $idKec;
			
			$sql = "SELECT id,nama FROM tbm_kelurahan WHERE SUBSTRING(id,1,7) = '$idFilter' ORDER BY nama ";
			
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAllBySql($sql);
			$this->DDKel->dataBind();
			$this->DDKel->Enabled = true;	
			$this->Page->CallbackClient->focus($this->DDKel);
		}
		else
		{
			$this->DDKel->DataSource='';
			$this->DDKel->dataBind();
			//$this->DDKel->Enabled = false;	
			
			if($this->DDProv->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDProv);
			elseif($this->DDKab->SelectedValue == '')
				$this->Page->CallbackClient->focus($this->DDKab);
			else
				$this->Page->CallbackClient->focus($this->DDKec);
			
		}
	}
	/*
	public function selectionChangedKab()
	{		
		$kab = $this->DDKab->SelectedValue;	
		$this->setViewState('idKab',$kab,'');
		$this->kabLain->Text='';
		
		$this->valKabLain->Enabled = false;
		
		if ($kab != '' && $kab != '06'){ 			
			//$this->KecLuar->Enabled=false;
			//$this->KelurahanLuar->Enabled=false;

			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = 'id_kab = :idkab';
			$criteria->Parameters[':idkab'] = $kab;
			$criteria->OrdersBy['nama'] = 'asc';

			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();				
			$this->kabLain->Enabled=false;
		}
		elseif ($kab=='06') //Lainnya
		{
			//$this->KecLuar->Enabled=false;
			//$this->KelurahanLuar->Enabled=false;
			$this->valKabLain->Enabled = true;
			
			$criteria = new TActiveRecordCriteria;
			$criteria->Condition = 'id_kab = :idkab';
			$criteria->Parameters[':idkab'] = $kab;
			$criteria->OrdersBy['nama'] = 'asc';

			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();
			
			$this->kabLain->Enabled=true;
			
		}elseif($kab==''){
			$criteria = new TActiveRecordCriteria;
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDKec->SelectedIndex=-1;
			$this->DDKel->SelectedIndex=-1;
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll($criteria);
			$this->DDKec->dataBind(); 
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
			$this->DDKel->dataBind();  
			//$this->DDKec->Text ="--Silakan Pilih--";
			//$this->DDKel->Text ="--Silakan Pilih--";
			
			$this->DDKec->Enabled=false;				
			$this->DDKel->Enabled=false;				
		}
		else{
			$this->kabLain->Enabled=false;
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			$this->KecLuar->focus();
			$this->DDKec->Enabled=false;
			$this->DDKel->Enabled=false;
		}	
	} 
	*/
	
	public function selectionChangedKec($sender,$param)
	{		
		$kec = $this->DDKec->SelectedValue;	
		$this->setViewState('idKec',$kec,'');
		
		$kec = $this->DDKec->SelectedValue;
		$kab = $this->getViewState('idKab');
		
		$criteria = new TActiveRecordCriteria;
		$criteria->Condition = 'id_kab = :idkab AND id_kec = :idkec';
		$criteria->Parameters[':idkab'] = $kab;
		$criteria->Parameters[':idkec'] = $kec;
		$criteria->OrdersBy['nama'] = 'asc';

		$this->DDKel->DataSource=KelurahanRecord::finder()->findAll($criteria);
		$this->DDKel->dataBind(); 	
		$this->DDKel->Enabled=true;
		$this->DDKel->focus();			
	}
	
	public function selectionChangedKel($sender,$param)
	{		
		$this->suku->focus();			
	}		
	
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}	
	
	public function checkRM($sender,$param)
    {/*
		//$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text)===null;		
		$checkRM=$this->cm->Text;
		$this->setViewState('cm',$checkRM);
		$sql="SELECT * FROM du_pasien WHERE cm='$checkRM'";		
		$data=$this->queryAction($sql,'S');		
		$dummy=PasienRecord::finder()->findByPk($this->cm->Text);
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
				$this->errMsg->text='';					
				$this->nama->focus();					
					}	
			
		}else if ($dummy){
			$this->errMsg->Text='No. CM tersebut sudah ada!';
			$cm=$this->getViewState('cm');
			foreach($dummy as $row)
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
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
		}*/
		
    }
	
	public function namaChanged()
	{
		if($this->nama->Text != '')
		{
			$this->nmPj->Text = ucwords($this->nama->Text);
			$this->DDHubPen->SelectedValue = '6';
			$this->DDHubPenChanged();
			$this->hubPasien->Text = 'Pasein yang Bersangkutan';
		}
		else
		{
			$this->nmPj->Text = '';
			$this->DDHubPen->SelectedValue = 'empty';
			$this->hubPasien->Text = '';
		}
		
		$this->msg->Text = '    
			<script type="text/javascript">
				document.all.'.$this->embel->getClientID().'.focus();
			</script>';
	}
	
	public function get_age($birth_year, $birth_month, $birth_day)
	{
		$birth_month_days = date( t, mktime(0, 0, 0, $birth_month, $birth_day, $birth_year) );
	
		$current_year = date(Y);
		$current_month = date(n);
		$current_day = date(j);
		$current_month_days = date(t);
	
		if($current_month > $birth_month)
		{
			$yy = $current_year - $birth_year;
			$mm = $current_month - $birth_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_month_days;
			}
		}
	
		if($current_month < $birth_month)
		{
			$yy = $current_year - $birth_year - 1;
			$mm = 12 - $birth_month + $current_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_day;
			}
		}
	
		if($current_month==$birth_month)
		{
			if($current_day == $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = 0;
				$dd = 0;
			}
			
			if($current_day < $birth_day)
			{
				$yy = $current_year - $birth_year - 1;
				$mm = $birth_month + $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				
				if($dd > $current_month_days)
				{
					$mm += 1;
					$dd -= $current_day;
				}
			}
			
			if($current_day > $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				if($dd > $current_month_days)
				{
				$mm += 1;
				$mm -= $current_month;
				$dd -= $current_month_days;
				}
			}
		}
	
		$age = $dd.'-'.$mm.'-'.$yy;
		return $age;

    }
	
	public function checkUmur($sender,$param)
	{
		$temp = $this->tgl_lahir->text;
		if ($temp <> NULL)
		{
			$this->umur->text='';
			
			$thn=substr($temp,strlen($temp)-4,4);
			$bln=substr($temp,3,2);
			$hari=substr($temp,0,2);	
			
			
			$umur = $this->get_age($thn, $bln, $hari);
			$umur = explode('-',$umur);
			
			$this->umur->text=$umur['2'];
			$this->bln->text=$umur['1'];	
			$this->hari->text=$umur['0'];	
			
			$this->DDKelompok->focus();
			/*
			$thn=date('Y')-intval(substr($temp,strlen($temp)-4,4));
			$bln=date('m')-intval(substr($temp,3,2));
			$hari=date('d')-intval(substr($temp,0,2));	
			
			if ($hari<0){
				$bln=$bln-1;
				$hari=date('t')+$hari;
			}
			
			$this->umur->text=$thn;
			$this->bln->text=$bln;	
			$this->hari->text=$hari;	
			$this->DDKelompok->focus();
			*/
		}elseif($this->umur->text <> NULL)
			{
				$this->tgl_lahir->text='';
				//$tahun = date('Y') - $this->umur->Text . '-01-01';
				
				$tahun = intval(date('Y')) - $this->umur->Text;
				
				if(date('m') == $this->bln->Text)
					$bulan = date('m');
				else
					$bulan = intval(date('m')) - $this->bln->Text;
						
				$hari = intval(date('d')) - $this->hari->Text;
				
				if(strlen($bulan)==1)
					$bulan='0'.$bulan;
				
				if (strlen($hari)==1)
					$hari='0'.$hari;
					
				//$thn = $this->ConvertDate($tahun,'1');
				//$this->tgl_lahir->text = $hari.'-'.$bulan.'-'.$tahun;
				$this->tgl_lahir->text = '01-'.date('m').'-'.$tahun;
				
				//$this->tgl_lahir->text = $tahun;
				//$this->tgl_lahir->text=$bulan;
				//$this->test->text=$hari.'-'.$bulan.'-'.$tahun;
				$this->DDAgama->focus();
			}
		} 
		
	public function checkNM($sender,$param)
	{
		/*
		$nama=ucwords($this->nama->Text);				
		$nama .= '%';
		$chNama=PasienRecord::finder()->find('nama LIKE ?', $nama);
		if($chNama)
		{
			$this->cm->Text=$chNama->cm;
			$this->nama->Text=$chNama->nama;
			$this->tmp_lahir->Text=$chNama->tmp_lahir;
			$this->tgl_lahir->Text=$this->ConvertDate($chNama->tgl_lahir,'1');
			$this->DDKelompok->SelectedValue=$chNama->kelompok;
			if($chNama->perusahaan)
				$this->DDKontrak->SelectedValue=$chNama->perusahaan;
			$this->DDAgama->SelectedValue=$chNama->agama;
			$this->jkel->SelectedIndex=$chNama->jkel;
			$this->alamat->Text=$chNama->alamat;
			$this->alamat->Text=$chNama->alamat;
			$this->rt->Text=$chNama->rt;
			$this->rw->Text=$chNama->rw;
			$this->DDKab->SelectedValue=$chNama->kabupaten;
			$this->DDKec->SelectedValue=$chNama->kecamatan;
			$this->DDKel->SelectedValue=$chNama->kelurahan;
			$this->suku->Text=$chNama->suku;
			$this->status->SelectedIndex=$chNama->status;
			$this->wni->Text=$chNama->wni;
			$this->DDKerja->SelectedValue=$chNama->pekerjaan;
			$this->DDPdk->SelectedValue=$chNama->pendidikan;
			$this->catatan->Text=$chNama->catatan;		
		}
	*/
	}

	public function checkAll($sender,$param)
	{/*
		$valIgnore=$this->ignore->Value;
		//$checkRM=$this->cm->Text;
		//$this->setViewState('cm',$checkRM);		
		$checkNM=$this->nama->text;
		$checkAL=$this->alamat->text;
		$checkTgl=$this->tgl_lahir->text;
		$date=$this->ConvertDate($checkTgl,'2');
		$checkUmr=$this->umur->text;
		$this->setViewState('nm',$checkNM);
		$this->setViewState('alm',$checkAL);		
		$this->setViewState('tgl',$date);
		$this->setViewState('umr',$checkUmr);
		if($valIgnore == "1")
		{
			if ($checkTgl==''){			
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND umur='$checkUmr'";
			}elseif ($checkUmr==''){
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND tgl_lahir='$date'";	
			}else{
			$sql="SELECT * FROM du_pasien WHERE nama = '$checkNM' AND alamat like '%$checkAL%' AND umur='$checkUmr' AND tgl_lahir='$date'";
			}
		}else{
			if ($checkTgl==''){			
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND umur='$checkUmr'";
			}elseif ($checkUmr==''){
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND tgl_lahir='$date'";	
			}else{
			$sql="SELECT * FROM du_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%' AND umur='$checkUmr' AND tgl_lahir='$date'";
			}
		}
		
		$data=$this->queryAction($sql,'S');
		foreach($data as $row)
		{
			$cek= $row['cm'];
		}
		$sql="SELECT * FROM tbd_pasien WHERE nama like '$checkNM%' AND alamat like '%$checkAL%'";		
		$dummy=$this->queryAction($sql,'S');
		foreach($dummy as $row)
		{
			$cek= $row['cm'];
		}
		if ($cek){
			$this->cm->text=$cek;
			$checkRM=$this->cm->Text;
		}else{
			$checkRM=$this->cm->Text;
			$this->setViewState('cm',$checkRM);
		}
		
		$this->setViewState('cm',$checkRM);
		$sql="SELECT * FROM du_pasien WHERE cm='$cek'";		
		$data=$this->queryAction($sql,'S');
		$dummy=PasienRecord::finder()->findByPk($cek);
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->rt->focus();			
		}
		else if($dummy===null)
		{					
			if($data)
			{			
				$this->errMsg->text="Harap diedit data lama telah ditemukan!";
				$cm=$this->getViewState('cm');
				//$cm=$cek;
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
				$this->errMsg->text='';					
				$this->rt->focus();					
				//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
			}	
			
		}else if ($dummy){
			$this->errMsg->Text='No. CM tersebut sudah ada!';
			$cm=$this->getViewState('cm');
			foreach($dummy as $row)
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
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarEdit',array('cm'=>$cm,'nama'=>$nama,'tmp_lahir'=>$tmp_lahir,'tgl_lahir'=>$tgl_lahir,'alamat'=>$alamat,'suku'=>$suku,'rt'=>$rt,'rw'=>$rw,'catatan'=>$catatan,'jkel'=>$jkel,'agama'=>$agama,'status'=>$status)));
		}*/
	}
	
	public function cekKab($sender,$param)
	{
		 if($param->Value == ($this->DDKab->SelectedValue == ''))
            $param->IsValid=false;
	}
	
	public function DDHubPenChanged()
	{
		if($this->DDHubPen->SelectedValue != '' ) //hubungan lainnya
		{
			if($this->DDHubPen->SelectedValue == '6')
			{
				$this->hubPasien->Enabled = true;
				//$this->Page->CallbackClient->focus($this->hubPasien);	
			}
			else
			{
				$this->hubPasien->Enabled = false;
				$this->hubPasien->Text = '';
				//$this->Page->CallbackClient->focus($this->alamatPj);			
			}
		}
		else
		{
			$this->hubPasien->Enabled = false;
			$this->hubPasien->Text = '';
			//$this->Page->CallbackClient->focus($this->DDHubPen);
		}
	}
	
	
	public function alamatPjClicked($sender,$param)
	{
		if($this->alamatPjChecklist->Checked === true)
		{
			
			if($this->alamat->Text != '')
			{
				$alamatLengkap = ucwords($this->alamat->Text).' No. '.$this->no->Text;
				
				if($this->rt->Text != '')
				{
					$alamatLengkap .= ' RT. '.$this->rt->Text;
				}
				
				if($this->rw->Text != '')
				{
					$alamatLengkap .= ' RW. '.$this->rw->Text;
				}
				/*
				if($this->KelurahanLuar->Text != '')
				{
					$alamatLengkap .= ' Kel. '.$this->KelurahanLuar->Text;
				}
				*/
				if($this->KecLuar->Text != '')
				{
					$alamatLengkap .= ' Kec. '.$this->KecLuar->Text;
				}
				
				if($this->DDKab->SelectedValue != '' && $this->DDKab->SelectedValue != '06') //Kota Jakarta
				{
					//$alamatLengkap .= ' Jakarta';
					$alamatLengkap .= ' '.$this->ambilTxt($this->DDKab);
					
				}
				else
				{
					$alamatLengkap .= ' '.$this->kabLain->Text;
				}
			}
						
			$this->alamatPj->Text = $alamatLengkap;	
		}
		else
		{
			$this->alamatPj->Text = '';
		}	
	}
	
	public function tlpPjClicked($sender,$param)
	{
		if($this->telp->Text != '')
		{
			if($this->tlpPjChecklist->Checked === true)
			{
				$this->tlpPj->Text = $this->telp->Text;
			}
			else
			{
				$this->tlpPj->Text = '';
			}
		}	
	}
	
	public function hpPjClicked($sender,$param)
	{
		if($this->hp->Text != '')
		{
			if($this->hpPjChecklist->Checked === true)
			{
				$this->hpPj->Text = $this->hp->Text;
			}
			else
			{
				$this->hpPj->Text = '';
			}	
		}
	}
	
	public function simpanData($cm)
	{
		if($this->tgl_lahir->Text)
		{
			$dateTmp = $this->tgl_lahir->Text;
			$mysqlDate = $this->ConvertDate($dateTmp,'2');
		}
		elseif($this->umur->Text){
			$mysqlDate = date('Y') - $this->umur->Text . '-01-01';
		}	
		
		$alamatLengkap = ucwords($this->alamat->Text).' No. '.$this->no->Text;
				
		if($this->rt->Text != '')
		{
			$alamatLengkap .= ' RT. '.$this->rt->Text;
		}
		
		if($this->rw->Text != '')
		{
			$alamatLengkap .= ' RW. '.$this->rw->Text;
		}
		
		$alamatLengkap .= ' Kel. '.$this->ambilTxt($this->DDKel);
		$alamatLengkap .= ' Kec. '.$this->ambilTxt($this->DDKec);
		$alamatLengkap .= ' '.$this->ambilTxt($this->DDKab);
		
		/*
		if($this->KelurahanLuar->Text != '')
		{
			$alamatLengkap .= ' Kel. '.$this->KelurahanLuar->Text;
		}
		
		if($this->KecLuar->Text != '')
		{
			$alamatLengkap .= ' Kec. '.$this->KecLuar->Text;
		}
		
		
		if($this->DDKab->SelectedValue != '' && $this->DDKab->SelectedValue != '06') 
		{
			//$alamatLengkap .= ' Jakarta';
			$alamatLengkap .= ' '.$this->ambilTxt($this->DDKab);
		}
		else
		{
			$alamatLengkap .= ' '.$this->kabLain->Text;
		}
		*/
		
		// populates a UserRecord object with user inputs
		$pasienRecord=new PasienRecord;
		$pasienRecord->cm = $cm;
		$pasienRecord->nama=ucwords($this->nama->Text);
		$pasienRecord->satuation=$this->embel->SelectedValue;
		$pasienRecord->tmp_lahir=ucwords($this->tmp_lahir->Text);
		$pasienRecord->tgl_lahir=(string)$mysqlDate;		
		$pasienRecord->kelompok=(string)$this->DDKelompok->SelectedValue;
		
		//if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07') //Asuransi / jamper
		if($this->DDKelompok->SelectedValue == '02') //NON UMUM
		{
			$pasienRecord->perusahaan = $this->DDPerusAsuransi->SelectedValue;
			$pasienRecord->no_asuransi = $this->noAsuransi->Text;
		}
		else
		{
			$pasienRecord->perusahaan = '';
			$pasienRecord->no_asuransi = '';
		}
		
		if($this->DDKelompok->SelectedValue == '05' || $this->DDKelompok->SelectedValue == '06')//kelompok keluarga inti / keluarga lain
		{
			$pasienRecord->id_keluarga = $this->DDNamaKar->SelectedValue;
		}
		else
		{
			$pasienRecord->id_keluarga = '';
		}
		
		//$pasienRecord->perusahaan=(string)$this->DDKontrak->Value;
		//$pasienRecord->perusahaan='00';				
		$this->collectSelectionListResult($this->status);
		$pasienRecord->status=(string)$this->collectSelectionListResult($this->status);
		$pasienRecord->agama=(string)$this->DDAgama->SelectedValue;						
		$pasienRecord->jkel=(string)$this->collectSelectionListResult($this->jkel);
		$pasienRecord->alamat = $alamatLengkap;
		$pasienRecord->alamat_tdk_lengkap = ucwords($this->alamat->Text);
		$pasienRecord->no_rmh = $this->no->Text;
		$pasienRecord->rt=$this->rt->Text;
		$pasienRecord->rw=$this->rw->Text;
		$pasienRecord->telp=$this->telp->text;
		$pasienRecord->hp=$this->hp->text;
		$pasienRecord->propinsi = $this->DDProv->SelectedValue;
		$pasienRecord->kabupaten = $this->DDKab->SelectedValue;
		$pasienRecord->kecamatan = $this->DDKec->SelectedValue;
		$pasienRecord->kelurahan = $this->DDKel->SelectedValue;
		
		if($this->DDKab->SelectedValue != '') 
		{
			//$pasienRecord->kabupaten_nama = 'Kota Jakarta';
			$pasienRecord->kabupaten_nama = $this->ambilTxt($this->DDKab);
		}
		else
		{
			$pasienRecord->kabupaten_nama=$this->kabLain->Text;
		}
		
		$pasienRecord->kecamatan_nama = $this->ambilTxt($this->DDKec);
		$pasienRecord->kelurahan_nama = $this->ambilTxt($this->DDKel);
		
		//$pasienRecord->kecamatan_nama = $this->KecLuar->Text;
		//$pasienRecord->kelurahan_nama = $this->KelurahanLuar->Text;
		
		$pasienRecord->suku=ucwords($this->suku->Text);
		$pasienRecord->umur=$this->umur->Text;
		
		$this->collectSelectionListResult($this->golDar);
		//$pasienRecord->goldar=(string)$this->collectSelectionListResult($this->golDar);
		$pasienRecord->goldar = $this->ambilTxt($this->golDar);
		
		$pasienRecord->wni=$this->wni->Text;
		$pasienRecord->pekerjaan=(string)$this->DDKerja->SelectedValue;
		$pasienRecord->pendidikan=(string)$this->DDPdk->SelectedValue;
		$pasienRecord->catatan=ucwords($this->catatan->Text);   
		
		$pasienRecord->nm_istri = $this->nmIstri->Text;   
		$pasienRecord->nm_suami = $this->nmSuami->Text;   
		$pasienRecord->nm_ayah = $this->nmAyah->Text;   
		$pasienRecord->nm_ibu = $this->nmIbu->Text;   
		
		if($this->DDHubPen->SelectedValue != '' ) 
		{
			if($this->DDHubPen->SelectedValue == '6') //hubungan lainnya
			{
				$pasienRecord->hubungan_pj = $this->hubPasien->Text;
			}
			else
			{
				$pasienRecord->hubungan_pj = $this->ambilTxt($this->DDHubPen);		
			}
		}
		else
		{
			$pasienRecord->hubungan_pj = '';
		}
		
		$pasienRecord->nm_pj = $this->nmPj->Text;   
		$pasienRecord->alamat_pj = $this->alamatPj->Text;   
		$pasienRecord->telp_pj = $this->tlpPj->Text;   
		$pasienRecord->hp_pj = $this->hpPj->Text;   
				 
		/*
		$kab = $this->getViewState('idKab');
		if ( $kab != '01'){
			$lkRecord=new LuarkotaRecord;
			$lkRecord->cm=$this->cm->Text;
			$lkRecord->id_kab=$this->DDKab->SelectedValue;
			$lkRecord->kecamatan=ucwords($this->KecLuar->Text);
			$lkRecord->kelurahan=ucwords($this->KelurahanLuar->Text);
			$lkRecord->save();
		}
		*/
		
		// saves to the database via Active Record mechanism
		$pasienRecord->save(); 			
		// redirects the browser to the homepage
		
		$this->getPage()->getClientScript()->registerEndScript
		('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Cetak Kartu Pasien ?<br/><br/></p>\',timeout: 600000,dialog:{
				modal: true,
				buttons: {
					"Ya": function() {
						konfirmasiCetak(\'ya\',\''.$cm.'\');
					},
					Tidak: function() {
						konfirmasiCetak(\'tidak\',\''.$cm.'\');
					}
				}
			}});');	
		
		/*$this->getPage()->getClientScript()->registerEndScript
		('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Data Pasien Baru sudah tersimpan.<br/><br/></p>\',timeout: 6000000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						konfirmasiCetak(\'ya\',\''.$cm.'\');
					}
				}
			}});');
		
		/*
		if($this->getViewState('mode')=='09')
		{

			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
		}
		else
		{		
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text)));
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text)));
		}
		*/
	}
	
	public function simpanClicked($sender,$param)
	{
		if($this->IsValid)  // when all validations succeed
		{      
			if($this->RBstDaftar->SelectedValue == '0')
			{
				$sql = "SELECT cm FROM tbd_pasien order by cm desc";
				$no = PasienRecord::finder()->findBySql($sql);
				
				if($no==NULL)//jika kosong bikin ndiri
					$urut='000001';
				else
				{
					if ($urut==999999)
						$urut='000001';
					else
					{
						$urut=intval(substr($no->getColumnValue('cm'),-6,6));
						$urut=$urut+1;
						$urut=substr('000000',-6,6-strlen($urut)).$urut;					
					}
				}
				
				$cm = $urut;
				$this->simpanData($cm);
			}
			else
			{
				$cm = $this->formatCm($this->cm->Text);
				
				$arr = PasienRecord::finder()->findByPk($cm);
				 if(count($arr) > 0) //no CM sudah ada dalam database 
				 {
					$this->msg->Text = '    
					<script type="text/javascript">
						unmaskContent();
						alert("No. Rekam  '.$this->cm->Text.' sudah ada. \nSilakan ganti dengan No. Rekam Medis yang lain.");
						document.all.'.$this->cm->getClientID().'.focus();
					</script>';
					$this->cm->Text = '';
				}
				else
				{
					$this->msg->Text = '';
					$this->simpanData($cm);
				}
			}			
		}	
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Pastikan pengisian data telah sesuai !<br/><br/></p>\',timeout: 4000,dialog:{
				modal: true
			}});');				
		}		
	}
	
	public function tes($sender,$param)
	{
		$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Cetak Kartu Pasien ?</p>\',timeout: 200000,dialog:{
					modal: true,
					buttons: {
						"Ya": function() {
							konfirmasiCetak(\'ya\');
						},
						Tidak: function() {
							konfirmasiCetak(\'tidak\');
						}
					}
				}});');		
	}
	
	public function prosesSimpan($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		$cm = $param->CallbackParameter->noCm;
		
		$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$cm,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
		}
		else
		{
			
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakStatusMap',array('cm'=>$cm,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
		}
		
		/*this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));*/
	}
	
	public function simpanClicked2($sender,$param)
	{			
		if($this->tgl_lahir->Text){
			$dateTmp = $this->tgl_lahir->Text;
			$mysqlDate = $this->ConvertDate($dateTmp,'2');
		}else if($this->umur->Text){
			$mysqlDate = date('Y') - $this->umur->Text . '-01-01';
		}	
		if($this->IsValid)  // when all validations succeed
        {      
			$alamatLengkap = ucwords($this->alamat->Text).' No. '.$this->no->Text;
			
			if($this->rt->Text != '')
			{
				$alamatLengkap .= ' RT. '.$this->rt->Text;
			}
			
			if($this->rw->Text != '')
			{
				$alamatLengkap .= ' RW. '.$this->rw->Text;
			}
			/*
			if($this->KelurahanLuar->Text != '')
			{
				$alamatLengkap .= ' Kel. '.$this->KelurahanLuar->Text;
			}
			*/
			if($this->KecLuar->Text != '')
			{
				$alamatLengkap .= ' Kec. '.$this->KecLuar->Text;
			}
			
			if($this->DDKab->SelectedValue != '' && $this->DDKab->SelectedValue != '06') //Kota Jakarta
			{
				//$alamatLengkap .= ' Jakarta';
				$alamatLengkap .= ' '.$this->ambilTxt($this->DDKab);
				
			}
			else
			{
				$alamatLengkap .= ' '.$this->kabLain->Text;
			}
			
			// populates a UserRecord object with user inputs
            $pasienRecord=new PasienRecord;
            $pasienRecord->cm=$this->cm->Text;
            $pasienRecord->nama=ucwords($this->nama->Text);
            $pasienRecord->satuation=$this->embel->SelectedValue;
			$pasienRecord->tmp_lahir=ucwords($this->tmp_lahir->Text);
            $pasienRecord->tgl_lahir=(string)$mysqlDate;		
  			$pasienRecord->kelompok=(string)$this->DDKelompok->SelectedValue;
				
			//if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07') //Asuransi / Jamper
			if($this->DDKelompok->SelectedValue == '02')//NON UMUM
			{
				$pasienRecord->perusahaan = $this->DDPerusAsuransi->SelectedValue;
				$pasienRecord->no_asuransi = $this->noAsuransi->Text;
			}
			else
			{
				$pasienRecord->perusahaan = '';
				$pasienRecord->no_asuransi = '';
			}
			
			if($this->DDKelompok->SelectedValue == '05' || $this->DDKelompok->SelectedValue == '06')//kelompok keluarga inti / keluarga lain
			{
				$pasienRecord->id_keluarga = $this->DDNamaKar->SelectedValue;
			}
			else
			{
				$pasienRecord->id_keluarga = '';
			}
			
            //$pasienRecord->perusahaan=(string)$this->DDKontrak->Value;
			//$pasienRecord->perusahaan='00';				
			$this->collectSelectionListResult($this->status);
 			$pasienRecord->status=(string)$this->collectSelectionListResult($this->status);
            $pasienRecord->agama=(string)$this->DDAgama->SelectedValue;						
 			$pasienRecord->jkel=(string)$this->collectSelectionListResult($this->jkel);
			$pasienRecord->alamat = $alamatLengkap;
            $pasienRecord->alamat_tdk_lengkap = ucwords($this->alamat->Text);
			$pasienRecord->no_rmh = $this->no->Text;
 			$pasienRecord->rt=$this->rt->Text;
            $pasienRecord->rw=$this->rw->Text;
			$pasienRecord->telp=$this->telp->text;
			$pasienRecord->hp=$this->hp->text;
			$pasienRecord->kabupaten=(string)$this->DDKab->SelectedValue;
            $pasienRecord->kecamatan=(string)$this->DDKec->SelectedValue;
			$pasienRecord->kelurahan=(string)$this->DDKel->SelectedValue;
			
			if($this->DDKab->SelectedValue != '') //Kota surabaya
			{
				//$pasienRecord->kabupaten_nama = 'Kota Jakarta';
				$pasienRecord->kabupaten_nama =$this->ambilTxt($this->DDKab);
			}
			else
			{
				$pasienRecord->kabupaten_nama=$this->kabLain->Text;
			}
			
			
			$pasienRecord->kecamatan_nama=$this->KecLuar->Text;
			//$pasienRecord->kelurahan_nama=$this->KelurahanLuar->Text;
            $pasienRecord->suku=ucwords($this->suku->Text);
            $pasienRecord->umur=$this->umur->Text;
			
			$this->collectSelectionListResult($this->golDar);
 			//$pasienRecord->goldar=(string)$this->collectSelectionListResult($this->golDar);
			$pasienRecord->goldar = $this->ambilTxt($this->golDar);
			
			$pasienRecord->wni=$this->wni->Text;
			$pasienRecord->pekerjaan=(string)$this->DDKerja->SelectedValue;
			$pasienRecord->pendidikan=(string)$this->DDPdk->SelectedValue;
			$pasienRecord->catatan=ucwords($this->catatan->Text);   
			
			$pasienRecord->nm_istri = $this->nmIstri->Text;   
			$pasienRecord->nm_suami = $this->nmSuami->Text;   
			$pasienRecord->nm_ayah = $this->nmAyah->Text;   
			$pasienRecord->nm_ibu = $this->nmIbu->Text;   
			
			if($this->DDHubPen->SelectedValue != '' ) 
			{
				if($this->DDHubPen->SelectedValue == '6') //hubungan lainnya
				{
					$pasienRecord->hubungan_pj = $this->hubPasien->Text;
				}
				else
				{
					$pasienRecord->hubungan_pj = $this->ambilTxt($this->DDHubPen);		
				}
			}
			else
			{
				$pasienRecord->hubungan_pj = '';
			}
			
			$pasienRecord->nm_pj = $this->nmPj->Text;   
			$pasienRecord->alamat_pj = $this->alamatPj->Text;   
			$pasienRecord->telp_pj = $this->tlpPj->Text;   
			$pasienRecord->hp_pj = $this->hpPj->Text;   
			         
			/*
			$kab = $this->getViewState('idKab');
			if ( $kab != '01'){
				$lkRecord=new LuarkotaRecord;
				$lkRecord->cm=$this->cm->Text;
				$lkRecord->id_kab=$this->DDKab->SelectedValue;
				$lkRecord->kecamatan=ucwords($this->KecLuar->Text);
				$lkRecord->kelurahan=ucwords($this->KelurahanLuar->Text);
				$lkRecord->save();
			}
			*/
			
			$pasienRecord->st_baru_lama = '0';   

			// saves to the database via Active Record mechanism
            $pasienRecord->save(); 			
            // redirects the browser to the homepage
			
			if($this->getViewState('mode')=='09')
			{

				//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text,'mode'=>$this->getViewState('mode'))));
			}
			else
			{		
				//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakKartuPas',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text)));
				$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln',array('cm'=>$this->cm->Text,'nama'=>ucwords($this->nama->Text),'embel'=>ucwords($this->embel->text),'tglLahir'=>$this->tgl_lahir->Text,'jkel'=>$this->collectSelectionListResult($this->jkel),'alamat'=>ucwords($this->alamat->Text . ' No. ' . $this->no->Text),'rt'=>$this->rt->Text,'rw'=>$this->rw->Text)));
			}
			
        }			
	}
	
	public function kabBaruBtnClicked($sender,$param)
	{
		$url = "index.php?page=Admin.KabBaruModal&idProv=".$this->DDProv->SelectedValue;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'Tambah Data Kabupaten')");		
	}
	
	public function kecBaruBtnClicked($sender,$param)
	{
		$url = "index.php?page=Admin.KecBaruModal&idProv=".$this->DDProv->SelectedValue."&idKab=".$this->DDKab->SelectedValue;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'Tambah Data Kecamatan')");		
	}
	
	public function kelBaruBtnClicked($sender,$param)
	{
		$url = "index.php?page=Admin.KelBaruModal&idProv=".$this->DDProv->SelectedValue."&idKab=".$this->DDKab->SelectedValue."&idKec=".$this->DDKec->SelectedValue;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'Tambah Data Kelurahan')");		
	}
	
	public function prosesModalKab($sender,$param)
	{
		$this->DDProvChanged();
		$idBaru = $param->CallbackParameter->Id;
		
		if($idBaru)
		{	
			$this->DDKab->SelectedValue = $idBaru;
			$this->DDKabChanged();
		}
				
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
	public function prosesModalKec($sender,$param)
	{
		//$this->DDProvChanged();
		$this->DDKabChanged();
		$idBaru = $param->CallbackParameter->Id;
		
		if($idBaru)
		{	
			$this->DDKec->SelectedValue = $idBaru;
			$this->DDKecChanged();
		}
				
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
	public function prosesModalKel($sender,$param)
	{
		//$this->DDProvChanged();
		//$this->DDKabChanged();
		$this->DDKecChanged();
		$idBaru = $param->CallbackParameter->Id;
		
		if($idBaru)
		{	
			$this->DDKel->SelectedValue = $idBaru;
		}
				
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	public function batalClicked($sender,$param)
	{		
		$this->Response->reload();
	}

}


?>
