<?php
class KasirRad extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
			$this->Response->redirect($this->Service->constructUrl('login'));
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
		}		
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{			
			if($this->getViewState('nmTable'))
			{		
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->clearViewState('tmpJml');
			$this->getViewState('sisa');
			$this->bayar->Text;			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';
			$this->errByr->Text='';
			$this->errMsg->Text='';
			$this->bayar->Text='';
			$this->notrans->Focus();
			$this->noTransCtrl->Visible=false;
			$this->showSecond->Visible=false;
			
			$this->Response->redirect($this->Service->constructUrl('Tarif.KasirRad',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	/*	
	public function checkRegister($sender,$param)
    {
		$jnsRujukan = $this->collectSelectionResult($this->modeInput);
		if($jnsRujukan == '0')
		{
			$activeRec = RadJualRecord::finder();
		}	
		elseif($jnsRujukan == '1')
		{
			$activeRec = RadJualLainRecord::finder();
		}
        // valid if the username is not found in the database
        if($activeRec->find('cm = ? AND flag = ? AND tgl = ?', array($this->notrans->Text,'0',date('Y-m-d'))))
		{			
			$tmp = $this->notrans->Text;
			if($jnsRujukan == '0')
			{
				$sql ="SELECT 
						b.nama AS id_tindakan,
						b.cm,
						a.cm,
						a.no_trans,
						a.harga
			  		 FROM tbt_rad_penjualan a, 
			         	  tbd_pasien b 
			  		 WHERE a.cm='$tmp' AND a.cm=b.cm
					 ";
			}	
			elseif($jnsRujukan == '1')
			{
				$sql ="SELECT 
						b.nama AS id_tindakan,
						b.cm,
						a.cm,
						a.no_trans,
						a.harga
			  		 FROM tbt_rad_penjualan_lain a, 
			         	  tbd_pasien b 
			  		 WHERE a.cm='$tmp' AND a.cm=b.cm
					 ";
			}
			
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				//$jmlHarga += $row['harga'];				
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['harga'];
				}
				else
				{
					$jmlHarga += 2 * $row['harga'];
				}			
			}
					
			$tmpPasien = $activeRec->findBySql($sql);
			
			$this->setViewState('nama',$tmpPasien->id_tindakan);	
			$this->setViewState('notrans',$this->notrans->Text);
			$this->setViewState('tmpJml',$jmlHarga);
			$this->setViewState('cm',$tmpPasien->cm);
			
			$this->nama->Text= $tmpPasien->id_tindakan;			
			$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
			
			
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';			
			
			$this->modeInput->Enabled=false;
			$this->citoCheck->Enabled=false;
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
			
			$this->modeInput->Enabled=true;
			$this->citoCheck->Enabled=true;
		}
    }	
	*/
	
	public function modeInputChanged($sender,$param)
    {
		$this->notrans->Text = '';
		$this->errMsg->Text = '';
		$this->notrans->focus();
		$this->asalPasien->SelectedIndex = -1;
		
		$jnsRujukan = $this->collectSelectionResult($this->modeInput);
		if($jnsRujukan == '0')
		{
			//$this->notrans->Enabled = true;
			$this->noTransCtrl->Visible=false;
			$this->asalPasien->Enabled = true;
			
		}	
		elseif($jnsRujukan == '1')
		{
			$this->asalPasien->Enabled = false;
			$this->notrans->Enabled = false;
			
			$activeRec = RadJualLainRecord::finder();
			$nmTbl = 'tbt_rad_penjualan_lain';
			
			$cm = $this->notrans->Text;
						
			$sql="SELECT 
					no_trans,
					operator
				FROM 
					$nmTbl
				WHERE  
					flag='0'
				GROUP BY no_trans ";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr)!=0)
			{
				foreach($arr as $row)
				{
					$data .= $row['no_trans'] .',';	
					$petugasRad = $row['operator'];	
				}			
				
				$v = strlen($data) - 1;
				$var=substr($data,0,$v);				
				$temp = explode(',',$var);
				
				$this->DDtrans->DataSource=$temp;
				$this->DDtrans->dataBind();
				
				$this->noTransCtrl->Visible=true;
			}
			else
			{
				$this->errMsg->Text='Belum ada data untuk pasien rujukan!';
			}
			
		}
	}
	
	public function asalPasienChanged($sender,$param)
    {		
		$asalPasien = $this->collectSelectionResult($this->asalPasien);
		if($asalPasien == '0')
		{
			$activeRec = RadJualRecord::finder();
			$nmTbl = 'tbt_rad_penjualan';
			
			$this->setViewState('activeRec',$activeRec);
			$this->setViewState('nmTbl',$nmTbl);
		}	
		elseif($asalPasien == '1')
		{
			$activeRec = RadJualInapRecord::finder();
			$nmTbl = 'tbt_rad_penjualan_inap';
			
			$this->setViewState('activeRec',$activeRec);
			$this->setViewState('nmTbl',$nmTbl);
		}
		
		$this->notrans->Text = '';
		$this->notrans->Enabled = true;
		$this->notrans->focus();
		$this->noTransCtrl->Visible=false;
		$this->showSecond->Visible=false;
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text='';
	}
	
	public function checkRegister($sender,$param)
    {
		$cm = $this->notrans->Text;
		$jnsRujukan = $this->collectSelectionResult($this->modeInput);
		$asalPasien = $this->collectSelectionResult($this->asalPasien);
				
		if($jnsRujukan == '0')
		{
			//$activeRec = RadJualRecord::finder();
			//$nmTbl = 'tbt_rad_penjualan';
			
			$activeRec = $this->getViewState('activeRec');
			$nmTbl = $this->getViewState('nmTbl');
			
			if($asalPasien == '0') //Pasien Rawat Jalan
			{
				$cekPasien = $activeRec->find('cm = ? AND flag = ?', array($this->notrans->Text,'0'));
				
				$sql="SELECT 
						no_trans,
						operator
					FROM 
						$nmTbl
					WHERE 
						cm='$cm' 
						AND flag='0'
					GROUP BY no_trans ";
			}
			elseif($asalPasien == '1') //Pasien Rawat Inap
			{
				//cek pasien di tbt_rad_penjualan_inap yg st_bayar=1 => tipe pembayaran tunai 
				$cekPasien = $activeRec->find('cm = ? AND flag = ? AND st_bayar = ?', array($this->notrans->Text,'0','1'));
				$sql="SELECT 
						no_trans,
						operator
					FROM 
						$nmTbl
					WHERE 
						cm='$cm' 
						AND flag='0'
						AND st_bayar='1'
					GROUP BY no_trans ";
			}
			 // valid if the username is not found in the database
			 
			if($cekPasien)
			{	
				$cm = $this->notrans->Text;
										
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
			else
			{
				$this->showFirst->Visible=true;
				$this->noTransCtrl->Visible=false;
				$this->showSecond->Visible=false;
				$this->errMsg->Text='No. Register tidak ada!!';
				$this->notrans->Focus();
				
				$this->modeInput->Enabled=true;
				$this->citoCheck->Enabled=true;
			}
		}	
		/*
		elseif($jnsRujukan == '1')
		{
			$this->notrans->Enabled = false;
			
			$activeRec = RadJualLainRecord::finder();
			$nmTbl = 'tbt_rad_penjualan_lain';
			
			$cm = $this->notrans->Text;
						
			$sql="SELECT 
					no_trans
				FROM 
					$nmTbl
				WHERE  
					flag='0'
				GROUP BY no_trans ";
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
			
			$this->noTransCtrl->Visible=true;
		}
		*/
       
    }	
	
	public function DDtransChanged()
	{
		if($this->DDtrans->SelectedValue!='')
		{
			$jnsRujukan = $this->collectSelectionResult($this->modeInput);
			if($jnsRujukan == '0') //jika bukan pasien rujukan
			{
				$tmp = $this->ambilTxt($this->DDtrans);		
				$activeRec = $this->getViewState('activeRec');
				
				$asalPasien = $this->collectSelectionResult($this->asalPasien);
				if($asalPasien == '0') //jika pasien rwtjln, cek status alih di tbt_rawat_jalan
				{
					$noTransRwtJln = $activeRec->find('no_trans = ?', $tmp)->no_trans_rwtjln;	
					
					if(RwtjlnRecord::finder()->find('no_trans = ? AND st_alih = ?', array($noTransRwtJln ,'1'))) //jika sudah alih status ke rwt inap
					{
						$this->errMsg->Text='Tagihan masuk rekap rawat inap !';
					}
					else
					{
						$this->checkNoTrans();
						$this->bayar->focus();
					}
				}
				else	//jika pasien rwt inap
				{
					$this->checkNoTrans();
					$this->bayar->focus();
				}
			}
			else//jika pasien rujukan
			{
				$this->checkNoTrans();
				$this->bayar->focus();
				//$this->showSql->text=$sql;
			}
		}
		else
		{
			$this->showSecond->Visible=false;
			$this->errByr->Text='';
			$this->errMsg->Text='';
			$this->bayar->Text='';
			$this->sisaByr->Text='';
		}
	}
	
	public function checkNoTrans()
    {
		$jnsRujukan = $this->collectSelectionResult($this->modeInput);
		$tmp = $this->ambilTxt($this->DDtrans);
		
		if($jnsRujukan == '0')
		{
			$activeRec = $this->getViewState('activeRec');
			$nmTbl = $this->getViewState('nmTbl');
			
			$sql ="SELECT 
						b.nama AS id_tindakan,
						b.cm,
						a.cm,
						a.no_trans,
						a.harga
					 FROM $nmTbl a, 
						  tbd_pasien b 
					 WHERE 
					 	a.no_trans='$tmp' 
						AND a.cm=b.cm
					 ";
			
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				//$jmlHarga += $row['harga'];				
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['harga'];
				}
				else
				{
					$jmlHarga += 2 * $row['harga'];
				}			
			}
					
			$tmpPasien = $activeRec->findBySql($sql);
			
			$this->setViewState('nama',$tmpPasien->id_tindakan);	
			$this->setViewState('notrans',$this->notrans->Text);
			$this->setViewState('cm',$tmpPasien->cm);
			
			$this->nama->Text= $tmpPasien->id_tindakan;							 
		}	
		elseif($jnsRujukan == '1')
		{
			$activeRec = RadJualLainRecord::finder();
			$nmTbl = 'tbt_rad_penjualan_lain';		
			
			$sql ="SELECT 
						cm,
						no_trans,
						harga
					 FROM $nmTbl 
					  WHERE no_trans='$tmp'
					 ";
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				//$jmlHarga += $row['harga'];				
				if($this->citoCheck->Checked==false)
				{
					$jmlHarga += $row['harga'];
				}
				else
				{
					$jmlHarga += 2 * $row['harga'];
				}			
			}
			
			$sql ="SELECT 
						cm,
						no_trans,
						operator
				   FROM $nmTbl
				    WHERE no_trans='$tmp'
				   GROUP BY no_trans ";
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$nmPas = $row['cm'];
				$noTrans = $row['no_trans'];
				$operator = $row['operator'];
			}
			
			$this->setViewState('nama',$nmPas);	
			$this->setViewState('notrans',$noTrans);
			$this->setViewState('petugasLab',$operator);
			
			$this->nama->Text= $nmPas;	
		}		
		
		$this->setViewState('tmpJml',$jmlHarga);
		$this->jmlShow->Text= 'Rp. '.number_format($this->getViewState('tmpJml'),2,',','.');
		$this->setViewState('query',$sql);
		$this->showSql->text=$this->getViewState('query');
		
		$this->showSecond->Visible=true;
		$this->notrans->Enabled=false;
		$this->citoCheck->Enabled=false;
		$this->modeInput->Enabled=false;
		$this->errMsg->Text='';	
    }	
	
	public function bayarClicked($sender,$param)
    {
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text= 'Rp. '.number_format($hitung,2,',','.');
			$this->setViewState('sisa',$hitung);
			
			
			$this->citoCheck->Enabled=false;	
			$this->modeInput->Enabled=false;
			$this->asalPasien->Enabled=false;	
			
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->Focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
			$this->bayarBtn->Enabled=false;	
			$this->bayar->Enabled=false;	
			$this->DDtrans->Enabled=false;
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
	
	public function batalClicked()
    {	
		$this->clearViewState('tmpJml');
		$this->clearViewState('activeRec');
		$this->clearViewState('nmTbl');
		$this->asalPasien->SelectedIndex = -1;
		
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->sisaByr->Text='';
		
		$this->modeInput->Enabled=true;
		$this->modeInput->SelectedValue='0';
		$this->citoCheck->Enabled=true;
		$this->DDtrans->Enabled=true;
		$this->bayarBtn->Enabled=true;	
		$this->bayar->Enabled=true;
			
		$this->citoCheck->Checked=false;
		$this->notrans->Focus();
		$this->noTransCtrl->Visible=false;
		$this->showSecond->Visible=false;
		$this->cetakBtn->Enabled=false;
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->batalClicked();
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {	
		$jnsRujukan = $this->collectSelectionResult($this->modeInput);	
		$asalPasien = $this->collectSelectionResult($this->asalPasien);			
		
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml');
		$notrans=$this->ambilTxt($this->DDtrans);
		$cm=$this->getViewState('notrans');
		$nama=$this->nama->Text;
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		
		if($jnsRujukan == '0')
		{
			if($asalPasien == '0') //Pasien Rawat Jalan
			{
				$sql="UPDATE tbt_rad_penjualan SET flag='1' WHERE no_trans='$notrans' AND cm='$cm' AND flag='0' ";
			}
			elseif($asalPasien == '1') //Pasien Rawat Inap
			{
				$sql="UPDATE tbt_rad_penjualan_inap SET flag='1' WHERE no_trans='$notrans' AND cm='$cm' AND flag='0' ";
			}
		}	
		elseif($jnsRujukan == '1')
		{
			$sql="UPDATE tbt_rad_penjualan_lain SET flag='1' WHERE no_trans='$notrans' AND flag='0' ";
		}
		
		$this->queryAction($sql,'C');
		
		//$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRadJual',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr)));
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakNotaRad',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'jmlTagihan'=>$jmlTagihan,'jnsRujukan'=>$jnsRujukan,'asalPasien'=>$asalPasien,'petugasRad'=>$this->getViewState('petugasLab'))));
		
	}
}
?>
