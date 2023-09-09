<?php
class bayarLab extends SimakConf
{   
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('5');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function lockPanel()
	{
		$this->inapPanel->Display = 'None';
		$this->dataPasienLuarPanel->Display = 'None';
		$this->dataPasienPanel->Display = 'None';
		$this->dataInapPanel->Display = 'None';
		$this->labPanel->Display = 'None';
		$this->gridPanel->Display = 'None';
		$this->konfPanel->Display = 'None';
		
		
		$this->inapPanel->Enabled = false;
		$this->dataPasienLuarPanel->Enabled = false;
		$this->dataPasienPanel->Enabled = false;
		$this->labPanel->Enabled = false;
	}
			
	public function ambilDataTdkLab()
	{
		$st_rujukan = $this->jnsPelaksana->SelectedValue;
		$labRujuk = $this->DDlabRujuk->SelectedValue;
		
		if($st_rujukan == '0') // Tidak dirujuk ke lab luar
		{
			$sql = "SELECT 
					  tbm_lab_tindakan.kode AS kode,
					  tbm_lab_tindakan.nama AS nama
					FROM
					  tbm_lab_tindakan
					  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
					WHERE
					  tbm_lab_tindakan.st_rujukan = '0' ";	
		 }
		 else // dirujuk ke lab luar
		 {
			$labRujuk = $this->DDlabRujuk->SelectedValue;
			$sql = "SELECT 
					  tbm_lab_tindakan.kode AS kode,
					  tbm_lab_tindakan.nama AS nama
					FROM
					  tbm_lab_tindakan
					  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
					WHERE
					  tbm_lab_tindakan.st_rujukan = '1' ";
			
			if($labRujuk)
				$sql .= " AND tbm_lab_tindakan.id_lab_rujukan = '$labRujuk'";	 
		 } 
		 
		 if($this->DDRadKel->SelectedValue == '2')
		 	$sql .= " AND tbm_lab_tindakan.st_paket > 0 ";	 
		 else
		 	$sql .= " AND tbm_lab_tindakan.st_paket = 0 ";	 
				
		$sql .= " ORDER BY tbm_lab_tindakan.nama ";	
		
		//$sql="select kode,nama from tbm_lab_tindakan where kode like 'Z%'";
		$this->DDTdkLab->DataSource=$this->queryAction($sql,'S');
		$this->DDTdkLab->dataBind();
				/*
		$sql = "SELECT 
				  tbm_lab_tindakan.kode AS kode,
				  tbm_lab_tindakan.nama AS nama
				FROM
				  tbm_lab_tindakan
				  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
				WHERE
				  tbm_lab_tarif.tarif <> '0' ";
		
		$this->DDTdkLab->DataSource=LabTdkRecord::finder()->findAllBySql($sql);
		$this->DDTdkLab->dataBind();*/
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->isPostBack || !$this->isCallBack )	
		{							
			$this->lockPanel();
				
			$this->notrans->Enabled=false;
			
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
			$this->DDDokter->dataBind();
			
			$this->dokter->Enabled=false;
			$this->dokter->Display='None';
						
			$this->DDRadKel->DataSource=LabKelRecord::finder()->findAll();
			$this->DDRadKel->dataBind();
			
			$this->DDRadKateg->DataSource=LabKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			$this->DDRadKateg->Enabled=false;
			
			$this->ambilDataTdkLab();
			$this->DDTdkLab->Enabled=false;	
			
			$this->DDlabRujuk->DataSource=LabRujukanRecord::finder()->findAll();
			$this->DDlabRujuk->dataBind();
			$this->DDlabRujuk->Enabled=false;	
			
			$this->modeByrInap->Enabled=true;
			
			$this->simpanBtn->Enabled=false;	
			
			/*
			$sql = "SELECT * FROM tbm_lab_rujukan";
			$arr = $this->queryAction($sql,'S');
			
			foreach($arr as $row)
			{
				$id_lab_rujukan = $row['id'];
				
				
				$sql2 = "SELECT * FROM tbm_lab_tarif ";
				$arr2 = $this->queryAction($sql2,'S');
				
				foreach($arr2 as $row2)
				{
					$id_tdk_lab = $row2['id'];
					$tarif = $row2['tarif'];
					$tarif1 = $row2['tarif1'];
					$tarif2 = $row2['tarif2'];
					$tarif3 = $row2['tarif3'];
					$tarif4 = $row2['tarif4'];
					$tarif5 = $row2['tarif5'];
					
					$sql3 = "INSERT INTO tbm_lab_rujukan_tarif (id_tdk_lab,id_lab_rujukan,tarif,tarif1,tarif2,tarif3,tarif4,tarif5) VALUE ('$id_tdk_lab','$id_lab_rujukan','$tarif','$tarif1','$tarif2','$tarif3','$tarif4','$tarif5')";
					$this->queryAction($sql3,'C');
				}	
				
			}
			*/
		}	
		else
		{
			if($this->getViewState('nmTable'))
			{
				$this->gridPanel->Display = 'Dynamic';	
			}
			else
			{
				$this->gridPanel->Display = 'None';		
			}	
		}
				
		$purge=$this->Request['purge'];
		$nmTable=$this->Request['nmTable'];
		
