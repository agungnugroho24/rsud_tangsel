<?php
class BayarKasirApotik extends SimakConf
{   
	
	public function onPreRender($param)
	 {		
		parent::onPreRender($param);
		$tmpVar=$this->authApp('2');
		if($tmpVar == "False")//Bila tidak ada hak utk modul aplikasi pendaftaran
			$this->Response->redirect($this->Service->constructUrl('login'));
	 }	
	
	public function onPreRenderComplete($param)
	{				
		parent::onPreRenderComplete($param);		
			
		if(!$this->IsPostBack)
		{					
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showBayar->Visible=false;
			$this->cetakBtn->Enabled=false;
			
		}		
		
		$purge=$this->Request['purge'];
		
		if($purge =='1'	)
		{			
			if($this->getViewState('nmTable'))
			{		
				$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
				$this->UserGrid->DataSource='';
				$this->UserGrid->dataBind();
				$this->clearViewState('nmTable');//Clear the view state				
			}
			
			$this->clearViewState('tmpJml');
			$this->getViewState('sisa');
			$this->bayar->Text;			
			$this->clearViewState('nmTable');
			$this->clearViewState('cm');
			$this->clearViewState('notrans');
			$this->clearViewState('nama');
			$this->clearViewState('dokter');
			$this->clearViewState('klinik');
			$this->notrans->Enabled=true;		
			$this->notrans->Text='';
			$this->errByr->Text='';
			$this->errMsg->Text='';
			$this->bayar->Text='';
			$this->bhp->Text='';
			$this->id_tindakan->Text='';
			$this->notrans->Focus();
			$this->showSecond->Visible=false;
			$this->showBayar->Visible=false;
			$this->Response->redirect($this->Service->constructUrl('Tarif.BayarKasirIgd',array('goto'=>'1')));
	
		}	
		
		$goto=$this->Request['goto'];	
		
		if($goto == '1')
		{
			$this->notrans->Focus();
		}
    }
	
	public function prosesClicked()
    {		
		if (!$this->getViewState('nmTable'))
		{
		$nmTable = $this->setNameTable('nmTable');
		$sql="CREATE TABLE $nmTable (id INT (2) auto_increment, 
									 nama VARCHAR(30) NOT NULL,
									 id_tdk VARCHAR(4) NOT NULL,
									 bhp INT(11) NOT NULL,
									 total INT(11) NOT NULL,
									 jml INT(11) NOT NULL, 									 
									 PRIMARY KEY (id)) ENGINE = MEMORY";
		
		$this->queryAction($sql,'C');//Create new tabel bro...
		$item=$this->id_tindakan->Text;
		$sql="SELECT b.nama AS nama, 
					 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
			  		 FROM tbm_tarif_tindakan a, 
			         	  tbm_nama_tindakan b 
			  		 WHERE a.id='$item' AND a.id=b.id";
		$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
		$bhp=$this->bhp->Text;
		$total= $tmpTarif->total;
		$nama=$tmpTarif->nama;	
		$jml=$total+$bhp;
		
		$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('$nama','$bhp','$total','$jml','$item')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...
		
		$sql="SELECT * FROM $nmTable ORDER BY id";
		$this->UserGrid->DataSource=$this->queryAction($sql,'S');//Insert new row in tabel bro...
		$this->UserGrid->dataBind();
		$this->bayar->Enabled=true;
		$this->bayarBtn->Enabled=true;						
		}
		else
		{
			$nmTable = $this->getViewState('nmTable');
			$item=$this->id_tindakan->Text;
			$sql="SELECT b.nama AS nama, 
						 (a.biaya1 + a.biaya2 + a.biaya3 + a.biaya4) AS total 
						 FROM tbm_tarif_tindakan a, 
							  tbm_nama_tindakan b 
						 WHERE a.id='$item' AND a.id=b.id";
			$tmpTarif = TarifTindakanRecord::finder()->findBySql($sql);					 
			$bhp=$this->bhp->Text;
			$total= $tmpTarif->total;
			$nama=$tmpTarif->nama;
			$jml=$total+$bhp;
			
			$sql="INSERT INTO $nmTable (nama,bhp,total,jml,id_tdk) VALUES ('$nama','$bhp','$total','$jml','$item')";
			$this->queryAction($sql,'C');//Insert new row in tabel bro...			

			$sql="SELECT * FROM $nmTable ORDER BY id";
			$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
			$this->UserGrid->DataSource=$arrData;
            $this->UserGrid->dataBind();								
		}	

		if($this->getViewState('tmpJml')){
			$t = (int)$this->getViewState('tmpJml') + $jml;
			$this->setViewState('tmpJml',$t);
		}else{
			$this->setViewState('tmpJml',$jml);
		}	
		$this->jmlShow->Text='Rp '.number_format($this->getViewState('tmpJml'),2,',','.');
		$this->id_tindakan->Text='';
		$this->bhp->Text='';
		$this->showBayar->Visible=true;
		$this->id_tindakan->Focus();	
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
				
				$sql="SELECT jml FROM $nmTable WHERE id='$ID'";
				$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
				foreach($arrData as $row)
				{
					$n=$row['jml'];					
					$t = ($this->getViewState('tmpJml') - $n);						
					$this->jmlShow->Text='Rp '.number_format($t,2,',','.');
					$this->setViewState('tmpJml',$t);
				}
				
				$sql = "DELETE FROM $nmTable WHERE id='$ID'";
				$this->queryAction($sql,'C');								
				
				$sql="SELECT * FROM $nmTable ORDER BY id";
				$arrData=$this->queryAction($sql,'S');//Select row in tabel bro...
				$this->UserGrid->DataSource=$arrData;
				$this->UserGrid->dataBind();		
				$this->bayar->Text='';											
				$this->id_tindakan->focus();
			//}	
			
