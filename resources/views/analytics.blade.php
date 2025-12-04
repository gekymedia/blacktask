@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Task Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Completion Chart -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Daily Completion</h2>
            <canvas id="completionChart"></canvas>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white p-4 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">Category Distribution</h2>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Completion Chart
    new Chart(document.getElementById('completionChart'), {
        type: 'line',
        data: {
            labels: {
                !!json_encode($data['daily_completion'] - > pluck('date')) !!
            },
            datasets: [{
                label: 'Tasks Completed',
                data: {
                    !!json_encode($data['daily_completion'] - > pluck('completed')) !!
                },
                borderColor: '#3b82f6',
                tension: 0.1
            }]
        }
    });

    // Category Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: {
                !!json_encode($data['category_distribution'] - > pluck('category.name')) !!
            },
            datasets: [{
                data: {
                    !!json_encode($data['category_distribution'] - > pluck('count')) !!
                },
                backgroundColor: {
                    !!json_encode($data['category_distribution'] - > pluck('category.color')) !!
                }
            }]
        }
    });
</script>
@endsection