<?php
class MasterStokAset extends SimakConf
{   	
	private $sortExp = "nm_barang";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	

   	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('12');
		$tmpVar2=$this->authApp('7');
		if($tmpVar == "False" && $tmpVar2 == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDTujuan->DataSource=DestinasiAsetRecord::finder()->findAll($criteria);
			$this->DDTujuan->dataBind();
			
			$sql = "SELECT id,nama FROM tbm_barang_kelompok ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			
			/*$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolBarangRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiBarangRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatBarangRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfBarangRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenBarangRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanBarangRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberBarangRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberBarangRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();			
			*/
			$this->bindGrid();									
			$this->DDTujuan->focus();						
		}else{
			$this->DDTujuan->focus();
		}
		
	}		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDJenis->SelectedValue=='')
		{
			
			$gol = $this->DDJenis->SelectedValue;	
			$sql = "SELECT id,nama FROM tbm_barang_kelompok WHERE id_jenis_barang = '$gol' ORDER BY nama )";
			$this->DDKelompok->DataSource = BarangKelompokRecord::finder()->findAllBySql($sql);
			$this->DDKelompok->dataBind(); 	
			//$this->DDKelompok->Enabled=true;
		}
		else
		{
			$this->DDKelompok->DataSource=BarangKelompokRecord::finder()->findAll();
			$this->DDKelompok->dataBind();	
			//$this->DDKelompok->Enabled=false;
		}
		
		$this->cariClicked();
		$this->DDKelompok->SelectedValue = 'empty';
	}
	
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeBarang->Enabled=true;
		}
		else
		{
			$this->RBtipeBarang->Enabled=false;
		}	
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		$this->cariClicked();
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeBarang);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();	
		$this->cariClicked();
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatBarangRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatBarangRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		$this->cariClicked();
	}
	
	public function DDTujuanChanged($sender,$param)
	{
		$this->cariClicked();
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
    
	public function bindGrid()
    {
        $this->pageSize = $this->dtgSomeData->PageSize;
        $this->offset = $this->pageSize * $this->dtgSomeData->CurrentPageIndex;
        
        $session = $this->getSession();
        
        if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			if($this->groupingIdharga->Checked === true)
			{
				if($this->DDTujuan->SelectedValue=='')
					$this->DDTujuan->SelectedIndex = '0';
			}	
			
			$tujuan = $this->DDTujuan->SelectedValue;
			$thnNow = date('Y');
			
			$sql = "select 
						tbm_barang.id AS id,
						tbm_barang.nama AS nm_barang,
						tbt_aset_harga.hrg_netto AS hrg_netto,
						tbt_aset_harga.hrg_ppn AS hrg,
						tbt_aset_harga.id AS id_harga,
						SUM(tbt_stok_aset.jumlah) AS jumlah,
						tbt_stok_aset.sumber AS sumber,
						tbt_stok_aset.tujuan AS tujuan,
						tbt_stok_aset.expired AS expired,
						tbt_stok_aset.thn_pengadaan AS thn_pengadaan,
						tbt_stok_aset.depresiasi AS depresiasi,
						tbm_barang.id_jenis_barang AS id_jenis_barang,
						tbm_barang.id_kelompok_barang AS id_kelompok_barang,
						tbm_barang_kelompok.nama AS nm_kelompok,
						tbm_destinasi_aset.nama AS nm_tujuan,
						(('$thnNow' - tbt_stok_aset.thn_pengadaan) * (tbt_aset_harga.hrg_netto * tbt_stok_aset.depresiasi / 100)) AS hrg_depresiasi
					  from
					  	tbm_barang
						INNER JOIN tbt_stok_aset ON (tbm_barang.id = tbt_stok_aset.id_barang)
						INNER JOIN tbt_aset_harga ON (tbt_stok_aset.id_harga = tbt_aset_harga.id)
						INNER JOIN tbm_barang_kelompok ON (tbm_barang_kelompok.id = tbm_barang.id_kelompok_barang)
						INNER JOIN tbm_destinasi_aset ON (tbt_stok_aset.tujuan = tbm_destinasi_aset.id)
					WHERE
						tbt_stok_aset.jumlah > 0 ";				
					
					
			if($tujuan <> '')
			{
				$sql .= "AND tbt_stok_aset.tujuan = '$tujuan' ";
			}
								
			if($this->DDJenis->SelectedValue != '')	
			{				
				$DDJenis = $this->DDJenis->SelectedValue;	
				$sql .= " AND tbm_barang.id_jenis_barang = '$DDJenis' ";	
			}
			
			if($this->DDKelompok->SelectedValue != '')	
			{				
				$DDKelompok = $this->DDKelompok->SelectedValue;	
				$sql .= " AND tbm_barang.id_kelompok_barang = '$DDKelompok' ";	
			}
			
			if($this->DDStandard->SelectedValue != '')	
			{				
				$DDStandard = $this->DDStandard->SelectedValue;	
				$sql .= " AND tbm_barang.standardisasi = '$DDStandard' ";	
			}
							
			if($this->cariNama->Text != '')	
			{			
				$cariByNama = $this->cariNama->Text;
				if($this->Advance->Checked=== true)
				{
					$sql .= "AND tbm_barang.nama LIKE '%$cariByNama%' ";
				}else{
					$sql .= "AND tbm_barang.nama LIKE '$cariByNama%' ";
				}
			}
				
			//$sql .= " GROUP BY tbt_stok_aset.id_harga ";			
			
			//if($this->groupingIdharga->Checked === true)
				$sql .= " GROUP BY tbm_barang.id";
			//else
				//$sql .= " GROUP BY tbm_barang.id ";	
			
			if($tujuan <> '')
				$sql .= ",tbt_stok_aset.thn_pengadaan,tbt_stok_aset.tujuan,tbt_stok_aset.id_harga, tbt_stok_aset.depresiasi ";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
			//$this->showSql->Visible=true;
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
			
			if($someDataList->getSomeDataCount($sql) == '0')
			{
				$this->cetakBtn->Enabled = false;
			}
			else
			{
				$this->cetakBtn->Enabled = true;
			}
			
			
			if ($this->getViewState('sql')) 				
			{
				$this->clearViewState('sql');
			}
			
			$this->setViewState('sql',$sql);
			
        }
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->dtgSomeData->DataSource = $session["SomeData"];
            $this->dtgSomeData->DataBind();
        }
    }
	
	
    // ---------- datagrid page and sort events ---------------
    
     protected function dtgSomeData_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function dtgSomeData_SortCommand($sender,$param)
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
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function itemCreated($sender,$param)
    {
        $item=$param->Item;
		if($item->ItemType==='EditItem')
        {
            // set column width of textboxes
           $item->jml->TextBox->Columns=10;
		  // $item->hargaNett->TextBox->Columns=5;	
		  // $item->harga->TextBox->Columns=5;	
		   
		   /*$item->gudang->TextBox->Columns=3;
		   $item->icu->TextBox->Columns=3;
		   $item->ugd->TextBox->Columns=3;
		   $item->ok->TextBox->Columns=3;
		   $item->vk->TextBox->Columns=3;
		   $item->anak->TextBox->Columns=3;
		   $item->poli5->TextBox->Columns=3;
		   $item->kandungan->TextBox->Columns=3;
		   $item->bayi->TextBox->Columns=3;
		   $item->fisio->TextBox->Columns=3;
		   $item->nurse_a->TextBox->Columns=3;
		   $item->nurse_b->TextBox->Columns=3;
		   $item->apotik->TextBox->Columns=3;
		   
		   if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
		   {
			   	$item->expired->expAwal->Value = $this->convertDate($item->DataItem['expired'],'1');
				$item->expired->txtExpEdit->Text = $this->convertDate($item->DataItem['expired'],'1');
		   }
			else
			{
				$item->expired->expAwal->Value = '';
				$item->expired->txtExpEdit->Text = '';			
			}*/
			
			$item->hargaNett->Text = number_format($item->DataItem['hrg_netto'],'2',',','.');
			$item->hargaDepresiasi->Text = number_format($item->DataItem['hrg_depresiasi'],'2',',','.');
			$item->thn_pengadaan->Text = $item->DataItem['thn_pengadaan'];
			$item->depresiasi->Text = $item->DataItem['depresiasi'];
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{		
			$idBarang = $item->DataItem['kode'];
			$hrgPpn = $item->DataItem['hrg'];
			
			$item->hargaNett->Text = number_format($item->DataItem['hrg_netto'],'2',',','.');
			$item->hargaDepresiasi->Text = number_format($item->DataItem['hrg_depresiasi'],'2',',','.');
			$item->thn_pengadaan->Text = $item->DataItem['thn_pengadaan'];
			$item->depresiasi->Text = $item->DataItem['depresiasi'];
			
			/*if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
				$item->expired->txtExp->Text = $this->convertDate($item->DataItem['expired'],'1');
			else
				$item->expired->txtExp->Text = '';	
			*/		
			
			//$idKelompokMargin = BarangRecord::finder()->findByPk($idBarang)->kel_margin;
			
			//$persenMarginAsuransi = BarangKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_asuransi / 100;
			//$persenMarginJamper = BarangKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_jamper / 100;
			//$persenMarginUmum = BarangKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase / 100;
				
			//$item->hargaJualUmum->Text = $hrgPpn * (1 + $persenMarginUmum);
			//$item->hargaJualAsuransi->Text = $hrgPpn * (1 + $persenMarginAsuransi);
			//$item->hargaJualJamper->Text = $hrgPpn * (1 + $persenMarginJamper);
			
			//$item->expired->Text = $item->DataItem['expired'];
			
			if($this->groupingIdharga->Checked === true)
			{
				if($this->DDTujuan->SelectedValue=='')
					$this->DDTujuan->SelectedIndex = '0';
					
				//$this->dtgSomeData->Columns[21]->Visible = true;
				//$this->dtgSomeData->Columns[22]->Visible = true;
			}	
			else
			{
				//$this->dtgSomeData->Columns[21]->Visible = false;
				//$this->dtgSomeData->Columns[22]->Visible = false;
			}
					
			if($this->DDTujuan->SelectedValue <> '' )
			{
				$this->dtgSomeData->Columns[2]->Visible = true;
				$this->dtgSomeData->Columns[3]->Visible = true;
				$this->dtgSomeData->Columns[4]->Visible = true;
				$this->dtgSomeData->Columns[5]->Visible = true;
				$this->dtgSomeData->Columns[6]->Visible = true;
				$this->dtgSomeData->Columns[8]->Visible = true;
				$this->dtgSomeData->Columns[9]->Visible = true;
				/*$this->dtgSomeData->Columns[9]->Visible = false;
				$this->dtgSomeData->Columns[10]->Visible = false;
				$this->dtgSomeData->Columns[11]->Visible = false;
				$this->dtgSomeData->Columns[12]->Visible = false;
				$this->dtgSomeData->Columns[13]->Visible = false;
				$this->dtgSomeData->Columns[14]->Visible = false;
				$this->dtgSomeData->Columns[15]->Visible = false;
				$this->dtgSomeData->Columns[16]->Visible = false;
				$this->dtgSomeData->Columns[17]->Visible = false;
				$this->dtgSomeData->Columns[18]->Visible = false;
				$this->dtgSomeData->Columns[19]->Visible = false;
				$this->dtgSomeData->Columns[20]->Visible = false;
				$this->dtgSomeData->Columns[21]->Visible = false;
				//$this->dtgSomeData->Columns[22]->Visible = false;
				//$this->dtgSomeData->Columns[21]->Visible = false;
				
				$item->gudang->Enabled=false;
				$item->icu->Enabled=false;
				$item->ugd->Enabled=false;
				$item->ok->Enabled=false;
				$item->vk->Enabled=false;
				$item->anak->Enabled=false;
				$item->kandungan->Enabled=false;
				$item->bayi->Enabled=false;
				$item->poli5->Enabled=false;
				$item->fisio->Enabled=false;
				$item->nurse_a->Enabled=false;
				$item->nurse_b->Enabled=false;
				$item->apotik->Enabled=false;
				$item->poli5->Enabled=false;
				
				if($this->DDTujuan->SelectedValue == '2')//gudang
					$this->dtgSomeData->Columns[9]->Visible = true;
					$item->gudang->Enabled=true;	
				
				if($this->DDTujuan->SelectedValue == '14')
					$this->dtgSomeData->Columns[10]->Visible = true;
					$item->icu->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '9')
					$this->dtgSomeData->Columns[11]->Visible = true;
					$item->ugd->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '11')
					$this->dtgSomeData->Columns[12]->Visible = true;
					$item->ok->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '4')
					$this->dtgSomeData->Columns[13]->Visible = true;
					$item->vk->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '8')
					$this->dtgSomeData->Columns[14]->Visible = true;
					$item->anak->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '3')
					$this->dtgSomeData->Columns[15]->Visible = true;
					$item->kandungan->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '5')
					$this->dtgSomeData->Columns[16]->Visible = true;
					$item->bayi->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '6')
					$this->dtgSomeData->Columns[17]->Visible = true;
					$item->fisio->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '7')
					$this->dtgSomeData->Columns[18]->Visible = true;
					$item->nurse_a->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '10')
					$this->dtgSomeData->Columns[19]->Visible = true;
					$item->nurse_b->Enabled=true;
				
				if($this->DDTujuan->SelectedValue == '12')
					$this->dtgSomeData->Columns[20]->Visible = true;
					$item->apotik->Enabled=true;
				if($this->DDTujuan->SelectedValue == '13')
					$this->dtgSomeData->Columns[21]->Visible = true;
					$item->apotik->Enabled=true;
				*/
			}
			else
			{
				$this->dtgSomeData->Columns[2]->Visible = false;
				$this->dtgSomeData->Columns[3]->Visible = false;
				$this->dtgSomeData->Columns[4]->Visible = false;
				$this->dtgSomeData->Columns[5]->Visible = false;
				$this->dtgSomeData->Columns[6]->Visible = false;
				$this->dtgSomeData->Columns[8]->Visible = false;
				$this->dtgSomeData->Columns[9]->Visible = false;
				/*$this->dtgSomeData->Columns[9]->Visible = true;
				$this->dtgSomeData->Columns[10]->Visible = true;
				$this->dtgSomeData->Columns[11]->Visible = true;
				$this->dtgSomeData->Columns[12]->Visible = true;
				$this->dtgSomeData->Columns[13]->Visible = true;
				$this->dtgSomeData->Columns[14]->Visible = true;
				$this->dtgSomeData->Columns[15]->Visible = true;
				$this->dtgSomeData->Columns[16]->Visible = true;
				$this->dtgSomeData->Columns[17]->Visible = true;
				$this->dtgSomeData->Columns[18]->Visible = true;
				$this->dtgSomeData->Columns[19]->Visible = true;
				$this->dtgSomeData->Columns[20]->Visible = true;
				$this->dtgSomeData->Columns[21]->Visible = true;
				//$this->dtgSomeData->Columns[21]->Visible = true;
				
				$item->gudang->Enabled=true;
				$item->icu->Enabled=true;
				$item->ugd->Enabled=true;
				$item->ok->Enabled=true;
				$item->vk->Enabled=true;
				$item->anak->Enabled=true;
				$item->kandungan->Enabled=true;
				$item->bayi->Enabled=true;
				$item->fisio->Enabled=true;
				$item->nurse_a->Enabled=true;
				$item->nurse_b->Enabled=true;
				$item->apotik->Enabled=true;
				$item->poli5->Enabled=true;
				*/
			}
		}
    }
	
	public function editItem($sender,$param)
    {
		if ($this->User->IsAdmin)
		{
			$this->dtgSomeData->EditItemIndex=$param->Item->ItemIndex;
			$this->bindGrid(); 
		}	
    }
	
	public function cancelItem($sender,$param)
    {        
		$this->dtgSomeData->EditItemIndex=-1;  
		$this->bindGrid();
    }
	
	public function saveItem($sender,$param)
    {
        $item=$param->Item;
			
		//$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
		//BarangRecord::finder()->FindByPk($ID);
		$idHarga = $this->dtgSomeData->DataKeys[$item->ItemIndex];		
		$idBarang = HrgAsetRecord::finder()->findByPk($idHarga)->kode;
		
		//$expiredAwal = $this->convertDate($item->expired->expAwal->Value,'2');	
		//$expired = $this->convertDate($item->findControl('txtExpEdit')->Text,'2');
		
		//$this->showSql->Text = $expired;
		
		//$sql = "SELECT expired FROM tbt_stok_aset WHERE id_barang='$idBarang' AND id_harga='$idHarga'";
		//$this->showSql->Text = StokLainRecord::finder()->findBySql($sql)->expired;
		
		//$sumberBarang=$item->sumberMaster->TextBox->Text;
		$sumberBarang = $this->getViewState('sumber');
		/*
		$update=StokGudangRecord::finder()->find('id_barang = ? AND sumber = ?', $idBarang, $sumberBarang);
		
		$update->jumlah=$item->jml->TextBox->Text; 
		$update->save();
		*/
		$tujuan = $this->DDTujuan->SelectedValue;
		$jumlah = $item->jml->TextBox->Text;
		$thn_pengadaan = $item->thn_pengadaan->Text;
		$depresiasi = $item->depresiasi->Text;
		
		if(intval($jumlah) || $jumlah == 0)
		{
			//$harga = $item->hargaNett->TextBox->Text;
		
			if($harga > 0)
			{
				$hrgNett = $harga/1.1;
			}
			else
			{
				$hrgNett = '0';
			}
			
			//$sql="UPDATE tbt_aset_harga SET hrg_ppn='$harga', hrg_netto='$hrgNett' WHERE id='$idHarga' AND sumber='01'";
			//$sql="UPDATE tbt_aset_harga SET hrg_ppn='$hrgNett', hrg_netto='$hrgNett' WHERE kode='$idBarang' AND sumber='01'";
			//$this->queryAction($sql,'C');	//$this->showSql->Text = $harga; 		
			
			/*
			if($this->getViewState('tujuan')=='1')
			{
				$sql="UPDATE tbt_stok_gudang SET jumlah='$jumlah' WHERE id_barang='$idBarang'";
			}
			else
			{
			*/
				//if($item->findControl('txtExp')->Text != '' && $item->findControl('txtExp')->Text != '0000-00-00' )	
				/*if($expired != '' && $expired != '0000-00-00' )	
				{
					if($item->expired->expAwal->Value != '' && $item->expired->expAwal->Value != '0000-00-00' )		
						$sql="UPDATE tbt_stok_aset SET jumlah='$jumlah', expired='$expired' WHERE id_harga='$idHarga' AND id_barang='$idBarang' AND expired='$expiredAwal' AND  tujuan = '$tujuan'";
					else
						$sql="UPDATE tbt_stok_aset SET jumlah='$jumlah', expired='$expired' WHERE id_harga='$idHarga' AND id_barang='$idBarang' AND (expired='0000-00-00' OR expired IS NULL) AND  tujuan = '$tujuan'";	
				}
				else
				{
					if($item->expired->expAwal->Value != '' && $item->expired->expAwal->Value != '0000-00-00' )		
						$sql="UPDATE tbt_stok_aset SET jumlah='$jumlah' WHERE id_harga='$idHarga' AND id_barang='$idBarang' AND expired='$expiredAwal' AND  tujuan = '$tujuan'";
					else
						$sql="UPDATE tbt_stok_aset SET jumlah='$jumlah' WHERE id_harga='$idHarga' AND id_barang='$idBarang' AND (expired='0000-00-00' OR expired IS NULL) AND  tujuan = '$tujuan'";
					
				}*/
				//$sql="UPDATE tbt_stok_aset SET jumlah='$jumlah' WHERE id_barang='$idBarang' AND tujuan = '$tujuan'";
				
				$sql="UPDATE 
						tbt_stok_aset 
					SET 
						jumlah='$jumlah' 
					WHERE 
						id_harga='$idHarga' 
						AND id_barang='$idBarang' 
						AND tujuan = '$tujuan'
						AND thn_pengadaan = '$thn_pengadaan'
						AND depresiasi = '$depresiasi' ";
						
				$this->queryAction($sql,'C');
				//$this->showSql->Text = $sql;
			//}
			//$this->showSql->Text = $sql;
			
			
		  //  $this->update(
		   //     $this->UserGrid->DataKeys[$item->ItemIndex],    
		   //     $item->jml->TextBox->Text
		   //     );
			$this->dtgSomeData->EditItemIndex=-1;  
			
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','jQuery.WsGrowl.show({title: \'\', content:\'<p class"msg_warning">Jumlah belum di isi !</p>\',timeout: 3000,dialog:{modal: true}});');	
		}
		
		$this->bindGrid();	
    }
	
    public function deleteButtonClicked($sender,$param)

    {
        if ($this->User->IsAdmin)
		{
			// obtains the datagrid item that contains the clicked delete button
			$item=$param->Item;
			// obtains the primary key corresponding to the datagrid item
			$ID=$this->dtgSomeData->DataKeys[$item->ItemIndex];
			// deletes the user record with the specified username primary key
			
			BarangRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterBarang'));
			
		}	
    }	

		
	public function sortGrid($sender,$param)
	{		
		$orderBy = $param->SortExpression;
		$this->setViewState('orderBy',$orderBy);	
		$this->bindGrid();
		
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterBarangBaru'));		
	}
	
	public function cariClicked()
	{	
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
		$orderBy=$this->getViewState('orderBy');	
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDGol->SelectedValue) {
			$this->setViewState('cariByGol', $this->DDGol->SelectedValue);
		}else{
			$this->clearViewState('cariByGol');	
		}
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}
		
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}		
		
		$this->bindGrid();
	}
		
	public function DDPbfChanged($sender,$param)
	{			
		$session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
			
		$orderBy=$this->getViewState('orderBy');
		
		if($this->cariNama->Text){
			$this->setViewState('cariByNama', $this->cariNama->Text);
		}else{
			$this->clearViewState('cariByNama');	
		}	
		if($this->ID->Text){ 
			$this->setViewState('cariByID', $this->ID->Text);	
		}else{
			$this->clearViewState('cariByID');	
		}	
		
		if($this->Advance->Checked) {
			$this->setViewState('elemenBy',$this->Advance->Checked);
		}else{
			$this->clearViewState('elemenBy');	
		}
		
		if($this->DDGol->SelectedValue) {
			$this->setViewState('cariByGol', $this->DDGol->SelectedValue);
		}else{
			$this->clearViewState('cariByGol');	
		}
		
		if($this->DDKlas->SelectedValue) {
			$this->setViewState('cariByKlas', $this->DDKlas->SelectedValue);
		}else{
			$this->clearViewState('cariByKlas');	
		}
		
		if($this->DDDerivat->SelectedValue) {
			$this->setViewState('cariByDerivat', $this->DDDerivat->SelectedValue);
		}else{
			$this->clearViewState('cariByDerivat');	
		}
		
		if($this->DDPbf->SelectedValue) {
			$this->setViewState('cariByPbf', $this->DDPbf->SelectedValue);
		}else{
			$this->clearViewState('cariByPbf');	
		}
		
		if($this->DDProd->SelectedValue) {
			$this->setViewState('cariByProd', $this->DDProd->SelectedValue);
		}else{
			$this->clearViewState('cariByProd');	
		}
		
		if($this->DDSat->SelectedValue) {
			$this->setViewState('cariBySat', $this->DDSat->SelectedValue);
		}else{
			$this->clearViewState('cariBySat');	
		}
		
		if($this->getViewState('sumber')){
			$sumber = $this->getViewState('sumber');
		}else{
			$this->clearViewState('sumber');
		}	
					
		if($this->DDTujuan->SelectedValue) {
			$this->setViewState('tujuan',$this->DDTujuan->SelectedValue);
		}else{
			$this->clearViewState('tujuan');	
		}
		
		
	}
		
	public function DDSumMasterChanged($sender,$param)
	{				
		if($this->DDSumMaster->SelectedValue == '' || $this->DDSumMaster->SelectedValue =='01'){
			//$this->DDSumSekunder->DataSource=SubSumberBarangRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberBarangRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=true;		
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
			
		}
	}
	
	public function DDSumSekunderChanged($sender,$param)
	{		
		if($this->getViewState('sumber'))
		{
			$sumber = substr($this->getViewState('sumber'),0,2);				
			$sumber .=	$this->DDSumSekunder->SelectedValue;	
			$this->setViewState('sumber',$sumber);		
		}	
	}
	
	protected function refreshMe()
	{
		$this->Reload();
	}
	
	public function cetakClicked($sender,$param)
	{
		$session=new THttpSession;
		$session->open();
		$session['cetakStokSql'] = $this->getViewState('sql');
		
		$tujuan = $this->DDTujuan->SelectedValue;
		$jnsBarang = $this->DDJenis->SelectedValue;
		$kelompokBarang = $this->DDKelompok->SelectedValue;
		
		//$tipe = $this->collectSelectionResult($this->RBtipeBarang);
		
		if($this->semuaStok->Checked === true){
			$tipeCetak = '1';
		}
		else
		{
			$tipeCetak = '0';
		}
				
		$this->Response->redirect($this->Service->constructUrl('Asset.cetakStokAset',array('tujuan'=>$tujuan,'jnsBarang'=>$jnsBarang,'kelompokBarang'=>$kelompokBarang,'tipeCetak'=>$tipeCetak)));
	}
	
}
?>
