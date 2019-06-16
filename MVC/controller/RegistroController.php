<?php

require_once('model/RegistroModel.php');
require_once('view/NavegacionView.php');

class RegistroController extends Controller{

  function __construct()
    {
      $this->view = new NavegacionView();
      $this->model = new RegistroModel();
    }


	public function VerificarLogin()
	  {
		if(null !== ($_POST['mail']) && null !== ($_POST['password'])){
		  $mail = $_POST['mail'];
		  $password = $_POST['password'];
		  $dbUsuario = $this->model->getUsuario($mail);
		  if(!empty($dbUsuario)){
				if(password_verify($password, $dbUsuario['password'])){
				    session_start();
					$_SESSION['user'] = $dbUsuario['mail'];
					$_SESSION['admin'] = $dbUsuario['admin'];
					$_SESSION['idUsuario'] = $dbUsuario['id_usuario'];
					if($dbUsuario['admin'] == 1){
						$this->view->HomeAdmin();
					}else{
						$this->view->Home();
					}
				}else{
					$this->view->errorFormLogin("Contraseña Incorrecta.");
				}
		  }else{
			$this->view->errorFormLogin("Usuario Incorrecto.");
		  }
		}else{
		  $this->view->errorFormLogin("Debe Ingresarse Primero.");
		}
	  }

  //Guardar la informacion de usuario y contraseña
  public function VerificarRegistro(){
    try
      {
        $this->excepcionesIssetRegistro();
          try
            { //Verificacion de longitud de contraseña y que un mismo usuario no se registre dos veces
              if (strlen($_POST['password1'])<6)
                throw new Exception("La contraseña debe tener mas de 6 caracteres");
              if (strlen($_POST['password2'])<6)
                throw new Exception("La contraseña debe tener mas de 6 caracteres");
              if (($_POST['password1']) !== ($_POST['password2']))
                throw new Exception("Las contraseñas no coinciden");  
              $mail = $this->model->getUsuario($_POST['mail']);
              if ($mail)
                  throw new Exception("Usuario ya registrado");
              $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
              $admin=false;
              $this->model->store($_POST['mail'],$_POST['nombre'],$_POST['apellido'],$password,$admin);
              header('Location: '.HOME);
            }
            catch (Exception $e)
              {
                $this->view->errorFormRegistro($e->getMessage());
              }
    }
      catch (Exception $e)
        {
          $this->view->errorFormRegistro($e->getMessage());
        }
  }


//Caso en los que no se ingresan datos de usuario o contraseña en el formulario
  private function excepcionesIssetRegistro()
    {
      if (!isset($_POST['mail']))
        throw new Exception("No se recibio el mail de usuario");
      if (!isset($_POST['password']))
        throw new Exception("No se recibio la contraseña");
    }


}
?>
