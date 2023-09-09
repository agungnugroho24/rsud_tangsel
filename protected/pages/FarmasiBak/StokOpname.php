<?php
class StokOpname extends SimakConf
{   
	private $sortExp = "nm_obat ASC, tujuan";
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
		
		$this->DDberdasarkan->Enabled = false;
		$this->DDberdasarkan->SelectedValue = '3';
		$this->setViewState('pilihPeriode',$this->DDberdasarkan->SelectedValue);
							
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
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
		}
		
		/*
		$sql = "SELECT * FROM tbt_stok_lain";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$idObat = $row['id_obat'];
			$tujuan = $row['tujuan'];
			$jumlah = $row['jumlah'];
			$tgl = date('Y-m-d');
			
			$sql = "INSERT INTO tbt_stok_opname (tgl,id_obat,tujuan,saldo_awal,saldo) VALUES ('$tgl','$idObat','$tujuan','$jumlah','$jumlah')";
			$this->queryAction($sql,'C');
		}
		*/
	}	
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$sql="SELECT id,nama FROM tbm_destinasi_farmasi ORDER BY nama";
			
			$this->DDtujuan->DataSource = $this->queryAction($sql,'S');
			$this->DDtujuan->dataBind();
			
			$sql="SELECT real_name, nip, allow FROM tbd_user";
			$arr=$this->queryAction($sql,'S');//Select row in tabel bro...
			foreach($arr as $row)
			{
				$arrApp=array();
				$var=$row['allow'];
				$arrApp = explode(',', $var);
				
				if (in_array('2', $arrApp))
				{
					$data[]=array('nip'=>$row['nip'],'nama'=>$row['real_name']);
				}	
			}
			
			$this->lockPeriode();
					
			//$this->bindGrid();									
			//$this->cariCM->focus();					
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
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
			
			$sql = "
				SELECT 
				  *
				FROM
				  view_stok_opname
				WHERE view_stok_opname.id <> '' ";
						
			if($this->DDtujuan->SelectedValue != '')
			{
				$DDtujuan = $this->DDtujuan->SelectedValue;
				$sql .= "AND view_stok_opname.id_tujuan = '$DDtujuan' ";
			}
			
			if($this->idObat->Text != '')
			{
				$idObat = $this->idObat->Text;
				$sql .= "AND view_stok_opname.id_obat = '$idObat' ";
			}
			
			if($this->nmObat->Text <> '')			
			{
				$cariNama = $this->nmObat->Text;
				
				if($this->Advance->Checked === true)
				{
					$sql .= "AND view_stok_opname.nm_obat LIKE '%$cariNama%' ";
				}
				else
				{	
					$sql .= "AND view_stok_opname.nm_obat LIKE '$cariNama%' ";
				}		
			}
					
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND view_stok_opname.tgl = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND view_stok_opname.tgl BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (view_stok_opname.tgl)='$cariBln' AND YEAR(view_stok_opname.tgl)='$cariThn' ";
				
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
			
			//$sql .= " GROUP BY view_hutang.no_po ";
			
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
			$id_obat = $item->DataItem['id_obat'];
			$nm_obat = $item->DataItem['nm_obat'];
			$tujuan = $item->DataItem['tujuan'];
			$saldo_awal = $item->DataItem['saldo_awal'];
			$jml_masuk = $item->DataItem['jml_masuk'];
			$jml_keluar = $item->DataItem['jml_keluar'];
			$saldo = $item->DataItem['saldo'];
			$jml_son = $item->DataItem['jml_son'];
			
			$item->nmColumn->Text = $nm_obat;
			$item->tujuanColumn->Text = $tujuan;
			$item->saldoAwalColumn->Text = number_format($saldo_awal,'0',',','.');
			$item->masukColumn->Text = number_format($jml_masuk,'0',',','.');
			$item->keluarColumn->Text = number_format($jml_keluar,'0',',','.');
			$item->saldoColumn->Text = number_format($saldo,'0',',','.');
			$item->sonColumn->sonTxt->Text = $jml_son;
			
			$item->sonColumn->idSon->Value = $item->ItemIndex;
			//$item->potColumn->Text = number_format($pot,'2',',','.');
			
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
	
	public function opnameBtnClicked($sender,$param)
	{	
		/*
		$ID = $param->opnameBtnClicked;
		
		$item=$param->Item;
		
		
		$jmlSon = $this->dtgSomeData->Items[$item->sonColumn->sonTxt->Text];
		
		$this->showSql->Text = '   
			<script type="text/javascript">
				alert("'.$jmlSon.'");
			</script>';	
		
		
		
		$item = $param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' ||$item->ItemType==='EditItem')
        {
			$ID = $this->dtgSomeData->DataKeys[$item->ItemIndex];
		}
		*/
		//$ID = $param->CommandParameter;
		foreach ($this->dtgSomeData->Items as $i) 
		{	
			$ID = $i->sonColumn->idSon->Value;
			$ck = $i->sonColumn->sonTxt->Text;
			/*
			$this->showSql->Text = '   
			<script type="text/javascript">
				alert("'.$ck.'");
			</script>';		
			*/
			$sql = "UPDATE tbt_stok_opname SET jml_son='$ck' WHERE id = '$ID'";
			$this->queryAction($sql,'C');
		}
		
		$this->bindGrid();
		/*
		$this->showSql->Text = '   
			<script type="text/javascript">
				alert("Jumlah SON sukses diperbaharui.");
			</script>';	
			
		
		$modeBayar = $item->sonColumn->sonTxt->Text;
			
			//$this->msg->Text = $item->fakturColumn6->modeBayar->SelectedValue.' - '.$noFaktur;
			
		$this->showSql->Text = '   
			<script type="text/javascript">
				alert("'.$modeBayar.'");
			</script>';	
		*/
	}
	
	public function prosesClicked()
	{
		if($this->IsValid)  // when all validations succeed
        {
			$session = $this->getSession();
			$session->remove("SomeData");        
			$this->dtgSomeData->CurrentPageIndex = 0;
			$this->bindGrid();
			$this->gridPanel->Display='Dynamic';
		}
		else
		{
			$this->gridPanel->Display='None';
		}
		
		if(!$this->getViewState('pilihPeriode'))
		{
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
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
	
	
	public function cariClicked()
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
		
	}
	
	public function DDtujuanChanged($sender,$param)
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
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function ChangedDDtahun($sender,$param)
	{
		$pilih=$this->ambilTxt($this->DDtahun);
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
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
		$session['cetakStokOpname'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakStokOpnameXls',
			array(
				'tujuan'=>$this->DDtujuan->SelectedValue,
				'idObat'=>$this->idObat->Text,
				'periode'=>$this->txtPeriode->Text)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
