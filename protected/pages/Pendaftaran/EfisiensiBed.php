<?php
class EfisiensiBed extends SimakConf
{
	private $sortExp = "tahun";
    private $sortDir = "DESC";
    private $offset = 0;
    private $pageSize = 10;	
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }
	
	public function onPreRender($param)
	{
		parent::onPreRender($param);
		if(!$this->Page->IsPostBack && !$this->Page->IsCallBack)  
		{
			$this->btnBatalTambah->Display = 'None';
			$this->DDtahun->DataSource = $this->dataTahun(date('Y')-20,date('Y')+10);
			$this->DDtahun->dataBind();
			$this->DDtahun->SelectedValue = date('Y');
			$this->DDbulan->SelectedValue = date('m');
			
			$this->tgl->Text = cal_days_in_month(CAL_GREGORIAN, $this->DDbulan->SelectedValue, $this->DDtahun->SelectedValue);
			
			$position='TopAndBottom';		
			$this->dtgSomeData->PagerStyle->Position=$position;
			$this->dtgSomeData->PagerStyle->Visible=true;
		}
		
		$this->bindGrid();
	}
	
	public function cekTgl($sender,$param)
	{
		if($this->DDbulan->SelectedValue != '' && $this->DDtahun->SelectedValue != '')
			$this->tgl->Text = cal_days_in_month(CAL_GREGORIAN, $this->DDbulan->SelectedValue, $this->DDtahun->SelectedValue);	
		else
			$this->tgl->Text = '';
	}
	
	public function TambahClicked($sender,$param)
	{
		if($this->Page->IsValid)
			$this->makeTmpTablePangkat();
		else
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','jQuery.WsGrowl.show({title: \'Konfirmasi\', content:\'<p class="msg_error" >Pastikan pengisian data sudah sesuai !</p>\',timeout: 3000,dialog:{modal: true}});');	
		}
	}
	
	public function makeTmpTablePangkat()
	{
		//$tgl = $this->DDtahun->SelectedValue.'-'.$this->DDbulan->SelectedValue.'-'.$this->tgl->Text;
		$thn = $this->DDtahun->SelectedValue;
		//$bln = $this->DDbulan->SelectedValue;
		
		if($this->btnTambah->Text == 'Tambah')
		{
			$sql = "SELECT id FROM tbt_efisiensi_bed WHERE tahun = '$thn' ";
		}
		else
		{
			$idEdit = $this->getViewState('idEdit');
			$sql="SELECT id FROM tbt_efisiensi_bed WHERE tahun = '$thn' AND id<>'$idEdit'";
		}
		
		if($this->queryAction($sql,'S'))
		{
			$this->getPage()->getClientScript()->registerEndScript
			('','unmaskContent();
				jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_error">Periode target yang dipilih sudah ada!</p>\',timeout: 3000,dialog:{
					modal: true
				}});');	
			$this->Page->CallbackClient->focus($this->bor);
		}
		else
		{
			//$thn = '2011';
			$jmlHari = 0;
			for($i=1;$i<=12;$i++)
			{
				$jmlHari += cal_days_in_month(CAL_GREGORIAN,$i,$thn);	
			}
			
			//jml pasien pulang
			$sql = "SELECT COUNT(no_trans) AS no_trans FROM tbt_rawat_inap WHERE status = '1' AND YEAR(tgl_masuk) = '$thn'";
			$jmlPasien = RwtInapRecord::finder()->findBySql($sql)->no_trans;
			
			//jml bed
			$sql = "SELECT SUM(jml_bed) AS jml_bed FROM tbm_ruang WHERE id_jns_kamar = '1'";
			$jmlBed = RuangRecord::finder()->findBySql($sql)->jml_bed;
			
			//jml total pasien meninggal
			$sql = "SELECT COUNT(id) AS id FROM tbt_rawat_inap_pulang WHERE keadaan_keluar = '4' AND YEAR(tgl_masuk) = '$thn' ";
			$jmlTotMeninggal = RwtInapPasPulangRecord::finder()->findBySql($sql)->id;
			
			//jml total pasien meninggal > 48 jam
			$sql = "SELECT COUNT(id) AS id FROM tbt_rawat_inap_pulang WHERE cr_keluar = '4' AND YEAR(tgl_masuk) = '$thn' ";
			$jmlTotMeninggal48 = RwtInapPasPulangRecord::finder()->findBySql($sql)->id;
			
			//jml hari rawat
			$sql = "SELECT 
					  tbt_inap_kamar.lama_inap AS jml_hari_rawat
					FROM
					  tbt_rawat_inap
					  INNER JOIN tbt_inap_kamar ON (tbt_rawat_inap.no_trans = tbt_inap_kamar.no_trans_inap)
					WHERE
					  tbt_rawat_inap.`status` = '1'
					  AND tbt_inap_kamar.`id_kmr_awal`='1'
					  AND YEAR(tbt_inap_kamar.`tgl_awal`) = '$thn'";
			foreach( $this->queryAction($sql,'S') as $row ) 
			{
				$jmlHariRawat += $row['jml_hari_rawat'];
			}
  			
			//bor
			if($jmlBed>0 && $jmlHari>0)
				$bor = $jmlHariRawat / ($jmlBed * $jmlHari) * 100;
			
			//alos
			if($jmlPasien >0)
				$alos = ceil($jmlHariRawat / $jmlPasien);
			
			//toi
			if($jmlPasien >0)
				$toi = ceil((($jmlBed * $jmlHari) - $jmlHariRawat) / $jmlPasien);
			
			//bto
			if($jmlBed >0)
				$bto = ceil($jmlPasien / $jmlBed);
			
			//gdr
			if($jmlPasien >0)
				$gdr = ($jmlTotMeninggal / $jmlPasien) * 100;
				
			//ndr
			if($jmlPasien >0)
				$ndr = ($jmlTotMeninggal48 / $jmlPasien) * 100;	
			
			/*$this->tes->Text = 'jml hari '.$jmlTotMeninggal .' - '.$jmlTotMeninggal48;
			$this->tes->Text .= '<br/> jml pasien '.$jmlPasien;
			$this->tes->Text .= '<br/> jml bed '.$jmlBed;
			$this->tes->Text .= '<br/> jml hari '.$jmlHariRawat;
			$this->tes->Text .= '<br/> BOR '.$bor;
			$this->tes->Text .= '<br/> ALOS '.$alos;
			$this->tes->Text .= '<br/> TOI '.$toi;
			$this->tes->Text .= '<br/> BTO '.$bto;
			*/
			
			if($this->btnTambah->Text == 'Tambah')
			{
				//INSERT tbt_efisiensi_bed
				$data = new EfisiensiBedRecord();
				$data->bor_target = floatval(trim($this->bor->Text));
				$data->alos_target = intval(trim($this->alos->Text));
				$data->toi_target = intval(trim($this->toi->Text));
				$data->bto_target = floatval(trim($this->bto->Text));
				$data->gdr_target = floatval(trim($this->gdr->Text));
				$data->ndr_target = floatval(trim($this->ndr->Text));
				
				$data->bor = $bor;
				$data->alos = $alos;
				$data->toi = $toi;
				$data->bto = $bto;
				$data->gdr = $gdr;
				$data->ndr = $ndr;
				
				$data->tahun = $thn;
			}
			else
			{
				//UPDATE tbt_efisiensi_bed
				$data = EfisiensiBedRecord::finder()->findByPk($idEdit);			
				$data->bor_target = floatval(trim($this->bor->Text));
				$data->alos_target = intval(trim($this->alos->Text));
				$data->toi_target = intval(trim($this->toi->Text));
				$data->bto_target = floatval(trim($this->bto->Text));
				$data->gdr_target = floatval(trim($this->gdr->Text));
				$data->ndr_target = floatval(trim($this->ndr->Text));
				
				$data->bor = $bor;
				$data->alos = $alos;
				$data->toi = $toi;
				$data->bto = $bto;
				$data->gdr = $gdr;
				$data->ndr = $ndr;
				
				$data->tahun = $thn;
			}
			
			$data->save();
			
			
			if($this->btnTambah->Text == 'Tambah')
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Target untuk periode tahun '.$this->DDtahun->SelectedValue.' sudah dimasukan dalam database.</p>\',timeout: 3000,dialog:{
						modal: true
					}});');		
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();
					jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_info">Target untuk periode tahun '.$data->tahun.' sudah diedit.</p>\',timeout: 3000,dialog:{
						modal: true
					}});');
			}
			
			$this->batalEditClicked();
		}
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


    // get data and bind it to datagrid
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
					  tbt_efisiensi_bed.id,
					  tbt_efisiensi_bed.tahun,
					  tbt_efisiensi_bed.bor,
					  tbt_efisiensi_bed.alos,
					  tbt_efisiensi_bed.toi,
					  tbt_efisiensi_bed.bto,
					  tbt_efisiensi_bed.gdr,
					  tbt_efisiensi_bed.ndr,
					  tbt_efisiensi_bed.bor_target,
					  tbt_efisiensi_bed.alos_target,
					  tbt_efisiensi_bed.toi_target,
					  tbt_efisiensi_bed.bto_target,
					  tbt_efisiensi_bed.gdr_target,
					  tbt_efisiensi_bed.ndr_target
					FROM
					  tbt_efisiensi_bed				
					WHERE tbt_efisiensi_bed.id <> ''  ";
			
			/*if($this->cariNama->Text != '')
			{
				$nama = $this->cariNama->Text;		
				if($this->Advance->Checked === true){
					$sql .= "AND tbm_lab_tindakan_paket_asuransi.nama LIKE '%$nama%' ";
				}
				else
				{	
					$sql .= "AND tbm_lab_tindakan_paket_asuransi.nama LIKE '$nama%' ";
				}
			}
			
			if($this->stRawat->SelectedValue != '')
			{
				$stRawat = $this->stRawat->SelectedValue;
				$sql .= "AND tbm_lab_tindakan_paket_asuransi.st_rawat = '$stRawat' ";
			} */
			
			/*$sql .= " GROUP BY
					  tbm_operasi_kategori.id,
					  tbm_kamar_kelas.id ";  */
			
			$this->setViewState('sql',$sql);
			//$this->showSql->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->dtgSomeData->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql);    			
            $this->dtgSomeData->DataSource = $data;
            $this->dtgSomeData->DataBind();
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
        else 
		{
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
		
        // clear data in session because we need to refresh it from db
        $session = $this->getSession();
        $session->remove("SomeData");
        
        $this->dtgSomeData->CurrentPageIndex = 0;
        $this->bindGrid();

    }
	
	public function itemCreatedPangkat($sender,$param)
    {
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem' || $item->ItemType==='UpdateItem' || $item->ItemType==='EditItem' || $item->ItemType==='DeleteItem')
        {
			$id = $this->dtgSomeData->DataKeys[$item->ItemIndex];
			
			$item->tglColumn->Text = $item->DataItem['tahun'];
			$item->borColumn->Text = number_format($item->DataItem['bor_target'],2,',','.');
			$item->alosColumn->Text = number_format($item->DataItem['alos_target'],0,',','.');
			$item->toiColumn->Text = number_format($item->DataItem['toi_target'],0,',','.');
			$item->btoColumn->Text = number_format($item->DataItem['bto_target'],2,',','.');
			$item->gdrColumn->Text = number_format($item->DataItem['gdr_target'],2,',','.');
			$item->ndrColumn->Text = number_format($item->DataItem['ndr_target'],2,',','.');
			
			$item->borColumn2->Text = number_format($item->DataItem['bor'],2,',','.');
			$item->alosColumn2->Text = number_format($item->DataItem['alos'],0,',','.');
			$item->toiColumn2->Text = number_format($item->DataItem['toi'],0,',','.');
			$item->btoColumn2->Text = number_format($item->DataItem['bto'],2,',','.');
			$item->gdrColumn2->Text = number_format($item->DataItem['gdr'],2,',','.');
			$item->ndrColumn2->Text = number_format($item->DataItem['ndr'],2,',','.');
        }
    }
	
	public function editRow($sender,$param)
    {
		$this->btnBatalTambah->Display = 'Dynamic';
		$this->btnTambah->Text = 'Simpan';
		
        $ID=$sender->CommandParameter;
		$this->setViewState('idEdit',$ID);	
		
		if(EfisiensiBedRecord::finder()->findByPk($ID))
		{
			$data = EfisiensiBedRecord::finder()->findByPk($ID);
			$this->bor->Text = floatval($data->bor_target);
			$this->alos->Text = floatval($data->alos_target);
			$this->toi->Text = floatval($data->toi_target);
			$this->bto->Text = floatval($data->bto_target);
			$this->gdr->Text = floatval($data->gdr_target);
			$this->ndr->Text = floatval($data->ndr_target);
			
			$this->DDtahun->SelectedValue = $data->tahun;
			//$this->DDbulan->SelectedValue = substr($data->tahun,5,2);
			//$this->tgl->Text = cal_days_in_month(CAL_GREGORIAN, $this->DDbulan->SelectedValue, $this->DDtahun->SelectedValue);
		}
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
    }	
	
	public function batalEditClicked()
    {
		$this->btnBatalTambah->Display = 'None';
		$this->btnTambah->Text = 'Tambah';
		$this->clearTmp();
    }	
	
	public function deleteRow($sender,$param)
    {
        $ID=$sender->CommandParameter;
		// deletes the user record with the specified username primary key	
		
		$sql = "DELETE FROM tbt_efisiensi_bed WHERE id='$ID'";
		//$sql = "UPDATE tbt_efisiensi_bed SET st_delete='1' WHERE id='$ID'";
		$this->queryAction($sql,'C');		
		
		$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
    }	
	
	public function cetakRow($sender,$param)
    {
        $ID=$sender->CommandParameter;
		// deletes the user record with the specified username primary key	
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakEfisiensiBed',array('pageParent'=>$this->Page->getPagePath(),'ID'=>$ID,'mode'=>'1')));
    }	
	
	public function cetakRow2($sender,$param)
    {
        $ID=$sender->CommandParameter;
		// deletes the user record with the specified username primary key	
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.cetakEfisiensiBed',array('pageParent'=>$this->Page->getPagePath(),'ID'=>$ID,'mode'=>'2')));
    }	
	
	public function clearTmp()
    {
		//$this->DDKelas->SelectedValue = 'empty';
		$this->bor->Text = '';
		$this->alos->Text = '';
		$this->toi->Text = '';
		$this->bto->Text = '';
		$this->gdr->Text = '';
		$this->ndr->Text = '';
		
		$this->DDtahun->SelectedValue = date('Y');
		$this->DDbulan->SelectedValue = date('m');
		
		$this->tgl->Text = cal_days_in_month(CAL_GREGORIAN, $this->DDbulan->SelectedValue, $this->DDtahun->SelectedValue);
		
		$this->Page->CallbackClient->focus($this->DDtahun);
		$this->clearViewState('idEdit');	
    }	
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			//$this->Page->CallbackClient->focus($this->DDKamar);
			//$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
			$this->Response->Reload();	
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Lab.masterLabPaketAsuransi'));	
		}
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table	
			$this->clearViewState('nmTable');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Lab.masterLabPaketAsuransi'));		
	}
}
?>