		if($purge =='1'	)
		{			
			if(!empty($nmTable))
			{		
				$this->queryAction($nmTable,'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->clearViewState('tmpJml');
			$this->getViewState('sisa');			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
			$this->notrans->Enabled=true;		
			$this->notrans->Text ='';			
			$this->errMsg->Text='';		
			$this->Page->CallbackClient->focus($this->notrans);	
			//$this->showSecond->Visible=false;
			//$this->showBayar->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Lab.bayarLab',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{			
			$this->Page->CallbackClient->focus($this->notrans);	
		}
    }
	
	public function cmCallback($sender, $param)
	{
		$this->cariCmPanel->render($param->getNewWriter());
		$this->inapPanel->render($param->getNewWriter());
		$this->dataPasienLuarPanel->render($param->getNewWriter());
		$this->dataPasienPanel->render($param->getNewWriter());
		$this->labPanel->render($param->getNewWriter());
	}
	
	public function modeInputChanged($sender, $param)
	{		
		$this->valNamaPasLuar->Enabled = false;
		$this->valUmurPasLuar->Enabled = false;
		$this->valJkelPasLuar->Enabled = false;
		$this->valDokterLuar->Enabled = false;
		
		$this->valNama->Enabled = true;
		$this->valKlinik->Enabled = true;
		$this->valDokter->Enabled = true;
		
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0' || $jnsPasien == '3')//Pasien Rawat Jalan
		{		
			$this->notrans->Enabled = true;
			$this->clearViewState('modeByrInap');
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				document.all.'.$this->notrans->getClientID().'.focus();
			</script>';
		}
		elseif($jnsPasien == '1')//Pasien Rawat Inap
		{
			$this->notrans->Enabled = true;
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				document.all.'.$this->notrans->getClientID().'.focus();
			</script>';
		}
		else//Pasien Rawat Inap//Pasien Luar
		{
			$this->valNamaPasLuar->Enabled = true;
			$this->valUmurPasLuar->Enabled = true;
			$this->valJkelPasLuar->Enabled = false;
			$this->valDokterLuar->Enabled = true;
			
			$this->valNama->Enabled = false;
			$this->valKlinik->Enabled = false;
			$this->valDokter->Enabled = false;
		
			$this->clearViewState('modeByrInap');
			$this->notrans->Enabled = false;
			
			$this->checkRegister();
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				document.all.'.$this->nmPasLuar->getClientID().'.focus();
			</script>';
		}
	
				
		$this->modeByrInapChanged();
	}
	
	public function modeByrInapChanged()
	{
		$modeByrInap = $this->collectSelectionResult($this->modeByrInap);		
		$this->clearViewState('modeByrInap');
		$this->setViewState('modeByrInap',$modeByrInap);		
	}		
	
	
	public function checkRegister()
    {
		$tmp = $this->formatCm($this->notrans->Text);
		$this->jkel->Text = $this->cariJkel($tmp);
		$this->Rbjkel->Text = $this->cariJkel2($tmp);
		$byrInap=$this->getViewState('modeByrInap');
		$this->showSql->text=$byrInap;
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien=='0') //Pasien Rawat Jalan
		{				
			$dateNow = date('Y-m-d');
			
			$sql = "SELECT *
					FROM
					  tbt_rawat_jalan
					WHERE
					  cm = '$tmp' AND 
					  flag = '0' AND 
					  st_alih = '0' AND 
					  TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
					  
			//if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND tgl_visit=?',$this->formatCm($this->notrans->Text),'0','0',$dateNow))	 //belum alih status ke rwt inap		
			if(RwtjlnRecord::finder()->findBySql($sql))
			{		
				$this->cariCmPanel->Enabled = false;
				$this->dataPasienPanel->Enabled = true;
				$this->dataPasienPanel->Display = 'Dynamic';
				
				$this->labPanel->Enabled = true;
				$this->labPanel->Display = 'Dynamic';
					
				$sql = "SELECT 
							tbt_rawat_jalan.cm AS cm,
							tbt_rawat_jalan.no_trans AS no_trans,
							tbt_rawat_jalan.st_asuransi,
							tbt_rawat_jalan.perus_asuransi,
							tbd_pasien.nama AS nama
						FROM
							tbt_rawat_jalan
							INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_jalan.cm='$tmp' 
							AND tbt_rawat_jalan.flag='0'
							AND tbt_rawat_jalan.st_alih='0'
							AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					$this->nmPas->Enabled = false;
					$this->nmPas->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);							
					$this->setViewState('no_trans_rwtjln',$row['no_trans']);							
					$this->setViewState('stAsuransi',$row['st_asuransi']);							
					$this->setViewState('perusAsuransi',$row['perus_asuransi']);
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';			
					$this->DDTdkLab->Enabled=true;	
				}
				
				//data u/ DDKlinik
				$sql = "SELECT 
					  tbm_poliklinik.id,
					  tbm_poliklinik.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbt_rawat_jalan.cm = '$tmp' 
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'
					  AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
				
				$arr = $this->queryAction($sql,'S');
										  
				$this->DDKlinik->DataSource = $arr;
				$this->DDKlinik->dataBind();
				
				if(count($arr) == '1')
				{
					$idPoli = PoliklinikRecord::finder()->findBySql($sql)->id;
					
					$this->DDKlinik->SelectedValue = $idPoli;
					$this->showDokter();
					$this->errMsg->Text = '    
					<script type="text/javascript">
						document.all.'.$this->DDDokter->getClientID().'.focus();
					</script>';
				}
				else
				{
					$this->errMsg->Text = '    
					<script type="text/javascript">
						document.all.'.$this->DDKlinik->getClientID().'.focus();
					</script>';
				}
				
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->formatCm($this->notrans->Text),'0','1'))	 //sudah alih status ke rwt inap		
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->notrans->Text).' sudah alih status ke Rawat Inap !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text ='';
			}
			else //Tidak ada atau belum terdaftar pendaftaran rawat jalan
			{
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->notrans->Text).' Belum Masuk Ke Pendaftaran Rawat Jalan !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text ='';
			}
		}
		elseif($jnsPasien=='1') //Pasien Rawat Inap
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->formatCm($this->notrans->Text),'0'))
			{			
				$this->cariCmPanel->Enabled = false;
				$this->inapPanel->Enabled = true;
				$this->inapPanel->Display = 'Dynamic';
				
				$this->dataPasienPanel->Enabled = true;
				$this->dataPasienPanel->Display = 'Dynamic';
				
				$this->dataInapPanel->Display = 'Dynamic';
				
				$this->labPanel->Enabled = true;
				$this->labPanel->Display = 'Dynamic';
				
				$this->DDKlinik->Enabled = false;
				$this->nmPas->Enabled = false;
					 
				$sql = "SELECT 
							tbt_rawat_inap.cm AS cm,
							tbt_rawat_inap.kelas AS kelas,
							tbt_rawat_inap.kamar AS kode_kamar,
							tbt_rawat_inap.jenis_kamar AS jenis_kamar,
							tbt_rawat_inap.st_asuransi,
							tbt_rawat_inap.perus_asuransi,
							tbd_pasien.nama AS nama
						FROM
							tbd_pasien 
							INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.cm = tbd_pasien.cm) 
						WHERE 
							tbt_rawat_inap.cm='$tmp' 
							AND tbt_rawat_inap.status='0' ";
							
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					$this->nmPas->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);
					$this->setViewState('kelas',$row['kelas']);	
					$this->setViewState('stAsuransi',$row['st_asuransi']);
					$this->setViewState('perusAsuransi',$row['perus_asuransi']);
					
					$idKelas = $row['kelas'];
					$idKamar = $row['jenis_kamar'];
					$idKodeKamar = $row['kode_kamar'];
					
					$this->kelasInap->Text= KamarKelasRecord::finder()->findByPk($idKelas)->nama;
					$this->jnsKamarInap->Text= KamarNamaRecord::finder()->findByPk($idKamar)->nama;
					$this->kodeRuangInap->Text= RuangRecord::finder()->findByPk($idKodeKamar)->nama;
					
				}
				
				$tmprwtinap=RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->formatCm($this->notrans->Text),'0');
				$this->setViewState('notransinap',$tmprwtinap->no_trans);
				
				//data u/ DDDokter
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbd_pegawai
					  INNER JOIN tbt_rawat_inap ON (tbt_rawat_inap.dokter = tbd_pegawai.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_inap.cm = '$tmp'
					  AND tbt_rawat_inap.status = 0";
						  
				$this->DDDokter->DataSource=$this->queryAction($sql,'S');
				$this->DDDokter->dataBind();
				
				$idDokter = PegawaiRecord::finder()->findBySql($sql)->id;
				$this->DDDokter->SelectedValue = $idDokter;
				$this->DDDokterChanged();
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->modeByrInap->getClientID().'.focus();
				</script>';
				
				$umur = $this->cariUmur('1',$this->formatCm($this->notrans->Text),'');
				$this->umur->Text = $umur['years'];
				
			}
			else
			{
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$tmp.' Belum Masuk Ke Pendaftaran Rawat Inap !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text ='';
			}
		}
		elseif($jnsPasien == '3') //Regis Langsung
		{
			if(PasienRecord::finder()->findByPk($tmp))
			{
				$data = PasienRecord::finder()->findByPk($tmp);
				$this->cariCmPanel->Enabled = false;
				$this->dataPasienPanel->Enabled = false;
			
				$this->dataPasienLuarPanel->Enabled = true;
				$this->dataPasienLuarPanel->Display = 'Dynamic';
			
				$this->labPanel->Enabled = true;
				$this->labPanel->Display = 'Dynamic';
			
				$this->DDKlinik->Enabled = false;
				$this->DDDokter->Enabled = false;
				$this->nmPasLuar->Text = $data->nama;
				$this->alamatPasLuar->Text = $data->alamat;
				$this->umur2->Text = $this->hitUmur($data->tgl_lahir);
				if($data->jkel == '0')
				{	
					$this->Rbjkel2->SelectedValue =0; 	
				}
				else
				{
					$this->Rbjkel2->SelectedValue =1;
				}
				if($data->kelompok == '01')
				{
												
					$this->setViewState('stAsuransi',0);	
				}
				else
				{
					$this->setViewState('stAsuransi',1);
					$this->setViewState('perusAsuransi',$data->perusahaan);	
				}
				
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Tidak Ada Pasien dengan No. Rekam Medis '.$tmp.' !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text ='';
			}
			 
		}
		else //Pasien Luar
		{
			$this->cariCmPanel->Enabled = false;
			$this->dataPasienPanel->Enabled = false;
			
			$this->dataPasienLuarPanel->Enabled = true;
			$this->dataPasienLuarPanel->Display = 'Dynamic';
			
			$this->labPanel->Enabled = true;
			$this->labPanel->Display = 'Dynamic';
			
			$this->DDKlinik->Enabled = false;
			$this->DDDokter->Enabled = false;
			
		}
    }
		
	public function showDokter()
	{
		if($this->DDKlinik->SelectedValue!='')
		{
			$tmp = $this->formatCm($this->notrans->Text);
			$dateNow = date('Y-m-d');
			$idKlinik = $this->DDKlinik->SelectedValue;
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien=='0')
			{	
				//data u/ DDDokter
				$sql = "SELECT 
					  tbd_pegawai.id,
					  tbd_pegawai.nama
					FROM
					  tbt_rawat_jalan
					  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
					  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
					WHERE
					  tbd_pegawai.kelompok = 1	
					  AND tbt_rawat_jalan.cm = '$tmp'
					  AND tbt_rawat_jalan.id_klinik = '$idKlinik'
					  AND tbt_rawat_jalan.flag = '0'
					  AND tbt_rawat_jalan.st_alih='0'
					  AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
			}
			
			$arr = $this->queryAction($sql,'S');
			
			$this->DDDokter->DataSource = $arr;
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled = true;				
			$this->Page->CallbackClient->focus($this->DDDokter);
			
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
			$this->setViewState('klinik',$klinik);
			$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
			
			if(count($arr) == '1')
			{
				$idDokter = PegawaiRecord::finder()->findBySql($sql)->id;
				$this->DDDokter->SelectedValue = $idDokter;
				$this->DDDokterChanged();
			}
			
			$umur = $this->cariUmur('0',$tmp,$idKlinik);
			$this->umur->Text = $umur['years'];
		}
		else
		{
			//$this->batalClicked();
			$this->umur->Text = '';
		}
	}
	
	public function DDDokterChanged()
	{	
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		//$this->notrans->Enabled=true;
		//$this->Page->CallbackClient->focus($this->DDRadKel);
	}
	
	public function jnsPelaksanaChanged($sender,$param)
	{
		$this->DDlabRujuk->SelectedValue = 'empty';
		
		if($this->jnsPelaksana->SelectedValue == '0')//Lab RS
		{
			$this->DDlabRujuk->Enabled = false;		
			$this->Page->CallbackClient->focus($this->DDRadKel);
			
			$this->DDRadKel->Enabled = true;
			$this->DDTdkLab->Enabled = true;	
		}
		else //Lab Rujukan
		{
			$this->DDlabRujuk->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDlabRujuk);
			
			$this->DDRadKel->Enabled = false;
			$this->DDRadKateg->Enabled = false;
			$this->DDTdkLab->Enabled = false;
		}
		
		$this->DDRadKateg->Enabled = false;
		
		$this->DDRadKel->SelectedValue = 'empty';
		$this->DDTdkLab->SelectedValue = 'empty';
		$this->DDRadKateg->SelectedValue = 'empty';
		$this->ambilDataTdkLab();
	}
	
	public function DDlabRujukChanged($sender,$param)
	{
		if($this->DDlabRujuk->SelectedValue != '')
		{
			$this->DDRadKel->Enabled = true;
			$this->DDTdkLab->Enabled = true;	
			
			$this->DDRadKel->SelectedValue = 'empty';
			$this->DDTdkLab->SelectedValue = 'empty';
			$this->DDRadKateg->SelectedValue = 'empty';
			
			$this->Page->CallbackClient->focus($this->DDRadKel);
		}
		
		$this->ambilDataTdkLab();
	}
	
	public function DDRadKelChanged($sender,$param)
	{
		$this->setViewState('kel',$this->DDRadKateg->SelectedValue);
		
		if($this->DDRadKel->SelectedValue != '')
		{
			if($this->DDRadKel->SelectedValue == '2'){//bila yang dipilh adalah paket
				$this->DDRadKateg->Enabled=false;
				
				$this->ambilDataTdkLab();
				
				$this->DDTdkLab->Enabled=true;
				$this->Page->CallbackClient->focus($this->DDTdkLab);
			}
			else
			{			
				$this->DDRadKateg->Enabled=true;
				$this->DDTdkLab->Enabled=true;
				
				$this->ambilDataTdkLab();
				$this->Page->CallbackClient->focus($this->DDTdkLab);
			}
		}
		else
		{
			$this->DDRadKateg->DataSource=LabKategRecord::finder()->findAll();
			$this->DDRadKateg->dataBind();
			$this->DDRadKateg->Enabled=false;
			
			$this->ambilDataTdkLab();
			$this->DDTdkLab->Enabled=false;
		}
	}
		
	public function DDRadKategChanged($sender,$param)
	{
		if  ($this->DDRadKateg->SelectedValue=='')
		{
			$this->DDTdkLab->DataSource=LabKategRecord::finder()->findAll();
			$this->DDTdkLab->dataBind();
			$this->DDTdkLab->Enabled=false;
			$this->Page->CallbackClient->focus($this->DDTdkLab);
		}else
		{
			$kel=$this->DDRadKel->SelectedValue;
			$kateg=$this->DDRadKateg->SelectedValue;
			
			$st_rujukan=$this->jnsPelaksana->SelectedValue;
			if($st_rujukan == '0') // Tidak dirujuk ke lab luar
			{
				/*if  ($kateg=='0')
				{
					$sql = "SELECT 
							  tbm_lab_tindakan.kode AS kode,
							  tbm_lab_tindakan.nama AS nama
							FROM
							  tbm_lab_tindakan
							  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
							WHERE
							  tbm_lab_tindakan.kode like 'U%' 
							  AND tbm_lab_tarif.tarif <> '0'";
				}else{
				*/
					$sql = "SELECT 
							  tbm_lab_tindakan.kode AS kode,
							  tbm_lab_tindakan.nama AS nama
							FROM
							  tbm_lab_tindakan
							  INNER JOIN tbm_lab_tarif ON (tbm_lab_tindakan.kode = tbm_lab_tarif.id)
							WHERE
							  tbm_lab_tindakan.kelompok = '$kel'
							  AND tbm_lab_tindakan.kategori = '$kateg'
							  AND tbm_lab_tindakan.status = '1'
							  AND tbm_lab_tarif.tarif <> '0'";
				//}
			}
			else // dirujuk ke lab luar
			{
				$labRujuk = $this->DDlabRujuk->SelectedValue;
				
				/*if  ($kateg=='0')
				{
					$sql = "SELECT 
							  tbm_lab_tindakan.kode AS kode,
							  tbm_lab_tindakan.nama AS nama
							FROM
							  tbm_lab_tindakan
							  INNER JOIN tbm_lab_rujukan_tarif ON (tbm_lab_tindakan.kode = tbm_lab_rujukan_tarif.id_tdk_lab)
							  INNER JOIN tbm_lab_rujukan ON (tbm_lab_rujukan_tarif.id_lab_rujukan = tbm_lab_rujukan.id)
							WHERE
							  tbm_lab_tindakan.kode like 'U%' 
							  AND tbm_lab_rujukan_tarif.id_lab_rujukan = '$labRujuk'";
				}else{ */
					$sql = "SELECT 
							  tbm_lab_tindakan.kode AS kode,
							  tbm_lab_tindakan.nama AS nama
							FROM
							  tbm_lab_tindakan
							  INNER JOIN tbm_lab_rujukan_tarif ON (tbm_lab_tindakan.kode = tbm_lab_rujukan_tarif.id_tdk_lab)
							  INNER JOIN tbm_lab_rujukan ON (tbm_lab_rujukan_tarif.id_lab_rujukan = tbm_lab_rujukan.id)
							WHERE
							  tbm_lab_tindakan.kelompok = '$kel'
							  AND tbm_lab_tindakan.kategori = '$kateg'
							  AND tbm_lab_tindakan.status = '0'
							  AND tbm_lab_rujukan_tarif.id_lab_rujukan = '$labRujuk'";
				//}
			}
			
			$sql .= " ORDER BY tbm_lab_tindakan.nama ";
			
			$this->DDTdkLab->DataSource=$this->queryAction($sql,'S');
			$this->DDTdkLab->dataBind();
			$this->DDTdkLab->Enabled=true;
			$this->Page->CallbackClient->focus($this->DDTdkLab);
		}
	}
	
	public function lapTrans()
    {	
	
		$this->mainPanel->Display = 'None';
		$this->konfPanel->Display = 'Dynamic';
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		$idDokter = $this->DDDokter->SelectedValue;
		$idKlinik = $this->DDKlinik->SelectedValue;
			
		$this->nmTdk->Text = $this->ambilTxt($this->DDTdkLab);
		
		$cm = $this->formatCm($this->notrans->Text);
		$tgl = date('Y-m-d');
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien=='0')
		{			
			$nmTable = 'tbt_lab_penjualan';
			$noTrans = RwtjlnRecord::finder()->find('cm=? AND id_klinik=? AND dokter=? AND flag=? AND tgl_visit=?',$cm,$idKlinik,$idDokter,'0',date('Y-m-d'))->no_trans;
			
			$this->konfNoCm->Text = $cm;
			$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$this->konfJnsPas->Text = "Pasien Rawat Jalan";
		}
		elseif($jnsPasien=='1')
		{			
			$nmTable = 'tbt_lab_penjualan_inap';
			$noTrans = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->no_trans;
			
			$this->konfNoCm->Text = $cm;
			$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$this->konfJnsPas->Text = "Pasien Rawat Inap";
		}
		elseif($jnsPasien=='2' || $jnsPasien=='3')
		{			
			$nmTable = 'tbt_lab_penjualan_lain';
			$this->konfNoCm->Text = '-';
			$this->konfNmPas->Text = $this->nmPas->Text;	
			$this->konfJnsPas->Text = "Pasien Rujukan";
		}
		
		if($jnsPasien!='2' || $jnsPasien!='3')
		{	
			$sql="SELECT 
				   $nmTable.id,
				   $nmTable.wkt,
				   tbm_lab_tindakan.nama,
				   tbm_lab_kelompok.nama AS kelompok,
				   tbm_lab_kategori.jenis AS kategori  
				FROM
				  tbm_lab_tindakan
				  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
				  INNER JOIN $nmTable ON (tbm_lab_tindakan.kode = $nmTable.id_tindakan)
				  LEFT JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
				WHERE
				  $nmTable.cm = '$cm' 
				  AND $nmTable.tgl = '$tgl' ";
			
			if($jnsPasien=='0')
			{			
				$sql .=" AND $nmTable.no_trans_rwtjln = '$noTrans' ";
			}
			elseif($jnsPasien=='1')
			{			
				$sql .=" AND $nmTable.no_trans_inap = '$noTrans' ";
			}			
				  
			$sql .=" ORDER BY
				  kelompok,
				  kategori,
				  tbm_lab_tindakan.nama";
		}
		else
		{
			$nmPasRujuk = $this->nmPas->Text;
			$sql="SELECT 
				   $nmTable.id,
				   $nmTable.wkt,
				   tbm_lab_tindakan.nama,
				   tbm_lab_kelompok.nama AS kelompok,
				   tbm_lab_kategori.jenis AS kategori  
				FROM
				  tbm_lab_tindakan
				  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
				  INNER JOIN $nmTable ON (tbm_lab_tindakan.kode = $nmTable.id_tindakan)
				  LEFT JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
				WHERE
				  $nmTable.cm LIKE '$nmPasRujuk%' 
				  AND $nmTable.tgl = '$tgl'
				ORDER BY
				  kelompok,
				  kategori,
				  tbm_lab_tindakan.nama";
		}
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Display = 'Dynamic';
		$this->konfPanel->Display = 'None';
		
		$this->DDTdkLab->SelectedValue = 'empty';
		$this->Page->CallbackClient->focus($this->DDTdkLab);
	}
	
	public function yaBtnClicked()
    {
		$this->mainPanel->Display = 'Dynamic';
		$this->konfPanel->Display = 'None';
		
		$this->prosesClicked();
	}
	
	public function DDTdkLabChanged()
    {
		if($this->DDTdkLab->SelectedValue != '')
		{
			$cm = $this->formatCm($this->notrans->Text);
			$idTdk = $this->DDTdkLab->SelectedValue;
			$jnsPasien = $this->modeInput->SelectedValue;
			$idDokter = $this->DDDokter->SelectedValue;
			$idKlinik = $this->DDKlinik->SelectedValue;
			
			if($jnsPasien=='0')
			{			
				$activeRec = LabJualRecord::finder();
				$noTrans = RwtjlnRecord::finder()->find('cm=? AND id_klinik=? AND dokter=? AND flag=? AND tgl_visit=?',$cm,$idKlinik,$idDokter,'0',date('Y-m-d'))->no_trans;
			}
			elseif($jnsPasien=='1')
			{			
				$activeRec = LabJualInapRecord::finder();
				$noTrans = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->no_trans;
			}
			elseif($jnsPasien=='2' || $jnsPasien=='3')
			{			
				$activeRec = LabJualLainRecord::finder();
			}	
			
			if($jnsPasien!='2' || $jnsPasien!='3') //bukan pasien rujuk
			{	
				//jika ditemukan transaksi dengan id tindakan lab yg sama dalam 1 hari 
				
				if($jnsPasien=='0')
				{			
					$data = $activeRec->find('no_trans_rwtjln=? AND id_tindakan=? AND tgl=? AND flag=?',$noTrans,$idTdk,date('Y-m-d'),'0');
				}
				elseif($jnsPasien=='1')
				{			
					$data = $activeRec->find('no_trans_inap=? AND id_tindakan=? AND tgl=? AND flag=?',$noTrans,$idTdk,date('Y-m-d'),'0');
				}
							
				if($data) //konfirmasi muncul
				{
					$this->lapTrans();
				}
				else
				{
					$this->prosesClicked();
				}
			}
			else
			{
				/*
				if($this->IsValid) //jika textbox nama dan dokter rujuk tidak kosong
				{
					$nmPasRujuk = $this->nmPas->Text;
					$tgl = date('Y-m-d');
					$sql="SELECT 
							cm
							FROM
							  tbt_lab_penjualan_lain
							WHERE
							  cm LIKE '$nmPasRujuk%' 
							  AND tgl = '$tgl'
							  AND id_tindakan = '$idTdk'";
					
					if($this->queryAction($sql,'S')) //konfirmasi muncul
					{
						$this->lapTrans();
					}
					else
					{
						$this->prosesClicked();
					}
				}
				else
				{
					$this->DDTdkLab->SelectedValue = 'empty';
				}
				*/
				
				$this->prosesClicked();
			}
			
		}		
	}
		
		public function cekAsuransi()
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$item=$this->DDTdkLab->SelectedValue;
		$st_rujukan=$this->jnsPelaksana->SelectedValue;
		$id_lab_rujukan=$this->DDlabRujuk->SelectedValue;
		
		//if($this->getViewState('stAsuransi') == '0')//pasien umum
		//{
			$tmpTarif = $this->ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_lab_rujukan);
		/*}
		elseif($this->getViewState('stAsuransi') == '1')//pasien penjamin
		{
			$idPerus = $this->getViewState('perusAsuransi');
			
			//cek tarif paket di tbm_rad_tindakan_paket_asuransi_detail
			$sql = "SELECT id,id_paket,tarif FROM tbm_lab_tindakan_paket_asuransi_detail WHERE id_paket > '0' AND id_asuransi = '$idPerus' AND id_tindakan = '$item' AND st_rawat = '$jnsPasien' ";
			if($this->queryAction($sql,'S'))
			{
				$tmpDetailPaket = LabTdkPaketAsuransiDetailRecord::finder()->findBySql($sql);
				$idPaket = $tmpDetailPaket->id_paket;
				$this->setViewState('idPaketTdkAsuransi',$tmpDetailPaket->id_paket);
				
				$sql = "SELECT id,tarif FROM tbm_lab_tindakan_paket_asuransi WHERE id='$idPaket' AND id_asuransi = '$idPerus' AND st_rawat = '$jnsPasien' ";
				$tmpTarif = LabTdkPaketAsuransiRecord::finder()->findBySql($sql);
			}
			else
			{
				//cek tarif non paket di tbm_rad_tindakan_paket_asuransi_detail
				$sql = "SELECT tarif FROM tbm_lab_tindakan_paket_asuransi_detail WHERE id_paket = '0' AND id_asuransi = '$idPerus' AND id_tindakan = '$item' AND st_rawat = '$jnsPasien' ";
				if($this->queryAction($sql,'S'))
				{
					$tmpTarif = LabTdkPaketAsuransiDetailRecord::finder()->findBySql($sql);
				}
				else
				{
					//ambil tarif RS
					$tmpTarif = $this->ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_lab_rujukan);
				}
			}
		}*/
		
		return $tmpTarif;
	}
	public function ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_lab_rujukan)
	{
		
		//---------------- JIKA TIDAK DIRUJUK KE LAB LUAR -----------------------
		if($st_rujukan == '0') 
		{
			if($jnsPasien=='2' || $jnsPasien=='0' || $jnsPasien=='3')//pasien rujukan / rawat jalan
			{	
				$sql = "SELECT b.nama AS id, ";
				
				if($this->modeCito->SelectedValue == '0')
					$sql .= " a.tarif AS tarif ";
				else
					$sql .= " a.tarif2 AS tarif ";	
						 
				$sql .= " FROM tbm_lab_tarif a, 
							  tbm_lab_tindakan b 
						 WHERE a.id='$item' AND a.id=b.kode";		 
			}
			elseif($jnsPasien=='1')//pasien rawat inap
			{			
				$kelas = $this->getViewState('kelas'); //tentukan kelas pasien
				if($kelas == '1')//kelas VIP
				{
					$sql = "SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif1 AS tarif  ";
					else
						$sql .= " a.tarif3 AS tarif ";	
							 
					$sql .= " FROM tbm_lab_tarif a, 
								  tbm_lab_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";
								 
				}
				elseif($kelas == '2' || $kelas == '3' || $kelas == '4' || $kelas == '5')//kelas IA / kelas IB / kelas II / kelas III
				{
					$sql = "SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif AS tarif  ";
					else
						$sql .= " a.tarif2 AS tarif ";	
							 
					$sql .= " FROM tbm_lab_tarif a, 
								  tbm_lab_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";
				}
			}
		}
		else //---------------- JIKA DIRUJUK KE LAB LUAR -----------------------
		{
			if($jnsPasien=='2' || $jnsPasien=='0' || $jnsPasien=='3')//pasien rujukan //rawat jalan
			{	
				$sql="SELECT b.nama AS id, ";
				
				if($this->modeCito->SelectedValue == '0')
					$sql .= " a.tarif AS tarif ";
				else
					$sql .= " a.tarif2 AS tarif ";	
				
				/*		 
				$sql .= " FROM tbm_lab_rujukan_tarif a, 
							  tbm_lab_tindakan b, tbm_lab_rujukan c
						 WHERE a.id_tdk_lab='$item' AND a.id_tdk_lab=b.kode AND a.id_lab_rujukan=c.id AND a.id_lab_rujukan='$id_lab_rujukan' ";	
					*/
				$sql .= " FROM tbm_lab_tarif a, 
								  tbm_lab_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";		 
			}
			elseif($jnsPasien=='1')//pasien rawat inap
			{			
				$kelas = $this->getViewState('kelas');
				if($kelas == '1')//kelas VIP
				{
					$sql="SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif1 AS tarif  ";
					else
						$sql .= " a.tarif3 AS tarif ";	
					/*		 
					$sql .= " FROM tbm_lab_rujukan_tarif a, 
							  tbm_lab_tindakan b, tbm_lab_rujukan c
						 WHERE 
						 	a.id_tdk_lab='$item' 
							AND a.id_tdk_lab=b.kode 
							AND a.id_lab_rujukan=c.id
							AND a.id_lab_rujukan='$id_lab_rujukan' ";*/
					$sql .= " FROM tbm_lab_tarif a, 
								  tbm_lab_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";			
				}
				elseif($kelas == '2' || $kelas == '3' || $kelas == '4' || $kelas == '5')//kelas IA / kelas IB / kelas II / kelas III
				{
					$sql="SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif AS tarif  ";
					else
						$sql .= " a.tarif2 AS tarif ";	
					/*		 
					$sql .= " FROM tbm_lab_rujukan_tarif a, 
							  tbm_lab_tindakan b, tbm_lab_rujukan c
						 WHERE 
						 	a.id_tdk_lab='$item' 
							AND a.id_tdk_lab=b.kode 
							AND a.id_lab_rujukan=c.id
							AND a.id_lab_rujukan='$id_lab_rujukan' ";*/
					$sql .= " FROM tbm_lab_tarif a, 
								  tbm_lab_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";		
				}
			}
		}
		
		$tmpTarif = LabTarifRecord::finder()->findBySql($sql);	
		return $tmpTarif;
		
	}
	public function prosesClicked()
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$item=$this->DDTdkLab->SelectedValue;
		$st_rujukan=$this->jnsPelaksana->SelectedValue;
		$id_lab_rujukan=$this->DDlabRujuk->SelectedValue;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_tdk VARCHAR(4) NOT NULL,									 
										 total INT(11) NOT NULL,
										 st_rujukan char(1) DEFAULT '0',
										 id_lab_rujukan char(2) DEFAULT NULL,									 							 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...	
			
			if($jnsPasien=='2')//PASIEN RUJUKAN
			{			
				$jml2=5000;		
				$sql="INSERT INTO $nmTable (nama,total,id_tdk,st_rujukan,id_lab_rujukan) VALUES ('PENDAFTARAN',$jml2,'PDT','0','')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
			}
			
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
		}				
		
		$tmpTarif = $this->cekAsuransi();					 		
		$total_asli= $tmpTarif->tarif;
		
		if($this->getViewState('stAsuransi') == '1')
			$total= $total_asli;	
		else
			$total=$this->bulatkan($total_asli);
		
		$nama=$tmpTarif->id;
		//$total=$total+($total*0.1);	
		$jml=$total+$jml1+$jml2;
				
		$sql="INSERT INTO $nmTable (nama,total,id_tdk,st_rujukan,id_lab_rujukan) VALUES ('$nama','$total','$item','$st_rujukan','$id_lab_rujukan')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();	
		
		//---------- Tabel Temp u/ tbt_lab_jual_sisa --------
		$sisa=$total-$total_asli;
		
		if($sisa > 0)
		{
			$sisaTmpTable = $this->setNameTable('sisaTmpTable');
			$sql="CREATE TABLE $sisaTmpTable (id INT (2) auto_increment, 
										 jumlah FLOAT(11,2) NOT NULL,							 							 
										 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			$sql="INSERT INTO $sisaTmpTable (jumlah) VALUES ('$sisa')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
		}
		
		
		if($this->getViewState('tmpJml')){
			$t = (int)$this->getViewState('tmpJml') + $jml;
			$this->setViewState('tmpJml',$t);
		}else{
			$this->setViewState('tmpJml',$jml);
		}	
		
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');		
		//$this->showBayar->Visible=true;
		$this->DDTdkLab->SelectedValue = 'empty';
		$this->Page->CallbackClient->focus($this->DDTdkLab);
		
		$this->simpanBtn->Enabled = true;
	}
	
	 public function deleteClicked($sender,$param)
    {/*
        if ($this->User->IsAdmin)
		{
			if ($this->getViewState('stQuery') == '1')
			{
				 //obtains the datagrid item that contains the clicked delete button*/
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT total FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['total'];					
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');								
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();
				$this->Page->CallbackClient->focus($this->DDTdkLab);
				
				if($jmlData==0)
				{
					$this->simpanBtn->Enabled = false;
					
					$this->DDRadKel->DataSource=LabKelRecord::finder()->findAll();
					$this->DDRadKel->dataBind();					
					
					$this->DDRadKateg->DataSource=LabKategRecord::finder()->findAll();
					$this->DDRadKateg->dataBind();
					$this->DDRadKateg->Enabled=false;
					
					$this->ambilDataTdkLab();
					
					$this->DDTdkLab->Enabled=false;	
					
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();
					$this->clearViewState('nmTable');//Clear the view state	
					
					if($this->getViewState('sisaTmpTable'))
					{
						$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table	
						$this->clearViewState('sisaTmpTable');//Clear the view state			
					}
					
					$this->Page->CallbackClient->focus($this->DDRadKel);
				}
			/*}
		}	*/
    }	
	
	
	public function batalClicked()
    {	
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		if($this->getViewState('sisaTmpTable'))
		{
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table	
			$this->clearViewState('sisaTmpTable');//Clear the view state			
		}
		
		$this->clearState();	
		$this->Response->Reload();
	}
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
		}
		
		if($this->getViewState('sisaTmpTable'))
		{
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table	
			$this->clearViewState('sisaTmpTable');//Clear the view state			
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	public function clearState()
    {
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		if($this->getViewState('sisaTmpTable'))
		{
			$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table	
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('sisa');			
		$this->clearViewState('nmTable');
		$this->clearViewState('cm');
		$this->clearViewState('notrans');
		$this->clearViewState('nama');
		$this->clearViewState('dokter');
		$this->clearViewState('klinik');
	}
	
	public function cetakClicked($sender,$param)
    {		
		if($this->IsValid)  // when all validations succeed
        {
			$dateNow = date('Y-m-d');
			
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			$byrInap=$this->getViewState('modeByrInap');
			
			$jmlTagihan=$this->getViewState('tmpJml');
			$table=$this->getViewState('nmTable');
			$cm=$this->getViewState('cm');		
			$nama=$this->getViewState('nama');
			$dokter=$this->getViewState('dokter');
			$klinik=$this->getViewState('klinik');
			$id_dokter=$this->getViewState('id_dokter');
			$id_klinik=$this->getViewState('id_klinik');
			$notransinap=$this->getViewState('notransinap');				
			$operator=$this->User->IsUserNip;
			$nipTmp=$this->User->IsUserNip;	
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...
			
			if($jnsPasien == '0') //Pasien Rawat Jalan
			{
						
				$sql = "SELECT 
						no_trans 
					FROM 
						tbt_rawat_jalan 
					WHERE 
						cm = '$cm'
						AND dokter = '$id_dokter'
						AND id_klinik = '$id_klinik' 
						AND flag = 0
						AND st_alih = 0
						AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
				
				$noRwtJln=RwtjlnRecord::finder()->findBySql($sql)->no_trans;
				$noReg = $this->numRegister('tbt_lab_penjualan',LabJualRecord::finder(),'06');
				
				foreach($arrData as $row)
				{
					$notransTmp = $this->numCounter('tbt_lab_penjualan',LabJualRecord::finder(),'06');
					
					$transRwtJln= new LabJualRecord();
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->no_trans_rwtjln=$noRwtJln;	
					$transRwtJln->no_reg=$noReg;				
					$transRwtJln->cm=$cm;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					$transRwtJln->operator=$operator;
					if($this->getViewState('stAsuransi') == '1')//pasien penjamin
					$transRwtJln->tanggungan_asuransi=$row['total'];
				else
					$transRwtJln->harga=$row['total'];
					
					$transRwtJln->dokter=$id_dokter;
					$transRwtJln->klinik=$id_klinik;
					$transRwtJln->flag='0';
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_lab_rujukan=$row['id_lab_rujukan'];
					$transRwtJln->Save();
				}
			}elseif($jnsPasien == '1') //Pasien Rawat Inap
			{
				$noReg = $this->numRegister('tbt_lab_penjualan_inap',LabJualInapRecord::finder(),'11');
				
				foreach($arrData as $row)
				{
					$notransTmp1 = $this->numCounter('tbt_lab_penjualan_inap',LabJualInapRecord::finder(),'11');
					$notransTmp = $notransTmp1;
				
					$transRwtJln= new LabJualInapRecord();
					$transRwtJln->no_trans=$notransTmp1;
					$transRwtJln->no_trans_inap=$notransinap;
					$transRwtJln->no_reg=$noReg;
					$transRwtJln->cm=$cm;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					$transRwtJln->operator=$operator;
					
					if($this->getViewState('stAsuransi') == '1')//pasien penjamin
					$transRwtJln->tanggungan_asuransi=$row['total'];
				else
					$transRwtJln->harga=$row['total'];
					
					$transRwtJln->dokter=$id_dokter;
					$transRwtJln->klinik=$id_klinik;
					$transRwtJln->flag='0';
					
					if($byrInap=='0')
					{
						$transRwtJln->st_bayar='0';
					}else{				
						$transRwtJln->st_bayar='1';
					}				
					
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_lab_rujukan=$row['id_lab_rujukan'];
					
					$transRwtJln->Save();
				}
			}
			else //Pasien Rawat Luar / Pasien Regis Langsung
			{ 	
				//INSERT data pasien luar ke tbd_pasien_luar
				$notransPas = $this->numCounter('tbd_pasien_luar',PasienLuarRecord::finder(),'30');
				
				$data= new PasienLuarRecord();
				$data->no_trans = $notransPas;
				if($this->modeInput->SelectedValue = '3')
				{
					$tmp = $this->formatCm($this->notrans->Text);
					$data->cm = $tmp;
				}
				
				$data->nama = ucwords(trim($this->nmPasLuar->Text));
				$data->alamat = ucwords(trim($this->alamatPasLuar->Text));
				$data->umur = ucwords(trim($this->umur2->Text));
				//$data->jkel = ucwords(trim($this->jkel2->Text));
				$data->jkel = $this->ambilTxt($this->Rbjkel2);
				$data->Save();
				
				$noReg = $this->numRegister('tbt_lab_penjualan_lain',LabJualLainRecord::finder(),'14');
				
				//INSERT tbt_lab_penjualan_lain
				foreach($arrData as $row)
				{
					$notransTmp = $this->numCounter('tbt_lab_penjualan_lain',LabJualLainRecord::finder(),'14');
					
					$transRwtJln= new LabJualLainRecord();
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->no_trans_pas_luar = $notransPas;
					$transRwtJln->no_reg = $noReg;
					$transRwtJln->nama = $this->nmPasLuar->Text;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					$transRwtJln->operator=$operator;
					
					if($this->getViewState('stAsuransi') == '1')//pasien penjamin
					$transRwtJln->tanggungan_asuransi=$row['total'];
				else
					$transRwtJln->harga=$row['total'];
					
					$transRwtJln->harga_non_adm='0';
					$transRwtJln->harga_adm='0';
					$transRwtJln->flag='0';
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_lab_rujukan=$row['id_lab_rujukan'];
					$transRwtJln->Save();
				}	
			}
			
			//-------- Insert Harga Sisa Pembulatan ke tbt_obat_jual_sisa -----------------
			if($this->getViewState('sisaTmpTable'))
			{
				$sisaTmpTable = $this->getViewState('sisaTmpTable');
				$sql="SELECT SUM(jumlah) AS jumlah FROM $sisaTmpTable ";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$jmlSisa = $row['jumlah'];
				}
						
				if($jmlSisa > 0) //jika ada sisa pembulatan
				{
					if($jnsPasien == '0')
					{
						$notransTmp = $noRwtJln;
					}
					elseif($jnsPasien == '1')
					{
						$notransTmp = $notransinap;
					}
					else
					{
						$notransTmp = $notransPas;
					}
					
					$sql="SELECT * FROM $sisaTmpTable WHERE jumlah <> 0 ORDER BY id";
					$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
					foreach($arrData as $row)
					{
						$ObatJualSisa= new LabJualSisaRecord();
						$ObatJualSisa->no_trans=$notransTmp;
						$ObatJualSisa->jumlah=$row['jumlah'];
						$ObatJualSisa->tgl=date('y-m-d');
						$ObatJualSisa->Save();	
					}
				}
			}
			
			$this->clearState();	
			
			$this->errMsg->Text = '    
			<script type="text/javascript">
				alert("Data Rekam Billing Laboratorium Telah Masuk Dalam Database.");
				window.location="index.php?page=Lab.bayarLab"; 
			</script>';
				
			//$this->Response->redirect($this->Service->constructUrl('Lab.bayarLabSukses'));
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent(); ');	
		}	
	}
}
?>
