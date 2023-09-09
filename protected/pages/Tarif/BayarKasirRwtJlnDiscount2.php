<?php
class BayarKasirRwtJlnDiscount2 extends SimakConf
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
			
		if(!$this->IsPostBack)
		{					
			//$this->notrans->Focus();
			$this->citoCheck->Focus();
			$this->citoCheck->Checked=false;
			$this->noTransCtrl->Visible=false;
			$this->showSecond->Visible=false;
			//$this->showBayar->Visible=false;
			$this->cetakBtn->Enabled=false;
			$this->notrans->Focus();
		}		
		
		if($this->getViewState('nmTable'))
		{
			$this->bindGrid();
		}
		
		$this->rekapBayar();
    }
	
	public function CBrwtInapCheck($sender, $param)
	{
		if($this->CBrwtInap->Checked==false)
		{
			$this->jnsTransCtrl->Visible=false; 
			$this->modeInputCtrl->Visible=true;			
			$this->modeInput->SelectedIndex='0';
		}
		else
		{			
			$this->jnsTransCtrl->Visible=true; //tampilkan jenis transaksi
			$this->modeInputCtrl->Visible=false;
			$this->modeInputTrans->SelectedIndex=-1;			
		}
		
		$this->notrans->Enabled = false;
		$this->notrans->Text = '';
		$this->errMsg->Text='';	
		$this->noTransCtrl->Visible=false;
		$this->modeInput->SelectedIndex=-1;
		$this->showSecond->Visible=false;
		$this->detailPanel->Visible = false ;
		$this->dgTdkCtrl->Visible = false;
		$this->dgLabCtrl->Visible = false;
		$this->dgRadCtrl->Visible = false;
		$this->dgApotikCtrl->Visible = false;
	}
	
	public function modeInputChanged($sender, $param)
	{
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //Jika bukan pasien rujukan
		{
			$this->notrans->Enabled = true;
			$this->notrans->focus();
			$this->jnsTransCtrl->Visible=false;
			
		}
		elseif($jnsPasien == '1') //Jika pasien rujukan
		{
			$this->notrans->Enabled = false;
			$this->notrans->Text = '';
			$this->errMsg->Text='';
			
			$this->jnsTransCtrl->Visible=true; //tampilkan jenis transaksi
			$this->noTransCtrl->Visible=false;
			$this->citoCheck->Enabled=true;
		}
		$this->modeInputTrans->SelectedIndex = -1;
		$this->noTransCtrl->Visible=false;
		$this->showSecond->Visible=false;
		$this->modeInputTrans->Enabled=true;
		$this->detailPanel->Visible=false;
		$this->errMsg->Text='';	
	}	
	
	//Jika yang dipilih pasien rujukan tampilkan pilihan jenis transaksi
	public function modeInputTransChanged($sender, $param)
	{
		$this->showFirst->Visible=true;
		$this->noTransCtrl->Visible=false;
		$this->showSecond->Visible=false;
		$this->notrans->Text = '';
		$this->notrans->focus();		
					
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
				
					$this->noTransCtrl->Visible=true;
					$this->citoCheck->Enabled=false;
					$this->errMsg->Text='';
				}
				else
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi laboratorium pasien rujukan belum ada !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}
			}
			else //jika checkbox rawat inap dipilih
			{			
				$this->notrans->Enabled = true;
				$this->notrans->focus();	
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
				
					$this->noTransCtrl->Visible=true;
					$this->citoCheck->Enabled=false;
					$this->errMsg->Text='';
				}
				else
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi radiologi pasien rujukan belum ada !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}
			}
			else //jika checkbox rawat inap dipilih
			{			
				$this->notrans->Enabled = true;
				$this->notrans->focus();	
			}
		}
	}
		
		
	public function checkRegister($sender,$param)
    {
		$cm = $this->notrans->Text;
		
		if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
		{
			$jnsPasien = $this->collectSelectionResult($this->modeInput);
			if($jnsPasien == '0') //jika bukan pasien luar/rujukan
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
					
					//$this->setViewState('petugasPoli',$petugasPoli);
				
					$this->noTransCtrl->Visible=true;
					$this->citoCheck->Enabled=false;
					$this->errMsg->Text='';
				}
				else
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Pasien dengan No. Register '.$cm.' tidak ada !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}
			}
		}
		else //jika checkbox rawat inap dipilih
		{			
			$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
			if($jnsTrans == '0') //Jika pasien rawat inap & pilih transaksi lab
			{		
				$cekPasien = LabJualInapRecord::finder()->find('cm = ? AND flag = ? AND st_bayar = ?', array($this->notrans->Text,'0','1'));
				if($cekPasien) //jika ada data
				{	
					$sql="SELECT 
						no_trans,
						operator
					FROM 
						tbt_lab_penjualan_inap
					WHERE 
						cm='$cm' 
						AND flag='0'
						AND st_bayar='1'
					GROUP BY no_trans ";
											
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$data .= $row['no_trans'] .',';	
						$petugasLab = $row['operator'];	
					}			
					
					$v = strlen($data) - 1;
					$var=substr($data,0,$v);				
					$temp = explode(',',$var);
					
					$this->DDtrans->DataSource=$temp;
					$this->DDtrans->dataBind();
					
					$this->setViewState('petugasLab',$petugasLab);
					$this->noTransCtrl->Visible=true;
					
					//$this->showSql->Text= $sql;
				}
				else //jika data tidak ditemukan
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi bayar tunai laboratorium pasien rawat inap belum ada !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}				
			}
			elseif($jnsTrans == '1') //Jika pasien rawat inap & pilih transaksi rad
			{
				$cekPasien = RadJualInapRecord::finder()->find('cm = ? AND flag = ? AND st_bayar = ?', array($this->notrans->Text,'0','1'));
				if($cekPasien) //jika ada data
				{	
					$sql="SELECT 
						no_trans,
						operator
					FROM 
						tbt_rad_penjualan_inap
					WHERE 
						cm='$cm' 
						AND flag='0'
						AND st_bayar='1'
					GROUP BY no_trans ";
											
					$arr=$this->queryAction($sql,'S');
					foreach($arr as $row)
					{
						$data .= $row['no_trans'] .',';	
						$petugasLab = $row['operator'];	
					}			
					
					$v = strlen($data) - 1;
					$var=substr($data,0,$v);				
					$temp = explode(',',$var);
					
					$this->DDtrans->DataSource=$temp;
					$this->DDtrans->dataBind();
					
					$this->setViewState('petugasLab',$petugasLab);
					$this->noTransCtrl->Visible=true;
					
					//$this->showSql->Text= $sql;
				}
				else //jika data tidak ditemukan
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Data transaksi bayar tunai radiologi pasien rawat inap belum ada !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}
			}
		}
    }	
	
	public function DDtransChanged()
	{
		if($this->DDtrans->SelectedValue!='')
		{
			if($this->CBrwtInap->Checked==false) //jika checkbox rawat inap tidak dipilih
			{
				$this->checkNoTrans();
				
				$jnsPasien = $this->collectSelectionResult($this->modeInput);
				if($jnsPasien == '0') //jika bukan pasien luar/rujukan
				{	
					$this->besarDiscCtrl->Visible=true;
					$this->disc->focus();
				}
				elseif($jnsPasien == '1') //jika pasien luar/rujukan
				{
					$this->besarDiscCtrl->Visible=false;	
					$this->bayar->focus();
				}
				
				
				
			}
			else //jika checkbox rawat inap dipilih
			{
				$this->checkNoTransInap();
				$this->besarDiscCtrl->Visible=false;	
				$this->bayar->focus();
			}			
		}
		else
		{
			$this->showSecond->Visible=false;
			$this->errMsg->Text='';	
		}
	}
	
	public function checkNoTrans()
    {
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
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
				$this->showSecond->Visible=false;
			}
			else
			{
				$sql ="SELECT 
							SUM(total) AS total
						 FROM 
							view_biaya_total_rwtjln 
						 WHERE 
							no_trans='$noTransRwtJln'
							AND flag = '0' ";
				
				$arr=$this->queryAction($sql,'S');
				//$this->showSql->Text= $sql;
				
				foreach($arr as $row)
				{			
					/*
					if($this->citoCheck->Checked==false)
					{
						$jmlHarga += $row['total'];
					}
					else
					{
						$jmlHarga += 2 * $row['total'];
					}
					*/			
				}
				
				
				if($kelompokPasien != '04') //kelompok pasien bukan karyawan
				{
					$sql ="SELECT 
								total
							 FROM 
								tbt_obat_jual 
							 WHERE 
								no_trans_rwtjln='$noTransRwtJln'
								AND flag = '0' ";
				}
				else
				{
					$sql ="SELECT 
								total
							 FROM 
								tbt_obat_jual_karyawan 
							 WHERE 
								no_trans_rwtjln='$noTransRwtJln'
								AND flag = '0' ";
				}
				
				$arr=$this->queryAction($sql,'S');
				$this->showSql->Text= $sql;
				
				foreach($arr as $row)
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
				
				$jmlTotalRwtJln = $jmlHarga + $jmlHargaApotik;
				
				if($jmlTotalRwtJln >0 || $jmlTotalRwtJln != NULL) //jika ada transaksi tdk rwtjln/lab/rad/apotik
				{
					$jmlHargaAsli = $jmlTotalRwtJln;
					$jmlHargaBulat = $this->bulatkan($jmlTotalRwtJln);  		
					$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
					
					//$tmpPasien = $activeRec->findBySql($sql);
					$nmPas = PasienRecord::finder()->findByPk($cm)->nama;			
					$this->setViewState('nama',$nmPas);
					
					$this->nama->Text= $nmPas;	
					$this->setViewState('tmpJml',$jmlHargaBulat);
					$this->setViewState('sisaBulat',$sisaBulat);
					$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
					
					$this->showSecond->Visible=true;
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';	
					$this->detailPanel->Visible = true ;
					$this->detailNonInap();
				}
				else //jika tidak ada transaksi tdk rwtjln/lab/rad
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Tidak ada transaksi Tindakan Rawat Jalan / Lab / Rad / Apotik!';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
					$this->detailNonInap();
					
				}				
			}
		}
		elseif($jnsPasien == '1') //cek no_trans pasien rujukan
		{
			$noTransRujuk = $this->DDtrans->SelectedValue;	
			$this->setViewState('noTransRujuk',$noTransRujuk);
			$dateNow = date('Y-m-d');
			
			$jnsTrans = $this->collectSelectionResult($this->modeInputTrans);
			if($jnsTrans == '0') //Jika pasien rujukan & pilih transaksi lab
			{		
				$sql="SELECT 
						SUM(harga) AS total	
					FROM 
						tbt_lab_penjualan_lain
					WHERE 
						no_trans='$noTransRujuk'
						AND flag='0'
						AND tgl='$dateNow'";
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
				
				if($jmlHarga >0 || $jmlHarga != NULL) //jika ada transaksi lab u/ pasien rujukan
				{
					$jmlHargaAsli = $jmlHarga;
					$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
					$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
					
					//$tmpPasien = $activeRec->findBySql($sql);
					$nmPas = LabJualLainRecord::finder()->find('no_trans = ?',$noTransRujuk)->cm;			
					$this->setViewState('nama',$nmPas);
					
					$this->nama->Text= $nmPas;	
					$this->setViewState('tmpJml',$jmlHargaBulat);
					$this->setViewState('sisaBulat',$sisaBulat);
					$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
					
					$this->showSecond->Visible=true;
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';		
				}
				else //jika tidak ada transaksi lab u/ pasien rujukan
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Tidak ada transaksi Lab !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}				
			}
			elseif($jnsTrans == '1') //Jika pasien rujukan & pilih transaksi rad
			{		
				$sql="SELECT 
						SUM(harga) AS total	
					FROM 
						tbt_rad_penjualan_lain
					WHERE 
						no_trans='$noTransRujuk'
						AND flag='0'
						AND tgl='$dateNow'";
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
				
				if($jmlHarga >0 || $jmlHarga != NULL) //jika ada transaksi rad u/ pasien rujukan
				{
					$jmlHargaAsli = $jmlHarga;
					$jmlHargaBulat = $this->bulatkan($jmlHarga);  		
					$sisaBulat = $jmlHargaBulat - $jmlHargaAsli;
					
					//$tmpPasien = $activeRec->findBySql($sql);
					$nmPas = RadJualLainRecord::finder()->find('no_trans = ?',$noTransRujuk)->cm;			
					$this->setViewState('nama',$nmPas);
					
					$this->nama->Text= $nmPas;	
					$this->setViewState('tmpJml',$jmlHargaBulat);
					$this->setViewState('sisaBulat',$sisaBulat);
					$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
					
					$this->showSecond->Visible=true;
					$this->notrans->Enabled=false;
					$this->errMsg->Text='';	
				}
				else //jika tidak ada transaksi rad u/ pasien rujukan
				{
					$this->showFirst->Visible=true;
					$this->noTransCtrl->Visible=false;
					$this->showSecond->Visible=false;
					$this->errMsg->Text='Tidak ada transaksi Lab !';
					$this->notrans->Text = '';
					$this->notrans->focus();				
					$this->citoCheck->Enabled=true;
				}
			}	
		}
    }	
	
	
	public function rekapBayar()
    {
		if($this->getViewState('tmpJml'))
		{
			$jmlBayar = $this->getViewState('tmpJml');
			
			if($this->jmlTransLab->Text != '' && floatval($this->jmlTransLab->Text)==true)
			{
				$jmlBayar += $this->jmlTransLab->Text + $this->admLab->Text;
			}
			
			if($this->jmlTransRad->Text != '' && floatval($this->jmlTransRad->Text)==true)
			{
				$jmlBayar += $this->jmlTransRad->Text + $this->admRad->Text;
			}
			
			if($this->jmlTransFisio->Text != '' && floatval($this->jmlTransFisio->Text)==true)
			{
				$jmlBayar += $this->jmlTransFisio->Text + $this->admFisio->Text;
			}
			
			if($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				$sql = "SELECT * FROM $nmTable  ";
				$arrData = $this->queryAction($sql,'S');
				foreach($arrData as $row)
				{
					if($this->citoCheck->Checked==false)
					{
						$jmlBayar += $row['total'];
					}
					else
					{
						$jmlBayar += 2 * $row['total'];
					}
				}
				
				
				$notrans = $this->getViewState('noTransRwtJln');
				//data untuk datagrid detail apotik
				$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
				if($kelompokPasien != '04') //kelompok pasien bukan karyawan
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
				else
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
				$arrData=$this->queryAction($sqlApotik,'S');//Select row in tabel bro...
				if(count($arrData) > 0)
				{
					$this->apotikRwtJlnGrid->Visible = true;
					$this->apotikRwtJlnGrid->DataSource=$arrData;
					$this->apotikRwtJlnGrid->dataBind();
					$this->apotikMsg->Text = '';
				}
				else
				{
					$this->apotikRwtJlnGrid->Visible = false;
					$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';			
				}
			}
			
			
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
			$this->discCtrl->Visible = true;
			
			$this->setViewState('stDisc','1');
			$this->setViewState('totBiayaDisc',$totBiayaDisc);
			$this->setViewState('totBiayaDiscBulat',$totBiayaDiscBulat);
			$this->setViewState('sisaDiscBulat',$sisaDiscBulat);
		}
		else
		{
			$this->jmlBiayaDisc->Text = '';
			$this->discCtrl->Visible = false;
			$this->clearViewState('stDisc');
			$this->clearViewState('totBiayaDisc');
			$this->clearViewState('totBiayaDiscBulat');
			$this->clearViewState('sisaDiscBulat');
		}
		
		$this->bayar->focus();
	}
	
	public function bayarClicked($sender,$param)
    {
		if($this->getViewState('stDisc') == '1') //jika ada discount
		{
			if($this->bayar->Text >= $this->getViewState('totBiayaDiscBulat'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('totBiayaDiscBulat'));
				$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->citoCheck->Enabled=false;
				
				$this->cetakBtn->Enabled=true;	
				$this->cetakBtn->Focus();	
				$this->errByr->Text='';		
				$this->setViewState('stDelete','1');	
				$this->bayarBtn->Enabled=false;	
				$this->bayar->Enabled=false;	
				$this->DDtrans->Enabled=false;
				$this->CBrwtInap->Enabled=false;
				$this->modeInput->Enabled=false;
				$this->modeInputTrans->Enabled=false;
				$this->detailClicked();
			}
			else
			{
				$this->errByr->Text='Jumlah pembayaran kurang';	
				$this->bayar->Focus();
				$this->cetakBtn->Enabled=false;
				$this->bayarBtn->Enabled=true;	
				$this->bayar->Enabled=true;
				$this->DDtrans->Enabled=true;
			}
		}
		else
		{
			if($this->bayar->Text >= $this->getViewState('tmpJml2'))
			{
				$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml2'));
				$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
				$this->setViewState('sisa',$hitung);
				
				$this->citoCheck->Enabled=false;
				
				$this->cetakBtn->Enabled=true;	
				$this->cetakBtn->Focus();	
				$this->errByr->Text='';		
				$this->setViewState('stDelete','1');	
				$this->bayarBtn->Enabled=false;	
				$this->bayar->Enabled=false;	
				$this->DDtrans->Enabled=false;
				$this->CBrwtInap->Enabled=false;
				$this->modeInput->Enabled=false;
				$this->modeInputTrans->Enabled=false;
				$this->detailPanel->Enabled=false;
				
				//$this->detailClicked();
				
			}
			else
			{
				$this->errByr->Text='Jumlah pembayaran kurang';	
				$this->bayar->Focus();
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
			
	public function detailNonInap()
    {
		$jnsPasien = $this->collectSelectionResult($this->modeInput);
		if($jnsPasien == '0') //jika bukan pasien luar/rujukan
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
				$this->admRwtJlnGrid->Visible = false;
				$this->tdkMsg->Text = 'Tidak Ada Transaksi Tindakan Rawat Jalan';
			}
			
			
			
			
			
			//data untuk datagrid detail apotik
			$kelompokPasien = PasienRecord::finder()->findByPk($this->notrans->Text)->kelompok;
			if($kelompokPasien != '04') //kelompok pasien bukan karyawan
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
			else
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
			$arrData=$this->queryAction($sqlApotik,'S');//Select row in tabel bro...
			if(count($arrData) > 0)
			{
				$this->apotikRwtJlnGrid->Visible = true;
				$this->apotikRwtJlnGrid->DataSource=$arrData;
				$this->apotikRwtJlnGrid->dataBind();
				$this->apotikMsg->Text = '';
			}
			else
			{
				$this->apotikRwtJlnGrid->Visible = false;
				$this->apotikMsg->Text = 'Tidak Ada Transaksi Apotik';			
			}
			
			$this->dgTdkCtrl->Visible = true;
			$this->dgLabCtrl->Visible = true;
			$this->dgRadCtrl->Visible = true;
			$this->dgApotikCtrl->Visible = true;			
		}
		elseif($jnsPasien == '1') //cek no_trans pasien rujukan
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
				
				$this->dgTdkCtrl->Visible = false;
				$this->dgLabCtrl->Visible = true;
				$this->dgRadCtrl->Visible = false;
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
				
				$this->dgTdkCtrl->Visible = false;
				$this->dgLabCtrl->Visible = false;
				$this->dgRadCtrl->Visible = true;
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
			
			$this->dgTdkCtrl->Visible = false;
			$this->dgLabCtrl->Visible = true;
			$this->dgRadCtrl->Visible = false;
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
			
			$this->dgTdkCtrl->Visible = false;
			$this->dgLabCtrl->Visible = false;
			$this->dgRadCtrl->Visible = true;
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
		$this->notrans->Focus();
		$this->noTransCtrl->Visible=false;
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
			if($jnsPasien == '0') //jika bukan pasien luar/rujukan
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
