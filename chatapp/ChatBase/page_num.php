<?php
// page_num.php

// Define the number of items per page
$items_per_page = 5;

// Function to calculate total pages for 'My Chat Rooms'
function calculate_my_total_pages($connection, $user_id) {
    global $items_per_page;

    $my_total_query = "SELECT COUNT(*) as total FROM chat_member WHERE userid = ?";
    if ($my_total_stmt = $connection->prepare($my_total_query)) {
        $my_total_stmt->bind_param("i", $user_id);

        if ($my_total_stmt->execute()) {
            $my_total_result = $my_total_stmt->get_result();
            $my_total_row = $my_total_result->fetch_assoc();
            $my_total_pages = ceil($my_total_row['total'] / $items_per_page);
        } else {
            // Log error or handle it accordingly
            error_log('Error executing query: ' . $my_total_stmt->error);
            $my_total_pages = 0; // Default to 0 in case of error
        }

        $my_total_stmt->close();
    } else {
        // Log error or handle it accordingly
        error_log('Error preparing statement: ' . $connection->error);
        $my_total_pages = 0; // Default to 0 in case of error
    }

    return $my_total_pages;
}



// Function to calculate total pages for 'List of Chat Rooms'
function calculate_list_total_pages($connection) {
    global $items_per_page;
    $list_total_query = "SELECT COUNT(*) as total FROM chatroom";
    $list_total_stmt = $connection->prepare($list_total_query);
    $list_total_stmt->execute();
    $list_total_result = $list_total_stmt->get_result();
    $list_total_row = $list_total_result->fetch_assoc();
    $list_total_pages = ceil($list_total_row['total'] / $items_per_page);
    $list_total_stmt->close();
    return $list_total_pages;
}
