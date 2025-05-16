// Task Management
let tasks = JSON.parse(localStorage.getItem('tasks')) || [];

// DOM Elements
const taskForm = document.querySelector('.task-form');
const taskInput = document.querySelector('.task-input');
const taskList = document.querySelector('.task-list');

// Add Task
function addTask(e) {
    if (e) e.preventDefault();
    
    const taskText = taskInput?.value.trim();
    if (!taskText) return;

    const task = {
        id: Date.now(),
        text: taskText,
        completed: false
    };

    tasks.push(task);
    saveTasksToStorage();
    renderTasks();
    
    if (taskInput) taskInput.value = '';
}

// Delete Task
function deleteTask(id) {
    tasks = tasks.filter(task => task.id !== id);
    saveTasksToStorage();
    renderTasks();
}

// Toggle Task Completion
function toggleTask(id) {
    tasks = tasks.map(task =>
        task.id === id ? { ...task, completed: !task.completed } : task
    );
    saveTasksToStorage();
    renderTasks();
}

// Save Tasks to LocalStorage
function saveTasksToStorage() {
    localStorage.setItem('tasks', JSON.stringify(tasks));
}

// Render Tasks
function renderTasks() {
    if (!taskList) return;
    
    taskList.innerHTML = '';
    tasks.forEach(task => {
        const li = document.createElement('li');
        li.className = 'task-item';
        li.innerHTML = `
            <input type="checkbox" ${task.completed ? 'checked' : ''}
                onchange="toggleTask(${task.id})">
            <span class="${task.completed ? 'completed' : ''}">${task.text}</span>
            <button onclick="deleteTask(${task.id})">Delete</button>
        `;
        taskList.appendChild(li);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    renderTasks();
    if (taskForm) {
        taskForm.addEventListener('submit', addTask);
    }
});


const navbarToggle = document.querySelector('.navbar-toggle');
const navLinks = document.querySelector('.nav-links');

if (navbarToggle && navLinks) {
    navbarToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
}