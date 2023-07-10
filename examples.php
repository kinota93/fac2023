<html lang="ja">
<head>
<meta http-equiv="Content-TYPE" content="text/html; charset=UTF-8">
</head>
<body>
<ol>

<?php
$urls = [
    "dat_yaml.php"=>"data in yaml",
    "dat_array.php"=>"data in php array",
    "available.php?y=2024"=>"undefined calendar",
    "available.php?f=12207"=>"undefined facility",
    "available.php"=>"available of 12216 on 2023-8",
    "available.php?y=2023&m=8"=>"given year, month, default facility(12216)",
    "available.php?y=2023&m=8&f=12107"=>"given year,month and facility(12107)",
    "available.php?y=2023&m=8&f=12216"=>"given year,month and facility(12216)",
    "available.php?y=2023&m=8&f=12311"=>"given year,month and facility(12311)",
    "available.php?y=2023&m=9&f=12107"=>"given year,month and facility(12107)",
    "available.php?y=2023&m=9&f=12216"=>"given year,month and facility(12216)",
    "available.php?y=2023&m=9&f=12311"=>"given year,month and facility(12311)",
];
foreach ($urls as $url=>$label){
    printf ('<li><a href="%s">%s</a>', $url,$label);
}
?>
</ol>
</body>
</html>
