<?php
/**
 * Auto generated by prado-cli.php on 2008-09-19 06:54:26.
 */
class CtScanFotoHasilRecord extends TActiveRecord
{
	const TABLE='tbt_ctscan_foto_hasil';

	public $id;
	public $no_trans;
	public $nama_file;
	public $tipe_file;
	public $tipe_pasien;
	
	public $tgl;
	public $wkt;
	public $operator;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>