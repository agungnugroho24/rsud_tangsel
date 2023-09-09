<?php
class beritaCtScan extends SimakConf
{   
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('13');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$sql = "SELECT id,nama FROM tbd_pegawai WHERE kelompok = '8' ORDER BY nama";
			$this->DDDokterRad->DataSource = $this->queryAction($sql,'S');
			$this->DDDokterRad->dataBind();
			
			$this->DDReg->Enabled = false;
			
			$this->showSecond->Display = 'None';
			$this->showTable2->Display = 'None';
			$this->showTable->Display = 'None';
			
			$this->cetakBtn->Enabled=false;
			
			$this->cariCm->Focus();
			
			$jnsPasien = $this->modeInput->SelectedValue;				
			
			if($this->Request['cm'] !='' && $this->Request['noTrans'] != '' && $this->Request['noReg'] != '' && $this->Request['jnsPasien'] != '')
			{
				$this->modeInput->SelectedValue = $this->Request['jnsPasien'];
				$this->cariCm->Text = $this->Request['cm'];
				$this->DDReg->SelectedValue = $this->Request['noReg'];
				
				$this->checkCm($sender,$param);
				$this->checkRegister($sender,$param);
			}
			
			$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=CtScan.CariPasienRad&parentPage=CtScan.beritaRad&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
		}		
		
		
		if ($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql="SELECT * FROM $nmTable ";
			$arr=$this->queryAction($sql,'S');
			
			if(count($arr) > 0)
				$this->cetakBtn->Enabled=false;
			else
				$this->cetakBtn->Enabled=true;
			
			//$this->showSql->Text = count($arr);
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
			
			$this->clearViewState('notrans');
			$this->clearViewState('cm');			
			$this->clearViewState('nama');
			$this->clearViewState('nmTable');
			
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';			
			$this->errMsg->Text='';
			
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('CtScan.beritaCtScan',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function showFirstLock()
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		$this->DDReg->Enabled = false;
	}
	
	public function showFirstCallback($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
		$this->showTable2->render($param->getNewWriter());
	}
	
	public function showSecondCallback($sender,$param)
    {
		$this->showSecond->render($param->getNewWriter());;
	}
	
	public function DDRegCallBack($sender,$param)
    {
		$this->showFirst->render($param->getNewWriter());
	}
	
	
	public function modeInputChanged($sender,$param)
    {
		$this->cariCm->Text = '';
		$this->DDReg->SelectedValue = 'empty';
		
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0' || $jnsPasien == '1') //pasien rawat jalan / inap
		{
			$this->cariCm->Enabled = true;	
			$this->DDReg->Enabled = false;
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->cariCm->getClientID().'.focus();');	
		}
		elseif($jnsPasien == '2' || $jnsPasien == '3') //Jika pasien Bebas / Bebas Karyawan
		{
			$this->cariCm->Enabled = false;	
			$this->DDReg->Enabled = true;
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			
			$sql = "SELECT 
					  tbt_ctscan_penjualan_lain.no_trans,
					  tbt_ctscan_penjualan_lain.no_trans_pas_luar,
					  tbt_ctscan_penjualan_lain.no_reg,
					  tbt_ctscan_penjualan_lain.id_tindakan,
					  tbd_pasien_luar.nama
					FROM
					  tbt_ctscan_penjualan_lain
					  INNER JOIN tbd_pasien_luar ON (tbt_ctscan_penjualan_lain.no_trans_pas_luar = tbd_pasien_luar.no_trans)
					WHERE 
						tbt_ctscan_penjualan_lain.no_reg <> '' AND
						tbt_ctscan_penjualan_lain.st_cetak_hasil = '0' AND 
						tbt_ctscan_penjualan_lain.st_ambil = '0' 
					GROUP BY tbt_ctscan_penjualan_lain.no_reg
					ORDER BY tbt_ctscan_penjualan_lain.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					$nama = $row['nama'];
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nama);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}	
		}
		
		$jnsPasien = $this->modeInput->SelectedValue;
			
		$this->cariPasBtn->Attributes->onclick = 'popup(\'index.php?page=CtScan.CariPasienRad&parentPage=CtScan.beritaRad&notransUpdate='.$this->cariCm->getClientID().'&jnsPasien='.$jnsPasien.'\',\'tes\')';
	}
	
	public function checkCm($sender,$param)
    {
		$cm = $this->formatCm($this->cariCm->Text);
		$jnsPasien = $this->modeInput->SelectedValue;
		
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			$sql = "SELECT 
					  tbt_ctscan_penjualan.no_trans,
					  tbt_ctscan_penjualan.no_trans_rwtjln,
					  tbt_ctscan_penjualan.no_reg,
					  tbt_ctscan_penjualan.id_tindakan,
					  tbm_poliklinik.nama AS nm_poli
					FROM
					  tbt_ctscan_penjualan
					  INNER JOIN tbt_rawat_jalan ON (tbt_ctscan_penjualan.no_trans_rwtjln = tbt_rawat_jalan.no_trans)
					  INNER JOIN tbm_poliklinik ON (tbt_ctscan_penjualan.klinik = tbm_poliklinik.id)
					WHERE 
						tbt_ctscan_penjualan.no_reg <> '' AND
						tbt_ctscan_penjualan.cm = '$cm' AND 
						tbt_ctscan_penjualan.st_cetak_hasil = '0' AND 
						tbt_ctscan_penjualan.st_ambil = '0'  AND
						tbt_rawat_jalan.st_alih = '0' 
					GROUP BY tbt_ctscan_penjualan.no_reg 
					ORDER BY tbt_ctscan_penjualan.no_reg ";
						
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noTransJln = $row['no_trans_rwtjln'];
					$noReg = $row['no_reg'];
					$nmKlinik = $row['nm_poli'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg.' - '.$nmKlinik);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}			
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			$sql = "SELECT 
					  tbt_ctscan_penjualan_inap.no_trans,
					  tbt_ctscan_penjualan_inap.no_trans_inap,
					  tbt_ctscan_penjualan_inap.no_reg,
					  tbt_ctscan_penjualan_inap.id_tindakan
					FROM
					  tbt_ctscan_penjualan_inap
					  INNER JOIN tbt_rawat_inap ON (tbt_ctscan_penjualan_inap.no_trans_inap = tbt_rawat_inap.no_trans)
					WHERE 
						tbt_ctscan_penjualan_inap.no_reg <> '' AND
						tbt_ctscan_penjualan_inap.cm = '$cm' AND 
						tbt_ctscan_penjualan_inap.st_cetak_hasil = '0' AND 
						tbt_ctscan_penjualan_inap.st_ambil = '0' AND
						tbt_rawat_inap.status = '0' 
					GROUP BY tbt_ctscan_penjualan_inap.no_reg
					ORDER BY tbt_ctscan_penjualan_inap.no_reg	 ";
						
			//$this->showSql->Text = $sql;
			
			$arr = $this->queryAction($sql,'S');
			
			if($arr)	
			{
				foreach($arr as $row)
				{
					$noReg = $row['no_reg'];
					
					$data[]=array('no_reg'=>$noReg,'nama'=>$noReg);
				}	
				
				$this->DDReg->DataSource=$data;
				$this->DDReg->dataBind();
				$this->DDReg->Enabled = true;
				
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDReg->getClientID().'.focus();');
			}
			else
			{
				$this->cmNoFound($cm);	
			}		
		}
	}
	
	public function cmNoFound($cm)
    {
		$this->showFirstLock();
		
		$jnsPasien = $this->modeInput->SelectedValue;		
		
		if($jnsPasien == '0') 
			$txt = 'Tidak ada transaksi radiologi Rawat Jalan yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '1')
			$txt = 'Tidak ada transaksi radiologi Rawat Inap yang bisa diproses untuk No. Rekam Medis '.$cm;
		elseif($jnsPasien == '2')
			$txt = 'Tidak ada transaksi radiologi Pasien Luar yang bisa diproses';	
		
		$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">'.$txt.'</p>\',timeout: 4000,dialog:{
					modal: true
				}});
				document.all.'.$this->cariCm->getClientID().'.focus();');	
	}
	
	public function prosesUploadFoto($sender,$param)
	{
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		//$id = $param->CallbackParameter->Id;
		$noTrans = $sender->CommandParameter;
		$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.'\'); unmaskContent();');
		
		$url = "index.php?page=CtScan.uploadFile&noTrans=".$noTrans."&index=".$index."&nmTableTmp=".$nmTableTmp;
		$this->getPage()->getClientScript()->registerEndScript
			('',"tesFrame('$url',650,500,'Upload File')");
			
		/*
		$this->getPage()->getClientScript()->registerEndScript
			('','
				var $dialog = jQuery.FrameDialog.create({
						url: "index.php?page=CtScan.uploadFile&noTrans='.$noTrans.'&index='.$index.'&nmTableTmp='.$nmTableTmp.'",
						loadingClass: "loading-image",
						title: "Upload File",
						width: 650,
						height: 500,
						autoOpen: false
					});

				$dialog.dialog("open");
				jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
				unmaskContent();
			');
		*/	
		/*
		$this->getPage()->getClientScript()->registerEndScript
			('','
				var $dialog =
					jQuery.FrameDialog.create({
						url: "'.$this->Service->constructUrl('CtScan.uploadFile',array('noTrans'=>$noTrans,'index'=>$index)).'",
						loadingClass: "loading-image",
						title: "Upload Foto",
						width: 600,
						height: 400,
						autoOpen: false
					});

				$dialog.dialog("open");
				jQuery( \'a.ui-dialog-titlebar-close\' ).remove();
				unmaskContent();
			');
		*/	
	}
	
	public function prosesModal($sender,$param)
	{
		$index = $param->CallbackParameter->Id;
		$nmTableTmp = $param->CallbackParameter->nmTableTmp;
		
		//$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		if($nmTableTmp)
		{
			$sql="SELECT * FROM $nmTableTmp ";
			$arr = $this->queryAction($sql,'S');
			
			$this->Repeater->Items[$index]->Repeater2->DataSource = $arr;
			$this->Repeater->Items[$index]->Repeater2->dataBind();			
			
			$this->Repeater->Items[$index]->nmTableTmp->Text = $nmTableTmp;
			$this->Repeater->Items[$index]->jmlFile->setText(count($arr).' File');
			//$this->Repeater->Items[$index]->jmlFile->setText($index.' '.count($arr).' File '.$sql);
			
			//$this->Repeater->Items[$index]->Repeater2->render($param->getNewWriter());
			$this->showTable2->render($param->getNewWriter());
		}
		
		//$this->Repeater->Items[$index]->Repeater2->render($param->getNewWriter());
		//$this->checkRegister();
		
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');	
	}
	
	public function repeaterDataBound($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$idTdk = $item->DataItem['id_tindakan'];
			
			$item->nmTindakan->Text = strtoupper(CtScanTdkRecord::finder()->findByPk($idTdk)->nama);
			$item->HtmlArea->Text = $item->DataItem['catatan'];
			
			
			//$nmField = $item->DataItem['nm_field'];
			//$item->nmSyarat->Text = $nmSyarat;
			//$sql="SELECT * FROM tbd_pegawai WHERE kelompok = '1' ";
			//$arr = $this->queryAction($sql,'S');
			
			//$item->Repeater2->DataSource = $arr;
			//$item->Repeater2->dataBind();
			
			$nmTableTmp = $item->nmTableTmp->Text ;
			
			if($nmTableTmp)
			{
				$sql="SELECT * FROM $nmTableTmp  ";
				$arr = $this->queryAction($sql,'S');
				
				$item->Repeater2->DataSource = $arr;
				$item->Repeater2->dataBind();		
				
				$item->nmTableTmp->Text = $nmTableTmp;
				$item->jmlFile->setText(count($arr).' File');	
			}	
			else
			{
				$item->jmlFile->setText('0 File');	
			}	
        }
    }
	
	public function repeaterDataBound2($sender,$param)
    {
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			$item->idTableTmp->Value = $item->DataItem['id'];
			$item->nmFileTmp->Text = $item->DataItem['nama'];
        }
    }
	
	public function delFileUploadClicked($sender,$param)
    {
		$item = $sender->Parent->Parent->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
		
		$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
		
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$item2 = $sender->getNamingContainer(); 
					$index2 = $this->Repeater->Items[$index]->Repeater2->DataKeys[$item2->getItemIndex()];
					$id = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				case "AlternatingItem":					
					$item2 = $sender->getNamingContainer(); 
					$index2 = $this->Repeater->Items[$index]->Repeater2->DataKeys[$item2->getItemIndex()];
					$id = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				default:
					break;
			}
		}
		
		$sql = "DELETE FROM $nmTableTmp WHERE id = $id ";
		$this->queryAction($sql,'C');
		
		//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->Text;
		$urlTmp ='protected/pages/CtScan/foto/tmp//'.$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->Text;
		if(file_exists($urlTmp))
		{
			unlink($urlTmp);
		}
		
		$sql="SELECT * FROM $nmTableTmp  ";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) > 0)
		{
			$this->Repeater->Items[$index]->Repeater2->DataSource = $arr;
			$this->Repeater->Items[$index]->Repeater2->dataBind();	
			
			$this->Repeater->Items[$index]->jmlFile->setText(count($arr).' File');	
		}
		else
		{
			$this->queryAction($nmTableTmp,'D');
			$this->Repeater->Items[$index]->nmTableTmp->Text = '';
								
			$this->Repeater->Items[$index]->Repeater2->DataSource = '';
			$this->Repeater->Items[$index]->Repeater2->dataBind();	
			
			$this->Repeater->Items[$index]->jmlFile->setText('0 File');	
		}
			
		//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
		//$value = $this->Repeater->Items[$index]->Repeater2->Items[$index2]->idTableTmp->Value;
		//$this->Repeater->Items[$index]->Repeater2->Items[$index2]->nmFileTmp->setText($value);
	}
	
	public function Repeater2Callback($sender,$param)
    {
	
	}
	
	public function uploadBtnClicked($sender,$param)
    {
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		$jml = $this->Repeater->Items[$index]->jmlFoto->Text;
		
		if(intval($jml) > 0)
		{
			$noTrans = $sender->CommandParameter;
			$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$jml.'\');');
		}
		else
		{
			$this->Repeater->Items[$index]->jmlFoto->setText('');
			$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah Foto Yang Akan Diunggah tidak sesuai !\'); document.all.'.$this->Repeater->Items[$index]->jmlFoto->getClientID().'.focus();');
			//$this->Repeater->Items[$index]->jmlFoto->Focus();
		}
	}
	
	public function uploadDocBtnClicked($sender,$param)
    {
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		$jml = $this->Repeater->Items[$index]->jmlDok->Text;
		
		if(intval($jml) > 0)
		{
			$noTrans = $sender->CommandParameter;
			$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$jml.'\');');
		}
		else
		{
			$this->Repeater->Items[$index]->jmlDok->setText('');
			$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah Dokumen Yang Akan Diunggah tidak sesuai !\'); document.all.'.$this->Repeater->Items[$index]->jmlDok->getClientID().'.focus();');
			//$this->Repeater->Items[$index]->jmlFoto->Focus();
		}
	}
	
	
	public function checkRegister()
    {
		$tmp = $this->DDReg->SelectedValue;
		$jnsPasien = $this->modeInput->SelectedValue;
				
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			if(CtScanJualRecord::finder()->find('no_reg = ?', $tmp))
			{								
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_ctscan_penjualan a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg = '$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = CtScanJualRecord::finder()->findBySql($sql);
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			if(CtScanJualInapRecord::finder()->find('no_reg = ?', $tmp))
			{						
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_ctscan_penjualan_inap a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg='$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = CtScanJualInapRecord::finder()->findBySql($sql);
			}	
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			if(CtScanJualLainRecord::finder()->find('no_reg = ?', $tmp))
			{					
				$sql ="SELECT 
						  tbt_ctscan_penjualan_lain.nama AS id,
						  tbt_ctscan_penjualan_lain.no_trans,
						  tbt_ctscan_penjualan_lain.id_tindakan
						FROM
						  tbt_ctscan_penjualan_lain 
						WHERE tbt_ctscan_penjualan_lain.no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
						
				$tmpPasien = CtScanJualLainRecord::finder()->findBySql($sql);
			}
		}
		
		if($tmpPasien)
		{	
			$this->setViewState('notrans',$this->notrans->Text);						
			$this->setViewState('nama',$tmpPasien->id);				
			
			if ($jnsPasien=='0' || $jnsPasien=='1')
			{
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->dokter);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->dokter->Text= $tmpPasien->dokter;
			}
			else
			{
				$this->cm->Text= '-';
				$this->dokter->Text= '-';
			}
						
			
			$this->nama->Text = $tmpPasien->id;			
									
			$this->showFirst->Enabled = false;
			$this->showSecond->Display = 'Dynamic';
			$this->showTable2->Display = 'Dynamic';
			$this->showTable->Display = 'Dynamic';			
			
			$this->errMsg->Text='';		
			
			$noTrans=$this->getViewState('notrans');
			$cm = $this->formatCm($this->cariCm->Text);
			
			if ($jnsPasien=='0')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan WHERE no_reg = '$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			elseif ($jnsPasien=='1')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan_inap WHERE no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			elseif ($jnsPasien=='2')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan_lain WHERE no_reg='$tmp' AND (id_tindakan <> 'PDT' AND id_tindakan <> 'RUJ' )";
			}
			
			$arr = $this->queryAction($sql,'R');
			
			$this->Repeater->DataSource=$arr;
			$this->Repeater->dataBind();
			
			$this->cetakBtn->Enabled=true;
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->showTable->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->cetakBtn->Enabled=false;
			$this->notrans->Focus();
		}
    }	
		
	public function checkRegister2($sender,$param)
    {
		$tmp = $this->DDReg->SelectedValue;
		$jnsPasien = $this->modeInput->SelectedValue;
				
		if($jnsPasien == '0') //pasien rawat jalan
		{	
			if(CtScanJualRecord::finder()->find('no_reg = ?', $tmp))
			{								
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_ctscan_penjualan a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg = '$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = CtScanJualRecord::finder()->findBySql($sql);
			}
		}
		elseif($jnsPasien == '1') //pasien rawat inap
		{
			if(CtScanJualInapRecord::finder()->find('no_reg = ?', $tmp))
			{						
				$sql ="SELECT 
							b.nama AS id,
							b.cm,
							a.cm,
							a.no_trans,						
							a.id_tindakan,
							c.nama AS dokter
						 FROM tbt_ctscan_penjualan_inap a, 
							  tbd_pasien b,
							  tbd_pegawai c 
						 WHERE a.no_reg='$tmp' 
								AND a.cm=b.cm
								AND a.dokter=c.id";
						
				$tmpPasien = CtScanJualInapRecord::finder()->findBySql($sql);
			}	
		}
		elseif($jnsPasien == '2') //pasien luar
		{
			if(CtScanJualLainRecord::finder()->find('no_reg = ?', $tmp))
			{					
				$sql ="SELECT 
						  tbt_ctscan_penjualan_lain.nama AS id,
						  tbt_ctscan_penjualan_lain.no_trans,
						  tbt_ctscan_penjualan_lain.id_tindakan
						FROM
						  tbt_ctscan_penjualan_lain 
						WHERE tbt_ctscan_penjualan_lain.no_reg='$tmp' ";
						
				$tmpPasien = CtScanJualLainRecord::finder()->findBySql($sql);
			}
		}
		
		if($tmpPasien)
		{	
			$this->setViewState('notrans',$this->notrans->Text);						
			$this->setViewState('nama',$tmpPasien->id);				
			
			if ($jnsPasien=='0' || $jnsPasien=='1')
			{
				$this->setViewState('cm',$tmpPasien->cm);
				$this->setViewState('dokter',$tmpPasien->dokter);
				
				$this->cm->Text= $tmpPasien->cm;
				$this->dokter->Text= $tmpPasien->dokter;
			}
			else
			{
				$this->cm->Text= '-';
				$this->dokter->Text= '-';
			}
						
			
			$this->nama->Text = $tmpPasien->id;			
									
			$this->showFirst->Enabled = false;
			$this->showSecond->Display = 'Dynamic';
			$this->showTable->Display = 'Dynamic';			
			
			$this->errMsg->Text='';					
			
			if (!$this->getViewState('nmTable'))
			{
				$nmTable = $this->setNameTable('nmTable');
				$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
										 nama VARCHAR(30) NOT NULL,
										 id_tdk VARCHAR(4) NOT NULL,									 
										 nilai TEXT DEFAULT '',
										 PRIMARY KEY (id)) ENGINE = MEMORY";
				
				$this->queryAction($sql,'C');
			}
			else
			{
				$nmTable = $this->getViewState('nmTable');						
			}			
			
			$noTrans=$this->getViewState('notrans');
			$cm = $this->formatCm($this->cariCm->Text);
			
			if ($jnsPasien=='0')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan WHERE no_reg = '$tmp' ";
			}
			elseif ($jnsPasien=='1')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan_inap WHERE no_reg='$tmp'";
			}
			elseif ($jnsPasien=='2')
			{
				$sql="SELECT * FROM tbt_ctscan_penjualan_lain WHERE no_reg='$tmp' ";
			}
			//CtScanJualRecord::finder()->findBySql($sql);
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$idTdk = $row['id_tindakan'];
				
				$sql="SELECT nama FROM tbm_ctscan_tindakan WHERE kode='$idTdk' ";
				
				$tmpTdk = CtScanTdkRecord::finder()->findBySql($sql);					 				
				$nama = $tmpTdk->nama;
				$nilai= '';				
				
				$sql="INSERT INTO $nmTable (nama,id_tdk,nilai) VALUES ('$nama','$idTdk','$nilai')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql="DELETE FROM $nmTable WHERE id_tdk='RUJ'";
			$arr=$this->queryAction($sql,'C');
			$sql="DELETE FROM $nmTable WHERE id_tdk='PDT'";
			$arr=$this->queryAction($sql,'C');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;				
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();	
		}
		else
		{
			$this->showFirst->Enabled = true;
			$this->showSecond->Display = 'None';
			$this->showTable2->Display = 'None';
			$this->showTable->Display = 'None';	
			
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
		}
    }	
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		}
		else
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}
			
		
		$page=$this->queryAction($sql,'S');		 
		
		return $page;
		
	}
	
	/*
	protected function getDataRows($offset,$rows)
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
        $data=$this->queryAction($sql,'R');
        $page=array();	
		
        for($i=0;$i<$rows;++$i)
        {
            if($offset+$i<$this->getRowCount())
                $page[$i]=$data[$offset+$i];
        }
		
		
		
        return $page;
    }
	
	
	protected function getRowCount()
    {
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$jmlRows=0;
		foreach($arr as $row)
		{
			$jmlRows=$jmlRows+1;
		}
        return $jmlRows;
    }
	*/
	
	public function changePage($sender,$param)
	{				
		$limit=$this->UserGrid->PageSize;		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		$nmTable = $this->getViewState('nmTable');		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
	} 
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}
	
	public function changePagerPosition($sender,$param)
	{		
		$position='TopAndBottom';		
		$this->UserGrid->PagerStyle->Position=$position;
		$this->UserGrid->PagerStyle->Visible=true;		
	}	
	
	public function itemCreated($sender,$param)
    {
		
		$item=$param->Item;
				
		//$this->DataGrid->DataKeys[$param->Item->ItemIndex]
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->nilai->TextBox->Columns=15;
		   $item->normal->TextBox->Columns=15;
		   $item->normal2->TextBox->Columns=15;
        }       
    }
	
	public function useNumericPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='Numeric';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		$this->UserGrid->PagerStyle->PageButtonCount=$this->PageButtonCount->Text;
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
	
	public function useNextPrevPager($sender,$param)
	{
		
		$this->UserGrid->PagerStyle->Mode='NextPrev';
		$this->UserGrid->PagerStyle->NextPageText=$this->NextPageText->Text;
		$this->UserGrid->PagerStyle->PrevPageText=$this->PrevPageText->Text;
		
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}
	
	public function changePageSize($sender,$param)
	{
		
		$this->UserGrid->PageSize=TPropertyValue::ensureInteger($this->PageSize->Text);
		$this->UserGrid->CurrentPageIndex=0;
		
		$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
	}	
	
	
	public function sortGrid($sender,$param)
	{		
		$item = $param->SortExpression;
		
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
	}
	
	public function toggleColumnVisibility($sender,$param)
	{			
		foreach($this->UserGrid->Columns as $index=>$column)
		$column->Visible=$sender->Items[$index]->Selected;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
		$this->UserGrid->dataBind();
	}	
	
	public function editItem($sender,$param)
    {
        
		if ($this->User->IsAdmin)
		{
		
			$limit=$this->UserGrid->PageSize;		
			$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;			
			
			$this->UserGrid->EditItemIndex=$param->Item->ItemIndex;
			
			$nmTable = $this->getViewState('nmTable');
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$i=0;
			foreach($arr as $row)
			{
				$i=$i+1;
			}
			$this->UserGrid->VirtualItemCount=$i;
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();	
		}	
    }
	
	public function cancelItem($sender,$param)
    {
       	$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$this->UserGrid->EditItemIndex=-1;
		
       	$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
    }
	
	public function saveItem($sender,$param)
    {
		$limit=$this->UserGrid->PageSize;		
		$offset=$this->UserGrid->CurrentPageIndex*$this->UserGrid->PageSize;	
		
		$nmTable = $this->getViewState('nmTable');
		
        $item = $param->Item;		
		$id = $this->UserGrid->DataKeys[$item->ItemIndex];
		$nilai = ucwords(trim($item->nilai->TextBox->Text));
		$normal = $item->normal->TextBox->Text;
		$normal2 = $item->normal2->TextBox->Text;
		
		$sql="UPDATE $nmTable SET nilai='$nilai',normal='$normal',normal_perempuan='$normal2' WHERE id='$id'";
		$this->queryAction($sql,'C');
      
        $this->UserGrid->EditItemIndex=-1;
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
    }
	
	public function batalClicked($sender,$param)
    {		
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
					
					if($nmTableTmp != '')
					{
						$sql = "SELECT * FROM $nmTableTmp ";
						$arr = $this->queryAction($sql,'S');
						
						foreach($arr as $row)
						{
							//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
							$urlTmp ='protected/pages/CtScan/foto/tmp//'.$row['nama'];
								
							if(file_exists($urlTmp))
							{
								unlink($urlTmp);
							}
						}
						
						$this->queryAction($nmTableTmp,'D');
					}
					
					//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
					break;
				case "AlternatingItem":					
					$index = $this->Repeater->DataKeys[$item->getItemIndex()];
					$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
					
					if($nmTableTmp != '')
					{
						$sql = "SELECT * FROM $nmTableTmp ";
						$arr = $this->queryAction($sql,'S');
						
						foreach($arr as $row)
						{
							//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
							$urlTmp ='protected/pages/CtScan/foto/tmp//'.$row['nama'];
								
							if(file_exists($urlTmp))
							{
								unlink($urlTmp);
							}
						}
						
						$this->queryAction($nmTableTmp,'D');
					}
					
					break;
				default:
					break;
			}
		}
		
		$this->Response->Reload();				
		$this->Response->redirect($this->Service->constructUrl('CtScan.beritaCtScan'));		
	}
	
	public function keluarClicked($sender,$param)
	{		
		
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	
	public function cetakClicked($sender,$param)
    {	
		if($this->DDDokterRad->SelectedValue != '')
		{
			$jnsPasien = $this->modeInput->SelectedValue;
			$noReg = $this->DDReg->SelectedValue;	
			$dokterRad = $this->DDDokterRad->SelectedValue;	
			
			$cm = $this->formatCm($this->cariCm->Text);
			$nama = $this->nama->Text;
			$dokter = $this->dokter->Text;
			
			$operator = $this->User->IsUserNip;
			$nipTmp = $this->User->IsUserNip;
			$tgl = date('Y-m-d');
			$wkt = date('G:i:s');
			
			foreach($this->Repeater->getItems() as $item) {
				switch($item->getItemType()) {
					case "Item":					
						$index = $this->Repeater->DataKeys[$item->getItemIndex()];
						$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
						$noTrans = $this->Repeater->Items[$index]->noTrans->Text;
						$catatan = $this->Repeater->Items[$index]->HtmlArea->Text;
						
						//UPDATE tbt_ctscan_hasil	
						if ($jnsPasien=='0')
						{
							$sql="UPDATE tbt_ctscan_penjualan SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='1')
						{
							$sql="UPDATE tbt_ctscan_penjualan_inap SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='2')
						{
							$cm = '';
							$sql="UPDATE tbt_ctscan_penjualan_lain SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
											
						$this->queryAction($sql,'C');
						
						//jika ada file yang di upload
						if($nmTableTmp != '')
						{
							$sql = "SELECT * FROM $nmTableTmp ";
							$arr = $this->queryAction($sql,'S');
							
							foreach($arr as $row)
							{
								$tipeFile = $row['tipe_file'];
								
								$data = new CtScanFotoHasilRecord();
								$data->no_trans = $noTrans;
								$data->tgl = $tgl;
								$data->wkt = $wkt;
								$data->operator = $operator;
								$data->nama_file = $row['nama'];
								$data->tipe_file = $row['tipe'];
								$data->tipe_pasien = $jnsPasien;
								$data->Save();			
								
								//$url ='protected\pages\Rad\foto\\'.$row['nama'];
								//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
								$url ='protected/pages/CtScan/foto//'.$row['nama'];
								$urlTmp ='protected/pages/CtScan/foto/tmp//'.$row['nama'];
									
								if(file_exists($urlTmp))
								{
									copy($urlTmp,$url);
									unlink($urlTmp);
								}
							}
							
							$this->queryAction($nmTableTmp,'D');
						}
						
						//$this->getPage()->getClientScript()->registerEndScript('','alert(\''.$index.' - '.$index2.'\');');
						break;
					case "AlternatingItem":					
						$index = $this->Repeater->DataKeys[$item->getItemIndex()];
						$nmTableTmp = $this->Repeater->Items[$index]->nmTableTmp->Text;
						$noTrans = $this->Repeater->Items[$index]->noTrans->Text;
						$catatan = $this->Repeater->Items[$index]->HtmlArea->Text;
						
						//UPDATE tbt_ctscan_hasil	
						if ($jnsPasien=='0')
						{
							$sql="UPDATE tbt_ctscan_penjualan SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='1')
						{
							$sql="UPDATE tbt_ctscan_penjualan_inap SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
						elseif ($jnsPasien=='2')
						{
							$cm = '';
							$sql="UPDATE tbt_ctscan_penjualan_lain SET catatan = '$catatan', dokter_ctscan = '$dokterRad', st_cetak_hasil = '1' WHERE no_trans = '$noTrans' ";
						}
											
						$this->queryAction($sql,'C');
						
						//jika ada file yang di upload
						if($nmTableTmp != '')
						{
							$sql = "SELECT * FROM $nmTableTmp ";
							$arr = $this->queryAction($sql,'S');
							
							foreach($arr as $row)
							{
								$tipeFile = $row['tipe_file'];
								
								$data = new CtScanFotoHasilRecord();
								$data->no_trans = $noTrans;
								$data->tgl = $tgl;
								$data->wkt = $wkt;
								$data->operator = $operator;
								$data->nama_file = $row['nama'];
								$data->tipe_file = $row['tipe'];
								$data->tipe_pasien = $jnsPasien;
								$data->Save();			
								
								//$url ='protected\pages\Rad\foto\\'.$row['nama'];
								//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$row['nama'];
								$url ='protected/pages/CtScan/foto//'.$row['nama'];
								$urlTmp ='protected/pages/CtScan/foto/tmp//'.$row['nama'];
									
								if(file_exists($urlTmp))
								{
									copy($urlTmp,$url);
									unlink($urlTmp);
								}
							}
							
							$this->queryAction($nmTableTmp,'D');
						}
						
						break;
					default:
						break;
				}
			}
			
			if ($jnsPasien=='2')
			{
				$sql="UPDATE tbt_ctscan_penjualan_lain SET st_cetak_hasil = '1' WHERE no_reg = '$noReg' AND id_tindakan='PDT'";
				$this->queryAction($sql,'C');
			}
				
			$this->Response->redirect($this->Service->constructUrl('CtScan.cetakLapHasilCtScan',array('jnsPasien'=>$jnsPasien,'noReg'=>$noReg,'notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'dokterRad'=>$this->ambilTxt($this->DDDokterRad),'table'=>$table)));
			
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Dokter Radioterapi belum di pilih !<br/><br/></p>\',timeout: 4000,dialog:{
					modal: true
				}});
				document.all.'.$this->DDDokterRad->getClientID().'.focus();');
		}		
	}
}
?>
