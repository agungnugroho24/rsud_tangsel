<?php
/**
 * Auto generated by prado-cli.php on 2008-04-02 08:06:14.
 */
class BantuRawatJalanRecord extends TActiveRecord
{
	const TABLE='tbb_rawat_jalan';

	public $id;
	public $kd_dtd;
	public $cm;
	public $no_trans_rwtjln;
	public $tgl_trans_rwtjln;
	public $st_umur;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>