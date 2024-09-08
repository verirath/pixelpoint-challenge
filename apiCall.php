<?php

function fetch_carinthia_events() {
    $api_url = "https://data.carinthia.com/api/v4/endpoints/557ea81f-6d65-6476-9e01-d196112514d2?include=image&token=9962098a5f6c6ae8d16ad5aba95afee0";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
        return null;
    }

    // Close cURL
    curl_close($ch);

    // Decode the JSON response
    return json_decode($response, true);
}

$events = fetch_carinthia_events();

$events_per_page = 6; // Number of events to show per page
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number from URL
$offset = ($current_page - 1) * $events_per_page; // Calculate the offset for pagination

if ($events && isset($events['@graph'])) {
    $total_events = count($events['@graph']);
    $total_pages = ceil($total_events / $events_per_page);
    $paginated_events = array_slice($events['@graph'], $offset, $events_per_page);

    echo '<div class="events-container">';
foreach ($paginated_events as $event) {
    if (!empty($event['name']) && !empty($event['description']) && !empty($event['image'][0]['thumbnailUrl'])) {
        echo '<div class="event-card">';
        echo '<img src="' . htmlspecialchars($event['image'][0]['thumbnailUrl']) . '" alt="' . htmlspecialchars($event['name']) . '" class="event-image">';
        echo '<div class="event-content">';

        // Event title
        echo '<h3 class="event-title">' . (!empty($event['name']) ? htmlspecialchars($event['name']) : 'Event name not available') . '</h3>';

        // Event date
        echo '<div class="event-info">';
        if (!empty($event['startDate'])) {
            $startDate = new DateTime($event['startDate']);
            echo '<p class="event-date"><i class="fa fa-calendar"></i> Start: ' . $startDate->format('d. F Y') . '</p>';
        } else {
            echo '<p class="event-date"><i class="fa fa-calendar"></i> Start date not specified</p>';
        }

        // Display end date if available
        if (!empty($event['endDate'])) {
            $endDate = new DateTime($event['endDate']);
            echo '<p class="event-date"><i class="fa fa-calendar"></i> End: ' . $endDate->format('d. F Y') . '</p>';
        } else {
            echo '<p class="event-date"><i class="fa fa-calendar"></i> End date not specified</p>';
        }
        echo '</div>'; // Close event-info

        // Details link
        echo '<a href="event_details.php?id=' . urlencode($event['@id']) . '" class="details-link">Details</a>';
        echo '</div>'; // Close event-content
        echo '</div>'; // Close event-card
    }
}

    echo '</div>'; // Close events-container

    if ($total_pages > 1) {
        echo '<div class="pagination">';
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == $current_page) {
                echo '<span class="current-page">' . $i . '</span>';
            } else {
                echo '<a href="?page=' . $i . '" class="page-link">' . $i . '</a>';
            }
        }
        echo '</div>';
    }
} else {
    echo '<p>Keine Veranstaltungen gefunden.</p>';
}
