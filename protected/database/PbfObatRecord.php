<?php
/**
 * Auto generated by prado-cli.php on 2008-04-27 05:15:09.
 */
class PbfObatRecord extends TActiveRecord
{
	const TABLE='tbm_pbf_obat';

	public $id;
	public $nama;
	public $npwp;
	public $npkp;
	public $alamat;
	public $tlp;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>