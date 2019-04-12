$(document).ready(function(){
  $('#to_tim').mdtimepicker({
    timeFormat: 'hh:mm:ss',
    format: 'hh:mm',
    theme: 'indigo'
  }).on('timechanged', function(e){
        console.log(e.value);
        console.log(e.time);
      });; //Initializes the time picker
});