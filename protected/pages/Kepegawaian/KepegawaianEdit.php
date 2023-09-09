<?php
class KepegawaianEdit extends SimakConf
{
	public function onInit($param)
	{
		parent::onInit($param);
		if(!$this->IsPostBack)  // if the page is initially requested
		{
		// Retrieves the existing user data. This is equivalent to:
		// $userRecord=$this->getUserRecord();
		$pegawaiRecord=$this->PegawaiRecord;	
		if ($pegawaiRecord->kabupaten == "01"){
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			//$this->DDKec->Enabled=true;
			//$this->DDKel->Enabled=true;
		}else{
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			//$this->DDKec->Enabled=false;
			//$this->DDKel->Enabled=false;
		}				
			// Populates the input controls with the existing user data
			$this->IdPegawai->Text=$pegawaiRecord->id;
			$this->Nama->Text=$pegawaiRecord->nama;
			$this->TmpLahir->Text=$pegawaiRecord->tmp_lahir;
			$this->TglLahir->Text=$this->ConvertDate($pegawaiRecord->tgl_lahir,'1');			
			if($pegawaiRecord->kelompok)
				$this->DDKelompok->SelectedValue=$pegawaiRecord->kelompok;
			if($pegawaiRecord->status)
				$this->DDStatus->SelectedValue=$pegawaiRecord->status;
			$this->stKawin->SelectedIndex=$pegawaiRecord->st_kawin;	
			if($pegawaiRecord->agama)
				$this->DDAgama->SelectedValue=$pegawaiRecord->agama;
			$this->JKel->SelectedIndex=$pegawaiRecord->jkel;	
			$this->Alamat->Text=$pegawaiRecord->alamat;
			$this->RT->Text=$pegawaiRecord->rt;
			$this->RW->Text=$pegawaiRecord->rw;
			if($pegawaiRecord->kabupaten)
				$this->DDKab->SelectedValue=$pegawaiRecord->kabupaten;
			
			$Nip1 = substr($pegawaiRecord->nip,0,3);
			$Nip2 = substr($pegawaiRecord->nip,3,3);
			$Nip3 = substr($pegawaiRecord->nip,6,3);
			$this->Nip1->Text=$Nip1;
			$this->Nip2->Text=$Nip2;
			$this->Nip3->Text=$Nip3;
			
			$this->nip->Text=$pegawaiRecord->nip;
			
			
			$this->NoTlp->Text=$pegawaiRecord->telepon;
			$this->NoHP->Text=$pegawaiRecord->no_hp;
			if($pegawaiRecord->jabatan)
				$this->DDJabatan->SelectedValue=$pegawaiRecord->jabatan;
			if($pegawaiRecord->kelompok == "1")			
			{
				$this->DDSpesialis->Enabled=true;
				$this->subSpesialis->Enabled=true;
				
				//$this->DDSpesialis->SelectedValue=$pegawaiRecord->spesialis;
				$spes=$pegawaiRecord->spesialis;
				if ($spes=='')
				{
					$this->DDSpesialis->SelectedValue=NULL;
				}else
				{
					$this->DDSpesialis->SelectedValue=$pegawaiRecord->spesialis;
				}
				$this->DDKlinik->Enabled=true;
				//$this->DDKlinik->SelectedValue=$pegawaiRecord->poliklinik;				
				
				$klin=$pegawaiRecord->poliklinik;
				if ($klin=='')
				{
					$this->DDKlinik->SelectedValue=NULL;
				}else
				{
					$this->DDKlinik->SelectedValues=explode(",",$pegawaiRecord->poliklinik);
				}
				
				if ($pegawaiRecord->st_sub_spesialis == '1')
				{
					$this->subSpesialis->Checked = true;
				}else
				{
					$this->subSpesialis->Checked = false;
				}
				
				
				if ($pegawaiRecord->st_tarif_khusus_operasi == '1')
				{
					$this->tarifKhusus->Checked = true;
					$this->tarifKhusus->Enabled=true;
					$this->tarifKhusus->Visible=true;
				}
				else
				{
					$this->tarifKhusus->Checked = false;
					
					if($pegawaiRecord->kelompok == "1")			
					{
						$this->tarifKhusus->Enabled=true;
						$this->tarifKhusus->Visible=true;	
					}
					else
					{
						$this->tarifKhusus->Enabled=false;
						$this->tarifKhusus->Visible=false;
					}
				}
				
				
			
			}else{
				$this->DDSpesialis->Enabled=false;
				$this->DDKlinik->Enabled=false;
				$this->subSpesialis->Enabled=false;
			}	
			if($pegawaiRecord->pendidikan)
				$this->DDPdk->SelectedValue=$pegawaiRecord->pendidikan;
			$this->Catatan->Text=$pegawaiRecord->catatan;
			$this->KecLuar->Text=$pegawaiRecord->kec_luar;
			$this->KelurahanLuar->Text=$pegawaiRecord->kel_luar;
			
			$this->noRek->Text=$pegawaiRecord->no_rek;
			$this->npwp->Text=$pegawaiRecord->npwp;
			$this->sip->Text=$pegawaiRecord->sip;
			
			/*
			if($pegawaiRecord->kecamatan)
				$this->DDKec->SelectedValue=$pegawaiRecord->kecamatan;
			if($pegawaiRecord->kelurahan)
				$this->DDKel->SelectedValue=$pegawaiRecord->kelurahan;
				*/
		}
	} 	
		
