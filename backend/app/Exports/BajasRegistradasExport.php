<?php

namespace App\Exports;

use App\Models\BajaAsignacionDocente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BajasRegistradasExport implements 
    FromQuery, 
    WithHeadings, 
    WithMapping, 
    WithColumnWidths, 
    WithEvents, 
    WithCustomStartCell
{
    protected int $counter = 0;

    public function query()
    {
        return BajaAsignacionDocente::query()->with([
            'asignacion.docente',
            'asignacion.cursoEtapaMateria.cursoEtapa.curso',
            'asignacion.cursoEtapaMateria.cursoMateria.materia',
            'asignacion.cursoEtapaMateria.cursoMateria.curso.anexo',
        ]);
    }

    // Definimos que los datos empiecen en la fila 3 para dejar espacio al título
    public function startCell(): string
    {
        return 'A3';
    }

    public function headings(): array
    {
        return [
            'Nº',
            'Fecha de Baja',
            'Docente',
            'Curso',
            'Division',
            'Cantidad de Horas',
            'Nº Cupof',
            'Materia',
            'Anexo',
            'Motivo',
        ];
    }

    public function map($row): array
    {
        $this->counter++;

        $asignacion = $row->asignacion;
        $cursoEtapaMateria = $asignacion?->cursoEtapaMateria;
        $curso = $cursoEtapaMateria?->cursoEtapa?->curso;
        $cursoMateria = $cursoEtapaMateria?->cursoMateria;
        $materia = $cursoMateria?->materia;
        $anexo = $curso?->anexo;
        $docente = $asignacion?->docente;

        return [
            $this->counter,
            $row->fecha_baja?->format('d/m/Y') ?? '',
            trim(($docente?->apellido ?? '') . ' ' . ($docente?->nombre ?? '')),
            $curso?->nombre ?? '',
            $curso?->division ?? '',
            $cursoEtapaMateria?->horas_catedra ?? '',
            $cursoMateria?->nro_cupof ?? '',
            $materia?->nombre ?? '',
            $anexo?->nombre ?? '',
            $row->motivo ?? '',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 16,
            'C' => 40,
            'D' => 20,
            'E' => 12,
            'F' => 18,
            'G' => 15,
            'H' => 35,
            'I' => 25,
            'J' => 45,
        ];
    }

    /**
     * Estilos y Título Dinámico
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $anioActual = date('Y');
                
                // 1. Insertar y Estilizar el Título
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', "PLANILLA DE CONTROL DE RENUNCIAS Y BAJAS - CICLO LECTIVO $anioActual");
                
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4472C4'], // Azul Institucional
                    ],
                ]);

                // 2. Estilizar el Encabezado (Fila 3 ya que startCell es A3)
                $headerRange = 'A3:J3';
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '203764'], // Azul Oscuro
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // 3. Altura de Filas (Hancha)
                $sheet->getRowDimension('1')->setRowHeight(40); // Fila del título
                $sheet->getRowDimension('3')->setRowHeight(25); // Fila de encabezados
                
                // Aplicar bordes a los datos (opcional, mejora la lectura)
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A3:J$highestRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                // ... dentro de AfterSheet ...
                $highestRow = $sheet->getHighestRow(); // Detecta hasta qué fila hay datos

                for ($row = 4; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(20); // Cambia 20 por el alto que desees
                }
            },
        ];
    }
}