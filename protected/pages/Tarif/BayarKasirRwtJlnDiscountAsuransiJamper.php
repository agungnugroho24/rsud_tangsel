<?php
class BayarKasirRwtJlnDiscountAsuransiJamper extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
			
		$session=$this->Application->getModule('session');	
		if($session['stCetakKasirRwtJln']=='1')
		{
			$session->remove('stCetakKasirRwtJln');
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));
		}	
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{			
			$this->citoCheck->Checked=false;
			$this->cetakBtn->Enabled=false;
			$this->detailPanel->Display = 'None';			
			
			$this->DDtrans->Enabled = false;
			$this->nmPas->Display = 'None';
			$this->perus->Display = 'None';
			$this->perusTxt->Display = 'None';
			$this->kryCheck->Display = 'None';
			$this->jmlPanel->Enabled = false;
			$this->jmlPanel2->Enabled = false;
			$this->jnsTransBebasPanel->Display = 'None';
			$this->detailPanel->Enabled = false;
			
			$this->noRef->Enabled = false;
			$this->noRefPanel->Display = 'None';
			
			$this->tipeBayarKaryawanPanel->Display = 'None';
			
			$jnsPasien = $this->modeInput->SelectedValue;
			
			$this->sisaByrTxt->Text = 'Sisa/Kembalian';
			$this->bayar2->Enabled = false;
			
			$sql = "SELECT * FROM tbm_carabayar ORDER BY id";
			$arr = $this->queryAction($sql,'S');
			$this->caraBayar->DataSource=$arr;
			$this->caraBayar->dataBind();
			
			$this->caraBayar2->DataSource=$arr;
			$this->caraBayar2->dataBind();	
				
			if($this->Request['cm'] !='' && $this->Request['noTrans'] != '' && $this->Request['jnsPasien'] != '')
			{
				$this->modeInput->SelectedValue = $this->Request['jnsPasien'];
				$this->notrans->Text = $this->Request['cm'];
				$this->DDtrans->SelectedValue = $this->Request['noTrans'];
				
				$this->checkRegister($sender,$param);
				$this->checkNoTrans();
				
				$this->detailPanel->Display = 'Dynamic';
				$this->cariPasBtn->Enabled=false;
			}
			
			$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Tarif.CariPasienRwtJlnAktif&parentPage=Tarif.BayarKasirRwtJlnDiscount&notransUpdate='.$this->notrans->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		}	
		else
		{
			if($this->getViewState('nmTable'))
			{
				$this->bindGrid();
			}
			
			if($this->getViewState('nmTableLab'))
			{
				$this->bindGridLabTmp();
			}
			
			if($this->getViewState('nmTableRad'))
			{
				$this->bindGridRadTmp();
			}
		}	
		
		
		
		$jnsPasien = $this->modeInput->SelectedValue;
		if($jnsPasien != '2' && $jnsPasien != '3') //bukan pasien bebas
		{	
			
		}
		
		$this->rekapBayar();
    }
	
	public function cmCallBack($sender,$param)
   	{
		$this->noTransPanel->render($param->getNewWriter());
		$this->pasBebasPanel->render($param->getNewWriter());
	}
	
	public function modeInputChanged($sender, $param)
	{
		$jnsPasien = $this->modeInput->SelectedValue;
		
		$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=Tarif.CariPasienRwtJlnAktif&parentPage=Tarif.BayarKasirRwtJlnDiscount&notransUpdate='.$this->notrans->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$this->notrans->Enabled = true;
			$this->cariPasBtn->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->nmPas->Display = 'None';
			$this->kryCheck->Display = 'None';
			$this->Page->CallbackClient->hide($this->nmPas);
			$this->Page->CallbackClient->hide($this->kryCheck);
			$this->jnsTransBebasPanel->Display = 'None';
			$this->detailPanel->Display = 'None';
			$this->detailRwtJalanPanel->Display = 'Dynamic';
			$this->detailApotikPanel->Display = 'Dynamic';
			
			$this->DDtrans->Enabled=false;
		}
		elseif($jnsPasien == '1') //Jika pasien rawat inap
		{
			$this->notrans->Enabled = true;
			$this->cariPasBtn->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->nmPas->Display = 'None';
			$this->kryCheck->Display = 'None';			
			$this->Page->CallbackClient->hide($this->nmPas);
			$this->Page->CallbackClient->hide($this->kryCheck);
			$this->jnsTransBebasPanel->Display = 'None';
			$this->detailPanel->Display = 'None';
			$this->detailRwtJalanPanel->Display = 'None';
			$this->detailApotikPanel->Display = 'Dynamic';
			
			$this->DDtrans->Enabled=false;
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //Jika pasien Bebas / Bebas Karyawan
		{
			$this->notrans->Enabled = false;
			$this->cariPasBtn->Enabled = false;
			$this->citoCheck->Enabled=true;			
			$this->nmPas->Display = 'None';
			$this->kryCheck->Display = 'None';			
			$this->jnsTransBebasPanel->Display = 'Dynamic';
		}
		
		$this->notrans->Text = '';
		$this->errMsg->Text='';	
		
		$this->jnsTransBebas->SelectedIndex = -1;
		$this->DDtrans->SelectedIndex = -1;
		$this->nama->Text = '';
		$this->nmPas->Text = '';
		$this->kryCheck->checked=false;
		$this->jmlShow->Text = '';
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
	}
	
	
	public function modeBayarKaryawanCallBack($sender,$param)
   	{
		$this->jmlPanel->render($param->getNewWriter());
	}
	
	public function modeBayarKaryawanChanged($sender, $param)
	{
		$jnsBayar = $this->collectSelectionResult($this->modeBayarKaryawan);
		$this->bayar->Text = '';
		if($jnsBayar == '0') //Karyawan Bayar Kredit
		{
			$this->jmlPanel->Enabled = 'false';
			$this->Page->CallbackClient->focus($this->cetakBtn);
			$this->cetakBtn->Enabled = true;
		}
		elseif($jnsBayar == '1') //Karyawan Bayar Tunai
		{
			$this->jmlPanel->Enabled = 'true';
			$this->Page->CallbackClient->focus($this->bayar);
			$this->cetakBtn->Enabled = false;
		}
	}
	
	
	public function hidePanel()
	{
		$this->nmPas->Display = 'None';
		$this->kryCheck->Display = 'None';
		
		$this->detailPanel->Display = 'None';
		$this->detailRwtJalanPanel->Display = 'None';
		$this->detailLabPanel->Display = 'None';
		$this->detailRadPanel->Display = 'None';
		$this->detailFisioPanel->Display = 'None';
		$this->detailApotikPanel->Display = 'None';
		$this->detailAmbulanPanel->Display = 'None';
	}
	
		
	public function jnsTransBebasChanged($sender, $param)
	{
		$this-> hidePanel();
		
		$jnsPasien = $this->modeInput->SelectedValue;
		$jnsTrans = $this->collectSelectionResult($this->jnsTransBebas);
		
		if($jnsTrans == '0') //Transaksi Non Apotik
		{
			$this->nmPas->Display = 'Dynamic';
			$this->kryCheck->Display = 'Dynamic';
			
			$this->detailPanel->Display = 'Dynamic';
			$this->detailRwtJalanPanel->Display = 'None';
			$this->detailLabPanel->Display = 'Dynamic';
			$this->detailRadPanel->Display = 'Dynamic';
			$this->detailFisioPanel->Display = 'Dynamic';
			$this->detailApotikPanel->Display = 'None';
			$this->detailAmbulanPanel->Display = 'None';
			
			
			$this->DDtrans->Enabled=false;
		}
		elseif($jnsTrans == '1') //Transaksi Apotik
		{
			$this->nmPas->Display = 'None';
			$this->kryCheck->Display = 'None';
			
			$this->detailPanel->Display = 'Dynamic';
			$this->detailRwtJalanPanel->Display = 'None';
			$this->detailLabPanel->Display = 'None';
			$this->detailRadPanel->Display = 'None';
			$this->detailFisioPanel->Display = 'None';
			$this->detailApotikPanel->Display = 'Dynamic';
			$this->detailAmbulanPanel->Display = 'None';
			
			
			$this->DDtrans->Enabled=true;			
			
			if($jnsPasien == '2')//Pasien Luar berstatus UMUM
			{
				$sql = "SELECT 
					  tbd_pasien_luar.nama,
					  tbt_obat_jual_lain.no_trans_pas_luar
					FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_obat_jual_lain ON (tbd_pasien_luar.no_trans = tbt_obat_jual_lain.no_trans_pas_luar)
					WHERE
						tbt_obat_jual_lain.flag = '0'
					GROUP BY tbt_obat_jual_lain.no_trans_pas_luar ";
			}
			elseif($jnsPasien == '3')//Pasien Luar berstatus karyawan
			{
				$sql = "SELECT 
					  tbd_pasien_luar.nama,
					  tbt_obat_jual_lain_karyawan.no_trans_pas_luar
					FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_obat_jual_lain_karyawan ON (tbd_pasien_luar.no_trans = tbt_obat_jual_lain_karyawan.no_trans_pas_luar)
					WHERE
						tbt_obat_jual_lain_karyawan.flag = '0'
					GROUP BY tbt_obat_jual_lain_karyawan.no_trans_pas_luar";
			}
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans_pas_luar'];
					$nama = $row['no_trans_pas_luar'].' - '.$row['nama'];
					$data[]=array('no_trans'=>$noTrans,'nama'=>$nama);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
				
		}
		
		if($jnsTrans == '2') //Transaksi Laboratorium Pasien Luar
		{
			$sql = "SELECT 
					  tbd_pasien_luar.nama,
					  tbt_lab_penjualan_lain.no_trans_pas_luar
					FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_lab_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_lab_penjualan_lain.no_trans_pas_luar)
					WHERE
						tbt_lab_penjualan_lain.flag = '0'";
			
			if($jnsPasien == '2')//Pasien Luar berstatus UMUM
			{
				$sql .= " AND st_karyawan = '0' ";
			}
			elseif($jnsPasien == '3')//Pasien Luar berstatus karyawan
			{
				$sql .= " AND st_karyawan = '1' ";
			}
			
			$sql .= " GROUP BY tbt_lab_penjualan_lain.no_trans_pas_luar ";
						
			$arr=$this->queryAction($sql,'S');
			if($arr)
			{	
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans_pas_luar'];
					$nama = $row['no_trans_pas_luar'].' - '.$row['nama'];
					$data[]=array('no_trans'=>$noTrans,'nama'=>$nama);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
					
				$this->DDtrans->Enabled=true;
				$this->detailLabPanel->Display = 'Dynamic';
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Belum ada transaksi laboratorium untuk pasien luar.");
					document.all.'.$this->jnsTransBebas->getClientID().'.focus();
				</script>';
				$this->DDtrans->Enabled=false;
			}
		}
		if($jnsTrans == '3') //Transaksi Radiologi Pasien Luar
		{
			$sql = "SELECT 
					  tbd_pasien_luar.nama,
					  tbt_rad_penjualan_lain.no_trans_pas_luar
					FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_rad_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_rad_penjualan_lain.no_trans_pas_luar)
					WHERE
						tbt_rad_penjualan_lain.flag = '0'";
			
			if($jnsPasien == '2')//Pasien Luar berstatus UMUM
			{
				$sql .= " AND st_karyawan = '0' ";
			}
			elseif($jnsPasien == '3')//Pasien Luar berstatus karyawan
			{
				$sql .= " AND st_karyawan = '1' ";
			}
			
			$sql .= " GROUP BY tbt_rad_penjualan_lain.no_trans_pas_luar ";
			
			$arr=$this->queryAction($sql,'S');
			if($arr)
			{	
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans_pas_luar'];
					$nama = $row['no_trans_pas_luar'].' - '.$row['nama'];
					$data[]=array('no_trans'=>$noTrans,'nama'=>$nama);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
					
				$this->DDtrans->Enabled=true;
				$this->detailRadPanel->Display = 'Dynamic';
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Belum ada transaksi radiologi untuk pasien luar.");
					document.all.'.$this->jnsTransBebas->getClientID().'.focus();
				</script>';
				$this->DDtrans->Enabled=false;
			}
		}
		if($jnsTrans == '4') //Transaksi Fisio Pasien Luar
		{
			$sql = "SELECT 
					  tbd_pasien_luar.nama,
					  tbt_fisio_penjualan_lain.no_trans_pas_luar
					FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_fisio_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_fisio_penjualan_lain.no_trans_pas_luar)
					WHERE
						tbt_fisio_penjualan_lain.flag = '0'";
			
			if($jnsPasien == '2')//Pasien Luar berstatus UMUM
			{
				$sql .= " AND st_karyawan = '0' ";
			}
			elseif($jnsPasien == '3')//Pasien Luar berstatus karyawan
			{
				$sql .= " AND st_karyawan = '1' ";
			}
			
			$sql .= " GROUP BY tbt_fisio_penjualan_lain.no_trans_pas_luar ";
			
			$arr=$this->queryAction($sql,'S');
			if($arr)
			{	
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans_pas_luar'];
					$nama = $row['no_trans_pas_luar'].' - '.$row['nama'];
					$data[]=array('no_trans'=>$noTrans,'nama'=>$nama);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
					
				$this->DDtrans->Enabled=true;
				$this->detailFisioPanel->Display = 'Dynamic';
			}
			else
			{
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Belum ada transaksi fisio untuk pasien luar.");
					document.all.'.$this->jnsTransBebas->getClientID().'.focus();
				</script>';
				
				$this->DDtrans->Enabled=false;
			}
		}
		
		$this->nmPas->Text = '';
		$this->kryCheck->Checked =false;
	}		
	
	public function nmPasChanged($sender, $param)
	{
		if($this->nmPas->Text != '')
		{
			$this->firstPanel->Enabled = false;
			$this->jmlPanel->Enabled = true;
			$this->detailPanel->Enabled = true ;
			$this->detailNonInap();
		}
		else
		{
			$this->firstPanel->Enabled = true;
			$this->jmlPanel->Enabled = false;
			$this->detailPanel->Enabled = false ;
			$this->clearViewState('tmpJml');
			$this->clearViewState('tmpJml2');
		}
	}
	
	public function CBrwtInapCheck($sender, $param)
	{
		if($this->CBrwtInap->Checked==false)
		{
			//$this->modeInputCtrl->Visible=true;			
			$this->modeInput->SelectedIndex='0';
		}
		else
		{	
			//$this->modeInputCtrl->Visible=false;
						
		}
		
		$this->notrans->Enabled = false;
		$this->notrans->Text = '';
		$this->errMsg->Text='';	
		
		$this->modeInput->SelectedIndex=-1;
		$this->showSecond->Visible=false;
		$this->detailPanel->Visible = false ;
		
	}
	
	
	
	//Jika yang dipilih pasien rujukan tampilkan pilihan jenis transaksi
	public function modeInputTransChanged($sender, $param)
	{
		$this->showFirst->Visible=true;
		
		$this->showSecond->Visible=false;
		$this->notrans->Text = '';
		$this->Page->CallbackClient->focus($this->notrans);
					
		$this->detailPanel->Visible=false;
		$this->errMsg->Text='';	
			
		$dateNow = date('Y-m-d');
		$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
		if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
		{
			if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
			{
				if(LabJualLainRecord::finder()->findAll('tgl = ? AND flag = ?', array($dateNow,'0')))
				{			
					$sql="SELECT 
							no_trans,
							cm as nama	
						FROM 
							tbt_lab_penjualan_lain
						WHERE 
							tgl='$dateNow' 
							AND flag='0'
						GROUP BY no_trans";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$data .= $row['no_trans'] .',';	
						//$petugasPoli = $row['operator'];	
					}			
					
					$v = strlen($data) - 1;
					$var=substr($data,0,$v);				
					$temp = explode(',',$var);
					
					$this->DDtrans->DataSource=$arr;
					$this->DDtrans->dataBind();
					
					//$this->setViewState('petugasPoli',$petugasPoli);
				
					
					$this->citoCheck->Enabled=false;
					$this->errMsg->Text='';
				}
				else
				{
					$this->showFirst->Visible=true;
					
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi laboratorium pasien rujukan belum ada !';
					$this->notrans->Text = '';
					$this->Page->CallbackClient->focus($this->notrans);				
					$this->citoCheck->Enabled=true;
				}
			}
			else //jika checkbox rawat inap dipilih
			{			
				$this->notrans->Enabled = true;
				$this->Page->CallbackClient->focus($this->notrans);	
			}
		}
		elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
		{
			if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
			{
				if(RadJualLainRecord::finder()->find('tgl = ? AND flag = ?', array($dateNow,'0')))
				{			
					$sql="SELECT 
							no_trans,
							cm as nama	
						FROM 
							tbt_rad_penjualan_lain
						WHERE 
							tgl='$dateNow' 
							AND flag='0'
							GROUP BY no_trans";
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$data .= $row['no_trans'] .',';	
						//$petugasPoli = $row['operator'];	
					}			
					
					$v = strlen($data) - 1;
					$var=substr($data,0,$v);				
					$temp = explode(',',$var);
					
					$this->DDtrans->DataSource=$arr;
					$this->DDtrans->dataBind();
					
					//$this->setViewState('petugasPoli',$petugasPoli);
				
					
					$this->citoCheck->Enabled=false;
					$this->errMsg->Text='';
				}
				else
				{
					$this->showFirst->Visible=true;
					
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi radiologi pasien rujukan belum ada !';
					$this->notrans->Text = '';
					$this->Page->CallbackClient->focus($this->notrans);				
					$this->citoCheck->Enabled=true;
				}
			}
			else //jika checkbox rawat inap dipilih
			{			
				$this->notrans->Enabled = true;
				$this->Page->CallbackClient->focus($this->notrans);	
			}
		}
	}
		
		
	public function checkRegister($sender,$param)
    {
		$cm = $this->notrans->Text;
		
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$dateNow = date('Y-m-d');
			// valid if the username is not found in the database
			if(RwtjlnRecord::finder()->find('cm = ? AND flag = ? AND st_alih = ? AND tgl_visit = ?', array($cm ,'0','0',$dateNow)))
			{			
				$sql="SELECT 
						no_trans,id_klinik,penjamin,perus_asuransi,st_asuransi
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND flag='0'
						AND st_alih='0'
						AND tgl_visit='$dateNow'";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans'];
					$idKlinik = $row['id_klinik'];
					$nmKlinik = PoliklinikRecord::finder()->findByPk($idKlinik)->nama;
					//$data .= $row['no_trans'] .',';	
					//$petugasPoli = $row['operator'];	
					$data[]=array('no_trans'=>$noTrans,'nama'=>$noTrans.' - '.$nmKlinik);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->DDtrans->getClientID().'.focus();
				</script>';
				
				$this->citoCheck->Enabled=false;
				$this->DDtrans->Enabled=true;
				$this->errMsg->Text='';
			}
			elseif(RwtjlnRecord::finder()->find('cm = ? AND flag = ? AND st_alih = ? AND tgl_visit = ?', array($cm ,'0','1',$dateNow)))
			{
				$this->DDtrans->Enabled=false;
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien rawat jalan dengan No.Rekam Medis '.$this->notrans->Text.' sudah alih status ke rawat inap!");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text = '';			
				$this->citoCheck->Enabled=true;
			}
			else
			{
				$this->DDtrans->Enabled=false;
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien rawat jalan dengan No.Rekam Medis '.$this->notrans->Text.' tidak ada !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				$this->notrans->Text = '';			
				$this->citoCheck->Enabled=true;
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{	
			// valid if the username is not found in the database
			if(RwtInapRecord::finder()->find('cm = ? AND status = ? ', array($cm ,'0')))
			{			
				$sql="SELECT 
						no_trans	
					FROM 
						tbt_rawat_inap
					WHERE 
						cm='$cm' 
						AND status='0'";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$noTrans = $row['no_trans'];
					$data[]=array('no_trans'=>$noTrans,'nama'=>$noTrans);
				}	
				
				$this->DDtrans->DataSource=$data;
				$this->DDtrans->dataBind();
				
				$this->errMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->DDtrans->getClientID().'.focus();
				</script>';
				
				$this->citoCheck->Enabled=false;
				$this->DDtrans->Enabled=true;
				$this->errMsg->Text='';
			}
			else
			{
				$this->DDtrans->Enabled=false;
				$this->errMsg->Text = '    
				<script type="text/javascript">
					alert("Pasien rawat inap dengan No.Rekam Medis '.$this->notrans->Text.' tidak ditemukan !");
					document.all.'.$this->notrans->getClientID().'.focus();
				</script>';
				
				$this->notrans->Text = '';		
				$this->citoCheck->Enabled=true;
			}
		}		
    }	
	
	
	public function DDtransCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
		//$this->detailPanel->render($param->getNewWriter());
		$this->jmlPanel->render($param->getNewWriter());
		$this->jmlPanel2->render($param->getNewWriter());
	}
	
	public function DDtransChanged()
	{
		$this->cleanAllTmpTable();
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
		
		if($this->DDtrans->SelectedValue!='')
		{
			$this->detailPanel->Display = 'Dynamic';
			$jnsPasien = $this->modeInput->SelectedValue;
			//if($jnsPasien != '2') //bukan pasien bebas
			//{	
				$this->checkNoTrans();
			//}
		}
		else
		{
			$this->detailPanel->Display = 'None';
			$this->errMsg->Text='';	
			
			$this->nama->Text ='';
			$this->kelPasien->Text ='';
			$this->perus->Text ='';
			$this->perus->Display = 'None';
			$this->perusTxt->Display = 'None';
		}
	}
	
	public function checkNoTrans()
    {
		$jnsPasien = $this->modeInput->SelectedValue;
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$cm = $this->notrans->Text;
						
			$noTransRwtJln = $this->DDtrans->SelectedValue;	
			$this->setViewState('noTransRwtJln',$noTransRwtJln);
			
			$sql ="SELECT b.nama 				
					 FROM tbt_rawat_jalan a, 
						  tbd_pegawai b 
					 WHERE a.no_trans='$noTransRwtJln' 
						AND a.dokter=b.id
					 ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				$dokter=$row['nama'];	
			}
			
			$this->setViewState('dokter',$dokter);					
			
			//pengecekan status alih di tbt_rawat_jalan
			if(RwtjlnRecord::finder()->find('no_trans = ? AND st_alih = ?', array($noTransRwtJln ,'1'))) //jika sudah alih status ke rwt inap
			{
				$this->errMsg->Text='Tagihan masuk rekap rawat inap !';
				//$this->showSecond->Visible=false;
			}
			else
			{
				//$tmpPasien = $activeRec->findBySql($sql);
				$nmPas = PasienRecord::finder()->findByPk($cm)->nama;			
				$this->setViewState('nama',$nmPas);
				$this->nama->Text= $nmPas;	
				
				$idKlinik = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->id_klinik;
				$this->setViewState('idKlinik',$idKlinik);
				
				//$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->cariPasBtn->Enabled=false;
				$this->errMsg->Text='';	
				$this->firstPanel->Enabled = false;
				$this->jmlPanel->Enabled = true;
				$this->detailPanel->Enabled = true ;
				
				$this->detailNonInap();
				
				$kelompokPasien = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->penjamin;
				$stAsuransi = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->st_asuransi;
				
				$this->setViewState('kelompokPasien',$kelompokPasien);
				$this->setViewState('stAsuransi',$stAsuransi);
				
				if($kelompokPasien=='04' && $stAsuransi=='1')//pasien adalah karyawan & st_asuransi=1 (status karyawan berlaku)
				{
					$this->tipeBayarKaryawanPanel->Display = 'Dynamic';
				}
				else//bukan pasien karyawan
				{
					$this->tipeBayarKaryawanPanel->Display = 'None';
				}
				
				$this->kelPasien->Text = KelompokRecord::finder()->findByPk($kelompokPasien)->nama;	
				
				if($kelompokPasien == '02')//Kelompok Penjamin Asuransi 
				{
					$perusAsuransi = RwtjlnRecord::finder()->findByPk($noTransRwtJln)->perus_asuransi;	
					$this->perus->Text = PerusahaanRecord::finder()->findByPk($perusAsuransi)->nama;	
					$this->perusTxt->Display = 'Dynamic';
					$this->perus->Display = 'Dynamic';
				}
				else
				{
					$this->perus->Text ='';
					$this->perus->Display = 'None';
					$this->perusTxt->Display = 'None';
				}
				
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$cm = $this->notrans->Text;
			
				
			$nmPas = PasienRecord::finder()->findByPk($this->notrans->Text)->nama;		
			$this->setViewState('nama',$nmPas);
			$this->nama->Text= $nmPas;	
			
			$noTransRwtInap = $this->DDtrans->SelectedValue;	
			$this->setViewState('noTransRwtInap',$noTransRwtInap);
			
			$sql ="SELECT b.nama 				
					 FROM tbt_rawat_inap a, 
						  tbd_pegawai b 
					 WHERE a.no_trans='$noTransRwtInap' 
						AND a.dokter=b.id
					 ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				$dokter=$row['nama'];	
			}
			
			$this->setViewState('dokter',$dokter);	
			
			$kelompokPasien = RwtInapRecord::finder()->findByPk($noTransRwtInap)->penjamin;
			$stAsuransi = RwtInapRecord::finder()->findByPk($noTransRwtInap)->st_asuransi;
			
			$this->setViewState('kelompokPasien',$kelompokPasien);
			$this->setViewState('stAsuransi',$stAsuransi);
				
			$this->kelPasien->Text = KelompokRecord::finder()->findByPk($kelompokPasien)->nama;	
				
			if($kelompokPasien == '02')//Kelompok Penjamin Asuransi 
			{
				$perusAsuransi = RwtInapRecord::finder()->findByPk($noTransRwtInap)->perus_asuransi;	
				$this->perus->Text = PerusahaanRecord::finder()->findByPk($perusAsuransi)->nama;	
				$this->perusTxt->Display = 'Dynamic';
				$this->perus->Display = 'Dynamic';
			}
			else
			{
				$this->perus->Text ='';
				$this->perus->Display = 'None';
				$this->perusTxt->Display = 'None';
			}
			
			//$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->cariPasBtn->Enabled=false;
			$this->errMsg->Text='';	
			$this->firstPanel->Enabled = false;
			$this->jmlPanel->Enabled = true;
			$this->detailPanel->Enabled = true ;
			
			$this->detailNonInap();
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien Luar atau bebas karyawan
		{
			$noTransRwtBebas = $this->DDtrans->SelectedValue;	
			$this->setViewState('noTransRwtBebas',$noTransRwtBebas);
			
			$namaPasBebas = PasienLuarRecord::finder()->findByPk($noTransRwtBebas)->nama;
					
			$this->setViewState('nama',$namaPasBebas);
			$this->nama->Text = $namaPasBebas;	
			
			$this->notrans->Enabled=false;
			$this->cariPasBtn->Enabled=false;
			$this->errMsg->Text='';	
			$this->firstPanel->Enabled = false;
			$this->jmlPanel->Enabled = true;
			$this->detailPanel->Enabled = true ;
			
			$this->detailNonInap();
		}
    }	
	
	
	public function rekapBayar()
    {
		if($this->getViewState('tmpJml') || $this->getViewState('tmpJml')==0)
		{
			$jnsPasien = $this->modeInput->SelectedValue;
			$jmlBayar = $this->getViewState('tmpJml');
			$noTransRawat = $this->getViewState('noTransRawat');
			
			if($this->getViewState('nmTable'))//temp table transaksi apotik tunai
			{
				$nmTable = $this->getViewState('nmTable');
				$sql = "SELECT * FROM $nmTable  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					$jmlBayar += $row['total'];
				}
			}
			
			if($this->getViewState('nmTableLab'))//temp table transaksi apotik tunai
			{
				$nmTableLab = $this->getViewState('nmTableLab');
				$sql = "SELECT * FROM $nmTableLab  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					$jmlBayar += $row['total'];
				}
			}
			
			if($this->getViewState('nmTableRad'))//temp table transaksi apotik tunai
			{
				$nmTableRad = $this->getViewState('nmTableRad');
				$sql = "SELECT * FROM $nmTableRad  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					$jmlBayar += $row['total'];
				}
			}
			
			//$this->bindGridLab($jnsPasien,$noTransRawat);			
			//$this->bindGridRad($jnsPasien,$noTransRawat);	
			$this->bindGridFisio($jnsPasien,$noTransRawat);	
			$this->bindGridAmbulan($jnsPasien,$noTransRawat);				
			$this->bindGridApotik($jnsPasien,$noTransRawat);
			
			$this->setViewState('tmpJml2',$jmlBayar);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml2'),2,',','.');
			
			if($this->getViewState('stBayar2') != '1')
			{
				$this->cekPiutang($jnsPasien,$noTransRawat);
			}
		}
		
	}
	
	
	public function checkNoTransInap()
    {
		$cm = $this->notrans->Text;
		$noTrans = $this->DDtrans->SelectedValue;
		
		$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
		if($jnsTrans == '0') //Jika pasien rawat inap & pilih transaksi lab
		{	
			$sql="SELECT 
					SUM(harga) AS total	
				FROM 
					tbt_lab_penjualan_inap
				WHERE 
					no_trans='$noTrans'
					AND flag='0'
					AND st_bayar='1'";
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['total'];
				}
				else
				{
					$jmlHarga += 2 * $row['total'];
				}			
			}
			
			$jmlHargaAsli = $jmlHarga;
			$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			//$tmpPasien = $activeRec->findBySql($sql);
			$nmPas = PasienRecord::finder()->find('cm = ?',$cm)->nama;			
			$this->setViewState('nama',$nmPas);
			
			$this->nama->Text= $nmPas;	
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->cariPasBtn->Enabled=false;
			$this->errMsg->Text='';	
		}
		elseif($jnsTrans == '1') //Jika pasien rawat inap & pilih transaksi rad
		{
			$sql="SELECT 
					SUM(harga) AS total	
				FROM 
					tbt_rad_penjualan_inap
				WHERE 
					no_trans='$noTrans'
					AND flag='0'
					AND st_bayar='1'";
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['total'];
				}
				else
				{
					$jmlHarga += 2 * $row['total'];
				}			
			}
			
			$jmlHargaAsli = $jmlHarga;
			$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			//$tmpPasien = $activeRec->findBySql($sql);
			$nmPas = PasienRecord::finder()->find('cm = ?',$cm)->nama;			
			$this->setViewState('nama',$nmPas);
			
			$this->nama->Text= $nmPas;	
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->cariPasBtn->Enabled=false;
			$this->errMsg->Text='';	
		}
	}
	
	public function discChanged($sender,$param)
    {
		if($this->disc->text != '' && $this->disc->text != '0' && TPropertyValue::ensureFloat($this->disc->Text))
		{
			$totBiaya = TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$disc = $this->disc->text;
			
			$totBiayaDisc = $totBiaya - ($totBiaya * $disc / 100);
			$totBiayaDiscBulat = $this->bulatkan($totBiayaDisc);
			$sisaDiscBulat = $totBiayaDiscBulat - $totBiayaDisc;
			
			$this->jmlBiayaDisc->Text = 'Rp. ' . number_format($totBiayaDiscBulat,'2',',','.');
			
			$this->setViewState('stDisc','1');
			$this->setViewState('totBiayaDisc',$totBiayaDisc);
			$this->setViewState('totBiayaDiscBulat',$totBiayaDiscBulat);
			$this->setViewState('sisaDiscBulat',$sisaDiscBulat);
		}
		else
		{
			$this->jmlBiayaDisc->Text = '';
			$this->clearViewState('stDisc');
			$this->clearViewState('totBiayaDisc');
			$this->clearViewState('totBiayaDiscBulat');
			$this->clearViewState('sisaDiscBulat');
		}
		
		$this->Page->CallbackClient->focus($this->bayar);
	}
	
	
	public function caraBayarChanged($sender, $param)
	{
		$caraBayar = $this->caraBayar->SelectedValue;
		
		$this->noRef->Text = '';
		$this->noRef->Enabled = false;
		
		$this->noRefPanel->Display = 'None';
		
		if($caraBayar == '1') //Tunai
		{
			$this->bayar->Text = '';
			$this->sisaByr->Text= '';
			$this->bayar->Enabled = true;
			$this->Page->CallbackClient->focus($this->bayar);
		}
		else //Non Tunai
		{	
			if($caraBayar == '2' || $caraBayar == '3') //Debit Card / Credit Card
			{
				$this->noRef->Enabled = true;
				$this->Page->CallbackClient->focus($this->noRef);
				$this->noRefPanel->Display = 'Dynamic';
			}
			else
			{
				$this->Page->CallbackClient->focus($this->bayarBtn);
				$this->bayar->Text = $this->getViewState('tmpJml2');
			}
			
			
			$this->bayar->Enabled = true;
			
			$this->sisaByr->Text= 'Rp. 0,00';
			$this->setViewState('sisa','0');
		}
	}
	
	
	public function caraBayarChanged2($sender, $param)
	{
		$caraBayar = $this->caraBayar2->SelectedValue;
		
		$this->noRef2->Text = '';
		$this->noRef2->Enabled = false;
		
		$this->noRefPanel2->Display = 'None';
		
		if($caraBayar == '1') //Tunai
		{
			$this->bayar2->Text = '';
			$this->sisaByr2->Text= '';
			$this->bayar2->Enabled = true;
			$this->Page->CallbackClient->focus($this->bayar2);
		}
		else //Non Tunai
		{	
			if($caraBayar == '2' || $caraBayar == '3') //Debit Card / Credit Card
			{
				$this->noRef2->Enabled = true;
				$this->Page->CallbackClient->focus($this->noRef2);
				$this->noRefPanel2->Display = 'Dynamic';
				$this->bayar2->Text = $this->getViewState('tmpSisaJmlBayar1');
			}
			else
			{
				$this->Page->CallbackClient->focus($this->bayarBtn2);
				$this->bayar2->Text = $this->getViewState('tmpSisaJmlBayar1');
			}
			
			
			$this->bayar2->Enabled = false;
			
			$this->sisaByr2->Text= 'Rp. 0,00';
			$this->setViewState('sisa','0');
		}
		
	}
	
	
	public function bayarCallBack($sender,$param)
   	{
		$this->noTransPanel->render($param->getNewWriter());
		$this->pasBebasPanel->render($param->getNewWriter());
		$this->firstPanel->render($param->getNewWriter());
		$this->detailPanel->render($param->getNewWriter());
		$this->jmlPanel->render($param->getNewWriter());
		$this->jmlPanel2->render($param->getNewWriter());
	}
	
			
	public function bayarClicked($sender,$param)
    {
		//if($this->page->IsValid)
		//{
			if($this->bayar->Text >= $this->getViewState('tmpJml2'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml2'));
				$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->citoCheck->Enabled=false;			
				
				$this->sisaByrTxt->Text = 'Sisa/Kembalian';	
				$this->errByr->Text='';		
				$this->setViewState('stDelete','1');	
				$this->bayarBtn->Enabled=false;	
				$this->caraBayar->Enabled=false;
				$this->bayar->Enabled=false;	
				$this->DDtrans->Enabled=false;
				$this->modeInput->Enabled=false;
				$this->detailPanel->Enabled=false;
				$this->cetakBtn->Enabled=true;	
				$this->Page->CallbackClient->focus($this->cetakBtn);
				
				$this->jmlPanel2->Display = 'None';
				$this->setViewState('stBayarLunasLangsung','1');
			}
			else
			{
				$this->jmlPanel->Enabled = false;
				$this->jmlPanel2->Enabled = true;
				
				$caraBayar1 = $this->caraBayar->SelectedValue;
								
				$sql = "SELECT * FROM tbm_carabayar ORDER BY id";
				$arr = $this->queryAction($sql,'S');
				
				$this->caraBayar2->DataSource=$arr;
				$this->caraBayar2->dataBind();
				
				$jmlSisaBayar1 = $this->getViewState('tmpJml2') - $this->bayar->Text;
				
				$this->setViewState('tmpSisaJmlBayar1',$jmlSisaBayar1);
				$this->setViewState('stBayarLunasLangsung','0');
				
				$this->jmlShow2->Text = 'Rp. '.number_format($jmlSisaBayar1,2,',','.');				
				$this->Page->CallbackClient->focus($this->bayar2);
				
				$this->bayar2->Text = $jmlSisaBayar1;
				
				/*
				$this->errByr->Text='Jumlah pembayaran kurang';	
				$this->Page->CallbackClient->focus($this->bayar);
				*/
				$this->sisaByr->Text = 'Rp. '.number_format($jmlSisaBayar1,2,',','.');
				$this->sisaByrTxt->Text = 'Sisa Pembayaran';
				$this->cetakBtn->Enabled=false;
				$this->bayarBtn->Enabled=true;	
				$this->bayar->Enabled=true;
				$this->DDtrans->Enabled=true;
			}
		//}
	}
	
	public function bayarClicked2($sender,$param)
    {
		if($this->page->IsValid)
		{
			if($this->bayar2->Text >= $this->getViewState('tmpSisaJmlBayar1'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar2->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpSisaJmlBayar1'));
				$this->sisaByr2->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->citoCheck->Enabled=false;			
				
				$this->errByr2->Text='';		
				$this->setViewState('stDelete','1');
				$this->caraBayar2->Enabled=false;	
				$this->bayarBtn2->Enabled=false;
				$this->tundaBtn->Enabled=false;		
				$this->bayar2->Enabled=false;	
				$this->DDtrans->Enabled=false;
				$this->modeInput->Enabled=false;
				$this->detailPanel->Enabled=false;
				$this->cetakBtn->Enabled=true;	
				$this->Page->CallbackClient->focus($this->cetakBtn);
				
				$this->setViewState('stBayar2','1');
			}
			else
			{
				$this->errByr2->Text='Jumlah pembayaran kurang';	
				$this->Page->CallbackClient->focus($this->bayar2);
				$this->cetakBtn->Enabled=false;
				$this->bayarBtn2->Enabled=true;	
				$this->tundaBtn->Enabled=true;
				$this->bayar2->Enabled=true;
				$this->DDtrans->Enabled=true;
				$this->clearViewState('stBayar2');
			}
		}
	}
	
	public function tundaClicked()
    {	
		$tglNow = date('Y-m-d');
		$wktNow = date('G:i:s');
		
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$notransAsal = $this->getViewState('noTransRwtJln');
		}
		elseif($jnsPasien == '1') //Jika pasien rawat inap
		{
			$notransAsal = $this->getViewState('noTransRwtInap');
		}		
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$notransAsal = $this->getViewState('noTransRwtBebas');
		}
		
		$cm = $this->notrans->Text;
		
		$operator = $this->User->IsUserName;
		$nipTmp = $this->User->IsUserNip;	
		
		$caraBayar = $this->caraBayar->SelectedValue;
		$noRef = $this->noRef->Text;
		$jmlBayar = $this->bayar->Text;
		
		$notrans = $this->numCounter('tbt_kasir_rwtjln_angsuran',KasirRwtJlnAngsuranRecord::finder(),'40');
		
		$data = new KasirRwtJlnAngsuranRecord();
		$data->tipe_pasien = $jnsPasien;
		$data->cm = $cm;
		$data->no_trans = $notrans;
		$data->no_trans_asal = $notransAsal;
		$data->tgl = $tglNow;
		$data->waktu = $wktNow;
		$data->operator = $nipTmp;
		$data->tarif = $jmlBayar;
		$data->no_ref = $noRef;
		$data->st='0';
		$data->st_carabayar = $caraBayar;
		$data->Save();
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			//Update st_piutang tbt_rawat_jalan
			$sql="UPDATE 
					tbt_rawat_jalan 
				  SET 
					st_piutang='1'
				  WHERE 
					no_trans='$notransAsal' 
					AND flag='0' ";
				
			$this->queryAction($sql,'C');
			
			
			//Update tbt_kasir_rwtjln  jika ada diskon
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				$sql = "SELECT * FROM $nmTable  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					$total = $row['total'];
					$disc = $row['disc'];
					$id_tindakan = $row['id_tindakan'];
					
					$sql = "UPDATE 
							tbt_kasir_rwtjln
						SET 
							total = '$total',
							disc = '$disc'
						WHERE 
						no_trans_rwtjln = '$notransRwtjln'
						AND st_flag = 0
						AND id_tindakan = '$id_tindakan'";	
					$this->queryAction($sql,'C');
				}
			}
		}
		elseif($jnsPasien == '1') //Jika pasien rawat inap
		{
			//Update st_piutang tbt_rawat_inap
			$sql="UPDATE 
					tbt_rawat_inap
				  SET 
					st_piutang='1'
				  WHERE 
					no_trans='$notransAsal' 
					AND status='0' ";
				
			$this->queryAction($sql,'C');
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			//Update st_piutang tbd_pasien_luar
			$sql="UPDATE 
					tbd_pasien_luar 
				  SET 
					st_piutang='1'
				  WHERE 
					no_trans='$notransAsal' ";
				
			$this->queryAction($sql,'C');
		}
		
		
		$this->batalClicked();
	}
	
	
	public function detailClicked()
    {
		$this->detailPanel->Visible = true ;		
		//$this->detailNoTrans->Text=$this->getViewState('noTransRwtJln');	
		
		if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
		{
			$this->detailNonInap();
		}
		else //jika checkbox rawat inap dipilih
		{
			$this->detailInap();
		}
	}


