$(function () {
    var cliente = $('select[name="id_cliente"]');
    var factura = $('#generar_factura');
    var formulario = cliente.closest('form');
    var aviso = $('#cliente_facturacion_aviso');
    function actualizar() {
        if (cliente.prop('disabled')) { aviso.hide(); return true; }
        var datos = cliente.find('option:selected').attr('data-fiscal');
        var resultado = datos ? JSON.parse(datos) : null;
        aviso.empty().removeClass('alert-danger alert-warning alert-info');
        if (!resultado) { aviso.hide(); return !factura.is(':checked'); }
        aviso.show().addClass(!resultado.valido ? 'alert-danger' : (resultado.avisos.length ? 'alert-warning' : 'alert-info'));
        $('<div>').text('CUIT: ' + (resultado.cuit || 'Sin cargar') + ' | Condicion fiscal: ' + resultado.condicion).appendTo(aviso);
        resultado.errores.concat(resultado.avisos).forEach(function (mensaje) {
            $('<div>').text(mensaje).appendTo(aviso);
        });
        if (!resultado.valido) {
            $('<div>').text(factura.is(':checked') ? 'No se puede generar la factura hasta corregir estos datos.' : 'Puede guardar el cobro sin factura. Para facturar debe corregir estos datos.').appendTo(aviso);
            $('<a>').attr({href: 'modificarCliente.php?id=' + encodeURIComponent(cliente.val()), target: '_blank', rel: 'noopener'}).text('Corregir datos del cliente').appendTo(aviso);
            $('<div>').text('Luego de corregirlos, recargue esta pagina para actualizar los datos.').appendTo(aviso);
        }
        return !factura.is(':checked') || resultado.valido;
    }
    cliente.on('change changed.bs.select', actualizar);
    factura.on('change', actualizar);
    formulario.on('submit', function (event) {
        if (!actualizar()) {
            event.preventDefault();
            aviso.attr('tabindex', '-1').show().trigger('focus');
        }
    });
    actualizar();
});
