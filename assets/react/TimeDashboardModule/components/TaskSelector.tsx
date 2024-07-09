import React, { useState, useEffect } from 'react';
import Select from 'react-select';

interface Task {
    id: number;
    title: string;
    description: string;
    status: string;
}

interface TaskSelectorProps {
    tasks: Task[];
    setSelectedTask: (task: Task | null) => void;
}

const TaskSelector: React.FC<TaskSelectorProps> = ({ tasks, setSelectedTask }) => {
    const [options, setOptions] = useState<{ value: number; label: string }[]>([]);
    const [selectedOption, setSelectedOption] = useState<{ value: number; label: string } | null>(null);

    useEffect(() => {
        setOptions(tasks.map(task => ({ value: task.id, label: task.title })));
    }, [tasks]);

    const handleChange = (option: { value: number; label: string } | null) => {
        setSelectedOption(option);
        const task = tasks.find(task => task.id === option?.value) || null;
        setSelectedTask(task);
    };

    return (
        <div className="mb-4">
            <Select
                value={selectedOption}
                onChange={handleChange}
                options={options}
                placeholder="Select a task..."
                className="react-select-container"
                classNamePrefix="react-select"
            />
        </div>
    );
};

export default TaskSelector;
