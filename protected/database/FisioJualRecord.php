<?php
/**
 * Auto generated by prado-cli.php on 2008-05-29 09:26:51.
 */
class FisioJualRecord extends TActiveRecord
{
	const TABLE='tbt_fisio_penjualan';

	public $id;
	public $cm;
	public $no_trans;
	public $no_trans_rwtjln;
	public $id_tindakan;
	public $tgl;
	public $wkt;
	public $harga;
	public $disc;
	public $tanggungan_asuransi;
	public $operator;
	public $flag;
	public $dokter;
	public $klinik;
	public $st_rujukan;
	public $id_fisio_rujukan;
	public $catatan;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>