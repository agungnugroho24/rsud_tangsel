<?php
class BayarKasirRwtJlnDiscount extends SimakConf
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
			$this->DDPilBebas->Enabled = false;
			$this->nmPas->Display = 'None';
			$this->jmlPanel->Enabled = false;
			$this->detailPanel->Enabled = false;
		}		
		
		if($this->getViewState('nmTable'))
		{
			$this->bindGrid();
		}
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien != '2') //bukan pasien bebas
		{	
			$this->admLab->Text = '0';
			$this->admRad->Text = '0';
			$this->admFisio->Text = '0';
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
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$this->notrans->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->nmPas->Display = 'None';
			$this->Page->CallbackClient->hide($this->nmPas);
			$this->detailPanel->Display = 'None';
			$this->detailRwtJalanPanel->Display = 'Dynamic';
			$this->detailApotikPanel->Display = 'Dynamic';
			
			$this->modeBayarLab->Enabled = false ;
			$this->modeBayarRad->Enabled = false ;
			$this->modeBayarFisio->Enabled = false ;
			$this->DDtrans->Enabled=false;
		}
		elseif($jnsPasien == '1') //Jika pasien rawat inap
		{
			$this->notrans->Enabled = true;
			$this->Page->CallbackClient->focus($this->notrans);
			$this->nmPas->Display = 'None';
			$this->Page->CallbackClient->hide($this->nmPas);
			$this->detailPanel->Display = 'None';
			$this->detailRwtJalanPanel->Display = 'None';
			$this->detailApotikPanel->Display = 'Dynamic';
			$this->modeBayarLab->Enabled = true ;
			$this->modeBayarRad->Enabled = true ;
			$this->modeBayarFisio->Enabled = true ;
			$this->DDtrans->Enabled=false;
		}
		elseif($jnsPasien == '2') //Jika pasien Bebas
		{
			$this->notrans->Enabled = false;
			$this->citoCheck->Enabled=true;			
			$this->nmPas->Display = 'None';
			$this->detailPanel->Display = 'Dynamic';
			$this->pilBebasPanel->Display = 'Dynamic';
			$this->pasBebasPanel->Display = 'Dynamic';
			$this->detailRwtJalanPanel->Display = 'None';
			$this->detailApotikPanel->Display = 'Dynamic';
			$this->modeBayarLab->Enabled = false ;
			$this->modeBayarRad->Enabled = false ;			
			$this->modeBayarFisio->Enabled = false ;
			$this->DDPilBebas->Enabled = true ;
			
		$this->nama->Text = '';
		$this->nmPas->Text = '';
		$this->jmlShow->Text = '';
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		}
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
		
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			// valid if the username is not found in the database
			if(RwtjlnRecord::finder()->find('cm = ? AND flag = ? AND st_alih = ?', array($cm ,'0','0')))
			{			
				$sql="SELECT 
						no_trans	
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 
						AND flag='0'
						AND st_alih='0'";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$data .= $row['no_trans'] .',';	
					//$petugasPoli = $row['operator'];	
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$temp;
				$this->DDtrans->dataBind();
				
				$this->citoCheck->Enabled=false;
				$this->DDtrans->Enabled=true;
				$this->errMsg->Text='';
			}
			else
			{
				$this->DDtrans->Enabled=false;
				$this->errMsg->Text='Pasien rawat jalan dengan No. Register '.$cm.' tidak ada !';
				$this->notrans->Text = '';
				$this->Page->CallbackClient->focus($this->notrans);				
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
					$data .= $row['no_trans'] .',';	
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$temp;
				$this->DDtrans->dataBind();
				
				$this->citoCheck->Enabled=false;
				$this->DDtrans->Enabled=true;
				$this->errMsg->Text='';
			}
			else
			{
				$this->DDtrans->Enabled=false;
				$this->errMsg->Text='Pasien rawat inap dengan No. Register '.$cm.' tidak ada !';
				$this->notrans->Text = '';
				$this->Page->CallbackClient->focus($this->notrans);				
				$this->citoCheck->Enabled=true;
			}
		}		
    }	
	
	
	public function DDtransCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
		$this->detailPanel->render($param->getNewWriter());
		$this->jmlPanel->render($param->getNewWriter());
	}
	
	public function DDtransChanged()
	{
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
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
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			//if($jnsPasien != '2') //bukan pasien bebas
			//{	
				$this->checkNoTrans();
			//}
		}
		else
		{
			$this->detailPanel->Display = 'None';
			$this->errMsg->Text='';	
		}
	}
	
	public function DDPilBebasCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
		$this->detailPanel->render($param->getNewWriter());
		$this->jmlPanel->render($param->getNewWriter());
	}
	
	public function DDPilBebasChanged()
	{
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
		
		if($this->DDPilBebas->SelectedValue=='0')//Bila pasien bebas dari apotik
		{
			$this->noTransPanel->Display = 'Dynamic';			
			$this->detailPanel->Display = 'Dynamic';	
			$this->pasBebasPanel->Display = 'None';
			$this->DDtrans->Enabled=true;			
			$sql="SELECT 
						no_trans	
					FROM 
						tbt_obat_jual_lain
					WHERE 
						flag='0'
					GROUP BY no_trans	";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$data .= $row['no_trans'] .',';	
					//$petugasPoli = $row['operator'];	
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$temp;
				$this->DDtrans->dataBind();
			
				$this->notrans->Text = '';
				$this->errMsg->Text='';	
				
				//$this->DDPilBebas->SelectedIndex = -1;
				//$this->DDtrans->SelectedIndex = -1;		
		}
		else
		{
			$this->noTransPanel->Display = 'None';			
			$this->pasBebasPanel->Display = 'Dynamic';
			$this->nama->Display = 'None';
			$this->nmPas->Display = 'Dynamic';
			$this->detailPanel->Display = 'Dynamic';
			$this->errMsg->Text='';	
			$this->nama->Text ='';
		}
		//$this->setViewState('pilPasBebas',$this->DDPilBebas->SelectedValue);//beri value jenis pasien
	}
	
	public function checkNoTrans()
    {
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$cm = $this->notrans->Text;
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			
			$noTransRwtJln = $this->ambilTxt($this->DDtrans);	
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
				
				//$this->showSecond->Visible=true;
				$this->notrans->Enabled=false;
				$this->errMsg->Text='';	
				$this->firstPanel->Enabled = false;
				$this->jmlPanel->Enabled = true;
				$this->detailPanel->Enabled = true ;
				$this->modeBayarLab->Enabled = false ;
				$this->modeBayarRad->Enabled = false ;
				$this->modeBayarFisio->Enabled = false ;
				$this->detailNonInap();
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$cm = $this->notrans->Text;
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			
			$nmPas = PasienRecord::finder()->findByPk($this->notrans->Text)->nama;		
			$this->setViewState('nama',$nmPas);
			$this->nama->Text= $nmPas;	
			
			$noTransRwtInap = $this->ambilTxt($this->DDtrans);	
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
			
			//$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';	
			$this->firstPanel->Enabled = false;
			$this->jmlPanel->Enabled = true;
			$this->detailPanel->Enabled = true ;
			$this->modeBayarLab->Enabled = true ;
			$this->modeBayarRad->Enabled = true ;
			$this->modeBayarFisio->Enabled = true ;
			$this->detailNonInap();
		}
		elseif($jnsPasien == '2') //pasien bebas
		{
			$noTransRwtBebas = $this->ambilTxt($this->DDtrans);	
			$this->setViewState('noTransRwtBebas',$noTransRwtBebas);
			
			$sql ="SELECT
						cm 				
					 FROM 
					 	tbt_obat_jual_lain
					 WHERE
					 	no_trans='$noTransRwtBebas' 
					 ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{			
				$namaPasBebas = $row['cm'];	
			}
			
			//$tmpPasien = $activeRec->findBySql($sql);		
			$this->setViewState('nama',$namaPasBebas);
			$this->nama->Text= $namaPasBebas;	
			
			//$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';	
			$this->firstPanel->Enabled = false;
			$this->jmlPanel->Enabled = true;
			$this->detailPanel->Enabled = true ;
			$this->modeBayarLab->Enabled = false ;
			$this->modeBayarRad->Enabled = false ;
			$this->modeBayarFisio->Enabled = false ;
			$this->detailNonInap();
		}
    }	
	
	
	public function rekapBayar()
    {
		if($this->getViewState('tmpJml') || $this->getViewState('tmpJml')==0)
		{
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			$jmlBayar = $this->getViewState('tmpJml');
			
			if($this->jmlTransLab->Text != '' && floatval($this->jmlTransLab->Text)==true)
			{
				if($jnsPasien == '0' || $jnsPasien == '2') //pasien rawat jalan atau bebas
				{
					$jmlBayar += $this->jmlTransLab->Text + $this->admLab->Text;
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					if($this->collectSelectionResult($this->modeBayarLab)=='1')
					{
						$jmlBayar += $this->jmlTransLab->Text + $this->admLab->Text;
					}
				}
			}
			
			if($this->jmlTransRad->Text != '' && floatval($this->jmlTransRad->Text)==true)
			{
				if($jnsPasien == '0' || $jnsPasien == '2') //pasien rawat jalan atau bebas
				{
					$jmlBayar += $this->jmlTransRad->Text + $this->admRad->Text;
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					if($this->collectSelectionResult($this->modeBayarRad)=='1')
					{
						$jmlBayar += $this->jmlTransRad->Text + $this->admRad->Text;
					}
				}
				
			}
			
			if($this->jmlTransFisio->Text != '' && floatval($this->jmlTransFisio->Text)==true)
			{
				if($jnsPasien == '0' || $jnsPasien == '2') //pasien rawat jalan atau bebas
				{
					$jmlBayar += $this->jmlTransFisio->Text + $this->admFisio->Text;
				}
				elseif($jnsPasien == '1') //pasien rawat inap
				{
					if($this->collectSelectionResult($this->modeBayarFisio)=='1')
					{
						$jmlBayar += $this->jmlTransFisio->Text + $this->admFisio->Text;
					}
				}
				
			}
			
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
			else
			{
				if(!$this->getViewState('sisa')) //jika tombol bayar belum di klik
				{
					if($jnsPasien == '1') //pasien rawat inap
					{
						if($this->jmlTransLab->Text != '')
						{
							if($this->collectSelectionResult($this->modeBayarLab)=='0')
							{
								if($this->jmlTransRad->Text != '')
								{
									if($this->collectSelectionResult($this->modeBayarRad)=='0')
									{
										if($this->jmlTransFisio->Text != '')
										{
											if($this->collectSelectionResult($this->modeBayarFisio)=='0')
											{
												$this->bayar->Enabled = false;
												$this->bayarBtn->Enabled = false;
												$this->cetakBtn->Enabled = true;
											}
											else
											{
												$this->bayar->Enabled = true;
												$this->bayarBtn->Enabled = true;
												$this->cetakBtn->Enabled = false;
											}
										}
										else
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
									}
									else
									{
										$this->bayar->Enabled = true;
										$this->bayarBtn->Enabled = true;
										$this->cetakBtn->Enabled = false;
									}
								}
								else
								{
									if($this->jmlTransFisio->Text != '')
									{
										if($this->collectSelectionResult($this->modeBayarFisio)=='0')
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
										else
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
									}
									else
									{
										$this->bayar->Enabled = false;
										$this->bayarBtn->Enabled = false;
										$this->cetakBtn->Enabled = true;
									}
								}
							}
							else
							{
								if($this->jmlTransRad->Text != '')
								{
									if($this->collectSelectionResult($this->modeBayarRad)=='0')
									{
										if($this->jmlTransFisio->Text != '')
										{
											if($this->collectSelectionResult($this->modeBayarFisio)=='0')
											{
												$this->bayar->Enabled = true;
												$this->bayarBtn->Enabled = true;
												$this->cetakBtn->Enabled = true;
											}
											else
											{
												$this->bayar->Enabled = true;
												$this->bayarBtn->Enabled = true;
												$this->cetakBtn->Enabled = false;
											}
										}
										else
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
									}
									else
									{
										$this->bayar->Enabled = true;
										$this->bayarBtn->Enabled = true;
										$this->cetakBtn->Enabled = false;
									}
								}
								else
								{
									if($this->jmlTransFisio->Text != '')
									{
										if($this->collectSelectionResult($this->modeBayarFisio)=='0')
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
										else
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
									}
									else
									{
										$this->bayar->Enabled = true;
										$this->bayarBtn->Enabled = true;
										$this->cetakBtn->Enabled = false;
									}
								}
							}
						}
						else
						{
							if($this->collectSelectionResult($this->modeBayarLab)=='0')
							{
								if($this->jmlTransRad->Text != '')
								{
									if($this->collectSelectionResult($this->modeBayarRad)=='0')
									{
										if($this->jmlTransFisio->Text != '')
										{
											if($this->collectSelectionResult($this->modeBayarFisio)=='0')
											{
												$this->bayar->Enabled = false;
												$this->bayarBtn->Enabled = false;
												$this->cetakBtn->Enabled = true;
											}
											else
											{
												$this->bayar->Enabled = true;
												$this->bayarBtn->Enabled = true;
												$this->cetakBtn->Enabled = false;
											}
										}
										else
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
									}
									else
									{
										$this->bayar->Enabled = true;
										$this->bayarBtn->Enabled = true;
										$this->cetakBtn->Enabled = false;
									}
								}
								else
								{
									if($this->jmlTransFisio->Text != '')
									{
										if($this->collectSelectionResult($this->modeBayarFisio)=='0')
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
										else
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
									}
									else
									{
										$this->bayar->Enabled = false;
										$this->bayarBtn->Enabled = false;
										$this->cetakBtn->Enabled = true;
									}
								}
							}
							else
							{
								if($this->jmlTransRad->Text != '')
								{
									if($this->collectSelectionResult($this->modeBayarRad)=='0')
									{
										if($this->jmlTransFisio->Text != '')
										{
											if($this->collectSelectionResult($this->modeBayarFisio)=='0')
											{
												$this->bayar->Enabled = false;
												$this->bayarBtn->Enabled = false;
												$this->cetakBtn->Enabled = true;
											}
											else
											{
												$this->bayar->Enabled = true;
												$this->bayarBtn->Enabled = true;
												$this->cetakBtn->Enabled = false;
											}
										}
										else
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
									}
									else
									{
										$this->bayar->Enabled = true;
										$this->bayarBtn->Enabled = true;
										$this->cetakBtn->Enabled = false;
									}
								}
								else
								{
									if($this->jmlTransFisio->Text != '')
									{
										if($this->collectSelectionResult($this->modeBayarFisio)=='0')
										{
											$this->bayar->Enabled = false;
											$this->bayarBtn->Enabled = false;
											$this->cetakBtn->Enabled = true;
										}
										else
										{
											$this->bayar->Enabled = true;
											$this->bayarBtn->Enabled = true;
											$this->cetakBtn->Enabled = false;
										}
									}
									else
									{
										$this->bayar->Enabled = false;
										$this->bayarBtn->Enabled = false;
										$this->cetakBtn->Enabled = false;
									}
								}
							}
						}
					}
				}
			}
			
			
			//if($jnsPasien != '2') //bukan pasien bebas
			//{
				$this->bindGridApotik();
			//}
			
			$this->setViewState('tmpJml2',$jmlBayar);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml2'),2,',','.');
		}
		
	}
	
	
	public function checkNoTransInap()
    {
		$cm = $this->notrans->Text;
		$noTrans = $this->ambilTxt($this->DDtrans);
		
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
	
	
	public function bayarCallBack($sender,$param)
   	{
		$this->noTransPanel->render($param->getNewWriter());
		$this->pasBebasPanel->render($param->getNewWriter());
		$this->firstPanel->render($param->getNewWriter());
		$this->detailPanel->render($param->getNewWriter());
		$this->jmlPanel->render($param->getNewWriter());
	}
	
	public function noRegLabCek($sender,$param)
	{
		if($this->jmlTransLab->Text != '' && floatval($this->jmlTransLab->Text)==true)
		{
			$param->IsValid=($this->noRegLab->Text != '');	
		}
	}
	
	public function noRegRadCek($sender,$param)
	{
		if($this->jmlTransRad->Text != '' && floatval($this->jmlTransRad->Text)==true)
		{
			$param->IsValid=($this->noRegRad->Text != '');	
		}
	}
	
	public function noRegFisioCek($sender,$param)
	{
		if($this->jmlTransFisio->Text != '' && floatval($this->jmlTransFisio->Text)==true)
		{
			$param->IsValid=($this->noRegFisio->Text != '');	
		}
	}
			
	public function bayarClicked($sender,$param)
    {
		if($this->IsValid)
		{
			if($this->bayar->Text >= $this->getViewState('tmpJml2'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml2'));
				$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->citoCheck->Enabled=false;
				
				$this->cetakBtn->Enabled=true;	
				$this->Page->CallbackClient->focus($this->cetakBtn);	
				$this->errByr->Text='';		
				$this->setViewState('stDelete','1');	
				$this->bayarBtn->Enabled=false;	
				$this->bayar->Enabled=false;	
				$this->DDtrans->Enabled=false;
				$this->modeInput->Enabled=false;
				$this->detailPanel->Enabled=false;
			}
			else
			{
				$this->errByr->Text='Jumlah pembayaran kurang';	
				$this->Page->CallbackClient->focus($this->bayar);
				$this->cetakBtn->Enabled=false;
				$this->bayarBtn->Enabled=true;	
				$this->bayar->Enabled=true;
				$this->DDtrans->Enabled=true;
			}
		}
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
		$idTabel = $this->admRwtJlnGrid->DataKeys[$item->ItemIndex];
		
		
		$sql = "SELECT total
						FROM 
							$nmTable 
						WHERE 
							id='$idTabel' ";
							
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$totalAwal = $row['total'];
		}
		
		
		if($disc < $totalAwal )
		{
			$totalAkhir = $totalAwal - $disc ;
			
			$sql = "UPDATE $nmTable SET total = '$totalAkhir', disc='$disc' WHERE id = '$idTabel' ";
							$this->queryAction($sql,'C');
			$this->showSql->Text = $sql;
		}
		// clear data in session because we need to refresh it from db
		// you could also modify the data in session and not clear the data from session!
		$session = $this->getSession();
		$session->remove("SomeData");  

        $this->admRwtJlnGrid->EditItemIndex=-1;
        $this->bindGrid();
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
	
	public function bindGridApotik()
    {
		$cm=$this->notrans->Text;
		$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$notrans = $this->getViewState('noTransRwtJln');
			$stAsuransi = RwtjlnRecord::finder()->findByPk($notrans)->st_asuransi;
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl
							 FROM 
								tbt_obat_jual_karyawan 
							 WHERE 
								no_trans_rwtjln='$notrans'
								AND flag = '0'
							  ORDER BY st_racik ";
			}
			else
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl
							 FROM 
								tbt_obat_jual 
							 WHERE 
								no_trans_rwtjln='$notrans'
								AND flag = '0'
							  ORDER BY st_racik ";
			}	
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$notrans = $this->getViewState('noTransRwtInap');
			$stAsuransi = RwtInapRecord::finder()->findByPk($notrans)->st_asuransi;
			if($kelompokPasien == '04' && $stAsuransi == '1') //kelompok pasien karyawan
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl
							 FROM 
								tbt_obat_jual_inap_karyawan 
							 WHERE 
								no_trans_inap='$notrans'
								AND flag = '0'
								AND st_bayar = '1'
							  ORDER BY st_racik ";
			}
			else
			{
				$sqlApotik = " SELECT 
								id, id_obat, jumlah, total, st_racik, tgl
							 FROM 
								tbt_obat_jual_inap 
							 WHERE 
								no_trans_inap='$notrans'
								AND flag = '0'
								AND st_bayar = '1'
							  ORDER BY st_racik ";
			}
		}
		elseif($jnsPasien == '2') //pasien bebas
		{
			$notrans = $this->getViewState('noTransRwtBebas');
			$sqlApotik = " SELECT 
							id, id_obat, jumlah, total, st_racik, tgl
						 FROM 
							tbt_obat_jual_lain 
						 WHERE 
							no_trans = '$notrans'
							AND flag = '0'
						  ORDER BY st_racik ";
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
		
		$this->setViewState('jmlHargaApotik',$jmlHargaApotik);	
	}
			
	public function detailNonInap()
    {
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$notrans = $this->getViewState('noTransRwtJln');
			$cm=$this->notrans->Text;
			
			//data untuk datagrid detail tindakan rwtjln
			if($this->citoCheck->Checked==false)
			{
				$sql = "SELECT
							tgl, nama, total,disc,no_trans_asal,no_trans,id_tindakan
						FROM 
							view_biaya_total_rwtjln 
						WHERE 
							cm='$cm' 
							AND no_trans='$notrans'
							AND flag = '0' ";
			}
			else
			{
				$sql = "SELECT
							tgl, nama, (2 * total) AS total,disc,no_trans_asal,no_trans,id_tindakan
						FROM 
							view_biaya_total_rwtjln 
						WHERE 
							cm='$cm' 
							AND no_trans='$notrans'
							AND flag = '0' ";
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
													 total float(15,0) default NULL,
													 disc float(15,0) default '0',	
													 no_trans_asal VARCHAR(20) NOT NULL,
													 no_trans VARCHAR(20) NOT NULL,
													 id_tindakan VARCHAR(20) NOT NULL,						 							 
													 PRIMARY KEY (id)) ENGINE = MEMORY";
						
						$this->queryAction($sql,'C');//Create new tabel bro...
						$nama = $row['nama'];
						$tgl = $row['tgl'];
						$total = $row['total'];
						$disc = $row['disc'];
						$no_trans_asal = $row['no_trans_asal'];
						$no_trans = $row['no_trans'];
						$id_tindakan = $row['id_tindakan'];
						
						$sql="INSERT INTO $nmTable (nama,tgl,total,disc,no_trans_asal,no_trans,id_tindakan) VALUES ('$nama','$tgl','$total','$disc','$no_trans_asal','$no_trans','$id_tindakan')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
						
					}
					else
					{
						$nmTable = $this->getViewState('nmTable');
						$nama = $row['nama'];
						$tgl = $row['tgl'];
						$total = $row['total'];
						$disc = $row['disc'];
						$no_trans_asal = $row['no_trans_asal'];
						$no_trans = $row['no_trans'];
						$id_tindakan = $row['id_tindakan'];
						
						$sql="INSERT INTO $nmTable (nama,tgl,total,disc,no_trans_asal,no_trans,id_tindakan) VALUES ('$nama','$tgl','$total','$disc','$no_trans_asal','$no_trans','$id_tindakan')";
						$this->queryAction($sql,'C');//Insert new row in tabel bro...
					}
				
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
			
			
			$this->bindGridApotik();
			$jmlHargaApotik = $this->getViewState('jmlHargaApotik');
			
			$jmlTotalRwtJln = $jmlHargaApotik;
			
			$jmlHargaAsli = $jmlHargaApotik;
			$jmlHargaBulat = $this->bulatkan($jmlTotalRwtJln);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$this->bindGridApotik();
			$jmlHargaApotik = $this->getViewState('jmlHargaApotik');
			
			$jmlTotalRwtJln = $jmlHargaApotik;
			
			$jmlHargaAsli = $jmlHargaApotik;
			$jmlHargaBulat = $this->bulatkan($jmlTotalRwtJln);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
		}
		elseif($jnsPasien == '2') //pasien rawat bebas
		{
			$this->bindGridApotik();
			$jmlHargaApotik = $this->getViewState('jmlHargaApotik');
			
			$jmlTotalRwtJln = $jmlHargaApotik;
			
			$jmlHargaAsli = $jmlHargaApotik;
			$jmlHargaBulat = $this->bulatkan($jmlTotalRwtJln);  		
			$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
			
			$this->setViewState('tmpJml',$jmlHargaBulat);
			$this->setViewState('sisaBulat',$sisaBulat);
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
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
		$this->detailPanel->focus();
	}
	
	
	public function jmlTransLabChanged()
    {
		$jmlTransLab = $this->jmlTransLab->Text;
		if($jmlTransLab != '')
		{
			if($jmlTransLab < 60000)
			{
				$this->admLab->Text = 5000;
			}
			
			if($jmlTransLab >= 60000 && $jmlTransLab < 350000)
			{
				$this->admLab->Text = 10000;
			}
			
			if($jmlTransLab >= 350000)
			{
				$this->admLab->Text = 15000;	 	
			}
		}
		else
		{
			$this->admLab->Text = '';	
		}
	}
	
	public function jmlTransRadChanged()
    {
		$jmlTransRad = $this->jmlTransRad->Text;
		if($jmlTransRad != '')
		{
				$this->admRad->Text = 10000;
			
		}	
		else
		{
			$this->admRad->Text = '';	
		}
	}
	
	public function jmlTransFisioChanged()
    {
		$jmlTransFisio = $this->jmlTransFisio->Text;
		if($jmlTransFisio != '')
		{
			if($jmlTransFisio < 60000)
			{
				$this->admFisio->Text = 5000;
			}
			
			if($jmlTransFisio >= 60000 && $jmlTransFisio < 350000)
			{
				$this->admFisio->Text = 10000;
			}
			
			if($jmlTransFisio >= 350000)
			{
				$this->admFisio->Text = 15000;	 	
			}
		}
		else
		{
			$this->admFisio->Text = '';	
		}
	}
	
	public function detailInap()
    {
		$cm = $this->notrans->Text;
		$noTrans = $this->ambilTxt($this->DDtrans);
		
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
	
	
	public function batalClicked()
    {	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');//Clear the view state	
		}
		
		$this->clearViewState('tmpJml');
		$this->clearViewState('tmpJml2');
		$this->clearViewState('sisaBulat');	
		
		$this->clearViewState('stDisc');
		$this->clearViewState('totBiayaDisc');
		$this->clearViewState('totBiayaDiscBulat');
		$this->clearViewState('sisaDiscBulat');
		
		$this->Response->reload();
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
	
	
	public function cetakClicked($sender,$param)
    {
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml2');
		
		$tglNow=date('Y-m-d');
		$wktNow=date('G:i:s');
		
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;	
			
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //pasien rawat jalan
		{
			$notrans = $this->getViewState('noTransRwtJln');
			$cm=$this->notrans->Text;
			$dokter=$this->getViewState('dokter');
			$id_klinik = RwtjlnRecord::finder()->findByPk($notrans)->id_klinik;
			$nama=$this->nama->Text;
			
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
							st_flag = 1 
						WHERE 
						no_trans_rwtjln = '$notrans'
						AND st_flag = 0
						AND id_tindakan = '$id_tindakan'";	
					$this->queryAction($sql,'C');
				}
				
			}
			
			//INSERT tbt_lab_penjualan
			if($this->jmlTransLab->Text != '' )
			{
				$transRwtJln= new LabJualRecord();
				$transRwtJln->no_trans = $this->noRegLab->Text;
				$transRwtJln->no_trans_rwtjln = $notrans;				
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan = '';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransLab->Text;
				$transRwtJln->dokter = $dokter;
				$transRwtJln->klinik = $id_klinik;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			//INSERT tbt_rad_penjualan
			if($this->jmlTransRad->Text != '' )
			{
				$transRwtJln= new RadJualRecord();
				$transRwtJln->no_trans = $this->noRegRad->Text;
				$transRwtJln->no_trans_rwtjln=$notrans;
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan='';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->film_size='';
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->klinik=$id_klinik;
				$transRwtJln->dokter=$dokter;
				$transRwtJln->harga=$this->jmlTransRad->Text;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			//INSERT tbt_fisio_penjualan
			if($this->jmlTransFisio->Text != '' )
			{
				$transRwtJln= new FisioJualRecord();
				$transRwtJln->no_trans = $this->noRegFisio->Text;
				$transRwtJln->no_trans_rwtjln = $notrans;				
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan = '';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransFisio->Text;
				$transRwtJln->dokter = $dokter;
				$transRwtJln->klinik = $id_klinik;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			//Update tbt_obat_jual / tbt_obat_jual_karyawan
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			$stAsuransi = RwtjlnRecord::finder()->findByPk($notrans)->st_asuransi;
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
					$sql="UPDATE tbt_obat_jual_karyawan SET flag='1' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
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
					$sql="UPDATE tbt_obat_jual SET flag='1' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
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
					wkt_kasir = '$wktNow' 
				  WHERE 
					no_trans='$notrans' 
					AND flag='0' ";
				
			$this->queryAction($sql,'C');
			
			
			if($this->getViewState('nmTable'))
			{
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
				$this->clearViewState('nmTable');//Clear the view state	
			}
			
			$stRujuk = RwtjlnRecord::finder()->findByPk($notrans)->st_rujuk;
			$nmPerujuk = RwtjlnRecord::finder()->findByPk($notrans)->nm_perujuk;
			$no_trans_rwt_jln = $notrans;
			$no_trans_rwt_inap = '';
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$notrans = $this->getViewState('noTransRwtInap');
			$cm=$this->notrans->Text;
			$dokter=$this->getViewState('dokter');
			$nama=$this->nama->Text;
			
			//INSERT tbt_lab_penjualan_inap
			if($this->jmlTransLab->Text != '' )
			{
				$transRwtJln= new LabJualInapRecord();
				$transRwtJln->no_trans=$this->noRegLab->Text;
				$transRwtJln->no_trans_inap=$notrans;
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan='';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->harga=$this->jmlTransLab->Text;
				$transRwtJln->dokter=$id_dokter;
				$transRwtJln->klinik='';
				
				
				if($this->collectSelectionResult($this->modeBayarLab)=='0')
				{
					$transRwtJln->st_bayar='0';
					$transRwtJln->flag='0';
				}else{				
					$transRwtJln->st_bayar='1';
					$transRwtJln->flag='1';
				}				
				$transRwtJln->Save();
			}
			
			//INSERT tbt_rad_penjualan
			if($this->jmlTransRad->Text != '' )
			{
				$transRwtJln= new RadJualInapRecord();
				$transRwtJln->no_trans = $this->noRegRad->Text;
				$transRwtJln->no_trans_inap=$notrans;
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan='';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->film_size='';
				$transRwtJln->operator=$nipTmp;
				$transRwtJln->dokter=$dokter;
				$transRwtJln->harga=$this->jmlTransRad->Text;
				
				
				if($this->collectSelectionResult($this->modeBayarRad)=='0')
				{
					$transRwtJln->st_bayar='0';
					$transRwtJln->flag='0';
				}else{				
					$transRwtJln->st_bayar='1';
					$transRwtJln->flag='1';
				}			
				
				$transRwtJln->Save();
			}
			
			//INSERT tbt_fisio_penjualan
			if($this->jmlTransFisio->Text != '' )
			{
				$transRwtJln= new FisioJualInapRecord();
				$transRwtJln->no_trans = $this->noRegFisio->Text;
				$transRwtJln->no_trans_inap = $notrans;				
				$transRwtJln->cm=$cm;
				$transRwtJln->id_tindakan = '';
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransFisio->Text;
				$transRwtJln->dokter = $dokter;
				$transRwtJln->klinik = '';
				
				
				if($this->collectSelectionResult($this->modeBayarFisio)=='0')
				{
					$transRwtJln->st_bayar='0';
					$transRwtJln->flag='0';
				}else{				
					$transRwtJln->st_bayar='1';
					$transRwtJln->flag='1';
				}		
				
				$transRwtJln->Save();
			}
			
			//Update tbt_obat_jual / tbt_obat_jual_karyawan
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			$stAsuransi = RwtInapRecord::finder()->findByPk($notrans)->st_asuransi;
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
					$sql="UPDATE tbt_obat_jual_inap_karyawan SET flag='1' WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag='0' ";		
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
					$sql="UPDATE tbt_obat_jual_inap SET flag='1' WHERE cm='$cm' AND no_trans_inap='$notrans' AND flag='0' AND st_bayar = '1'";		
					$this->queryAction($sql,'C');
				}
			}	
			
			$stRujuk = RwtInapRecord::finder()->findByPk($notrans)->st_rujukan;
			$nmPerujuk = RwtInapRecord::finder()->findByPk($notrans)->nm_perujuk;
			$no_trans_rwt_jln = '';
			$no_trans_rwt_inap = $notrans;
		}	
		elseif($jnsPasien == '2') //pasien bebas
		{
			$notrans = $this->numCounter('tbt_rawat_jalan_lain',RwtjlnLainRecord::finder(),'23');
			$pilBebas = $this->getViewState('pilPasBebas');
			if( $pilBebas == '0'){//Bilas pasien bebas dari apotik
				$nama=$this->nama->Text;
			}else{
				$nama=$this->nmPas->Text;
			}
			//INSERT tbt_rawat_jalan_lain
			$transRwtJlnLain = new RwtjlnLainRecord();
			$transRwtJlnLain->no_trans = $notrans;
			$transRwtJlnLain->nama = $this->nama->Text;
			$transRwtJlnLain->kasir = $nipTmp;
			$transRwtJlnLain->tgl = $tglNow;
			$transRwtJlnLain->wkt = $wktNow;
			$transRwtJlnLain->flag='1';
			$transRwtJlnLain->Save();
				
			//INSERT tbt_lab_penjualan_lain
			if($this->jmlTransLab->Text != '' )
			{
				$transRwtJln= new LabJualLainRecord();
				$transRwtJln->no_trans = $this->noRegLab->Text;
				$transRwtJln->no_trans_rwtjln_lain = $notrans;				
				$transRwtJln->nama=$this->nama->Text;
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransLab->Text + $this->admLab->Text;
				$transRwtJln->harga_non_adm = $this->jmlTransLab->Text;
				$transRwtJln->harga_adm = $this->admLab->Text;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			//INSERT tbt_rad_penjualan_lain
			if($this->jmlTransRad->Text != '' )
			{
				$transRwtJln= new RadJualLainRecord();
				$transRwtJln->no_trans = $this->noRegRad->Text;
				$transRwtJln->no_trans_rwtjln_lain = $notrans;				
				$transRwtJln->nama=$this->nama->Text;
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransRad->Text + $this->admRad->Text;
				$transRwtJln->harga_non_adm = $this->jmlTransRad->Text;
				$transRwtJln->harga_adm = $this->admRad->Text;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			//INSERT tbt_fisio_penjualan_lain
			if($this->jmlTransFisio->Text != '' )
			{
				$transRwtJln= new FisioJualLainRecord();
				$transRwtJln->no_trans = $this->noRegFisio->Text;
				$transRwtJln->no_trans_rwtjln_lain = $notrans;				
				$transRwtJln->nama=$this->nama->Text;
				$transRwtJln->tgl = $tglNow;
				$transRwtJln->wkt = $wktNow;
				$transRwtJln->operator = $nipTmp;
				$transRwtJln->harga = $this->jmlTransFisio->Text + $this->admFisio->Text;
				$transRwtJln->harga_non_adm = $this->jmlTransFisio->Text;
				$transRwtJln->harga_adm = $this->admFisio->Text;
				$transRwtJln->flag='1';
				$transRwtJln->Save();
			}
			
			$noTransRwtBebas = $this->getViewState('noTransRwtBebas');
			//Update tbt_obat_jual_lain
			$sql = "SELECT
					no_trans
				FROM 
					tbt_obat_jual_lain
				WHERE 
					no_trans = '$noTransRwtBebas'
					AND flag = '0'";
			$arr=$this->queryAction($sql,'S');
			if(count($arr)>0) //jika ada transaksi apotik, UPDATE flag di tbt_obat_jual_lain
			{
				$sql="UPDATE tbt_obat_jual_lain SET flag='1' WHERE no_trans ='$noTransRwtBebas' AND flag='0' ";		
				$this->queryAction($sql,'C');
			}
			
			
			$stRujuk = '0';
		}	
			
		//komisi perujuk
		if($stRujuk == '1')//jika pasien rujukan => INSERT ke tbt_komisi_trans 
		{
			$newKomisi= new KomisiTransRecord();
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
						'jnsPasien'=>$jnsPasien
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
						'jnsPasien'=>$jnsPasien
					)));
			}				
		}	
		else
		{
			$this->Response->reload();
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
		//$notrans=$this->ambilTxt($this->DDtrans);
		
		
		if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
		{
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
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
					$sql="UPDATE tbt_obat_jual SET flag='1' WHERE cm='$cm' AND no_trans_rwtjln='$notrans' AND flag='0' ";		
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
			$notrans = $this->ambilTxt($this->DDtrans);
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
