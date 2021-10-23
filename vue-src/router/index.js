import { createWebHashHistory, createRouter } from "vue-router";


import Employee from '../components/Employee.vue'
import ViewEmployee from '../components/ViewEmployee.vue'
import HolidayOverview from '../components/holiday/HolidayOverview.vue'
import AddHoliday from '../components/holiday/AddHoliday.vue'
import Projects from '../components/projects/projects.vue'
import AddProject from '../components/projects/AddProject.vue'
import Tasks from '../components/tasks/Tasks.vue'
import AddTask from '../components/tasks/AddTask.vue'
import AllocateTasks from '../components/tasks/AllocateTasks.vue'
import AllocateProject from '../components/projects/AllocateProject.vue'

const routes = [
    { path: '/', name:'HolidayOverview', component: HolidayOverview },
    { path: '/view-employee/:employeeId', name:'ViewEmployee', component: ViewEmployee },
    { path: '/add-holiday', name:'AddHoliday', component: AddHoliday },
    { path: '/projects', name:'Projects', component: Projects },
    { path: '/add-project', name:'AddProject', component: AddProject },
    { path: '/tasks', name:'Tasks', component: Tasks },
    { path: '/add-task', name:'AddTask', component: AddTask },
    { path: '/allocate-tasks', name:'AllocateTasks', component: AllocateTasks },
    { path: '/allocate-project', name:'AllocateProject', component: AllocateProject }
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;