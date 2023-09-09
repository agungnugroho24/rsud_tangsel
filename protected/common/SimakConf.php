<?php
class SimakConf extends TPage
{
	
	public function onInit($param)
 	{		
		parent::onInit($param);
		$arrApp=array();
		$var=$this->User->IsAppAuth;
		$arrApp = explode(',', $var);	 
		$this->setViewState('arrApp',$arrApp);	
 	}	
	
	public function hitHari($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar)
	{
		/* ---------------- RULES --------------------------
			$sparetime : jumlah jam yang menjadi patokan 1 hari
		*/
		 
		$sparetime = SpareTimeInapRecord::finder()->findByPk('1')->jml_jam;
		       
        //convert to unix timestamp		
		list($G,$i,$s) = explode(":",$wktMasuk);
		list($Y,$m,$d) = explode("-",$tglMasuk);
		$wktAwal = mktime($G,$i,$s,$m,$d,$Y);
		
		list($G,$i,$s) = explode(":",$wktKeluar);
		list($Y,$m,$d) = explode("-",$tglKeluar);
		$wktAkhir = mktime($G,$i,$s,$m,$d,$Y);

        $offset = $wktAkhir-$wktAwal;

        //$jmlHari = ceil($offset/60/60/(24)); //pembulatan ke atas
		$jmlJam = ceil($offset/60/60); //pembulatan ke atas
		
		$hari = floor($jmlJam / 24);
		$jamLebih = $jmlJam % 24 ;
		
		if($hari < 1)
		{
			$jmlHari = 1  ;
			/*
			if($jamLebih > $sparetime )
				$jmlHari = 1;
			else
				$jmlHari = 0;
			*/	
		}
		else
		{
			if($jamLebih <= $sparetime )	
				$jmlHari = $hari  ;
			else
				$jmlHari = $hari + 1  ;
		}
		
		return $jmlHari;
	}
	
	public function hitHari2($tglMasuk,$tglKeluar,$wktMasuk,$wktKeluar)
	{
		/* ---------------- RULES --------------------------
			jam masuk > 0 detik = 1 hari
			jam masuk > 24 jam = 2 hari
			jam masuk > 1 menit && jam masuk <= 24 jam = 1 hari
		*/
		       
        //convert to unix timestamp		
		list($G,$i,$s) = explode(":",$wktMasuk);
		list($Y,$m,$d) = explode("-",$tglMasuk);
		$wktAwal = mktime($G,$i,$s,$m,$d,$Y);
		
		list($G,$i,$s) = explode(":",$wktKeluar);
		list($Y,$m,$d) = explode("-",$tglKeluar);
		$wktAkhir = mktime($G,$i,$s,$m,$d,$Y);

        $offset = $wktAkhir-$wktAwal;

        $jmlHari = ceil($offset/60/60/24); //pembulatan ke atas
        return $jmlHari;
	}
	
	
	
	public function ConvertDate($tgl,$mode)
	{
		 if($mode == "1"){ //to normal
		 	$strtmp = substr($tgl,8,2) . "-" . substr($tgl,5,2)  . "-" . substr($tgl,0,4);
		}elseif($mode == "2"){ //to mysql		 
		 	$strtmp = substr($tgl,6,4) . "-" . substr($tgl,3,2)  . "-" . substr($tgl,0,2);
		}else{//to tgl indonesia 
				$blnIndo=$this->namaBulan(substr($tgl,5,2));
				$strtmp=substr($tgl,8,2) . " " . $blnIndo  . " " . substr($tgl,0,4);
			}		
		
		 return $strtmp;
	}
	
	public function akhirBln($bulan)
	{		
							
		if($bulan ==" 01" || $bulan == "03" || $bulan == "05" || $bulan == "07" || $bulan == "08" || $bulan == "10" || $bulan == "12")
			$akhir_bulan = 31;
		elseif($bulan == "04" || $bulan == "06" || $bulan == "09" || $bulan == "11" )
			$akhir_bulan = 30;
		elseif($bulan == "02")
			{
			if($tahun%4 == 0)
				$akhir_bulan = 29;
			else
				$akhir_bulan = 28;
			}			
		
		$blnTmp = date('Y') .'-'. $bulan . '-' . $akhir_bulan;
		
		return $blnTmp;
	}
	
	public function collectSelectionResult($input)
    {
        $indices=$input->SelectedIndices;
        $result='';
        foreach($indices as $index)
        {
            $item=$input->Items[$index];
            $result.="$item->Value,";
        }
		$v = strlen($result) - 1;
		$res=substr($result,0,$v);
        return $res;
    }
	
	protected function authApp($varApp)
    {		
		$authVar=$this->getViewState('arrApp');
		if (in_array($varApp, $authVar)) {
    		$value = "True";
		}else{
			$value = "False";
		}		
		return $value;
	}	
	
