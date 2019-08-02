<!DOCTYPE html>
<!-- cTurtle98 Website Index file -->
<html>
    <head>
        <?php
            $path=$_GET["path"];
            if ($path == "") {
                $path="home";
            }
        ?>
        
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-67214315-6"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'UA-67214315-6');
        </script>
        <!-- /Google Analytics -->
        
        <link rel="stylesheet" href="/include/main.css">
        
        <!-- INCLUDE HEAD FOR EACH PAGE -->
        <?php
            include($_SERVER['DOCUMENT_ROOT'] . "/$path/head.php")
        ?>
        
        <!-- JQUERY STUFFS -->
        <script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
        
    </head>
    <body>
        <div id="header">
            <a id="header-title">cTurtle98</a>
            <a id="header-tagline">Ciaran Farley</a>
        </div>
        <div id="menu">
            <nav>
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/ham/">HAM</a></li>
                    <!--<li><a href="internet-speed-log/">Internet Speed Log</a></li>-->
                </ul>
            </nav>
        </div>
        <div id="content">
            <!-- INCLUDE BODY FOR EACH PAGE -->
            <?php
            include($_SERVER['DOCUMENT_ROOT'] . "/$path/content.php")
            ?>
        </div>
    </body>
</html>