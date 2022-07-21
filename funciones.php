<?php 
$con = new mysqli("localhost", "root", "", "registrador");

$funcion = isset($_POST['funcion']) ? $_POST["funcion"] : "";

if($funcion == "calcular_totales"){
    $total = get_total();
    $totalIngresos = datos_iniciales('ingresos');
    $totalEgresos = datos_iniciales('egresos');
    echo json_encode(array(
        "error"=>0,
        "total"=>$total,
        "totalIngresos"=>$totalIngresos,
        "totalEgresos"=>$totalEgresos
    ));
} else{
    $tipo = isset($_POST['tipo']) ? $_POST["tipo"] : "";
    $descripcion = isset($_POST['descripcion']) ? $_POST["descripcion"] : "";
    $numero = isset($_POST['numero']) ? $_POST["numero"] : "";
    $id = isset($_POST['id']) ? $_POST["id"] : "";
    
    if ($funcion == "agregar_tipo") {
        $sql = "INSERT INTO $tipo (descripcion, valor) VALUES ('$descripcion', '$numero') ";

        $stmt = $con->prepare($sql);

        $stmt->execute();
        $resultado = $stmt->get_result();
        
        $id = $stmt->insert_id;

        $total = get_total();
        $totalDatos = get_totalDatos('+', $tipo, $numero);
        if($tipo == "ingresos"){
            echo json_encode(array(
                "error"=>0,
                "total"=>$total,
                "totalIngresos"=>$totalDatos,
                "id"=>$id,
            ));
        } else{
            echo json_encode(array(
                "error"=>0,
                "total"=>$total,
                "totalEgresos"=>$totalDatos,
                "id"=>$id,
            ));
        }
    } else{
        if ($funcion == "eliminar") {
            $sql = "DELETE FROM $tipo WHERE id = '$id'";
            $stmt = $con->prepare($sql);

            $stmt->execute();
            $resultado = $stmt->get_result();

            $total = get_total();
            $totalDatos = get_totalDatos('-', $tipo, $numero);
            if($tipo == "ingresos"){
                echo json_encode(array(
                    "error"=>0,
                    "total"=>$total,
                    "totalIngresos"=>$totalDatos
                ));
            } else{
                echo json_encode(array(
                    "error"=>0,
                    "total"=>$total,
                    "totalEgresos"=>$totalDatos
                ));
            }
        }
    }
}

function get_total(){
    $datos = cargarTipo('ingresos');
    $totalIngresos = 0;
    while($data = $datos->fetch_object()){
        $totalIngresos += $data->valor;
    }
    
    $totalEgresos = 0;
    $datos = cargarTipo('egresos');
    while($data = $datos->fetch_object()){
        $totalEgresos += $data->valor;
    }
    
    $total = $totalIngresos - $totalEgresos;

    return "$".$total;
}




function cargarTipo($tipo){
    global $con;
    $sql = "SELECT * FROM $tipo";
    $stmt = $con->prepare($sql);
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    return $resultado;
}

function get_totalDatos($signo, $tipo, $numero){
    $datos = cargarTipo($tipo);

    $totalDatos = 0;
    while($data = $datos->fetch_object()){
        $totalDatos += $data->valor;
    }

    return "$".$totalDatos;
}

function datos_iniciales($tipo){
    $datos = cargarTipo($tipo);

    $total = 0;
    while($data = $datos->fetch_object()){
        $total+= $data->valor;
    }
    
    return "$".$total;
}
?>