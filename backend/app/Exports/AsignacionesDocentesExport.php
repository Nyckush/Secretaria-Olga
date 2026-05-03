<?php

namespace App\Exports;

use App\Models\AsignacionDocente;
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

class AsignacionesDocentesExport implements FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    protected array $rowsConBaja = [];

    public function query()
    {
        return AsignacionDocente::query()
            ->with([
                'docente',
                'bajas',
                'cursoEtapaMateria.cursoEtapa.curso',
                'cursoEtapaMateria.cursoEtapa.etapa',
                'cursoEtapaMateria.cursoMateria.materia',
                'cursoEtapaMateria.cursoMateria.curso.anexo',
            ]);
    }

    public function headings(): array
    {
        return [
            'Nº',
            'Apellido y Nombre',
            'DNI',
            'CUIL',
            'Sit. Rev.',
            'Materia',
            "Hs \nCatedra",
            'Curso',
            'Div.',
            'Turno',
            'Nº CUPOF',
            'Período',
            'Anexo',
            'Desde',
            'Hasta',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 35,
            'C' => 12,
            'D' => 14,
            'E' => 10,
            'F' => 35,
            'O' => 12,
            'N' => 20,
            'O' => 20,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();

                // Reserva filas superiores para el membrete; deja encabezado de tabla en fila 6.
                $sheet->insertNewRowBefore(1, 5);

                $sheet->setCellValue('A1', 'COLEGIO SECUNDARIO N°59 "OLGA M. DE AREDEZ" MODALIDAD DE JOVENES Y ADULTOS');
                $sheet->setCellValue('A2', 'Alvear N° 1145 - 4600 San Salvador de Jujuy - JUJUY');
                $sheet->mergeCells('A1:O1');
                $sheet->mergeCells('A2:O2');

                $sheet->getRowDimension(1)->setRowHeight(44);
                $sheet->getRowDimension(2)->setRowHeight(34);
                $sheet->getRowDimension(3)->setRowHeight(16);
                $sheet->getRowDimension(4)->setRowHeight(12);
                $sheet->getRowDimension(5)->setRowHeight(12);
                $sheet->getRowDimension(6)->setRowHeight(34);

                $highestRow = max(6, $sheet->getHighestRow());

                if ($highestRow > 6) {
                    for ($i = 7; $i <= $highestRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(25);
                    }
                }

                $sheet->getStyle('A1:O5')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEDEDED'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('A1:O1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                $sheet->getStyle('A2:O2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                    ],
                ]);

                $sheet->getStyle('A6:O6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF7FD3F4'],
                    ],
                ]);

                $sheet->getStyle('H6')->getAlignment()->setWrapText(true);

                if ($highestRow > 6) {
                    $sheet->getStyle("A7:O{$highestRow}")->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->getStyle("A6:O{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                foreach ($this->rowsConBaja as $excelRow) {
                    $sheet->getStyle("A{$excelRow}:O{$excelRow}")->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['argb' => 'FFFFF59D'],
                        ],
                    ]);
                }

                            // --- Ajuste del Escudo (Izquierda) ---
                $escudoArgentina = new Drawing();
                $escudoArgentina->setName('Escudo Argentina');
                $escudoArgentina->setPath(public_path('images/escudo-argentina.png'));
                $escudoArgentina->setHeight(75); // Ligeramente más pequeño para que no invada el texto
                $escudoArgentina->setCoordinates('A1');
                $escudoArgentina->setOffsetX(35); // Aumenta este valor para moverlo hacia la derecha (acercarlo al texto)
                $escudoArgentina->setOffsetY(8);  // Ajusta verticalmente para centrarlo respecto a las filas 1 y 2
                $escudoArgentina->setWorksheet($sheet);

                // --- Ajuste del Logo (Derecha) ---
                $logoEscuela = new Drawing();
                $logoEscuela->setName('Logo Escuela');
                $logoEscuela->setPath(public_path('images/Olga aredez.png'));
                $logoEscuela->setHeight(75); 
                $logoEscuela->setCoordinates('O1'); // Si quieres que esté más a la izquierda, cámbialo a 'N1'
                $logoEscuela->setOffsetX(20); // Ajusta para moverlo hacia la izquierda (acercarlo al texto)
                $logoEscuela->setOffsetY(8);
                $logoEscuela->setWorksheet($sheet);
                            },
        ];
    }


    

  

    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        if ($row->bajas->isNotEmpty()) {
            $this->rowsConBaja[] = 6 + $rowNumber;
        }

        $docente = $row->docente;
        $cursoEtapaMateria = $row->cursoEtapaMateria;
        $cursoEtapa = $cursoEtapaMateria?->cursoEtapa;
        $curso = $cursoEtapa?->curso;
        $cursoMateria = $cursoEtapaMateria?->cursoMateria;
        $materia = $cursoMateria?->materia;
        $anexo = $curso?->anexo;

        return [
            $rowNumber,
            trim(($docente?->apellido ?? '') . ' ' . ($docente?->nombre ?? '')),
            $docente?->dni ?? '',
            $docente?->cuil ?? '',
            $row->situacion_revista ?? '',
            $materia?->nombre ?? '',
            $cursoEtapaMateria?->horas_catedra ?? '',
            $curso?->nombre ?? '',
            $curso?->division ?? '',
            $curso?->turno ?? '',
            $cursoMateria?->nro_cupof ?? '',
            $cursoMateria?->periodo ?? '',
            $anexo?->nombre ?? '',
            $row->fecha_desde?->format('d/m/Y') ?? '',
            $row->hasta ?? '',
        ];
    }
}
