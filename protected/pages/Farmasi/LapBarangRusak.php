<?php
class LapBarangRusak extends SimakConf
{   
	private $sortExp = "no_bbk";
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
	 
	public function onPreRender($param)
	{
		parent::onPreRender($param);
				
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
			$this->gridPanel->Display='Dynamic';
			//$this->cetakBtn->Enabled = false;
		}
	}
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDJenisBrg->DataSource=JenisBrgRecord::finder()->findAll();
			$this->DDJenisBrg->dataBind(); 
			
			$this->DDGol->DataSource=GolObatRecord::finder()->findAll();
			$this->DDGol->dataBind();	
			
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll();
			$this->DDKlas->dataBind();	
			
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();	
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll($criteria);
			$this->DDPbf->dataBind();	
			
			$this->DDProd->DataSource=ProdusenObatRecord::finder()->findAll($criteria);
			$this->DDProd->dataBind();	
			
			$this->DDSat->DataSource=SatuanObatRecord::finder()->findAll($criteria);
			$this->DDSat->dataBind();				
			
			$this->RBtipeObat->Enabled=false;	
			
			$sql = "SELECT * FROM tbm_destinasi_farmasi WHERE id <> '2' ORDER BY nama";
			$this->DDAsal->DataSource=DesFarmasiRecord::finder()->findAllBySql($sql);
			$this->DDAsal->dataBind();				
			
			$this->lockPeriode();
					
			//$this->bindGrid();									
			//$this->cariCM->focus();					
			
			
			$this->cetakBtn->Enabled = false;
		}
	}
	
	public function panelCallback($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
		
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
	

//----------------------------------------------------------- GRID BERDASARKAN POLI ---------------------------------
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

    private function bindGrid()
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
			
			$sql = "SELECT 
					  tbm_obat.kode,
					  tbm_obat.nama AS nm_obat,
					  tbm_obat.satuan,
					  tbm_obat.gol,
					  tbm_obat.klasifikasi,
					  tbm_obat.derivat,
					  tbm_obat.produsen,
					  tbm_obat.pbf,
					  tbm_obat.jenis,
					  tbm_obat.tipe,
					  tbm_obat.kategori,
					  tbt_bbk_barang_rusak.jumlah,
					  tbt_bbk_barang_rusak.id_harga,
					  tbt_bbk_barang_rusak.hrg_ppn,
					  tbt_bbk_barang_rusak.expired,
					  tbt_bbk_barang_rusak.asal_dari,
					  tbt_bbk_barang_rusak.id_petugas,
					  tbt_bbk_barang_rusak.id_penerima,
					  tbt_bbk_barang_rusak.petugas,
					  tbt_bbk_barang_rusak.penerima,
					  tbt_bbk_barang_rusak.tgl,
					  tbt_bbk_barang_rusak.no_bbk,
					  tbm_destinasi_farmasi.nama AS nm_asal
					FROM
					  tbt_bbk_barang_rusak
					  INNER JOIN tbm_obat ON (tbt_bbk_barang_rusak.kode_obat = tbm_obat.kode) 
					  INNER JOIN tbm_destinasi_farmasi ON (tbt_bbk_barang_rusak.asal_dari = tbm_destinasi_farmasi.id)
					WHERE tbt_bbk_barang_rusak.asal_dari <> '2' ";
			
			if($this->DDAsal->SelectedValue != '')	
			{				
				$DDAsal = $this->DDAsal->SelectedValue;	
				$sql .= " AND asal_dari = '$DDAsal' ";	
			}
			
			if($this->ID->Text != '')	
			{				
				$kode = $this->ID->Text;	
				$sql .= " AND kode = '$kode' ";	
			}
			
			if($this->DDJenisBrg->SelectedValue != '')	
			{				
				$kategori = $this->DDJenisBrg->SelectedValue;	
				$sql .= " AND kategori = '$kategori' ";	
			}
							
			if($this->cariNama->Text != '')	
			{			
				$cariByNama = $this->cariNama->Text;
				if($this->Advance->Checked=== true)
				{
					$sql .= "AND tbm_obat.nama LIKE '%$cariByNama%' ";
				}else{
					$sql .= "AND tbm_obat.nama LIKE '$cariByNama%' ";
				}
			}
			
			if($this->RBtipeObat->SelectedValue != '')	
			{				
				$cariTipe = $this->RBtipeObat->SelectedValue;	
				$sql .= " AND tipe = '$cariTipe' ";	
			}
			
			if($this->DDGol->SelectedValue != '')	
			{				
				$cariByGol = $this->DDGol->SelectedValue;	
				$sql .= " AND gol = '$cariByGol' ";	
			}
			
			if($this->DDKlas->SelectedValue != '')	
			{				
				$cariByKlas = $this->DDKlas->SelectedValue;	
				$sql .= " AND klasifikasi = '$cariByKlas' ";	
			}
			
			if($this->DDDerivat->SelectedValue != '')	
			{				
				$cariByDerivat = $this->DDDerivat->SelectedValue;	
				$sql .= " AND derivat = '$cariByDerivat' ";	
			}
			
			if($this->DDPbf->SelectedValue != '')	
			{				
				$cariByPbf = $this->DDPbf->SelectedValue;	
				$sql .= " AND pbf = '$cariByPbf' ";	
			}
			
			if($this->DDProd->SelectedValue != '')	
			{				
				$cariByProd = $this->DDProd->SelectedValue;	
				$sql .= " AND produsen = '$cariByProd' ";	
			}
			
			if($this->DDSat->SelectedValue != '')	
			{				
				$cariBySat = $this->DDSat->SelectedValue;	
				$sql .= " AND satuan = '$cariBySat' ";	
			}
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND tgl = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (tgl)='$cariBln' AND YEAR(tgl)='$cariThn' ";
				
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
			
			//$sql .= "GROUP BY tgl ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	//$this->showSql->Text=$sql;
			
			if($someDataList->getSomeDataCount($sql) > 0)
			{
				$this->cetakBtn->Enabled = true;
        	}
			else
			{
				$this->cetakBtn->Enabled = false;
			}
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
		
		 
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$tglDist = $this->convertDate($item->DataItem['tgl'],'1');
			$exp = $this->convertDate($item->DataItem['expired'],'3');
			$hrgPpn = $item->DataItem['hrg_ppn'];
			
			$item->tglColumn->Text = $tglDist;
			$item->hrgColumn->Text = number_format($hrgPpn,'2',',','.');
			$item->tglColumnExp->Text = $exp;	
        } 	
    }
	
    public function deleteButtonClicked($sender,$param)
    {
        if ($this->User->IsAdmin)
			{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key
				
				KasirRwtJlnRecord::finder()->deleteByPk($ID);	
				$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObat'));
				
			}	
    }	

	
	
	public function prosesClicked()
	{
		$session = $this->getSession();
		$session->remove("SomeData"); 
		
		$this->dtgSomeData->Display='Dynamic';
		
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();	
		
		$this->gridPanel->Display='Dynamic';
		
		if(!$this->getViewState('pilihPeriode'))
		{
			$this->gridPanel->Display='None';
			//$this->cetakBtn->Enabled = false;
		}
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.MasterObatBaru'));		
	}
	
	
	public function prosesPilihGrid()
	{			
		$position='Top'; 
		$this->dtgSomeData->PagerStyle->Position=$position;
				
		$session = $this->getSession();
		$session->remove("SomeData");
				
		$this->dtgSomeData->PagerStyle->Visible=true;
			
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function cariClicked()
	{		
		$this->prosesPilihGrid();
		
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
			$this->RBtipeObat->SelectedIndex = -1;
		}		
		
		$this->cariClicked();
	}
	
	public function selectionChangedGol($sender,$param)
	{	
		if(!$this->DDGol->SelectedValue=='')
		{
			
			$gol = $this->DDGol->SelectedValue;	
			$this->DDKlas->DataSource=KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol);
			
			if(count(KlasifikasiObatRecord::finder()->findAll('gol_id = ?', $gol)) > 0)
			{
				$this->DDKlas->dataBind(); 	
				$this->DDKlas->Enabled=true;
			}
			else
			{
				$this->DDKlas->Enabled=false;
			}
			
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
		$this->DDKlas->SelectedIndex = -1;
	}
	
	public function selectionChangedKlas($sender,$param)
	{	
		if(!$this->DDKlas->SelectedValue=='')
		{
			$klas = $this->DDKlas->SelectedValue;	
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll('klas_id = ?', $klas);
			
			if(count(DerivatObatRecord::finder()->findAll('klas_id = ?', $klas)) > 0)
			{
				$this->DDDerivat->dataBind(); 	
				$this->DDDerivat->Enabled=true;
			}
			else
			{
				$this->DDDerivat->Enabled=false;
			}
			
		}
		else
		{
			$this->DDDerivat->DataSource=DerivatObatRecord::finder()->findAll();
			$this->DDDerivat->dataBind();
			$this->DDDerivat->Enabled=false;
		}		
		
		$this->cariClicked();
		$this->DDDerivat->SelectedIndex = -1;
	}
	
	public function ChangedDDberdasarkan($sender,$param)
	{		
		$this->prosesPilihGrid();
			
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
		
		$this->prosesPilihGrid();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$this->prosesPilihGrid();
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
	
	public function cetakClicked()
	{		
		$session=new THttpSession;
		$session->open();
		$session['cetakLapBarangRusak'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakLapBarangRusak',
			array(
				'asal'=>$this->ambilTxt($this->DDAsal),
				'jnsBarang'=>$this->ambilTxt($this->DDJenisBrg),
				'periode'=>$this->txtPeriode->Text)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
