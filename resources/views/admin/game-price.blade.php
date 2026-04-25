@extends('layouts.app')

@section('title', 'Game Price')

@section('content')
    <div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-center fw-semibold">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show text-center fw-semibold">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="text-center mb-4">
            <h4 class="fw-bold">Game Price</h4>
            <p class="text-muted small">Set win amount per ₹10 bet for each game type</p>
        </div>

        {{-- FORM CARD --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('admin.game-price.update') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Game Play</label>
                        <select name="game_type" id="gameTypeSelect" class="form-select"
                            onchange="autoFillPrice(this.value)">
                            <option value="">-- Select Game Type --</option>
                            <option value="jodi" {{ old('game_type') == 'jodi' ? 'selected' : '' }}>Jodi</option>
                            <option value="andar" {{ old('game_type') == 'andar' ? 'selected' : '' }}>Andar</option>
                            <option value="bahar" {{ old('game_type') == 'bahar' ? 'selected' : '' }}>Bahar</option>
                        </select>
                        @error('game_type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Price per 10 Rs.</label>
                        <input type="number" step="0.01" min="0" name="price" id="priceInput" value="{{ old('price') }}"
                            class="form-control" placeholder="e.g. 98" oninput="updatePreview()">
                        @error('price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        {{-- Live preview --}}
                        <div id="pricePreview" class="mt-2 p-2 rounded bg-light small" style="display:none;">
                            Per <strong>₹10</strong> bet → Win:
                            <strong class="text-success" id="previewWin">—</strong>
                            &nbsp;|&nbsp; Grand Amount (×10):
                            <strong class="text-primary" id="previewGrand">—</strong>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white fw-semibold px-4" style="background:#1976d2">
                            ➕ Add / Update
                        </button>
                        <button type="reset" class="btn btn-outline-secondary px-4"
                            onclick="document.getElementById('pricePreview').style.display='none'">
                            Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- PRICE TABLE --}}
        <div class="card shadow-sm">
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover align-middle mb-0 text-center">
                    <thead style="background:#1a3a6e;color:#fff;">
                        <tr>
                            <th>Sn. no</th>
                            <th>Game Type</th>
                            <th>Price</th>
                            <th>Multiply</th>
                            <th>Grand Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($priceRows as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge fs-6
                                            @if($row['type'] == 'jodi') bg-primary
                                            @elseif($row['type'] == 'andar') bg-success
                                            @elseif($row['type'] == 'bahar') bg-danger
                                            @elseif($row['type'] == 'crossing') bg-warning text-dark
                                            @else bg-info text-dark @endif">
                                        {{ $row['label'] }}
                                    </span>
                                </td>
                                <td class="fw-bold">
                                    @if($row['price'] > 0)
                                        ₹{{ number_format($row['price'], 2) }}
                                    @else
                                        <span class="text-muted small">Not set</span>
                                    @endif
                                </td>
                                <td>{{ $row['multiply'] }}</td>
                                <td class="fw-bold {{ $row['price'] > 0 ? 'text-success' : 'text-muted' }}">
                                    @if($row['price'] > 0)
                                        ₹{{ number_format($row['grand'], 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    {{-- ✅ Default active --}}
                                    <span class="badge {{ $row['status'] == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($row['status']) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="fillForm('{{ $row['type'] }}', '{{ $row['price'] }}')">
                                        ✏️ Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-muted py-4">No prices set yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        const existingPrices = {
            @foreach($priceRows as $row)
                '{{ $row['type'] }}': {{ $row['price'] }},
            @endforeach
        };

        function autoFillPrice(type) {
            const input = document.getElementById('priceInput');
            if (existingPrices[type] !== undefined && existingPrices[type] > 0) {
                input.value = existingPrices[type];
            } else {
                input.value = '';
            }
            updatePreview();
        }

        function fillForm(type, price) {
            document.getElementById('gameTypeSelect').value = type;
            document.getElementById('priceInput').value = price;
            updatePreview();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updatePreview() {
            const price = parseFloat(document.getElementById('priceInput').value);
            const preview = document.getElementById('pricePreview');
            if (!isNaN(price) && price > 0) {
                preview.style.display = 'block';
                document.getElementById('previewWin').textContent = '₹' + price.toFixed(2);
                document.getElementById('previewGrand').textContent = '₹' + (price * 10).toFixed(2);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
@endsection