
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Basic HTML Example</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <!-- Flipbook StyleSheet -->
  <link href="/resources/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">

  <!-- Icons Stylesheet -->
  <link href="/resources/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">

  <style>
    body, html {
      height: 100%;
      margin: 0;
    }

    .row {
      padding-top: 20px;
    }

    a {
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="container">


  <div class="row">

    <div class="col-xs-3">

      <!--Thumbnail Lightbox-->
      <div
              class="_df_thumb"
              id="df_manual_thumb"
              tags="3d,images"
              
              thumb="https://jquery.dearflip.com/examples/jquery/example-assets/books/thumbs/dflip.jpg"><!--Recommend using absolute path to PDFs-->
        Images
      </div>


    </div>

    


  </div>


</div>


<br>
</div>

<!-- jQuery 1.9.1 or above -->
<script src="/resources/dflip/js/libs/jquery.min.js" type="text/javascript"></script>

<!-- Flipbook main Js file -->
<script src="/resources/dflip/js/dflip.min.js" type="text/javascript"></script>

<!-- <script src="https://jquery.dearflip.com/examples/jquery/example-assets/js/prism.js?_=1701340403601" type="text/javascript"></script> -->

<script>

var insImage = [];
	
insImage.push('/uploads/noti/20231129/1701247133_65c80bc9f8dfc1cae3f4.jpg');
insImage.push('/uploads/noti/20231129/1701247133_b5baf384fd4a5ea72f16.jpg');
insImage.push('/uploads/noti/20231129/1701247133_f3276457253ff5b03f04.jpg');
insImage.push('/uploads/noti/20231129/1701247133_27a945c2d31cd95a5a2c.jpg');
insImage.push('/uploads/noti/20231129/1701247133_01e53bb6783d1b7dc68a.jpg');

	
	var option_df_manual_thumb = {
		source : insImage,
		webgl:false,
	};

</script>

</body>
</html>
