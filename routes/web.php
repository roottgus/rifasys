<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Admin\SplashController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PremioEspecialController;
use App\Http\Controllers\Admin\LoteriaController;
use App\Http\Controllers\Admin\TipoLoteriaController;
use App\Http\Controllers\Admin\TicketVentaController;
use App\Http\Controllers\Admin\TicketGestionController;
use App\Http\Controllers\Admin\ClienteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home pública
Route::get('/', function () {
    return redirect()->route('login');
});


// Verificación de tickets (público)
Route::get('/tickets/verificar/{uuid}', [TicketController::class, 'verificar'])
     ->name('tickets.verificar');

// Marcar ticket como verificado (POST público)
Route::post('/tickets/verificar/{uuid}/marcar', [TicketController::class, 'marcarVerificado'])
     ->name('tickets.marcarVerificado');

/*
|--------------------------------------------------------------------------
| Área Administrativa (Splash + Backend)
|--------------------------------------------------------------------------
*/

// Splash page antes de entrar al panel
Route::get('/admin', [SplashController::class, 'index'])
     ->name('admin.splash');

// Rutas protegidas por autenticación (Laravel Breeze)
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Gestión de Loterías y Tipos
        Route::get('loterias/gestion', [LoteriaController::class, 'gestion'])->name('loterias.gestion');
        Route::resource('loterias', LoteriaController::class)->names('loterias');
        Route::resource('tipos-loteria', TipoLoteriaController::class)->names('tipos-loteria');
        Route::get('loterias/{loteria}/tipos', [TipoLoteriaController::class, 'tiposPorLoteria'])
            ->name('loterias.tipos');

        // CRUD de Rifas
        Route::resource('rifas', \App\Http\Controllers\Admin\RifaController::class);
        Route::post('rifas/{rifa}/winner', [\App\Http\Controllers\Admin\RifaController::class, 'winnerPrincipal'])
            ->name('rifas.winner');
        Route::post('rifas/clone-last', [\App\Http\Controllers\Admin\RifaController::class, 'cloneLast'])
            ->name('rifas.cloneLast');
        Route::get('rifas/{rifa}/participantes', [\App\Http\Controllers\Admin\RifaController::class, 'participantesSolventes'])
            ->name('rifas.participantes');

        // --- TICKETS ---
        // Venta de tickets
        Route::get('tickets/venta', [TicketVentaController::class, 'sale'])->name('tickets.sale');
        Route::post('tickets/procesar-venta', [TicketVentaController::class, 'procesarVenta'])->name('tickets.procesar-venta');
        Route::post('tickets/abonar-global', [TicketGestionController::class, 'abonarGlobal'])->name('tickets.abonar-global');

        // --- GESTIÓN DE TICKETS: CRUD + AJAX ---
        // AJAX para listado de tarjetas inteligente
        Route::get('tickets/ajax', [TicketGestionController::class, 'ajaxIndex'])->name('tickets.ajax');
        // Tickets JSON por rifa
        Route::get('rifas/{rifa}/tickets/json', [TicketGestionController::class, 'ticketsJson'])->name('rifas.tickets.json');
        // NUEVO: Detalle de ticket para modal listado
        Route::get('tickets/{ticket}/detalle', [TicketGestionController::class, 'detalleJson'])->name('tickets.detalle');
        Route::get('tickets/{ticket}/detalle-json', [TicketGestionController::class, 'detalleJson'])->name('tickets.detalle-json');
        // AJAX: Resumen rápido de rifa seleccionada (para el dashboard de tickets)
        Route::get('rifas/{rifa}/resumen', [\App\Http\Controllers\Admin\RifaController::class, 'resumenRifa'])
            ->name('rifas.resumen');

        // CRUD principal de tickets
Route::resource('tickets', TicketGestionController::class);
Route::get('tickets/{ticket}/qr', [TicketGestionController::class, 'qr'])->name('tickets.qr'); // <--- AÑADE ESTA LÍNEA
Route::post('tickets/{ticket}/reserve', [TicketGestionController::class, 'reserve'])->name('tickets.reserve');
Route::get('tickets/{ticket}/pdf', [TicketGestionController::class, 'pdf'])->name('tickets.pdf');
Route::post('tickets/{ticket}/sell', [TicketGestionController::class, 'sell'])->name('tickets.sell');

