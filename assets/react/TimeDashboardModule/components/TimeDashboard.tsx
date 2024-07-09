import React, { useEffect, useState } from 'react';
import TaskSelector from './TaskSelector';
import LogWorkModal from './LogWorkModal';

export interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
}

export interface TimeEntry {
    id: number;
    task: Task;
    isRunning: boolean;
    elapsedTime: number;
    startTime: Date;
    duration: number;
    endTime?: Date;
    description?: string;
}

const TimeDashboard: React.FC = () => {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [selectedTask, setSelectedTask] = useState<Task | null>(null);
    const [activeTasks, setActiveTasks] = useState<TimeEntry[]>([]);
    const [modalIsOpen, setModalIsOpen] = useState<boolean>(false);
    const [currentTaskIndex, setCurrentTaskIndex] = useState<number | null>(null);

    useEffect(() => {
        fetchTasks();
        fetchActiveTimeEntries();
    }, []);

    const fetchTasks = async () => {
        const response = await fetch('/api/tasks');
        const data = await response.json();
        setTasks(data);
    };

    const fetchActiveTimeEntries = async () => {
        const response = await fetch('/api/time-entries');
        const data = await response.json();
        const activeEntries = data.map((entry: any) => {
            const elapsedTime = Math.floor((new Date().getTime() - new Date(entry.startTime * 1000).getTime()) / 1000);
            return {
                ...entry,
                isRunning: entry.endTime === null,
                elapsedTime: entry.endTime === null ? elapsedTime : entry.duration,
            };
        });
        setActiveTasks(activeEntries);
    };

    const startTracking = async () => {
        if (selectedTask) {
            const response = await fetch('/api/time-entries', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ task_id: selectedTask.id }),
            });
            const timeEntry = await response.json();
            setActiveTasks([...activeTasks, { ...timeEntry, isRunning: true, elapsedTime: 0 }]);
            setSelectedTask(null);
        }
    };

    const toggleTimer = async (index: number) => {
        const task = activeTasks[index];
        const isRunning = !task.isRunning;
        setActiveTasks(activeTasks.map((task, i) => {
            if (i === index) {
                return { ...task, isRunning };
            }
            return task;
        }));

        if (!isRunning) {
            await fetch(`/api/time-entries/${task.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ end_time: new Date().toISOString() }),
            });
        }
    };

    const stopTimer = (index: number) => {
        setCurrentTaskIndex(index);
        setModalIsOpen(true);
    };

    const handleModalClose = async (description: string, startTime: Date, endTime: Date) => {
        if (currentTaskIndex !== null) {
            const task = activeTasks[currentTaskIndex];
            await fetch(`/api/time-entries/${task.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    end_time: endTime.toISOString(),
                    description: description,
                    start_time: startTime.toISOString(),
                }),
            });

            setActiveTasks(activeTasks.filter((_, i) => i !== currentTaskIndex));
            fetchTasks(); // Odśwież listę zadań po zatrzymaniu trackingu
        }
        setModalIsOpen(false);
    };

    const handleModalCancel = () => {
        setModalIsOpen(false);
    };

    useEffect(() => {
        const timers = activeTasks.map((task, index) => {
            if (task.isRunning) {
                return setInterval(() => {
                    setActiveTasks(prevTasks => {
                        const updatedTasks = [...prevTasks];
                        if (updatedTasks[index]) {
                            updatedTasks[index].elapsedTime += 1;
                        }
                        return updatedTasks;
                    });
                }, 1000);
            }
            return null;
        });

        return () => {
            timers.forEach(timer => {
                if (timer) {
                    clearInterval(timer);
                }
            });
        };
    }, [activeTasks]);

    const formatTime = (seconds: number) => {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        return `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    };

    return (
        <div className="container mx-auto mt-8 p-4 bg-white rounded-lg shadow-lg">
            <h2 className="text-3xl font-semibold mb-6 text-center">Time Dashboard</h2>
            <div className="mb-6">
                <TaskSelector tasks={tasks} setSelectedTask={setSelectedTask} />
                <button className="btn btn-primary w-full mt-2" onClick={startTracking} disabled={!selectedTask}>
                    Start Tracking
                </button>
            </div>
            <div className="mb-6">
                <button className="btn btn-secondary fixed bottom-4 right-4" onClick={() => setModalIsOpen(true)}>
                    + Add Timer
                </button>
            </div>
            {activeTasks.length > 0 && (
                <>
                    <table className="table w-full">
                        <thead>
                        <tr>
                            <th>Issues</th>
                            <th className="text-center">Time Spent</th>
                        </tr>
                        </thead>
                        <tbody>
                        {activeTasks.map((task, index) => (
                            <tr key={index}>
                                <td>{task.task.title}</td>
                                <td className="text-center">{formatTime(task.elapsedTime)}</td>
                            </tr>
                        ))}
                        </tbody>
                    </table>
                    {activeTasks.some(task => task.isRunning) && (
                        <div className="fixed bottom-0 right-0 m-4">
                            {activeTasks.map((task, index) => (
                                task.isRunning && (
                                    <div key={index} className="card w-96 bg-white shadow-lg p-4 mb-4">
                                        <h3 className="card-title">{task.task.title}</h3>
                                        <div className="text-2xl font-bold">{formatTime(task.elapsedTime)}</div>
                                        <div className="card-actions justify-end mt-4">
                                            <button className="btn btn-secondary" onClick={() => toggleTimer(index)}>
                                                {task.isRunning ? 'Pause' : 'Resume'}
                                            </button>
                                            <button className="btn btn-error" onClick={() => stopTimer(index)}>Stop</button>
                                        </div>
                                    </div>
                                )
                            ))}
                        </div>
                    )}
                </>
            )}
            {modalIsOpen && (
                <LogWorkModal
                    isOpen={modalIsOpen}
                    onRequestClose={handleModalClose}
                    onCancel={handleModalCancel}
                    task={selectedTask}
                    startTime={new Date()}
                    endTime={new Date()}
                />
            )}
        </div>
    );
};

export default TimeDashboard;