//------------------------------------- DATAGRID TINDAKAN RAWAT JALAN ------------------------------------------		
	protected function dtgSomeData_EditCommand($sender,$param)
    {
        $this->admRwtJlnGrid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $nmTable = $this->getViewState('nmTable');
		$disc = $item->discColumn->TextBox->Text;
		$bebanAsuransi = $item->bebanAsuransiColumn->TextBox->Text;
		$idTabel = $this->admRwtJlnGrid->DataKeys[$item->ItemIndex];
		
		
		$sql = "SELECT *
						FROM 
							$nmTable 
						WHERE 
							id='$idTabel' ";
							
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totalAwal = $row['tarif'];
		}
		
		
		if(floatval($disc + $bebanAsuransi) <= $totalAwal )
		{
			$totalAkhir = $totalAwal - ($disc + $bebanAsuransi);
			
			$sql = "UPDATE $nmTable SET total = '$totalAkhir', disc='$disc', beban_asuransi='$bebanAsuransi' WHERE id = '$idTabel' ";
			$this->queryAction($sql,'C');
			
			$this->showSql->Text = $sql;
		}
		else
		{
			$this->msg->Text = '    
			<script type="text/javascript">
				alert("Jumlah discount dan Beban Asuransi TIndakan Rawat Jalan yang dimasukan melebihi jumlah bayar !");
			</script>';
		}
		
		$this->msg->Text = '';
		
		// clear data in session because we need to refresh it from db
		// you could also modify the data in session and not clear the data from session!
		$session = $this->getSession();
		$session->remove("SomeData");  

        $this->admRwtJlnGrid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function itemCreated($sender,$param)
    {
		$item=$param->Item;
		if($item->ItemType==='EditItem')
        {
		   $item->discColumn->TextBox->Columns=6;
		   $item->bebanAsuransiColumn->TextBox->Columns=6;
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem')
		{	
			//jika pasien asuransi/jamper dan statusnya berlaku
			if(($this->getViewState('kelompokPasien') == '02' || $this->getViewState('kelompokPasien') == '07') && $this->getViewState('stAsuransi') == '1')
			{
				$this->admRwtJlnGrid->Columns[3]->Visible = true;
			}	
			else
			{
				$this->admRwtJlnGrid->Columns[3]->Visible = false;
			}
			
		}
	}
	
    protected function dtgSomeData_CancelCommand($sender,$param)
    {
        $this->admRwtJlnGrid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function bindGrid()
    {
		$nmTable = $this->getViewState('nmTable');
		$sql = "SELECT  * FROM $nmTable  ";
		$arrData=$this->queryAction($sql,'S');
		if(count($arrData) > 0)
		{
			$this->admRwtJlnGrid->Visible = true;
			$this->admRwtJlnGrid->DataSource=$arrData;
			$this->admRwtJlnGrid->dataBind();
			$this->tdkMsg->Text = '';
		}
		else
		{
			$this->admRwtJlnGrid->Visible = false;
			$this->tdkMsg->Text = 'Tidak Ada Transaksi Tindakan Rawat Jalan';
		}
	}
	
	
	public function bindGridTdkRwtJln($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$cm=$this->notrans->Text;
			$dateNow = date('Y-m-d');
			//data untuk datagrid detail tindakan rwtjln
			if($this->citoCheck->Checked==false)
			{
				$sql = "SELECT
							tgl, nama,tarif, total,disc,no_trans_asal,no_trans,id_tindakan
						FROM 
							view_biaya_total_rwtjln 
						WHERE 
							cm='$cm' 
							AND no_trans='$notrans'
							AND flag = '0'
							AND tgl = '$dateNow' ";
			}
			else
			{
				$sql = "SELECT
							tgl, nama, (2 * tarif) AS tarif, (2 * total) AS total,disc,no_trans_asal,no_trans,id_tindakan
						FROM 
							view_biaya_total_rwtjln 
						WHERE 
							cm='$cm' 
							AND no_trans='$notrans'
							AND flag = '0' 
							AND tgl = '$dateNow' ";
			}
			
			$sqlTdk = $sql . " AND jns_trans = 'tindakan rawat jalan'";
			$arrData=$this->queryAction($sqlTdk,'S');//Select row in tabel bro...
			if(count($arrData) > 0)
			{
				foreach($arrData as $row)
				{
					$jmlTindakanRwtJln += $row['total'];
					
					if(!$this->getViewState('nmTable'))
					{
						$nmTable = $this->setNameTable('nmTable');
						$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
													 nama VARCHAR(70) NOT NULL,
													 tgl date NOT NULL,	
													 tarif float(15,0) default NULL,
													 total float(15,0) default NULL,
													 disc float(15,0) default '0',	
													 beban_asuransi float(15,0) default '0',	
													 no_trans_asal VARCHAR(20) NOT NULL,
													 no_trans VARCHAR(20) NOT NULL,
													 id_tindakan VARCHAR(20) NOT NULL,						 							 
													 PRIMARY KEY (id)) ENGINE = MEMORY";
						
						$this->queryAction($sql,'C');//Create new tabel bro...						
					}
					else
					{
						$nmTable = $this->getViewState('nmTable');
					}
					
					$nama = $row['nama'];
					$tgl = $row['tgl'];
					$tarif = $row['tarif'];
					$total = $row['total'];
					$disc = $row['disc'];
					$no_trans_asal = $row['no_trans_asal'];
					$no_trans = $row['no_trans'];
					$id_tindakan = $row['id_tindakan'];
					
					$sql="INSERT INTO $nmTable (nama,tgl,tarif,total,disc,no_trans_asal,no_trans,id_tindakan) VALUES ('$nama','$tgl','$tarif','$total','$disc','$no_trans_asal','$no_trans','$id_tindakan')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				
				}
				
				$sql = "SELECT  * FROM $nmTable  ";
				$arrData=$this->queryAction($sqlTdk,'S');
				
				$this->admRwtJlnGrid->Visible = true;
				$this->admRwtJlnGrid->DataSource=$arrData;
				$this->admRwtJlnGrid->dataBind();
				$this->tdkMsg->Text = '';
			}
			else
			{
				$jmlTindakanRwtJln = 0 ;
				$this->admRwtJlnGrid->Visible = false;
				$this->tdkMsg->Text = 'Tidak Ada Transaksi Tindakan Rawat Jalan';
			}
		}
	}
	
	

