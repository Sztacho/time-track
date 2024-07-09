import { createRoot } from 'react-dom/client';
import * as React from "react";
import TimeDashboard from "./components/TimeDashboard";

function TimeDashboardModule() {
    return <TimeDashboard />;
}

const domNode = document.getElementById('time-dashboard-module');
const root = createRoot(domNode!);

root.render(<TimeDashboardModule />);
