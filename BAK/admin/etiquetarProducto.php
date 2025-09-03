<?php
echo "<center>".$_GET['nombre']." (".$_GET['codigo'].")</center>";
echo "<center><img alt='testing' src='barcode.php?codetype=Code128&size=50&text=".$_GET['cb']."&print=true'/></center>";
echo "<center>$".number_format($_GET['precio'],2)."</center>";
?>