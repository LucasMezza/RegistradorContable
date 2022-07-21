<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Registador</title>
</head>
<body onload="totales()">

    <?php 
    $con = new mysqli("localhost", "root", "", "registrador");

    function cargarTipo($tipo){
        global $con;
        $sql = "SELECT * FROM $tipo";
        $stmt = $con->prepare($sql);
        
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        return $resultado;
    }
    ?>

    <div class="navbar">
        <p>REGISTRADOR CONTABLE</p>
    </div>
    
    <header>
        <div class="cabecero" >
            <div id="totales">
                <div class="monto_total">
                    <div class="saldo_total">SALDO TOTAL</div>
                    <div class="presupuesto" id="presupuesto"></div>
                </div>
                <div class="presupuesto_ingresos">
                    <div class="orden1">Ingresos</div>
                    <div class="orden" id="totalIngresos"></div>
                </div>
                <div class="presupuesto_egresos">
                    <div class="orden1">Egresos</div>
                    <div class="orden" id="totalEgresos"></div>
                </div>       
            </div>

            <div class="formulario">
                <div class="cuerpo_formulario">
                    <select class="select" name="select" id="form_select">
                        <option value="ingresos" selected>+</option>
                        <option value="egresos">-</option>
                    </select>
                    <input type="text" class="input" id="form_descripcion" name="descripcion" placeholder="  Ingrese descripcion" size="30" required>
                    <input type="number" id="form_numero" class="numero" name="valor" placeholder=" 0" min="0"  step="any" size="3" required>
                    <button type="submit" class="boton" value="Ingresar" name="boton" onclick="agregar_campo()">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <section class="cuerpo">
        <div class="cuadro_ingreso" id="ingreso" value="ingreso">
            <h1>INGRESOS</h1>
            <?php $ingresos = cargarTipo('ingresos')?>
            <?php while($data = $ingresos->fetch_object()) { ?>
                <div class="elemento">
                    <div class="espacio_left" id="descripcion_ingreso"><?php echo $data->descripcion; ?></div>
                    <div class="derecha limpiar_estilos">
                        <div class="valor espacio_right" id="numero_ingreso"><?php echo "$".$data->valor; ?></div>
                        <div class="elemento_eliminar">
                            <button data-id="<?php echo $data->id; ?>" class="btn_eliminar espacio_right" onclick="eliminar_ingreso(this)" >
                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="cuadro_egreso" id="egreso" value="egreso">
            <h1>EGRESOS</h1>
            <?php $egresos = cargarTipo('egresos')?>
            <?php while($data = $egresos->fetch_object()) { ?>
                <div class="elemento">
                    <div class="espacio_left" id="descripcion_egreso"><?php echo $data->descripcion; ?></div>
                    <div class="derecha limpiar_estilos">
                        <div class="valor espacio_right" id="numero_egreso"><?php echo "$".$data->valor; ?></div>
                        <div class="elemento_eliminar">
                            <button data-id="<?php echo $data->id; ?>" class="btn_eliminar espacio_right" onclick="eliminar_egreso(this)">
                                <i class="fa fa-times-circle" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </section>

    <footer class="info" id="datos">
        <div class="redes_sociales">
            <span>By LucasMezza</span>
            <a href="https://github.com/LucasMezza?tab=repositories" target="_blank"><i class="fa fa-github" aria-hidden="true"></i></a>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function totales(){
            $.ajax({
                "url": "funciones.php",
                "type": "post",
                "dataType": "json",
                "data":{
                    "funcion": "calcular_totales",
                },success:function(r){
                    console.log(r);
                    if (r.error == 0) {
                        $("#presupuesto").html(r.total);
                        $("#totalIngresos").html(r.totalIngresos);
                        $("#totalEgresos").html(r.totalEgresos);
                    }
                },
            });
        }
        
        function agregar_campo(){
            let tipo = $("#form_select").val();
            let descripcion = $("#form_descripcion").val();
            let numero = $("#form_numero").val();

            $.ajax({
                "url": "funciones.php",
                "type": "post",
                "dataType": "json",
                "data":{
                    "tipo": tipo,
                    "descripcion": descripcion,
                    "numero": numero,
                    "funcion": "agregar_tipo",
                },success:function(r){
                    if (r.error == 0) {
                        $("#presupuesto").html(r.total);
                        if(tipo == "ingresos"){
                            $("#totalIngresos").html(r.totalIngresos);
                            var funcion = "eliminar_ingreso(this)";
                        } else {
                            $("#totalEgresos").html(r.totalEgresos);
                            var funcion = "eliminar_egreso(this)";
                        }
                        html=`  <div class="elemento">
                                <div class="espacio_left">${descripcion}</div>
                                <div class="derecha limpiar_estilos">
                                    <div class="valor espacio_right">$${numero}</div>
                                    <div class="elemento_eliminar">
                                        <button data-id="${r.id}" class="btn_eliminar" onclick="${funcion}" >
                                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                                        </button>
                                </div>
                                </div>
                                </div>`;
                        if (tipo == "ingresos") {
                            $("#ingreso").append(html);
                    
                        } else{
                            $("#egreso").append(html);
                        }
                    }
                },
            });

        }

        function eliminar_ingreso(e){
            //Para eliminar el campo solo necesitamos tipo e id
            let tipo = "ingresos";
            //Conseguimos con JS el id del elemento a eliminar
            let id = $(e).attr("data-id");
            console.log(id);

            eliminar_campo(tipo, id);
            //Ahora tenemos que eliminar el elemento
            //Por ende, vamos a buscar el elemento general y lo removemos
            //Ya que e = elemento seleccionado
            //Y nuestro elemento seleccionado es el boton, hay que buscar al padre general
            $(e).parent().parent().parent().remove();
        }

        function eliminar_egreso(e){
            //Para eliminar el campo solo necesitamos tipo e id
            let tipo = "egresos";
            //Conseguimos con JS el id del elemento a eliminar
            let id = $(e).attr("data-id");
            console.log(id);

            eliminar_campo(tipo, id);
            //Ahora tenemos que eliminar el elemento
            //Por ende, vamos a buscar el elemento general y lo removemos
            //Ya que e = elemento seleccionado
            // nuestro elemento seleccionado es el boton, hay que buscar al padre general
            $(e).parent().parent().parent().remove();
        }

        function eliminar_campo(tipo, id){
            $.ajax({
                "url": "funciones.php",
                "type": "post",
                "dataType": "json",
                "data":{
                    "tipo": tipo,
                    "id": id,
                    "funcion": "eliminar",
                },success:function(r){
                    if (r.error == 0) {
                        $("#presupuesto").html(r.total);
                        html=``;
                        if (tipo == "ingresos") {
                            $("#ingreso").append(html);
                            $("#totalIngresos").html(r.totalIngresos);
                        } else{
                            $("#egreso").append(html);
                            $("#totalEgresos").html(r.totalEgresos);
                        }
                    }
                },
            });

        }
    </script>
</body>
</html>