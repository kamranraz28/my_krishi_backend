<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Finance Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .row {
            margin-bottom: 15px;
        }
        .row h6 {
            font-weight: bold;
        }
        .row p {
            margin: 5px 0;
        }
        .row div {
            display: inline-block;
            width: 48%;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h2>Project Finance Details</h2>
            <p>{{ $project->details->title ?? '' }}-{{ $project->unique_id }}</p>
        </div>

        <div class="row">
            <div>
                <h6>Project Cost</h6>
                <p>{{ $totalCost }} BDT</p>
            </div>
            <div>
                <h6>Project Revenue</h6>
                <p>{{ $revenue }} BDT</p>
            </div>
        </div>

        <div class="row">
            <div>
                <h6>Project Outcome</h6>
                <p>{{ $profit }} BDT</p>
            </div>
            <div>
                <h6>Service Charge</h6>
                <p>{{ $serviceChargePercent }}%</p>
            </div>
        </div>

        <div class="row">
            <div>
                <h6>Net Profit</h6>
                <p>{{ $netProfit }} BDT</p>
            </div>
            <div>
                <h6>Total Unit</h6>
                <p>{{ $unit }}</p>
            </div>
        </div>

        <div class="row">
            <div>
                <h6>Profit/Unit</h6>
                <p>{{ $profitPerUnit }} BDT</p>
            </div>
        </div>
    </div>

</body>
</html>
