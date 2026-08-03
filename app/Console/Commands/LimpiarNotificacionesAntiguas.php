<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use Illuminate\Console\Command;

class LimpiarNotificacionesAntiguas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notificaciones:limpiar
        {--dias-leidas=30 : Días de antigüedad para borrar notificaciones ya leídas}
        {--dias-todas=90 : Días de antigüedad para borrar cualquier notificación, leída o no}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra notificaciones del panel que ya cumplieron su
    período de retención: las leídas se borran a los 30 días, y cualquiera
    (leída o no) se borra a los 90 días para que la tabla no crezca sin control.';

    public function handle(): void
    {
        $diasLeidas = (int) $this->option('dias-leidas');
        $diasTodas  = (int) $this->option('dias-todas');

        $borradasLeidas = Notificacion::where('leida', true)
            ->where('fecha_envio', '<=', now()->subDays($diasLeidas))
            ->delete();

        $borradasPorAntiguedad = Notificacion::where('fecha_envio', '<=', now()->subDays($diasTodas))
            ->delete();

        $total = $borradasLeidas + $borradasPorAntiguedad;

        $this->info("{$total} notificacion(es) borrada(s) "
            . "({$borradasLeidas} leídas de +{$diasLeidas} días, "
            . "{$borradasPorAntiguedad} de +{$diasTodas} días sin importar su estado).");
    }
}
