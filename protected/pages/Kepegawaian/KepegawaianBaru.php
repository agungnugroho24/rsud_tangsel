<?php
class KepegawaianBaru extends SimakConf
{
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
			//$this->IdPegawai->focus();
			$sql = "SELECT id FROM tbd_pegawai order by id desc";
			$num = PegawaiRecord::finder()->findBySql($sql);
			if($num==NULL)//jika kosong bikin ndiri
			{
				$urut='0001';
			}else{			
					$urut = $num->getColumnValue('id') + 1;				
					$tmp=substr('0000',0,4-strlen($urut)).$urut;
				}
			$this->IdPegawai->text=$tmp;
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
			$this->DDSpesialis->Enabled=false;
			$this->subSpesialis->Enabled=false;
			
			$this->tarifKhusus->Enabled=false;
			$this->tarifKhusus->Visible=false;
			
			
			$this->DDKlinik->Enabled=false;
			//$this->DDKec->Enabled=false;	
			$this->KecLuar->Enabled=false;
			//$this->DDKel->Enabled=false;	
			$this->KelurahanLuar->Enabled=false;
		}		
	}
	
	public function selectionChangedKelompok($sender,$param)
	{
		$this->tarifKhusus->Checked = false;
		
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
			/*$this->DDKec->DataSource=KecamatanRecord::finder()->findAll('id_kab = ?', $kab);
			$this->DDKec->dataBind(); 	
			$this->DDKec->Enabled=true;
			$this->DDKec->focus();			*/
			$this->KecLuar->Enabled=true;
			$this->KelurahanLuar->Enabled=true;
			$this->KecLuar->focus();
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
		//$NipTmp = $this->Nip1->Text . $this->Nip2->Text . $this->Nip3->Text;
		$NipTmp = $this->Nip1->Text . $this->Nip2->Text . '-';
		
		if($this->IsValid)  // when all validations succeed
       {
            // populates a UserRecord object with user inputs
            $pegawaiRecord=new PegawaiRecord;
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
            //$pegawaiRecord->kecamatan=TPropertyValue::ensureString($this->DDKec->SelectedValue);
			//$pegawaiRecord->kelurahan=TPropertyValue::ensureString($this->DDKel->SelectedValue);
			$pegawaiRecord->kecamatan='00';
			$pegawaiRecord->kelurahan='00';
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
			//if ( $kab != '01'){
				$pegawaiRecord->kec_luar=ucwords(TPropertyValue::ensureString($this->KecLuar->Text)); 
				$pegawaiRecord->kel_luar=ucwords(TPropertyValue::ensureString($this->KelurahanLuar->Text));  
			/*}else{
				$pegawaiRecord->kec_luar=''; 
				$pegawaiRecord->kel_luar='';
			}*/
			// saves to the database via Active Record mechanism
            $pegawaiRecord->save(); 			
            // redirects the browser to the homepage
			$this->Response->redirect($this->Service->constructUrl('Kepegawaian.KepegawaianAdmin'));
        }			
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