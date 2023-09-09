<?php
class MasterStokGudang extends SimakConf
{   	
	private $sortExp = "nama";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;	

   	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('4');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	}
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack && !$this->IsCallBack)
        { 
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$this->DDTujuan->DataSource=DesFarmasiRecord::finder()->findAll($criteria);
			$this->DDTujuan->dataBind();
			
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll();
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll();
			$this->DDSat->dataBind();	
			
			$this->DDSumMaster->DataSource=SumberObatRecord::finder()->findAll();
			$this->DDSumMaster->dataBind();	
			
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll();
			$this->DDSumSekunder->dataBind();			
			
			$this->bindGrid();									
			$this->DDTujuan->focus();						
		}else{
			$this->DDTujuan->focus();
		}
		
	}		
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			$this->DDKlas->dataBind(); 	
			$this->DDKlas->Enabled=true;
		}
		else
		{
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			$this->DDKlas->Enabled=false;
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}
		$this->cariClicked();
		
	}
	
	public function DDJenisBrgChanged($sender,$param)
	{	
		if($this->DDJenisBrg->SelectedValue=='01')
		{			
			$this->RBtipeObat->Enabled=true;
		}
		else
		{
			$this->RBtipeObat->Enabled=false;
		}	
		$this->setViewState('kategori',$this->DDJenisBrg->SelectedValue);	
		$this->cariClicked();
		
	}
	
	public function chTipe()
	{
		$tmp=$this->collectSelectionResult($this->RBtipeObat);
		$this->setViewState('tipe',$tmp);
		$this->ID->focus();	
		$this->cariClicked();
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			$this->DDDerivat->dataBind(); 	
			$this->DDDerivat->Enabled=true;
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
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
			
			$cariNama=$this->getViewState('cariByNama');
			$cariID=$this->getViewState('cariByID');	
			$elemenBy=$this->getViewState('elemenBy');
			$cariGol=$this->getViewState('cariByGol');
			$cariKlas=$this->getViewState('cariByKlas');
			$cariDerivat=$this->getViewState('cariByDerivat');
			$cariPbf=$this->getViewState('cariByPbf');
			$cariProd=$this->getViewState('cariByProd');
			$cariSat=$this->getViewState('cariBySat');
			//$sumber=$this->getViewState('sumber');			
			
			if($this->groupingIdharga->Checked === true)
			{
				if($this->DDTujuan->SelectedValue=='')
					$this->DDTujuan->SelectedIndex = '0';
			}	
			
			$tujuan = $this->DDTujuan->SelectedValue;
			
			$tipe=$this->getViewState('tipe');
			$kategori=$this->getViewState('kategori');
			
			/*
            $sql = "SELECT tbm_obat.kode AS kode,
						   tbm_obat.nama AS nama,						   		  
						   tbm_obat.pbf AS pbf,						  
						   tbm_obat.satuan AS sat,
						   tbt_obat_harga.hrg_ppn AS hrg,
						   tbt_obat_harga.id AS id_harga,
						   sum(if((tbt_stok_lain.tujuan = '2'),1,0)) AS stok_gudang,
						   sum(if((tbt_stok_lain.tujuan = '4'),1,0)) AS stok_icu,
						    ";
			if($sumber <> '')
			{
				$sql .= "tbt_stok_lain.jumlah AS jumlah,
						   tbt_stok_lain.sumber AS sumber ";				
			}else{
				$sql .= "SUM(tbt_stok_lain.jumlah) AS jumlah,
						 tbt_stok_lain.sumber AS sumber ";
			}	
			
			$sql .=	" FROM tbm_obat
							INNER JOIN tbt_stok_lain ON (tbt_stok_lain.id_obat = tbm_obat.kode)
							INNER JOIN tbt_obat_harga ON (tbt_obat_harga.id = tbt_stok_lain.id_harga)
						WHERE	
							tbt_stok_lain.tujuan='$tujuan' ";
			*/
			
			$sql = "SELECT *, 
					  sum(if((view_stok_gudang.tujuan = '2'), view_stok_gudang.jumlah, 0)) AS stok_gudang,
					  sum(if((view_stok_gudang.tujuan = '3'), view_stok_gudang.jumlah, 0)) AS stok_poliklinik5,
					  sum(if((view_stok_gudang.tujuan = '4'), view_stok_gudang.jumlah, 0)) AS stok_poliklinik3,
					  sum(if((view_stok_gudang.tujuan = '5'), view_stok_gudang.jumlah, 0)) AS stok_ugd,
					  sum(if((view_stok_gudang.tujuan = '6'), view_stok_gudang.jumlah, 0)) AS stok_ok,
					  sum(if((view_stok_gudang.tujuan = '7'), view_stok_gudang.jumlah, 0)) AS stok_vk,
					  sum(if((view_stok_gudang.tujuan = '8'), view_stok_gudang.jumlah, 0)) AS stok_poliklinik4,
					  sum(if((view_stok_gudang.tujuan = '9'), view_stok_gudang.jumlah, 0)) AS stok_poliklinik1,
					  sum(if((view_stok_gudang.tujuan = '10'), view_stok_gudang.jumlah, 0)) AS stok_ruang_bayi,
					  sum(if((view_stok_gudang.tujuan = '11'), view_stok_gudang.jumlah, 0)) AS stok_poliklinik2,
					  sum(if((view_stok_gudang.tujuan = '12'), view_stok_gudang.jumlah, 0)) AS stok_nurse_a,
					  sum(if((view_stok_gudang.tujuan = '13'), view_stok_gudang.jumlah, 0)) AS stok_nurse_b,
					  sum(if((view_stok_gudang.tujuan = '14'), view_stok_gudang.jumlah, 0)) AS stok_apotik,
					  sum(if((view_stok_gudang.tujuan = '16'), view_stok_gudang.jumlah, 0)) AS stok_poli_4,
					  sum(view_stok_gudang.jumlah) AS jml_stok_total
					FROM
					  view_stok_gudang 
					WHERE
						view_stok_gudang.kode <> '' ";				
					
					
					if($tujuan <> '')
					{
						$sql .= "AND view_stok_gudang.tujuan = '$tujuan' ";
					}
						
			/*		   
			//$tujuan=$this->getViewState('tujuan');
			if ($this->getViewState('tujuan') == '1')
			{
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_gudang b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							c.kode=a.kode  ";
			}else{									   
				//$tujuan='2';
				$sql .=	" FROM tbm_obat a,							
							tbt_stok_lain b,
							tbt_obat_harga c
						WHERE	 							
							a.kode=b.id_obat AND
							b.id_harga=c.id AND 
							b.tujuan='$tujuan' 
							AND a.kode=c.kode ";				
			}
			*/
								
			if($cariNama <> '')			
				if($tipeCari === true){
						$sql .= "AND view_stok_gudang.nama LIKE '%$cariNama%' ";
					}
					else
					{	
						$sql .= "AND view_stok_gudang.nama LIKE '$cariNama%' ";
					}		
						
			if($cariID <> '')			
				$sql .= "AND view_stok_gudang.kode = '$cariID' ";
			
			if($tipe <> '')			
				$sql .= "AND view_stok_gudang.tipe = '$tipe' ";
			
			if($kategori <> '')			
				$sql .= "AND view_stok_gudang.kategori = '$kategori' ";		
						
			if($cariGol <> '')			
				$sql .= "AND view_stok_gudang.gol = '$cariGol' ";
			
			if($cariKlas <> '')					
				$sql .= "AND view_stok_gudang.klasifikasi = '$cariKlas' ";		
				
			if($cariDerivat <> '')	
			{		
				$sql .= "AND view_stok_gudang.derivat = '$cariDerivat' ";
				//$sql .= "AND a.derivat=d.id	";		
			}
			
			if($cariPbf <> '')					
				$sql .= "AND view_stok_gudang.pbf = '$cariPbf' ";				
			
			if($cariProd <> '')				
				$sql .= "AND view_stok_gudang.produsen = '$cariProd' ";				
			
			if($cariSat <> '')			
				$sql .= "AND view_stok_gudang.sat = '$cariSat' ";
				
			//$sql .= " GROUP BY tbt_stok_lain.id_harga ";			
			
			if($this->groupingIdharga->Checked === true)
				$sql .= " GROUP BY view_stok_gudang.id_harga, view_stok_gudang.expired, view_stok_gudang.kode ";
			else
				$sql .= " GROUP BY view_stok_gudang.kode ";	
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
			$this->showSql->Visible=true;
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
           //$item->jml->TextBox->Columns=3;
		   $item->hargaNett->TextBox->Columns=5;	
		   $item->harga->TextBox->Columns=5;	
		   
		   $item->gudang->TextBox->Columns=3;
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
			
			}
        }       
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem')
		{		
			$idObat = $item->DataItem['kode'];
			$hrgPpn = $item->DataItem['hrg'];
			
			if($item->DataItem['expired'] != '' && $item->DataItem['expired'] != '0000-00-00')
				$item->expired->txtExp->Text = $this->convertDate($item->DataItem['expired'],'1');
			else
				$item->expired->txtExp->Text = '';	
					
			
			$idKelompokMargin = ObatRecord::finder()->findByPk($idObat)->kel_margin;
			
			$persenMarginAsuransi = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_asuransi / 100;
			$persenMarginJamper = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase_jamper / 100;
			$persenMarginUmum = ObatKelompokMarginRecord::finder()->findByPk($idKelompokMargin)->persentase / 100;
				
			$item->hargaJualUmum->Text = $hrgPpn * (1 + $persenMarginUmum);
			$item->hargaJualAsuransi->Text = $hrgPpn * (1 + $persenMarginAsuransi);
			$item->hargaJualJamper->Text = $hrgPpn * (1 + $persenMarginJamper);
			
			//$item->expired->Text = $item->DataItem['expired'];
			
			if($this->groupingIdharga->Checked === true)
			{
				if($this->DDTujuan->SelectedValue=='')
					$this->DDTujuan->SelectedIndex = '0';
					
				$this->dtgSomeData->Columns[21]->Visible = true;
				$this->dtgSomeData->Columns[22]->Visible = true;
			}	
			else
			{
				$this->dtgSomeData->Columns[21]->Visible = false;
				$this->dtgSomeData->Columns[22]->Visible = false;
			}
					
			if($this->DDTujuan->SelectedValue <> '' )
			{
				$this->dtgSomeData->Columns[9]->Visible = false;
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
			}
			else
			{
				$this->dtgSomeData->Columns[9]->Visible = true;
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
		//ObatRecord::finder()->FindByPk($ID);
		$idHarga = $this->dtgSomeData->DataKeys[$item->ItemIndex];		
		$idObat = HrgObatRecord::finder()->findByPk($idHarga)->kode;
		
		$expiredAwal = $this->convertDate($item->expired->expAwal->Value,'2');	
		$expired = $this->convertDate($item->findControl('txtExpEdit')->Text,'2');
		
		//$this->showSql->Text = $expired;
		
		//$sql = "SELECT expired FROM tbt_stok_lain WHERE id_obat='$idObat' AND id_harga='$idHarga'";
		//$this->showSql->Text = StokLainRecord::finder()->findBySql($sql)->expired;
		
		//$sumberObat=$item->sumberMaster->TextBox->Text;
		$sumberObat = $this->getViewState('sumber');
		/*
		$update=StokGudangRecord::finder()->find('id_obat = ? AND sumber = ?', $idObat, $sumberObat);
		
		$update->jumlah=$item->jml->TextBox->Text; 
		$update->save();
		*/
		$tujuan = $this->DDTujuan->SelectedValue;
		
		if($tujuan == '2')//gudang
			$jumlah = $item->gudang->TextBox->Text;	
		if($tujuan == '3')//Poli 5
			$jumlah = $item->poli5->TextBox->Text;	
		
		if($tujuan == '4')
			$jumlah = $item->icu->TextBox->Text;
		
		if($tujuan == '5')
			$jumlah = $item->ugd->TextBox->Text;
		
		if($tujuan == '6')
			$jumlah = $item->ok->TextBox->Text;
		
		if($tujuan == '7')
			$jumlah = $item->vk->TextBox->Text;
		
		if($tujuan == '8')
			$jumlah = $item->anak->TextBox->Text;
		
		if($tujuan == '9')
			$jumlah = $item->kandungan->TextBox->Text;
		
		if($tujuan == '10')
			$jumlah = $item->bayi->TextBox->Text;
		
		if($tujuan == '11')
			$jumlah = $item->fisio->TextBox->Text;
		
		if($tujuan == '12')
			$jumlah = $item->nurse_a->TextBox->Text;
		
		if($tujuan == '13')
			$jumlah = $item->nurse_b->TextBox->Text;
		
		if($tujuan == '14')
			$jumlah = $item->apotik->TextBox->Text;
			
			
		if(intval($jumlah) || $jumlah == 0)
		{
			$harga = $item->harga->TextBox->Text;
		
			if($harga > 0)
			{
				$hrgNett = $harga/1.1;
			}else{
				$hrgNett = '0';
			}
				//$sql="UPDATE tbt_obat_harga SET hrg_ppn='$harga', hrg_netto='$hrgNett' WHERE id='$idHarga' AND sumber='01'";
				$sql="UPDATE tbt_obat_harga SET hrg_ppn='$harga', hrg_netto='$hrgNett' WHERE kode='$idObat' AND sumber='01'";
				$this->queryAction($sql,'C');	//$this->showSql->Text = $harga; 		
			/*
			if($this->getViewState('tujuan')=='1')
			{
				$sql="UPDATE tbt_stok_gudang SET jumlah='$jumlah' WHERE id_obat='$idObat'";
			}
			else
			{
			*/
				//if($item->findControl('txtExp')->Text != '' && $item->findControl('txtExp')->Text != '0000-00-00' )	
				if($expired != '' && $expired != '0000-00-00' )	
				{
					if($item->expired->expAwal->Value != '' && $item->expired->expAwal->Value != '0000-00-00' )		
						$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah', expired='$expired' WHERE id_harga='$idHarga' AND id_obat='$idObat' AND expired='$expiredAwal' AND  tujuan = '$tujuan'";
					else
						$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah', expired='$expired' WHERE id_harga='$idHarga' AND id_obat='$idObat' AND (expired='0000-00-00' OR expired IS NULL) AND  tujuan = '$tujuan'";	
				}
				else
				{
					if($item->expired->expAwal->Value != '' && $item->expired->expAwal->Value != '0000-00-00' )		
						$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah' WHERE id_harga='$idHarga' AND id_obat='$idObat' AND expired='$expiredAwal' AND  tujuan = '$tujuan'";
					else
						$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah' WHERE id_harga='$idHarga' AND id_obat='$idObat' AND (expired='0000-00-00' OR expired IS NULL) AND  tujuan = '$tujuan'";
					
				}
				//$sql="UPDATE tbt_stok_lain SET jumlah='$jumlah' WHERE id_obat='$idObat' AND tujuan = '$tujuan'";
				$this->queryAction($sql,'C');
				//$this->showSql->Text = $sql;
			//}
			
			
			
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
			
			ObatRecord::finder()->deleteByPk($ID);	
			$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
			
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
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
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
			//$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
			$this->DDSumSekunder->dataBind(); 	
			$this->DDSumSekunder->Enabled=false;
			$this->clearViewState('sumber');
			$this->setViewState('sumber',$this->DDSumMaster->SelectedValue);
		}		
		else{
			$this->DDSumSekunder->DataSource=SubSumberObatRecord::finder()->findAll('id_sumber = ?', $this-> DDSumMaster->SelectedValue);
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
		$jnsBarang = $this->DDJenisBrg->SelectedValue;
		$tipe = $this->collectSelectionResult($this->RBtipeObat);
		
		if($this->semuaStok->Checked === true){
			$tipeCetak = '1';
		}
		else
		{
			$tipeCetak = '0';
		}
				
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakStokGudang',array('tujuan'=>$tujuan,'jnsBarang'=>$jnsBarang,'tipe'=>$tipe,'tipeCetak'=>$tipeCetak)));
	}
	
}
?>
