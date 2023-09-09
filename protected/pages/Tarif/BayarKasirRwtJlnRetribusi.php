<?php
class BayarKasirRwtJlnRetribusi extends SimakConf
{   
	private $sortExp = "id";
	private $sortDir = "ASC";
	private $offset = 0;
	private $pageSize = 10;	
	
	
	public function onInit($param)
	{		
		parent::onInit($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi kasir
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
			$this->DDtahun->DataSource=$this->tahun(date('Y')-10,date('Y')+10);
			$this->DDtahun->dataBind();
			
			$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			
			$this->DDPoli->DataSource = PoliklinikRecord::finder()->findAll($criteria);
			$this->DDPoli->dataBind();
			
			$this->lockPeriode();
			$this->DDberdasarkan->SelectedValue = '1';
			$this->setViewState('pilihPeriode','1');
			$this->prosesPilihGrid();
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
		$this->DDtahun->DataSource=$this->tahun(date('Y')-10,date('Y')+10);
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
			
			$sql = "SELECT * FROM view_retribusi WHERE no_trans IS NOT NULL ";
			
			if($this->st->SelectedValue <> '')
			{
				$st = $this->st->SelectedValue;
				$sql .= "AND st_flag = '$st' ";
			}
			
			if(trim($this->cm->Text) <> '')
			{
				$cm = $this->formatCm(trim($this->cm->Text));
				$sql .= "AND cm = '$cm' ";
			}
			
			if($this->DDPoli->SelectedValue <> '')
			{
				$DDPoli = $this->DDPoli->SelectedValue;
				$sql .= "AND id_poli = '$DDPoli' ";
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
			$no_trans = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$item->tglColumn->Text = $this->convertDate($item->DataItem['tgl'],'1');
			$item->wktColumn->Text = $item->DataItem['wkt'];
			$item->tarifColumn->Text = number_format($item->DataItem['tarif'],2,',','.');	
			
			if($item->DataItem['st_flag'] == '0')
				$item->bayarColumn->bayarBtn->Enabled = true;
			else
				$item->bayarColumn->bayarBtn->Enabled = false;	
				
			if($this->st->SelectedValue == '0')
				$this->dtgSomeData->Columns[8]->Visible = false;
			else
				$this->dtgSomeData->Columns[8]->Visible = true;
				
    }
	}
	
	public function bayarClicked($sender,$param)
	{
		$id = $sender->CommandParameter;
		$url = "index.php?page=Tarif.BayarRetribusi&id=".$id;
		$this->getPage()->getClientScript()->registerEndScript('',"tesFrame('$url',650,500,'PEMBAYARAN RETRIBUSI')");		
	}	
	
	public function cetakKwtClicked($sender,$param)
	{
		$id = $sender->CommandParameter;
		
		$data = KasirPendaftaranRecord::finder()->findByPk($id);
		$no_trans = $data->no_trans;
		$cm = RwtjlnRecord::finder()->findByPk($no_trans)->cm;
		$st_asuransi = RwtjlnRecord::finder()->findByPk($no_trans)->st_asuransi;
		$perus_asuransi = RwtjlnRecord::finder()->findByPk($no_trans)->perus_asuransi;
		$nama = PasienRecord::finder()->findByPk($cm)->nama;
		
		$url = "index.php?page=Tarif.cetakKwtRetribusi&layout=modal&notrans=".$no_trans;
		
		//$this->getPage()->getClientScript()->registerEndScript('',"window.open('".$url."');");		
		$this->getPage()->getClientScript()->registerEndScript('',"tesFrame('$url',jQuery(window).width()-100,jQuery(window).height()-50,'KWITANSI PEMBAYARAN RETRIBUSI')");		
	}	
	
	public function prosesBayar($sender,$param)
	{
		$operator=$this->User->IsUserNip;
		$nipTmp=$this->User->IsUserNip;
		$this->prosesPilihGrid();
		
		$Id = $param->CallbackParameter->Id;
		
		if($Id)
		{
			$data = KasirPendaftaranRecord::finder()->findByPk($Id);
			$no_trans = $data->no_trans;
			$cm = RwtjlnRecord::finder()->findByPk($no_trans)->cm;
			$st_asuransi = RwtjlnRecord::finder()->findByPk($no_trans)->st_asuransi;
			$perus_asuransi = RwtjlnRecord::finder()->findByPk($no_trans)->perus_asuransi;
			$nama = PasienRecord::finder()->findByPk($cm)->nama;
			
			//if($perus_asuransi == '01' && $st_asuransi == '1')
			//{
				$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtRetribusi',array('notrans'=>$no_trans)));
			/*}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Pembayaran Retribusi atas nama <strong>'.$nama.'</strong> sudah diproses.<br/></p>\',timeout: 3000,dialog:{
						modal: true
					}});');
			}*/
		}
		else
		{
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
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
	
	public function DDPoliChanged($sender,$param)
	{			
		//if($this->DDPoli->SelectedValue != '')
		//{
			/*$criteria = new TActiveRecordCriteria;												
			$criteria->OrdersBy['nama'] = 'asc';
			$criteria->Condition = 'kelompok = :kelompok AND poliklinik = :poli';
			$criteria->Parameters[':kelompok'] = '1';
			$criteria->Parameters[':poli'] = $this->DDPoli->SelectedValue;
			
			if($this->DDPoli->SelectedValue == '07')//IGD
			{
				$criteria->Parameters[':poli'] = '08'; //ambil dokter poli umum
			}*/
			
			//$this->DDDokter->DataSource = PegawaiRecord	::finder()->findAll($criteria);
			//$this->DDDokter->dataBind();
		//}
		
		$this->prosesPilihGrid();
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
		$session['cetakPenerimaanKasirRwtJln'] = $this->getViewState('sql');
		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakLapPenerimaanKasirRwtJlnNew',
			array(
				'kasir'=>$this->DDKasir->SelectedValue,
				'poli'=>$this->DDPoli->SelectedValue,
				'dokter'=>$this->DDDokter->SelectedValue,
				'periode'=>$this->txtPeriode->Text,
				'jnsTrans'=>$this->DDjnsTrans->SelectedValue,
				'jnsLap'=>$this->jnsLap->SelectedValue)));
		
	}
			
	protected function refreshMe()
	{
		//$this->prosesClicked();
	}
}

?>
