<?php
class RekapIcd extends SimakConf
{   
	private $sortExp = "jml";
	private $sortDir = "DESC";
	private $offset = 0;
	private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
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
			$this->jnsPas->SelectedValue = '1';
			$this->DDtahun->DataSource=$this->dataTahun(2008,2051);
			$this->DDtahun->dataBind();
			
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
			
			$this->DDKasir->DataSource = $data;
			$this->DDKasir->dataBind();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			//$this->DDPoli->Enabled = false;
			
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok = '1' ORDER BY nama";
			//$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
			$this->DDDokter->DataSource = $this->queryAction($sql,'S');
			$this->DDDokter->dataBind();
			//$this->DDDokter->Enabled = false;
			
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
		$this->DDtahun->DataSource=$this->dataTahun(date('Y')-50,date('Y')+1);
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
			
			$sql = "SELECT
					  view_icd_ten.icd,
					  tbm_icd.indonesia,
						IF(LENGTH(tbm_icd.inggris) > 0, tbm_icd.inggris, tbm_icd.indonesia) AS inggris,
					  SUM(IF(view_icd_ten.icd <> '', 1, 0)) AS jml
					FROM
					  view_icd_ten
					  INNER JOIN tbm_icd ON (view_icd_ten.icd = tbm_icd.kode) 
					WHERE view_icd_ten.icd <> '' ";
			
			if($this->jnsPas->SelectedValue != '3')
			{
				$jnsPas = $this->jnsPas->SelectedValue;
				$sql .= "AND view_icd_ten.tipe_rawat = '$jnsPas' ";
			}
			
			if($this->jnsPas->SelectedValue == '1')
			{
				if($this->DDPoli->SelectedValue != '')
				{
					$poli = $this->DDPoli->SelectedValue;
					$sql .= "AND view_icd_ten.id_klinik = '$poli' ";
				}
			}
			
			if($this->DDDokter->SelectedValue != '')
			{
				$dokter = $this->DDDokter->SelectedValue;
				$sql .= "AND view_icd_ten.dokter = '$dokter' ";
			}
			
			if($this->getViewState('pilihPeriode')==='1')
			{
				if($this->tgl->Text != '')
				{
					$cariTgl = $this->ConvertDate($this->tgl->Text,'2');
					$sql .= "AND view_icd_ten.tgl_visit = '$cariTgl' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tgl->Text,'2'),'3');
				}
				
			}
			elseif($this->getViewState('pilihPeriode')==='2')
			{
				if($this->tglawal->Text != '' && $this->tglakhir->Text != '')
				{
					$cariTglAwal=$this->ConvertDate($this->tglawal->Text,'2');
					$cariTglAkhir=$this->ConvertDate($this->tglakhir->Text,'2');
					$sql .= "AND view_icd_ten.tgl_visit BETWEEN '$cariTglAwal' AND '$cariTglAkhir' ";
					
					$this->txtPeriode->Text='PERIODE : '.$this->ConvertDate($this->ConvertDate($this->tglawal->Text,'2'),'3').' s.d. '.$this->ConvertDate($this->ConvertDate($this->tglakhir->Text,'2'),'3');
				}
			}
			elseif($this->getViewState('pilihPeriode')==='3')
			{
				if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
				{
					$cariBln=$this->DDbulan->SelectedValue;
					$cariThn=$this->ambilTxt($this->DDtahun);
					$sql .= "AND MONTH (view_icd_ten.tgl_visit)='$cariBln' AND YEAR(view_icd_ten.tgl_visit)='$cariThn' ";
				
					$this->txtPeriode->Text='PERIODE : '.$this->namaBulan($this->DDbulan->SelectedValue).' '.$this->ambilTxt($this->DDtahun);
				}
				else
				{
					//$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
					$this->txtPeriode->Text='PERIODE : Semua Periode';
				}		
			}
			else
			{
				//$this->txtPeriode->Text='PERIODE : '.$this->namaBulan(date('m')).' '.date('Y');
				$this->txtPeriode->Text='PERIODE : Semua Periode';
			}
			
			$sql .= " GROUP BY view_icd_ten.icd ";
			
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			$this->jmlData->Text = $someDataList->getSomeDataCount($sql);    			
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
		
			$sql .= " ORDER BY ".$this->SortExpression.' '.$this->SortDirection.' ';
			//$sql .= " LIMIT ".$this->offset.', '.$this->pageSize;
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
			$icd = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			$item->icdColumn->Text = $item->DataItem['icd'];			
			$item->ketColumn->Text = $item->DataItem['inggris'];	
			$item->jmlColumn->Text = $item->DataItem['jml'];
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
			//$this->gridPanel->Display='None';
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
	
	
	public function cariClicked()
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
		
	}
	
	public function DDjnsTransChanged($sender,$param)
	{	
		$criteria = new TActiveRecordCriteria;												
		$criteria->OrdersBy['nama'] = 'asc';
				
		$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
		$this->DDPoli->dataBind();	
		
		$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
		$this->DDDokter->dataBind();
					
		if($this->DDjnsTrans->SelectedValue == 1)
		{	
			$this->DDPoli->Enabled = false;			
			$this->DDDokter->Enabled = false;
		}
		else
		{
			$this->DDPoli->Enabled = true;			
			$this->DDDokter->Enabled = true;
		}
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
		
	public function DDKasirChanged($sender,$param)
	{	
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function DDPoliChanged($sender,$param)
	{	
		if($this->DDPoli->SelectedValue=='')
		{
			$this->DDDokter->SelectedValue='empty';
			$this->DDDokter->Enabled=true;
		}
		elseif($this->DDPoli->SelectedValue=='06')//Bila UGD munculkan dokter umum
		{
			$sql = "SELECT * FROM tbd_pegawai WHERE kelompok = '1' AND poliklinik = '01' ORDER BY nama";
			$this->DDDokter->DataSource = $this->queryAction($sql,'S');
			//$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1','01');
			$this->DDDokter->dataBind();
			$this->DDDokter->Enabled=true;			
		}
		else
		{
			$this->DDDokter->DataSource = PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $this->DDPoli->SelectedValue);
			$this->DDDokter->dataBind();
		}
				
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
	}
	
	public function DDDokterChanged($sender,$param)
	{		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
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
		
		$session = $this->getSession();
		$session->remove("SomeData");        
		$this->dtgSomeData->CurrentPageIndex = 0;
		$this->bindGrid();
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
		$session['cetakRekapIcd'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakRekapIcdXls',
			array(
				'poli'=>$this->DDPoli->SelectedValue,
				'dokter'=>$this->DDDokter->SelectedValue,
				'periode'=>$this->txtPeriode->Text,
				'tipeRawat'=>$this->jnsPas->SelectedValue)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