//------------------------------------- DATAGRID APOTIK ------------------------------------------	
	public function bindGridApotik($jnsPasien,$notrans)
    {
		$cm=$this->notrans->Text;
		$kelompokPasien = $this->getViewState('kelompokPasien');
		$stAsuransi = $this->getViewState('stAsuransi');
			
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$idKlinik = $this->getViewState('idKlinik');
			
			//$stAsuransi = RwtjlnRecord::finder()->findByPk($notrans)->st_asuransi;
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan & st_asuransi berlaku
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl, id_kel_racik, st_imunisasi, id_kel_imunisasi
							 FROM 
								tbt_obat_jual_karyawan 
							 WHERE 
								no_trans_rwtjln='$notrans'
								AND flag = '0'
								AND klinik = '$idKlinik'
							  ORDER BY st_racik, id_kel_racik, id_kel_imunisasi ";
			
				$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual_karyawan WHERE no_trans_rwtjln='$notrans' AND bhp <> '0' AND flag = '0' AND klinik = '$idKlinik' ORDER BY id";				
				
			}
			else
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl, id_kel_racik, st_imunisasi, id_kel_imunisasi
							 FROM 
								tbt_obat_jual 
							 WHERE 
								no_trans_rwtjln='$notrans'
								AND flag = '0'
								AND klinik = '$idKlinik'
							  ORDER BY st_racik, id_kel_racik, id_kel_imunisasi ";
				
				$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual  WHERE no_trans_rwtjln='$notrans' AND bhp <> '0' AND flag = '0' AND klinik = '$idKlinik'  ORDER BY id";
			}
			
			$arrData=$this->queryAction($sqlApotik,'S');//Select row in tabel bro...
			if(count($arrData) > 0)
			{
				foreach($arrData as $row)
				{			
					if($this->citoCheck->Checked==false)
					{
						$jmlHargaApotik += $row['total'];
					}
					else
					{
						$jmlHargaApotik += 2 * $row['total'];
					}			
				}
				
				$this->apotikRwtJlnGrid->Visible = true;
				$this->apotikRwtJlnGrid->DataSource=$arrData;
				$this->apotikRwtJlnGrid->dataBind();
				$this->apotikMsg->Text = '';
			}
			else
			{
				$jmlHargaApotik = 0;
				$this->apotikRwtJlnGrid->Visible = false;
				$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';			
			}
			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			//$stAsuransi = RwtInapRecord::finder()->findByPk($notrans)->st_asuransi;
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl, id_kel_racik, st_imunisasi, id_kel_imunisasi
							 FROM 
								tbt_obat_jual_inap_karyawan 
							 WHERE 
								no_trans_inap='$notrans'
								AND flag = '0'
								AND st_bayar = '1'
							  ORDER BY st_racik, id_kel_racik, id_kel_imunisasi "; //transaksi apotik tunai krn st_bayar = 1
				
				$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual_inap_karyawan WHERE no_trans_inap='$notrans' AND bhp <> '0' AND flag = '0' ORDER BY id";
			}
			else
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl, id_kel_racik, st_imunisasi, id_kel_imunisasi
							 FROM 
								tbt_obat_jual_inap 
							 WHERE 
								no_trans_inap='$notrans'
								AND flag = '0'
								AND st_bayar = '1'
							  ORDER BY st_racik, id_kel_racik, id_kel_imunisasi "; //transaksi apotik tunai krn st_bayar = 1
				
				$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual_inap WHERE no_trans_inap='$notrans' AND bhp <> '0' AND flag = '0' ORDER BY id";
			}
			
			$arrData=$this->queryAction($sqlApotik,'S');//Select row in tabel bro...
			if(count($arrData) > 0)
			{
				foreach($arrData as $row)
				{			
					if($this->citoCheck->Checked==false)
					{
						$jmlHargaApotik += $row['total'];
					}
					else
					{
						$jmlHargaApotik += 2 * $row['total'];
					}			
				}
				
				$this->apotikRwtJlnGrid->Visible = true;
				$this->apotikRwtJlnGrid->DataSource=$arrData;
				$this->apotikRwtJlnGrid->dataBind();
				$this->apotikMsg->Text = '';
			}
			else
			{
				$jmlHargaApotik = 0;
				$this->apotikRwtJlnGrid->Visible = false;
				$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';			
			}	
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$jnsTrans = $this->collectSelectionResult($this->jnsTransBebas);
			if($jnsTrans == '1') //Transaksi Apotik
			{
				$notrans = $this->getViewState('noTransRwtBebas');
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl, id_kel_racik, st_imunisasi, id_kel_imunisasi
							 FROM ";
					if($jnsPasien == '2')//Pasien bebas
					{
						$sqlApotik .= " tbt_obat_jual_lain ";
						
						$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual_lain WHERE no_trans_pas_luar = '$notrans' AND bhp <> '0' AND flag = '0' ORDER BY id";
					}
					elseif($jnsPasien == '3')//Pasien bebas karyawan
					{
						$sqlApotik .= " tbt_obat_jual_lain_karyawan ";
						
						$sqlBhp="SELECT id,tgl,id_bhp,bhp FROM tbt_obat_jual_lain_karyawan WHERE no_trans_pas_luar = '$notrans' AND bhp <> '0' AND flag = '0' ORDER BY id";
					}
					
					$sqlApotik .= "	WHERE 
								no_trans_pas_luar = '$notrans'
								AND flag = '0'
							  ORDER BY st_racik, id_kel_racik, id_kel_imunisasi ";
							  
				$arrData=$this->queryAction($sqlApotik,'S');//Select row in tabel bro...
				if(count($arrData) > 0)
				{
					foreach($arrData as $row)
					{			
						if($this->citoCheck->Checked==false)
						{
							$jmlHargaApotik += $row['total'];
						}
						else
						{
							$jmlHargaApotik += 2 * $row['total'];
						}			
					}
										
					$this->apotikRwtJlnGrid->Visible = true;
					$this->apotikRwtJlnGrid->DataSource=$arrData;
					$this->apotikRwtJlnGrid->dataBind();
					$this->apotikMsg->Text = '';
				}
				else
				{
					$jmlHargaApotik = 0;
					$this->apotikRwtJlnGrid->Visible = false;
					$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';			
				}
				
				$arrBhp = $this->queryAction($sqlBhp,'S');
				if(count($arrBhp) > 0)
				{
					foreach($arrBhp as $rowBhp)
					{			
						if($this->citoCheck->Checked==false)
						{
							$jmlHargaBhp += $rowBhp['bhp'];
						}
						else
						{
							$jmlHargaBhp += 2 * $rowBhp['bhp'];
						}			
					}
					
					$this->apotikBhpRwtJlnGrid->Visible = true;
					$this->apotikBhpRwtJlnGrid->DataSource=$arrBhp;
					$this->apotikBhpRwtJlnGrid->dataBind();
				}
				else
				{
					$jmlHargaBhp = 0;
					$this->apotikBhpRwtJlnGrid->Visible = false;
				}			  
			}
			else
			{
				$jmlHargaApotik = 0;
				$jmlHargaBhp = 0;
				$this->apotikRwtJlnGrid->Visible = false;
				$this->apotikBhpRwtJlnGrid->Visible = false;
				$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';
			}
		}
		
		if($jnsPasien != '2' && $jnsPasien != '3') //pasien bebas atau bebas karyawan
		{
			$arrBhp = $this->queryAction($sqlBhp,'S');
			if(count($arrBhp) > 0)
			{
				foreach($arrBhp as $rowBhp)
				{			
					if($this->citoCheck->Checked==false)
					{
						$jmlHargaBhp += $rowBhp['bhp'];
					}
					else
					{
						$jmlHargaBhp += 2 * $rowBhp['bhp'];
					}			
				}
				
				$this->apotikBhpRwtJlnGrid->Visible = true;
				$this->apotikBhpRwtJlnGrid->DataSource=$arrBhp;
				$this->apotikBhpRwtJlnGrid->dataBind();
			}
			else
			{
				$jmlHargaBhp = 0;
				$this->apotikBhpRwtJlnGrid->Visible = false;
			}
		}
		
		$jmlHargaApotik = $jmlHargaApotik + $jmlHargaBhp;
		
		$this->setViewState('jmlHargaApotik',$jmlHargaApotik);	
	}
	


