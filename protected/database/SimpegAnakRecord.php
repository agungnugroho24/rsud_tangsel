<?php
/**
 * Auto generated by prado-cli.php on 2008-05-07 11:45:19.
 */
class SimpegAnakRecord extends TActiveRecord
{
	const TABLE='tbd_simpeg_anak';

	public $id;
	public $nip;
	public $nm_anak;
	public $tgl_lahir_anak;
	public $jns_kel_anak;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>