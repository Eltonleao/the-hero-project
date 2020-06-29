<?php


class user extends Controller
{

	public function init($params = null)
	{

		$this->redir = new RedirectHelper();

		$this->_auth 						= new AuthHelper();
		$this->_auth->_tableName 		= "users";
		$this->_auth->_userColumn 		= 'user_email';
		$this->_auth->_senhaColumn 		= 'user_password';
		$this->_auth->_controllerLogado = 'home';
		$this->_auth->_controllerErro 	= 'home';
		$this->_auth->_actionErro 		= 'login';

		if (Utils::verificaPagina(array('login', 'loginRedes', 'cadastro', 'esqueciSenha', 'novaSenha', 'editaemail'))) {
			if (!$this->_auth->verificaLogin())
				$this->redir->goToAction('login');
		}


		$this->bd = new User_Model();
	}

	public function index_action($params = null)
	{

		$this->view('index');
	}


	private function getBd($nome)
	{
		$nome = ucfirst($nome) . '_Model';
		return new $nome();
	}



	public function login($params = null)
	{
		if ($_POST) {
			$this->_auth->_user  	 = $_POST['user_email'];
			$this->_auth->_senha 	 = $_POST['user_password'];

			if ($this->_auth->login()) {
				if ($_SESSION['dados_usuario']['deletado'] == 1) {
					echo '0';
					unset($_SESSION);
				} else {
					echo '1';
				}
			} else
				echo '0';
			exit();
		}
		$this->view('login');
	}

	/* Sair */
	public function logout($params = null)
	{

		$this->_auth->logout();
		header("location: " . $_SERVER['PHP_SELF'] . "/home");
	}

	public function cadastro($params = null)
	{
		$this->bd->_tabela = "users";

		
		// $check = $this->bd->consulta("SELECT * FROM users");
		
		
		if ($_POST) {

			$user_email =  $_POST['user_email'];
			$_POST['data_primeiro_acesso'] = date('Y-m-d H:i:s');

			$checkEmail = $this->bd->readLine("users.user_email = '$user_email'","","","","");

			if ($checkEmail == 0) {
				$this->_tabela = "users";
				$insert = $this->bd->insert($_POST);

				if ($insert > 0) {
					echo "1";
				} else {
					echo "0";
				}
			} else {
				$check = $this->bd->update(array('user_email' => $_POST['user_email'], 'user_password' => $_POST['user_password'], 'data_primeiro_acesso' => date('Y-m-d H:i:s')), true);

				if($check > 0){
					echo '1';
				} else{
					echo "0";
				}
			}
			exit();
		}


		// $this->agencia = $this->bd->readLine("id_agencia = ".$params[0]);
		// if ((!is_null($this->agencia['data_primeiro_acesso']))||(!isset($this->agencia['id_agencia'])))
		// 	$this->redir->goToAction('login');

		$this->view('cadastro');
	}


	public function licao($params){
		$dados['licao'] = $this->bd->getLicao($params[0]);
	}
}
