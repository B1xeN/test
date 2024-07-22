<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>การทำนายคุณภาพไวน์</title>
    <style>
        table {
            border-collapse: collapse;
            width: 80%;
            margin: auto;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <form action="#" method="POST" enctype="multipart/form-data">
        <center><h3>การทำนายคุณภาพไวน์</h3></center>
        <table>
            <tr>
                <th>ความเป็นกรดคงที่</th>
                <th>ความเป็นกรดระเหย</th>
                <th>กรดมะนาว</th>
                <th>น้ำตาลที่เหลือ</th>
                <th>คลอไรด์</th>
                <th>ซัลเฟอร์ไดออกไซด์อิสระ</th>
                <th>ซัลเฟอร์ไดออกไซด์ทั้งหมด</th>
                <th>ความหนาแน่น</th>
                <th>ค่า pH</th>
                <th>ซัลเฟต</th>
                <th>แอลกอฮอล์</th>
            </tr>
            <tr>
                <td><input type="number" name="f_acidity" value="" step="0.01" size="2"></td>
                <td><input type="number" name="v_acidity" value="" step="0.01"></td>
                <td><input type="number" name="c_acid" value="" step="0.01"></td>
                <td><input type="number" name="r_sugar" value="" step="0.01"></td>
                <td><input type="number" name="chlorides" value="" step="0.01"></td>
                <td><input type="number" name="f_dioxide" value="" step="0.01"></td>
                <td><input type="number" name="t_dioxide" value="" step="0.01"></td>
                <td><input type="number" name="density" value="" step="0.01"></td>
                <td><input type="number" name="pH" value="" step="0.01"></td>
                <td><input type="number" name="sulphates" value="" step="0.01"></td>
                <td><input type="number" name="alcohol" value="" step="0.01"></td>
            </tr>
            <tr>
                <td colspan="11">
                    <center>
                        <button type="submit" name="process">Process</button>
                    </center>
                </td>
            </tr>
        </table>
    </form>

   <?php
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process'])) {
    // Retrieve data from the form
    $f_acidity = floatval($_POST['f_acidity']);
    $v_acidity = floatval($_POST['v_acidity']);
    $c_acid = floatval($_POST['c_acid']);
    $r_sugar = floatval($_POST['r_sugar']);
    $chlorides = floatval($_POST['chlorides']);
    $f_dioxide = floatval($_POST['f_dioxide']);
    $t_dioxide = floatval($_POST['t_dioxide']);
    $density = floatval($_POST['density']);
    $pH = floatval($_POST['pH']);
    $sulphates = floatval($_POST['sulphates']);
    $alcohol = floatval($_POST['alcohol']);

    // Display the received form values
    echo '<h4>ค่าที่รับมาจากฟอร์ม:</h4>';
    echo '<p>ความเป็นกรดคงที่: ' . $f_acidity . '</p>';
    echo '<p>ความเป็นกรดระเหย: ' . $v_acidity . '</p>';
    echo '<p>กรดมะนาว: ' . $c_acid . '</p>';
    echo '<p>น้ำตาลที่เหลือ: ' . $r_sugar . '</p>';
    echo '<p>คลอไรด์: ' . $chlorides . '</p>';
    echo '<p>ซัลเฟอร์ไดออกไซด์อิสระ: ' . $f_dioxide . '</p>';
    echo '<p>ซัลเฟอร์ไดออกไซด์ทั้งหมด: ' . $t_dioxide . '</p>';
    echo '<p>ความหนาแน่น: ' . $density . '</p>';
    echo '<p>ค่า pH: ' . $pH . '</p>';
    echo '<p>ซัลเฟต: ' . $sulphates . '</p>';
    echo '<p>แอลกอฮอล์: ' . $alcohol . '</p>';

    // Read training data from the CSV file
    $csv_filename = 'Data_set.csv'; // Update with the correct file path
    $training_set = readTrainingDataFromCSV($csv_filename);

    // Check if the training set is valid
    if (!is_array($training_set) || count($training_set) < 2) {
        echo 'Error: Invalid or insufficient training data.';
        exit();
    }

    // Calculate distances
    $distances = calculateDistances($training_set, $f_acidity, $v_acidity, $c_acid, $r_sugar, $chlorides, $f_dioxide, $t_dioxide, $density, $pH, $sulphates, $alcohol);

    // Find the k-nearest neighbors
    $k = 3;
    $k_nearest_neighbors = getKNearestNeighbors($distances, $k);

    // Extract the quality levels of the k-nearest neighbors
    $neighbor_qualities = array_column(array_intersect_key($training_set, array_flip($k_nearest_neighbors)), 12);

    // Predicted quality
    $predicted_quality = getMajorityClass($neighbor_qualities);

    // Display the predicted quality
    echo '<h4>ผลลัพธ์:</h4>';
    echo '<p>ระดับคุณภาพที่ทำนายได้: ' . $predicted_quality . '</p>';
}

function calculateDistances($training_set, $f_acidity, $v_acidity, $c_acid, $r_sugar, $chlorides, $f_dioxide, $t_dioxide, $density, $pH, $sulphates, $alcohol) {
    $distances = [];

    foreach ($training_set as $data) {
        $distance = sqrt(
            pow(($data[1] - $f_acidity), 2) +
            pow(($data[2] - $v_acidity), 2) +
            pow(($data[3] - $c_acid), 2) +
            pow(($data[4] - $r_sugar), 2) +
            pow(($data[5] - $chlorides), 2) +
            pow(($data[6] - $f_dioxide), 2) +
            pow(($data[7] - $t_dioxide), 2) +
            pow(($data[8] - $density), 2) +
            pow(($data[9] - $pH), 2) +
            pow(($data[10] - $sulphates), 2) +
            pow(($data[11] - $alcohol), 2)
        );

        $distances[] = $distance;
    }

    return $distances;
}

function getKNearestNeighbors($distances, $k) {
    $sorted_distances = $distances;
    asort($sorted_distances);
    $neighbor_indices = array_keys($sorted_distances);
    return array_slice($neighbor_indices, 0, $k);
}

function getMajorityClass($neighbor_qualities) {
    $neighbor_qualities = array_map('intval', $neighbor_qualities);

    $counts = array_count_values($neighbor_qualities);
    arsort($counts);
    $most_common = key($counts);
    return $most_common;
}

function readTrainingDataFromCSV($file) {
    $training_set = [];

    if (($handle = fopen($file, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle)) !== FALSE) {
            $training_set[] = array_map('floatval', $data);
        }

        fclose($handle);
    }

    return $training_set;
}
?>


</body>
</html>
