<?php

/*-----------------------------------
PENERIMAAN BARANG DALAM SATUAN KECIL
-----------------------------------*/

class ObatMasuk extends SimakConf
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
			$this->potongan->text=0;	
			/*
			Set initial stok for tbt_stok_lain
			$sql = "SELECT id,kode FROM tbt_obat_harga ORDER BY kode";	
			$arr=$this->queryAction($sql,'R');			
			foreach($arr as $row)
			{		
				$kode = $row['kode'];
				$id_harga = $row['id'];
				
				$sql = "INSERT INTO tbt_stok_lain (id_obat,
												   id_harga,
												   sumber,
												   tujuan,
												   jumlah) 
												   VALUES ('$kode',
												   		   '$id_harga',
												   		   '01',
												   		   '10',
												   		   '0')";
				$this->queryAction($sql,'R');								   		   
			}	
			
			
			$sql = "SELECT kode FROM tbm_obat GROUP BY kode ORDER BY kode";
			$arr = $this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$kode =  $row['kode'];
				$sql = "SELECT count(kode) AS jml, kode FROM tbt_obat_harga WHERE kode=$kode";
				$q = $this->queryAction($sql,'S');
				foreach($q as $brs)
				{
					$jml = $brs['jml'];
					$id = $brs['kode'];
					if($jml > 1)
					{						
						if (!$this->getViewState('nmTable'))
						{			
							$nmTable = 'tempDuplicate';
							$this->setViewState('nmTable',$nmTable);	
							$sql = "CREATE TABLE $nmTable (kode CHAR (5), PRIMARY KEY (kode)) ENGINE = MEMORY";							
							$this->queryAction($sql,'C');//Create new tabel bro...		
							$sql = "INSERT INTO $nmTable VALUES ('$id')";
							$this->queryAction($sql,'C');							
						}
						else//Tabel sudah eksis!!
						{
							$nmTable = $this->getViewState('nmTable');	
							$sql = "INSERT INTO $nmTable VALUES ('$id')";
							$this->queryAction($sql,'C');						
						}					
					}
				}			
			}*/			
		}	
		
		if ($this->getViewState('nmTable'))
		{
			$this->test->Text = $this->getViewState('nmTable');
		}	
	}
	
	public function onLoad($param)
	{	
		$mode=$this->Request['ode'];
		$noPO=$this->Request['noPO'];		
		$tgl=$this->Request['tgl'];		
		$noFak=$this->Request['noFak'];
		$mat=$this->Request['mat'];
		$ppn=$this->Request['ppn'];
		$pot=$this->Request['pot'];
		//$nmTable=$this->Request['tab'];
		$this->setViewState('mode',$mode);
		$this->setViewState('noPO',$noPO);
		$this->setViewState('tgl',$tgl);
		$this->setViewState('noFak',$noFak);
		$this->setViewState('mat',$mat);
		$this->setViewState('ppn',$ppn);
		$this->setViewState('pot',$pot);
		//$this->setViewState('nmTable',$nmTable);
	}

	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
			
		if($this->getViewState('mode') =='1')
		{
			$this->noPO->text=$mode;
			$this->jmlFktr->text=1;
			//$this->tglFaktur->text=$tgl;
			//$this->noFktr->text=$noFak;
			//$this->mtr->SelectedValue=$mat;
			//$this->ppn->SelectedValue=$ppn;
			//$this->potongan->text=$pot;
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
		if($this->modeTerima->SelectedValue == '0')//penerimaan baru
		{
			$sql = "SELECT no_po FROM tbt_obat_beli WHERE no_po='$noPO' AND flag='0'";
		}
		elseif($this->modeTerima->SelectedValue == '1')//penerimaan tunda
		{
			$sql = "SELECT no_po FROM tbt_obat_beli_tunda WHERE no_po='$noPO' AND flag='0'";
		}
		
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
		if($this->modeTerima->SelectedValue == '0')//penerimaan baru
		{
			$sql = "SELECT * FROM tbt_obat_beli WHERE no_po='$noPO' AND flag='0' ORDER BY id DESC";
		}
		elseif($this->modeTerima->SelectedValue == '1')//penerimaan tunda
		{
			$sql = "SELECT * FROM tbt_obat_beli_tunda WHERE no_po='$noPO' AND flag='0' ORDER BY id DESC";
		}
					
		$arr = $this->queryAction($sql,'R');
		
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
					jml_terima_tunda int(11) DEFAULT '0',
					hrg float DEFAULT '0',
					batch varchar(25) DEFAULT NULL,
					expired date DEFAULT NULL,
					disc float(4,1) DEFAULT '0',
					st_pembelian char(1) DEFAULT '0',
					st_tunda_sisa char(1) DEFAULT '0',
					PRIMARY KEY (id)
				) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			$nofak = $this->noFktr->Text;
			//insert kode,pbf,jml dari tbt_obat_beli dengan No.PO yg ditentukan ke tblTemp
			
			foreach($this->ambilDataObatBeli($this->noPO->Text) as $row)
			{
				$kode = $row['kode'];
				
				$st_pembelian = $row['st_pembelian'];
				
				if($st_pembelian == '0') //Pembelian dari pbf
				{
					/*----------- FORMAT SATUAN BESAR -----------*/
					//$jml = $row['jumlah'];
					
					/*----------- FORMAT SATUAN BESAR -----------*/
					$jml = $row['jumlah_kecil'];
				}
				else
				{
					$jml = $row['jumlah_kecil'];
				}
				
				
				$sql="INSERT INTO $nmTable (nofak,kode,jml,st_pembelian) VALUES ('$nofak','$kode','$jml',$st_pembelian)";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql = "SELECT 
						*
					FROM 
						$nmTable ";					
			$arr = $this->queryAction($sql,'R');
			
			//insert hrg_ppn dari tbt_obat_hrg untuk tiap barang di tblTemp
			foreach($arr as $row)
			{
				$kode = $row['kode'];
				$jmlSatBesar = ObatRecord::finder()->findByPk($kode)->jml_satuan_besar;
				//$sql="SELECT hrg_ppn FROM tbt_obat_harga WHERE kode='$kode' ";
				
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$kode'";
				$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
			
				$sql="SELECT hrg_netto FROM tbt_obat_harga WHERE kode='$kode' AND id = '$idMax'  ";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					if($st_pembelian == '0') //pembelian dari PBF
					{
						/*----------- FORMAT SATUAN BESAR -----------*/
						//$hrg_netto = $row['hrg_netto'] * $jmlSatBesar;		
						
						/*----------- FORMAT SATUAN KECIL -----------*/
						$hrg_netto=$row['hrg_netto'];
					}
					else
					{
						$hrg_netto=$row['hrg_netto'];
					}
				
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
				
				$st_pembelian = $row['st_pembelian'];
				
				if($st_pembelian == '0') //Pembelian dari pbf
				{
					/*----------- FORMAT SATUAN BESAR -----------*/
					//$jml = $row['jumlah'];
					
					/*----------- FORMAT SATUAN KECIL -----------*/
					$jml = $row['jumlah_kecil'];
				}
				else
				{
					$jml = $row['jumlah_kecil'];
				}
				
				$sql="INSERT INTO $nmTable (kode,jml) VALUES ('$kode','$jml')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql = "SELECT 
						*
					FROM 
						$nmTable ";					
			$arr = $this->queryAction($sql,'R');
			
			//insert hrg_ppn dari tbt_obat_hrg untuk tiap barang di tblTemp
			foreach($arr as $row)
			{
				$kode = $row['kode'];
				$jmlSatBesar = ObatRecord::finder()->findByPk($kode)->jml_satuan_besar;
				
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$kode'";
				$idMax = HrgObatRecord::finder()->findBySql($sql)->id;
			
				$sql="SELECT hrg_netto FROM tbt_obat_harga WHERE kode='$kode' AND id = '$idMax'  ";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					if($st_pembelian == '0') //pembelian dari PBF
					{
						/*----------- FORMAT SATUAN BESAR -----------*/
						//$hrg_netto = $row['hrg_netto'] * $jmlSatBesar;		
						
						/*----------- FORMAT SATUAN KECIL -----------*/
						$hrg_netto=$row['hrg_netto'];		
					}
					else
					{
						$hrg_netto=$row['hrg_netto'];
					}
				
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
		$stTunda = $item->DataItem['st_tunda_sisa'];	
		
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
		   $item->nofak->noFakTxt->Columns=10;
           $item->jmlTerima->TextBox->Columns=5;
		   $item->hrg->TextBox->Columns=7;
		   $item->batch->TextBox->Columns=7;
		   $item->expired->TextBox->Columns=7;
		   $item->disc->TextBox->Columns=2;
		   
			if($stTunda == '0')
			{
				$item->stTunda->checkBoxList->Checked = false;	
			}	
			elseif($stTunda == '1')
			{	
				$item->stTunda->checkBoxList->Checked = true;	
			}
			
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$item->stTunda->checkBoxList->Enabled=true;
			
			if($stTunda == '0')
			{
				$item->stTunda->stTundaText->Text = 'Tidak';	
			}	
			elseif($stTunda == '1')
			{
				$item->stTunda->stTundaText->Text = 'Ya';		
			}	
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
				
		$sql="SELECT kode,jml,jml_terima_tunda,hrg,st_pembelian,st_tunda_sisa FROM $nmTable WHERE id='$id' ";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			$jml=$row['jml'];
			$kode=$row['kode'];
			$st_pembelian=$row['st_pembelian'];
			$st_tunda=$row['st_tunda_sisa'];
		}		
		
		$nofak = $item->nofak->noFakTxt->Text;
		$jml_terima = abs($item->jmlTerima->TextBox->Text);
		$hrg=$item->hrg->TextBox->Text;
		$batch=$item->batch->TextBox->Text;
		//$expired=$this->convertDate($item->expired->TextBox->Text,'2');
		//$expiredTmp=$item->expired->TextBox->Text;
		
		if(substr($item->expired->TextBox->Text,2,1) == '-'){
			$exp = explode('-',$item->expired->TextBox->Text);
		}elseif(substr($item->expired->TextBox->Text,2,1) == '/'){
			$exp = explode('/',$item->expired->TextBox->Text);
		}
		
		
		$expired = '20' . $exp[1] . '-' . $exp[0] . '-' . '01'; 
		$disc=$item->disc->TextBox->Text;
		
		if($item->stTunda->checkBoxList->Checked === true)
			$stTunda = '1';
		else
			$stTunda = '0';	
		
		if($expired == '' && $stTunda == '0') //tanggal expired tidak diisi
		{
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Tanggal Expired harus di isi !</p>\',timeout: 3000,dialog:{modal: true}});');		
		}
		elseif(($jml_terima == '' || $jml_terima == 0) && $stTunda == '0') //jumlah terima belum di isi
		{
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Jumlah belum di isi !</p>\',timeout: 3000,dialog:{modal: true}});');		
		}
		elseif($jml_terima > $jml) //jumlah terima lebih besar
		{
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Jumlah terima melebihi jumlah yang dipesan !</p>\',timeout: 3000,dialog:{modal: true}});');		
		}
		elseif(!preg_match('/^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$/', $expired) && $jml_terima > 0)
		{
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arr=$this->queryAction($sql,'R');
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();
			
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Format tanggal expired harus yyyy-mm-dd (misal : 2004-02-29) !</p>\',timeout: 3000,dialog:{modal: true}});');
		}
		else
		{
			$sql="SELECT jml FROM $nmTable WHERE nofak='$nofak' AND kode='$kode' ";
			$arr=$this->queryAction($sql,'S');
			foreach($arr as $row)
			{
				$jmlTotalPesan+=$row['jml'];
			}
				
			$jmlSisaTerima = $jml - $jml_terima;
			
			if($jmlSisaTerima == 0)
				$stTunda = 0;
			/*
			if($jml_terima == $jml)
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
			*/
			
			if($jml_terima <= $jml)
			{
				$sql="SELECT id,jml,jml_terima,jml_terima_tunda,st_tunda_sisa FROM $nmTable WHERE id<>'$id' AND nofak='$nofak' AND kode='$kode' AND expired='$expired' ";
				$arr=$this->queryAction($sql,'S');
				if($arr)
				{
					foreach($arr as $row)
					{
						$idAwal=$row['id'];
						$jmlAwal=$row['jml'];
						$jmlTerimaAwal=$row['jml_terima'];
						$jmlTerimaTunda=$row['jml_terima_tunda'];
					}
					
					$jmlAwal = $jmlAwal + $jml_terima;
					$jmlTerimaAwal = $jmlTerimaAwal + $jml_terima;
					
					if($stTunda == '1')
					{
						$jmlTerimaTunda = $jmlTerimaTunda + $jmlSisaTerima;
						$jmlAwal = $jmlTerimaAwal + $jmlTerimaTunda;
							
						$sql="UPDATE $nmTable SET
							jml='$jmlAwal', 
							jml_terima='$jmlTerimaAwal',
							jml_terima_tunda='$jmlTerimaTunda',
							st_tunda_sisa='1'
							WHERE id='$idAwal'";
					}
					else
					{
						$sql="UPDATE $nmTable SET
							jml='$jmlAwal', 
							jml_terima='$jmlTerimaAwal',
							st_tunda_sisa='0'
							WHERE id='$idAwal'";
					}
					
					$this->queryAction($sql,'C');
					
					$sql="SELECT id,jml FROM $nmTable WHERE nofak='$nofak' AND kode='$kode' AND jml_terima='0' ";
					$arr=$this->queryAction($sql,'S');
					
					if($arr)
					{
						foreach($arr as $row)
						{
							$idAwal=$row['id'];
							$jmlAwal=$row['jml'];
						}	
						
						$jmlAwal = 	$jmlAwal - $jml_terima;
						
						if($stTunda == '1')
						{
							$jmlAwal = 	$jmlAwal - $jmlTerimaTunda;
						}
						
						$sql="UPDATE $nmTable SET jml='$jmlAwal' WHERE nofak='$nofak' AND kode='$kode' AND jml_terima='0' ";
						$this->queryAction($sql,'C');
						
						if($jmlAwal == '0')
						{
							$sql="DELETE FROM $nmTable WHERE id = '$idAwal' ";
							$this->queryAction($sql,'C');
						}
					}
					
					$jmlSisaTerima = 0;
				}
				else
				{		
					if($stTunda == '1')
					{	
						$sql="UPDATE $nmTable SET
							nofak='$nofak', 
							jml='$jml', 
							jml_terima='$jml_terima', 
							jml_terima_tunda='$jmlSisaTerima', 
							hrg='$hrg', 
							batch='$batch', 
							expired='$expired',
							disc='$disc',
							st_tunda_sisa='1' 
							WHERE id='$id'";
						$this->queryAction($sql,'C');	
						
						$jmlSisaTerima = 0;
					}
					else
					{				
						$sql="UPDATE $nmTable SET
							nofak='$nofak', 
							jml='$jml_terima', 
							jml_terima='$jml_terima', 
							jml_terima_tunda='0', 
							hrg='$hrg', 
							batch='$batch', 
							expired='$expired',
							disc='$disc',
							st_tunda_sisa='0'
							WHERE id='$id'";
						$this->queryAction($sql,'C');	
					}	
				}
				
				if($jmlSisaTerima > 0)
				{
					//$this->makeTblTemp();
					
					$sql="SELECT jml FROM $nmTable WHERE nofak='$nofak' AND kode='$kode' AND jml_terima='0' ";
					$arr=$this->queryAction($sql,'S');
					
					if($arr)
					{
						foreach($arr as $row)
						{
							$jml=$row['jml'];
						}	
						
						$jml = 	$jml + $jmlSisaTerima;
						$sql="UPDATE $nmTable SET jml='$jml' WHERE nofak='$nofak' AND kode='$kode' AND jml_terima='0' ";
					}
					else
					{
						$sql="INSERT INTO $nmTable (nofak,kode,jml,hrg,st_pembelian) VALUES ('$nofak','$kode','$jmlSisaTerima','$hrg',$st_pembelian)";
					}
					$this->queryAction($sql,'C');
				}
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
			 else //tanggal expired belum di isi
			 {
				$this->cetakBtn->Enabled=true;
				$this->prevBtn->Enabled=true;
			 }
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
		
		if(intval($this->jmlHari->Text))
		{
			$durasi = intval($this->jmlHari->Text);
			
			$jam = date("G");
			$menit = date("i");
			$detik = date("s");
			
			$hari = date("d");
			$bulan = date("m");
			$tahun = date("Y");
			
			$time = mktime($jam,$menit,$detik,$bulan,$hari,$tahun);
			$plus = $durasi*24*60*60; //lama sewa diubah kedetik
			$timePlus = $time + $plus;
			$hasil = date("Y-m-d H:i:s", $timePlus);
			$tglAkhir = substr($hasil,0,10);
			$wktAkhir = substr($hasil,11,8);
			
			//$tglJthTempo = $this->convertDate($this->tglJthTempo->Text,'2');	
			$tglJthTempo = $tglAkhir;
		}
		
		$tglTerima = $this->convertDate($this->tglTerima->Text,'2');
		$dateNow = date('Y-m-d');
		$pot=$this->potongan->text;
		
		$newObatMasukEmbel= new ObatMasukEmbelRecord();
		$newObatMasukEmbel->no_po=$noPO;
		$newObatMasukEmbel->jml_fktr=$this->jmlFktr->text;
		$newObatMasukEmbel->no_fktr=$this->noFktr->text;
		$newObatMasukEmbel->tgl_fktr=$tglFaktur;
		$newObatMasukEmbel->tgl_jth_tempo=$tglJthTempo;
		$newObatMasukEmbel->tgl_trima_brg=$tglTerima;
		$newObatMasukEmbel->materai=$cek;			
		$newObatMasukEmbel->ppn=$ppn;
		$newObatMasukEmbel->pot=$pot;						
		
		$newObatMasukEmbel->Save();
		
		$sql="SELECT * FROM $nmTable WHERE jml_terima > 0 OR (jml_terima =0 AND st_tunda_sisa=1)";
		$arr=$this->queryAction($sql,'S');
		foreach($arr as $row)
		{	
			$noFak=$row['nofak'];
			$id_obat=$row['kode'];
			$jumlah=$row['jml_terima'];
			$jml_terima_tunda=$row['jml_terima_tunda'];
			$stBeli=$row['st_pembelian'];
			$batch=$row['batch'];
			$tgl_exp=$row['expired'];
			
			$tgl_exp2 = explode('-',$tgl_exp);
			$thnExp = $tgl_exp2[0];
			$blnExp = $tgl_exp2[1];
			
			$disc=$row['disc'];
			$st_tunda_sisa=$row['st_tunda_sisa'];
			
			$jmlSatBesar = ObatRecord::finder()->findByPk($id_obat)->jml_satuan_besar;			
			
			if($stBeli == '0') //pembelian dari PBF
			{
				/*----------- FORMAT SATUAN BESAR -----------
				$hrg = $row['hrg'] / $jmlSatBesar;
				$jmlSatKecil = $jmlSatBesar * $jumlah;
				$jmlTundaSatKecil = $jmlSatBesar * $jml_terima_tunda;
				*/
				
				/*----------- FORMAT SATUAN KECIL -----------*/
				$hrg = $row['hrg'];
				$jmlSatKecil = $jumlah;
				$jmlTundaSatKecil = $jml_terima_tunda;
			}
			else
			{
				$hrg = $row['hrg'];
				$jmlSatKecil = $jumlah;
				$jmlTundaSatKecil = $jml_terima_tunda;
			}
			
			$hrg_nett = $hrg ;
			
			if($this->ppn->SelectedValue == '0')
			{
				$hrg_ppn = $hrg ;
			}
			else
			{
				$hrg_ppn = $hrg  + ($hrg  * (0.1));
			}
			
			$hrg_disc = $hrg_nett - ($hrg_nett*($disc/100)) ;
			$hrg_ppn_disc = $hrg_ppn - ($hrg_ppn*($disc/100)) ;
			
			if($this->modeTerima->SelectedValue == '0')
			{
				//insert tbt_obat_masuk
				$newObatMasuk= new ObatMasukRecord();
				$newObatMasuk->no_po=$noPO;
				$newObatMasuk->no_faktur=$noFak;			
				$newObatMasuk->tgl_faktur=$tglFaktur;
				$newObatMasuk->tgl_jth_tempo=$tglJthTempo;						
				$newObatMasuk->tgl_terima=$tglTerima;
				$newObatMasuk->id_obat=$id_obat;
				$newObatMasuk->jumlah=$jmlSatKecil;
				$newObatMasuk->jumlah_tunda_sisa=$jmlTundaSatKecil;			
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
				$newObatMasuk->st_tunda_sisa=$st_tunda_sisa;
				
				$newObatMasuk->Save();
			}
			elseif($this->modeTerima->SelectedValue == '1')
			{
				//$sql = "SELECT * FROM tbt_obat_masuk WHERE no_po='$noPO' AND id_obat = '$id_obat' AND hrg_nett = '$hrg_nett' AND tgl_exp = '$tgl_exp' ";
				$sql = "SELECT * FROM tbt_obat_masuk WHERE no_po='$noPO' AND id_obat = '$id_obat' AND hrg_nett = '$hrg_nett' AND MONTH(tgl_exp) = '$blnExp' AND YEAR(tgl_exp) = '$thnExp' ";
				$arr = $this->queryAction($sql,'S');
				
				if($arr)
				{
					//update tbt_obat_masuk
					$data = ObatMasukRecord::finder()->findBySql($sql);
					$data->jumlah = $jmlSatKecil + $data->jumlah;
					$data->jumlah_tunda_sisa = $data->jumlah_tunda_sisa - $jmlSatKecil;	
					$data->Save();
				}
				else
				{
					//insert tbt_obat_masuk
					$newObatMasuk= new ObatMasukRecord();
					$newObatMasuk->no_po=$noPO;
					$newObatMasuk->no_faktur=$noFak;			
					$newObatMasuk->tgl_faktur=$tglFaktur;
					$newObatMasuk->tgl_jth_tempo=$tglJthTempo;						
					$newObatMasuk->tgl_terima=$tglTerima;
					$newObatMasuk->id_obat=$id_obat;
					$newObatMasuk->jumlah=$jmlSatKecil;
					$newObatMasuk->jumlah_tunda_sisa=$jmlTundaSatKecil;			
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
					$newObatMasuk->st_tunda_sisa=$st_tunda_sisa;
					
					$newObatMasuk->Save();
				}
			}						
			
			if($st_tunda_sisa == '1')
			{	
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_masuk";
				$id_obat_masuk = ObatMasukRecord::finder()->findBySql($sql)->id;
				
				//insert tbt_obat_beli_tunda
				$data = new ObatBeliTundaRecord();
				$data->id_obat_masuk = $id_obat_masuk;
				$data->no_po = $noPO;
				$data->tgl_po = ObatBeliRecord::finder()->find('no_po=? AND kode=?',array($noPO,$id_obat))->tgl_po;
				$data->waktu = ObatBeliRecord::finder()->find('no_po=? AND kode=?',array($noPO,$id_obat))->waktu;
				$data->pbf = ObatBeliRecord::finder()->find('no_po=? AND kode=?',array($noPO,$id_obat))->pbf;
				$data->kode = $id_obat;
				$data->jumlah = $jml_terima_tunda;
				$data->jumlah_kecil = $jmlTundaSatKecil;
				$data->catatan = ObatBeliRecord::finder()->find('no_po=? AND kode=?',array($noPO,$id_obat))->catatan;
				$data->petugas=$operator;
				$data->flag = '0';
				$data->nm_apotik_luar = ObatBeliRecord::finder()->find('no_po=? AND kode=?',array($noPO,$id_obat))->nm_apotik_luar;
				$data->st_pembelian = $stBeli;
				$data->Save();
			}
				//Cek hrg_ppn di tbt_obat_harga, jika beda dengan $hrg_ppn=$row['hrg'] => INSERT hrg_ppn,hrg_netto,tgl di tbt_obat_harga
				//$sql="SELECT hrg_ppn FROM tbt_obat_harga WHERE kode='$id_obat' ";
				//ini ngambil idHarga paling baru mas
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
				$id = HrgObatRecord::finder()->findBySql($sql)->id;
				
				$sql="SELECT 
						hrg_netto, disc
					FROM 
						tbt_obat_harga 
					WHERE 
						kode='$id_obat'
						AND id = '$id'";
				
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					if($row['hrg_netto'] <> $hrg_nett) //harga berubah
					{
						//$hrg_nett = ($hrg_nett + $row['hrg_netto']) / 2;
						
						//$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$id_obat' AND id_harga = '$id' AND expired = '$tgl_exp' ";
						$sql = "SELECT SUM(jumlah) AS jumlah FROM tbt_stok_lain WHERE id_obat = '$id_obat' AND id_harga = '$id' AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";

						
						$jmlStok = StokLainRecord::finder()->findBySql($sql)->jumlah;
						
						/*----------- FORMAT HARGA RATA-RATA JIKA TERJADI PERUBAHAN HARGA -----------
						$hrg_nett = (($jmlSatKecil * $hrg_nett) + ($jmlStok * $row['hrg_netto'])) / ($jmlStok + $jmlSatKecil) ;
						*/
						
						if($this->ppn->SelectedValue == '0')
						{
							$hrg_ppn = $hrg_nett ;
						}
						else
						{
							$hrg_ppn = $hrg_nett  + ($hrg_nett  * (0.1));
						}
						
						$hrg_disc = $hrg_nett - ($hrg_nett*($disc/100)) ;
						$hrg_ppn_disc = $hrg_ppn - ($hrg_ppn*($disc/100)) ;
						 
						if($row['hrg_netto'] == '0') //obat baru
						{	
							$sql="UPDATE 
								tbt_obat_harga 
							SET 
								hrg_netto='$hrg_nett',
								hrg_ppn='$hrg_ppn',
								hrg_netto_disc='0',
								hrg_ppn_disc='0',
								tgl='$dateNow',
								st_pembelian='$stBeli'
							WHERE 
								kode='$id_obat'
								AND id = '$id' ";
							
							$this->queryAction($sql,'C');
							
							//UPDATE harga-harga obat untuk id_harga yg lain => harga mengikuti harga paling baru
							$sql="UPDATE 
								tbt_obat_harga 
								SET 
									hrg_netto='$hrg_nett',
									hrg_ppn='$hrg_ppn',
									hrg_netto_disc='0',
									hrg_ppn_disc='0'
								WHERE 
									kode='$id_obat' ";
							
							$this->queryAction($sql,'C');
							
							//ambil jumlah di tbt_stok_lain
							$sql="SELECT 
									jumlah 
								  FROM 
									tbt_stok_lain 
								  WHERE 
									id_obat='$id_obat' 
									AND id_harga='$id' 
									AND tujuan = '2'
									AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";
									
							$arr=$this->queryAction($sql,'R');
							foreach($arr as $row)
							{
								$jmlAwal = $row['jumlah'];
							}			
							
							//Update tbt_stok_lain
							$jmlTotal = $jmlSatKecil + $jmlAwal;
							$sql="UPDATE 
									tbt_stok_lain 
								SET 
									jumlah='$jmlTotal' 
								WHERE 
									id_obat='$id_obat'
									AND id_harga='$id' 
									AND tujuan='2' 
									AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";
							
							$this->queryAction($sql,'C');
						}
						else
						{
							// INSERT hrg_ppn,hrg_netto,tgl di tbt_obat_harga
							$sql="INSERT INTO 
									tbt_obat_harga 
									(kode,sumber,hrg_netto,hrg_ppn,hrg_netto_disc,hrg_ppn_disc,tgl,st_pembelian)
								VALUES
									('$id_obat','01','$hrg_nett','$hrg_ppn','0','0','$dateNow','$stBeli') ";
							$this->queryAction($sql,'C');
									
							//UPDATE harga-harga obat untuk id_harga yg lain => harga mengikuti harga paling baru
							$sql="UPDATE 
								tbt_obat_harga 
								SET 
									hrg_netto='$hrg_nett',
									hrg_ppn='$hrg_ppn',
									hrg_netto_disc='0',
									hrg_ppn_disc='0'
								WHERE 
									kode='$id_obat' ";
							
							$this->queryAction($sql,'C');
						}
					}
					else //harga tetap
					{
						if($row['disc'] <> $disc) //disc berubah
						{
							// INSERT hrg_ppn,hrg_netto,tgl di tbt_obat_harga
							$sql="INSERT INTO 
									tbt_obat_harga 
									(kode,sumber,hrg_netto,hrg_ppn,hrg_netto_disc,hrg_ppn_disc,tgl,st_pembelian)
								VALUES
									('$id_obat','01','$hrg_nett','$hrg_ppn','0','0','$dateNow','$stBeli') ";
							
							$this->queryAction($sql,'C');
							
							//UPDATE harga-harga obat untuk id_harga yg lain => harga mengikuti harga paling baru
							$sql="UPDATE 
								tbt_obat_harga 
								SET 
									hrg_netto='$hrg_nett',
									hrg_ppn='$hrg_ppn',
									hrg_netto_disc='0',
									hrg_ppn_disc='0'
								WHERE 
									kode='$id_obat' ";
							
							$this->queryAction($sql,'C');
						}
						else
						{
							//ambil jumlah di tbt_stok_lain
							$sql="SELECT 
									jumlah 
								  FROM 
									tbt_stok_lain 
								  WHERE 
									id_obat='$id_obat' 
									AND id_harga='$id' 
									AND tujuan = '2'
									AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";
									
							$arr=$this->queryAction($sql,'R');
							foreach($arr as $row)
							{
								$jmlAwal = $row['jumlah'];
							}			
							
							//Update tbt_stok_lain
							$jmlTotal = $jmlSatKecil + $jmlAwal;
							$sql="UPDATE 
									tbt_stok_lain 
								SET 
									jumlah='$jmlTotal' 
								WHERE 
									id_obat='$id_obat'
									AND id_harga='$id' 
									AND tujuan='2' 
									AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";
							
							$this->queryAction($sql,'C');
							
							//UPDATE tgl di tbt_obat_harga
							$sql="UPDATE 
									tbt_obat_harga 
								SET 
									tgl='$dateNow' 
								WHERE 
									kode='$id_obat'
									AND id = '$id' ";
						}
					}
					
					$this->queryAction($sql,'C');
				}
				
				
				$sql = "SELECT MAX(id) AS id FROM tbt_obat_harga WHERE kode='$id_obat'";
				$id = HrgObatRecord::finder()->findBySql($sql)->id;
				
				//jika Discount!=0, update hrg_netto_disc & hrg_ppn_disc di tbt_obat_harga
				if($disc!=0)
				{
					$sql="UPDATE 
								tbt_obat_harga 
							SET 
								hrg_netto_disc='$hrg_disc', 
								hrg_ppn_disc='$hrg_ppn_disc',
								disc='$disc'  
							WHERE 
								kode='$id_obat'
								AND id = '$id'";
					$this->queryAction($sql,'C');
					
					$sql="UPDATE 
								tbt_obat_harga 
							SET 
								hrg_netto_disc='$hrg_disc', 
								hrg_ppn_disc='$hrg_ppn_disc',
								disc='$disc'  
							WHERE 
								kode='$id_obat' ";
					$this->queryAction($sql,'C');
				}
				
				//------------------------------ tbt_stok_lain ------------------------------
							
				$sql="SELECT 
						id_harga
					FROM 
						tbt_stok_lain
					WHERE 
						id_obat='$id_obat'
						AND id_harga = '$id'
						AND tujuan='2' 
						AND MONTH(expired) = '$blnExp' AND YEAR(expired) = '$thnExp' ";
				$tes = $sql;
				$arr=$this->queryAction($sql,'S');			
				
				if(count($arr)==0) //harga berubah
				{
					// INSERT tbt_stok_lain
					$sql="INSERT INTO 
							tbt_stok_lain 
							(id_obat,id_harga,jumlah,sumber,tujuan,expired)
						VALUES
							('$id_obat','$id','$jmlSatKecil','01','2','$tgl_exp') ";
					$this->queryAction($sql,'C');
				}
		}
		
		if($this->modeTerima->SelectedValue == '0')
		{
			//Update status di tbt_obat_beli 
			$sql="UPDATE tbt_obat_beli SET flag='1' WHERE no_po='$noPO' ";
		}
		elseif($this->modeTerima->SelectedValue == '1')
		{
			//Update status di tbt_obat_beli_tunda jadi
			$sql="UPDATE tbt_obat_beli_tunda SET flag='1' WHERE no_po='$noPO' ";
		}
		
		$this->queryAction($sql,'C');
			
		//$this->queryAction($nmTable,'D');
		//$this->test->Text = $tes;
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakObatMasuk1',array('noPO'=>$noPO,'tgl'=>$tglFaktur,'noFak'=>$noFak,'mat'=>$cek,'ppn'=>$ppn,'pot'=>$pot,'modeTerima'=>$this->modeTerima->SelectedValue,'nmTable'=>$nmTable)));
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
			if($this->ppn->SelectedValue == '0')
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
