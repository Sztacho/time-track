import React from 'react';

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    timeSpent: number; // Time spent in seconds
}

interface TaskProps {
    task: Task;
    onEdit: (task: Task) => void;
    onDelete: (id: number) => void;
}

const formatTimeSpent = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours}h ${minutes}m`;
};

const Task: React.FC<TaskProps> = ({ task, onEdit, onDelete }) => {
    return (
        <tr>
            <td>{task.title}</td>
            <td>{task.description}</td>
            <td>{task.status}</td>
            <td>{formatTimeSpent(task.timeSpent)}</td>
            <td>
                <button className="btn btn-sm w-full md:w-auto btn-secondary mr-2" onClick={() => onEdit(task)}>Edit</button>
                <button className="btn btn-sm w-full md:w-auto btn-error" onClick={() => onDelete(task.id)}>Delete</button>
            </td>
        </tr>
    );
};

export default Task;
