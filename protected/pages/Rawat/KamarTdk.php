<?php
class KamarTdk extends SimakConf
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
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');
		$this->DDobgyn->DataSource=$arrData;	
		$this->DDobgyn->dataBind();
		
		//$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='13' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');
		$this->DDanastesi->DataSource=$arrData;	
		$this->DDanastesi->dataBind();
				
		//$sql="SELECT * FROM tbd_pegawai WHERE kelompok='1' AND spesialis='' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');		
		$this->DDResusitasi->DataSource=$arrData;	
		$this->DDResusitasi->dataBind();
		
		$sql="SELECT * FROM tbd_pegawai WHERE kelompok='2' ORDER BY nama";
		$arrData=$this->queryAction($sql,'S');
		
		$this->DDdokter->DataSource=$arrData;	
		$this->DDdokter->dataBind();
		
		$this->DDAsDokUtama->DataSource=$arrData;	
		$this->DDAsDokUtama->dataBind();
		
		$this->DDParamedis->DataSource=$arrData;	
		$this->DDParamedis->dataBind();
		
		$idOperasi = $this->DDtindakan->SelectedValue;
		$idKelas = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->kelas;
		
		$this->tarifKhususPanel->Display = 'Hidden';
		$this->tarifKhususPanel->Enabled = false;
		
		$this->modePenyulitAnastesi->Checked = false;
		$this->modePenyulitAnastesi->Enabled = false;
			
		/*
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
		*/
	}
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->cm->focus();
			$this->kategTxt->Text = '-';
			$this->DDtindakan->Enabled = false;
			$this->secondPanel->Display = 'None';
			$this->warningPanel->Display = 'None';
			$this->simpanBtn->Enabled=false;
			
			$sql="SELECT * FROM tbm_operasi_kamar_nama ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDKamar->DataSource=$arrData;	
			$this->DDKamar->dataBind();
			
			$sql="SELECT * FROM tbm_operasi_kategori ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDKategori->DataSource=$arrData;	
			$this->DDKategori->dataBind();			
			
			$sql="SELECT * FROM tbm_operasi_nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDtindakan->DataSource=$arrData;	
			$this->DDtindakan->dataBind();
			
			$this->DDtindakan->Enabled = false;
			
			$this->clearDD();
		}
		else
		{
			//------------------------- Cek No. RM -------------------------
			$cm = $this->getViewState('cm');	
			$data=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0');
			if($data)
			{
				$kateg=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->st_rujukan;
				$this->setViewState('kategST',$kateg);
				
				$tipeRujukan=RwtInapRecord::finder()->find('cm = ? AND status = ?', $this->formatCm($this->cm->Text),'0')->tipe_rujukan;
				$this->setViewState('tipeRujukan',$tipeRujukan);
				
				$this->setViewState('notrans',$data->no_trans);			
				$this->setViewState('kelas',$data->kelas);			
				
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
				/*
				if($this->DDKamar->SelectedValue == '')
					$this->Page->CallbackClient->focus($this->DDKamar);
				elseif($this->DDKategori->SelectedValue == '')
					$this->Page->CallbackClient->focus($this->DDKategori);	
				else
					$this->Page->CallbackClient->focus($this->DDKategori);	
				*/	
			}
			else
			{			
				$this->clearViewState('cm');
				$this->errMsg->Text='Pasien rawat inap dengan No. RM <b>'.$cm.'</b> tidak ditemukan !';
				$this->cm->Text = '';
				$this->Page->CallbackClient->focus($this->cm);
				$this->DDtindakan->Enabled=false;
				$this->simpanBtn->Enabled=false;
				
				//$this->firstPanel->Enabled = false;
				$this->secondPanel->Display = 'None';
			}
		}		
    }
	
	public function checkCM($sender,$param)
    {
		$this->setViewState('cm',$this->formatCm($this->cm->Text));	
	}
	
	public function DDTdkCallBack($sender,$param)
	{	
		$this->firstPanel->render($param->getNewWriter());
		$this->secondPanel->render($param->getNewWriter());
	}
	
	public function DDobgynCallback($sender,$param)
	{	
		$this->tarifKhususPanel->render($param->getNewWriter());
	}
	
	public function DDKategoriChanged()
	{
		$kateg = $this->DDKategori->SelectedValue;
		
		if($kateg != '')
		{
			$sql="SELECT * FROM tbm_operasi_nama WHERE id_kategori_operasi = '$kateg' ORDER BY nama";
			$arrData=$this->queryAction($sql,'S');
			$this->DDtindakan->DataSource = $arrData;	
			$this->DDtindakan->dataBind();	
			$this->DDtindakan->Enabled = true;
		}
		else
		{
			$this->DDtindakan->SelectedValue = 'empty';	
			$this->DDtindakan->Enabled = false;	
		}
	}
	
	public function ChangedTdk()
	{
		if($this->Page->IsValid)
		{
			if($this->DDtindakan->SelectedValue!='')
			{
				$this->nmOperasi->Text = '';
				
				$noTrans = $this->getViewState('notrans');
				$kelas = $this->getViewState('kelas');
				//$idKamar = $this->DDKamar->SelectedValue;
				//$idKateg = $this->DDKategori->SelectedValue;
				
				$idOpr = $this->DDtindakan->SelectedValue;
				$this->setViewState('idOpr',$idOpr);
				
				$index_resusitasi = OperasiNamaRecord::finder()->findByPk($idOpr)->index_resusitasi;
				$idKamar = OperasiNamaRecord::finder()->findByPk($idOpr)->id_kamar_operasi;
				$idKateg = OperasiNamaRecord::finder()->findByPk($idOpr)->id_kategori_operasi;
				
				if($index_resusitasi > 0)
					$this->DDResusitasi->Enabled = true;
				else
					$this->DDResusitasi->Enabled = false;	
				
				//CEK APA SUDAH ADA OPERASI YG SAMA SEBELUMNYA
				$sql = "SELECT * FROM tbt_inap_operasi_billing WHERE no_trans='$noTrans' AND id_opr='$idOpr' AND st='0'";
				$arr = $this->queryAction($sql,'S');
				
				if(count($arr) > 0 )//jika sudah ada operasi yg sama sebelmnya
				{
					$this->firstPanel->Display = 'None';
					$this->warningPanel->Display = 'Dynamic';
					$this->simpanBtn->Enabled = false;
				}
				else
				{
					$this->nmOperasi->Text = $this->ambilTxt($this->DDtindakan);
					
					$tarifStandar = KamarOperasiTarifRecord::finder()->find('id_kamar_operasi=? AND id_kategori_operasi=?',array($idKamar,$idKateg))->tarif;
					$persetaseKelas = KelasKamarRecord::finder()->findByPk($kelas)->persentase;
					
					$this->tarifOK->Text = $tarifStandar + ($tarifStandar * $persetaseKelas / 100);
					
					$this->firstPanel->Enabled = false;
					$this->secondPanel->Display = 'Dynamic';
					
					//$st = OperasiNamaRecord::finder()->findByPK($this->DDtindakan->SelectedValue)->st;
					//if($st==1)
					//{
						$this->clearDD();
						$this->simpanBtn->Enabled=true;	
					//}
				}
			}
			else
			{
				$this->secondPanel->Display = 'None';
				$this->CBcito->Enabled = true;
			}	
			
			$this->Page->CallbackClient->focus($this->nmOperasi);
		}
		else
		{
			$this->DDtindakan->SelectedValue = 'empty';
			$this->Page->CallbackClient->focus($this->DDKamar);
		}
	}
	
	public function cekObgyn($sender,$param)
	{
		$param->IsValid = $this->DDobgyn->SelectedValue != '';
	}
	
	public function DDobgynChanged($sender,$param)
    {
		$this->tarifKhusus->Text = '0';
		
		if($this->DDobgyn->selectedValue != '')
		{
			$idDokter = $this->DDobgyn->selectedValue;
			
			if(PegawaiRecord::finder()->findByPk($idDokter)->st_tarif_khusus_operasi == '1')
			{
				$this->tarifKhususPanel->Display = 'Fixed';
				$this->tarifKhususPanel->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->tarifKhusus->getClientID().'.focus();');
			}
			else
			{
				$this->tarifKhususPanel->Display = 'Hidden';
				$this->tarifKhususPanel->Enabled = false;
			}
		}
		else
		{
			$this->tarifKhususPanel->Display = 'Hidden';
			$this->tarifKhususPanel->Enabled = false;
		}
	}
	
	public function DDanastesiChanged($sender,$param)
    {
		$this->modePenyulitAnastesi->Checked = false;
				
		if($this->DDanastesi->selectedValue != '')
		{
			$this->modePenyulitAnastesi->Enabled = true;
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->modePenyulitAnastesi->getClientID().'.focus();');
		}
		else
			$this->modePenyulitAnastesi->Enabled = false;
	}
	
	
	public function simpanClicked($sender,$param)
    {	
		if($this->Page->IsValid)
		{
			$idOperasi = $this->DDtindakan->SelectedValue;
			$idKamar = $this->DDKamar->SelectedValue;
			
			$idKelas = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->kelas;
			$stRujuk = RwtInapRecord::finder()->findByPk($this->getViewState('notrans'))->st_rujukan;
			
			//$pengaliKoordinator = PengaliJasaKoordinatorRecord::finder()->findByPk('1')->nilai;
			$pengaliKoordinator = 1;
			
			$persentaseKoordinator = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator * $pengaliKoordinator) / 100;
			$persentaseKoordinatorAnastesi = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator_anastesi * $pengaliKoordinator) / 100;
			$persentaseKoordinatorAstAnastesi = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator_ast_anastesi * $pengaliKoordinator) / 100;
			$persentaseKoordinatorAstInstrumen = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator_ast_instrumen * $pengaliKoordinator) / 100;
			$persentaseKoordinatorParamedis = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator_paramedis * $pengaliKoordinator) / 100;
			$persentaseKoordinatorResusitasi = (OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_jasa_koordinator_resusitasi * $pengaliKoordinator) / 100;
			
			$persentaseRs = OperasiNamaRecord::finder()->findByPk($idOperasi)->persentase_rs / 100;


			$tglNow=date('Y-m-d');
			$wktNow=date('G:i:s');
			$nipTmp=$this->User->IsUserNip;
			
			$tarifStandar = OperasiTarifRecord::finder()->find('id_operasi=?',array($idOperasi))->js_dok_obgyn;
			$persetaseKelas = KelasKamarRecord::finder()->findByPk($idKelas)->persentase;
			$tarifStandar = $tarifStandar + ($tarifStandar * $persetaseKelas / 100);
			
			//jika ada penambahan tarif operator untuk dokter khusus
			if(floatval($this->tarifKhusus->Text))
				$tarifStandar += $this->tarifKhusus->Text;
				
			//if($this->modeCito->SelectedValue == '1') //CITO
			if($this->modeCito->Checked == true) //CITO
			{
				$tarifCito = $tarifStandar * 0.2;
				$tarifStandar += $tarifCito;
			}
			else
			{
				$tarifCito = 0;
			}
			
			//if($this->modeCito->SelectedValue == '1') //CITO
			if($this->modePenyulit->Checked == true) //CITO
			{
				$tarifPenyulit = $tarifStandar * 0.2;
				$tarifStandar += $tarifPenyulit;
			}
			else
			{
				$tarifPenyulit = 0;
			}
			
			$index_operator = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_operator;	
			$index_anastesi = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_anastesi;	
			$index_ast_anastesi = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_ast_anastesi;	
			$index_ast_instrumen = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_ast_instrumen;	
			$index_paramedis = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_paramedis;	
			$index_resusitasi = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_resusitasi;	
			
			$index_pengembang = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_pengembang;	
			$index_penyulit = OperasiNamaRecord::finder()->findByPk($idOperasi)->index_penyulit;	
			
			if($this->DDanastesi->SelectedValue != '')
				$tarifAnastesi =  $tarifStandar * $index_anastesi / $index_operator;
			else
				$tarifAnastesi = 0;
			
			if($this->modePenyulitAnastesi->Checked == true) //jika ada penyulit dokter anastesi
				$tarifAnastesi += $tarifAnastesi * 0.2;
			
			$tarifAstAnastesi =  $tarifStandar * $index_ast_anastesi / $index_operator;
			$tarifInstrumen =  $tarifStandar * $index_ast_instrumen / $index_operator;
			$tarifParamedis =  $tarifStandar * $index_paramedis / $index_operator;
			$tarifResusitasi =  $tarifStandar * $index_resusitasi / $index_operator;
			$tarifPengembang = ($tarifStandar  - $tarifCito - $tarifPenyulit) * $index_pengembang / $index_operator;
			//$tarifPenyulit = ($tarifStandar  - $tarifCito) * $index_penyulit / $index_operator;
			
			$tarifJasaKoordinator = $tarifStandar * $persentaseKoordinator;
			$tarifJasaKoordinatorAnastesi = $tarifStandar * $persentaseKoordinatorAnastesi;
			$tarifJasaKoordinatorAstAnastesi = $tarifStandar * $persentaseKoordinatorAstAnastesi;
			$tarifJasaKoordinatorAstInstrumen = $tarifStandar * $persentaseKoordinatorAstInstrumen;
			$tarifJasaKoordinatorParamedis = $tarifStandar * $persentaseKoordinatorParamedis;
			$tarifJasaKoordinatorResusitasi = $tarifStandar * $persentaseKoordinatorResusitasi;
			
			$totalJasaMedis = ($tarifStandar + $tarifAnastesi + $tarifAstAnastesi + $tarifInstrumen + $tarifParamedis + $tarifResusitasi + $tarifPengembang) * $persentaseRs;
			/*
			$this->showSql->Text = $tarifStandar.' - '.$index_operator.' - '.$index_anastesi.' - '.$index_ast_anastesi.' - '.$index_ast_instrumen.' - '.$index_paramedis.' - '.$index_resusitasi.' - '.$index_pengembang.' - '.$index_penyulit.'<br/><br/>';
			
			$this->showSql->Text .= ' Standar : '.$tarifStandar.'<br/>';
			$this->showSql->Text .= ' Cito : '.$tarifCito.'<br/>';
			$this->showSql->Text .= ' tarifAnastesi : '.$tarifAnastesi.'<br/>';
			$this->showSql->Text .= ' tarifAstAnastesi : '.$tarifAstAnastesi.'<br/>';
			$this->showSql->Text .= ' tarifInstrumen : '.$tarifInstrumen.'<br/>';
			$this->showSql->Text .= ' tarifParamedis : '.$tarifParamedis.'<br/>';
			$this->showSql->Text .= ' tarifResusitasi : '.$tarifResusitasi.'<br/>';
			$this->showSql->Text .= ' tarifPengembang : '.$tarifPengembang.'<br/>';
			$this->showSql->Text .= ' tarifPenyulit : '.$tarifPenyulit.'<br/>';
			*/
			
			$newOperasi= new OperasiBillingRecord();
				
			$newOperasi->no_trans = $this->getViewState('notrans');
			$newOperasi->cm = $this->formatCm($this->cm->Text);
			$newOperasi->id_opr = $this->DDtindakan->SelectedValue;
			$newOperasi->nm_opr = $this->nmOperasi->Text;
			$newOperasi->tgl = $tglNow;	
			$newOperasi->wkt = $wktNow;		
			
			$newOperasi->dktr_obgyn = $this->DDobgyn->SelectedValue;
			$newOperasi->tarif_obgyn = $tarifStandar;	
			
			$newOperasi->dktr_anastesi = $this->DDanastesi->SelectedValue;
			$newOperasi->tarif_anastesi = $tarifAnastesi;	
							
			$newOperasi->dktr_anak = $this->DDdokter->SelectedValue;
			$newOperasi->tarif_anak = $tarifAstAnastesi;	
			
			$newOperasi->ass_dktr = $this->DDAsDokUtama->SelectedValue;
			$newOperasi->tarif_assdktr = $tarifInstrumen;	
			
			$newOperasi->paramedis = $this->DDParamedis->SelectedValue;
			$newOperasi->tarif_paramedis = $tarifParamedis;		
			
			$newOperasi->dktr_resusitasi = $this->DDResusitasi->SelectedValue;
			$newOperasi->tarif_resusitasi = $tarifResusitasi;
			
			$newOperasi->tarif_pengembang = $tarifPengembang;
			$newOperasi->tarif_penyulit = $tarifPenyulit;
			
			$newOperasi->rs = $totalJasaMedis;
			
			$newOperasi->jasa_koordinator = $tarifJasaKoordinator;
			$newOperasi->jasa_koordinator_anastesi = $tarifJasaKoordinatorAnastesi;
			$newOperasi->jasa_koordinator_ast_anastesi = $tarifJasaKoordinatorAstAnastesi;
			$newOperasi->jasa_koordinator_ast_instrumen = $tarifJasaKoordinatorAstInstrumen;
			$newOperasi->jasa_koordinator_paramedis = $tarifJasaKoordinatorParamedis;
			$newOperasi->jasa_koordinator_resusitasi = $tarifJasaKoordinatorResusitasi;						
			
			$newOperasi->sewa_ok = $this->tarifOK->Text;
			
			$newOperasi->st='0';
			$newOperasi->operator=$nipTmp;
										
			$newOperasi->save();
			
			
			/*
			$sql = "SELECT * FROM tbm_operasi_tarif WHERE id_operasi='$idOperasi' AND id_kelas='$idKelas'";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{	
				$tarifStandar = $row['js_dok_obgyn'];	
				
				$newOperasi= new OperasiBillingRecord();
				
				$newOperasi->no_trans=$this->getViewState('notrans');
				$newOperasi->cm=$this->formatCm($this->cm->Text);
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
					//$newOperasi->kamar_ibu = '0';
					//$newOperasi->kamar_bayi = '0';
					$newOperasi->js_bidan_pengirim='0';
				}
				
				$newOperasi->kamar_ibu = $row['kamar_ibu'];
				$newOperasi->kamar_bayi = $row['kamar_bayi'];
					
				$newOperasi->adm=$row['adm'];
				$newOperasi->materai=$row['materai'];
				$newOperasi->st='0';
				$newOperasi->operator=$nipTmp;
											
				$newOperasi->save();
				
			}
			
			//$this->clearViewState('notrans');
			//$this->errMsg->Text='';	
			
			//$this->Response->redirect($this->Service->constructUrl('Rawat.KamarTdk'));
			*/
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();');
			
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Transaksi Billing Kamar Tindakan sukses .<br/><br/></p>\',timeout: 600000,dialog:{
					modal: true,
					buttons: {
						"OK": function() {
							jQuery( this ).dialog( "close" );
							maskContent();
							konfirmasi(\'tidak\');
						}
					}
				}});');	
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('', 'unmaskContent();');
		
		}
	}
	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
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
