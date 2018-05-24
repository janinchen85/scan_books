$(function() {
    var newSeries = document.getElementById("newSeries");
    var notNew    = document.getElementById("notNew");
    $('input:radio[name="new_series"]').change(function() {
        if ($(this).val() == 'yes') {
            newSeries.style.display = "none";
            notNew.style.display = "block";
        } else {
            newSeries.style.display = "block";
            notNew.style.display = "none";
        }
    });
});

function getVolumes(vol) {
    if (vol == "") {
    } else { 
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        } else {
            // code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET","admin.php?page=addmanually?q="+vol,true);
        xmlhttp.send();
    }
}