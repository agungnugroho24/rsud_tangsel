<?php
/**
 * Auto generated by prado-cli.php on 2008-05-30 03:30:34.
 */
class ObatBeliTundaRecord extends TActiveRecord
{
	const TABLE='tbt_obat_beli_tunda';

	public $id;
	public $id_obat_masuk;
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

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>