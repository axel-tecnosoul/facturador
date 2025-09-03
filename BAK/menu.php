<div class="page-sidebar">
  <div class="main-header-left d-none d-lg-block">
	<div class="logo-wrapper"><a href="dashboard.php"><img src="assets/images/logoBackend.png" width="200px" alt=""></a></div>
  </div>
  <div class="sidebar custom-scrollbar">
	<ul class="sidebar-menu">
		
		<?php
    $id_perfil=$_SESSION['user']['id_perfil']; 

		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarProductos.php"><i data-feather="clipboard"></i><span>Productos</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
			<li><a class="sidebar-header" href="listarProductosVendidos.php"><i data-feather="dollar-sign"></i><span>Productos Vendidos</span><i class="fa fa-angle-right pull-right"></i></a></li><?php
		}
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarStock.php"><i data-feather="layout"></i><span>Stock</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarMovimientoStock.php"><i data-feather="repeat"></i><span>Movimientos de Stock</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}?>
		<li><a class="sidebar-header" href="listarVentas.php"><i data-feather="settings"></i><span>Ventas</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
    //if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarCajaChica.php"><i data-feather="dollar-sign"></i><span>Caja chica</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		//}
    if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarCajaGrande.php"><i data-feather="dollar-sign" class="mr-0"></i><i data-feather="dollar-sign"></i><span>Caja grande</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarDescuentos.php"><i data-feather="flag"></i><span>Descuentos</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarPagosPendientes.php"><i data-feather="maximize"></i><span>Pagos Pendientes</span><i class="fa fa-angle-right pull-right"></i></a></li>
      <li><a class="sidebar-header" href="listarPagosRealizados.php"><i data-feather="check-square"></i><span>Pagos Realizados</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarCanjes.php"><i data-feather="alert-circle"></i><span>Canjes Crédito</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
			<li><a class="sidebar-header" href="listarDevoluciones.php"><i data-feather="repeat"></i><span>Devoluciones</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		  }
		  
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarProveedores.php"><i data-feather="file"></i><span>Proveedores</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarSuscripciones.php"><i data-feather="tag"></i><span>Suscripciones</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarContactos.php"><i data-feather="home"></i><span>Contactos</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		if ($id_perfil != 3) {?>
		  <li><a class="sidebar-header" href="listarTurnos.php"><i data-feather="calendar"></i><span>Turnos</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}
		/*if ($id_perfil != 3) {?>
			<li><a class="sidebar-header" href="listarEmpleados.php"><i data-feather="user"></i><span>Empleados</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		  }
		if ($id_perfil == 1) {?>
		  <li><a class="sidebar-header" href="listarUsuarios.php"><i data-feather="user"></i><span>Usuarios</span><i class="fa fa-angle-right pull-right"></i></a></li><?php 
		}*/
		if ($id_perfil == 1) {?>
      <li><a class="sidebar-header" href="listarParametros.php"><i data-feather="settings"></i><span>Parametros</span><i class="fa fa-angle-right pull-right"></i></a></li>
      <li><a class="sidebar-header" href="#"><i data-feather="bar-chart"></i><span>Reportes</span><i class="fa fa-angle-right pull-right"></i></a>
        <ul class="sidebar-submenu">
          <li><a href="balanceAlmacenes.php"><i class="fa fa-circle"></i>Balance de Almacenes</a></li>
          <li><a href="rankingProveedores.php"><i class="fa fa-circle"></i>Ranking Proveedores</a></li>
          <li><a href="rankingProductos.php"><i class="fa fa-circle"></i>Ranking Productos</a></li>
          <li><a href="rankingCategorias.php"><i class="fa fa-circle"></i>Ranking Categorías</a></li>
          <li><a href="evolutivoVentas.php"><i class="fa fa-circle"></i>Evolutivo Ventas</a></li>
          <li><a href="reporteAsistencias.php"><i class="fa fa-circle"></i>Asistencia Empleados</a></li>
          <li><a href="reporteCierreCaja.php"><i class="fa fa-circle"></i>Cierres de Caja</a></li>
          <li><a href="reporteComprasDirectas.php"><i class="fa fa-circle"></i>Compras Directas</a></li>
        </ul>
      </li><?php 
		}
    if ($id_perfil == 1) {?>
      <li><a class="sidebar-header" href="#"><i data-feather="database"></i><span>Maestros</span><i class="fa fa-angle-right pull-right"></i></a>
        <ul class="sidebar-submenu"><?php
          if ($id_perfil != 3) {?>
            <li><a href="listarCategorias.php"><i class="fa fa-circle"></i>Categorías</a></li><?php 
          }?>
          <li><a href="listarAlmacenes.php"><i class="fa fa-circle"></i>Almacenes</a></li>
          <li><a href="listarFormasPago.php"><i class="fa fa-circle"></i>Formas de Pago</a></li>
		      <li><a href="listarTiposMotivos.php"><i class="fa fa-circle"></i>Tipos de Motivos</a></li>
          <li><a href="listarMotivosCaja.php"><i class="fa fa-circle"></i>Motivos Caja</a></li><?php
          if ($id_perfil != 3) {?>
            <li><a href="listarEmpleados.php"><i class="fa fa-circle"></i>Empleados</a></li>
			      <!-- <li><a class="sidebar-header" href="listarEmpleados.php"><i data-feather="user"></i><span>Empleados</span><i class="fa fa-angle-right pull-right"></i></a></li> --><?php 
		      }?>
          <li><a href="listarUsuarios.php"><i class="fa fa-circle"></i>Usuarios</a></li>
        </ul>
      </li><?php 
		}?>

	</ul>
  </div>
</div>