function startTime() {
  var today = new Date();
  var h = today.getHours();
  var i = today.getMinutes();
  var s = today.getSeconds();
  var ampm = h >= 12 ? ' pm' : ' am'
  i = checkTime(i);
  s = checkTime(s);
  const date = new Date();
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  var time = (h % 12 || 12) + ":" + i + ":" + s;
  document.getElementById('fecha_hora').innerHTML = date.toLocaleDateString('es-ES', options) + " " + time + ampm;
  var t = setTimeout(startTime, 500);
}

function checkTime(i) {
  if (i < 10){ i = "0" + i ;}
  return i;
}

function showToastMessage(type, message, duration = 2000) {
  var options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "md-toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": 300,
    "hideDuration": 1000,
    "timeOut": duration,
    "extendedTimeOut": 1000,
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  };

  if(type === 'success') toastr.success(null,message,options);
  if(type === 'danger' || type === 'error') toastr.error(null,message,options);
  if(type === 'info') toastr.info(null,message,options);
  if(type === 'warning') toastr.warning(null,message,options);

}

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
      $('.profile-photo').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}
