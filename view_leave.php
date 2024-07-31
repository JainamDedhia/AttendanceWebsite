<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Leave Applications</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
            background-image: url('dashboard2.jpg');
            background-size: cover; /* Ensures the background image covers the entire body */
            background-position: center; /* Centers the background image */
            background-repeat: no-repeat; /* Prevents the background image from repeating */
            min-height: 100vh; /* Ensures the body takes at least the full height of the viewport */
        }
        .container {
            width: 80%;
            margin: 40px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            transition: transform 0.3s ease;
        }
        .container:hover {
            transform: translateY(-5px);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 14px;
        }
        th {
            background-color: #e0aaff;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tbody tr:nth-child(odd) {
            background-color: #fff;
        }
        tbody tr:hover {
            background-color: #EDE3FF;
            transform: scale(1.02);
        }
        .filter-form {
            margin-bottom: 20px;
            text-align: center;
            animation: fadeIn 1s ease-in-out;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        label {
            font-weight: bold;
            margin-right: 10px;
            color: #555;
        }
        input[type=text] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 200px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
            transition: border-color 0.3s;
        }
        input[type=text]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            padding: 10px 16px;
            background-color: #7b2cbf;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            font-size: 14px;
        }
        button:hover {
            background-color: #c77dff;
            transform: scale(1.05);
        }
        button:focus {
            outline: none;
        }
        @media screen and (max-width: 600px) {
            table {
                font-size: 12px;
            }
            input[type=text] {
                width: 150px;
            }
            button {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Leave Applications</h2>
        <form method="GET" action="" class="filter-form">
            <label for="employee_id">Employee ID:</label>
            <input type="text" id="employee_id" name="employee_id" placeholder="Enter Employee ID">
            <button type="submit">Filter</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require 'vendor/autoload.php';

                use MongoDB\Client;

                function bsonToString($field) {
                    if (is_object($field) && method_exists($field, 'toDateTime')) {
                        return $field->toDateTime()->format('Y-m-d');
                    } elseif (is_object($field)) {
                        return json_encode($field);
                    }
                    return htmlspecialchars((string)$field);
                }

                $mongoClient = new Client('mongodb://localhost:27017');
                $database = $mongoClient->leave_management;
                $collection = $database->leave_applications;

                $employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : '';

                if (!empty($employee_id)) {
                    $filter = ['employee_id' => $employee_id];
                    $options = ['sort' => ['start_date' => 1]];
                    $cursor = $collection->find($filter, $options);
                } else {
                    $cursor = $collection->find();
                }

                foreach ($cursor as $document) {
                    $start_date = bsonToString($document['start_date']);
                    $end_date = bsonToString($document['end_date']);
                    $reason = bsonToString($document['reason']);
                    $status = bsonToString($document['status']);

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars((string)$document['employee_id']) . '</td>';
                    echo '<td>' . $start_date . '</td>';
                    echo '<td>' . $end_date . '</td>';
                    echo '<td>' . $reason . '</td>';
                    echo '<td>' . $status . '</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
