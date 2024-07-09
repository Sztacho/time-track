import React from 'react';
import Timer from './Timer';

interface TimerCardProps {
    task: { title: string };
    elapsedTime: number;
    isRunning: boolean;
    onToggle: () => void;
    onStop: () => void;
}

const TimerCard: React.FC<TimerCardProps> = ({ task, elapsedTime, isRunning, onToggle, onStop }) => {
    return (
        <div className="card bg-white shadow-xl transform hover:scale-105 transition-transform duration-200 ease-in-out">
            <div className="card-body flex flex-col items-center">
                <h3 className="card-title text-xl font-semibold text-center">{task.title}</h3>
                <Timer elapsedTime={elapsedTime} />
                <div className="flex space-x-2 mt-4">
                    <button className={`btn ${isRunning ? 'btn-secondary' : 'btn-primary'} btn-sm`} onClick={onToggle}>
                        {isRunning ? 'Pause' : 'Resume'}
                    </button>
                    <button className="btn btn-error btn-sm" onClick={onStop}>
                        Stop
                    </button>
                </div>
            </div>
        </div>
    );
};

export default TimerCard;
