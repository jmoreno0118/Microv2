<?php
  
if (!empty($_POST['numedicion']) && !empty($_POST['rci']) )
{
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try   
    {
        $sql='SELECT id FROM puntostbl
            INNER JOIN puntorecilumtbl ON puntostbl.id = puntorecilumtbl.puntoidfk
            WHERE puntorecilumtbl.recilumidfk = :rci AND puntostbl.medicion = :numedicion';
        $s=$pdo->prepare($sql); 
        $s->bindValue(':rci',$_POST['rci']);
        $s->bindValue(':numedicion',$_POST['numedicion']);
        $s->execute();
    }
    catch (PDOException $e)
    {
        $mensaje='Hubo un error extrayendo la información de parametros.'.$e;
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }
    $punto = $s->fetchAll();

    if( strcmp($_POST['id'], '') === 0 AND count($punto) < 1 )
    {
        echo "true";
    }
    elseif( strcmp($_POST['id'], '') !== 0 AND strcmp($_POST['id'], $punto[0]['id']) === 0 )
    {
        echo "true";
    }
    else
    {
        echo "false";
    }
}
else
{
    echo "false";
}
