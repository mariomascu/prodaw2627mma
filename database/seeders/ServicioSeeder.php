<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Servicio;

class ServicioSeeder extends Seeder
{
    public function run(): void
    {
        $servicios = [
            // Cursos
            ['tipo' => 'cursos', 'titulo' => 'Curso Intensivo Online', 'descripcion' => 'Formación intensiva online para iniciarte en el mundo del masaje terapéutico y la terapia deportiva. Material didáctico completo y tutorías personalizadas.', 'imagen' => 'curso_intensivo_online', 'duracion' => '8 h', 'precio' => '180 €', 'orden' => 1],
            ['tipo' => 'cursos', 'titulo' => 'Taller Masaje Thai en suelo', 'descripcion' => 'Aprende las técnicas fundamentales del masaje Thai tradicional practicado en suelo. Incluye material y certificado de asistencia.', 'imagen' => 'curso_taller_holistico_masaje', 'duracion' => '120 min', 'precio' => '34 €', 'orden' => 2],
            ['tipo' => 'cursos', 'titulo' => 'Taller Ayuno y dieta', 'descripcion' => 'Taller práctico sobre ayuno intermitente y nutrición saludable para optimizar tu rendimiento físico y bienestar general.', 'imagen' => 'curso_taller_holistico_ayuno', 'duracion' => '90 min', 'precio' => '29 €', 'orden' => 3],
            ['tipo' => 'cursos', 'titulo' => 'Curso Terapia Thai Fusión', 'descripcion' => 'Formación profesional completa en Terapia Thai Fusión. Combina técnicas orientales y occidentales para un tratamiento integral.', 'imagen' => 'curso_terapia_fusion', 'duracion' => '30 h', 'precio' => '840 €', 'orden' => 4],
            ['tipo' => 'cursos', 'titulo' => 'Mentoría personalizada', 'descripcion' => 'Sesiones de mentoría individual adaptadas a tus necesidades y objetivos profesionales. Aprende a tu ritmo con guía experta.', 'imagen' => 'curso_mentoria_personalizada', 'duracion' => '10 h', 'precio' => '299 €', 'orden' => 5],

            // Masajes
            ['tipo' => 'masajes', 'titulo' => 'Thai Fusión', 'descripcion' => 'Masaje Thai Fusión completo que combina presiones, estiramientos y técnicas miofasciales para una relajación profunda y recuperación muscular.', 'imagen' => 'masaje_thai_fusion', 'duracion' => '60 min', 'precio' => '90 €', 'orden' => 1],
            ['tipo' => 'masajes', 'titulo' => 'Natural energético de espalda', 'descripcion' => 'Masaje energético centrado en espalda, cuello y hombros. Elimina tensiones acumuladas y restaura el flujo energético natural del cuerpo.', 'imagen' => 'masaje_energetico_espalda', 'duracion' => '40 min', 'precio' => '50 €', 'orden' => 2],
            ['tipo' => 'masajes', 'titulo' => 'Piernas y pies', 'descripcion' => 'Tratamiento relajante y revitalizante centrado en piernas y pies. Ideal para aliviar la pesadez, mejorar la circulación y reducir el estrés.', 'imagen' => 'masaje_piernas_pies_relax', 'duracion' => '40 min', 'precio' => '50 €', 'orden' => 3],
            ['tipo' => 'masajes', 'titulo' => 'Combo Thai Fusión', 'descripcion' => 'Sesión completa que combina masaje Thai Fusión con trabajo de espalda y extremidades. La experiencia más completa para tu bienestar total.', 'imagen' => 'masaje_thai_fusion_cuerpo_completo', 'duracion' => '95 min', 'precio' => '120 €', 'orden' => 4],
            ['tipo' => 'masajes', 'titulo' => 'Masaje Linfático', 'descripcion' => 'Drenaje linfático manual para eliminar toxinas, reducir la retención de líquidos y estimular el sistema inmunológico.', 'imagen' => 'masaje_linfatico', 'duracion' => '60 min', 'precio' => '60 €', 'orden' => 5],
            ['tipo' => 'masajes', 'titulo' => 'Experiencia pasaje terapéutico', 'descripcion' => 'La experiencia definitiva: sesión completa de terapia manual integrando múltiples técnicas para una transformación profunda del cuerpo y la mente.', 'imagen' => 'masaje_pasaje_terapeutico', 'duracion' => '120 min', 'precio' => '200 €', 'orden' => 6],
            ['tipo' => 'masajes', 'titulo' => 'Tailandés tradicional en suelo', 'descripcion' => 'Masaje Thai tradicional practicado sobre tatami, con ropa cómoda. Incluye estiramientos profundos y presiones en líneas energéticas.', 'imagen' => 'masaje_tradicional_suelo', 'duracion' => '60 min', 'precio' => '90 €', 'orden' => 7],
            ['tipo' => 'masajes', 'titulo' => 'Acupuntura', 'descripcion' => 'Sesión de acupuntura para equilibrar el flujo energético del organismo. Tratamiento eficaz para el dolor, el estrés y múltiples afecciones.', 'imagen' => 'masaje_acupuntura', 'duracion' => '60 min', 'precio' => '60 €', 'orden' => 8],

            // Yoga
            ['tipo' => 'yoga', 'titulo' => 'Pack On', 'descripcion' => 'Iniciación perfecta al yoga terapéutico. 8 sesiones al mes para establecer una práctica regular y descubrir los beneficios del yoga en tu vida diaria.', 'imagen' => 'yoga_pack_on', 'duracion' => '8 sesiones/mes', 'precio' => '160 €/mes', 'orden' => 1],
            ['tipo' => 'yoga', 'titulo' => 'Pack Full', 'descripcion' => 'El equilibrio perfecto para una práctica constante. 12 sesiones mensuales que te permitirán progresar notablemente en tu práctica de yoga.', 'imagen' => 'yoga_pack_full', 'duracion' => '12 sesiones/mes', 'precio' => '219 €/mes', 'orden' => 2],
            ['tipo' => 'yoga', 'titulo' => 'Pack Everyday', 'descripcion' => 'Para los más comprometidos con su bienestar. 16 sesiones al mes para transformar el yoga en un hábito diario y experimentar cambios profundos.', 'imagen' => 'yoga_pack_everyday', 'duracion' => '16 sesiones/mes', 'precio' => '279 €/mes', 'orden' => 3],
            ['tipo' => 'yoga', 'titulo' => 'Bono Individual', 'descripcion' => 'Flexibilidad total con 4 sesiones individuales para usar a tu ritmo. Ideal si tu agenda no permite una práctica fija mensual.', 'imagen' => 'yoga_bono_individual', 'duracion' => '4 sesiones', 'precio' => '200 €', 'orden' => 4],
            ['tipo' => 'yoga', 'titulo' => 'Bono Dúo', 'descripcion' => 'Comparte la experiencia del yoga con tu pareja, amigo o familiar. 4 sesiones para dos personas con instrucción personalizada.', 'imagen' => 'yoga_bono_duo', 'duracion' => '4 sesiones en pareja', 'precio' => '300 €', 'orden' => 5],
        ];

        foreach ($servicios as $s) {
            Servicio::create($s);
        }
    }
}