		//}	
    }
	
	public function bayarClicked($sender,$param)
    {
		if($this->bayar->Text >= $this->getViewState('tmpJml'))
		{
			$hitung = TPropertyValue::ensureFloat($this->bayar->Text)-TPropertyValue::ensureFloat($this->getViewState('tmpJml'));
			$this->sisaByr->Text='Rp ' . $hitung;
			$this->setViewState('sisa',$hitung);
			$this->id_tindakan->Enabled=false;
			$this->bhp->Enabled=false;
			$this->tambahBtn->Enabled=false;
			$this->cetakBtn->Enabled=true;	
			$this->cetakBtn->Focus();	
			$this->errByr->Text='';		
			$this->setViewState('stDelete','1');	
		}
		else
		{
			$this->errByr->Text='Jumlah pembayaran kurang';	
			$this->bayar->Focus();
			$this->cetakBtn->Enabled=false;
		}
	}
	
	public function batalClicked($sender,$param)
    {		
		if($this->getViewState('nmTable'))
		{		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table						
			$this->UserGrid->DataSource='';
            $this->UserGrid->dataBind();
			$this->clearViewState('nmTable');//Clear the view state				
		}
		
		$this->clearViewState('tmpJml');
		$this->notrans->Enabled=true;		
		$this->notrans->Text='';
		$this->errByr->Text='';
		$this->errMsg->Text='';
		$this->bayar->Text='';
		$this->bhp->Text='';
		$this->id_tindakan->Text='';
		$this->notrans->Focus();
		$this->showSecond->Visible=false;
		$this->showBayar->Visible=false;
	}
		
	public function checkRegister($sender,$param)
    {
        // valid if the username is not found in the database
        if(IgdRecord::finder()->findByPk($this->notrans->Text))
		{			
			$tmp = $this->notrans->Text;
			$sql = "SELECT b.nama AS cm,
						   c.nama AS dokter,						   
						   b.cm AS cr_masuk 
						   FROM tbt_igd a, 
								tbd_pasien b, 
								tbd_pegawai c								
						   WHERE a.no_trans='$tmp'
						         AND a.cm=b.cm
								 AND a.dokter=c.id ";
			$tmpPasien = IgdRecord::finder()->findBySql($sql);
			$this->nama->Text= $tmpPasien->cm;
			$this->dokter->Text= $tmpPasien->dokter;					 			
			$this->setViewState('cm',$tmpPasien->cr_masuk);
			$this->setViewState('nama',$tmpPasien->cm);
			$this->setViewState('dokter',$tmpPasien->dokter);			
			$this->setViewState('notrans',$this->notrans->Text);
			$this->showSecond->Visible=true;
			$this->notrans->Enabled=false;
			$this->errMsg->Text='';			
			$this->id_tindakan->Enabled=true;
			$this->bhp->Enabled=true;
			$this->tambahBtn->Enabled=true;
			$this->id_tindakan->Focus();
		}
		else
		{
			$this->showFirst->Visible=true;
			$this->showSecond->Visible=false;
			$this->errMsg->Text='No. Register tidak ada!!';
			$this->notrans->Focus();
		}
    }	
	
	public function checkRM($sender,$param)
    {   		
		$param->IsValid=TarifTindakanRecord::finder()->findByPk($this->id_tindakan->Text);		
    }
	
	public function pagerCreated($sender,$param)
	{
		$param->Pager->Controls->insertAt(0,'Page: ');
	}		
	
	public function keluarClicked($sender,$param)
	{		
		if($this->getViewState('nmTable')){		
			$this->queryAction($this->getViewState('nmTable'),'D');//Droped the table
			$this->clearViewState('nmTable');//Clear the view state	
			$this->clearViewState('tmpJml');
		}
		$this->Response->redirect($this->Service->constructUrl('Home'));		
	}	
	
	public function cetakClicked($sender,$param)
    {		
		$sisaByr=$this->getViewState('sisa');
		$jmlBayar=$this->bayar->Text;
		$jmlTagihan=$this->getViewState('tmpJml');
		$table=$this->getViewState('nmTable');
		$cm=$this->getViewState('cm');
		$notrans=$this->getViewState('notrans');
		$nama=$this->getViewState('nama');
		$dokter=$this->getViewState('dokter');
		$klinik=$this->getViewState('klinik');
		$operator=$this->User->IsUserName;
		$nipTmp=$this->User->IsUserNip;
		$sql="SELECT * FROM $table ORDER BY id";
		$arrData=$this->queryAction($sql,'R');//Select row in tabel bro...				
		foreach($arrData as $row)
		{
			$transIgd= new KasirIgdRecord();
			$transIgd->no_trans=$notrans;
			$transIgd->id_tindakan=$row['id_tdk'];
			$transIgd->tgl=date('y-m-d');
			$transIgd->waktu=date('G:i:s');
			$transIgd->operator=$operator;
			$transIgd->bhp=$row['bhp'];
			$transIgd->tarif=$row['total'];
			$transIgd->total=$row['jml'];
			$transIgd->st_flag='0';
			$transIgd->Save();
		}		
		$this->Response->redirect($this->Service->constructUrl('Tarif.cetakKwtIgd',array('notrans'=>$notrans,'cm'=>$cm,'nama'=>$nama,'dokter'=>$dokter,'klinik'=>$klinik,'jmlTagihan'=>$jmlTagihan,'jmlBayar'=>$jmlBayar,'sisa'=>$sisaByr,'table'=>$table)));
		
	}
}
?>
