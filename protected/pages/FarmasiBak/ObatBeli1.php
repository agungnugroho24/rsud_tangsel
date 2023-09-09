<?php
class ObatBeli1 extends SimakConf
{
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
		
		if(!$this->IsPostBack && !$this->IsCallBack){
			$this->noPO->focus();
			
			$sql = "SELECT id FROM tbt_obat_beli";		
			$data = ObatBeliRecord::finder()->findBySql($sql);
			if($data==NULL)
			{
				$this->noPO->Text = '0001/RSAL-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
			}
			else
			{
				//$this->noPO->Text = $this->numUrut('tbt_obat_beli',ObatBeliRecord::finder(),'4').'/RSAL-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
				 $this->noPO->Text = $this->noUrut().'/RSAL-PO/'.$this->bulanRomawi(date('m')).'/'.date('Y');
			}
			
			$this->DDPbf->DataSource=PbfObatRecord::finder()->findAll();
			$this->DDPbf->dataBind();	
			
			$this->prosesPanel->Display = 'None';	
			$this->maxErrMsgPanel->Display = 'None';	
			$this->checkJmlMax->Display = 'None';		
			$this->cetakBtn->Enabled = false;					
			$this->previewBtn->Enabled = false;					
		}		
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...			
			$this->UserGrid->DataSource=$arrData;
			$this->UserGrid->dataBind();
		}
	}
	
	
	public function prosesCallBack($sender,$param)
   	{
		$this->firstPanel->render($param->getNewWriter());
	}
	
	public function secondCallBack($sender,$param)
   	{
		$this->prosesPanel->render($param->getNewWriter());
	}
	
	public function noUrut()
    {			
		//Mbikin No Urut
		$find = ObatBeliRecord::finder();//::finder();		
		$sql = "SELECT no_po FROM tbt_obat_beli order by id desc";
		
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			$tmp='0001';
				
		}
		else
		{	
			$urut = substr($num->getColumnValue('no_po'),0,4) + 1;				
			$tmp=substr('0000',0,4-strlen($urut)).$urut;		
		}
		return $tmp;
	}
	
	public function DDJenisBrgChanged($sender,$param)
	{		
		if($this->DDJenisBrg->SelectedValue != ''){
			$idJnsBrg = $this->DDJenisBrg->SelectedValue;
			
			$sql = "SELECT kode, nama FROM tbm_obat WHERE kategori = '$idJnsBrg' ORDER BY nama ";
					
				//$this->DDNamaBrg->DataSource=ObatRecord::finder()->findAll('kategori = ?', $this->DDJenisBrg->SelectedValue);
				$this->DDNamaBrg->DataSource=ObatRecord::finder()->findAllBySql($sql);
				$this->DDNamaBrg->dataBind(); 	
				$this->DDNamaBrg->Enabled=true;
				$this->DDNamaBrg->focus();
		}
		else{
			$this->DDNamaBrg->DataSource=ObatRecord::finder()->findAll();
			$this->DDNamaBrg->dataBind();
			$this->DDNamaBrg->Enabled=false;
			$this->clearViewState('minLoket');
			$this->clearViewState('maxLoket');
			$this->jml->Text = '';
			$this->DDJenisBrg->focus();
		}
		
		
		$this->maxErrMsgPanel->Display = 'None';
		$this->checkJmlMax->Display = 'None';
	} 
	
	public function DDNamaBrgChanged($sender,$param)
	{
		$idBarang = $this->DDNamaBrg->SelectedValue;
		$this->clearViewState('minLoket');
		$this->clearViewState('maxLoket');
			
		if($this->DDNamaBrg->SelectedValue != ''){			
			$minLoket=ObatRecord::finder()->findByPk($idBarang)->min_2;
			$maxLoket=ObatRecord::finder()->findByPk($idBarang)->max_2;
			
			if(($minLoket!=0 ) && ($maxLoket!=0 ))
			{
				$this->setViewState('minLoket',$minLoket);
				$this->setViewState('maxLoket',$maxLoket);
				
				$jmlStokLain = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$idBarang,'2')->jumlah;
				$this->jml->Text = $maxLoket - $jmlStokLain;
			}
			else
			{
				$this->jml->Text = '';
			}
		}
		else{
			$this->jml->Text = '';
		}
		
		$this->jml->focus();
	} 
	
	
	public function prosesLock()
	{	
		$this->prosesPanel->Display = 'Dynamic';
		$this->firstPanel->Enabled=false;
	} 
	
	public function prosesUnlock()
	{	
		$this->Response->Reload();
	} 
	
	public function prosesClicked()
	{	
		if($this->IsValid)  // when all validations succeed
        { 
			$this->prosesLock();
		}
	} 
	
	public function checkJmlMax($sender,$param)
    {   
		if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
			{
				$idBarang = $this->DDNamaBrg->SelectedValue;
				$minLoket=$this->getViewState('minLoket');
				$maxLoket=$this->getViewState('maxLoket');
				
				if(($minLoket!=0 ) && ($maxLoket!=0 ))
				{
					$jmlStokLain = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$idBarang,'2')->jumlah;
					
					//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
					$jmlTotal = $this->jml->Text + $jmlStokLain;
					
					// valid if the id kabupaten is not found in the database
					$param->IsValid=($jmlTotal <= $maxLoket);
					
				}
			}
		
    }
	
	public function makeTblTemp()
	{
		$this->maxErrMsgPanel->Display = 'None';
		$this->cetakBtn->Enabled = true;
		$this->previewBtn->Enabled = true;
		
		if (!$this->getViewState('nmTable'))
		{
			$nmTable = $this->setNameTable('nmTable');
			$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
					  kode varchar(5) NOT NULL,
					  jml int(11) NOT NULL,
					  hrg int(11) NOT NULL,
					  PRIMARY KEY (id)) ENGINE = MEMORY";
				
			$this->queryAction($sql,'C');//Create new tabel bro...
			
			$kode = $this->DDNamaBrg->SelectedValue;
			$nama =$this->ambilTxt($this->DDNamaBrg);
			$jml = $this->jml->Text;

			$sql="SELECT * FROM tbt_obat_harga WHERE kode = '$kode' ORDER BY hrg_netto DESC LIMIT 0 , 1 ";
			$arr=$this->queryAction($sql,'R');
			foreach($arr as $row)
			{
				$hrg=$row['hrg_netto'];
			}
			
			$sql="INSERT INTO $nmTable (kode,jml,hrg) VALUES ('$kode','$jml','$hrg')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
				
			$arr=$this->queryAction($sql,'R');
			$i=0;
			$tot=0;
			foreach($arr as $row)
			{
				$i=$i+1;
				$tot=$tot+($row['jml'] * $row['hrg']);
			}
			$this->tot->text=number_format($tot,2,',','.');
			$this->UserGrid->VirtualItemCount=$i;				
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','2');
			$this->UserGrid->dataBind();
		}
		else
		{
			$kode = $this->DDNamaBrg->SelectedValue;
			$jml = $this->jml->Text;
			$nmTable = $this->getViewState('nmTable');
			
			$sql="SELECT jml FROM $nmTable WHERE kode='$kode'";
			$arr=$this->queryAction($sql,'R');
			$jmlData=0;			
			foreach($arr as $row)
			{
				$jmlAwal=$row['jml'];				
				$jmlData++;
			}			
			
			if($jmlData > 0)
			{
				if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
				{
					$idBarang = $this->DDNamaBrg->SelectedValue;
					$minLoket=$this->getViewState('minLoket');
					$maxLoket=$this->getViewState('maxLoket');
					
					if(($minLoket!=0 ) && ($maxLoket!=0 ))
					{
						$jmlStokLain = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$idBarang,'2')->jumlah;
						
						//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
						$jmlTotalStok = $jml + $jmlStokLain + $jmlAwal;
						if( $jmlTotalStok <= $maxLoket)
						{
							$jmlTot = $jmlAwal + $jml;
							$sql="UPDATE $nmTable SET jml='$jmlTot' WHERE kode='$kode' ";
							$this->queryAction($sql,'C');//Insert new row in tabel bro...
						}
						else
						{
							$this->DDJenisBrg->focus();
							$this->maxErrMsgPanel->Display = 'Dynamic';	}
					}
				}
				else
				{
					$jmlTot = $jmlAwal + $jml;
					$sql="UPDATE $nmTable SET jml='$jmlTot' WHERE kode='$kode' ";
					$this->queryAction($sql,'C');//Insert new row in tabel bro...
				}
				
			}
			else
			{
				$sql="SELECT * FROM tbt_obat_harga WHERE kode = '$kode' ORDER BY hrg_netto DESC LIMIT 0 , 1 ";
				$arr=$this->queryAction($sql,'R');
				foreach($arr as $row)
				{
					$hrg=$row['hrg_netto'];
				}
				$sql="INSERT INTO $nmTable (kode,jml,hrg) VALUES ('$kode','$jml','$hrg')";
				$this->queryAction($sql,'C');//Insert new row in tabel bro...
			}
			
			$sql="SELECT * FROM $nmTable ORDER BY id";
				
			$arr=$this->queryAction($sql,'R');
			$i=0;
			$tot=0;
			foreach($arr as $row)
			{
				$i=$i+1;
				$tot=$tot+($row['jml'] * $row['hrg']);
			}
			$this->tot->text=number_format($tot,2,',','.');
			$this->UserGrid->VirtualItemCount=$i;				
			
			$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
			$this->UserGrid->dataBind();
		}
	} 
	
	protected function getDataRows($offset,$rows,$order,$sort,$pil)
	{
		$nmTable = $this->getViewState('nmTable');
		if($pil == "1")
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
			
			if($offset <> '')	
				$sql .= " LIMIT " . $offset . ', ' . $rows;	
		}
		else
		{
			$sql="SELECT * FROM $nmTable ";
			$sql .= " GROUP BY id ";
			
			if($order <> '')							
				$sql .= " ORDER BY " . $order . ' ' . $sort;
		}
			
		
		$page=$this->queryAction($sql,'S');		 
		
		return $page;
		
	}	
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}	
	
	public function changePage($sender,$param)
	{	
		$limit=$this->UserGrid->PageSize;		
		$this->UserGrid->CurrentPageIndex=$param->NewPageIndex;
		$offset=$param->NewPageIndex*$this->UserGrid->PageSize;
		
		$nmTable = $this->getViewState('nmTable');		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();	
	} 
	
	public function changePagerPosition($sender,$param)
	{
		$top=$sender->Items[0]->Selected;
		$bottom=$sender->Items[1]->Selected;
		if($top && $bottom)
			$position='TopAndBottom';
		else if($top)
			$position='Top';
		else if($bottom)
			$position='Bottom';
		else
			$position='';
		if($position==='')
			$this->UserGrid->PagerStyle->Visible=false;
		else
		{
			$this->UserGrid->PagerStyle->Position=$position;
			$this->UserGrid->PagerStyle->Visible=true;
		}
	}
	
	public function sortGrid($sender,$param)
	{		
		$item = $param->SortExpression;
		
		$nmTable = $this->getViewState('nmTable');
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arr=$this->queryAction($sql,'R');
		$i=0;
		foreach($arr as $row)
		{
			$i=$i+1;
		}
		$this->UserGrid->VirtualItemCount=$i;
		
		$this->UserGrid->DataSource=$this->getDataRows($offset,$limit,$orderBy,'','1');
		$this->UserGrid->dataBind();
	}
	
	public function deleteClicked($sender,$param)
    {
        //if ($this->User->IsAdmin)
		//{
			//if ($this->getViewState('stQuery') == '1')
			//{
				// obtains the datagrid item that contains the clicked delete button
				$item=$param->Item;				
				// obtains the primary key corresponding to the datagrid item
				$ID=$this->UserGrid->DataKeys[$item->ItemIndex];
				// deletes the user record with the specified username primary key				
				$nmTable = $this->getViewState('nmTable');
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');								
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$jmlData=0;
				foreach($arrData as $row)
				{
					$jmlData++;
				}
				
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();
				$this->DDJenisBrg->focus();
				
				if($jmlData==0)
				{
					$this->cetakBtn->Enabled = false;
					$this->previewBtn->Enabled = false;
				}
				
				$this->maxErrMsgPanel->Display = 'None';
			//}	
			
		//}	
    }	
	
	public function refreshProsesCtrl()
	{
		$this->DDJenisBrg->SelectedIndex =-1;
		//$this->DDNamaBrg->DataSource=ObatRecord::finder()->findAll();
		//$this->DDNamaBrg->dataBind();
		$this->DDNamaBrg->Enabled=false;
		$this->clearViewState('minLoket');
		$this->clearViewState('maxLoket');
		$this->jml->Text = '';
	}
						
	public function tambahClicked()
	{	
		$this->checkJmlMax->Display = 'None';
		
		if($this->IsValid)  // when all validations succeed
        { 
			if($this->getViewState('minLoket') && $this->getViewState('maxLoket'))
			{
				$idBarang = $this->DDNamaBrg->SelectedValue;
				$minLoket=$this->getViewState('minLoket');
				$maxLoket=$this->getViewState('maxLoket');
				
				if(($minLoket!=0 ) && ($maxLoket!=0 ))
				{
					$jmlStokLain = StokLainRecord::finder()->find('id_obat = ? AND tujuan = ?',$idBarang,'2')->jumlah;
					
					//jika jumlah yg dimasukan + jml di stok tidak lebih besar daripada jumlah max 
					$jmlTotal = $this->jml->Text + $jmlStokLain;
					if( $jmlTotal <= $maxLoket)
					{
						$this->makeTblTemp();
						$this->refreshProsesCtrl();
					}
					else
					{
						//$this->jml->focus();
						$this->checkJmlMax->Dispalay='None';	
						$this->refreshProsesCtrl();
					}
				}
				else
				{
					$this->makeTblTemp();
					$this->refreshProsesCtrl();
				}

			}
			else
			{
				$this->makeTblTemp();
				$this->refreshProsesCtrl();
			}
		//*/
		}
		
		$this->DDJenisBrg->focus();
	} 
	
	public function batalClicked()
	{
		$this->prosesUnlock();
		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
	} 
	
	public function PreviewClicked($sender,$param)
	{		
		$noPO=$this->noPO->Text;
		$tgl=$this->convertDate($this->tglPO->Text,'2');
		$pbf=$this->DDPbf->SelectedValue;
		$wkt=date('G:i:s');
		$catatan=$this->catatan->Text;
		$operator=$this->User->IsUserNip;
		$nmTable=$this->getViewState('nmTable');
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.prevObatBeli',array('noPO'=>$noPO,'tgl'=>$tgl,'pbf'=>$pbf,'nmTable'=>$nmTable)));
		
		//$this->queryAction($this->getViewState('nmTable'),'D');
       			
	}
		
	public function cetakClicked($sender,$param)
	{		
		$noPO=$this->noPO->Text;
		$tgl=$this->convertDate($this->tglPO->Text,'2');
		$pbf=$this->DDPbf->SelectedValue;
		$wkt=date('G:i:s');
		$catatan=$this->catatan->Text;
		$operator=$this->User->IsUserNip;
		$nmTable=$this->getViewState('nmTable');
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$newData= new ObatBeliRecord();
			$newData->no_po=$noPO;
			$newData->tgl_po=$tgl;
			$newData->waktu=$wkt;
			$newData->pbf=$pbf;
			$newData->kode=$row['kode'];
			$newData->jumlah=$row['jml'];
			$newData->catatan=$catatan;
			$newData->petugas=$operator;
			$newData->flag='0';
			$newData->Save();			
		}
		
		$this->Response->redirect($this->Service->constructUrl('Farmasi.cetakObatBeli',array('noPO'=>$noPO,'tgl'=>$tgl,'pbf'=>$pbf)));
		
		$this->queryAction($this->getViewState('nmTable'),'D');
       			
	}
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable'))
		{
			$this->queryAction($this->getViewState('nmTable'),'D');
		}
		
		$this->Response->redirect($this->Service->constructUrl('Simak'));		
	}
}
?>
