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

<ul id="task-list" class="list-group">
@foreach($tasks as $task)
    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $task->id }}">
        <div>
            <form action="{{ route('tasks.toggle', $task->id) }}" method="POST" class="d-inline">
                @csrf @method('PATCH')
                <button class="btn btn-sm {{ $task->completed ? 'btn-success' : 'btn-outline-secondary' }}">
                    {{ $task->completed ? '✔' : '✖' }}
                </button>
            </form>
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
