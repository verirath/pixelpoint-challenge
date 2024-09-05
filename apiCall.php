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

if ($events && isset($events['@graph'])) {
    echo '<div class="events-container">';
    foreach ($events['@graph'] as $event) {
        if (!empty($event['name']) && !empty($event['description']) && !empty($event['image'][0]['thumbnailUrl'])) {
            echo '<div class="event-card">';
            echo '<img src="' . htmlspecialchars($event['image'][0]['thumbnailUrl']) . '" alt="' . htmlspecialchars($event['name']) . '">';
            echo '<div class="event-content">';
            echo '<h3>' . htmlspecialchars($event['name']) . '</h3>';
            echo '<p class="event-date"><i class="fa fa-calendar"></i> ' . htmlspecialchars($event['startDate']) . '</p>';
            echo '<p class="event-location"><i class="fa fa-map-marker"></i> ' . htmlspecialchars($event['location']['name']) . '</p>';
            echo '<a href="event_details.php?id=' . urlencode($event['@id']) . '" class="details-link">Details anzeigen</a>';
            echo '</div>';
            echo '</div>';
        }
    }
    echo '</div>';
} else {
    echo '<p>Keine Veranstaltungen gefunden.</p>';
}

?>
