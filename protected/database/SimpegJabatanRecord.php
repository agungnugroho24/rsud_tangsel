<?php
/**
 * Auto generated by prado-cli.php on 2008-05-07 11:45:37.
 */
class SimpegJabatanRecord extends TActiveRecord
{
	const TABLE='tbd_simpeg_jabatan';

	public $id;
	public $nip;
	public $nm_jabatan;
	public $tgl_mulai;
	public $tgl_akhir;
	public $sf;
	public $eselon;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>