//------------------------------------- DATAGRID AMBULAN ------------------------------------------		
	public function bindGridAmbulan($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$dateNow = date('Y-m-d');
			
			$sql = "SELECT
						tbt_rwtjln_ambulan.id AS id,
						tbt_rwtjln_ambulan.tgl AS tgl,
						tbm_ambulan.nama AS tujuan,
						CONCAT(tbt_rwtjln_ambulan.catatan,'  ',tbt_rwtjln_ambulan.lainnya) AS catatan, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_rwtjln_ambulan.tarif AS tarif ";}
			else{$sql .= "(2 * tbt_rwtjln_ambulan.tarif) AS tarif ";}	
			
			$sql .= "FROM
						tbm_ambulan
						Inner Join tbt_rwtjln_ambulan ON tbt_rwtjln_ambulan.tujuan = tbm_ambulan.id
					WHERE 
						tbt_rwtjln_ambulan.no_trans_rwtjln = '$notrans'
						AND tbt_rwtjln_ambulan.flag = '0'
						AND tgl = '$dateNow' ";
			
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			if(count($arrData) > 0)
			{
				foreach($arrData as $row)
				{	
					$jmlHargaAmbulan += $row['tarif'];
				}
				
				$this->ambulanRwtJlnGrid->Visible = true;
				$this->ambulanRwtJlnGrid->DataSource=$arrData;
				$this->ambulanRwtJlnGrid->dataBind();
				$this->ambulanMsg->Text = '';
			}
			else
			{
				$jmlHargaAmbulan = 0;
				$this->ambulanRwtJlnGrid->Visible = false;
				$this->ambulanMsg->Text = 'Tidak Ada Transaksi Ambulan';			
			}	
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			
		}
		elseif($jnsPasien == '2') //pasien bebas
		{
			
		}
		
		$this->setViewState('jmlHargaAmbulan',$jmlHargaAmbulan);	
	}


