<?php
class LapHutang extends SimakConf
{   
	private $sortExp = "tgl_faktur";
    private $sortDir = "DESC";
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
			$this->gridPanel->Display='None';
			$this->cetakBtn->Enabled = false;
		}
	}	
		
    public function onLoad($param)
	{
		if(!$this->IsPostBack && !$this->IsCallBack)
		{	
			$this->DDtahun->DataSource=$this->tahun(2008,2051);
			$this->DDtahun->dataBind();
			
			$sql="SELECT id,nama FROM tbm_pbf_obat ORDER BY nama";
			
			$this->DDpbf->DataSource = $this->queryAction($sql,'S');
			$this->DDpbf->dataBind();
			
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
				  view_hutang.no_faktur,
				  view_hutang.total,
				  view_hutang.materai,
				  view_hutang.ppn,
				  view_hutang.pot,
				  view_hutang.tgl_faktur,
				  view_hutang.no_po,
				  view_hutang.st_keuangan,
				  tbm_pbf_obat.id AS id_pbf,
				  tbm_pbf_obat.nama AS nm_pbf
				FROM
				  view_hutang
				  INNER JOIN tbt_obat_beli ON (view_hutang.no_po = tbt_obat_beli.no_po)
				  INNER JOIN tbm_pbf_obat ON (tbt_obat_beli.pbf = tbm_pbf_obat.id)				
				WHERE view_hutang.no_faktur <> '' ";
						
			if($this->DDpbf->SelectedValue != '')
			{
				$DDpbf = $this->DDpbf->SelectedValue;
				$sql .= "AND tbm_pbf_obat.id = '$DDpbf' ";
			}
			
			if($this->noFak->Text != '')
			{
				$noFak = $this->noFak->Text;
				$sql .= "AND view_hutang.no_faktur = '$noFak' ";
			}
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND view_hutang.tgl_faktur = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND view_hutang.tgl_faktur BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (view_hutang.tgl_faktur)='$cariBln' AND YEAR(view_hutang.tgl_faktur)='$cariThn' ";
				
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
			
			$sql .= " GROUP BY view_hutang.no_po ";
			
			$this->setViewState('sql',$sql);
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
	    	$this->showSql->Text=$sql;
			
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
			$no_faktur = $item->DataItem['no_faktur'];
			$total = $item->DataItem['total'];
			$materai = $item->DataItem['materai'];
			$ppn = $item->DataItem['ppn'];
			$pot = $item->DataItem['pot'];
			$tgl_faktur = $item->DataItem['tgl_faktur'];
			$nm_pbf = $item->DataItem['nm_pbf'];
			$st_keuangan = $item->DataItem['st_keuangan'];
			
			$item->tglColumn->Text = $this->convertDate($tgl_faktur,'3');
			$item->pbfColumn->Text = $nm_pbf;
			$item->noFakColumn->Text = $no_faktur;
			$item->totalColumn->Text = number_format($total,'2',',','.');
			$item->potColumn->Text = number_format($pot,'2',',','.');
			
			if($ppn == '1')
			{
				$ppn = 0.1 * $total;
			}
			else
			{
				$ppn = 0;
			}
			
			if($materai == '1')
			{
				$mat = 3000;
			}
			elseif($materai == '2')
			{
				$mat = 6000;
			}
			else
			{
				$mat = 0;
			}
			
			if($st_keuangan == '1')
			{
				$st = 'Sedang Diajukan';
			}
			elseif($st_keuangan == '2')
			{
				$st = 'Sudah Dibayar';
			}
			else
			{
				$st = 'Belum Dibayar';
			}
			
			$item->ppnColumn->Text = number_format($ppn,'2',',','.');
			$item->materaiColumn->Text = number_format($mat,'2',',','.');
			$item->grandTotColumn->Text = number_format(($total - $pot) + $ppn + $mat,'2',',','.');
			$item->stColumn->Text = $st;
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
	
	public function DDpbfChanged($sender,$param)
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
		$session['cetakLapHutang'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakLapHutangXls',
			array(
				'pbf'=>$this->DDpbf->SelectedValue,
				'noFak'=>$this->noFak->Text,
				'periode'=>$this->txtPeriode->Text)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
