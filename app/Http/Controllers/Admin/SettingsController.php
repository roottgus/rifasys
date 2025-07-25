<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Muestra la página de configuración de métodos de pago.
     */
    public function payments()
{
    $methods = PaymentMethod::orderBy('key')->get();

    $configs = [
        'tran_bancaria_nacional' => [
            'icon'        => 'fas fa-university text-indigo-500',
            'descripcion' => 'Datos requeridos para transferencias en bancos nacionales.',
            'info'        => 'Solo cuentas a nombre de la empresa.',
            'fields'      => [
                ['key'=>'banco', 'label'=>'Banco', 'placeholder'=>'Banco del Titular'],
                ['key'=>'titular', 'label'=>'Titular', 'placeholder'=>'Nombre completo titular'],
                ['key'=>'cuenta', 'label'=>'Cuenta o IBAN', 'placeholder'=>'Número de cuenta'],
                ['key'=>'ci_rif', 'label'=>'CI/RIF Titular', 'placeholder'=>'V12345678 o J123456789'],
            ],
        ],
        'pago_efectivo' => [
            'icon'        => 'fas fa-money-bill-wave text-green-600',
            'descripcion' => 'Para cobros en efectivo en taquilla o punto de venta físico.',
            'info'        => 'Puedes describir ubicación o condiciones.',
            'fields'      => [
                ['key'=>'detalle', 'label'=>'Descripción (opcional)', 'placeholder'=>'Condiciones o lugar de pago'],
            ],
        ],
        'pago_movil' => [
            'icon'        => 'fas fa-mobile-alt text-orange-500',
            'descripcion' => 'Permite recibir pagos vía Pago Móvil nacional.',
            'info'        => 'Incluye número y datos del titular.',
            'fields'      => [
                ['key'=>'banco', 'label'=>'Banco', 'placeholder'=>'Banco emisor'],
                ['key'=>'telefono', 'label'=>'Teléfono Pago Móvil', 'placeholder'=>'0414xxxxxxx'],
                ['key'=>'ci_rif', 'label'=>'CI/RIF Titular', 'placeholder'=>'V12345678'],
            ],
        ],
        'tran_bancaria_internacional' => [
            'icon'        => 'fas fa-globe text-indigo-400',
            'descripcion' => 'Configura transferencias desde bancos internacionales.',
            'info'        => 'Indica IBAN/SWIFT, banco y titular.',
            'fields'      => [
                ['key'=>'banco', 'label'=>'Banco', 'placeholder'=>'Banco internacional'],
                ['key'=>'titular', 'label'=>'Titular', 'placeholder'=>'Nombre completo titular'],
                ['key'=>'cuenta', 'label'=>'Cuenta o IBAN', 'placeholder'=>'IBAN/SWIFT'],
                ['key'=>'ci_rif', 'label'=>'CI/RIF Titular', 'placeholder'=>'Identificación'],
            ],
        ],
       'zelle' => [
        // SOLO LA RUTA, NO HTML
        'icon'        => '/images/zelle.svg',
        'descripcion' => 'Configura recepción de pagos a través de Zelle.',
        'info'        => 'Debe incluir email y nombre del titular.',
        'fields'      => [
            ['key'=>'correo', 'label'=>'Correo Zelle', 'placeholder'=>'ejemplo@email.com'],
            ['key'=>'titular', 'label'=>'Titular Zelle', 'placeholder'=>'Nombre completo titular'],
        ],
    ],

    ];

    // Mapeo para pasar a Alpine todos los métodos existentes
    $paymentConfigs = $methods->map(function($m) use ($configs) {
    $config = $configs[$m->key] ?? [
        'icon'        => 'fas fa-credit-card text-gray-400',
        'descripcion' => '',
        'info'        => '',
        'fields'      => [],
    ];
    $details = is_array($m->details) ? $m->details : (json_decode($m->details, true) ?: []);
    // Aquí el truco: si es zelle, pasa solo la ruta, si no, arma el <i>
    $icon = $m->key === 'zelle'
        ? $config['icon'] // solo la ruta
        : "<i class=\"{$config['icon']}\"></i>";

    return [
        'id'          => $m->id,
        'key'         => $m->key,
        'name'        => $m->name,
        'alias'       => $m->alias,
        'enabled'     => !! $m->enabled,
        'icon'        => $icon,
        'descripcion' => $config['descripcion'],
        'info'        => $config['info'],
        'fields'      => collect($config['fields'])->map(function($f) use ($details) {
            $val = $details[$f['key']] ?? '';
            return $f + ['value' => $val];
        })->toArray(),
    ];
})->values();


    return view('admin.settings.payments', [
        'methods' => $paymentConfigs,
    ]);
}


    /**
     * Procesa el guardado de la configuración de métodos de pago.
     * Ahora soporta múltiples variantes por tipo.
     */
    public function saveAllPaymentMethods(Request $request)
    {
        $data = $request->input('methods', []);

        $actualIds = [];
        foreach ($data as $idx => $input) {
            $id = $input['id'] ?? null;
            $key = $input['key'] ?? null;

            // No procesar si no hay key (esto evita el error en la BD)
            if (!$key) {
                continue;
            }

            // Busca por id si existe, si no, crea uno nuevo
            if ($id && $method = PaymentMethod::find($id)) {
                // Actualiza existente
            } else {
                // Crear nuevo (requiere key y name)
                $method = new PaymentMethod();
                $method->key = $key;
                $method->name = $input['name'] ?? ucfirst(str_replace('_', ' ', $key));
            }

            // Guardar/actualizar datos
            $method->alias = $input['alias'] ?? null;
            $method->enabled = isset($input['enabled']) && $input['enabled'] ? 1 : 0;
            $method->details = $input['details'] ?? [];
            $method->save();
            $actualIds[] = $method->id;
        }

        // Opcional: desactivar métodos eliminados (puedes activar/desactivar si deseas)
        // PaymentMethod::whereNotIn('id', $actualIds)->update(['enabled' => 0]);

        // Detectar si es AJAX/fetch (JSON)
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Métodos de pago actualizados correctamente.',
            ]);
        }

        // Submit tradicional
        return redirect()
            ->route('admin.settings.payments')
            ->with('success', 'Configuración de métodos de pago actualizada correctamente.');
    }

    public function togglePaymentMethod(Request $request)
    {
        $id = $request->input('id');
        $enabled = $request->input('enabled') ? 1 : 0;

        $method = \App\Models\PaymentMethod::findOrFail($id);
        $method->enabled = $enabled;
        $method->save();

        return response()->json([
            'success' => true,
            'enabled' => $enabled,
            'id' => $method->id,
        ]);
    }

    public function deletePaymentMethod($id)
    {
        $method = \App\Models\PaymentMethod::findOrFail($id);
        $method->delete();

        return response()->json([
            'success' => true,
            'message' => 'Variante eliminada correctamente.',
        ]);
    }

    /**
     * Muestra la página de configuración de empresa (logo, nombre, etc).
     */
    public function company()
    {
        $settings = [
            'empresa_nombre'    => Setting::get('empresa_nombre', config('app.name')),
            'empresa_logo'      => Setting::get('empresa_logo', null),
            'empresa_favicon'   => Setting::get('empresa_favicon', null),
            'empresa_color'     => Setting::get('empresa_color', '#ff7f00'),
            'dashboard_title'   => Setting::get('dashboard_title', 'Dashboard Administrativo'),
        ];
        return view('admin.settings.company', compact('settings'));
    }

    /**
     * Procesa la actualización de la configuración de empresa (logo, nombre, favicon, color, dashboard title).
     */
    public function companyUpdate(Request $request)
    {
        // --- Eliminar logo ---
        if ($request->filled('eliminar_logo')) {
            $logo = Setting::get('empresa_logo');
            if ($logo) {
                Storage::disk('public')->delete('logos/' . $logo);
                Setting::set('empresa_logo', null);
            }
            return back()->with('success', 'Logo eliminado correctamente.');
        }

        // --- Eliminar favicon ---
        if ($request->filled('eliminar_favicon')) {
            $favicon = Setting::get('empresa_favicon');
            if ($favicon) {
                Storage::disk('public')->delete('logos/' . $favicon);
                Setting::set('empresa_favicon', null);
            }
            return back()->with('success', 'Favicon eliminado correctamente.');
        }

        // --- Validación general ---
        $request->validate([
            'empresa_nombre'   => 'required|string|max:100',
            'empresa_logo'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'empresa_favicon'  => 'nullable|image|mimes:png,ico|max:1024',
            'empresa_color'    => 'nullable|string|max:9',
            'dashboard_title'  => 'nullable|string|max:60', // <--- Nuevo campo
        ]);

        // Nombre de empresa
        Setting::set('empresa_nombre', $request->empresa_nombre);

        // Logo de empresa
if ($request->hasFile('empresa_logo')) {
    // Asegúrate que la carpeta existe
    $path = storage_path('app/public/logos');
    if (!file_exists($path)) {
        mkdir($path, 0775, true);
    }
    $logo = $request->file('empresa_logo')->store('logos', 'public');
// Esto ya retorna solo 'logos/xxxx.png', SIN el prefijo 'public/'
Setting::set('empresa_logo', $logo); // Guarda la ruta completa relativa a /storage

}


        // Favicon de empresa
        if ($request->hasFile('empresa_favicon')) {
            $favicon = $request->file('empresa_favicon')->store('public/logos');
            $faviconPath = basename($favicon);
            Setting::set('empresa_favicon', $faviconPath);
        }

        // Color corporativo
        if ($request->filled('empresa_color')) {
            Setting::set('empresa_color', $request->empresa_color);
        }

        // Dashboard Title
        Setting::set('dashboard_title', $request->dashboard_title ?? 'Dashboard Administrativo');

        return back()->with('success', 'Configuración de empresa actualizada correctamente.');
    }
}
