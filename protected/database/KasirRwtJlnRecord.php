<?php
/**
 * Auto generated by prado-cli.php on 2008-05-20 03:51:43.
 */
class KasirRwtJlnRecord extends TActiveRecord
{
	const TABLE='tbt_kasir_rwtjln';
	
	public $id;
	public $cm;
	public $no_trans;
	public $no_trans_rwtjln;
	public $klinik;
	public $dokter;
	public $id_tindakan;
	public $tgl;
	public $waktu;
	public $operator;
	public $bhp;
	public $sewa_alat;
	public $tarif;
	public $total;
	public $disc;
	public $tanggungan_asuransi;
	public $st_flag;	
	public $st_kredit;
	public $pengali;
	public $catatan;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>