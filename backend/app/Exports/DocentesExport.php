<?php

namespace App\Exports;

use App\Models\Docente;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DocentesExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    public function query()
    {
        return Docente::query()
            ->with('titulos')
            ->orderBy('apellido')
            ->orderBy('nombre');
    }

    public function headings(): array
    {
        return [
            'Nº',
            'Apellido',
            'Nombre',
            'DNI',
            'CUIL',
            'Email',
            'Teléfono',
            'Dirección',
            'Legajo Junta',
            'Cobra As. Familiares',
            'Trabaja en otras instituciones',
            'Detalle otras instituciones',
            'Tiene Abono Docente',
            'Antigüedad Institución',
            'Antigüedad Docente',
            'Títulos',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 25,
            'C' => 25,
            'D' => 14,
            'E' => 18,
            'F' => 30,
            'G' => 16,
            'H' => 35,
            'I' => 16,
            'J' => 22,
            'K' => 30,
            'L' => 40,
            'M' => 20,
            'N' => 22,
            'O' => 20,
            'P' => 50,
        ];
    }

    public function map($docente): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $docente->apellido ?? '',
            $docente->nombre ?? '',
            $docente->dni ?? '',
            $docente->cuil ?? '',
            $docente->email ?? '',
            $docente->telefono ?? '',
            $docente->direccion ?? '',
            $docente->legajo_junta ?? '',
            $docente->cobra_asignaciones_familiares ? 'Sí' : 'No',
            $docente->trabaja_otras_instituciones ? 'Sí' : 'No',
            $docente->otras_instituciones ?? '',
            $docente->tiene_abono_docente ? 'Sí' : 'No',
            $docente->antiguedad_institucion ?? '',
            $docente->antiguedad_docente ?? '',
            $docente->titulos->pluck('nombre')->implode(', '),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                $sheet->insertNewRowBefore(1, 5);

                $sheet->setCellValue('A1', 'COLEGIO SECUNDARIO N°59 "OLGA M. DE AREDEZ" MODALIDAD DE JOVENES Y ADULTOS');
                $sheet->setCellValue('A2', 'Alvear N° 1145 - 4600 San Salvador de Jujuy - JUJUY');
                $sheet->setCellValue('A3', 'LISTADO DE DOCENTES');
                $sheet->mergeCells('A1:P1');
                $sheet->mergeCells('A2:P2');
                $sheet->mergeCells('A3:P3');

                $sheet->getRowDimension(1)->setRowHeight(44);
                $sheet->getRowDimension(2)->setRowHeight(28);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(12);
                $sheet->getRowDimension(5)->setRowHeight(12);
                $sheet->getRowDimension(6)->setRowHeight(30);

                $highestRow = max(6, $sheet->getHighestRow());

                if ($highestRow > 6) {
                    for ($i = 7; $i <= $highestRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(22);
                    }
                }

                // Encabezado membrete
                $sheet->getStyle('A1:P5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEDEDED'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:P1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                ]);

                $sheet->getStyle('A2:P2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
                ]);

                $sheet->getStyle('A3:P3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                ]);

                // Encabezado tabla
                $sheet->getStyle('A6:P6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF7FD3F4'],
                    ],
                ]);

                // Filas de datos
                if ($highestRow > 6) {
                    $sheet->getStyle("A7:P{$highestRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Bordes tabla completa
                $sheet->getStyle("A6:P{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Escudo izquierda
                if (file_exists(public_path('images/escudo-argentina.png'))) {
                    $escudo = new Drawing();
                    $escudo->setName('Escudo Argentina');
                    $escudo->setPath(public_path('images/escudo-argentina.png'));
                    $escudo->setHeight(75);
                    $escudo->setCoordinates('A1');
                    $escudo->setOffsetX(35);
                    $escudo->setOffsetY(8);
                    $escudo->setWorksheet($sheet);
                }

                // Logo escuela derecha
                if (file_exists(public_path('images/Olga aredez.png'))) {
                    $logo = new Drawing();
                    $logo->setName('Logo Escuela');
                    $logo->setPath(public_path('images/Olga aredez.png'));
                    $logo->setHeight(75);
                    $logo->setCoordinates('O1');
                    $logo->setOffsetX(20);
                    $logo->setOffsetY(8);
                    $logo->setWorksheet($sheet);
                }
            },
        ];
    }
}
