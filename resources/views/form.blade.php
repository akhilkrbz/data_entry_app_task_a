<style>
    .import-form {
        max-width: 400px;
        margin: 40px auto;
        padding: 24px;
        background: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .import-form input[type="file"] {
        display: block;
        margin-bottom: 16px;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: #fff;
    }

    .import-form button {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.2s;
    }

    .import-form button:hover {
        background: #0056b3;
    }

    .import-results {
        max-width: 600px;
        margin: 20px auto;
        padding: 16px;
        background: #f1f1f1;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        font-size: 14px;
        color: #333;
    }
</style>


<h2 style="text-align: center; margin-top: 24px;">Task A — Bulk Import + Chunked Drag-and-Drop Image Upload</h2>
<form class="import-form" action="{{ route('import.data') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="csv_file" required>
    <button type="submit">Import CSV</button>
</form>

<div class="import-results">
    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    @if(session('totalRows'))
        <div style="margin-top: 16px; color: #333;">
            <strong>Total rows processed:</strong> {{ session('totalRows') }}<br>
            <strong>New rows inserted:</strong> {{ session('newRows') }}<br>
            <strong>Existing rows updated:</strong> {{ session('updatedRows') }}
        </div>
    @endif

    @if(session('invalidRows') && count(session('invalidRows')) > 0)
        <div style="color: red; margin-top: 16px;">
            <strong>Invalid rows ({{ count(session('invalidRows')) }}):</strong>
            <ul>
                @foreach(session('invalidRows') as $row)
                    <li>Row {{ $row }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('duplicateRows') && count(session('duplicateRows')) > 0)
        <div style="color: orange; margin-top: 16px;">
            <strong>Duplicated rows ({{ count(session('duplicateRows')) }}):</strong>
            <ul>
                @foreach(session('duplicateRows') as $row)
                    <li>Row {{ $row }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>