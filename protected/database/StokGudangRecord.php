<?php
/**
 * Auto generated by prado-cli.php on 2008-05-23 06:21:21.
 */
class StokGudangRecord extends TActiveRecord
{
	const TABLE='tbt_stok_gudang';

	public $id_obat;
	public $jumlah;
	public $sumber;
	public $id;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>