<?php
    $servername = "";
    $username = "";
    $password = "";
    $dbname = "";
    $port = "";

    $conn = mysqli_connect($servername, $username, $password, $dbname, $port);
    if(!$conn){
        die("Connection failed: " . mysql_connect_error());
    }

    $cmd = $_GET['command'];
    if(strcmp($cmd, "post") == 0){
        $postID = intval($_GET["postid"]);
        $query = "SELECT v.html FROM post_visualization_html v WHERE v.post_id = '$postID';";
        $result = mysqli_query($conn, $query);
        if($result){
            while($row = mysqli_fetch_assoc($result)){
                   // header('Content-Type: image/svg+xml', true);
                    echo $row['html'];
            }
            mysqli_free_result($result);
        }
        else{
            die("Error: " . mysqli_error());
        }
    }
    else if(strcmp($cmd, "price") == 0){
        $company = $_GET["company"];
        $query = "SELECT p.id, DATE_FORMAT(DATE(p.created_time), '%m/%d/%Y') AS created_time, p.predicted_impressions_count, h.price
        FROM posts p
        JOIN historical_stock_prices h ON p.company_id = h.company_id
        JOIN companies c ON p.company_id = c.id
        WHERE c.symbol = '$company' AND DATE(p.created_time) = DATE(h.date)
        ORDER BY p.created_time DESC
            LIMIT 200;";
        $result = mysqli_query($conn, $query);
        if($result){
                $data = array();
                while($row = mysqli_fetch_object($result)){
                        array_push($data, $row);
                }
                mysqli_free_result($result);
                echo json_encode($data);
        }
        else{
                die("Error: " . mysqli_error());
        }
    }
    else if(strcmp($cmd, "data") == 0){
        $postID = intval($_GET['postid']);
        $query = "SELECT p.polarity, p.afinn_sentiment, p.pronoun_fraction, p.noun_fraction, p.verb_fraction, p.adjective_fraction, p.adverb_fraction, p.total_words 
            FROM posts p 
            WHERE p.id='$postID';";
        $result = mysqli_query($conn, $query);
        if($result){
            while($row = mysqli_fetch_object($result)){
                echo json_encode($row);
            }
            mysqli_free_result($result);
        }
        else{
            die("Error: " . mysqli_error());
        }
    }
    else{
        echo "invalid";
    }
?>
