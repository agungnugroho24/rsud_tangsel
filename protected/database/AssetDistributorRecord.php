<?php
/**
 * Auto generated by prado-cli.php on 2008-03-18 12:28:13.
 */
class AssetDistributorRecord extends TActiveRecord
{
	const TABLE='tbm_asset_distributor';

	public $id;
	public $nama;
	public $alamat;
	public $tlp;
	public $fax;
	public $npwp;
	public $ket;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>