<?php
/**
 * Auto generated by prado-cli.php on 2008-05-30 03:30:34.
 */
class AsetBeliRecord extends TActiveRecord
{
	const TABLE='tbt_aset_beli';

	public $id;
	public $no_po;
	public $tgl_po;
	public $waktu;
	public $pbf;
	public $kode;
	public $jumlah;
	public $jumlah_kecil;
	public $catatan;
	public $petugas;
	public $flag;
	
	public $nm_apotik_luar;
	public $st_pembelian;
	public $st_rkbu_rtbu;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>