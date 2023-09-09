<?php
class update extends SimakConf
{
     public function onLoad($param)
	{
		parent::onLoad($param);
		
		$sql="SELECT kode FROM tbm_obat";
		$arrData=$this->queryAction($sql,'S');
		foreach($arrData as $row)
		{
			$kode=$row['kode'];
			/*
			$sql="SELECT id FROM tbt_obat_harga WHERE kode='$kode'";
			$arrData=$this->queryAction($sql,'S');
			foreach($arrData as $row)
			{
				$id_harga=$row['id'];
			}*/
			
			$sql="SELECT * FROM tbt_stok_lain WHERE id_obat='$kode' AND tujuan='2' ORDER BY id_obat DESC";
			//$id_obat = StokLainBackupRecord::finder()->findBySql($sql);
			$arr=$this->queryAction($sql,'S');
			$jml=count($arr);
			$this->showSql->text=$id_obat;
			if ($jml > 1){
				$sql="INSERT INTO tbt_stok_lain_new (id_obat,id_harga,jumlah,sumber,tujuan) VALUES ('$kode','$id_harga',0,'01','2')";
				//$sql="INSERT INTO tbt_stok_lain_new SELECT * FROM tbt_stok_lain_backup WHERE id_obat='$kode' AND tujuan='2'";
				$this->queryAction($sql,'C');
			}
			//$arrData=$this->queryAction($sql,'S');
			//$this->showSql->text=$sql;
			/*
			foreach($arrData as $row)
			{
				$id_obat=$row['id_obat'];
				//$this->showSql->text=$id_obat;
				if ($row['id_obat'] == NULL){
					//$sql="INSERT INTO tbt_stok_lain_backup (id_obat,id_harga,jumlah,sumber,tujuan) VALUES ('$kode','$id_harga',0,'01','2')";
					//$this->queryAction($sql,'C');
					$this->showSql->text='ga ada'.$id_obat;
				}
			}*/	
		}							
		//$sql="update tbt_obat_jual_backup,tbt_obat_harga set tbt_obat_jual_backup.id_harga=tbt_obat_harga.id WHERE tbt_obat_harga.kode=tbt_obat_jual_backup.id_obat";	
		//$this->queryAction($sql,'C');	
	}
}
?>