	/*
	public function numCounter($varTable,$activeTable,$consModul)
    {	
		
			$varTable adalah tabel yang dirujuk untuk diquery dalam pemrosesan no_trans
			$activeTabel adalah nama Class ActiveRecord dari tabel yang dirujuk
			$consModul adalah nilai konstanta dari modul2 yang akan dibuatkan no_trans-nya!
			 seperti rawat jalan memiliki konstanta modul '01', rawat inap '02' dst...
			 
			 Daftar Konstanta Modul:
			 '01' = Rawat Jalan;
			 '02' = Rawat Inap;
			 '03' = IGD;
			 '04' = Apotik Rwt. Jln;
			 '05' = Depo;
			 '06' = Lab Rwt. Jalan;
			 '07' = Radiologi Rwt. Jalan;
			 '08' = Apotik Rwt. Inap
		
		
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT no_transaksi FROM " . $varTable . " order by no_transaksi desc";
		$no = $find->findBySql($sql);
						
		if($no==NULL)//jika kosong bikin ndiri
		{
			$today=date("Ym");
			$urut='0001';
			$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
		}elseif((substr($no->getColumnValue('no_transaksi'),4,2))<>(date("m")))//jika bulannya brubah....(emang bulan brubah jadi apa ????)
		{
			$today=date("Ym");
			$urut='0001';
			$notrans=$today.$consModul.$urut;
		}else
		{
			$today=date("Ym");
			$urut=intval(substr($no->getColumnValue('no_transaksi'),-4,4));
			if ($urut==9999)//jika nomornya udah 9999. Sebulan 9999 IJIN !!!!....mungkin ga??? mungkin ajee klo ijin bikin radio button
			{
				$urut=1;
				$urut=substr('0000',-4,4-strlen($urut)).$urut;
				$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
			}else{
				$urut=$urut+1;
				$urut=substr('0000',-4,4-strlen($urut)).$urut;
				$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
			}
		}
		return $notrans;
	}	
	*/
	
