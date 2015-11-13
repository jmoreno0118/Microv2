<?php
  
if (!empty($_POST['fecha']) && !empty($_POST['fechafin']) )
{
    if( strtotime($_POST['fecha']) > strtotime($_POST['fechafin']) OR strtotime($_POST['fecha']) === strtotime($_POST['fechafin']) )
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