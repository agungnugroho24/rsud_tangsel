<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 12:28:13.
 */
class AssetStokLokasiRecord extends TActiveRecord
{
	const TABLE='tbt_asset_stok_lokasi';

	public $id;
	public $id_brg;
	public $jml;
	public $lokasi;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>