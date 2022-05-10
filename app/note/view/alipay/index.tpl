<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://gw.alipayobjects.com/as/g/h5-lib/alipayjsapi/3.1.1/alipayjsapi.inc.min.js"></script>
<button id="J_btn" class="btn btn-default">支付</button>
<script>

    $("#J_btn").click(function (){
        ap.tradePay({
            tradeNO: '201802282100100427058809844'
        }, function(res){
            ap.alert(res.resultCode);
        });
    })
</script>
</body>
</html>