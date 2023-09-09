<?php
class SomeData extends TActiveRecord
{
    const TABLE='du_pasien';

    public $cm;
    public $nama;
    public $alamat;
    public $tgl_lahir;

    public static function finder($className=__CLASS__)
    {
        return parent::finder($className);
    }
}
?>