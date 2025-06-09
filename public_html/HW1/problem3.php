<?php

require_once "base.php";

$ucid = "may23"; // <-- set your ucid

// Don't edit the arrays below, they are used to test your code
$array1 = [42, -17, 89, -256, 1024, -4096, 50000, -123456];
$array2 = [3.14159265358979, -2.718281828459, 1.61803398875, -0.5772156649, 0.0000001, -1000000.0];
$array3 = [1.1, -2.2, 3.3, -4.4, 5.5, -6.6, 7.7, -8.8];
$array4 = ["123", "-456", "789.01", "-234.56", "0.00001", "-99999999"];
$array5 = [-1, 1, 2.0, -2.0, "3", "-3.0"];

function bePositive($arr, $arrayNumber)
{
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoMixed($arr, $arrayNumber);

    // Challenge 1: Make each value positive
    // Challenge 2: Convert the values back to their original data type and assign it to the proper slot of the `output` array
    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)

    $output = array_fill(0, count($arr), null); // Initialize output array
    // Start Solution Edits
    
    // UCID: may23, Date: 6/9/2025
    // Plan:
    // - Loop through each element of the input array
    // - Convert the element to its correct numeric value if it's a string
    // - Use abs() to make the value positive
    // - Convert it back to its original type:
    //     - if original was string, convert back to string using (string)
    //     - if original was int, keep as int
    //     - if original was float, keep as float
    // - Store the result in the $output array at the same index

    foreach ($arr as $i => $val) {
        $type = gettype($val);
        $positive = abs($val);

        if ($type === "string") {
            $output[$i] = (string)$positive;
        } elseif ($type === "integer") {
            $output[$i] = (int)$positive;
        } elseif ($type === "double") {
            $output[$i] = (float)$positive;
        } else {
            // fallback if it's a weird type
            $output[$i] = $positive;
        }
    }

    // End Solution Edits
    echo "<span>Output: </span>";
    printOutputWithType($output);
    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 3);
bePositive($array1, 1);
bePositive($array2, 2);
bePositive($array3, 3);
bePositive($array4, 4);
bePositive($array5, 5);
printFooter($ucid, 3);