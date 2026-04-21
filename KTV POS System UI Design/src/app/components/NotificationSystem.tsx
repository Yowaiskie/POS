import { Bell, X } from 'lucide-react';
import { useEffect, useState } from 'react';

export interface Notification {
  id: string;
  message: string;
  type: 'info' | 'warning' | 'danger';
  timestamp: Date;
}

interface NotificationSystemProps {
  notifications: Notification[];
  onDismiss: (id: string) => void;
}

export function NotificationSystem({ notifications, onDismiss }: NotificationSystemProps) {
  const typeColors = {
    info: 'bg-blue-500',
    warning: 'bg-amber-500',
    danger: 'bg-red-500',
  };

  useEffect(() => {
    const timers = notifications.map(notif => {
      return setTimeout(() => {
        onDismiss(notif.id);
      }, 5000);
    });

    return () => {
      timers.forEach(timer => clearTimeout(timer));
    };
  }, [notifications, onDismiss]);

  return (
    <div className="fixed bottom-24 lg:bottom-4 right-4 z-50 w-80 max-w-[calc(100vw-2rem)]">
      <div className="space-y-2">
        {notifications.map(notif => (
          <div
            key={notif.id}
            className={`${typeColors[notif.type]} text-white p-4 rounded-lg shadow-lg animate-[slideIn_0.3s_ease-out] flex items-start justify-between gap-3`}>
            <div className="flex-1">
              <p className="text-sm font-medium">{notif.message}</p>
            </div>
            <button
              onClick={() => onDismiss(notif.id)}
              className="p-1 hover:bg-white/20 rounded transition-colors active:scale-95 flex-shrink-0">
              <X className="w-4 h-4" />
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}
