import React, { useState, useEffect } from 'react';

interface TaskFormProps {
    addTask: (task: { title: string; description: string; status: string }) => void;
    updateTask: (task: { id: number; title: string; description: string; status: string }) => void;
    editingTask: { id: number; title: string; description: string; status: string } | null;
}

const TaskForm: React.FC<TaskFormProps> = ({ addTask, updateTask, editingTask }) => {
    const [title, setTitle] = useState('');
    const [description, setDescription] = useState('');
    const [status, setStatus] = useState('open');

    useEffect(() => {
        if (editingTask) {
            setTitle(editingTask.title);
            setDescription(editingTask.description);
            setStatus(editingTask.status);
        }
    }, [editingTask]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editingTask) {
            updateTask({ id: editingTask.id, title, description, status });
        } else {
            addTask({ title, description, status });
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-4">
            <div>
                <label className="block text-sm font-medium text-gray-700">Title</label>
                <input
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    className="input input-bordered w-full"
                />
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700">Description</label>
                <textarea
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    className="textarea textarea-bordered w-full"
                ></textarea>
            </div>
            <div>
                <label className="block text-sm font-medium text-gray-700">Status</label>
                <select
                    value={status}
                    onChange={(e) => setStatus(e.target.value)}
                    className="select select-bordered w-full"
                >
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="done">Done</option>
                </select>
            </div>
            <button type="submit" className="btn btn-sm btn-primary w-full">{editingTask ? 'Update Task' : 'Add Task'}</button>
        </form>
    );
};

export default TaskForm;