	public function onLoad($param)
	{
		parent::onLoad($param);		
		$tmpVar=$this->authApp('8');//ID aplikasi kepegawaian
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('Home'));			
		$kab = $this->getViewState('idKab');
		if($this->IsPostBack){							
			if ($kab == "01"){
				$this->KecLuar->Enabled=true;
				$this->KelurahanLuar->Enabled=true;
				//$this->DDKec->Enabled=true;
				//$this->DDKel->Enabled=true;
			}else{
				$this->KecLuar->Enabled=true;
				$this->KelurahanLuar->Enabled=true;
				//$this->DDKec->Enabled=false;
				//$this->DDKel->Enabled=false;
			}	
		}
			
		if(!$this->IsPostBack){			
			$this->IdPegawai->focus();			
			$this->DDStatus->DataSource=StatusPegawaiRecord::finder()->findAll();
			$this->DDStatus->dataBind(); 
			$this->DDJabatan->DataSource=JabatanRecord::finder()->findAll();
			$this->DDJabatan->dataBind(); 			
			$this->DDKelompok->DataSource=KelompokPegawaiRecord::finder()->findAll();
			$this->DDKelompok->dataBind(); 			
			$this->DDSpesialis->DataSource=SpesialisRecord::finder()->findAll();
			$this->DDSpesialis->dataBind();
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			$this->DDPdk->DataSource=PendidikanRecord::finder()->findAll();
			$this->DDPdk->dataBind();
			$this->DDKab->DataSource=KabupatenRecord::finder()->findAll();
			$this->DDKab->dataBind(); 
			$this->DDAgama->DataSource=AgamaRecord::finder()->findAll();
			$this->DDAgama->dataBind(); 
			
			
			
			/*
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll();	
			$this->DDKec->dataBind();
			$this->DDKel->DataSource=KelurahanRecord::finder()->findAll();	
			$this->DDKel->dataBind();
			*/
		}		
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		if(TPropertyValue::ensureString($this->DDKelompok->SelectedValue) == '1')
		{
			$this->DDSpesialis->Enabled=true;
			$this->DDSpesialis->focus();
			$this->DDKlinik->Enabled=true;
			
			$this->subSpesialis->Enabled=true;
			
			$this->tarifKhusus->Enabled=true;
			$this->tarifKhusus->Visible=true;
		}
		else
		{
			$this->DDSpesialis->Enabled=false;
			$this->DDKlinik->Enabled=false;
			$this->DDStatus->focus();
			$this->subSpesialis->Enabled=false;
			
			$this->tarifKhusus->Enabled=false;
			$this->tarifKhusus->Visible=false;
		}
	}	
	
	public function selectionChangedKab($sender,$param)
	{		
		$kab = $this->DDKab->SelectedValue;	
		$this->setViewState('idKab',$kab,'');	
		if ($kab == '01'){ //Bila kota bandung		
			/*
			$this->DDKec->DataSource=KecamatanRecord::finder()->findAll('id_kab = ?', $kab);
			$this->DDKec->dataBind(); 	
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();
			*/
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
		}else{
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			$this->KecLuar->focus();
			//$this->DDKec->Enabled=false;
			//$this->DDKel->Enabled=false;
		}	
	} 
	
	public function selectionChangedKec($sender,$param)
	{		
		$kec = $this->DDKec->SelectedValue;
		$kab = $this->getViewState('idKab');
		$this->DDKel->DataSource=KelurahanRecord::finder()->findAll('id_kec = ? AND id_kab = ?', $kec, $kab);
		$this->DDKel->dataBind(); 	
		$this->DDKel->Enabled=true;
		$this->DDKel->focus();			
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
	
	public function simpanClicked($sender,$param)
	{			
		$dateTmp = $this->TglLahir->Text;
		$mysqlDate = $this->ConvertDate($dateTmp,'2');
		$NipTmp = $this->Nip1->Text . $this->Nip2->Text . $this->Nip3->Text;
		//if($this->IsValid)  // when all validations succeed
        //{
            // populates a UserRecord object with user inputs
            $pegawaiRecord=$this->PegawaiRecord;
			$pegawaiRecord->id=ucwords($this->IdPegawai->Text);		            
            $pegawaiRecord->nama=ucwords($this->Nama->Text);
			$pegawaiRecord->tmp_lahir=ucwords($this->TmpLahir->Text);
            $pegawaiRecord->tgl_lahir=TPropertyValue::ensureString($mysqlDate);		
  			$pegawaiRecord->kelompok=TPropertyValue::ensureString($this->DDKelompok->SelectedValue);
            $pegawaiRecord->status=TPropertyValue::ensureString($this->DDStatus->SelectedValue);			
 			$pegawaiRecord->st_kawin=TPropertyValue::ensureString($this->collectSelectionListResult($this->stKawin));
            $pegawaiRecord->agama=TPropertyValue::ensureString($this->DDAgama->SelectedValue);						
 			$pegawaiRecord->jkel=TPropertyValue::ensureString($this->collectSelectionListResult($this->JKel));
            $pegawaiRecord->alamat=ucwords(TPropertyValue::ensureString($this->Alamat->Text));
 			$pegawaiRecord->rt=TPropertyValue::ensureString($this->RT->Text);
            $pegawaiRecord->rw=TPropertyValue::ensureString($this->RW->Text);
			$pegawaiRecord->kabupaten=TPropertyValue::ensureString($this->DDKab->SelectedValue);
            $pegawaiRecord->kecamatan=TPropertyValue::ensureString($this->DDKec->SelectedValue);
			$pegawaiRecord->kelurahan=TPropertyValue::ensureString($this->DDKel->SelectedValue);  
			$pegawaiRecord->nip=TPropertyValue::ensureString($this->nip->Text);
			
			$pegawaiRecord->no_rek=TPropertyValue::ensureString($this->noRek->Text);
			$pegawaiRecord->npwp=TPropertyValue::ensureString($this->npwp->Text);
			$pegawaiRecord->sip=TPropertyValue::ensureString($this->sip->Text);
			
            $pegawaiRecord->telepon=TPropertyValue::ensureString($this->NoTlp->Text);
			$pegawaiRecord->no_hp=TPropertyValue::ensureString($this->NoHP->Text);
			$pegawaiRecord->jabatan=TPropertyValue::ensureString($this->DDJabatan->SelectedValue);
			$pegawaiRecord->spesialis=TPropertyValue::ensureString($this->DDSpesialis->SelectedValue);
			$pegawaiRecord->poliklinik=TPropertyValue::ensureString($this->collectSelectionResult($this->DDKlinik));
			$pegawaiRecord->pendidikan=TPropertyValue::ensureString($this->DDPdk->SelectedValue);
			$pegawaiRecord->catatan=ucwords(TPropertyValue::ensureString($this->Catatan->Text));   
			
			if($this->subSpesialis->Checked === true )
				$pegawaiRecord->st_sub_spesialis = '1';
			else
				$pegawaiRecord->st_sub_spesialis = '0';
				
			if($this->tarifKhusus->Checked === true )
				$pegawaiRecord->st_tarif_khusus_operasi = '1';
			else
				$pegawaiRecord->st_tarif_khusus_operasi = '0';	
				
				
			$kab = $this->getViewState('idKab');
			if ( $kab != '01'){
				$pegawaiRecord->kec_luar=ucwords(TPropertyValue::ensureString($this->KecLuar->Text)); 
				$pegawaiRecord->kel_luar=ucwords(TPropertyValue::ensureString($this->KelurahanLuar->Text));  
			}else{
				$pegawaiRecord->kec_luar=''; 
				$pegawaiRecord->kel_luar='';
			}
			// saves to the database via Active Record mechanism
            $pegawaiRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianAdmin'));
        //}			
	}
	protected function getPegawaiRecord()
	{
		// use Active Record to look for the specified username
		$id=$this->Request['id'];
		$pegawaiRecord=PegawaiRecord::finder()->findByPk($id);
		if(!($pegawaiRecord instanceof PegawaiRecord))
			throw new THttpException(500,'id tidak benar.');
		return $pegawaiRecord;
	}
	
	public function batalClicked($sender,$param)
	{
		//$this->Response->Reload();
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianAdmin'));	
	}
	
    public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianAdmin'));		
	}
}
?>
