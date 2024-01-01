<?php

namespace App\Enums;

use App\Enums\Traits\FromStringTrait;
use Illuminate\Support\Collection;

enum SymptomEnum: string
{
    use FromStringTrait;

    case CONCENTRATION_DIFFICULTY = 'concentration-difficulty'; // Dificultad de concentración

    case HEADACHE = 'headache';                        // Dolor de cabeza
    case FATIGUE = 'fatigue';                          // Fatiga
    case BACK_PAIN = 'back-pain';                      // Dolor de espalda
    case JOINT_PAIN = 'joint-pain';                    // Dolor articular
    case ABDOMINAL_PAIN = 'abdominal-pain';            // Dolor abdominal
    case LIMB_PAIN = 'limb-pain';                      // Dolor de extremidades
    case CHEST_PAIN = 'chest-pain';                    // Dolor de pecho

    // Problemas Gastrointestinales
    case ACID_REFLUX = 'acid-reflux';                  // Reflujo ácido
    case CONSTIPATION = 'constipation';                // Estreñimiento
    case DIARRHEA = 'diarrhea';                        // Diarrea
    case NAUSEA = 'nausea';                            // Náusea
    case FLATULENCES = 'flatulences';                   // Flatulencia

    // Reacciones Cutáneas y Sensibilidad
    case RASHES = 'rashes';                            // Erupciones
    case DRYNESS = 'dryness';                          // Sequedad
    case SWELLING = 'swelling';                        // Hinchazón

    // Síntomas Relacionados con el Peso y el Apetito
    case LOW_APPETITE = 'low-appetite';                // Apetito bajo
    case HIGH_APPETITE = 'high-appetite';              // Apetito alto
    case WEIGHT_LOSS = 'weight-loss';                  // Pérdida de peso
    case WEIGHT_GAIN = 'weight-gain';                  // Ganancia de peso
    case FOOD_CRAVINGS = 'food-cravings';              // Antojos

    // Otros Síntomas Específicos
    case DIZZINESS = 'dizziness';                      // Mareos
    // case VISION_CHANGES = 'vision-changes';            // Cambios visuales
    // case BREATH_SHORTNESS = 'breath-shortness';        // Falta de aire
    // case TEMPERATURE_SENSITIVITY = 'temperature-sensitivity'; // Sensibilidad temperatura
    // case EXCESSIVE_SWEATING = 'excessive-sweating';    // Sudoración excesiva
    // case TINNITUS = 'tinnitus';                        // Tinnitus
    case MEMORY_PROBLEMS = 'memory-problems';          // Problemas de memoria
    case TINGLING = 'tingling';                        // Hormigueo
    // case SEXUAL_DYSFUNCTION = 'sexual-dysfunction';    // Disfunción sexual
    // case BAD_BREATH = 'bad-breath';                    // Mal aliento
    // case SNORING = 'snoring';                          // Ronquidos
    case ALLERGIES = 'allergies';                      // Alergías

    public static function collect(): Collection
    {
        return collect(self::cases())->map(fn ($case) => $case->value);
    }

    public function emoji(): string
    {
        return match ($this) {
            self::CONCENTRATION_DIFFICULTY => '🧠',
            self::HEADACHE => '🤕',
            self::FATIGUE => '😴',
            self::BACK_PAIN => '🔙',
            self::JOINT_PAIN => '🦴',
            self::ABDOMINAL_PAIN => '🤢',
            self::CHEST_PAIN => '💔',
            self::ACID_REFLUX => '🔥',
            self::CONSTIPATION => '💩',
            self::DIARRHEA => '💨',
            self::NAUSEA => '🤮',
            self::FLATULENCES => '💨',
            self::RASHES => '🌿',
            self::DRYNESS => '🏜️',
            self::SWELLING => '🎈',
            self::LOW_APPETITE => '🍽️',
            self::HIGH_APPETITE => '🍔',
            self::WEIGHT_LOSS => '⬇️',
            self::WEIGHT_GAIN => '⬆️',
            self::FOOD_CRAVINGS => '🍫',
            self::DIZZINESS => '💫',
            self::MEMORY_PROBLEMS => '📔',
            self::TINGLING => '✨',
            self::LIMB_PAIN => '🦾',
            self::ALLERGIES => '🤧',
            default => '❓',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::HEADACHE => 'Headache',
            self::FATIGUE => 'Fatigue',
            self::BACK_PAIN => 'Back Pain',
            self::ACID_REFLUX => 'Acid Reflux',
            self::JOINT_PAIN => 'Joint Pain',
            self::ALLERGIES => 'Allergies',
            self::ABDOMINAL_PAIN => 'Abdominal Pain',
            self::LOW_APPETITE => 'Low Appetite',
            self::HIGH_APPETITE => 'High Appetite',
            self::RASHES => 'Rashes',
            self::CONSTIPATION => 'Constipation',
            self::DIARRHEA => 'Diarrhea',
            self::NAUSEA => 'Nausea',
            self::DIZZINESS => 'Dizziness',
            self::WEIGHT_LOSS => 'Weight Loss',
            self::WEIGHT_GAIN => 'Weight Gain',
            self::CHEST_PAIN => 'Chest Pain',
            self::CONCENTRATION_DIFFICULTY => 'Concentration Difficulty',
            self::DRYNESS => 'Dryness',
            self::SWELLING => 'Swelling',
            self::MEMORY_PROBLEMS => 'Memory Problems',
            self::TINGLING => 'Tingling',
            self::LIMB_PAIN => 'Limb Pain',
            self::FOOD_CRAVINGS => 'Food Cravings',
            self::FLATULENCES => 'Flatulences',
            default => dd($this),
        };
    }
}
