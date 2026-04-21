import { useEffect, useState } from 'react';

interface TimerProps {
  endTime: Date;
  size?: 'sm' | 'lg';
}

export function Timer({ endTime, size = 'lg' }: TimerProps) {
  const [timeDiff, setTimeDiff] = useState<number>(0);

  useEffect(() => {
    const calculateTimeDiff = () => {
      const now = new Date().getTime();
      const end = endTime.getTime();
      const diff = end - now;
      setTimeDiff(diff);
    };

    calculateTimeDiff();
    const interval = setInterval(calculateTimeDiff, 1000);

    return () => clearInterval(interval);
  }, [endTime]);

  const isOvertime = timeDiff < 0;
  const absoluteTime = Math.abs(timeDiff);

  const hours = Math.floor(absoluteTime / (1000 * 60 * 60));
  const minutes = Math.floor((absoluteTime % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((absoluteTime % (1000 * 60)) / 1000);

  const isWarning = !isOvertime && timeDiff <= 10 * 60 * 1000;

  const sizeClasses = size === 'lg'
    ? 'text-5xl md:text-6xl'
    : 'text-xl md:text-2xl';

  const colorClasses = isOvertime
    ? 'text-[--status-danger]'
    : isWarning
    ? 'text-[--status-warning]'
    : 'text-[--status-active]';

  return (
    <div className="flex flex-col items-center">
      <div className={`font-mono ${sizeClasses} ${colorClasses} tracking-wider font-bold`}
           style={{
             textShadow: isOvertime
               ? '0 0 20px var(--status-danger)'
               : isWarning
               ? '0 0 15px var(--status-warning)'
               : '0 0 10px var(--status-active)'
           }}>
        {isOvertime && <span className="text-3xl mr-2">+</span>}
        {String(hours).padStart(2, '0')}:{String(minutes).padStart(2, '0')}:{String(seconds).padStart(2, '0')}
      </div>
      {isOvertime && (
        <div className="text-sm font-semibold text-[--status-danger] mt-2 animate-pulse">
          OVERTIME
        </div>
      )}
    </div>
  );
}
