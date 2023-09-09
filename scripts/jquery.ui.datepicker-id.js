/* Indonesian initialisation for the jQuery UI date picker plugin. */
/* Written by Deden Fathurahman (dedenf@gmail.com). */
jQuery(function($){
	$.datepicker.regional['id'] = {
		closeText: 'Tutup',
		//prevText: '&#x3c;mundur',
		prevText: 'bulan sebemlunya',
		//nextText: 'maju&#x3e;',
		nextText: 'bln berikutnya',
		currentText: 'hari ini',
		monthNames: ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agus','Sep','Okt','Nop','Des'],
		dayNames: ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
		dayNamesShort: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
		//dayNamesMin: ['Mg','Sn','Sl','Rb','Km','jm','Sb'],
		dayNamesMin: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
		weekHeader: 'Mg',
		dateFormat: 'dd/mm/yy',
		firstDay: 0,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''};
	$.datepicker.setDefaults($.datepicker.regional['id']);
});