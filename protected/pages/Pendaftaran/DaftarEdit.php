<?php
class DaftarEdit extends SimakConf
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
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			$this->valPerusAsuransi->Enabled = false;
			$this->valNoAsuransi->Enabled = false;
			$this->valKeluarga->Enabled = false;
			$this->valKabLain->Enabled = false;			
			
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
			$this->DDProv->SelectedValue = '02';
			
			$sql = "SELECT * FROM tbm_kabupaten WHERE id_propinsi='02' ORDER BY nama";
			$this->DDKab->DataSource = KabupatenRecord::finder()->findAllBySql($sql);
			$this->DDKab->dataBind();
			$this->DDKab->SelectedValue = '010';
			
			$idProv = $this->DDProv->SelectedValue;
			$idKab = $this->DDKab->SelectedValue;
			$idFilter = $idProv.''.$idKab;
			
			$sql = "SELECT id,nama FROM tbm_kecamatan WHERE SUBSTRING(id,1,5) = '$idFilter' ORDER BY nama ";
			$this->DDKec->DataSource = KecamatanRecord::finder()->findAllBySql($sql);
			$this->DDKec->dataBind();
			
			$this->DDAgama->DataSource=AgamaRecord::finder()->findAll($criteria);
			$this->DDAgama->dataBind();
			$this->DDAgama->SelectedValue='1';
			
			$this->namaKarPanel->Enabled = false;
			$this->namaKarPanel->Display = 'None';
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDNamaKar->DataSource=PegawaiRecord::finder()->findAll($criteria);
			$this->DDNamaKar->dataBind();
			
			$this->hubPasien->Enabled = false;
			$this->nama->focus();
			
			$this->ambilData();
		}
	}
	
	public function ambilData()
	{
		$pasienRecord = $this->PasienRecord;
		$cm_tmp = $pasienRecord->cm;
		
		$this->cm->Text = $cm_tmp;	
		$this->cm->ReadOnly = true;		
		$this->setViewState('cm',$cm_tmp);
		
		$this->nama->Text = $pasienRecord->nama;
		$this->embel->SelectedValue = $pasienRecord->satuation ;
		$this->nmIbu->Text = $pasienRecord->nm_ibu;
		$this->tmp_lahir->Text = ucwords($pasienRecord->tmp_lahir) ;
		$this->tgl_lahir->Text =  $this->ConvertDate($pasienRecord->tgl_lahir,'1');	
		
		if($pasienRecord->tgl_lahir)
		{
			$temp = $this->ConvertDate($pasienRecord->tgl_lahir,'1');
			$thn=substr($temp,strlen($temp)-4,4);
			$bln=substr($temp,3,2);
			$hari=substr($temp,0,2);
			
			$umur = $this->get_age($thn, $bln, $hari);
			$umur = explode('-',$umur);
			
			$this->umur->text=$umur['2'];
			$this->bln->text=$umur['1'];	
			$this->hari->text=$umur['0'];	
		}
		
		$this->DDKelompok->SelectedValue = $pasienRecord->kelompok ;
		
		if($pasienRecord->kelompok == '02' || $pasienRecord->kelompok == '07' ) //Asuransi
		{
			$this->DDPerusAsuransi->SelectedValue = $pasienRecord->perusahaan;
			$this->noAsuransi->Text = $pasienRecord->no_asuransi;
			
			$this->DDPerusAsuransi->Enabled = true;
			$this->noAsuransi->Enabled = true;
			
			$this->valPerusAsuransi->Enabled = true;
			$this->valNoAsuransi->Enabled = true;			
		}
		else
		{
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->noAsuransi->Text = '';
			
			$this->DDPerusAsuransi->Enabled = false;
			$this->noAsuransi->Enabled = true;
		}
		
		if($this->DDKelompok->SelectedValue == '05' || $this->DDKelompok->SelectedValue == '06')//kelompok keluarga inti / keluarga lain
		{
			$this->DDNamaKar->SelectedValue = $pasienRecord->id_keluarga;
			$this->valKeluarga->Enabled = true;
		}
		else
		{
			$this->DDNamaKar->SelectedValue = 'empty';
		}
		
		$this->DDAgama->SelectedValue = $pasienRecord->agama ;
		
		if($pasienRecord->jkel != '')
			$this->jkel->SelectedIndex = $pasienRecord->jkel;
		
		$this->alamat->Text = ucwords($pasienRecord->alamat_tdk_lengkap);
		$this->no->Text = $pasienRecord->no_rmh;
		$this->rt->Text = $pasienRecord->rt ;
		$this->rw->Text = $pasienRecord->rw ;
		$this->telp->text = $pasienRecord->telp ;
		$this->hp->text = $pasienRecord->hp ;
		
		$this->DDProv->SelectedValue = $pasienRecord->propinsi;
		$this->DDProvChanged();
		$this->DDKab->SelectedValue = $pasienRecord->kabupaten;
		$this->DDKabChanged();
		$this->DDKec->SelectedValue = $pasienRecord->kecamatan;
		$this->DDKecChanged();
		$this->DDKel->SelectedValue = $pasienRecord->kelurahan;
		
		$this->suku->Text = ucwords($pasienRecord->suku);
		
		if($pasienRecord->status != '')
			$this->status->SelectedIndex = $pasienRecord->status;
			
		if($pasienRecord->goldar == 'A')
			$this->golDar->SelectedValue = '1';	
		elseif($pasienRecord->goldar == 'B')
			$this->golDar->SelectedValue = '2';	
		elseif($pasienRecord->goldar == 'AB')
			$this->golDar->SelectedValue = '3';	
		elseif($pasienRecord->goldar == 'O')
			$this->golDar->SelectedValue = '4';	
			
		$this->wni->Text = $pasienRecord->wni ;	
		
		$this->DDKerja->SelectedValue = $pasienRecord->pekerjaan ;
		$this->DDPdk->SelectedValue = $pasienRecord->pendidikan ;
		$this->catatan->Text = $pasienRecord->catatan ;
		
		$this->nmIstri->Text = $pasienRecord->nm_istri;
		$this->nmSuami->Text = $pasienRecord->nm_suami;
		$this->nmAyah->Text = $pasienRecord->nm_ayah;
		
		if($this->DDHubPen->getItems()->findItemByText($pasienRecord->hubungan_pj))
		{
			$this->DDHubPen->SelectedValue = $this->DDHubPen->getItems()->findIndexByText($pasienRecord->hubungan_pj);
			$this->hubPasien->Enabled = false;
			$this->hubPasien->Text = '';
		}
		else
		{	
			$this->DDHubPen->SelectedValue = $this->DDHubPen->getItems()->findIndexByText('Lainnya');
			$this->hubPasien->Enabled = true;		
			$this->hubPasien->Text = $pasienRecord->hubungan_pj;
		}
		
		$this->nmPj->Text = $pasienRecord->nm_pj;
		$this->alamatPj->Text = $pasienRecord->alamat_pj;
		$this->tlpPj->Text = $pasienRecord->telp_pj;
		$this->hpPj->Text = $pasienRecord->hp_pj;
			
		/*
			$this->ambilTxt()= $pasienRecord->hubungan_pj;
		*/
	}
	
	public function checkUmur($sender,$param)
	{
		$temp = trim($this->tgl_lahir->text);
		if ($temp != '')
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
			
		}
		elseif($this->umur->text <> NULL)
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
			
		if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07') //Asuransi / jamper
		{
			if($this->DDKelompok->SelectedValue == '02') //Asuransi
			{
				$this->valNoAsuransi->Enabled = true;
			}
			
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
		elseif($this->DDKelompok->SelectedValue == '05' || $this->DDKelompok->SelectedValue == '06') //Keluarga Pasien
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
		}
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
    {
        // valid if the username is not found in the database
        $param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text)===null;
    }
	
	public function DDHubPenChanged($sender,$param)
	{
		if($this->DDHubPen->SelectedValue != '' ) //hubungan lainnya
		{
			if($this->DDHubPen->SelectedValue == '6')
			{
				$this->hubPasien->Enabled = true;
				$this->Page->CallbackClient->focus($this->hubPasien);	
			}
			else
			{
				$this->hubPasien->Enabled = false;
				$this->hubPasien->Text = '';
				$this->Page->CallbackClient->focus($this->alamatPj);			
			}
		}
		else
		{
			$this->hubPasien->Enabled = false;
			$this->hubPasien->Text = '';
			$this->Page->CallbackClient->focus($this->DDHubPen);
		}
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
	
	
	public function simpanClicked($sender,$param)
	{
		$dateTmp = $this->tgl_lahir->Text;
		$mysqlDate = $this->ConvertDate($dateTmp,'2');
		if($this->IsValid)  // when all validations succeed
        {
		    $pasienRecord = $this->PasienRecord;
			
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
			
			
			$pasienRecord->nama=ucwords($this->nama->Text);
			$pasienRecord->satuation=$this->embel->SelectedValue;
			$pasienRecord->tmp_lahir=ucwords($this->tmp_lahir->Text);
			$pasienRecord->tgl_lahir = $this->ConvertDate($this->tgl_lahir->Text,'2');		
			$pasienRecord->kelompok=(string)$this->DDKelompok->SelectedValue;
			
			if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07') //Asuransi/Jamper
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
			$pasienRecord->save(); 	
			
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Data Pasien dengan No. Rekam Medis '.$this->cm->Text.' telah diperbaharui. Apakah akan mencetak kartu status pasien ?</p>\',timeout: 600000,dialog:{
					modal: true,
					buttons: {
						"Ya": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							konfirmasiCetak(\'ya\');
						},
						Tidak: function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							konfirmasiCetak(\'tidak\');
						}
					}
				}});');	
				/*
			$this->getPage()->getClientScript()->registerEndScript
				('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Data Pasien dengan No. Rekam Medis '.$this->cm->Text.' telah diperbaharui.</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								konfirmasiCetak(\'ya\');
							}
						}
					}});');	
					*/	
            /*
			if($this->getViewState('mode') == '07'){
				$this->Response->redirect($this->Service->constructUrl($this->originPath(),array('cm'=>$this->getViewState('cm'))));
			}else{
				$this->Response->redirect($this->Service->constructUrl($this->originPath()));
			}
			*/
        }
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Edit Data Pasien Gagal<br/>Pastikan pengisian data sudah lengkap !<br/></p>\',timeout: 4000,dialog:{
					modal: true
				}});');		
		}
	}
	
	public function prosesSimpan($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
				
		if($mode == 'ya')
		{	
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakStatusMap').'&cm='.$this->getViewState('cm').'&mode=01');
		}
		else
		{
			if($this->getViewState('mode') == '07')
				$this->Response->redirect($this->Service->constructUrl($this->originPath(),array('cm'=>$this->getViewState('cm'))));
			else
				$this->Response->redirect($this->Service->constructUrl($this->originPath()));
		}
	}
	
	protected function originPath ()
	{
		$mode=$this->Request['mode'];
		if($mode == '01'){
			$balik='Pendaftaran.DaftarCariData';
		}else if($mode == '02'){
			$balik='Pendaftaran.DaftarCariRwtJln';
		}else if($mode == '03'){
			$balik='Pendaftaran.DaftarCariRwtInap';
		}else if($mode == '04'){
			$balik='Pendaftaran.DaftarCariRwtIgd';
		}else if($mode == '05'){
			$balik='Pendaftaran.kunjPas';
		}else if($mode == '06'){
			$balik='Pendaftaran.kunjPasIgd';
		}else if($mode == '07'){
			$balik='Pendaftaran.DaftarRwtJln';
		}else if($mode == '08'){
			$balik='Pendaftaran.DaftarCariPdftrn';
		}

		return $balik;
	}

	protected function getPasienRecord()
	{
		// use Active Record to look for the specified username
		$cm=$this->Request['cm'];
		$this->setViewState('cm',$cm);
		$mode=$this->Request['mode'];
		$this->setViewState('mode',$mode);
		if($mode == '07'){
			$this->setViewState('cm',$cm);
			$this->setViewState('nama',$this->Request['nama']);
			$this->setViewState('jkel',$this->Request['jkel']);
			$this->setViewState('status',$this->Request['status']);
			$this->setViewState('agama',$this->Request['agama']);
			$this->setViewState('tmp_lahir',$this->Request['tmp_lahir']);
			$this->setViewState('tgl_lahir',$this->Request['tgl_lahir']);
			$this->setViewState('alamat',$this->Request['alamat']);
			$this->setViewState('suku',$this->Request['suku']);
			$this->setViewState('rt',$this->Request['rt']);
			$this->setViewState('rw',$this->Request['rw']);
			$this->setViewState('catatan',$this->Request['catatan']);
		}else{
			$pasienRecord=PasienRecord::finder()->findByPk($cm);
			if(!($pasienRecord instanceof PasienRecord))
				throw new THttpException(500,'id tidak benar.');
			return $pasienRecord;
		}

	}
	
	protected function getLuarkotaRecord()
	{
		// use Active Record to look for the specified username
		$cm=$this->Request['cm'];
		$this->setViewState('cm',$cm);
		$mode=$this->Request['mode'];
		$this->setViewState('mode',$mode);
		$luarkotaRecord=LuarkotaRecord::finder()->findByPk($cm);
		//if(!($luarkotaRecord instanceof LuarkotaRecord))
			//throw new THttpException(500,'id tidak benar.');
			return $luarkotaRecord;
		
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
	
    public function batalClicked($sender,$param)
	{
		//$this->Response->redirect($this->Service->constructUrl($this->originPath(),array('cm'=>$this->getViewState('cm'))));
		$this->Response->redirect($this->Service->constructUrl($this->originPath()));
	}
}
?>
