<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <!-- META DATA BRON: https://www.w3schools.com/tags/tag_meta.asp -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Stylesheet: GEMAAKT DOOR DOUWE -->
        <link rel="stylesheet" href="master.css">

        <!-- BRON: https://fontawesome.com -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <title>productpage</title>
    </head>
    <body>
        <?php include "./navbar/index.php"; ?>
        <div class="content">
            <!--            <div class="container1">-->

            <?php
            $db = "mysql:host=localhost;dbname=wideworldimporters;port=3306";
            $username = "root";
            $password = "";
            // Create connection
            $conn = new PDO($db, $username, $password);

            $sql = "use wideworldimporters";
            ////////////////////////////////////////////////////////////////////////
            //from here it's the pull down menu in combination with the search for//
            //the product category. Made by (mostly) Sjoerd./////////////////////////////////
            ////////////////////////////////////////////////////////////////////////
            ?>
            <section>
                <nav>
                    <h2>Kies een categorie:</h2>
                    <?php
                    $sql = $conn->prepare("SELECT StockGroupName "
                            . "FROM stockgroups "
                            . "WHERE StockGroupName <> 'Airline Novelties'" // this needs to be fixed!!!!!!!!!!
                            . "ORDER BY StockGroupName"); // retrieve the categories from the database
                    $sql->execute();
                    // output data of each row
                    $key_cat = 0; // this variable is used to be the key of the array by the name of $stockGroupName it increments every turn
//                    print ("<div class=\"hoi\">");
                    while ($row = $sql->fetch()) {
                        $stockGroupName[$key_cat] = $row["StockGroupName"]; // this adds the difrent categories to an array
                        $key_cat++; // this increments the key in the array
                        $link = preg_replace('/\s+/', '+', $row["StockGroupName"]); // from https://stackoverflow.com/questions/12704613/php-str-replace-replace-spaces-with-underscores
                        echo "<div class=\"kies\" onclick=\"window.location.href='index.php?cat=" . $link . "'\">";
                        echo "<h3>" . $row["StockGroupName"] . "</h3>";
                        echo "</div>\n";
                    }
                    print ("<div class=\"kies\" onclick=\"window.location.href='index.php?cat='\">"
                            . "<h3>Alles</h3>"
                            . "</div>");
                    //diffrent categories to select
                    ?>
                    <br>
                </nav>
                <?php
                $item = filter_input(INPUT_GET, "cat", FILTER_SANITIZE_STRING); // Get the information from the link
                $item = str_replace("+", " ", $item);
                if (in_array($item, $stockGroupName)) { //Check if the name from the category is a category
                    $categorising = TRUE;
                } else {
                    $categorising = FALSE;
                }
                if ($item == "alles" || $categorising != TRUE) { //check if you want to see categorisation
                    $where = "";
                } else {//$where eqeals the where function in the sql query
                    $where = "WHERE StockGroupName = '$item'";
                }

                $sql = $conn->prepare("SELECT stockitemID, StockItemName, StockItemStockGroupID, 
                StockGroupName, MarketingComments, RecommendedRetailPrice, 
                Tags, TaxRate
                FROM stockitemstockgroups
                JOIN stockitems USING (stockitemID)
                JOIN stockgroups USING (StockGroupID)
                $where
                ORDER BY StockGroupName");
                $sql->execute();
                // output data of each row
                while ($row = $sql->fetch()) {
                    echo "<div class=\"product\" onclick=\"window.location.href='artikelpagina?id=" . $row["stockitemID"] . "'\">";
                    echo "<div class=\"ovezicht\">";
                    if (file_exists("product_foto/" . $row["stockitemID"] . ".jpg")) {
                        echo "<br><img src=\"product_foto/" . $row["stockitemID"] . ".jpg\" width=\"200\" height=\"200\">"; // display product picture
                    } else {
                        echo "<br><img src=\"product_foto/" . $row["StockGroupName"] . ".jpg\" width=\"200\" height=\"200\">"; // display category picture
                    }
                    echo "</div>";
                    echo "<div class=\"overzicht\">";
                    echo "<h2>" . $row["StockItemName"] . "</h2>";
                    echo "<p>Nu voor maar €" . number_format($row["RecommendedRetailPrice"] * 1.21, 2, ",", ".") . "</p>"; // product price
                    echo "</div></div>";
                }

                ///////////////////////////////////////////////////////////////////////
                //the above part was for categorisation and to view all////////////////
                // a part from this code was retrieved from:///////////////////////////
                // https://www.w3schools.com/php/php_mysql_create.asp//////////////////
                ///////////////////////////////////////////////////////////////////////
                ?>




                <?php
                $conn = NULL; // close the connection to the server
                ?>
            </section>
            <!--            </div>-->
        </div>
    </body>
</html>
