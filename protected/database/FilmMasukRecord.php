<?php
/**
 * Auto generated by prado-cli.php on 2008-05-23 05:36:27.
 */
class FilmMasukRecord extends TActiveRecord
{
	const TABLE='tbt_film_masuk';
	
	public $id;
	public $id_barang;
	public $no_po;
	public $tgl_po;
	public $no_faktur;
	public $tgl_faktur;
	public $tgl_jth_tempo;
	public $tgl_terima;
	public $jumlah;
	public $jumlah_tunda_sisa;
	public $discount;
	public $hrg_nett;
	public $hrg_disc;
	public $hrg_ppn;
	public $hrg_ppn_disc;
	public $sumber;
	public $no_spk;
	public $no_surat;
	public $tgl_spk;
	public $no_bap;
	public $tgl_bap;
	public $no_batch;
	public $tgl_exp;
	public $st_keuangan;
	public $petugas;
	public $st_tunda_sisa;
	
	public $thn_pengadaan;
	public $depresiasi;
	public $st_rkbu_rtbu;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
}
?>
