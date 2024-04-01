<!DOCTYPE html>
<html>
    <head>
        <title>FRANCISPARKER ERROR </title>
        <script type="text/javascript">
            window.onload = function(){
                window.addEventListener("flutterInAppWebViewPlatformReady", function(event) {
                    window.flutter_inappwebview.callHandler("call", "reauth").then(function(result) {
                        console.log('logout' + result); 
                    });
                });
            }

        </script>
    </head>
    <body>wranning!!</body>
</html>

