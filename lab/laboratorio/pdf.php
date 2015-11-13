<?php
 /********** Norma 001 **********/
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';

/**************************************************************************************************/
/* Modificaciones al FPDF */
/**************************************************************************************************/

//include ('fpdf/fpdf.php');
include ($_SERVER['DOCUMENT_ROOT'].'/reportes/includes/fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        if ( !isset($this->header) )
        {
            $this->Image("../img/logoyeslogan.gif", 30, 5, 150, 40);
            $this->SetTextColor(69, 147, 56);
            $this->SetFont('Arial', '', 12);
            $this->SetY(45);
            $this->Cell(0, 2, utf8_decode('LABORATORIO DEL GRUPO MICROANALISIS, S.A. DE C.V.'), 0, 1, 'C');
        }
    }

    function Footer()
    {
        if ( !isset($this->footer) )
        {
            $this->SetY(-35);

            $this->SetTextColor(125);
            $this->SetFont('Arial', '', 6);
            $this->MultiCell(0, 3, utf8_decode('El presente informe no podrá ser alterado ni reproducido total o parcialmente sin autorización previa por escrito del Laboratorio del Grupo Microanálisis, S.A. de C.V.'), 0, 'C'); //////////// Dirección
            $this->Ln();

            $this->SetTextColor(69, 147, 56);
            $this->SetFont('Arial', 'B', 7);
            $this->Cell(0, 3, utf8_decode('General Sóstenes Rocha No. 28 Col. Magdalena Mixhuca Del. Venustiano Carranza, México D.F. CP 15850'), 0, 1, 'C');
            $this->Ln(1);
            $this->Cell(0, 3, utf8_decode('Tel. 01 (55) 57 68 77 44                E-Mail:ventas@microanalisis.com                Web: www.microanalisis.com'), 0, 1, 'C');
        }
    }

    var $widths;
    var $aligns;
    var $fonts;
    var $fontsizes;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function SetFonts($f)
    {
        //Set the array of fonts
        $this->nfonts=$f;
    }

    function SetFontSizes($fs)
    {
        //Set the array of font sizes
        $this->nfontsize=$fs;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb=0;
        $sh=array();
        $this->SetFont('Arial', '', 9);
        for($i=0;$i<count($data);$i++){
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

            //Se guarda la altura de cada texto
            $sh[]=$this->NbLines($this->widths[$i],$data[$i]);
        }
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            if( count($this->aligns) === 1 ){
                $a = $this->aligns[0];
            }elseif( isset($this->aligns[$i]) ){
                $a = $this->aligns[$i];
            }else{
                $a = 'L';
            }
            //$a=(count($this->aligns) === 1) ? $this->aligns[0] : isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h);

            //Número de renglones de separación arriba y abajo, se resta la altura
            //total menos la altura del texto, se divide entre dos (obtener altura de
            //arriba y de abajo) y esto entre 5 para obtener el número de renglones
            //según la altura del renglón, y así anexar dichos renglones extra al texto
            $nr = (($h-($sh[$i]*5))/2)/5;
            for ($j=0; $j < $nr; $j++){ 
                $data[$i]="\n".$data[$i]."\n";
            }
            if(count($this->nfonts) > 0 AND count($this->nfontsize) > 0){
                $b=(count($this->nfonts) === 1) ? $this->nfonts[0] : $this->nfonts[$i];
                $c=(count($this->nfontsize) === 1) ? $this->nfontsize[0] : $this->nfontsize[$i];
                $this->SetFont('Arial', $b, $c);
            }

            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a, true);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}

$pdf = new PDF();

