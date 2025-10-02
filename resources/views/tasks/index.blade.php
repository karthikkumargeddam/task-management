@extends('layouts.app')

@section('content')
<h1>Tasks</h1>
<a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">Add Task</a>

<form method="GET" action="{{ route('tasks.index') }}" class="mb-3">
    <select name="filter" onchange="this.form.submit()" class="form-select w-auto d-inline">
        <option value="all" {{ $filter=='all'?'selected':'' }}>All</option>
        <option value="completed" {{ $filter=='completed'?'selected':'' }}>Completed</option>
        <option value="incomplete" {{ $filter=='incomplete'?'selected':'' }}>Incomplete</option>
    </select>
</form>

<ul id="task-list" class="list-group">
@foreach($tasks as $task)
    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $task->id }}">
        <div>
            <button 
                class="btn btn-sm toggle-btn {{ $task->completed ? 'btn-success' : 'btn-outline-secondary' }}" 
                data-id="{{ $task->id }}">
                {{ $task->completed ? 'Completed' : 'Mark Complete' }}
            </button>
            <strong>{{ $task->title }}</strong> - {{ $task->description }}
        </div>
        <div>
            <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    </li>
@endforeach
</ul>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Use event delegation so new tasks work too
document.getElementById('task-list').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('toggle-btn')) {
        const btn = e.target;
        const taskId = btn.dataset.id;

        // Generate the toggle URL using Laravel's base URL
        const toggleUrl = '{{ url("tasks") }}/' + taskId + '/toggle';

        fetch(toggleUrl, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.completed) {
                   btn.classList.remove('btn-outline-secondary');
                   btn.classList.add('btn-success');
                   btn.textContent = 'Completed';
                } else {
                   btn.classList.remove('btn-success');
                   btn.classList.add('btn-outline-secondary');
                   btn.textContent = 'Mark Complete';
                }
             }  
        })
        // .catch(err => alert('Error: ' + err));
    }
});

// Sortable (optional)
new Sortable(document.getElementById('task-list'), {
    animation: 150,
    onEnd: function(evt) {
        const order = Array.from(document.querySelectorAll('#task-list li')).map(li => li.dataset.id);
        fetch('{{ route('tasks.reorder') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ order })
        });
    }
});
</script>
@endsection
