import React from 'react';

interface TimerProps {
    elapsedTime: number; // Time in seconds
}

const formatTime = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
};

const Timer: React.FC<TimerProps> = ({ elapsedTime }) => {
    return (
        <div className="text-3xl font-mono text-gray-800">
            {formatTime(elapsedTime)}
        </div>
    );
};

export default Timer;
