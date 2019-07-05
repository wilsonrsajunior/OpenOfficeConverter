<?php
    $xpto = get_headers('http://192.168.33.10/modules/OpenOfficeConverter/Api/Server.php?from=oi');
    echo "<pre>";
    print_r($xpto);
    die;

?>
