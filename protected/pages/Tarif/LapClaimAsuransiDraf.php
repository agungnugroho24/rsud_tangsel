<?php
class LapClaimAsuransiDraf extends SimakConf
{ 
	private $sortExp = "no_trans";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 20;
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('7');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		
		if(!$this->IsPostBack && !$this->IsCallBack)
		{
			$this->gridPanel->Display = "None";
			$this->cetakBtn->Enabled = false;
			
			$this->bindGridCariFaktur();
		}
		else
		{
			if ($this->getViewState('nmTable'))
			{
				$nmTable = $this->getViewState('nmTable');
				
				$this->gridPanel->Display = 'Dynamic';	
				
				$this->bindGridBrg();
				$this->cetakBtn->Enabled = true;
			}
			else
			{
				$this->gridPanel->Display = 'None';
				$this->cetakBtn->Enabled = false;
			}
		}	
				
		$pilih = $this->getViewState('pilihPeriode');
		if ($pilih=='3')
		{
			$this->bulan->Display='Dynamic';			
			$this->prosesBtn->ValidationGroup = 'valBulan';
		}
		elseif ($pilih=='2')
		{
			$this->minggu->Display='Dynamic';
			$this->prosesBtn->ValidationGroup = 'valMinggu';
			
		}
		elseif ($pilih=='1')
		{
			$this->hari->Display='Dynamic';		
			$this->prosesBtn->ValidationGroup = 'valHari';
		}
		else
		{
			//$this->gridPanel->Display='None';
			//$this->cetakBtn->Enabled = false;
		}
	}
	
	public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$sql = "SELECT id,nama FROM tbm_kelompok WHERE (id='02' OR id='03') ORDER BY nama";
			//$this->DDUrut->DataSource=KelompokRecord::finder()->findAll($criteria);
			$this->DDUrut->DataSource = $this->queryAction($sql,'S');
			$this->DDUrut->dataBind();
			
			$this->DDKontrak->DataSource=PerusahaanRecord::finder()->findAll($criteria);
			$this->DDKontrak->dataBind();
			
			$this->DDKelompoKateg->Enabled=false;
			$this->DDanggota->Enabled=false;
			$this->DDKontrak->Enabled=false;
			
			$this->lockPeriode();
		}
	}
	 	
	public function lockPeriode()
   	{
		$dateNow = date('d-m-Y');
		
		$this->minggu->Display='None';
		$this->hari->Display='None';
		$this->bulan->Display='None';
		
		$this->DDbulan->SelectedIndex=-1;
		$this->DDtahun->SelectedIndex=-1;
		$this->tgl->Text='';
		$this->tglawal->Text='';
		$this->tglakhir->Text='';
		$this->clearViewState('cariThn');
		$this->clearViewState('cariBln');
		
		$this->tglawal->Text = $dateNow;
		$this->tglakhir->Text = $dateNow;
		$this->tgl->Text = $dateNow;
			
		//$this->DDtahun->Enabled=false;
		
		$this->DDbulan->SelectedValue = date('m');
		$this->DDtahun->DataSource=$this->tahun();
		$this->DDtahun->dataBind();
		$this->DDtahun->SelectedValue = date('Y');
		$this->prosesBtn->ValidationGroup = '';
		
	}
	
	public function panelCallback($sender,$param)
   	{
		$this->cariPanel->render($param->getNewWriter());
		
	}
	
	public function selectionChangedUrut($sender,$param)
	{
		$idKel = $this->DDUrut->SelectedValue;
		if($idKel == '02')
		{
			//$this->DDKelompoKateg->SelectedValue = 'empty';
			$this->DDKontrak->Enabled = true;	
		}
		else
		{
			$this->DDKontrak->SelectedValue = 'empty';
			$this->DDKontrak->Enabled = false;
		}
		
		$this->prosesClicked();
	}
	
	public function DDKontrakChanged($sender,$param)
	{
		$this->prosesClicked();
	}
	
	
	public function ChangedDDberdasarkan($sender,$param)
	{		
		$this->lockPeriode();
		$pilih=$this->DDberdasarkan->SelectedValue;
		if ($pilih=='3')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->DDbulan);
		}
		elseif ($pilih=='2')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);
			$this->Page->CallbackClient->focus($this->tglawal);		
			
		}
		elseif ($pilih=='1')
		{
			$this->clearViewState('pilihPeriode');
			$this->setViewState('pilihPeriode',$pilih);		
		}
		else
		{
			$this->clearViewState('pilihPeriode');
			$this->Page->CallbackClient->focus($this->DDberdasarkan);
			$this->Page->CallbackClient->focus($this->tgl);	
		}
		
		$this->prosesClicked();
	}
	
	public function ChangedDDbulan($sender,$param)
	{
		$pilih=$this->DDbulan->SelectedValue;
		if ($pilih=='')
		{
			$this->Page->CallbackClient->focus($this->DDbulan);
		}			
		else
		{
			$this->Page->CallbackClient->focus($this->DDtahun);
		}
		
		$this->prosesClicked();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$this->prosesClicked();
	}
		
	public function checkTgl($sender,$param)
	{
		$pecahTglAwal=explode('-',$this->tglawal->Text);
		$pecahTglAkhir=explode('-',$this->tglakhir->Text);
		$tglAwal=$pecahTglAwal['0'];
		$cariBln=$pecahTglAwal['1'];
		$thnAwal=$pecahTglAwal['2'];
		$tglAkhir=$pecahTglAkhir['0'];
		$cariThn=$pecahTglAkhir['1'];
		$thnAkhir=$pecahTglAkhir['2'];
		
		if($thnAkhir<$thnAwal) 
		{
			$hasil='0';
		}
		else
		{
			if($cariThn<$cariBln) 
			{
				$hasil='0';
			}
			else
			{
				if($tglAkhir<$tglAwal) 
				{
					$hasil='0';
				}
				else
				{
					//jika tgl akhir benar
					//$id_ijin=$this->getViewState('id');
					//$this->Response->redirect($this->Service->constructUrl('Lap'.$id_ijin,array('idIjin'=>$id_ijin)));
					$hasil='1';
				}
			}
		}	
		
		$param->IsValid=($hasil==='1');
	}
	
	public function prosesLock()
	{	
		//$this->tglBayar->Enabled = false;
		$this->prosesBtn->Enabled = false;
	} 
	
	public function prosesUnlock()
	{	
		//$this->tglBayar->Enabled = true;
		$this->prosesBtn->Enabled = true;
		//$this->tglBayar->Date = date('d-m-Y');
		$this->cetakBtn->Enabled = false;
		$this->gridPanel->Display = 'None';
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}
		
			
	} 
	
	public function checkNoFaktur($noTrans)
	{
		//$tglBayar = $this->convertDate($tglBayar,'2');
		//$sql = "SELECT * FROM tbt_obat_masuk WHERE tgl_jth_tempo <= '$tglBayar' AND st_keuangan='0'";	
		
		$sql = "SELECT * FROM tbt_obat_masuk WHERE no_trans = '$noTrans' AND st_keuangan='0'";	
		$arr = $this->queryAction($sql,'S');
		$jmlData = count($arr);
		if($jmlData>0)
		{
			$sql = "SELECT * FROM tbt_bayar_pbf WHERE no_trans = '$noTrans'";	
			$arr = $this->queryAction($sql,'S');
			$jmlData = count($arr);
			if($jmlData>0)
			{
				$hasil = false;
			}
			else
			{
				$hasil = true;
			}
			
		}
		else
		{
			$hasil = false;
		}
		
		return $hasil;
	}
	
	public function checkNoTransTmp($noTrans)
	{
		$nmTable = $this->getViewState('nmTable');	
		$sql = "SELECT * FROM $nmTable WHERE no_trans = '$noTrans'";	
		$arr = $this->queryAction($sql,'S');
		$jmlData = count($arr);
		if($jmlData > 0)
		{
			$hasil = true;
		}
		else
		{
			$hasil = false;
		}
		
		return $hasil;
	}
	
	public function prosesClicked()
	{	
		//if($this->IsValid)
        //{ 
			$session = $this->getSession();
			$session->remove("SomeData");    	
			$this->gridCariFaktur->CurrentPageIndex = 0;
			$this->bindGridCariFaktur();
			
			/*
			if($this->checkNoFaktur($this->noTrans->Text)==true)
			{	
				$this->makeTblTemp();			
				
				$session = $this->getSession();
				$session->remove("SomeData");    	
				$this->gridBrg->CurrentPageIndex = 0;
				$this->bindGridBrg();
				$this->noTrans->Text = '';
				$this->modeBayar->SelectedValue= '0';
			}
			else
			{
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("No. Faktur : '.$this->noTrans->Text.' tidak ditemukan.\nHarap cek ulang No. Faktur yang dimasukan.");
				</script>';	
				$this->msg->Text = '';				
			}
			*/
			
		//}
	} 
	
	public function insertTmpTable($noTrans)
	{
		$nmTable = $this->getViewState('nmTable');	
		
		$sql = "SELECT 
					SUM(if(view_asuransi.total_tdk <> 'Null',view_asuransi.total_tdk,'0')
					  +if(view_asuransi.total_apotik <> 'Null',view_asuransi.total_apotik,'0')
					  +if(view_asuransi.total_lab <> 'Null',view_asuransi.total_lab,'0')
					  +if(view_asuransi.total_rad <> 'Null',view_asuransi.total_rad,'0')
					  +if(view_asuransi.total_fisio <> 'Null',view_asuransi.total_fisio,'0') ) AS jml,
					  view_asuransi.* 
				FROM view_asuransi 
				WHERE 
					no_trans = '$noTrans' AND st_asuransi_byr= '0'
				GROUP BY no_trans";	
					  
		$arr = $this->queryAction($sql,'S');
	
		foreach($arr as $row)
		{
			$cm = $row['cm'];
			$nama = $row['nama'];
			$penjamin = $row['penjamin'];
			$nm_penjamin = $row['nm_penjamin'];
			$perus_asuransi = $row['perus_asuransi'];
			$nm_perus = $row['nm_perus'];
			$jml = $row['jml'];
			$tipe_rawat = $row['tipe_rawat'];
			
			$sql="INSERT INTO $nmTable (
					cm,
					no_trans,
					nama,
					penjamin,
					nm_penjamin,						
					perus_asuransi,
					nm_perus,
					jml,
					tipe_rawat) 
				VALUES (
					'$cm',
					'$noTrans',
					'$nama',
					'$penjamin',
					'$nm_penjamin',
					'$perus_asuransi',
					'$nm_perus',
					'$jml',
					'$tipe_rawat')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
		}
	}
	
	public function makeTblTemp($noTrans)
	{	
		//$noTrans = $this->noTrans->Text;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (
					id INT (2) auto_increment,
					cm varchar(6) NOT NULL,
					no_trans varchar(14) NOT NULL,
					nama varchar(255) NOT NULL,
					penjamin char(2) NOT NULL,
					nm_penjamin varchar(255) NOT NULL,
					perus_asuransi char(2) DEFAULT NULL,
					nm_perus varchar(255) DEFAULT NULL,
					jml float DEFAULT '0',
					tipe_rawat char(1) NOT NULL,
					PRIMARY KEY (id)
				) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			$this->insertTmpTable($noTrans);
		
		}
		else
		{
			//Cek apakah no faktur sudah masuk sebelumnya
			if($this->checkNoTransTmp($noTrans)==true)
			{	
				$this->msg->Text = '   
				<script type="text/javascript">
					alert("No. Transaksi : '.$noTrans.' sudah dimasukan sebelumnya.");
				</script>';	
				$this->msg->Text = '';
			}
			else
			{
				$this->insertTmpTable($noTrans);
			}
		}
		
		//$this->msg->Text .= ' '.$nmTable;
	} 
	
	// ---------- datagrid helper functions --------    
    public function getSortExpression() {
        if ($this->getViewState('sortExpression',null)!==null) {
            return $this->getViewState('sortExpression');
        }
        // set default in case there's no 'sortExpression' key in viewstate
        $this->setViewState('sortExpression', $this->sortExp);
        return $this->sortExp;
    }

    public function setSortExpression($sort) {
        $this->setViewState('sortExpression',$sort);
    }

    public function getSortDirection() {
        if ($this->getViewState('sortDirection',null)!==null) {
            return $this->getViewState('sortDirection');
        }
        // set default in case there's no 'sortDirection' key in viewstate
        $this->setViewState('sortDirection', $this->sortDir);
        return $this->sortDir;
    }

    public function setSortDirection($sort) {
        $this->setViewState('sortDirection',$sort);
    }
	
	private function bindGridCariFaktur()
    {
        $this->pageSize = $this->gridCariFaktur->PageSize;
		
		/*
		if($this->getViewState('offsetEdit')!='')
		{
			$this->gridCariFaktur->CurrentPageIndex = $this->getViewState('offsetEdit') - 1;
			$this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;		
			
			$this->clearViewState('offsetEdit');
		}
		else
		{
			 $this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;
		}
		*/
		
        $this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			//$sql = "SELECT * FROM tbt_obat_masuk WHERE no_trans = '$noTrans' AND st_keuangan='0'";	
			//$sql = "SELECT * FROM tbt_obat_masuk WHERE st_keuangan='0'";	
			
			$sql="SELECT
					  view_asuransi.tipe_rawat,
					  view_asuransi.nama,
					  view_asuransi.cm,
					  view_asuransi.no_trans,
					  view_asuransi.tgl_visit,
					  view_asuransi.penjamin,
					  view_asuransi.perus_asuransi,
					  view_asuransi.st_asuransi,
					  view_asuransi.st_asuransi_byr,
					  view_asuransi.nm_perus,
					  view_asuransi.nm_penjamin,
					  if(view_asuransi.total_tdk <> 'Null',view_asuransi.total_tdk,'0') AS total_tdk,
					  if(view_asuransi.total_apotik <> 'Null',view_asuransi.total_apotik,'0') AS total_apotik,
					  if(view_asuransi.total_lab <> 'Null',view_asuransi.total_lab,'0') AS total_lab,
					  if(view_asuransi.total_rad <> 'Null',view_asuransi.total_rad,'0') AS total_rad,
					  if(view_asuransi.total_fisio <> 'Null',view_asuransi.total_fisio,'0') AS total_fisio,
					  SUM(if(view_asuransi.total_tdk <> 'Null',view_asuransi.total_tdk,'0')
					  +if(view_asuransi.total_apotik <> 'Null',view_asuransi.total_apotik,'0')
					  +if(view_asuransi.total_lab <> 'Null',view_asuransi.total_lab,'0')
					  +if(view_asuransi.total_rad <> 'Null',view_asuransi.total_rad,'0')
					  +if(view_asuransi.total_fisio <> 'Null',view_asuransi.total_fisio,'0') ) AS jml
					FROM
					  view_asuransi
				    WHERE 
					  view_asuransi.flag = '1' 
					  AND view_asuransi.st_asuransi_byr = '0' 
					  AND (view_asuransi.penjamin = '02' OR view_asuransi.penjamin = '03')
				  ";
					 
			if($this->DDUrut->SelectedValue != '')
			{
				$DDUrut = $this->DDUrut->SelectedValue;
				$sql .= "AND view_asuransi.penjamin = '$DDUrut' ";
			}
			
			if($this->DDKelompoKateg->SelectedValue != '')
			{
				$DDKelompoKateg = $this->DDKelompoKateg->SelectedValue;
				$sql .= "AND view_asuransi.penjamin_kategori = '$DDKelompoKateg' ";
			}
			
			if($this->DDanggota->SelectedValue != '')
			{
				$DDanggota = $this->DDanggota->SelectedValue;
				$sql .= "AND view_asuransi.sub_penjamin_kategori = '$DDanggota' ";
			}
			
			if($this->DDKontrak->SelectedValue != '')
			{
				$DDKontrak = $this->DDKontrak->SelectedValue;
				$sql .= "AND view_asuransi.perus_asuransi = '$DDKontrak' ";
			}
			
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND view_asuransi.tgl_visit = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND view_asuransi.tgl_visit BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (view_asuransi.tgl_visit)='$cariBln' AND YEAR(view_asuransi.tgl_visit)='$cariThn' ";
				
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}
				else
				{
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				}		
			}
			else
			{
				$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
			}
			
			$sql .= " GROUP BY view_asuransi.no_trans ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridCariFaktur->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->gridCariFaktur->DataSource = $data;
            $this->gridCariFaktur->DataBind();
	    	
			//$this->msg->Text=$sql;
			/*
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
			*/
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridCariFaktur->DataSource = $session["SomeData"];
            $this->gridCariFaktur->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridCariFaktur_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->gridCariFaktur->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridCariFaktur();
    }

    protected function gridCariFaktur_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
		
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridCariFaktur->CurrentPageIndex = 0;
        $this->bindGridCariFaktur();

    }	
	
	protected function gridCariFaktur_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			//$noTrans = $item->DataItem['no_trans'];
			
			$item->fakturColumn1->Text = $item->DataItem['cm'];
			$item->fakturColumn2->Text = $item->DataItem['no_trans'];
			$item->fakturColumn3->Text = $item->DataItem['nama'];
			$item->fakturColumn4->Text = $item->DataItem['nm_penjamin'];
			$item->fakturColumn5->Text = $item->DataItem['nm_perus'];
			$item->fakturColumn6->Text = number_format($item->DataItem['jml'],'0',',','.');
			
			if($this->jnsPas->SelectedValue == '0') //Rawat Jalan
			{
				$item->RincianColumn->rincianBtn->Attributes->onclick = 'popup(\'index.php?page=Tarif.cetakRincianClaimAsuransiJln&cm='.$item->DataItem['cm'].'&notrans='.$item->DataItem['no_trans'].'&jmlTagihan='.$item->DataItem['jml'].'&jnsPasien='.$item->DataItem['tipe_rawat'].'\',\'tes\')';
			}
			else
			{
				$item->RincianColumn->rincianBtn->Attributes->onclick = 'popup(\'index.php?page=Tarif.cetakRincianClaimAsuransiJln&cm='.$item->DataItem['cm'].'&notrans='.$item->DataItem['no_trans'].'&jmlTagihan='.$item->DataItem['jml'].'&jnsPasien='.$item->DataItem['tipe_rawat'].'\',\'tes\')';
			}
			
        } 
    }
	
	protected function gridCariFaktur_EditCommand($sender,$param)
    { 
		$this->gridCariFaktur->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridCariFaktur();
    }
	
	protected function gridCariFaktur_CancelCommand($sender,$param)
    { 
		$this->gridCariFaktur->EditItemIndex=-1;
        $this->bindGridCariFaktur();
    }
	
	protected function gridCariFaktur_UpdateCommand($sender,$param)
    {
        $item = $param->Item; 
		$this->gridCariFaktur->EditItemIndex=-1;
        $this->bindGridCariFaktur();
    }
	
	public function noTrans($sender,$param)
	{
		$ID = $param->CommandParameter;
	}
	
	public function gridCariFaktur_itemCommand($sender,$param)
	{
		if($param->CommandParameter != 'preview')
		{
			$item=$param->Item;
			
			if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
			{
				$ID = $this->gridCariFaktur->DataKeys[$item->ItemIndex];
				//$noTrans = ObatMasukRecord::finder()->findByPk($ID)->no_trans;
				//$modeBayar = $item->fakturColumn6->modeBayar->SelectedValue;
				
				//$this->msg->Text = $ID;
				
				$this->makeTblTemp($ID);
			}
			
			$this->pageSize = $this->gridCariFaktur->PageSize;
			$this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;		
			
			$this->bindGridCariFaktur();
		}
	}
	
	public function ajukanClicked($sender,$param)
	{		
		$ID = $param->CommandParameter;
		$this->makeTblTemp($ID);
		
		$this->pageSize = $this->gridCariFaktur->PageSize;
		$this->offset = $this->pageSize * $this->gridCariFaktur->CurrentPageIndex;		
		
		$this->bindGridCariFaktur();
	}
	
	public function pengajuanBtnClicked($sender,$param)
	{	
		//$page = $this->gridCariFaktur->CurrentPageIndex+1;
		//$item = $param->Item;
		//$this->datagrid->EditItemIndex=$item->ItemIndex;
		//$this->datagrid->DataSource=$this->tariff->getAll($page);
		//$this->datagrid->dataBind();
		
		//$item = $this->gridCariFaktur->Item;
		
		$no_trans = $param->CommandParameter;
		
		
		
		foreach ($this->gridCariFaktur->Items as $i) {	
			$id = $i->ItemIndex;
			
			if($id == '0')
			{
				$ck = $i->fakturColumn6->modeBayar->SelectedValue;
				//$this->msg->Text = 	$ck; 	
			}
			
			
			
			//$this->msg->Text = 	$i->ItemIndex; 	
			//$ch = $this->getViewState($this->pageName . ".checkBoxes");
			//$ch[$id] = $ck;
			//$this->setViewState($this->pageName . ".checkBoxes", $ch);
			//$this->msg->Text = 	$this->gridCariFaktur->EditItem->modeBayar->SelectedValue; 
			
		}
		
		
		//$this->msg->Text = 	$no_trans; 	
		
		//$this->gridCariFaktur->modeBayar->SelectedValue;
		
		//$this->makeTblTemp($noTrans);
		
	}
	
	
	private function bindGridBrg()
    {
        $this->pageSize = $this->gridBrg->PageSize;
        $this->offset = $this->pageSize * $this->gridBrg->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
			$nmTable = $this->getViewState('nmTable');
			
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$sql="SELECT * FROM $nmTable ";
			
			$this->setViewState('sql',$sql);
            $data = $someDataList->getSomeDataPage($sql);
            $this->gridBrg->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->msg->Text=$someDataList->getSomeDataCount($sql).' Barang';    			
            $this->gridBrg->DataSource = $data;
            $this->gridBrg->DataBind();
	    	//$this->showSql->Text=$sql;
			/*
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
			*/
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->gridBrg->DataSource = $session["SomeData"];
            $this->gridBrg->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function gridBrg_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); 
		$this->gridBrg->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGridBrg();
    }

    protected function gridBrg_SortCommand($sender,$param)
    {
        if ($this->SortExpression !== $param->SortExpression)
        {
            $this->SortExpression = $param->SortExpression;
            $this->SortDirection = "ASC";
        }
        else {
            if ($this->SortDirection === "ASC")
                $this->SortDirection = "DESC";
            else
                $this->SortDirection = "ASC";
        }
		
        $session = $this->getSession();
        $session->remove("SomeData"); $this->gridBrg->CurrentPageIndex = 0;
        $this->bindGridBrg();

    }	
	
	protected function gridBrg_ItemCreated($sender,$param)
    {
        $item=$param->Item;	
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$item->column1->Text = $item->DataItem['cm'];
			$item->column2->Text = $item->DataItem['no_trans'];
			$item->column3->Text = $item->DataItem['nama'];
			$item->column4->Text = $item->DataItem['nm_penjamin'];
			$item->column5->Text = $item->DataItem['nm_perus'];
			$item->column6->Text = number_format($item->DataItem['jml'],'0',',','.');
			
        } 
    }
	
	protected function gridBrg_EditCommand($sender,$param)
    { $this->gridBrg->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_CancelCommand($sender,$param)
    { 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	protected function gridBrg_UpdateCommand($sender,$param)
    {
        $item = $param->Item; 
		$this->gridBrg->EditItemIndex=-1;
        $this->bindGridBrg();
    }
	
	public function gridEditBrgClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
	}
	
	public function gridHapusBrgClicked($sender,$param)
	{
		$no_trans = $param->CommandParameter;
		
		$nmTable = $this->getViewState('nmTable');
				
		$sql = "DELETE FROM $nmTable WHERE no_trans='$no_trans'";
		$this->queryAction($sql,'C');	
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr)==0)
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}
		
		
	}
	
	public function cetakClicked($sender,$param)
    {
		$nmTable = $this->getViewState('nmTable');
		$notrans = $this->numCounter('tbt_claim_asuransi',ClaimAsuransiRecord::finder(),'32');
		
		$sql = "SELECT * FROM $nmTable ORDER BY id";	
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$no_trans_rawat = $row['no_trans'];
			
			$data= new ClaimAsuransiRecord();			
			$data->no_trans = $notrans;
			$data->no_trans_rawat = $row['no_trans'];
			$data->cm = $row['cm'];
			$data->tipe_rawat = $row['tipe_rawat'];
			$data->penjamin = $row['penjamin'];
			$data->perus_asuransi = $row['perus_asuransi'];
			$data->total = $row['jml'];
			$data->tgl = date('Y-m-d');
			$data->wkt = date('G:i:s');
			$data->petugas = $this->User->IsUserNip;
			$data->Save();
			
			//Update st_asuransi_byr
			if($row['tipe_rawat'] == '0') //Rawat Jalan
			{
				$sql="UPDATE tbt_rawat_jalan SET st_asuransi_byr = '1' WHERE no_trans='$no_trans_rawat' ";
				$this->queryAction($sql,'C');
			}
		}
			
		$this->queryAction($nmTable,'D');
		$this->clearViewState('nmTable');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapClaimAsuransiDraf',array('noTrans'=>$notrans,'periode'=>$this->txtPeriode->Text)));
	}
	
	public function batalClicked($sender,$param)
	{
		$this->prosesUnlock();
	}
	
	public function keluarClicked($sender,$param)
	{	
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');
		}	
			
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	}
?>
