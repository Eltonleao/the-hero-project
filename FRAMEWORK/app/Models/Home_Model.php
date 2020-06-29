<?php
	class Home_Model extends Model
	{
		public $_tabela = 'users';

		// Pega os a quantidade que está disponiveis
		public function getDisponiveis($id)
		{
			$analistas = $this->consultaValor("SELECT (p.analistas - (SELECT count(id_analista) FROM analista WHERE deletado = 0 AND id_agencia = {$id})) as an
											   FROM plano as p
											   INNER JOIN agencia_pagamento as ap ON ap.id_plano = p.id_plano
											   WHERE ap.id_agencia = {$id}");
			$pautas = $this->consultaValor("SELECT (p.pautas - (SELECT count(id_pauta) FROM pauta WHERE deletado = 0 AND id_agencia = {$id}))
											   FROM plano as p
											   INNER JOIN agencia_pagamento as ap ON ap.id_plano = p.id_plano
											   WHERE ap.id_agencia = {$id}");
			$designs = $this->consultaValor("SELECT (p.designs - (SELECT count(id_design) FROM design WHERE deletado = 0 AND id_agencia = {$id}))
											   FROM plano as p
											   INNER JOIN agencia_pagamento as ap ON ap.id_plano = p.id_plano
											   WHERE ap.id_agencia = {$id}");
			
			$contas = $this->consultaValor("SELECT (p.contas - (SELECT count(id_conta) FROM conta WHERE deletado = 0 AND status LIKE 'ativo' AND id_agencia = {$id})) as an
											FROM plano as p
											INNER JOIN agencia_pagamento as ap ON ap.id_plano = p.id_plano
											WHERE ap.id_agencia = {$id}");
			return array('analistas'=>(int)$analistas, 'contas'=>(int)$contas, 'pautas' => (int)$pautas, 'designs' => (int)$designs);
		}

		#######
		###############
		###### FACEBOOK ##########
		###############
		#######


		// Salva o id do facebook
		public function saveface($id, $tok, $response)
		{
			$id_agencia = '';
			if(!isset($_SESSION['dados_usuario']['id_agencia']))
			{
				if(isset($_COOKIE['postingsmart_agencia']))
				{
					$aux = explode('.', $_COOKIE['postingsmart_agencia']);
					$id_agencia = $aux[1];
				}
				else if(isset($_COOKIE['postingsmart_analista']))
				{
					$aux = explode('.', $_COOKIE['postingsmart_analista']);
					$id_analista = $aux[1];
					$id_agencia = $this->consultaValor("SELECT id_agencia FROM analista WHERE id_analista = ".$id_analista);
				}
			}
			else
				$id_agencia = $_SESSION['dados_usuario']['id_agencia'];

			// echo 'ag '.$id_agencia;

			// Verifica se ja tem essa conta
			$this->_tabela = 'agencia_facebook';

			$check = $this->readLine('id_agencia = '.$id_agencia.' AND id_facebook = '.$id);
			if(isset($check['id_agencia_facebook']))
			{
				// return -1;
				$this->update(array(
							'imagem'      => $response['picture']['data']['url'],
							'nome'        => $response['name'],
							'token'       => $tok,
						), 'id_agencia_facebook = '.$check['id_agencia_facebook']);
				$id = $check['id_agencia_facebook'];
			}
			else
			{
				$id = $this->insert(array(
								'id_agencia'  => $id_agencia,
								'id_facebook' => $id,
								'imagem'      => $response['picture']['data']['url'],
								'nome'        => $response['name'],
								'token'       => $tok,
								'data'        => date("Y-m-d H:i:s"),
							));
			}


			$this->_tabela = 'agencia';

			return $id;
		}


		// Verifica se tem token
		public function getToken($id = false)
		{
			if(!$id)
				$id = $_SESSION['dados_usuario']['id_agencia'];

			// $ag = $this->readLine('id_agencia = '.$id);
			// if($ag['token']=='')
			// 	return false;

			// return $ag['token'];

			$facebooks = $this->consulta("SELECT * FROM agencia_facebook WHERE id_agencia = {$id} AND deletado = 0");
			if(count($facebooks)<=0)
				return false;

			return $facebooks;
		}

		// Retorna os dados do facebook
		public function getFacebook($id)
		{
			return $this->consultaLinha("SELECT * FROM agencia_facebook WHERE id_agencia_facebook = ".$id);
		}
		
		###############
		###### FIM FACEBOOK ##########
		###############


		#######
		###############
		###### Twitter ##########
		###############
		#######

		// Salva o id do Twitter
		public function saveTwitter($user, $accesToken)
		{
			$this->_tabela = 'agencia_twitter';
			$id            = $this->insert(array(
				'id_agencia'         => $_SESSION['dados_usuario']['id_agencia'],
				'id_twitter'         => $accesToken['user_id'],
				'imagem'             => $user->profile_image_url_https,
				'nome'               => $user->name,
				'oauth_token'        => $accesToken['oauth_token'],
				'oauth_token_secret' => $accesToken['oauth_token_secret'],
				'data'               => date("Y-m-d H:i:s"),
			));

			$this->_tabela = 'agencia';
			return $id;
		}

		// Retorna os dados do twitter
		public function getTwitter($id)
		{
			return $this->consultaLinha("SELECT * FROM agencia_twitter WHERE id_agencia_twitter = ".$id);
		}

		###############
		###### FIM Twitter ##########
		###############

		public function getMinhaConta()
		{
			$agencia = $this->consultaLinha("
				SELECT ap.*, p.nome as assinatura, p.paypal, p.gerencianet, p.api, p.valor, p.valor_usd, p.valor_eur
				FROM agencia_pagamento as ap
				INNER JOIN plano as p ON p.id_plano = ap.id_plano
				INNER JOIN agencia as emp ON ap.id_agencia = emp.id_agencia
				WHERE ap.id_agencia = {$_SESSION['dados_usuario']['id_agencia']}
			");

			return $agencia;
		}

		public function adicionaContas($id, $contas)
		{
			$this->update(array('contas'=>$contas), 'id_agencia = '.$id);
		}

		public function getNumContas()
		{
			return $this->consultaValor("SELECT count(*) FROM conta WHERE id_agencia = {$_SESSION['dados_usuario']['id_agencia']}");
		}

		public function getDadosContato()
		{
			return $this->consultaLinha("SELECT nome, responsavel, email, telefone, telefone2 FROM agencia WHERE id_agencia = {$_SESSION['dados_usuario']['id_agencia']}");
		}

		public function getDadosCancelamento()
		{
			return $this->consultaLinha("SELECT * FROM solicitacao_cancelamento WHERE id_agencia = {$_SESSION['dados_usuario']['id_agencia']} AND status = 'pendente'");
		}

		public function getAgencia($id = false)
		{
			if (!$id)
				$id = $_SESSION['dados_usuario']['id_agencia'];
			$this->_tabela = "agencia";

			$dados = $this->consultaLinha("SELECT a.*, ass.data_promocao, ass.documento, ass.forma_pagamento, ass.id_paypal, ass.id_gerencianet, ass.id_plano
										   FROM agencia as a
										   LEFT JOIN agencia_pagamento as ass ON ass.id_agencia = a.id_agencia
										   WHERE a.id_agencia = {$id}
										  ");

			if($dados['id_plano'] != '')
				$dados['plano'] = $this->consultaLinha("SELECT * FROM plano WHERE id_plano = {$dados['id_plano']}");
			else
				$dados['plano'] = array();
			
			$this->_tabela = "developer";

			return $dados;
		}

		public function getTransacoes()
		{
			$id = $_SESSION['dados_usuario']['id_agencia'];

			// Resumo
			$res = $this->consulta("SELECT t.descricao, t.status, t.valor, t.data_criacao as data, t.vencimento
									FROM (SELECT t1.* from agencia_pagamento_transacao as t1 WHERE t1.status IS NOT NULL ORDER BY t1.data_criacao DESC, t1.id_agencia_pagamento_transacao DESC) as t
									INNER JOIN agencia_pagamento as ea ON t.id_agencia_pagamento = ea.id_agencia_pagamento
									WHERE ea.id_agencia = {$id}
									GROUP BY t.id_aux
									ORDER BY t.data_criacao DESC, t.id_agencia_pagamento_transacao DESC
									LIMIT 4");

			// Historico completo
			$com = $this->consulta("SELECT t.descricao, t.status, t.valor, t.data_criacao as data
									FROM agencia_pagamento as ea
									INNER JOIN (SELECT t1.* from agencia_pagamento_transacao as t1 ORDER BY t1.data_criacao DESC, t1.id_agencia_pagamento_transacao DESC) as t ON t.id_agencia_pagamento = ea.id_agencia_pagamento
									WHERE ea.id_agencia = {$id}
									GROUP BY t.id_aux
									ORDER BY t.data_criacao DESC, t.id_agencia_pagamento_transacao DESC");

			return array("resumo" => $res, "completo" => $com);
		}

		public function getMudancaPlanoPendente()
		{
			return $this->consultaLinha("SELECT v.data, v.id_plano, p.nome
										 FROM agencia_valor as v
										 INNER JOIN plano as p ON p.id_plano = v.id_plano
										 WHERE v.id_agencia = {$_SESSION['dados_usuario']['id_agencia']}
										 AND v.deletado = 0
										 ORDER BY v.id_agencia_valor DESC
										");
		}

		public function getDadosRenovacao()
		{
			$date = new DateTime();
			$date->add(new DateInterval('P1M1D'));
			$hoje = $date->format('Y-m-d');
			return $this->consultaLinha("SELECT r.*
										 FROM agencia_pagamento_renovacao as r
										 INNER JOIN agencia_pagamento as ass ON ass.id_agencia_pagamento = r.id_agencia_pagamento
										 WHERE r.id_agencia = {$_SESSION['dados_usuario']['id_agencia']}
										 AND r.data_renovacao < '{$hoje}'
										 AND ass.id_plano = r.id_plano_antigo");
		}

		public function verificaEmailAssinatura()
		{
			$email    = $this->consultaValor("SELECT email FROM agencia WHERE id_agencia = {$_SESSION['dados_usuario']['id_agencia']}");
			$verifica = $this->consultaValor("SELECT COUNT(e.id_agencia)
											  FROM agencia as e
											  INNER JOIN agencia_pagamento as ass ON ass.id_agencia = e.id_agencia
											  WHERE e.id_agencia != {$_SESSION['dados_usuario']['id_agencia']}
											  AND e.email = '{$email}'
											  AND ass.forma_pagamento != ''");
			if($verifica > 0)
				return false;

			return true;
		}

		public function verificaEmail($dados)
		{
			$antigo = $this->consultaValor("SELECT email FROM agencia WHERE id_agencia = {$_SESSION['dados_usuario']['id_agencia']}");
			if($antigo == $dados['email'])
				return true;

			$verifica = $this->consultaValor("SELECT COUNT(id_agencia) FROM agencia WHERE id_agencia != {$_SESSION['dados_usuario']['id_agencia']} AND email LIKE '{$dados['email']}'");
			if($verifica > 0)
				return false;
			return true;
		}
		
		public function salvaAgencia($dados)
		{
			$this->_tabela = "agencia";
			$result = $this->update($dados, "id_agencia = ".$_SESSION['dados_usuario']['id_agencia']);
			$this->_tabela = "developer";
			return $result;
		}


		public function estenderPagamento()
		{
			$agencia = $this->getAgencia();

			$this->_tabela = 'agencia_extensao_pagamento';
			$idExtensao = $this->insert(array('id_agencia' => $agencia['id_agencia'], 'data'=>date("Y-m-d H:i:s")));

			if($idExtensao > 0){
				$this->_tabela = 'agencia';
				return $this->update(array('deletado' => 0, 'status'=>'ativo'), 'id_agencia = '.$agencia['id_agencia']);
			}

			return 0;
		}

		public function verificaExtensaoPagamentoMes($idAgencia)
		{
			$data = date("Y-m");
			$extensao = $this->consultaLinha("SELECT * FROM agencia_extensao_pagamento WHERE id_agencia = {$idAgencia} AND data LIKE '{$data}%'");
			if($extensao['id_agencia_extensao_pagamento'])
				return true;

			return false;
		}
		
	}