<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;
    public string $type; // 'order', 'stock', 'invoice', 'user', 'system'
    public ?string $actionUrl;
    public ?string $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        string|array $title,
        string $message = '',
        string $type = 'system',
        ?string $actionUrl = null,
        ?string $icon = null
    ) {
        if (is_array($title)) {
            $data = $title;
            $this->title = $data['title'] ?? 'System Notification';
            $this->message = $data['message'] ?? '';
            $this->type = $data['type'] ?? 'system';
            $this->actionUrl = $data['action_url'] ?? $data['actionUrl'] ?? null;
            $this->icon = $data['icon'] ?? $this->resolveDefaultIcon($this->type);
        } else {
            $this->title = $title;
            $this->message = $message;
            $this->type = $type;
            $this->actionUrl = $actionUrl;
            $this->icon = $icon ?? $this->resolveDefaultIcon($type);
        }
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = $this->actionUrl;
        if ($url && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
            $parsed = parse_url($url);
            $url = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '') . (isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '');
        }

        return [
            'title'      => $this->title,
            'message'    => $this->message,
            'type'       => $this->type,
            'action_url' => $url,
            'icon'       => $this->icon,
        ];
    }

    /**
     * Resolve default FontAwesome icon class by type.
     */
    protected function resolveDefaultIcon(string $type): string
    {
        return match ($type) {
            'order'   => 'fa-solid fa-cart-shopping text-blue-500',
            'stock'   => 'fa-solid fa-triangle-exclamation text-amber-500',
            'invoice' => 'fa-solid fa-file-invoice-dollar text-emerald-500',
            'user'    => 'fa-solid fa-user-plus text-purple-500',
            default   => 'fa-solid fa-bell text-sky-500',
        };
    }
}
