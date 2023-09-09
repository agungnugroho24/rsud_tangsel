<?php
class ObatMasukEdit extends SimakConf
{ 
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack){
			$this->noPO->focus();				
			$this->noFktrPanel->Display = 'None';
			$this->tglFktrPanel->Display = 'None';			
			$this->tglJthTmpPanel->Display = 'None';			
			$this->tglTrmBrgPanel->Display = 'None';
			$this->materaiPanel->Display = 'None';
			$this->prosesCtrl->Display = 'None';
			$this->checkPoTrueCtrl->Display = 'None';
			$this->checkPoFalseCtrl->Display = 'None';
			$this->cetakBtn->Enabled=false;	
			$this->prevBtn->Enabled=false;	
			//$this->potongan->text=0;					
		}	
		
		if ($this->getViewState('nmTable'))
		{
			//$this->test->Text = $this->getViewState('nmTable');
		}	
	}
	
	public function onLoad($param)
	{	
		$noPO=$this->Request['noPO'];
	}

	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
			
		$noPO=$this->Request['noPO'];
		if(!$this->IsPostBack)  // if the page is initially requested
		{
			$this->noPO->text=$noPO;
			$sql="SELECT * FROM tbt_obat_masuk_embel WHERE no_po='$noPO' ";
			$arr = $this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$this->jmlFktr->text=$row['jml_fktr'];
				$this->noFktr->text=$row['no_fktr'];
				$this->tglFaktur->Text=$this->convertDate($row['tgl_fktr'],'1');
				$this->tglJthTempo->Text=$this->convertDate($row['tgl_jth_tempo'],'1');
				$this->tglTerima->Text=$this->convertDate($row['tgl_trima_brg'],'1');
				$this->mtr->SelectedValue=$row['materai'];
				$this->ppn->SelectedValue=$row['ppn'];
				$this->potongan->text=$row['pot'];
			}
		//$this->prosesClicked();
		}
	 }
	
	public function prosesLock()
	{	
		$this->noPO->Enabled=false;
		//$this->noFak->Enabled=false;
		$this->tglFaktur->Enabled=false;
		$this->tglJthTempo->Enabled=false;		
		$this->tglTerima->Enabled=false;		
		$this->prosesBtn->Enabled=false;
		$this->prosesCtrl->Display = 'Dynamic';
	} 
	
	public function prosesUnlock()
	{	
		$this->noPO->Enabled=true;
		//$this->noFak->Enabled=true;
		$this->tglFaktur->Enabled=true;
		$this->tglJthTempo->Enabled=true;		
		$this->tglTerima->Enabled=true;
		$this->prosesBtn->Enabled=true;
		$this->prosesCtrl->Display = 'None';
	} 
	
	public function cb($sender,$param)
	{
		$cek=$this->mat->Checked;		
		if($cek==true)
		{
			$this->mat->text='Ya';
			$cek=1;
		}else{
			$this->mat->text='Tidak';
			$cek=0;
		}
		$this->setViewState('cek',$cek);
	}
	
	public function mtrChanged($sender,$param)
	{
		$cek=$this->collectSelectionResult($this->mtr);
		//$cek=$this->mat->Checked;						
		$this->setViewState('cek',$cek);
	}
	
	public function ppnChanged($sender,$param)
	{
		$ppn=$this->collectSelectionResult($this->ppn);
		//$cek=$this->mat->Checked;						
		$this->setViewState('ppn',$ppn);
	}
	
	public function prosesClicked()
	{	
		if($this->IsValid)  // when all validations succeed
        { 
			$this->prosesLock();
			if($this->checkNoPO($this->noPO->Text)==true)
			{
				$this->checkPoTrueCtrl->Display = 'Dynamic';
				$this->checkPoFalseCtrl->Display = 'None';
				
				$this->makeTblTemp();
			}
			else
			{
				$this->checkPoTrueCtrl->Display = 'None';
				$this->checkPoFalseCtrl->Display = 'Dynamic';
				
				$this->noPO->Enabled=true;
				$this->noPO->focus();
				$this->prosesBtn->Enabled=true;
			}
		}
	} 
	
	public function checkPo($sender, $param)
	{
		if($this->checkNoPO($this->noPO->Text)==true)
		{
			$this->checkPoTrueCtrl->Display = 'Dynamic';
			$this->checkPoFalseCtrl->Display = 'None';
		}
		else
		{
			$this->checkPoTrueCtrl->Display = 'None';
			$this->checkPoFalseCtrl->Display = 'Dynamic';
		}
	}
	
	public function checkNoPO($noPO)
	{
		$sql = "SELECT no_po FROM tbt_obat_beli WHERE no_po='$noPO' AND flag='1'";
		//$sql = "SELECT no_po FROM tbt_obat_masuk WHERE no_po='$noPO' ";
		$arr = $this->queryAction($sql,'S');
		$jmlData = count($arr);
		if($jmlData>0)
		{
			$hasil = true;
		}
		else
		{
			$hasil = false;
		}
		
		return $hasil;
	}
	
	public function ambilDataObatBeli($noPO)
	{
		$sql="SELECT 
				  tbt_obat_masuk.no_faktur AS nofak,
				  tbt_obat_masuk.id_obat AS kode,
				  tbt_obat_beli.jumlah AS jml,
				  tbt_obat_masuk.jumlah AS jml_terima,
				  tbt_obat_masuk.no_batch AS batch,
				  tbt_obat_masuk.tgl_exp AS exp,
				  tbt_obat_masuk.discount AS disc
				FROM
				  tbt_obat_beli
				  INNER JOIN tbt_obat_masuk ON (tbt_obat_beli.no_po = tbt_obat_masuk.no_po)
				WHERE 
					tbt_obat_masuk.no_po='$noPO'
				GROUP BY  
					tbt_obat_masuk.id ";
		
		$arr = $this->queryAction($sql,'R');
		//$this->showSql->text=$sql;
		return $arr;
	}
	
	public function makeTblTemp()
	{		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
					id INT (2) auto_increment,
					nofak varchar(30) DEFAULT '0', 
					kode varchar(5) NOT NULL,
					jml int(11) NOT NULL,
					jml_terima int(11) DEFAULT '0',
					hrg float DEFAULT '0',
					batch varchar(25) DEFAULT NULL,
					expired date DEFAULT '0000-00-00',
					disc float(4,1) DEFAULT '0',
					PRIMARY KEY (id)
				) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			$nofak = $this->noFktr->Text;
			//insert kode,pbf,jml dari tbt_obat_beli dengan No.PO yg ditentukan ke tblTemp
			
			foreach($this->ambilDataObatBeli($this->noPO->Text) as $row)
			{
				//$kode = $row['kode'];
				//$jml = $row['jumlah'];
				$nofak=$row['nofak'];
				$kode=$row['kode'];
				$jml=$row['jml'];
				$jml_terima=$row['jml_terima'];
				$batch=$row['batch'];
				$exp=$row['exp'];
				$disc=$row['disc'];
				
				$sql="INSERT INTO $nmTable (nofak,kode,jml,jml_terima,batch,expired,disc) VALUES ('$nofak','$kode','$jml','$jml_terima','$batch','$exp','$disc')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql = "SELECT 
						kode
					FROM 
						$nmTable ";					
			$arr = $this->queryAction($sql,'R');
			
			//insert hrg_ppn dari tbt_obat_hrg untuk tiap barang di tblTemp
			foreach($arr as $row)
			{
				$kode = $row['kode'];
				
				//$sql="SELECT hrg_ppn FROM tbt_obat_harga WHERE kode='$kode' ";
				
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$kode'";
				$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
			
				$sql="SELECT hrg_netto FROM tbt_obat_harga WHERE kode='$kode' AND id = '$idMax'  ";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$hrg_netto=$row['hrg_netto'];		
				
					$sql="UPDATE $nmTable SET hrg='$hrg_netto' WHERE kode='$kode' ";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
			}
			
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
			$nmTable = $this->getViewState('nmTable');
			
			//insert kode,pbf,jml dari tbt_obat_beli dengan No.PO yg ditentukan ke tblTemp
			foreach($this->ambilDataObatBeli($this->noPO->Text) as $row)
			{
				$kode = $row['kode'];
				$jml = $row['jumlah'];
				
				$sql="INSERT INTO $nmTable (kode,jml) VALUES ('$kode','$jml')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql = "SELECT 
						kode
					FROM 
						$nmTable ";					
			$arr = $this->queryAction($sql,'R');
			
			//insert hrg_ppn dari tbt_obat_hrg untuk tiap barang di tblTemp
			foreach($arr as $row)
			{
				$kode = $row['kode'];
				
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$kode'";
				$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
			
				$sql="SELECT hrg_netto FROM tbt_obat_harga WHERE kode='$kode' AND id = '$idMax'  ";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$hrg_netto=$row['hrg_netto'];		
				
					$sql="UPDATE $nmTable SET hrg='$hrg_netto' WHERE kode='$kode' ";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
			}
			
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
	
	public function dtgSomeData_PageIndexChanged($sender,$param)
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

	
	public function itemCreated($sender,$param)
    {
		
		$item=$param->Item;
				
		//$this->DataGrid->DataKeys[$param->Item->ItemIndex]
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
		   $item->nofak->noFakTxt->Columns=12;
           $item->jmlTerima->TextBox->Columns=5;
		   $item->hrg->TextBox->Columns=12;
		   $item->batch->TextBox->Columns=12;
		   $item->expired->TextBox->Columns=12;
		   $item->disc->TextBox->Columns=12;
        }       
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
		
        $item=$param->Item;		
		$id=$this->UserGrid->DataKeys[$item->ItemIndex];		
				
		$sql="SELECT jml FROM $nmTable WHERE id='$id' ";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			$jml=$row['jml'];
		}		
		
		$nofak = $item->nofak->noFakTxt->Text;
		$jml_terima=abs($item->jmlTerima->TextBox->Text);
		$hrg=$item->hrg->TextBox->Text;
		$batch=$item->batch->TextBox->Text;
		//$expired=$this->convertDate($item->expired->TextBox->Text,'2');
		$expired=$item->expired->TextBox->Text;
		$disc=$item->disc->TextBox->Text;
		
		if($jml_terima<=$jml)
		{
			$sql="UPDATE $nmTable SET
					nofak='$nofak', 
					jml_terima='$jml_terima', 
					hrg='$hrg', 
					batch='$batch', 
					expired='$expired',
					disc='$disc' 
					WHERE id='$id'";
			$this->queryAction($sql,'C');
		}		
		
      
        $this->UserGrid->EditItemIndex=-1;
		
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;			
			$totJmlTerima += $row['jml_terima'];
			$tot +=$row['jml_terima']*$row['hrg'];
		}
		$this->tot->text=number_format($tot,2,',','.');//$tot;
		
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
		
		//periksa tabel temp, jika ada data maka tombol cetak aktif
		 if($totJmlTerima == 0)
		 {
		 	$this->cetakBtn->Enabled=false;
			$this->prevBtn->Enabled=false;	
		 }
		 else
		 {
		 	$this->cetakBtn->Enabled=true;
			$this->prevBtn->Enabled=true;
		 }
    }
	
	public function cetakClicked($sender,$param)
    {
		$nmTable = $this->getViewState('nmTable');
		$cek = $this->getViewState('cek');
		//$ppn = $this->getViewState('ppn');
		$ppn=$this->collectSelectionResult($this->ppn);
		$operator=$this->User->IsUserNip;
		$noPO = $this->noPO->Text;
		$tglFaktur = $this->convertDate($this->tglFaktur->Text,'2');
		$tglJthTempo = $this->convertDate($this->tglJthTempo->Text,'2');	
		$tglTerima = $this->convertDate($this->tglTerima->Text,'2');
		$dateNow = date('Y-m-d');
		$pot=$this->potongan->text;
		
		//$newObatMasukEmbel= new ObatMasukEmbelRecord();
		$newObatMasukEmbel = ObatMasukEmbelRecord::finder()->find('no_po = ?',$noPO);
		$newObatMasukEmbel->jml_fktr=$this->jmlFktr->text;
		$newObatMasukEmbel->no_fktr=$this->noFktr->text;
		$newObatMasukEmbel->tgl_fktr=$this->convertDate($this->tglFaktur->Text,'2');
		$newObatMasukEmbel->tgl_jth_tempo=$this->convertDate($this->tglJthTempo->Text,'2');
		$newObatMasukEmbel->tgl_trima_brg=$this->convertDate($this->tglTerima->Text,'2');
		$newObatMasukEmbel->materai=$this->mtr->SelectedValue;
		$newObatMasukEmbel->ppn=$this->ppn->SelectedValue;
		$newObatMasukEmbel->pot=$this->potongan->text;
		
		$newObatMasukEmbel->Save();
		
		//$newObatMasuk = ObatMasukRecord::finder()->find('no_po = ?',$noPO);
		$sql="SELECT * FROM $nmTable WHERE jml_terima <>0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{	
			$noFak=$row['nofak'];
			$id_obat=$row['kode'];
			$jumlah=$row['jml_terima'];
			
			$batch=$row['batch'];
			$tgl_exp=$row['expired'];
			$disc=$row['disc'];
			
			$hrg_nett = $row['hrg'];
			if($this->getViewState('ppn') == '0')
			{
				$hrg_ppn = $row['hrg'];
			}
			else
			{
				$hrg_ppn = $row['hrg'] + ($row['hrg'] * (0.1));
			}
			$hrg_disc = $hrg_nett - ($hrg_nett*($disc/100)) ;
			$hrg_ppn_disc = $hrg_disc + ($hrg_disc*($disc/100)) ;
						
			//insert tbt_obat_masuk
			
			//$newObatMasuk= new ObatMasukRecord();
			$newObatMasuk = ObatMasukRecord::finder()->find('no_po = ? AND id_obat =?',$noPO,$id_obat);
			$newObatMasuk->no_faktur=$noFak;			
			$newObatMasuk->tgl_faktur=$tglFaktur;
			$newObatMasuk->tgl_jth_tempo=$tglJthTempo;						
			$newObatMasuk->tgl_terima=$tglTerima;
			$newObatMasuk->id_obat=$id_obat;
			$newObatMasuk->jumlah=$jumlah;			
			$newObatMasuk->discount=$disc;
			$newObatMasuk->hrg_nett=$hrg_nett;
			$newObatMasuk->hrg_disc=$hrg_disc;
			$newObatMasuk->hrg_ppn=$hrg_ppn;
			$newObatMasuk->hrg_ppn_disc=$hrg_ppn_disc;
			$newObatMasuk->sumber='01';
			$newObatMasuk->no_batch=$batch;
			$newObatMasuk->tgl_exp=$tgl_exp;
			$newObatMasuk->st_keuangan='0';
			$newObatMasuk->petugas=$operator;
			
			$newObatMasuk->Save();
			
			$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
			$id = HrgObatRecord::finder()->findBySql($sql)->id;			
			
					//ambil jumlah di tbt_stok_lain
					$sql="SELECT 
							jumlah 
						  FROM 
						  	tbt_stok_lain 
						  WHERE 
						  	id_obat='$id_obat' 
							AND id_harga='$id' 
							AND tujuan = '2'";
							
					$arr=$this->queryAction($sql,'R');
					foreach($arr as $row)
					{
						$jmlAwal = $row['jumlah'];
					}			
					/*
					//Update tbt_stok_lain
					$jmlTotal = $jumlah + $jmlAwal;
					$sql="UPDATE 
							tbt_stok_lain 
						SET 
							jumlah='$jmlTotal' 
						WHERE 
							id_obat='$id_obat'
							AND id_harga='$id' 
							AND tujuan='2' ";
					
					$this->queryAction($sql,'C');
					*/
					//UPDATE tgl di tbt_obat_harga
					$sql="UPDATE 
							tbt_obat_harga 
						SET 
							tgl='$dateNow' 
						WHERE 
							kode='$id_obat'
							AND id = '$id' ";
				
				$this->queryAction($sql,'C');
			
			//jika Discount!=0, update hrg_netto_disc & hrg_ppn_disc di tbt_obat_harga
			if($disc!=0)
			{
				$sql="UPDATE 
							tbt_obat_harga 
						SET 
							hrg_netto_disc='$hrg_disc', 
							hrg_ppn_disc='$hrg_ppn_disc' 
						WHERE 
							kode='$id_obat' ";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}else{
				$sql="UPDATE 
							tbt_obat_harga 
						SET 
							hrg_netto='$hrg_nett', 
							hrg_ppn='$hrg_ppn' 
						WHERE 
							kode='$id_obat' ";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}			
			
			//------------------------------ tbt_stok_lain ------------------------------
			$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
			$id = HrgObatRecord::finder()->findBySql($sql)->id;
			
			$sql="SELECT 
					id_harga
				FROM 
					tbt_stok_lain
				WHERE 
					id_obat='$id_obat'
					AND id_harga = '$id'";
			$tes = $sql;
			$arr=$this->queryAction($sql,'S');			
			
			/*
			if(count($arr)==0) //harga berubah
			{
				// INSERT tbt_stok_lain
				$sql="INSERT INTO 
						tbt_stok_lain 
						(id_obat,id_harga,jumlah,sumber,tujuan)
					VALUES
						('$id_obat','$id','$jumlah','01','2') ";
				$this->queryAction($sql,'C');
			}*/
			
		}
		
		//Update status di tbt_obat_beli jadi
		$sql="UPDATE tbt_obat_beli SET flag='1' WHERE no_po='$noPO' ";
		$this->queryAction($sql,'C');
			
		$this->queryAction($nmTable,'D');
		//$this->test->Text = $tes;
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakObatMasuk1',array('noPO'=>$noPO,'tgl'=>$tglFaktur,'noFak'=>$noFak,'mat'=>$cek,'ppn'=>$ppn,'pot'=>$pot)));
	}
	
	public function batalClicked($sender,$param)
	{
		if ($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}	
		
		$this->Response->Reload();
	}
	
	public function keluarClicked($sender,$param)
	{	
		if ($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}	
			
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function prevClicked($sender,$param)
    {
		$nmTable = $this->getViewState('nmTable');
		$cek = $this->getViewState('cek');
		$ppn = $this->getViewState('ppn');
		$operator=$this->User->IsUserNip;
		$noPO = $this->noPO->Text;
		$tglFaktur = $this->convertDate($this->tglFaktur->Text,'2');
		$tglJthTempo = $this->convertDate($this->tglJthTempo->Text,'2');	
		$tglTerima = $this->convertDate($this->tglTerima->Text,'2');
		$dateNow = date('Y-m-d');
		$pot=$this->potongan->text;
		
		$sql="SELECT * FROM $nmTable WHERE jml_terima <>0";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{	
			$noFak=$row['nofak'];
			$id_obat=$row['kode'];
			$jumlah=$row['jml_terima'];
			
			$batch=$row['batch'];
			$tgl_exp=$row['expired'];
			$disc=$row['disc'];
			
			$hrg_nett = $row['hrg'];
			if($this->getViewState('ppn') == '0')
			{
				$hrg_ppn = $row['hrg'];
			}
			else
			{
				$hrg_ppn = $row['hrg'] + ($row['hrg'] * (0.1));
			}
			$hrg_disc = $hrg_nett - ($hrg_nett*($disc/100)) ;
			$hrg_ppn_disc = $hrg_disc + ($hrg_disc*($disc/100)) ;
			/*
			//insert tbt_obat_masuk
			$newObatMasuk= new ObatMasukRecord();
			$newObatMasuk->no_po=$noPO;
			$newObatMasuk->no_faktur=$noFak;			
			$newObatMasuk->tgl_faktur=$tglFaktur;
			$newObatMasuk->tgl_jth_tempo=$tglJthTempo;						
			$newObatMasuk->tgl_terima=$tglTerima;
			$newObatMasuk->id_obat=$id_obat;
			$newObatMasuk->jumlah=$jumlah;			
			$newObatMasuk->discount=$disc;
			$newObatMasuk->hrg_nett=$hrg_nett;
			$newObatMasuk->hrg_disc=$hrg_disc;
			$newObatMasuk->hrg_ppn=$hrg_ppn;
			$newObatMasuk->hrg_ppn_disc=$hrg_ppn_disc;
			$newObatMasuk->sumber='01';
			$newObatMasuk->no_batch=$batch;
			$newObatMasuk->tgl_exp=$tgl_exp;
			$newObatMasuk->st_keuangan='0';
			$newObatMasuk->petugas=$operator;
			
			$newObatMasuk->Save();
			
			$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
			$id = HrgObatRecord::finder()->findBySql($sql)->id;
						
			//ambil jumlah di tbt_stok_lain
			$sql="SELECT 
						jumlah 
					  FROM 
						tbt_stok_lain 
					  WHERE 
						id_obat='$id_obat' 
						AND id_harga='$id' 
						AND tujuan = '2'";
							
					$arr=$this->queryAction($sql,'R');
					foreach($arr as $row)
					{
						$jmlAwal = $row['jumlah'];
					}			
					
					//Update tbt_stok_lain
					$jmlTotal = $jumlah + $jmlAwal;
					$sql="UPDATE 
							tbt_stok_lain 
						SET 
							jumlah='$jmlTotal' 
						WHERE 
							id_obat='$id_obat'
							AND id_harga='$id' 
							AND tujuan='2' ";
					
					$this->queryAction($sql,'C');
					
					//UPDATE tgl di tbt_obat_harga
					$sql="UPDATE 
							tbt_obat_harga 
						SET 
							tgl='$dateNow' 
						WHERE 
							kode='$id_obat'
							AND id = '$id' ";
							
				$this->queryAction($sql,'C');
			
			//jika Discount!=0, update hrg_netto_disc & hrg_ppn_disc di tbt_obat_harga
			if($disc!=0)
			{
				$sql="UPDATE 
							tbt_obat_harga 
						SET 
							hrg_netto_disc='$hrg_disc', 
							hrg_ppn_disc='$hrg_ppn_disc' 
						WHERE 
							kode='$id_obat' ";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			//------------------------------ tbt_stok_lain ------------------------------
			$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
			$id = HrgObatRecord::finder()->findBySql($sql)->id;
			
			$sql="SELECT 
					id_harga
				FROM 
					tbt_stok_lain
				WHERE 
					id_obat='$id_obat'
					AND id_harga = '$id'";
			$tes = $sql;
			$arr=$this->queryAction($sql,'S');			
			
			if(count($arr)==0) //harga berubah
			{
				// INSERT tbt_stok_lain
				$sql="INSERT INTO 
						tbt_stok_lain 
						(id_obat,id_harga,jumlah,sumber,tujuan)
					VALUES
						('$id_obat','$id','$jumlah','01','2') ";
				$this->queryAction($sql,'C');
			}*/
		}
		/*
		//Update status di tbt_obat_beli jadi
		$sql="UPDATE tbt_obat_beli SET flag='1' WHERE no_po='$noPO' ";
		$this->queryAction($sql,'C');
			
		$this->queryAction($nmTable,'D');*/
		//$this->test->Text = $tes;
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakObatMasukEdit',array('noPO'=>$noPO,'tgl'=>$tglFaktur,'noFak'=>$noFak,'mat'=>$cek,'ppn'=>$ppn,'pot'=>$pot,'tab'=>$nmTable)));
	}	
}
?>
