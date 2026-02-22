<?php
  require_once("../includes/padrao.inc.php");
  //session_start();
  if( !isset($_SESSION["usuariosaw"]) ){ @header("Location: ../index.php"); }

  //print_r($_SESSION);
  include_once("../includes/conexao.php");
  

 
?>

<?php
      
function query($query){	
    global $conexao;
     $consulta = mysqli_query($conexao, $query);
     return mysqli_fetch_assoc($consulta);
     mysqli_free_result($consulta);
     mysqli_close($conexao);
   }

function qtdLinhas($query){
    global $conexao;
    $query = mysqli_query($conexao, $query);
    return !$query || mysqli_num_rows($query);
    mysqli_close($link);
}
function RetornaTempo($date){
  
$mydate= date("Y-m-d H:i:s");
$theDiff="";
//echo $mydate;//2014-06-06 21:35:55
$datetime1 = date_create($date);
$datetime2 = date_create($mydate);
$interval = date_diff($datetime1, $datetime2);
//echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
$min=$interval->format('%i');
$sec=$interval->format('%s');
$hour=$interval->format('%h');
$mon=$interval->format('%m');
$day=$interval->format('%d');
$year=$interval->format('%y');
if($interval->format('%i%h%d%m%y')=="00000")
{
  //echo $interval->format('%i%h%d%m%y')."<br>";
  return $sec." Segundos";

} 

  else if($interval->format('%h%d%m%y')=="0000"){
     return $min." Minutos";
     }


  else if($interval->format('%d%m%y')=="000"){
     return $hour." Horas";
     }


  else if($interval->format('%m%y')=="00"){
     return $day." Dias";
     }

  else if($interval->format('%y')=="0"){
     return $mon." Mêses";
     }

  else{
     return $year." anos";
     }

  }

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="imgs/favicon.ico">

    <title>Administração do Site</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"  rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    
        <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>    
    <script src="vendor/forms/jquery.form.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Scripts para Modais e Diálogos -->
    <script src="../js/js_modal.js"></script>

  

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color: <?php echo $_SESSION["parametros"]["color"]; ?> !important;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <?php
                        // Buscar imagem do perfil da tbparametros
                        $sqlLogo = "SELECT imagem_perfil FROM tbparametros LIMIT 1";
                        $resultLogo = mysqli_query($conexao, $sqlLogo);
                        $imagemPerfil = null;
                        
                        if ($resultLogo && mysqli_num_rows($resultLogo) > 0) {
                            $linhaLogo = mysqli_fetch_assoc($resultLogo);
                            $imagemPerfil = $linhaLogo['imagem_perfil'];
                        }
                        
                        // Se encontrou imagem em base64, usa; senão usa logo padrão
                        $srcLogo = (!empty($imagemPerfil)) ? $imagemPerfil : "imgs/logo.png";
                    ?>
                    <img src="<?php echo $srcLogo; ?>" width="48" onerror="this.src='imgs/logo.png'">
                </div>
                <div class="sidebar-brand-text mx-3">Início</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

         

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Gerenciamento
            </div>

            <!-- Nav Item - Gerenciar Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseGerenciar"
                    aria-expanded="true" aria-controls="collapseGerenciar">
                    <i class="fas fa-fw fa-tasks"></i>
                    <span>Gerenciar</span>
                </a>
                <div id="collapseGerenciar" class="collapse" aria-labelledby="headingGerenciar"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">      
                        <a class="collapse-item" href="?pg=Kanban">Kanban</a>  
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Cadastros</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">      
                        <a class="collapse-item" href="?pg=Menu">Menus</a>  
                        <a class="collapse-item" href="?pg=Departamentos">Departamentos</a>
                        <a class="collapse-item" href="?pg=Telefones">Telefones Notificações</a> 
                        <a class="collapse-item" href="?pg=Telefones">Notificações Agendadas</a>       
                        <a class="collapse-item" href="?pg=Respostas_automaticas">Respostas Automáticas</a> 
                        <a class="collapse-item" href="?pg=Agendamentos">Mensagens Agendadas</a>  
                        <a class="collapse-item" href="?pg=Horarios">Horários de Atendimentos</a>  
                        <a class="collapse-item" href="?pg=Usuarios">Usuários</a>
                        <a class="collapse-item" href="?pg=Contatos">Contatos</a>
                        <a class="collapse-item" href="?pg=Etiquetas">Etiquetas</a>
                        <a class="collapse-item" href="?pg=RespostasRapidas">Respostas Rápidas</a>  
                    </div>
                </div>
            </li>
            
             <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOutros"
                    aria-expanded="true" aria-controls="collapseOutros">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Relatórios</span>
                </a>
                <div id="collapseOutros" class="collapse" aria-labelledby="headingOutros" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">   
                       <a class="collapse-item" href="?pg=RelAtendimentos">Atendimentos</a>
                       <a class="collapse-item" href="?pg=RelClassificacaoMedia">Classificação Média</a>                        
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMarketing"
                    aria-expanded="true" aria-controls="collapseMarketing">
                    <i class="fas fa-fw fa-share-alt"></i>
                    <span>Marketing</span>
                </a>
                <div id="collapseMarketing" class="collapse" aria-labelledby="headingMarketing" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">   
                       <a class="collapse-item" href="?pg=Stories">Stories</a>
                       <a class="collapse-item" href="?pg=EnvioemMassa">Envio em massa</a>                        
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseConhecimento"
                    aria-expanded="true" aria-controls="collapseConhecimento">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Conhecimento</span>
                </a>
                <div id="collapseConhecimento" class="collapse" aria-labelledby="headingConhecimento" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">   
                       <a class="collapse-item" href="?pg=BaseConhecimento">Base de Conhecimento</a>                        
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseChat"
                    aria-expanded="true" aria-controls="collapseChat">
                    <i class="fas fa-fw fa-comments"></i>
                    <span>Chat</span>
                </a>
                <div id="collapseChat" class="collapse" aria-labelledby="headingChat" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">   
                       <a class="collapse-item" href="?pg=ChatSite">Chat Site</a>
                       <a class="collapse-item" href="?pg=WebChat">Web Chat</a>                        
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">


            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <div class="sidebar-card">
                <img class="sidebar-card-illustration mb-2" src="imgs/undraw_rocket.svg" alt="">
                <p class="text-center mb-2"><strong>BSSI Tech Info</strong> nós criamos as ferramentas para você crescer!</p>
                <a class="btn btn-success btn-sm" href="https://www.bitstreamsi.com.br">Acesse nosso site!</a>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">                           
                            <div class="input-group-append">
                                <a href="../conversas.php" target="_blank" class="btn btn-success" type="button">
                                    <i class="fas fa-bars fa-sm"></i> Abrir o SAW
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Pesquisar por..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                       

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">0</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Emails Recebidos
                                </h6>
                                <?php
								// LIsto os emails								 
								
								 if (qtdLinhas('select * from tbnotificacoes where visualizado = false limit 1') != 0){
						
								 $consulta = "select * from tbnotificacoes where visualizado = false order by id desc limit 7";
								 $lista = mysqli_query($conexao, $consulta);
								 while ($email = mysqli_fetch_assoc($lista) ) {
									
									  echo '
										 <a class="dropdown-item d-flex align-items-center" href="?pg=Emails">
										<div class="dropdown-list-image mr-3">
											<img class="rounded-circle" src="imgs/undraw_profile_1.svg"
												alt="">
											<div class="status-indicator bg-success"></div>
										</div>
										<div class="font-weight-bold">
											<div class="text-truncate">'.$email["titulo"].' </div>
											<div class="small text-gray-500">'.$email["nome"].' · '.RetornaTempo($email["data"]).'</div>
										</div>
									</a>
									   ';
									   
									 }
								 }
								// }
								
								?>                          
                               
                               
                                
                                <a class="dropdown-item text-center small text-gray-500" href="#">Ver todas as mensagens</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION["usuariosaw"]["nome"]; ?></span>
                                <?php
                                    // Buscar foto do usuário logado
                                    $idUsuario = $_SESSION["usuariosaw"]["id"];
                                    $sqlFoto = "SELECT foto FROM tbusuario WHERE id = " . intval($idUsuario) . " LIMIT 1";
                                    $resultFoto = mysqli_query($conexao, $sqlFoto);
                                    $fotoUsuario = null;
                                    
                                    if ($resultFoto && mysqli_num_rows($resultFoto) > 0) {
                                        $linhaFoto = mysqli_fetch_assoc($resultFoto);
                                        $fotoUsuario = $linhaFoto['foto'];
                                    }
                                    
                                    // Se encontrou foto em base64, usa; senão usa imagem padrão
                                    $srcFoto = (!empty($fotoUsuario)) ? $fotoUsuario : "imgs/undraw_profile.svg";
                                ?>
                                <img class="img-profile rounded-circle"
                                    src="<?php echo $srcFoto; ?>"
                                    width="90" height="90"
                                    onerror="this.src='imgs/undraw_profile.svg'">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="?pg=Perfil">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Perfil do usuário
                                </a>
                                <a class="dropdown-item" href="?pg=Config">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Configurações Gerais
                                </a>
                                  <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../logOff.php">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
              
                 <?php
					if (isset($_GET["pg"])){
                        
						switch ($_GET["pg"]){
                            case 'Kanban'                       : $incluir = 'kanban/index.php'; break;
                            
                            case 'Menu'                         : $incluir = 'cadastros/menu/index.php'; break;
                            case 'Departamentos'                : $incluir = 'cadastros/departamentos/index.php'; break;
                            case 'Telefones'                    : $incluir = 'cadastros/telefoneaviso/index.php'; break;
                            case 'Respostas_automaticas'        : $incluir = 'cadastros/respostasautomaticas/index.php'; break;
                            case 'ListaTransmissao'             : $incluir = 'cadastros/lista_transmissao/index.php'; break;
                            case 'Agendamentos'                 : $incluir = 'cadastros/agendamentos/index.php'; break;
                            case 'Horarios'                     : $incluir = 'cadastros/horarios/index.php'; break;
                            case 'Usuarios'                     : $incluir = 'cadastros/usuarios/index.php'; break;	
                            case 'Config'                       : $incluir = 'cadastros/configuracoes/index.php'; break;
                            case 'Perfil'                       : $incluir = 'cadastros/perfil/index.php'; break;
                            case 'Contatos'                     : $incluir = '../cadastros/contatos/index.php'; break;
                            case 'Etiquetas'                    : $incluir = '../cadastros/etiquetas/index.php'; break;
                            case 'RespostasRapidas'             : $incluir = '../cadastros/respostasrapidas/index.php'; break;

                            case 'RelAtendimentos'              : $incluir = 'relatorios/atendimentos/index.php'; break;
                            case 'RelClassificacaoMedia'        : $incluir = 'relatorios/classificacao_media/index.php'; break;
                            
                            case 'Stories'                      : $incluir = 'marketing/stories/index.php'; break;
                            case 'EnvioemMassa'                 : $incluir = 'marketing/envios_massa/index.php'; break;
                            
                            case 'BaseConhecimento'             : $incluir = '../modulos/baseconhecimento/index.php'; break;
                            case 'ChatSite'                     : $incluir = '../modulos/chatsite/index.php'; break;
                            case 'WebChat'                      : $incluir = '../webchat/index.php'; break;
							default : $incluir = "dashboard/index.php"; break;
						}
						include($incluir);
					}else{
						include("dashboard/index.php");
					}
					?>
                    

                 

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

			<!-- Modal para Crop de Imagem -->
			<div id="modalUpload" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
				<div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%;">
					<h4 style="margin-top: 0;">Recortar Imagem</h4>
					<div id="image_demo" style="width: 100%; max-width: 500px;"></div>
					<br>
					<div style="text-align: center;">
						<button class="btn btn-success crop_image" style="margin-right: 10px;">Recortar</button>
						<button class="btn btn-danger" id="btnCancelaUpload">Cancelar</button>
					</div>
				</div>
			</div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Desenvolvido por <a href="https://www.bitstreamsi.com.br">BSSI Tech Info</a></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

  <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>







</body>

</html>