// Registrar abono para ticket desde la vista de detalle de ticket
Route::post('tickets/{ticket}/abonar', [TicketGestionController::class, 'abonar'])->name('tickets.abonar');

// Imprimir recibo de abono específico
Route::get('abonos/{abono}/recibo', [\App\Http\Controllers\Admin\AbonoController::class, 'recibo'])->name('abonos.recibo');

        // AJAX: Validación de campos únicos de cliente en tiempo real
        Route::post('clientes/validar-campo', [ClienteController::class, 'validarCampo'])->name('clientes.validarCampo');

        // Clientes y Abonos
        Route::resource('clientes', ClienteController::class);
        // AJAX: Tickets del cliente (para modal o consulta rápida)
        Route::get('clientes/{cliente}/tickets-ajax', [ClienteController::class, 'ticketsAjax'])
            ->name('clientes.ticketsAjax');

            // AJAX: Validar referencia única de abono (para pagos)
        Route::get('abonos/validar-referencia', [\App\Http\Controllers\Admin\AbonoController::class, 'validarReferencia'])
            ->name('abonos.validar-referencia');

        Route::resource('abonos',  \App\Http\Controllers\Admin\AbonoController::class);

        // Métodos de Pago (Settings)
        Route::get('settings/payments', [\App\Http\Controllers\Admin\SettingsController::class, 'payments'])
            ->name('settings.payments');
        Route::post('settings/payments', [\App\Http\Controllers\Admin\SettingsController::class, 'saveAllPaymentMethods'])
            ->name('settings.payments.save');
        Route::post('settings/payments/toggle', [\App\Http\Controllers\Admin\SettingsController::class, 'togglePaymentMethod'])
            ->name('settings.payments.toggle');
        Route::delete('settings/payments/delete/{id}', [\App\Http\Controllers\Admin\SettingsController::class, 'deletePaymentMethod'])
            ->name('settings.payments.delete');

        // Configuración de Empresa
        Route::get('configuracion/empresa', [\App\Http\Controllers\Admin\SettingsController::class, 'company'])
            ->name('configuracion.empresa');
        Route::post('configuracion/empresa', [\App\Http\Controllers\Admin\SettingsController::class, 'companyUpdate'])
            ->name('configuracion.empresa.update');

        // Participantes de un Premio Especial (JSON y PDF)
        Route::get('premios/{premio}/participantes', [PremioEspecialController::class, 'participantes'])
            ->name('premios.participantes');
        Route::get('premios/{premio}/participantes/export', [PremioEspecialController::class, 'exportPdf'])
            ->name('premios.participantes.pdf');

           // Descuentos por ticket
Route::resource('descuentos', \App\Http\Controllers\Admin\DescuentoController::class);

// AJAX: Consultar descuento por cantidad (de una rifa, para un cliente y tickets seleccionados)
Route::get('descuentos/ajax', [\App\Http\Controllers\Admin\DescuentoController::class, 'descuentoAjax'])
    ->name('descuentos.ajax');

Route::post('descuentos/obtener-descuento', [\App\Http\Controllers\Admin\DescuentoController::class, 'obtenerDescuentoAjax'])
    ->name('descuentos.obtener-descuento');


    });

    
/*
|--------------------------------------------------------------------------
| User Dashboard & Profile (Laravel Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| RUTA PÚBLICA DE DEPURACIÓN PARA VALIDAR REFERENCIA (quitar al terminar)
|--------------------------------------------------------------------------
*/

// Ruta pública temporal para depuración AJAX de referencia (¡BORRAR EN PRODUCCIÓN!)
Route::get('test/validar-referencia', [\App\Http\Controllers\Admin\AbonoController::class, 'validarReferencia']);

require __DIR__ . '/auth.php';
