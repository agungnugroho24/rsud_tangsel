<?php
class IcdRM extends SimakConf
{   
/**
 * IcdRM class file
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 * @version $Id: IcdRM.php 1001 2008-03-02 15:03:16Z anton $
 */

/**
 * IcdRM class
 *
 * @author Anton <anton@intergo.co.id>
 * @link http://www.intergo.co.id/
 * @copyright Copyright &copy; 2008 Intergo Telematics
 * @license http://www.intergo.co.id/license/
 */
 	private $sortExp = "kode";
    private $sortDir = "ASC";
    private $offset = 0;
    private $pageSize = 10;
	
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
	    $this->pageSize = $this->grid->PageSize;
        $this->offset = $this->pageSize * $this->grid->CurrentPageIndex;
        
        $session = $this->getSession();
		
		if (!$session->contains("SomeData")) 
        {
            $someDataList = new SomeDataList();
            $someDataList->offset = $this->offset;
            $someDataList->rowsPerPage = $this->pageSize;
            $someDataList->sortDirection = $this->SortDirection;
            $someDataList->sortExpression = $this->SortExpression;
			
			$sql = "SELECT 
						dtd, 
						kode, 
						indonesia, 
						inggris 
					FROM tbm_icd 
					WHERE kode <> ''	";
			
			if($this->nmIndo->Text != '')
			{
				$cariNmIndo = $this->nmIndo->Text;
				if($this->Advance->Checked === true){
					$sql .= "AND indonesia LIKE '%$cariNmIndo%' ";
				}
				else
				{	
					$sql .= "AND indonesia LIKE '$cariNmIndo%' ";
				}
			}	
			
			if($this->nmEng->Text != '')
			{
				$cariNmEng = $this->nmEng->Text;
				if($this->Advance->Checked === true){
					$sql .= "AND inggris LIKE '%$cariNmEng%' ";
				}
				else
				{	
					$sql .= "AND inggris LIKE '$cariNmEng%' ";
				}
			}	
			
			if($this->dtd->Text != '')
			{
				$dtd = $this->dtd->Text;
				if($this->AdvanceDtd->Checked === true){
					$sql .= "AND dtd LIKE '%$dtd%' ";
				}else{
					$sql .= "AND dtd LIKE '$dtd%' ";
				}	
			}
				
			if($this->kat->Text != '')
			{
				$kat = $this->kat->Text;
				$sql .= "AND kat = '$kat' ";
			}
			
			if($this->kode->Text != '')
			{
				$kode = $this->kode->Text;
				if($this->AdvanceIcdKode->Checked === true){
					$sql .= "AND kode LIKE '%$kode%' ";
				}else{
					$sql .= "AND kode LIKE '$kode%' ";
				}
			}
			
			//$this->setViewState('sql',$sql);
			//$this->msg->Text = $sql;
            $data = $someDataList->getSomeDataPage($sql);
            $this->grid->VirtualItemCount = $someDataList->getSomeDataCount($sql);
			//$this->jmlData->Text=$someDataList->getSomeDataCount($sql).' produk';    			
            $this->grid->DataSource = $data;
            $this->grid->DataBind();
			
			if($someDataList->getSomeDataCount($sql) > 10)
			{
				//$this->pagerPanel->Display = 'Dynamic';
        	}
			elseif($someDataList->getSomeDataCount($sql) <= 10 )
			{
				//$this->pagerPanel->Display = 'None';
			}
		}
        else {
            Prado::trace("db not called for datagrid", "SomeApp");
            
            $this->grid->DataSource = $session["SomeData"];
            $this->grid->DataBind();
			
			$this->clearViewState('sql');
        }
    }
	
	protected function grid_PageIndexChanged($sender,$param)
    {
        // clear data in session because we need to get a new page from db
        $session = $this->getSession();
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = $param->NewPageIndex;
        $this->bindGrid();
    }

    protected function grid_SortCommand($sender,$param)
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
        $session->remove("SomeData"); $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();

    }	
	
	protected function grid_ItemCreated($sender,$param)
    {
        $item=$param->Item;
		
		if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			$ID = $this->grid->DataKeys[$item->ItemIndex];
			
			//$nmDpn = $item->DataItem['nama_dpn'];
			//$nmBlk = $item->DataItem['nama_blk'];
			
			//$item->telpColumn->telpTxt->Text = $tlp;
			//$item->telpColumn->hpTxt->Text = $hp;
			
			
		
		}
    }
	
	protected function grid_EditCommand($sender,$param)
    { $this->grid->EditItemIndex=$param->Item->ItemIndex;
        $this->bindGrid();
    }
	
	protected function grid_CancelCommand($sender,$param)
    { $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	protected function grid_UpdateCommand($sender,$param)
    {
        $item = $param->Item; $this->grid->EditItemIndex=-1;
        $this->bindGrid();
    }
	
	public function gridEditClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$this->Response->redirect($this->Service->constructUrl('member.editMember',array('ID'=>$ID)));
		/*
		$this->Page->CallbackClient->focus($this->DDkateg);
		$this->DDkateg->SelectedValue = BiblioRecord::finder()->findByPk($ID)->id_kategori;
		$this->namaBrg->Text = BiblioRecord::finder()->findByPk($ID)->nama;
		$this->minStok->Text = BiblioRecord::finder()->findByPk($ID)->min_stok;
		$this->maxStok->Text = BiblioRecord::finder()->findByPk($ID)->max_stok;
		$this->setViewState('editBrg',$ID);
		*/
	}
	
	public function gridHapusClicked($sender,$param)
	{
		$ID = $param->CommandParameter;
		
		$sql = "DELETE FROM tbd_member WHERE id='$ID'";
		$this->queryAction($sql,'C');
		
		$this->bindGrid();
	}
	
	public function onInit($param)
	 {		
		parent::onInit($param);
		$tmpVar=$this->authApp('0');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
	
	
	public function onLoad($param)
	{				
		parent::onLoad($param);		
			
		if(!$this->IsPostBack || !$this->IsCallBack )
		{	
			/*
			$this->grid->VirtualItemCount=IcdRecord::finder()->count();			
			$this->grid->PagerStyle->Mode='Numeric';			
			// fetches all data account information 		
			$this->grid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2',$cariByDtd,$cariByKode,$cariByAdv,$cariByNmIndo,$cariByNmEng,$cariByKat);	
			$this->grid->dataBind();				
			*/
			$position='TopAndBottom';		
			$this->grid->PagerStyle->Position=$position;
			$this->grid->PagerStyle->Visible=true;
			
			$this->bindGrid();
			
			$this->showUp->Display = 'None';						
			$this->tglMsk->Text=date('d-m-Y');
			$this->tglMsk->focus();	
			
			$this->cm->Enabled = false;
			
			$tipeRawat = $this->Request['tipeRawat'];	
			$cm = $this->Request['cm'];
			$no_trans = $this->Request['no_trans'];
			
			if($tipeRawat!='' && $no_trans!='')
			{
				if($tipeRawat=='0')
				{
					$this->jnsPas->SelectedValue='1';
					$cm = RwtjlnRecord::finder()->findByPk($no_trans)->cm;
					$this->tglMsk->Text = $this->convertDate(RwtjlnRecord::finder()->findByPk($no_trans)->tgl_visit,'1');
					$id_klinik = RwtjlnRecord::finder()->findByPk($no_trans)->id_klinik;
				}
				elseif($tipeRawat=='1')	
				{
					$this->jnsPas->SelectedValue='2';
					$cm = RwtInapRecord::finder()->findByPk($no_trans)->cm;
					$this->tglMsk->Text = $this->convertDate(RwtInapRecord::finder()->findByPk($no_trans)->tgl_masuk,'1');
				}
				
				$this->jnsPasChanged();
				
				$this->cm->Text = $cm;	
				$this->callCM();
				
				if($tipeRawat!='' && $no_trans!='')
				{
					if($tipeRawat=='0')
					{
						$this->DDKlinik->SelectedValue = $id_klinik;
						$this->chKlinik($sender,$param);
					}
				}
				
			}	
		}
   }		
	
	public function cmCallback($sender,$param)
	{
		$this->cariPanel->render($param->getNewWriter());
		$this->showUp->render($param->getNewWriter());
	}
	
	public function callCM()
	{
		$cm = $this->formatCm($this->cm->Text);
		$tgl = $this->ConvertDate($this->tglMsk->Text,'2');
		
		if($this->jnsPas->SelectedValue == '1' ) //Rawat Jalan
		{
			//if($tmpShow=RwtjlnRecord::finder()->find('cm = ? AND st_alih = ? AND tgl_visit = ?', $this->formatCm($this->cm->Text), '0', $this->ConvertDate($this->tglMsk->Text,'2')))
			if($tmpShow=RwtjlnRecord::finder()->find('cm = ? AND st_alih = ? AND tgl_visit = ?', $this->formatCm($this->cm->Text), '0', $this->ConvertDate($this->tglMsk->Text,'2')))
			{	
				$tmpPas = PasienRecord::finder()->findByPK($this->formatCm($this->cm->Text));
				$this->nmPas->Text = $tmpPas->nama;
				
				$sql="SELECT 
						id_klinik	
					FROM 
						tbt_rawat_jalan
					WHERE 
						cm='$cm' 					
						AND st_alih='0'
						AND tgl_visit = '$tgl'";
				$arr=$this->queryAction($sql,'S');
				
				foreach($arr as $row)
				{
					$idPoli = $row['id_klinik'];	
					$nmPoli = PoliklinikRecord::finder()->findByPK($idPoli)->nama;
					
					$data[]=array('id'=>$idPoli,'nama'=>$nmPoli);
				
					//$petugasPoli = $row['operator'];	
				}
				
				$this->DDKlinik->DataSource = $data;
				$this->DDKlinik->dataBind();
				
				$tmpKlinik = PoliklinikRecord::finder()->findByPK($tmpShow->id_klinik);
				//$this->klinik->Text=$tmpKlinik->nama;
				
				//$this->DDDokter->DataSource=PegawaiRecord::finder()->findAll('kelompok = ? AND poliklinik = ?', '1', $tmpShow->id_klinik);//kelompok pegawai '1' adalah untuk dokter
				//$this->DDDokter->dataBind();
				
				$this->cariPanel->Enabled = false;
				$this->showUp->Display='Dynamic';
				$this->ErrMsg->Visible=false;
				//$this->Page->CallbackClient->focus($this->DDKlinik);	
				
				$this->ErrMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->DDKlinik->getClientID().'.focus();
				</script>';
			}
			else
			{
				$this->showUp->Display='None';
				//$this->ErrMsg->Text='No. RM tidak ada!';
				
				$this->cm->Text = '';
				//$this->Page->CallbackClient->focus($this->cm);	
				
				$this->ErrMsg->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$cm.' belum terdaftar di Rawat Jalan!");
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
			}
		}
		elseif($this->jnsPas->SelectedValue == '2' ) //Rawat Inap
		{
			if($tmpShow=RwtInapRecord::finder()->find('cm = ? AND status = ? ', $this->formatCm($this->cm->Text), '0'))
			{	
				$tmpPas = PasienRecord::finder()->findByPK($this->formatCm($this->cm->Text));
				$this->nmPas->Text = $tmpPas->nama;
				
				
				$this->DDKlinik->SelectedValue = 'empty';				
				$this->DDKlinik->Enabled = false;
				
				$noTrans =  $tmpShow->no_trans;
				$this->setViewState('noTrans',$noTrans);
			
				$idDokter = $tmpShow->dokter;		
				$nmDokter = PegawaiRecord::finder()->findByPk($idDokter)->nama;
				
				$this->dokterTxt->Text = $nmDokter;
				$this->keluhan->Text = $tmpShow->keluhan;
				$this->icdTxt->Text = $tmpShow->icd;
				
				$this->cariPanel->Enabled = false;
				$this->showUp->Display='Dynamic';
				$this->ErrMsg->Visible=false;
				//$this->Page->CallbackClient->focus($this->DDKlinik);	
				
				$this->ErrMsg->Text = '    
				<script type="text/javascript">
					document.all.'.$this->DDKlinik->getClientID().'.focus();
				</script>';
			}
			else
			{
				$this->showUp->Display='None';
				//$this->ErrMsg->Text='No. RM tidak ada!';
				
				$this->cm->Text = '';
				//$this->Page->CallbackClient->focus($this->cm);	
				
				$this->ErrMsg->Text = '    
				<script type="text/javascript">
					alert("No. Rekam Medis '.$cm.' belum terdaftar di Rawat Inap!");
					document.all.'.$this->cm->getClientID().'.focus();
				</script>';
			}
		}
		
		//$this->tes->Text=$sql;
	}	
	
	public function chKlinik($sender,$param)
	{
		$idKlinik = $this->DDKlinik->SelectedValue;
		
		if($idKlinik != '')
		{
			$cm = $this->formatCm($this->cm->Text);
			$tgl = $this->ConvertDate($this->tglMsk->Text,'2');
			
			$sql="SELECT 
					no_trans,dokter,keluhan,icd
				FROM 
					tbt_rawat_jalan
				WHERE 
					cm='$cm' 
					AND id_klinik='$idKlinik'					
					AND st_alih='0'
					AND tgl_visit = '$tgl'";
			
			$noTrans = RwtjlnRecord::finder()->findBySql($sql)->no_trans;
			$this->setViewState('noTrans',$noTrans);
					
			$idDokter = RwtjlnRecord::finder()->findBySql($sql)->dokter;		
			$nmDokter = PegawaiRecord::finder()->findByPk($idDokter)->nama;
			$this->dokterTxt->Text = $nmDokter;
			$this->keluhan->Text = RwtjlnRecord::finder()->findBySql($sql)->keluhan;
			$this->icdTxt->Text = RwtjlnRecord::finder()->findBySql($sql)->icd;
		}
		else
		{
			$this->dokterTxt->Text = '';
			$this->icdTxt->Text = '';
			$this->keluhan->Text = '';
		}
	}
	
    public function gridMasukanClicked($sender,$param)
    {
		$ID = $param->CommandParameter;
		$noTrans = $this->getViewState('noTrans');
		
        if (!$this->User->IsOrdinary)
		{
			$ID = $param->CommandParameter;
			$keluhan = $this->keluhan->Text ;
			
			if($this->jnsPas->SelectedValue == '1' ) //Rawat Jalan
			{
				$rwtJlnRecord = RwtjlnRecord::finder()->findByPk($noTrans);	
				/*
				if($rwtJlnRecord->icd2)
				{			
					$this->tes->Text = '    
					<script type="text/javascript">
						alert("GAGAL!! Keluhan dan ICD untuk pasien Rawat Inap dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\nsudah dimasukan sebelumnya.");
						window.location="index.php?page=Pendaftaran.IcdRM"; 
					</script>';
				}
				else if($rwtJlnRecord->icd)
				{
					$rwtJlnRecord->icd2 = $ID;
				}else{
					$rwtJlnRecord->icd = $ID;
				}	
				*/
				
				if($rwtJlnRecord->icd)
				{		
					if($this->Request['tipeRawat']!='' && $this->Request['cm']!='')	
					{
						$this->tes->Text = '    
						<script type="text/javascript">
							alert("GAGAL!! Keluhan dan ICD untuk pasien Rawat Jalan dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\nsudah dimasukan sebelumnya.");
							window.location="index.php?page=Pendaftaran.DaftarCariPdftrn"; 
						</script>';
					}
					else
					{
						$this->tes->Text = '    
						<script type="text/javascript">
							alert("GAGAL!! Keluhan dan ICD untuk pasien Rawat Jalan dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\nsudah dimasukan sebelumnya.");
							window.location="index.php?page=Pendaftaran.IcdRM"; 
						</script>';
					}
						
				}
				else
				{
					
				$rwtJlnRecord->icd = $ID;	
				$rwtJlnRecord->keluhan = $keluhan;
				$rwtJlnRecord->save(); 	
				
				$sql="SELECT dtd FROM tbm_icd WHERE kode = '$ID' LIMIT 1";
			$dtd = IcdRecord::finder()->findBySql($sql)->dtd;
			$tgllhr= PasienRecord::finder()->findbyPk($this->formatCm($this->cm->Text))->tgl_lahir;
				
			$thn=substr($tgllhr,0,4);
			$bln=substr($tgllhr,5,2);
			$hari=substr($tgllhr,8,2);	
			
			
			$umur = $this->get_age($thn, $bln, $hari);
			$umur = explode('-',$umur);
			
				if($umur['2']==0 && $umur['1']==0 && $umur['0']<=28)
				{
					$st_umur = 1;
				}
				elseif($umur['2']==0 && $umur['1']==0 && $umur['0']>28 || $umur['2']==0 && $umur['1']>=1 )
				{
					$st_umur = 2;
				}
				elseif($umur['2']>=1 && $umur['2']<=4 )
				{
					$st_umur = 3;
				}
				elseif($umur['2']>=5 && $umur['2']<=14 )
				{
					$st_umur = 4;
				}
				elseif($umur['2']>=15 && $umur['2']<=24 )
				{
					$st_umur = 5;
				}
				elseif($umur['2']>=25 && $umur['2']<=44 )
				{
					$st_umur = 6;
				}
				elseif($umur['2']>=45 && $umur['2']<=64 )
				{
					$st_umur = 7;
				}
				elseif($umur['2']>=65)
				{
					$st_umur = 8;
				}
				else
				{
					$st_umur = 0;
				}
				$thnsql=substr($this->tglMsk->Text,6,4);
				$blnsql=substr($this->tglMsk->Text,3,2);
				$hrsql=substr($this->tglMsk->Text,0,2);
				$sqldate=$thnsql.'-'.$blnsql.'-'.$hrsql;
				$recordBantuBaru=new BantuRawatJalanRecord();
				$recordBantuBaru->kd_dtd=$dtd;
				$recordBantuBaru->cm=$this->formatCm($this->cm->Text);
				$recordBantuBaru->no_trans_rwtjln=$noTrans;
				$recordBantuBaru->tgl_trans_rwtjln=$sqldate;
				$recordBantuBaru->st_umur=$st_umur;
				$recordBantuBaru->save();
				
				if($this->Request['tipeRawat']!='' && $this->Request['cm']!='')	
				{
					$this->tes->Text = '    
					<script type="text/javascript">
						alert("Keluhan dan ICD untuk pasien Rawat Jalan dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\ntelah dimasukan ke dalam database.");
						window.location="index.php?page=Pendaftaran.DaftarCariPdftrn"; 
					</script>';
				}
				else
				{
					$this->tes->Text = '    
					<script type="text/javascript">
						alert("Keluhan dan ICD untuk pasien Rawat Jalan dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\ntelah dimasukan ke dalam database.");
						window.location="index.php?page=Pendaftaran.IcdRM"; 
					</script>';
				}
				
				}
			}
			elseif($this->jnsPas->SelectedValue == '2' ) //Rawat Inap
			{
				$rwtInapRecord = RwtInapRecord::finder()->findByPk($noTrans);	
				if($rwtInapRecord->icd)
				{		
					if($this->Request['tipeRawat']!='' && $this->Request['cm']!='')	
					{
						$this->tes->Text = '    
						<script type="text/javascript">
							alert("GAGAL!! Keluhan dan ICD untuk pasien Rawat Inap dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\nsudah dimasukan sebelumnya.");
							window.location="index.php?page=Pendaftaran.DaftarCariPdftrn"; 
						</script>';
					}
					else
					{
						$this->tes->Text = '    
						<script type="text/javascript">
							alert("GAGAL!! Keluhan dan ICD untuk pasien Rawat Inap dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\nsudah dimasukan sebelumnya.");
							window.location="index.php?page=Pendaftaran.IcdRM"; 
						</script>';
					}
						
					
				}
				else
				{
					$rwtInapRecord->icd = $ID;
					
				
				$rwtInapRecord->keluhan = $keluhan;
				$rwtInapRecord->save(); 	
				
				if($this->Request['tipeRawat']!='' && $this->Request['cm']!='')	
				{
					$this->tes->Text = '    
					<script type="text/javascript">
						alert("Keluhan dan ICD untuk pasien Rawat Inap dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\ntelah dimasukan ke dalam database.");
						window.location="index.php?page=Pendaftaran.DaftarCariPdftrn"; 
					</script>';
				}
				else
				{
					$this->tes->Text = '    
					<script type="text/javascript">
						alert("Keluhan dan ICD untuk pasien Rawat Inap dengan No. Rekam Medis '.$this->formatCm($this->cm->Text).'\ntelah dimasukan ke dalam database.");
						window.location="index.php?page=Pendaftaran.IcdRM"; 
					</script>';
				}
				}
				
			}
			
			//$this->Response->redirect($this->Service->constructUrl('Pendaftaran.IcdRM'));
		}	
    }	
	
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
	public function batalClicked($sender,$param)
	{		
		$this->Response->reload();
	}
	public function baruClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Pendaftaran.IcdBaru'));
	}
	
	
	public function jnsPasChanged()
	{
		$this->cm->Enabled = true;
		$this->cm->Text = '';
		$this->Page->CallbackClient->focus($this->cm);
	}
	
	
	public function cariICDsajaCallback($sender,$param)
	{
		$this->cariPanel->render($param->getNewWriter());
		$this->cariIcdPanel->render($param->getNewWriter());
	}
	
	
	
	public function cariICDsajaChanged($sender,$param)
	{
		if($this->cariICDsaja->Checked === true){
			//$this->noAct1->Enabled=false;
			//$this->noAct2->Enabled=false;
			//$this->noAct3->Enabled=false;
			
			$this->cariBtn->CausesValidation = false;
		}
		else
		{
			//$this->noAct1->Enabled=true;
			//$this->noAct2->Enabled=true;
			//$this->noAct3->Enabled=true;
			
			$this->cariBtn->CausesValidation = true;
		}
	}
	
	public function cariClicked($sender,$param)
	{			
		
		$session = $this->getSession();
        $session->remove("SomeData");        
        $this->grid->CurrentPageIndex = 0;
        $this->bindGrid();
	}			
		
}
?>
