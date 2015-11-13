<?php
  
if (!empty($_POST['fechahoraentrega']) && !empty($_POST['fechahoradevolucion']) )
{
    if( strtotime($_POST['fechahoraentrega']) > strtotime($_POST['fechahoradevolucion']) OR strtotime($_POST['fechahoraentrega']) === strtotime($_POST['fechahoradevolucion']) )
    {
        echo "false";
    }
    else
    {
        echo "true";
    }
}
else
{
    echo "false";
}
?>