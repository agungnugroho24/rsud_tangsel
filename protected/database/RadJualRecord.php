<?php
/**
 * Auto generated by prado-cli.php on 2008-05-29 10:22:24.
 */
class RadJualRecord extends TActiveRecord
{
	const TABLE='tbt_rad_penjualan';

	public $id;
	public $cm;
	public $no_trans;
	public $no_trans_rwtjln;
	public $no_reg;
	public $id_tindakan;
	public $pengali;
	public $tgl;
	public $wkt;
	public $harga;
	public $disc;
	public $tanggungan_asuransi;
	public $operator;
	public $dokter;
	public $dokter_rad;
	public $klinik;
	public $catatan;
	public $film_size;
	public $jml_film;
	public $st_ambil;
	public $st_cetak_hasil;
	public $flag;	
	
	public $st_rujukan;
	public $id_rad_rujukan;
	
	public $operator_ambil;
	public $tgl_ambil;
	public $wkt_ambil;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>