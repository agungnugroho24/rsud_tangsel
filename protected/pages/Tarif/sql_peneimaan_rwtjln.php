SELECT 
  tbt_rawat_jalan.tgl_visit,
  tbt_rawat_jalan.cm,
  tbd_pasien.nama,
  tbm_poliklinik.nama,
  tbd_pegawai.nama,
  sum(tbt_kasir_rwtjln.total) AS total
FROM
  tbt_rawat_jalan
  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
  INNER JOIN tbt_kasir_rwtjln ON (tbt_rawat_jalan.no_trans = tbt_kasir_rwtjln.no_trans_rwtjln)
GROUP BY  tbt_rawat_jalan.no_trans
  
UNION ALL

SELECT 
  tbt_rawat_jalan.tgl_visit,
  tbt_rawat_jalan.cm,
  tbd_pasien.nama,
  tbm_poliklinik.nama,
  tbd_pegawai.nama,
  sum(tbt_obat_jual.total) AS total  
FROM
  tbt_rawat_jalan
  INNER JOIN tbd_pegawai ON (tbt_rawat_jalan.dokter = tbd_pegawai.id)
  INNER JOIN tbm_poliklinik ON (tbt_rawat_jalan.id_klinik = tbm_poliklinik.id)
  INNER JOIN tbd_pasien ON (tbt_rawat_jalan.cm = tbd_pasien.cm)
  INNER JOIN tbt_obat_jual ON (tbt_rawat_jalan.no_trans = tbt_obat_jual.no_trans_rwtjln)  
GROUP BY  tbt_rawat_jalan.no_trans