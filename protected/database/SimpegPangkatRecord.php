<?php
/**
 * Auto generated by prado-cli.php on 2008-05-07 11:45:54.
 */
class SimpegPangkatRecord extends TActiveRecord
{
	const TABLE='tbd_simpeg_pangkat';

	public $id;
	public $nip;
	public $nm_pangkat;
	public $gol_pangkat;
	public $sk_tmt_pangkat;
	public $nm_pejabat;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>