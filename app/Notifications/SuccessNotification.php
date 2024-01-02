<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Log;
use App\Models\User;
use Illuminate\Support\Str;
use NotificationChannels\Telegram\TelegramBase;
use NotificationChannels\Telegram\TelegramMessage;

class SuccessNotification extends TelegramNotification
{
    public function __construct(private Log $foodLog)
    {
    }

    public function toTelegram(User $notifiable): TelegramBase
    {

        $message = TelegramMessage::create();
        $message->to($notifiable->telegram_user_id);

        $content = <<<'EOT'
*🧠⚡️: Data stored successfully*

*Sleep Quality:* %s
*Wake Up State:* %s
*Symptoms:* %s
*Mood:* %s

**Food Nutrition Log:**

```
| Food    | Cals | Sugar | Prot | Fat | Carbs | Glute | Lacto | Allergens |
| ------- | ---- | ----- | ---- | --- | ----- | ----- | ----- | --------- |
%s

```

EOT;

        $tableItems = $this->foodLog->food->map(function ($food) {
            return sprintf(
                '| %s | %s | %s | %s | %s | %s | %s | %s | %s |',
                $this->tableCell($food->name, 7),
                $this->tableCell($food->calories, 4),
                $this->tableCell($food->sugar, 5),
                $this->tableCell($food->protein, 4),
                $this->tableCell($food->fat, 3),
                $this->tableCell($food->carbohydrates, 5),
                $this->tableCell($food->gluten_level, 5),
                $this->tableCell($food->lactose_level, 5),
                $this->tableCell(collect($food->common_allergens)->join(','), 9),
            );
        });

        $content = sprintf($content,
            collect($this->foodLog->sleep_quality)->pluck('value')->join(', '),
            collect($this->foodLog->wake_up_state)->pluck('value')->join(', '),
            collect($this->foodLog->symptoms)->pluck('value')->join(', '),
            collect($this->foodLog->mood)->pluck('value')->join(', '),
            $tableItems->implode("\n")
        );

        // Escape - with \ to avoid markdown formatting

        $content = str_replace('-', '\-', $content);

        return $message
            ->options(['parse_mode' => 'MarkdownV2'])
            ->content($content);
    }

    private function tableCell($value, int $length): string
    {
        return Str::of($value)->limit($length - 1, '…')->padRight($length)->__toString();
    }
}
