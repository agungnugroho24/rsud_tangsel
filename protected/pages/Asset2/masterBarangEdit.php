<?php
class masterBarangEdit extends SimakConf
{
	protected function getAssetBarangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AssetBarangRecord=AssetBarangRecord::finder()->findByPk($ID);		
		if(!($AssetBarangRecord instanceof AssetBarangRecord))
			throw new THttpException(500,'id tidak benar.');
		return $AssetBarangRecord;
	}	
	
	protected function getAssetFotoBarangRecord()
	{
		// use Active Record to look for the specified username
		$ID=$this->Request['ID'];
		$this->setViewState('ID',$ID);
		$AssetFotoBarangRecord=AssetFotoBarangRecord::finder()->find('id_brg = ?',$ID);		
		//if(!($AssetFotoBarangRecord instanceof AssetFotoBarangRecord))
			//throw new THttpException(500,'id tidak benar.');
		return $AssetFotoBarangRecord;
	}	
	
	public function onLoad($param)
	{				
		parent::onLoad($param);
		
		if(!$this->isPostBack)	            		
		{
			$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
			$this->DDsubHabisPakai->dataBind();	
			
			$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
			$this->DDsubBergerak->dataBind();	
			
			$this->DDVen->DataSource=AssetProdusenRecord::finder()->findAll();
			$this->DDVen->dataBind();	
			
			$this->DDDist->DataSource=AssetDistributorRecord::finder()->findAll();
			$this->DDDist->dataBind();
			
			$dataBarangRecord=$this->AssetBarangRecord;
			
			// Populates the input controls with the existing user data
			$kelHabisPakai = $dataBarangRecord->st_kel_habis_pakai;
			if($kelHabisPakai == '1') //barang habis pakai
			{		
				$this->modeJenis->SelectedValue = $kelHabisPakai;
				$this->DDsubHabisPakai->SelectedValue = $dataBarangRecord->st_sub_kel_habis_pakai;
				$this->hbsPakaiCtrl->Visible = true;
			}
			elseif($kelHabisPakai == '2') //barang tidak habis pakai
			{
				$this->modeJenis->SelectedValue = $kelHabisPakai;	
				$this->TdkHbsPakaiCtrl->Visible = true;
				
				$cekBergerak = $dataBarangRecord->st_bergerak;
				if($cekBergerak == '1') // barang habis pakai - bergerak
				{
					$this->modeTdkHabisPakai->SelectedValue = $cekBergerak;					
					$this->bergerakCtrl->Visible = true;
					
					$this->DDsubBergerak->SelectedValue = $dataBarangRecord->st_sub_bergerak;			
				}
				elseif($cekBergerak == '2') // barang habis pakai - tidak bergerak
				{
					$this->modeTdkHabisPakai->SelectedValue = $cekBergerak;
					$this->tdkBergerakCtrl->Visible = true;
					
					$cekMedis = $dataBarangRecord->st_medis;
					if($cekMedis == '1') //barang habis pakai - tidak bergerak - medis
					{			
						$this->modeTdkBergerak->SelectedValue = $cekMedis;	
					}
					elseif($cekMedis == '2') //barang habis pakai - tidak bergerak - non medis
					{		
						$this->modeTdkBergerak->SelectedValue = $cekMedis;	
						$this->nonMedisCtrl->Visible = true;
						
						$cekElektrik =  $dataBarangRecord->st_elektrik;
						if($cekElektrik == '1') //barang habis pakai - tidak bergerak - non medis - elektrikal
						{
							$this->modeNonMedis->SelectedValue = $cekElektrik;	
						}
						elseif($cekElektrik == '2') //barang habis pakai - tidak bergerak - non medis - non elektrikal
						{
							$this->modeNonMedis->SelectedValue = $cekElektrik;		
						}		
					}
				}
			}	
			
			$this->nmBarang->Text=$dataBarangRecord->nama;	
			
			if($this->AssetFotoBarangRecord)
			{
				$this->fotoBrg->ImageUrl=$this->Service->constructUrl('Asset.fotoBarangView',array('ID'=>$dataBarangRecord->id));
			}
			else
			{
				$this->fotoCtrl->Visible = false ;
			}
						
			
			$this->DDVen->SelectedValue=$dataBarangRecord->prod;
			$this->DDDist->SelectedValue=$dataBarangRecord->dist;
			$this->penyusutan->Text=$dataBarangRecord->penyusutan;
			$this->ket->Text=$dataBarangRecord->ket;
							
			$this->nmBarang->focus();					
		}
	}	
	
	public function modeJenisChanged($sender, $param)
	{
		$jnsBrg = $this->collectSelectionResult($this->modeJenis);
		if($jnsBrg == '1')
		{
			$this->hbsPakaiCtrl->Visible = true;
			$this->TdkHbsPakaiCtrl->Visible = false;			
		}
		elseif($jnsBrg == '2')
		{
			$this->hbsPakaiCtrl->Visible = false;
			$this->TdkHbsPakaiCtrl->Visible = true;			
		}
		
		$this->DDsubHabisPakai->DataSource=AssetKelHbsPakaiRecord::finder()->findAll();
		$this->DDsubHabisPakai->dataBind();	
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
			
		$this->modeTdkHabisPakai->SelectedIndex = -1 ;
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->tdkBergerakCtrl->Visible = false;
		$this->bergerakCtrl->Visible = false;
		$this->nonMedisCtrl->Visible = false;
	}
	
	public function modeTdkHabisPakaiChanged($sender, $param)
	{
		$subTdkHabis = $this->collectSelectionResult($this->modeTdkHabisPakai);
		if($subTdkHabis == '1')
		{
			$this->bergerakCtrl->Visible = true;
			$this->tdkBergerakCtrl->Visible = false;			
		}
		elseif($subTdkHabis == '2')
		{
			$this->bergerakCtrl->Visible = false;
			$this->tdkBergerakCtrl->Visible = true;			
		}
		
		$this->DDsubBergerak->DataSource=AssetKelBergerakRecord::finder()->findAll();
		$this->DDsubBergerak->dataBind();
		
		$this->modeTdkBergerak->SelectedIndex = -1 ;
		$this->modeNonMedis->SelectedIndex = -1 ;
		
		$this->nonMedisCtrl->Visible = false;
	}
	
	public function modeTdkBergerakChanged($sender, $param)
	{
		$subTdkbergerak = $this->collectSelectionResult($this->modeTdkBergerak);
		if($subTdkbergerak == '1')
		{			
			$this->nmBarang->focus();
			//$this->nmBarang->Text = '';
			$this->nonMedisCtrl->Visible = false;			
		}
		elseif($subTdkbergerak == '2')
		{			
			$this->nonMedisCtrl->Visible = true;			
		}
		
		$this->modeNonMedis->SelectedIndex = -1 ;
	}
	
	public function modeNonMedisChanged($sender, $param)
	{
		$nonMedis = $this->collectSelectionResult($this->modeNonMedis);
		if($nonMedis == '1')
		{
			//$this->nonElektrikalCtrl->Visible = false;			
		}
		elseif($nonMedis == '2')
		{
			//$this->elektrikalCtrl->Visible = false;
			//$this->nonElektrikalCtrl->Visible = true;			
		}
		$this->nmBarang->focus();
		//$this->nmBarang->Text = '';
	}
	
	
	public function fileUploaded($sender)
    {
        if($sender->HasFile) //jika foto barang disertakan
        {
			if($sender->FileSize >204800) //check ukuran file foto
			{
				$this->Result->Text="File Gagal Diupload, ukuran file tidak boleh lebih dari 200 Kb";
			}
			elseif($sender->FileType != 'image/jpeg' && $sender->FileType != 'image/jpg' && $sender->FileType != 'image/gif' && $sender->FileType != 'image/png') //check tipe file foto
			{
				$this->Result->Text="File Gagal Diupload, jenis file harus jpeg, jpg, gif atau png.";
			}
			else
			{		
				// Open the uploaded file
			   $file = fopen($sender->LocalName, "rb");
			
			   // Read in the uploaded file
			   $fileContents = fread($file, filesize($sender->LocalName));
				
				$this->updateDataBarang();
				
				if($this->AssetFotoBarangRecord)
				{
					$dataFotoBarangRecord=$this->AssetFotoBarangRecord;
				    
					//$newFoto=new AssetFotoBarangRecord;
					$dataFotoBarangRecord->id_brg=$this->getViewState('idBrg');
					$dataFotoBarangRecord->nama_file=$sender->FileName;
					$dataFotoBarangRecord->foto=$fileContents;			
					$dataFotoBarangRecord->tipe=$sender->FileType;	
					$dataFotoBarangRecord->save();
				}
				else
				{
					$newFoto=new AssetFotoBarangRecord;
					$newFoto->id_brg=$this->getViewState('idBrg');
					$newFoto->nama_file=$sender->FileName;
					$newFoto->foto=$fileContents;			
					$newFoto->tipe=$sender->FileType;	
					$newFoto->save();
				}	
								
				$this->clearViewState('idBrg');
				
				//$this->batalClicked();
				$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));
			}
        }
		else //jika foto barang tidak disertakan
		{
			$this->updateDataBarang();
			//$this->batalClicked();
			$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));	
		}
    }
	
	public function updateDataBarang()
	{
		$dataBarangRecord=$this->AssetBarangRecord;
		
		$idBrg = $dataBarangRecord->id;
		$this->setViewState('idBrg',$idBrg);		
		
		//$dataBarangRecord=new AssetAssetBarangRecord();			
		//$dataBarangRecord->id=$idBrg;
		$dataBarangRecord->nama=$this->nmBarang->Text;	
		$dataBarangRecord->prod=$this->DDVen->SelectedValue;
		$dataBarangRecord->dist=$this->DDDist->SelectedValue;
		$dataBarangRecord->penyusutan=$this->penyusutan->Text;
		$dataBarangRecord->ket=$this->ket->Text;
		
		$kelHabisPakai = $this->collectSelectionResult($this->modeJenis);
		if($kelHabisPakai == '1') //barang habis pakai
		{		
			$dataBarangRecord->st_kel_habis_pakai=$kelHabisPakai;
			$dataBarangRecord->st_sub_kel_habis_pakai=$this->DDsubHabisPakai->SelectedValue;
		}
		elseif($kelHabisPakai == '2') //barang tidak habis pakai
		{
			$dataBarangRecord->st_kel_habis_pakai=$kelHabisPakai;		
			
			$cekBergerak = $this->collectSelectionResult($this->modeTdkHabisPakai);
			if($cekBergerak == '1') // barang habis pakai - bergerak
			{
				$dataBarangRecord->st_bergerak=$cekBergerak;	
				$dataBarangRecord->st_sub_bergerak=$this->DDsubBergerak->SelectedValue;				
			}
			elseif($cekBergerak == '2') // barang habis pakai - tidak bergerak
			{
				$dataBarangRecord->st_bergerak=$cekBergerak;
				
				$cekMedis = $this->collectSelectionResult($this->modeTdkBergerak);
				if($cekMedis == '1') //barang habis pakai - tidak bergerak - medis
				{			
					$dataBarangRecord->st_medis=$cekMedis;		
				}
				elseif($cekMedis == '2') //barang habis pakai - tidak bergerak - non medis
				{			
					$dataBarangRecord->st_medis=$cekMedis;	
					
					$cekElektrik = $this->collectSelectionResult($this->modeNonMedis);
					if($cekElektrik == '1') //barang habis pakai - tidak bergerak - non medis - elektrikal
					{
						$dataBarangRecord->st_elektrik=$cekElektrik;			
					}
					elseif($cekElektrik == '2') //barang habis pakai - tidak bergerak - non medis - non elektrikal
					{
						$dataBarangRecord->st_elektrik=$cekElektrik;		
					}		
				}
			}
		}		
		
		$dataBarangRecord->save();	
	}
		
	public function simpanClicked($sender,$param)
	{		
		if($this->IsValid)
		{
			$this->fileUploaded($this->aplod);
		}	
	}
	
	public function batalClicked($sender,$param)
	{		
		//$this->Response->reload();		
		$this->Response->redirect($this->Service->constructUrl('Asset.masterBarang'));		
	}
		
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('Asset.menuAsset'));		
	}
}
?>
