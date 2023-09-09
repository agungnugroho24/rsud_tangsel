<?php
/**
 * DaftarRwtInap class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: DaftarRwtInap.php 1001 2008-04-28 16:07:03Z xue $
 */

/**
 * DaftarRwtInap class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
class DaftarRwtInap extends SimakConf
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
		
		if(!$this->isPostBack || !$this->isCallBack )	 	            		
		{
			$cm = $this->formatCm($this->Request['cm']);
			
			if($cm)
			{
				$this->cm->Text = intval($cm) ;
				$this->checkRM();
				$this->nmPasien->focus();
			}
		}
	}
	 
	public function onLoad($param)
		{				
			parent::onLoad($param);		
			
			if(!$this->isPostBack || !$this->isCallBack )	 	            		
			{		
				$this->cm->focus();
				$this->wktMsk->Text=date("G:i:s");
				$this->tglMsk->Text=date("d-m-Y"); 
				
				$this->tglMsk->Enabled = true;
				$this->wktMsk->Enabled = true;
				$this->panel->Display = 'None';
				
				$this->crMskLuar->Enabled=false;
				$this->DDCaraMsk->Enabled = false;
				$this->DDbidanPerujuk->Enabled = false;
				
				$this->simpanBtn->Enabled = false;
				
				$this->DDKelas->DataSource=KelasKamarRecord::finder()->findAll();
				$this->DDKelas->dataBind();
				$this->DDJenKmr->DataSource=KamarNamaRecord::finder()->findAll();
				$this->DDJenKmr->dataBind();
				$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
				$this->DDKamar->dataBind();
				$this->DDKamar->Enabled = false;
							
				$this->DDKrjPen->DataSource=PekerjaanRecord::finder()->findAll();
				$this->DDKrjPen->dataBind();  
				
				$sql = "SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama ";
				$this->DDDokter->DataSource=PegawaiRecord::finder()->findAllBySql($sql);//kelompok pegawai '1' adalah untuk dokter
				$this->DDDokter->dataBind();
				
				$this->DDCaraMsk->DataSource=caramasukRecord::finder()->findAll();
				$this->DDCaraMsk->dataBind();
				
				$this->DDbidanPerujuk->DataSource=BidanPerujukRecord::finder()->findAll();
				$this->DDbidanPerujuk->dataBind();
				
				$this->DDKelompok->DataSource=KelompokRecord::finder()->findAll($criteria);
				$this->DDKelompok->dataBind();
				
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE st='1'";
				$this->DDPerusAsuransi->DataSource=PerusahaanRecord::finder()->findAllBySql($sql);
				$this->DDPerusAsuransi->dataBind();
				
				
				//$this->tipeRujukanCtrl->Visible = false;
				//$this->modeInputCtrl->Visible = false;
				$this->noRegCtrl->Visible = false;
				$this->noRegCtrl->Enabled = false;
				$this->hubPasien->Enabled = false;
				/*	
				$this->crMskLuar->Enabled=false;	
				
				$this->RBvalAsuransi->Enabled = false ;
				$this->DDKelompok->Enabled = false ;
				$this->DDPerusAsuransi->Enabled = false ;
				$this->DDKelas->Enabled = false;
				$this->DDJenKmr->Enabled = false;
				$this->DDKamar->Enabled = false;
				$this->DDKrjPen->Enabled = false;
				$this->DDDokter->Enabled = false;
				$this->DDDokter->Enabled = false;
				$this->DDCaraMsk->Enabled = false;
				$this->nmPen->Enabled = false;
				$this->almPen->Enabled = false;
				$this->DDHubPen->Enabled = false;
				$this->keluhan->Enabled = false;
				$this->catatan->Enabled = false;
				
				$this->dp->Enabled = false;
				*/
			}				
		}
	
	public function DDKelasChanged($sender,$param)
   	{
		$idKelas = $this->DDKelas->SelectedValue;
		$idJnsKamar = $this->DDJenKmr->SelectedValue;
		$this->jmlBed->Text = '';
		$this->jmlBedPakai->Text = '';
			
		if($idKelas != '' && $idJnsKamar != '')
		{
			//$this->DDJenKmr->DataSource=KamarNamaRecord::finder()->findAll();
			//$this->DDJenKmr->dataBind();	
			$this->DDKamar->Enabled = true;
			
			if($idJnsKamar == '1') //kamar pasien
			{
				$sql = "SELECT id,nama FROM tbm_ruang WHERE id_kelas = '$idKelas' AND id_jns_kamar = '$idJnsKamar' AND jml_bed > 0 ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->DDKamar->DataSource = $arr;
					$this->DDKamar->dataBind();	
				}
				else
				{
					$this->msg->Text = '    
					<script type="text/javascript">
						alert("Maaf, tidak ada bed yang tersedia untuk kelas dan kamar yang dipilih !");
					</script>';
					
					$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
					$this->DDKamar->dataBind();
					$this->DDKamar->Enabled = false;
				}
				
			}
			else
			{
				$sql = "SELECT id,nama FROM tbm_ruang WHERE id_jns_kamar = '$idJnsKamar' AND jml_bed > 0";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					$this->DDKamar->DataSource = $arr;
					$this->DDKamar->dataBind();	
				}
				else
				{
					$this->msg->Text = '    
					<script type="text/javascript">
						alert("Maaf, tidak ada bed yang tersedia untuk kelas dan kamar yang dipilih !");
					</script>';
					
					$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
					$this->DDKamar->dataBind();
					$this->DDKamar->Enabled = false;
				}	
			}
		}
		else
		{	
			$this->DDKamar->DataSource=RuangRecord::finder()->findAll();
			$this->DDKamar->dataBind();
			$this->DDKamar->Enabled = false;
		}
	}
	
	public function DDKamarChanged($sender,$param)
   	{
		$idKamar = $this->DDKamar->SelectedValue;
		
		if($idKamar != '')
		{
			$this->jmlBed->Text = RuangRecord::finder()->findByPk($idKamar)->jml_bed;
			
			if(RuangRecord::finder()->findByPk($idKamar)->jml_bed_pakai != '')
			{
				$this->jmlBedPakai->Text = RuangRecord::finder()->findByPk($idKamar)->jml_bed_pakai;
				$this->setViewState('jmlBedPakai',$this->jmlBedPakai->Text);
			}
			else
			{
				$this->jmlBedPakai->Text = '0';
				$this->setViewState('jmlBedPakai','0');
			}
			
			$this->jmlBedTotal->Text = $this->jmlBed->Text + $this->jmlBedPakai->Text;
		}
		else
		{	
			$this->jmlBed->Text = '';
			$this->jmlBedPakai->Text = '';
			$this->jmlBedTotal->Text = '';
			$this->clearViewState('jmlBedPakai');
		}
	}
	
	public function cmCallBack($sender,$param)
   	{
		$this->cariCmPanel->render($param->getNewWriter());
		$this->panel->render($param->getNewWriter());
	}
	
	public function panelCallBack($sender,$param)
   	{
		$this->panel->render($param->getNewWriter());
	}
	
	public function modeRujukanChanged($sender,$param)
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
			if($this->DDCaraMsk->SelectedValue == '5')//rujukan luar dari bidan
			{	
				$this->DDbidanPerujuk->DataSource=BidanPerujukRecord::finder()->findAll();
				$this->DDbidanPerujuk->dataBind(); 
				
				//$this->crMskLuar->Visible = false;
				//$this->DDbidanPerujuk->Visible = true;
				
				$this->DDbidanPerujuk->Enabled = true;	
				$this->crMskLuar->Enabled=false;
				
				$this->Page->CallbackClient->focus($this->DDbidanPerujuk);
			}
			elseif($this->DDCaraMsk->SelectedValue == '7') //rujukan dalam dari dokter dalam
			{
				$sql="SELECT id,nama FROM  tbd_pegawai WHERE kelompok = '1' ORDER BY nama";
				$arr = $this->queryAction($sql,'S');
			
				$this->DDbidanPerujuk->DataSource = $arr;
				$this->DDbidanPerujuk->dataBind();
				
				//$this->crMskLuar->Visible = false;
				//$this->DDbidanPerujuk->Visible = true;
				
				$this->DDbidanPerujuk->Enabled = true;	
				$this->crMskLuar->Enabled=false;
				
				$this->Page->CallbackClient->focus($this->DDbidanPerujuk);
			}
			else //rujukan luar selain bidan
			{		
				//$this->crMskLuar->Visible = true;
				//$this->DDbidanPerujuk->Visible = false;
				$this->DDbidanPerujuk->SelectedValue = 'empty';
				$this->DDbidanPerujuk->Enabled = false;	
				$this->crMskLuar->Enabled=true;
				
				$this->Page->CallbackClient->focus($this->crMskLuar);	
			}
			
		}
		
		else
		{
			//$this->crMskLuar->Visible = true;
			//$this->DDbidanPerujuk->Visible = false;
				
			$this->crMskLuar->Enabled=false;
			$this->DDbidanPerujuk->SelectedValue = 'empty';	
			$this->DDbidanPerujuk->Enabled = false;	
		}
		
		$this->crMskLuar->Text = '';	
		
	}	
	
	public function modeDaftarChanged()
    {	
		$cm = $this->formatCm($this->cm->Text) ;
		$jnsDaftar = $this->collectSelectionResult($this->modeDaftar);		
		$this->errMsgCm->Text='';
		
		if($cm!='')
		{		
			if($jnsDaftar == '0') //Daftar Normal
			{			
				//$this->modeInputCtrl->Visible=true;
				$this->noRegCtrl->Visible=false;
				$this->noRegCtrl->Enabled = false;
				$this->DDKelas->focus();					
				//$this->modeInput->SelectedIndex = -1;	
				$this->errMsg->Text='';
				$this->simpanBtn->Enabled = true;
			}	
			elseif($jnsDaftar == '1') //Daftar Alih Status
			{
				////$this->modeInputCtrl->Visible=false;
				
				$sql="SELECT 
						no_trans, id_klinik
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND flag='0'";
				
				if($this->queryAction($sql,'S')) //jika masih ada tagihan untuk transaksi semasa di rawat jalan
				{
					$arr=$this->queryAction($sql,'S');
				
					foreach($arr as $row)
					{
						$id = $row['no_trans'];	
						$idKlinik = $row['id_klinik'];	
						$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;	
						
						$data[]=array('id'=>$id,'nama'=>$id.' - '.$nmKlinik);
					}		
					
					$this->noRegCtrl->Visible=true;
					$this->noRegCtrl->Enabled = true;
					$this->DDtrans->focus();	
					$this->DDtrans->DataSource=$data;
					$this->DDtrans->dataBind();
					
					$this->errMsgCm->Text='';
					$this->simpanBtn->Enabled = true;
				}
				else
				{
					$this->errMsgCm->Text = '    
					<script type="text/javascript">
						alert("Transaksi Rawat Jalan/IGD untuk pasien dengan No. Rekam Medis '.$cm.' sudah terselesaikan, \atau tidak ada transaksi rawat jalan untuk pasien dengan No. Rekam Medis '.$cm.' .");
						document.all.'.$this->nmPasien->getClientID().'.focus();
					</script>';
					
					$this->modeDaftar->SelectedValue = '0';
					//$this->errMsgCm->Text='Semua Transaksi Rawat Jalan/IGD sudah terselesaikan. Silakan pilih mode daftar normal';
					//$this->simpanBtn->Enabled = false;	
				}
			}
		}
		else
		{
			$this->cm->focus();
			$this->errMsgCm->Text='Isi No. Rekam Medis Terlebih dahulu';
		}
	}
	
	public function checkRM()
    {        
		// valid if the username is not found in the database
		$cm = $this->formatCm($this->cm->Text) ;
		$sql = "SELECT cm FROM tbt_rawat_inap WHERE status = 0	AND cm='$cm'";		
		$tmp = $this->queryAction($sql,'S');
					
		if(RwtInapRecord::finder()->findAll('cm = ? AND status=0',$cm)) //pasien sudah terdaftar di rawat inap
		{
			//$param->IsValid=false;	
			$this->modeDaftar->Enabled = false;
			$this->DDtrans->Enabled = false;
			$this->simpanBtn->Enabled = false;
			
			$this->errMsgCm->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$cm.' \masih terdaftar sebagai pasien rawat inap ! ");
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
				
			$this->cm->Text='';			
		}
		else //pasien belum terdaftar di rawat inap
		{
			//$param->IsValid=PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text));
			
			if(PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text))) //no RM ditemukan 
			{
				$kelPasien = PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text))->kelompok;
				if($kelPasien != '01' && $kelPasien != '') //bukan kelompok pasien UMUM
				{
					if($kelPasien == '02' || $kelPasien == '07') //kelompok pasien ASURANSI / jamper
					{
						if($kelPasien == '02') //kelompok pasien ASURANSI
						{
							$this->valAsuransi->Text = 'Asuransi';
						}
						elseif($kelPasien == '07') //jamper
						{
							$this->valAsuransi->Text = 'Jamper';
						}
			
						$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$kelPasien' ORDER BY nama";
						$data = $this->queryAction($sql,'S');
						
						$this->DDPerusAsuransi->DataSource = $data;
						$this->DDPerusAsuransi->dataBind();
						
						$this->DDPerusAsuransi->SelectedValue = PasienRecord::finder()->findByPk($cm)->perusahaan;
						$this->DDPerusAsuransi->Enabled = true;
						
						$this->setViewState('perusAsuransi',PasienRecord::finder()->findByPk($cm)->perusahaan);
					}
					elseif($kelPasien == '03') //kelompok pasien ASKES
					{
						$this->valAsuransi->Text = 'Askes';
					}
					elseif($kelPasien == '04') //kelompok pasien KARYAWAN 
					{
						$this->valAsuransi->Text = 'Karyawan';
					}
					elseif($kelPasien == '05') //kelompok pasien KELUARGA INTI
					{
						$this->valAsuransi->Text = 'Keluarga Inti';
					}
					elseif($kelPasien == '06') //kelompok pasien KELUARGA LAIN
					{
						$this->valAsuransi->Text = 'Keluarga Lain';
					}
					
					//$this->RBvalAsuransi->Enabled = true;
					$this->setViewState('kelPasien',$kelPasien);
					$this->RBvalAsuransi->SelectedValue = '1';
					$this->DDKelompok->Enabled = true;
				}
				elseif($kelPasien == '01') //kelompok pasien UMUM
				{ 
					//$this->RBvalAsuransi->Enabled = false;
					$this->valAsuransi->Text = 'Asuransi';
					$this->RBvalAsuransi->SelectedValue = '0';
					$this->DDKelompok->Enabled = false;
					$this->DDPerusAsuransi->Enabled = false;
				}
				else
				{
					$this->RBvalAsuransi->SelectedValue = '';
					$this->DDKelompok->Enabled = false;
					$this->DDPerusAsuransi->Enabled = false;
				}
				
				$hubPasien = PasienRecord::finder()->findByPk($cm)->hubungan_pj;
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
				
				$this->cariCmPanel->Enabled = false;
				$this->panel->Display = 'Dynamic';
				
				$this->nmPasien->Text=PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text))->nama;	
				
				$this->DDKelompok->SelectedValue = PasienRecord::finder()->findByPk($cm)->kelompok;
				$this->setViewState('kelompokPas',$this->DDKelompok->SelectedValue);
				
				/*
				$this->nmPen->Text = PasienRecord::finder()->findByPk($cm)->nm_pj;
				$this->almPen->Text = PasienRecord::finder()->findByPk($cm)->alamat_pj;
				$this->tlpPj->Text = PasienRecord::finder()->findByPk($cm)->telp_pj;
				$this->hpPj->Text = PasienRecord::finder()->findByPk($cm)->hp_pj;
				*/
				
				$this->nmPen->Text = PasienRecord::finder()->findByPk($cm)->nama;
				$this->DDKrjPen->SelectedValue = PasienRecord::finder()->findByPk($cm)->pekerjaan;
				$this->almPen->Text = PasienRecord::finder()->findByPk($cm)->alamat;
				$this->tlpPj->Text = PasienRecord::finder()->findByPk($cm)->telp;
				$this->hpPj->Text = PasienRecord::finder()->findByPk($cm)->hp;
				
				$this->errMsgCm->Text = '    
				<script type="text/javascript">
					document.all.'.$this->nmPasien->getClientID().'.focus();
				</script>';
				
				$this->errMsgCm->Text='';
				
				$this->modeDaftar->Enabled = true;
				$this->DDtrans->Enabled = true;
				$this->simpanBtn->Enabled = true;
			}
			else //no RM tidak ditemukan 
			{
				$this->errMsgCm->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$cm.' \ tidak ditemukan dalam database pasien ! ");
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
				//$this->errMsgCm->Text='Maaf, No. RM tersebut tidak ada!';
				
				$this->cm->Text='';	
				$this->nmPasien->Text='';	
				$this->modeDaftar->Enabled = false;
				$this->DDtrans->Enabled = false;
				$this->simpanBtn->Enabled = false;	
			}	
			
		}       
		
		$this->modeDaftar->SelectedIndex = -1;
		//$this->modeInput->SelectedIndex = -1;	
    }	
	
	public function checkRegister($sender,$param)
    {
		$this->errMsg->Text='';
		//$asalPasien = substr($this->notrans->Text,6,2);
		$noTrans = $this->DDtrans->SelectedValue;
		
		if($this->DDtrans->SelectedValue!='')
		{
			$cekPasien =RwtjlnRecord::finder()->find('no_trans = ? AND flag = ? AND st_alih = ?',$noTrans,'0','0');
			if($cekPasien == NULL)
			{
				$this->errMsgCm->Text = '    
				<script type="text/javascript">
					alert("No. Register Rawat Jalan tidak ada ! ");
					document.all.'.$this->DDtrans->getClientID().'.focus();
				</script>';
				
				$this->DDtrans->SelectedValue = 'empty';
				$this->errMsg->Text='';
				$this->DDtrans->Focus();
				$this->simpanBtn->Enabled = false;	
			}
			else
			{
				$this->cm->focus();	
				$this->simpanBtn->Enabled = true;		
				$dok=RwtjlnRecord::finder()->find('no_trans=?',$noTrans)->dokter;
				$this->showSql->text=$dok;
				$this->DDDokter->SelectedValue = $dok;	
			}
		}
		else
		{
			$this->errMsg->Text='';
			$this->simpanBtn->Enabled = false;		
		}
		/*
		if($asalPasien  == '01' || $asalPasien  == '03') //Peralihan dari pasien rawat jalan
		{
			if($asalPasien  == '01' ) //Peralihan dari pasien rawat jalan
			{
				$activeRec = RwtjlnRecord::finder();
				$tbtObatTbl = 'tbt_rawat_jalan';
			}
			elseif($asalPasien  == '03') //Peralihan dari pasien IGD
			{
				$activeRec = IgdRecord::finder();
				$tbtObatTbl = 'tbt_igd';	
			}
						
			$cekPasien = $activeRec->find('no_trans = ? AND flag = ? AND st_alih = ?',$this->notrans->Text,'0','0');
			if($cekPasien == NULL)
			{
				$this->errMsg->Text='No. Register tidak ada !';
				$this->notrans->Focus();
				$this->simpanBtn->Enabled = false;	
			}
			else
			{
				$this->cm->focus();		
				$this->simpanBtn->Enabled = true;		
			}
		}		
		else
		{
			$this->errMsg->Text='No. Register tidak ada !';
			$this->notrans->Focus();
			$this->simpanBtn->Enabled = false;	
		}
		*/
		$this->DDKelas->focus();
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
		}
		else //Penjamin berlaku
		{
			//$this->DDKelompok->SelectedValue = $this->getViewState('kelompokPas');
			$this->DDKelompok->Enabled = true;
			$this->DDKelompok->SelectedValue = 'empty';		
			$this->DDPerusAsuransi->SelectedValue = 'empty';				
			
			if($this->getViewState('kelPasien'))
			{
				$this->DDKelompok->SelectedValue = $this->getViewState('kelPasien');
			}
			
			$this->selectionChangedKelompok();
			
		}
	}
	
	public function selectionChangedKelompok()
	{
		if($this->DDKelompok->SelectedValue == '01' ) //Penjamin Umum
		{
			$this->RBvalAsuransi->SelectedValue = '0';
			
			$this->DDPerusAsuransi->SelectedValue = 'empty';
			$this->DDPerusAsuransi->Enabled = false;
		}
		else //Penjamin Non Umum
		{
			$this->RBvalAsuransi->SelectedValue = '1';
			
			if($this->DDKelompok->SelectedValue == '02' || $this->DDKelompok->SelectedValue == '07')
			{
				$this->DDPerusAsuransi->Enabled = true;	
				$idKelPerus = $this->DDKelompok->SelectedValue;
			
				$sql = "SELECT * FROM tbm_perusahaan_asuransi WHERE id_kel = '$idKelPerus' AND st='1' ORDER BY nama";
				$data = $this->queryAction($sql,'S');
				
				$this->DDPerusAsuransi->DataSource = $data;
				$this->DDPerusAsuransi->dataBind();
			
				if($this->getViewState('perusAsuransi'))
				{	
					$this->DDPerusAsuransi->SelectedValue = $this->getViewState('perusAsuransi');
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
				$this->Page->CallbackClient->focus($this->DDDokter);			
			}
		}
		else
		{
			$this->hubPasien->Enabled = false;
			$this->hubPasien->Text = '';
			$this->Page->CallbackClient->focus($this->DDHubPen);
		}
	}
	
	public function simpanClicked()
	{
		if($this->IsValid)
		{
			$cm = $this->formatCm($this->cm->Text);
		
			$sql="SELECT 
						no_trans, id_klinik
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND flag='0'";
				
			if($this->queryAction($sql,'S'))
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_question">Pasien dengan No. Rekam Medis <b>'.ucwords($cm).'</b> masih berstatus sebagai pasien rawat jalan. <br/> Apakah akan melanjutkan pendaftaran rawat inap untuk pasien ini ?</p>\',timeout: 600000,dialog:{
						modal: true,
						buttons: {
							"Ya": function() {
								jQuery( this ).dialog( "close" );
								maskContent();
								konfirmasi(\'ya\');
							},
							Tidak: function() {
								jQuery( this ).dialog( "close" );
								konfirmasi(\'tidak\');
							}
						}
					}});');	
			}
			else
			{	
				$this->insertRawatInap();								
			}	
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
		}
	}	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->insertRawatInap();		
		}
		else
		{
			//$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
	}
	
	public function insertRawatInap()
	{
		//UPDATE st_baru_lama di tbd_pasien
		$dataRwtJalan = RwtjlnRecord::finder()->find('cm=?',$this->formatCm($this->cm->Text));
		$dataRwtInap = RwtInapRecord::finder()->find('cm=?',$this->formatCm($this->cm->Text));
		
		if($dataRwtJalan || $dataRwtInap)
		{
			$data = PasienRecord::finder()->findByPk($this->formatCm($this->cm->Text));
			$data->st_baru_lama = '1';
			$data->save();
		}
		
		$noTrans = $this->numCounter('tbt_rawat_inap',RwtInapRecord::finder(),'02');//key '02' adalah konstanta modul untuk Rawat Inap
		$nipTmp=$this->User->IsUserNip;
		
		$jnsDaftar = $this->collectSelectionResult($this->modeDaftar);
		if($jnsDaftar == '1') //Jika mode daftar alih status
		{
			//Insert ke tbt_rawat_khusus
			$newInapKhusus=new InapKhususRecord();		
			$newInapKhusus->cm = $this->formatCm($this->cm->Text);
			$newInapKhusus->no_trans_inap = $noTrans;
			$newInapKhusus->no_trans_jln = $this->DDtrans->SelectedValue;
			$newInapKhusus->tgl = $this->ConvertDate($this->tglMsk->Text,'2');
			$newInapKhusus->wkt = $this->wktMsk->Text;
			$newInapKhusus->operator = $nipTmp;
			$newInapKhusus->flag = '0';
			$newInapKhusus->save();
			
			//Update status alih tabel asal pasien => tbt_rawat_jln atau tbt_rawat_jln
			$dataPas = RwtjlnRecord::finder()->find('no_trans = ?',$this->DDtrans->SelectedValue);
			$dataPas->st_alih = '1';
			$dataPas->save();
		}
		
		//Insert ke tbt_rawat_inap
		$newRwtInap=new RwtInapRecord();
		$newRwtInap->no_trans = $noTrans;
		$newRwtInap->tgl_masuk = $this->ConvertDate($this->tglMsk->Text,'2');
		$newRwtInap->wkt_masuk = $this->wktMsk->Text;
		$newRwtInap->cm = $this->formatCm($this->cm->Text);			
		$newRwtInap->kelas = (string)$this->DDKelas->SelectedValue;
		$newRwtInap->kamar = (string)$this->DDKamar->SelectedValue;
		$newRwtInap->jenis_kamar = (string)$this->DDJenKmr->SelectedValue;
		$newRwtInap->bed = $this->getViewState('jmlBedPakai') + 1;
		
		$newRwtInap->dokter = (string)$this->DDDokter->SelectedValue;			
		$newRwtInap->cr_masuk = (string)$this->DDCaraMsk->SelectedValue;
		$newRwtInap->diagnosa_masuk = '';			
		$newRwtInap->catatan = $this->catatan->Text;	
		
		$newRwtInap->status = '0';//Pasien belum keluar masih dirawat inap...						
		$newRwtInap->st_pulang = '0';
		$newRwtInap->st_resume = '0';
		
		$newRwtInap->st_asuransi = $this->collectSelectionResult($this->RBvalAsuransi);
		$newRwtInap->st_rujukan = $this->collectSelectionResult($this->modeInput);
		$newRwtInap->penjamin = $this->DDKelompok->SelectedValue;
		
		if($this->DDKelompok->SelectedValue == '02') //Asuransi
		{
			if($this->DDPerusAsuransi->SelectedValue != '')
			{
				$newRwtInap->perus_asuransi = $this->DDPerusAsuransi->SelectedValue;
			}
			else
			{
				$newRwtInap->perus_asuransi = '';
			}
		}
		else
		{
			$newRwtInap->perus_asuransi = '';
		}
		
		if($this->DDCaraMsk->SelectedValue == '5')//rujukan bidan
		{	
			$newRwtInap->id_bidan_perujuk = $this->DDbidanPerujuk->SelectedValue;
			$newRwtInap->nm_perujuk = $this->ambilTxt($this->DDbidanPerujuk);
		}
		elseif($this->DDCaraMsk->SelectedValue == '7') //rujukan dalam dari dokter dalam
		{
			$newRwtInap->id_dokter_perujuk_dalam = $this->DDbidanPerujuk->SelectedValue;
			$newRwtInap->nm_perujuk = $this->ambilTxt($this->DDbidanPerujuk);
		}
		else
		{
			$newRwtInap->nm_perujuk = $this->crMskLuar->Text;	
		}
		
		$newRwtInap->nama_pgg = $this->nmPen->Text;
		$newRwtInap->pekerjaan_pgg = (string)$this->DDKrjPen->SelectedValue;
		$newRwtInap->alamat_pj = $this->almPen->Text;
		$newRwtInap->telp_pj = $this->tlpPj->Text;
		$newRwtInap->hp_pj = $this->hpPj->Text;
		
		if($jnsDaftar == '0') //Jika mode daftar normal
		{			
			$newRwtInap->st_alih = '0';
		}	
		elseif($jnsDaftar == '1') //Jika mode daftar alih status
		{
			$newRwtInap->st_alih = '1';
		}
		
		if($this->DDHubPen->SelectedValue != '' ) 
		{
			if($this->DDHubPen->SelectedValue == '6') //hubungan lainnya
			{
				$newRwtInap->hub_pasien = $this->hubPasien->Text;
			}
			else
			{
				$newRwtInap->hub_pasien = $this->ambilTxt($this->DDHubPen);		
			}
		}
		else
		{
			$newRwtInap->hub_pasien = '';
		}
		
		$newRwtInap->discount = '0';
		//$newRwtInap->dp = $this->dp->Text;
		$newRwtInap->dp = '0';
		$newRwtInap->kasir = ''	;
		$newRwtInap->tgl_kasir = ''	;
		$newRwtInap->wkt_kasir = ''	;
		$newRwtInap->save();
		
		//Insert tbt_inap_adm => tarif adm standar = 5000, jika pasien rujukan ada tambahan u/ biaya administrasi			
		$dataAdm=new InapAdmRecord;
		$dataAdm->cm=$this->formatCm($this->cm->Text);
		$dataAdm->no_trans=$noTrans;	
		$dataAdm->tgl=date('Y-m-d');
		$dataAdm->wkt=date('G:i:s');
		$dataAdm->operator=$nipTmp;
		$dataAdm->tarif_adm=5000;
		$dataAdm->tarif_rujukan=0;
		$dataAdm->total_tarif=5000; // tarif_rujukan+tarif standar
		$dataAdm->operator=$nipTmp;
		$dataAdm->flag='0';
		//$dataAdm->Save();
	
		//Insert jml askep ke tbt_inap_askep
		$dataAskep_ok=new InapAskepRecord;
		$dataAskep_ok->cm=$this->formatCm($this->cm->Text);
		$dataAskep_ok->no_trans=$noTrans;	
		$dataAskep_ok->tgl=date('Y-m-d');
		$dataAskep_ok->wkt=date('G:i:s');
		$dataAskep_ok->operator=$nipTmp;
		$dataAskep_ok->flag='0';
		$dataAskep_ok->catatan='';
		$dataAskep_ok->tarif=7500;
		//$dataAskep_ok->Save();
		
		//Insert ke tbt_inap_kamar
		$dataKmr=new InapKamarRecord();
		$dataKmr->cm=$this->formatCm($this->cm->Text);
		$dataKmr->no_trans_inap=$noTrans;
		$dataKmr->tgl_awal=date('Y-m-d');
		$dataKmr->wkt_masuk=date('G:i:s');
		$dataKmr->id_kmr_awal=(string)$this->DDKelas->SelectedValue;
		$dataKmr->id_kmr_skrg=(string)$this->DDKelas->SelectedValue;
		$dataKmr->Save();
		
		//UPDATE jml_bed_pakai di tbm_ruang
		$jmlBedPakaiAwal = $this->getViewState('jmlBedPakai');
		$data = RuangRecord::finder()->findByPk($this->DDKamar->SelectedValue);
		$data->jml_bed = $data->jml_bed - 1;
		$data->jml_bed_pakai = $jmlBedPakaiAwal + 1;
		$data->Save();
		
		$bed = $jmlBedPakaiAwal+1;
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Data pasien rawat inap telah disimpan.<br/><br/></p>\',timeout: 1000000,dialog:{
				modal: true,
				buttons: {
					"OK": function() {
						jQuery( this ).dialog( "close" );
						maskContent();
						window.location="index.php?page=Pendaftaran.cetakRingakasanMasukInapHtml&cm='.$this->formatCm($this->cm->Text).'&noTrans='.$noTrans.'&jmlBed='.$bed.'"; 
					},
				}
			}});');	
		
		/*	
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtInap'));
		
		$this->msg->Text = '    
		<script type="text/javascript">
			alert("Data pasien rawat inap telah disimpan !");
			window.location="index.php?page=Pendaftaran.DaftarRwtInap"; 
		</script>';
		*/
		//$this->Response->Reload();
	}
	
	
	
	public function batalClicked()
    {
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.DaftarRwtInap'));
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}

}
?>