/**************************************************************************************************/
/* Búsqueda de ordenes de la norma 001 */
/**************************************************************************************************/
if( isset($_GET['ot']) AND isset($_GET['idep']) )
{
    $errores = 0;
    $error = '';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try   
    {
        $sql='SELECT * FROM infomelabtbl WHERE ot=:ot AND idep=:idep';
        $s=$pdo->prepare($sql);
        $s->bindValue(':ot', $_GET['ot']);
        $s->bindValue(':idep', $_GET['idep']);
        $s->execute();
        $orden = $s->fetch(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error al tratar de obtener información de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    try   
    {
        $sql='SELECT Razon_Social FROM clientestbl WHERE Numero_Cliente=:id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['requerido']);
        $s->execute();
        $requerido = $s->fetch(PDO::FETCH_ASSOC);
        $orden['requerido'] = $requerido['Razon_Social'];
    }
    catch (PDOException $e)
    {
        $mensaje='Error al tratar de obtener información de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    try   
    {
        $sql='SELECT Razon_Social FROM clientestbl WHERE Numero_Cliente=:id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['Numero_Cliente']);
        $s->execute();
        $requerido = $s->fetch(PDO::FETCH_ASSOC);
        $orden['para'] = $requerido['Razon_Social'];
    }
    catch (PDOException $e)
    {
        $mensaje='Error al tratar de obtener información de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    /*echo '<pre>';
    var_dump($orden);
    echo '</pre>';*/

    try{
        $sql='SELECT nombre, apellido, firmaarchivar
            FROM labmuestreadorestbl
            INNER JOIN usuariostbl ON labmuestreadorestbl.usuarioidfk = usuariostbl.id
            WHERE informelabtbl = :id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['id']);
        $s->execute();
        $muestreadores = $s->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error, no se encontró signatario de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    /*echo '<pre>';
    var_dump($muestreadores);
    echo '</pre>';*/

    try{
        $sql='SELECT nombre, apellido, firmaarchivar
            FROM labsignatariostbl
            INNER JOIN usuariostbl ON labsignatariostbl.usuarioidfk = usuariostbl.id
            WHERE informelabtbl = :id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['id']);
        $s->execute();
        $signatarios = $s->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error, no se encontró signatario de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    /*echo '<pre>';
    var_dump($signatarios);
    echo '</pre>';*/

    try{
        $sql='SELECT muestranum, resultado, identificacion, clave, parametro, unidades, metodo, LD, LC
            FROM parainformetbl
            INNER JOIN aparametrostbl ON parainformetbl.parametroidfk = aparametrostbl.id
            WHERE informeidfk = :id AND aparametrostbl.clave = "GYA"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['id']);
        $s->execute();
        $gya = $s->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error, no se encontró signatario de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    try{
        $sql='SELECT muestranum, resultado, identificacion, clave, parametro, unidades, metodo, LD, LC
            FROM parainformetbl
            INNER JOIN aparametrostbl ON parainformetbl.parametroidfk = aparametrostbl.id
            WHERE informeidfk = :id AND aparametrostbl.clave = "NMPCF-AR1"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['id']);
        $s->execute();
        $coliformes = $s->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error, no se encontró signatario de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    try{
        $sql='SELECT muestranum, resultado, identificacion, clave, parametro, unidades, metodo, LD, LC
            FROM parainformetbl
            INNER JOIN aparametrostbl ON parainformetbl.parametroidfk = aparametrostbl.id
            WHERE informeidfk = :id AND aparametrostbl.clave <> "NMPCF-AR1" AND aparametrostbl.clave <> "GYA"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $orden['id']);
        $s->execute();
        $parametros = $s->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $mensaje='Error, no se encontró signatario de la orden. ';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

    /*echo '<pre>';
    var_dump($parametros);
    echo '</pre>';*/

    if(!$orden){
        $mensaje='Error al tratar de obtener información de la orden.';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
    }

/**************************************************************************************************/
/********************************************* Hoja 0 *********************************************/
/**************************************************************************************************/
    $pdf->AddPage();
    $pdf->SetMargins(20, 0, 25);

    $pdf->Ln(5);
    $pdf->SetTextColor(100);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(0, 3, 'AIR-F-11', 0, 1, 'R');
    $pdf->Ln(5);

    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 5, utf8_decode('INFORME DE RESULTADOS DE PRUEBA'), 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(125, 5, utf8_decode('REQUERIDO POR: '.$orden['requerido']), 0, 0);
    $pdf->Cell(0, 5, utf8_decode('Ref. Cliente: '.$orden['ot']), 0, 1);

    $pdf->Cell(125, 5, utf8_decode('PARA: '.$orden['para']), 0, 0);
    $pdf->Cell(0, 5, utf8_decode('N. I. de P.: '.$orden['idep']), 0, 1);
    $pdf->Ln(10);

    $muestreadopor = '';
    foreach ($muestreadores as $key => $value) {
        $muestreadopor .= $value['nombre'].' '.$value['apellido'].', ';
    }
    $muestreadopor = rtrim($muestreadopor, ', ');
    $pdf->MultiCell(0, 5, utf8_decode('INFORMACION DE LA MUESTRA: Aguas muestreadas por '.$muestreadopor.' en contenedores y conservadores adecuados, entregadas a nuestro laboratorio el '.$orden['fecharecepcion'].'.'), 0);
    $pdf->Ln(10);

    $fechamuestreo = $orden['fechamui'];
    if( !is_null($orden['fechamuf']) ){
        $fechamuestreo .= ' y '.date('d', strtotime($orden['fechamuf']));
    }
    $pdf->Cell(82.5, 5, utf8_decode('FECHA DE MUESTREO: '.$fechamuestreo), 0, 0, 'C');

    $fechaanalisis = $orden['fechani'];
    if( !is_null($orden['fechanf']) ){
        $fechaanalisis .= ' al '.date('d', strtotime($orden['fechanf']));
    }
    $pdf->Cell(0, 5, utf8_decode('LAPSO DE ANALISIS: '.$fechaanalisis), 0, 1, 'C');

    $pdf->Cell(0, 5, utf8_decode('FECHA DE INFORME: '.$orden['fechainforme']), 0, 1, 'C');
    $pdf->Ln(10);

    if($gya){
        $pdf->Cell(100, 5, utf8_decode('Parámetro: '.$gya[0]['parametro']), 0, 0);
        $pdf->Cell(0, 5, utf8_decode('Método: '.$gya[0]['metodo']), 0, 1, 'R');
        $pdf->SetFillColor(200);
        $pdf->SetWidths(array(20,45,40,45,15));
        $pdf->SetAligns(array('C'));
        $pdf->SetFonts(array('B'));
        $pdf->SetFontSizes(array(9));
        $pdf->Row(array('No. Mtra.', utf8_decode('Identificación'), 'Unidad', 'Resultado', 'M.C.'));

        $pdf->SetFillColor(255);
        $pdf->SetFonts(array(''));
        $pdf->SetAligns(array('C','L','C','C','C'));
        foreach ($gya as $key => $value) {
            $pdf->Row(array($value['muestranum'],
                            utf8_decode($value['identificacion']),
                            $value['unidades'],
                            utf8_decode($value['resultado']),
                            $value['LC'])
                    );
        }
        $pdf->Ln(10);
    }

    if($coliformes){
        $pdf->Cell(100, 5, utf8_decode('Parámetro: '.$coliformes[0]['parametro']), 0, 0);
        $pdf->Cell(0, 5, utf8_decode('Método: '.$coliformes[0]['metodo']), 0, 1, 'R');
        $pdf->SetFillColor(200);
        $pdf->SetWidths(array(20,45,40,45,15));
        $pdf->SetAligns(array('C'));
        $pdf->SetFonts(array('B'));
        $pdf->SetFontSizes(array(9));
        $pdf->Row(array('No. Mtra.', utf8_decode('Identificación'), 'Unidad', 'Resultado', 'M.C.'));

        $pdf->SetFillColor(255);
        $pdf->SetFonts(array(''));
        $pdf->SetAligns(array('C','L','C','C','C'));
        foreach ($coliformes as $key => $value) {
            $pdf->Row(array($value['muestranum'],
                            utf8_decode($value['identificacion']),
                            $value['unidades'],
                            utf8_decode($value['resultado']),
                            $value['LC'])
                    );
        }
        $pdf->Ln(15);
    }

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5, utf8_decode('Hoja 1 de 2'), 0, 0, 'R');

/**************************************************************************************************/
/********************************************* Hoja 0 *********************************************/
/**************************************************************************************************/
    $pdf->AddPage();
    $pdf->SetMargins(20, 0, 25);
    $pdf->Ln(15);

    $pdf->Cell(0, 5, utf8_decode('No. de Muestra: '.$parametros[0]['muestranum']), 0, 1);
    $pdf->Cell(0, 5, utf8_decode('Identificación: '.$parametros[0]['identificacion']), 0, 1);
    $pdf->SetFillColor(200);
    $pdf->SetWidths(array(50,20,25,40,15,15));
    $pdf->SetAligns(array('C'));
    $pdf->SetFonts(array('B'));
    $pdf->SetFontSizes(array(9));
    $pdf->Row(array(utf8_decode('Parámetro'), 'Unidad', 'Resultado', utf8_decode('Método'), 'L.D.','L.C.'));

    $pdf->SetFillColor(255);
    $pdf->SetFonts(array(''));
    $pdf->SetAligns(array('L','C','C','C','C','C'));
    foreach ($parametros as $key => $value) {
        $pdf->Row(array(utf8_decode($value['parametro']),
                        $value['unidades'],
                        utf8_decode($value['resultado']),
                        $value['metodo'],
                        $value['LD'],
                        $value['LC'])
                );
    }
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, utf8_decode('L. D. = Límite de Detección, L. C. = Límite de Cuantificación'), 0, 1);
    $pdf->Cell(0, 4, utf8_decode('N. A. = No Aplica, *M. C.= Mínimo a Cuantificar'), 0, 1);

    $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(0, 4, utf8_decode('ACREDITACIÓN EMA No. '.$orden['acrednombre'].' ('.$orden['acredestudio'].') Vigencia : A partir del '.date('d',strtotime($orden['acredfecha'])).' de '.$meses[date('n', strtotime($orden['acredfecha']))-1].' '.date('Y', strtotime($orden['acredfecha'])).'.'), 0, 1);

    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(0, 2, utf8_decode('Nota: El término a adicionar o substraer del resultado dado en cada caso, que define los valores de los límites superior e inferior del intervalo de confianza a 95%, fue obtenido experimentalmente con la aplicación del procedimiento analítico en muestras sintéticas, por lo que pudiera diferir del que se alcance en la matriz problema. En consecuencia, esa expresión de la incertidumbre deberá ser interpretada con las reservas del caso.'), 0);
    $pdf->Ln(15);


    $firma1 = $_SERVER['DOCUMENT_ROOT'].'/reportes/muestreadores/archivo/'.$signatarios[0]['firmaarchivar'];
    $firma2 = $_SERVER['DOCUMENT_ROOT'].'/reportes/muestreadores/archivo/'.$signatarios[1]['firmaarchivar'];
    $firma3 = $_SERVER['DOCUMENT_ROOT'].'/reportes/muestreadores/archivo/'.$signatarios[2]['firmaarchivar'];

    $down = count($parametros) * 5;
    $pdf->Image($firma1, 50, 105 + $down, 30, 15);
    $pdf->Image($firma2, 100, 105 + $down, 30, 15);
    $pdf->Image($firma2, 150, 105 + $down, 30, 15);


    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(20, 5, utf8_decode('Signatarios:'), 0, 0);
    $pdf->SetFont('Arial', 'U', 8);
    $pdf->Cell(48.3, 3, utf8_decode('                                                         '), 0, 0, 'C');
    $pdf->Cell(48.3, 3, utf8_decode('                                                         '), 0, 0, 'C');
    $pdf->Cell(48.3, 3, utf8_decode('                                                         '), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(20, 5, '', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(48.3, 5, utf8_decode($signatarios[0]['nombre'].' '.$signatarios[0]['apellido']), 0, 0, 'C');
    $pdf->Cell(48.3, 5, utf8_decode($signatarios[1]['nombre'].' '.$signatarios[1]['apellido']), 0, 0, 'C');
    $pdf->Cell(48.3, 5, utf8_decode($signatarios[2]['nombre'].' '.$signatarios[2]['apellido']), 0, 1, 'C');

    $pdf->SetFont('Arial', '', 6);
    $pdf->MultiCell(0, 2, utf8_decode('ESTE INFORME QUE REPRESENTA LAS CARACTERISTICAS DE LA MUESTRA RECIBIDA, MAS NO DEL UNIVERSO DE DONDE DERIVA, NO PODRA SER ALTERADO O REPRODUCIDO TOTAL O PARCIALMENTE SIN AUTORIZACION POR ESCRITO DEL LABORATORIO DEL GRUPO MICROANALISIS, S.A. DE C.V.'), 0);
    $pdf->Ln(15);

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 5, utf8_decode('Hoja 2 de 2'), 0, 0, 'R');

    $pdf->Output();
    exit();
}
?>