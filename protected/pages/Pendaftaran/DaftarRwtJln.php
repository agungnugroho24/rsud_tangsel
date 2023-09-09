<?php
class DaftarRwtJln extends SimakConf
{
	 public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('10');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
		
		if(!$this->isPostBack && !$this->isCallBack )	            		
		{		
			$this->cm->focus();
			$this->noKarcis->Enabled=false;
			$this->simpanBtn->Enabled = false;
			$this->panel->Display = 'None';
				
			$this->DDPerusAsuransi->Enabled = false;
			$this->hubPasien->Enabled = false;
			
			$this->sjpView->Visible= false;//No. SJP Askes disabled dulu		
			
			$cm = $this->formatCm($this->Request['cm']);
			$this->setViewState('backTo',$this->Request['mode']);
			if($cm)
			{
				$this->cm->Text = intval($cm);
				$kelPasien = PasienRecord::finder()->findByPk($cm)->kelompok;
				$perusPasien = PasienRecord::finder()->findByPk($cm)->perusahaan;
				$nmPas = PasienRecord::finder()->findByPk($cm)->nama;
				$this->hubPasien->Enabled = false;
				$hubPasien = PasienRecord::finder()->findByPk($cm)->hubungan_pj;
				//$this->showSql->Text = $kelPasien.' - '.$perusPasien;
				/*
				if($hubPasien != '')
				{
					if($hubPasien == 'Suami')		{$this->DDHubPen->SelectedValue = '0';}
					elseif($hubPasien == 'Istri')	{$this->DDHubPen->SelectedValue = '1';}
					elseif($hubPasien == 'Ayah')	{$this->DDHubPen->SelectedValue = '2';}
					elseif($hubPasien == 'Ibu')	{$this->DDHubPen->SelectedValue = '3';}
					elseif($hubPasien == 'Anak')	{$this->DDHubPen->SelectedValue = '4';}
					elseif($hubPasien == 'Saudara')	{$this->DDHubPen->SelectedValue = '5';}
					else
					{
						$this->DDHubPen->SelectedValue = '6';
						$this->hubPasien->Enabled = true;
						$this->hubPasien->Text = $hubPasien ;
						$this->setViewState('hubPasien',$hubPasien);
					}
					
					
				}
				else
				{
					$this->DDHubPen->SelectedValue = 'empty';
				}
				*/
				$this->DDHubPen->SelectedValue = 'empty';
				
				if($kelPasien != '01' && $kelPasien != '') //bukan kelompok pasien UMUM
				{
					$this->noKarcis->Enabled=false;	
					if($kelPasien == '02') //kelompok pasien NON UMUM
					{
						if($perusPasien == '01') //askes
						{
							$this->valAsuransi->Text = 'Askes';
							$this->valAskes->Text = 'Askes';
							$this->sjpView->Visible= true;
							$this->kodeRS->Text = '0120R004';
							$this->kodeTgl->Text = date('my');
							$this->kodeCek->Text = 'Y';
							$this->setViewState('stAskes','1');
							$this->sjpView->Visible= true;//No. SJP Askes disabled dulu
						}
						elseif($perusPasien == '03') //sktm
						{
							$this->valAsuransi->Text = 'SKTM';
						}
						elseif($perusPasien == '05') //gakinda
						{
							$this->valAsuransi->Text = 'Gakinda';
						}
						elseif($perusPasien == '07') //jamkesmas
						{
							$this->valAsuransi->Text = 'Jamkesmas';
						}
						elseif($perusPasien == '09') //jampersal
						{
							$this->valAsuransi->Text = 'Jampersal';
						}
						elseif($perusPasien == '13') //jampersal
						{
							$this->valAsuransi->Text = 'Karyawan';
						}
						else
						{
							$this->valAsuransi->Text = 'Asuransi';
						}
			
						$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$kelPasien' AND st='0' ORDER BY nama";
						$data = $this->queryAction($sql,'S');
						
						$this->DDPerusAsuransi->DataSource = $data;
						$this->DDPerusAsuransi->dataBind();
						
						$this->DDPerusAsuransi->SelectedValue = $perusPasien;
						$this->DDPerusAsuransi->Enabled = true;
						
						$this->setViewState('perusAsuransi',PasienRecord::finder()->findByPk($cm)->perusahaan);
					}
					
					$this->setViewState('kelPasien',$kelPasien);
					$this->RBvalAsuransi->SelectedValue = '1';
					$this->RBvalAsuransi->Enabled = true;
					
				}
				else //kelompok pasien UMUM
				{ 
					//$this->RBvalAsuransi->Enabled = false;
					$this->valAsuransi->Text = 'Asuransi';
					$this->RBvalAsuransi->SelectedValue = '0';
					$this->DDKelompok->Enabled = false;
					$this->DDPerusAsuransi->Enabled = false;
					$this->noKarcis->Enabled=true;
					$this->setViewState('stAskes','0');
					$this->sjpView->Visible= false;//No. SJP Askes disabled dulu
				}
				
				$this->nmPas->Text = $nmPas;
				$this->DDKelompok->SelectedValue = PasienRecord::finder()->findByPk($cm)->kelompok;
				$this->setViewState('kelompokPas',$this->DDKelompok->SelectedValue);
				
				//$this->penttg->Text = PasienRecord::finder()->findByPk($cm)->nm_pj;
				$this->penttg->Text = PasienRecord::finder()->findByPk($cm)->nama;
				
				//$this->alamatPj->Text = PasienRecord::finder()->findByPk($cm)->alamat_pj;
				$this->alamatPj->Text = PasienRecord::finder()->findByPk($cm)->alamat;
				
				//$this->tlpPj->Text = PasienRecord::finder()->findByPk($cm)->telp_pj;
				$this->tlpPj->Text = PasienRecord::finder()->findByPk($cm)->telp;
				
				//$this->hpPj->Text = PasienRecord::finder()->findByPk($cm)->hp_pj;
				$this->hpPj->Text = PasienRecord::finder()->findByPk($cm)->hp;
				
				$this->cariCmPanel->Enabled = false;
				$this->panel->Display = 'Dynamic';
				$this->simpanBtn->Enabled = true;
				$this->nmPas->focus();
			}
			
			
			$this->crMskLuar->Enabled=false;
			$this->DDDokter->Enabled=false;		
			$this->wktMsk->Text=date("G:i:s");
			$this->tglMsk->Text=date("d-m-Y"); 
			$this->tglMsk->Enabled = false;
			$this->wktMsk->Enabled = false;
			
			$sql = "SELECT * FROM tbm_poliklinik ORDER BY nama ";
			$this->DDKlinik->DataSource = PoliklinikRecord::finder()->findAllBySql($sql);
			$this->DDKlinik->dataBind();  
			
			
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll();
			$this->DDDokter->dataBind(); 
			
			//$this->modeInput->SelectedValue = '0';
			$this->modeInputChanged();
			
			$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
			$this->DDCaraMsk->dataBind();
			$this->DDCaraMsk->Enabled = false;
			
			$this->DDbidanPerujuk->DataSource=BidanPerujukRecord::finder()->findAll();
			$this->DDbidanPerujuk->dataBind();
			$this->DDbidanPerujuk->Enabled = false;
			
			$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll($criteria);
			$this->DDKelompok->dataBind();
			
			$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDPerusAsuransi->dataBind();
			
			//$this->DDKelompok->SelectedValue='01';
			//$this->crMskLuar->Visible = false;
			//$this->DDbidanPerujuk->Visible = false;
			
			//$this->suksesMsgCtrl->Visible = false;
			//$this->suksesMsg->Text = '' ;
		}
	}
	
	public function checkKarcis($sender,$param)
    {
		//$param->IsValid=PasienRecord::finder()->findByPk($this->cm->Text)===null;		
		$checkKarcis=$this->noKarcis->Text;
		if(strlen($checkKarcis) < 6)
		{
			$this->msg->Text = '    
				<script type="text/javascript">
					alert("No. Karcis '.$checkKarcis.' TIDAK VALID.\nSilakan cek ulang kembali No. Karcis !");
					window.location="index.php?page=Pendaftaran.DaftarRwtJln";					
				</script>';
		}
				$this->penttg->focus();
			
	}	
	
	public function cmCallBack($sender,$param)
   	{
		$this->cariCmPanel->render($param->getNewWriter());
		$this->panel->render($param->getNewWriter());
	}
	
	public function cekKlinik($sender,$param)
	{
		 if($param->Value == ($this->DDKlinik->SelectedValue == ''))
            $param->IsValid=false;
	}
	
	public function cekCaraMsk($sender,$param)
	{
		 if($param->Value == ($this->DDCaraMsk->SelectedValue == ''))
            $param->IsValid=false;
	}
	
	public function cekDokter($sender,$param)
	{
		 if($param->Value == ($this->DDDokter->SelectedValue == ''))
            $param->IsValid=false;
	}
		
	public function chKlinik($sender,$param)
	{
		if($this->DDKlinik->SelectedValue=='')
		{
			$this->DDDokter->SelectedValue='empty';
			$this->DDDokter->Enabled=false;
			$this->Page->CallbackClient->focus($this->DDKlinik);
		}
		/*elseif($this->DDKlinik->SelectedValue=='06' || $this->DDKlinik->SelectedValue=='11')//IGD / PEMERIKSAAN KESEHATAN
		{
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok='1' AND poliklinik='01' ORDER BY nama ";
			//$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1','08');
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAllBySql($sql);
			$this->DDDokter->dataBind();
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;	
			$this->DDDokter->Enabled=true;	
			$this->Page->CallbackClient->focus($this->DDDokter);
		}
		elseif($this->DDKlinik->SelectedValue=='15')//HAJI
		{
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok='1' AND (poliklinik='01' OR spesialis IS NOT NULL OR spesialis <> '') ORDER BY nama ";
			//$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1','08');
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAllBySql($sql);
			$this->DDDokter->dataBind();
			$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;	
			$this->DDDokter->Enabled=true;	
			$this->Page->CallbackClient->focus($this->DDDokter);
		}*/
		else
		{
			$idKlinik = $this->DDKlinik->SelectedValue;
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok='1' AND FIND_IN_SET('$idKlinik',poliklinik) ORDER BY nama ";
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAllBySql($sql);
			//$this->DDDokter->DataSource = PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDKlinik->SelectedValue);			
			
			$jmlData = count(PegawaiRecord::finder()->findAllBySql($sql));
			
			if($jmlData > 0)
				$this->DDDokter->Enabled=true;
			else	
				$this->DDDokter->Enabled=false;
				
			$this->DDDokter->dataBind();
			
			$klinik = PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
			$this->Page->CallbackClient->focus($this->DDDokter);
		}
				
		$this->doublePasienMsg->Text='';
	}
	
	/*
	public function modeRujukanChanged($sender,$param)
    {	
		if($this->collectSelectionResult($this->modeInput) == '1')//rujukan
		{
			$this->nmPerujuk->Enabled = true;
		}
		else //non rujukan
		{
			$this->nmPerujuk->Enabled = false;
		}
		
	}	
	*/
	
	public function checkRM($sender,$param)
    {
		//$param->IsValid=PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text))===null;		
		$checkRM=$this->formatCm($this->cm->Text);
		//$sql="SELECT * FROM du_pasien WHERE cm='$checkRM'";		
		//$data=$this->queryAction($sql,'S');		
		$dummy=PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text));		
		$this->setViewState('cm',$checkRM);		
		$nmPas = PasienRecord::finder()->findByPk($checkRM)->nama;
		$rj=RwtjlnRecord::finder()->find('cm = ?',$checkRM);
		$ri=RwtInapRecord::finder()->find('cm = ?',$checkRM);
		if(($rj) || ($ri)){		
			$this->setViewState('pas','1');
		} else{
			$this->setViewState('pas','0');
		}		
		
		//$this->suksesMsgCtrl->Visible = false;
		//$this->suksesMsg->Text = '' ;
		
		//$this->errMsg->Text =$jam;		
		
		//$id=DUPasienRecord::finder()->finderByPK($checkRM);
		/*
		if($data === null && $dummy === null)
		{			
			$this->errMsg->text='';					
			$this->nama->focus();			
		}
		else */if($dummy===null)
		{	/*				
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
			}else{*/
			
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$checkRM.' Tidak Ditemukan.\nSilakan Lakukan Pendaftaran Pasien Baru !");
					window.location="index.php?page=Pendaftaran.DaftarBaru"; 
				</script>';
			
				//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));								
			//}			
		}else if ($dummy){
			$this->Page->CallbackClient->focus($this->DDKlinik);
			$tgl=PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text))->tgl_lahir;
			$thn=date('Y')-intval(substr($tgl,0,4));
			$bln=date('m')-intval(substr($tgl,5,2));
			$hari=date('d')-intval(substr($tgl,8,2));	
			if ($hari<0){
				$bln=$bln-1;
				$hari=date('t')+$hari;
			}
			
			$hubPasien = PasienRecord::finder()->findByPk($checkRM)->hubungan_pj;
			
			/*
			if($hubPasien != '')
			{
				if($hubPasien == 'Suami')		{$this->DDHubPen->SelectedValue = '0';}
				elseif($hubPasien == 'Istri')	{$this->DDHubPen->SelectedValue = '1';}
				elseif($hubPasien == 'Ayah')	{$this->DDHubPen->SelectedValue = '2';}
				elseif($hubPasien == 'Ibu')	{$this->DDHubPen->SelectedValue = '3';}
				elseif($hubPasien == 'Anak')	{$this->DDHubPen->SelectedValue = '4';}
				elseif($hubPasien == 'Saudara')	{$this->DDHubPen->SelectedValue = '5';}
				else
				{
					$this->DDHubPen->SelectedValue = '6';
					$this->hubPasien->Enabled = true;
					$this->hubPasien->Text = $hubPasien ;
					$this->setViewState('hubPasien',$hubPasien);
				}
			}
			else
			{
				$this->DDHubPen->SelectedValue = 'empty';
			}
			*/
			$this->DDHubPen->SelectedValue = 'empty';
			
			$kelPasien = PasienRecord::finder()->findByPk($checkRM)->kelompok; 
			$perusPasien = PasienRecord::finder()->findByPk($checkRM)->perusahaan;
			
			if($kelPasien != '01' && $kelPasien != '') //bukan kelompok pasien UMUM
			{
				$this->noKarcis->Enabled = false;
				if($kelPasien == '02') //kelompok pasien NON UMUM
				{
					if($perusPasien == '01') //askes
					{
						$this->valAsuransi->Text = 'Askes';
						$this->valAskes->Text = 'Askes';
						$this->sjpView->Visible= true;
						$this->kodeRS->Text = '0120R004';
						$this->kodeTgl->Text = date('my');
						$this->kodeCek->Text = 'Y';
						$this->setViewState('stAskes','1');
						$this->sjpView->Visible= true;//No. SJP Askes disabled dulu
					}
					elseif($perusPasien == '03') //sktm
					{
						$this->valAsuransi->Text = 'SKTM';
					}
					elseif($perusPasien == '05') //gakinda
					{
						$this->valAsuransi->Text = 'Gakinda';
					}
					elseif($perusPasien == '07') //jamkesmas
					{
						$this->valAsuransi->Text = 'Jamkesmas';
					}
					elseif($perusPasien == '09') //jampersal
					{
						$this->valAsuransi->Text = 'Jampersal';
					}
					elseif($perusPasien == '13') //jampersal
					{
						$this->valAsuransi->Text = 'Karyawan';
					}
					else
					{
						$this->valAsuransi->Text = 'Asuransi';
					}
				
					$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$kelPasien' AND st='0' ORDER BY nama";
					$data = $this->queryAction($sql,'S');
					
					$this->DDPerusAsuransi->DataSource = $data;
					$this->DDPerusAsuransi->dataBind();
					
					$this->DDPerusAsuransi->SelectedValue = $perusPasien;
					$this->DDPerusAsuransi->Enabled = true;
					
					$this->setViewState('perusAsuransi',$perusPasien);
				}
				
				$this->setViewState('kelPasien',$kelPasien);
				$this->RBvalAsuransi->SelectedValue = '1';
				$this->RBvalAsuransi->Enabled = true;
			}
			elseif($kelPasien == '01') //kelompok pasien UMUM
			{ 
				$this->noKarcis->Enabled = true;
				//$this->RBvalAsuransi->Enabled = false;
				$this->valAsuransi->Text = 'Asuransi';
				$this->RBvalAsuransi->SelectedValue = '0';
				$this->DDKelompok->Enabled = false;
				$this->DDPerusAsuransi->Enabled = false;
			}
			else
			{
				$this->DDKelompok->Enabled = false;
				$this->DDPerusAsuransi->Enabled = false;
			}
			//$this->valAsuransi->Text = $kelPasien;
			//$this->keluhan->text='Umur : '.$thn.' '.$bln.' '.$hari;
		}
		
		$this->cariCmPanel->Enabled = false;
		$this->panel->Display = 'Dynamic';
		$this->simpanBtn->Enabled = true;
				
		$this->nmPas->Text = $nmPas;
		
		$this->DDKelompok->SelectedValue = PasienRecord::finder()->findByPk($checkRM)->kelompok;
		$this->setViewState('kelompokPas',$this->DDKelompok->SelectedValue);
		
		//$this->penttg->Text = PasienRecord::finder()->findByPk($checkRM)->nm_pj;
		$this->penttg->Text = PasienRecord::finder()->findByPk($checkRM)->nama;
		
		//$this->alamatPj->Text = PasienRecord::finder()->findByPk($checkRM)->alamat_pj;
		$this->alamatPj->Text = PasienRecord::finder()->findByPk($checkRM)->alamat;
		
		//$this->tlpPj->Text = PasienRecord::finder()->findByPk($checkRM)->telp_pj;
		$this->tlpPj->Text = PasienRecord::finder()->findByPk($checkRM)->telp;
		
		//$this->hpPj->Text = PasienRecord::finder()->findByPk($checkRM)->hp_pj;
		$this->hpPj->Text = PasienRecord::finder()->findByPk($checkRM)->hp;
		
		$this->doublePasienMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->nmPas->getClientID().'.focus();
				</script>';
    }
	
	//Cek validitas no SJP
	public function checkSJP($sender,$param)
    {
		if(strlen($this->noSJP->Text) < 6)
		{
			$this->msgAskes->Text = '    
				<script type="text/javascript">
					alert("No. SJP '.$this->noSJP->Text.' Tidak Benar, harap cek kembali !");					
				</script>';
		}
	}
	
	protected function DDDokterChanged ()
	{
		$idKlinik = $this->DDKlinik->SelectedValue;
		$idDokter = $this->DDDokter->SelectedValue;
		$tgl = date('Y-m-d');
		$wkt = date('G:i:s');		
		$cm = $this->formatCm($this->cm->Text);
		if($this->DDDokter->SelectedValue != '')
		{	
			$sql="SELECT 
						no_trans,id_klinik,penjamin,perus_asuransi,st_asuransi
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND dokter='$idDokter'
						AND id_klinik='$idKlinik'
						AND flag='0'
						AND st_alih='0'
						AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
								
			//if(RwtjlnRecord::finder()->find('cm = ? AND dokter = ? AND tgl_visit = ? AND flag=0 AND st_alih=0',$this->formatCm($this->cm->Text),$idDokter,$tgl))
			if(RwtjlnRecord::finder()->findBySql($sql))
			{
				$data = RwtjlnRecord::finder()->findBySql($sql);
				$tglMasuk = $data->tgl_visit;
				$wktMasuk = $data->wkt_visit;
				
				$idPoli = $data->id_klinik;
				$nmPoli = PoliklinikRecord::finder()->findByPk($idPoli)->nama;
				
				$nmPoliTmp = explode(' ',$nmPoli);
				$nmPoliTmp = $nmPoliTmp['0'];
				
				if(strtolower($nmPoliTmp) != 'poli' )
				{
					$nmPoli = 'Poli '.$nmPoli;
				}
					
				$nmDokter = $this->ambilTxt($this->DDDokter);
			
				$this->doublePasienMsg->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$cm.' \ndengan dokter rawat '.$nmDokter.'\nmasih terdaftar di '.$nmPoli.'");
					document.all.'.$this->DDKlinik->getClientID().'.focus();
				</script>';
				
				$this->DDKlinik->SelectedValue = 'empty';
				$this->DDDokter->SelectedValue = 'empty';
				$this->DDDokter->Enabled = false;
				//$this->doublePasienMsg->Text='Pasien telah terdaftar !';
				//$this->simpanBtn->Enabled = false;
			}
			else
			{
				$this->doublePasienMsg->Text='';
				$this->simpanBtn->Enabled = true;
				$this->Page->CallbackClient->focus($this->DDCaraMsk);
			}
		}
		else
		{
			$this->Page->CallbackClient->focus($this->DDDokter);
			$this->simpanBtn->Enabled = true;
		}
		
		
	}
	
	protected function modeInputChanged()
	{
		$jnsRujuk = $this->modeInput->SelectedValue;
		
		$sql="SELECT 
					id,nama	
				FROM 
					tbm_cara_masuk";
			$arr=$this->queryAction($sql,'S');
			
		if($jnsRujuk == '0') //Non Rujukan
		{
			foreach($arr as $row)
			{
				$id = $row['id'];	
				$nama = $row['nama'];	
				
				if($id == '1') // datang sendiri 
				{
					$data[]=array('id'=>$id,'nama'=>$nama);
				}
			}
			
			$this->DDCaraMsk->DataSource = $data;
			$this->DDCaraMsk->dataBind();
			
			$this->DDCaraMsk->SelectedValue = '1';
			$this->DDCaraMsk->Enabled = false;
			
		}
		elseif($jnsRujuk == '2') //Rujukan dari dalam
		{
			foreach($arr as $row)
			{
				$id = $row['id'];	
				$nama = $row['nama'];	
				
				if($id == '7') // dokter dalam 
				{
					$data[]=array('id'=>$id,'nama'=>$nama);
				}
			}
			
			$this->DDCaraMsk->DataSource = $data;
			$this->DDCaraMsk->dataBind();
			
			$this->DDCaraMsk->SelectedValue = '7';
			$this->DDCaraMsk->Enabled = false;
		}
		else //Rujukan dari luar (bukan datang sendiri dan bukan dokter dalam)
		{			
			foreach($arr as $row)
			{
				$id = $row['id'];	
				$nama = $row['nama'];	
				
				if($id != '1' && $id != '7') //bukan datang sendiri dan bukan dokter dalam
				{
					$data[]=array('id'=>$id,'nama'=>$nama);
				}
			}
			
			$this->DDCaraMsk->DataSource = $data;
			$this->DDCaraMsk->dataBind();
			
			$this->DDCaraMsk->SelectedValue = 'empty';
			$this->DDCaraMsk->Enabled = true;
		}
		
		$this->caraMasuk();
	}
	
	protected function caraMasuk ()
	{	
		if(($this->DDCaraMsk->SelectedValue != '1') && ($this->DDCaraMsk->SelectedValue != '') )
		{
			/*if($this->DDCaraMsk->SelectedValue == '5')//rujukan luar dari bidan
			{	
				$this->DDbidanPerujuk->DataSource=BidanPerujukRecord::finder()->findAll();
				$this->DDbidanPerujuk->dataBind(); 
				
				$this->crMskLuar->Visible = false;
				$this->DDbidanPerujuk->Visible = true;
				
				$this->DDbidanPerujuk->Enabled = true;	
				$this->crMskLuar->Enabled=false;
				
				$this->Page->CallbackClient->focus($this->DDbidanPerujuk);
			}
			else*/
			if($this->DDCaraMsk->SelectedValue == '7') //rujukan dalam dari dokter dalam
			{
				$sql="SELECT id,nama FROM  tbd_pegawai WHERE kelompok = '1' ORDER BY nama";
				$arr = $this->queryAction($sql,'S');
			
				$this->DDbidanPerujuk->DataSource = $arr;
				$this->DDbidanPerujuk->dataBind();
				
				$this->crMskLuar->Visible = false;
				$this->DDbidanPerujuk->Visible = true;
				
				$this->DDbidanPerujuk->Enabled = true;	
				$this->crMskLuar->Enabled=false;
				
				$this->Page->CallbackClient->focus($this->DDbidanPerujuk);
			}
			else //rujukan luar selain bidan
			{		
				$this->crMskLuar->Visible = true;
				$this->DDbidanPerujuk->Visible = true;
				
				$this->DDbidanPerujuk->Enabled = true;	
				$this->crMskLuar->Enabled=true;
				
				$this->Page->CallbackClient->focus($this->crMskLuar);	
			}
		}
		else
		{
			$this->crMskLuar->Visible = true;
			$this->DDbidanPerujuk->Visible = false;
				
			$this->crMskLuar->Enabled=false;	
			$this->DDbidanPerujuk->Enabled = false;		
		}
		
		$this->crMskLuar->Text = '';	
		
	}		   
	
	public function RBvalAsuransiChanged($sender,$param)
	{
		$stAsuransi = $this->RBvalAsuransi->SelectedValue;
		$this->DDPerusAsuransi->Enabled = false;
		
		if($stAsuransi == '0') //Pejamin tidak berlaku
		{
			$this->DDKelompok->SelectedValue = '01';
			$this->DDKelompok->Enabled = false;
			
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
			
			$this->noKarcis->Enabled=true;
			$this->noKarcis->focus();
			
			$this->setViewState('stAskes','0');
			$this->sjpView->Visible= false;//No. SJP Askes disabled dulu
		}
		else //Penjamin berlaku
		{
			//$this->DDKelompok->SelectedValue = $this->getViewState('kelompokPas');
			$this->noKarcis->Enabled=false;
			$this->DDKelompok->Enabled = true;
			$this->DDKelompok->SelectedValue = 'empty';		
			$this->DDPerusAsuransi->SelectedValue = 'empty';				
			
			if($this->getViewState('kelPasien'))
			{
				$this->DDKelompok->SelectedValue = $this->getViewState('kelPasien');
			}
			
			if($this->getViewState('stAskes')=='1')
			{
				$this->sjpView->Visible= true;//No. SJP Askes disabled dulu
			}
			$this->selectionChangedKelompok();
			$this->DDKelompok->focus();
		}
	}
	
	public function DDKelompokCallback()
	{
		$this->panel->render($param->getNewWriter());		
	}
	
	public function selectionChangedKelompok()
	{
		$this->setViewState('stAskes','0');
		$this->sjpView->Visible= false;//No. SJP Askes disabled dulu	
					
		if($this->DDKelompok->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->noKarcis->Enabled = true;
			$this->RBvalAsuransi->SelectedValue = '0';
			
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			$this->noKarcis->Enabled = false;
			$this->RBvalAsuransi->SelectedValue = '1';
			
			if($this->DDKelompok->SelectedValue == '02')
			{
				$this->DDPerusAsuransi->Enabled = true;	
				$idKelPerus = $this->DDKelompok->SelectedValue;
			
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' AND st='0' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerusAsuransi->DataSource = $data;
				$this->DDPerusAsuransi->dataBind();
			
				if($this->getViewState('perusAsuransi'))
				{	
					$this->DDPerusAsuransi->SelectedValue = $this->getViewState('perusAsuransi');
					
					if($this->getViewState('perusAsuransi') == '01')//askes
					{
						$this->valAsuransi->Text = 'Askes';
						$this->valAskes->Text = 'Askes';
						$this->sjpView->Visible= true;
						$this->kodeRS->Text = '0120R004';
						$this->kodeTgl->Text = date('my');
						$this->kodeCek->Text = 'Y';
						$this->setViewState('stAskes','1');
						$this->sjpView->Visible= true;//No. SJP Askes disabled dulu
					}
				}	
			}
			else
			{
				$this->DDPerusAsuransi->SelectedValue = 'empty';
				$this->DDPerusAsuransi->Enabled = false;
			}
		}
	}
	
	public function DDHubPenChanged($sender,$param)
	{
		if($this->DDHubPen->SelectedValue != '' ) //hubungan lainnya
		{
			if($this->DDHubPen->SelectedValue == '6')
			{
				if($this->getViewState('hubPasien'))
				{	
					$this->hubPasien->Text = $this->getViewState('hubPasien');
				}
				
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
	
	public function simpanClicked($sender,$param)
	{
		if($this->IsValid)
		{
			$cm = $this->formatCm($this->cm->Text);
			$poli = $this->DDKlinik->SelectedValue;
			$dokter = $this->DDDokter->SelectedValue;
			
			$nmPoli = $this->ambilTxt($this->DDKlinik);
			$nmDokter = $this->ambilTxt($this->DDDokter);
			$dateNow = date('Y-m-d');
			
			$sql = "SELECT * FROM tbt_rawat_jalan WHERE cm='$cm' AND id_klinik = '$poli' AND dokter = '$dokter' AND tgl_visit = '$dateNow' AND flag='0' ";
			$arr = $this->queryAction($sql,'S');
			
			if($arr)
			{
				$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
				/*
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Pendaftaran Rawat Jalan Tidak Bisa Dilakukan Karena Pasien Dengan No. RM '.$cm.' Masih Terdaftar Untuk Poliklinik dan Dokter Yang Dipilih !<br/><br/></p>\',timeout: 5000,dialog:{
						modal: true,
						buttons: {
							"OK": function() {
								jQuery( this ).dialog( "close" );								
							}
						}
					}});');	
				*/	
			}
			else
			{
				//UPDATE st_baru_lama di tbd_pasien
				$dataRwtJalan = RwtjlnRecord::finder()->find('cm=?',$cm);
				$dataRwtInap = RwtInapRecord::finder()->find('cm=?',$cm);
				
				if($dataRwtJalan || $dataRwtInap)
				{
					$data = PasienRecord::finder()->findByPk($cm);
					$data->st_baru_lama = '1';
					$data->save();
				}
				
				$notran=$this->numCounter('tbt_rawat_jalan',RwtjlnRecord::finder(),'01');
				
				$newRwtJln=new RwtjlnRecord();
				$newRwtJln->cm = $this->formatCm($this->cm->Text);
				$newRwtJln->no_trans=$notran;
				$newRwtJln->no_karcis=trim($this->noKarcis->Text);
				//key '01' adalah konstanta modul untuk Rawat Jalan
				$newRwtJln->id_klinik=(string)$this->DDKlinik->SelectedValue;
				$newRwtJln->dokter=(string)$this->DDDokter->SelectedValue;
				$newRwtJln->tgl_visit=$this->ConvertDate($this->tglMsk->Text,'2');
				$newRwtJln->wkt_visit=$this->wktMsk->Text;
				//$newRwtJln->keluhan=$this->keluhan->Text;
				//$newRwtJln->catatan=$this->catatan->Text;
				
				$newRwtJln->st_rujuk = $this->collectSelectionResult($this->modeInput);
				$newRwtJln->st_asuransi = $this->RBvalAsuransi->SelectedValue;
				$newRwtJln->penjamin = $this->DDKelompok->SelectedValue;
				
				if($this->DDKelompok->SelectedValue == '02') //kelompok pasien NON UMUM
				{
					$newRwtJln->perus_asuransi = $this->DDPerusAsuransi->SelectedValue;
				}
				else
				{
					$newRwtJln->perus_asuransi = '';
				}
				
				/*if($this->DDCaraMsk->SelectedValue == '5')//rujukan bidan
				{	
					$newRwtJln->id_bidan_perujuk = $this->DDbidanPerujuk->SelectedValue;
					$newRwtJln->nm_perujuk = $this->ambilTxt($this->DDbidanPerujuk);
				}
				else*/
				if($this->DDCaraMsk->SelectedValue == '7') //rujukan dalam dari dokter dalam
				{
					$newRwtJln->id_dokter_perujuk_dalam = $this->DDbidanPerujuk->SelectedValue;
					$newRwtJln->nm_perujuk = $this->ambilTxt($this->DDbidanPerujuk);
				}
				else
				{
					$newRwtJln->nm_perujuk = $this->crMskLuar->Text;	
				}
				
				$newRwtJln->penanggung_jawab = $this->penttg->Text;
				
				if($this->DDHubPen->SelectedValue != '' ) 
				{
					if($this->DDHubPen->SelectedValue == '6') //hubungan lainnya
					{
						$newRwtJln->hubungan_pj = $this->hubPasien->Text;
					}
					else
					{
						$newRwtJln->hubungan_pj = $this->ambilTxt($this->DDHubPen);		
					}
				}
				else
				{
					$newRwtJln->hubungan_pj = '';
				}
				
				$newRwtJln->alamat_pj = $this->alamatPj->Text;
				$newRwtJln->telp_pj = $this->tlpPj->Text;
				$newRwtJln->hp_pj = $this->hpPj->Text;
				
				$newRwtJln->st_tebus_luar = '0';
				
				$newRwtJln->cr_masuk=(string)$this->DDCaraMsk->SelectedValue;
				if($this->DDCaraMsk->SelectedValue == '3' || $this->DDCaraMsk->SelectedValue=='4' || $this->DDCaraMsk->SelectedValue=='5')
				{
					$newCaraMsk=new SimpanCaraMskRecord();
					$newCaraMsk->no_trans=$this->numCounter('tbt_rawat_jalan',RwtjlnRecord::finder(),'01');
					//key '01' adalah konstanta modul untuk Rawat Jalan
					$newCaraMsk->keterangan = $this->crMskLuar->Text;
					//$newCaraMsk->save(); 
				}
				$newRwtJln->shift = $this->shift->SelectedValue;
				$newRwtJln->save();	
				
				//INSERT tbt_kasir_pendaftaran
				$newRecord= new KasirPendaftaranRecord();
				$newRecord->no_trans=$notran;
				$newRecord->klinik=(string)$this->DDKlinik->SelectedValue;
				$newRecord->dokter=(string)$this->DDDokter->SelectedValue;
				$newRecord->no_karcis=trim($this->noKarcis->Text);
				$newRecord->id_tindakan='';
				$newRecord->tgl=date('y-m-d');
				$newRecord->waktu=date('G:i:s');
				$newRecord->operator=$this->User->Name;
				
				$st_flag = '0';
				if($this->DDKelompok->SelectedValue == '02') //kelompok pasien NON UMUM
				{
					$id_poli = $this->DDKlinik->SelectedValue;
					$id_provider = $this->DDPerusAsuransi->SelectedValue;
					
					$sql = "SELECT tarif FROM tbm_provider_detail_retribusi WHERE id_poli = '$id_poli' AND id_provider = '$id_provider' ";					
					if(count($this->queryAction($sql,'S')) > 0)
					{
						$data = ProviderDetailRetribusiRecord::finder()->find('id_poli=? AND id_provider=?',$this->DDKlinik->SelectedValue,$this->DDPerusAsuransi->SelectedValue);
						$retribusi = $data->tarif;
						/*if($retribusi == 0)
						{
							$st_flag = '1';
							$newRecord->tgl_kasir=date('y-m-d');
							$newRecord->wkt_kasir=date('G:i:s');
							$newRecord->operator_kasir=$this->User->Name;
						}*/
					}
					else
						$retribusi = TarifPendaftaranRecord::finder()->find('id_klinik=? AND shift=?',array($this->DDKlinik->SelectedValue,$this->shift->SelectedValue))->tarif;
				}
				else
					$retribusi = TarifPendaftaranRecord::finder()->find('id_klinik=? AND shift=?',array($this->DDKlinik->SelectedValue,$this->shift->SelectedValue))->tarif;
				
				if($retribusi == 0)
				{
					$st_flag = '1';
					$newRecord->tgl_kasir=date('y-m-d');
					$newRecord->wkt_kasir=date('G:i:s');
					$newRecord->operator_kasir=$this->User->Name;
				}
				
				$newRecord->tarif=$retribusi;
				$newRecord->st_flag=$st_flag;
				$newRecord->Save();			
				
				//Bila pasien ASKES simpan data transaksi SJP ke dalam tabel tbt_rawat_jalan_askes
				if($this->getViewState('stAskes') == '1')
				{
					$newRwtJlnAskes=new RwtJlnAskesRecord();
					$newRwtJlnAskes->no_trans_rwtjln=$notran;
					$newRwtJlnAskes->tgl=$this->ConvertDate($this->tglMsk->Text,'2');				
					$newRwtJlnAskes->wkt=$this->wktMsk->Text;
					$newRwtJlnAskes->no_sjp=$this->noSJP->Text;
					$newRwtJlnAskes->operator=$this->User->IsUserNip;
					$newRwtJlnAskes->st_claim='0';
					$newRwtJlnAskes->st_bayar='0';
					$newRwtJlnAskes->save();		
				}
				
				////$this->suksesMsgCtrl->Visible = true;
				////$this->suksesMsg->Text = "Pendaftaran Pasien Rawat Jalan dengan No. Rekam Medis ".$this->formatCm($this->cm->Text)." <br/> Berhasil Ditambahkan Ke Dalam Database" ;
				//$this->setViewState('st_simpan','1');
				//$this->batalClicked();
			//$this->msg->Text="Data telah disimpan!";
			//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKartu'));		
			
			$klinik = $this->DDKlinik->SelectedValue;
			/*if($klinik == '04')//Bila POLI GIGI
			{
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data pasien rawat jalan telah disimpan !");
					window.location="index.php?page=Tarif.cetakKartuStatusGigi&cm='.$this->formatCm($this->cm->Text).'&poli='.$this->DDKlinik->SelectedValue.'&dokter='.$this->DDDokter->SelectedValue.'&pas='.$this->getViewState('pas').'&pen='.$this->penttg->Text.'&notrans='.$notran.'"; 
				</script>';		
			}
			elseif($klinik == '06')//Bila IGD
			{
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data pasien rawat jalan telah disimpan !");
					window.location="index.php?page=Tarif.cetakKartuStatusIgd&cm='.$this->formatCm($this->cm->Text).'&poli='.$this->DDKlinik->SelectedValue.'&dokter='.$this->DDDokter->SelectedValue.'&pas='.$this->getViewState('pas').'&pen='.$this->penttg->Text.'&notrans='.$notran.'"; 
				</script>';		
			}
			else
			{
				$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data pasien rawat jalan telah disimpan !");
					window.location="index.php?page=Tarif.cetakKartuStatusPoli&cm='.$this->formatCm($this->cm->Text).'&poli='.$this->DDKlinik->SelectedValue.'&dokter='.$this->DDDokter->SelectedValue.'&pas='.$this->getViewState('pas').'&pen='.$this->penttg->Text.'&notrans='.$notran.'"; 
				</script>';	
			}*/	
			
			$this->msg->Text = '    
				<script type="text/javascript">
					alert("Data pasien rawat jalan telah disimpan !");
					window.location="index.php?page=Tarif.cetakKartu&cm='.$this->formatCm($this->cm->Text).'&poli='.$this->DDKlinik->SelectedValue.'&dokter='.$this->DDDokter->SelectedValue.'&pas='.$this->getViewState('pas').'&pen='.$this->penttg->Text.'&notrans='.$notran.'&shift='.$this->shift->SelectedValue.'"; 
				</script>';
				
				//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKartu',array('cm'=>$this->formatCm($this->cm->Text),'poli'=>$this->DDKlinik->SelectedValue,'dokter'=>$this->DDDokter->SelectedValue,'pas'=>$this->getViewState('pas'),'pen'=>$this->penttg->Text,'notrans'=>$notran)));
				
				/*	
				if($this->getViewState('backTo')){
					$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));		
				}else{
					$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));
				}
				*/	
			}
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		
	}	
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('backTo')){
			$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarBaru'));		
		}else{
			$this->Response->redirect($this->Service->constructUrl('Simak'));
		}	
	}

	public function batalClicked()
	{		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtJln'));			
	}

}
?>
