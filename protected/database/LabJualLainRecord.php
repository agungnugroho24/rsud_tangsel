<?php
/**
 * Auto generated by prado-cli.php on 2008-05-29 09:26:51.
 */
class LabJualLainRecord extends TActiveRecord
{
	const TABLE='tbt_lab_penjualan_lain';

	public $id;
	public $nama;
	public $no_trans;
	public $no_trans_pas_luar;
	public $no_reg;
	public $id_tindakan;
	public $tgl;
	public $wkt;
	public $harga;
	public $disc;
	public $tanggungan_asuransi;
	public $harga_non_adm;
	public $harga_adm;
	public $operator;
	public $flag;
	public $catatan;
	public $st_karyawan;
	public $st_rujukan;
	public $id_lab_rujukan;
	public $operator_kasir;
	public $no_trans_rwtjln_lain;
	
	public $st_cetak_hasil;
	public $st_ambil;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>