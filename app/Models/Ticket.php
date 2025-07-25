<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\PremioEspecial;

class Ticket extends Model
{
    /**
     * Atributos que se añaden automáticamente al array/JSON.
     */
    protected $appends = ['qr_code', 'numero_formateado'];

    /**
     * Generar UUID automáticamente al crear si no está presente.
     */
    protected static function booted()
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->uuid)) {
                $ticket->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Campos asignables.
     */
    protected $fillable = [
        'rifa_id',
        'cliente_id',
        'uuid',
        'numero',
        'estado',
        'precio_ticket',
    ];

    /**
     * Relación con la Rifa.
     */
    public function rifa(): BelongsTo
    {
        return $this->belongsTo(Rifa::class);
    }

    /**
     * Relación con el Cliente.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con los Abonos.
     */
    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class);
    }

    /**
     * Total abonado.
     */
    public function getTotalAbonadoAttribute(): float
    {
        return (float) $this->abonos()->sum('monto');
    }

    /**
     * Número formateado con ceros a la izquierda según la cantidad de números de la rifa.
     */
    public function getNumeroFormateadoAttribute(): string
{
    if ($this->rifa && $this->rifa->cantidad_numeros) {
        $padLength = strlen((string) ($this->rifa->cantidad_numeros - 1));
    } else {
        $padLength = 3;
    }
    return str_pad($this->numero, $padLength, '0', STR_PAD_LEFT);
}


    /**
     * Generar QR code como SVG Base64 (sin Imagick ni GD).
     */
   public function getQrCodeAttribute(): string
{
    try {
        $url = route('tickets.verificar', $this->uuid);

        // Genera PNG base64 usando Imagick (requiere la extensión habilitada)
        $png = QrCode::format('png')->size(120)->margin(1)->generate($url);


        return 'data:image/png;base64,' . base64_encode($png);
    } catch (\Throwable $e) {
        // En caso extremo, devuelve una imagen PNG vacía de 1×1 px
        $blank = base64_encode("\x89PNG\r\n\x1a\n\x00\x00\x00\rIHDR\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00\x1f\x15\xc4\x89\x00\x00\x00\nIDATx\xdac\xf8\xff\xff?\x00\x05\xfe\x02\xfeA\x81\x90\xcd\x00\x00\x00\x00IEND\xaeB`\x82");
        return 'data:image/png;base64,' . $blank;
    }
}


    /**
     * Evalúa todos los premios especiales de la rifa y retorna el estado de participación del ticket en cada uno.
     *
     * @return array [premio_especial_id => [
     *      'participa' => bool,
     *      'abono_requerido' => float,
     *      'abono_actual' => float,
     *      'mensaje' => string,
     *      'premio' => PremioEspecial
     * ]]
     */
    public function evaluacionPremiosEspeciales(): array
    {
        // Asegura que la relación esté cargada y actualizada
        $rifa = $this->rifa()->with('premiosEspeciales')->first();
        $abono_actual = $this->total_abonado;

        $resultados = [];

        foreach ($rifa->premiosEspeciales as $premio) {
            $participa = $abono_actual >= $premio->abono_minimo;

            $mensaje = $participa
                ? 'Este ticket participa en el premio especial: "' . ($premio->descripcion ?: $premio->tipo_premio) . '".'
                : 'Este ticket NO participa en el premio especial: "' . ($premio->descripcion ?: $premio->tipo_premio) . '". Abono requerido: $' . number_format($premio->abono_minimo, 2) . '.';

            $resultados[$premio->id] = [
                'participa'       => $participa,
                'abono_requerido' => (float) $premio->abono_minimo,
                'abono_actual'    => (float) $abono_actual,
                'mensaje'         => $mensaje,
                'premio'          => $premio,
            ];
        }

        return $resultados;
    }

    /**
     * Evalúa si el ticket califica para un premio especial específico.
     *
     * @param PremioEspecial $premio
     * @return bool
     */
    public function calificaPremioEspecial(PremioEspecial $premio): bool
    {
        return $this->total_abonado >= $premio->abono_minimo;
    }
}
