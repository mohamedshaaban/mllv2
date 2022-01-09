<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

     

        <!-- FontAwesome CSS -->
        <script src="https://kit.fontawesome.com/667634235d.js" crossorigin="anonymous"></script>
        <link id="" rel="shortcut icon" href="/favicon.ico?" />
        <title>شركة أم أل أل للجر والمساعدة للمركبات على الطرق</title>
    </head>
    <body class="page-bg-payment">

       
        @yield('content')
      

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="{{ asset('/js/jquery.js')}}" ></script>
        <script src="{{ asset('/js/popper.min.js')}}"  ></script>
        <script src="{{ asset('/js/bootstrap.min.js')}}" ></script>
        <script src="{{ asset('/js/slick.min.js')}}"></script>
        <script src="{{ asset('/js/wow.min.js')}}"></script>
        <script src="{{ asset('/js/select2.min.js')}}"></script>
        <script src="{{ asset('/js/custom.js')}}"></script>
       <script type="text/javascript" src="/js/html2pdf.bundle.min.js"></script>
<script type="text/javascript" src="https://html2canvas.hertzen.com/dist/html2canvas.js"></script>
        <script>
                      $( document ).ready(function() {
           
 
$('#cmd').click(function () {
    $('#cmd').hide();
    var element = document.getElementById('content');
html2pdf(element);
 setTimeout(function(){$('#cmd').show(); }, 1000);

});
});
        </script>
        @yield('jscustomer')
    </body>
</html>
