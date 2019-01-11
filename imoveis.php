<? 
	require_once('Connections/conn.php');

	if (!function_exists("GetSQLValueString")) {
	  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
	  {
	    if (PHP_VERSION < 6) {
	      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
	    }

	    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	    switch ($theType) {
	      case "text":
	      $theValue = ($theValue != "") ? "'" . trim($theValue) . "'" : "NULL";
	      break;    
	      case "long":
	      case "int":
	      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
	      break;
	      case "double":
	      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
	      break;
	      case "date":
	      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	      break;
	      case "defined":
	      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
	      break;
	    }
	    return $theValue;
	  }
	}
	$categoria = '';
	if(isset($_GET['categoria'])) {
		$categoria = GetSQLValueString($_GET['categoria'], 'int');
		$sql = " AND categ_id = " . $categoria;
	}
	$caracteristica = '';
	if(isset($_GET['caracteristica'])) {
		$caracteristica = GetSQLValueString($_GET['caracteristica'], 'int');
		$sql .= " AND ic_id_carac = " . $caracteristica;
	}

	// Consulta de Imóveis
	$query_imoveis = "SELECT * FROM imovel
									LEFT OUTER JOIN categoria ON categ_id = imov_id_categ
									LEFT OUTER JOIN imovel_caracteristica ON ic_id_imov = imov_id
							WHERE 1=1 $sql
							GROUP BY imov_titulo ASC";
	$imoveis_list = mysql_query($query_imoveis, $conn) or die(mysql_error());
	$total_imoveis = mysql_num_rows($imoveis_list);
?>
<!DOCTYPE html>
<html>
<head>
	<? 
		$headTitle = ' - Imóveis';
		require_once('head.php');
	?>