	public function numCounter($varTable,$activeTable,$consModul)
    {	
		/*
			$varTable adalah tabel yang dirujuk untuk diquery dalam pemrosesan no_trans
			$activeTabel adalah nama Class ActiveRecord dari tabel yang dirujuk
			$consModul adalah nilai konstanta dari modul2 yang akan dibuatkan no_trans-nya!
			 seperti rawat jalan memiliki konstanta modul '01', rawat inap '02' dst...
			 
			 Daftar Konstanta Modul:
			 '01' = Rawat Jalan;
			 '02' = Rawat Inap;
			 '03' = IGD;
			 '04' = Apotik Sentral;
			 '05' = Depo;
			 '06' = Lab;
			 '07' = Radiologi;
			 '08' = Kasir Rawat Jalan;
			 '09' = Kasir Pendaftaran;
			 '10' = Apotik Rwt. Inap;
			 '11' = Lab Rwt. Inap;
			 '12' = Rad Rwt. Inap;
			 '13' = Penjualan Apotik Pasien Luar;
			 '14' = Lab Pasien Luar;
			 '15' = Rad Pasien Luar;
			 
			 '16' = Fisio Rawat Jalan;
			 '17' = Fisio Rawat Inap;
			 '18' = Fisio Pasien Luar;
			 
			 '19' = Ambulan Rawat Jalan;
			 '20' = Ambulan Rawat Inap;
			 
			 
			 '20' = Retur Obat Rawat Jalan;
			 '21' = Retur Obat Rawat Inap;
			 '23' = Rawat Jalan Lain;
			 
			 '30' = Pasien Luar;
			 '31' = Pembayaran PBF;
			 '32' = Claim Asuransi;
			 '33' = Uang Muka;
			 
			 '40' = Angsuran Rawat Jalan => Transaksi dilakukan dgn beberapa cara pembayaran;
			 '41' = Penjualan Apotik Pasien Luar Karyawan;
			 '42' = Angsuran Rawat Inap => Transaksi dilakukan dgn beberapa cara pembayaran;
			 
			 '43' = Apotik Unit Internal;
			 
			 '50' = CtScan Rawat Jalan;
			 '51' = CtScan Rawat Inap;
			 '52' = CtScan Pasien Luar;
		*/
		
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT no_trans FROM " . $varTable . " order by no_trans desc";
		$no = $find->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$today=date("Ym");
			$urut='000001';
			$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
		}else{			
			
			$urut=intval(substr($no->getColumnValue('no_trans'),-6,6));
			$bln = substr(($no->getColumnValue('no_trans')),4,2);
			$month = date('m');
			if($bln==$month){
				$today = date('Y').$bln;
				if ($urut==999999)
				{
					$urut=1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;
					$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				}else{
					$urut=$urut+1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;
					$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				}
			}else{
				$today=date("Ym");
				$urut='000001';
				$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
			}			
		}
		return $notrans;
	}	
	
	public function numRegister($varTable,$activeTable,$consModul)
    {	
		/*
			$varTable adalah tabel yang dirujuk untuk diquery dalam pemrosesan no_trans
			$activeTabel adalah nama Class ActiveRecord dari tabel yang dirujuk
			$consModul adalah nilai konstanta dari modul2 yang akan dibuatkan no_trans-nya!
			 seperti rawat jalan memiliki konstanta modul '01', rawat inap '02' dst...
			 
			 Daftar Konstanta Modul:
			 '01' = Rawat Jalan;
			 '02' = Rawat Inap;
			 '03' = IGD;
			 '04' = Apotik Sentral;
			 '05' = Depo;
			 '06' = Lab;
			 '07' = Radiologi;
			 '08' = Kasir Rawat Jalan;
			 '09' = Kasir Pendaftaran;
			 '10' = Apotik Rwt. Inap;
			 '11' = Lab Rwt. Inap;
			 '12' = Rad Rwt. Inap;
			 '13' = Penjualan Apotik Pasien Luar;
			 '14' = Lab Rujukan;
			 '15' = Rad Rujukan;
			 
			 '20' = Retur Obat Rawat Jalan;
			 '21' = Retur Obat Rawat Inap;
			 '23' = Rawat Jalan Lain;
			 
			 '30' = Pasien Luar;
			 '31' = Pembayaran PBF;
			 '32' = Claim Asuransi;
			 '33' = Uang Muka;
			 
			 '73' = Pengeluaran BHP;
		*/
		
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT no_reg FROM " . $varTable . " order by no_reg desc";
		$no = $find->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$today=date("Ym");
			$urut='000001';
			$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
		}else{			
			
			$urut=intval(substr($no->getColumnValue('no_reg'),-6,6));
			$bln = substr(($no->getColumnValue('no_reg')),4,2);
			$month = date('m');
			if($bln==$month){
				$today = date('Y').$bln;
				if ($urut==999999)
				{
					$urut=1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;
					$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				}else{
					$urut=$urut+1;
					$urut=substr('000000',-6,6-strlen($urut)).$urut;
					$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
				}
			}else{
				$today=date("Ym");
				$urut='000001';
				$notrans=$today.$consModul.$urut;// '01' adalah kode untuk rawat jalan
			}			
		}
		return $notrans;
	}
	
	//collect list of array from list component		
	protected function collectSelectionListResult($input)
	{
		$indices=$input->SelectedIndices;		
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			return $index;
		}		
	}
	/*
	public function numUrut($varTable,$activeTable,$bykDigit)
    {			
		//Mbikin No Urut
		$find = $activeTable;//::finder();		
		$sql = "SELECT id FROM " . $varTable . " order by id desc";
		
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{	
			if($bykDigit == '5'){
				$urut='00001';
			}
			elseif($bykDigit == '4'){
				$urut='0001';
			}elseif($bykDigit == '3'){
				$urut='001';
			}elseif($bykDigit == '2'){	
				$urut='01';
			}			
		}else{			
			if($bykDigit == '5'){
				$urut = $num->getColumnValue('id') + 1;				
				$tmp=substr('00000',0,5-strlen($urut)).$urut;
			}
			if($bykDigit == '4'){
				$urut = $num->getColumnValue('id') + 1;				
				$tmp=substr('0000',0,4-strlen($urut)).$urut;
			}
			if($bykDigit == '3'){
				$urut = $num->getColumnValue('id') + 1;			
				$tmp=substr('000',0,3-strlen($urut)).$urut;
			}
			if($bykDigit == '2'){					
				$urut = $num->getColumnValue('id') + 1;				
				$tmp=substr('00',0,2-strlen($urut)) . $urut;
			}						
			
		}
		return $tmp;
	}
	*/
	
	public function numUrut($varTable,$activeTable,$bykDigit)
    {			
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT id FROM " . $varTable . " order by id desc";
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}		
			
			$urut = 1;
			
			$tmp = substr($tmpDigit,-$bykDigit,$bykDigit-strlen($urut)).$urut;
			//$tmp .= '1';
		}
		else
		{		
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}	
			
			for($i=1;$i<=$bykDigit;$i++)
			{
				if($bykDigit == $i){
					$urut = $num->getColumnValue('id') + 1;				
					$tmp=substr($tmpDigit,-$i,$i-strlen($urut)).$urut;
				}
			}	
		}
		
		return $tmp;
	}
	
	public function numUrutKecamatan($varTable,$activeTable,$bykDigit,$idFilter)
    {			
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT SUBSTRING(id,6,2) AS id FROM " . $varTable . " WHERE SUBSTRING(id,1,5) = '$idFilter' order by id desc";
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}		
			
			$urut = 1;
			
			$tmp = $idFilter.''.substr($tmpDigit,-$bykDigit,$bykDigit-strlen($urut)).$urut;
			//$tmp .= '1';
		}
		else
		{		
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}	
			
			for($i=1;$i<=$bykDigit;$i++)
			{
				if($bykDigit == $i){
					$urut = $num->getColumnValue('id') + 1;				
					$tmp = $idFilter.''.substr($tmpDigit,-$i,$i-strlen($urut)).$urut;
				}
			}	
		}
		
		return $tmp;
	}
	
	public function numUrutKelurahan($varTable,$activeTable,$bykDigit,$idFilter)
    {			
		//Mbikin No Urut
		$find = $activeTable;		
		$sql = "SELECT SUBSTRING(id,8,3) AS id FROM " . $varTable . " WHERE SUBSTRING(id,1,7) = '$idFilter' order by id desc";
		$num = $find->findBySql($sql);
		if($num==NULL)//jika kosong bikin ndiri
		{
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}		
			
			$urut = 1;
			
			$tmp = $idFilter.''.substr($tmpDigit,-$bykDigit,$bykDigit-strlen($urut)).$urut;
			//$tmp .= '1';
		}
		else
		{		
			for($i=1;$i<=$bykDigit;$i++)
			{
				$tmpDigit .= '0';
			}	
			
			for($i=1;$i<=$bykDigit;$i++)
			{
				if($bykDigit == $i){
					$urut = $num->getColumnValue('id') + 1;				
					$tmp = $idFilter.''.substr($tmpDigit,-$i,$i-strlen($urut)).$urut;
				}
			}	
		}
		
		return $tmp;
	}
	
	public function queryAction($sql,$mode)
	{
		
		//$conn = new TDbConnection("mysql:host=localhost;dbname=simak_tangsel","root","");
		$conn = new TDbConnection("mysql:host=localhost;dbname=simak_tangsel","simyantu","jackass");				
		$conn->Persistent=true;
		$conn->Active=true;				
		if($mode == "C")//Use this with INSERT, DELETE and EMPTY operation
		{
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();						
		}
		else if($mode == "S")//Return for select statement
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader->readAll();				
			
		}		
		else if($mode == "R") //Return set of rows
		{	
			$comm=$conn->createCommand($sql);		
			$dataReader = $comm->query();
			$rows=$dataReader;		
			
		}			
		else if($mode == "D") //Droped table
		{
			$que = "DROP TABLE " . $sql;
			$comm=$conn->createCommand($que);		
			$dataReader = $comm->query();						
		}
		else if($mode == "X") //Droped table
		{
			$comm=$conn->createCommand($sql);
			$dataReader = $comm->query();
			$row=$dataReader->read();
			$rows = $row[count];	
		}	
		return $rows;
		$conn->Active=false;				
	}	
	
	public function namaBulan($month)
	{
		$nmBln=array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
		$sayBln = $nmBln[$month];
		return $sayBln;
	}
	
	public function bulanRomawi($month)
	{
		$nmBln=array('01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII');
		$sayBln = $nmBln[$month];
		return $sayBln;
	}
	
	public function numberToRoman($num) 
	 {
		 // Make sure that we only use the integer portion of the value
		 $n = intval($num);
		 $result = '';
	 
		 // Declare a lookup array that we will use to traverse the number:
		 $lookup = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
		 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
		 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
	 
		 foreach ($lookup as $roman => $value) 
		 {
			 // Determine the number of matches
			 $matches = intval($n / $value);
	 
			 // Store that many characters
			 $result .= str_repeat($roman, $matches);
	 
			 // Substract that from the number
			 $n = $n % $value;
		 }
	 
		 // The Roman numeral should be built, return it
		 return $result;
	 }
	
	public function textToNumber($var)
	{
		$data = array();
		
		$data = explode(',',strtolower(trim($var)));
		
		if(count($data) >1)
		{	
			$txt1 = $data[0];
			$txt2 = $data[1];
			
			$jmlDigit1 = strlen($txt1);
			for($i=0;$i<$jmlDigit1;$i++) 
			{
				if(intval(substr($txt1,$i,1)) == true)
				{
					$hasilTxt1 .= substr($txt1,$i,1);
				}
			}
			
			$jmlDigit2 = strlen($txt2);
			for($i=0;$i<$jmlDigit2;$i++)
			{
				if(intval(substr($txt2,$i,1)) == true)
				{
					$hasilTxt2 .= substr($txt2,$i,1);
				}
			}
			
			$hasil = floatval($hasilTxt1.'.'.$hasilTxt2);
		}
		else
		{
			$txt1 = $data[0];
			
			$jmlDigit1 = strlen($txt1);
			for($i=0;$i<$jmlDigit1;$i++)
			{
				if(intval(substr($txt1,$i,1)) == true)
				{
					$hasilTxt1 .= substr($txt1,$i,1);
				}
			}
			
			$hasil = $hasilTxt1;
		}
		
		return $hasil;
	}
	 
	public function stKawin($st)
	{
		$nmStatus=array('Kawin','Belum Kawin','Duda','Janda');
		$sayStatus = $nmStatus[$st];
		return $sayStatus;
	}
	
	public function randomPrefix($length)  
	{ 
		$random= "";
		srand((double)microtime()*1000000);
		
		$data = "AbcDE123IJKLMN67QRSTUVWXYZ"; 
		$data .= "aBCdefghijklmn123opq45rs67tuv89wxyz"; 
		$data .= "0FGH45OP89";
		
		for($i = 0; $i < $length; $i++) 
		{ 
		$random .= substr($data, (rand()%(strlen($data))), 1); 
		}
		
		return $random; 
	}

	public function setNameTable($nmTbl)
	{
		$nmTable = 'temp_';
		//$nmTable .= rand(substr(microtime(),2,5),10);
		$nmTable .= $this->randomPrefix(20);
		$this->setViewState($nmTbl,$nmTable);	
		$user=$this->User->Name;
		$sql = "INSERT INTO tbx_destroy VALUES('$user','$nmTable')";
		$this->queryAction($sql,'C');//Insert new row in tabel bro...	
		return $nmTable;
	}
	
	public function tahun()
	{
		$thnNow = date('Y');
		$thnAwal = $thnNow - 20;
		$thnAkhir = $thnNow + 50;
		
		for($i=$thnAwal; $i<$thnAkhir; $i++);
		{
			$temp =array_fill($thnAwal,$thnAkhir-$thnAwal,$data);
			$data=thnAwal+1;
		}
		return $temp;
	}
	
	public function dataTahun($thnAwal,$thnAkhir) 
	{
		for($i=$thnAwal; $i<$thnAkhir; $i++);
		{
			$temp =array_fill($thnAwal,$thnAkhir-$thnAwal,$data);
			$data=thnAwal+1;
		}
		return $temp;
	}
		
	public function bhpData($awal,$n) 
	{		
		$temp = array();		
		$i = 1;
		while ($i <= $n){	
			$x = $awal * $i;
			$data .= $x .',';			
			$i++;
		}
		$data = '0,'. $data;
		$v = strlen($data) - 1;
		$var=substr($data,0,$v);				
		$temp = explode(',',$var);
		return $temp;
	}
	
	public function terbilang($num) {
		  $digits = array(
			0 => "Nol",
			1 => "satu",
			2 => "dua",
			3 => "tiga", 
			4 => "empat",
			5 => "lima",
			6 => "enam",
			7 => "tujuh",
			8 => "delapan",
			9 => "sembilan");
		  $orders = array(
			 0 => "",
			 1 => "puluh",
			 2 => "ratus",
			 3 => "ribu",
			 6 => "juta",
			 9 => "miliar",
			12 => "triliun",
			15 => "kuadriliun");
		
		  $is_neg = $num < 0; $num = "$num";
		
		  //// angka di kiri desimal
		
		  $int = ""; if (preg_match("/^[+-]?(\d+)/", $num, $m)) $int = $m[1];
		  $mult = 0; $wint = "";
		
		  // ambil ribuan/jutaan/dst
		  while (preg_match('/(\d{1,3})$/', $int, $m)) {
			
			// ambil satuan, puluhan, dan ratusan
			$s = $m[1] % 10; 
			$p = ($m[1] % 100 - $s)/10;
			$r = ($m[1] - $p*10 - $s)/100;
			
			// konversi ratusan
			if ($r==0) $g = "";
			elseif ($r==1) $g = "se$orders[2]";
			else $g = $digits[$r]." $orders[2]";
			
			// konversi puluhan dan satuan
			if ($p==0) {
			  if ($s==0);
			  elseif ($s==1) $g = ($g ? "$g ".$digits[$s] :
										($mult==0 ? $digits[1] : "se"));			  							
			  else $g = ($g ? "$g ":"") . $digits[$s];
			} elseif ($p==1) {
			  if ($s==0) $g = ($g ? "$g ":"") . "se$orders[1]";
			  elseif ($s==1) $g = ($g ? "$g ":"") . "sebelas";
			  else $g = ($g ? "$g ":"") . $digits[$s] . " belas";
			} else {
			  $g = ($g ? "$g ":"").$digits[$p]." puluh".
				   ($s > 0 ? " ".$digits[$s] : "");
			}
		
			// gabungkan dengan hasil sebelumnya			
			$wint = ($g ? $g.($g=="se" ? "":" ").$orders[$mult]:"").
					($wint ? " $wint":""); 
			
			
			// pangkas ribuan/jutaan/dsb yang sudah dikonversi
			$int = preg_replace('/\d{1,3}$/', '', $int);
			$mult+=3;
		  }
		  if (!$wint) $wint = $digits[0];
		  
		  //// angka di kanan desimal
		
		  $frac = ""; if (preg_match("/\.(\d+)/", $num, $m)) $frac = $m[1];
		  $wfrac = "";
		  for ($i=0; $i<strlen($frac); $i++) {
			$wfrac .= ($wfrac ? " ":"").$digits[substr($frac,$i,1)];
		  }		
		  $wintEYD=str_ireplace("sejuta","satu juta",$wint);
		  return ($is_neg ? "minus ":"").$wintEYD.($wfrac ? " koma $wfrac":"");
		}
		
		// Fungsi untuk mengambil text terpilih dalam drop dowwn //
		protected function ambilTxt($input)
		{
			$indices=$input->SelectedIndices;
			//$result='';
			foreach($indices as $index)
			{
				$item=$input->Items[$index];
				$result ="$item->Text";
			}
			return $output->Text=$result;
		}
		
	//Pembulatan ke pecahan 100	
	public function bulatkan($nilaiInput)
	{		
		$nilai = round($nilaiInput);
		$jmlTxt=strlen($nilai);
		$txt01=substr($nilai,0,($jmlTxt-2));
		$txt02=substr($nilai,-2,2);
		
		
		if($txt02>=50)
		{
			$hasil=($txt01+1).'00';		
		}
		elseif($txt02<50 && $txt02>0)
		{
			//$hasil=($txt01).'50'; 
			$hasil=($txt01+1).'00';	
		}
		else
		{
			$hasil=$nilai;
		}	
		
		return $hasil;	
		
		/*
		if($txt02 != '00')
		{
			$hasil=($txt01+1).'00';		
		}
		else
		{
			$hasil=$nilai;
		}			
		return $hasil;			
		*/	
	}
	
	public function bulatkan500($nilaiInput)
	{		
		$nilai = round($nilaiInput);
		$jmlTxt=strlen($nilai);
		$txt01=substr($nilai,0,($jmlTxt-3));
		$txt02=substr($nilai,-3,3);
		
		
		if($txt02>500)
		{
			$hasil=($txt01+1).'000';		
		}
		elseif($txt02<500 && $txt02>0)
		{
			//$hasil=($txt01).'50'; 
			$hasil=($txt01).'500';	
		}
		else
		{
			$hasil=$nilai;
		}	
		
		return $hasil;	
	}
	
	
	
	public function nipCounter()
    {	
		//Mbikin No Urut	
		$sql = "SELECT 
					nip 
				FROM 
					tbd_user 
				WHERE
					nip LIKE '001%'
				ORDER BY  nip DESC";
		$no = UserRecord::finder()->findBySql($sql);
		if($no==NULL)//jika kosong bikin ndiri
		{
			$urut='000001';
			$notrans=$urut;// '01' adalah kode untuk rawat jalan
		}
		else
		{						
			$urut=intval(substr($no->getColumnValue('nip'),-6,6));			
			if ($urut==999999)
			{
				$urut=1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$notrans=$urut;// '01' adalah kode untuk rawat jalan
			}else{
				$urut=$urut+1;
				$urut=substr('000000',-6,6-strlen($urut)).$urut;
				$notrans=$urut;// '01' adalah kode untuk rawat jalan
			}
						
		}
		return $notrans;
	}
	
	//------------------ HITUNG UMUR ANTARA 2 TANGGAL --------------------------
	public function hitUmur($tglLahir)
	{
        // tglLahir 'YYYY-MM-DD'
        $arrTglLahir = explode("-",$tglLahir);
       
        $thn = $arrTglLahir[0];
        $bln = $arrTglLahir[1];
        $tgl = $arrTglLahir[2];
       
	  	$timestamp = mktime(0, 0, 0, $bln, $tgl, $thn); // tgl lahir
		$t = time(); // wkt sekarang
		$umur = ($timestamp < 0) ? ( $t + ($timestamp * -1) ) : $t - $timestamp;
		$pembagi = 60 * 60 * 24 * 365;
		$umur = floor($umur / $pembagi);
       
        return $umur;
	}
	
	
	//------------------ HITUNG UMUR SEKARANG --------------------------
	public function hitUmurSekarang($birth_year, $birth_month, $birth_day)
	{
		$birth_month_days = date( t, mktime(0, 0, 0, $birth_month, $birth_day, $birth_year) );
	
		$current_year = date(Y);
		$current_month = date(n);
		$current_day = date(j);
		$current_month_days = date(t);
	
		if($current_month > $birth_month)
		{
			$yy = $current_year - $birth_year;
			$mm = $current_month - $birth_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_month_days;
			}
		}
	
		if($current_month < $birth_month)
		{
			$yy = $current_year - $birth_year - 1;
			$mm = 12 - $birth_month + $current_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_day;
			}
		}
	
		if($current_month==$birth_month)
		{
			if($current_day == $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = 0;
				$dd = 0;
			}
			
			if($current_day < $birth_day)
			{
				$yy = $current_year - $birth_year - 1;
				$mm = $birth_month + $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				
				if($dd > $current_month_days)
				{
					$mm += 1;
					$dd -= $current_day;
				}
			}
			
			if($current_day > $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				if($dd > $current_month_days)
				{
				$mm += 1;
				$mm -= $current_month;
				$dd -= $current_month_days;
				}
			}
		}
	
		$age = $dd.'-'.$mm.'-'.$yy;
		return $age;
    }
	
	//------------------ DATE CALCULATION --------------------------
	public function date_diff($date, $date2)
	{
		//if(!$date2)
			//$date2 = mktime();
	
		$date_diff = array('seconds'  => '',
						   'minutes'  => '',
						   'hours'    => '',
						   'days'     => '',
						   'weeks'    => '',
						   
						   'tseconds' => '',
						   'tminutes' => '',
						   'thours'   => '',
						   'tdays'    => '',
						   'tdays'    => '');
	
		////////////////////
		
		//if($date2 > $date)
			$tmp = $date2 - $date;
		//else
			//$tmp = $date - $date2;
		
		$seconds = $tmp;
	
		// Relative ////////
		$date_diff['weeks'] = floor($tmp/604800);
		$tmp -= $date_diff['weeks'] * 604800;
	
		$date_diff['days'] = floor($tmp/86400);
		$tmp -= $date_diff['days'] * 86400;
	
		$date_diff['hours'] = floor($tmp/3600);
		$tmp -= $date_diff['hours'] * 3600;
	
		$date_diff['minutes'] = floor($tmp/60);
		$tmp -= $date_diff['minutes'] * 60;
	
		$date_diff['seconds'] = $tmp;
		
		// Total ///////////
		$date_diff['tweeks'] = floor($seconds/604800);
		$date_diff['tdays'] = floor($seconds/86400);
		$date_diff['thours'] = floor($seconds/3600);
		$date_diff['tminutes'] = floor($seconds/60);
		$date_diff['tseconds'] = $seconds;
	
		return $date_diff;
	}
	
	function dateDifference($date1, $date2)
	{
		//date1 = 'Y-m-d';
		//date2 = 'Y-m-d';
		
		$d1 = (is_string($date1) ? strtotime($date1) : $date1);
		$d2 = (is_string($date2) ? strtotime($date2) : $date2);
	
		$diff_secs = abs($d1 - $d2);
		$base_year = min(date("Y", $d1), date("Y", $d2));
	
		$diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
	
		return array
		(
			"years"         => abs(substr(date('Ymd', $d1) - date('Ymd', $d2), 0, -4)),
			"months_total"  => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
			"months"                => date("n", $diff) - 1,
			"weeks_total"    => floor($diff_secs / (3600 * 24 * 7)),
			"weeks"                  => date("W", $diff) - 1,
			"days_total"    => floor($diff_secs / (3600 * 24)),
			"days"                  => date("j", $diff) - 1,
			"hours_total"   => floor($diff_secs / 3600),
			"hours"                 => date("G", $diff),
			"minutes_total" => floor($diff_secs / 60),
			"minutes"               => (int) date("i", $diff),
			"seconds_total" => $diff_secs,
			"seconds"               => (int) date("s", $diff)
		);
	} 
	
	function cariUmur2($mode,$cm,$klinik)
	{
		if($mode == '0')//rawat jalan
			$tglVisit = RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND id_klinik=?',$cm,'0','0',$klinik)->tgl_visit;			
		elseif($mode == '1')//rawat jalan
			$tglVisit = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->tgl_masuk;				
			
		
		$tglLahir = PasienRecord::finder()->findByPk($cm)->tgl_lahir;
		$umur = $this->dateDifference($tglLahir,$tglVisit);
		$umur = $umur['years']; 	
		return $umur;	
	}
	
	function cariUmur($mode,$cm,$klinik)
	{
		if($mode == '0')//rawat jalan
		{
			$sql = "SELECT tgl_visit FROM tbt_rawat_jalan WHERE cm = '$cm' AND st_alih = '0' AND id_klinik = '$klinik' ORDER BY no_trans DESC ";
			$tglVisit = RwtjlnRecord::finder()->findBySql($sql)->tgl_visit;			
			//$tglVisit = RwtjlnRecord::finder()->find('cm=? AND flag=? AND st_alih=? AND id_klinik=?',$cm,'0','0',$klinik)->tgl_visit;			
		}	
		elseif($mode == '1')//rawat jalan
		{
			$sql = "SELECT tgl_masuk FROM tbt_rawat_inap WHERE cm = '$cm' ORDER BY no_trans DESC ";
			$tglVisit = RwtInapRecord::finder()->findBySql($sql)->tgl_masuk;				
			//$tglVisit = RwtInapRecord::finder()->find('cm=? AND status=?',$cm,'0')->tgl_masuk;				
		}	
		
		$tglLahir = PasienRecord::finder()->findByPk($cm)->tgl_lahir;
		$umur = $this->dateDifference($tglLahir,$tglVisit);
		//$umur = $umur['years']; 	
		
		return array
		(
			"years"         => $umur['years'],
			"months_total"  => $umur['months_total'],
			"months"                => $umur['months'],
			"weeks_total"    => $umur['weeks_total'],
			"weeks"                  => $umur['weeks'],
			"days_total"    => $umur['days_total'],
			"days"                  => $umur['days'],
			"hours_total"   => $umur['hours_total'],
			"hours"                 => $umur['hours'],
			"minutes_total" => $umur['minutes_total'],
			"minutes"               => $umur['minutes'],
			"seconds_total" => $umur['seconds_total'],
			"seconds"               => $umur['seconds']
		);
			
	}
	
	function cariJkel($cm)
	{
		$jkel = PasienRecord::finder()->findByPk($cm)->jkel;
		
		if($jkel == '0')
			$jkel = 'Laki-Laki';
		else
			$jkel = 'Perempuan';	
			
		return $jkel;	
	}
	
	function cariJkel2($cm)
	{
		$jkel = PasienRecord::finder()->findByPk($cm)->jkel;			
		return $jkel;	
	}
	
	function formatCm($cm)
	{
		if($cm != '')
		{
		
			//$cm = substr('0000000',-7,7-strlen($cm)).$cm;
			$cm = substr('000000',-6,6-strlen($cm)).$cm;
		}
		else
			$cm = '';
				
		return $cm;
	}
	
	public function get_age($birth_year, $birth_month, $birth_day)
	{
		$birth_month_days = date( t, mktime(0, 0, 0, $birth_month, $birth_day, $birth_year) );
	
		$current_year = date(Y);
		$current_month = date(n);
		$current_day = date(j);
		$current_month_days = date(t);
	
		if($current_month > $birth_month)
		{
			$yy = $current_year - $birth_year;
			$mm = $current_month - $birth_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_month_days;
			}
		}
	
		if($current_month < $birth_month)
		{
			$yy = $current_year - $birth_year - 1;
			$mm = 12 - $birth_month + $current_month - 1;
			$dd = $birth_month_days - $birth_day + $current_day;
			if($dd > $current_month_days)
			{
				$mm += 1;
				$dd -= $current_day;
			}
		}
	
		if($current_month==$birth_month)
		{
			if($current_day == $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = 0;
				$dd = 0;
			}
			
			if($current_day < $birth_day)
			{
				$yy = $current_year - $birth_year - 1;
				$mm = $birth_month + $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				
				if($dd > $current_month_days)
				{
					$mm += 1;
					$dd -= $current_day;
				}
			}
			
			if($current_day > $birth_day)
			{
				$yy = $current_year - $birth_year;
				$mm = $current_month - 1;
				$dd = $birth_month_days - $birth_day + $current_day;
				if($dd > $current_month_days)
				{
				$mm += 1;
				$mm -= $current_month;
				$dd -= $current_month_days;
				}
			}
		}
	
		$age = $dd.'-'.$mm.'-'.$yy;
		return $age;

    }

	function dokterList($idKlinik)//Tambah function untuk Dokter multi klinik
	{
		$sql = "SELECT id, nama, poliklinik FROM tbd_pegawai WHERE kelompok='1' ORDER BY nama";
		$arr = $this->queryAction($sql,'S');
		foreach($arr as $row)
		{
			$arrPoli = explode(',',$row['poliklinik']);
			if(in_array($idKlinik,$arrPoli))
				$dataDokter[]=array('id'=>$row['id'],'nama'=>$row['nama']);
		}
		
		return $dataDokter;
	}
	
}
