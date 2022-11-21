function whereisuser(wwwroot, blockid){

    inputobj = document.getElementById('id_searchwhereis');	
    url = wwwroot+'/auth/multimnet/ajax/whereis.php?query='+inputobj.value;
    $.post(url, function(data) {
        $('#whereisresults'+blockid).html(data);
    });
}