//------------------------------------- DATAGRID LAB ------------------------------------------	
	protected function labGrid_EditCommand($sender,$param)
    {
        $this->labGrid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridLabTmp();
    }

    protected function labGrid_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $nmTableLab = $this->getViewState('nmTableLab');
		$disc = $item->discLabColumn->TextBox->Text;
		$bebanAsuransi = $item->bebanAsuransiLabColumn->TextBox->Text;
		$idTabel = $this->labGrid->DataKeys[$item->ItemIndex];
		
		$sql = "SELECT *
						FROM 
							$nmTableLab 
						WHERE 
							id='$idTabel' ";
							
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totalAwal = $row['tarif'];
		}
		
		
		if(floatval($disc + $bebanAsuransi) <= $totalAwal )
		{
			$totalAkhir = $totalAwal - ($disc + $bebanAsuransi);
			
			$sql = "UPDATE $nmTableLab SET total = '$totalAkhir', disc='$disc', beban_asuransi='$bebanAsuransi' WHERE id = '$idTabel' ";
			$this->queryAction($sql,'C');
			
			$this->showSql->Text = $sql;
		}
		else
		{
			$this->msg->Text = '    
			<script type="text/javascript">
				alert("Jumlah discount dan Beban Asuransi Laboratorium yang dimasukan melebihi jumlah bayar !");
			</script>';
		}
		
		$this->msg->Text = '';
		
		// clear data in session because we need to refresh it from db
		// you could also modify the data in session and not clear the data from session!
		$session = $this->getSession();
		$session->remove("SomeData");  

        $this->labGrid->EditItemIndex=-1;
        $this->bindGridLabTmp();
    }
	
	public function labGrid_itemCreated($sender,$param)
    {
		$item=$param->Item;
		if($item->ItemType==='EditItem')
        {
		   $item->discLabColumn->TextBox->Columns=6;
		   $item->bebanAsuransiLabColumn->TextBox->Columns=6;
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem')
		{	
			//jika pasien asuransi/jamper dan statusnya berlaku
			if(($this->getViewState('kelompokPasien') == '02' || $this->getViewState('kelompokPasien') == '07') && $this->getViewState('stAsuransi') == '1')
			{
				$this->labGrid->Columns[5]->Visible = true;
			}	
			else
			{
				$this->labGrid->Columns[5]->Visible = false;
			}
			
		}
	}
	
    protected function labGrid_CancelCommand($sender,$param)
    {
        $this->labGrid->EditItemIndex=-1;
        $this->bindGridLabTmp();
    }
	
	public function bindGridLabTmp()
    {
		$nmTableLab = $this->getViewState('nmTableLab');
		$sql = "SELECT  * FROM $nmTableLab  ";
		$arrData=$this->queryAction($sql,'S');
		if(count($arrData) > 0)
		{
			$this->labGrid->Visible = true;
			$this->labGrid->DataSource = $arrData;
			$this->labGrid->dataBind();
			$this->labMsg->Text = '';
		}
		else
		{
			$jmlTindakanLab = 0 ;
			$this->labGrid->Visible = false;
			$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
		}
	}
			
	public function bindGridLab($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sqlTdk = "SELECT 
					  tbt_rawat_jalan.no_trans AS no_trans_asal,
					  tbt_lab_penjualan.no_trans,
					  tbt_lab_penjualan.id_tindakan,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sqlTdk .= "tbt_lab_penjualan.harga ";}
			else{$sqlTdk .= "(2 * tbt_lab_penjualan.harga) AS harga ";}	
			
			$sqlTdk .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_lab_penjualan ON (tbt_rawat_jalan.no_trans = tbt_lab_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$notrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sqlTdk,'S');
			if(count($arr) > 0) //Jika ada data tindakan lab
			{
				foreach($arr as $row)
				{				
					$jmlTindakanLab += $row['harga'];	
					
					if(!$this->getViewState('nmTableLab'))
					{
						$nmTableLab = $this->setNameTable('nmTableLab');
						$sql="CREATE TABLE $nmTableLab (id INT (2) auto_increment, 
													 no_trans VARCHAR(20) NOT NULL,
													 no_trans_asal VARCHAR(20) NOT NULL,
													 nm_tdk VARCHAR(100) NOT NULL,
													 id_tindakan VARCHAR(20) NOT NULL,
													 kel_tdk VARCHAR(30) NOT NULL,
													 kateg_tdk VARCHAR(50) NOT NULL,
													 tarif float(15,0) default NULL,
													 total float(15,0) default NULL,
													 disc float(15,0) default '0',	
													 beban_asuransi float(15,0) default '0',
													 
													 PRIMARY KEY (id)) ENGINE = MEMORY";
						
						$this->queryAction($sql,'C');//Create new tabel bro...						
					}
					else
					{
						$nmTableLab = $this->getViewState('nmTableLab');
					}
					
					$nm_tdk = $row['nm_tdk'];
					$kel_tdk = $row['kel_tdk'];
					$kateg_tdk = $row['kateg_tdk'];
					$id_tindakan = $row['id_tindakan'];
					$tarif = $row['harga'];
					$total = $row['harga'];
					$no_trans_asal = $row['no_trans_asal'];
					$no_trans = $row['no_trans'];
					
					$sql="INSERT INTO $nmTableLab (nm_tdk,kel_tdk,kateg_tdk,id_tindakan,tarif,total,disc,no_trans_asal,no_trans) VALUES ('$nm_tdk','$kel_tdk','$kateg_tdk','$id_tindakan','$tarif','$total','$disc','$no_trans_asal','$no_trans')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
				$this->setViewState('jmlTindakanLab',$jmlTindakanLab);
				
				$sql = "SELECT  * FROM $nmTableLab  ";
				$arrData=$this->queryAction($sqlTdk,'S');
				
				$this->labGrid->Visible = true;
				$this->labGrid->DataSource = $arrData;
				$this->labGrid->dataBind();
				$this->labMsg->Text = '';
			}
			else
			{
				$jmlTindakanLab = 0 ;
				$this->labGrid->Visible = false;
				$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{	
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbt_lab_penjualan_inap.id_tindakan,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_lab_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan_inap.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_lab_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_lab_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					  INNER JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  tbt_rawat_inap.no_trans = '$notrans'
					  AND tbt_lab_penjualan_inap.st_bayar = '1'
					  AND tbt_rawat_inap.status = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan lab
			{
				foreach($arr as $row)
				{				
					$jmlTindakanLab += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanLab',$jmlTindakanLab);
				
				$this->labGrid->Visible = true;
				$this->labGrid->DataSource = $arr;
				$this->labGrid->dataBind();
				$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
			}
			else
			{
				$jmlTindakanLab = 0 ;
				$this->labGrid->Visible = false;
				$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
			}
		}
		elseif($jnsPasien == '2') //pasien Luar
		{	
			$sql = "SELECT 
					  tbd_pasien_luar.no_trans,
					  tbt_lab_penjualan_lain.id_tindakan,
					  tbm_lab_kelompok.nama AS kel_tdk,
					  tbm_lab_kategori.jenis AS kateg_tdk,
					  tbm_lab_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_lab_penjualan_lain.harga ";}
			else{$sql .= "(2 * tbt_lab_penjualan_lain.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_lab_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_lab_penjualan_lain.no_trans_pas_luar)
					  LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
					  LEFT JOIN tbm_lab_kelompok ON (tbm_lab_tindakan.kelompok = tbm_lab_kelompok.kode)
					  LEFT OUTER JOIN tbm_lab_kategori ON (tbm_lab_tindakan.kategori = tbm_lab_kategori.kode)
					WHERE
					  tbd_pasien_luar.no_trans = '$notrans'
					  AND tbt_lab_penjualan_lain.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan lab
			{
				foreach($arr as $row)
				{				
					$jmlTindakanLab += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanLab',$jmlTindakanLab);
				
				$this->labGrid->Visible = true;
				$this->labGrid->DataSource = $arr;
				$this->labGrid->dataBind();
				$this->labMsg->Text = '';
			}
			else
			{
				$jmlTindakanLab = 0 ;
				$this->labGrid->Visible = false;
				$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
			}
		}
	}


//------------------------------------- DATAGRID RAD ------------------------------------------		
	protected function radGrid_EditCommand($sender,$param)
    {
        $this->radGrid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridRadTmp();
    }

    protected function radGrid_UpdateCommand($sender,$param)
    {
        $item = $param->Item;
		
        // implement doSomeValidation() if you need to check the modified data before you commit the changes
        $nmTableRad = $this->getViewState('nmTableRad');
		$disc = $item->discRadColumn->TextBox->Text;
		$bebanAsuransi = $item->bebanAsuransiRadColumn->TextBox->Text;
		$idTabel = $this->radGrid->DataKeys[$item->ItemIndex];
		
		$sql = "SELECT *
						FROM 
							$nmTableRad 
						WHERE 
							id='$idTabel' ";
							
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totalAwal = $row['tarif'];
		}
		
		
		if(floatval($disc + $bebanAsuransi) <= $totalAwal )
		{
			$totalAkhir = $totalAwal - ($disc + $bebanAsuransi);
			
			$sql = "UPDATE $nmTableRad SET total = '$totalAkhir', disc='$disc', beban_asuransi='$bebanAsuransi' WHERE id = '$idTabel' ";
			$this->queryAction($sql,'C');
			
			$this->showSql->Text = $sql;
		}
		else
		{
			$this->msg->Text = '    
			<script type="text/javascript">
				alert("Jumlah discount dan Beban Asuransi Radiologi yang dimasukan melebihi jumlah bayar !");
			</script>';
		}
		
		$this->msg->Text = '';
		
		// clear data in session because we need to refresh it from db
		// you could also modify the data in session and not clear the data from session!
		$session = $this->getSession();
		$session->remove("SomeData");  

        $this->radGrid->EditItemIndex=-1;
        $this->bindGridRadTmp();
    }
	
	public function radGrid_itemCreated($sender,$param)
    {
		$item=$param->Item;
		if($item->ItemType==='EditItem')
        {
		   $item->discRadColumn->TextBox->Columns=3;
		   $item->bebanAsuransiRadColumn->TextBox->Columns=3;
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem')
		{	
			//jika pasien asuransi/jamper dan statusnya berlaku
			if(($this->getViewState('kelompokPasien') == '02' || $this->getViewState('kelompokPasien') == '07') && $this->getViewState('stAsuransi') == '1')
			{
				$this->radGrid->Columns[6]->Visible = true;
			}	
			else
			{
				$this->radGrid->Columns[6]->Visible = false;
			}
			
		}
	}
	
    protected function radGrid_CancelCommand($sender,$param)
    {
        $this->radGrid->EditItemIndex=-1;
        $this->bindGridRadTmp();
    }
	
	public function bindGridRadTmp()
    {
		$nmTableRad = $this->getViewState('nmTableRad');
		$sql = "SELECT  * FROM $nmTableRad  ";
		$arrData=$this->queryAction($sql,'S');
		if(count($arrData) > 0)
		{
			$this->radGrid->Visible = true;
			$this->radGrid->DataSource = $arrData;
			$this->radGrid->dataBind();
			$this->radMsg->Text = '';
		}
		else
		{
			$jmlTindakanRad = 0 ;
			$this->radGrid->Visible = false;
			$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
		}
	}
		
	public function bindGridRad($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans AS no_trans_asal,
					  tbt_rad_penjualan.no_trans,
					  tbt_rad_penjualan.id_tindakan,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan.film_size, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_rad_penjualan.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_rad_penjualan ON (tbt_rawat_jalan.no_trans = tbt_rad_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$notrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan rad
			{
				foreach($arr as $row)
				{				
					$jmlTindakanRad += $row['harga'];	
					
					if(!$this->getViewState('nmTableRad'))
					{
						$nmTableRad = $this->setNameTable('nmTableRad');
						$sql="CREATE TABLE $nmTableRad (id INT (2) auto_increment, 
													 no_trans VARCHAR(20) NOT NULL,
													 no_trans_asal VARCHAR(20) NOT NULL,
													 nm_tdk VARCHAR(100) NOT NULL,
													 id_tindakan VARCHAR(20) NOT NULL,
													 kel_tdk VARCHAR(30) NOT NULL,
													 kateg_tdk VARCHAR(50) NOT NULL,
													 film_size VARCHAR(30) NOT NULL,
													 tarif float(15,0) default NULL,
													 total float(15,0) default NULL,
													 disc float(15,0) default '0',	
													 beban_asuransi float(15,0) default '0',
													 
													 PRIMARY KEY (id)) ENGINE = MEMORY";
						
						$this->queryAction($sql,'C');//Create new tabel bro...						
					}
					else
					{
						$nmTableRad = $this->getViewState('nmTableRad');
					}
					
					$nm_tdk = $row['nm_tdk'];
					$kel_tdk = $row['kel_tdk'];
					$kateg_tdk = $row['kateg_tdk'];
					$id_tindakan = $row['id_tindakan'];
					$film_size = $row['film_size'];
					$tarif = $row['harga'];
					$total = $row['harga'];
					$no_trans_asal = $row['no_trans_asal'];
					$no_trans = $row['no_trans'];
					
					$sql="INSERT INTO $nmTableRad (nm_tdk,kel_tdk,kateg_tdk,id_tindakan,film_size,tarif,total,disc,no_trans_asal,no_trans) VALUES ('$nm_tdk','$kel_tdk','$kateg_tdk','$id_tindakan','$film_size','$tarif','$total','$disc','$no_trans_asal','$no_trans')";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
				$this->setViewState('jmlTindakanRad',$jmlTindakanRad);
				
				$sql = "SELECT  * FROM $nmTableRad  ";
				$arrData=$this->queryAction($sql,'S');
				
				$this->setViewState('jmlTindakanRad',$jmlTindakanRad);
				
				$this->radGrid->Visible = true;
				$this->radGrid->DataSource = $arrData;
				$this->radGrid->dataBind();
				$this->radMsg->Text = '';
			}
			else
			{
				$jmlTindakanRad = 0 ;
				$this->radGrid->Visible = false;
				$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{	
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbm_rad_tindakan.nama AS nm_tdk,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,  
					  tbt_rad_penjualan_inap.film_size, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_rad_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan_inap.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_rad_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_rad_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_inap.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  tbt_rawat_inap.no_trans = '$notrans'
					  AND tbt_rad_penjualan_inap.st_bayar = '1'
					  AND tbt_rawat_inap.status = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan rad
			{
				foreach($arr as $row)
				{				
					$jmlTindakanRad += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanRad',$jmlTindakanRad);
				
				$this->radGrid->Visible = true;
				$this->radGrid->DataSource = $arr;
				$this->radGrid->dataBind();
				$this->radMsg->Text = '';
			}
			else
			{
				$jmlTindakanRad = 0 ;
				$this->radGrid->Visible = false;
				$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
			}
		}
		elseif($jnsPasien == '2') //pasien Luar
		{	
			$sql = "SELECT 
					  tbd_pasien_luar.no_trans,
					  tbt_rad_penjualan_lain.id_tindakan,
					  tbm_rad_kelompok.nama AS kel_tdk,
					  tbm_rad_kategori.jenis AS kateg_tdk,
					  tbm_rad_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_rad_penjualan_lain.harga ";}
			else{$sql .= "(2 * tbt_rad_penjualan_lain.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_rad_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_rad_penjualan_lain.no_trans_pas_luar)
					  LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_lain.id_tindakan = tbm_rad_tindakan.kode)
					  LEFT JOIN tbm_rad_kelompok ON (tbm_rad_tindakan.kelompok = tbm_rad_kelompok.kode)
					  LEFT OUTER JOIN tbm_rad_kategori ON (tbm_rad_tindakan.kategori = tbm_rad_kategori.kode)
					WHERE
					  tbd_pasien_luar.no_trans = '$notrans'
					  AND tbt_rad_penjualan_lain.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan rad
			{
				foreach($arr as $row)
				{				
					$jmlTindakanRad += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanRad',$jmlTindakanRad);
				
				$this->radGrid->Visible = true;
				$this->radGrid->DataSource = $arr;
				$this->radGrid->dataBind();
				$this->radMsg->Text = '';
			}
			else
			{
				$jmlTindakanRad = 0 ;
				$this->radGrid->Visible = false;
				$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
			}
		}
	}


//------------------------------------- DATAGRID FISIO ------------------------------------------		
	public function bindGridFisio($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sql = "SELECT 
					  tbt_rawat_jalan.no_trans,
					  tbt_fisio_penjualan.id_tindakan,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_fisio_penjualan.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_jalan
					  INNER JOIN tbt_fisio_penjualan ON (tbt_rawat_jalan.no_trans = tbt_fisio_penjualan.no_trans_rwtjln)
					  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan.id_tindakan = tbm_fisio_tindakan.kode)
					  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  tbt_rawat_jalan.no_trans = '$notrans'
					  AND tbt_rawat_jalan.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan fisio
			{
				foreach($arr as $row)
				{				
					$jmlTindakanFisio += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanFisio',$jmlTindakanFisio);
				
				$this->fisioGrid->Visible = true;
				$this->fisioGrid->DataSource = $arr;
				$this->fisioGrid->dataBind();
				$this->fisioMsg->Text = '';
			}
			else
			{
				$jmlTindakanFisio = 0 ;
				$this->fisioGrid->Visible = false;
				$this->fisioMsg->Text = 'Tidak Ada Transaksi Fisio';
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{	
			$sql = "SELECT 
					  tbt_rawat_inap.no_trans,
					  tbt_fisio_penjualan_inap.id_tindakan,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_fisio_penjualan_inap.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan_inap.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_fisio_penjualan_inap ON (tbt_rawat_inap.no_trans = tbt_fisio_penjualan_inap.no_trans_inap)
					  INNER JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_inap.id_tindakan = tbm_fisio_tindakan.kode)
					  INNER JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  tbt_rawat_inap.no_trans = '$notrans'
					  AND tbt_fisio_penjualan_inap.st_bayar = '1'
					  AND tbt_rawat_inap.status = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan fisio
			{
				foreach($arr as $row)
				{				
					$jmlTindakanFisio += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanFisio',$jmlTindakanFisio);
				
				$this->fisioGrid->Visible = true;
				$this->fisioGrid->DataSource = $arr;
				$this->fisioGrid->dataBind();
				$this->fisioMsg->Text = '';
			}
			else
			{
				$jmlTindakanFisio = 0 ;
				$this->fisioGrid->Visible = false;
				$this->fisioMsg->Text = 'Tidak Ada Transaksi Fisio';
			}
		}
		elseif($jnsPasien == '2') //pasien Luar
		{	
			$sql = "SELECT 
					  tbd_pasien_luar.no_trans,
					  tbt_fisio_penjualan_lain.id_tindakan,
					  tbm_fisio_kelompok.nama AS kel_tdk,
					  tbm_fisio_kategori.jenis AS kateg_tdk,
					  tbm_fisio_tindakan.nama AS nm_tdk, ";
			
			if($this->citoCheck->Checked==false){$sql .= "tbt_fisio_penjualan_lain.harga ";}
			else{$sql .= "(2 * tbt_fisio_penjualan_lain.harga) AS harga ";}	
			
			$sql .= "FROM
					  tbd_pasien_luar
					  INNER JOIN tbt_fisio_penjualan_lain ON (tbd_pasien_luar.no_trans = tbt_fisio_penjualan_lain.no_trans_pas_luar)
					  LEFT JOIN tbm_fisio_tindakan ON (tbt_fisio_penjualan_lain.id_tindakan = tbm_fisio_tindakan.kode)
					  LEFT JOIN tbm_fisio_kelompok ON (tbm_fisio_tindakan.kelompok = tbm_fisio_kelompok.kode)
					  LEFT OUTER JOIN tbm_fisio_kategori ON (tbm_fisio_tindakan.kategori = tbm_fisio_kategori.kode)
					WHERE
					  tbd_pasien_luar.no_trans = '$notrans'
					  AND tbt_fisio_penjualan_lain.flag = '0' ";
					  		  		  
			$arr = $this->queryAction($sql,'S');
			if(count($arr) > 0) //Jika ada data tindakan fisio
			{
				foreach($arr as $row)
				{				
					$jmlTindakanFisio += $row['harga'];	
				}
				
				$this->setViewState('jmlTindakanFisio',$jmlTindakanFisio);
				
				$this->fisioGrid->Visible = true;
				$this->fisioGrid->DataSource = $arr;
				$this->fisioGrid->dataBind();
				$this->fisioMsg->Text = '';
			}
			else
			{
				$jmlTindakanFisio = 0 ;
				$this->fisioGrid->Visible = false;
				$this->fisioMsg->Text = 'Tidak Ada Transaksi Fisio';
			}
		}
	}
	
	public function bindAllGrid($jnsPasien,$notrans)
	{
		$this->bindGridTdkRwtJln($jnsPasien,$notrans);
									
		$this->bindGridAmbulan($jnsPasien,$notrans);
		$jmlHargaAmbulan = $this->getViewState('jmlHargaAmbulan');
		$jmlTotalRwtJln += $jmlHargaAmbulan;
		
		$this->bindGridApotik($jnsPasien,$notrans);
		$jmlHargaApotik = $this->getViewState('jmlHargaApotik');			
		$jmlTotalRwtJln += $jmlHargaApotik;
		
		$this->bindGridLab($jnsPasien,$notrans);
		//$jmlTindakanLab = $this->getViewState('jmlTindakanLab');
		//$jmlTotalRwtJln += $jmlTindakanLab;
		
		$this->bindGridRad($jnsPasien,$notrans);
		//$jmlTindakanRad = $this->getViewState('jmlTindakanRad');
		//$jmlTotalRwtJln += $jmlTindakanRad;
		
		$this->bindGridFisio($jnsPasien,$notrans);
		$jmlTindakanFisio = $this->getViewState('jmlTindakanFisio');
		$jmlTotalRwtJln += $jmlTindakanFisio;
		
		$jmlHargaAsli =  $jmlHargaApotik + $jmlHargaAmbulan + $jmlTindakanLab + $jmlTindakanRad + $jmlTindakanFisio;
		$jmlHargaBulat = $this->bulatkan($jmlTotalRwtJln);  		
		$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
		
		$this->setViewState('tmpJml',$jmlHargaBulat);
		$this->setViewState('sisaBulat',$sisaBulat);
		$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
	}
	
	public function cekPiutang($jnsPasien,$notrans)
    {
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$stPiutang = RwtjlnRecord::finder()->findByPk($notrans)->st_piutang;	
			
			if($stPiutang == '1')//pembayaran pertama telah dilakukan
			{
				//$this->jmlPanel->Enabled = false;
				//$this->jmlPanel2->Enabled = true;
				//$dataBayar1 = KasirRwtJlnAngsuranRecord::finder()->find('no_trans_asal = ? AND tipe_pasien = ? AND st = ?',array($notrans,$jnsPasien,'0'));
				$dataBayar1 = KasirRwtJlnAngsuranRecord::finder()->find('no_trans_asal = ? AND tipe_pasien = ?',array($notrans,$jnsPasien));
				$this->prosesCekPiutang($dataBayar1);
			}
		}
		elseif($jnsPasien == '1') //Jika pasien rawat inap
		{
			$stPiutang = RwtInapRecord::finder()->findByPk($notrans)->st_piutang;
			
			if($stPiutang == '1')//pembayaran pertama telah dilakukan
			{
				//$this->jmlPanel->Enabled = false;
				//$this->jmlPanel2->Enabled = true;
				
				$dataBayar1 = KasirRwtJlnAngsuranRecord::finder()->find('no_trans_asal = ? AND tipe_pasien = ?',array($notrans,$jnsPasien));
				$this->prosesCekPiutang($dataBayar1);
			}
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$stPiutang = PasienLuarRecord::finder()->findByPk($notrans)->st_piutang;	
			
			if($stPiutang == '1')//pembayaran pertama telah dilakukan
			{
				//$this->jmlPanel->Enabled = false;
				//$this->jmlPanel2->Enabled = true;
				
				$dataBayar1 = KasirRwtJlnAngsuranRecord::finder()->find('no_trans_asal = ? AND tipe_pasien = ?',array($notrans,$jnsPasien));
				$this->prosesCekPiutang($dataBayar1);
			}
		}
	}
	
	public function prosesCekPiutang($dataBayar1)
    {
		$caraBayar = $dataBayar1->st_carabayar;
		$noRef = $dataBayar1->no_ref;
		$jmlBayar = $dataBayar1->tarif;
		
		$this->caraBayar->SelectedValue = $caraBayar;
		$this->noRef->Text = $noRef;
		$this->bayar->Text = $jmlBayar;
		
		$this->bayarClicked($sender,$param);
		$this->getPage()->getCallbackClient()->click( $this->bayarBtn->getClientID() );
		//$this->bayarClicked($sender,$param);
		
		if($this->getViewState('tmpJml2'))
		{
			$this->sisaByr->Text = 'Rp. '.number_format($this->getViewState('tmpJml2') - $jmlBayar,2,',','.');
		}
		
		$this->tundaBtn->Enabled = false;
		//$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml2'),2,',','.');
		$this->setViewState('stPiutang','1');
	}
		
	public function detailNonInap()
    {
		$jnsPasien = $this->modeInput->SelectedValue;
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$notrans = $this->getViewState('noTransRwtJln');
			$this->bindAllGrid($jnsPasien,$notrans);			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$notrans = $this->getViewState('noTransRwtInap');
			$this->bindAllGrid($jnsPasien,$notrans);
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien Luar atau bebas karyawan
		{
			$jnsTrans = $this->collectSelectionResult($this->jnsTransBebas);
			$notrans = $this->getViewState('noTransRwtBebas');
			$this->bindAllGrid($jnsPasien,$notrans);
			/*
			if($jnsTrans == '1') //Transaksi Pasien Luar Apotik
			{
				$this->bindGridApotik($jnsPasien,$notrans);
				$jmlHargaApotik = $this->getViewState('jmlHargaApotik');			
				$jmlTotalRwtJln += $jmlHargaApotik;
			}
			elseif($jnsTrans == '2') //Transaksi Pasien Luar Laboratorium 
			{
				$this->bindGridLab($jnsPasien,$notrans);
				$jmlTindakanLab = $this->getViewState('jmlTindakanLab');
				$jmlTotalRwtJln += $jmlTindakanLab;
			}
			elseif($jnsTrans == '3') //Transaksi Pasien Luar Radiologi
			{
				$this->bindGridRad($jnsPasien,$notrans);
				$jmlTindakanRad = $this->getViewState('jmlTindakanRad');
				$jmlTotalRwtJln += $jmlTindakanRad;
			}
			elseif($jnsTrans == '4') //Transaksi Pasien Luar Fisio
			{
				$this->bindGridFisio($jnsPasien,$notrans);
				$jmlTindakanFisio = $this->getViewState('jmlTindakanFisio');
				$jmlTotalRwtJln += $jmlTindakanFisio;
			}
			else
			{
				$jmlHargaApotik = 0;
				$jmlHargaAsli = $jmlHargaApotik;
				$jmlHargaBulat = 0;  		
				$sisaBulat = 0;
			}
			
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			*/
		}
		
		elseif($jnsPasien == '5') //cek no_trans pasien rujukan
		{
			$noTransRujuk = $this->DDtrans->SelectedValue;	
			$dateNow = date('Y-m-d');
			
			$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
			if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
			{	
				if($this->citoCheck->Checked==false)
				{
					$sql="SELECT 
						tbt_lab_penjualan_lain.tgl,
						tbt_lab_penjualan_lain.cm,
						tbt_lab_penjualan_lain.harga AS total,
						tbm_lab_tindakan.nama "	;
				}
				else
				{
					$sql="SELECT 
						tbt_lab_penjualan_lain.tgl,
						tbt_lab_penjualan_lain.cm,
						(2 * tbt_lab_penjualan_lain.harga) AS total,
						tbm_lab_tindakan.nama "	;
				}
					
				
				$sql .=	" FROM 
							tbt_lab_penjualan_lain 
							LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_lain.id_tindakan = tbm_lab_tindakan.kode)
						WHERE 
							tbt_lab_penjualan_lain.no_trans='$noTransRujuk'
							AND tbt_lab_penjualan_lain.flag='0'
							AND tbt_lab_penjualan_lain.tgl='$dateNow'";
						
				$arr=$this->queryAction($sql,'S');
				
				if(count($arr) > 0)
				{
					$this->labRwtJlnGrid->Visible = true;
					$this->labRwtJlnGrid->DataSource=$arr;
					$this->labRwtJlnGrid->dataBind();
					$this->labMsg->Text = '';
				}
				else
				{
					$this->labRwtJlnGrid->Visible = false;
					$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
				}
			}
			elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
			{
				if($this->citoCheck->Checked==false)
				{
				$sql="SELECT 
						tbt_rad_penjualan_lain.tgl,
						tbt_rad_penjualan_lain.cm,
						tbt_rad_penjualan_lain.harga AS total,
						tbm_rad_tindakan.nama ";
				}
				else
				{
					$sql="SELECT 
						tbt_rad_penjualan_lain.tgl,
						tbt_rad_penjualan_lain.cm,
						(2 * tbt_rad_penjualan_lain.harga) AS total,
						tbm_rad_tindakan.nama ";
				}
					
				$sql .=" FROM 
							tbt_rad_penjualan_lain 
							LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_lain.id_tindakan = tbm_rad_tindakan.kode)
						WHERE 
							tbt_rad_penjualan_lain.no_trans='$noTransRujuk'
							AND tbt_rad_penjualan_lain.flag='0'
							AND tbt_rad_penjualan_lain.tgl='$dateNow'";
						
				$arr=$this->queryAction($sql,'S');
				
				if(count($arr) > 0)
				{
					$this->radRwtJlnGrid->Visible = true;
					$this->radRwtJlnGrid->DataSource=$arr;
					$this->radRwtJlnGrid->dataBind();
					$this->radMsg->Text = '';
				}
				else
				{
					$this->radRwtJlnGrid->Visible = false;
					$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
				}
			}	
		}
		
		$this->setViewState('noTransRawat',$notrans);
		$this->detailPanel->focus();
	}
	
	public function kryChanged()
	{
		
		$cek=$this->kryCheck->Checked;
		$this->setViewState('krycheck',$cek);
		
	}
	
	
	public function detailInap()
    {
		$cm = $this->notrans->Text;
		$noTrans = $this->DDtrans->SelectedValue;
		
		$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
		
		if($jnsTrans == '0') //Jika pasien rawat inap & pilih transaksi lab
		{	
			if($this->citoCheck->Checked==false)
			{
				$sql="SELECT 
					tbt_lab_penjualan_inap.tgl,
					tbt_lab_penjualan_inap.cm,
					tbt_lab_penjualan_inap.harga AS total,
					tbm_lab_tindakan.nama "	;
			}
			else
			{
				$sql="SELECT 
					tbt_lab_penjualan_inap.tgl,
					tbt_lab_penjualan_inap.cm,
					(2 * tbt_lab_penjualan_inap.harga) AS total,
					tbm_lab_tindakan.nama "	;
			}
				
			
			$sql .=	" FROM 
						tbt_lab_penjualan_inap 
						LEFT JOIN tbm_lab_tindakan ON (tbt_lab_penjualan_inap.id_tindakan = tbm_lab_tindakan.kode)
					WHERE 
						tbt_lab_penjualan_inap.no_trans='$noTrans'
						AND tbt_lab_penjualan_inap.flag='0'";
					
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->labRwtJlnGrid->Visible = true;
				$this->labRwtJlnGrid->DataSource=$arr;
				$this->labRwtJlnGrid->dataBind();
				$this->labMsg->Text = '';
			}
			else
			{
				$this->labRwtJlnGrid->Visible = false;
				$this->labMsg->Text = 'Tidak Ada Transaksi Laboratorium';
			}
		}
		elseif($jnsTrans == '1') //Jika pasien rawat inap & pilih transaksi rad
		{
			if($this->citoCheck->Checked==false)
			{
			$sql="SELECT 
					tbt_rad_penjualan_inap.tgl,
					tbt_rad_penjualan_inap.cm,
					tbt_rad_penjualan_inap.harga AS total,
					tbm_rad_tindakan.nama ";
			}
			else
			{
				$sql="SELECT 
					tbt_rad_penjualan_inap.tgl,
					tbt_rad_penjualan_inap.cm,
					(2 * tbt_rad_penjualan_inap.harga) AS total,
					tbm_rad_tindakan.nama ";
			}
				
			$sql .=" FROM 
						tbt_rad_penjualan_inap 
						LEFT JOIN tbm_rad_tindakan ON (tbt_rad_penjualan_inap.id_tindakan = tbm_rad_tindakan.kode)
					WHERE 
						tbt_rad_penjualan_inap.no_trans='$noTrans'
						AND tbt_rad_penjualan_inap.flag='0'";
					
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0)
			{
				$this->radRwtJlnGrid->Visible = true;
				$this->radRwtJlnGrid->DataSource=$arr;
				$this->radRwtJlnGrid->dataBind();
				$this->radMsg->Text = '';
			}
			else
			{
				$this->radRwtJlnGrid->Visible = false;
				$this->radMsg->Text = 'Tidak Ada Transaksi Radiologi';
			}
		}
	}
	
	public function cleanAllTmpTable()
	{
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		if($this->getViewState('nmTableLab'))
		{
			$this->queryAction($this->getViewState('nmTableLab'),'D');//Droped the table	
			$this->clearViewState('nmTableLab');//Clear the view state	
		}
		
		if($this->getViewState('nmTableRad'))
		{
			$this->queryAction($this->getViewState('nmTableRad'),'D');//Droped the table	
			$this->clearViewState('nmTableRad');//Clear the view state	
		}
	}
	
	public function batalClicked()
    {	
		$this->cleanAllTmpTable();
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		$this->clearViewState('tmpSisaJmlBayar1');
		
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
		
		$this->clearViewState('stBayarLunasLangsung');
		$this->clearViewState('stBayar2');
		
		//$this->Response->reload();
		$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirRwtJlnDiscount'));		
		
		
		/*	
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text='';
		
		$this->citoCheck->Enabled=true;
		$this->DDtrans->Enabled=true;
		$this->bayarBtn->Enabled=true;	
		$this->bayar->Enabled=true;
			
		$this->citoCheck->Checked=false;
		$this->Page->CallbackClient->focus($this->notrans);
		
		$this->showSecond->Visible=false;
		$this->detailPanel->Visible = false ;
		*/
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	
	public function insertTbtAngsuran($jnsPasien,$cm,$notrans,$tglNow,$wktNow,$nipTmp,$noRef,$caraBayar,$noRef2,$caraBayar2)
	{
		//INSERT tbt_kasir_rwtjln_angsuran
		$notransAngsur = $this->numCounter('tbt_kasir_rwtjln_angsuran',KasirRwtJlnAngsuranRecord::finder(),'40');
		
		$data = new KasirRwtJlnAngsuranRecord();
		$data->tipe_pasien = $jnsPasien;
		$data->cm = $cm;
		$data->no_trans = $notransAngsur;
		$data->no_trans_asal = $notrans;
		$data->tgl = $tglNow;
		$data->waktu = $wktNow;
		$data->operator = $nipTmp;
		$data->st='0';
		
		if($this->getViewState('stBayarLunasLangsung') == '1')//1x pembayaran dalam 1 waktu
		{
			$data->tarif = $this->getViewState('tmpJml2');
			$data->no_ref = $noRef;
			
			$data->st_carabayar = $caraBayar;
			$data->Save();
		}
		else//2x pembayaran
		{
			if($this->getViewState('stPiutang') != '1')//2x pembayaran dalam 1 waktu
			{
				$data->tarif = $this->bayar->Text;				
				$data->no_ref = $noRef;
				$data->st_carabayar = $caraBayar;
				$data->Save();
				
				if($this->getViewState('stBayar2') == '1')
				{
					$notransAngsur = $this->numCounter('tbt_kasir_rwtjln_angsuran',KasirRwtJlnAngsuranRecord::finder(),'40');
					
					$data = new KasirRwtJlnAngsuranRecord();
					$data->tipe_pasien = $jnsPasien;
					$data->cm = $cm;
					$data->no_trans = $notransAngsur;
					$data->no_trans_asal = $notrans;
					$data->tgl = $tglNow;
					$data->waktu = $wktNow;
					$data->operator = $nipTmp;
					$data->tarif = $this->getViewState('tmpSisaJmlBayar1');
					$data->no_ref = $noRef2;
					$data->st='0';
					$data->st_carabayar = $caraBayar2;
					$data->Save();
				}
			}
			else //2x pembayaran dalam 2 waktu
			{
				$data->tarif = $this->getViewState('tmpSisaJmlBayar1');
				$data->no_ref = $noRef2;
				$data->st_carabayar = $caraBayar2;
				$data->Save();
			}	
		}
	}
	
	
	public function cetakClicked($sender,$param)
    {
		$sisaByr=$this->getViewState('sisa');
		
		$jmlTagihan=$this->getViewState('tmpJml2');
		
		$tglNow=date('Y-m-d');
		$wktNow=date('G:i:s');
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
		
		$caraBayar = $this->caraBayar->SelectedValue;
		$noRef = $this->noRef->Text;
		$jmlBayar=$this->bayar->Text;
		
		$caraBayar2 = $this->caraBayar2->SelectedValue;
		$noRef2 = $this->noRef2->Text;	
		$jmlBayar2=$this->bayar->Text;	
		
		
		if($this->getViewState('stBayarLunasLangsung') != '1')//2x pembayaran dalam 1 waktu
		{
			$jmlBayar += $this->bayar2->Text;
		}
			
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$notrans = $this->getViewState('noTransRwtJln');
			$cm=$this->notrans->Text;
			$dokter=$this->getViewState('dokter');
			$id_klinik = RwtjlnRecord::finder()->findByPk($notrans)->id_klinik;
			$nama=$this->nama->Text;
			
			$kelompokPasien = RwtjlnRecord::finder()->findByPk($notrans)->penjamin;
			$stAsuransi = RwtjlnRecord::finder()->findByPk($notrans)->st_asuransi;
			
			if($kelompokPasien == '04' && $stAsuransi=='1') //kelompok pasien karyawan
			{
				if($this->modeBayarKaryawan->SelectedValue == '0')
				{
					$stKredit = '1';
				}
				else
				{
					$stKredit = '0';
				}
			}
			else
			{
				$stKredit = '0';
			}
			
			
			//Update tbt_kasir_rwtjln 
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				$sql = "SELECT * FROM $nmTable  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					$total = $row['total'];
					$disc = $row['disc'];
					$id_tindakan = $row['id_tindakan'];
					
					$sql = "UPDATE 
							tbt_kasir_rwtjln
						SET 
							total = '$total',
							disc = '$disc',
							st_flag = '1'
						WHERE 
						no_trans_rwtjln = '$notrans'
						AND st_flag = 0
						AND id_tindakan = '$id_tindakan'";	
					$this->queryAction($sql,'C');
				}
				
			}
			
			//Update tbt_obat_jual / tbt_obat_jual_karyawan
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
						no_trans_rwtjln
					FROM 
						tbt_obat_jual_karyawan
					WHERE 
						cm='$cm' 
						AND no_trans_rwtjln='$notrans'
						AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual
				{
					$sql="UPDATE tbt_obat_jual_karyawan SET flag='1', operator_kasir='$nipTmp' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
					$this->queryAction($sql,'C');
				}
			}
			else
			{
				$sql = "SELECT
						no_trans_rwtjln
					FROM 
						tbt_obat_jual
					WHERE 
						cm='$cm' 
						AND no_trans_rwtjln='$notrans'
						AND flag = '0'";
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual
				{
					$sql="UPDATE tbt_obat_jual SET flag='1', operator_kasir='$nipTmp' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
					$this->queryAction($sql,'C');
				}
			}	
			
			//Update status tbt_rawat_jalan
			$sql="UPDATE 
					tbt_rawat_jalan 
				  SET 
					flag='1',
					kasir = '$nipTmp', 
					tgl_kasir = '$tglNow', 
					wkt_kasir = '$wktNow',
					st_kredit = '$stKredit',
					st_carabayar = '$caraBayar',
					no_referensi = '$noRef' 
				  WHERE 
					no_trans='$notrans' 
					AND flag='0' ";
				
			$this->queryAction($sql,'C');
			
			$this->insertTbtAngsuran($jnsPasien,$cm,$notrans,$tglNow,$wktNow,$nipTmp,$noRef,$caraBayar,$noRef2,$caraBayar2);	
			
			//Update flag tbt_lab_penjualan
			if(LabJualRecord::finder()->find('no_trans_rwtjln=?',$notrans))
			{
				$sql="UPDATE 
						tbt_lab_penjualan 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_rwtjln='$notrans' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			//Update flag tbt_rad_penjualan
			if(RadJualRecord::finder()->find('no_trans_rwtjln=?',$notrans))
			{
				$sql="UPDATE 
						tbt_rad_penjualan 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_rwtjln='$notrans' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			
			//Update flag tbt_fisio_penjualan
			if(FisioJualRecord::finder()->find('no_trans_rwtjln=?',$notrans))
			{
				$sql="UPDATE 
						tbt_fisio_penjualan 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_rwtjln='$notrans' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			//Update flag tbt_rwtjln_ambulan
			if(AmbulanRwtJlnRecord::finder()->find('no_trans_rwtjln=?',$notrans))
			{
				$sql="UPDATE 
						tbt_rwtjln_ambulan 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_rwtjln='$notrans' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			$this->cleanAllTmpTable();
		
			$stRujuk = RwtjlnRecord::finder()->findByPk($notrans)->st_rujuk;
			
			if($stRujuk == '1') //Jika Pasien Rujukan
			{
				if(RwtjlnRecord::finder()->findByPk($notrans)->id_dokter_perujuk_dalam)
				{
					$idDokterPerujuk = RwtjlnRecord::finder()->findByPk($notrans)->id_dokter_perujuk_dalam;
				}
				else
				{
					$idDokterPerujuk = '';
				}
				
				if(RwtjlnRecord::finder()->findByPk($notrans)->id_bidan_perujuk)
				{
					$idBidanPerujuk = RwtjlnRecord::finder()->findByPk($notrans)->id_bidan_perujuk;
				}
				else
				{
					$idDokterPerujuk = '';
				}
				
				$nmPerujuk = RwtjlnRecord::finder()->findByPk($notrans)->nm_perujuk;
			}
			
			$no_trans_rwt_jln = $notrans;
			$no_trans_rwt_inap = '';
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$notrans = $this->getViewState('noTransRwtInap');
			$cm=$this->notrans->Text;
			$dokter=$this->getViewState('dokter');
			$nama=$this->nama->Text;
			
			$this->insertTbtAngsuran($jnsPasien,$cm,$notrans,$tglNow,$wktNow,$nipTmp,$noRef,$caraBayar,$noRef2,$caraBayar2);	
			
			//Update st_piutang tbt_rawat_inap
			$sql="UPDATE 
					tbt_rawat_inap 
				  SET 
					st_piutang='0'
				  WHERE 
					no_trans='$notrans' 
					AND status='0' ";
				
			$this->queryAction($sql,'C');
			
			//Update tbt_obat_jual / tbt_obat_jual_karyawan
			$kelompokPasien = $this->getViewState('kelompokPasien');
			$stAsuransi = $this->getViewState('stAsuransi');
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sql = "SELECT
						no_trans_inap
					FROM 
						tbt_obat_jual_inap_karyawan
					WHERE 
						cm='$cm' 
						AND no_trans_inap='$notrans'
						AND flag = '0'
						AND st_bayar = '1'";
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual
				{
					$sql="UPDATE tbt_obat_jual_inap_karyawan SET flag='1', operator_kasir='$nipTmp' WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag='0' ";		
					$this->queryAction($sql,'C');
				}
			}
			else
			{
				$sql = "SELECT
						no_trans_inap
					FROM 
						tbt_obat_jual_inap
					WHERE 
						cm='$cm' 
						AND no_trans_inap='$notrans'
						AND flag = '0'
						AND st_bayar = '1'";
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual
				{
					$sql="UPDATE tbt_obat_jual_inap SET flag='1', operator_kasir='$nipTmp' WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag='0' AND st_bayar = '1'";		
					$this->queryAction($sql,'C');
				}
			}	
			
			//Update flag tbt_lab_penjualan_inap_inap
			if(LabJualInapRecord::finder()->find('no_trans_inap=?',$notrans))
			{
				$sql="UPDATE 
						tbt_lab_penjualan_inap 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_inap='$notrans' 
						AND st_bayar='1' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			//Update flag tbt_rad_penjualan_inap
			if(RadJualInapRecord::finder()->find('no_trans_inap=?',$notrans))
			{
				$sql="UPDATE 
						tbt_rad_penjualan_inap 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_inap='$notrans' 
						AND st_bayar='1' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			//Update flag tbt_fisio_penjualan_inap
			if(FisioJualInapRecord::finder()->find('no_trans_inap=?',$notrans))
			{
				$sql="UPDATE 
						tbt_fisio_penjualan_inap 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_inap='$notrans' 
						AND st_bayar='1' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			//Update flag tbt_inap_ambulan
			if(AmbulanRecord::finder()->find('no_trans_inap=?',$notrans))
			{
				$sql="UPDATE 
						tbt_inap_ambulan 
					  SET 
						flag='1' 
					  WHERE 
						no_trans_inap='$notrans' 
						AND flag='0' ";
				$this->queryAction($sql,'C');
			}
			
			$stRujuk = RwtInapRecord::finder()->findByPk($notrans)->st_rujukan;
			
			if($stRujuk == '1') //Jika Pasien Rujukan
			{
				if(RwtInapRecord::finder()->findByPk($notrans)->id_dokter_perujuk_dalam)
				{
					$idDokterPerujuk = RwtInapRecord::finder()->findByPk($notrans)->id_dokter_perujuk_dalam;
				}
				else
				{
					$idDokterPerujuk = '';
				}
				
				if(RwtInapRecord::finder()->findByPk($notrans)->id_bidan_perujuk)
				{
					$idBidanPerujuk = RwtInapRecord::finder()->findByPk($notrans)->id_bidan_perujuk;
				}
				else
				{
					$idDokterPerujuk = '';
				}
				
				$nmPerujuk = RwtInapRecord::finder()->findByPk($notrans)->nm_perujuk;
			}
			
			$no_trans_rwt_jln = '';
			$no_trans_rwt_inap = $notrans;
		}	
		elseif($jnsPasien == '2' || $jnsPasien == '3') //pasien bebas atau bebas karyawan
		{
			$notrans = $this->numCounter('tbt_rawat_jalan_lain',RwtjlnLainRecord::finder(),'23');
			
			$jnsTrans = $this->collectSelectionResult($this->jnsTransBebas);
			if($jnsTrans == '0') //Transaksi Non Apotik
			{
				$nama=$this->nmPas->Text;
			}	
			elseif($jnsTrans == '1') //Transaksi Apotik
			{
				$nama=$this->nama->Text;
				
				$noTransRwtBebas = $this->getViewState('noTransRwtBebas');
				//Update tbt_obat_jual_lain
				$sql = "SELECT
						no_trans,
						no_trans_rwtjln_lain = '$notrans' 
					FROM ";
				
				if($jnsPasien == '2')
				{ $sql .=" tbt_obat_jual_lain "; }
				elseif($jnsPasien == '3')
				{ $sql .=" tbt_obat_jual_lain_karyawan "; }
				
				$sql .=" WHERE 
						no_trans_pas_luar = '$noTransRwtBebas'
						AND flag = '0'";
						
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual_lain
				{
					if($jnsPasien == '2')
					{ 
						$sql="UPDATE tbt_obat_jual_lain SET no_trans_rwtjln_lain='$notrans', flag='1',operator_kasir='$nipTmp' WHERE no_trans_pas_luar ='$noTransRwtBebas' AND flag='0' ";		
					}
					elseif($jnsPasien == '3')
					{
						$sql="UPDATE tbt_obat_jual_lain_karyawan SET no_trans_rwtjln_lain='$notrans', flag='1',operator_kasir='$nipTmp' WHERE no_trans_pas_luar ='$noTransRwtBebas' AND flag='0' ";		
					}
					
					
					$this->queryAction($sql,'C');
				}
			}
			elseif($jnsTrans == '2') //Transaksi Lab
			{
				$nama=$this->nama->Text;
				
				$noTransRwtBebas = $this->getViewState('noTransRwtBebas');
				//Update tbt_obat_jual_lain
				$sql = "SELECT *
						FROM 
							tbt_lab_penjualan_lain
						WHERE
							no_trans_pas_luar = '$noTransRwtBebas'
							AND flag = '0'	";
						
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi lab, UPDATE flag di tbt_lab_penjualan_lain
				{
					$sql="UPDATE 
							tbt_lab_penjualan_lain 
						  SET 
						  	no_trans_rwtjln_lain='$notrans', 
							flag='1',
							operator_kasir='$nipTmp' 
						   WHERE 
							no_trans_pas_luar ='$noTransRwtBebas' 
							AND flag='0' ";	
					$this->queryAction($sql,'C');
				}
			}
			elseif($jnsTrans == '3') //Transaksi Rad
			{
				$nama=$this->nama->Text;
				
				$noTransRwtBebas = $this->getViewState('noTransRwtBebas');
				//Update tbt_obat_jual_lain
				$sql = "SELECT *
						FROM 
							tbt_rad_penjualan_lain
						WHERE
							no_trans_pas_luar = '$noTransRwtBebas'
							AND flag = '0'	";
						
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi rad, UPDATE flag di tbt_rad_penjualan_lain
				{
					$sql="UPDATE 
							tbt_rad_penjualan_lain 
						  SET 
						  	no_trans_rwtjln_lain='$notrans', 
							flag='1',
							operator_kasir='$nipTmp' 
						   WHERE 
							no_trans_pas_luar ='$noTransRwtBebas' 
							AND flag='0' ";	
					$this->queryAction($sql,'C');
				}
			}
			elseif($jnsTrans == '4') //Transaksi Fisio
			{
				$nama=$this->nama->Text;
				
				$noTransRwtBebas = $this->getViewState('noTransRwtBebas');
				//Update tbt_obat_jual_lain
				$sql = "SELECT *
						FROM 
							tbt_fisio_penjualan_lain
						WHERE
							no_trans_pas_luar = '$noTransRwtBebas'
							AND flag = '0'	";
						
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi fisio, UPDATE flag di tbt_fisio_penjualan_lain
				{
					$sql="UPDATE 
							tbt_fisio_penjualan_lain 
						  SET 
						  	no_trans_rwtjln_lain='$notrans', 
							flag='1',
							operator_kasir='$nipTmp' 
						   WHERE 
							no_trans_pas_luar ='$noTransRwtBebas' 
							AND flag='0' ";	
					$this->queryAction($sql,'C');
				}
			}
			
			$this->insertTbtAngsuran($jnsPasien,$cm,$noTransRwtBebas,$tglNow,$wktNow,$nipTmp,$noRef,$caraBayar,$noRef2,$caraBayar2);
			
			//INSERT tbt_rawat_jalan_lain
			$transRwtJlnLain = new RwtjlnLainRecord();
			$transRwtJlnLain->no_trans = $notrans;
			$transRwtJlnLain->nama = $nama;
			$transRwtJlnLain->kasir = $nipTmp;
			$transRwtJlnLain->tgl = $tglNow;
			$transRwtJlnLain->wkt = $wktNow;
			$transRwtJlnLain->flag='1';
			$transRwtJlnLain->st_carabayar = $caraBayar;
			$transRwtJlnLain->no_referensi = $noRef;
			$transRwtJlnLain->Save();
			
			$stRujuk = '0';
		}	
			
		//komisi perujuk
		if($stRujuk == '1')//jika pasien rujukan => INSERT ke tbt_komisi_trans 
		{
			$newKomisi= new KomisiTransRecord();
			$newKomisi->id_dokter_perujuk_dalam = $idDokterPerujuk;
			$newKomisi->id_bidan_perujuk = $idBidanPerujuk;
			$newKomisi->nama = $nmPerujuk;
			$newKomisi->tgl = $tglNow;
			$newKomisi->wkt = $wktNow;
			$newKomisi->komisi = '5000';
			$newKomisi->st_rawat = $jnsPasien;
			$newKomisi->no_trans_rwt_jln = $no_trans_rwt_jln;
			$newKomisi->no_trans_rwt_inap = $no_trans_rwt_inap;
			$newKomisi->st_dibyr='0';
			$newKomisi->Save();
		}	
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		
		if($this->citoCheck->Checked==false)
		{
			$cito = '0';
		}
		else
		{
			$cito = '1';
		}
			
		if($this->getViewState('sisa')) 
		{
			if($jnsPasien != '2')
			{ 
				$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnDiscount',
					array(
						'notrans'=>$notrans,
						'cm'=>$cm,
						'nama'=>$nama,
						'jmlTagihan'=>$jmlTagihan,
						'sisaByr'=>$sisaByr,
						'jmlBayar'=>$jmlBayar,
						'jnsPasien'=>$jnsPasien,
						'cito'=>$cito
					)));
			}
			else
			{
				$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnDiscount',
					array(
						'notrans'=>$notrans,
						'notransObat'=>$noTransRwtBebas,
						'cm'=>$cm,
						'nama'=>$nama,
						'jmlTagihan'=>$jmlTagihan,
						'sisaByr'=>$sisaByr,
						'jmlBayar'=>$jmlBayar,
						'jnsPasien'=>$jnsPasien,
						'cito'=>$cito
					)));
			}				
		}	
		else
		{
			//$this->Response->reload();
			$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnDiscount',
					array(
						'notrans'=>$notrans,
						'notransObat'=>$noTransRwtBebas,
						'cm'=>$cm,
						'nama'=>$nama,
						'jmlTagihan'=>$jmlTagihan,
						'sisaByr'=>$sisaByr,
						'jmlBayar'=>$jmlBayar,
						'jnsPasien'=>$jnsPasien,
						'cito'=>$cito
					)));
		}	
	}
	
	
	public function cetakClicked2($sender,$param)
    {	
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml2');
		
		$besarDisc = $this->disc->Text;
		$stDisc = $this->getViewState('stDisc');
		$totBiayaDisc = $this->getViewState('totBiayaDisc');
		$totBiayaDiscBulat = $this->getViewState('totBiayaDiscBulat');
		$sisaDiscBulat = $this->getViewState('sisaDiscBulat');
		
		$tglNow=date('Y-m-d');
		$wktNow=date('G:i:s');
		//$notrans=$this->DDtrans->SelectedValue;
		
		
		if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
		{
			$jnsPasien = $this->modeInput->SelectedValue;
			if($jnsPasien == '0') //pasien rawat jalan
			{
				$notrans = $this->getViewState('noTransRwtJln');
				$cm=$this->notrans->Text;
				$dokter=$this->getViewState('dokter');
				$nama=$this->nama->Text;
				$operator=$this->User->IsUserName;
				$nipTmp=$this->User->IsUserNip;	
				
				//update status flag di masing-masing tabel 
				$sql = "SELECT
							jns_trans
						FROM 
							view_biaya_total_rwtjln 
						WHERE 
							cm='$cm' 
							AND no_trans='$notrans'
							AND flag = '0'";
			
				$arr=$this->queryAction($sql,'S');
				foreach($arr as $row)
				{			
					if($row['jns_trans']=='tindakan rawat jalan')
					{
						//Update tbt_kasir_rwtjln
						$nmTable = 'tbt_kasir_rwtjln ';
						$field = 'st_flag ';
					}
					elseif($row['jns_trans']=='laboratorium')
					{
						//Update tbt_lab_penjualan
						$nmTable = 'tbt_lab_penjualan ';
						$field = 'flag ';
					}
					elseif($row['jns_trans']=='radiologi')
					{
						//Update tbt_rad_penjualan	
						$nmTable = 'tbt_rad_penjualan ';
						$field = 'flag ';
					}
					
					$sql = "UPDATE 
								$nmTable
							SET 
								$field=1 
							WHERE 
							no_trans_rwtjln = '$notrans'
							AND $field = 0";	
					$this->queryAction($sql,'C');
				}
				
				
				$sql = "SELECT
							no_trans_rwtjln
						FROM 
							tbt_obat_jual
						WHERE 
							cm='$cm' 
							AND no_trans_rwtjln='$notrans'
							AND flag = '0'";
			
				$arr=$this->queryAction($sql,'S');
				if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual
				{
					$sql="UPDATE tbt_obat_jual SET flag='1', operator_kasir='$nipTmp' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
					$this->queryAction($sql,'C');
				}
				
				//Update status tbt_rawat_jalan
				if($stDisc == '1') //jika ada discount
				{
					$sql="UPDATE 
							tbt_rawat_jalan 
						  SET 
						  	flag='1', 
							discount='$besarDisc',
							kasir = '$nipTmp', 
							tgl_kasir = '$tglNow', 
							wkt_kasir = '$wktNow'
						  WHERE 
						  	no_trans='$notrans' 
							AND flag='0' ";	
					
					if($sisaDiscBulat != '0' || $sisaDiscBulat != '') //jika ada pembulatan setelah discount
					{
						//-------- Insert Sisa Pembulatan discount ke tbt_rawat_inap_sisa_disc -----------------
						$sisaDisc= new RwtJalanSisaDiscRecord();
						$sisaDisc->no_trans=$notrans;
						$sisaDisc->jumlah=$sisaDiscBulat;
						$sisaDisc->tgl=date('y-m-d');
						$sisaDisc->Save();
					}			
				}
				else
				{
					$sql="UPDATE 
							tbt_rawat_jalan 
						  SET 
						  	flag='1',
							kasir = '$nipTmp', 
							tgl_kasir = '$tglNow', 
							wkt_kasir = '$wktNow' 
						  WHERE 
						  	no_trans='$notrans' 
							AND flag='0' ";	
				}
					
				$this->queryAction($sql,'C');
				
			}
			elseif($jnsPasien == '1') //cek no_trans pasien rujukan
			{
				$notrans =  $this->getViewState('noTransRujuk');	
				$dateNow = date('Y-m-d');
				$nama=$this->nama->Text;
				
				$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
				if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
				{	
					//Update status tbt_lab_penjualan_lain
					$sql = "UPDATE 
								tbt_lab_penjualan_lain
							SET 
								flag=1 
							WHERE 
								no_trans = '$notrans'
								AND flag = 0
								AND tgl = '$dateNow'";	
					$this->queryAction($sql,'C');
				}
				elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
				{
					//Update status tbt_rad_penjualan_lain
					$sql = "UPDATE 
								tbt_rad_penjualan_lain
							SET 
								flag=1 
							WHERE 
								no_trans = '$notrans'
								AND flag = 0
								AND tgl = '$dateNow'";	
					$this->queryAction($sql,'C');	
				}	
			
			}	
		}
		else //jika checkbox rawat inap dipilih
		{
			$cm=$this->notrans->Text;
			$notrans = $this->DDtrans->SelectedValue;
			$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
			$jnsPasien = '2';
			$nama=$this->nama->Text;
			
			if($jnsTrans == '0') //Jika pasien rawat inap & pilih transaksi lab
			{	
				//Update status tbt_rad_penjualan_lain
				$sql = "UPDATE 
							tbt_lab_penjualan_inap
						SET 
							flag=1 
						WHERE 
							no_trans = '$notrans'
							AND flag = 0";	
				$this->queryAction($sql,'C');
			}
			elseif($jnsTrans == '1') //Jika pasien rawat inap & pilih transaksi rad
			{
				//Update status tbt_rad_penjualan_lain
				$sql = "UPDATE 
							tbt_rad_penjualan_inap
						SET 
							flag=1 
						WHERE 
							no_trans = '$notrans'
							AND flag = 0";	
				$this->queryAction($sql,'C');	
			}
		}
		
		/*
		//-------- Insert Harga Sisa Pembulatan ke tbt_obat_jual_sisa Jika ada Pembulatan-----------------
		$sisaBulat = $this->getViewState('sisaBulat');		
		if($sisaBulat > 0)
		{
			$ObatJualSisa= new ObatJualSisaRecord();
			$ObatJualSisa->no_trans=$notrans;
			$ObatJualSisa->jumlah=$sisaBulat;
			$ObatJualSisa->tgl=date('y-m-d');
			$ObatJualSisa->Save();			
		}	
		*/
		
		//Register cookies u/ status cetak
		$session=$this->Application->getModule('session');		
		$session['stCetakKasirRwtJln'] = '1';
		
		//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtLabJual',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr)));
			
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRwtJlnDiscount',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'sisaByr'=>$sisaByr,'jmlBayar'=>$jmlBayar,'petugasPoli'=>$this->getViewState('petugasPoli'),'jnsPasien'=>$jnsPasien,'jnsTrans'=>$jnsTrans,
		'besarDisc'=>$besarDisc,
		'stDisc'=>$stDisc,
		'totBiayaDisc'=>$totBiayaDisc,
		'totBiayaDiscBulat'=>$totBiayaDiscBulat,
		'sisaDiscBulat'=>$sisaDiscBulat)));
		
	}
}
?>
