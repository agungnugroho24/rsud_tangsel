<?php
class gantiObat extends SimakConf
{	
	public function onInit($param)
	{		
            parent::onInit($param);
            $tmpVar=$this->authApp('2');
            
            if($tmpVar == "False")
                $this->Response->redirect($this->Service->constructUrl('login'));
	}
	 
	public function onPreRender($param)
	{
            parent::onPreRender($param);
            if(!$this->IsPostBack && !$this->IsCallBack)  
            {			
                if($this->Request['id'] != '')
                {
                    $no_trans = $this->Request['id'];
                    $this->txt2->Text = $no_trans; 
                    //$data = ObatJualRecord::finder()->findByno_trans($this->Request['id']);

                    $sql = "SELECT * FROM tbt_obat_jual WHERE no_trans='".$no_trans."'";
                    $arr = $this->queryAction($sql,'S');
                    $arr_result = Array();

                    foreach($arr as $row)
                    {
                        $no_trans = $row['no_trans'];
                        $no_reg = $row['no_reg'];
                        $id_obat = $row['id_obat'];
                        $jumlah_obat = $row['jumlah'];
                        $keterangan_obat = $row['keterangan_obat'];
                        
                        $sql1 = "SELECT nama, tipe FROM tbm_obat WHERE kode = '".$id_obat."'";
                        $arr1 = $this->queryAction($sql1,'S');
                        
                        foreach( $arr1 as $row1 )
                        {
                            $nmObat = $row1['nama'];
                            
                            if( $row1['tipe'] == '2' )
                                $this->tipeObat->Text = 'Generik';
                            
                            else 
                                $this->tipeObat->Text = 'Non-Generik';
                        }
                        
                        // -- Meng-assign kelompok racikan
                        
                        // Kalau 0 berarti non-racikan
                        if( $row['id_kel_racik'] == '0' )
                            $this->txt4->Text = "Non-Racikan";
                        
                        else
                            $this->txt4->Text = "Racikan ".$row['$id_kel_racik'];
                    }
                    
                    // Mengassign ke dalam textbox
                    $this->cariNama->Text = $nmObat;
                    $this->jumlahObat->Text = $jumlah_obat;
                    $this->keterangan->Text = $keterangan_obat;
                    $this->kodeObat->Text=$id_obat;
                }
            }
	}	
	
	public function suggestNames($sender,$param) {
            // Get the token
            $token=$param->getToken();
            
            // Sender is the Suggestions repeater
            $sender->DataSource=$this->getDummyData($token);
            $sender->dataBind();          
        }
 
        public function suggestionSelected1($sender,$param) 
        {
            $id = $sender->Suggestions->DataKeys[$param->selectedIndex];
            
            $sql="SELECT kode, tipe from tbm_obat WHERE nama='".$id."'";
            $arr = $this->queryAction($sql,'S');
            foreach($arr as $row)
            {
                    $kode = $row['kode'];
                    if( $row['tipe'] == '2' )
                        $this->tipeObat->Text = 'Generik';
                            
                    else 
                        $this->tipeObat->Text = 'Non-Generik';
            }
            if($id)
            {
                    $this->kodeObat->Text=$kode;
            }
            
            else
            {
                    $this->kodeObat->Text = '';
            }
            

        }
        
    
    public function getDummyData($token){
                $sql = "SELECT nama FROM tbm_obat WHERE tbm_obat.nama LIKE '$token%' GROUP BY tbm_obat.kode ORDER BY tbm_obat.nama ASC "; 
                $arr = $this->queryAction($sql,'S');
		return $arr;
    }
	
    public function inputChanged($sender, $param){
    	$nmObat = trim($this->cariNama->Text);
    	$sql = "SELECT * FROM tbm_obat WHERE nama='".$nmObat."'";
    	$arr = $this->queryAction($sql,'S');
    	
        foreach($arr as $row)
        {
                $kode = $row['kode'];
                if( $row1['tipe'] == '2' )
                    $this->tipeObat->Text = 'Generik';

                else 
                    $this->tipeObat->Text = 'Non-Generik';
        }
        if($id)
        {
                $this->kodeObat->Text=$kode;
        }

        else
        {
                $this->kodeObat->Text = '';
        }
    }

