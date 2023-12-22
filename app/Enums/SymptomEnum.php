<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum SymptomEnum: string
{
    case HEADACHE = 'headache';
    case IRRITABILITY = 'irritability';
    case FATIGUE = 'fatigue';
    case BACK_PAIN = 'back-pain';
    case ANXIETY = 'anxiety';
    case ACID_REFLUX = 'acid-reflux';
    case INSOMNIA = 'insomnia';
    case JOINT_PAIN = 'joint-pain';
    case DEPRESSION = 'depression';
    case ALLERGIES = 'allergies';
    case ABDOMINAL_PAIN = 'abdominal-pain';
    case LOW_APPETITE = 'low-appetite';
    case HIGH_APPETITE = 'high-appetite';
    case RASHES = 'rashes';
    case CONSTIPATION = 'constipation';
    case DIARRHEA = 'diarrhea';
    case NAUSEA = 'nausea';
    case DIZZINESS = 'dizziness';
    case WEIGHT_LOSS = 'weight-loss';
    case WEIGHT_GAIN = 'weight-gain';
    case CHEST_PAIN = 'chest-pain';
    case CONCENTRATION_DIFFICULTY = 'concentration-difficulty';
    case DRYNESS = 'dryness';
    case SWELLING = 'swelling';
    case VISION_CHANGES = 'vision-changes';
    case BREATH_SHORTNESS = 'breath-shortness';
    case BOWEL_CHANGES = 'bowel-changes';
    case TEMPERATURE_SENSITIVITY = 'temperature-sensitivity';
    case EXCESSIVE_SWEATING = 'excessive-sweating';
    case TINNITUS = 'tinnitus';
    case MEMORY_PROBLEMS = 'memory-problems';
    case TINGLING = 'tingling';
    case LIMB_PAIN = 'limb-pain';
    case SEXUAL_DYSFUNCTION = 'sexual-dysfunction';
    case FOOD_CRAVINGS = 'food-cravings';
    case BAD_BREATH = 'bad-breath';
    case MOOD_CHANGES = 'mood-changes';
    case SNORING = 'snoring';
    case NIGHTMARES = 'nightmares';
    case LIGHT_SENSITIVITY = 'light-sensitivity';
    case SOUND_SENSITIVITY = 'sound-sensitivity';

    public static function collect(): Collection
    {

        return collect(self::cases())->map(fn ($case) => $case->value);

    }
}
