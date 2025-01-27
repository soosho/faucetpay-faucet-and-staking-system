<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latest Posts from FaucetMineHub</title>
    <style>
        /* Styling for the latest posts section */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f9;
        }

        .section-latest-posts {
            padding: 40px 20px;
            max-width: 1000px;
            margin: 0 auto;
            background-color: #f4f6f9;
            text-align: center;
        }

        .title {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .lead {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .post-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 100%;
            max-width: 200px;
            box-sizing: border-box;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
            text-align: left;
        }

        .post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .post-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 12px;
            object-fit: cover;
        }

        .post-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .post-title a {
            color: inherit;
            text-decoration: none;
        }

        .post-title a:hover {
            color: #007bff;
        }

        .post-description {
            color: #555;
            font-size: 14px;
            line-height: 1.4;
        }

        .read-more {
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .read-more:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="section-latest-posts">
        <h2 class="title">Read Our Latest Posts</h2>
        <p class="lead">Stay updated with our latest posts about cryptocurrency!</p>
        <div class="row">
            <?php
            // Define the WordPress RSS feed URL
            $rss_url = "https://faucetminehub.com/read/feed/"; // Replace with your WordPress RSS feed URL

            // Use cURL to fetch the RSS feed
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rss_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $rss_data = curl_exec($ch);

            // Check if there was an error with cURL
            if (curl_errno($ch)) {
                echo "<p>Error fetching the RSS feed: " . curl_error($ch) . "</p>";
            }

            curl_close($ch);

            if ($rss_data) {
                // Load the RSS feed into SimpleXML
                $rss_feed = simplexml_load_string($rss_data);

                if ($rss_feed && isset($rss_feed->channel->item)) {
                    // Convert items to an array
                    $items = [];
                    foreach ($rss_feed->channel->item as $item) {
                        $items[] = $item;
                    }

                    // Limit to the latest 5 posts
                    $latest_posts = array_slice($items, 0, 5);

                    // Display each of the latest 5 posts in a styled card format
                    foreach ($latest_posts as $post) {
                        $title = $post->title;
                        $link = $post->link;

                        // Check for a non-empty description and use a fallback if empty
                        $description = !empty($post->description) ? strip_tags(html_entity_decode($post->description)) : 'No description available.';
                        $truncated_description = substr($description, 0, 80) . '...';
                        
                        // Attempt to extract the image from the content:encoded section
                        $content = $post->children('content', true)->encoded;
                        $image_url = '';

                        if ($content) {
                            // Use regex to extract the first image URL from content
                            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
                            $image_url = $image['src'] ?? '';
                        }
            ?>
                        <div class="post-card">
                            <?php if ($image_url): ?>
                                <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Post Image" class="post-image">
                            <?php endif; ?>
                            <h3 class="post-title"><a href="<?php echo htmlspecialchars($link); ?>" target="_blank"><?php echo htmlspecialchars($title); ?></a></h3>
                            <p class="post-description"><?php echo htmlspecialchars($truncated_description); ?></p>
                            <a href="<?php echo htmlspecialchars($link); ?>" class="read-more" target="_blank">Read more</a>
                        </div>
            <?php
                    }
                } else {
                    echo "<p>Could not parse the RSS feed or no items found in the feed.</p>";
                }
            } else {
                echo "<p>Could not fetch the RSS feed data. Please check the feed URL and server settings.</p>";
            }
            ?>
        </div><!-- .row -->
    </div><!-- .section-latest-posts -->
</body>
</html>
