$(document).ready(function(){
	$(".date_picker").datepicker({
      changeMonth: true,
      changeYear: true,
      minDate: "-90Y",
      maxDate: "+0D",
      yearRange: "-100:+0"
    });

	$(".date_picker").datepicker('option', 'dateFormat', 'yy-mm-dd');

});