<?php
/**
 * Auto generated by prado-cli.php on 2008-04-28 04:02:20.
 */
class FraksiJasmedRecord extends TActiveRecord
{
	const TABLE='tbm_fraksi_jasmed';

	public $id;
	public $jns_tindakan;
	public $id_tindakan;
	public $jml_level;
	public $level;
	public $sub_level;
	public $id_per_level;
	public $nama;
	public $persentase;
	public $jml_fraksi;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>