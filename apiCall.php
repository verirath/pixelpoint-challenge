<?php

function fetch_carinthia_events() {
    $api_url = "https://data.carinthia.com/api/v4/endpoints/557ea81f-6d65-6476-9e01-d196112514d2?include=image&token=9962098a5f6c6ae8d16ad5aba95afee0";

    // Initialize cURL
    $ch = curl_init();

    // Set the URL
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
    $data = json_decode($response, true);

    // Return the decoded data
    return $data;
}

function display_events($posts_per_page = 5) {
    $data = fetch_carinthia_events();

    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

    if (!empty($data) && isset($data['@graph'])) {
        $total_items = count($data['@graph']);
        $offset = ($paged - 1) * $posts_per_page;

        $paginated_data = array_slice($data['@graph'], $offset, $posts_per_page);

        echo '<ul class="carinthia-events-list" style="list-style-type: none; padding-left: 0;">';
        foreach ($paginated_data as $event) {
            if (!empty($event['name']) && !empty($event['description'])) {
                echo '<li class="carinthia-event-item" style="margin-bottom: 20px;">';
                if (!empty($event['image'][0]['thumbnailUrl'])) {
                    echo '<img src="' . htmlspecialchars($event['image'][0]['thumbnailUrl']) . '" alt="' . htmlspecialchars($event['name']) . '" style="max-width: 100%; height: auto;">';
                }
                echo '<h2 style="font-size: 24px;">' . htmlspecialchars($event['name']) . '</h2>';
                echo '<p style="font-size: 16px; color: #333; margin: 5px;">' . htmlspecialchars(strip_tags($event['description'])) . '</p>';
                echo '</li>';
            }
        }
        echo '</ul>';

        $total_pages = ceil($total_items / $posts_per_page);
        if ($total_pages > 1) {
            echo '<div class="pagination" style="margin-top: 20px;">';
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $paged) {
                    echo '<span style="font-weight: bold; margin-right: 5px;">' . $i . '</span>';
                } else {
                    echo '<a href="?paged=' . $i . '" style="margin-right: 5px;">' . $i . '</a>';
                }
            }
            echo '</div>';
        }
    } else {
        echo '<p>Keine Veranstaltungen gefunden.</p>';
    }
}

// Call the function to display events
display_events();

?>
