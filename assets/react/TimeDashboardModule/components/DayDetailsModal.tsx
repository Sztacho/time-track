import React, { useEffect, useState } from 'react';
import { Task, TimeEntry } from './TimeDashboard';

interface DayDetailsModalProps {
    isOpen: boolean;
    onRequestClose: () => void;
    task: Task;
    date: Date;
}

const DayDetailsModal: React.FC<DayDetailsModalProps> = ({ isOpen, onRequestClose, task, date }) => {
    const [timeEntries, setTimeEntries] = useState<TimeEntry[]>([]);

    useEffect(() => {
        fetchTimeEntries();
    }, [task, date]);

    const fetchTimeEntries = async () => {
        const response = await fetch(`/api/time-entries?task_id=${task.id}&date=${date.toISOString()}`);
        const data = await response.json();
        setTimeEntries(data);
    };

    return (
        <div className={`modal ${isOpen ? 'modal-open' : ''}`}>
            <div className="modal-box">
                <h2 className="text-2xl font-semibold mb-4">Day Details</h2>
                <div className="mb-4">
                    <label className="block text-gray-700">Task</label>
                    <input type="text" value={task.title} disabled className="input input-bordered w-full" />
                </div>
                <div className="mb-4">
                    <label className="block text-gray-700">Date</label>
                    <input type="text" value={date.toDateString()} disabled className="input input-bordered w-full" />
                </div>
                <div className="mb-4">
                    <ul>
                        {timeEntries.map(entry => (
                            <li key={entry.id} className="flex justify-between">
                                <span>{entry.description}</span>
                                <span>{new Date(entry.duration * 1000).toISOString().substr(11, 8)}</span>
                            </li>
                        ))}
                    </ul>
                </div>
                <div className="modal-action">
                    <button className="btn btn-secondary" onClick={onRequestClose}>Close</button>
                </div>
            </div>
        </div>
    );
};

export default DayDetailsModal;
