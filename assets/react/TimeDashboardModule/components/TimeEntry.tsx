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
    endTime?: Date;
    description?: string;
}
