<?
	/**
	* Classe com funções uteis
	*/
	class Utils
	{
		public static function verificaPagina($paginas)
		{
			$redir = new RedirectHelper();

			foreach ($paginas as $p)
			{
				if($redir->getCurrentAction()==$p)
					return false;
			}

			return true;
		}

		public static function operacao($o){
			switch($o){
				case "sum":
					return "+";
					break;
				case "sub":
					return "-";
					break;
				case "mult":
					return "*";
				break;
				case "div":
					return "/";
				break;
				default:
					return "undefined";
				break;
			}

		}

		public static function meses()
		{
			return array( 1  => 'janeiro',
						  2  => 'fevereiro',
						  3  => 'março',
						  4  => 'abril',
						  5  => 'maio',
						  6  => 'junho',
						  7  => 'julho',
						  8  => 'agosto',
						  9  => 'setembro',
						  10 => 'outubro',
						  11 => 'novembro',
						  12 => 'dezembro');
		}

		public static function getMes($numeroDoMes)
		{
			$meses = Utils::meses();

			return $meses[(int)$numeroDoMes];
		}

		public static function diasDaSemana()
		{
			return array( 0  => 'DOM',
						  1  => 'SEG',
						  2  => 'TER',
						  3  => 'QUAR',
						  4  => 'QUIN',
						  5  => 'SEX',
						  6  => 'SÁB');
		}

		public static function getDiaDaSemana($numeroDiaDaSemana)
		{
			$diasDaSemana = Utils::diasDaSemana();
			return $diasDaSemana[(int)$numeroDiaDaSemana];
		}

		public static function retornaDia($dt)
		{
			$html = date("d", strtotime($dt));
			$html .= ' de ';

			$meses = Utils::meses();
			$html .= $meses[date('n', strtotime($dt))];

			return $html;
		}

		/* Retorna data do post formatada */
		public static function retornaData($dt)
		{
			$html = Utils::retornaDia($dt);
			$html .= ' às ';
			$html .= date("H:i", strtotime($dt));
			return $html;
		}

		public static function pushPost($id, $statusPost, $users = array('agencia', 'analista'))
		{
			// Envia push
			$bd = new Post_Model();
			$pushDb = new Push_Model();

			$dados = $pushDb->getUsersPost($users, $id);
			$conta = $bd->getNomeConta($id);

			$pushLib = new Push();

			foreach ($dados as $tipo => $tks)
			{
				foreach ($tks as $t)
				{
					$emailLib = new SendEmail();
					
					$tokens = explode(',', $t['tokens']);

					$link = 'https://www.postingsmart.com/'.$tipo;
					
					$emailLib->postRevisao($statusPost, $t['email'], $conta, $id);

					// foreach ($tokens as $tCod)
					// {
					// 	if($statusPost == 'aprovado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Post aprovado',
					// 								  'O cliente aprovou a postagem para '.$conta,
					// 								  array('id_post'=>$id, 'icone'=>'check_circle', 'icone_color'=>'#58b57b'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($statusPost == 'reprovado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Post reprovado',
					// 								  'O cliente reprovou a postagem para '.$conta,
					// 								  array('id_post'=>$id, 'icone'=>'error', 'icone_color'=>'#f75a5a'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($statusPost == 'pendente')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Novo post para revisão',
					// 								  'A conta '.$conta.' tem uma nova postagem para revisão',
					// 								  array('id_post'=>$id, 'icone'=>'hourglass_full', 'icone_color'=>'#ffde57'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($statusPost == 'alterado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Novo post para revisão',
					// 								  'A postagem reprovada da conta '.$conta.' foi modificada pela agência e está aguardando nova revisão',
					// 								  array('id_post'=>$id, 'icone'=>'hourglass_full', 'icone_color'=>'#ffde57'),
					// 								  $link
					// 								);
					// 	}
					// }

				}
				
			}
		}

		public static function pushPautas($id, $status, $users = array('agencia'), $idConta = false)
		{
			
			$pushDb = new Push_Model();

			if(!$idConta){
				$bd = new Pautas_Model();
				$dados = $pushDb->getUsersPauta($users, $id);
				$conta = $bd->getNomeConta($id);
			}
			else{
				$bd = new Model();
				$dados = $pushDb->getUsersConta($users, $idConta);
				$conta = $bd->consultaValor("SELECT nome FROM conta WHERE id_conta = {$idConta}");
			}
			

			$pushLib = new Push();

			foreach ($dados as $tipo => $tks)
			{
				foreach ($tks as $t)
				{
					$emailLib = new SendEmail();
					
					$tokens = explode(',', $t['tokens']);

					$link = 'https://www.postingsmart.com/'.$tipo;
					
					$envioEmail = $emailLib->pautaRevisao($status, $t['email'], $conta, $id, $tipo);

					// foreach ($tokens as $tCod)
					// {
					// 	if($status == 'aprovado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Pauta aprovada',
					// 								  'O cliente aprovou a pauta para '.$conta,
					// 								  array('id_conta'=>$id, 'icone'=>'check_circle', 'icone_color'=>'#58b57b'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($status == 'reprovado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Pauta reprovada',
					// 								  'O cliente reprovou a pauta para '.$conta,
					// 								  array('id_conta'=>$id, 'icone'=>'error', 'icone_color'=>'#f75a5a'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($status == 'pendente')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Nova pauta para revisão',
					// 								  'A conta '.$conta.' tem novas pautas para revisão',
					// 								  array('id_conta'=>$id, 'icone'=>'hourglass_full', 'icone_color'=>'#ffde57'),
					// 								  $link
					// 								);
					// 	}
					// 	else if($status == 'alterado')
					// 	{
					// 		$r = $pushLib -> sendUser($tCod,
					// 								  'Nova pauta para revisão',
					// 								  'A pauta reprovada da conta '.$conta.' foi modificada pela agência e está aguardando nova revisão',
					// 								  array('id_conta'=>$id, 'icone'=>'hourglass_full', 'icone_color'=>'#ffde57'),
					// 								  $link
					// 								);
					// 	}
					// }

					return $envioEmail;

				}
				
			}
		}

		public static function getRedes($id = null)
		{
			$bd = new Model();
			$redes = $bd->consulta("SELECT * FROM rede");
			if(is_null($id))
			{
				return $redes;
			}

			// Pega as redes que estão marcadas para o post
			$retorno = array();
			foreach ($redes as $r)
			{
				$check = $bd->consultaLinha("SELECT *
										     FROM post_rede 
										     WHERE id_rede = {$r['id_rede']}
										     AND id_post = {$id}
										     AND deletado = 0
										  ");
				if(isset($check['id_post_rede']))
					$r['post'] = true;
				else
					$r['post'] = false;

				$retorno[] = $r;
			}

			return $retorno;
		}

		public static function getUmaRede($nome)
		{
			$bd = new Model();
			return $bd->consultaLinha("SELECT * FROM rede WHERE nome = '{$nome}'");
		}

		public static function getNomeRede($redes, $rd, $padrao = '')
		{
			$usuario = $padrao;
			foreach ($redes as $conta_r)
			{
				if($conta_r['rede']==$rd)
					$usuario = $conta_r['nome'];
			}

			return $usuario;
		}

		// Tipos de publicação
		public static function getTipoPublicacao()
		{
			$bd = new Model();
			return $bd->consulta("SELECT * FROM publicacao_tipo WHERE deletado = 0");
		}

		// Tipos de publicação
		public static function getTipoPublicacaoBlog()
		{
			$bd = new Model();
			return $bd->consulta("SELECT * FROM blog_publicacao_tipo WHERE deletado = 0");
		}

		// Tipos de publicação
		public static function getTipoArte()
		{
			$bd = new Model();
			return $bd->consulta("SELECT * FROM arte_tipo WHERE deletado = 0");
		}

		// Tipos de publicação
		public static function getTipoArteBlog()
		{
			$bd = new Model();
			return $bd->consulta("SELECT * FROM blog_arte_tipo WHERE deletado = 0");
		}


		public static function checkPautaDay($dataObj, $diasSemana, $diasPautas, $idConta)
		{
			foreach ($diasSemana as $d){
				if($d == $dataObj->format('w')){
					$retorno = true;
					foreach ($diasPautas as $dp){
						if($dp['data'] == $dataObj->format('Y-m-d') && $idConta == $dp['id_conta']){
							$retorno = false;
						}	
					}
				}
			}

			return $retorno;
		}


		public static function checkPautaDayBlog($dataObj, $diasSemana, $diasPautas, $idBlog)
		{
			$retorno = false;
			
			foreach ($diasSemana as $d){
				if($d == $dataObj->format('w')){
					$retorno = true;
					foreach ($diasPautas as $dp){
						if($dp['data'] == $dataObj->format('Y-m-d') && $idBlog == $dp['id_blog']){
							$retorno = false;
						}	
					}
				}
			}

			return $retorno;
		}

		// Verifca se é dia de postagem e se nao tem postagem
		// public static function checkPostDay($dataObj, $diasSemana, $diasPostagens, $diasPautas)
		// {
		// 	foreach ($diasSemana as $d){
		// 		if($d == $dataObj->format('w')){
		// 			$retorno = true;
		// 			foreach ($diasPostagens as $dp){
		// 				if($dp == $dataObj->format('Y-m-d')){
		// 					return false;
		// 				}	
		// 			}

		// 			foreach ($diasPautas as $diaPauta) {
		// 				if($diaPauta == $dataObj->format('Y-m-d')){
		// 					return false;
		// 				}	
		// 			}

		// 		}
		// 	}

		// 	return $retorno;
		// }

		public static function checkPostDayConta($dataObj, $diasSemana, $diasPostagens, $diasPautas, $idConta)
		{
			foreach ($diasSemana as $d){
				if($d == $dataObj->format('w')){
					$retorno = true;
					foreach ($diasPostagens as $dp){
						if($dp['data'] == $dataObj->format('Y-m-d') && $idConta == $dp['id_conta']){
							return false;
						}	
					}

					foreach ($diasPautas as $diaPauta) {
						if($diaPauta['data'] == $dataObj->format('Y-m-d') && $idConta == $diaPauta['id_conta']){
							return false;
						}	
					}

				}
			}

			return $retorno;
		}

		public static function checkPostDayBlog($dataObj, $diasSemana, $diasPostagens, $diasPautas, $idBlog)
		{
			foreach ($diasSemana as $d){
				if($d == $dataObj->format('w')){
					$retorno = true;
					foreach ($diasPostagens as $dp){
						if($dp['data'] == $dataObj->format('Y-m-d') && $idBlog == $dp['id_blog']){
							return false;
						}	
					}

					foreach ($diasPautas as $diaPauta) {
						if($diaPauta['data'] == $dataObj->format('Y-m-d') && $idBlog == $diaPauta['id_blog']){
							return false;
						}	
					}

				}
			}

			return $retorno;
		}

		// Coloca tooltip nos botoes e links
		public static function setTooltip($msg, $pos)
		{
			return 'data-toggle="tooltip" data-placement="'.$pos.'" title="'.$msg.'"';
		}

		public static function getDiasGratuitos(){
			$bd = new Model();
			return $bd->consultaValor("SELECT valor FROM config WHERE configuracao = 'dias_gratuitos'");
		}

		public static function getTextoCalendarioPauta($p, $tipo_login = ''){
			if ($p['status_agencia'] == 'pendente'){
				if ($tipo_login == 'analista')
					return ' Pauta em aprovação <br><b>Analista</b> ';
				else
					return ' Pauta em aprovação <br><b>Agência</b> ';
			}
			elseif ($p['status_agencia'] == 'reprovado')
				return ' Pauta em modificação ';
			elseif ($p['status_agencia'] == 'aprovado'){
				if ($p['status_cliente'] == 'pendente' || $p['status_cliente'] == 'rascunho')
					return ' Pauta em aprovação <br><b>Cliente</b> ';
				elseif ($p['status_cliente'] == 'reprovado')
					return ' Pauta em modificação ';
				elseif($p['status_cliente'] == 'aprovado')
					return ' Pauta Aprovada ';
			}

			return '';
		}

		public static function getTextoCalendarioPost($p, $tipo_login = ''){
			if($tipo_login == 'cliente')
				if ($p['status'] == 'arte_pendente' || $p['status'] == 'aguardando_arte' || $p['status'] == 'arte_reprovado' || $p['status'] == 'arte_rascunho' || $p['status'] == 'arte_pendente_cliente' || $p['status'] == 'arte_reprovado_cliente')
					return ' Aguardando Arte ';
			
			if ($p['status'] == 'arte_pendente')
				return ' Arte em aprovação <br><b>Agência</b> ';
			if ($p['status'] == 'arte_pendente_cliente')
				return ' Arte em aprovação <br><b>Cliente</b> ';
			if ($p['status'] == 'arte_reprovado' || $p['status'] == 'arte_reprovado_cliente')
					return ' Arte em modificação ';
			if ($p['status'] == 'pendente')
				return ' Post Pendente ';
			if ($p['status'] == 'reprovado')
				return ' Post Reprovado ';
			if($p['status'] == 'aprovado')
				return ' Post Agendado ';
			if($p['status'] == 'postado')
				return ' Post Efetuado ';
			if($p['status'] == 'nao_postado')
				return ' Post Não Efetuado ';
			if($p['status'] == 'aguardando_arte')
				return ' Aguardando Arte ';
			
			if($p['status'] == 'arte_rascunho')
				return ' Arte em rascunho ';

			return'Post '.$p['status'];
		}

		public static function getCoresCalendario(){
			return array(
				//pauta
					'pauta_reprovado'         => array('background' => '#0096cb','color' => '#fff'),
					'pauta_rascunho'          => array('background' => '#8badd2','color' => '#fff'),
					'pauta_pendente'          => array('background' => '#70b3e3','color' => '#fff'),
					'pauta_cliente_pendente'  => array('background' => '#6395b2','color' => '#fff'),
					'pauta_cliente_aprovada'  => array('background' => '#75c9d3','color' => '#fff'),
					'sem_pauta'               => array('background' => '#8dcee2','color' => '#fff'),
					'pauta_cliente_reprovado' => array('background' => '#a2d9de','color' => '#fff'),
					// ''                 => array('background' => '#c1dce1','color' => '#fff'),
				//arte
					'post_aguardando_arte' => array('background' => '#f1ca8e','color' => '#fff'),
					'post_arte_pendente'   => array('background' => '#f2b990','color' => '#fff'),
					'post_arte_reprovado'  => array('background' => '#f1be9f','color' => '#fff'),
					'post_arte_rascunho'   => array('background' => '#e9ceb3','color' => '#fff'),
					'post_arte_pendente_cliente' => array('background' => '#9cdab3','color' => '#fff'),
				//post
					'post_rascunho'     => array('background' => '#f2efa5','color' => '#fff'),
					'post_pendente'     => array('background' => '#c5df97','color' => '#fff'),
					'post_reprovado'    => array('background' => '#f8d7da','color' => '#fff'),
					'post_aprovado'     => array('background' => '#9edeaa','color' => '#fff'),
					'post_efetuado'     => array('background' => '#81d4af','color' => '#fff'),
					'post_nao_efetuado' => array('background' => '#c8d4a6','color' => '#fff'),
					// 'post_'             => array('background' => '#d3ddbc','color' => '#fff'),
					// 'post_'             => array('background' => '#c0dbd2','color' => '#fff'),
				'padrao'            => array('background' => '#37464f','color' => '#fff'),
				// 'agencia'           => '#5291b7',
				// 'naoagendado'       => '#c3c2c2',
				// 'aguardando_arte'   => '#856404',
				// 'pendente'          => '#f19845',
				// 'cliente_pendente'  => '#2196F3',
				// 'reprovado'         => '#f33434',
				// 'cliente_reprovado' => '#f33434',
				// 'arte_reprovado'    => '#f33434',
				// 'aprovado'          => '#50b174',
				// 'cliente_aprovado'  => '#50b174',
			);
		}
		public static function getCorCalendario($key){
			$cores = Utils::getCoresCalendario();
			return $cores[$key]['background'];
		}

		public static function agendarEnvioEmail($dados){
			$bd = new Model();
			$bd->_tabela = 'enviar_email';
			$check = $bd->readLine("tipo = '{$dados['tipo']}' AND usuario = '{$dados['usuario']}' AND id_usuario = {$dados['id_usuario']} AND enviado = 0");
			if (isset($check['id_enviar_email'])) {
				$bd->update(array('data_enviar' => $dados['data_enviar']), 'id_enviar_email');
			}
			else{
				$bd->insert($dados);
			}
		}

		public static function salvarHistoricoStatus($dados){
			$bd = new Model();
			$bd->_tabela = 'post_historico';
			$bd->insert($dados);
		}
		
		public static function getArte($idPost){
			$bd = new Model();
			$midia = $bd->consultaValor("SELECT info FROM post_info WHERE id_post = {$idPost} AND id_tipo = 2 ORDER BY id_post_info DESC");
			
			if(@json_decode($midia))
				return json_decode($midia);

			return $midia;
		}
		
		public static function getArteBlog($idBlogPost){
			$bd = new Model();
			$midia = $bd->consultaValor("SELECT info FROM blog_post_info WHERE id_blog_post = {$idBlogPost} AND id_tipo = 2 ORDER BY id_blog_post_info DESC");
			
			if(@json_decode($midia))
				return json_decode($midia);

			return $midia;
		}

		public static function getArteHtml($dados){
			$bd        = new Model();
			if(isset($dados['id_blog_post'])){
				$midia     = Utils::getArteBlog($dados['id_blog_post']);
				$tipoMidia = $bd->consultaValor("SELECT slug 
													FROM blog_arte_tipo 
													INNER JOIN blog_post ON blog_post.id_blog_arte_tipo = blog_arte_tipo.id_blog_arte_tipo 
													WHERE blog_post.id_blog_post = {$dados['id_blog_post']}
				");
			}
			else{
				$midia     = Utils::getArte($dados['id_post']);
				$tipoMidia = $bd->consultaValor("SELECT slug 
													FROM arte_tipo 
													INNER JOIN post ON post.id_arte_tipo = arte_tipo.id_arte_tipo 
													WHERE post.id_post = {$dados['id_post']}
				");
			}

			if($midia == "")
				return "<p>Aguardando Arte</p>";
			switch ($tipoMidia) {
				case 'album':
					$html = '<div class="owl-carousel album-carousel owl-theme">';
					foreach($midia as $key => $imagem){
						$html .= '
						<div>
							<p class="hidden-com-carousel mt-2 mb-0"><b>Álbum - Foto '.($key+1).'</b> </p>
							<a href="'.URL.'files/'.$imagem.'" target="blank"><img src="'.URL.'files/'.$imagem.'"></a>
						</div>';
					}
					$html .= '</div>';
					return $html;
				case 'post':
					return '<a href="'.URL.'files/'.$midia.'" target="blank"><img src="'.URL.'files/'.$midia.'" class="img-post"></a>';
			}
		}

		public static function getSimboloMoeda($moeda)
		{
			switch ($moeda) {
				case 'BRL':
					return 'R$';
				case 'EUR':
					return '€';
				case 'USD':
					return '$';
				default:
					return '';
			}
		}

		public static function getValorPlano($valores, $moeda = 'BRL', $format = false)
		{
			switch ($moeda) {
				case 'EUR':
					$valor = $valores['valor_eur'];
					break;
				case 'USD':
					$valor = $valores['valor_usd'];
					break;
				default:
					$valor = $valores['valor'];
					break;
			}

			return $format ? number_format($valor, 2, ',', '') : $valor;
		}



		public static function nomePlano($plano, $moeda = false)
		{
			if (!$moeda)
				$moeda = MOEDA;

			switch ($moeda) {
				default:
					return $plano;
			}
		}

		public static function statusLinguas($st)
		{
			if(LANG == 'pt')
				return $st;

			$status = array(
				'pendente'     => STATUS_PAGAMENTO_PENDENTE,
				'pago'         => STATUS_PAGAMENTO_PAGO,
				'inadimplente' => STATUS_PAGAMENTO_INADIMPLENTE,
				'cancelado'    => STATUS_PAGAMENTO_CANCELADO
			);
			return $status[$st];
		}

		public static function descricaoLinguas($desc)
		{
			return $desc;
		}

		public static function formatDtPaypal($dt, $time = false)
		{
			if(!$time)
				$dt = strtotime($dt);
			$meses = array(1 => 'JAN', 2 => 'FEV', 3 => 'MAR', 4 => 'ABR', 5 => 'MAI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGO', 9 => 'SET', 10 => 'OUT', 11 => 'NOV', 12 => 'DEZ');

			return date('d', $dt).' '.$meses[date("n", $dt)].' '.date("Y", $dt);
		}
	}
?>