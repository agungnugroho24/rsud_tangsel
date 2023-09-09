<?php
class fotoBarangView extends SimakConf
{	
	public function onPreLoad($param)
	{
		parent::onPreLoad($param);		
		
		$ID=$this->Request['ID'];
		$sql = "SELECT id_brg,foto,tipe FROM tbm_asset_foto_barang WHERE id_brg='$ID'";
		$arr=$this->queryAction($sql,'R');
		foreach($arr as $row)
		{
			$foto = $row['foto'];
			$tipe = $row['tipe']; 
		}
		
		header("Content-type: $tipe");
		echo $foto; 
	} 	 
	
}
?>