	public function updateClicked()
	{
        
		$no_trans = $this->txt2->Text;
		$notransTmp = $this->numCounter('tbt_obat_jual',ObatJualRecord::finder(),'04'); //nomor transaksi 
		$noReg = $this->numRegister('tbt_obat_jual',ObatJualRecord::finder(),'04'); //nomor register atau nomor obat
		
		$this->keterangan->Text = $no_trans;
		$sql = "SELECT * FROM tbt_obat_jual WHERE no_trans='".$no_trans."'";
                $arr = $this->queryAction($sql,'S');

                $nmObat = $this->cariNama->Text;
                $sqlObat = "SELECT * FROM tbm_obat WHERE nama='".$nmObat."'";
                $arr2 = $this->queryAction($sql,'S');
		
		if($this->kodeObat->Text == '')
                {
                    $this->cariNama->Text='';
                    $this->jumlahObat->Text='';
                    $this->keterangan->Text='';
                    $this->getPage()->getClientScript()->registerEndScript
                        ('','jQuery.WsGrowl.show({title: \'\', content:\'<p class="msg_warning">Data Obat Tidak Ditemukan</p>\',timeout: 4000,dialog:{modal: true,
                        buttons: {
                                "OK": function() {
                                        jQuery( this ).dialog( "close" );
                                }
                        }
                    }});');
                }
		//$ObatJual = new ObatJualRecord();
		else
                {
                    foreach($arr as $row)
                    {
                        $nmObat = $this->cariNama->Text;
                        $kodeObat = $this->kodeObat->Text;

                        $no_transTmp = $notransTmp;
                        $no_trans_rwtjln = $row['no_trans_rwtjln'];
                        $no_reg = $row['no_reg'];
                        $cm = $row['cm'];
                        $dokter = $row['dokter'];
                        $klinik = $row['klinik'];
                        $tgl = date('y-m-d');
                        $wkt = date('H:i:s');
                        $operator=$row['operator'];
                        $sumber = $row['sumber'];
                        $tujuan = $row['tujuan'];
                        $id_obat = $kodeObat;
                        $ket_obat = $row['keterangan_obat'];
                        $id_trans_pengganti = 0;
					
                        $sql1 = "SELECT id FROM tbt_obat_harga WHERE kode='$kodeObat'";
                        $arr1 = $this->queryAction($sql1,'S');
                        foreach($arr1 as $row1)
                        {
                                $id_harga = $row1['id'];
                        }
					
                        //$id_harga = HrgObatRecord::finder()->findBySql($sql1);
                        $jumlah = $this->jumlahObat->Text;
                        $hrg_nett = $row['hrg_nett'];
                        $hrg_ppn = $row['hrg_ppn'];
                        $hrg_nett_disc = $row['hrg_nett_disc'];
                        $hrg_ppn_disc = $row['hrg_ppn_disc'];
                        $hrg = $row['hrg'];
                        $total = $row['total'];
                        $total_real = $row['total_real'];
                        $flag = $row['flag'];
                        $flag_ambil_resep = $row['flag_ambil_resep'];
                        $flag_salinan_resep = $row['flag_salinan_resep'];
                        $st_obat_khusus = $row['st_obat_khusus'];
                        $st_racik = $row['st_racik'];
                        $r_item = $row['r_item'];
                        $r_racik = $row['r_racik'];
                        $bungkus_racik = $row['bungkus_racik'];
                        $id_kemasan = $row['id_kemasan'];
                        $id_kel_racik = $row['id_kel_racik'];
                        $st_imunisasi = $row['st_imunisasi'];
                        $id_kel_imunisasi = $row['id_kel_imunisasi'];
                        $operator_kasir = $row['operator_kasir'];	
                    }
			
                    $sql2 = "INSERT INTO tbt_obat_jual( no_trans, no_trans_rwtjln, no_reg, cm, dokter,klinik,tgl,wkt,operator,sumber,tujuan,id_obat,
                    keterangan_obat,id_trans_pengganti,id_harga,jumlah,hrg_nett,hrg_ppn,hrg_nett_disc,hrg_ppn_disc,hrg,total,total_real,flag,
                    flag_ambil_resep,flag_salinan_resep,st_obat_khusus,st_racik,r_item,r_racik,bungkus_racik,id_kemasan,id_kel_racik,
                    st_imunisasi,id_kel_imunisasi,operator_kasir)
                    VALUES('$no_transTmp','$no_trans_rwtjln','$no_reg','$cm','$dokter','$klinik','$tgl','$wkt','$operator',
                    '$sumber','$tujuan','$id_obat','$ket_obat','$id_trans_pengganti','$id_harga','$jumlah','$hrg_nett','$hrg_ppn',
                    '$hrg_nett_disc','$hrg_ppn_disc','$hrg','$total','$total_real','$flag','$flag_ambil_resep','$flag_salinan_resep',
                    '$st_obat_khusus','$st_racik','$r_item','$r_racik','$bungkus_racik','$id_kemasan','$id_kel_racik','$st_imunisasi',
                    '$id_kel_imunisasi','$operator_kasir')";

                    $this->queryAction($sql2,'C');
			
                    $sql3 = "UPDATE tbt_obat_jual SET id_trans_pengganti='".$no_transTmp."' WHERE no_trans='".$no_trans."'";
                    $this->queryAction($sql3,'C');

                    /* 
                    $sql4 = "UPDATE tbt_obat_jual SET flag_ambil_resep='1' WHERE no_trans='".$no_trans."'";
                    $this->queryAction($sql4,'C');
                    */
                    $this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.reloadpage(); jQuery.FrameDialog.closeDialog();');
            }
	}
	
	public function clear()
	{		
		$this->nama->Text = '';
	}
	
    public function batalClicked($sender,$param)
	{		
		$this->msg->Text = '';
		$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.reloadpage(); jQuery.FrameDialog.closeDialog();');
		//$this->getPage()->getClientScript()->registerEndScript('','window.parent.maskContent(); window.parent.prosesModal("'.$this->Request['id'].'"); jQuery.FrameDialog.closeDialog();');
	}
	
	public function keluarClicked($sender,$param)
	{		
		$this->Response->redirect($this->Service->constructUrl('admin.KabAdmin'));		
	}
}
?>