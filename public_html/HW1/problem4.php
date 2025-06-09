<?php

require_once "base.php";

$ucid = "may23"; // <-- set your ucid

// Don't edit the arrays below, they are used to test your code
$array1 = ["hello world!", "php programming", "special@#$%^&characters", "numbers 123 456", "mIxEd CaSe InPut!"];
$array2 = ["hello world", "php programming", "this is a title case test", "capitalize every word", "mixEd CASE input"];
$array3 = ["  hello   world  ", "php    programming  ", "  extra    spaces  between   words   ",
    "      leading and trailing spaces      ", "multiple      spaces"];
$array4 = ["hello world", "php programming", "short", "a", "even"];


function transformText($arr, $arrayNumber) {
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoBasic($arr, $arrayNumber);

    // Challenge 1: Remove non-alphanumeric characters except spaces
    // Challenge 2: Convert text to Title Case
    // Challenge 3: Trim leading/trailing spaces and remove duplicate spaces
    // Result 1-3: Assign final phrase to `$placeholderForModifiedPhrase`
    // Challenge 4 (extra credit): Extract up to the middle 3 characters (middle index and +/- 1 if it's not the first/last character),
    // Do not include the first or last character of the phrase/word. (e.g., oven should show as ve)
    // assign the result to `$placeholderForMiddleCharacters`
    // If the phrase is shorter than 3 characters, return "Not enough characters"

    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)
    $placeholderForModifiedPhrase = "";
    $placeholderForMiddleCharacters = "";
    foreach ($arr as $index => $text) {
        // Start Solution Edits
        
        // UCID: may23, Date: 6/9/2025
        // Plan:
        // 1. Remove non-alphanumeric characters (except spaces) using regex
        // 2. Convert to Title Case using ucwords(strtolower(...))
        // 3. Trim and reduce multiple spaces to a single space
        // 4. For extra credit:
        //    - Remove first and last character
        //    - If remaining length < 3, set "Not enough characters"
        //    - Otherwise, extract the middle 3 characters

        // Step 1: Remove special characters (except space)
        $cleaned = preg_replace('/[^a-zA-Z0-9 ]/', '', $text);

        // Step 2: Reduce multiple spaces to a single space
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);

        // Step 3: Trim leading/trailing spaces
        $cleaned = trim($cleaned);

        // Step 4: Convert to Title Case
        $placeholderForModifiedPhrase = ucwords(strtolower($cleaned));

        // Step 5: Extra credit - middle 3 characters (excluding first and last)
        $core = substr($placeholderForModifiedPhrase, 1, -1);  // exclude first and last char

        if (strlen($core) < 3) {
            $placeholderForMiddleCharacters = "Not enough characters";
        } else {
            $mid = floor(strlen($core) / 2);
            $placeholderForMiddleCharacters = substr($core, $mid - 1, 3);
        }
        

        // End Solution Edits
        echo "<div>";
        printStringTransformations($index, $placeholderForModifiedPhrase, $placeholderForMiddleCharacters);
        echo "</div>";
    }

    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 4);
transformText($array1, 1);
transformText($array2, 2);
transformText($array3, 3);
transformText($array4, 4);
printFooter($ucid, 4);

?>