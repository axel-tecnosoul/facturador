<div class="page-sidebar">
  <div class="main-header-left d-none d-lg-block">
	<div class="logo-wrapper"><a href="dashboard.php"><img src="assets/images/logo.jpg" width="70px" alt=""></a></div>
  </div>
  <div class="sidebar custom-scrollbar">
	<ul class="sidebar-menu">
		
		<?php
    //$id_perfil=$_SESSION['user']['id_perfil'];
    $id_perfil=1;?>
		<li><a class="sidebar-header" href="listarCobros.php"><i data-feather="dollar-sign"></i><span>Cobros</span><i class="fa fa-angle-right pull-right"></i></a></li>
    <li><a class="sidebar-header" href="listarPagos.php"><i data-feather="check-square"></i><span>Pagos</span><i class="fa fa-angle-right pull-right"></i></a></li>
    <li><a class="sidebar-header" href="reporteRentabilidad.php"><i data-feather="check-square"></i><span>Rentabilidad</span><i class="fa fa-angle-right pull-right"></i></a></li>
    <li><a class="sidebar-header" href="listarIPC.php"><i data-feather="percent"></i><span>IPC</span><i class="fa fa-angle-right pull-right"></i></a></li>
<?php
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarClientes.php"><i data-feather="users"></i><span>Clientes</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarColaboradores.php"><i data-feather="user"></i><span>Colaboradores</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}?>
	</ul>
  </div>
</div>