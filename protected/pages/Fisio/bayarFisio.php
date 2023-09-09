<?php
class bayarFisio extends SimakConf 
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('11');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function lockPanel()
	{
		$this->inapPanel->Display = 'None';
		$this->dataPasienLuarPanel->Display = 'None';
		$this->dataPasienPanel->Display = 'None';
		$this->dataInapPanel->Display = 'None';
		$this->radPanel->Display = 'None';
		$this->gridPanel->Display = 'None';
		$this->konfPanel->Display = 'None';
		
		
		$this->inapPanel->Enabled = false;
		$this->dataPasienLuarPanel->Enabled = false;
		$this->dataPasienPanel->Enabled = false;
		$this->radPanel->Enabled = false;
	}
	
	public function ambilDataTdkFisio()
	{
		$sql = "SELECT 
				  tbm_fisio_tindakan.kode AS kode,
				  tbm_fisio_tindakan.nama AS nama
				FROM
				  tbm_fisio_tindakan
				  INNER JOIN tbm_fisio_tarif ON (tbm_fisio_tindakan.kode = tbm_fisio_tarif.id) ";
		
		if($this->getViewState('nmTable'))
		{
			$sql .= " WHERE tbm_fisio_tarif.tarif <> '0' ";
				
			$nmTable = $this->getViewState('nmTable');
			
			$sql2 = "SELECT id_tdk FROM $nmTable" ;
			$arr = $this->queryAction($sql2,'S');
			$i = 1;
			foreach($arr as $row)
			{
				$id_tdk = $row['id_tdk'];
				$sql .= " AND tbm_fisio_tindakan.kode <> '$id_tdk' ";
					
				$i++;
			}
			
			$sql .= " ORDER BY nama  ";
		}
		else
		{
			$sql .= " WHERE
				  tbm_fisio_tarif.tarif <> '0'
				ORDER BY nama  ";
		}	
				
		$this->DDTdkFisio->DataSource = $this->queryAction($sql,'S');
		$this->DDTdkFisio->dataBind();
	}
	 
	public function onPreRender($param)
	{				
		parent::onPreRender($param);		
			
		if(!$this->IsPostBack)
		{		
			$this->lockPanel();
				
			$this->notrans->Enabled=false;
						
			$this->DDKlinik->DataSource=PoliklinikRecord::finder()->findAll();
			$this->DDKlinik->dataBind();
			
			$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? ', '1');
			$this->DDDokter->dataBind();
			
			$this->dokter->Enabled=false;
			$this->dokter->Display='None';
					
			$this->ambilDataTdkFisio();
			$this->DDTdkFisio->Enabled=false;	
			
			$this->DDradRujuk->DataSource=FisioRujukanRecord::finder()->findAll();
			$this->DDradRujuk->dataBind();
			$this->DDradRujuk->Enabled=false;	
			
			$this->modeByrInap->Enabled=true;
			
			$this->simpanBtn->Enabled=false;
			
			$this->modeInput->SelectedValue = '0';
			$this->modeInputChanged();
		}
		else
		{
			if($this->getViewState('nmTable'))
			{
				$this->gridPanel->Display = 'Dynamic';	
				$this->simpanBtn->Enabled = true;
			}
			else
			{
				$this->gridPanel->Display = 'None';		
				$this->simpanBtn->Enabled = false;
			}	
		}
		
		
    }
	
	public function cmCallback($sender, $param)
	{
		$this->cariCmPanel->render($param->getNewWriter());
		$this->inapPanel->render($param->getNewWriter());
		$this->dataPasienLuarPanel->render($param->getNewWriter());
		$this->dataPasienPanel->render($param->getNewWriter());
		$this->radPanel->render($param->getNewWriter());
	}
	
	public function modeInputChanged()
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
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->notrans->getClientID().'").focus();');
		}
		elseif($jnsPasien == '1')//Pasien Rawat Inap
		{
			$this->notrans->Enabled = true;
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->notrans->getClientID().'").focus();');
		}
		else//Pasien Luar
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
			$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->nmPasLuar->getClientID().'").focus();');
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
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$tmp = $this->formatCm($this->notrans->Text);
		$this->DDFilm->SelectedValue = 'empty';
		$this->ambilDataTdkFisio();
		
		$this->jkel->Text = $this->cariJkel($cm);
		$this->Rbjkel->Text = $this->cariJkel2($tmp);
		
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
					  
			//if(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND tgl_visit=?',$this->formatCm($this->notrans->Text),'0','0',$dateNow))
			if(RwtjlnRecord::finder()->findBySql($sql))
			{	
				$this->cariCmPanel->Enabled = false;
				$this->dataPasienPanel->Enabled = true;
				$this->dataPasienPanel->Display = 'Dynamic';
				
				$this->radPanel->Enabled = true;
				$this->radPanel->Display = 'Dynamic';
				
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
							AND tbt_rawat_jalan.st_alih='0' ";
				$arrData=$this->queryAction($sql,'R');
				foreach($arrData as $row)
				{
					
					$this->nmPas->Text= $row['nama'];			
					$this->setViewState('cm',$row['cm']);
					$this->setViewState('nama',$row['nama']);							
					$this->setViewState('no_trans_rwtjln',$row['no_trans']);
					$this->setViewState('stAsuransi',$row['st_asuransi']);
					$this->setViewState('perusAsuransi',$row['perus_asuransi']);	
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
				}
				else
				{
					$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->DDKlinik->getClientID().'").focus();');
				}
				
				$this->nmPas->Enabled = false;
				$this->DDFilm->Enabled=false;
				$this->DDTdkFisio->Enabled=false;
			}
			elseif(RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=?',$this->formatCm($this->notrans->Text),'0','1'))			
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->notrans->Text).' sudah alih status ke Rawat Inap !");
					jQuery("#'.$this->notrans->getClientID().'").focus();
				</script>';
				$this->notrans->Text ='';
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$this->formatCm($this->notrans->Text).' Belum Masuk Ke Pendaftaran Rawat Jalan !");
					jQuery("#'.$this->notrans->getClientID().'").focus();
				</script>';
				$this->notrans->Text ='';
			}
			
		}elseif($jnsPasien=='1') //Pasien Rawat Inap
		{
			if(RwtInapRecord::finder()->find('cm = ? AND status = ?',$this->formatCm($this->notrans->Text),'0'))
			{
				$this->cariCmPanel->Enabled = false;
				$this->inapPanel->Enabled = true;
				$this->inapPanel->Display = 'Dynamic';
				
				$this->dataPasienPanel->Enabled = true;
				$this->dataPasienPanel->Display = 'Dynamic';
				
				$this->dataInapPanel->Display = 'Dynamic';
				
				$this->radPanel->Enabled = true;
				$this->radPanel->Display = 'Dynamic';
				
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
					$this->setViewState('kamar',$row['jenis_kamar']);
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
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					jQuery("#'.$this->modeByrInap->getClientID().'").focus();
				</script>';
				
				$umur = $this->cariUmur('1',$this->formatCm($this->notrans->Text),'');
				$this->umur->Text = $umur['years'];
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien dengan No.Rekam Medis '.$tmp.' Belum Masuk Ke Pendaftaran Rawat Inap !");
					jQuery("#'.$this->notrans->getClientID().'").focus();
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
			
				$this->radPanel->Enabled = true;
				$this->radPanel->Display = 'Dynamic';
			
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
			
			$this->radPanel->Enabled = true;
			$this->radPanel->Display = 'Dynamic';
			
			$this->DDKlinik->Enabled = false;
			$this->DDDokter->Enabled = false;
			$this->setViewState('stAsuransi','0');
		}
    }
	
	public function showDokter()
	{
		if(!$this->DDKlinik->SelectedValue=='')
		{
			$tmp = $this->formatCm($this->notrans->Text);
			$dateNow = date('Y-m-d');
			$idKlinik = $this->DDKlinik->SelectedValue;
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			
			/*if($this->cekTdkPenunjangList($tmp,$idKlinik,'4') == 'False')
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_forbidden">Transaksi Elektromedik untuk pasien dengan No. Rekam Medis <b>'.$tmp.'</b> di Poli '.$this->ambilTxt($this->DDKlinik).' belum dipilih!. <br/><br/> </p>\',timeout: 600000,dialog:{
						modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						konfirmasi(\'ya\');
					}
				}
			}});');	
			}
			else
			{*/
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
				$this->DDDokter->Enabled=true;
				
				$klinik=PoliklinikRecord::finder()->findByPk($this->DDKlinik->SelectedValue)->nama;		
				$this->setViewState('klinik',$klinik);
				$this->setViewState('id_klinik',$this->DDKlinik->SelectedValue);
				
				$kelPasien = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->penjamin;
				$stAsuransi = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->st_asuransi;
				$perusAsuransi = RwtjlnRecord::finder()->findByPk($this->getViewState('no_trans_rwtjln'))->perus_asuransi;
				$this->setViewState('kelPasien',$kelPasien);
				$this->setViewState('stAsuransi',$stAsuransi);
				$this->setViewState('perusAsuransi',$perusAsuransi);
				
				if(count($arr) == '1')
				{
					$idDokter = PegawaiRecord::finder()->findBySql($sql)->id;
					$this->DDDokter->SelectedValue = $idDokter;
					$this->DDDokterChanged();
				}
				
				$umur = $this->cariUmur('0',$tmp,$idKlinik);
				$this->umur->Text = $umur['years'];
				$this->getPage()->getClientScript()->registerEndScript('','jQuery("#'.$this->DDDokter->getClientID().'").focus();');
			}
		/*}
		else
		{
			//$this->batalClicked();
			$this->umur->Text = '';
		}*/
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->batalClicked();
		}
		else
		{
			$this->Page->CallbackClient->focus($this->cetakBtn);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
	}
	
	public function DDDokterChanged()
	{	
		$this->setViewState('dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->nama);
		$this->setViewState('id_dokter',PegawaiRecord::finder()->findByPk($this->DDDokter->SelectedValue)->id);		
		//$this->notrans->Enabled=true;
		//$this->Page->CallbackClient->focus($this->DDFilm);
	}
	
	public function jnsPelaksanaChanged($sender,$param)
	{
		$this->DDradRujuk->SelectedValue = 'empty';
		
		if($this->jnsPelaksana->SelectedValue == '0')//Fisio RS
		{
			$this->DDradRujuk->Enabled = false;		
			$this->Page->CallbackClient->focus($this->DDFilm);
			
			$this->DDFilm->Enabled = true;
			$this->DDTdkFisio->Enabled = true;	
		}
		else //Fisio Rujukan
		{
			$this->DDradRujuk->Enabled = true;
			$this->Page->CallbackClient->focus($this->DDradRujuk);
			
			$this->DDFilm->Enabled = false;
			$this->DDTdkFisio->Enabled = false;
		}
		
		
		$this->DDFilm->SelectedValue = 'empty';
		$this->DDTdkFisio->SelectedValue = 'empty';
	}
	
	public function DDradRujukChanged($sender,$param)
	{
		if($this->DDradRujuk->SelectedValue != '')
		{
			$this->DDFilm->Enabled = true;
			$this->DDTdkFisio->Enabled = true;	
			
			$this->DDFilm->SelectedValue = 'empty';
			$this->DDTdkFisio->SelectedValue = 'empty';
			
			$this->Page->CallbackClient->focus($this->DDFilm);
		}
	}
	
	public function DDFilmChanged($sender,$param)
	{
		$this->setViewState('film',$this->DDFilm->SelectedValue);
		
		if($this->DDFilm->SelectedValue != '')
		{
			$this->DDTdkFisio->Enabled=true;	
			$this->ambilDataTdkFisio();
			$this->Page->CallbackClient->focus($this->DDTdkFisio);
		}
		else
		{
			$this->ambilDataTdkFisio();
			$this->DDTdkFisio->Enabled=false;
		}
	}
	
	public function lapTrans()
    {	
		$this->mainPanel->Display = 'None';
		$this->konfPanel->Display = 'Dynamic';
		$this->konfTgl->Text = $this->convertDate(date('Y-m-d'),'3');
		$idDokter = $this->DDDokter->SelectedValue;
		$idKlinik = $this->DDKlinik->SelectedValue;
			
		$this->nmTdk->Text = $this->ambilTxt($this->DDTdkFisio);
		
		$cm = $this->formatCm($this->notrans->Text);
		$tgl = date('Y-m-d');
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien=='0')
		{			
			$nmTable = 'tbt_fisio_penjualan';
			$noTrans = RwtjlnRecord::finder()->find('cm=? AND id_klinik=? AND dokter=? AND flag=? AND tgl_visit=?',$cm,$idKlinik,$idDokter,'0',date('Y-m-d'))->no_trans;
				
			$this->konfNoCm->Text = $cm;
			$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$this->konfJnsPas->Text = "Pasien Rawat Jalan";
		}
		elseif($jnsPasien=='1')
		{			
			$nmTable = 'tbt_fisio_penjualan_inap';
			$noTrans = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->no_trans;
			
			$this->konfNoCm->Text = $cm;
			$this->konfNmPas->Text = PasienRecord::finder()->findByPk($cm)->nama;
			$this->konfJnsPas->Text = "Pasien Rawat Inap";
		}
		elseif($jnsPasien=='2' || $jnsPasien=='3')
		{			
			$nmTable = 'tbt_fisio_penjualan_lain';
			$this->konfNoCm->Text = '-';
			$this->konfNmPas->Text = $this->nmPas->Text;	
			$this->konfJnsPas->Text = "Pasien Rujukan";
		}
		
		if($jnsPasien!='2' || $jnsPasien!='3')
		{	
			$sql="SELECT 
				   $nmTable.id,
				   $nmTable.wkt,
				   tbm_fisio_tindakan.nama,
				   tbm_fisio_kelompok.nama AS kelompok,
				   tbm_fisio_kategori.jenis AS kategori  
				FROM
				  tbm_fisio_tindakan
				  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
				  INNER JOIN $nmTable ON (tbm_fisio_tindakan.kode = $nmTable.id_tindakan)
				  LEFT JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
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
				  tbm_fisio_tindakan.nama";
		}
		else
		{
			$nmPasRujuk = $this->nmPas->Text;
			$sql="SELECT 
				   $nmTable.id,
				   $nmTable.wkt,
				   tbm_fisio_tindakan.nama,
				   tbm_fisio_kelompok.nama AS kelompok,
				   tbm_fisio_kategori.jenis AS kategori  
				FROM
				  tbm_fisio_tindakan
				  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
				  INNER JOIN $nmTable ON (tbm_fisio_tindakan.kode = $nmTable.id_tindakan)
				  LEFT JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
				WHERE
				  $nmTable.cm LIKE '$nmPasRujuk%' 
				  AND $nmTable.tgl = '$tgl'
				ORDER BY
				  kelompok,
				  kategori,
				  tbm_fisio_tindakan.nama";
		}
			  
		$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
		$this->UserGrid2->DataSource=$arrData;
		$this->UserGrid2->dataBind();
	}
	
	public function tidakBtnClicked()
    {
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
		$this->DDTdkFisio->SelectedValue = 'empty';
	}
	
	public function yaBtnClicked()
    {
		$this->prosesClicked();
		$this->mainPanel->Visible = true;
		$this->konfPanel->Visible = false;
	}
	
	public function DDTdkFisioChanged()
    {
		if($this->DDTdkFisio->SelectedValue != '')
		{
			$cm = $this->formatCm($this->notrans->Text);
			$idTdk = $this->DDTdkFisio->SelectedValue;
			$idDokter = $this->DDDokter->SelectedValue;
			$idKlinik = $this->DDKlinik->SelectedValue;
			
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien=='0')
			{			
				$activeRec = FisioJualRecord::finder();
				$noTrans = RwtjlnRecord::finder()->find('cm=? AND id_klinik=? AND dokter=? AND flag=? AND tgl_visit=?',$cm,$idKlinik,$idDokter,'0',date('Y-m-d'))->no_trans;
			}
			elseif($jnsPasien=='1')
			{			
				$activeRec = FisioJualInapRecord::finder();
				$noTrans = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->no_trans;
			}
			elseif($jnsPasien=='2' || $jnsPasien=='3')
			{			
				$activeRec = FisioJualLainRecord::finder();
			}				
			
			if($jnsPasien!='2' || $jnsPasien!='3') //bukan pasien rujuk
			{	
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
							  tbt_fisio_penjualan_lain
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
					$this->DDTdkFisio->SelectedValue = 'empty';
				}
				*/
				$this->prosesClicked();
			}
			
		}		
	}
	
	public function ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_lab_rujukan)
    {	
		$persetaseKelas = 0;
		
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
						 
				$sql .= " FROM tbm_fisio_tarif a, 
							  tbm_fisio_tindakan b 
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
							 
					$sql .= " FROM tbm_fisio_tarif a, 
								  tbm_fisio_tindakan b 
							 WHERE a.id='$item' AND a.id=b.kode";
								 
				}
				elseif($kelas == '2' || $kelas == '3' || $kelas == '4' || $kelas == '5')//kelas I,II,IIIA,IIIB
				{
					$sql = "SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif AS tarif  ";
					else
						$sql .= " a.tarif2 AS tarif ";	
							 
					$sql .= " FROM tbm_fisio_tarif a, 
								  tbm_fisio_tindakan b 
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
						 
				$sql .= " FROM tbm_fisio_rujukan_tarif a, 
							  tbm_fisio_tindakan b, tbm_fisio_rujukan c
						 WHERE a.id_tdk_fisio='$item' AND a.id_tdk_fisio=b.kode AND a.id_fisio_rujukan=c.id AND a.id_fisio_rujukan='$id_fisio_rujukan' ";	
						 
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
							 
					$sql .= " FROM tbm_fisio_rujukan_tarif a, 
							  tbm_fisio_tindakan b, tbm_fisio_rujukan c
						 WHERE 
						 	a.id_tdk_fisio='$item' 
							AND a.id_tdk_fisio=b.kode 
							AND a.id_fisio_rujukan=c.id
							AND a.id_fisio_rujukan='$id_fisio_rujukan' ";
				}
				elseif($kelas == '2' || $kelas == '3' || $kelas == '4' || $kelas == '5')//kelas I,II,IIIA,IIIB
				{
					$sql="SELECT b.nama AS id, ";
					
					if($this->modeCito->SelectedValue == '0')
						$sql .= " a.tarif AS tarif  ";
					else
						$sql .= " a.tarif2 AS tarif ";	
							 
					$sql .= " FROM tbm_fisio_rujukan_tarif a, 
							  tbm_fisio_tindakan b, tbm_fisio_rujukan c
						 WHERE 
						 	a.id_tdk_fisio='$item' 
							AND a.id_tdk_fisio=b.kode 
							AND a.id_fisio_rujukan=c.id
							AND a.id_fisio_rujukan='$id_fisio_rujukan' ";
				}
			}
		}
		
		$tmpTarif = FisioTarifRecord::finder()->findBySql($sql);	
		return $tmpTarif;
	}
	
	public function cekAsuransi()
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$item=$this->DDTdkFisio->SelectedValue;
		$st_rujukan=$this->jnsPelaksana->SelectedValue;
		$id_fisio_rujukan=$this->DDradRujuk->SelectedValue;
		
		//if($this->getViewState('stAsuransi') == '0')//pasien umum
		//{
			$tmpTarif = $this->ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_fisio_rujukan);
		/*}
		elseif($this->getViewState('stAsuransi') == '1')//pasien penjamin
		{
			$idPerus = $this->getViewState('perusAsuransi');
			
			//cek tarif paket di tbm_fisio_tindakan_paket_asuransi_detail
			$sql = "SELECT id,id_paket,tarif FROM tbm_fisio_tindakan_paket_asuransi_detail WHERE id_paket > '0' AND id_asuransi = '$idPerus' AND id_tindakan = '$item' AND st_rawat = '$jnsPasien' ";
			if($this->queryAction($sql,'S'))
			{
				$tmpDetailPaket = FisioTdkPaketAsuransiDetailRecord::finder()->findBySql($sql);
				$idPaket = $tmpDetailPaket->id_paket;
				$this->setViewState('idPaketTdkAsuransi',$tmpDetailPaket->id_paket);
				
				$sql = "SELECT id,tarif FROM tbm_fisio_tindakan_paket_asuransi WHERE id='$idPaket' AND id_asuransi = '$idPerus' AND st_rawat = '$jnsPasien' ";
				$tmpTarif = FisioTdkPaketAsuransiRecord::finder()->findBySql($sql);
			}
			else
			{
				//cek tarif non paket di tbm_fisio_tindakan_paket_asuransi_detail
				$sql = "SELECT tarif FROM tbm_fisio_tindakan_paket_asuransi_detail WHERE id_paket = '0' AND id_asuransi = '$idPerus' AND id_tindakan = '$item' AND st_rawat = '$jnsPasien' ";
				if($this->queryAction($sql,'S'))
				{
					$tmpTarif = FisioTdkPaketAsuransiDetailRecord::finder()->findBySql($sql);
				}
				else
				{
					//ambil tarif RS
					$tmpTarif = $this->ambilTarifRs($jnsPasien,$item,$st_rujukan,$id_fisio_rujukan);
				}
			}
		}*/
		
		return $tmpTarif;
	}
	
	public function prosesClicked()
    {		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		$item=$this->DDTdkFisio->SelectedValue;
		$st_rujukan=$this->jnsPelaksana->SelectedValue;
		$id_fisio_rujukan=$this->DDradRujuk->SelectedValue;
		
		$film=$this->getViewState('film');
		switch ($film)
		{
			case '0':
				$nmFilm='18 x 24';
				break;
			case '1':
				$nmFilm='24 x 30';
				break;
			case '2':
				$nmFilm='30 x 40';
				break;
			case '3':
				$nmFilm='35 x 35';
				break;
		}
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment,
					 id_film VARCHAR(1) NOT NULL,
					 nm_film VARCHAR(8) NOT NULL,
					 nama VARCHAR(100) NOT NULL,
					 id_tdk VARCHAR(4) NOT NULL,									 
					 total INT(11) NOT NULL,	
					 st_rujukan char(1) DEFAULT '0',
					 id_fisio_rujukan char(2) DEFAULT NULL,	
					 st_tanggungan char(1) DEFAULT '0',
					 id_paket_tdk_asuransi char(1) DEFAULT '0',
					 PRIMARY KEY (id)) ENGINE = MEMORY";
			
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			if($jnsPasien=='2')
			{			
				$jml2=5000;
				$sql="INSERT INTO $nmTable (nama,total,id_tdk,st_rujukan,id_fisio_rujukan,st_tanggungan) VALUES ('PENDAFTARAN',$jml2,'PDT','0','','0')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}				
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');								
		}	
		
		
		
		//$this->showSql->Text = $sql;
		$tmpTarif = $this->cekAsuransi();	
		$total_asli= $tmpTarif->tarif;
		//$total_asli = $tmpTarif->tarif + ($tmpTarif->tarif * $persetaseKelas) ;
		//$nama=$tmpTarif->id;	
		/*
		//---------------- JIKA TIDAK DIRUJUK KE LAB LUAR -----------------------
		if($st_rujukan == '0') 
		{
			//jika ada tindakan yg dicover asuransi => pakai harga tesebut
			if($this->getViewState('kelPasien') == '02' && $this->getViewState('stAsuransi') == '1')
			{
				if($jnsPasien=='0')//rawat jalan
				{	
					$perusAsuransi = $this->getViewState('perusAsuransi');
					$sql = "SELECT * FROM tbm_provider_detail_fisio WHERE id_provider = '$perusAsuransi' AND id_tindakan = '$item' ";
					$arr = $this->queryAction($sql,'S');
					if(count($arr) > 0)
					{
						$tmpTarif = ProviderDetailFisioRecord::finder()->findBySql($sql);	
						$total_asli= $tmpTarif->tarif;
						$st_tanggungan = '1';
					}	 
				}
			}
		}
		*/
		if($this->getViewState('stAsuransi') == '1')
			$total= $total_asli;	
		else
			$total=$this->bulatkan($total_asli);
		
		$nama=$this->ambilTxt($this->DDTdkFisio);
		$jml=$total+$jml1+$jml2;
		
		if($this->getViewState('idPaketTdkAsuransi'))
		{
			$idPaketTdkAsuransi = $this->getViewState('idPaketTdkAsuransi');
			$sql="SELECT id_paket_tdk_asuransi FROM $nmTable WHERE id_paket_tdk_asuransi = '$idPaketTdkAsuransi' ";
			if($this->queryAction($sql,'S'))
			{
				$total = 0;
				$jml = 0;
			}
		}
		
		$sql="INSERT INTO $nmTable (id_film,nm_film,nama,total,id_tdk,st_rujukan,id_fisio_rujukan,st_tanggungan,id_paket_tdk_asuransi) VALUES ('$film','$nmFilm','$nama','$total','$item','$st_rujukan','$id_fisio_rujukan','$st_tanggungan','$idPaketTdkAsuransi')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();
		
		//---------- Tabel Temp u/ tbt_fisio_jual_sisa --------
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
		
		$this->DDFilm->SelectedValue = 'empty';
		$this->Page->CallbackClient->focus($this->DDFilm);
		
		$this->ambilDataTdkFisio();
		$this->DDTdkFisio->SelectedValue = 'empty';
		
		$this->clearViewState('idPaketTdkAsuransi');
	}
	
	 public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql="SELECT id_paket_tdk_asuransi,total FROM $nmTable WHERE id='$ID' AND id_paket_tdk_asuransi > 0 AND total > 0";
				$arrData = $this->queryAction($sql,'R');//Select row in tabel bro...
				if(count($arrData) > 0)
				{
					foreach($arrData as $row)
					{
						$id_paket_tdk_asuransi = $row['id_paket_tdk_asuransi'];
						$total_paket_tdk_asuransi = $row['total'];
					}
					
					$sql="UPDATE $nmTable SET total = '$total_paket_tdk_asuransi' WHERE id<>'$ID' AND id_paket_tdk_asuransi = '$id_paket_tdk_asuransi' AND total = 0 LIMIT 1";
					$this->queryAction($sql,'C');
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
				
				if($jmlData==0)
				{
					$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
					$this->UserGrid->DataSource='';
					$this->UserGrid->dataBind();
					$this->clearViewState('nmTable');//Clear the view state	
					$this->clearViewState('tmpJml');//Clear the view state	
					$this->jmlShow->Text='Rp '.number_format(0,2,',','.');
					
					if($this->getViewState('sisaTmpTable'))
					{
						$this->queryAction($this->getViewState('sisaTmpTable'),'D');//Droped the table	
						$this->clearViewState('sisaTmpTable');//Clear the view state			
					}
					
					$this->Page->CallbackClient->focus($this->DDFilm);
					
					$this->simpanBtn->Enabled = false;
				}
				else
				{
					//$sql="SELECT total FROM $nmTable WHERE id='$ID'";
					$sql="SELECT total FROM $nmTable";
					$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...				
					foreach($arrData as $row)
					{
						//$n=$row['total'];
						//$t = ($this->getViewState('tmpJml') - $n);						
						$t += $row['total'];
						//$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
						//$this->setViewState('tmpJml',$t);
					}
					
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);	
				}
			//}
		//}	
		
		$this->ambilDataTdkFisio();
    }	
		
	
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
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
			$this->clearViewState('sisaTmpTable');//Clear the view state			
		}
					
		$this->UserGrid->DataSource='';
		$this->UserGrid->dataBind();
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('sisa');			
		$this->clearViewState('nmTable');
		$this->clearViewState('cm');
		$this->clearViewState('notrans');
		$this->clearViewState('nama');
		$this->clearViewState('dokter');
		$this->clearViewState('klinik');
		$this->clearViewState('film');
	}
	
	public function cetakClicked($sender,$param)
    {	
		if($this->IsValid)  // when all validations succeed
        {
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			$byrInap=$this->getViewState('modeByrInap');
			
			$jmlTagihan=$this->getViewState('tmpJml');
			$table=$this->getViewState('nmTable');
			$cm=$this->getViewState('cm');
			$id_dokter=$this->getViewState('id_dokter');
			$klinik=$this->getViewState('id_klinik');
			$notransinap=$this->getViewState('notransinap');
			$operator=$this->User->IsUserNip;
			$nipTmp=$this->User->IsUserNip;
			
			$dateNow = date('Y-m-d');		
			
			$sql="SELECT * FROM $table ORDER BY id";
			$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...		
			
			if($jnsPasien == '0')
			{
				
				$sql = "SELECT 
						no_trans 
					FROM 
						tbt_rawat_jalan 
					WHERE 
						cm = '$cm'
						AND dokter = '$id_dokter'
						AND id_klinik = '$klinik' 
						AND flag = 0
						AND st_alih = 0
						AND TIME_TO_SEC(TIMEDIFF(CONCAT_WS(' ', CURDATE(), CURTIME()), CONCAT_WS(' ', tgl_visit, wkt_visit))) / 3600 <= 24 ";
				$noRwtJln=RwtjlnRecord::finder()->findBySql($sql)->no_trans;
				$noReg = $this->numRegister('tbt_fisio_penjualan',FisioJualRecord::finder(),'07');
				
				foreach($arrData as $row)
				{
					$notransTmp = $this->numCounter('tbt_fisio_penjualan',FisioJualRecord::finder(),'07');
					
					$transRwtJln= new FisioJualRecord();
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->no_trans_rwtjln=$noRwtJln;
					$transRwtJln->no_reg=$noReg;
					$transRwtJln->cm=$cm;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					//$transRwtJln->film_size=$row['id_film'];
					$transRwtJln->operator=$operator;
					$transRwtJln->klinik=$klinik;
					$transRwtJln->dokter=$id_dokter;
					/*
					if($row['st_tanggungan'] == '1')
					{
						$transRwtJln->harga = 0;
						$transRwtJln->tanggungan_asuransi = $row['total'];
					}
					else
					{
						$transRwtJln->harga = $row['total'];
						$transRwtJln->tanggungan_asuransi = 0;
					}
					*/
					if($this->getViewState('stAsuransi') == '1')//pasien penjamin
						$transRwtJln->tanggungan_asuransi=$row['total'];
					else
						$transRwtJln->harga=$row['total'];
						
					$transRwtJln->flag='0';
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_fisio_rujukan=$row['id_fisio_rujukan'];
					$transRwtJln->Save();
						
				}
			}elseif($jnsPasien == '1')
			{
				$noReg = $this->numRegister('tbt_fisio_penjualan_inap',FisioJualInapRecord::finder(),'12');
				
				foreach($arrData as $row)
				{
					$notransTmp1 = $this->numCounter('tbt_fisio_penjualan_inap',FisioJualInapRecord::finder(),'12');
					
					$transRwtJln= new FisioJualInapRecord();
					$transRwtJln->no_trans=$notransTmp1;
					$transRwtJln->no_trans_inap=$notransinap;
					$transRwtJln->no_reg=$noReg;
					$transRwtJln->cm=$cm;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					//$transRwtJln->film_size=$row['id_film'];
					$transRwtJln->operator=$operator;
					//$transRwtJln->klinik=$klinik;
					$transRwtJln->dokter=$id_dokter;
					$transRwtJln->harga=$row['total'];
					$transRwtJln->flag='0';
					
					if($byrInap=='0')
					{					
						$transRwtJln->st_bayar='0';
					}else{				
						$transRwtJln->st_bayar='1';
					}				
					
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_fisio_rujukan=$row['id_fisio_rujukan'];
					
					$transRwtJln->Save();		
				}
			}
			else
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
				
				$noReg = $this->numRegister('tbt_fisio_penjualan_lain',FisioJualLainRecord::finder(),'15');
				
				//INSERT tbt_fisio_penjualan_lain
				foreach($arrData as $row)
				{
					$notransTmp = $this->numCounter('tbt_fisio_penjualan_lain',FisioJualLainRecord::finder(),'15');
					
					$transRwtJln= new FisioJualLainRecord();
	
					$transRwtJln->no_trans=$notransTmp;
					$transRwtJln->no_trans_pas_luar = $notransPas;
					$transRwtJln->no_reg = $noReg;
					$transRwtJln->nama = $this->nmPasLuar->Text;
					$transRwtJln->id_tindakan=$row['id_tdk'];
					$transRwtJln->tgl=date('y-m-d');
					$transRwtJln->wkt=date('G:i:s');
					//$transRwtJln->film_size=$row['id_film'];
					$transRwtJln->operator=$operator;
					
					if($this->getViewState('stAsuransi') == '1')//pasien penjamin
					$transRwtJln->tanggungan_asuransi=$row['total'];
				else
					$transRwtJln->harga=$row['total'];
					
					$transRwtJln->harga_non_adm='0';
					$transRwtJln->harga_adm='0';
					$transRwtJln->flag='0';
					$transRwtJln->st_rujukan=$row['st_rujukan'];
					$transRwtJln->id_fisio_rujukan=$row['id_fisio_rujukan'];
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
						$ObatJualSisa= new FisioJualSisaRecord();
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
				alert("Data Rekam Billing Elektromedik Telah Masuk Dalam Database.");
				window.location="index.php?page=Fisio.bayarFisio"; 
			</script>';
			
			//$this->Response->redirect($this->Service->constructUrl('Fisio.bayarFisioSukses'));
			//$this->MultiView->ActiveViewIndex='1';
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent(); ');	
		}	
	}
	
	public function batalClicked()
    {	
		$this->clearState();
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->clearState();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
		
	public function viewChanged($sender,$param)
	{
		if($this->MultiView->ActiveViewIndex===1)
        {
            $this->kembaliBtn->Focus();
        }
	}
	public function kembaliClicked()
	{	
		$this->clearState();
		$this->Response->Reload();
	}	
}
?>
