<?php

namespace Database\Seeders;

use App\Models\Servicio;
use App\Models\ServicioTraduccion;
use Illuminate\Database\Seeder;

/**
 * Traducciones (inglés, alemán y portugués de Brasil) de los servicios iniciales.
 * Busca cada servicio por su título en español y solo crea las traducciones que falten,
 * así no pisa lo que el administrador haya cambiado desde el panel.
 * Duración y precio solo se rellenan cuando cambian con el idioma; si quedan vacíos se muestra el español.
 *
 *   php artisan db:seed --class=ServicioTraduccionSeeder
 */
class ServicioTraduccionSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->traducciones() as $tituloEs => $idiomas) {
            $servicio = Servicio::where('titulo', $tituloEs)->first();
            if (! $servicio) {
                continue;
            }

            foreach ($idiomas as $idioma => [$titulo, $descripcion, $duracion, $precio]) {
                ServicioTraduccion::firstOrCreate(
                    ['servicio_id' => $servicio->id, 'idioma' => $idioma],
                    compact('titulo', 'descripcion', 'duracion', 'precio')
                );
            }
        }
    }

    /** [título en español => [idioma => [título, descripción, duración, precio]]] */
    private function traducciones(): array
    {
        return [
            // ──────── CURSOS ────────
            'Curso Intensivo Online' => [
                'en' => ['Intensive Online Course', 'Intensive online training to get started in therapeutic massage and sports therapy. Complete course materials and personal tutoring.', null, null],
                'de' => ['Online-Intensivkurs', 'Intensive Online-Ausbildung für den Einstieg in therapeutische Massage und Sporttherapie. Komplettes Kursmaterial und persönliche Betreuung.', '8 Std.', null],
                'pt_BR' => ['Curso Intensivo Online', 'Formação intensiva online para começar no mundo da massagem terapêutica e da terapia esportiva. Material didático completo e tutorias personalizadas.', null, null],
            ],
            'Taller Masaje Thai en suelo' => [
                'en' => ['Floor Thai Massage Workshop', 'Learn the fundamental techniques of traditional Thai massage performed on the floor. Materials and certificate of attendance included.', null, null],
                'de' => ['Workshop Thai-Massage am Boden', 'Lerne die grundlegenden Techniken der traditionellen Thai-Massage am Boden. Inklusive Material und Teilnahmebescheinigung.', '120 Min.', null],
                'pt_BR' => ['Oficina de Massagem Thai no chão', 'Aprenda as técnicas fundamentais da massagem tailandesa tradicional feita no chão. Inclui material e certificado de participação.', null, null],
            ],
            'Taller Ayuno y dieta' => [
                'en' => ['Fasting and Diet Workshop', 'Practical workshop on intermittent fasting and healthy eating to boost your physical performance and overall wellbeing.', null, null],
                'de' => ['Workshop Fasten und Ernährung', 'Praxis-Workshop zu Intervallfasten und gesunder Ernährung für mehr Leistungsfähigkeit und Wohlbefinden.', '90 Min.', null],
                'pt_BR' => ['Oficina de Jejum e Dieta', 'Oficina prática sobre jejum intermitente e alimentação saudável para melhorar seu desempenho físico e seu bem-estar geral.', null, null],
            ],
            'Curso Terapia Thai Fusión' => [
                'en' => ['Thai Fusion Therapy Course', 'Complete professional training in Thai Fusion Therapy. It combines Eastern and Western techniques for a holistic treatment.', null, null],
                'de' => ['Ausbildung Thai-Fusion-Therapie', 'Umfassende Berufsausbildung in Thai-Fusion-Therapie. Sie verbindet östliche und westliche Techniken zu einer ganzheitlichen Behandlung.', '30 Std.', null],
                'pt_BR' => ['Curso de Terapia Thai Fusão', 'Formação profissional completa em Terapia Thai Fusão. Combina técnicas orientais e ocidentais para um tratamento integral.', null, null],
            ],
            'Mentoría personalizada' => [
                'en' => ['Personal Mentoring', 'One-to-one mentoring sessions tailored to your needs and professional goals. Learn at your own pace with expert guidance.', null, null],
                'de' => ['Persönliches Mentoring', 'Individuelle Mentoring-Sitzungen, abgestimmt auf deine Bedürfnisse und beruflichen Ziele. Lerne in deinem Tempo mit fachkundiger Begleitung.', '10 Std.', null],
                'pt_BR' => ['Mentoria personalizada', 'Sessões de mentoria individual adaptadas às suas necessidades e objetivos profissionais. Aprenda no seu ritmo com orientação especializada.', null, null],
            ],

            // ──────── MASAJES ────────
            'Thai Fusión' => [
                'en' => ['Thai Fusion', 'Full Thai Fusion massage combining pressure, stretching and myofascial techniques for deep relaxation and muscle recovery.', null, null],
                'de' => ['Thai-Fusion', 'Komplette Thai-Fusion-Massage mit Druck, Dehnungen und myofaszialen Techniken für tiefe Entspannung und Muskelregeneration.', '60 Min.', null],
                'pt_BR' => ['Thai Fusão', 'Massagem Thai Fusão completa que combina pressões, alongamentos e técnicas miofasciais para um relaxamento profundo e a recuperação muscular.', null, null],
            ],
            'Natural energético de espalda' => [
                'en' => ['Natural Energising Back Massage', 'Energising massage focused on the back, neck and shoulders. It releases built-up tension and restores the body’s natural energy flow.', null, null],
                'de' => ['Natürliche energetische Rückenmassage', 'Energetische Massage für Rücken, Nacken und Schultern. Sie löst angestaute Verspannungen und stellt den natürlichen Energiefluss wieder her.', '40 Min.', null],
                'pt_BR' => ['Massagem natural energética nas costas', 'Massagem energética focada nas costas, pescoço e ombros. Elimina tensões acumuladas e restaura o fluxo natural de energia do corpo.', null, null],
            ],
            'Piernas y pies' => [
                'en' => ['Legs and Feet', 'Relaxing and revitalising treatment for the legs and feet. Ideal for relieving heaviness, improving circulation and reducing stress.', null, null],
                'de' => ['Beine und Füße', 'Entspannende und belebende Behandlung für Beine und Füße. Ideal gegen schwere Beine, für eine bessere Durchblutung und weniger Stress.', '40 Min.', null],
                'pt_BR' => ['Pernas e pés', 'Tratamento relaxante e revitalizante para pernas e pés. Ideal para aliviar o peso nas pernas, melhorar a circulação e reduzir o estresse.', null, null],
            ],
            'Combo Thai Fusión' => [
                'en' => ['Thai Fusion Combo', 'A complete session combining Thai Fusion massage with work on the back and limbs. The most complete experience for your overall wellbeing.', null, null],
                'de' => ['Thai-Fusion-Kombi', 'Eine komplette Sitzung, die Thai-Fusion-Massage mit Rücken- und Gliedmaßenbehandlung verbindet. Das umfassendste Erlebnis für dein Wohlbefinden.', '95 Min.', null],
                'pt_BR' => ['Combo Thai Fusão', 'Sessão completa que combina a massagem Thai Fusão com trabalho nas costas e nos membros. A experiência mais completa para o seu bem-estar.', null, null],
            ],
            'Masaje Linfático' => [
                'en' => ['Lymphatic Massage', 'Manual lymphatic drainage to eliminate toxins, reduce fluid retention and stimulate the immune system.', null, null],
                'de' => ['Lymphdrainage', 'Manuelle Lymphdrainage, um Giftstoffe auszuleiten, Wassereinlagerungen zu verringern und das Immunsystem anzuregen.', '60 Min.', null],
                'pt_BR' => ['Drenagem linfática', 'Drenagem linfática manual para eliminar toxinas, reduzir a retenção de líquidos e estimular o sistema imunológico.', null, null],
            ],
            'Experiencia pasaje terapéutico' => [
                'en' => ['Therapeutic Journey Experience', 'The ultimate experience: a full manual therapy session combining several techniques for a deep transformation of body and mind.', null, null],
                'de' => ['Erlebnis Therapeutische Reise', 'Das ultimative Erlebnis: eine komplette manuelle Therapiesitzung, die mehrere Techniken für eine tiefe Veränderung von Körper und Geist vereint.', '120 Min.', null],
                'pt_BR' => ['Experiência Passagem Terapêutica', 'A experiência definitiva: uma sessão completa de terapia manual que integra várias técnicas para uma transformação profunda do corpo e da mente.', null, null],
            ],
            'Tailandés tradicional en suelo' => [
                'en' => ['Traditional Thai Floor Massage', 'Traditional Thai massage on a tatami mat, wearing comfortable clothes. It includes deep stretches and pressure along the energy lines.', null, null],
                'de' => ['Traditionelle Thai-Massage am Boden', 'Traditionelle Thai-Massage auf der Tatami-Matte in bequemer Kleidung. Mit tiefen Dehnungen und Druck entlang der Energielinien.', '60 Min.', null],
                'pt_BR' => ['Massagem tailandesa tradicional no chão', 'Massagem tailandesa tradicional sobre tatame, com roupa confortável. Inclui alongamentos profundos e pressões nas linhas de energia.', null, null],
            ],
            'Acupuntura' => [
                'en' => ['Acupuncture', 'Acupuncture session to balance the body’s energy flow. An effective treatment for pain, stress and many other conditions.', null, null],
                'de' => ['Akupunktur', 'Akupunktursitzung zum Ausgleich des Energieflusses im Körper. Eine wirksame Behandlung bei Schmerzen, Stress und vielen weiteren Beschwerden.', '60 Min.', null],
                'pt_BR' => ['Acupuntura', 'Sessão de acupuntura para equilibrar o fluxo de energia do organismo. Tratamento eficaz para dor, estresse e várias outras condições.', null, null],
            ],

            // ──────── YOGA ────────
            'Pack On' => [
                'en' => ['Pack On', 'The perfect introduction to therapeutic yoga. 8 sessions a month to build a regular practice and discover the benefits of yoga in your daily life.', '8 sessions/month', '160 €/month'],
                'de' => ['Pack On', 'Der perfekte Einstieg ins therapeutische Yoga. 8 Einheiten im Monat, um eine regelmäßige Praxis aufzubauen und die Vorteile von Yoga im Alltag zu entdecken.', '8 Einheiten/Monat', '160 €/Monat'],
                'pt_BR' => ['Pack On', 'A introdução perfeita ao yoga terapêutico. 8 sessões por mês para criar uma prática regular e descobrir os benefícios do yoga no seu dia a dia.', '8 sessões/mês', '160 €/mês'],
            ],
            'Pack Full' => [
                'en' => ['Pack Full', 'The perfect balance for a steady practice. 12 sessions a month that will help you make real progress in your yoga practice.', '12 sessions/month', '219 €/month'],
                'de' => ['Pack Full', 'Die perfekte Balance für eine beständige Praxis. 12 Einheiten im Monat, mit denen du in deiner Yoga-Praxis deutlich vorankommst.', '12 Einheiten/Monat', '219 €/Monat'],
                'pt_BR' => ['Pack Full', 'O equilíbrio perfeito para uma prática constante. 12 sessões por mês para você evoluir muito na sua prática de yoga.', '12 sessões/mês', '219 €/mês'],
            ],
            'Pack Everyday' => [
                'en' => ['Pack Everyday', 'For those most committed to their wellbeing. 16 sessions a month to turn yoga into a daily habit and experience deep changes.', '16 sessions/month', '279 €/month'],
                'de' => ['Pack Everyday', 'Für alle, denen ihr Wohlbefinden besonders wichtig ist. 16 Einheiten im Monat, um Yoga zur täglichen Gewohnheit zu machen und tiefe Veränderungen zu erleben.', '16 Einheiten/Monat', '279 €/Monat'],
                'pt_BR' => ['Pack Everyday', 'Para quem está mais comprometido com o próprio bem-estar. 16 sessões por mês para transformar o yoga em um hábito diário e sentir mudanças profundas.', '16 sessões/mês', '279 €/mês'],
            ],
            'Bono Individual' => [
                'en' => ['Individual Pass', 'Total flexibility with 4 individual sessions to use at your own pace. Ideal if your schedule does not allow a fixed monthly practice.', '4 sessions', null],
                'de' => ['Einzelkarte', 'Volle Flexibilität mit 4 Einzelsitzungen, die du in deinem Tempo nutzen kannst. Ideal, wenn dein Terminkalender keine feste monatliche Praxis zulässt.', '4 Einheiten', null],
                'pt_BR' => ['Pacote Individual', 'Flexibilidade total com 4 sessões individuais para usar no seu ritmo. Ideal se a sua agenda não permite uma prática mensal fixa.', '4 sessões', null],
            ],
            'Bono Dúo' => [
                'en' => ['Duo Pass', 'Share the yoga experience with your partner, a friend or a family member. 4 sessions for two people with personal instruction.', '4 sessions for two', null],
                'de' => ['Duo-Karte', 'Teile das Yoga-Erlebnis mit deinem Partner, einer Freundin oder einem Familienmitglied. 4 Einheiten für zwei Personen mit persönlicher Anleitung.', '4 Einheiten zu zweit', null],
                'pt_BR' => ['Pacote Duo', 'Compartilhe a experiência do yoga com seu parceiro, um amigo ou um familiar. 4 sessões para duas pessoas com instrução personalizada.', '4 sessões em dupla', null],
            ],
        ];
    }
}
