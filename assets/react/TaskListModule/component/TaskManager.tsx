import React, { useState, useEffect } from 'react';
import TaskForm from "./TaskForm";
import TaskList from "./TaskList";

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
    timeSpent: number; // Time spent in seconds
}

const TaskManager: React.FC = () => {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingTask, setEditingTask] = useState<Task | null>(null);

    useEffect(() => {
        fetchTasks();
    }, []);

    const fetchTasks = async () => {
        const response = await fetch('/api/tasks');
        const data = await response.json();
        setTasks(data);
    };

    const addTask = async (task: Omit<Task, 'id'>) => {
        const response = await fetch('/api/tasks', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(task),
        });
        const newTask = await response.json();
        setTasks([...tasks, newTask]);
        setIsModalOpen(false);
    };

    const updateTask = async (task: Task) => {
        const response = await fetch(`/api/tasks/${task.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(task),
        });
        const updatedTask = await response.json();
        setTasks(tasks.map(t => (t.id === updatedTask.id ? updatedTask : t)));
        setEditingTask(null);
        setIsModalOpen(false);
    };

    const deleteTask = async (id: number) => {
        await fetch(`/api/tasks/${id}`, { method: 'DELETE' });
        setTasks(tasks.filter(task => task.id !== id));
    };

    const openEditModal = (task: Task) => {
        setEditingTask(task);
        setIsModalOpen(true);
    };

    return (
        <div className="p-4 bg-white rounded-lg shadow-md">
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-xl font-semibold">Lista zadań</h2>
                <button className="btn btn-sm btn-primary" onClick={() => setIsModalOpen(true)}>Add Task</button>
            </div>
            <TaskList tasks={tasks} onEdit={openEditModal} onDelete={deleteTask} />
            {isModalOpen && (
                <div className="modal modal-open">
                    <div className="modal-box">
                        <h3 className="font-bold text-lg">{editingTask ? 'Edit Task' : 'Add New Task'}</h3>
                        <TaskForm addTask={addTask} updateTask={updateTask} editingTask={editingTask} />
                        <div className="modal-action">
                            <button className="btn btn-sm" onClick={() => setIsModalOpen(false)}>Close</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default TaskManager;
