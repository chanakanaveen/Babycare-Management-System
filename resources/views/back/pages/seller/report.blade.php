@extends('back.layout.pages-layout')
@section('pagetitle', 'Baby Weight History')
@section('content')

<div class="col-md-12">
    <div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="h4 text-blue">Baby Weight History</h4>
            </div>
        </div>

        <div class="row card-box">
            <!-- Selectpicker Dropdown -->
            <div class="mb-3 form-group pd-20 col-md-4">

                <select class="custom-select2 form-control select2-hidden-accessible" name="baby" id="baby" style="width: 100%; height: 38px" data-select2-id="1" tabindex="-1" aria-hidden="true">
                    <option value="" selected disabled>Select a Baby</option>
                    @foreach ($babies as $row )
                        <option value="{{ $row->baby_id  }}" >{{ $row->full_name  }}</option>
                    @endforeach
                </select>

            </div>

            <div class="pd-20 col-md-4">
                <button id="generateReportBtn" class="btn btn-primary" style="margin-left: 10px;">Generate Report</button>
            </div>

            <!-- Chart Container -->
            {{-- <div id="baby-history-chart" style="height: 400px;"></div> --}}

            <div class="col-md-12">
                <canvas id="weightChart"></canvas>
            </div>

            <div class="col-md-12">
                <canvas id="heightChart"></canvas>
            </div>


        </div>



    </div>
</div>
<div>
    <canvas id="myChart"></canvas>
  </div>


@endsection

@section('myscript')
<script>
$(document).ready(function(){
    //province change
    $("#generateReportBtn").click(function(){
        console.log('report');
        var selectid = $('#baby').val();
        console.log(selectid);
        $.ajax({
            url:'{{ route('midwife.report-data') }}',
            method:"POST",
            data:{"selectid":selectid},
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            beforeSend:function(){

            },
            success:function(response){
                console.log(response);
                 // Extract weight and record_date from the response
                 const weights = response.map(record => parseFloat(record.weight));
                 const heights = response.map(record => parseFloat(record.height));
                 const dates = response.map(record => record.record_date);

                // Update the weightChart
                updateWeightChart(weights, dates);

                // Update the heightChart
                // updateHeightChart(heights, dates);
            },
            error:function(xhr, status, error){
                console.log(xhr.responseText);
            }
        });
    });

    // Initialize Chart.js chart
    let weightChart;
    let heightChart;

    function updateWeightChart(weights, dates) {
        const ctx = document.getElementById('weightChart').getContext('2d');

        // Destroy existing chart instance if it exists
        if (weightChart) {
            weightChart.destroy();
        }

        // Create a new chart instance
        weightChart = new Chart(ctx, {
            type: 'line', // Line chart for weight history
            data: {
                labels: dates, // X-axis labels (dates)
                datasets: [{
                    label: 'Weight History (kg)',
                    data: weights, // Y-axis data (weights)
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.4 // Smooth curves
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Weight (kg)'
                        }
                    }
                }
            }
        });
    }

    function updateHeightChart(heights, dates) {
        const ctx = document.getElementById('heightChart').getContext('2d');

        // Destroy existing chart instance if it exists
        if (heightChart) {
            heightChart.destroy();
        }

        // Create a new chart instance
        heightChart = new Chart(ctx, {
            type: 'line', // Line chart for height history
            data: {
                labels: dates, // X-axis labels (dates)
                datasets: [{
                    label: 'Height History (cm)',
                    data: heights, // Y-axis data (heights)
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    tension: 0.4 // Smooth curves
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Height (cm)'
                        }
                    }
                }
            }
        });
    }


});

</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- <script>
  const ctx = document.getElementById('weightChart');

  new Chart(ctx, {
    type: 'bubble',
    data: {
      labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
      datasets: [{
        label: '# of Votes',
        data: [12, 19, 3, 5, 2, 3],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script> --}}

@endsection
