<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 12:28:13.
 */
class AssetBarcodeRecord extends TActiveRecord
{
	const TABLE='tbt_asset_barcode';

	public $id;
	public $no_trans;
	public $id_brg;
	public $kd_barcode;
	public $tgl;
	public $wkt;
	public $st;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>