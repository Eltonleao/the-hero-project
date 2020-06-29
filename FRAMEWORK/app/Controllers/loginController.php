<?php


class login extends Controller
{

	public function init($params = null)
	{
		ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);

		$this->redir = new RedirectHelper();

		$this->_auth 						= new AuthHelper();
		$this->_auth->_tableName 		= "users";
		$this->_auth->_userColumn 		= 'user_email';
		$this->_auth->_senhaColumn 		= 'user_password';
		$this->_auth->_controllerLogado = 'home';
		$this->_auth->_controllerErro 	= 'home';
		$this->_auth->_actionErro 		= 'login';

		// if (Utils::verificaPagina(array('login', 'loginRedes', 'cadastro', 'esqueciSenha', 'novaSenha', 'editaemail'))) {
		// 	if (!$this->_auth->verificaLogin())
		// 		$this->redir->goToAction('login');
		// }

	}

	public function index_action($params = null)
	{
		$this->view('login');
	}


}
