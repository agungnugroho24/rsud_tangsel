<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 12:28:13.
 */
class AssetDistBrgRecord extends TActiveRecord
{
	const TABLE='tbt_asset_dist_brg';

	public $id;
	public $no_trans;
	public $id_brg;
	public $jml;
	public $tgl;
	public $wkt;
	public $operator;
	public $tujuan;
	public $pengirim;
	public $penerima;
	public $ket;
	public $st;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>