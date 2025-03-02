<?php
class ViewTurno {

    // Muestra el formulario para crear un turno
    public function showCreateTurnoForm() {
        //echo '<h2>Crear Turno</h2>';
        //echo '<form method="POST" action="' . BASE_URL . 'turnos/create">
               // <label for="fecha_ingreso">Fecha de Ingreso:</label>
                //<<input type="date" id="fecha_ingreso" name="fecha_ingreso" required><br>

                //<label for="fecha_entrega">Fecha de Entrega:</label>
                //<input type="date" id="fecha_entrega" name="fecha_entrega" required><br>

                //<label for="id_cliente">ID Cliente:</label>
                //<input type="number" id="id_cliente" name="id_cliente" required><br>

                //<input type="submit" value="Crear Turno">
             // </form>';
    }

    // Muestra un mensaje de error
    public function showError($message) {
        echo '<div style="color: red;">' . $message . '</div>';
    }

    // Muestra los turnos de un cliente
    public function showTurnosByCliente($turnos) {
        if (empty($turnos)) {
            echo '<p>No hay turnos para este cliente.</p>';
        } else {
            echo '<h2>Turnos del Cliente</h2>';
            echo '<ul>';
            foreach ($turnos as $turno) {
                echo '<li>';
                echo 'Turno ID: ' . $turno['id'] . ' | ';
                echo 'Fecha de Ingreso: ' . $turno['fecha_ingreso'] . ' | ';
                echo 'Fecha de Entrega: ' . $turno['fecha_entrega'];
                //echo ' <a href="' . BASE_URL . 'turnos/' . $turno['id'] . '">Ver detalles</a>';
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    public function showhome(){
        require_once 'app/templates/ViewHome.phtml';
    }

    // Muestra los detalles de un turno
    public function showTurnoDetails($turno) {
        echo '<h2>Detalles del Turno</h2>';
        echo '<p>Turno ID: ' . $turno['id'] . '</p>';
        echo '<p>Fecha de Ingreso: ' . $turno['fecha_ingreso'] . '</p>';
        echo '<p>Fecha de Entrega: ' . $turno['fecha_entrega'] . '</p>';
        echo '<p>ID Cliente: ' . $turno['id_cliente'] . '</p>';
    }

    public function showTurns($turns) {
        require_once './app/templates/listTurns.phtml';
    }

    public function addTurn() {
        require_once './app/templates/listTurns.phtml';
    }

    public function showTurnForm($turn = null, $isEdit = false, $turns = []) {
        require_once './app/templates/listTurns.phtml';
    }

    public function showTurn($turno,$moto,$cliente) {
        require_once './app/templates/turn.phtml';
    }
}
?>