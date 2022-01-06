function _print(print) {
    if(typeof(print)==='undefined') var print='print';
    var prtContent = document.getElementById(print);
    var location = "//"+window.location.hostname+"/";

    var WinPrint = window.open('', 'Print', 'letf=0,top=0,width="100%",height="700",toolbar="0",scrollbars="1",status="0"');

    WinPrint.document.write('<html><head><title>Alesharide</title>');
    WinPrint.document.write('<link href="'+location+'admin_assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="all"></link>');
    WinPrint.document.write('<link href="'+location+'css/print.css" rel="stylesheet" media="all"></link>');
    WinPrint.document.write('</head><body >');
    WinPrint.document.write(prtContent.innerHTML);    
    WinPrint.document.write('</body></html>');

    WinPrint.document.close();
    WinPrint.focus();
	WinPrint.print();
	WinPrint.close(); 
}

