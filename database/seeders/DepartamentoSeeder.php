<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recepcion\Departamento;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            [1, 'Presidencia Municipal', 'TITULAR', 'PRE'],
            [2, 'Secretaría Municipal', 'TITULAR', 'SM'],
            [3, 'Oficialía Mayor', 'TITULAR', 'OM'],
            [4, 'Jefatura del Despacho de la Presidencia Municipal', 'TITULAR', 'DP'],
            [5, 'Sindicatura Municipal', 'TITULAR', 'SIND'],
            [6, 'Dirección de Desarrollo Urbano y Obras Públicas', 'TITULAR', 'DUOP'],
            [7, 'Dirección de Servicios Públicos', 'TITULAR', 'DSPM'],
            [8, 'Dirección de Gestión Integral de Residuos Sólidos(PROLIMPIA)', 'David Escalante Echeverria', 'DGYRS'],
            [9, 'Dirección de Seguridad Pública y Tránsito', 'CMDTE Omar de la Cruz Herrera Cocom', 'DSPT'],
            [10, 'Dirección de Transporte', 'TITULAR', 'DT'],
            [11, 'Dirección de Movilidad Urbana', 'TITULAR', 'DTMU'],
            [12, 'Sistema Municipal de Agua Potable y Alcantarillado de Progreso(SMAPAP)', 'TITULAR', 'SMAPAP'],
            [13, 'Dirección de Desarrollo Social', 'TITULAR', 'DESOL'],
            [14, 'Dirección del Sistema Municipal para el Desarrollo Integral de la Familia', 'TITULAR', 'DIF'],
            [15, 'Dirección de Salud', 'TITULAR', 'DS'],
            [16, 'Dirección de Educación', 'TITULAR', 'EDU'],
            [17, 'Dirección de Cultura', 'TITULAR', 'DC'],
            [18, 'Dirección de Deporte y Juventud', 'TITULAR', 'DEP'],
            [19, 'Instituto Municipal de la Mujer', 'TITULAR', 'IMM'],
            [20, 'Dirección de Turismo', 'Aurea Elena Gomez Novelo', 'DT'],
            [21, 'Dirección de Pesca', 'TITULAR', 'DP'],
            [22, 'Dirección de Zona Costera y Humedales', 'TITULAR', 'ZCOS'],
            [23, 'Dirección de Fomento al Desarrollo Agropecuario y Pequeños Productores', 'TITULAR', 'DFAPPP'],
            [24, 'Dirección Mercados', 'TITULAR', 'DM'],
            [25, 'Dirección Reguladora de Comercios Establecidos y Espectáculos', 'TITULAR', 'DRCEE'],
            [26, 'Dirección de Finanzas y Tesorería', 'TITULAR', 'DFT'],
            [27, 'Dirección de Administración', 'TITULAR', 'DA'],
            [28, 'Dirección de Contraloría', 'CP Lidia Esther Peraza Zapata', 'CONT'],
            [29, 'ZOFEMAT', 'TITULAR', 'DZMT'],
            [30, 'Dirección de Tecnologías de la Información y Conectividad', 'TITULAR', 'TICS'],
            [31, 'Dirección de Gobernación, Planeación y Mejora Regulatoria', 'TITULAR', 'DGPYMR'],
            [32, 'Unidad Jurídica', 'TITULAR', 'UT'],
            [33, 'Unidad Municipal de Protección Civil', 'TITULAR', 'UMPC'],
            [34, 'Unidad Municipal de la Policía Ecológica', 'TITULAR', 'UMPE'],
            [35, 'Unidad de Transparencia', 'TITULAR', 'UT'],
            [36, 'Dirección de Archivo General', 'TITULAR', 'DAG'],
            [37, 'Unidad de Comunicación Social', 'TITULAR', 'UCS'],
            [38, 'Unidad de Atención Ciudadana', 'TITULAR', 'UAC'],
            [39, 'Unidad de Atención a Comisarías', 'TITULAR', 'UDC'],
            [40, 'Unidad de Funerarias y Cementerios', 'TITULAR', 'UFC'],
            [41, 'Unidad de Fiscalización', 'TITULAR', 'UF'],
            [42, 'Dirección Municipal para el Desarrollo e Inclusión de la Diversidad', 'TITULAR', 'DDID'],
            [43, 'Dirección de Protocolo Y Logística', 'TITULAR', 'DPL'],
            [51, 'Unidad Municipal de Protección Civil', 'Aurelio Teodoro Medina Perez', 'UMPC'],
            [52, 'Subdirección de Comercio en la vía Pública', 'titular', 'SCVPM'],
        ];

        foreach ($departamentos as $depto) {
            Departamento::updateOrCreate(
                ['id_departamento' => $depto[0]], // Busca por ID
                [
                    'nombre' => $depto[1],
                    'responsable' => $depto[2],
                    'prefijo' => $depto[3],
                ]
            );
        }
    }
}