</head>
<body>
	<? require_once('header.php');?>
	<div class="container">
		<? if($total_imoveis > 0) { ?>
			<div id="imoveis-list">
			        <? 
			          while($row_imovel = mysql_fetch_assoc($imoveis_list)) {
			        ?>
			        <div class="col-xs-12 col-sm-6 destaque-item">
			          <div class="col-xs-12 col-sm-6">
			            <div class="destaque-img">
			              <a href="Javascript:void(0);" data-toggle="modal" data-target="#modal-destaque" onclick="getDestaque(<?=$row_imovel['imov_id'] . ', ' . $row_imovel['imov_id_galeria'];?>)">
			                <img src="imagens/imagens_imovel/<?=$row_imovel['imov_img'];?>" alt="<?=$row_imovel['imov_titulo'];?>">
			              </a>
			            </div>
			            <div class="destaque-galeria">
			              <?
			                // Itens da Galeria
			                $query_galeriaDestaque = sprintf("SELECT * FROM foto WHERE foto_id_galeria = %s LIMIT 3", GetSQLValueString($row_imovel['imov_id_galeria'], 'int'));
			                $rs_galeriaDestaque = mysql_query($query_galeriaDestaque, $conn) or die(mysql_error());
			                $total_galeria = mysql_num_rows($rs_galeriaDestaque);

			                // Pegar número de registros da galeria
			                $query_numGaleria = sprintf("SELECT count(*) AS Contador FROM foto WHERE foto_id_galeria = %s", GetSQLValueString($row_imovel['imov_id_galeria'], 'int'));
			                $rs_numGaleria = mysql_query($query_numGaleria, $conn) or die(mysql_error());
			                $num_galeria = mysql_fetch_assoc($rs_numGaleria);
			                $galeriaGrande = false;
			                if($num_galeria['Contador'] > 3) {
			                  $galeriaGrande = true;
			                  $contador_galeria = $num_galeria['Contador'] - 2;
			                }

			                $cont = 0;
			                while($row_rsGaleriaDestaque = mysql_fetch_assoc($rs_galeriaDestaque)) {
			                  $cont++;
			                  if($cont < 3) {
			              ?>
			                  <div>
			                      <a href="Javascript:void(0);" data-toggle="modal" data-target="#modal-destaque" onclick="getDestaque(<?=$row_imovel['imov_id'] . ', ' . $row_imovel['imov_id_galeria'];?>)">
			                        <img src="admin/upload_photo/imagens/thumbnail@2x/<?=$row_rsGaleriaDestaque['foto_img']; ?>"
			                        <? if(isset($row_rsGaleriaDestaque['foto_legenda'])) { ?>
			                          alt="<?=$row_rsGaleriaDestaque['foto_legenda']; ?>"
			                        <? } else {echo '';} ?>
			                      >
			                      </a>
			                  </div>
			                  <? } else { ?>
			                    <div>
			                      <? if ($galeriaGrande == true) { ?>
			                        <a href="Javascript:void(0);" data-toggle="modal" data-target="#modal-destaque" onclick="getDestaque(<?=$row_imovel['imov_id'] . ', ' . $row_imovel['imov_id_galeria'];?>)">
			                      <? } ?>
			                        <img src="admin/upload_photo/imagens/thumbnail@2x/<?=$row_rsGaleriaDestaque['foto_img']; ?>"
			                          <? if(isset($row_rsGaleriaDestaque['foto_legenda'])) { ?>
			                            alt="<?=$row_rsGaleriaDestaque['foto_legenda']; ?>"
			                          <? } else {echo '';} ?>
			                        >
			                      <? if($galeriaGrande == true) { ?>
			                        <div class="galeria-vejaMais">
			                          <span>+ <?=$contador_galeria;?></span>
			                        </div>
			                        </a>
			                      <? } ?>
			                    </div>
			                  <? 
			                      }
			                    } 
			                  ?>
			            </div>
			          </div>
			          <div class="col-xs-12 col-sm-6">
			            <div class="destaque-titulo">
			              <a href="Javascript:void(0);" data-toggle="modal" data-target="#modal-destaque" onclick="getDestaque(<?=$row_imovel['imov_id'] . ', ' . $row_imovel['imov_id_galeria'];?>)">
			                <h1>
			                  <? 
			                    $textoMenor = wordwrap($row_imovel['imov_titulo'], 60, "<!!>");
			                    $textoMenor = reset(explode("<!!>", $textoMenor));
			                    echo $textoMenor;
			                    if(strlen($row_imovel['imov_texto']) > strlen($textoMenor)) 
			                      echo '';
			                  ?>
			                </h1>
			              </a>
			            </div>
			            <p>
			              <? 
			                $textoMenor = wordwrap($row_imovel['imov_texto'], 320, "<!!>");
			                $textoMenor = reset(explode("<!!>", $textoMenor));
			                echo nl2br($textoMenor);
			                if(strlen($row_imovel['imov_texto']) > strlen($textoMenor))
			                  echo '...';
			              ?>
			            </p>
			            <div class="destaque-link">
			              <a href="Javascript:void(0);" data-toggle="modal" data-target="#modal-destaque" onclick="getDestaque(<?=$row_imovel['imov_id'] . ', ' . $row_imovel['imov_id_galeria'];?>)">
			                + veja mais
			              </a>
			              <hr class="visible-xs">
			            </div>
			          </div>
			        </div>
			    <? } ?>
			</div>	
		<? } else { ?>
			<div class="nao-encontrado">
				<h4 class="corCinzaClaro fw600 text-center">Nenhum imóvel foi encontrado.</h4>
				<a href="index.php">
					<span class="glyphicon glyphicon-arrow-left corCinzaClaro t22"></span>
					<span class="corCinzaClaro t18">Retornar a pagina principal</span>
				</a>
			</div>
		<? } ?>
	</div>
	<div class="clearfix"></div>
	<? require_once('footer.php');?>
	<!-- Modal Destaque -->
	<div id="modal-destaque" class="modal fade" tabindex="-1" role="dialog">
	  <div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></span></button>
	      </div>
	      <div class="modal-body">
	        <div class="container-fluid" style="width: 100%;">
	          <div class="col-xs-12 col-md-6">
	              <div>
	                <? if(isset($row_rsConfig['config_whatsapp'])) { ?>
	                  <a class="btn btn-lg btn-whatsapp" href="https://api.whatsapp.com/send?phone=55<? echo preg_replace("/[^0-9]/", "", $row_rsConfig['config_whatsapp']); ?>" target="_BLANK">
	                    <i class="fab fa-whatsapp"></i>
	                    <span>CONVERSAR SOBRE ESTE IMÓVEL</span>
	                  </a>
	                <? } if(isset($row_rsConfig['config_facebook'])) { ?>
	                  <a class="btn btn-lg btn-facebook" href="https://www.facebook.com/sharer/sharer.php?u=http%3A%2F%2F<?=$row_rsConfig['config_facebook'];?>" target="_BLANK">
	                    <i class="fab fa-facebook-f"></i>
	                    <span>COMPARTILHAR</span>
	                  </a>
	                <? } ?>
	              </div>
	            <div class="clearfix"></div>
	            <div class="files-galery" style="padding-top: 20px;">
	              <div class="item-galeria">
	                <a href="" class="imovel-imagem">
	                  <img class="imovel-imagem" src=""  alt="">
	                  <div class="ampliar hidden-xs hidden-sm">
	                    <img src="images/ampliar.png">
	                  </div>
	                </a>
	              </div>

	            </div>
	          </div>
	          <div class="col-xs-12 col-md-6">
	            <div class="modal-titulo">
	              <h1 id="imovel-titulo"></h1>
	            </div>
	            <p id="imovel-texto">
	              
	            </p>
	          </div>
	        </div>
	        <div class="clearfix"></div>
	      </div>
	    </div><!-- /.modal-content -->
	  </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
	<script>
	  function getDestaque(destaque, galeria){ 
	    $('.item-galeria:not(:first-of-type)').remove();
	    $.ajax({
	      url: 'destaque.php',
	      type: 'POST',
	      dataType: 'json',
	      data: {id: destaque, idGaleria: galeria}
	    })
	    .success(function(retorno){
	      $('#imovel-titulo').html(retorno.imov_titulo);
	      $('#imovel-texto').html(retorno.imov_texto);
	      $('.imovel-imagem').attr('src', retorno.imov_img);
	      $('.imovel-imagem').attr('href', retorno.imov_img);
	      $('.imovel-imagem').attr('alt', retorno.imov_titulo);
	      $('.imovel-imagem').attr('title', retorno.imov_titulo);
	      $.each(retorno.fotos, function( index, foto ) {
	        var html = '<div class="item-galeria"><a href="admin/upload_photo/imagens/hphotos/'+foto.file+'" title="'+foto.nome+'"><img src="admin/upload_photo/imagens/thumbnail@2x/'+foto.file+'"  alt="'+foto.nome+'"><div class="ampliar hidden-xs hidden-sm"><img src="images/ampliar.png"></div></a></div>';
	        $('.files-galery').append(html);
	      });
	    });
	  }

	  	$(document).ready(function() {
	  		<? 
	  			if(isset($_GET['imovel']) && isset($_GET['galeria'])) {
	  				$imovel = $_GET['imovel'];
	  				$galeria = $_GET['galeria'];
	  				echo "getDestaque($imovel, $galeria);"; 
	  			} 
	  		?>
	  	});

	</script>
</body>
</html>