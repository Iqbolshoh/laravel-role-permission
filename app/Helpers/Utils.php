<?php

use Filament\Notifications\Notification;

if (!function_exists('notify')) {
    /*
    |---------------------------------------------------------------------- 
    | Notification Helper
    |---------------------------------------------------------------------- 
    | Sends notifications to the user interface with customizable options.
    */
    function notify(
        string $title,
        string $message,
        string $type = 'success',
        string $icon = 'heroicon-o-check-circle',
        int $duration = 3000,
        bool $dismissible = true,
        string $iconPosition = 'left',
        bool $isProgress = false,
        string $backgroundColor = '',
        string $color = ''
    ) {
        $notification = Notification::make()
                    ->title($title)
                    ->body($message)
                    ->icon($icon)
            ->$type()
                ->duration($duration)
                ->dismissible($dismissible)
                ->iconPosition($iconPosition)
                ->isProgress($isProgress);

        if (!empty($backgroundColor)) {
            $notification->backgroundColor($backgroundColor);
        }

        if (!empty($color)) {
            $notification->color($color);
        }

        $notification->send();
    }
}
