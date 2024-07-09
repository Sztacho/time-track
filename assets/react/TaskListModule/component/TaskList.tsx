import React from 'react';
import Task from './Task';

interface ITask {
    id: number;
    title: string;
    description: string;
    status: string;
    timeSpent: number;
}

interface TaskListProps {
    tasks: ITask[];
    onEdit: (task: ITask) => void;
    onDelete: (id: number) => void;
}

const formatTimeSpent = (seconds: number) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    return `${hours}h ${minutes}m`;
};

const TaskList: React.FC<TaskListProps> = ({ tasks, onEdit, onDelete }) => {
    return (
        <div className="overflow-x-auto">
            <table className="table w-full">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Time Spent</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                {tasks.map(task => (
                    <Task key={task.id} task={task} onEdit={onEdit} onDelete={onDelete} />
                ))}
                </tbody>
            </table>
        </div>
    );
};

export default TaskList;
