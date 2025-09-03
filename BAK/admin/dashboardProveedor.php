<?php 
require("config.php");
require 'database.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('head_tables.php');?>
  </head>
  <body class="light-only">
    <!-- page-wrapper Start-->
    <div class="page-wrapper">
      <!-- Page Header Start-->
      <?php include('header_proveedor.php');?>
      <!-- Page Header Ends                              -->
      <!-- Page Body Start-->
      <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <?php include('menu_proveedor.php');?>
        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-header">
              <div class="row">
                <div class="col-10">
                  <div class="page-header-left">
                    <h3>Panel General</h3>
                    <ol class="breadcrumb">
                      <li class="breadcrumb-item"><a href="index.php"><i data-feather="home"></i></a></li>
                      <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                  </div>
                </div>
                <!-- Bookmark Start-->
                <div class="col-2">
                  <div class="bookmark pull-right">
                    <ul>
                      <li><a  target="_blank" data-container="body" data-toggle="popover" data-placement="top" title="" data-original-title="<?php echo date('d-m-Y');?>"><i data-feather="calendar"></i></a></li>
                    </ul>
                  </div>
                </div>
                <!-- Bookmark Ends-->
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
			  <div class="col-lg-12">
                  <div class="header-faq">
                    <h5 class="mb-0">Ayuda / Preguntas Frecuentes</h5>
                  </div>
                  <div class="row default-according style-1 faq-accordion" id="accordionoc">
                    <div class="col-xl-8 xl-60 col-lg-6 col-md-7">
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon" aria-expanded="false" aria-controls="collapseicon"><i data-feather="help-circle"></i> Integrating WordPress with Your Website?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon" aria-labelledby="collapseicon" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon2" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i> WordPress Site Maintenance ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon2" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon3" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Meta Tags in WordPress ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon3" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon4" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>   WordPress in Your Language ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon4" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="faq-title">
                        <h6>Ventas</h6>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon5" aria-expanded="false"><i data-feather="help-circle"></i> WordPress Site Maintenance ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon5" aria-labelledby="collapseicon5" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon7" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>   WordPress in Your Language ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon7" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon8" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Integrating WordPress with Your Website ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon8" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="faq-title">
                        <h6>Cobranzas</h6>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon9" aria-expanded="false"><i data-feather="help-circle"></i> WordPress Site Maintenance ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon9" aria-labelledby="collapseicon9" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon10" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Meta Tags in WordPress ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon10" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon11" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Validating a Website ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon11" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon12" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Know Your Sources ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon12" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="faq-title">
                        <h6>Canjes</h6>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon13" aria-expanded="false"><i data-feather="help-circle"></i>Integrating WordPress with Your Website ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon13" aria-labelledby="collapseicon13" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon14" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>WordPress Site Maintenance ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon14" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon16" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i> WordPress in Your Language ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon16" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon17" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>  Validating a Website ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon17" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                      <div class="card">
                        <div class="card-header">
                          <h5 class="mb-0">
                            <button class="btn btn-link collapsed pl-0" data-toggle="collapse" data-target="#collapseicon18" aria-expanded="false" aria-controls="collapseicon2"><i data-feather="help-circle"></i>Meta Tags in WordPress ?</button>
                          </h5>
                        </div>
                        <div class="collapse" id="collapseicon18" data-parent="#accordionoc">
                          <div class="card-body">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</div>
                        </div>
                      </div>
                    </div>
				</div>
				</div>
			</div>
          </div>
          <!-- Container-fluid Ends-->
        </div>
          <!-- Container-fluid Ends-->
        <!-- footer start-->
        <?php include("footer.php"); ?>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap js-->
    <script src="assets/js/bootstrap/popper.min.js"></script>
    <script src="assets/js/bootstrap/bootstrap.js"></script>
    <!-- feather icon js-->
    <script src="assets/js/icons/feather-icon/feather.min.js"></script>
    <script src="assets/js/icons/feather-icon/feather-icon.js"></script>
    <!-- Sidebar jquery-->
    <script src="assets/js/sidebar-menu.js"></script>
    <script src="assets/js/config.js"></script>
    <!-- Plugins JS start-->
	<script src="assets/js/datatable/datatables/jquery.dataTables.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.buttons.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/jszip.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.colVis.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/pdfmake.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/vfs_fonts.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.autoFill.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.select.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.html5.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/buttons.print.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.responsive.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/responsive.bootstrap4.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.keyTable.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.colReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.fixedHeader.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.rowReorder.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/dataTables.scroller.min.js"></script>
    <script src="assets/js/datatable/datatable-extension/custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
	<script src="assets/js/bootstrap/tableExport.js"></script>
	<script src="assets/js/bootstrap/jquery.base64.js"></script>
    <script src="assets/js/chart/chartist/chartist.js"></script>
    <script src="assets/js/chart/morris-chart/raphael.js"></script>
    <script src="assets/js/chart/morris-chart/morris.js"></script>
    <script src="assets/js/chart/morris-chart/prettify.min.js"></script>
    <script src="assets/js/chart/chartjs/chart.min.js"></script>
    <script src="assets/js/chart/flot-chart/excanvas.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.time.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.categories.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.stack.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.pie.js"></script>
    <script src="assets/js/chart/flot-chart/jquery.flot.symbol.js"></script>
    <script src="assets/js/chart/google/google-chart-loader.js"></script>
    <script src="assets/js/chart/peity-chart/peity.jquery.js"></script>
    <script src="assets/js/prism/prism.min.js"></script>
    <script src="assets/js/clipboard/clipboard.min.js"></script>
    <script src="assets/js/counter/jquery.waypoints.min.js"></script>
    <script src="assets/js/counter/jquery.counterup.min.js"></script>
    <script src="assets/js/counter/counter-custom.js"></script>
    <script src="assets/js/custom-card/custom-card.js"></script>
    <script src="assets/js/dashboard/project-custom.js"></script>
    <script src="assets/js/select2/select2.full.min.js"></script>
    <script src="assets/js/select2/select2-custom.js"></script>
    <script src="assets/js/typeahead/handlebars.js"></script>
    <script src="assets/js/typeahead/typeahead.bundle.js"></script>
    <script src="assets/js/typeahead/typeahead.custom.js"></script>
    <script src="assets/js/chat-menu.js"></script>
    <script src="assets/js/tooltip-init.js"></script>
    <script src="assets/js/typeahead-search/handlebars.js"></script>
    <script src="assets/js/typeahead-search/typeahead-custom.js"></script>
	<script src="assets/js/chart/morris-chart/raphael.js"></script>
    <script src="assets/js/chart/morris-chart/morris.js"></script>
    <script src="assets/js/chart/morris-chart/prettify.min.js"></script>
    <script src="assets/js/chart/morris-chart/morris-script.js"></script>
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    
    <!-- Plugin used-->
  </body>
</html>