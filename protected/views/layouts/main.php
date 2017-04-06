<!doctype html>

<html>
<head>
  	<meta charset="utf-8">
	
	<!-- og image come first, fast cache facebook ! -->
	<meta property="og:image" content="<?=Yii::app()->getBaseUrl(true)?>/thumbnail?v=<?= Yii::app()->request->getQuery("v") ?>"/>
	
  	<title>&#8635; <?=$this->title?> :: youtube9loop</title>

  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta name="description" content="a tool for repeating your favourite youtube video or your favourite music">
  	<meta name="author" content="ulikela.com">
	<meta name="keywords" content="youtube, video, mtv, music, repeat, webtool, utilities, song, daft punk">

	<!-- facebook OG -->
	<!--meta property="og:image" content="<?=Yii::app()->getBaseUrl(true)?>/img/fb_img.png"/>-->
	
	<meta property="og:title" content="&#8635; <?=htmlspecialchars($this->title)?> :: youtube9loop"/>
	<meta property="og:site_name" content="YOUTUBE9LOOP"/>
	<meta property="og:type" content="website"/>
	<meta property="fb:app_id" content="761376527223987"/>
	<meta property="og:url" content="<?=Yii::app()->getBaseUrl(true).Yii::app()->request->getUrl()?>"/>
	<meta property="og:description" content="The song list contains <?=$this->numOfSong?> song<?=($this->numOfSong>1)?"s":""?> :: loop your fav youtube music"/>

	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon_32.png" sizes="32x32">
	<link rel="image_src" href="<?=Yii::app()->getBaseUrl(true)?>/thumbnail?v=<?= Yii::app()->request->getQuery("v") ?>" />
	
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

  	<link rel="stylesheet" href="css/style.css?v=3">

    <!-- Google web fonts -->
    <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
    
    <!-- jQuery UI for testing -->
    <link href="//code.jquery.com/ui/1.10.4/themes/cupertino/jquery-ui.css" rel="stylesheet">
	
	<!-- bxSlider CSS file -->
	<link href="/css/jquery.bxslider.css" rel="stylesheet" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script src="js/jquery.ui.touch-punch.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <!-- js/jquery plugin -->
    <script src="js/jquery.visible.js"></script>
	<script src="js/jquery.sticky.js"></script>
	<!-- js/bxslider plugin -->
	<script src="js/jquery.bxslider.js"></script>
	<!-- js/cookies plugin -->
	<script src="js/jquery.cookie.js"></script>
	
	<!-- google analytics -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	  ga('create', 'UA-47079201-1', 'youtube9loop.com');
	  ga('send', 'pageview');
	
	</script>
	<!-- 9 loop script -->
	<script src="js/loopSearchBox.js"></script>
	<script src="js/customPlayer.js"></script>
	<script src="js/statUtil.js"></script>
  </head>

<body class="level_0">

	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=761376527223987";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
 
	<?= $content ?>

</body>
</html>
