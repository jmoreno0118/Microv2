<?php
 /********** Norma 001 **********/
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';

/**************************************************************************************************/
/* Búsqueda de ordenes de la norma 001 */
/**************************************************************************************************/
if (isset($_POST['accion']) AND ($_POST['accion']=='buscar' OR $_POST['accion']=='preliminar'))
{	
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    $ot = $_POST['ot'];
    try   
    {
        $sql='SELECT generalesaguatbl.numedicion, generalesaguatbl.nom01maximosidfk, muestreosaguatbl.fechamuestreo,
                    muestreosaguatbl.identificacion, muestreosaguatbl.id as "muestreoaguaid"
              FROM clientestbl
              INNER JOIN ordenestbl ON clientestbl.Numero_Cliente = ordenestbl.clienteidfk
              INNER JOIN generalesaguatbl ON ordenestbl.id = generalesaguatbl.ordenaguaidfk
              INNER JOIN muestreosaguatbl ON generalesaguatbl.id = muestreosaguatbl.generalaguaidfk
              INNER JOIN estudiostbl ON ordenestbl.id = estudiostbl.ordenidfk
              WHERE estudiostbl.nombre="NOM 002" AND ordenestbl.ot = :ot AND generalesaguatbl.estudio = "nom002"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':ot', $ot);
        $s->execute();
        $orden = $s->fetchAll();

        $sql='SELECT parametrostbl.*
             FROM parametrostbl
             INNER JOIN muestreosaguatbl ON muestreosaguatbl.id = parametrostbl.muestreoaguaidfk
             INNER JOIN generalesaguatbl ON generalesaguatbl.id = muestreosaguatbl.generalaguaidfk
             INNER JOIN ordenestbl ON ordenestbl.id = generalesaguatbl.ordenaguaidfk
             WHERE ordenestbl.ot = :ot AND generalesaguatbl.estudio = "nom002"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':ot', $ot);
        $s->execute();
        $parametrospuro = $s->fetchAll();

        foreach ($parametrospuro as $key => $value) {
            $parametros[$value['muestreoaguaidfk']] = $value;
        }

        foreach ($orden as $key => $value) {
            $sql='SELECT *
                  FROM maximostbl
                  WHERE id = :id';
            $s = $pdo->prepare($sql);
            $s->bindValue(':id', $value['nom01maximosidfk']);
            $s->execute();
            $maximos[$value['muestreoaguaid']] = $s->fetch();
        }

        foreach ($parametros as $key => $value) {
            $sql='SELECT *
                  FROM adicionalestbl
                  WHERE parametroidfk = :id';
            $s=$pdo->prepare($sql);
            $s->bindValue(':id', $value['id']);
            $s->execute();
            $adicionales[$key] = "";
            foreach ($s as $linea) {
                $adicionales[$key][]=array("nombre" => $linea["nombre"],
                                            "unidades" => $linea["unidades"],
                                            "resultado" => $linea["resultado"]);
            }
        }
    }
    catch (PDOException $e)
    {
        $mensaje='Hubo un error extrayendo la orden.'.$e;
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }
    include 'formaorden.html.php';
    exit();
}

/**************************************************************************************************/
/* Acción por defualt, llevar a búsqueda de ordenes */
/**************************************************************************************************/
include 'formabuscaordenes.html.php';
exit();