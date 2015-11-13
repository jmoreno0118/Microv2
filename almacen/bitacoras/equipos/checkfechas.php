<?php
if (!empty($_POST['fechahoraentrega']) && !empty($_POST['equipoid']) && !empty($_POST['bitacoraid']) )
{
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try   
    {
     $sql='SELECT fechahoraentrega, fechahoradevolucion FROM bitacoraeqtbl
          WHERE equipoidfk = :equipoidfk AND bitacoraidfk <> :bitacoraidfk';
     $s=$pdo->prepare($sql); 
     $s->bindValue(':equipoidfk',$_POST['equipoid']);
     $s->bindValue(':bitacoraidfk',$_POST['bitacoraid']);
     $s->execute();
    }
    catch (PDOException $e)
    {
     $mensaje='Hubo un error extrayendo la información de parametros.';
     include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
     exit();
    }
    /*$true = 0;*/
    $false = 0;
    //var_dump($s->fetchAll(PDO::FETCH_ASSOC));
    foreach($s->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
        if( is_null($value['fechahoradevolucion']) ){
            $false++;
        }else{
            if(strtotime($_POST['fechahoraentrega']) > strtotime($value['fechahoraentrega']) ){
                /*if(strtotime($_POST['fechahoraentrega']) > strtotime($value['fechahoradevolucion']) ){
                    $true++;
                }else*/if(strtotime($_POST['fechahoraentrega']) < strtotime($value['fechahoradevolucion']) ){
                    $false++;
                }
            }
        }
    }
    if($false > 0)
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