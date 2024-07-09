import { createRoot } from 'react-dom/client';
import * as React from "react";
import TaskManager from "./component/TaskManager";

function TaskListModule() {
    return <TaskManager></TaskManager>;
}

const domNode = document.getElementById('task-list-module');
const root = createRoot(domNode);

root.render(<TaskListModule />);
