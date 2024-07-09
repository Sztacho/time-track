import React, { useState } from 'react';
import { Task } from './TimeDashboard';

interface LogWorkModalProps {
    isOpen: boolean;
    onRequestClose: (description: string, startTime: Date, endTime: Date) => void;
    onCancel: () => void;
    task: Task;
    startTime: Date;
    endTime: Date;
}

const LogWorkModal: React.FC<LogWorkModalProps> = ({ isOpen, onRequestClose, onCancel, task, startTime, endTime }) => {
    const [description, setDescription] = useState<string>('');
    const [start, setStart] = useState<Date>(startTime);
    const [end, setEnd] = useState<Date>(endTime);

    const handleSave = () => {
        onRequestClose(description, start, end);
    };

    return (
        <div className={`modal ${isOpen ? 'modal-open' : ''}`}>
            <div className="modal-box">
                <h2 className="text-2xl font-semibold mb-4">Log Work</h2>
                <div className="mb-4">
                    <label className="block text-gray-700">Task</label>
                    <input type="text" value={task.title} disabled className="input input-bordered w-full" />
                </div>
                <div className="mb-4">
                    <label className="block text-gray-700">Description</label>
                    <textarea value={description} onChange={(e) => setDescription(e.target.value)} className="textarea textarea-bordered w-full" />
                </div>
                <div className="mb-4">
                    <label className="block text-gray-700">Start Time</label>
                    <input type="datetime-local" value={start.toISOString().substring(0, 16)} onChange={(e) => setStart(new Date(e.target.value))} className="input input-bordered w-full" />
                </div>
                <div className="mb-4">
                    <label className="block text-gray-700">End Time</label>
                    <input type="datetime-local" value={end.toISOString().substring(0, 16)} onChange={(e) => setEnd(new Date(e.target.value))} className="input input-bordered w-full" />
                </div>
                <div className="modal-action">
                    <button className="btn btn-secondary" onClick={onCancel}>Cancel</button>
                    <button className="btn btn-primary" onClick={handleSave}>Save</button>
                </div>
            </div>
        </div>
    );
};

export default LogWorkModal;
