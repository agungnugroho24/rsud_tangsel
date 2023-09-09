<?php

//Prado::using('Application.modules.PWCWindow.PWCWindow');

class fraksiJasmed extends SimakConf
{   
		
	public function onInit($param)
	 {		
		parent::onInit($param);
		
		$tmpVar=$this->authApp('9');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	 
			
	public function onPreRender($param)
	{				
		parent::onPreRender($param);				
		if(!$this->IsPostBack && !$this->IsCallBack)
        {   
			$noTrans = $this->Request['noTrans'];
			$index = $this->Request['index'];
			$nmTableTmp = $this->Request['nmTableTmp'];
			
			//$this->jml->Text = $nmTableTmp;
			$this->DDjnsTindakan->Focus();
			$this->uploadPanel->Display = 'None';
			$this->uploadBtn->Enabled = false;
			
			$this->selesaiBtn->Visible = false;
		}	
		
		/*
		if($this->DDjnsTindakan->SelectedValue != '' && $this->DDNamaTindakan->SelectedValue != '')
			$this->prosesBtn->Enabled = true;
		else
			$this->prosesBtn->Enabled = false;
		*/	
		
		if($this->getViewState('levelAktif'))
		{
			if($this->getViewState('levelAktif') > intval($this->jml->Text))
			{
				$this->uploadBtn->Enabled = true;	
				$this->uploadBtn->Focus();
			}	
			else
				$this->uploadBtn->Enabled = false;		
		}
		/*
		if($this->jml->Text != '' && $this->jmlFraksiLevel1->Text != '' )
		{
			$this->tes->Text = '';
			
			for($i=1; $i<=$this->jml->Text; $i++)
			{
				if($this->getViewState('dataFraksiLevel'.$i))
				{
					foreach($this->getViewState('dataFraksiLevel'.$i) as $row)
					{
						$this->tes->Text .= $row['id'].'-'.$row['nama'].'-'.$row['persentase'].'-'.$row['jmlFraksi'].'<br/>';
					}
					
					$this->tes->Text .= '<br/>';
				}
			}
		}
		*/	
    }		
	
	
	public function DDjnsTindakanChanged($sender,$param)
	{
		$idJnsTdk = $this->DDjnsTindakan->SelectedValue;
		
		if($idJnsTdk != '')
		{
			if($idJnsTdk > 3)
			{
				$this->DDNamaTindakan->SelectedValue = 'empty';
				$this->DDNamaTindakan->Enabled = false;
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->jml->getClientID().'.focus();');
			}
			else
			{
				if($idJnsTdk == 1) //tindakan rawat jalan/inap
					$sql = "SELECT id,nama FROM tbm_nama_tindakan ORDER BY nama";
				elseif($idJnsTdk == 2) //tindakan lab
					$sql = "SELECT kode AS id,nama FROM tbm_lab_tindakan ORDER BY nama";
				elseif($idJnsTdk == 3) //tindakan rad
					$sql = "SELECT kode AS id,nama FROM tbm_rad_tindakan ORDER BY nama";
				
				$this->DDNamaTindakan->DataSource = $this->queryAction($sql,'S');
				$this->DDNamaTindakan->dataBind();	
				
				$this->DDNamaTindakan->Enabled = true;
				$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDNamaTindakan->getClientID().'.focus();');
			}
		}
		else
		{
			$this->DDNamaTindakan->SelectedValue = 'empty';
			$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->DDjnsTindakan->getClientID().'.focus();');
		}	
		
	}
	
	
	public function DDNamaTindakanChanged($sender,$param)
	{
		$this->getPage()->getClientScript()->registerEndScript('','document.all.'.$this->jml->getClientID().'.focus();');
	}
	
  	public function prosesClicked($sender,$param)
	{	
		if($this->Page->IsValid) 
		{
			$jmlLevel = $this->jml->Text;
			$jmlFraksiLevel1 = $this->jmlFraksiLevel1->Text;
			
			if(intval($jmlLevel) > 0 && intval($jmlFraksiLevel1) > 0)
			{
				$this->cariPanel->Enabled = false;
				$this->uploadPanel->Display = 'Dynamic';
				//$this->uploadBtn->Enabled = true;
				$this->bindData();
			}
			else
			{
				$this->getPage()->getClientScript()->registerEndScript('','alert(\'Jumlah yang diisikan tidak sesuai !\'); document.all.'.$this->jml->getClientID().'.focus();');
			}	
		}
	}
	
	public function cariPanelCallback($sender,$param)
	{	
		//$this->cariPanel->render($param->getNewWriter());
		$this->cariPanel->render($param->getNewWriter());
	}
	
	public function prosesCallback($sender,$param)
	{	
		//$this->cariPanel->render($param->getNewWriter());
		$this->uploadPanel->render($param->getNewWriter());
	}
	
	public function repeaterDataBound2($sender,$param)
    {
		$jmlLevel = intval($this->jml->Text);
		
        $item = $param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			$item->level->Text = $item->DataItem['level'];
			$item->sublevel->Text = $item->DataItem['sublevel'];
			
			if($item->DataItem['nama'])
				$item->level1Lbl->Text = $item->DataItem['nama'].' - '.'Fraksi '.$item->DataItem['id'];
			else
				$item->level1Lbl->Text = 'Fraksi '.$item->DataItem['id'];	
				
			$item->tes->Text = $item->DataItem['id'];	
			$item->id->Text = $item->DataItem['idPerLevel'];	
			
			if($jmlLevel>1)
				$item->jmlFraksi->Enabled = true;
			else
				$item->jmlFraksi->Enabled = false;
				
        	if($this->getViewState('levelAktif'))
			{
				if($this->getViewState('levelAktif') == intval($this->jml->Text))
					$item->jmlFraksi->Enabled = false;	
			}	
		}
    }
	
	public function prosesPembagianFraksi($sender,$param)
    {
		//$sender->Enabled = false;
		$jmlLevel = intval($this->jml->Text);
		$level = $sender->CommandParameter; 
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
		
		
		if($this->Page->IsValid) 
		{
			$idPerLevel = 1;
			
			foreach($this->Repeater->Items[$index]->Repeater2->getItems() as $item)
			{
				if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
				{
					for($i=1; $i<=$item->jmlFraksi->Text; $i++)
					{
						//$data[] = array('level'=>$level+1,'sublevel'=>$index+1,'id'=>$i,'nama'=>$item->label->Text,'persentase'=>$item->persentase2->Text,'jmlFraksi'=>$item->jmlFraksi->Text);
						$data[] = array('level'=>$level+1,'sublevel'=>$item->id->Text,'idPerLevel'=>$idPerLevel,'id'=>$i,'nama'=>$item->label->Text,'persentase'=>$item->persentase2->Text,'jmlFraksi'=>$item->jmlFraksi->Text);
						
						$idPerLevel++;
					}	
				}	
				
				$item->label->Enabled = false;
				$item->persentase2->Enabled = false;
				$item->jmlFraksi->Enabled = false;
			}
			
			$this->setViewState('dataFraksiLevel'.$level,$data);
			$this->setViewState('levelAktif',$level+1);		
			
			$this->Repeater->Items[$index]->prosesPembagianFraksiBtn->Enabled = false;
			
			if($level < $jmlLevel)
			{
				$this->Repeater->Items[$index+1]->prosesPembagianFraksiBtn->Enabled = true;
				$this->Repeater->Items[$index+1]->Repeater2->DataSource = $data;
				$this->Repeater->Items[$index+1]->Repeater2->dataBind();
			}			
			/*
			$this->tes->Text = '';
			foreach($data as $row)
			{
				$this->tes->Text .= $row['id'].'-'.$row['nama'].'-'.$row['persentase'].'-'.$row['jmlFraksi'].'<br/>';
			}
			*/
			//$this->tes->Text = $data;
		}
	}
	
	public function bindData2($sender,$param)
    {
		$level = $sender->CommandParameter; 
		$item = $sender->getNamingContainer(); 
		$index = $this->Repeater->DataKeys[$item->getItemIndex()];
			
		if($this->Page->IsValid) 
		{
			$sender->Enabled = false;
			
			if($this->getViewState('jmlFraksiLevel'.$level))
			{	
				$jmlFraksiLevel1 = $this->getViewState('jmlFraksiLevel1');
				foreach($jmlFraksiLevel1 as $row)
				{
					for($i=1; $i<=$this->Repeater->Items[$index]->persentase->Text; $i++)
					{
						$data[] = array('level'=>$level+1,'sublevel'=>$row['id'],'id'=>$i,'nama'=>$i);
					}	
				}
			}
			else
			{
				for($i=1; $i<=$this->Repeater->Items[$index]->persentase->Text; $i++)
				{
					$data[] = array('level'=>$level+1,'sublevel'=>$index+1,'id'=>$i,'nama'=>$i);
				}
				
				$this->setViewState('jmlFraksiLevel'.$level,$data);
			}
			
			
			$this->Repeater->Items[$index]->prosesPembagianFraksiBtn->Enabled = true;
			$this->Repeater->Items[$index]->Repeater2->DataSource = $data;
			$this->Repeater->Items[$index]->Repeater2->dataBind();
		}
		else
		{
			$this->Repeater->Items[$index]->persentase->Focus();
		}	
	}
	
	public function repeaterDataBound($sender,$param)
    {		
        $item=$param->Item;
        if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
        {
			//$idTdk = $item->DataItem['id_tindakan'];
			
			$item->no->Text = $item->DataItem['id'];
			//$item->uploadBtnTrigger->Attributes->onclick = "{$item->foto->getCallbackJavascript()}; return false;";
			//$item->HtmlArea->Text = $item->DataItem['catatan'];
			
        }
    }
	
	
	public function bindData()
    {
		$level = 1;
		
		for($i=1; $i<=$this->jml->Text; $i++)
		{
			$dataLevel[] = array('level'=>$level,'id'=>$i,'nama'=>$i);
		}
		
		$this->Repeater->DataSource = $dataLevel;
		$this->Repeater->dataBind();
		
		$this->setViewState('dataLevel',$dataLevel);
		
		$index = 0;
		
		for($i=1; $i<=$this->jmlFraksiLevel1->Text; $i++)
		{
			$data[] = array('level'=>$level,'sublevel'=>'0','idPerLevel'=>$i,'id'=>$i,'nama_level'=>$i);
		}
				
		$this->Repeater->Items[$index]->prosesPembagianFraksiBtn->Enabled = true;
		$this->Repeater->Items[$index]->Repeater2->DataSource = $data;
		$this->Repeater->Items[$index]->Repeater2->dataBind();
	}
	
	
	public function selesaiClicked($sender,$param)
    {
		$noTrans = $this->Request['noTrans'];
		$index = $this->Request['index'];
		
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT id FROM $nmTable WHERE nama = ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) //jika masih ada file yg kosong, tapi user jadi mengupload untuk file tersebut
			{
				foreach($arr as $row)
				{
					$id = $row['id'];
					
					$sql = "DELETE FROM $nmTable WHERE id = '$id'";
					$this->queryAction($sql,'C');
				}	
			}	
		}
		
		$this->getPage()->getClientScript()->registerEndScript
		('','window.parent.maskContent(); window.parent.modalCallback(\''.$index.'\',\''.$nmTable.'\'); jQuery.FrameDialog.closeDialog();');		
	}
	
	public function batalClicked($sender,$param)
    {
		$this->Response->Reload();	
	}
	
	public function uploadBtnClicked($sender,$param)
    {
		foreach($this->Repeater->getItems() as $item) {
			switch($item->getItemType()) {
				case "Item":					
					$this->getPage()->getCallbackClient()->click( $item->uploadBtnTrigger->getClientID() );
					break;
				case "AlternatingItem":					
					$this->getPage()->getCallbackClient()->click( $item->uploadBtnTrigger->getClientID() );
					break;
				default:
					break;
			}
		}
		
		$this->getPage()->getClientScript()->registerEndScript
				('','unmaskContent();');	
	}
	
	public function uploadTempFoto($foto,$result,$tipeFile,$noUrut,$uploadPanel)
    {
		$noTrans = $this->Request['noTrans'];
		
		if($foto->ErrorCode == 2)//kelebihan ukuran
		{
			$result->Text = 'File Gagal Diupload, ukuran file tidak boleh lebih dari 300 kilobyte';
			$uploadPanel->Visible = true;
		}		
		elseif($foto->HasFile) //ada file yg akan di upload
		{
			if($tipeFile == '1') //tipe foto
			{
				if($foto->FileType != 'image/jpeg' && $foto->FileType != 'image/jpg' && $foto->FileType != 'image/gif' && $foto->FileType != 'image/png')
				{
					$result->Text = "Tipe file harus jpeg, jpg, gif atau png.";
					$uploadPanel->Visible = true;
				}	
				else
				{
					$result->Text = $noTrans.'-'.$foto->FileName;
					$nmFile = $noTrans.'-'.$noUrut.'-'.$foto->FileName;
					
					//$url = 'protected\pages\Rad\foto\\'.$nmFile;
					//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$nmFile;
					$url = 'protected/pages/Rad/foto//'.$nmFile;
					$urlTmp ='protected/pages/Rad/foto/tmp//'.$nmFile;
					
					if(file_exists($urlTmp))
					{
						unlink($urlTmp);
					}
					
					$this->setViewState('file'.$noUrut,$foto);
					$this->setViewState('url'.$noUrut,$url);
					$this->setViewState('urlTmp'.$noUrut,$urlTmp);
					$foto->saveAs($urlTmp);
					
					if($this->getViewState('nmTable'))
					{
						$nmTable = $this->getViewState('nmTable');
					
						$sql = "UPDATE $nmTable SET nama = '$nmFile', tipe = '$tipeFile' WHERE id = '$noUrut' ";
						$this->queryAction($sql,'C');
					}
					$uploadPanel->Visible = false;
					//$param->IsValid=true;
					//$this->uploadProses($noUrut,$noTrans,$url,$urlTmp,$nmFile);	
				}
			}
			elseif($tipeFile == '2') //tipe dokumen
			{
				if($foto->FileType != 'application/msword' && $foto->FileType != 'application/pdf')
				{
					$result->Text ="Tipe file harus doc atau pdf.";
					$uploadPanel->Visible = true;
				}	
				else
				{
					$result->Text = $noTrans.'-'.$foto->FileName;
					$nmFile = $noTrans.'-'.$noUrut.'-'.$foto->FileName;
					
					//$url = 'protected\pages\Rad\foto\\'.$nmFile;
					//$urlTmp ='protected\pages\Rad\foto\tmp\\'.$nmFile;
					$url = 'protected/pages/Rad/foto//'.$nmFile;
					$urlTmp ='protected/pages/Rad/foto/tmp//'.$nmFile;
					
					if(file_exists($urlTmp))
					{
						unlink($urlTmp);
					}
					
					$this->setViewState('file'.$noUrut,$foto);
					$this->setViewState('url'.$noUrut,$url);
					$this->setViewState('urlTmp'.$noUrut,$urlTmp);
					$foto->saveAs($urlTmp);
					
					if($this->getViewState('nmTable'))
					{
						$nmTable = $this->getViewState('nmTable');
					
						$sql = "UPDATE $nmTable SET nama = '$nmFile', tipe = '$tipeFile' WHERE id = '$noUrut' ";
						$this->queryAction($sql,'C');
					}
					
					$uploadPanel->Visible = false;
				}
			}
		}
		else
		{
			$result->Text ="Belum ada file yang dipilih !";
		}		
		/*
		if($this->getViewState('nmTable'))
		{
			$nmTable = $this->getViewState('nmTable');
			
			$sql = "SELECT GROUP_CONCAT(id SEPARATOR ', ' ) AS id FROM $nmTable WHERE nama = ''";
			$arr = $this->queryAction($sql,'S');
			
			if(count($arr) > 0) //jika masih ada file yg belum di upload
			{
				foreach($arr as $row)
				{
					$id = $row['id'];
				}
			
				
			}
			
			$this->getPage()->getClientScript()->registerEndScript('', 'alert(\'tret \')');			
		}	
			*/			
	}	
	
	public function uploadProses($i,$idBuku)
	{
		$data = BiblioRecord::finder()->findByPk($idBuku);
		
		$uploadId = 'foto'.$i;
			
		if($i == '1')
		{
			$field = 'image';
		}
		else
		{
			$field = 'image'.$i;
		}
		
		if($this->getViewState($uploadId) == '1')
		{
			$url = $this->getViewState('url'.$i);
			$urlTmp = $this->getViewState('urlTmp'.$i);				
			copy($urlTmp,$url);				
			unlink($urlTmp);
			$data->$field = $this->getViewState('fileName'.$i);
			$this->setViewState($uploadId,'0');
		}
		
		$data->save();
	}
	
	public function fileUploaded($sender,$param)
    {
		$jnsTdk = $this->DDjnsTindakan->SelectedValue;
		$nmTdk = $this->DDNamaTindakan->SelectedValue;
		
		//$this->tes->Text = '';
		
		$sql = "SELECT id FROM tbm_fraksi_jasmed WHERE jns_tindakan = '$jnsTdk' AND id_tindakan = '$nmTdk' ";
		$arr = $this->queryAction($sql,'S');
		
		if(count($arr) >1)
		{
			$sql = "DELETE FROM tbm_fraksi_jasmed WHERE jns_tindakan = '$jnsTdk' AND id_tindakan = '$nmTdk' ";
			$arr = $this->queryAction($sql,'C');
			
			for($i=0; $i<$this->jml->Text; $i++)
			{
				foreach($this->Repeater->Items[$i]->Repeater2->getItems() as $item)
				{
					if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
					{
						$id = $item->id->Text;
						$level = $item->level->Text;
						$sublevel = $item->sublevel->Text;
						$nama = $item->label->Text;
						$persentase = $item->persentase2->Text;
						$jmlFraksi = $item->jmlFraksi->Text;
						
						$data = new FraksiJasmedRecord();												
						$data->jns_tindakan = $jnsTdk;
						$data->id_tindakan = $nmTdk;
						$data->jml_level = intval($this->jml->text);
						$data->level = $level;
						$data->sub_level = $sublevel;
						$data->id_per_level = $id;
						$data->nama = $nama;	
						$data->persentase = $persentase;
						$data->jml_fraksi = $jmlFraksi;			
						$data->Save();
						
						//$this->tes->Text .= $level.'-'.$sublevel.'-'.$id.'-'.$nama.'-'.$persentase.'-'.$jmlFraksi.'<br/>';
					}	
				}
				
				//$this->tes->Text .= '<br/>';
			}
		}
		else
		{
			for($i=0; $i<$this->jml->Text; $i++)
			{
				foreach($this->Repeater->Items[$i]->Repeater2->getItems() as $item)
				{
					if($item->ItemType==='Item' || $item->ItemType==='AlternatingItem')
					{
						$id = $item->id->Text;
						$level = $item->level->Text;
						$sublevel = $item->sublevel->Text;
						$nama = $item->label->Text;
						$persentase = $item->persentase2->Text;
						$jmlFraksi = $item->jmlFraksi->Text;
						
						
						$data = new FraksiJasmedRecord();												
						$data->jns_tindakan = $jnsTdk;
						$data->id_tindakan = $nmTdk;
						$data->jml_level = intval($this->jml->text);
						$data->level = $level;
						$data->sub_level = $sublevel;
						$data->id_per_level = $id;
						$data->nama = $nama;	
						$data->persentase = $persentase;
						$data->jml_fraksi = $jmlFraksi;			
						$data->Save();
						
						//$this->tes->Text .= $level.'-'.$sublevel.'-'.$id.'-'.$nama.'-'.$persentase.'-'.$jmlFraksi.'<br/>';
					}	
				}
				
				//$this->tes->Text .= '<br/>';
			}
		}
		
		
		$this->getPage()->getClientScript()->registerEndScript
		('','unmaskContent();
			jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_information">Pembagian Fraksi Jasmed telah dimasukan dalam database.</p>\',timeout: 600000,dialog:{
				modal: true,
			buttons: {
				"OK": function() {
					jQuery( this ).dialog( "close" );
					maskContent();
					konfirmasi(\'tidak\');
				}
			}
		}});');	
	}
	
	public function prosesKonfirmasi($sender,$param)
	{
		$mode = $param->CallbackParameter->Id;
		
		//$this->getPage()->getClientScript()->registerEndScript('','alert('.$mode.')');	
				
		if($mode == 'ya')
		{	
			$this->Page->CallbackClient->focus($this->nama);
			$this->getPage()->getClientScript()->registerEndScript('','unmaskContent();');
		}
		else
		{
			$this->Response->redirect($this->Service->constructUrl('Admin.fraksiJasmed'));
		}
	}
}

?>
